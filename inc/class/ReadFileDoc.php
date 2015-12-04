<?php
	/**
	 * reads *.doc files
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * reads *.doc files
	 * @package file-o-meter
	 * @subpackage class
	 */
	class ReadFileDoc
	{
		/**
	 	 * Liest eine Doc Datei Teilweise oder ganz ein und gibt den String zurueck
	 	 * @param int $job_id
	 	 * @return string
	 	 */
	 	public function read_file($job_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT file_id, save_name FROM fom_file_job_index WHERE job_id='.$job_id);
			$result = $cdb->fetch_array($sql);

			//Pfad zu antiword
			$antiword_folder = FOM_ABS_PFAD_EXEC_ANTIWORD;

			if (!empty($antiword_folder))
			{
				//Pfad zur Output Datei
				$tmp_text_file = $result['save_name'].'.txt';

				if (strtoupper(substr(PHP_OS, 0,3) == 'WIN'))
				{
					$antiword_file_text = 'antiword.exe';
				}
				else
				{
					$antiword_file_text = 'antiword';
				}

				//pruefen ob antiword vorhanden ist
				if (file_exists($antiword_folder.$antiword_file_text))
				{
					$antiword_text_exec = 1;
					//gesamtes Dokument einlesen
					system($antiword_folder.$antiword_file_text.' '.FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].' > '.FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_text_file, $antiword_text_exec);

					if ($antiword_text_exec == 0 and file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_text_file))
					{
						$file_string = '';
						$h = fopen(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_text_file, 'r');
						while (!feof($h))
						{
							$line = trim(fgets($h));
							if (!empty($line))
							{
								$file_string .= ' '.$line;
							}
						}
						fclose ($h);

						//Letzte seite ausgelesen Datei kann entfernt werden
						$this->delete_job($job_id);

						//String zurueck geben
						return $file_string;
					}
				}
			}
			return '';
		}

		/**
		 * entfernt einen Jobauftrag aus der Tabelle und alle dazugehoerigen Dateien
		 */
		public function delete_job($job_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT save_name FROM fom_file_job_index WHERE job_id='.$job_id);
			$result = $cdb->fetch_array($sql);

			//Datendatei loeschen
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].'.txt'))
			{
				@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].'.txt');
			}

			//Originaldatei loeschen
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']))
			{
				@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']);
			}
			$cdb->delete('DELETE FROM fom_file_job_index WHERE job_id='.$job_id.' LIMIT 1');
		}
	}
?>
