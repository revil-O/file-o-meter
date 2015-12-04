<?php
	//gibt an welche functionen dem SOAP Server zur Verfuegung stehen sollen
	$soap_server_functions[] = 'add_link';
	$soap_server_functions[] = 'get_links';
	$soap_server_functions[] = 'get_link';
	$soap_server_functions[] = 'get_link_exists';
	$soap_server_functions[] = 'get_one_link';

	/**
	 * adds an external link to the DMS
	 *
	 * @param string $ws_key
	 * @param int $folder_id
	 * @param int $project_id
	 * @param string $link_string
	 * @param string $protokoll_string
	 * @param string $link_name_string
	 * @param string $tagging_string
	 * @param string $linkcomment_string
	 * @return boolean
	 */
	function add_link($ws_key, $folder_id = 0, $project_id = 0, $link_string = '', $protokoll_string = '', $link_name_string = '', $tagging_string = '', $linkcomment_string = '')
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
				$ac = new Access; //Accessklasse
				$lj = new LinkJobs();

				$start_time = time();

				if ($folder_id > 0 and $project_id > 0 and !empty($link_string) and !empty($protokoll_string))
				{
					if ($lj->insert_link($folder_id, $project_id, htmlentities($link_string, ENT_QUOTES, 'UTF-8', false), stripslashes($protokoll_string), htmlentities($link_name_string, ENT_QUOTES, 'UTF-8', false), htmlentities($tagging_string, ENT_QUOTES, 'URF-8', false), htmlentities($linkcomment_string, ENT_QUOTES, 'UTF-8', false)))
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

	/**
	 * Erstellt eine Liste von Links.
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @param string $link_comment
	 * @param string $order_by
	 * @param string $return_type
	 * @param boole $recursive
	 * @return string
	 */
	function get_links($ws_key, $project_id = 0, $folder_id = 0, $link_comment = '', $order_by = 'name_asc', $return_type = 'array', $recursive = false)
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
						$link_array = array();
						$link_array = get_link($project_id, $folder_id, $link_comment, $order_by);

						if (is_array($link_array) and !empty($link_array))
						{
							if ($return_type == 'array')
							{
								return serialize($link_array);
							}
							elseif ($return_type == 'json')
							{
								return json_encode($link_array);
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
						$link_array = array();
						$link_array = get_link($project_id, $folder_id, $link_comment, $order_by);

						$link_array = array_merge($link_array, get_recursive_link($project_id, $folder_id, $link_comment, $order_by));

						if (is_array($link_array) and !empty($link_array))
						{
							if ($return_type == 'array')
							{
								return serialize($link_array);
							}
							elseif ($return_type == 'json')
							{
								return json_encode($link_array);
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
	 * Fuehrt eine Rekursive suche nach Links durch
	 * @param int $project_id
	 * @param int $ob_folder_id
	 * @param string $link_comment
	 * @param string $order_by
	 * @return array
	 */
	function get_recursive_link($project_id, $ob_folder_id, $link_comment, $order_by)
	{
		$cdb = new MySql;
		$link_array = array();

		$sql = $cdb->select('SELECT folder_id FROM fom_folder WHERE ob_folder='.$ob_folder_id);
		while ($result = $cdb->fetch_array($sql))
		{
			$link_array = array_merge($link_array, get_link($project_id, $result['folder_id'], $link_comment, $order_by));

			$sub_sql = $cdb->select('SELECT folder_id FROM fom_folder WHERE ob_folder='.$result['folder_id']);
			$sub_result = $cdb->fetch_array($sub_sql);

			if (isset($sub_result['folder_id']) and $sub_result['folder_id'] > 0)
			{
				$link_array = array_merge($link_array, get_recursive_link($project_id, $result['folder_id'], $link_comment, $order_by));
			}
		}
		return $link_array;
	}

	/**
	 * Erstellt eine liste von Links aus einem Projekt bzw. Projektverzeichnis
	 * @param int $project_id
	 * @param int $folder_id
	 * @param string $link_comment
	 * @param string $order_by
	 * @return array
	 */
	function get_link($project_id, $folder_id, $link_comment, $order_by)
	{
		$cdb = new MySql;
		$dl = new Download;
		$gt = new Tree;
		//Accessklasse
		$ac = new Access;

		$link_array = array();
		$where_array = array();

		if ($project_id > 0)
		{
			$where_array[] = 't2.projekt_id='.$project_id;
		}

		if ($folder_id > 0)
		{
			$where_array[] = 't1.folder_id='.$folder_id;
		}

		if (!empty($link_comment))
		{
			$where_array[] = "t1.bemerkungen LIKE '%$link_comment%'";
		}

		$where_array[] = "t1.anzeigen='1'";
		$where_array[] = "t2.anzeigen='1'";

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
			$order = 'ORDER BY t1.name ASC';
		}
		elseif ($order_by == 'name_desc')
		{
			$order = 'ORDER BY t1.name DESC';
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
			$order = 'ORDER BY t1.name ASC';
		}

		$sql = $cdb->select("SELECT t1.link_id, t1.folder_id, t1.file_id, t1.name, t1.link, t1.md5_link, t1.save_time, t1.bemerkungen, t1.link_type FROM fom_link t1
							LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
							$where $order");
		while ($result = $cdb->fetch_array($sql))
		{
			//interner link
			if ($result['link_type'] == 'INTERNAL')
			{
				if ($ac->chk('file', 'r', $result['file_id']))
				{
					$link_array[] = array(	'link_id'	=> $result['link_id'],
											'link_type'	=> $result['link_type'],
											'folder_id'	=> $result['folder_id'],
											'file_id'	=> $result['file_id'],
											'name'		=> $result['name']);
				}
			}
			//externer link
			elseif ($ac->chk('folder', 'r', $result['folder_id']))
			{
				$link_array[] = array(	'link_id'	=> $result['link_id'],
										'link_type'	=> $result['link_type'],
										'folder_id'	=> $result['folder_id'],
										'name'		=> $result['name'],
										'link'		=> $result['link'],
										'md5'		=> $result['md5_link'],
										'save_time'	=> $result['save_time'],
										'comment'	=> $result['bemerkungen']);
			}
		}
		return $link_array;
	}

	/**
	 * Prueft ob eine Datei im angegebenen Verzeichnis oder Projekt vorhanden ist.
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @return boole
	 */
	function get_link_exists($ws_key, $project_id = 0, $folder_id = 0)
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

				$sql = $cdb->select('SELECT link_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									'.$where);
				$result = $cdb->fetch_array($sql);

				if (isset($result['link_id']) and $result['link_id'] > 0)
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

	function get_one_link($ws_key, $link_id, $return_type = 'array')
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
				if ($link_id > 0)
				{
					$ac = new Access();
					$sql = $cdb->select("SELECT * FROM fom_link WHERE link_id=$link_id AND anzeigen='1'");
					$result = $cdb->fetch_array($sql);

					if ($result['link_type'] == 'EXTERNAL')
					{
						//Rechte fuer das Anlagen von Versionen vorhanden
						if ($ac->chk('link', 'r', $link_id))
						{
							$file_array = array('name'				=>	$result['name'],
												'save_time'			=>	$result['save_time'],
												'comment'			=>	$result['bemerkungen'],
												'link'				=>	$result['link'],
												'folder_id'			=>	$result['folder_id'],
												'link_type'			=>	'EXTERNAL',
												'link_id'			=>	$link_id);

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
							return false;
						}
					}
					//interner link
					elseif($result['file_id'] > 0)
					{
						$file_array = array();

						$sql = $cdb->select('SELECT file_id, folder_id, org_name, save_name, md5_file, mime_type, file_size, save_time, bemerkungen FROM fom_files WHERE file_id='.$result['file_id']);
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
													'md5_file'			=>	$result['md5_file'],
													'link_type'			=>	'INTERNAL',
													'link_id'			=>	$link_id);

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
			else
			{
				return 'false';
			}
		}
		return 'false';
	}
?>