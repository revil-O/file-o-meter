<?php
	/**
	 * Klasse zum Loeschen und Ausblenden von Verzeichnisen und Dateien
	 * @author Soeren Pieper
	 *
	 */
	class FileFolderDel
	{
		/**
		 * Datenbankzugriff
		 * @var object
		 */
		private $cdb;

		/**
		 * Fehlerspeicher
		 * @var boole
		 */
		private $error = false;

		/**
		 * Speichert tmp Daten zwischen
		 * @var array
		 */
		private $tmp_data = array();

		/**
		 * Datenbankzugriff
		 * @return void
		 */
		public function __construct()
		{
			$this->cdb = new MySql();
		}

		public function project_kill($project_id)
		{
			//Alle Verzeichnisse inkl inhalt ausblenden
			$sql = $sql = $this->cdb->select("SELECT folder_id FROM fom_folder WHERE projekt_id=$project_id");
			while ($result =  $this->cdb->fetch_array($sql))
			{
				$this->folder_del($result['folder_id']);
			}

			//Alle ausgeblendeten Verzeichnisse inkl inhalt loeschen
			$this->folder_kill($project_id);

			$sql = $sql = $this->cdb->select("SELECT * FROM fom_file_server WHERE projekt_id=$project_id");
			$result = $this->cdb->fetch_array($sql);

			//Uploadverzeichnis vom Server löschen
			if (file_exists($result['pfad'].$project_id.'/'))
			{
				$this->project_folder_kill($result['pfad'].$project_id.'/');
				@rmdir($result['pfad'].$project_id.'/');
			}

			$this->cdb->delete("DELETE FROM fom_file_server WHERE projekt_id=$project_id");
			$this->cdb->delete("DELETE FROM fom_projekte WHERE projekt_id=$project_id");
			//Folder aus Zugriffssteuerung entfernen
			$this->cdb->delete("DELETE FROM fom_access WHERE type='PROJECT' AND id=".$project_id);
		}

		/**
		 * Loescht alle Verzeichnisse und Dateien zu einem Projekt von der Festplatte
		 * @param string $pfad
		 * @return void
		 */
		private function project_folder_kill($pfad)
		{
			if (!empty($pfad))
			{
				if ($dh = opendir($pfad))
				{
					while (($f = readdir($dh)) !== false)
					{
						if (is_file($pfad.$f) and $f != '.' and $f != '..')
						{
							@unlink($pfad.$f);
						}
						elseif (is_dir($pfad.$f) and $f != '.' and $f != '..')
						{
							$this->project_folder_kill($pfad.$f.'/');

							@rmdir($pfad.$f);
						}
					}
					closedir($dh);
				}
			}
		}

		/**
		 * Verzeichnisse inkl inhalt loeschen. Es werden nur Verzeichnisse geloescht die ausgeblendet sind.
		 * @param int $project_id
		 * @param int $folder_id
		 * @return void
		 */
		public function folder_kill($project_id = 0, $folder_id = 0)
		{
			//Alle ausgeblendeten Verzeichnisse aus einem Projekt loeschen
			if ($project_id > 0)
			{
				$sql = $sql = $this->cdb->select("SELECT folder_id, projekt_id FROM fom_folder WHERE projekt_id=$project_id AND anzeigen='0'");
				while($result = $this->cdb->fetch_array($sql))
				{
					$this->file_kill($result['folder_id'], $result['projekt_id']);
					$this->link_kill($result['folder_id']);

					$this->cdb->delete('DELETE FROM fom_folder WHERE folder_id='.$result['folder_id'].' LIMIT 1');

					//Folder aus Zugriffssteuerung entfernen
					$this->cdb->delete("DELETE FROM fom_access WHERE type='FOLDER' AND id=".$result['folder_id']);
				}
			}

			if ($folder_id > 0)
			{
				$sql = $this->cdb->select("SELECT projekt_id FROM fom_folder WHERE folder_id=$folder_id AND anzeigen='0'");
				$result = $this->cdb->fetch_array($sql);

				if ($result['projekt_id'] > 0)
				{
					$this->file_kill($folder_id, $result['projekt_id']);
					$this->link_kill($folder_id);

					//alle unterverzeichnisse loeschen
					$sql = $this->cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$folder_id AND anzeigen='0'");
					while($result = $this->cdb->fetch_array($sql))
					{
						$this->folder_kill(0, $result['folder_id']);
					}
				}
				$this->cdb->delete("DELETE FROM fom_folder WHERE folder_id=$folder_id AND anzeigen='0'");
				//Folder aus Zugriffssteuerung entfernen
				$this->cdb->delete("DELETE FROM fom_access WHERE type='FOLDER' AND id=".$folder_id);
			}
		}

		/**
		 * Links loeschen
		 * @param int $folder
		 * @param int $file_id
		 * @return void
		 */
		public function link_kill($folder, $file_id = 0, $link_id = 0)
		{
			if ($folder > 0)
			{
				$sql = $this->cdb->select("SELECT link_id FROM fom_link WHERE folder_id=$folder AND anzeigen='0'");
				while ($result = $this->cdb->fetch_array($sql))
				{
					//eventuell vorhandene indexeintraege entfernen
					$this->cdb->delete('DELETE FROM fom_search_word_link WHERE link_id='.$result['link_id']);

					//link aus Zugriffssteuerung entfernen
					$this->cdb->delete("DELETE FROM fom_access WHERE type='LINK' AND id=".$result['link_id']);
				}

				//Links aus DB entfernen
				$this->cdb->delete("DELETE FROM fom_link WHERE folder_id=$folder AND anzeigen='0'");
			}
			elseif ($file_id > 0)
			{
				$sql = $this->cdb->select("SELECT link_id FROM fom_link WHERE file_id=$file_id AND anzeigen='0'");
				while ($result = $this->cdb->fetch_array($sql))
				{
					//eventuell vorhandene indexeintraege entfernen
					$this->cdb->delete('DELETE FROM fom_search_word_link WHERE link_id='.$result['link_id']);

					//link aus Zugriffssteuerung entfernen
					$this->cdb->delete("DELETE FROM fom_access WHERE type='LINK' AND id=".$result['link_id']);
				}

				//Links aus DB entfernen
				$this->cdb->delete("DELETE FROM fom_link WHERE file_id=$file_id AND anzeigen='0'");
			}
			elseif ($link_id > 0)
			{
				//eventuell vorhandene indexeintraege entfernen
				$this->cdb->delete('DELETE FROM fom_search_word_link WHERE link_id='.$link_id);
				//Links aus DB entfernen
				$this->cdb->delete("DELETE FROM fom_link WHERE link_id=$link_id AND anzeigen='0'");

				//link aus Zugriffssteuerung entfernen
				$this->cdb->delete("DELETE FROM fom_access WHERE type='LINK' AND id=".$link_id);
			}
		}

		/**
		 * Dateien loeschen
		 * @param int $folder
		 * @param int $project_id
		 * @param int $file_id
		 * @return void
		 */
		public function file_kill($folder, $project_id, $file_id = 0)
		{
			$tn = new Thumbnail();

			$file_array = array();

			if ($file_id > 0)
			{
				$sql = $this->cdb->select("SELECT t1.file_id, t1.save_name, t1.save_time, t2.projekt_id FROM fom_files t1
											LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
											WHERE t1.file_id=$file_id AND t1.anzeigen='0'");
				$result = $this->cdb->fetch_array($sql);

				if (isset($result['file_id']) and $result['file_id'] > 0)
				{
					$file_array[] = $result;
				}
			}
			elseif ($folder > 0 and $project_id > 0)
			{
				$sql = $this->cdb->select("SELECT file_id, save_name, save_time FROM fom_files WHERE folder_id=$folder AND anzeigen='0'");
				while ($result = $this->cdb->fetch_array($sql))
				{
					$result['projekt_id'] = $project_id;
					$file_array[] = $result;
				}
			}

			if (!empty($file_array))
			{
				$project_array = array();
				foreach ($file_array as $file_data)
				{
					if (!isset($project_array[$file_data['projekt_id']]))
					{
						$sql = $this->cdb->select('SELECT typ, pfad FROM fom_file_server WHERE projekt_id='.$file_data['projekt_id']);
						$result = $this->cdb->fetch_array($sql);

						$project_array[$file_data['projekt_id']] = $result;
					}

					if (isset($project_array[$file_data['projekt_id']]['typ']) and $project_array[$file_data['projekt_id']]['typ'] == 'local')
					{
						if (substr($project_array[$file_data['projekt_id']]['pfad'], -1) == '/')
						{
							$pfad = $project_array[$file_data['projekt_id']]['pfad'].$file_data['projekt_id'].'/'.substr($file_data['save_time'], 0, 6).'/'.$file_data['save_name'];
						}
						else
						{
							$pfad = $project_array[$file_data['projekt_id']]['pfad'].$file_data['projekt_id']."\\".substr($file_data['save_time'], 0, 6)."\\".$file_data['save_name'];
						}

						if (file_exists($pfad))
						{
							if (@unlink($pfad))
							{
								//eventuell vorhandene thumbnails entfernen
								$tn_result = $tn->search_thumbnail($file_data['file_id']);
								if ($tn_result !== false)
								{
									@unlink($tn_result['pfad'].$tn_result['name']);
								}

								//eventuell vorhandene doctyps jobs entfernen
								$this->cdb->delete('DELETE FROM fom_document_type_file WHERE file_id='.$file_data['file_id']);

								//eventuell vorhandene downloadlinks entfernen
								$this->cdb->delete('DELETE FROM fom_download WHERE file_id='.$file_data['file_id']);

								//eventuell vorhandene file copy jobs entfernen
								$this->cdb->delete('DELETE FROM fom_file_job_copy WHERE file_id='.$file_data['file_id']);

								//eventuell vorhandene file index jobs entfernen
								$this->cdb->delete('DELETE FROM fom_file_job_index WHERE file_id='.$file_data['file_id']);

								//eventuell vorhandene file thumbnail jobs entfernen
								$this->cdb->delete('DELETE FROM fom_file_job_tn WHERE file_id='.$file_data['file_id']);

								//eventuell vorhandene filelocks entfernen
								$this->cdb->delete('DELETE FROM fom_file_lock WHERE file_id='.$file_data['file_id']);

								//eventuell vorhandene filelversionen entfernen
								$this->file_version_kill($file_data['file_id'], $file_data['projekt_id']);

								//eventuell vorhandene interne links entfernen
								$this->link_kill(0, $file_data['file_id']);

								//eventuell vorhandene indexeintraege entfernen
								$this->cdb->delete('DELETE FROM fom_search_word_az_file WHERE file_id='.$file_data['file_id']);
								$this->cdb->delete('DELETE FROM fom_search_word_file WHERE file_id='.$file_data['file_id']);

								//Subdateien entfernen
								$sql = $this->cdb->select('SELECT subfile_id FROM fom_sub_files WHERE file_id='.$file_data['file_id']);
								while ($result = $this->cdb->fetch_array($sql))
								{
									$this->file_kill(0, 0, $result['subfile_id']);
								}
								//eventuell vorhandene Subdateien entfernen
								$this->cdb->delete('DELETE FROM fom_sub_files WHERE file_id='.$file_data['file_id']);

								//Datei aus DB entfernen
								$this->cdb->delete('DELETE FROM fom_files WHERE file_id='.$file_data['file_id'].' LIMIT 1');

								//Dateien aus Zugriffssteuerung entfernen
								$this->cdb->delete("DELETE FROM fom_access WHERE type='FILE' AND id=".$file_data['file_id']);
							}
						}
					}
					//FTP
					else
					{
						//FIXME
					}

				}
			}
		}

		/**
		 * Loescht Fileversionen
		 * @param int $file_id
		 * @param int $project_id
		 * @return void
		 */
		private function file_version_kill($file_id, $project_id)
		{
			$tn = new Thumbnail();

			$sql = $this->cdb->select('SELECT typ, pfad FROM fom_file_server WHERE projekt_id='.$project_id);
			$result = $this->cdb->fetch_array($sql);

			if (substr($result['pfad'], -1) == '/')
			{
				$pfad = $result['pfad'].$project_id.'/';
			}
			else
			{
				$pfad = $result['pfad'].$project_id."\\";
			}

			//alle versionen zu einer datei finden
			$sql = $this->cdb->select('SELECT sub_fileid, save_name, save_time FROM fom_file_subversion WHERE file_id='.$file_id);
			while ($result = $this->cdb->fetch_array($sql))
			{
				//unix pfad
				if (substr($pfad, -1) == '/')
				{
					$file_pfad = $pfad.substr($result['save_time'], 0, 6).'/'.$result['save_name'];
				}
				//win pfad
				else
				{
					$file_pfad = $pfad.substr($result['save_time'], 0, 6)."\\".$result['save_name'];
				}

				//datei loeschen
				if (file_exists($file_pfad))
				{
					@unlink($file_pfad);
				}

				//eventuell vorhandene thumbnails entfernen
				$tn_result = $tn->search_thumbnail(0, $result['sub_fileid']);
				if ($tn_result !== false)
				{
					@unlink($tn_result['pfad'].$tn_result['name']);
				}

				$this->cdb->delete('DELETE FROM fom_search_word_az_file WHERE sub_fileid='.$result['sub_fileid']);
				$this->cdb->delete('DELETE FROM fom_search_word_file WHERE sub_fileid='.$result['sub_fileid']);
			}

			//alle versionen loeschen
			$this->cdb->delete('DELETE FROM fom_file_subversion WHERE file_id='.$file_id);
		}

		/**
		 * Blendet ein Verzeichnis aus
		 * @param int $folder_id
		 * @return boole
		 */
		public function folder_del($folder_id)
		{
			$mn = new MailNotification();

			//Verzeichnis ausblenden
			if ($this->cdb->update("UPDATE fom_folder SET anzeigen='0' WHERE folder_id=$folder_id"))
			{
				$mn->log_trigger_events(0, $folder_id, 'folder_del');

				//alle dazugehoerigen dateien ausblenden
				$sql = $this->cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND anzeigen='1'");
				while($result = $this->cdb->fetch_array($sql))
				{
					if (!$this->file_del($result['file_id']))
					{
						$this->error = true;
					}

					//alle internen links entfernen
					if (!$this->link_del(0, $result['file_id']))
					{
						$this->error = true;
					}
				}

				//alle dazugehoerigen links ausblenden
				$sql = $this->cdb->select("SELECT link_id FROM fom_link WHERE folder_id=$folder_id AND anzeigen='1'");
				while($result = $this->cdb->fetch_array($sql))
				{
					if (!$this->link_del($result['link_id']))
					{
						$this->error = true;
					}
				}

				//alle unterverzeichnise ausblenden
				$sql = $this->cdb->select('SELECT folder_id FROM fom_folder WHERE ob_folder='.$folder_id);
				while($result = $this->cdb->fetch_array($sql))
				{
					if (!$this->folder_del($result['folder_id']))
					{
						$this->error = true;
					}
				}
			}
			else
			{
				$this->error = true;
			}

			//Fehler aufgetretten
			if ($this->error === true)
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		/**
		 * Blendet einen Link aus
		 * @param int $link_id
		 * @return boole
		 */
		private function link_del($link_id, $file_id = 0)
		{
			if ($link_id > 0)
			{
				if ($this->cdb->update("UPDATE fom_link SET anzeigen='0' WHERE link_id=$link_id"))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Alle internen links zu einer datei entfernen
			elseif ($file_id > 0)
			{
				if ($this->cdb->update("UPDATE fom_link SET anzeigen='0' WHERE file_id=$file_id"))
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
		 * Blendet eine Datei aus
		 * @param int $file_id
		 * @return boole
		 */
		public function file_del($file_id)
		{
			$mn = new MailNotification();
			if ($this->cdb->update("UPDATE fom_files SET anzeigen='0' WHERE file_id=$file_id"))
			{
				$mn->log_trigger_events(0, $file_id, 'file_del');

				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Prueft ob in einem Projekt geloeschte objekte vorhanden sind
		 * @param int $project_id
		 * @return boole
		 */
		public function deleted_object_exists($project_id)
		{
			//Verzeichnis Pruefen
			$sql = $this->cdb->select("SELECT folder_id FROM fom_folder WHERE projekt_id=$project_id AND anzeigen='0'");
			$result = $this->cdb->fetch_array($sql);

			if (isset($result['folder_id']) and $result['folder_id'] > 0)
			{
				return true;
			}

			//Dateien Pruefen
			$sql = $this->cdb->select("SELECT t1.file_id FROM fom_files t1
										LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
										WHERE t1.anzeigen='0' AND t2.projekt_id=$project_id");
			$result = $this->cdb->fetch_array($sql);

			if (isset($result['file_id']) and $result['file_id'] > 0)
			{
				return true;
			}

			//Link Pruefen
			$sql = $this->cdb->select("SELECT t1.link_id FROM fom_link t1
										LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
										WHERE t1.anzeigen='0' AND t2.projekt_id=$project_id");
			$result = $this->cdb->fetch_array($sql);

			if (isset($result['link_id']) and $result['link_id'] > 0)
			{
				return true;
			}

			return false;
		}
	}
?>