<?php
	//gibt an welche functionen dem SOAP Server zur Verfuegung stehen sollen
	$soap_server_functions[] = 'add_files';
	$soap_server_functions[] = 'get_file_data';
	$soap_server_functions[] = 'get_files';
	$soap_server_functions[] = 'get_file_exists';
	$soap_server_functions[] = 'get_one_file';
	$soap_server_functions[] = 'del_file_link';

	/**
	 * adds a file to the DMS
	 *
	 * @param string $ws_key
	 * @param int $folder_id
	 * @param string $file_name
	 * @param string $file_data, file in base64 format
	 * @return string
	 */
	function add_files($ws_key, $folder_id = 0, $project_id = 0, $file_name = '', $file_data = '', $file_id = 0, $file_type = 'PRIMARY', $filecomment_string = '', $filesearch_string = '', $document_type = '', $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;

		//Zugriffsrechte Pruefen
		if ($sl->webservice_key($ws_key))
		{
			//Konstante fuer UserId festlegen
			if (!defined('USER_ID'))
			{
				$sql = $cdb->select("SELECT user_id FROM fom_webservice_access WHERE ws_key='$ws_key'");
				$result = $cdb->fetch_array($sql);
				if (isset ($result['user_id']) and $result['user_id'] > 0)
				{
					define('USER_ID', $result['user_id']);
				}
			}

			//UserID vorhanden
			if (defined('USER_ID'))
			{
				//Accessklasse
				$ac = new Access;
				//Uploadklasse
				$fu = new FileUpload;

				$start_time = time();

				//Dokumententypen
				if (!empty($document_type))
				{
					$document_type = @unserialize($document_type);

					if (!is_array($document_type))
					{
						$document_type = array();
					}
				}
				else
				{
					$document_type = array();
				}

				//Neue Datei anlegen
				if ($file_id == 0)
				{
					//Verzeichnis fuer neue Datei vorhanden und schreibrechte
					if ($folder_id > 0 and $ac->chk('folder', 'w', $folder_id))
					{
						$result_file = $fu->save_webservice_file($folder_id, $project_id, $file_name, $file_data, $file_id, $file_type, htmlentities($filecomment_string, ENT_QUOTES, 'UTF-8', false), htmlentities($filesearch_string, ENT_QUOTES, 'UTF-8', false), $document_type);
					}
				}
				//Dateiversion anlegen
				elseif ($file_id > 0 and $file_type == 'PRIMARY')
				{
					//Rechte fuer das Anlagen von Versionen vorhanden
					if ($ac->chk('file', 'va', $file_id))
					{
						//$result = $fu->save_webservice_file($file_name, $file_data);
						$result_file = $fu->save_webservice_file($folder_id, $project_id, $file_name, $file_data, $file_id, $file_type, htmlentities($filecomment_string, ENT_QUOTES, 'UTF-8', false), htmlentities($filesearch_string, ENT_QUOTES, 'UTF-8', false), $document_type);
					}
				}
				//SubDatei anlegen
				elseif ($file_id > 0 and $file_type == 'SUB')
				{
					//Rechte fuer das Anlegen von SubDateien vorhanden
					if ($ac->chk('file', 'w', $file_id))
					{
						$result_file = $fu->save_webservice_file($folder_id, $project_id, $file_name, $file_data, $file_id, $file_type, htmlentities($filecomment_string, ENT_QUOTES, 'UTF-8', false), htmlentities($filesearch_string, ENT_QUOTES, 'UTF-8', false), $document_type);
					}
				}

				//resultat zurueck geben
				if (isset($result_file) and is_array($result_file))
				{
					if ($return_type == 'array')
					{
						return serialize($result_file);
					}
					elseif ($return_type == 'json')
					{
						return json_encode($result_file);
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
	 * Loescht bzw. blendet eine Datei aus
	 * @param string $ws_key
	 * @param int $file_id
	 * @param int $link_id
	 * @return boole
	 */
	function del_file_link($ws_key, $file_id = 0, $link_id = 0)
	{
		$cdb = new MySql;
		$sl = new Login;

		//Zugriffsrechte Pruefen
		if ($sl->webservice_key($ws_key))
		{
			//Konstante fuer UserId festlegen
			if (!defined('USER_ID'))
			{
				$sql = $cdb->select("SELECT user_id FROM fom_webservice_access WHERE ws_key='$ws_key'");
				$result = $cdb->fetch_array($sql);
				if (isset ($result['user_id']) and $result['user_id'] > 0)
				{
					define('USER_ID', $result['user_id']);
				}
			}

			//UserID vorhanden
			if (defined('USER_ID'))
			{
				//Accessklasse
				$ac = new Access;

				if ($file_id > 0)
				{
					if ($ac->chk('file', 'w', $file_id))
					{
						if ($cdb->update("UPDATE fom_files SET anzeigen='0' WHERE file_id=$file_id LIMIT 1"))
						{
							return 'true';
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
				elseif ($link_id > 0)
				{
					if ($ac->chk('link', 'w', $link_id))
					{
						if ($cdb->update("UPDATE fom_link SET anzeigen='0' WHERE link_id=$link_id LIMIT 1"))
						{
							return 'true';
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
	 * Gibt einen base64_encode String zu einer Datei zurueck
	 * @param string $ws_key
	 * @param int $file_id
	 * @return string
	 */
	function get_file_data($ws_key, $file_id)
	{
		$cdb = new MySql;
		$sl = new Login;
		//Zugriffsrechte Pruefen
		if ($sl->webservice_key($ws_key))
		{
			//Konstante fuer UserId festlegen
			if (!defined('USER_ID'))
			{
				$sql = $cdb->select("SELECT user_id FROM fom_webservice_access WHERE ws_key='$ws_key'");
				$result = $cdb->fetch_array($sql);
				if (isset ($result['user_id']) and $result['user_id'] > 0)
				{
					define('USER_ID', $result['user_id']);
				}
			}

			//UserID vorhanden
			if (defined('USER_ID'))
			{
				//Accessklasse
				$ac = new Access;

				if ($ac->chk('file', 'r', $file_id))
				{
					$sql = $cdb->select("SELECT t1.save_name, t1.save_time, t2.projekt_id, t3.pfad FROM fom_files t1
										LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
										LEFT JOIN fom_file_server t3 ON t2.projekt_id=t3.projekt_id
										WHERE t1.file_id=$file_id");
					$result = $cdb->fetch_array($sql);

					if (isset($result['projekt_id']) and !empty($result['projekt_id']))
					{
						$pfad = $result['pfad'].$result['projekt_id'].'/'.substr($result['save_time'], 0, 6).'/'.$result['save_name'];

						if (file_exists($pfad))
						{
							if ($h = fopen($pfad, 'rb'))
							{
								return base64_encode(fread($h, filesize($pfad)));
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
	 * Gibt fuer eine Datei alle notwenigen Daten zurueck
	 * @param string $ws_key
	 * @param int $file_id
	 * @param string $return_type
	 * @return string
	 */
	function get_one_file($ws_key, $file_id, $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;
		$dl = new Download;
		$gt = new Tree;

		//Zugriffsrechte Pruefen
		if ($sl->webservice_key($ws_key))
		{
			//Konstante fuer UserId festlegen
			if (!defined('USER_ID'))
			{
				$sql = $cdb->select("SELECT user_id FROM fom_webservice_access WHERE ws_key='$ws_key'");
				$result = $cdb->fetch_array($sql);
				if (isset ($result['user_id']) and $result['user_id'] > 0)
				{
					define('USER_ID', $result['user_id']);
				}
			}

			//UserID vorhanden
			if (defined('USER_ID'))
			{
				if ($file_id > 0)
				{
					$ac = new Access;
					$file_array = array();

					$sql = $cdb->select('SELECT file_id, folder_id, org_name, save_name, md5_file, mime_type, file_size, save_time, bemerkungen FROM fom_files WHERE file_id='.$file_id);
					$result = $cdb->fetch_array($sql);

					//Rechte fuer das Anlagen von Versionen vorhanden
					if ($ac->chk('file', 'r', $result['file_id']))
					{

						$dl_result = $dl->insert_download($result['file_id'], date("YmdHis", time() + 60 * 60 * 24 * 7));

						if ($dl_result['result'] == true)
						{
							$file_array = array('file_id'			=>	$result['file_id'],
												'file_name'			=>	$result['org_name'],
												'mime_type'			=>	$result['mime_type'],
												'mime_type_icon'	=>	$gt->GetFileType($result['save_name'], $result['mime_type']),
												'file_size'			=>	$result['file_size'],
												'save_time'			=>	$result['save_time'],
												'comment'			=>	$result['bemerkungen'],
												'link_download'		=>	$dl_result['download'],
												'link_open'			=>	$dl_result['download'].'&amp;mime_type='.$result['mime_type'],
												'folder_id'			=>	$result['folder_id'],
												'md5_file'			=>	$result['md5_file']);

							if ($return_type == 'array')
							{
								return serialize($file_array);
							}
							elseif ($return_type == 'json')
							{
								return json_encode($file_array);
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
		return 'false';
	}

	/**
	 * Hauptfunktion fuer die ausgabe von Dateiinformationen
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @param int $doctype_id
	 * @param string $file_comment
	 * @param string $order_by
	 * @param string $return_type
	 * @param boole $recursive
	 * @return string
	 */
	function get_files($ws_key, $project_id = 0, $folder_id = 0, $doctype_id = 0, $file_comment = '', $order_by = 'name_asc', $return_type = 'array', $recursive = false)
	{
		$cdb = new MySql;
		$sl = new Login;
		//Zugriffsrechte Pruefen
		if ($sl->webservice_key($ws_key))
		{
			//Konstante fuer UserId festlegen
			if (!defined('USER_ID'))
			{
				$sql = $cdb->select("SELECT user_id FROM fom_webservice_access WHERE ws_key='$ws_key'");
				$result = $cdb->fetch_array($sql);
				if (isset ($result['user_id']) and $result['user_id'] > 0)
				{
					define('USER_ID', $result['user_id']);
				}
			}

			//UserID vorhanden
			if (defined('USER_ID'))
			{
				//mindestens eins von beiden sollte vorhanden sein
				if ($project_id > 0 or $folder_id > 0)
				{
					//keine Rekursive Suche
					if ($recursive == false)
					{
						$file_array = array();
						$file_array = get_file($project_id, $folder_id, $doctype_id, $file_comment, $order_by);

						if (is_array($file_array) and !empty($file_array))
						{
							if ($return_type == 'array')
							{
								return serialize($file_array);
							}
							elseif ($return_type == 'json')
							{
								return json_encode($file_array);
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
					//rekursive suche
					elseif ($recursive == true and $folder_id > 0)
					{
						$file_array = array();
						$file_array = get_file($project_id, $folder_id, $doctype_id, $file_comment, $order_by);

						$file_array = array_merge($file_array, get_recursive_file($project_id, $folder_id, $doctype_id, $file_comment, $order_by));

						if (is_array($file_array) and !empty($file_array))
						{
							if ($return_type == 'array')
							{
								return serialize($file_array);
							}
							elseif ($return_type == 'json')
							{
								return json_encode($file_array);
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
		}
	}

	/**
	 * Fuehrt eine Rekursive suche nach Dateien durch
	 * @param int $project_id
	 * @param int $ob_folder_id
	 * @param int $doctype_id
	 * @param string $file_comment
	 * @param string $order_by
	 * @return array
	 */
	function get_recursive_file($project_id, $ob_folder_id, $doctype_id, $file_comment, $order_by)
	{
		$cdb = new MySql;
		$file_array = array();

		$sql = $cdb->select('SELECT folder_id FROM fom_folder WHERE ob_folder='.$ob_folder_id);
		while ($result = $cdb->fetch_array($sql))
		{
			$file_array = array_merge($file_array, get_file($project_id, $result['folder_id'], $doctype_id, $file_comment, $order_by));

			$sub_sql = $cdb->select('SELECT folder_id FROM fom_folder WHERE ob_folder='.$result['folder_id']);
			$sub_result = $cdb->fetch_array($sub_sql);

			if (isset($sub_result['folder_id']) and $sub_result['folder_id'] > 0)
			{
				$file_array = array_merge($file_array, get_recursive_file($project_id, $result['folder_id'], $doctype_id, $file_comment, $order_by));
			}
		}
		return $file_array;
	}

	/**
	 * Erstellt eine liste von Dateien.
	 * @param int $project_id
	 * @param int $folder_id
	 * @param int $doctype_id
	 * @param string $file_comment
	 * @param string $order_by
	 * @return array
	 */
	function get_file($project_id, $folder_id, $doctype_id, $file_comment, $order_by)
	{
		$cdb = new MySql;
		$dl = new Download;
		$gt = new Tree;
		//Accessklasse
		$ac = new Access;

		$file_array = array();

		$where_array = array();

		if ($project_id > 0)
		{
			$where_array[] = 't2.projekt_id='.$project_id;
		}

		if ($folder_id > 0)
		{
			$where_array[] = 't1.folder_id='.$folder_id;
		}

		$where_array[] = "t1.anzeigen='1'";
		$where_array[] = "t2.anzeigen='1'";
		$where_array[] = "t1.file_type='PRIMARY'";

		if ($doctype_id > 0)
		{
			$where_array[] = 't3.document_type_id='.$doctype_id;
		}

		if (!empty($file_comment))
		{
			$where_array[] = "t1.bemerkungen LIKE '%$file_comment%'";
		}

		$where = '';
		for ($i = 0; $i < count($where_array); $i++)
		{
			if (empty($where))
			{
				$where = 'WHERE '.$where_array[$i];
			}
			else
			{
				$where .= ' AND '.$where_array[$i];
			}
		}

		$order_by = strtolower($order_by);
		if ($order_by == 'name_asc')
		{
			$order = 'ORDER BY t1.org_name ASC';
		}
		elseif ($order_by == 'name_desc')
		{
			$order = 'ORDER BY t1.org_name DESC';
		}
		elseif ($order_by == 'time_asc')
		{
			$order = 'ORDER BY t1.save_time ASC';
		}
		elseif ($order_by == 'time_desc')
		{
			$order = 'ORDER BY t1.save_time DESC';
		}
		else
		{
			$order = 'ORDER BY t1.org_name ASC';
		}

		$index_count = 0;
		$sql = $cdb->select("SELECT t1.file_id, t1.folder_id, t1.org_name, t1.md5_file, t1.mime_type, t1.file_size, t1.save_time, t1.save_name, t1.bemerkungen FROM fom_files t1
							LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
							LEFT JOIN fom_document_type_file t3 ON t1.file_id=t3.file_id
							$where $order");
		while ($result = $cdb->fetch_array($sql))
		{
			//Rechte fuer das Anlagen von Versionen vorhanden
			if ($ac->chk('file', 'r', $result['file_id']))
			{

				$dl_result = $dl->insert_download($result['file_id'], date("YmdHis", time() + 60 * 60 * 24 * 7));

				if ($dl_result['result'] == true)
				{
					$file_array[$index_count] = array('file_id'				=>	$result['file_id'],
														'file_name'			=>	$result['org_name'],
														'mime_type'			=>	$result['mime_type'],
														'mime_type_icon'	=>	$gt->GetFileType($result['save_name'], $result['mime_type']),
														'file_size'			=>	$result['file_size'],
														'save_time'			=>	$result['save_time'],
														'comment'			=>	$result['bemerkungen'],
														'link_download'		=>	$dl_result['download'],
														'link_open'			=>	$dl_result['download'].'&amp;mime_type='.$result['mime_type'],
														'folder_id'			=>	$result['folder_id'],
														'md5_file'			=>	$result['md5_file']);

					$sub_sql = $cdb->select('SELECT subfile_id FROM fom_sub_files WHERE file_id='.$result['file_id']);
					while ($sub_result = $cdb->fetch_array($sub_sql))
					{
						$s_sql = $cdb->select("SELECT file_id, folder_id, org_name, md5_file, mime_type, file_size, save_time, bemerkungen FROM fom_files WHERE file_id=".$sub_result['subfile_id']." AND anzeigen='1' ORDER BY org_name ASC");
						$s_result = $cdb->fetch_array($s_sql);

						if (isset($s_result['file_id']) and $s_result['file_id'] > 0)
						{
							$s_dl_result = $dl->insert_download($s_result['file_id'], date("YmdHis", time() + 60 * 60 * 24 * 7));

							if ($s_dl_result['result'] == true)
							{
								$file_array[$index_count]['sub_files'][] = array('file_id'				=>	$s_result['file_id'],
																					'file_name'			=>	$s_result['org_name'],
																					'mime_type'			=>	$s_result['mime_type'],
																					'mime_type_icon'	=>	$gt->GetFileType($s_result['save_name'], $s_result['mime_type']),
																					'file_size'			=>	$s_result['file_size'],
																					'save_time'			=>	$s_result['save_time'],
																					'comment'			=>	$s_result['bemerkungen'],
																					'link_download'		=>	$s_dl_result['download'],
																					'link_open'			=>	$dl_result['download'].'&amp;mime_type='.$s_result['mime_type'],
																					'folder_id'			=>	$result['folder_id'],
																					'md5_file'			=>	$result['md5_file']);
							}
						}
					}
					$index_count++;
				}
			}
		}
		return $file_array;
	}

	/**
	 * Prueft ob ein Link im angegebenen Verzeichnis oder Projekt vorhanden ist.
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @return boole
	 */
	function get_file_exists($ws_key, $project_id = 0, $folder_id = 0)
	{
		$cdb = new MySql;
		$sl = new Login;
		//Zugriffsrechte Pruefen
		if ($sl->webservice_key($ws_key))
		{
			//Konstante fuer UserId festlegen
			if (!defined('USER_ID'))
			{
				$sql = $cdb->select("SELECT user_id FROM fom_webservice_access WHERE ws_key='$ws_key'");
				$result = $cdb->fetch_array($sql);
				if (isset ($result['user_id']) and $result['user_id'] > 0)
				{
					define('USER_ID', $result['user_id']);
				}
			}

			//UserID vorhanden
			if (defined('USER_ID'))
			{
				$where = '';

				if ($folder_id > 0)
				{
					$where = ' WHERE t1.folder_id='.$folder_id;
				}
				if ($project_id > 0)
				{
					if (empty($where))
					{
						$where = ' WHERE t2.projekt_id='.$project_id;
					}
					else
					{
						$where .= ' AND t2.projekt_id='.$project_id;
					}
				}

				if (empty($where))
				{
					$where = " WHERE t1.anzeigen='1'";
				}
				else
				{
					$where .= " AND t1.anzeigen='1'";
				}

				$sql = $cdb->select('SELECT file_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									'.$where);
				$result = $cdb->fetch_array($sql);

				if (isset($result['file_id']) and $result['file_id'] > 0)
				{
					return 'true';
				}
				else
				{
					return 'false';
				}
			}
		}
	}
?>