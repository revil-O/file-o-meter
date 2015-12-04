<?php
	/**
	 * manages all downloads for which no login is required
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * manages all downloads for which no login is required
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Download
	{
		public $setup_array = array();

		public function __construct()
		{
			$this->setup_array['download_pfad'] = FOM_ABS_URL.'inc/download.php';
		}

		/**
		 * Traegt einen Downloadticket in die Datenbank
		 * @param int $file_id
		 * @param string $expire
		 * @param string $only_current_version
		 * @param string $public
		 * @return array
		 */
		public function insert_download($file_id, $expire, $only_current_version = '1', $public = '0')
		{
			$cdb = new MySql;

			$return_array = array();

			if ($file_id > 0)
			{
				$sql = $cdb->select('SELECT save_time FROM fom_files WHERE file_id='.$file_id);
				$result = $cdb->fetch_array($sql);

				//nach bereits exitierenden downloadkeys suchen
				$sql_exists = $cdb->select("SELECT md5 FROM fom_download WHERE file_id=$file_id AND only_current_version='$only_current_version' AND save_time='".$result['save_time']."' AND public='$public' AND expire='$expire'");
				$result_exists = $cdb->fetch_array($sql_exists);

				if (isset($result_exists['md5']) and !empty($result_exists['md5']))
				{
					$insert_sql = false;
					$key = $result_exists['md5'];
				}
				else
				{
					$insert_sql = true;
					$key = $this->get_download_key($file_id);
				}

				//neu eintragen
				if ($insert_sql === true)
				{
					if ($cdb->insert("INSERT INTO fom_download (file_id, md5, only_current_version, save_time, public, expire) VALUES ($file_id, '$key', '$only_current_version', '".$result['save_time']."', '$public', '$expire')"))
					{
						if ($cdb->get_affected_rows() == 1)
						{
							$return_array['result'] = true;
							$return_array['download'] = $this->setup_array['download_pfad'].'?typ_string=ex&amp;fileid_int='.$file_id.'&amp;key_string='.$key;

							return $return_array;
						}
						else
						{
							$return_array['result'] = false;
							return $return_array;
						}
					}
					else
					{
						$return_array['result'] = false;
						return $return_array;
					}
				}
				//bereits vorhandenen verwenden
				else
				{
					$return_array['result'] = true;
					$return_array['download'] = $this->setup_array['download_pfad'].'?typ_string=ex&amp;fileid_int='.$file_id.'&amp;key_string='.$key;

					return $return_array;
				}
			}
			else
			{
				$return_array['result'] = false;
				return $return_array;
			}
		}

		/**
		 * Erstellt Informationen zu einem Downloadticket
		 * @param int $file_id
		 * @param string $key
		 * @return array
		 */
		public function get_download($file_id, $key)
		{
			$cdb = new MySql;

			$return_array = array();

			$sql = $cdb->select("SELECT t1.expire, t2.org_name, t2.file_type FROM fom_download t1
								LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
								WHERE t1.file_id=$file_id AND t1.md5='$key'");
			$result = $cdb->fetch_array($sql);

			if ($result['expire'] > date('YmdHis'))
			{
				$pfad = $this->get_download_pfad($file_id, $key);

				if (!empty($pfad))
				{
					$cdb->update("UPDATE fom_download SET downloads = downloads + 1 WHERE file_id=$file_id AND md5='$key'");
					$return_array['result'] = true;
					$return_array['pfad'] = $pfad;
					$return_array['org_name'] = $result['org_name'];
					$return_array['mime_type'] = $result['file_type'];
					$return_array['size'] = filesize($pfad);
					return $return_array;
				}
				else
				{
					$return_array['result'] = false;
					return $return_array;
				}
			}
			else
			{
				$return_array['result'] = false;
				return $return_array;
			}
		}

		/**
		 * Erstellt Informationen zu einem Backup Downloadticket
		 * @param int $file_id
		 * @param string $key
		 * @return array
		 */
		public function get_backup_download($file_id, $key)
		{
			$cdb = new MySql();
			$cp = new CryptPw();

			$cryptpw_salt_array = $cp->get_salt_array();

			$return_array = array();

			$sql = $cdb->select('SELECT * FROM fom_backup WHERE backup_id='.$file_id);
			$result = $cdb->fetch_array($sql);

			if ($result['backup_id'] > 0)
			{
				if ($key == md5($cryptpw_salt_array['sz'][0].md5($result['filename']).$cryptpw_salt_array['sz'][1].md5($result['type']).$cryptpw_salt_array['sz'][2].md5($result['filesize']).$cryptpw_salt_array['sz'][3].md5($result['backup_id']).$cryptpw_salt_array['sz'][4].md5($result['backup_time']).$cryptpw_salt_array['sz'][5]))
				{
					$pfad = FOM_ABS_PFAD;

					if (substr($pfad, -1) == '/')
					{
						$pfad .= 'files/backup/'.$result['filename'];
					}
					else
					{
						$pfad .= "files\\backup\\".$result['filename'];
					}

					if (file_exists($pfad))
					{
						$return_array['result'] = true;
						$return_array['pfad'] = $pfad;
						$return_array['backup_name'] = $result['filename'];
						$return_array['size'] = filesize($pfad);
						return $return_array;
					}
					else
					{
						$return_array['result'] = false;
						return $return_array;
					}
				}
				else
				{
					$return_array['result'] = false;
					return $return_array;
				}
			}
			else
			{
				$return_array['result'] = false;
				return $return_array;
			}
		}

		/**
		 * Gibt den Absoluten Pfad zu einer Downloaddatei zurueck
		 * @param int $file_id
		 * @return string
		 */
		private function get_download_pfad($file_id, $key)
		{
			$cdb = new MySql;

			$sql = $cdb->select("SELECT file_id, only_current_version, save_time FROM fom_download WHERE file_id=$file_id AND md5='$key'");
			$result = $cdb->fetch_array($sql);

			//Immer die Aktuellste Version zum Download anbieten
			if ($result['only_current_version'] == '1')
			{
				$file_sql = $cdb->select('SELECT t1.save_name, t1.save_time, t2.projekt_id, t3.typ, t3.pfad FROM fom_files t1
										LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
										LEFT JOIN fom_file_server t3 ON t2.projekt_id=t3.projekt_id
										WHERE t1.file_id='.$result['file_id']);
				$file_result = $cdb->fetch_array($file_sql);
			}
			//Nur eine Bestimmt Version zum Download anbieten
			else
			{
				$file_sql = $cdb->select('SELECT t1.save_name, t1.save_time, t2.projekt_id, t3.typ, t3.pfad FROM fom_files t1
										LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
										LEFT JOIN fom_file_server t3 ON t2.projekt_id=t3.projekt_id
										WHERE t1.file_id='.$result['file_id']);
				$file_result = $cdb->fetch_array($file_sql);

				//In Versiontabelle suchen
				if ($file_result['save_time'] != $result['save_time'])
				{
					$file_sql = $cdb->select('SELECT t1.save_name, t1.save_time, t3.projekt_id, t4.typ, t4.pfad FROM fom_file_subversion t1
											LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
											LEFT JOIN fom_folder t3 ON t2.folder_id=t3.folder_id
											LEFT JOIN fom_file_server t4 ON t3.projekt_id=t4.projekt_id
											WHERE t1.file_id='.$result['file_id']." AND t1.save_time='".$result['save_time']."'");
					$file_result = $cdb->fetch_array($file_sql);
				}
			}

			if (!empty($file_result['typ']))
			{
				if ($file_result['typ'] == 'local')
				{
					$pfad = $file_result['pfad'].$file_result['projekt_id'].'/'.substr($file_result['save_time'], 0, 6).'/'.$file_result['save_name'];

					if (file_exists($pfad))
					{
						return $pfad;
					}
					else
					{
						return '';
					}
				}
				else
				{
					//FIXME: FTP fehlt hier
					return '';
				}
			}
			else
			{
				return '';
			}
		}

		/**
		 * Erstellt einen Eindeutigen Downloadkey
		 * @param int $file_id
		 * @return string
		 */
		private function get_download_key($file_id)
		{
			$cdb = new MySql;

			$key = md5(uniqid(rand()));

			$sql = $cdb->select("SELECT md5 FROM fom_download WHERE file_id=$file_id AND md5='$key'");
			$result = $cdb->fetch_array($sql);

			if (isset($result['md5']) and !empty($result['md5']))
			{
				return $this->get_download_key($file_id);
			}
			else
			{
				return $key;
			}
		}

		/**
		 * Entfernt alle abgelaufenen Downloadtickets
		 * @return void
		 */
		public function del_download()
		{
			$cdb = new MySql;

			$cdb->delete("DELETE FROM fom_download WHERE expire<'".date('YmdHis')."'");
		}
	}
?>