<?php
	/**
	 * manages copy and move operations of files and folders
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * manages copy and move operations of files and folders
	 * @package file-o-meter
	 * @subpackage class
	 */
	class FileFolderCopyMove
	{
		private $tmp_array = array();

		public function __construct()
		{
			$this->chk_cookie_value();
		}

		/**
		 * Erstellt ein Array mit allen Informationen die fuer eine Js Kopierauftrag notwendig sind
		 * @return array
		 */
		public function create_js_cookie_array()
		{
			$cdb = new MySql;

			$return_array = array();
			$return_array['result'] = false;

			//Dateien Link
			if (isset($_COOKIE['FOM_FileLink_string']) and !empty($_COOKIE['FOM_FileLink_string']) and isset($_GET['pid_int']) and isset($_GET['fid_int']))
			{
				$this->chk_file_data($_COOKIE['FOM_FileLink_string']);

				if ($this->tmp_array['result'] == true)
				{
					$return_array['file_link'] = $this->tmp_array['file_data'];
					$return_array['result'] = $this->tmp_array['result'];
				}
			}

			//Dateien Kopieren
			if (isset($_COOKIE['FOM_FileCopy_string']) and !empty($_COOKIE['FOM_FileCopy_string']) and isset($_GET['pid_int']) and isset($_GET['fid_int']))
			{
				$this->chk_file_data($_COOKIE['FOM_FileCopy_string']);

				if ($this->tmp_array['result'] == true)
				{
					$return_array['file_copy'] = $this->tmp_array['file_data'];
					$return_array['result'] = $this->tmp_array['result'];
				}
			}

			//Dateien Verschieben
			if (isset($_COOKIE['FOM_FileMove_string']) and !empty($_COOKIE['FOM_FileMove_string']) and isset($_GET['pid_int']) and isset($_GET['fid_int']))
			{
				$this->chk_file_data($_COOKIE['FOM_FileMove_string']);

				if ($this->tmp_array['result'] == true)
				{
					$return_array['file_move'] = $this->tmp_array['file_data'];
					$return_array['result'] = $this->tmp_array['result'];
				}
			}

			//Verzeichnis Kopieren
			if (isset($_COOKIE['FOM_FolderCopy_string']) and !empty($_COOKIE['FOM_FolderCopy_string']) and isset($_GET['pid_int']))
			{
				$this->chk_folder_data($_COOKIE['FOM_FolderCopy_string']);

				if ($this->tmp_array['result'] == true)
				{
					$return_array['folder_copy'] = $this->tmp_array['folder_data'];
					$return_array['result'] = $this->tmp_array['result'];
				}
			}

			//Verzeichnis Verschieben
			if (isset($_COOKIE['FOM_FolderMove_string']) and !empty($_COOKIE['FOM_FolderMove_string']) and isset($_GET['pid_int']))
			{
				$this->chk_folder_data($_COOKIE['FOM_FolderMove_string']);

				if ($this->tmp_array['result'] == true)
				{
					$return_array['folder_move'] = $this->tmp_array['folder_data'];
					$return_array['result'] = $this->tmp_array['result'];
				}
			}

			return $return_array;
		}

		/**
		 * Prueft die Daten fuer Folderaktionen
		 * @param string $cookie_string
		 * @return void
		 */
		private function chk_folder_data($cookie_string)
		{
			$cdb = new MySql;

			$this->tmp_array = array();

			$this->tmp_array['result'] = false;
			//Cookiestring am Trennzeichen zerlegen
			$cookie_array = explode('|', $cookie_string);

			for($i = 0; $i < count($cookie_array); $i++)
			{
				//Pruefen ob das Verzeichnis Existiert
				$sql = $cdb->select('SELECT folder_name, ob_folder FROM fom_folder WHERE folder_id='.$cookie_array[$i]." AND anzeigen='1' AND projekt_id=".$_GET['pid_int']);
				$result = $cdb->fetch_array($sql);

				if (!empty($result['folder_name']))
				{
					//Pruefen, dass Ziel und Quelle nicht identisch ist
					$same_sub_folder = false;
					//Nur Pruefen wenn in ein Unterverzeichnis Kopiert werden soll
					if (isset($_GET['fid_int']) and $_GET['fid_int'] > 0)
					{
						$sub_sql = $cdb->select('SELECT ob_folder FROM fom_folder WHERE folder_id='.$_GET['fid_int']);
						$sub_result = $cdb->fetch_array($sub_sql);

						//Einen Ordner nicht in sich selbst Kopieren
						if ($_GET['fid_int'] == $cookie_array[$i])
						{
							$same_sub_folder = true;
						}
						//Einen Ordner nicht in sein eigenes Unterverzeichnis Kopieren
						else
						{
							$while = true;
							$tmp_file_int = $_GET['fid_int'];
							while($while == true)
							{
								$sub_sql = $cdb->select("SELECT ob_folder FROM fom_folder WHERE folder_id=$tmp_file_int");
								$sub_result = $cdb->fetch_array($sub_sql);

								//Oberstes Verzeichnis erreicht
								if ($sub_result['ob_folder'] == 0)
								{
									$while = false;
									break;
								}
								//Ziel ist ein eigenes Unterverzeichnis
								elseif($sub_result['ob_folder'] == $cookie_array[$i])
								{
									$while = false;
									$same_sub_folder = true;
									break;
								}
								else
								{
									$tmp_file_int = $sub_result['ob_folder'];
								}
							}
						}
					}
					//Verzeichnis soll auf Hauptebene kopiert werden und sollte da natuerlich noch nicht sein
					elseif($result['ob_folder'] == 0)
					{
						$same_sub_folder = false;
						//alle verzeichnisse auf der hauütebene
						$sub_sql = $cdb->select('SELECT folder_name FROM fom_folder WHERE projekt_id='.$_GET['pid_int'].' AND ob_folder=0 '."AND anzeigen='1'");
						while ($sub_result = $cdb->fetch_array($sub_sql))
						{
							//nicht auf eine ebene kopieren wo es das gleiche verzeichnis gibt
							if ($result['folder_name'] == $sub_result['folder_name'])
							{
								$same_sub_folder = true;
								break;
							}
						}
					}

					if ($same_sub_folder == false)
					{
						$this->tmp_array['folder_data'][] = array('folder_id' => $cookie_array[$i], 'folder_name' => $result['folder_name']);
					}
				}
			}

			//es wurden Ergebnisse gefunden
			if (isset($this->tmp_array['folder_data']) and count($this->tmp_array['folder_data']) > 0)
			{
				$this->tmp_array['result'] = true;
			}
		}

		/**
		 * Prueft die Daten fuer Fileaktionen
		 * @param string $cookie_string
		 * @return void
		 */
		private function chk_file_data($cookie_string)
		{
			$cdb = new MySql;

			$this->tmp_array = array();

			$this->tmp_array['result'] = false;
			//Cookiestring am Trennzeichen zerlegen
			$cookie_array = explode('|', $cookie_string);

			for($i = 0; $i < count($cookie_array); $i++)
			{
				//Pruefen ob es die Datei gibt
				//Pruefen das Quell- und Zielordner unterschiedlich sind
				$sql = $cdb->select("SELECT t1.org_name FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.file_id=$cookie_array[$i] AND t1.anzeigen='1' AND t2.projekt_id=".$_GET['pid_int'].' AND t1.folder_id!='.$_GET['fid_int']);
				$result = $cdb->fetch_array($sql);

				if (!empty($result['org_name']))
				{
					$this->tmp_array['file_data'][] = array('file_id' => $cookie_array[$i], 'file_name' => $result['org_name']);
				}
			}

			//es wurden Ergebnisse gefunden
			if (isset($this->tmp_array['file_data']) and count($this->tmp_array['file_data']) > 0)
			{
				$this->tmp_array['result'] = true;
			}
		}

		/**
		 * Prueft die Cookie Daten auf einen Korrekten Datentyp und entfernt doppelte werte
		 * @return void
		 */
		private function chk_cookie_value()
		{
			//Cookie Index der Geprueft werden soll
			$cookie_index = array('FOM_FileCopy_string', 'FOM_FileMove_string', 'FOM_FolderCopy_string', 'FOM_FolderMove_string', 'FOM_FileLink_string');

			for($i = 0; $i < count($cookie_index); $i++)
			{
				//Cookie vorhanden
				if (isset($_COOKIE[$cookie_index[$i]]) and !empty($_COOKIE[$cookie_index[$i]]))
				{
					//Trennzeichen zerlegen
					$ex_value = explode('|', $_COOKIE[$cookie_index[$i]]);
					//Doppelte Werte entfernen
					$ex_value = array_unique($ex_value);
					$cookie_string = '';

					foreach($ex_value as $value)
					{
						//Datentyp Pruefen
						if (is_numeric($value))
						{
							if (empty($cookie_string))
							{
								$cookie_string = $value;
							}
							else
							{
								$cookie_string .= '|'.$value;
							}
						}
					}
					$_COOKIE[$cookie_index[$i]] = $cookie_string;
				}
			}
		}

		/**
		 * Gibt die Einfuegenoptionen in einem html string zurueck
		 * @param array $js_cookie_job
		 * @return string
		 */
		public function get_paste_option($js_cookie_job)
		{
			$reload = new Reload;

			$form_get = '?fileinc=';
			$form_hidden = '';
			if (isset($_GET['pid_int']))
			{
				$form_hidden .= '<input type="hidden" name="pid_int" value="'.$_GET['pid_int'].'" />';
				$form_get .= '&amp;pid_int='.$_GET['pid_int'];
			}
			if (isset($_GET['fid_int']))
			{
				$form_hidden .= '<input type="hidden" name="fid_int" value="'.$_GET['fid_int'].'" />';
				$form_get .= '&amp;fid_int='.$_GET['fid_int'];
			}


			$return = '<div id="paste_option" class="paste_box" style="display: none;">
						<form action="index.php'.$GLOBALS['gv']->create_get_string($form_get).'" method="post" onsubmit="rm_cookie();" accept-charset="UTF-8">
							<input type="hidden" name="job_string" value="paste_file_folder" />';
			$return .=		$form_hidden;
			$return .= 		$reload->create('return').'
							<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
								<colgroup>
									<col width="10%" />
									<col width="90%" />
								</colgroup>';

			$job_box_exists = false;

			//Datei Verknuepfung
			if (isset($js_cookie_job['file_link']))
			{
				$style = 1;
				$return .= '<tr class="paste_line_'.$style.'"><td colspan="2"><strong>'.get_text(300, 'return').':</strong></td></tr>';//Copy files
				$style++;
				for($i = 0; $i < count($js_cookie_job['file_link']); $i++)
				{
					$return .= '<tr class="paste_line_'.$style.'"><td><input type="checkbox" name="file_link[]" value="'.$js_cookie_job['file_link'][$i]['file_id'].'" /></td><td>'.$js_cookie_job['file_link'][$i]['file_name'].'</td></tr>';
					if ($style == 1)
					{
						$style++;
					}
					else
					{
						$style = 1;
					}
				}
				$job_box_exists = true;
			}

			//Datei Kopieren
			if (isset($js_cookie_job['file_copy']))
			{
				$style = 1;
				$return .= '<tr class="paste_line_'.$style.'"><td colspan="2"><strong>'.get_text(241, 'return').':</strong></td></tr>';//Copy files
				$style++;
				for($i = 0; $i < count($js_cookie_job['file_copy']); $i++)
				{
					$return .= '<tr class="paste_line_'.$style.'"><td><input type="checkbox" name="file_copy[]" value="'.$js_cookie_job['file_copy'][$i]['file_id'].'" /></td><td>'.$js_cookie_job['file_copy'][$i]['file_name'].'</td></tr>';
					if ($style == 1)
					{
						$style++;
					}
					else
					{
						$style = 1;
					}
				}
				$job_box_exists = true;
			}

			//Datei Verschieben
			if (isset($js_cookie_job['file_move']))
			{
				if($job_box_exists == true)
				{
					$return .= '<tr class="paste_line_2"><td colspan="2"><hr /></td></tr>';
				}

				$style = 1;
				$return .= '<tr class="paste_line_'.$style.'"><td colspan="2"><strong>'.get_text(242, 'return').':</strong></td></tr>';//Move files
				$style++;
				for($i = 0; $i < count($js_cookie_job['file_move']); $i++)
				{
					$return .= '<tr class="paste_line_'.$style.'"><td><input type="checkbox" name="file_move[]" value="'.$js_cookie_job['file_move'][$i]['file_id'].'" /></td><td>'.$js_cookie_job['file_move'][$i]['file_name'].'</td></tr>';
					if ($style == 1)
					{
						$style++;
					}
					else
					{
						$style = 1;
					}
				}
				$job_box_exists = true;
			}

			//Verzeichnis Kopieren
			if (isset($js_cookie_job['folder_copy']))
			{
				if($job_box_exists == true)
				{
					$return .= '<tr class="paste_line_2"><td colspan="2"><hr /></td></tr>';
				}

				$style = 1;
				$return .= '<tr class="paste_line_'.$style.'"><td colspan="2"><strong>'.get_text(243, 'return').':</strong></td></tr>';//Copy folders
				$style++;
				for($i = 0; $i < count($js_cookie_job['folder_copy']); $i++)
				{
					$return .= '<tr class="paste_line_'.$style.'"><td><input type="checkbox" name="folder_copy[]" value="'.$js_cookie_job['folder_copy'][$i]['folder_id'].'" /></td><td>'.$js_cookie_job['folder_copy'][$i]['folder_name'].'</td></tr>';
					if ($style == 1)
					{
						$style++;
					}
					else
					{
						$style = 1;
					}
				}
				$job_box_exists = true;
			}

			//Verzeichnis Kopieren
			if (isset($js_cookie_job['folder_move']))
			{
				if($job_box_exists == true)
				{
					$return .= '<tr class="paste_line_2"><td colspan="2"><hr /></td></tr>';
				}

				$style = 1;
				$return .= '<tr class="paste_line_'.$style.'"><td colspan="2"><strong>'.get_text(244, 'return').':</strong></td></tr>';//Move folders
				$style++;
				for($i = 0; $i < count($js_cookie_job['folder_move']); $i++)
				{
					$return .= '<tr class="paste_line_'.$style.'"><td><input type="checkbox" name="folder_move[]" value="'.$js_cookie_job['folder_move'][$i]['folder_id'].'" /></td><td>'.$js_cookie_job['folder_move'][$i]['folder_name'].'</td></tr>';
					if ($style == 1)
					{
						$style++;
					}
					else
					{
						$style = 1;
					}
				}
				$job_box_exists = true;
			}

			if($job_box_exists == true)
			{

				$return .= '<tr class="paste_line_2"><td colspan="2"><hr /></td></tr>
							<tr class="paste_line_1">
								<td colspan="2">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td width="50%" align="center">
												<a href="javascript:hidden_paste_option();">'.get_img('cancel.png', get_text('close', 'return')).' '.get_text('close', 'return').'</a>
											</td>
											<td width="50%" align="center">
												<input type="submit" value="'.get_text('paste', 'return').'" />
											</td>
										</tr>
									</table>
								</td>
							</tr>';
			}

			$return .= '</table>
					</form>
				</div>';

			return $return;
		}

		/**
		 * Verschiebt Dateien in einen anderen Ordner
		 * @param int $project_id
		 * @param int $folder_id
		 * @param array $file_array
		 * @return boole
		 */
		public function job_move_file($project_id, $folder_id, $file_array)
		{
			if ($project_id > 0 and $folder_id > 0)
			{
				$cdb = new MySql;

				$error_count = 0;

				for($i = 0; $i < count($file_array); $i++)
				{
					$sql = $cdb->select('SELECT t1.org_name, t2.projekt_id FROM fom_files t1
										LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
										WHERE t1.file_id='.$file_array[$i]);
					$result = $cdb->fetch_array($sql);

					if ($project_id == $result['projekt_id'])
					{
						//Keine Doppelten Dateien
						$sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name='".$result['org_name']."' AND anzeigen='1'");
						$result = $cdb->fetch_array($sql);

						if (!isset($result['file_id']) or empty($result['file_id']))
						{
							//FIXME: Hier sollte ein Logeintrag rein
							if ($cdb->update('UPDATE fom_files SET folder_id='.$folder_id.' WHERE file_id='.$file_array[$i]))
							{
								//Alle zugehoerigen Subdateien mitverschieben
								$sub_sql = $cdb->select('SELECT subfile_id FROM fom_sub_files WHERE file_id='.$file_array[$i]);
								while ($sub_result = $cdb->fetch_array($sub_sql))
								{
									if (!$cdb->update('UPDATE fom_files SET folder_id='.$folder_id.' WHERE file_id='.$sub_result['subfile_id']))
									{
										$error_count++;
									}
								}
							}
							else
							{
								$error_count++;
							}
						}
						else
						{
							$error_count++;
						}
					}
					else
					{
						$error_count++;
					}
				}

				if ($error_count == 0)
				{
					$this->clear_cookie();
					return true;
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
		 * Verschiebt ein Verzeichnis in einen anderen Ordner
		 * @param int $project_id
		 * @param int $folder_id
		 * @param array $folder_array
		 * @return boole
		 */
		public function job_move_folder($project_id, $folder_id, $folder_array)
		{
			if ($project_id > 0)
			{
				$cdb = new MySql;

				$error_count = 0;

				for($i = 0; $i < count($folder_array); $i++)
				{
					$sql = $cdb->select('SELECT projekt_id, folder_name FROM fom_folder WHERE folder_id='.$folder_array[$i]);
					$result = $cdb->fetch_array($sql);

					if ($project_id == $result['projekt_id'])
					{
						$sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$folder_id AND folder_name='".$result['folder_name']."' AND anzeigen='1'");
						$result = $cdb->fetch_array($sql);

						if (!isset($result['folder_id']) or empty($result['folder_id']))
						{
							//FIXME: Hier sollte ein Logeintrag rein
							if ($cdb->update('UPDATE fom_folder SET ob_folder='.$folder_id.' WHERE folder_id='.$folder_array[$i]))
							{
								$this->update_ob_folder($folder_id, $folder_array[$i]);
							}
							else
							{
								$error_count++;
							}
						}
						else
						{
							$error_count++;
						}
					}
					else
					{
						$error_count++;
					}
				}

				if ($error_count == 0)
				{
					$this->clear_cookie();
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		/**
		 * Erstellt Dateiverknuepfungen in einen anderen Ordner
		 * @param int $project_id
		 * @param int $folder_id
		 * @param array $file_array
		 * @return boole
		 */
		public function job_link_file($project_id, $folder_id, $file_array)
		{
			if ($project_id > 0 and $folder_id > 0)
			{
				$cdb = new MySql;
				$lj = new LinkJobs();

				$error_count = 0;

				for($i = 0; $i < count($file_array); $i++)
				{
					//Existiert die Datei
					$sql = $cdb->select('SELECT file_id, org_name FROM fom_files WHERE file_id='.$file_array[$i]." AND anzeigen='1'");
					$result = $cdb->fetch_array($sql);

					if (isset($result['file_id']) and !empty($result['file_id']))
					{
						//Keine Doppelten Links
						$chk_sql = $cdb->select("SELECT link_id FROM fom_link WHERE folder_id=$folder_id AND file_id=".$result['file_id']." AND anzeigen='1'");
						$chk_result = $cdb->fetch_array($chk_sql);

						if (!isset($chk_result['file_id']) or empty($chk_result['file_id']))
						{
							$file_server_id = $lj->get_fileserver_id($project_id);
							if (!$cdb->insert("INSERT INTO fom_link (folder_id, file_server_id, user_id, file_id, name, save_time, link_type) VALUES ($folder_id, $file_server_id, ".USER_ID.", ".$result['file_id'].", '".$result['org_name']."', '".date('YmdHis')."', 'INTERNAL')"))
							{
								$error_count++;
							}
						}
						else
						{
							$error_count++;
						}
					}
					else
					{
						$error_count++;
					}
				}

				if ($error_count == 0)
				{
					$this->clear_cookie();
					return true;
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
		 * Kopiert Dateien in einen anderen Ordner
		 * @param int $project_id
		 * @param int $folder_id
		 * @param array $file_array
		 * @return boole
		 */
		public function job_copy_file($project_id, $folder_id, $file_array)
		{
			if ($project_id > 0 and $folder_id > 0)
			{
				$cdb = new MySql;
				$gt = new Tree;

				$error_count = 0;

				for($i = 0; $i < count($file_array); $i++)
				{
					$sql = $cdb->select('SELECT t1.*, t2.pfad FROM fom_files t1
										LEFT JOIN fom_file_server t2 ON t1.file_server_id=t2.file_server_id
										WHERE t1.file_id='.$file_array[$i]);
					$result = $cdb->fetch_array($sql);

					//Keine Doppelten Dateien
					$chk_sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name='".$result['org_name']."' AND anzeigen='1'");
					$chk_result = $cdb->fetch_array($chk_sql);

					if (!isset($chk_result['file_id']) or empty($chk_result['file_id']))
					{
						//FIXME: Hier koennte auch ein FTP Server sein
						$source_pfad = $result['pfad'].$project_id.'/'.substr($result['save_time'],0,6).'/'.$result['save_name'];

						if (file_exists($source_pfad))
						{
							$file_name_ex = $gt->GetFileExtension($result['org_name']);

							//mit Dateierweiterung z.B. 'doc'
							if (!empty($file_name_ex))
							{
								$save_filename = $gt->GetNewFileName().'.'.strtolower($file_name_ex);
							}
							else
							{
								$save_filename = $gt->GetNewFileName();
							}

							$dest_pfad = $result['pfad'].$project_id.'/'.substr($result['save_time'],0,6).'/'.$save_filename;

							if (copy($source_pfad, $dest_pfad))
							{
								if ($cdb->insert("INSERT INTO fom_files (folder_id, file_server_id, user_id, org_name, save_name, md5_file, mime_type, file_size, save_time, bemerkungen, tagging, file_type, anzeigen) VALUES ($folder_id, ".$result['file_server_id'].", ".$result['user_id'].", '".$result['org_name']."', '$save_filename', '".$result['md5_file']."', '".$result['mime_type']."', ".$result['file_size'].", '".$result['save_time']."', '".$result['bemerkungen']."', '".$result['tagging']."', '".$result['file_type']."', '".$result['anzeigen']."')"))
								{
									//Suchindex erstellen
									if ($cdb->get_affected_rows() == 1)
									{
										$last_file_id = $cdb->get_last_insert_id();

										if ($last_file_id > 0)
										{
											$sql_sub = $cdb->select('SELECT word_id FROM fom_search_word_file WHERE file_id='.$file_array[$i]);
											while($sub_result = $cdb->fetch_array($sql_sub))
											{
												$cdb->insert('INSERT INTO fom_search_word_file (word_id, file_id) VALUES ('.$sub_result['word_id'].', '.$last_file_id.')');
											}

											//Subdateien Kopieren
											$sub_sql = $cdb->select('SELECT subfile_id FROM fom_sub_files WHERE file_id='.$file_array[$i]);
											while ($sub_result = $cdb->fetch_array($sub_sql))
											{
												//Alle Daten vom Subfile
												$sub_file_sql = $cdb->select('SELECT t1.*, t2.pfad FROM fom_files t1
																			LEFT JOIN fom_file_server t2 ON t1.file_server_id=t2.file_server_id
																			WHERE t1.file_id='.$sub_result['subfile_id']);
												$sub_file_result = $cdb->fetch_array($sub_file_sql);

												//Keine Doppelten Dateien
												$sub_file_chk_sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name='".$sub_file_result['org_name']."' AND anzeigen='1'");
												$sub_file_chk_result = $cdb->fetch_array($sub_file_chk_sql);

												if (!isset($sub_file_chk_result['file_id']) or empty($sub_file_chk_result['file_id']))
												{
													//FIXME: Hier koennte auch ein FTP Server sein
													$sub_file_source_pfad = $sub_file_result['pfad'].$project_id.'/'.substr($sub_file_result['save_time'],0,6).'/'.$sub_file_result['save_name'];

													if (file_exists($sub_file_source_pfad))
													{
														$sub_file_name_ex = $gt->GetFileExtension($sub_file_result['org_name']);

														//mit Dateierweiterung z.B. 'doc'
														if (!empty($sub_file_name_ex))
														{
															$sub_file_save_filename = $gt->GetNewFileName().'.'.strtolower($sub_file_name_ex);
														}
														else
														{
															$sub_file_save_filename = $gt->GetNewFileName();
														}

														$sub_file_dest_pfad = $sub_file_result['pfad'].$project_id.'/'.substr($sub_file_result['save_time'],0,6).'/'.$sub_file_save_filename;

														if (copy($sub_file_source_pfad, $sub_file_dest_pfad))
														{
															if ($cdb->insert("INSERT INTO fom_files (folder_id, file_server_id, user_id, org_name, save_name, md5_file, mime_type, file_size, save_time, bemerkungen, tagging, file_type, anzeigen) VALUES ($folder_id, ".$sub_file_result['file_server_id'].", ".$sub_file_result['user_id'].", '".$sub_file_result['org_name']."', '$sub_file_save_filename', '".$sub_file_result['md5_file']."', '".$sub_file_result['mime_type']."', ".$sub_file_result['file_size'].", '".$sub_file_result['save_time']."', '".$sub_file_result['bemerkungen']."', '".$sub_file_result['tagging']."', '".$sub_file_result['file_type']."', '".$sub_file_result['anzeigen']."')"))
															{
																//Suchindex erstellen
																if ($cdb->get_affected_rows() == 1)
																{
																	$sub_file_last_file_id = $cdb->get_last_insert_id();

																	if ($sub_file_last_file_id > 0)
																	{
																		if ($cdb->insert("INSERT INTO fom_sub_files (file_id, subfile_id) VALUES ($last_file_id ,$sub_file_last_file_id)"))
																		{
																			$sql_sub = $cdb->select('SELECT word_id FROM fom_search_word_file WHERE file_id='.$sub_file_result['file_id']);
																			while($sub_sw_result = $cdb->fetch_array($sql_sub))
																			{
																				$cdb->insert('INSERT INTO fom_search_word_file (word_id, file_id) VALUES ('.$sub_sw_result['word_id'].', '.$sub_file_last_file_id.')');
																			}
																		}
																		else
																		{
																			$error_count++;
																		}
																	}
																	else
																	{
																		$error_count++;
																	}
																}
																else
																{
																	$error_count++;
																}
															}
															else
															{
																$error_count++;
															}
														}
														else
														{
															$error_count++;
														}
													}
													else
													{
														$error_count++;
													}
												}
												else
												{
													$error_count++;
												}
											}
										}
										else
										{
											$error_count++;
										}
									}
									else
									{
										$error_count++;
									}
								}
								else
								{
									$error_count++;
								}
							}
							else
							{
								$error_count++;
							}
						}
						else
						{
							$error_count++;
						}
					}
					else
					{
						$error_count++;
					}
				}

				if ($error_count == 0)
				{
					$this->clear_cookie();
					return true;
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
		 * Kopiert ein Verzeichnis in einen anderen Ordner
		 * @param int $project_id
		 * @param int $folder_id
		 * @param array $folder_array
		 * @return boole
		 */
		public function job_copy_folder($project_id, $folder_id, $folder_array)
		{
			$cdb = new MySql;

			//Array mit allen Verzeichnissen die Kopiert werden sollen abarbeiten
			for($i = 0; $i < count($folder_array); $i++)
			{
				//Alle Unterverzeichnisse zu dem Ordner ermitteln
				$this->tmp_array = array();
				$this->get_sub_folder($folder_array[$i]);

				//Wird nur fuer erstdurchlauf benoetigt
				$count = 0;
				foreach($this->tmp_array as &$f_array)
				{
					//Erstdurchlauf Oberverzeichnis und ebene aus DB abfragen
					if ($count == 0)
					{
						if ($folder_id > 0)
						{
							$sql = $cdb->select('SELECT ebene FROM fom_folder WHERE folder_id='.$folder_id);
							$result = $cdb->fetch_array($sql);

							$f_array['new_ob_folder'] = $folder_id;
							$f_array['new_ebene'] = $result['ebene'] + 1;
						}
						else
						{
							$f_array['new_ob_folder'] = 0;
							$f_array['new_ebene'] = 0;
						}

						$sql = $cdb->select('SELECT folder_name, bemerkungen, anzeigen FROM fom_folder WHERE folder_id='.$f_array['org_folder_id']);
						$result = $cdb->fetch_array($sql);
					}
					//Alle Unterverzeichnisse erhalten Oberverzeichnis und Ebene aus dem Array $this->tmp_array
					else
					{
						$sql = $cdb->select('SELECT folder_name, bemerkungen, ob_folder FROM fom_folder WHERE folder_id='.$f_array['org_folder_id']);
						$result = $cdb->fetch_array($sql);

						$f_array['new_ob_folder'] = $this->tmp_array[$result['ob_folder']]['new_folder_id'];
						$f_array['new_ebene'] = $this->tmp_array[$result['ob_folder']]['new_ebene'] + 1;
					}

					$chk_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=".$f_array['new_ob_folder']." AND folder_name='".$result['folder_name']."' AND anzeigen='1'");
					$chk_result = $cdb->fetch_array($chk_sql);

					if (!isset($chk_result['folder_id']) or empty($chk_result['folder_id']))
					{
						if ($cdb->insert("INSERT INTO fom_folder (projekt_id, folder_name, bemerkungen, ob_folder, ebene) VALUES ($project_id, '".$result['folder_name']."', '".$result['bemerkungen']."', ".$f_array['new_ob_folder'].", ".$f_array['new_ebene'].")"))
						{
							if ($cdb->get_affected_rows() == 1)
							{
								$f_array['new_folder_id'] = $cdb->get_last_insert_id();

								//Dateiliste
								$file_array = $this->get_file_list($f_array['org_folder_id']);
								//Dateien Kopieren
								if (count($file_array) > 0)
								{
									if ($this->job_copy_file($project_id, $f_array['new_folder_id'], $file_array) === false)
									{
										return false;
									}
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
					$count++;
				}
			}
			$this->clear_cookie();
			return true;
		}

		private function update_ob_folder($ob_fid, $fid)
		{
			$cdb = new MySql;

			if ($ob_fid == 0)
			{
				$new_ebene = 0;
			}
			else
			{
				$sql = $cdb->select('SELECT ebene FROM fom_folder WHERE folder_id='.$ob_fid);
				$result = $cdb->fetch_array($sql);

				$new_ebene = $result['ebene'] + 1;
			}

			$cdb->update('UPDATE fom_folder SET ebene='.$new_ebene.' WHERE folder_id='.$fid);

			$sql = $cdb->select('SELECT folder_id FROM fom_folder WHERE ob_folder='.$fid);
			while($result = $cdb->fetch_array($sql))
			{
				$this->update_ob_folder($fid, $result['folder_id']);
			}
		}

		/**
		 * Entfernt Id aus den Cookies die bereits verwendet wurden
		 * @param string $type
		 * @param array $data_array
		 * @return void
		 */
		private function clear_cookie()
		{
			if (isset($_COOKIE['FOM_FileLink_string']) and !empty($_COOKIE['FOM_FileLink_string']))
			{
				$_COOKIE['FOM_FileLink_string'] = '';
			}

			if (isset($_COOKIE['FOM_FileMove_string']) and !empty($_COOKIE['FOM_FileMove_string']))
			{
				$_COOKIE['FOM_FileMove_string'] = '';
			}

			if (isset($_COOKIE['FOM_FileCopy_string']) and !empty($_COOKIE['FOM_FileCopy_string']))
			{
				$_COOKIE['FOM_FileCopy_string'] = '';
			}

			if (isset($_COOKIE['FOM_FolderMove_string']) and !empty($_COOKIE['FOM_FolderMove_string']))
			{
				$_COOKIE['FOM_FolderMove_string'] = '';
			}

			if (isset($_COOKIE['FOM_FolderCopy_string']) and !empty($_COOKIE['FOM_FolderCopy_string']))
			{
				$_COOKIE['FOM_FolderCopy_string'] = '';
			}
		}

		/**
		 * Erstellt ein array mit allen in einem Verzeichnis vorhandenen PRIMARY Dateien
		 * @param int $folder_id
		 * @return array
		 */
		private function get_file_list($folder_id)
		{
			$cdb = new MySql;

			$return_array = array();

			$sql = $cdb->select('SELECT file_id FROM fom_files WHERE folder_id='.$folder_id." AND file_type='PRIMARY'");
			while($result = $cdb->fetch_array($sql))
			{
				$return_array[] = $result['file_id'];
			}
			return $return_array;
		}

		/**
		 * Erstellt ein Array mit den Folder Id aller Unterverzeichnisse
		 * @param int $folder_id
		 * @return void
		 */
		private function get_sub_folder($folder_id)
		{
			$cdb = new MySql;

			$this->tmp_array[$folder_id] = array('org_folder_id' => $folder_id);

			$sql = $cdb->select('SELECT folder_id FROM fom_folder WHERE ob_folder='.$folder_id);
			while($result = $cdb->fetch_array($sql))
			{
				$this->get_sub_folder($result['folder_id']);
			}
		}
	}
?>