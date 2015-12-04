<?php
	/**
	 * connects to a MySQL-DB and provides a variety of SQL-Statements
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @TODO fuer die Verwendung wird die Funktion setError() aus der Datei error_handler.php benoetigt.
	 * @package file-o-meter
	 */

	/**
	 * MySQL
	 *
	 * Erstellt eine Verbindung zu einer MySQL DB und uebergibt SQL-Satements.
	 * @author Soeren Pieper soeren.pieper@docemos.de>
	 * @copyright 2007 docemos
	 * @TODO fuer die Verwendung wird die Funktion setError() aus der Datei error_handler.php benoetigt.
	 * @version $Id
	 */

	/**
	 * Enthuelt die Zugangsdaten fuer eine oder mehrere MySql Datenbanken.
	 * Wenn nur eine Mysqldatenbank verwendet wird braucht kein Mehrdimensionales Array erstellt werden z.B.
	 * $MySql_db_setup = array('db_server'=>'localhost', 'db_name'=>'example_db', 'db_user'=>'db_user', 'db_pw'=>'db_pw');
	 * Bei der verwednung mehrerer Datenbank ist ein Mehrdimensionales Array zu erstellen z.B.
	 * $MySql_db_setup = array('db_identifier_1' => array('db_server'=>'localhost', 'db_name'=>'example_db', 'db_user'=>'db_user', 'db_pw'=>'db_pw'),
	 * 							'db_identifier_2' => array('db_server'=>'localhost', 'db_name'=>'example_db', 'db_user'=>'db_user', 'db_pw'=>'db_pw'));
	 * In diesem Falle ist die DB-Bezeichnung "db_identifier_1" bzw. "db_identifier_2" beim Aufruf der Klasse mit zu uebergeben z.B. $db = new MySql('db_identifier_1'); oder $db = new MySql('db_identifier_2');
	 * Die Angabe des Verbindungsports muss nicht erfolgen kann aber under dem index 'db_port'=>'3306' erfolgen
	 * @var array
	 */
	$GLOBALS['MySql_db_setup'] = array('db_server'	=> FOM_DB_SERVER,
										'db_name'	=> FOM_DB_NAME,
										'db_user'	=> FOM_DB_USER,
										'db_pw'		=> FOM_DB_PW,
										'db_port'	=> FOM_DB_PORT,
										'db_socket'	=> FOM_DB_SOCKET);

	/*
	$MySql_db_setup = array('erste'	=> array('db_server'=>'localhost', 'db_name'=>'test_mysql_1', 'db_user'=>'username', 'db_pw'=>'db_pw'),
							'zweite'=> array('db_server'=>'localhost', 'db_name'=>'test_mysql_2', 'db_user'=>'username', 'db_pw'=>'db_pw'));
	*/


	/**
	 * connects to a MySQL-DB and provides a variety of SQL-Statements
	 * @package file-o-meter
	 * @subpackage class
	 */
	class MySql
	{
		/**
		 * Speichert die Zugangsdaten zu einer Datenbank
		 * @var array
		 */
		private $db_connect_setup_array = array();
		public $setup_array = array();

		/**
		 * Legt Fest zu welcher Datenbank eine Verbindung Hergestellt werden soll.
		 *
		 * @param string $db_identifier
		 * @return MySql
		 */
		public function __construct($db_identifier = '')
		{
			//Pruefen ob Datenbankverbindungsdaten vorhanden sind
			if (isset($GLOBALS['MySql_db_setup']))
			{
				//Waerter die zu einem Abbruch bei einem SQL Statment fuehren wuerden
				$this->setup_array['forbidden_command'] = array('sleep', 'benchmark');;

				//nur eine DB wird verwendet
				if (empty($db_identifier))
				{
					//MySQL Socket verwenden
					if (isset($GLOBALS['MySql_db_setup']['db_socket']) and !empty($GLOBALS['MySql_db_setup']['db_socket']))
					{
						$db_server = $GLOBALS['MySql_db_setup']['db_server'].$GLOBALS['MySql_db_setup']['db_socket'];
					}
					//MySQL Port verwenden
					elseif (isset($GLOBALS['MySql_db_setup']['db_port']) and !empty($GLOBALS['MySql_db_setup']['db_port']))
					{
						$db_server = $GLOBALS['MySql_db_setup']['db_server'].':'.$GLOBALS['MySql_db_setup']['db_port'];
					}
					else
					{
						$db_server = $GLOBALS['MySql_db_setup']['db_server'];
					}

					$this->db_connect_setup_array = array('db_server' 	=> $db_server,
															'db_name'	=> $GLOBALS['MySql_db_setup']['db_name'],
															'db_user'	=> $GLOBALS['MySql_db_setup']['db_user'],
															'db_pw'		=> $GLOBALS['MySql_db_setup']['db_pw']);
				}
				else
				{
					if(isset($GLOBALS['MySql_db_setup'][$db_identifier]))
					{
						//Pruefen ob kein Standardport verwendet werden soll
						if (isset($GLOBALS['MySql_db_setup'][$db_identifier]['db_socket']) and !empty($GLOBALS['MySql_db_setup'][$db_identifier]['db_socket']))
						{
							$db_server = $GLOBALS['MySql_db_setup'][$db_identifier]['db_server'].$GLOBALS['MySql_db_setup'][$db_identifier]['db_port'];
						}
						elseif (isset($GLOBALS['MySql_db_setup'][$db_identifier]['db_port']) and !empty($GLOBALS['MySql_db_setup'][$db_identifier]['db_port']))
						{
							$db_server = $GLOBALS['MySql_db_setup'][$db_identifier]['db_server'].':'.$GLOBALS['MySql_db_setup'][$db_identifier]['db_port'];
						}
						else
						{
							$db_server = $GLOBALS['MySql_db_setup'][$db_identifier]['db_server'];
						}

						$this->db_connect_setup_array = array('db_server' 	=> $db_server,
																'db_name'	=> $GLOBALS['MySql_db_setup'][$db_identifier]['db_name'],
																'db_user'	=> $GLOBALS['MySql_db_setup'][$db_identifier]['db_user'],
																'db_pw'		=> $GLOBALS['MySql_db_setup'][$db_identifier]['db_pw']);
					}
					else
					{
						//Fehlermeldung
						setError(get_text(260, 'return'), WARNING, __LINE__);//No connection data found for the database server.
						exit(get_text(259, 'return'));//The connection to the database could not be established.
					}
				}
			}
			else
			{
				//Fehlermeldung
				setError(get_text(260, 'return'), WARNING, __LINE__);//No connection data found for the database server.
				exit(get_text(259, 'return'));//The connection to the database could not be established.
			}
		}

		/**
		 * Stellt eine Verbindung zu einer MySql DB her.
		 * @return resource
		 */
		private function db_connect()
		{
			//Verbindung zum Datenbankserver
			if ($link = @mysql_connect($this->db_connect_setup_array['db_server'], $this->db_connect_setup_array['db_user'], $this->db_connect_setup_array['db_pw']))
			{
				@mysql_set_charset('utf8', $link);
				//Datenbank auswaehlen
				if(!@mysql_select_db($this->db_connect_setup_array['db_name'], $link))
				{
					//Fehlermeldung
					setError(get_text(263, 'return', 'decode_on', array('database'=>$this->db_connect_setup_array['db_name']) ).' '.mysql_error(), WARNING, __LINE__);//The specified database [var]database[/var] could not be found.
					exit(get_text(259, 'return'));//The connection to the database could not be established.
				}
				else
				{
					return $link;
				}
			}
			else
			{
				//Fehlermeldung
				setError(get_text(264, 'return', 'decode_on', array('dbserver'=>$this->db_connect_setup_array['db_server']) ).' '.mysql_error(), WARNING, __LINE__);//The connection to the database server [var]dbserver[/var] could not be established.
				exit(get_text(259, 'return'));//The connection to the database could not be established.
			}
		}

		/**
		 * Entfernt unerwuenschte Zeichen aus einem SQL Statment. Sollten sich Zeichen in einem SQL Statemant befinden die da nicht hingehoeren wird angebrochen.
		 *
		 * @param string $sql
		 * @return mixed
		 */
		public function clear_sql_string($sql)
		{
			$sql = trim($sql);
			//SQL Kommentare entfernen
			$search_array = array('/*','--');
			$replace_array = array(' ',' ');
			str_replace($search_array, $replace_array, $sql);

			//SQL string in kleine Buchstaben wandeln
			$tmp_sql = strtolower($sql);
			//alle SQL Kommentare entfernen
			$tmp_sql = preg_replace('~\/\*(.*?)\*\/~s', '', $tmp_sql);

			//Speichern ob ein unerlaubter Befehl vorhanden ist
			$forbidden_command_exists_bool = false;

			//nicht erlaubte Befehle welche zu einem Abbruch fuehren
			foreach ($this->setup_array['forbidden_command'] as $command)
			{
				if (strpos($tmp_sql, $command) !== false && preg_match('~(^|[^a-z])'.$command.'($|[^[a-z])~s', $tmp_sql) != 0)
				{
					$forbidden_command_exists_bool = true;
				}
			}

			$forbidden_command_array = array('update', 'insert', 'delete', 'load data', 'truncate', 'drop', 'alter', 'show');
			//Subabfragen unterbinden
			foreach ($forbidden_command_array as $command)
			{
				//if (preg_match('~\([^)]*?'.$command.'~s', $tmp_sql) != 0)
				// nach Klammer und 0 oder mehr Leerzeichen gefolgt vom SQL Befehl suchen
				if (preg_match('~\(\s*'.$command.'~s', $tmp_sql) != 0)
				{
					$forbidden_command_exists_bool = true;
				}
			}

			if($forbidden_command_exists_bool)
			{
				//Fehlermeldung
				echo '<br><br>'.$sql.'<br><br>';
				return false;
			}
			else
			{
				return $sql;
			}
		}

		/**
		 * Function fuer eine SELECT Abfrage
		 * @param string $sql
		 * @return mixed
		 */
		public function select($sql)
		{
			return $this->query($sql);
		}

		/**
		 * Function fuer eine INSERT Abfrage
		 * @param string $sql
		 * @return bool
		 */
		public function insert($sql)
		{
			$result = $this->query($sql);
			if($result !== false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Function fuer eine UPDATE Abfrage
		 * @param string $sql
		 * @return bool
		 */
		public function update($sql)
		{
			$result = $this->query($sql);
			if($result !== false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Function fuer eine DELETE Abfrage
		 * @param string $sql
		 * @return bool
		 */
		public function delete($sql)
		{
			$result = $this->query($sql);
			if($result !== false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Liefert die Anzahl der Datensaetze im Ergebnis
		 * @param resource $sql
		 * @return int
		 */
		public function get_records($resource)
		{
			return @mysql_num_rows($resource);
		}

		/**
		 * Liefert die Anzahl betroffener Datensaetze einer vorhergehenden MySQL Operation
		 *
		 * @return int
		 */
		public function get_affected_rows()
		{
			return @mysql_affected_rows();
		}

		/**
		 * Liefert die ID einer vorherigen INSERT-Operation
		 *
		 * @return int
		 */
		public function get_last_insert_id()
		{
			return @mysql_insert_id();
		}

		/**
		 * Liefert einen Datensatz als assoziatives Array, als numerisches Array oder beides
		 *
		 * @param resource $resource
		 * @param define $array
		 * @return array
		 */
		public function fetch_array($resource, $array = MYSQL_ASSOC)
		{
			return @mysql_fetch_array($resource, $array);
		}

		/**
		 * Liefert einen Datensatz als indiziertes Array
		 *
		 * @param resource $resource
		 * @return array
		 */
		public function fetch_row($resource)
		{
			return @mysql_fetch_row($resource);
		}

		/**
		 * Liefert eine Ergebniszeile als Objekt
		 *
		 * @param resource $resource
		 * @return object
		 */
		public function fetch_object($resource)
		{
			return @mysql_fetch_object($resource);
		}

		/**
		 * Function fuer eine SQL Abfrage
		 * @param string $sql
		 * @return resource
		 * @todo exit ausgabe mit mysql_error() entfernen
		 */
		public function query($sql)
		{
			$chk_sql = $this->clear_sql_string($sql);

			if ($chk_sql !== false)
			{
				//$this->log_sql_query($chk_sql);
				if ($result = @mysql_query($chk_sql, $this->db_connect()))
				{
					return $result;
				}
				else
				{
					//$this->log_sql_query($chk_sql);
					//Fehlermeldung
					setError(get_text(261, 'return').' '.mysql_error(), WARNING, __LINE__);//Incorrect MySQL-Query.
					//exit('Der MySql-Query ist Fehlerhaft.');
					//Sollte nicht fuer den Produktiveinsatz verwendet werden!!
					exit(mysql_error().' '.get_text(261, 'return'));//Incorrect MySQL-Query.
				}
			}
			else
			{
				//$this->log_sql_query($chk_sql);
				//Fehlermeldung
				setError(get_text(262, 'return'), ERROR, __LINE__);//Unauthorized MySql-Query.
				exit(get_text(262, 'return'));//Unauthorized MySql-Query.
			}
		}

		/**
		 * Only for tests
		 * @param string $sql
		 * @return void
		 */
		private function log_sql_query($sql)
		{
			$h = @fopen(FOM_ABS_PFAD.'files/log/sql.log', 'a');

			@fwrite($h, date('YmdHis')."\t\t".$sql."\r\n\r\n");

			@fclose($h);
		}
	}
?>