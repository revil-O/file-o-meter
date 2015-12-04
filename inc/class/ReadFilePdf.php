<?php
	/**
	 * reads *.pdf files
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * reads *.pdf files
	 * @package file-o-meter
	 * @subpackage class
	 */
	class ReadFilePdf
	{
		public $setup_array = array();

	 	public function __construct()
		{
			//Gibt die maximale anzahl an Seiten an die mit einem Auftrag indiziert werden sollen
			$this->setup_array['max_page'] = $GLOBALS['setup_array']['index_job_max_page'];
		}
	 	/**
	 	 * Liest einen PDF Datei Teilweise oder ganz ein und gibt den String zurueck
	 	 * @param int $job_id
	 	 * @return string
	 	 */
	 	public function read_file($job_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT file_id, save_name, last_page FROM fom_file_job_index WHERE job_id='.$job_id);
			$result = $cdb->fetch_array($sql);

			//Pfad zu XPDF
			$xpdf_folder = FOM_ABS_PFAD_EXEC_XPDF;

			if (!empty($xpdf_folder))
			{
				//Pfad zur PDF-Infodatei
				$tmp_info_file = $result['save_name'].'_i.txt';
				$tmp_text_file = $result['save_name'].'.txt';
				//PDF-Info System result
				$pdf_info = '';

				if (strtoupper(substr(PHP_OS, 0,3) == 'WIN'))
				{
					$xpdf_file_info = 'pdfinfo.exe';
					$xpdf_file_text = 'pdftotext.exe';
				}
				else
				{
					$xpdf_file_info = 'pdfinfo';
					$xpdf_file_text = 'pdftotext';
				}

				//pruefen ob xpdf vorhanden ist
				if (file_exists($xpdf_folder.$xpdf_file_text))
				{
					//Bei grossen PDF Dateien erfolgt ein mehrmaliger aufruf in diesem fall kann die info datei bereits existieren
					if (!file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_info_file))
					{
						system($xpdf_folder.$xpdf_file_info.' '.FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].' > '.FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_info_file, $pdf_info);
					}
					//Info Datei wurde angelegt
					if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_info_file))
					{
						$info_array = file(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_info_file);
						$pdf_info = array();

						for($i = 0; $i < count($info_array); $i++)
						{
							if (strtolower(substr($info_array[$i], 0, 5)) == 'pages')
							{
								$pdf_info['pages'] = trim(str_replace('Pages:', '', $info_array[$i]));
							}
							elseif(strtolower(substr($info_array[$i], 0, 11)) == 'pdf version')
							{
								$pdf_info['version'] = trim(str_replace('PDF version:', '', $info_array[$i]));
							}
						}

						$pdf_text_exec = 1;
						//xpdf derzeit bis version 1.7
						if (isset($pdf_info['version']) and $pdf_info['version'] <= 1.7)
						{
							//Bei grossen PDF Documenten die indizierung splitten
							if (isset($pdf_info['pages']) and $pdf_info['pages'] > $this->setup_array['max_page'])
							{
								//Die Datei wurde noch nie indiziert also die ersten $this->setup_array['max_page'] Seiten einlesen
								if ($result['last_page'] == 0)
								{
									$start_page = 1;
									$end_page = $this->setup_array['max_page'];
								}
								else
								{
									// Die zu letzt eingelesene Seite + $this->setup_array['max_page'] ist groesser als es seiten im Dokument gibt
									if ($result['last_page'] + $this->setup_array['max_page'] > $pdf_info['pages'])
									{
										$start_page = $result['last_page'] + 1;
										$end_page = $pdf_info['pages'];
									}
									else
									{
										$start_page = $result['last_page'] + 1;
										$end_page = $result['last_page'] + $this->setup_array['max_page'];
									}
								}

								//nur max. $this->setup_array['max_page'] Seiten einlesen
								system($xpdf_folder.$xpdf_file_text.' -f '.$start_page.' -l '.$end_page.' '.FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].' '.FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_text_file, $pdf_text_exec);

								//zu letzt ausgelsene Seite in DB schreiben
								if ($pdf_text_exec == 0 and $end_page != $pdf_info['pages'])
								{
									$cdb->update("UPDATE fom_file_job_index SET last_page=$end_page WHERE job_id=$job_id");
								}
							}
							else
							{
								$end_page = $pdf_info['pages'];
								//gesamtes Dokument einlesen
								system($xpdf_folder.$xpdf_file_text.' '.FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].' '.FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_text_file, $pdf_text_exec);
							}

							if ($pdf_text_exec == 0 and file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_text_file))
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

								//Datei loeschen wird nicht mehr benoetigt
								@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_text_file);

								//Letzte seite ausgelesen Datei kann entfernt werden
								if ($end_page == $pdf_info['pages'])
								{
									$this->delete_job($job_id);
								}

								//String zurueck geben
								return $file_string;
							}
						}
						else
						{
							//indexauftrag loeschen geht leider nicht
							$this->delete_job($job_id);
							return '';
						}
					}
				}
			}
			return '';
		}

		/**
		 * entfernt einen Jobauftragaus der Tabelle und alle dazugehoerigen Dateien
		 */
		public function delete_job($job_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT save_name FROM fom_file_job_index WHERE job_id='.$job_id);
			$result = $cdb->fetch_array($sql);

			//InfoDatei loeschen
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].'_i.txt'))
			{
				@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'].'_i.txt');
			}

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
