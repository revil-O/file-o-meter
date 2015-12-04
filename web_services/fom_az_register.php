<?php
	//gibt an welche functionen dem SOAP Server zur Verfuegung stehen sollen
	$soap_server_functions[] = 'get_az_register_file';
	$soap_server_functions[] = 'get_az_register_link';
	$soap_server_functions[] = 'get_az_register_project';
	$soap_server_functions[] = 'get_az_register_folder';
	$soap_server_functions[] = 'insert_az_register';

	/**
	 * Prueft ob zur Uebergebenen FileId ein A-Z Register eintrag vorhanden ist und gibt eventuell vorhandene Daten zurueck
	 * @param string $ws_key
	 * @param int $file_id
	 * @param string $return_type
	 * @return string
	 */
	function get_az_register_file($ws_key, $file_id, $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;

		$result_array = array();
		$result_exists = false;

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

				$sql = $cdb->select('SELECT file_id FROM fom_files WHERE file_id='.$file_id);
				$result = $cdb->fetch_array($sql);

				if (isset($result['file_id']) and $result['file_id'] > 0)
				{
					//Rechte fuer das Anlagen von Versionen vorhanden
					if ($ac->chk('file', 'r', $result['file_id']))
					{
						$az_sql = $cdb->select("SELECT t1.word_id, t1.sign, t2.word FROM fom_search_word_az_file t1
												LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
												WHERE t1.file_id=$file_id OR t1.sub_fileid=$file_id");
						while ($az_result = $cdb->fetch_array($az_sql))
						{
							$result_exists = true;
							$result_array[] = array(	'file_id'	=> $result['file_id'],
														'word_id'	=> $az_result['word_id'],
														'word'		=> $az_result['word'],
														'sign'		=> $az_result['sign']);
						}
					}
				}

			}
		}

		if ($result_exists === true)
		{
			if ($return_type == 'array')
			{
				return serialize($result_array);
			}
			elseif ($return_type == 'json')
			{
				return json_encode($result_array);
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
	 * Prueft ob zur Uebergebenen LinkId ein A-Z Register eintrag vorhanden ist und gibt eventuell vorhandene Daten zurueck
	 * @param string $ws_key
	 * @param int $link_id
	 * @param string $return_type
	 * @return string
	 */
	function get_az_register_link($ws_key, $link_id, $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;

		$result_array = array();
		$result_exists = false;

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

				$sql = $cdb->select('SELECT link_id FROM fom_link WHERE link_id='.$link_id);
				$result = $cdb->fetch_array($sql);

				if (isset($result['link_id']) and $result['link_id'] > 0)
				{
					//Rechte fuer das Anlagen von Versionen vorhanden
					if ($ac->chk('link', 'r', $result['link_id']))
					{
						$az_sql = $cdb->select('SELECT t1.word_id, t1.sign, t2.word FROM fom_search_word_az_link t1
												LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
												WHERE t1.link_id='.$link_id);
						while ($az_result = $cdb->fetch_array($az_sql))
						{
							$result_exists = true;
							$result_array[] = array(	'link_id'	=> $result['link_id'],
														'word_id'	=> $az_result['word_id'],
														'word'		=> $az_result['word'],
														'sign'		=> $az_result['sign']);
						}
					}
				}

			}
		}

		if ($result_exists === true)
		{
			if ($return_type == 'array')
			{
				return serialize($result_array);
			}
			elseif ($return_type == 'json')
			{
				return json_encode($result_array);
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
	 * Gibt alle Dateien aus einem Projekt zurueck die einen A-Z Register Eintrag haben
	 * @param string $ws_key
	 * @param int $project_id
	 * @param string $return_type
	 * @return string
	 */
	function get_az_register_project($ws_key, $project_id, $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;

		$result_array = array();

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
				$sql = $cdb->select('SELECT t1.file_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t2.projekt_id='.$project_id." AND t1.anzeigen='1'");
				while ($result = $cdb->fetch_array($sql))
				{
					$az_result = get_az_register_file($ws_key, $result['file_id'], 'array');

					if ($az_result != 'false')
					{
						$az_result_array = @unserialize($az_result);

						if (is_array($az_result_array))
						{
							for ($i = 0; $i < count($az_result_array); $i++)
							{
								$az_result_array[$i]['from'] = 'extern';
								$az_result_array[$i]['type'] = 'file';
							}
							$result_array = array_merge($result_array, $az_result_array);
						}
					}
				}

				$sql = $cdb->select('SELECT t1.link_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t2.projekt_id='.$project_id." AND t1.anzeigen='1'");
				while ($result = $cdb->fetch_array($sql))
				{
					$az_result = get_az_register_link($ws_key, $result['link_id'], 'array');

					if ($az_result != 'false')
					{
						$az_result_array = @unserialize($az_result);

						if (is_array($az_result_array))
						{
							for ($i = 0; $i < count($az_result_array); $i++)
							{
								$az_result_array[$i]['from'] = 'extern';
								$az_result_array[$i]['type'] = 'link';
							}
							$result_array = array_merge($result_array, $az_result_array);
						}
					}
				}
			}
		}

		if (count($result_array) > 0)
		{
			if ($return_type == 'array')
			{
				return serialize($result_array);
			}
			elseif ($return_type == 'json')
			{
				return json_encode($result_array);
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
	 * Gibt alle Dateien aus einem Verzeichnis zurueck die einen A-Z Register Eintrag haben
	 * @param string $ws_key
	 * @param int $folder_id
	 * @param string $return_type
	 * @return string
	 */
	function get_az_register_folder($ws_key, $folder_id, $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;

		$result_array = array();

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
				$sql = $cdb->select('SELECT file_id FROM fom_files WHERE folder_id='.$folder_id." AND anzeigen='1'");
				while ($result = $cdb->fetch_array($sql))
				{
					$az_result = get_az_register_file($ws_key, $result['file_id'], 'array');

					if ($az_result != 'false')
					{
						$az_result_array = @unserialize($az_result);

						if (is_array($az_result_array))
						{
							for ($i = 0; $i < count($az_result_array); $i++)
							{
								$az_result_array[$i]['from'] = 'intern';
								$az_result_array[$i]['type'] = 'file';
							}
							$result_array = array_merge($result_array, $az_result_array);
						}
					}
				}

				$sql = $cdb->select('SELECT link_id FROM fom_link WHERE folder_id='.$folder_id." AND anzeigen='1'");
				while ($result = $cdb->fetch_array($sql))
				{
					$az_result = get_az_register_link($ws_key, $result['link_id'], 'array');

					if ($az_result != 'false')
					{
						$az_result_array = @unserialize($az_result);

						if (is_array($az_result_array))
						{
							for ($i = 0; $i < count($az_result_array); $i++)
							{
								$az_result_array[$i]['from'] = 'intern';
								$az_result_array[$i]['type'] = 'link';
							}
							$result_array = array_merge($result_array, $az_result_array);
						}
					}
				}
			}
		}

		if (count($result_array) > 0)
		{
			if ($return_type == 'array')
			{
				return serialize($result_array);
			}
			elseif ($return_type == 'json')
			{
				return json_encode($result_array);
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
	 * Traegt zu einer Datei A-Z Register Eintraege ein
	 * @param string $ws_key
	 * @param int $file_id
	 * @param string $sign_string, serialize(array())
	 * @param string $word_string, serialize(array())
	 * @param string $is_subfile, 'true' or 'false'
	 * @return string
	 */
	function insert_az_register($ws_key, $file_id, $sign_string, $word_string, $is_subfile = 'false')
	{
		$cdb = new MySql;
		$sl = new Login;

		$result_array = array();

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
				if (!empty($sign_string) and !empty($word_string))
				{
					$sign_array = @unserialize($sign_string);
					$word_array = @unserialize($word_string);

					if (is_array($sign_array) and is_array($word_array))
					{
						if ($is_subfile == 'false')
						{
							$is_subfile = false;
						}
						else
						{
							$is_subfile = true;
						}

						$fj = new FileJobs();

						if ($fj->insert_az_register_keys($sign_array, $word_array, $file_id, $is_subfile))
						{
							return 'true';
						}
					}
				}
			}
		}
		return 'false';
	}
?>