<?php
	/**
	 * version-history class
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * version-history class
	 * @package file-o-meter
	 * @subpackage class
	 */
	class VersionHistory
	{
		public function get_file_overview($file_id)
		{
			$cdb = new MySql;

			$return_array = array();

			//Aktuelle Version auslesen
			$sql = $cdb->select("SELECT t1.file_id, t1.org_name, t1.file_size, t1.save_time, t2.vorname, t2.nachname FROM fom_files t1
								LEFT JOIN fom_user t2 ON t1.user_id=t2.user_id
								WHERE t1.file_id=$file_id AND t1.anzeigen='1'");
			$result = $cdb->fetch_array($sql);

			//Existiert eine Datei zur uebergebenen FileID
			if (isset($result['file_id']) and $result['file_id'] > 0)
			{
				$return_array['result'] = true;
				//Daten der Aktuellsten Version
				$return_array['data'][] = array('file_id' => $result['file_id'], 'org_name' => $result['org_name'], 'file_size' => round($result['file_size'] / 1048576,2).' MB', 'save_time' => $result['save_time'], 'user' => $result['vorname'].' '.$result['nachname']);

				//Daten aller frueheren Versionen
				$sub_sql = $cdb->select("SELECT t1.sub_fileid, t1.org_name, t1.file_size, t1.save_time, t2.vorname, t2.nachname FROM fom_file_subversion t1
										LEFT JOIN fom_user t2 ON t1.user_id=t2.user_id
										WHERE t1.file_id=".$result['file_id']." ORDER BY t1.save_time DESC");
				while($sub_result = $cdb->fetch_array($sub_sql))
				{
					$return_array['data'][] = array('subfile_id' => $sub_result['sub_fileid'], 'org_name' => $sub_result['org_name'], 'file_size' => round($sub_result['file_size'] / 1048576,2).' MB', 'save_time' => $sub_result['save_time'], 'user' => $sub_result['vorname'].' '.$sub_result['nachname']);
				}

				return $return_array;
			}
			else
			{
				$return_array['result'] = false;
				return $return_array;
			}
		}

		/**
		 * Gibt die aktuelle Versionnummer einer Datei zurueck
		 * @param int $file_id
		 * @return int
		 */
		public function get_version_number($file_id)
		{
			$cdb = new MySql;

			$sql = $cdb->select('SELECT COUNT(sub_fileid) AS count FROM fom_file_subversion WHERE file_id='.$file_id);
			$result = $cdb->fetch_array($sql);

			if (isset($result['count']) and !empty($result['count']))
			{
				return $result['count'] + 1;
			}
			else
			{
				return 1;
			}
		}
	}
?>