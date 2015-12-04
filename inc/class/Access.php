<?php
	/**
	 * authorization-check for the access on DMS objects.
	 * the check starts from the lowest level to the next higher level.
	 * file => folder => parent folder => project
	 * user => usergroup
	 *
	 * Rechte Pruefung fuer den Zugriff auf Objekte im DMS
	 * Die Pruefung erfolgt von der kleinsten Ebene zur naechsthoeheren
	 * Datei => Verzeichnis => Oberverzeichnis => Projekt
	 * User => Usergruppe
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * authorization-check for the access on DMS objects
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Access
	{
		/**
		 * Speichert allgemeine Grundeinstellungen
		 * @var array
		 */
		private $setup_array = array();

		/**
		 * Speichert Temporaere Daten
		 * @var array
		 */
		private $tmp_array = array();

		/**
		 * MySQL Class Object
		 * @var object
		 */
		private $cdb;

		/**
		 * Speichert den Uebergebenen Zugriffstyp
		 * @var string
		 */
		private $access_typ = '';

		/**
		 * Setzt allgemeine Grundwerte
		 */
		public function __construct()
		{
			$this->cdb = new MySql();
			$this->setup_array['foreign_key'] = false;

			//Pruefen ob Define existiert (sollte eigentlich immer der fall sein)
			if (defined('USER_ID'))
			{
				$this->setup_array['usergroup_id'] = array();
				//Usergruppenid suchen
				//$sql = $this->cdb->select('SELECT usergroup_id FROM fom_user WHERE user_id='.USER_ID);
				$sql = $this->cdb->select('SELECT usergroup_id FROM fom_user_membership WHERE user_id='.USER_ID);
				while($result = $this->cdb->fetch_array($sql))
				{
					//Usergruppenid vorhanden
					if (isset($result['usergroup_id']) and !empty($result['usergroup_id']))
					{
						//Usergruppen-ID Speichern
						$this->setup_array['usergroup_id'][] = $result['usergroup_id'];
					}
				}

				//Usergruppenid vorhanden
				if (isset($this->setup_array['usergroup_id']) and !empty($this->setup_array['usergroup_id']))
				{
					//User-ID Speichern
					$this->setup_array['user_id'] = USER_ID;

					/**
					 * Moegliche Zugriffsrechte.
					 * Dieses Array sollte bei neuen Zugriffsrechten erweitert werden. Man erspart sich so eine isset() Pruefung des Returnarrays der Funktion chk
					 * r = read Lesen ist das kleinste recht
					 * w = write Bearbeiten
					 * d = del Loeschen
					 * vo = VersionOverview Versionsuebersicht
					 * va = VersionAdd Version anlegen
					 * dl = Downloadlink erstellen
					 * as = AccessSetup Zugriffseinstellungen bearbeiten
					 * di = Datenimport
					 * de = Datenexport
					 * ocf = Ein- Auscheckstaus &Uuml;berschreiben
					 * mn = Mail Notification
					 */
					//ACHTUNG auf get_access_list() achten
					$this->setup_array['access_types'] = array('r',
																'w',
																'd',
																'vo',
																'va',
																'dl',
																'as',
																'di',
																'de',
																'ocf',
																'mn');

					/**
					 * Da die meisten Dateien keine eigenen Zugriffsrechte haben und sie die Zugriffsberechtigung von
					 * ihrem Verzeichnis erben, werden hier die Rechte des geoeffneten Verzeichnisses zwischengespeichert.
					 */
					if (isset($_GET['fid_int']) and $_GET['fid_int'] > 0)
					{
						$this->tmp_array['access_cache'] = $this->chk_folder($_GET['fid_int']);
					}
				}
				else
				{
					$this->setup_array['user_id'] = 0;
					$this->setup_array['usergroup_id'] = array();
					$this->setup_array['usergroup_id'][] = 0;
				}
			}
			else
			{
				$this->setup_array['user_id'] = 0;
				$this->setup_array['usergroup_id'] = array();
				$this->setup_array['usergroup_id'][] = 0;
			}
		}

		/**
		 * Speichert die UserID bzw. UsergruppenID
		 * @param int $user_id
		 * @param array $usergroup_id_array
		 */
		public function set_foreign_key($user_id = 0, $usergroup_id_array = array())
		{
			//achtung eins von beiden sollte schon einen Wert inhalten
			if (!empty($user_id) or !empty($usergroup_id_array))
			{
				$this->setup_array['foreign_key'] = true;

				$this->setup_array['foreign_user_id'] = $user_id;
				$this->setup_array['foreign_usergroup_id'] = $usergroup_id_array;
			}
			else
			{
				$this->setup_array['foreign_key'] = false;
			}
		}

		/**
		 * Gibt die gespeicherte UserID zurueck
		 * @return int
		 */
		private function get_user_id()
		{
			if ($this->setup_array['foreign_key'] === false)
			{
				return $this->setup_array['user_id'];
			}
			else
			{
				return $this->setup_array['foreign_user_id'];
			}
		}

		/**
		 * Gibt die gespeicherte UsergruppenID zurueck
		 * @return array
		 */
		private function get_usergroup_id()
		{
			if ($this->setup_array['foreign_key'] === false)
			{
				return $this->setup_array['usergroup_id'];
			}
			else
			{
				return $this->setup_array['foreign_usergroup_id'];
			}
		}

		/**
		 * Prueft die Zugriffsrechte. Wenn als Return false geliefert wird ist kein zugriff erlaubr.
		 * @param string $type
		 * @param string $access index von $this->setup_array['access_types']
		 * @param int $id
		 * @return mixed
		 */
		public function chk($type, $access = '', $id = 0)
		{
			$return_array = array();
			$this->access_typ = $access;

			$type = strtolower($type);

			if ($this->get_user_id() > 0)
			{
				//Kleinste Ebene Dateipruefung
				if ($type == 'file')
				{
					return $this->chk_file($id);
				}
				//Kleinste Ebene Linkpruefung
				elseif ($type == 'link')
				{
					return $this->chk_link($id);
				}
				//Verzeichnispruefung
				elseif ($type == 'folder')
				{
					return $this->chk_folder($id);
				}
				//Projektpruefung
				elseif ($type == 'project')
				{
					return $this->chk_project($id);
				}
				// Sonstige Pruefungen z.B. Userverwaltung, Setup usw.
				else
				{
					return $this->chk_other($type);
				}

				return false;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Gleicht die uebergebenen Zugriffsrechte mit $this->setup_array['access_types'] ab
		 * @param array
		 * @return array
		 */
		public function verify_access($access_array)
		{
			for($i = 0; $i < count($this->setup_array['access_types']); $i++)
			{
				//nicht vorhandenen index erstellen
				if (!isset($access_array[$this->setup_array['access_types'][$i]]))
				{
					$access_array[$this->setup_array['access_types'][$i]] = false;
				}
				//Pruefen das auch wirklich nur true oder false im array steht
				elseif($access_array[$this->setup_array['access_types'][$i]] != true and $access_array[$this->setup_array['access_types'][$i]] != false)
				{
					$access_array[$this->setup_array['access_types'][$i]] = false;
				}
			}
			return $access_array;
		}

		/**
		 * Prueft ob das Abgefragte Zugriffsrecht fuer dieses Access Array vorhanden ist
		 * @param array $access_array
		 * @return boole
		 */
		private function access_exists($access_array)
		{
			if (!empty($this->access_typ))
			{
				$access_array = $this->verify_access($access_array);
				if (isset($access_array[$this->access_typ]) and $access_array[$this->access_typ] == true)
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
				return false;
			}
		}

		/**
		 * Dateizugriffsrechte Pruefen
		 * @param int $file_id
		 * @return boole
		 */
		private function chk_file($file_id)
		{
			//Dateizugriffsrechte fuer User Pruefen
			$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FILE' AND id=$file_id AND user_id=".$this->get_user_id());
			$result = $this->cdb->fetch_array($sql);

			//Zugriffsrechte fuer den User fuer diese Datei vorhanden
			if (!empty($result['access']))
			{
				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer diese Datei
			$user_group_id_array = $this->get_usergroup_id();
			foreach($user_group_id_array as $ug_id_value)
			{
				//Usergruppenzugriffsrechte Pruefen
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FILE' AND id=$file_id AND usergroup_id=".$ug_id_value);
				$result = $this->cdb->fetch_array($sql);

				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}

			//Naechsthoehere Ebene Pruefen
			$sql = $this->cdb->select('SELECT folder_id FROM fom_files WHERE file_id='.$file_id);
			$result = $this->cdb->fetch_array($sql);

			return $this->chk_folder($result['folder_id']);
		}

		/**
		 * Linkzugriffsrechte Pruefen
		 * @param int $link_id
		 * @return boole
		 */
		private function chk_link($link_id)
		{
			//pruefen ob link ein interner Dateilink ist
			$sql = $this->cdb->select('SELECT file_id FROM fom_link WHERE link_id='.$link_id);
			$result = $this->cdb->fetch_array($sql);

			if (isset($result['file_id']) and $result['file_id'] > 0)
			{
				return $this->chk_file($result['file_id']);
			}
			else
			{
				//Dateizugriffsrechte fuer User Pruefen
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='LINK' AND id=$link_id AND user_id=".$this->get_user_id());
				$result = $this->cdb->fetch_array($sql);


				//Zugriffsrechte fuer den User fuer diesen Link vorhanden
				if (!empty($result['access']))
				{
					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						//Pruefen ob ein zugriffsrecht vorhanden ist
						if ($this->access_exists($tmp_auth_array) === true)
						{
							return true;
						}
					}
				}

				//Zugriffsrechte fuer die Usergruppen fuer diese Link
				$user_group_id_array = $this->get_usergroup_id();
				foreach($user_group_id_array as $ug_id_value)
				{
					//Usergruppenzugriffsrechte Pruefen
					$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='LINK' AND id=$link_id AND usergroup_id=".$ug_id_value);
					$result = $this->cdb->fetch_array($sql);

					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						//Pruefen ob ein zugriffsrecht vorhanden ist
						if ($this->access_exists($tmp_auth_array) === true)
						{
							return true;
						}
					}
				}

				//Naechsthoehere Ebene Pruefen
				$sql = $this->cdb->select('SELECT folder_id FROM fom_link WHERE link_id='.$link_id);
				$result = $this->cdb->fetch_array($sql);

				return $this->chk_folder($result['folder_id']);
			}
		}

		/**
		 * Verzeichniszugriffsrechte Pruefen
		 * @param int $folder_id
		 * @return boole
		 */
		private function chk_folder($folder_id)
		{
			//Verzeichniszugriffsrechte fuer User Pruefen
			$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FOLDER' AND id=$folder_id AND user_id=".$this->get_user_id());
			$result = $this->cdb->fetch_array($sql);

			//Zugriffsrechte fuer den User fuer dieses Verzeichnis vorhanden
			if (!empty($result['access']))
			{
				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer diese Verzeichnis
			$user_group_id_array = $this->get_usergroup_id();
			foreach($user_group_id_array as $ug_id_value)
			{
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FOLDER' AND id=$folder_id AND usergroup_id=".$ug_id_value);
				$result = $this->cdb->fetch_array($sql);

				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}

			//Naechsthoehere Ebene Pruefen
			$sql = $this->cdb->select('SELECT projekt_id, ob_folder FROM fom_folder WHERE folder_id='.$folder_id);
			$result = $this->cdb->fetch_array($sql);

			if ($result['ob_folder'] > 0)
			{
				return $this->chk_folder($result['ob_folder']);
			}
			elseif ($result['projekt_id'] > 0)
			{
				return $this->chk_project($result['projekt_id']);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Projektzugriffsrechte Pruefen
		 * @param int $project_id
		 * @return boole
		 */
		private function chk_project($project_id)
		{
			//Projektzugriffsrechte fuer User Pruefen
			$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='PROJECT' AND id=$project_id AND user_id=".$this->get_user_id());
			$result = $this->cdb->fetch_array($sql);

			//Zugriffsrechte fuer den User fuer dieses Projekt vorhanden
			if (!empty($result['access']))
			{
				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer dieses Projekt
			$user_group_id_array = $this->get_usergroup_id();
			foreach($user_group_id_array as $ug_id_value)
			{
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='PROJECT' AND id=$project_id AND usergroup_id=".$ug_id_value);
				$result = $this->cdb->fetch_array($sql);

				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}
			//keine Zugriffsrechte gefunden
			return false;
		}

		/**
		 * Ueberprueft Zugriffsrechte, die sich nicht auf Projekte, Verzeichnisse bzw. Dateien beziehen
		 * @param string $type
		 * @return boole
		 */
		private function chk_other($type)
		{
			$type = strtoupper($type);

			//Sonstige Zugriffsrechte fuer User Pruefen
			$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='$type' AND user_id=".$this->get_user_id());
			$result = $this->cdb->fetch_array($sql);

			//Zugriffsrechte fuer den User fuer dieses Projekt vorhanden
			if (!empty($result['access']))
			{
				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer dieses Projekt
			$user_group_id_array = $this->get_usergroup_id();
			foreach($user_group_id_array as $ug_id_value)
			{
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='$type' AND usergroup_id=".$ug_id_value);
				$result = $this->cdb->fetch_array($sql);

				$tmp_auth_array = @unserialize($result['access']);
				if (is_array($tmp_auth_array))
				{
					//Pruefen ob ein zugriffsrecht vorhanden ist
					if ($this->access_exists($tmp_auth_array) === true)
					{
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Erstellt ein Array mit den Zugriffsrechten des Angemeldeten Users zu einer Datei, Link, Verzeichnis oder Projekt
		 * @param string $type
		 * @param int $id
		 * @return array
		 */
		public function get_access($type, $id = 0)
		{
			$type = strtolower($type);

			//Kleinste Ebene Dateipruefung
			if ($type == 'file')
			{
				return $this->get_access_file($id);
			}
			//Kleinste Ebene Linkpruefung
			elseif ($type == 'link')
			{
				return $this->get_access_link($id);
			}
			//Verzeichnispruefung
			elseif ($type == 'folder')
			{
				return $this->get_access_folder($id);
			}
			//Projektpruefung
			elseif ($type == 'project')
			{
				return $this->get_access_project($id);
			}
			// Sonstige Pruefungen z.B. Userverwaltung, Setup usw.
			else
			{
				return $this->get_access_other($type);
			}

			//leeres array nur mit false erzeugen
			$return_array['user_access'] = $this->verify_access(array());
			return $return_array;
		}

		/**
		 * Gibt alle Zugriffsrechte des Aktuell angemeldeten Users zu einer Datei zurueck
		 * @param int $file_id
		 * @return array
		 */
		private function get_access_file($file_id)
		{
			$return_array = array();
			if ($this->get_user_id() > 0)
			{
				//Dateizugriffsrechte fuer User Pruefen
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FILE' AND id=$file_id AND user_id=".$this->get_user_id());
				$result = $this->cdb->fetch_array($sql);

				//Zugriffsrechte fuer den User fuer diese Datei vorhanden
				if (!empty($result['access']))
				{
					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['user_access'] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer diese Datei
			$user_group_id_array = $this->get_usergroup_id();
			if (is_array($user_group_id_array) and !empty($user_group_id_array))
			{
				foreach($user_group_id_array as $ug_id_value)
				{
					//Usergruppenzugriffsrechte Pruefen
					$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FILE' AND id=$file_id AND usergroup_id=".$ug_id_value);
					$result = $this->cdb->fetch_array($sql);

					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['usergroup_access'][$ug_id_value] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			//keine Rechte auf Dateiebene vorhanden naechsthoehere Ebene abfragen
			if (empty($return_array))
			{
				//Naechsthoehere Ebene Pruefen
				$sql = $this->cdb->select('SELECT folder_id FROM fom_files WHERE file_id='.$file_id);
				$result = $this->cdb->fetch_array($sql);

				return $this->get_access_folder($result['folder_id']);
			}
			else
			{
				return $return_array;
			}
		}

		/**
		 * Gibt alle Zugriffsrechte des Aktuell angemeldeten Users zu einem Link zurueck
		 * @param int $link_id
		 * @return array
		 */
		private function get_access_link($link_id)
		{
			//pruefen ob link ein interner Dateilink ist
			$sql = $this->cdb->select('SELECT file_id FROM fom_link WHERE link_id='.$link_id);
			$result = $this->cdb->fetch_array($sql);

			if (isset($result['file_id']) and $result['file_id'] > 0)
			{
				return $this->get_access_file($result['file_id']);
			}
			else
			{
				$return_array = array();
				if ($this->get_user_id() > 0)
				{
					//Dateizugriffsrechte fuer User Pruefen
					$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='LINK' AND id=$link_id AND user_id=".$this->get_user_id());
					$result = $this->cdb->fetch_array($sql);


					//Zugriffsrechte fuer den User fuer diesen Link vorhanden
					if (!empty($result['access']))
					{
						$tmp_auth_array = @unserialize($result['access']);
						if (is_array($tmp_auth_array))
						{
							$return_array['user_access'] = $this->verify_access($tmp_auth_array);
						}
					}
				}

				//Zugriffsrechte fuer die Usergruppen fuer diese Link
				$user_group_id_array = $this->get_usergroup_id();
				if (is_array($user_group_id_array) and !empty($user_group_id_array))
				{
					foreach($user_group_id_array as $ug_id_value)
					{
						//Usergruppenzugriffsrechte Pruefen
						$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='LINK' AND id=$link_id AND usergroup_id=".$ug_id_value);
						$result = $this->cdb->fetch_array($sql);

						$tmp_auth_array = @unserialize($result['access']);
						if (is_array($tmp_auth_array))
						{
							$return_array['usergroup_access'][$ug_id_value] = $this->verify_access($tmp_auth_array);
						}
					}
				}

				if (empty($return_array))
				{
					//Naechsthoehere Ebene Pruefen
					$sql = $this->cdb->select('SELECT folder_id FROM fom_link WHERE link_id='.$link_id);
					$result = $this->cdb->fetch_array($sql);

					return $this->get_access_folder($result['folder_id']);
				}
				else
				{
					return $return_array;
				}
			}
		}

		/**
		 * Gibt alle Zugriffsrechte des Aktuell angemeldeten Users zu einem Verzeichnis zurueck
		 * @param int $folder_id
		 * @return array
		 */
		private function get_access_folder($folder_id)
		{
			$return_array = array();
			if ($this->get_user_id() > 0)
			{
				//Verzeichniszugriffsrechte fuer User Pruefen
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FOLDER' AND id=$folder_id AND user_id=".$this->get_user_id());
				$result = $this->cdb->fetch_array($sql);

				//Zugriffsrechte fuer den User fuer dieses Verzeichnis vorhanden
				if (!empty($result['access']))
				{
					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['user_access'] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer diese Verzeichnis
			$user_group_id_array = $this->get_usergroup_id();
			if (is_array($user_group_id_array) and !empty($user_group_id_array))
			{
				foreach($user_group_id_array as $ug_id_value)
				{
					$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='FOLDER' AND id=$folder_id AND usergroup_id=".$ug_id_value);
					$result = $this->cdb->fetch_array($sql);

					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['usergroup_access'][$ug_id_value] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			if (empty($return_array))
			{
				//Naechsthoehere Ebene Pruefen
				$sql = $this->cdb->select('SELECT projekt_id, ob_folder FROM fom_folder WHERE folder_id='.$folder_id);
				$result = $this->cdb->fetch_array($sql);

				if ($result['ob_folder'] > 0)
				{
					return $this->get_access_folder($result['ob_folder']);
				}
				elseif ($result['projekt_id'] > 0)
				{
					return $this->get_access_project($result['projekt_id']);
				}
				else
				{
					//leeres array nur mit false erzeugen
					$return_array['user_access'] = $this->verify_access(array());
					return $return_array;
				}
			}
			else
			{
				return $return_array;
			}
		}

		/**
		 * Gibt alle Zugriffsrechte des Aktuell angemeldeten Users zu einem Projekt zurueck
		 * @param int $project_id
		 * @return array
		 */
		private function get_access_project($project_id)
		{
			$return_array = array();
			if ($this->get_user_id() > 0)
			{
				//Projektzugriffsrechte fuer User Pruefen
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='PROJECT' AND id=$project_id AND user_id=".$this->get_user_id());
				$result = $this->cdb->fetch_array($sql);

				//Zugriffsrechte fuer den User fuer dieses Projekt vorhanden
				if (!empty($result['access']))
				{
					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['user_access'] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer dieses Projekt
			$user_group_id_array = $this->get_usergroup_id();
			if (is_array($user_group_id_array) and !empty($user_group_id_array))
			{
				foreach($user_group_id_array as $ug_id_value)
				{
					$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='PROJECT' AND id=$project_id AND usergroup_id=".$ug_id_value);
					$result = $this->cdb->fetch_array($sql);

					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['usergroup_access'][$ug_id_value] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			if (empty($return_array))
			{
				//leeres array nur mit false erzeugen
				$return_array['user_access'] = $this->verify_access(array());
				return $return_array;
			}
			else
			{
				return $return_array;
			}
		}

		/**
		 * Gibt alle Zugriffsrechte des Aktuell angemeldeten Users zurueck die sich nicht auf Projekte, Verzeichnisse bzw. Dateien beziehen
		 * @param int $type
		 * @return array
		 */
		private function get_access_other($type)
		{
			$return_array = array();
			$type = strtoupper($type);

			if ($this->get_user_id() > 0)
			{
				//Sonstige Zugriffsrechte fuer User Pruefen
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='$type' AND user_id=".$this->get_user_id());
				$result = $this->cdb->fetch_array($sql);

				//Zugriffsrechte fuer den User fuer dieses Projekt vorhanden
				if (!empty($result['access']))
				{
					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['user_access'] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			//Zugriffsrechte fuer die Usergruppen fuer dieses Projekt
			$user_group_id_array = $this->get_usergroup_id();
			if (is_array($user_group_id_array) and !empty($user_group_id_array))
			{
				foreach($user_group_id_array as $ug_id_value)
				{
					$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='$type' AND usergroup_id=".$ug_id_value);
					$result = $this->cdb->fetch_array($sql);

					$tmp_auth_array = @unserialize($result['access']);
					if (is_array($tmp_auth_array))
					{
						$return_array['usergroup_access'][$ug_id_value] = $this->verify_access($tmp_auth_array);
					}
				}
			}

			if (empty($return_array))
			{
				//leeres array nur mit false erzeugen
				$return_array['user_access'] = $this->verify_access(array());
				return $return_array;
			}
			else
			{
				return $return_array;
			}
		}

		/**
		 * Traegt Zugriffsrechte ein bzw. aendert bereits vorhandene
		 * @param string $type
		 * @param int $id
		 * @param int $user_id
		 * @param int $usergroup_id
		 * @param array $access_array
		 * @return boole
		 */
		public function insert($type, $id = 0, $user_id = 0, $usergroup_id = 0, $access_array)
		{
			$type = trim(strtoupper($type));

			//Index kontrollieren
			$access_string = serialize($this->verify_access($access_array));

			//Userzugriffsrechte
			if ($user_id > 0)
			{
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='$type' AND id=$id AND user_id=$user_id");
				$result = $this->cdb->fetch_array($sql);

				//Update
				if (!empty($result['access']))
				{
					if ($this->cdb->update("UPDATE fom_access SET access='$access_string' WHERE type='$type' AND id=$id AND user_id=$user_id"))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				//Insert
				else
				{
					if ($this->cdb->insert("INSERT INTO fom_access (type, id, user_id, access) VALUES ('$type', $id, $user_id, '$access_string')"))
					{
						if ($this->cdb->get_affected_rows() == 1)
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
						return false;
					}
				}
			}
			//Usergruppenzugriffsrechte
			elseif ($usergroup_id > 0)
			{
				$sql = $this->cdb->select("SELECT access FROM fom_access WHERE type='$type' AND id=$id AND usergroup_id=$usergroup_id");
				$result = $this->cdb->fetch_array($sql);

				//Update
				if (!empty($result['access']))
				{
					if ($this->cdb->update("UPDATE fom_access SET access='$access_string' WHERE type='$type' AND id=$id AND usergroup_id=$usergroup_id"))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				//Insert
				else
				{
					if ($this->cdb->insert("INSERT INTO fom_access (type, id, usergroup_id, access) VALUES ('$type', $id, $usergroup_id, '$access_string')"))
					{
						if ($this->cdb->get_affected_rows() == 1)
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
						return false;
					}
				}
			}
			else
			{
				return false;
			}
		}

		/**
		 * Loescht Zugriffsrechte
		 * @param string $type
		 * @param int $id
		 * @param int $user_id
		 * @param int $usergroup_id
		 * @return boole
		 */
		public function delete($type, $id = 0, $user_id = 0, $usergroup_id = 0)
		{
			//FIXME Hier sollte ein Logbucheintrag erfolgen

			$type = trim(strtoupper($type));

			//Userzugriffsrechte
			if ($user_id > 0)
			{
				if ($this->cdb->delete("DELETE FROM fom_access WHERE type='$type' AND id=$id AND user_id=$user_id"))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Usergruppenzugriffsrechte
			elseif ($usergroup_id > 0)
			{
				if ($this->cdb->delete("DELETE FROM fom_access WHERE type='$type' AND id=$id AND usergroup_id=$usergroup_id"))
				{
					return true;
				}
				//Insert
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
		 * Traegt beim Anlegen eines Projektes fuer die Anlegende Usergruppe Vollzugriff ein.
		 * ACHTUNG es wird hierbei davon ausgegangen, dass nur der Admin Projekte anlegt
		 * @param int $project_id
		 * @return boole
		 */
		public function insert_admin($project_id)
		{
			/*
			 * FIXME jedes neu angelegte projekt ist derzeit NUR fuer die ADMIN Benutzergruppe (1) sichtbar!
			*/
			if ($this->setup_array['user_id'] > 0)
			{
				$access_array = array();

				for($i = 0; $i < count($this->setup_array['access_types']); $i++)
				{
					$access_array[$this->setup_array['access_types'][$i]] = true;
				}

				return $this->insert('PROJECT', $project_id, 0, 1, $access_array);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Erstellt aus einem Mehrdimensionalem Zugriffsarray ein eindimensionales
		 * @param array $array
		 * @return array
		 */
		public function simplify_access_array($array)
		{
			$tmp_one_access_array = array();

			//Macht aus dem mehrdimensionalen Accessarray ein eindimensionales
			foreach ($array as $access_authority_from => $access_array)
			{
				if ($access_authority_from == 'user_access')
				{
					foreach($access_array as $access_typ => $access)
					{
						if ($access == true)
						{
							$tmp_one_access_array[$access_typ] = $access;
						}
					}
				}
				elseif ($access_authority_from == 'usergroup_access')
				{
					if (is_array($access_array))
					{
						foreach ($access_array as $project_id => $project_access)
						{
							foreach($project_access as $access_typ => $access)
							{
								if ($access == true)
								{
									$tmp_one_access_array[$access_typ] = $access;
								}
							}
						}
					}
				}
			}

			return $this->verify_access($tmp_one_access_array);
		}

		/**
		 * Prueft die beiden access Arrays. Sollte $foreign_access_array mehr rechte haben als $user_access_array liefert die function false zurueck
		 * @param array $user_access_array
		 * @param array $foreign_access_array
		 * @return boole
		 */
		public function compare_acces_arrays($user_access_array, $foreign_access_array, $access_index = 'all')
		{
			$user_one_access_array = $this->simplify_access_array($user_access_array);
			$foreign_one_access_array = $this->simplify_access_array($foreign_access_array);

			if ($access_index == 'all')
			{
				foreach ($user_one_access_array as $access_typ => $access)
				{
					if ($access == false and $foreign_one_access_array[$access_typ] == true)
					{
						return false;
					}
				}
				return true;
			}
			else
			{
				if ($user_one_access_array[$access_index] == false and $foreign_one_access_array[$access_index] == true)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}

		/**
		 * Gibt ein Array mit den zur Verfuegung stehenden Zugriffspruefungen fuer Projekt, Verzeichnis, Datei zurueck und einer Bezeichnung dazu.
		 * @return array
		 */
		public function get_access_list()
		{
			return array('r'	=>	get_text('access_r','return'),	//Read
						'w'		=>	get_text('access_w','return'),	//Write
						'd'		=>	get_text('access_d','return'),	//Delete
						'vo'	=>	get_text('access_vo','return'),	//Version overview
						'va'	=>	get_text('access_va','return'),	//Add version
						'dl'	=>	get_text('access_dl','return'),	//Create downloadlink
						'as'	=>	get_text('access_as','return'),	//Edit access control
						'di'	=>	get_text('access_di','return'),	//Data import
						'de'	=>	get_text('access_de','return'),	//Data export
						'ocf'	=>	get_text('access_ocf','return'),//Edit check-in/check-out status
						'mn'	=>	get_text('access_mn','return')	//E-Mail Benachrichtigung
						);
		}

		/**
		 * Gibt ein Array mit den sonstigen Zugriffsmoeglichkeiten zurueck.
		 * @return array
		 */
		public function get_other_access_options()
		{
			//ACHTUNG: fuer eine bessere uebersicht in der Tabelle alle Sonstigen Zugriffvarianten mit einem _ anfangen lassen
			return array('_USER_V'		=>	get_text(55,'return'),	//Useraccount management
						'_USER_G'		=>	get_text(56,'return'),	//Usergroup management
						'_PROJECT_V'	=>	get_text(57,'return'),	//Project management
						'_SETUP_V'		=>	get_text(58,'return'),	//Basic setup
						'_LOGBOOK_V'	=>	get_text(360,'return')	//Logbuch
						);
		}

		/**
		 * Prueft ob fuer die Uebergebene Benutzergruppe die E-Mailbenachrichitigung aktiv ist
		 * @param int $project_id
		 * @param int $usergroup_id
		 * @return boole
		 */
		public function mn_exists($project_id, $usergroup_id)
		{
			$cdb = new MySql();

			$sql = $cdb->select("SELECT access FROM fom_access WHERE type='PROJECT' AND id=$project_id AND usergroup_id=$usergroup_id");
			$result = $cdb->fetch_array($sql);

			$access_array = @unserialize($result['access']);

			if (is_array($access_array))
			{
				if (isset($access_array['mn']) and $access_array['mn'] == 1)
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
				return false;
			}
		}
	}
?>