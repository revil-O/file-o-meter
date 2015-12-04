<?php
	/**
	 * this file contains all actions for the folder-directory
	 * @package file-o-meter
	 * @subpackage folder
	 */

	if ($_POST['job_string'] == 'add_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['foldername_string']) or empty($_POST['foldername_string']))	{$pflichtfelder++;}
			if (!isset($_POST['pid_int']))													{$pflichtfelder++;}
			if (!isset($_POST['fid_int']))													{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				//Unterverzeichnis anlegen
				if ($_POST['fid_int'] > 0)
				{
					$sql_chk = $db->select('SELECT folder_id, projekt_id, ebene FROM fom_folder WHERE folder_id='.$_POST['fid_int']);
					$result = $db->fetch_array($sql_chk);

					if ($result['folder_id'] > 0)
					{
						//Keine Doppelten Verzeichnisse
						$sql_chk = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=".$_POST['fid_int']." AND folder_name='".$_POST['foldername_string']."' AND anzeigen='1'");
						$sub_result = $db->fetch_array($sql_chk);

						if (!isset($sub_result['folder_id']) or empty($sub_result['folder_id']))
						{
							$ebene = $result['ebene'] + 1;
							$sql = "INSERT INTO fom_folder (projekt_id, folder_name, bemerkungen, ob_folder, ebene) VALUES ('".$result['projekt_id']."', '".$_POST['foldername_string']."', '".$_POST['foldercomment_string']."', '".$result['folder_id']."', '$ebene')";
							$mn_projekt_id = $result['projekt_id'];
						}
						else
						{
							$meldung['error'][] = setError(get_text(99,'return'), WARNING, __LINE__);//The specified foldername already exists!
						}
					}
				}
				else
				{
					//Keine Doppelten Verzeichnisse
					$sql_chk = $cdb->select("SELECT folder_id FROM fom_folder WHERE projekt_id='".$_POST['pid_int']."' AND ob_folder=0 AND folder_name='".$_POST['foldername_string']."' AND anzeigen='1'");
					$sub_result = $db->fetch_array($sql_chk);

					if (!isset($sub_result['folder_id']) or empty($sub_result['folder_id']))
					{
						$sql = "INSERT INTO fom_folder (projekt_id, folder_name, bemerkungen, ob_folder, ebene) VALUES ('".$_POST['pid_int']."', '".$_POST['foldername_string']."', '".$_POST['foldercomment_string']."', 0, 0)";
						$mn_projekt_id = $_POST['pid_int'];
					}
					else
					{
						$meldung['error'][] = setError(get_text(99,'return'), WARNING, __LINE__);//The specified foldername already exists!
					}
				}
				if (isset($sql))
				{
					if ($db->insert($sql))
					{
						if ($db->get_affected_rows() == 1)
						{
							$mn->log_trigger_events($mn_projekt_id, $db->get_last_insert_id(), 'folder_add');

							$meldung['ok'][] = get_text(96,'return');//The dataset was created.
							$GLOBALS['FOM_VAR']['fileinc'] = '';
						}
						else
						{
							$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
						}
					}
					else
					{
						$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
					}
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'edit_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['foldername_string']) or empty($_POST['foldername_string']))	{$pflichtfelder++;}
			if (!isset($_POST['fid_int']))													{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				//Keine Doppelten Verzeichnisse
				$sql_chk = $cdb->select('SELECT projekt_id, folder_name, ob_folder FROM fom_folder WHERE folder_id='.$_POST['fid_int']);
				$result_dv = $cdb->fetch_array($sql_chk);

				$mn_projekt_id = $result_dv['projekt_id'];

				if ($result_dv['folder_name'] != $_POST['foldername_string'])
				{
					$sql_chk = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=".$result_dv['ob_folder']." AND folder_name='".$_POST['foldername_string']."'");
					$result = $db->fetch_array($sql_chk);

					if (!isset($result['folder_id']) or empty($result['folder_id']))
					{
						$chk_folder_name = true;
					}
					else
					{
						$chk_folder_name = false;
					}
				}
				else
				{
					$chk_folder_name = true;
				}

				if ($chk_folder_name === true)
				{
					if ($db->update("UPDATE fom_folder SET folder_name='".$_POST['foldername_string']."',
									bemerkungen='".$_POST['foldercomment_string']."'
									WHERE folder_id=".$_POST['fid_int']))
					{
						if ($result_dv['folder_name'] != $_POST['foldername_string'])
						{
							$mn->log_trigger_events($mn_projekt_id, $_POST['fid_int'], 'folder_edit', $result_dv['folder_name']);
						}
						else
						{
							$mn->log_trigger_events($mn_projekt_id, $_POST['fid_int'], 'folder_edit');
						}

						$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
						$GLOBALS['FOM_VAR']['fileinc'] = '';
					}
					else
					{
						$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
					}
				}
				else
				{
					$meldung['error'][] = setError(get_text(99,'return'), WARNING, __LINE__);//The specified foldername already exists!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'del_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fid_int']))
			{
				$ffd = new FileFolderDel();

				$sql_chk = $cdb->select('SELECT projekt_id FROM fom_folder WHERE folder_id='.$_POST['fid_int']);
				$result = $cdb->fetch_array($sql_chk);
				$mn_projekt_id = $result['projekt_id'];

				if ($ffd->folder_del($_POST['fid_int']))
				{
					$meldung['ok'][] = get_text(101,'return');//The folder was deleted.
					$GLOBALS['FOM_VAR']['fileinc'] = '';
					unset($_GET['pid_int']);
					unset($_GET['fid_int']);
					unset($_POST['fid_int']);
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'add_newfile')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fid_int']) and isset($_POST['pid_int']))
			{
				$start_time = time();
				//Uploadklasse
				$fu = new FileUpload;
				//Upload in ein tmp Verzeichnis
				$result = $fu->file_upload($_FILES['file']);

				//keine Fehler
				if (!isset($result['error']))
				{
					$fj = new FileJobs;
					//Datei in die DB eintragen
					//FIXME
					$file_id = $fj->insert_new_file($result['save_filename'], $result['md5_file'], $result['org_filename'], $result['org_filename_no_iso'], $result['file_mimetype'], $_FILES['file']['size'], $_POST['fid_int'], $_POST['pid_int'], $_POST['filecomment_string'], $start_time, 'upload', $_POST['filesearch_string'], $_POST['document_type'], 'int');
					if ($file_id > 0)
					{
						//Datei soll ins A-Z Register
						if (isset($_POST['az_register']) and $_POST['az_register'] == 1)
						{
							if (isset($_POST['az_sign_array']) and is_array($_POST['az_sign_array']) and count($_POST['az_sign_array']) > 0)
							{
								if (isset($_POST['az_search_array']) and is_array($_POST['az_search_array']) and count($_POST['az_search_array']) > 0)
								{
									if ($fj->insert_az_register_keys($_POST['az_sign_array'], $_POST['az_search_array'], $file_id))
									{
										$meldung['ok'][] = get_text(103,'return');//The file was successfully saved.
										$GLOBALS['FOM_VAR']['fileinc'] = '';
									}
								}
								else
								{
									$meldung['error'][] = get_text('error','return');//An error has occurred!
								}
							}
							else
							{
								$meldung['error'][] = get_text('error','return');//An error has occurred!
							}
						}
						else
						{
							$meldung['ok'][] = get_text(103,'return');//The file was successfully saved.
							$GLOBALS['FOM_VAR']['fileinc'] = '';
						}
					}
					else
					{
						$meldung['error'] = $fj->get_error();
					}
				}
				else
				{
					$meldung['error'][] = $result['error'];
				}
			}
			else
			{
				$meldung['error'][] = get_text('error','return');//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'multiupload')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fid_int']) and isset($_POST['pid_int']))
			{
				$start_time = time();

				$fj = new FileJobs;
				$fu = new FileUpload;

				$array_files = array();
				$result_counter_ok = 0;
				$result_counter_error = 0;

				if (isset($_POST['html5_uploader_count']) && $_POST['html5_uploader_count'] > 0)
				{
					$filecounter = $_POST['html5_uploader_count'];
				}
				else
				{
					$filecounter = 0;
				}

				if ($filecounter > 0)
				{
					for($i = 0; $i < $filecounter; $i++)
					{
						if (isset($_POST['html5_uploader_'.$i.'_tmpname']))
						{
							$source = TMP_UPLOAD_DIR.$_POST['html5_uploader_'.$i.'_tmpname'];

							if (is_file($source) && is_readable($source))
							{
								$array_files[] = array('name'=>$_POST['html5_uploader_'.$i.'_name'], 'tmp_name'=>$source, 'error'=>0);
							}
							else
							{
								$array_files[] = array('name'=>$_POST['html5_uploader_'.$i.'_name'], 'tmp_name'=>$source, 'error'=>6);//error-code willkürlich gewählt
							}
						}
						else
						{
							$array_files[] = array('name'=>'', 'tmp_name'=>'', 'error'=>5);//error-code willkürlich gewählt
						}
					}

					foreach($array_files as $fileindex => $filedetails)
					{
						if (isset($filedetails) && is_array($filedetails))
						{
							$result = $fu->multiupload($filedetails);

							//keine Fehler
							if (!isset($result['error']))
							{
								$file_id = $fj->insert_new_file($result['save_filename'], $result['md5_file'], $result['org_filename'], $result['org_filename_no_iso'], $result['file_mimetype'], $result['filesize'], $_POST['fid_int'], $_POST['pid_int'], '', $start_time, 'upload', '', '', 'int');

								if ($file_id > 0)
								{
									//Datei soll ins A-Z Register
									if (isset($_POST['az_register']) and $_POST['az_register'] == 1)
									{
										if (isset($_POST['az_sign_array']) and is_array($_POST['az_sign_array']) and count($_POST['az_sign_array']) > 0)
										{
											if (isset($_POST['az_search_array']) and is_array($_POST['az_search_array']) and count($_POST['az_search_array']) > 0)
											{
												if ($fj->insert_az_register_keys($_POST['az_sign_array'], $_POST['az_search_array'], $file_id))
												{
													$result_counter_ok++;
												}
											}
											else
											{
												$result_counter_error++;
											}
										}
										else
										{
											$result_counter_error++;
										}
									}
									else
									{
										$result_counter_ok++;
									}
								}
								else
								{
									$result_counter_error++;
								}
							}
							else
							{
								$result_counter_error++;
							}
						}
						else
						{
							$result_counter_error++;
						}
					}

					if ($filecounter == $result_counter_ok && $result_counter_error == 0)
					{
						$meldung['ok'][] = get_text(342,'return','decode_on',array('anzahl_ok'=>$result_counter_ok));//[var]anzahl_ok[/var] files were successfully saved.
					}
					else
					{
						$meldung['error'][] = get_text(343,'return','decode_on',array('anzahl_error'=>$result_counter_error));//[var]anzahl_error[/var] file(s) could not be saved!
					}

					$GLOBALS['FOM_VAR']['fileinc'] = '';
				}
			}
			else
			{
				$meldung['error'][] = get_text('error','return');//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'add_subfile')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fid_int']) and isset($_POST['pid_int']) and isset($_POST['fileid_int']))
			{
				$start_time = time();
				//Uploadklasse
				$fu = new FileUpload;
				//Upload in ein tmp Verzeichnis
				$result = $fu->file_upload($_FILES['file']);

				//keine Fehler
				if (!isset($result['error']))
				{
					$fj = new FileJobs;
					//Datei in die DB eintragen
					//FIXME
					if ($fj->insert_new_subfile($result['save_filename'], $result['md5_file'], $result['org_filename'], $result['org_filename_no_iso'], $result['file_mimetype'], $_FILES['file']['size'], $_POST['fileid_int'],$_POST['fid_int'], $_POST['pid_int'], $_POST['filecomment_string'], $start_time, $_POST['filesearch_string'], $_POST['document_type']) === true)
					{
						$meldung['ok'][] = get_text(103,'return');//The file was successfully saved.
						$GLOBALS['FOM_VAR']['fileinc'] = '';
					}
					else
					{
						$meldung['error'] = $fj->get_error();
					}
				}
				else
				{
					$meldung['error'][] = $result['error'];
				}
			}
			else
			{
				$meldung['error'][] = get_text('error','return');//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'edit_file')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if ($db->update("UPDATE fom_files SET bemerkungen='".$_POST['filecomment_string']."', tagging='".$_POST['filesearch_string']."' WHERE file_id=".$_POST['fileid_int']." LIMIT 1"))
			{
				$mn->log_trigger_events(0, $_POST['fileid_int'], 'file_edit');

				//alte Taggingeintrarge loeschen
				$cdb->delete("DELETE FROM fom_search_word_file WHERE file_id=".$_POST['fileid_int']." AND tagging='1'");

				//Alle Dokumententypen loeschen
				$cdb->delete('DELETE FROM fom_document_type_file WHERE file_id='.$_POST['fileid_int']);
				//Neue Dokumententypen eintragen
				if (isset($_POST['document_type']) and is_array($_POST['document_type']) and count($_POST['document_type']) > 0)
				{
					for ($i = 0; $i < count($_POST['document_type']); $i++)
					{
						if ($_POST['document_type'][$i] > 0)
						{
							$cdb->insert('INSERT INTO fom_document_type_file (document_type_id, file_id) VALUES ('.$_POST['document_type'][$i].', '.$_POST['fileid_int'].')');
						}
					}
				}

				$fj = new FileJobs();

				//Datei soll ins A-Z Register
				if (isset($_POST['az_register']) and $_POST['az_register'] == 1)
				{
					if (isset($_POST['az_sign_array']) and is_array($_POST['az_sign_array']) and count($_POST['az_sign_array']) > 0)
					{
						if (isset($_POST['az_search_array']) and is_array($_POST['az_search_array']) and count($_POST['az_search_array']) > 0)
						{
							$cdb->delete('DELETE FROM fom_search_word_az_file WHERE file_id='.$_POST['fileid_int']);

							$fj->insert_az_register_keys($_POST['az_sign_array'], $_POST['az_search_array'], $_POST['fileid_int']);
						}
					}
				}
				//Neue Taggings eintragen
				elseif (!empty($_POST['filesearch_string']))
				{
					$sql = $cdb->select('SELECT save_name FROM fom_files WHERE file_id='.$_POST['fileid_int']);
					$result = $cdb->fetch_array($sql);

					$fj->insert_file_tagging($_POST['filesearch_string'], $_POST['fileid_int'], $result['save_name']);
				}

				$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				$GLOBALS['FOM_VAR']['fileinc'] = '';
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'del_file')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fileid_int']) and !empty($_POST['fileid_int']))
			{
				$ffd = new FileFolderDel();
				if ($ffd->file_del($_POST['fileid_int']))
				{
					$meldung['ok'][] = get_text(104,'return');//Die Datei wurde geloescht.
					$GLOBALS['FOM_VAR']['fileinc'] = '';
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'add_fileversion')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fileid_int']))
			{
				$start_time = time();
				//Uploadklasse
				$fu = new FileUpload;
				//Upload in ein tmp Verzeichnis
				$result = $fu->file_upload($_FILES['file']);

				//keine Fehler
				if (!isset($result['error']))
				{
					$fj = new FileJobs;
					//Datei in die DB eintragen
					//FIXME
					if ($fj->insert_fileversion($_POST['fileid_int'], $result['save_filename'], $result['md5_file'], $result['org_filename'], $result['org_filename_no_iso'], $result['file_mimetype'], $_FILES['file']['size'], $start_time) === true)
					{
						$meldung['ok'][] = get_text(103,'return');//The file was successfully saved.
						$GLOBALS['FOM_VAR']['fileinc'] = '';
					}
					else
					{
						$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
					}
				}
				else
				{
					$meldung['error'][] = $result['error'];
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'paste_file_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$ffcm = new FileFolderCopyMove;

			//Sollte immer da sein
			if (isset($_POST['pid_int']))
			{
				$pid = $_POST['pid_int'];
			}
			else
			{
				$pid = 0;
			}
			//Kann nur fehlen wenn direkt auf der Hauptebene Kopiert- Verschoben wird
			if (isset($_POST['fid_int']))
			{
				$fid = $_POST['fid_int'];
			}
			else
			{
				$fid = 0;
			}

			$count_error = 0;

			//Dateien Verknuepfung
			if (isset($_POST['file_link']) and is_array($_POST['file_link']) and count($_POST['file_link']) > 0)
			{
				if (!$ffcm->job_link_file($pid, $fid, $_POST['file_link']))
				{
					$count_error++;
				}
			}

			//Dateien Kopieren
			if (isset($_POST['file_copy']) and is_array($_POST['file_copy']) and count($_POST['file_copy']) > 0)
			{
				if (!$ffcm->job_copy_file($pid, $fid, $_POST['file_copy']))
				{
					$count_error++;
				}
				else
				{
					for($i = 0; $i < count($_POST['file_copy']); $i++)
					{
						$mn->log_trigger_events($pid, $_POST['file_copy'][$i], 'file_copy');
					}
				}
			}

			//Dateien Verschieben
			if (isset($_POST['file_move']) and is_array($_POST['file_move']) and count($_POST['file_move']) > 0)
			{
				if (!$ffcm->job_move_file($pid, $fid, $_POST['file_move']))
				{
					$count_error++;
				}
				else
				{
					for($i = 0; $i < count($_POST['file_move']); $i++)
					{
						$mn->log_trigger_events($pid, $_POST['file_move'][$i], 'file_move');
					}
				}
			}

			//Ordner Kopieren
			if (isset($_POST['folder_copy']) and is_array($_POST['folder_copy']) and count($_POST['folder_copy']) > 0)
			{
				if (!$ffcm->job_copy_folder($pid, $fid, $_POST['folder_copy']))
				{
					$count_error++;
				}
				else
				{
					$mn->log_trigger_events($pid, $fid, 'folder_copy');
				}
			}

			//Ordner Verschieben
			if (isset($_POST['folder_move']) and is_array($_POST['folder_move']) and count($_POST['folder_move']) > 0)
			{
				if (!$ffcm->job_move_folder($pid, $fid, $_POST['folder_move']))
				{
					$count_error++;
				}
				else
				{
					$mn->log_trigger_events($pid, $fid, 'folder_move');
				}
			}

			if ($count_error == 0)
			{
				$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				$GLOBALS['FOM_VAR']['fileinc'] = '';
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif($_POST['job_string'] == 'import_data')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['folder']) or isset($_POST['files']))
			{
				//Datenimport
				$im = new Import;

				if (!isset($_POST['folder']) or !is_array($_POST['folder']))
				{
					$_POST['folder'] = array();
				}
				else
				{
					for ($i = 0; $i < count($_POST['folder']); $i++)
					{
						$_POST['folder'][$i] = html_entity_decode($_POST['folder'][$i], ENT_QUOTES, 'UTF-8');
					}
				}

				if (!isset($_POST['files']) or !is_array($_POST['files']))
				{
					$_POST['files'] = array();
				}
				else
				{
					for ($i = 0; $i < count($_POST['files']); $i++)
					{
						$_POST['files'][$i] = html_entity_decode($_POST['files'][$i], ENT_QUOTES, 'UTF-8');
					}
				}

				//Automatische Namensanpassung
				if (isset($_POST['rename']) and $_POST['rename'] == 1)
				{
					$rename = true;
				}
				else
				{
					$rename = false;
				}

				$v_setup = array();
				//Vorhandene Dateien in Version aendern
				if (isset($_POST['add_version_int']) and $_POST['add_version_int'] == 1)
				{
					$v_setup['add_version'] = true;
				}
				else
				{
					$v_setup['add_version'] = false;
				}

				//Nicht vorhandene Dateien Verzeichnisse entfernen
				if (isset($_POST['del_file_folder_int']) and $_POST['del_file_folder_int'] == 1)
				{
					$v_setup['del_file_folder'] = true;
				}
				else
				{
					$v_setup['del_file_folder'] = false;
				}

				//Import Starten
				$result = $im->start_import($_POST['pid_int'], $_POST['fid_int'], $rename, $_POST['folder'], $_POST['files'], $v_setup);

				//eventuelle Fehlermeldungen ausgeben
				if (isset($result['error']) and count($result['error']) > 0)
				{
					for($i = 0; $i < count($result['error']); $i++)
					{
						$meldung['error'][] = $result['error'][$i];
					}
				}
				else
				{
					$meldung['ok'][] = get_text(107,'return');//Data import successfully finished.
				}

				//ENDE
				$GLOBALS['FOM_VAR']['fileinc'] = '';
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif($_POST['job_string'] == 'export_data')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['setup']) and count($_POST['setup']) > 0 and (isset($_POST['pid_int']) or isset($_POST['fid_int'])))
			{
				$ep = new Export;

				$result = $ep->export_data($_POST['fid_int'], $_POST['pid_int'], $_POST['setup']);

				if ($result === true)
				{
					$meldung['ok'][] = get_text(108,'return');//Data export successfully finished.
				}
				else
				{
					$meldung['error']= $result;
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		//ENDE
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	//Downloadlink erstellen
	elseif($_POST['job_string'] == 'add_download')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['version_int']) and isset($_POST['fileid_int']) and (isset($_POST['date_nolimit_int']) or isset($_POST['date_string'])))
			{
				$dl = new Download;
				$cal = new Calendar;

				if ($_POST['version_int'] == 1)
				{
					$version = 1;
				}
				else
				{
					$version = 0;
				}

				$chk_date_boole = false;
				if (isset($_POST['date_nolimit_int']) and $_POST['date_nolimit_int'] == 1)
				{
					//FIXME: Max Unixtimestamp hier sollte man eine bessere loesung finden
					$expire_date = '20380118235959';
					$chk_date_boole = true;
				}
				elseif (isset($_POST['date_string']) and !empty($_POST['date_string']))
				{
					$iso_date = $cal->format_date($_POST['date_string'], 'ISO');
					$iso_date = $cal->check_iso_date($iso_date);

					if ($iso_date != '0000-00-00')
					{
						$expire_date = str_replace('-', '', $iso_date).'235959';
						$chk_date_boole = true;
					}
				}

				if ($chk_date_boole === true)
				{
					$result = $dl->insert_download($_POST['fileid_int'], $expire_date, $version, '0');

					//Eintragen erfolgreich
					if ($result['result'] == true)
					{
						$meldung['ok'][] = get_text(109,'return').' <input type="text" name="message[]" value="'.$result['download'].'" readonly="readonly" class="ipt_200" />';//The following downloadlink was created:
					}
					else
					{
						$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
					}
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		//ENDE
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	//Datei auschecken
	elseif($_POST['job_string'] == 'checkout_file')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fileid_int']) and $_POST['fileid_int'] > 0)
			{
				if ($cdb->insert('INSERT INTO fom_file_lock (file_id, user_id) VALUES ('.$_POST['fileid_int'].', '.USER_ID.')'))
				{
					$mn->log_trigger_events(0, $_POST['fileid_int'], 'file_checkout');

					$meldung['ok'][] = get_text(105,'return');//The file was checked out.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		//ENDE
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	//Datei einchecken
	elseif($_POST['job_string'] == 'checkin_file')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['fileid_int']) and $_POST['fileid_int'] > 0)
			{
				if ($cdb->delete('DELETE FROM fom_file_lock WHERE file_id='.$_POST['fileid_int']))
				{
					$mn->log_trigger_events(0, $_POST['fileid_int'], 'file_checkin');

					$meldung['ok'][] = get_text(106,'return');//The file was checked in.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		//ENDE
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	//zugriffssteuerung bearbeiten
	elseif($_POST['job_string'] == 'edit_as')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['ugid_int']) or empty($_POST['ugid_int'])){$pflichtfelder++;}
			if (!isset($_POST['access_array'])){$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if (isset($_POST['']))
				//errorzaehler
				$access_error_count = 0;

				$post_array = array();

				//Verzeichnisbezogene Zugriffsmoelichkeiten
				if (isset($_POST['folder']))
				{
					$post_array['folder'] = $_POST['folder'];
				}

				$access_array = $ac->verify_access($_POST['access_array']);

				//Zugriffsrechte fuer einen User eintragen
				if (isset($_POST['userid_int']) and !empty($_POST['userid_int']))
				{
					$userid_int = $_POST['userid_int'];
					$ugid_int = 0;
				}
				//zugriffsrechte fuer eine Benutzergruppe eintragen
				else
				{
					$userid_int = 0;
					$ugid_int = $_POST['ugid_int'];
				}

				//Zugriffsrechte eintragen
				if ($ac->insert($_POST['access_type_string'], $_POST['access_type_id'], $userid_int, $ugid_int, $access_array))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}

			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		//ENDE
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	//Externen Link anlegen
	elseif ($_POST['job_string'] == 'add_newlink')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$error_count = 0;
			if (!isset($_POST['fid_int']) or empty($_POST['fid_int']))						{$error_count++;}
			if (!isset($_POST['pid_int']) or empty($_POST['pid_int']))						{$error_count++;}
			if (!isset($_POST['link_string']) or empty($_POST['link_string']))				{$error_count++;}
			if (!isset($_POST['protokoll_string']) or empty($_POST['protokoll_string']))	{$error_count++;}

			//keine Fehler
			if ($error_count == 0)
			{
				$lj = new LinkJobs();

				//Datei in die DB eintragen
				if ($lj->insert_link($_POST['fid_int'], $_POST['pid_int'], $_POST['link_string'], $_POST['protokoll_string'], $_POST['link_name_string'], $_POST['tagging_string'], $_POST['linkcomment_string']))
				{
					$link_id = $lj->get_last_insert_link_id();

					if ($link_id > 0)
					{
						//Link soll ins A-Z Register
						if (isset($_POST['az_register']) and $_POST['az_register'] == 1)
						{
							if (isset($_POST['az_sign_array']) and is_array($_POST['az_sign_array']) and count($_POST['az_sign_array']) > 0)
							{
								if (isset($_POST['az_search_array']) and is_array($_POST['az_search_array']) and count($_POST['az_search_array']) > 0)
								{
									if ($lj->insert_az_link_register_keys($_POST['az_sign_array'], $_POST['az_search_array'], $link_id))
									{
										$meldung['ok'][] = get_text(298,'return');//Der Link wurde gespeichert.
										$GLOBALS['FOM_VAR']['fileinc'] = '';
									}
								}
								else
								{
									$meldung['error'][] = get_text('error','return');//An error has occurred!
								}
							}
							else
							{
								$meldung['error'][] = get_text('error','return');//An error has occurred!
							}
						}
						else
						{
							$meldung['ok'][] = get_text(298,'return');//Der Link wurde gespeichert.
							$GLOBALS['FOM_VAR']['fileinc'] = '';
						}
					}
					else
					{
						$meldung['error'][] = get_text('error','return');//An error has occurred!
					}
				}
				else
				{
					$meldung['error'] = $lj->get_error();
				}
			}
			else
			{
				$meldung['error'][] = get_text('error','return');//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	//Externen Link bearbeiten
	elseif ($_POST['job_string'] == 'edit_link')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$error_count = 0;
			if (!isset($_POST['linkid_int']) or empty($_POST['linkid_int']))				{$error_count++;}
			if (!isset($_POST['link_string']) or empty($_POST['link_string']))				{$error_count++;}
			if (!isset($_POST['protokoll_string']) or empty($_POST['protokoll_string']))	{$error_count++;}

			//keine Fehler
			if ($error_count == 0)
			{
				$lj = new LinkJobs();

				//Datei in die DB eintragen
				if ($lj->edit_link($_POST['linkid_int'], $_POST['link_string'], $_POST['protokoll_string'], $_POST['link_name_string'], $_POST['tagging_string'], $_POST['linkcomment_string']))
				{
					//Link soll ins A-Z Register
					if (isset($_POST['az_register']) and $_POST['az_register'] == 1)
					{
						if (isset($_POST['az_sign_array']) and is_array($_POST['az_sign_array']) and count($_POST['az_sign_array']) > 0)
						{
							if (isset($_POST['az_search_array']) and is_array($_POST['az_search_array']) and count($_POST['az_search_array']) > 0)
							{
								$cdb->delete('DELETE FROM fom_search_word_az_link WHERE link_id='.$_POST['linkid_int']);

								$lj->insert_az_link_register_keys($_POST['az_sign_array'], $_POST['az_search_array'], $_POST['linkid_int']);
							}
						}
					}

					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
					$GLOBALS['FOM_VAR']['fileinc'] = '';
				}
				else
				{
					$meldung['error'] = $lj->get_error();
				}
			}
			else
			{
				$meldung['error'][] = get_text('error','return');//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	//Externen Link Loeschen
	elseif ($_POST['job_string'] == 'del_link')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['linkid_int']))
			{
				if ($db->update("UPDATE fom_link SET anzeigen='0' WHERE link_id=".$_POST['linkid_int']." LIMIT 1"))
				{
					$mn->log_trigger_events(0, $_POST['linkid_int'], 'link_del');

					$meldung['ok'][] = get_text(299,'return');//Der Link wurde geloescht.
					$GLOBALS['FOM_VAR']['fileinc'] = '';
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	//benutzerkonto bearbeiten
	elseif ($_POST['job_string'] == 'edit_useraccount')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{

			$pflichtfelder = 0;
			if (!isset($_POST['uid_int']) or $_POST['uid_int'] == 0)				{$pflichtfelder++;}
			if (!isset($_POST['language_int']) or empty($_POST['language_int']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$error_counter = 0;
				$cp = new CryptPw;

				$sql_pw = $db->select("SELECT pw FROM fom_user WHERE user_id=".$_POST['uid_int']);
				$result_pw = $db->fetch_array($sql_pw);


				if (isset($_POST['change_pw']) and $_POST['change_pw'] == 'j')
				{
					if (isset($_POST['current_pw_string']) and !empty($_POST['current_pw_string']) and $cp->encode_pw($_POST['current_pw_string']) == $result_pw['pw'] and isset($_POST['pw_string']) and !empty($_POST['pw_string']) && $_POST['pw_string'] == $_POST['pw2_string'])
					{
						$pw = "pw='".$cp->encode_pw($_POST['pw_string'])."',";
						$current_pw = " AND pw='".$cp->encode_pw($_POST['current_pw_string'])."'";
					}
					else
					{
						$pw = '';
						$current_pw = '';

						$error_counter++;
					}
				}
				else
				{
					$pw = '';
					$current_pw = '';
				}

				if (!$db->update("UPDATE fom_user SET
								$pw
								language_id='".$_POST['language_int']."'
								WHERE user_id=".$_POST['uid_int']."$current_pw"))
				{
					$error_counter++;
				}

				if ($error_counter == 0)
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}

			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}

		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'edit_mn')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['pid_int']) or $_POST['pid_int'] == 0)	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$trigger_array = array();
				foreach ($mn->trigger_array as $index => $txt)
				{
					if (isset($_POST[$index]) and $_POST[$index] == 1)
					{
						$trigger_array[$index] = 1;
					}
					else
					{
						$trigger_array[$index] = 0;
					}
				}

				if ($mn->update_trigger_events($_POST['pid_int'], $trigger_array))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
?>