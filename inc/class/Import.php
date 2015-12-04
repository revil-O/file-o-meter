<?php
	/**
	 * imports data from a source-directory into the db
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * imports data from a source-directory into the db
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Import
	{
		private $setup_array = array();
		public $import_array = array();
		private $tmp_array = array();
		private $gl_class = array();

		public function __construct()
		{
			$this->setup_array['abs_pfad'] = FOM_ABS_PFAD.'files/imex/'.USER_ID.'/';

			//FileJob
			$this->gl_class['fj'] = new FileJobs;
		}

		/**
		 * Importiert Verzeichnisse bzw. Dateien ind die DB
		 * @param int $project_id
		 * @param int $folder_id
		 * @param boole $rename
		 * @param array $folder_array
		 * @param array $file_array
		 * @param array $version_setup
		 * @return array
		 */
		public function start_import($project_id, $folder_id, $rename, $folder_array, $file_array, $version_setup)
		{
			$up = new FileUpload;
			$cdb = new MySql;
			$mn = new MailNotification();

			//Alle nicht Importierten Verzeichnisse und Dateien entfernen
			if ($version_setup['del_file_folder'] === true)
			{
				//Erstellt ein Array mit allen Verzeichnis- und Datei-ID`s
				$this->read_org_data($project_id, $folder_id);
			}

			//Speichert die Rueckgabewerte
			$return_array = array();
			//Speichert die Oberverzeichnis-ID`s
			$folder_ebenen_id = array();

			//Liest alle Verzeichnisse und Dateien aus die Importiert werden sollen
			$data_array = $this->read_import_folder($folder_id, $folder_array, $file_array);

			//Existierende Dateien durch neue Version ersetzen
			if (isset($version_setup['add_version']) and $version_setup['add_version'] === true)
			{
				$add_file_version = true;
			}
			else
			{
				$add_file_version = false;
			}

			//Verzeichnisimport
			if (isset($data_array['folder']) and count($data_array['folder']) > 0 and ($project_id > 0 or $folder_id > 0))
			{
				//Erstes Oberverzeichnis beim Import in ein Unterverzeichnis
				if ($folder_id > 0)
				{
					$sql = $cdb->select('SELECT ebene FROM fom_folder WHERE folder_id='.$folder_id);
					$result = $cdb->fetch_array($sql);
					$folder_ebenen_id[$result['ebene']] = $folder_id;
				}
				//Erstes Oberverzeichnis beim Import auf der Hauptebene
				else
				{
					$folder_ebenen_id[0] = 0;
					$folder_ebenen_id[-1] = 0;
				}

				for($i = 0; $i < count($data_array['folder']); $i++)
				{
					//Verzeichnisnamen von &Ouml; befreien
					$org_name = html_entity_decode($data_array['folder'][$i]['dir_name'], ENT_QUOTES, 'UTF-8');
					//Automatische Namensanpassung
					if ($rename === true)
					{
						$iso_name = $up->chk_name($org_name, 'folder');
						$tmp_name = $iso_name;
					}
					else
					{
						$tmp_name = $org_name;
					}

					$tmp_name = htmlentities($tmp_name, ENT_QUOTES, 'UTF-8', false);

					//Import als unterverzeichnis
					if ($folder_id > 0)
					{
						$sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$folder_id AND folder_name='$tmp_name' AND anzeigen='1'");
						$result = $cdb->fetch_array($sql);
					}
					//Import auf Hauptebene
					elseif($project_id > 0)
					{
						$sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE projekt_id=$project_id AND ob_folder=0 AND folder_name='$tmp_name' AND anzeigen='1'");
						$result = $cdb->fetch_array($sql);
					}

					//Verzeichnis existiert bereits
					if (isset($result['folder_id']) and $result['folder_id'] > 0)
					{
						//Nur Ebene auslesen
						$folder_ebenen_id[$data_array['folder'][$i]['ebene']] = $result['folder_id'];
						$tmp_folder_id = $result['folder_id'];
						//Verzeichnis-ID fuer eventuell spaetere Loeschungen speichern
						$this->tmp_array['new']['folder'][] = $tmp_folder_id;
					}
					//Verzeichnis neu anlegen
					else
					{
						if ($cdb->insert("INSERT INTO fom_folder (projekt_id, folder_name, ob_folder, ebene) VALUES ($project_id, '$tmp_name', ".$folder_ebenen_id[$data_array['folder'][$i]['ebene'] -1].", ".$data_array['folder'][$i]['ebene'].")"))
						{
							if ($cdb->get_affected_rows() == 1)
							{
								$tmp_folder_id = $cdb->get_last_insert_id();

								$mn->log_trigger_events($project_id, $tmp_folder_id, 'folder_add');

								//Ebene Speichern
								$folder_ebenen_id[$data_array['folder'][$i]['ebene']] = $tmp_folder_id;
								//Verzeichnis-ID fuer eventuell spaetere Loeschungen speichern
								$this->tmp_array['new']['folder'][] = $tmp_folder_id;
							}
							else
							{
								$return_array['error'][] = get_text(245, 'return', 'decode_on', array('foldername'=>$org_name));//Folder [var]foldername[/var] could not be created!
							}
						}
						else
						{
							$return_array['error'][] = get_text(245, 'return', 'decode_on', array('foldername'=>$org_name));//Folder [var]foldername[/var] could not be created!
						}
					}

					//Import von Dateien die innerhalb der Importierten Verzeichnisse liegen
					if (isset($data_array['folder'][$i]['files']) and count($data_array['folder'][$i]['files']) > 0)
					{
						for($j = 0; $j < count($data_array['folder'][$i]['files']); $j++)
						{
							$file_result = $this->file_import($tmp_folder_id, $project_id, $data_array['folder'][$i]['files'][$j], $data_array['folder'][$i]['pfad'], $rename, $add_file_version);

							if ($file_result !== true)
							{
								$return_array['error'][] = $file_result;
							}
						}
					}
				}
			}

			//Import von Dateien die auf der Hauptebene liegen
			if (isset($data_array['file']) and count($data_array['file']) > 0 and $folder_id > 0)
			{
				for($i = 0; $i < count($data_array['file']); $i++)
				{
					$file_result = $this->file_import($folder_id, $project_id, $data_array['file'][$i], $this->setup_array['abs_pfad'], $rename, $add_file_version);

					if ($file_result !== true)
					{
						$return_array['error'][] = $file_result;
					}
				}
			}

			//Unveraenderte Verzeichnisse bzw. Dateien entfernen
			if ($version_setup['del_file_folder'] === true)
			{
				$this->tmp_array['new']['files'] = $this->gl_class['fj']->get_file_ids();

				//Pruefen ob Verzeichnis-ID`s existieren
				if (isset($this->tmp_array['org']['folder']) and count($this->tmp_array['org']['folder']) > 0)
				{
					if (!isset($this->tmp_array['new']['folder']))
					{
						$this->tmp_array['new']['folder'] = array();
					}

					foreach($this->tmp_array['org']['folder'] as $fid)
					{
						if (!in_array($fid, $this->tmp_array['new']['folder']))
						{
							//FIXME: Hier muss ein Logeintrag rein
							$cdb->update("UPDATE fom_folder SET anzeigen='0' WHERE folder_id=$fid");
						}
					}
				}

				//Pruefen ob alle Datei-ID`s existieren
				if (isset($this->tmp_array['org']['files']) and count($this->tmp_array['org']['files']) > 0)
				{
					if (!isset($this->tmp_array['new']['files']))
					{
						$this->tmp_array['new']['files'] = array();
					}

					foreach($this->tmp_array['org']['files'] as $fid)
					{
						if (!in_array($fid, $this->tmp_array['new']['files']))
						{
							//FIXME: Hier muss ein Logeintrag rein
							$cdb->update("UPDATE fom_files SET anzeigen='0' WHERE file_id=$fid");
						}
					}
				}
			}
			return $return_array;
		}

		/**
		 * Imortiert eine Datei in die DB.
		 * @param int $folder_id
		 * @param int $project_id
		 * @param string $file_name
		 * @param string $pfad
		 * @param boole $rename
		 * @param array $version
		 */
		private function file_import($folder_id, $project_id, $file_name, $pfad, $rename, $version)
		{
			$up = new FileUpload;
			$cdb = new MySql;
			$fi = new FileInfo;
			$gt = new Tree;
			$mn = new MailNotification();

			//Dateiname von &Ouml; befreien
			$org_name = html_entity_decode($file_name, ENT_QUOTES, 'UTF-8');

			//Automatische Namensanpassung
			if ($rename === true)
			{
				$iso_name = $up->chk_name($org_name);

				//Pruefen ob Datei bereits existiert
				$sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND (org_name_no_iso='$org_name' OR org_name='$iso_name') AND anzeigen='1'");
				$file_result = $cdb->fetch_array($sql);
			}
			else
			{
				$iso_name = $org_name;

				//Pruefen ob Datei bereits existiert
				$sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name_no_iso='$org_name' AND anzeigen='1'");
				$file_result = $cdb->fetch_array($sql);
			}

			//Dateiinformationen auslesen
			$mime = $fi->get_mime_type($pfad.$org_name);
			$ex = $fi->get_extension($org_name);
			$md5 = $fi->get_md5_file($pfad.$org_name);
			$save_name = $gt->GetNewFileName();
			$size = $fi->get_filesize($pfad.$org_name);

			if (!empty($ex))
			{
				$save_name .= '.'.$ex;
			}

			//FileJob Klasse den Pfad zum Importverzeichnis mitteilen
			$this->gl_class['fj']->set_setup(array('import_folder' => html_entity_decode($pfad, ENT_QUOTES)));

			//Dateiversion aendern
			if (isset($file_result['file_id']) and $file_result['file_id'] > 0 and $version === true)
			{
				$file_import = $this->gl_class['fj']->insert_fileversion($file_result['file_id'], $save_name, $md5, $iso_name, $org_name, $mime, $size, time(), 'import');
				$mn->log_trigger_events(0, $file_result['file_id'], 'file_add_version');
			}
			//Neue Datei anlegen
			else
			{
				$file_import = $this->gl_class['fj']->insert_new_file($save_name, $md5, $iso_name, $org_name, $mime, $size, $folder_id, $project_id, '', time(), 'import');
			}

			if ($file_import === false)
			{
				return get_text(280, 'return').' '.$org_name;//File import failed:
			}
			else
			{
				return true;
			}
		}

		/**
		 * Liest einen Verzeichnisbaum inkl. Dateien aus der DB aus. Dies wird fuer das eventuelle Spaetere loeschen von unveraenderten Dateien benoetigt
		 * @param int $project_id
		 * @param int $file_result
		 * @return void
		 */
		private function read_org_data($project_id, $folder_id, $first_run = true)
		{
			$cdb = new MySql;

			//Unterverzeichnis auslesen
			if ($folder_id > 0)
			{
				if ($first_run === true)
				{
					$file_sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND anzeigen='1'");
					while($file_result = $cdb->fetch_array($file_sql))
					{
						$this->tmp_array['org']['files'][] = $file_result['file_id'];
					}

					$this->read_org_data(0, $folder_id, false);
				}
				else
				{
					$sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$folder_id AND anzeigen='1'");
					while($result = $cdb->fetch_array($sql))
					{
						$this->tmp_array['org']['folder'][] = $result['folder_id'];

						$file_sql = $cdb->select('SELECT file_id FROM fom_files WHERE folder_id='.$result['folder_id']." AND anzeigen='1'");
						while($file_result = $cdb->fetch_array($file_sql))
						{
							$this->tmp_array['org']['files'][] = $file_result['file_id'];
						}

						$this->read_org_data(0, $result['folder_id'], false);
					}
				}
			}
			//Gesamtes Projekt auslesen
			else
			{
				$sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE projekt_id=$project_id AND anzeigen='1'");
				while($result = $cdb->fetch_array($sql))
				{
					$this->tmp_array['org']['folder'][] = $result['folder_id'];

					$file_sql = $cdb->select('SELECT file_id FROM fom_files WHERE folder_id='.$result['folder_id']." AND anzeigen='1'");
					while($file_result = $cdb->fetch_array($file_sql))
					{
						$this->tmp_array['org']['files'][] = $file_result['file_id'];
					}

					$this->read_org_data(0, $result['folder_id'], false);
				}
			}
		}

		/**
		 * Ruft Funktionen fuer das auslesen von Verzeichnisinhalten auf
		 * @param int $folder_id, Id des Verzeichnisses in das die Daten Importiert werden soll
		 * @param array $folder_array, Array mit allen Verzeichnissen die auf der Haupteben sind und Importiert werden sollen
		 * @param array $file_array, Array mit allen Dateien die auf der Hauptebene sind und Importiert werden sollen.
		 * @return array
		 */
		public function read_import_folder($folder_id = 0, $folder_array, $file_array)
		{
			if (count($folder_array) > 0)
			{
				//Ebene auslesen
				if ($folder_id > 0)
				{
					$cdb = new MySql;

					$sql = $cdb->select('SELECT ebene FROM fom_folder WHERE folder_id='.$folder_id);
					$result = $cdb->fetch_array($sql);

					$ebene = $result['ebene'];
				}
				else
				{
					$ebene = -1;
				}

				foreach($folder_array as $f)
				{
					//Verzeichnis inkl. Unterverzeichnis auslesen
					$this->list_import_folder($f, $ebene);
				}
			}

			//Alle Dateien auf der Hauptebene auslesen
			if (count($file_array) > 0)
			{
				foreach($file_array as $f)
				{
					$this->import_array['file'][] = $f;
				}
			}
			return $this->import_array;
		}

		/**
		 * Liest ein Verzeichnis inkl. Unterverzeichnisse und Dateien aus
		 * @param string $pfad
		 * @param int $ebene
		 * @return void
		 */
		private function list_import_folder($pfad, $ebene = 0)
		{
			$ebene++;

			//Erstaufruf
			if (substr($pfad, 0, strlen($this->setup_array['abs_pfad'])) != $this->setup_array['abs_pfad'])
			{
				$f = $pfad;
				$pfad = $this->setup_array['abs_pfad'].$pfad.'/';

				$this->import_array['folder'][] = array('dir_name' => $f,
														'ebene' => $ebene,
														'pfad' => $pfad,
														'files' => $this->list_import_files($pfad));
				$ebene++;

			}

			if (file_exists($pfad) and is_dir($pfad))
			{
				if ($h = opendir($pfad))
				{
					$count = 0;
					while (($f = readdir($h)) !== false)
					{
						if ($f != '.' and $f != '..')
						{
							if (is_dir($pfad.$f))
							{
								$this->import_array['folder'][] = array('dir_name' => $f,
																		'ebene' => $ebene,
																		'pfad' => $pfad.$f.'/',
																		'files' => $this->list_import_files($pfad.$f.'/'));

								$this->list_import_folder($pfad.$f.'/', $ebene);
							}
						}
					}
				}
			}
		}

		/**
		 * Liest alle Dateien aus einem Verzeichnis aus
		 * @param string $folder
		 * @return array
		 */
		private function list_import_files($folder)
		{
			$return = array();
			if (file_exists($folder) and is_dir($folder))
			{
				if ($h = opendir($folder))
				{
					$count = 0;
					while (($f = readdir($h)) !== false)
					{
						if ($f != '.' and $f != '..')
						{
							if (is_file($folder.$f))
							{
								$return[] = $f;
							}
						}
					}
				}
			}
			return $return;
		}

		/**
		 * Prueft ob beim Import Versionsoptionen eingeblendet werden muessen
		 * @param array $data_array
		 * @param boole $rename
		 * @param int $folder_id
		 * @param int $project_id
		 * @return boole
		 */
		public function show_import_option($data_array, $rename, $folder_id, $project_id)
		{
			$cdb = new MySql;
			$up = new FileUpload;

			//Verzeichnisse Pruefen
			if (isset($data_array['folder']) and count($data_array['folder']) > 0)
			{
				//SQL Abfrage fuer Import in ein Unterverzeichnis
				if ($folder_id > 0)
				{
					$sql_string = 'SELECT folder_id FROM fom_folder WHERE ob_folder='.$folder_id;
				}
				//SQL Abfrage fuer den Import auf der Projekthauptebene
				else
				{
					$sql_string = 'SELECT folder_id FROM fom_folder WHERE projekt_id='.$project_id.' AND ob_folder=0';
				}

				for($i = 0; $i < count($data_array['folder']); $i++)
				{
					$org_folder_name = $data_array['folder'][$i]['dir_name'];

					$sql = $cdb->select($sql_string." AND folder_name='$org_folder_name'");
					$result = $cdb->fetch_array($sql);

					if (isset($result['folder_id']) and !empty($result['folder_id']))
					{
						return true;
					}

					//Iso Konformer Name
					if ($rename === true)
					{
						$tmp_name = $up->chk_name($org_folder_name, 'folder');

						$sql = $cdb->select($sql_string." AND folder_name='$tmp_name'");
						$result = $cdb->fetch_array($sql);

						if (isset($result['folder_id']) and !empty($result['folder_id']))
						{
							return true;
						}
					}
				}
			}

			//Dateien Pruefen
			if (isset($data_array['file']) and count($data_array['file']) > 0)
			{
				if ($folder_id > 0)
				{
					for($i = 0; $i < count($data_array['file']); $i++)
					{
						$org_file_name = $data_array['file'][$i];

						$sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name='$org_file_name'");
						$result = $cdb->fetch_array($sql);

						if (isset($result['file_id']) and !empty($result['file_id']))
						{
							return true;
						}

						//Iso Konformer Name
						if ($rename === true)
						{
							$tmp_name = $up->chk_name($org_file_name);

							$sql = $cdb->select("SELECT file_id FROM fom_files WHERE folder_id=$folder_id AND org_name='$tmp_name'");
							$result = $cdb->fetch_array($sql);

							if (isset($result['file_id']) and !empty($result['file_id']))
							{
								return true;
							}
						}
					}
				}
			}
			//keine uebereinstimmungen gefunden
			return false;
		}

		/**
		 * Prueft die Importdaten auf ISO Konforme Namen und Pfadlaenge
		 * @param array $data_array
		 * @return array
		 */
		public function chk_import_data($data_array)
		{
			$return_array = array();
			$up = new FileUpload;

			//Dateien auf der Hauptebene Pruefen
			if (isset($data_array['file']) and count($data_array['file']) > 0)
			{
				for($i = 0; $i < count($data_array['file']); $i++)
				{
					$tmp_org_name = html_entity_decode($data_array['file'][$i], ENT_QUOTES);
					$tmp_name = $up->chk_name($tmp_org_name);

					if ($tmp_name != $tmp_org_name)
					{
						$return_array['WARNING'][] = get_text(257, 'return', 'decode_on', array('filename'=>$tmp_org_name,'filename_new'=>$tmp_name));//The filename [var]filename[/var] is not ISO-9660 compliant! An automatic adaption to [var]filename_new[/var] is possible.
					}
				}
			}

			//Verzeichnis Pruefen
			if (isset($data_array['folder']) and count($data_array['folder']) > 0)
			{
				for($i = 0; $i < count($data_array['folder']); $i++)
				{
					$tmp_org_name = html_entity_decode($data_array['folder'][$i]['dir_name'], ENT_QUOTES);
					$tmp_pfad = str_replace($this->setup_array['abs_pfad'], '', html_entity_decode($data_array['folder'][$i]['pfad'], ENT_QUOTES));
					$tmp_name = $up->chk_name($tmp_org_name, 'folder');

					//Verzeichnisname Pruefen
					if ($tmp_name != $tmp_org_name)
					{
						$return_array['WARNING'][] = get_text(258, 'return', 'decode_on', array('foldername'=>$tmp_org_name,'foldername_new'=>$tmp_name));//The foldername [var]foldername[/var] is not ISO-9660 compliant! An automatic adaption to [var]foldername_new[/var] is possible.
					}
					//Pfadlaenge Pruefen
					if (strlen($data_array['folder'][$i]['pfad']) > 255)
					{
						$return_array['ERROR'][] = get_text(246, 'return', 'decode_on', array('foldername'=>str_replace('/', '/ ', $tmp_pfad)) );//The path for the folder [var]foldername[/var] exceeds the allowed length! ...
					}
					//Enthaltene Dateien Pruefen
					if (isset($data_array['folder'][$i]['files']) and count($data_array['folder'][$i]['files']) > 0)
					{
						for($j = 0; $j < count($data_array['folder'][$i]['files']); $j++)
						{
							$tmp_org_file_name = html_entity_decode($data_array['folder'][$i]['files'][$j], ENT_QUOTES);
							$tmp_name = $up->chk_name($tmp_org_file_name);

							//Dateiname Pruefen
							if ($tmp_name != $tmp_org_file_name)
							{
								$return_array['WARNING'][] = get_text(257, 'return', 'decode_on', array('filename'=>$tmp_org_file_name,'filename_new'=>$tmp_name));//The filename [var]filename[/var] is not ISO-9660 compliant! An automatic adaption to [var]filename_new[/var] is possible.
							}
							//Gesamtpfadlaenge Pruefen
							if (strlen(html_entity_decode($data_array['folder'][$i]['pfad'], ENT_QUOTES).$tmp_org_file_name) > 255)
							{
								$return_array['ERROR'][] = get_text(246, 'return', 'decode_on', array('foldername'=>str_replace('/', '/ ', $tmp_pfad.$tmp_org_file_name)) );//The path for the folder [var]foldername[/var] exceeds the allowed length! ...
							}
						}
					}
				}
			}
			return $return_array;
		}
	}
?>