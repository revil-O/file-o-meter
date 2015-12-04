<?php
	/**
	 * Create Thumbnails
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * Create Thumbnails
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Thumbnail
	{
		private $setup_array = array();

		/**
		 * Legt allgemeine Grundeinstellungen fest
		 * @return void
		 */
		public function __construct()
		{
			//Maximale Thumbnail groesse
			$this->setup_array['max_tn_size'] = 256;

			$exec_gs = FOM_ABS_PFAD_EXEC_GHOSTSCRIPT;
			if (!empty($exec_gs))
			{
				//Ghostscript Pfad
				if (strtoupper(substr(PHP_OS, 0,3) == 'WIN'))
				{
					if (file_exists($exec_gs.'gswin32.exe'))
					{
						$this->setup_array['gs_pfad'] = $exec_gs.'gswin32.exe';
					}
					elseif (file_exists($exec_gs.'gs.exe'))
					{
						$this->setup_array['gs_pfad'] = $exec_gs.'gs.exe';
					}
					else
					{
						$this->setup_array['gs_pfad'] = '';
					}
				}
				else
				{
					$this->setup_array['gs_pfad'] = $exec_gs.'gs';
				}
			}
			else
			{
				$this->setup_array['gs_pfad'] = '';
			}
		}

		/**
		 * Ruft die Einzelnen Funktionen zur erstellung von Thumbnails auf
		 * @param int $job_id
		 * @param int $file_id
		 * @param string $save_name
		 * @return void
		 */
		public function create_thumbnail($job_id = 0, $file_id = 0, $save_name = '')
		{
			$cdb = new MySql();
			$gt = new Tree();
			$fj = new FileJobs();

			$start_time = time();

			if ($job_id > 0)
			{
				$sql = $cdb->select('SELECT * FROM fom_file_job_tn WHERE job_id='.$job_id);
			}
			elseif ($file_id > 0 and !empty($save_name))
			{
				$sql = $cdb->select("SELECT * FROM fom_file_job_tn WHERE file_id=$file_id AND save_name='$save_name'");
			}
			else
			{
				$sql = $cdb->select('SELECT * FROM fom_file_job_tn ORDER BY save_time ASC');
			}

			while($result = $cdb->fetch_array($sql))
			{
				$current_time = time();

				//nicht mehr als 10 sek thumbnails erstellen
				if ($current_time - $start_time > 10)
				{
					break;
				}

				$projekt_id = $fj->get_project_id($result['file_id']);

				$pfad_sql = $cdb->select('SELECT pfad FROM fom_file_server WHERE projekt_id='.$projekt_id);
				$pfad_result = $cdb->fetch_array($pfad_sql);

				$pfad = $pfad_result['pfad'].$projekt_id.'/'.substr($result['save_time'], 0, 6).'/';

				if (file_exists($pfad.$result['save_name']))
				{
					$del_job = false;

					$ex = $gt->GetFileExtension($result['save_name']);

					if ($ex == 'pdf')
					{
						//thumbnail existiert bereits
						if (!file_exists($pfad.'tn_'.substr($result['save_name'], 0, -3).'jpg'))
						{
							if ($this->create_pdf_thumbnail($pfad.$result['save_name'], 0, $result['job_id']) === true)
							{
								$del_job = true;
							}
						}
						else
						{
							$del_job = true;
						}
					}
					elseif ($ex == 'jpg' or $ex == 'jpe' or $ex == 'jpeg')
					{
						//thumbnail existiert bereits
						if (!file_exists($pfad.'tn_'.$result['save_name']))
						{
							if ($this->create_img_thumbnail($pfad.$result['save_name'], 0, $result['job_id']) === true)
							{
								$del_job = true;
							}
						}
						else
						{
							$del_job = true;
						}
					}
					elseif ($ex == 'gif')
					{
						//thumbnail existiert bereits
						if (!file_exists($pfad.'tn_'.$result['save_name']))
						{
							if ($this->create_img_thumbnail($pfad.$result['save_name'], 0, $result['job_id']) === true)
							{
								$del_job = true;
							}
						}
						else
						{
							$del_job = true;
						}
					}
					elseif ($ex == 'png')
					{
						//thumbnail existiert bereits
						if (!file_exists($pfad.'tn_'.$result['save_name']))
						{
							if ($this->create_img_thumbnail($pfad.$result['save_name'], 0, $result['job_id']) === true)
							{
								$del_job = true;
							}
						}
						else
						{
							$del_job = true;
						}
					}
					elseif ($ex == 'odt' or $ex == 'ods')
					{
						//thumbnail existiert noch nicht
						if (!file_exists($pfad.'tn_'.substr($result['save_name'], 0, -3).'png'))
						{
							$od_folder = str_replace('.'.$ex, '', $result['save_name']);

							//entpacktes verzeichnis existiert bereits
							if (file_exists(FOM_ABS_PFAD.'files/tmp/unpack/'.$od_folder.'/Thumbnails/thumbnail.png'))
							{
								if ($this->create_od_thumbnail(FOM_ABS_PFAD.'files/tmp/unpack/'.$od_folder.'/Thumbnails/thumbnail.png', 0, 0, $result['job_id']) === true)
								{
									$del_job = true;
								}
							}
							//muss erstnoch entpackt werden
							else
							{
								if (@copy($pfad.$result['save_name'], FOM_ABS_PFAD.'files/tmp/unpack/'.$result['save_name']))
								{
									$zip_name = str_replace($ex, 'zip', $result['save_name']);
									if (rename(FOM_ABS_PFAD.'files/tmp/unpack/'.$result['save_name'], FOM_ABS_PFAD.'files/tmp/unpack/'.$zip_name))
									{
										//Datei entpacken
										$zip = new PclZip(FOM_ABS_PFAD.'files/tmp/unpack/'.$zip_name);
										$zip_folder = FOM_ABS_PFAD.'files/tmp/unpack/'.$od_folder.'/';

										if ($zip->extract(PCLZIP_OPT_PATH, $zip_folder))
										{
											//Thumbnail Erzeugen
											if (file_exists($zip_folder.'Thumbnails/thumbnail.png'))
											{
												if ($this->create_od_thumbnail($zip_folder.'Thumbnails/thumbnail.png', 0, 0, $result['job_id']) === true)
												{
													$del_job = true;
												}
											}
										}
									}
								}
							}
						}
						else
						{
							$del_job = true;
						}
					}
					elseif ($ex == 'txt' or $ex == 'csv' or $ex == 'xml' or $ex == 'log')
					{
						//thumbnail existiert bereits
						if (!file_exists($pfad.'tn_'.substr($result['save_name'], 0, -3).'jpg'))
						{
							if (file_exists($pfad.$result['save_name']) and filesize($pfad.$result['save_name']) > 0)
							{
								$im = @imagecreatetruecolor($this->setup_array['max_tn_size'], $this->setup_array['max_tn_size']);
								$text_color = @imagecolorallocate($im, 0, 0, 0);
								$white = @imagecolorallocate($im, 255, 255, 255);
								imagefill($im, 0, 0, $white);

								$line_count = 10;
								$handle = fopen ($pfad.$result['save_name'], 'r');
								while (!feof($handle))
								{
									$line = fgets($handle, 1024);

									imagettftext($im, 8, 0, 10, $line_count, $text_color, FOM_ABS_PFAD.'template/default/DejaVuSansMono.ttf', trim($line));
									$line_count += 10;

									if ($line_count > 250)
									{
										break;
									}
								}
								fclose ($handle);

								imagejpeg($im, $pfad.'tn_'.substr($result['save_name'], 0, -3).'jpg');
								imagedestroy($im);

								if (file_exists($pfad.'tn_'.substr($result['save_name'], 0, -3).'jpg'))
								{
									$del_job = true;
								}
							}
							else
							{
								$del_job = true;
							}
						}
						else
						{
							$del_job = true;
						}
					}
					//kein Thumbnail moeglich jobauftrag loeschen
					else
					{
						$del_job = true;
					}
				}
				//kein Thumbnail moeglich jobauftrag loeschen
				else
				{
					$del_job = true;
				}

				if ($del_job === true)
				{
					$cdb->delete('DELETE FROM fom_file_job_tn WHERE job_id='.$result['job_id'].' LIMIT 1');
				}
			}
		}

		/**
		 * Erstellt ein Thumbnail von einem Bild
		 * @param string $img
		 * @param int $file_id
		 * @param int $job_id
		 * @return boole
		 */
		public function create_img_thumbnail($img, $file_id = 0, $job_id = 0)
		{
			$cdb = new MySql();

			if ($file_id > 0)
			{
				$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t2.projekt_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.file_id='.$file_id);
			}
			elseif ($job_id > 0)
			{
				$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t3.projekt_id FROM fom_file_job_tn t1
									LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
									LEFT JOIN fom_folder t3 ON t2.folder_id=t3.folder_id
									WHERE t1.job_id='.$job_id);
			}
			$result = $cdb->fetch_array($sql);

			$dest = FOM_ABS_PFAD.'files/upload/'.$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/tn_'.$result['save_name'];

			if (!file_exists($dest))
			{
				if (@copy($img, $dest) === true)
				{
					$img_info = getimagesize($dest);

					//Pruefen ob Thumbnail zu gross ist
					if ($img_info[0] > $this->setup_array['max_tn_size'] or $img_info[1] > $this->setup_array['max_tn_size'])
					{
						return $this->resize_thumbnail($dest);
					}
					else
					{
						return true;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return true;
			}
		}

		/**
		 * Erstellt ein Thumbnail aus einem OpenDocument Archiv
		 * @param string $thumbnail
		 * @param int $file_id
		 * @param int $job_id
		 * @return boole
		 */
		public function create_od_thumbnail($thumbnail, $file_id = 0, $link_id = 0, $job_id = 0)
		{
			$cdb = new MySql();

			if ($file_id > 0)
			{
				$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t2.projekt_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.file_id='.$file_id);
			}
			elseif ($link_id > 0)
			{
				$sql = $cdb->select('SELECT t1.md5_link AS save_name, t1.save_time, t2.projekt_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.link_id='.$link_id);
			}
			elseif ($job_id > 0)
			{
				$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t3.projekt_id FROM fom_file_job_tn t1
									LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
									LEFT JOIN fom_folder t3 ON t2.folder_id=t3.folder_id
									WHERE t1.job_id='.$job_id);
			}

			if (isset($sql))
			{
				$gt = new Tree();

				$result = $cdb->fetch_array($sql);

				$ex_string = $gt->GetFileExtension($result['save_name']);

				if (!empty($ex_string))
				{
					$dest = FOM_ABS_PFAD.'files/upload/'.$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/tn_'.str_replace('.'.$ex_string, '', $result['save_name']).'.png';
				}
				//Sollte nur bei Externen Links der fall sein
				else
				{
					$dest = FOM_ABS_PFAD.'files/upload/'.$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/tn_'.$result['save_name'].'.png';
				}

				//Theoretisch kann diese funktion fuer eine Datei mehrmals aufgerufen werden
				//Die Thumbnail sollte in diesem Fall nur einmal Erstellt werden
				if (!file_exists($dest))
				{
					if (@copy($thumbnail, $dest) === true)
					{
						$img_info = getimagesize($dest);

						//Pruefen ob Thumbnail zu gross ist
						if ($img_info[0] > $this->setup_array['max_tn_size'] or $img_info[1] > $this->setup_array['max_tn_size'])
						{
							return $this->resize_thumbnail($dest);
						}
						else
						{
							return true;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return true;
				}
			}
			else
			{
				return false;
			}
		}

		/**
		 * Erstellt ein Thumbnail aus einer PDF
		 * @param string $pdf
		 * @param int $file_id
		 * @param int $job_id
		 * @return boole
		 */
		public function create_pdf_thumbnail($pdf, $file_id = 0, $job_id = 0)
		{
			if (!empty($this->setup_array['gs_pfad']) and file_exists($this->setup_array['gs_pfad']))
			{
				$cdb = new MySql();

				if ($file_id > 0)
				{
					$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t2.projekt_id FROM fom_files t1
										LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
										WHERE t1.file_id='.$file_id);
				}
				elseif ($job_id > 0)
				{
					$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t3.projekt_id FROM fom_file_job_tn t1
										LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
										LEFT JOIN fom_folder t3 ON t2.folder_id=t3.folder_id
										WHERE t1.job_id='.$job_id);
				}
				$result = $cdb->fetch_array($sql);

				$dest = substr(FOM_ABS_PFAD.'files/upload/'.$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/tn_'.$result['save_name'], 0, -3).'jpg';

				$gs_string = $this->setup_array['gs_pfad'].' -q -dBATCH -dMaxBitmap=300000000 -dNOPAUSE -dSAFER -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -dFirstPage=1 -dLastPage=1 -sOutputFile='.$dest.' '.$pdf.' -c quit';

				system($gs_string, $fp);
				if ($fp == 0)
				{
					if (file_exists($dest))
					{
						$img_info = getimagesize($dest);

						//Pruefen ob Thumbnail zu gross ist
						if ($img_info[0] > $this->setup_array['max_tn_size'] or $img_info[1] > $this->setup_array['max_tn_size'])
						{
							return $this->resize_thumbnail($dest);
						}
						else
						{
							return true;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		/**
		 * Aendert die groesse eines bereits Existierenden Thumbnails
		 * @param string $thumbnail
		 * @return boole
		 */
		private function resize_thumbnail($thumbnail)
		{
			$gt = new Tree();

			$ex = $gt->GetFileExtension($thumbnail);

			if ($ex == 'jpe' or $ex == 'jpeg')
			{
				$ex = 'jpg';
			}

			$size = getimagesize($thumbnail);
			$file_size = filesize($thumbnail);
			$new_width = $size[0];
			$new_height = $size[0];

			//neue Groesse
			if ($size[0] > $this->setup_array['max_tn_size'])
			{
				$new_width = $this->setup_array['max_tn_size'];
				$new_height = round($this->setup_array['max_tn_size'] * $size[1] / $size[0]);
			}

			if ($new_height > $this->setup_array['max_tn_size'])
			{
				$new_width = round($this->setup_array['max_tn_size'] * $new_width / $new_height);
				$new_height = $this->setup_array['max_tn_size'];
			}

			//Original auslesen
			if ($ex == 'jpg')
			{
				$tmp_image = @imagecreatefromjpeg($thumbnail);
			}
			elseif ($ex == 'png')
			{
				$tmp_image = @imagecreatefrompng($thumbnail);
			}
			elseif ($ex == 'gif')
			{
				$tmp_image = @imagecreatefromgif($thumbnail);
			}

			if (isset($tmp_image))
			{
				//bild erstellen
				$new_image = @imagecreatetruecolor($new_width, $new_height);
				//Original verkleinern
				@imagecopyresampled($new_image, $tmp_image, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

				if (file_exists($thumbnail))
				{
					@unlink($thumbnail);
				}
				//Speichern
				if ($ex == 'jpg')
				{
					@imagejpeg($new_image, $thumbnail);
				}
				elseif ($ex == 'png')
				{
					@imagepng($new_image, $thumbnail);
				}
				elseif ($ex == 'gif')
				{
					@imagegif($new_image, $thumbnail);
				}
				@imagedestroy($new_image);
				@imagedestroy($tmp_image);

				if (file_exists($thumbnail))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				@unlink($thumbnail);
				return false;
			}
		}

		/**
		 * Prueft ob ein Thumbnail vorhanden ist
		 * @param int $file_id
		 * @return mixed
		 */
		public function search_thumbnail($file_id, $sub_file_id = 0)
		{
			$cdb = new MySql();
			$gt = new Tree();

			if ($file_id > 0)
			{
				$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t2.projekt_id, t3.pfad FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									LEFT JOIN fom_file_server t3 ON t1.file_server_id=t3.file_server_id
									WHERE t1.file_id='.$file_id);
			}
			elseif ($sub_file_id > 0)
			{
				$sql = $cdb->select('SELECT t1.save_name, t1.save_time, t3.projekt_id, t4.pfad FROM fom_file_subversion t1
									LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
									LEFT JOIN fom_folder t3 ON t2.folder_id=t3.folder_id
									LEFT JOIN fom_file_server t4 ON t2.file_server_id=t4.file_server_id
									WHERE t1.sub_fileid='.$sub_file_id);
			}

			$result = $cdb->fetch_array($sql);

			if (!empty($result['save_name']))
			{
				$ex = $gt->GetFileExtension($result['save_name']);
				$thumbnail_ex = '';

				if ($ex == 'pdf' or $ex == 'jpg')
				{
					$thumbnail_ex = 'jpg';
				}
				elseif ($ex == 'odt' or $ex == 'ods' or $ex == 'png')
				{
					$thumbnail_ex = 'png';
				}
				elseif ($ex == 'gif')
				{
					$thumbnail_ex = 'gif';
				}
				elseif ($ex == 'jpe')
				{
					$thumbnail_ex = 'jpe';
				}
				elseif ($ex == 'jpeg')
				{
					$thumbnail_ex = 'jpeg';
				}
				elseif ($ex == 'txt' or $ex == 'csv' or $ex == 'xml' or $ex == 'log')
				{
					$thumbnail_ex = 'jpg';
				}


				if (!empty($thumbnail_ex))
				{
					$thumbnail_pfad = $result['pfad'].$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/';
					$thumbnail_name = 'tn_'.str_replace('.'.$ex, '', $result['save_name']).'.'.$thumbnail_ex;

					if (file_exists($thumbnail_pfad.$thumbnail_name))
					{
						$img_size = getimagesize($thumbnail_pfad.$thumbnail_name);

						return array(	'pfad'		=> $thumbnail_pfad,
										'name'		=> $thumbnail_name,
										'ex'		=> $thumbnail_ex,
										'width'		=> $img_size[0],
										'height'	=> $img_size[1]);
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		/*
		 * Diese Funktion wird nicht benoetigt
		 * Man kann diese Funktion benutzen um fuer alle Dateien im FOM einen Thumbnailjob zu erstellen.
		 * Bereits existierende Thumbnails werden nicht ueberschrieben!
		 */
		public function _tmp_create_thumbnail_job_for_all()
		{
			$cdb = new MySql();

			$sql = $cdb->select('SELECT file_id, save_name, save_time FROM fom_files');
			while ($result = $cdb->fetch_array($sql))
			{
				$cdb->insert("INSERT INTO fom_file_job_tn (file_id, save_name, save_time) VALUES (".$result['file_id'].", '".$result['save_name']."', '".$result['save_time']."')");
			}
		}
	}
?>