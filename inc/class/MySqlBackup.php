<?php
	/**
	 * creates a MySQL backup
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * creates a MySQL backup
	 * @package file-o-meter
	 * @subpackage class
	 */
	class MySqlBackup
	{
		/**
		* Speichert die Dumpinformarionen fuer eine Spaetere Ausgabe
		* @var
		*/
		public $dump_info = array();

		/**
		 * Speichert alle Grundeinstellungen
		 * @var
		 */
		private $dump_setup = array();

		/**
		 * Legt die Grundeinstellungen fest
		 */
		public function __construct()
		{
			//Nur Ausfuehren wenn mysqldump vorhanden ist
			if (defined('FOM_MYSQL_EXEC') and defined('FOM_MYSQL_DUMP') and FOM_MYSQL_EXEC != '' and FOM_MYSQL_DUMP != '')
			{
				$this->dump_setup = array('tmp_pfad'		=> FOM_ABS_PFAD.'files/tmp/',
										'save_pfad'			=> FOM_ABS_PFAD.'files/backup/',
										'mysql_exec'		=> FOM_MYSQL_EXEC,
										'mysql_dump_exec'	=> FOM_MYSQL_DUMP,
										'db_user'			=> FOM_DB_USER,
										'db_pw'				=> FOM_DB_PW,
										'db_server'			=> FOM_DB_SERVER,
										'db_name'			=> FOM_DB_NAME,
										'db_port'			=> FOM_DB_PORT,
										'db_socket'			=> FOM_DB_SOCKET);
			}
			else
			{
				$this->dump_setup = array('tmp_pfad'		=> FOM_ABS_PFAD.'files/tmp/',
										'save_pfad'			=> FOM_ABS_PFAD.'files/backup/',
										'db_user'			=> FOM_DB_USER,
										'db_pw'				=> FOM_DB_PW,
										'db_server'			=> FOM_DB_SERVER,
										'db_name'			=> FOM_DB_NAME,
										'db_port'			=> FOM_DB_PORT,
										'db_socket'			=> FOM_DB_SOCKET);
			}

		}

		/**
		* Erstellt einen Dump einer MySql DB
		* @param string $typ, Gibt an ob es sich um ein automatisches bzw. manuelles Backup handelt
		* @param string $txt
		* @param string $save_pfad, Achtung braucht nicht angegeben werden es sei denn es soll ein anderer Speicherort als der Standard genommen werden
		* @param return boole
		* @function
		*/
		public function create_dump($typ = 'auto', $txt = 'Full-Backup', $save_pfad = '')
		{
			$dumpresult = false;
			$file_name = 'fom_'.$this->dump_setup['db_name'].'_'.date("YmdHis").'.sql';

			if (empty($save_pfad))
			{
				$save_pfad = $this->dump_setup['save_pfad'];
			}

			//Backup mit Hilfe von mysqldump erstellen
			if (isset($this->dump_setup['mysql_dump_exec']))
			{
				$dumpresult = $this->create_dump_with_mysqldump($file_name, $save_pfad);
			}
			//Manuelles Backup. Dies ist nicht zu empfehlen da es einfach zu lange dauert.
			else
			{
				$dumpresult = $this->create_dump_without_mysqldump($file_name, $save_pfad);
			}

			/*
			$abfrage = $this->dump_setup['mysql_dump_exec']." -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server']." --add-drop-table ".$this->dump_setup['db_name']." > ".$save_pfad.$file_name;
			system($abfrage, $fp);
			if ($fp == 0)
			*/

			//Backup wurde erstellt
			if ($dumpresult == true)
			{
				//Backupinfo hinzufuegen
				if ($this->add_dump_info($save_pfad.$file_name))
				{
					//Datei komprimieren
					$compress_file_name = $this->dump_compress($save_pfad, $file_name);
					if ($compress_file_name !== false)
					{
						if ($this->save_dump($save_pfad, $compress_file_name, $typ, $txt))
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
				else
				{
					return false;
				}
			}
		}

		/**
		 * Erstellt ein Backup ohne mysqldump. Dies ist nicht zu empfehlen
		 * @param string $file_name
		 * @param string $save_pfad
		 * @return boole
		 */
		private function create_dump_without_mysqldump($file_name, $save_pfad)
		{
			$cdb = new MySql();

			$table_array = array();

			$sql = $cdb->query('SHOW TABLES FROM '.$this->dump_setup['db_name']);
			while($result = $cdb->fetch_row($sql))
			{
				$table_array[] = $result[0];
			}

			$table_count = count($table_array);

			if ($bf = fopen($save_pfad.$file_name, 'w'))
			{
				$sql_dump_string = "-- MySql Dump\r\n";
				$sql_dump_string .= "-- Create without mysqldump\r\n";
				$sql_dump_string .= "-- ".date('YmdHis')."\r\n\r\n\r\n\r\n";

				//Allgemeine Dumpinfos
				fwrite($bf, $sql_dump_string);

				//Create Table Syntax
				$sql_dump_string = '';
				for ($i = 0; $i < $table_count; $i++)
				{
					$sql = $cdb->query('SHOW CREATE TABLE '.$table_array[$i]);
					$result = $cdb->fetch_row($sql);

					$sql_dump_string .= '-- Create Table for '.$result[0]."\r\n\r\n";
					$sql_dump_string .= 'DROP TABLE IF EXISTS `'.$result[0]."`;\r\n";
					$sql_dump_string .= $result[1].";\r\n\r\n";
				}

				//Create Table Syntax
				fwrite($bf, $sql_dump_string);

				//Tabellen Inhalt
				$sql_dump_string = '';
				$table_struktur_array = array();
				for ($i = 0; $i < $table_count; $i++)
				{
					$table = $table_array[$i];

					//Spalteninformationen auslesen
					$sql = $cdb->query("SHOW COLUMNS FROM $table");
					while($result = $cdb->fetch_array($sql))
					{
						if (strpos($result['Type'], 'int') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'float') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'tinyint') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'smallint') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'mediumint') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'integer') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'bigint') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'double') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'real') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'decimal') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						elseif (strpos($result['Type'], 'numeric') !== false)
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'numeric';
						}
						else
						{
							$table_struktur_array[$table][$result['Field']]['type'] = 'string';
						}

						//Default ist NULL
						if (is_null($result['Default']))
						{
							$table_struktur_array[$table][$result['Field']]['null'] = true;
						}
						else
						{
							$table_struktur_array[$table][$result['Field']]['null'] = false;
						}
					}

					$sql_dump_string = '';
					$data_count = 0;
					$sql = $cdb->select('SELECT * FROM '.$table);
					while ($result = $cdb->fetch_array($sql))
					{
						//SQL String ist leer neu beginnen
						if (empty($sql_dump_string))
						{
							$sql_dump_string = '(';
						}
						else
						{
							$sql_dump_string .= ",\r\n(";
						}

						//Anzahl der Spalten in der Tabelle
						$column_count = count($result) - 1;
						$count = 0;
						foreach ($result as $column => $data)
						{
							//keine Daten vorhanden
							if (_empty($data))
							{
								//default ist NULL
								if ($table_struktur_array[$table][$column]['null'] == true and is_null($data))
								{
									$value = 'NULL';
								}
								else
								{
									$value = "''";
								}
							}
							//Daten vorhanden
							else
							{
								//Daten sind zahlen
								if ($table_struktur_array[$table][$column]['type'] == 'numeric')
								{
									$value = $data;
								}
								//String mit anfuehrungszeichen schreiben
								else
								{
									$data = str_replace("\r", '\r', $data);
									$data = str_replace("\n", '\n', $data);
									$data = str_replace("'", "\'", $data);
									$value = "'$data'";
								}
							}

							//Bei Letzter Spalte kein Komma ans ende setzen
							if ($count == $column_count)
							{
								$sql_dump_string .= $value;
							}
							else
							{
								$sql_dump_string .= "$value, ";
							}
							$count++;
						}

						//Zeile abschliessen
						$sql_dump_string .= ')';

						$data_count++;
						//Aller 250 Datensaetze ein neues Insert beginnen
						if ($data_count == 250)
						{
							$data_count = 0;

							if (!empty($sql_dump_string))
							{
								$sql_dump_string = "-- Insert for $table \r\n\r\nINSERT INTO $table VALUES $sql_dump_string;\r\n\r\n";

								fwrite($bf, $sql_dump_string);
								$sql_dump_string = '';
							}
						}
					}

					//Letes Satement erfassen
					if (!empty($sql_dump_string))
					{
						$sql_dump_string = "-- Insert for $table \r\n\r\nINSERT INTO $table VALUES $sql_dump_string;\r\n\r\n";

						fwrite($bf, $sql_dump_string);
					}
				}

				fclose($bf);
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Erstellt ein Backup mit hilfe von mysqldump
		 * @param string $file_name
		 * @param string $save_pfad
		 * @return boole
		 */
		private function create_dump_with_mysqldump($file_name, $save_pfad)
		{
			//MySQL Socket verwenden
			if (!empty($this->dump_setup['db_socket']))
			{
				//gegebenenfalls fuehrenden : entfernen
				if (substr($this->dump_setup['db_socket'], 0, 1) == ':')
				{
					$abfrage = $this->dump_setup['mysql_dump_exec']." -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server']." -S ".substr($this->dump_setup['db_socket'], 1)." --add-drop-table ".$this->dump_setup['db_name']." > ".$save_pfad.$file_name;
				}
				else
				{
					$abfrage = $this->dump_setup['mysql_dump_exec']." -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server']." -S ".$this->dump_setup['db_socket']." --add-drop-table ".$this->dump_setup['db_name']." > ".$save_pfad.$file_name;
				}
			}
			//MySQL Port verwenden
			elseif (!empty($this->dump_setup['db_port']))
			{
				$abfrage = $this->dump_setup['mysql_dump_exec']." -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server'].":".$this->dump_setup['db_port']." --add-drop-table ".$this->dump_setup['db_name']." > ".$save_pfad.$file_name;
			}
			else
			{
				$abfrage = $this->dump_setup['mysql_dump_exec']." -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server']." --add-drop-table ".$this->dump_setup['db_name']." > ".$save_pfad.$file_name;
			}

			system($abfrage, $fp);
			if ($fp == 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Pruefen ob in der aktuellen Stunde schon ein Dump erstellt wurde
		 * @return boole
		 */
		public function dump_exists()
		{
			$cdb = new MySql;

			$sql = $cdb->select("SELECT backup_id FROM fom_backup WHERE LEFT(backup_time, 10)='".date("YmdH")."'");
			$result = $cdb->fetch_array($sql);

			if (isset($result['backup_id']) and $result['backup_id'] > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		* Spielt eine SQL Datei in die Datenbank ein
		* @param string $file
		* @param string $save_pfad, Achtung kann leer bleiben es sei denn das Backup liegt nicht im Standard Ordner
		* @param return boole
		* @function
		*/
		public function restore_dump($file, $save_pfad = '')
		{
			if (empty($save_pfad))
			{
				$save_pfad = $this->dump_setup['save_pfad'];
			}

			if (file_exists($save_pfad.$file))
			{
				if (copy($save_pfad.$file, $this->dump_setup['tmp_pfad'].$file))
				{
					if (file_exists($this->dump_setup['tmp_pfad'].$file))
					{
						//SQL Dump ist gepackt
						if (strtolower(substr($file, -3)) == '.gz')
						{
							$uncompress_dump_file = $this->dump_uncompress($this->dump_setup['tmp_pfad'], $file, 'gz');

							if ($uncompress_dump_file === false)
							{
								return false;
							}
							$new_filename = $this->dump_setup['tmp_pfad'].$uncompress_dump_file;
						}
						elseif (strtolower(substr($file, -3)) == 'zip')
						{
							$uncompress_dump_file = $this->dump_uncompress($this->dump_setup['tmp_pfad'], $file, 'zip');

							if ($uncompress_dump_file === false)
							{
								return false;
							}
						}
						else
						{
							$new_filename = $this->dump_setup['tmp_pfad'].$file;
						}

						if (file_exists($new_filename))
						{
							if (strtolower(substr($new_filename, -3)) == 'sql')
							{
								//Backup ueber die MysqlExec wiederherstellen
								if (isset($this->dump_setup['mysql_exec']))
								{
									$backup_restore = $this->restore_dump_with_mysqlexec($new_filename);
								}
								else
								{
									$backup_restore = $this->restore_dump_without_mysqlexec($new_filename);
								}

								//tmp Dateien loeschen
								$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
								$this->del_dump_file($new_filename);

								return $backup_restore;
							}
							else
							{
								//tmp Dateien loeschen
								$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
								$this->del_dump_file($new_filename);
								return false;
							}
						}
						else
						{
							//tmp Dateien loeschen
							$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
							return false;
						}
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
			else
			{
				return false;
			}
		}

		/**
		 * Stellt Backup ohne MySqlExec wiederher.
		 * @param $new_filename
		 * @return unknown_type
		 */
		public function restore_dump_without_mysqlexec($new_filename)
		{
			$cdb = new MySql();

			if (file_exists($new_filename))
			{
				if ($bf = fopen($new_filename, 'r'))
				{
					//Speichert die aktuelle zeile
					$line = '';
					//Speichert den Aktuellen SQL String
					$sql_dump_string = '';
					//Zaehlt eventuell auftretende Fehler
					$error_count = 0;

					while (!feof($bf))
					{
						//Zeile auslesen
						$line = fgets($bf);
						$ltrim_line = ltrim($line);

						//Kommentarspalten und LOCK / UNLOCK Spalten ignorieren
						if (substr($ltrim_line, 0, 3) != '-- ' and substr($ltrim_line, 0, 3) != '/*!' and substr($ltrim_line, 0, 11) != 'LOCK TABLES' and substr($ltrim_line, 0, 13) != 'UNLOCK TABLES')
						{
							//Queryanfang finden
							if (substr($ltrim_line, 0, 10) == 'DROP TABLE' or substr($ltrim_line, 0, 12) == 'CREATE TABLE' or substr($ltrim_line, 0, 11) == 'INSERT INTO')
							{
								//eventuell bereits vorhandene SQL Satments eintragen
								$sql_dump_string = trim($sql_dump_string);
								if (!empty($sql_dump_string))
								{
									if ($cdb->query($sql_dump_string) === false)
									{
										$error_count++;
									}
								}
								//Statment neu beginnen
								$sql_dump_string = $line;
							}
							else
							{
								//Statement erweitern
								$sql_dump_string .= $line;
							}
						}
						else
						{
							//eventuell bereits vorhandene SQL Satments eintragen
							$sql_dump_string = trim($sql_dump_string);
							if (!empty($sql_dump_string))
							{
								if ($cdb->query($sql_dump_string) === false)
								{
									$error_count++;
								}
							}
							//Statement leeren
							$sql_dump_string = '';
						}
					}

					//letztes SQL Statment ausfuehren
					$sql_dump_string = trim($sql_dump_string);
					if (!empty($sql_dump_string))
					{
						if ($cdb->query($sql_dump_string) === false)
						{
							$error_count++;
						}
					}

					//keine Fehler
					if ($error_count == 0)
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
			else
			{
				return false;
			}
		}

		/**
		 * Stellt Backup ueber die MySqlExec wiederher.
		 * @param string $new_filename
		 * @return boole
		 */
		public function restore_dump_with_mysqlexec($new_filename)
		{
			//MySQL Socket verwenden
			if (!empty($this->dump_setup['db_socket']))
			{
				//gegebenenfalls fuehrenden : entfernen
				if (substr($this->dump_setup['db_socket'], 0, 1) == ':')
				{
					$abfrage = $this->dump_setup['mysql_exec']." --default-character-set=utf8 -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server']." -S ".substr($this->dump_setup['db_socket'], 1)." ".$this->dump_setup['db_name']." < ".$new_filename;
				}
				else
				{
					$abfrage = $this->dump_setup['mysql_exec']." --default-character-set=utf8 -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server']." -S ".$this->dump_setup['db_socket']." ".$this->dump_setup['db_name']." < ".$new_filename;
				}
			}
			//MySQL Port verwenden
			elseif (!empty($this->dump_setup['db_port']))
			{
				$abfrage = $this->dump_setup['mysql_exec']."  --default-character-set=utf8 -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server'].":".$this->dump_setup['db_port']." ".$this->dump_setup['db_name']." < ".$new_filename;
			}
			else
			{
				$abfrage = $this->dump_setup['mysql_exec']."  --default-character-set=utf8 -u".$this->dump_setup['db_user']." -p".$this->dump_setup['db_pw']." -h ".$this->dump_setup['db_server']." ".$this->dump_setup['db_name']." < ".$new_filename;
			}

			system($abfrage, $fp);
			if ($fp == 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		* Speichert die Dumpinformationen in die Datenbank
		* Die Klasse "MySql" wird fuer die Datenbankabfragen eingebunden.
		* @param string $PFAD
		* @param string $file
		* @param string $typ
		* @param string $txt
		* @return boole
		*/
		private function save_dump($PFAD, $file, $typ, $txt)
		{
			$cdb = new MySql;

			$file_size = round(filesize($PFAD.$file) / 1024 / 1024, 2);
			$save_time = date("YmdHis");

			if ($cdb->insert("INSERT INTO fom_backup (backup_time, filename, filesize, type, beschreibung) VALUES ('$save_time','$file','$file_size','".strtolower($typ)."','$txt')"))
			{
				if ($cdb->get_affected_rows() == 1)
				{
					$dump_id = $cdb->get_last_insert_id();
					if ($dump_id > 0)
					{
						$this->dump_info['file_name'] = $file;
						$this->dump_info['dump_typ'] = strtolower($typ);
						$this->dump_info['file_size'] = $file_size;
						$this->dump_info['dump_id'] = $dump_id;
						$this->dump_info['save_time'] = $save_time;

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
			else
			{
				return false;
			}
		}

		/**
		* Komprimiert eine sql Datei
		* @param string $save_pfad
		* @param string $dump_filename
		* @return mixed
		*/
		private function dump_compress($save_pfad, $dump_filename)
		{
			//unter Linux wird vorrangig gz verwendet
			if (strtolower(PHP_OS) == 'linux')
			{
				//gz komprimierung
				$compress_dump_filename = $this->dump_compress_gz($save_pfad, $dump_filename);

				//Auf Fehler Pruefen
				if ($compress_dump_filename !== false)
				{
					return $compress_dump_filename;
				}
				//Eventuell eine zip erstellen
				elseif (class_exists('PclZip'))
				{
					$compress_dump_filename = $this->dump_compress_zip($save_pfad, $dump_filename);

					if ($compress_dump_filename !== false)
					{
						return $compress_dump_filename;
					}
					else
					{
						return $dump_filename;
					}
				}
				else
				{
					return $dump_filename;
				}
			}
			//Allen nicht Linuxsystemen eventuell eine zip erstellen
			elseif (class_exists('PclZip'))
			{
				$compress_dump_filename = $this->dump_compress_zip($save_pfad, $dump_filename);

				if ($compress_dump_filename !== false)
				{
					return $compress_dump_filename;
				}
				else
				{
					return $dump_filename;
				}
			}
			else
			{
				return $dump_filename;
			}
		}

		/**
		 * Komprimiert eine SQL Datei mit zip
		 * @param string $save_pfad
		 * @param string $dump_filename
		 * @return mixed
		 */
		private function dump_compress_zip($save_pfad, $dump_filename)
		{
			//Archivename
			$compress_dump_filename = $dump_filename.'.zip';

			//Archiv erstellen
			$pclzip = new PclZip($save_pfad.$compress_dump_filename);
			//Sql-Datei in Archive komprimieren
			$zip_file = $pclzip->create($save_pfad.$dump_filename);

			//Fehler
			if ($zip_file == 0)
			{
				return false;
			}
			elseif (file_exists($save_pfad.$compress_dump_filename) and is_readable($save_pfad.$compress_dump_filename))
			{
				//Original SQL Datei loeschen
				@unlink($save_pfad.$dump_filename);
				return $compress_dump_filename;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Komprimiert eine SQL Datei mit gz
		 * @param string $save_pfad
		 * @param string $dump_filename
		 * @return mixed
		 */
		private function dump_compress_gz($save_pfad, $dump_filename)
		{
			//Archivename
			$compress_dump_filename = $dump_filename.'.gz';
			//Konsolenbefehl
			$gzip = 'gzip -9 '.$save_pfad.$dump_filename;
			//Archive erstellen
			system($gzip, $fp);

			//pruefen, ob zieldatei angelegt wurde
			if ($fp == 0 and file_exists($save_pfad.$compress_dump_filename) and is_readable($save_pfad.$compress_dump_filename))
			{
				return $compress_dump_filename;
			}
			else
			{
				return false;
			}
		}

		/**
		* Entpackt eine gzip Datei
		* @param string $save_pfad
		* @param string $dump_filename
		* @return mixed
		*/
		private function dump_uncompress($save_pfad, $dump_filename, $compression)
		{
			//GZip Archiv
			if (strtolower(PHP_OS) == 'linux' and $compression == 'gz')
			{
				//Dateiname ohne .gz
				$uncompress_dump_filename = substr($dump_filename, 0, -3);

				//Entpacken
				system('gzip -d '.$save_pfad.$dump_filename, $fp);

				//Archive loeschen
				$this->del_dump_file($save_pfad.$dump_filename);

				// pruefen, ob zieldatei angelegt wurde
				if ($fp == 0 and file_exists($save_pfad.$uncompress_dump_filename) and is_readable($save_pfad.$uncompress_dump_filename))
				{
					return $uncompress_dump_filename;
				}
				else
				{
					return false;
				}
			}
			//Zip Archiv
			elseif ($compression == 'zip' and class_exists('PclZip'))
			{
				//Dateiname ohne .zip
				$uncompress_dump_filename = substr($dump_filename, 0, -4);

				//Entpacken
				$zip = new PclZip($save_pfad.$dump_filename);

				if ($zip->extract(PCLZIP_OPT_PATH, $save_pfad) != 0)
				{
					//Archive loeschen
					$this->del_dump_file($save_pfad.$dump_filename);
					return $uncompress_dump_filename;
				}
				else
				{
					//Archive loeschen
					$this->del_dump_file($save_pfad.$dump_filename);
					return false;
				}
			}
			else
			{
				//Archive loeschen
				$this->del_dump_file($save_pfad.$dump_filename);
				return false;
			}
		}

		/**
		* Schreibt zusaetzliche informationen in die SQL Datei
		* @param string $file
		* @return boole
		* @function
		*/
		private function add_dump_info($file)
		{
			if ($this->add_salt_array($file))
			{
				return true;

				/*
				if (file_exists($file))
				{
					$cdb = new MySql;

					$sql = $cdb->select('SELECT setup FROM hdb_setup');
					$result = $cdb->fetch_array($sql);
					$setup = unserialize($result['setup']);

					if (is_array($setup) and isset($setup['database_version']) and !empty($setup['database_version']))
					{
						if ($h = fopen($file, 'a'))
						{
							fputs($h, "\r\n\r\n");
							fputs($h, '-- DB Version'."\r\n");
							fputs($h, '-- '.$setup['database_version']);
							fclose($h);
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
				else
				{
					return false;
				}
				*/
			}
			else
			{
				return false;
			}
		}

		/**
		 * Fuegt das Saltarray der SQL Datei hinzu.
		 * @param string $file
		 * @return boole
		 */
		private function add_salt_array($file)
		{
			if (class_exists('CryptPw'))
			{
				$cp = new CryptPw();
				$saltfile = $cp->salt_file;

				if (file_exists($saltfile) and is_readable($saltfile))
				{
					if ($h = @fopen($file, 'a'))
					{
						fputs($h, "\r\n\r\n");
						fputs($h, '-- Salt Array'."\r\n");

						if ($s = fopen ($saltfile, 'r'))
						{
							while (!feof($s))
							{
								$line = fgets($s);

								$line = str_replace(array('<?php', '?>'), '', $line);

								$line = trim($line);

								if (!empty($line))
								{
									fputs($h, '-- '.$line."\r\n");
								}
							}
							fclose ($s);
							fclose($h);
							return true;
						}
						else
						{
							fclose($h);
							return false;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return true;
				}
			}
			else
			{
				return true;
			}
		}

		/**
		* Gibt die DB Version der SQL Datei zurueck
		* @param string $file
		* @param string $save_pfad, Braucht nur verwendet werden wenn der Dump nicht im Standardverzeichnis liegt
		* @return mixed
		*/
		public function get_db_version($file, $save_pfad = '')
		{
			if (empty($save_pfad))
			{
				$save_pfad = $this->dump_setup['save_pfad'];
			}

			if (file_exists($save_pfad.$file))
			{
				if (copy($save_pfad.$file, $this->dump_setup['tmp_pfad'].$file))
				{
					if (file_exists($this->dump_setup['tmp_pfad'].$file))
					{
						//SQL Dump ist gepackt
						if (strtolower(substr($file, -3)) == '.gz')
						{
							$uncompress_dump_file = $this->dump_uncompress($this->dump_setup['tmp_pfad'], $file, 'gz');

							if ($uncompress_dump_file === false)
							{
								//tmp Dateien loeschen
								$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
								return false;
							}
							$new_filename = $this->dump_setup['tmp_pfad'].$uncompress_dump_file;
						}
						elseif (strtolower(substr($file, -4)) == '.zip')
						{
							$uncompress_dump_file = $this->dump_uncompress($this->dump_setup['tmp_pfad'], $file, 'zip');

							if ($uncompress_dump_file === false)
							{
								//tmp Dateien loeschen
								$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
								return false;
							}
							$new_filename = $this->dump_setup['tmp_pfad'].$uncompress_dump_file;
						}
						else
						{
							$new_filename = $this->dump_setup['tmp_pfad'].$file;
						}


						if ($h = @fopen($new_filename, 'r'))
						{
							fseek($h, -5, SEEK_END);
							$v = fgets($h);
							fclose($h);

							//tmp Dateien loeschen
							$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
							$this->del_dump_file($new_filename);
							if (empty($v))
							{
								return false;
							}
							else
							{
								return $v;
							}
						}
						else
						{
							//tmp Dateien loeschen
							$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
							$this->del_dump_file($new_filename);
							return false;
						}
					}
					else
					{
						//tmp Dateien loeschen
						$this->del_dump_file($this->dump_setup['tmp_pfad'].$file);
						return false;
					}
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
		 * Entfernt Temporaere Dateien
		 * @param string $file
		 */
		private function del_dump_file($file)
		{
			if (file_exists($file) and is_file($file))
			{
				@unlink($file);
			}
		}

		/**
		* Gibt die Dumpinformationen zurueck
		* @return array
		*/
		public function get_dump_info()
		{
			return $this->dump_info;
		}
	}
?>