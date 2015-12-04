<?php
	/**
	 * reads *.odf files
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * reads *.odf files
	 * @package file-o-meter
	 * @subpackage class
	 */
	class ReadFileOdf
	{
		/**
	 	 * Liest einen Doc Datei Teilweise oder ganz ein und gibt den String zurueck
	 	 * @param int $job_id
	 	 * @return string
	 	 */
	 	public function read_file($job_id)
		{
			$cdb = new MySql;
			$gt = new Tree;
			$tn = new Thumbnail();

			$sql = $cdb->select('SELECT file_id, link_id, save_name FROM fom_file_job_index WHERE job_id='.$job_id);
			$result = $cdb->fetch_array($sql);

			//Dateiendung in zip aendern
			$ex = $gt->GetFileExtension($result['save_name']);
			$zip_name = str_replace($ex, 'zip', $result['save_name']);
			$zip_folder_name = str_replace('.'.$ex, '', $result['save_name']);
			$zip_folder = FOM_ABS_PFAD.'files/tmp/unpack/'.$zip_folder_name.'/';

			//Pruefen ob zip Datei bereits da ist oder ob original datei da ist und umbenannt werden kann
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$zip_name) or (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']) and rename(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'], FOM_ABS_PFAD.'files/tmp/index_job/'.$zip_name)))
			{
				//Datei entpacken
				$zip = new PclZip(FOM_ABS_PFAD.'files/tmp/index_job/'.$zip_name);
				if ($zip->extract(PCLZIP_OPT_PATH, $zip_folder))
				{
					//Thumbnail Erzeugen
					if (file_exists($zip_folder.'Thumbnails/thumbnail.png'))
					{
						if (isset($result['file_id']) and !empty($result['file_id']))
						{
							$tn->create_od_thumbnail($zip_folder.'Thumbnails/thumbnail.png', $result['file_id']);
						}
						elseif (isset($result['link_id']) and !empty($result['link_id']))
						{
							$tn->create_od_thumbnail($zip_folder.'Thumbnails/thumbnail.png', 0, $result['link_id']);
						}
					}

					if (file_exists($zip_folder.'content.xml'))
					{
						$file_string = '';
						$h = fopen($zip_folder.'content.xml', 'r');
						while (!feof($h))
						{
							$line = trim(fgets($h));
							if (!empty($line))
							{
								$file_string .= ' '.$line;
							}
						}
						fclose ($h);
						$file_string = str_replace('</', ' </', $file_string);

						//tmporaeredaten loeschen
						$this->delete_job($job_id, $zip_folder, $zip_name);

						if (strpos($file_string, 'encoding="UTF-8"?>') === false)
						{
							return strip_tags($file_string);
						}
						else
						{
							return utf8_decode(strip_tags($file_string));
						}
					}
				}
			}
			return '';
		}

		/**
		 * entfernt einen Jobauftragaus der Tabelle und alle dazugehoerigen Dateien
		 */
		private function delete_job($job_id, $unpack_folder, $file_name)
		{
			$cdb = new MySql;

			$cdb->delete('DELETE FROM fom_file_job_index WHERE job_id='.$job_id.' LIMIT 1');

			//Datendatei loeschen
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$file_name))
			{
				@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$file_name);
			}

			$this->remove_rk($unpack_folder);
			rmdir($unpack_folder);
		}
		/**
		 * Loescht den inhalt eines Verzeichnises
		 * @return void
		 */
		private function remove_rk($folder)
		{
			$remove_array = array();
			if ($h = opendir($folder))
			{
				while (false !== ($f = readdir($h)))
				{
					if (is_dir($folder.$f) and $f != '.' and $f != '..')
					{
						$remove_array['folder'][] = $folder.$f.'/';
					}
					elseif(is_file($folder.$f) and $f != '.' and $f != '..')
					{
						$remove_array['file'][] = $folder.$f;
					}
				}
				closedir($h);
	    	}

	    	if (isset($remove_array['folder']) and count($remove_array['folder']) > 0)
	    	{
				for($i = 0; $i < count($remove_array['folder']); $i++)
				{
					$this->remove_rk($remove_array['folder'][$i]);
					rmdir($remove_array['folder'][$i]);
				}
	    	}

	    	if (isset($remove_array['file']) and count($remove_array['file']) > 0)
	    	{
				for($i = 0; $i < count($remove_array['file']); $i++)
				{
					@unlink($remove_array['file'][$i]);
				}
	    	}
		}
	}
?>
