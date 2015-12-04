<?php
	/**
	 * checks user-generated variables for the correct datatypes and removes unwanted characters
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @TODO fuer die Verwendung wird die Funktion setError() aus der Datei error_handler.php benoetigt.
	 * @package file-o-meter
	 */

	/**
	 * checks user-generated variables for the correct datatypes and removes unwanted characters
	 * @package file-o-meter
	 * @subpackage class
	 */
	class ChkVariable
	{
		/**
		 * Bestimmt welche globalen Variablen automatisch geprueft werden. REQUEST wird in der DB nicht verwendet.
		 * @var array
		 */
		private $verify_global_array = array('POST','GET','COOKIE');

		/**
		 * Gibt an ob eine automatische Pruefung erwuenscht ist. Mit Hilfe der function switch_chk_globals() kann dieser Wert veraendert werden.
		 * @var boole
		 */
		private $chk_globals_bool = true;

		/**
		 * Gibt an ob der Datentyp entsprechend der Namensvorgabe geaendert werden soll. Achtung eine aenderung des Datentypes kann einen anderen Variablenwert bewirken.
		 * @var array
		 */
		private $change_datatype_array = array('int' => true, 'float' => true);

		/**
		 * Gibt an ob String Variablen mit addcslashes() und htmlentities() bearbeitet werden sollen.
		 * @var array
		 */
		private $change_string_array = array('slash' => true, 'html' => true);

		/**
		 * Gibt die Anzahl der aufgetretenen Errors zurueck
		 * @var int
		 */
		public $error_count_int = 0;

		/**
		 * Legt fest ob eine automatische Pruefung der Globalen Variablen erfolgen soll.
		 * @param bool $chk_bool
		 * @return void
		 */
		public function switch_chk_globals($chk_bool = true)
		{
			if (is_bool($chk_bool))
			{
				$this->chk_globals_bool = $chk_bool;
			}
			else
			{
				$this->chk_globals_bool = true;
			}
		}

		/**
		 * Prueft Globale Arrays nach den vorgaben von $this->verify_global_array
		 * @return void
		 */
		public function chk_globals()
		{
			if ($this->chk_globals_bool === true)
			{
				//Alle Postwerte Pruefen
				if (isset($_POST) and in_array('POST', $this->verify_global_array) and count($_POST) > 0)
				{
					$this->chk_vars($_POST, 'POST');
				}
				//Alle Getwerte Pruefen
				if (isset($_GET) and in_array('GET', $this->verify_global_array) and count($_GET) > 0)
				{
					$this->chk_vars($_GET, 'GET');
				}
				//Alle Requestwerte Pruefen
				if (isset($_REQUEST) and in_array('REQUEST', $this->verify_global_array) and count($_REQUEST) > 0)
				{
					$this->chk_vars($_REQUEST, 'REQUEST');
				}
				//Alle Cookiewerte Pruefen
				if (isset($_COOKIE) and in_array('COOKIE', $this->verify_global_array) and count($_COOKIE) > 0)
				{
					$this->chk_vars($_COOKIE, 'COOKIE');
				}
			}
		}

		/**
		 * Prueft eine Variable auf einen Korrekten Datentyp
		 * @param mixed $var
		 * @param string $typ
		 * @return bool
		 */
		public function chk_var($var, $typ = 'string')
		{
			$typ = strtolower($typ);
			if ($typ == 'int')
			{
				if (!$this->chk_is_int($var))
				{
					setError(get_text(277, 'return', 'decode_on', array('varname'=>$var, 'datatype'=>'INT')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
					return false;
				}
				else
				{
					return true;
				}
			}
			elseif ($typ == 'float')
			{
				if (!$this->chk_is_float($var))
				{
					setError(get_text(277, 'return', 'decode_on', array('varname'=>$var, 'datatype'=>'FLOAT')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
					return false;
				}
				else
				{
					return true;
				}
			}
			elseif ($typ == 'bool')
			{
				if (!$this->chk_is_bool($var))
				{
					setError(get_text(277, 'return', 'decode_on', array('varname'=>$var, 'datatype'=>'BOOLEAN')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
					return false;
				}
				else
				{
					return true;
				}
			}
			elseif ($typ == 'string')
			{
				if (!$this->chk_is_string($var))
				{
					setError(get_text(277, 'return', 'decode_on', array('varname'=>$var, 'datatype'=>'STRING')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
					return false;
				}
				else
				{
					return true;
				}
			}
			elseif ($typ == 'array')
			{
				if (!$this->chk_is_array($var))
				{
					setError(get_text(277, 'return', 'decode_on', array('varname'=>$var, 'datatype'=>'ARRAY')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
					return false;
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
		 * Prueft die Datentypen eines Arrays
		 * @param mixed $variable
		 * @param string $typ
		 */
		private function chk_vars(&$variable, $typ)
		{
			if (is_array($variable))
			{
				foreach ($variable as $i => &$v)
				{
					//assoziatives array
					if (!is_int($i))
					{
						if (strpos($i, '_int') !== false)
						{
							//Fehler kein int
							if (!$this->chk_is_int($v))
							{
								setError(get_text(277, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'INT')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!

								//Datentyp aendern
								if($this->change_datatype_array['int'] === true)
								{
									setError(get_text(278, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'INT')) ,WARNING,__LINE__);//The data type of variable "[var]varname[/var]" will be changed to [var]datatype[/var]!
									$v = intval($v);
								}
							}
						}
						elseif (strpos($i, '_float') !== false)
						{
							if (!$this->chk_is_float($v, ''))
							{
								if ($this->chk_is_float($v))
								{
									if ($this->change_datatype_array['float'] === true)
									{
										setError(get_text(278, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'FLOAT')) , NOTICE, __LINE__);//The data type of variable "[var]varname[/var]" will be changed to [var]datatype[/var]!
										$v = str_replace(',', '.', $v);
										$v = floatval($v);
										$this->error_count_int--;
									}
								}
								else
								{
									setError(get_text(277, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'FLOAT')) , WARNING, __LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!

									//Datentyp aendern
									if ($this->change_datatype_array['float'] === true)
									{
										setError(get_text(278, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'FLOAT')) , WARNING, __LINE__);//The data type of variable "[var]varname[/var]" will be changed to [var]datatype[/var]!
										$v = floatval($v);
									}
								}
							}
						}
						elseif (strpos($i, '_bool') !== false)
						{
							if (!$this->chk_is_bool($v))
							{
								setError(get_text(277, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'BOOLEAN')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
							}
						}
						elseif (strpos($i, '_string') !== false)
						{
							if (!$this->chk_is_string($v))
							{
								setError(get_text(277, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'STRING')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
							}
							else
							{
								if ($this->change_string_array['html'] === true or $this->change_string_array['slash'] === true)
								{
									$v = $this->modify_string($v);
								}
							}
						}
						elseif (strpos($i, '_array') !== false)
						{
							if (!$this->chk_is_array($v))
							{
								setError(get_text(277, 'return', 'decode_on', array('varname'=>$v, 'datatype'=>'ARRAY')) ,WARNING,__LINE__);//Wrong data type of variable "[var]varname[/var]" - [var]datatype[/var] expected!
							}
							else
							{
								//Selbstaufruf
								$this->chk_vars($v, $typ);
							}
						}
						else
						{
							if (gettype($v) == 'string')
							{
								if ($this->change_string_array['html'] === true or $this->change_string_array['slash'] === true)
								{
									$v = $this->modify_string($v);
								}
							}
							elseif (gettype($v) == 'array')
							{
								//Selbstaufruf
								$this->chk_vars($v, $typ);
							}
							//Sollte im normalbetrieb nicht passieren
							elseif (gettype($v) != 'boolean' and gettype($v) != 'integer' and gettype($v) != 'float' and gettype($v) != 'double')
							{
								setError(get_text(279, 'return', 'decode_on', array('varname'=>'"'.$v.'" - "'.gettype($v).'"')) ,WARNING,__LINE__);//The data type of variable [var]varname[/var] could not be identified!
								$this->error_count_int++;
							}
						}
					}
					//Nummerischer Array Index
					else
					{
						if (gettype($v) == 'string')
						{
							if ($this->change_string_array['html'] === true or $this->change_string_array['slash'] === true)
							{
								$v = $this->modify_string($v);
							}
						}
						elseif (gettype($v) == 'array')
						{
							//Selbstaufruf
							$this->chk_vars($v, $typ);
						}
						//Sollte im normalbetrieb nicht passieren
						elseif (gettype($v) != 'boolean' and gettype($v) != 'integer' and gettype($v) != 'float' and gettype($v) != 'double')
						{
							setError(get_text(279, 'return', 'decode_on', array('varname'=>'"'.$v.'" - "'.gettype($v).'"')) ,WARNING,__LINE__);//The data type of variable [var]varname[/var] could not be identified!
							$this->error_count_int++;
						}
					}
				}
			}
			//kein Array
			else
			{
				if (gettype($variable) == 'string')
				{
					if ($this->change_string_array['html'] === true or $this->change_string_array['slash'] === true)
					{
						$variable = $this->modify_string($variable);
					}
				}
				//Sollte im normalbetrieb nicht passieren
				elseif (gettype($variable) != 'boolean' and gettype($variable) != 'integer' and gettype($variable) != 'float' and gettype($variable) != 'double')
				{
					setError(get_text(279, 'return', 'decode_on', array('varname'=>'"'.$variable.'" - "'.gettype($variable).'"')) ,WARNING,__LINE__);//The data type of variable [var]varname[/var] could not be identified!
					$this->error_count_int++;
				}
			}
		}

		/**
		 * Modifiziert einen string
		 * @param string $var
		 * return string
		 */
		private function modify_string($var)
		{
			$get_magic_quotes_gpc = get_magic_quotes_gpc();

			if ($this->change_string_array['html'] === true)
			{
				if ($get_magic_quotes_gpc == 1)
				{
					$var = stripcslashes($var);
				}

				if (intval(str_replace('.', '', PHP_VERSION)) >= 523)
				{
					//ACHTUNG: das ISO muss gegebenenfalls angepasst werden
					$var = htmlentities($var, ENT_QUOTES, 'UTF-8', false);
				}
				else
				{
					//html Code entfernen um varianten wie &amp;amp; zu vermeiden
					$var = html_entity_decode($var, ENT_QUOTES, 'UTF-8');
					$var = htmlentities($var, ENT_QUOTES, 'UTF-8');
				}
				if ($get_magic_quotes_gpc == 1)
				{
					$var = addslashes($var);
				}
			}

			if ($this->change_string_array['slash'] === true and $get_magic_quotes_gpc == 0)
			{
				$var = addslashes($var);
			}
			return $var;
		}

		/**
		 * Prueft ob die Variable vom Datentyp array ist
		 * @param mixed $var
		 * @return bool
		 */
		private function chk_is_array($var)
		{
			if (is_array($var))
			{
				return true;
			}
			else
			{
				$this->error_count_int++;
				return false;
			}
		}

		/**
		 * Prueft ob die Variable vom Datentyp string ist
		 * @param mixed $var
		 * @return bool
		 */
		private function chk_is_string($var)
		{
			if (is_string($var))
			{
				return true;
			}
			else
			{
				$tmp_var = (string) $var;

				if ("$tmp_var" === "$var")
				{
					return true;
				}
				else
				{
					$this->error_count_int++;
					return false;
				}
			}
		}

		/**
		 * Prueft ob die Variable vom Datentyp boolean ist
		 * @param mixed $var
		 * @return bool
		 */
		private function chk_is_bool($var)
		{
			if (is_bool($var))
			{
				return true;
			}
			else
			{
				$this->error_count_int++;
				return false;
			}
		}

		/**
		 * Prueft ob die Variable vom Datentyp Float ist
		 * @param mixed $var
		 * @return bool
		 */
		private function chk_is_float($var, $type = 'replace')
		{
			if (is_float($var))
			{
				return true;
			}
			elseif ($type == 'replace')
			{
				//wandelt gegebenenfalls vorhandene komma in punkt
				if (substr_count($var, ',') == 1 and substr_count($var, '.') == 0)
				{
					$var = str_replace(',', '.', $var);
				}
				$tmp_var = (float) $var;

				if ("$tmp_var" === "$var")
				{
					return true;
				}
				else
				{
					$this->error_count_int++;
					return false;
				}
			}
			else
			{
				$this->error_count_int++;
				return false;
			}
		}

		/**
		 * Prueft ob die Variable vom Datentyp integer ist
		 * @param mixed $var
		 * @return bool
		 */
		private function chk_is_int($var)
		{
			if (is_int($var))
			{
				return true;
			}
			else
			{
				$tmp_var = (int) $var;

				if ("$tmp_var" === "$var")
				{
					return true;
				}
				else
				{
					$this->error_count_int++;
					return false;
				}
			}
		}
	}
 ?>