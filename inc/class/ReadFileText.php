<?php
	/**
	 * reads textfiles
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * reads textfiles
	 * @package file-o-meter
	 * @subpackage class
	 */
	class ReadFileText
	{
		/**
	 	 * Liest eine Text Datei ein und gibt den String zurueck
	 	 * @param int $job_id
	 	 * @return string
	 	 */
	 	public function read_file($job_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT file_id, save_name FROM fom_file_job_index WHERE job_id='.$job_id);
			$result = $cdb->fetch_array($sql);

			//Pruefen ob die Textdatei wirklich da ist sollte eingendlich immer der Fall sein
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']))
			{
				$file_string = file_get_contents(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']);

				$file_string = str_replace('</', ' </', $file_string);
				$file_string = str_replace("\n", ' ', $file_string);
				$file_string = str_replace("\r", ' ', $file_string);
				//standard csv trennzeichen ersetzten
				$file_string = str_replace(';', ' ', $file_string);
				$file_string = str_replace(',', ' ', $file_string);
				$file_string = str_replace('|', ' ', $file_string);

				//tmporaeredaten loeschen
				$this->delete_job($job_id, $result['save_name']);

				//Suchen nach anzeichen fuer eine UTF-8 Codierung
				if (stripos($file_string, 'UTF-8') === false)
				{
					return strip_tags($file_string);
				}
				else
				{
					return utf8_decode(strip_tags($file_string));
				}
			}
			return '';
		}

		/**
		 * entfernt einen Jobauftragaus der Tabelle und alle dazugehoerigen Dateien
		 */
		private function delete_job($job_id, $file_name)
		{
			$cdb = new MySql;

			$cdb->delete('DELETE FROM fom_file_job_index WHERE job_id='.$job_id.' LIMIT 1');

			//Datendatei loeschen
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$file_name))
			{
				@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$file_name);
			}
		}
	}
?>
