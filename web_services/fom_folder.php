<?php
	//gibt an welche functionen dem SOAP Server zur Verfuegung stehen sollen
	$soap_server_functions[] = 'get_folder';
	$soap_server_functions[] = 'add_folder';
	$soap_server_functions[] = 'edit_folder';
	//$soap_server_functions[] = 'edit_folder';

	/**
	 * Erstellt eine liste von Verzeichnissen aus einem Projekt bzw. Projektverzeichnis
	 *
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @return string
	 */
	function get_folder($ws_key, $project_id = 0, $folder_id = 0, $return_type = 'array')
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
				$gt = new Tree();
				$ac = new Access;

				if ($project_id > 0)
				{
					$where = "WHERE projekt_id=$project_id";
				}
				else
				{
					$where = '';
				}

				$sql = $cdb->select("SELECT projekt_id FROM fom_projekte $where ORDER BY projekt_name ASC");
				while($result = $cdb->fetch_array($sql))
				{
					if ($ac->chk('project', 'r', $result['projekt_id']))
					{
						$gt->get_folder($result['projekt_id'], $folder_id);
					}
				}

				$folder_result = array();
				$folder_result = $gt->tmp_array;

				if (count($folder_result) > 0)
				{
					if ($return_type == 'array')
					{
						return serialize($folder_result);
					}
					elseif ($return_type == 'json')
					{
						return json_encode($folder_result);
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
	 * Erstellt ein neues Verzeichnis
	 *
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @param string $utf8_folder_name
	 * @param string $utf8_folder_desc
	 * @return string
	 */
	function add_folder($ws_key, $project_id = 0, $folder_id = 0, $utf8_folder_name = '', $utf8_folder_desc = '', $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;
		$gt = new Tree();

		$return_array = array();

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
				$ac = new Access;

				if (is_numeric($project_id) && $project_id > 0)
				{
					$where = "WHERE projekt_id=$project_id";
				}
				else
				{
					$where = '';
				}

				$sql = $cdb->select("SELECT projekt_id FROM fom_projekte $where ORDER BY projekt_name ASC");
				while($result = $cdb->fetch_array($sql))
				{
					if ($ac->chk('project', 'w', $result['projekt_id']))
					{
						$gt->get_folder($result['projekt_id'], $folder_id);
					}
				}



				$pflichtfelder = 0;

				if (!isset($utf8_folder_name) or empty($utf8_folder_name)){$pflichtfelder++;}

				if ($pflichtfelder == 0)
				{
					//$folder_name = mysql_real_escape_string(utf8_decode($utf8_folder_name));
					$folder_name = $utf8_folder_name;
					//$folder_desc = mysql_real_escape_string(utf8_decode($utf8_folder_desc));
					$folder_desc = $utf8_folder_desc;

					//Unterverzeichnis anlegen
					if (is_numeric($folder_id) and $folder_id > 0)
					{
						$sql_chk = $cdb->select('SELECT folder_id, projekt_id, ebene FROM fom_folder WHERE folder_id='.$folder_id);
						$result = $cdb->fetch_array($sql_chk);

						if ($result['folder_id'] > 0)
						{
							//Keine Doppelten Verzeichnisse
							$sql_chk = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=".$folder_id." AND anzeigen='1' AND folder_name='".$folder_name."'");
							$sub_result = $cdb->fetch_array($sql_chk);

							if (!isset($sub_result['folder_id']) or empty($sub_result['folder_id']))
							{
								$ebene = $result['ebene'] + 1;
								$sql_insert = "INSERT INTO fom_folder (projekt_id, folder_name, bemerkungen, ob_folder, ebene) VALUES ('".$result['projekt_id']."', '".$folder_name."', '".$folder_desc."', '".$result['folder_id']."', '$ebene')";
							}
							else
							{
								$return_array['error'] = 'The specified foldername "'.$folder_name.'" already exists!';
								$return_array['error_code'] = 'folder_exists';
							}
						}
					}
					else
					{
						$sub_result_counter = 0;

						//Keine Doppelten Verzeichnisse
						$sql_chk = $cdb->select("SELECT folder_id FROM fom_folder WHERE projekt_id='".$project_id."' AND anzeigen='1' AND ob_folder=0 AND folder_name='".$folder_name."'");
						$sub_result = $cdb->fetch_array($sql_chk);

						if (!isset($sub_result['folder_id']) or empty($sub_result['folder_id']))
						{
							$sql_insert = "INSERT INTO fom_folder (projekt_id, folder_name, bemerkungen, ob_folder, ebene) VALUES ('".$project_id."', '".$folder_name."', '".$folder_desc."', 0, 0)";
						}
						else
						{
							$return_array['error'] = 'The specified foldername "'.$folder_name.'" already exists!';
							$return_array['error_code'] = 'folder_exists';
						}
					}

					//Fehler mit abbruch
					if (isset($return_array['error']))
					{
						if ($return_type == 'array')
						{
							return serialize($return_array);
						}
						elseif ($return_type == 'json')
						{
							return json_encode($return_array);
						}
						else
						{
							return 'false';
						}
					}

					if (isset($sql_insert))
					{
						if ($cdb->insert($sql_insert))
						{
							if ($cdb->get_affected_rows() == 1)
							{
								$return_array = array('folder_id' => $cdb->get_last_insert_id(), 'folder_name' => $folder_name, 'folder_desc' => $folder_desc);
							}
							else
							{
								$return_array['error'] = 'Database insert failed';
							}
						}
						else
						{
							$return_array['error'] = 'Database insert error';
						}
					}
					else
					{
						$return_array['error'] = 'Database query error';
					}
				}
				else
				{
					$return_array['error'] = 'Missing value: Foldername';
				}
			}
			else
			{
				$return_array['error'] = 'No User-ID defined!';
			}
		}
		else
		{
			$return_array['error'] = 'Not authorized!';
		}

		//Daten bzw. Fehler uebergeben
		if ($return_type == 'array')
		{
			return serialize($return_array);
		}
		elseif ($return_type == 'json')
		{
			return json_encode($return_array);
		}
		else
		{
			return 'false';
		}
	}

	/**
	 * Bearbeitet ein Verzeichnis
	 *
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @param string $folder_name
	 * @param string $folder_desc
	 * @return string
	 */
	function edit_folder($ws_key, $folder_id, $folder_name, $folder_desc)
	{
		$cdb = new MySql;
		$sl = new Login;
		$gt = new Tree();

		$return_array = array();

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
				$ac = new Access;

				if ($ac->chk('folder', 'w', $folder_id))
				{
					$pflichtfelder = 0;

					if (empty($folder_name))							{$pflichtfelder++;}
					if (empty($folder_id) or !is_numeric($folder_id))	{$pflichtfelder++;}

					if ($pflichtfelder == 0)
					{
						//$folder_name = mysql_real_escape_string(utf8_decode($folder_name));
						//$folder_desc = mysql_real_escape_string(utf8_decode($folder_desc));

						$folder_name = $folder_name;
						$folder_desc = $folder_desc;

						if ($cdb->update("UPDATE fom_folder SET
											folder_name='".$folder_name."',
											bemerkungen='".$folder_desc."'
											WHERE folder_id=".$folder_id))
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