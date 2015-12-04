<?php
	/**
	 * export-class
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * export-class
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Export
	{
		public $setup_array = array();
		private $tmp_array = array();

		public function __construct()
		{
			$this->setup_array['abs_pfad'] = FOM_ABS_PFAD.'files/imex/'.USER_ID.'/';
			$this->setup_array['abs_pfad_len'] = strlen($this->setup_array['abs_pfad']);
		}

		/**
		 * Export Starten
		 */
		public function export_data($folder_id, $project_id, $setup_array)
		{
			$cdb = new MySql;

			$this->setup_array['setup_chk_export_data'] = $setup_array;

			$export_status = true;

			//Alle Hauptverzeichnisse eines Projektes
			if ($folder_id > 0)
			{
				//Eventuell bereits existierende Verzeichnisse loeschen
				if (isset($this->setup_array['setup_chk_export_data']['del_exists_folder_int']) and $this->setup_array['setup_chk_export_data']['del_exists_folder_int'] == 1)
				{
					$sql = $cdb->select("SELECT folder_name FROM fom_folder WHERE folder_id=$folder_id");
					$result = $cdb->fetch_array($sql);

					//Verzeichnisse loeschen
					$export_status = $this->del_folder($this->setup_array['abs_pfad'].$result['folder_name'].'/', 0);
				}
				//Pruefen ob bis jetzt fehler aufgetreten sind
				if ($export_status === true)
				{
					$this->export_folder($folder_id, $this->setup_array['abs_pfad']);
				}
			}
			else
			{
				//alle Verzeichnisse des Projektes auslesen
				$sql = $cdb->select("SELECT folder_id, folder_name FROM fom_folder WHERE projekt_id=$project_id AND ebene=0 AND anzeigen='1'");
				while($result = $cdb->fetch_array($sql))
				{
					//Eventuell bereits existierende Verzeichnisse loeschen
					if (isset($this->setup_array['setup_chk_export_data']['del_exists_folder_int']) and $this->setup_array['setup_chk_export_data']['del_exists_folder_int'] == 1)
					{
						//Verzeichnisse loeschen
						$export_status = $this->del_folder($this->setup_array['abs_pfad'].$result['folder_name'].'/', 0);
					}
					//Pruefen ob bis jetzt fehler aufgetreten sind
					if ($export_status === true)
					{
						$this->export_folder($result['folder_id'], $this->setup_array['abs_pfad']);
					}
				}
			}

			if (isset($this->tmp_array['export_error']) and count($this->tmp_array['export_error']) > 0)
			{
				return $this->tmp_array['export_error'];
			}
			else
			{
				return true;
			}
		}

		/**
		 * Erstellt Verzeichnisse im Exportverzeichnis und Kopiert die jeweilige Dateiversion in die Ordner
		 * @param int $folder_id
		 * @param string $pfad
		 * @return void
		 */
		private function export_folder($folder_id, $pfad)
		{
			$cdb = new MySql;

			//Verzeichnisename
			$sql = $cdb->select('SELECT folder_name FROM fom_folder WHERE folder_id='.$folder_id);
			$result = $cdb->fetch_array($sql);

			//GesamtPfad
			$folder_name = html_entity_decode($result['folder_name'], ENT_QUOTES, 'ISO-8859-1');

			if (function_exists('iconv'))
			{
				//$folder_name = iconv('ISO-8859-1', 'UTF-8', $folder_name);
			}

			$tmp_pfad = $pfad.$folder_name.'/';

			@mkdir($tmp_pfad, 0774);

			if (file_exists($tmp_pfad))
			{
				//Dateiliste erstellen
				$file_list = $this->get_file_list($folder_id);

				for($i = 0; $i < count($file_list); $i++)
				{
					//Kopieren
					if (!@copy($file_list[$i]['pfad'].$file_list[$i]['save_name'], $tmp_pfad.$file_list[$i]['org_name']))
					{
						$this->tmp_array['export_error'][] = 'Die Datei "'.str_replace($this->setup_array['abs_pfad'], '', $tmp_pfad.$file_list[$i]['org_name']).'" konnte nicht exportiert werden!';
					}
					else
					{
						//CHMOD setzen
						chmod($tmp_pfad.$file_list[$i]['org_name'], 0664);
					}
				}

				//unterverzeichnisse Pruefen
				$folder_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$folder_id AND anzeigen='1'");
				while($folder_result = $cdb->fetch_array($folder_sql))
				{
					$this->export_folder($folder_result['folder_id'], $tmp_pfad);
				}
			}
			else
			{
				$this->tmp_array['export_error'][] = get_text(245, 'return', 'decode_on', array('foldername'=>str_replace($this->setup_array['abs_pfad'], '', $tmp_pfad)) );//Folder [var]foldername[/var] could not be created!
			}
		}

		/**
		 * Loescht ein Verzeichnis inkl. unterverzeichnissen und Dateien
		 * @param string $pfad
		 * @param int $count
		 * @return boole
		 */
		private function del_folder($pfad, $count = 1)
		{
			if ($count == 0)
			{
				$this->tmp_array['del_data'][] = $pfad;
			}

			while (false !== ($f = readdir($pfad)))
			{
				if ($f != '..' and $f != '.')
				{
					$this->tmp_array['del_data'][] = $pfad.$f;

					if (is_dir($pfad.$f))
					{
						$this->del_folder($pfad.$f.'/');
					}
				}
			}

			if ($count == 0)
			{
				$error = 0;
				for($i = count($this->tmp_array['del_data'] - 1); $i >= 0; $i--)
				{
					if ($error == 0)
					{
						if (is_dir($this->tmp_array['del_data'][$i]))
						{
							if (@rmdir($this->tmp_array['del_data'][$i]) !== true)
							{
								$error++;
								break;
							}
						}
						elseif (is_file($this->tmp_array['del_data'][$i]))
						{
							if (@unlink($this->tmp_array['del_data'][$i]) !== true)
							{
								$error++;
								break;
							}
						}
					}
				}

				if ($error == 0)
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
		 * Prueft die zu Exportierenden Daten auf Fehler z.B. zu lange Pfadnamen
		 * @param int $folder_id
		 * @param int $project_id
		 * @param array $setup_array
		 * @return array
		 */
		public function chk_export_data($folder_id, $project_id, $setup_array)
		{
			$cdb = new MySql;

			$this->setup_array['setup_chk_export_data'] = $setup_array;

			//Alle Hauptverzeichnisse eines Projektes
			if ($project_id > 0)
			{
				$sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE projekt_id=$project_id AND ebene=0 AND anzeigen='1'");
				while($result = $cdb->fetch_array($sql))
				{
					$this->read_pfad_len($result['folder_id'], $this->setup_array['abs_pfad']);
				}
			}
			else
			{
				$this->read_pfad_len($folder_id, $this->setup_array['abs_pfad']);
			}

			if(isset($this->tmp_array['error']))
			{
				return $this->tmp_array['error'];
			}
			else
			{
				return array();
			}
		}

		/**
		 * Prueft die Pfadlaengen eines Verzeichnisses inkl. Inhalt
		 * @param int $folder_id
		 * @param string $abs_pfad
		 * @return void
		 */
		private function read_pfad_len($folder_id, $abs_pfad)
		{
			$cdb = new MySql;

			//Verzeichnisename
			$sql = $cdb->select('SELECT folder_name FROM fom_folder WHERE folder_id='.$folder_id);
			$result = $cdb->fetch_array($sql);

			//GesamtPfad
			$tmp_pfad = $abs_pfad.$result['folder_name'].'/';

			//Pfadlaenge Pruefen
			if (strlen($tmp_pfad) > 255)
			{
				$this->tmp_array['error'][] = get_text(246, 'return', 'decode_on', array('foldername'=>str_replace($this->setup_array['abs_pfad'], '', $tmp_pfad)) );//The path for the folder [var]foldername[/var] exceeds the allowed length!
			}

			//Dateien Pruefen
			$file_sql = $cdb->select("SELECT org_name FROM fom_files WHERE folder_id=$folder_id AND anzeigen='1'");
			while($file_result = $cdb->fetch_array($file_sql))
			{
				$file_list = $this->get_file_list($folder_id);

				for($i = 0; $i < count($file_list); $i++)
				{
					if (strlen($tmp_pfad.$file_list[$i]['org_name']) > 255)
					{
						$this->tmp_array['error'][] = get_text(247, 'return', 'decode_on', array('foldername'=>str_replace($this->setup_array['abs_pfad'], '', $tmp_pfad.$file_list[$i]['org_name'])) );//The path for the file [var]filename[/var] exceeds the allowed length!
					}
				}
			}

			//unterverzeichnisse Pruefen
			$folder_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$folder_id AND anzeigen='1'");
			while($folder_result = $cdb->fetch_array($folder_sql))
			{
				$this->read_pfad_len($folder_result['folder_id'], $tmp_pfad);
			}
		}

		/**
		 * Liest alle Dateien zu einem Verzeichnis aus
		 * @param int $folder_id
		 * @return array
		 */
		private function get_file_list($folder_id)
		{
			$cdb = new MySql;
			$gt = new Tree;

			$return_array = array();

			//Einen bestimten Versionsstand ausgeben
			if ($this->setup_array['setup_chk_export_data']['version_string'] == 'old')
			{
				$sql = $cdb->select("SELECT t1.file_id, t1.org_name, t1.save_name, t1.save_time, t2.projekt_id, t2.typ, t2.pfad FROM fom_files t1
									LEFT JOIN fom_file_server t2 ON t1.file_server_id=t2.file_server_id
									WHERE t1.folder_id=$folder_id AND t1.anzeigen='1'");
				while($result = $cdb->fetch_array($sql))
				{
					//Pruefen ob eine Fruehere Version verwendet werden muss
					if ($this->setup_array['setup_chk_export_data']['version_date_string'] < substr($result['save_time'], 0, 8))
					{
						$sub_sql = $cdb->select("SELECT org_name, save_name, save_time FROM fom_file_subversion WHERE file_id=".$result['file_id']." AND LEFT(save_time, 8)<='".$this->setup_array['setup_chk_export_data']['version_date_string']."' ORDER BY save_time DESC");
						$sub_result = $cdb->fetch_array($sub_sql);
					}

					//Subversion verwenden
					if (isset($sub_result['org_name']) and !empty($sub_result['org_name']))
					{
						$save_name = $sub_result['save_name'];
						$org_name = html_entity_decode($sub_result['org_name'], ENT_QUOTES, 'UTF-8');
						$pfad = $result['pfad'].$result['projekt_id'].'/'.substr($sub_result['save_time'], 0, 6).'/';
					}
					else
					{
						$save_name = $result['save_name'];
						$org_name = html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8');
						$pfad = $result['pfad'].$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/';
					}

					//Nur nachfolgende Dateiendungen zulassen
					if(isset($this->setup_array['setup_chk_export_data']['only_extention_array']) and count($this->setup_array['setup_chk_export_data']['only_extention_array']) > 0)
					{
						$tmp_ex = $gt->GetFileExtension($result['save_name']);

						if (in_array($tmp_ex, $this->setup_array['setup_chk_export_data']['only_extention_array']))
						{
							$return_array[] = array('save_name' => $save_name,
													'org_name' => $org_name,
													'pfad' => $pfad);
						}
					}
					//Nur Dateien die nicht die Dateiendungen haben
					elseif(isset($this->setup_array['setup_chk_export_data']['without_extention_array']) and count($this->setup_array['setup_chk_export_data']['without_extention_array']) > 0)
					{
						$tmp_ex = $gt->GetFileExtension($result['save_name']);

						if (!in_array($tmp_ex, $this->setup_array['setup_chk_export_data']['without_extention_array']))
						{
							$return_array[] = array('save_name' => $save_name,
													'org_name' => $org_name,
													'pfad' => $pfad);
						}
					}
					//Alles Exportieren
					else
					{
						$return_array[] = array('save_name' => $save_name,
												'org_name' => $org_name,
												'pfad' => $pfad);
					}
				}
			}
			//Aktuelle Dateiversion ausgeben
			else
			{
				$sql = $cdb->select("SELECT t1.org_name, t1.save_name, t1.save_time, t2.projekt_id, t2.typ, t2.pfad FROM fom_files t1
									LEFT JOIN fom_file_server t2 ON t1.file_server_id=t2.file_server_id
									WHERE t1.folder_id=$folder_id AND t1.anzeigen='1'");
				while($result = $cdb->fetch_array($sql))
				{
					$pfad = $result['pfad'].$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/';

					//Nur nachfolgende Dateiendungen zulassen
					if(isset($this->setup_array['setup_chk_export_data']['only_extention_array']) and count($this->setup_array['setup_chk_export_data']['only_extention_array']) > 0)
					{
						$tmp_ex = $gt->GetFileExtension($result['save_name']);

						if (in_array($tmp_ex, $this->setup_array['setup_chk_export_data']['only_extention_array']))
						{
							$return_array[] = array('save_name' => $result['save_name'],
													'org_name' => html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8'),
													'pfad' => $pfad);
						}
					}
					//Nur Dateien die nicht die Dateiendungen haben
					if(isset($this->setup_array['setup_chk_export_data']['without_extention_array']) and count($this->setup_array['setup_chk_export_data']['without_extention_array']) > 0)
					{
						$tmp_ex = $gt->GetFileExtension($result['save_name']);

						if (!in_array($tmp_ex, $this->setup_array['setup_chk_export_data']['without_extention_array']))
						{
							$return_array[] = array('save_name' => $result['save_name'],
													'org_name' => html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8'),
													'pfad' => $pfad);
						}
					}
					//Alles Exportieren
					else
					{
						$return_array[] = array('save_name' => $result['save_name'],
												'org_name' => html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8'),
												'pfad' => $pfad);
					}
				}
			}
			return $return_array;
		}

		/**
		 * Prueft ob im Exportordner bereits Verzeichnisse mit dem Selben Namen vorhanden sind
		 * @param int $folder_id
		 * @param int $project_id
		 * @return boole
		 */
		public function chk_export_folder($folder_id, $project_id)
		{
			$cdb = new MySql;

			//Es wird ein Verzeichnis Exportiert
			if ($folder_id > 0)
			{
				$sql = $cdb->select('SELECT folder_name FROM fom_folder WHERE folder_id='.$folder_id);
				$result = $cdb->fetch_array($sql);

				//Verzeichnis ist im Exportverzeichnis nicht vorhanden
				if (!file_exists($this->setup_array['abs_pfad'].$result['folder_name']))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Es wird ein Projekt Exportiert (dieses kann mehrere Verzeichnisse enthalten)
			elseif ($project_id > 0)
			{
				$sql = $cdb->select("SELECT folder_name FROM fom_folder WHERE projekt_id=$project_id AND ebene='0'");
				while($result = $cdb->fetch_array($sql))
				{
					//Verzeichnis ist im Exportverzeichnis vorhanden
					if (file_exists($this->setup_array['abs_pfad'].$result['folder_name']))
					{
						return false;
					}
				}
				return true;
			}
			//Sollte eingendlich nie der Fall sein
			else
			{
				return false;
			}
		}

		/**
		 * Prueft die Exporteinstellungen und erstellt ein SetupArray
		 * @param array $setup
		 * @return array
		 */
		public function get_setup_array($setup)
		{
			$return_array = array();
			$return_array['error'] = false;

			//Bereits Existierende Verzeichnisse loeschen
			if (isset($setup['del_exists_folder_int']) and $setup['del_exists_folder_int'] == 1)
			{
				$return_array['del_exists_folder_int'] = 1;
			}
			else
			{
				$return_array['del_exists_folder_int'] = 0;
			}

			//Dateiendungen
			if (isset($setup['only_extention_string']) or isset($setup['without_extention_string']))
			{
				//Nur nachfolgende Dateiendungen Exportieren
				if (isset($setup['only_extention_string']) and !empty($setup['only_extention_string']))
				{
					//Alle nicht Alphanummerischen Zeichen entfernen
					$tmp_only_ex = preg_replace('@\W@', ' ', $setup['only_extention_string']);

					//Alle Leerzeichen entfernen
					$tmp_only_ex = preg_replace('@\s@', '_', $tmp_only_ex);

					//Alle Doppelten _ entfernen
					$while_boole = true;
					while($while_boole === true)
					{
						if (strpos($tmp_only_ex, '__') !== false)
						{
							$tmp_only_ex = str_replace('__', '_', $tmp_only_ex);
						}
						else
						{
							$while_boole = false;
						}
					}

					//String in Array wandeln
					$tmp_only_array = explode('_', $tmp_only_ex);

					if (count($tmp_only_array) > 0)
					{
						$tmp_only_array_second = array();

						for($i = 0; $i < count($tmp_only_array); $i++)
						{
							if (!empty($tmp_only_array[$i]))
							{
								$tmp_only_array_second[] = strtolower($tmp_only_array[$i]);
							}
						}
						$return_array['only_extention_array'] = $tmp_only_array_second;
					}
					else
					{
						$return_array['only_extention_array'] = array();
					}
				}

				//Nachfolgende Dateiendungen nicht Exportieren
				elseif (isset($setup['without_extention_string']) and !empty($setup['without_extention_string']))
				{
					//Alle nicht Alphanummerischen Zeichen entfernen
					$tmp_without_ex = preg_replace('@\W@', ' ', $setup['without_extention_string']);

					//Alle Leerzeichen entfernen
					$tmp_without_ex = preg_replace('@\s@', '_', $tmp_without_ex);

					//Alle Doppelten _ entfernen
					$while_boole = true;
					while($while_boole === true)
					{
						if (strpos($tmp_without_ex, '__') !== false)
						{
							$tmp_without_ex = str_replace('__', '_', $tmp_without_ex);
						}
						else
						{
							$while_boole = false;
						}
					}

					//String in Array wandeln
					$tmp_without_array = explode('_', $tmp_without_ex);

					if (count($tmp_without_array) > 0)
					{
						$tmp_without_array_second = array();

						for($i = 0; $i < count($tmp_without_array); $i++)
						{
							if (!empty($tmp_without_array[$i]))
							{
								$tmp_without_array_second[] = strtolower($tmp_without_array[$i]);
							}
						}
						$return_array['without_extention_array'] = $tmp_without_array_second;
					}
					else
					{
						$return_array['without_extention_array'] = array();
					}
				}
				else
				{
					$return_array['only_extention_array'] = array();
					$return_array['without_extention_array'] = array();
				}
			}
			else
			{
				$return_array['only_extention_array'] = array();
				$return_array['without_extention_array'] = array();
			}

			//Versionseinstellungen
			if (isset($setup['version_string']))
			{
				//Aktuelleste Dateiversion Verwenden
				if ($setup['version_string'] == 'current')
				{
					$return_array['version_string'] = 'current';
				}
				//Dateiversion mit einem Bestimmten Datum Verwenden
				elseif ($setup['version_string'] == 'old')
				{
					if (!empty($setup['version_date_string']))
					{
						//Kalenderklasse
						$cal = new Calendar;

						$tmp_date = $cal->format_date($setup['version_date_string'], 'ISO');

						if ($tmp_date != '0000-00-00')
						{
							//Datum auf ein gueltiges ISO Format Pruefen
							$tmp_date = $cal->check_iso_date($tmp_date);
							if ($tmp_date != '0000-00-00')
							{
								$tmp_date_string = str_replace('-', '', $tmp_date);

								//Das Versionsdatum sollte nicht in der Zukunft liegen ergibt ja auch keinen Sinn
								if ($tmp_date_string <= date('Ymd'))
								{
									$return_array['version_string'] = 'old';
									$return_array['version_date'] = $tmp_date;
									$return_array['version_date_string'] = $tmp_date_string;
								}
								else
								{
									$return_array['version_string'] = 'current';
									$return_array['error'] = true;
								}
							}
							else
							{
								$return_array['version_string'] = 'current';
								$return_array['error'] = true;
							}
						}
						//Sollte eigentlich nicht passieren
						else
						{
							$return_array['version_string'] = 'current';
							$return_array['error'] = true;
						}
					}
					//Sollte eigentlich nicht passieren
					else
					{
						$return_array['version_string'] = 'current';
						$return_array['error'] = true;
					}
				}
				//Sollte eigentlich nicht passieren
				else
				{
					$return_array['version_string'] = 'current';
					$return_array['error'] = true;
				}
			}
			//Sollte eigentlich nicht passieren
			else
			{
				$return_array['version_string'] = 'current';
				$return_array['error'] = true;
			}

			return $return_array;
		}
	}
?>