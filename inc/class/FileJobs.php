<?php
	/**
	 * provides several file management functions
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * provides several file management functions
	 * @package file-o-meter
	 * @subpackage class
	 */
	class FileJobs
	{
		public $error_array = array();
		public $setup_array = array();
		public $tmp_array = array();
		public $file_job_count = 0;

		/**
		 * Setzt individuelle Einstellungen
		 * @param array
		 * @return void
		 */
		public function set_setup($setup)
		{
			foreach($setup as $i => $v)
			{
				$this->setup_array[$i] = $v;
			}
		}

		/**
		 * Gibt Fehlermeldungen der einzelnen Funktionen zurueck
		 * @return array
		 */
		public function get_error()
		{
			return $this->error_array;
		}

		/**
		 * Traegt zu einer Datei A-Z Register Keywords ein
		 * @param array $sign_array
		 * @param array $word_array
		 * @param int $fileid_int
		 * @param boole $is_subfile
		 * @return boole
		 */
		public function insert_az_register_keys($sign_array, $word_array, $fileid_int, $is_subfile = false)
		{
			$cdb = new MySql();

			//sollte eingendlich immer gleich sein
			$count = max(count($sign_array), count($word_array));
			$insert_count = 0;

			if ($count > 0)
			{
				for ($i = 0; $i < $count; $i++)
				{
					//Anfangszeichen und Suchwort vorhanden
					if (isset($sign_array[$i]) and isset($word_array[$i]) and !empty($word_array[$i]))
					{
						$word = strtolower($word_array[$i]);
						$sign = strtolower($sign_array[$i]);

						$word_id = 0;
						$sql = $cdb->select("SELECT word_id FROM fom_search_word WHERE word='$word'");
						$result = $cdb->fetch_array($sql);

						if (isset($result['word_id']) and $result['word_id'] > 0)
						{
							$word_id = $result['word_id'];
						}
						else
						{
							if ($cdb->insert("INSERT INTO fom_search_word (word) VALUES ('$word')"))
							{
								$word_id = $cdb->get_last_insert_id();
							}
						}

						//wort gefunden
						if ($word_id > 0)
						{
							//anfangsbuchstaben verwenden
							if ($sign == 'empty')
							{
								$sign = substr($word, 0, 1);

							}
							elseif (empty($sign))
							{
								$sign = 0;
							}

							if ($is_subfile == false)
							{
								if ($cdb->insert("INSERT INTO fom_search_word_az_file (word_id, file_id, sign) VALUES ($word_id, $fileid_int, '$sign')"))
								{
									$insert_count++;
								}
							}
							else
							{
								if ($cdb->insert("INSERT INTO fom_search_word_az_file (word_id, sub_fileid, sign) VALUES ($word_id, $fileid_int, '$sign')"))
								{
									$insert_count++;
								}
							}
						}
					}
				}
			}

			if ($insert_count > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Traegt eine neue Datei in die DB ein und legt alle erforderlichen Jobs an
		 * @param string $file_savename
		 * @param string $md5_file
		 * @param string $file_iso_name
		 * @param string $file_typ
		 * @param string $file_size
		 * @param int $folder_id
		 * @param int $project_id
		 * @param string $comment
		 * @param int $start_time
		 * @param string $RETURN_TYPE ('bool', 'int') gibt an, ob true oder die ID der datei zurueckgeliefert werden sollen
		 * @return mixed
		 */
		public function insert_new_file($file_savename, $md5_file, $file_iso_name, $file_no_iso_name, $mime_typ, $file_size, $folder_id, $project_id, $comment, $start_time, $source_typ = 'upload', $search_string = '', $document_type_array = array(), $RETURN_TYPE = 'bool')
		{
			$cdb = new MySql;
			$mn = new MailNotification();

			$file_server_id = $this->get_fileserver_id($project_id);
			$save_time = date('YmdHis');

			$return_bool = false;	//return value fuer $RETURN_TYPE='bool'
			$return_int = 0;		//return value fuer $RETURN_TYPE='int'

			//Keine Doppelten Dateien
			$sql_chk = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name='$file_iso_name' AND anzeigen='1'");
			$result = $cdb->fetch_array($sql_chk);

			if (!isset($result['file_id']) or empty($result['file_id']))
			{
				if ($cdb->insert("INSERT INTO fom_files (folder_id, file_server_id, user_id, org_name, org_name_no_iso, save_name, md5_file, mime_type, file_size, save_time, bemerkungen, tagging) VALUES ($folder_id, $file_server_id, ".USER_ID.", '$file_iso_name', '$file_no_iso_name','$file_savename', '$md5_file', '$mime_typ', '$file_size', '$save_time', '$comment', '$search_string')"))
				{
					if ($cdb->get_affected_rows() == 1)
					{
						$last_insert_id = $cdb->get_last_insert_id();
						$this->tmp_array['new']['files'][] = $last_insert_id;

						$mn->log_trigger_events(0, $last_insert_id, 'file_add');

						//Nur beim Upload, bei einem Import dauert es sonst zu lange
						if ($source_typ == 'upload')
						{
							//Dokumententypen eintragen
							$this->insert_document_type($last_insert_id, $document_type_array);

							//Separate Suchbegriffe (Tagging)
							if (!empty($search_string))
							{
								$this->insert_file_tagging($search_string, $last_insert_id, $file_savename);
							}

							//Datei fuer einen Kopier- und Indexauftrag eintragen
							if ($cdb->insert("INSERT INTO fom_file_job_copy (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_index (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_tn (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true)
							{
								$current_time = time();
								//Zeit die zwischen dem Start des Uploadvorganges und jetzt liegt
								// Wenn mehr als 10 Sek. soll der Kopierauftrag vom Cronjob erledigt werden
								if ($current_time - $start_time > 10)
								{
									$return_bool = true;
									$return_int = $last_insert_id;
								}
								else
								{
									//Datei auf den jeweiligen Fileserver Kopieren
									$this->copy_to_fileserver($last_insert_id, $file_savename);
									$current_time = time();
									//Zeit die zwischen dem Start des Uploadvorganges und jetzt liegt
									// Wenn mehr als 10 Sek. soll der Indexierungsvorgang vom Cronjob erledigt werden
									if ($current_time - $start_time < 10 and $file_size < 1048576)
									{
										//Datei indizieren
										$this->create_file_index($last_insert_id, $file_savename);
									}

									$current_time = time();
									//noch nicht mehr als 10 sek vergangen und die datei ist kleiner als 1 MB
									if ($current_time - $start_time < 10 and $file_size < 1048576)
									{
										$this->create_thumbnail($last_insert_id, $file_savename);
									}
									$return_bool = true;
									$return_int = $last_insert_id;
								}
							}
							else
							{
								//Kopier- und Indexauftrag konnte nicht angelegt werden
								//Fileeintrag lueschen da der Download und Suche nie Funktionieren wuerde
								$cdb->delete('DELETE FROM fom_files WHERE file_id='.$last_insert_id.' LIMIT 1');
								$this->error_array[] = get_text(251, 'return');//The file could not be indexed!
								$return_bool = false;
								$return_int = 0;
							}
						}
						//Import
						elseif($source_typ == 'import')
						{
							//Datei fuer einen Indexauftrag eintragen
							if ($cdb->insert("INSERT INTO fom_file_job_index (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_tn (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true)
							{
								if ($this->copy_import_file($last_insert_id))
								{
									$return_bool = true;
									$return_int = $last_insert_id;
								}
								else
								{
									$return_bool = false;
									$return_int = 0;
								}
							}
							else
							{
								$return_bool = false;
								$return_int = 0;
							}
						}
					}
					else
					{
						$this->error_array[] = get_text(248, 'return');//The file could not be saved!
						$return_bool = false;
						$return_int = 0;
					}
				}
				else
				{
					$this->error_array[] = get_text(248, 'return');//The file could not be saved!
					$return_bool = false;
					$return_int = 0;
				}
			}
			else
			{
				$this->error_array[] = get_text(100, 'return');//The specified filename already exists!
				$return_bool = false;
				$return_int = 0;
			}

			if ($RETURN_TYPE == 'bool')
			{
				return $return_bool;
			}
			else
			{
				return $return_int;
			}
		}

	/**
		 * Traegt eine neue Datei in die DB ein und legt aller erforderlichen Jobs an
		 * @param string $file_savename
		 * @param string $md5_file
		 * @param string $file_iso_name
		 * @param string $file_typ
		 * @param string $file_size
		 * @param int $file_id
		 * @param int $folder_id
		 * @param int $project_id
		 * @param string $comment
		 * @param int $start_time
		 * @return boole
		 */
		public function insert_new_subfile($file_savename, $md5_file, $file_iso_name, $file_no_iso_name, $mime_typ, $file_size, $file_id, $folder_id, $project_id, $comment, $start_time, $search_string = '', $document_type_array = array())
		{
			$cdb = new MySql;
			$mn = new MailNotification();

			$file_server_id = $this->get_fileserver_id($project_id);
			$save_time = date('YmdHis');

			//Keine Doppelten Dateien
			$sql_chk = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name='$file_iso_name' AND anzeigen='1'");
			$result = $cdb->fetch_array($sql_chk);

			if (!isset($result['file_id']) or empty($result['file_id']))
			{
				if ($cdb->insert("INSERT INTO fom_files (folder_id, file_server_id, user_id, org_name, org_name_no_iso, save_name, md5_file, mime_type, file_size, save_time, bemerkungen, tagging, file_type) VALUES ($folder_id, $file_server_id, ".USER_ID.", '$file_iso_name', '$file_no_iso_name','$file_savename', '$md5_file', '$mime_typ', '$file_size', '$save_time', '$comment', '$search_string', 'SUB')"))
				{
					if ($cdb->get_affected_rows() == 1)
					{
						$last_insert_id = $cdb->get_last_insert_id();

						$mn->log_trigger_events(0, $last_insert_id, 'file_add');

						//Verknuepfung zwischen PRIMAY und SUB File erstellen
						if ($cdb->insert("INSERT INTO fom_sub_files (file_id, subfile_id) VALUES ($file_id, $last_insert_id)"))
						{
							$this->tmp_array['new']['files'][] = $last_insert_id;

							//Dokumententypen eintragen
							$this->insert_document_type($last_insert_id, $document_type_array);

							//Separate Suchbegriffe (Tagging)
							if (!empty($search_string))
							{
								$this->insert_file_tagging($search_string, $last_insert_id, $file_savename);
							}

							//Datei fuer einen Kopier- und Indexauftrag eintragen
							if ($cdb->insert("INSERT INTO fom_file_job_copy (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_index (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_tn (file_id, save_name, save_time, job_time) VALUES ($last_insert_id, '$file_savename', '$save_time', '$save_time')") === true)
							{
								$current_time = time();
								//Zeit die zwischen dem Start des Uploadvorganges und jetzt liegt
								// Wenn mehr als 10 Sek. soll der Kopierauftrag vom Cronjob erledigt werden
								if ($current_time - $start_time > 10)
								{
									return true;
								}
								else
								{
									//Datei auf den jeweiligen Fileserver Kopieren
									$this->copy_to_fileserver($last_insert_id, $file_savename);
									$current_time = time();
									//Zeit die zwischen dem Start des Uploadvorganges und jetzt liegt
									// Wenn mehr als 10 Sek. soll der Indexierungsvorgang vom Cronjob erledigt werden
									if ($current_time - $start_time < 10 and $file_size < 1048576)
									{
										//Datei indizieren
										$this->create_file_index($last_insert_id, $file_savename);
									}
									$current_time = time();
									//noch nicht mehr als 10 sek vergangen und die datei ist kleiner als 1 MB
									if ($current_time - $start_time < 10 and $file_size < 1048576)
									{
										$this->create_thumbnail($last_insert_id, $file_savename);
									}
									return true;
								}
							}
							else
							{
								//Kopier- und Indexauftrag konnte nicht angelegt werden
								//Fileeintrag loeschen da der Download und Suche nie Funktionieren wuerde
								$cdb->delete('DELETE FROM fom_files WHERE file_id='.$last_insert_id.' LIMIT 1');
								$this->error_array[] = get_text(251, 'return');//The file could not be indexed!
								return false;
							}
						}
						else
						{
							$this->error_array[] = get_text(248, 'return');//The file could not be saved!
							return false;
						}
					}
					else
					{
						$this->error_array[] = get_text(248, 'return');//The file could not be saved!
						return false;
					}
				}
				else
				{
					$this->error_array[] = get_text(248, 'return');//The file could not be saved!
					return false;
				}
			}
			else
			{
				$this->error_array[] = get_text(100, 'return');//The specified filename already exists!
				return false;
			}
		}

		/**
		 * Traegt eine neue Fileversion ein
		 *
		 */
		public function insert_fileversion($file_id, $file_savename, $md5_file, $file_iso_name, $file_no_iso_name, $mime_typ, $file_size, $start_time, $source_typ = 'upload', $search_string = '')
		{
			$cdb = new MySql;
			$mn = new MailNotification();

			$save_time = date('YmdHis');

			$sql = $cdb->select('SELECT * FROM fom_files WHERE file_id='.$file_id);
			$org_result = $cdb->fetch_array($sql);

			//Pruefen ob Datei-ID existiert
			//Pruefen ob die Datei nicht bereits waehrend des Importvorganges schon einmal bearbeitet wurde
			//Dies sollte nur passieren wenn eine automatische Namensanpssung aktiv ist und die ersten 30 Zeichen von mehr als einer Datei identisch ist.
			if ($org_result['file_id'] > 0)
			{
				if (!isset($this->tmp_array['new']['files']) or !in_array($file_id, $this->tmp_array['new']['files']))
				{
					if ($cdb->update("UPDATE fom_files SET
									user_id=".USER_ID.",
									org_name='$file_iso_name',
									org_name_no_iso='$file_no_iso_name',
									save_name='$file_savename',
									md5_file='$md5_file',
									mime_type='$mime_typ',
									file_size='$file_size',
									save_time='$save_time'
									WHERE file_id=$file_id"))
					{
						if ($cdb->get_affected_rows() == 1)
						{
							$mn->log_trigger_events(0, $file_id, 'file_add_version');

							//Alle Links Aktuelisieren
							$lj = new LinkJobs();
							$lj->refresh_internal_linkname($file_id, $file_iso_name);

							$this->tmp_array['new']['files'][] = $file_id;

							//Subversion in DB eintragen
							if ($cdb->insert("INSERT INTO fom_file_subversion (file_id, user_id, org_name, save_name, md5_file, mime_type, file_size, save_time, file_type) VALUES ($file_id, ".$org_result['user_id'].", '".$org_result['org_name']."', '".$org_result['save_name']."', '".$org_result['md5_file']."', '".$org_result['mime_type']."','".$org_result['file_size']."', '".$org_result['save_time']."', '".$org_result['file_type']."')"))
							{
								if ($cdb->get_affected_rows() == 1)
								{
									$sub_file_id = $cdb->get_last_insert_id();
									//Suchindex aendern
									$cdb->update("UPDATE fom_search_word_file SET
												file_id=0,
												sub_fileid=$sub_file_id
												WHERE file_id=$file_id");
								}
							}

							//Nur beim Upload, bei einem Import dauert es sonst zu lange
							if ($source_typ == 'upload')
							{
								//Datei fuer einen Kopier- und Indexauftrag eintragen
								if ($cdb->insert("INSERT INTO fom_file_job_copy (file_id, save_name, save_time, job_time) VALUES ($file_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_index (file_id, save_name, save_time, job_time) VALUES ($file_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_tn (file_id, save_name, save_time, job_time) VALUES ($file_id, '$file_savename', '$save_time', '$save_time')") === true)
								{
									$current_time = time();
									//Zeit die zwischen dem Start des Uploadvorganges und jetzt liegt
									// Wenn mehr als 10 Sek. soll der Kopierauftrag vom Cronjob erledigt werden
									if ($current_time - $start_time > 10)
									{
										return true;
									}
									else
									{
										//Datei auf den jeweiligen Fileserver Kopieren
										$this->copy_to_fileserver($file_id, $file_savename);

										$current_time = time();
										//Zeit die zwischen dem Start des Uploadvorganges und jetzt liegt
										// Wenn mehr als 10 Sek. soll der Indexierungsvorgang vom Cronjob erledigt werden
										if ($current_time - $start_time < 10 and $file_size < 1048576)
										{
											//Datei indizieren
											$this->create_file_index($file_id, $file_savename);
										}
										$current_time = time();
										//noch nicht mehr als 10 sek vergangen und die datei ist kleiner als 1 MB
										if ($current_time - $start_time < 10 and $file_size < 1048576)
										{
											$this->create_thumbnail($file_id, $file_savename);
										}
										return true;
									}
								}
								else
								{
									$this->error_array[] = get_text(251, 'return');//The file could not be indexed!
									return false;
								}
							}
							//Import
							elseif($source_typ == 'import')
							{
								//Datei fuer einen Indexauftrag eintragen
								if ($cdb->insert("INSERT INTO fom_file_job_index (file_id, save_name, save_time, job_time) VALUES ($file_id, '$file_savename', '$save_time', '$save_time')") === true and $cdb->insert("INSERT INTO fom_file_job_tn (file_id, save_name, save_time, job_time) VALUES ($file_id, '$file_savename', '$save_time', '$save_time')") === true)
								{
									if ($this->copy_import_file($file_id))
									{
										return true;
									}
									else
									{
										$this->error_array[] = get_text('error', 'return');//An error has occurred!
										return false;
									}
								}
								else
								{
									$this->error_array[] = get_text('error', 'return');//An error has occurred!
									return false;
								}
							}
						}
						else
						{
							$this->error_array[] = get_text(250, 'return');//The changes could not be saved!
							return false;
						}
					}
					else
					{
						$this->error_array[] = get_text(250, 'return');//The changes could not be saved!
						return false;
					}
				}
				else
				{
					$this->error_array[] = get_text(252, 'return');//The file was already imported!
					return false;
				}
			}
			else
			{
				$this->error_array[] = get_text(249, 'return');//The file could not be found!
				return false;
			}
		}

		/**
		 * Suchbegriffe zu einer Datein in den Index eintragen
		 * @param string $search_string
		 * @param int $file_id
		 * @param string $file_name
		 * @return void
		 */
		public function insert_file_tagging($search_string, $file_id, $file_name)
		{
			$rf = new ReadFile;

			$search_string = html_entity_decode($search_string, ENT_QUOTES);
			$search_string = strtolower($search_string);
			$word_array = $rf->clear_string($search_string);
			$rf->insert_file_word_array($word_array, $file_id, $file_name, 'tagging');
		}

		/**
		 * Gibt ein Array mit allen bis dahin Neu bzw. geaenderten Dateien zurueck
		 * @return array
		 */
		public function get_file_ids()
		{
			if (isset($this->tmp_array['new']['files']))
			{
				return $this->tmp_array['new']['files'];
			}
			else
			{
				return array();
			}
		}

		/**
		 * Kopiert Dateien aus dem Importverzeichnis an den Zielort
		 * @param int $file_id
		 * @return boole
		 */
		public function copy_import_file($file_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select("SELECT t1.org_name_no_iso, t1.save_name, t1.save_time,t2.projekt_id, t3.typ, t3.pfad FROM fom_files t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								LEFT JOIN fom_file_server t3 ON t2.projekt_id=t3.projekt_id
								WHERE t1.file_id=$file_id");
			$result = $cdb->fetch_array($sql);

			$source = $this->setup_array['import_folder'].$result['org_name_no_iso'];

			if (file_exists($source))
			{
				$fu = new FileUpload;
				if ($result['typ'] == 'local')
				{
					//Projektverzeichnis
					if (!file_exists($result['pfad'].$result['projekt_id'].'/'))
					{
						mkdir($result['pfad'].$result['projekt_id'].'/');
					}
					//Monatsverzeichnis
					if (!file_exists($result['pfad'].$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/'))
					{
						mkdir($result['pfad'].$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/');
					}

					$dest_index_job = $fu->setup_array['save_folder'].'index_job/'.$result['save_name'];
					$dest = $result['pfad'].$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/'.$result['save_name'];

					if (@copy($source, $dest))
					{
						@copy($source, $dest_index_job);

						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					//FIXME: FTP Klasse
				}
			}
			else
			{
				return false;
			}
		}

		/**
		 * Kopiert Dateien aus dem Temporaeren Verzeichnis auf den jeweiligen Fileserver
		 * @param int $file_id
		 * @param string $file_name
		 * @return boole
		 */
		public function copy_to_fileserver($file_id = 0, $file_name = '')
		{
			$cdb = new MySql;
			$fu = new FileUpload;

			$where_array = array();
			if ($file_id > 0)
			{
				$where_array[] = 't1.file_id='.$file_id;
			}
			if (!empty($file_name))
			{
				$where_array[] = "t1.save_name='$file_name'";
			}
			$where = '';
			for($i = 0; $i < count($where_array); $i++)
			{
				if (empty($where))
				{
					$where = 'WHERE '.$where_array[$i];
				}
				else
				{
					$where .= ' AND '.$where_array[$i];
				}
			}

			if (!empty($where))
			{
				$sql = $cdb->select('SELECT t1.file_id, t1.save_name, t1.save_time, t3.typ, t3.pfad, t3.setup FROM fom_file_job_copy t1
									LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
									LEFT JOIN fom_file_server t3 ON t2.file_server_id=t3.file_server_id
									'.$where);
				$result = $cdb->fetch_array($sql);

				//Lokales Kopieren
				if ($result['typ'] == 'local')
				{
					$project_id = $this->get_project_id($result['file_id']);

					if ($project_id > 0)
					{
						$source = $fu->setup_array['save_folder'].$result['save_name'];
						$dest_index_job = $fu->setup_array['save_folder'].'index_job/'.$result['save_name'];

						//Pruefen ob Projektverzeichnis existiert
						$dest = $result['pfad'].$project_id.'/';
						if (!file_exists($dest))
						{
							mkdir($dest);
						}

						//Pruefen ob Monatsverzeichnis existiert
						$dest .= substr($result['save_time'], 0, 6).'/';
						if (!file_exists($dest))
						{
							mkdir($dest);
						}

						$dest .= $result['save_name'];

						if (copy($source, $dest))
						{
							//$this->error_array[] = 'alles ok';
							//Datei zum Indizieren ins leseverzeichnis kopieren
							@copy($source, $dest_index_job);
							@unlink($source);
							//Copyauftrag loeschen
							$cdb->delete("DELETE FROM fom_file_job_copy WHERE file_id=".$result['file_id']." AND save_name='".$result['save_name']."'");
							$this->file_job_count++;
							return true;
						}
						else
						{
							//FIXME: fehlermeldung anpassen ... 'Fehlercode: xxx1 (kopieren fehlgeschlagen)';
							$this->error_array[] = get_text('error', 'return');//An error has occurred!
							return false;
						}
					}
					else
					{
						//FIXME: fehlermeldung anpassen ... 'Fehlercode: xxx2 (keine Projekt-ID)';
						$this->error_array[] = get_text('error', 'return');//An error has occurred!
						return false;
					}
				}
				else
				{
					//FIXME: Hier sollte dann das Kopieren per FTP rein
				}
			}
			else
			{
				$start_time = time();
				$error_count = 0;
				//soviele kopierauftraege durchfuehren wie moeglich. Max zeit 10 sek.
				$sql = $cdb->select('SELECT t1.file_id, t1.save_name, t1.save_time, t3.typ, t3.pfad, t3.setup FROM fom_file_job_copy t1
									LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
									LEFT JOIN fom_file_server t3 ON t2.file_server_id=t3.file_server_id
									ORDER BY t1.save_time ASC');
				while($result = $cdb->fetch_array($sql))
				{
					//Lokales Kopieren
					if ($result['typ'] == 'local')
					{
						$project_id = $this->get_project_id($result['file_id']);

						if ($project_id > 0)
						{
							$source = $fu->setup_array['save_folder'].$result['save_name'];
							$dest_index_job = $fu->setup_array['save_folder'].'index_job/'.$result['save_name'];

							//Pruefen ob Projektverzeichnis existiert
							$dest = $result['pfad'].$project_id.'/';
							if (!file_exists($dest))
							{
								mkdir($dest);
							}

							//Pruefen ob Monatsverzeichnis existiert
							$dest .= substr($result['save_time'], 0, 6).'/';
							if (!file_exists($dest))
							{
								mkdir($dest);
							}

							$dest .= $result['save_name'];

							if (@copy($source, $dest))
							{
								//Datei zum Indizieren ins leseverzeichnis kopieren
								@copy($source, $dest_index_job);

								@unlink($source);
								//Copyauftrag loeschen
								$cdb->delete("DELETE FROM fom_file_job_copy WHERE file_id=".$result['file_id']." AND save_name='".$result['save_name']."'");
							}
							else
							{
								//FIXME: fehlermeldung anpassen ... 'Fehlercode: xxx3 (kopieren fehlgeschlagen)';
								$this->error_array[] = get_text('error', 'return');//An error has occurred!
								$error_count++;
							}
						}
						else
						{
							//FIXME: fehlermeldung anpassen ... 'Fehlercode: xxx4 (keine Projekt-ID)';
							$this->error_array[] = get_text('error', 'return');//An error has occurred!
							$error_count++;
						}
					}
					else
					{
						//FIXME: Hier sollte dann das Kopieren per FTP rein
					}
					//Kopierauftraege nach 10 sek. beenden
					$end_time = time();

					if ($end_time - $start_time > 10)
					{
						break;
					}
				}

				if ($error_count == 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		/**
		 * Arbeitet einen/alle Thumbnailjobs ab
		 * @param int $file_id
		 * @param string $file_name
		 * @return void
		 */
		public function create_thumbnail($file_id = 0, $file_name = '')
		{
			$cdb = new MySql;
			$tn = new Thumbnail();

			if ($file_id > 0 and !empty($file_name))
			{
				$sql = $cdb->select("SELECT job_id FROM fom_file_job_tn WHERE file_id=$file_id AND save_name='$file_name'");
				$result = $cdb->fetch_array($sql);

				if ($result['job_id'] > 0)
				{
					$tn->create_thumbnail($result['job_id']);
					$this->file_job_count++;
				}
			}
			else
			{
				$start_time = time();
				//nicht mehr als 50 tn pro durchlauf
				$sql = $cdb->select("SELECT job_id FROM fom_file_job_tn ORDER BY save_time ASC");
				while($result = $cdb->fetch_array($sql))
				{
					$tn->create_thumbnail($result['job_id']);
					$this->file_job_count++;
					$current_time = time();
					if ($current_time - $start_time > 180)
					{
						break;
					}
				}
			}
		}

		/**
		 * Startet die Suchindexerstellung
		 * @param int $file_id
		 * @param string $file_name
		 * @return void
		 */
		public function create_file_index($file_id = 0, $file_name = '')
		{
			$cdb = new MySql;
			$rf = new ReadFile;

			if ($file_id > 0 and !empty($file_name))
			{
				$sql = $cdb->select("SELECT job_id FROM fom_file_job_index WHERE file_id=$file_id AND save_name='$file_name'");
				$result = $cdb->fetch_array($sql);

				if ($result['job_id'] > 0)
				{
					$rf->read_file($result['job_id']);
					$this->file_job_count++;
				}
			}
			else
			{
				$start_time = time();
				$sql = $cdb->select("SELECT job_id FROM fom_file_job_index ORDER BY save_time ASC");
				while($result = $cdb->fetch_array($sql))
				{
					$rf->read_file($result['job_id']);
					$this->file_job_count++;
					$current_time = time();
					if ($current_time - $start_time > 180)
					{
						break;
					}
				}
			}
		}

		/**
		 * Gibt die zugehoerige Projekt-ID zu einer Datei oder Verzeichnis aus
		 * @param int $file_id
		 * @param int $folder_id
		 * @return int
		 */
		public function get_project_id($file_id = 0, $folder_id = 0)
		{
			$cdb = new MySql;

			if ($file_id > 0)
			{
				$sql = $cdb->select('SELECT t1.folder_id, t2.projekt_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.file_id='.$file_id);
				$result = $cdb->fetch_array($sql);

				if ($result['projekt_id'] > 0)
				{
					return $result['projekt_id'];
				}
				else
				{
					return $this->get_project_id(0, $result['folder_id']);
				}
			}
			elseif($folder_id > 0)
			{
				$sql = $cdb->select('SELECT projekt_id FROM fom_folder WHERE folder_id='.$folder_id);
				$result = $cdb->fetch_array($sql);

				if ($result['projekt_id'] > 0)
				{
					return $result['projekt_id'];
				}
				else
				{
					return 0;
				}
			}
		}

		/**
		 * Gibt die Fileserver Id an auf dem die Datei gespeichert werden soll
		 * @param int $project_id
		 * @return int
		 */
		public function get_fileserver_id($project_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT file_server_id FROM fom_file_server WHERE projekt_id='.$project_id);
			$result = $cdb->fetch_array($sql);

			return $result['file_server_id'];
		}

		/**
		 * Entfernt alle Alten Dateien die aus irgendeinem Grund im tmp Verzeichnis verblieben sind
		 * @return void
		 */
		public function clear_tmp_folder($folder = '')
		{
			$unquenchable_array = array(FOM_ABS_PFAD.'files/tmp/index_job', FOM_ABS_PFAD.'files/tmp/unpack');
			if (empty($folder))
			{
				$folder = FOM_ABS_PFAD.'files/tmp/';
			}

			if ($dh = opendir($folder))
			{
				while (($f = readdir($dh)) !== false)
				{
					if (is_file($folder.$f) and $f != '.' and $f != '..')
					{
						//Datei aelter als 48h
						if (fileatime($folder.$f) < time() - 172800)
						{
							@unlink($folder.$f);
						}
					}
					elseif (is_dir($folder.$f) and $f != '.' and $f != '..')
					{
						$this->clear_tmp_folder($folder.$f.'/');

						if (!in_array($folder.$f, $unquenchable_array))
						{
							if ($this->folder_empty($folder.$f))
							{
								@rmdir($folder.$f);
							}
						}
					}
				}
				closedir($dh);
			}
		}

		/**
		 * Gibt an ob ein Verzeichnis leer ist
		 * @param string $folder
		 * @return boole
		 */
		private function folder_empty($folder)
		{
			$empty = true;
			if ($dh = opendir($folder))
			{
				while (($f = readdir($dh)) !== false)
				{
					if ((is_file($folder.$f) or is_dir($folder.$f)) and $f != '.' and $f != '..')
					{
						$empty = false;
						break;
					}
				}
				closedir($dh);
			}
			return $empty;
		}

		/**
		 * Loescht alle Indexjobs die aelter als 48h sind.
		 * Das sollten z.B. PDF Dokumente sein die nur bilder enthalten oder leer sind und sich daher nicht indizieren lassen
		 */
		public function clear_index_job()
		{
			$cdb = new MySql;

			$date = date('Ymd', time() - 172800);

			$cdb->delete("DELETE FROM fom_file_job_index WHERE LEFT(job_time, 6) < '$date'");
		}

		/**
		 * Loescht alle Thumbnailjobs die aelter als 48h sind.
		 */
		public function clear_thumbnail_job()
		{
			$cdb = new MySql;

			$date = date('Ymd', time() - 172800);

			$cdb->delete("DELETE FROM fom_file_job_tn WHERE LEFT(job_time, 6) < '$date'");
		}

		/**
		 * Traegt Dokumententypen ein
		 * @param int $file_id
		 * @param array $document_type_array
		 * @return void
		 */
		private function insert_document_type($file_id, $document_type_array)
		{
			if (is_array($document_type_array) and count($document_type_array) > 0 and $file_id > 0)
			{
				$cdb = new MySql;

				for ($i = 0; $i < count($document_type_array); $i++)
				{
					if ($document_type_array[$i] > 0)
					{
						$cdb->insert('INSERT INTO fom_document_type_file (document_type_id, file_id) VALUES ('.$document_type_array[$i].', '.$file_id.')');
					}
				}
			}
		}
	}
?>