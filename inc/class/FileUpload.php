<?php
	/**
	 * file-upload class
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * file-upload class
	 * @package file-o-meter
	 * @subpackage class
	 */
	class FileUpload
	{
		public $setup_array = array();

		public function __construct()
		{
			/**
			 * Dateinamen auf ISO-9660 Pruefen
			 * @var bool
			 */
			$this->setup_array['iso_9660'] = $GLOBALS['setup_array']['iso_9660'];
			/**
			 * Dateinamen die nicht der ISO-9660 entsprechen aendern
			 * @var bool
			 */
			$this->setup_array['iso_9660_edit_filename'] = $GLOBALS['setup_array']['iso_9660_edit_filename'];
			/**
			 * Verzeichnis in dem der Dateienupload erfolgen soll
			 * @var string
			 */
			$this->setup_array['save_folder'] = FOM_ABS_PFAD.'files/tmp/';

			/**
			 * Nicht erlaubte Zeichen für den Dateinamen
			 * FIXME: sollte aus dem Setup kommen
			 */
			$this->setup_array['wrong_file_sign_array'] = array('\\', '/', '*', ':', '?', "'", '"', '<', '>', '|');

			/**
			 * Nicht erlaubte Zeichen für den Verzeichnisnamen
			 * FIXME: sollte aus dem Setup kommen
			 */
			$this->setup_array['wrong_folder_sign_array'] = array('\\', '/', '*', ':', '?', "'", '"', '<', '>', '|');
		}

		/**
		 * Fuehrt einen Upload durch
		 *
		 * @param array $file, == $_FILES['XX']
		 * @return array
		 */
		public function file_upload($file)
		{
			$return_array = array();

			//Allgemeine Fehlerpruefung
			if ($file['error'] == 0)
			{
				$org_filename = $this->chk_name($file['name']);

				if (!empty($org_filename))
				{
					//Ist eigendlich ueberfluessig aber sicher ist sicher :)
					if ($file['size'] <= $GLOBALS['setup_array']['upload_max_filesize'])
					{
						$gt = new Tree;
						//Name fuer die Speicherung auf dem FileServer
						$file_name_ex = $gt->GetFileExtension($org_filename);

						//mit Dateierweiterung z.B. 'doc'
						if (!empty($file_name_ex))
						{
							$save_filename = $gt->GetNewFileName().'.'.strtolower($file_name_ex);
						}
						else
						{
							$save_filename = $gt->GetNewFileName();
						}
						//Dateiupload
						if(@move_uploaded_file($file['tmp_name'], $this->setup_array['save_folder'].$save_filename) !== false)
						{
							if (file_exists($this->setup_array['save_folder'].$save_filename))
							{
								$return_array['save_filename'] = $save_filename;
								$return_array['org_filename'] = $org_filename;
								$return_array['org_filename_no_iso'] = $file['name'];
								$return_array['md5_file'] = md5_file($this->setup_array['save_folder'].$save_filename);

								if (strtolower($file['type']) != 'application/octet-stream')
								{
									$return_array['file_mimetype'] = $file['type'];
								}
								//Mimetype ist application/octet-stream eine genauere Pruefung durchfuehren
								else
								{
									$fi = new FileInfo();
									$return_array['file_mimetype'] = $fi->get_mime_type($this->setup_array['save_folder'].$save_filename);
								}
							}
							else
							{
								$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
							}
						}
						else
						{
							$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
						}
					}
					else
					{
						$return_array['error'] = setError(get_text(254, 'return'), WARNING, __LINE__);//The uploaded file exceeds the maximum filesize!
					}
				}
				else
				{
					$return_array['error'] = setError(get_text(253, 'return'), WARNING, __LINE__);//Invalid filename!
				}
			}
			else
			{
				//Genauere Fehleranalyse
				if ($file['error'] == 1 or $file['error'] == 2)
				{
					$return_array['error'] = setError(get_text(254, 'return'), WARNING, __LINE__);//The uploaded file exceeds the maximum filesize!
				}
				elseif ($file['error'] == 3)
				{
					$return_array['error'] = setError(get_text(255, 'return'), WARNING, __LINE__);//Fileupload incomplete!
				}
				elseif ($file['error'] == 4)
				{
					$return_array['error'] = setError(get_text(256, 'return'), WARNING, __LINE__);//There was no file uploaded!
				}
				else
				{
					$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
				}
			}
			return $return_array;
		}

		
		/**
		 * Fuehrt einen Upload durch
		 *
		 * @param array $file POST-Array vom Multiploader
		 * @return array
		 */
		public function multiupload($file)
		{
			$return_array = array();

			//Allgemeine Fehlerpruefung
			if ($file['error'] == 0)
			{
				$org_filename = $this->chk_name($file['name']);

				if (!empty($org_filename))
				{

					$gt = new Tree;
						
					//Name fuer die Speicherung auf dem FileServer
					$file_name_ex = $gt->GetFileExtension($org_filename);

					//mit Dateierweiterung z.B. 'doc'
					if (!empty($file_name_ex))
					{
						$save_filename = $gt->GetNewFileName().'.'.strtolower($file_name_ex);
					}
					else
					{
						$save_filename = $gt->GetNewFileName();
					}
					
					//Dateiupload
					if (@copy($file['tmp_name'], $this->setup_array['save_folder'].$save_filename) !== false)
					{
						if (file_exists($this->setup_array['save_folder'].$save_filename))
						{
							$return_array['save_filename'] = $save_filename;
							$return_array['org_filename'] = $org_filename;
							$return_array['org_filename_no_iso'] = $file['name'];
							$return_array['md5_file'] = md5_file($this->setup_array['save_folder'].$save_filename);
							
							$fi = new FileInfo();
							$return_array['file_mimetype'] = $fi->get_mime_type($this->setup_array['save_folder'].$save_filename);
							$return_array['filesize'] = $fi->get_filesize($this->setup_array['save_folder'].$save_filename);
						}
						else
						{
							$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
						}
					}
					else
					{
						$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
					}
				
				}
				else
				{
					$return_array['error'] = setError(get_text(253, 'return'), WARNING, __LINE__);//Invalid filename!
				}
			}
			else
			{
				//Genauere Fehleranalyse
				if ($file['error'] == 1 or $file['error'] == 2)
				{
					$return_array['error'] = setError(get_text(254, 'return'), WARNING, __LINE__);//The uploaded file exceeds the maximum filesize!
				}
				elseif ($file['error'] == 3)
				{
					$return_array['error'] = setError(get_text(255, 'return'), WARNING, __LINE__);//Fileupload incomplete!
				}
				elseif ($file['error'] == 4)
				{
					$return_array['error'] = setError(get_text(256, 'return'), WARNING, __LINE__);//There was no file uploaded!
				}
				else
				{
					$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
				}
			}
			return $return_array;
		}
		
		public function save_webservice_file($folder_id, $project_id, $filename, $file_data, $file_id = 0, $file_type = '', $comment = '', $search_string = '', $document_type_array = array())
		{
			$return_array = array();

			//Allgemeine Fehlerpruefung
			if (!empty($filename) and !empty($file_data))
			{
				$org_filename = $this->chk_name($filename);

				if (!empty($org_filename))
				{

					$gt = new Tree;
					//Name fuer die Speicherung auf dem FileServer
					$file_name_ex = $gt->GetFileExtension($org_filename);

					//mit Dateierweiterung z.B. 'doc'
					if (!empty($file_name_ex))
					{
						$save_filename = $gt->GetNewFileName().'.'.strtolower($file_name_ex);
					}
					else
					{
						$save_filename = $gt->GetNewFileName();
					}

					//Dateidaten schreiben
					//base64_decode($file_data)
					if (strtolower(substr(PHP_OS, 0, 3)) == 'win')
					{
						$h = fopen($this->setup_array['save_folder'].$save_filename, 'wb');
					}
					else
					{
						$h = fopen($this->setup_array['save_folder'].$save_filename, 'w');
					}

					if (fwrite($h, base64_decode($file_data)))
					{
						if(file_exists($this->setup_array['save_folder'].$save_filename))
						{
							$return_array['save_filename'] = $save_filename;
							$return_array['org_filename'] = $org_filename;
							$return_array['org_filename_no_iso'] = $filename;
							$return_array['md5_file'] = md5_file($this->setup_array['save_folder'].$save_filename);
							$return_array['filesize'] = filesize($this->setup_array['save_folder'].$save_filename);

							$fi = new FileInfo();
							$return_array['file_mimetype'] = $fi->get_mime_type($this->setup_array['save_folder'].$save_filename);

							$fj = new FileJobs();

							//SUB
							if ($file_id > 0 and $file_type == 'SUB')
							{
								$fj->insert_new_subfile($save_filename, $return_array['md5_file'], $org_filename, $filename, $return_array['file_mimetype'], $return_array['filesize'], $file_id, $folder_id, $project_id, $comment, time(), $search_string, $document_type_array);
								$fj->copy_to_fileserver(0, $save_filename);
							}
							//DATEIVERSION (PRIMARY)
							elseif ($file_id > 0 and $file_type == 'PRIMARY')
							{
								$fj->insert_fileversion($file_id, $save_filename, $return_array['md5_file'], $org_filename, $filename, $return_array['file_mimetype'], $return_array['filesize'], time(), 'upload', $search_string);
							}
							//neue datei
							elseif ($file_id == 0)
							{
								$return_array['file_id'] = $fj->insert_new_file($save_filename, $return_array['md5_file'], $org_filename, $filename, $return_array['file_mimetype'], $return_array['filesize'], $folder_id, $project_id, $comment, time(), 'upload', $search_string, $document_type_array, 'int');
							}

							//aufgetretene Fehler durchreichen
							if (is_array($fj->error_array) and count($fj->error_array) > 0)
							{
								if (is_array($fj->error_array) and count($fj->error_array) > 0)
								{
									$tmp_string = '';

									foreach($fj->error_array as $errormsg)
									{
										if (strlen($tmp_string) > 0)
										{
											$tmp_string .= ' ';
										}

										$tmp_string .= $errormsg;
									}

									$return_array['error'] = setError($tmp_string, WARNING, __LINE__);
								}
							}

						}
						else
						{
							$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
						}
					}
					else
					{
						$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
					}

				}
				else
				{
					$return_array['error'] = setError(get_text(253, 'return'), WARNING, __LINE__);//Invalid filename!
				}
			}
			else
			{
				$return_array['error'] = setError(get_text('error', 'return'), WARNING, __LINE__);//An error has occurred!
			}
			return $return_array;
		}

		/**
		 * Prueft einen Namen auf unerlaubte zeichen
		 * @param string $name
		 * @param string $typ, 'file' oder 'folder'
		 * @return string
		 */
		public function chk_name($name, $typ = 'file')
		{
			$fi = new FileInfo();

			if ($typ == 'file')
			{
				$wrong_sign_array = $this->setup_array['wrong_file_sign_array'];
			}
			else
			{
				$wrong_sign_array = $this->setup_array['wrong_folder_sign_array'];
			}

			//Dateinamen auf ISO 9660 Pruefen
			if ($this->setup_array['iso_9660'] === true)
			{
				$iso_chk_bool = true;
				//Dateinamenluengen Pruefung
				if ($typ == 'file')
				{
					$max_name_len_int = FOM_MAX_LENGTH_FILE;
				}
				else
				{
					$max_name_len_int = FOM_MAX_LENGTH_FOLDER;
				}

				if (strlen($name) > $max_name_len_int)
				{
					$iso_chk_bool = false;
				}
				//noch keine Fehler aufgetreten
				if ($iso_chk_bool === true)
				{
					for($i = 0; $i < strlen($name); $i++)
					{
						if (in_array(strtolower($name{$i}), $wrong_sign_array))
						{
							$iso_chk_bool = false;
							break;
						}
					}
				}

				//keine Fehler gefunden
				if ($iso_chk_bool === true)
				{
					return $name;
				}
				//Namen an ISO anpassen
				elseif($this->setup_array['iso_9660_edit_filename'] === true)
				{
					for($i = strlen($name) - 1; $i >= 0; $i--)
					{
						//nicht erlaubt zeichen
						if (in_array(strtolower($name{$i}), $wrong_sign_array))
						{
							$name{$i} = '_';
						}
					}
					//Doppelte __ entfernen
					while(strpos($name, '__') !== false)
					{
						$name = str_replace('__', '_', $name);
					}

					//Namenslaenge Pruefen
					if (strlen($name) > $max_name_len_int)
					{
						//Name mit Separator
						if (strpos($name, '.') !== false)
						{
							$ex_name = $fi->get_extension($name);

							return substr(substr($name, 0, strlen($ex_name)), 0, $max_name_len_int - strlen($ex_name)).'.'.$ex_name;

						}
						//Name hat keine Dateierweiterung
						else
						{
							return substr($name, 0, $max_name_len_int);
						}
					}
					else
					{
						return $name;
					}
				}
				else
				{
					return '';
				}
			}
			else
			{
				return $name;
			}
		}
	}
?>