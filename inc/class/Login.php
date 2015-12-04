<?php
	/**
	 * provides several login/logout functions and compares logindata with userdata from database
	 *
	 * Diese Klasse Prueft ob mit den angegebenen Zugangsdaten ein Zugang gewaehrt werden darf.
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2010  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * provides several login/logout functions and compares logindata with userdata from database
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Login
	{
		/**
		* Gibt die Loginsperrzeit in Sekunden an.
		* @var int
		*/
		private $timeout = 600;

		/**
		* Gibt die Anzahl der Loginversuche an.
		* @var int
		*/
		private $login_trials = 3;

		/**
		* Diese Funktion Prueft die angegebenen Userdaten. Bei erfolgreicher Pruefung wird ein define mit der User_Id erstellt.
		* @param string $pw Das unverschluesselte PW vom User.
		* @param string $username Loginname des Users.
		* @return array $return['error'] = array('1=Kein PW vorhanden.','2=Keine Benutzername vorhanden.','4=Zu den Angegebenen Benutzerdaten konnte keine User gefunden werden.','5=Account ist deaktiviert','6=Account ist zur Zeit gesperrt. Siehe $return['timeout']','7=Fehler beim aendern der Userdaten.');
		*/
		public function chk_login($pw, $username)
		{
			//SQL Klasse
			$cdb = new MySql;
			//Passwort
			$cp = new CryptPw;

			//array fuer return meldungen
			$return = array();
			//Pruefen ob Passwort vorhanden
			if (!empty($pw))
			{
				//Pruefen ob Benutzername vorhanden
				if (!empty($username))
				{
					$new_pw = $cp->encode_pw($pw);

					$sql = $cdb->select("SELECT user_id, timeout, login_aktiv FROM fom_user WHERE loginname='$username' AND pw='$new_pw'");
					$result = $cdb->fetch_array($sql);

					//Pruefen ob der User existiert
					if ($result['user_id'] > 0)
					{
						//Ptuefen ob Account aktiv ist
						if ($result['login_aktiv'] == '1')
						{
							//Pruefen ob timeout vorhanden
							if ($result['timeout'] <= time())
							{
								$global_key = $GLOBALS['gv']->create_glob_key();

								//Loginkey und IP Adresse eintragen. Loginversuche und Timeout Wert Nullen.
								if ($cdb->update("UPDATE fom_user SET
												session_key='$global_key',
												login_ip='".$_SERVER['REMOTE_ADDR']."',
												login_trials='0',
												timeout='0'
												WHERE user_id=".$result['user_id']))
								{
									//USER ID in define Schreiben
									define('USER_ID', $result['user_id']);
									$return['login'] = true;
									$return['global_var'] = $global_key;

									//Logbucheintrag
									$log = new Logbook();
									$log->login_insert(USER_ID, 1, $global_key);
								}
								else
								{
									//Fehler beim aendern der Userdaten
									$return['error'][] = 7;
									$return['login'] = false;
								}
							}
							else
							{
								//Timeout ist aktiv
								$return['timeout'] = $result['timeout'];
								$return['error'][] = 6;
								$return['login'] = false;
							}
						}
						else
						{
							//Account ist deaktiviert
							$return['error'][] = 5;
							$return['login'] = false;
						}
					}
					else
					{
						//kein Benutzeraccount vorhanden
						$return['error'][] = 4;
						$return['login'] = false;

						//timeout aktualisieren
						$this->update_timeout($username);
					}
				}
				else
				{
					//kein benutzername vorhanden
					$return['error'][] = 2;
					$return['login'] = false;
				}
			}
			else
			{
				//kein pw vorhanden
				$return['error'][] = 1;
				$return['login'] = false;
			}
			return $return;
		}

		/**
		* Diese Funktion prueft bei jedem Seitenaufruf ob das Login aktiv ist.
		* @param string $global_key
		* @return bool
		*/
		public function chk_login_key($global_key)
		{
			$cdb = new MySql;
			//Prueft ob Loginkey vorhanden
			if (!empty($global_key))
			{
				//Benutzersuche
				$sql = $cdb->select("SELECT user_id FROM fom_user WHERE session_key='$global_key' AND login_ip='".$_SERVER['REMOTE_ADDR']."' AND login_aktiv='1';");
				$result = $cdb->fetch_array($sql);

				//Pruefen ob der User existiert
				if ($result['user_id'] > 0)
				{
					//zusaetzlich cookiepruefung durchfuehren
					if (isset($GLOBALS['FOM_VAR']['FOM_COOKIE_EXISTS']) and $GLOBALS['FOM_VAR']['FOM_COOKIE_EXISTS'] == 1)
					{
						if (isset($_COOKIE[FOM_SESSION_NAME]) and isset($GLOBALS['FOM_VAR']['FOM_SESSION_COOKIE']) and $_COOKIE[FOM_SESSION_NAME] == $GLOBALS['FOM_VAR']['FOM_SESSION_COOKIE'])
						{
							//USER ID in define Schreiben
							define('USER_ID', $result['user_id']);

							return true;
						}
						else
						{
							//alle Sessions zu diesem Benutzer loeschen
							$this->del_session($global_key);
							return false;
						}

					}
					//eunfach Pruefung ohne cookie
					else
					{
						//USER ID in define Schreiben
						define('USER_ID', $result['user_id']);

						return true;
					}
				}
				else
				{
					//alle Sessions zu diesem Benutzer loeschen
					$this->del_session($global_key);
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		/**
		* Zaehlt die Loginversuche.
		* @param string $username
		* @return void
		*/
		private function update_timeout($username)
		{
			$cdb = new MySql;

			//Prueft ob zum Angegebenen Beutzernamen ein Account existiert.
			$sql = $cdb->select("SELECT user_id, login_trials, timeout FROM fom_user WHERE loginname='$username';");
			$result = $cdb->fetch_array($sql);

			//Pruefen ob der User existiert
			if ($result['user_id'] > 0)
			{
				//counter zuruecksetzten wenn der letzte fehlgeschlagene loginversuch laenger her ist als der timeout wert vorgibt
				if ($result['timeout'] + $this->timeout <= time())
				{
					$cdb->update("UPDATE fom_user SET
								login_trials='0',
								timeout='0'
								WHERE user_id=".$result['user_id']);
				}

				//Erster fehlversuch.
				if ($result['login_trials'] == 0)
				{
					$cdb->update("UPDATE fom_user SET
								login_trials='1',
								timeout='".time()."'
								WHERE user_id=".$result['user_id']);
				}
				//letzter Fehlversuch
				elseif ($result['login_trials'] == $this->login_trials)
				{
					$time = time() + $this->timeout;
					$cdb->update("UPDATE fom_user SET
								login_trials=login_trials+1,
								timeout='$time'
								WHERE user_id=".$result['user_id']);
				}
				else
				{
					$cdb->update("UPDATE fom_user SET
								login_trials=login_trials+1
								WHERE user_id=".$result['user_id']);
				}
			}
		}

		/**
		* Logout Funktion.
		* @param string $global_key Loginkey
		* @return void
		*/
		public function logout($global_key = '')
		{
			$this->del_session($global_key);
		}

		/**
		* Loescht alle Sessions.
		* @param string $global_key Loginkey
		* @return void
		*/
		private function del_session($global_key)
		{
			$cdb = new MySql;

			//Pruefen ob Userid vorhanden ist
			if (defined('USER_ID'))
			{
				$user_id = USER_ID;
			}
			else
			{
				$user_id = '';
			}

			if ($user_id > 0)
			{
				$cdb->update('UPDATE fom_user SET session_key=NULL WHERE user_id='.$user_id);
			}
			else
			{
				$cdb->update("UPDATE fom_user SET session_key=NULL WHERE session_key='$global_key'");
			}

			//logbucheintrag
			$log = new Logbook();
			$log->login_insert(0, 0, $global_key);

			//einen speziellen Key loeschen
			$cdb->delete("DELETE FROM fom_session WHERE sess_key='$global_key'");
			//alle die abgelaufen sind loeschen
			$cdb->delete("DELETE FROM fom_session WHERE sess_expiry<'".date('YmdHis', time())."'");
		}

		/**
		* Diese Funktion Prueft die uebergebenen Logindaten fuer den Zugriff auf einen Webservice.
		* @param string $pw Das vom User angegebene Passwort.
		* @param string $username Das vom User angegebene Passwort.
		* @return string false oder md5 hash
		*/
		public function webservice_login($pw, $username)
		{
			//SQL Klasse
			$cdb = new MySql;

			//Pruefen ob Passwort vorhanden
			if (!empty($pw))
			{
				//Pruefen ob Benutzername vorhanden
				if (!empty($username))
				{
					$sql = $cdb->select("SELECT user_id, timeout, login_aktiv FROM fom_user WHERE loginname='$username' AND pw='$pw'");
					$result = $cdb->fetch_array($sql);

					//Pruefen ob der User existiert
					if ($result['user_id'] > 0)
					{
						//Ptuefen ob Account aktiv ist
						if ($result['login_aktiv'] == '1')
						{
							//Pruefen ob timeout vorhanden
							if ($result['timeout'] <= time())
							{
								$key = md5(uniqid(rand(), true));
								$expire = date('YmdHis', time() + 3600);

								if ($cdb->insert("INSERT INTO fom_webservice_access (ws_key, user_id, expire) VALUES ('$key', ".$result['user_id'].", '$expire')"))
								{
									$log = new Logbook();
									$log->webservice_login_insert($result['user_id']);
									return $key;
								}
								else
								{
									return 'false';
								}
							}
							else
							{
								return 'false';
							}
						}
						else
						{
							return 'false';
						}
					}
					else
					{
						return 'false';
					}
				}
				else
				{
					return 'false';
				}
			}
			else
			{
				return 'false';
			}
		}

		/**
		 * Prueft den uebergebenen Loginkey fuer den Zugriff auf Webservice Funktionen
		 * @param string $key
		 * @return boole
		 */
		public function webservice_key($key)
		{
			//SQL Klasse
			$cdb = new MySql;

			$sql = $cdb->select("SELECT ws_key FROM fom_webservice_access WHERE ws_key='$key' AND expire>'".date('YmdHis')."'");
			$result = $cdb->fetch_array($sql);

			if (isset($result['ws_key']) and !empty($result['ws_key']))
			{
				$cdb->update("UPDATE fom_webservice_access SET expire='".date('YmdHis', time() + 3600)."' WHERE ws_key='$key'");

				return 'true';
			}
			else
			{
				return 'false';
			}
		}
	}
?>