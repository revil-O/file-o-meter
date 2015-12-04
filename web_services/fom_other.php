<?php
	//gibt an welche functionen dem SOAP Server zur Verfuegung stehen sollen
	$soap_server_functions[] = 'get_files_and_links';
	$soap_server_functions[] = 'get_file_link_exists';
	$soap_server_functions[] = 'get_doctypes';
	$soap_server_functions[] = 'get_projects';

	/**
	 * Erstellt eine liste von Links und Dateien aus einem Projekt bzw. Projektverzeichnis
	 *
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @return string
	 */
	function get_files_and_links($ws_key, $project_id = 0, $folder_id = 0, $doctype_id = 0, $comment = '', $order_by = 'name_asc', $return_type = 'array', $recursive = false)
	{
		$link = get_links($ws_key, $project_id, $folder_id, $comment, $order_by, $return_type, $recursive);
		$file = get_files($ws_key, $project_id, $folder_id, $doctype_id, $comment, $order_by, $return_type, $recursive);

		if (!empty($link) and $link != 'false')
		{
			$link_array = @unserialize($link);
		}

		if (!empty($file) and $file != 'false')
		{
			$file_array = @unserialize($file);
		}

		if (isset($link_array) and isset($file_array) and is_array($link_array) and is_array($file_array))
		{
			$data_array = array_merge($link_array, $file_array);

			$sort_array = array();

			foreach ($data_array as $index => $data)
			{
				if (isset($data['file_name']))
				{
					$sort_array[$index] = strtolower($data['file_name']);
				}
				elseif (isset($data['name']))
				{
					$sort_array[$index] = strtolower($data['name']);
				}
				else
				{
					$sort_array[$index] = '';
				}
			}

			//array sortieren
			array_multisort($sort_array, SORT_ASC, SORT_STRING, $data_array);
		}
		elseif (isset($link_array) and is_array($link_array))
		{
			$data_array = $link_array;
		}
		elseif (isset($file_array) and is_array($file_array))
		{
			$data_array = $file_array;
		}
		else
		{
			return 'false1';
		}

		if ($return_type == 'array')
		{
			return serialize($data_array);
		}
		elseif ($return_type == 'json')
		{
			return json_encode($data_array);
		}
		else
		{
			return 'false2';
		}
	}

	/**
	 * Prueft ob eine Datei/Link im angegebenen Verzeichnis oder Projekt vorhanden ist.
	 * @param string $ws_key
	 * @param int $project_id
	 * @param int $folder_id
	 * @return boole
	 */
	function get_file_link_exists($ws_key, $project_id = 0, $folder_id = 0)
	{
		if (get_file_exists($ws_key, $project_id, $folder_id) == 'true')
		{
			return 'true';
		}
		elseif (get_link_exists($ws_key, $project_id, $folder_id) == 'true')
		{
			return 'true';
		}
		else
		{
			return 'false';
		}
	}

	/**
	 * Liefert alle verfuegbaren Dokumenttypen bzw. den Namen eines bestimmten Dokumententyps
	 *
	 * @param string $ws_key
	 * @param int $doctype_id
	 * @return string
	 */
	function get_doctypes($ws_key, $doctype_id = 0, $return_type = 'array')
	{
		$cdb = new MySql;
		$sl = new Login;

		$result_array = array();

		//Zugriffsrechte Pruefen
		if ($sl->webservice_key($ws_key))
		{

			if ($doctype_id > 0)
			{
				$where = 'WHERE t1.document_type_id ='.$doctype_id;
			}
			else
			{
				$where = '';
			}

			$sql = $cdb->select("SELECT t1.document_type_id, t1.document_type FROM fom_document_type t1
								$where ORDER BY t1.document_type ASC");

			while ($result = $cdb->fetch_array($sql))
			{
				if (!empty($result['document_type']))
				{
					$result_array['document_types'][] = array(	'document_type_id'	=> $result['document_type_id'],
																'document_type'		=> $result['document_type']);
				}
			}
		}

		if (is_array($result_array) and count($result_array) > 0)
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
	 * Gibt die Projektliste zurueck bzw. infos zu einem bestimmten projekt
	 * @param string $ws_key
	 * @param int $project_id
	 * @return string
	 */
	function get_projects($ws_key, $project_id = 0, $return_type = 'array')
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
				$ac = new Access;

				$return_array = array();
				$sql = $cdb->select("SELECT * FROM fom_projekte WHERE anzeigen='1' ORDER BY projekt_name ASC");
				while($result = $cdb->fetch_array($sql))
				{
					if ($ac->chk('project', 'r', $result['projekt_id']))
					{
						$return_array[] = $result;
					}
				}

				if (is_array($return_array) and count($return_array))
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
?>