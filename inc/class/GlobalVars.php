<?php
	class GlobalVars
	{
		private $setup_array = array();

		public function __construct()
		{
			//Index von $_GET festlegen
			$this->setup_array['get_index'] = FOM_GLOBAL_VAR_NAME.'_string';

			//eventuell vorhandenenen $_GET key zurueckgeben
			//Der $_GET Key sollte nur nicht vorhanden sein beim aufruf aus der Loginklasse
			$this->setup_array['global_key'] = '';
			$this->get_glob_key();

			if (!empty($this->setup_array['global_key']))
			{
				$this->read_glob_data();
			}
		}

		public function __destruct()
		{
			if (!$this->write_glob_data())
			{
				//hmm gute frage und was nu
			}
		}

		/**
		 * Gibt den $_GET indexnamen zurueck
		 * @return string
		 */
		public function get_index_name()
		{
			return $this->setup_array['get_index'];
		}

		/**
		 * Gibt den Aktuellen Key zurueck
		 * @return string
		 */
		public function get_key()
		{
			return $this->setup_array['global_key'];
		}

		/**
		 * Fuegt einem String aus getparametern den global_key hinzu
		 * @param string $get_string
		 * @param string $return
		 * @param string $amp
		 * @return string
		 */
		public function create_get_string($get_string = '', $return = 'return', $amp = '&amp;')
		{
			if (!empty($get_string))
			{
				$get_string .= $amp.$this->setup_array['get_index'].'='.$this->setup_array['global_key'];
			}
			else
			{
				$get_string = '?'.$this->setup_array['get_index'].'='.$this->setup_array['global_key'];
			}

			if ($return == 'return')
			{
				return $get_string;
			}
			else
			{
				echo $get_string;
			}
		}


		/**
		 * Schreibt alle Daten aus $GLOBALS['FOM_VAR'] in die DB
		 * @return boole
		 */
		private function write_glob_data()
		{
			if (!empty($this->setup_array['global_key']))
			{
				$cdb = new MySql();

				if (isset($GLOBALS['FOM_VAR']))
				{
					$glob_data_array = $GLOBALS['FOM_VAR'];
				}
				else
				{
					$glob_data_array = array();
				}

				if (!is_array($glob_data_array))
				{
					$glob_data_array = array();
				}

				if ($cdb->update("UPDATE fom_session SET sess_value='".@serialize($glob_data_array)."',
														sess_expiry='".date("YmdHis", time() + FOM_SESSION_MAX_LIFE)."'
														WHERE sess_key='".$this->setup_array['global_key']."'"))
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
		 * Liest alle Daten aus der DB und Schreibt diese in das Globale Array $GLOBALS['FOM_VAR']
		 * @return boole
		 */
		private function read_glob_data()
		{
			$cdb = new MySql();

			$sql = $cdb->select("SELECT sess_value FROM fom_session WHERE sess_key='".$this->setup_array['global_key']."'");
			$result = $cdb->fetch_array($sql);

			if (isset($result['sess_value']) and !empty($result['sess_value']))
			{
				$glob_data_array = @unserialize($result['sess_value']);

				if (is_array($glob_data_array))
				{
					$GLOBALS['FOM_VAR'] = $glob_data_array;
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
		 * Erstellt einen eindeutigen Key fuer $_GET
		 * Diese Funktion sollte nur von der Login Klasse aufgerufen werden
		 * @return mixed
		 */
		public function create_glob_key()
		{
			$cdb = new MySql();

			$key = md5(uniqid(rand(), true));

			$sql = $cdb->select("SELECT sess_key FROM fom_session WHERE sess_key='$key'");
			$result = $cdb->fetch_array($sql);

			//key wird bereits verwendet einen anderen suchen
			if (isset($result['sess_key']) and !empty($result['sess_key']))
			{
				return $this->create_glob_key();
			}
			else
			{
				$first_value = array();

				$cookie_key = md5(uniqid(rand(), true));

				$cookie_path = '/';
				if (function_exists('parse_url'))
				{
					$parse_url = parse_url(FOM_ABS_URL);

					if (isset($parse_url['path']) and !empty($parse_url['path']))
					{
						//$parse_url['path'] = str_replace('login/index.php', '', $parse_url['path']);

						if (!empty($parse_url['path']))
						{
							$cookie_path = $parse_url['path'];
						}
					}
				}

				if (FOM_LOGIN_COOKIE == true and setcookie(FOM_SESSION_NAME, $cookie_key, 0, $cookie_path))
				{
					$first_value['FOM_SESSION_COOKIE'] = $cookie_key;
					$first_value['FOM_COOKIE_CHK'] = 1;
				}

				//Leerer erst eintrag
				if ($cdb->insert("INSERT INTO fom_session (sess_key, sess_value, sess_expiry) VALUES ('$key', '".serialize($first_value)."', '".date("YmdHis", time() + FOM_SESSION_MAX_LIFE)."')"))
				{
					$this->setup_array['global_key'] = $key;
					$GLOBALS['FOM_VAR'] = $first_value;
					return $key;
				}
				else
				{
					return false;
				}
			}
		}

		/**
		 * Liest den Key aus $_GET aus bzw. gibt den bereits ausgelesenen zurueck
		 * @return string
		 */
		private function get_glob_key()
		{
			if (empty($this->setup_array['global_key']))
			{
				if (isset($_GET[$this->setup_array['get_index']]) and !empty($_GET[$this->setup_array['get_index']]))
				{
					$this->setup_array['global_key'] = $_GET[$this->setup_array['get_index']];
				}
			}
			return $this->setup_array['global_key'];
		}
	}
?>