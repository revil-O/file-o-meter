<?php
	/**
	 * read file contents
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * read file contents
	 * @package file-o-meter
	 * @subpackage class
	 */
	class ReadFile
	{
		private $setup_array = array();

		public function __construct()
		{
			$this->setup_array['min_len'] = $GLOBALS['setup_array']['index_min_len'];
		}

		public function read_file($jobid_int)
		{
			$cdb = new MySql;
			$gt = new Tree;

			$sql = $cdb->select('SELECT t1.job_id, t1.file_id, t1.link_id, t1.save_name, t1.save_time, t2.file_server_id, t2.mime_type FROM fom_file_job_index t1
								LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
								WHERE t1.job_id='.$jobid_int);
			$result = $cdb->fetch_array($sql);

			//Projekt Id
			$project_id = 0;
			if (isset($result['file_id']) and !empty($result['file_id']))
			{
				$fj = new FileJobs;
				$project_id = $fj->get_project_id($result['file_id']);
			}
			elseif (isset($result['link_id']) and !empty($result['link_id']))
			{
				$lj = new LinkJobs();
				$project_id = $lj->get_project_id($result['link_id']);
			}

			if ($project_id > 0)
			{
				$copy_to_index_folder = false;
				if (isset($result['file_id']) and !empty($result['file_id']))
				{
					$copy_to_index_folder = $this->copy_to_indexfolder($result['save_name'], FOM_ABS_PFAD.'files/tmp/index_job/', $result['save_time'], $project_id, $result['file_server_id']);
				}
				//sind bereits vorhanden
				elseif (isset($result['link_id']) and !empty($result['link_id']))
				{
					$copy_to_index_folder = true;
				}

				if ($copy_to_index_folder)
				{
					if (!isset($result['mime_type']) or empty($result['mime_type']))
					{
						if (function_exists('mime_content_type'))
						{
							$result['mime_type'] = mime_content_type(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']);
						}
					}

					$ex_string = $gt->GetFileExtension($result['save_name']);

					$file_string = '';
					//PDF Lesen
					if ($result['mime_type'] == 'application/pdf' or $ex_string == 'pdf')
					{
						//echo 'PDF';
						$file_string .= $this->read_pdf_file($result['job_id']);
					}
					//Ms-Wordfile auslesen
					elseif ($result['mime_type'] == 'application/msword' or $ex_string == 'doc')
					{
						//echo 'DOC';
						$file_string .= $this->read_doc_file($result['job_id']);
					}
					//Excelfile auslesen
					elseif ($result['mime_type'] == 'application/msexcel' or $ex_string == 'xls' or $ex_string == 'xlsx')
					{
						//echo 'XLS';
						$file_string .= $this->read_xls_file($result['job_id']);
					}
					//OpenDocumentText auslesen
					elseif ($result['mime_type'] == 'application/vnd.oasis.opendocument.text' or $ex_string == 'odt')
					{
						//echo 'odt';
						$file_string .= $this->read_odf_file($result['job_id']);
					}
					//OpenDocumentCalc auslesen
					elseif ($result['mime_type'] == 'application/vnd.oasis.opendocument.spreadsheet' or $ex_string == 'ods')
					{
						//echo 'ods';
						$file_string .= $this->read_xls_file($result['job_id']);
					}
					//TextDateien
					elseif ($this->is_text_file($result['mime_type'], $ex_string))
					{
						//echo 'TXT';
						$file_string .= $this->read_text_file($result['job_id']);
					}
					//Datei kann nicht indiziert werden Job auftrag loeschen
					else
					{
						if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']))
						{
							@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']);
						}
						$cdb->delete('DELETE FROM fom_file_job_index WHERE job_id='.$jobid_int);
					}
					//echo '<br>'.$file_string;

					//Es wurden Daten aus der Datei gelesen
					if (!empty($file_string))
					{
						//den gesamten string klein schreiben es wird bei der suche keine unterscheidung zwischen gross und kleinschreibung geben
						//$file_string = strtolower($file_string);
						$file_string = mb_strtolower($file_string);
						//bereinigt den string von unerwuenschten woertern oder zeichen
						$word_array = $this->clear_string($file_string);
						//Woerter in DB eintragen
						if (isset($result['file_id']) and !empty($result['file_id']))
						{
							$this->insert_file_word_array($word_array, $result['file_id'], $result['save_name'], 'file');
						}
						elseif (isset($result['link_id']) and !empty($result['link_id']))
						{
							$this->insert_link_word_array($word_array, $result['link_id']);
						}
					}
				}
			}
		}

		/**
		 * Ruft die Klasse fuer das auslesen von Text Dokumenten auf
		 * @param int $job_id
		 * @return void
		 */
		private function read_text_file($job_id)
		{
			$rft = new ReadFileText;
			return $rft->read_file($job_id);
		}

		/**
		 * Ruft die Klasse fuer das auslesen von PDF Dokumenten auf
		 * @param int $job_id
		 * @return void
		 */
		private function read_pdf_file($job_id)
		{
			$rfp = new ReadFilePdf;
			return $rfp->read_file($job_id);
		}

		/**
		 * Liest MS-Word Dokumente
		 * @return string
		 */
		private function read_doc_file($job_id)
		{
			$rfd = new ReadFileDoc;
			return $rfd->read_file($job_id);
		}

		/**
		 * Liest MS-Excel Dokumente
		 * @return string
		 */
		private function read_xls_file($job_id)
		{
			$rfx = new ReadFileXls;
			return $rfx->read_file($job_id);
		}

		/**
		 * Liest Opendocument Dokumente
		 * @return string
		 */
		private function read_odf_file($job_id)
		{
			$rfo = new ReadFileOdf;
			return $rfo->read_file($job_id);
		}

		/**
		 * Prueft ob der uebergebene MIME-Typ eine TextDatei ist
		 * @return boole
		 */
		private function is_text_file($mime, $ex_string)
		{
			if ($mime == 'text/xml' or $ex_string == 'xls')
			{
				return true;
			}
			elseif($mime == 'text/plain' or $ex_string == 'txt')
			{
				return true;
			}
			elseif($mime == 'text/html' or $ex_string == 'htm' or $ex_string == 'html')
			{
				return true;
			}
			elseif($mime == 'text/css' or $ex_string == 'css')
			{
				return true;
			}
			elseif($mime == 'text/comma-separated-values' or $ex_string == 'csv')
			{
				return true;
			}
			elseif($mime == 'application/xml' or $ex_string == 'xml')
			{
				return true;
			}
			elseif($mime == 'application/xhtml+xml' or $ex_string == 'xhtml')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		private function insert_link_word_array($word_array, $link_id)
		{
			$rl = new ReadLink();
			$rl->insert_link_word_array($word_array, $link_id, false);
		}

		/**
		 * Traegt die Woerter ind die DB ein und ordnet diese der Datei zu.
		 * @param array $word_array
		 * @param int $file_int
		 * @return void
		 */
		public function insert_file_word_array($word_array, $file_id, $file_name, $typ = 'file')
		{
			$cdb = new MySql;
			$sql = $cdb->select("SELECT file_id FROM fom_files WHERE file_id=$file_id and save_name='$file_name'");
			$result = $cdb->fetch_array($sql);
			//Die Datei ist keine Version
			if ($result['file_id'] > 0)
			{
				foreach($word_array as $word)
				{
					$sql = $cdb->select("SELECT word_id FROM fom_search_word WHERE word='$word'");
					$result = $cdb->fetch_array($sql);

					$word_id = 0;
					$word_file_exists = false;

					//Das Wort existiert bereits
					if ($result['word_id'] > 0)
					{
						$word_id = $result['word_id'];

						//Doppelte eintraege verhindern
						$sql = $cdb->select('SELECT word_id, tagging FROM fom_search_word_file WHERE word_id='.$result['word_id'].' AND file_id='.$file_id);
						$sub_result = $cdb->fetch_array($sql);

						if ($sub_result['word_id'] > 0)
						{
							$word_file_exists = true;
						}
					}
					else
					{
						//Neues Wort eintragen
						if ($cdb->insert("INSERT INTO fom_search_word (word) VALUES ('$word')"))
						{
							if ($cdb->get_affected_rows() == 1)
							{
								$last_id = $cdb->get_last_insert_id();
								if ($last_id > 0)
								{
									$word_id = $last_id;
								}
							}
						}
					}

					//Neueintrag
					if ($word_id > 0)
					{
						//Suchbegriff kommt aus Dateiinhalt
						if ($typ == 'file')
						{
							//Keine Doppelten zuordnungen
							if ($word_file_exists == false)
							{
								$cdb->insert("INSERT INTO fom_search_word_file (word_id, file_id, tagging) VALUES ($word_id, $file_id, '0')");
							}
						}
						//Suchbegriff kommt aus tagging
						elseif ($typ == 'tagging')
						{
							//keine Doppelten zuordnungen
							if ($word_file_exists == false)
							{
								$cdb->insert("INSERT INTO fom_search_word_file (word_id, file_id, tagging) VALUES ($word_id, $file_id, '1')");
							}
							elseif ($sub_result['tagging'] == '0')
							{
								$cdb->update("UPDATE fom_search_word_file SET tagging='1' WHERE word_id=$word_id AND file_id=$file_id");
							}
						}
					}
					unset($sub_result);
				}
			}
			else//Datei ist einer Version
			{
				$sql = $cdb->select("SELECT sub_fileid FROM fom_file_subversion WHERE file_id=$file_id and save_name='$file_name'");
				$v_result = $cdb->fetch_array($sql);

				if ($v_result['sub_fileid'] > 0)
				{
					foreach($word_array as $word)
					{
						$sql = $cdb->select("SELECT word_id FROM fom_search_word WHERE word='$word'");
						$result = $cdb->fetch_array($sql);

						$word_id = 0;
						$word_file_exists = false;
						//Das Wort existiert bereits
						if ($result['word_id'] > 0)
						{
							$word_id = $result['word_id'];

							//Doppelte eintraege verhindern
							$sql = $cdb->select('SELECT word_id, tagging FROM fom_search_word_file WHERE word_id='.$result['word_id'].' AND sub_fileid='.$v_result['sub_fileid']);
							$sub_result = $cdb->fetch_array($sql);

							if ($sub_result['word_id'] > 0)
							{
								$word_file_exists = true;
							}
						}
						else
						{
							//Neues Wort eintragen
							if ($cdb->insert("INSERT INTO fom_search_word (word) VALUES ('$word')"))
							{
								if ($cdb->get_affected_rows() == 1)
								{
									$last_id = $cdb->get_last_insert_id();
									if ($last_id > 0)
									{
										$word_id = $last_id;
									}

								}
							}
						}

						if ($word_id > 0)
						{
							if ($typ == 'file')
							{
								//Keine Doppelten zuordnungen
								if ($word_file_exists == false)
								{
									$cdb->insert("INSERT INTO fom_search_word_file (word_id, sub_fileid, tagging) VALUES ($word_id, ".$v_result['sub_fileid'].", '0')");
								}
							}
							elseif ($typ == 'tagging')
							{
								//Keine Doppelten zuordnungen
								if ($word_file_exists == false)
								{
									$cdb->insert("INSERT INTO fom_search_word_file (word_id, sub_fileid, tagging) VALUES ($word_id, ".$v_result['sub_fileid'].", '1')");
								}
								elseif ($sub_result['tagging'] == '0')
								{
									$cdb->update("UPDATE fom_search_word_file SET tagging='1' WHERE word_id=$word_id AND sub_fileid=".$v_result['sub_fileid']);
								}
							}
						}
						unset($sub_result);
					}
				}
			}
		}

		/**
		 * Bereinigt den String von Sonderzeichen und Stopwoertern
		 * @param string $file_string
		 * @return array
		 */
		public function clear_string($file_string)
		{
			$cdb = new MySql;
			//symbole die entfernt werden sollen
			$search_sign = array('«', '»', '€', ', ', ';', '. ', ':', '- ', '_', '#', '\'', '+', '~', '*', '´', '`', '\\', '?', '}', '=', ']', ')', '(', '/' ,'{', '&', '%', '$', '§', '"', '!', '°', '^', '<', '>', '|');

			$file_string = str_replace($search_sign, ' ', $file_string);
			//Woerter entfernen die zu einem SQL Abbruch fuehren wuerden
			$file_string = str_replace($cdb->setup_array['forbidden_command'], ' ', $file_string);


			$file_array = explode(' ', $file_string);
			$new_file_array = array();

			for($i = 0; $i < count($file_array); $i++)
			{
				$tmp_word = trim($file_array[$i]);
				//mindestlaenge
				if (strlen($tmp_word) > $this->setup_array['min_len'])
				{
					$tmp_word = htmlentities($tmp_word, ENT_QUOTES);
					$sql = $cdb->select("SELECT word FROM fom_search_stopword WHERE word='$tmp_word'");
					$result = $cdb->fetch_array($sql);

					if (empty($result['word']))
					{
						$new_file_array[] = $tmp_word;
					}
				}
			}
			//doppelte werte entfernen
			$new_file_array = array_unique($new_file_array);
			return $new_file_array;
		}

		/**
		 * Kopiert eine Datei vom Speicherverzeichnis in das indizierungsverzeichnis
		 * @param string $file_name
		 * @param string $dest_pfad
		 * @param int $save_time
		 * @param int $project_id
		 * @param int $file_server_id
		 * @return
		 */
		private function copy_to_indexfolder($file_name, $dest_pfad, $save_time, $project_id, $file_server_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT typ, pfad, setup FROM fom_file_server WHERE file_server_id='.$file_server_id);
			$result = $cdb->fetch_array($sql);

			if ($result['typ'] == 'local')
			{
				//Sollte eigendlich immer schon da sein
				if (file_exists($dest_pfad.$file_name))
				{
					return true;
				}
				else
				{
					$source_pfad = $result['pfad'].$project_id.'/'.substr($save_time, 0, 6).'/';
					//Datei aus dem Speicherverzeichnis ins indizierungsverzeichnis kopieren
					if (@copy($source_pfad.$file_name, $dest_pfad.$file_name))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				//FIXME: hier FTP rein
			}
		}
	}
?>
