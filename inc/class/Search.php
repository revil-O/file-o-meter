<?php
	/**
	 * provides several search-functions
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * provides several search-functions
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Search
	{
		private $search_result = array();
		public $search_counter = array();
		private $setup_array = array();
		private $mime_array = array();
		private $tmp_array = array();

		public function __construct()
		{
			//Relevanzfaktor fuer die Suchpositionierung
			//z.B. zaehlt das Suchwort im Dateinamen mehr als im enthaltenen Text innerhalb der Datei
			$this->search_counter['word_detail'] = 1;
			$this->search_counter['word'] = 10;
			$this->search_counter['word_tagging_detail'] = 5;
			$this->search_counter['word_tagging'] = 15;
			$this->search_counter['file_name_detail'] = 1;
			$this->search_counter['file_name'] = 20;
			$this->search_counter['file_date'] = 20;
			$this->search_counter['link_name_detail'] = 1;
			$this->search_counter['link_name'] = 20;
			$this->search_counter['link_date'] = 20;

			//Speicherzeit fuer Suchergebnisse in Sek.
			$this->setup_array['cache_time'] = 900;
		}

		/**
		 * Fuehrt eine Suche nach einer Datei in der DB durch
		 * @param array $param
		 * @return array
		 */
		public function search($param)
		{
			$cdb = new MySql;

			//Pruefen ob die Suchanfrage bereits existiert, max alter eines gespeicherten Suchergebnisses 15 min
			$search_key = md5(serialize($param));
			$sql = $cdb->select("SELECT search_result FROM fom_search_cache WHERE seach_key='$search_key' AND search_time>='".date('YmdHis', time() - $this->setup_array['cache_time'])."'");
			$result = $cdb->fetch_array($sql);

			if (!empty($result['search_result']))
			{
				$result = @unserialize($result['search_result']);
				if (is_array($result))
				{
					return $result;
				}
			}

			//Normale Suche durchfuehren
			//Suche im Inhalt einer Datei
			if (!empty($param['file_data']))
			{
				$word_list_array = array();
				//mehr als ein Wort
				if (strpos($param['file_data'], ' ') !== false)
				{
					$tmp_ex = explode(' ', $param['file_data']);

					for($i = 0; $i < count($tmp_ex); $i++)
					{
						$word_list_array[] = strtolower(trim($tmp_ex[$i]));
					}
				}
				else
				{
					$word_list_array[] = strtolower(trim($param['file_data']));
				}
				$this->search_word($word_list_array, $param);

				//Unterverzeichnisse durchsuchen
				if ($param['subfolder'] == 1 and $param['fid_int'] > 0)
				{
					$this->search_word_subfolder($word_list_array, $param);
				}
			}

			//nach Dateinamen Suchen
			if (!empty($param['file_name']))
			{
				//Suche nache name einer Datei
				$this->search_file_name($param);
				//suche nach name eines links
				$this->search_link_name($param);

				//Unterverzeichnisse durchsuchen
				if ($param['subfolder'] == 1 and $param['fid_int'] > 0)
				{
					//Dateien
					$this->search_file_name_subfolder($param);
					//Links
					$this->search_link_name_subfolder($param);
				}
			}

			//es wird nur nach Datum gesucht
			if (!empty($param['file_date']) and empty($param['file_name']) and empty($param['file_data']))
			{
				//Suche nache name einer Datei
				$this->search_file_date($param);
				//suche nach name eines links
				$this->search_link_date($param);

				//Unterverzeichnisse durchsuchen
				if ($param['subfolder'] == 1 and $param['fid_int'] > 0)
				{
					//Dateien
					$this->search_file_date_subfolder($param);
					//Links
					$this->search_link_date_subfolder($param);
				}
			}

			//Array nach Relevanz Sortieren
			arsort($this->search_result);

			$new_search_array = $this->order_search_array($this->search_result);
			//Suchergebniss zwischenspeichern
			$cdb->insert("INSERT INTO fom_search_cache (seach_key, search_result, search_time) VALUES ('$search_key', '".serialize($new_search_array)."', '".date('YmdHis')."')");

			return $new_search_array;
		}

		/**
		 * Sucht Dateien nach dem Speicherdatum im aktuellen Verzeichnis
		 * @param array $param
		 * @return void
		 */
		private function search_file_date($param)
		{
			$cdb = new MySql;

			$folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];
			$mime_typ = $param['mime_typ'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$where_array = array();

			if ($folder_id > 0)
			{
				$where_array[] = 't2.folder_id='.$folder_id;
			}

			if ($project_id > 0)
			{
				$where_array[] = 't2.projekt_id='.$project_id;
			}

			if (!empty($file_date))
			{
				if ($file_date_type == 'before')
				{
					$file_date_sql = $file_date.'235959';
					$where_array[] = "t1.save_time<='$file_date_sql'";
				}
				else
				{
					$file_date_sql = $file_date.'000000';
					$where_array[] = "t1.save_time>='$file_date_sql'";
				}
			}

			if (!empty($mime_typ))
			{
				//nur ein teil des MIME Typs vorhanden
				if (substr($mime_typ, -1) == '%')
				{
					$tmp_len = strlen($mime_typ) - 1;
					$where_array[] = "SUBSTRING(t1.mime_type, 1, $tmp_len)='".substr($mime_typ, 0, $tmp_len)."'";
				}
				//Ganzer MIME Type vorhanden
				else
				{
					$where_array[] = "t1.mime_type='$mime_typ'";
				}
			}

			$where_array[] = "t1.anzeigen='1'";

			$where_string = '';
			foreach ($where_array as $wa)
			{
				if (empty($where_string))
				{
					$where_string = 'WHERE '.$wa;
				}
				else
				{
					$where_string .= ' AND '.$wa;
				}
			}

			//Exakte Suche
			$sql = $cdb->select("SELECT t1.file_id FROM fom_files t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								$where_string");
			while($result = $cdb->fetch_array($sql))
			{
				$this->count_result($result['file_id'], 'file_date');
			}
		}

		/**
		 * Sucht Dateien nach dem Speicherdatum in allen Unterverzeichnissen
		 * @param array $param
		 * @return void
		 */
		private function search_file_date_subfolder($param)
		{
			$cdb = new MySql;

			$ob_folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];
			$mime_typ = $param['mime_typ'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$f_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$ob_folder_id AND anzeigen='1'");
			while($f_result = $cdb->fetch_array($f_sql))
			{
				$where_array = array();

				$where_array[] = 't2.folder_id='.$f_result['folder_id'];

				if ($project_id > 0)
				{
					$where_array[] = 't2.projekt_id='.$project_id;
				}

				if (!empty($file_date))
				{
					if ($file_date_type == 'before')
					{
						$file_date_sql = $file_date.'235959';
						$where_array[] = "t1.save_time<='$file_date_sql'";
					}
					else
					{
						$file_date_sql = $file_date.'000000';
						$where_array[] = "t1.save_time>='$file_date_sql'";
					}
				}

				if (!empty($mime_typ))
				{
					//nur ein teil des MIME Typs vorhanden
					if (substr($mime_typ, -1) == '%')
					{
						$tmp_len = strlen($mime_typ) - 1;
						$where_array[] = "SUBSTRING(t1.mime_type, 1, $tmp_len)='".substr($mime_typ, 0, $tmp_len)."'";
					}
					//Ganzer MIME Type vorhanden
					else
					{
						$where_array[] = "t1.mime_type='$mime_typ'";
					}
				}

				$where_array[] = "t1.anzeigen='1'";

				$where_string = '';
				foreach ($where_array as $wa)
				{
					if (empty($where_string))
					{
						$where_string = 'WHERE '.$wa;
					}
					else
					{
						$where_string .= ' AND '.$wa;
					}
				}

				//Exakte Suche
				$sql = $cdb->select("SELECT t1.file_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									$where_string");
				while($result = $cdb->fetch_array($sql))
				{
					$this->count_result($result['file_id'], 'file_date');
				}

				//Selbstaufruf fuer weitere unterverzeichnisse
				$tmp_param = array('file_date' => $param['file_date'], 'file_date_type' => $param['file_date_type'], 'fid_int' => $f_result['folder_id'], 'pid_int' => $project_id, 'mime_typ' => $param['mime_typ']);
				$this->search_file_date_subfolder($tmp_param );
			}
		}

		/**
		 * Sucht Links nach dem Speicherdatum im aktuellen Verzeichnis
		 * @param array $param
		 * @return void
		 */
		private function search_link_date($param)
		{
			$cdb = new MySql;

			$link_name = $param['file_name'];
			$folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$where_array = array();

			if ($folder_id > 0)
			{
				$where_array[] = 't2.folder_id='.$folder_id;
			}

			if ($project_id > 0)
			{
				$where_array[] = 't2.projekt_id='.$project_id;
			}

			if (!empty($file_date))
			{
				if ($file_date_type == 'before')
				{
					$file_date_sql = $file_date.'235959';
					$where_array[] = "t1.save_time<='$file_date_sql'";
				}
				else
				{
					$file_date_sql = $file_date.'000000';
					$where_array[] = "t1.save_time>='$file_date_sql'";
				}
			}

			$where_array[] = "t1.anzeigen='1'";

			$where_string = '';
			foreach ($where_array as $wa)
			{
				if (empty($where_string))
				{
					$where_string = 'WHERE '.$wa;
				}
				else
				{
					$where_string .= ' AND '.$wa;
				}
			}

			//Exakte Suche
			$sql = $cdb->select("SELECT t1.link_id FROM fom_link t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								$where_string");
			while ($result = $cdb->fetch_array($sql))
			{
				$this->count_result($result['link_id'], 'link_date', 'link');
			}
		}

		/**
		 * Sucht links nach Ihrem Speicherdatum im allen Unterverzeichnissen
		 * @param array $param
		 * @return void
		 */
		private function search_link_date_subfolder($param)
		{
			$cdb = new MySql;

			$ob_folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$f_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$ob_folder_id AND anzeigen='1'");
			while($f_result = $cdb->fetch_array($f_sql))
			{
				$where_array = array();

				$where_array[] = 't2.folder_id='.$f_result['folder_id'];

				if ($project_id > 0)
				{
					$where_array[] = 't2.projekt_id='.$project_id;
				}

				if (!empty($file_date))
				{
					if ($file_date_type == 'before')
					{
						$file_date_sql = $file_date.'235959';
						$where_array[] = "t1.save_time<='$file_date_sql'";
					}
					else
					{
						$file_date_sql = $file_date.'000000';
						$where_array[] = "t1.save_time>='$file_date_sql'";
					}
				}
				$where_array[] = "t1.anzeigen='1'";

				$where_string = '';
				foreach ($where_array as $wa)
				{
					if (empty($where_string))
					{
						$where_string = 'WHERE '.$wa;
					}
					else
					{
						$where_string .= ' AND '.$wa;
					}
				}

				//Exakte Suche
				$sql = $cdb->select("SELECT t1.link_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									$where_string");
				while($result = $cdb->fetch_array($sql))
				{
					$this->count_result($result['link_id'], 'link_date', 'link');
				}
				//Selbstaufruf fuer weitere unterverzeichnisse
				$tmp_param = array('file_date' => $param['file_date'], 'file_date_type' => $param['file_date_type'], 'fid_int' => $f_result['folder_id'], 'pid_int' => $project_id, 'mime_typ' => $param['mime_typ']);
				$this->search_link_date_subfolder($tmp_param );
			}
		}

		/**
		 * Sucht Dateinamen im aktuellen Verzeichnis
		 * @param array $param
		 * @return void
		 */
		private function search_file_name($param)
		{
			$cdb = new MySql;

			$file_name = $param['file_name'];
			$folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];
			$mime_typ = $param['mime_typ'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$tmp_file_id_array = array();
			$where_string = '';

			if ($folder_id > 0)
			{
				$where_string .= ' AND t2.folder_id='.$folder_id;
			}

			if ($project_id > 0)
			{
				$where_string .= ' AND t2.projekt_id='.$project_id;
			}

			if (!empty($mime_typ))
			{
				//nur ein teil des MIME Typs vorhanden
				if (substr($mime_typ, -1) == '%')
				{
					$tmp_len = strlen($mime_typ) - 1;
					$where_string .= " AND SUBSTRING(t1.mime_type, 1, $tmp_len)='".substr($mime_typ, 0, $tmp_len)."'";
				}
				//Ganzer MIME Type vorhanden
				else
				{
					$where_string .= " AND t1.mime_type='$mime_typ'";
				}
			}

			if (!empty($file_date))
			{
				if ($file_date_type == 'before')
				{
					$file_date_sql = $file_date.'235959';
					$where_string .= " AND t1.save_time<='$file_date_sql'";
				}
				else
				{
					$file_date_sql = $file_date.'000000';
					$where_string .= " AND t1.save_time>='$file_date_sql'";
				}
			}

			$where_string .= " AND t1.anzeigen='1'";

			//Exakte Suche
			$sql = $cdb->select("SELECT t1.file_id FROM fom_files t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								WHERE t1.org_name='$file_name' $where_string");
			while($result = $cdb->fetch_array($sql))
			{
				$tmp_file_id_array[] = $result['file_id'];
				$this->count_result($result['file_id'], 'file_name');
			}

			//Teilweise uebereinstimmung
			$sql = $cdb->select("SELECT t1.file_id FROM fom_files t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								WHERE t1.org_name LIKE '%$file_name%' $where_string");
			while($result = $cdb->fetch_array($sql))
			{
				//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
				if (!in_array($result['file_id'], $tmp_file_id_array))
				{
					$this->count_result($result['file_id'], 'file_name_detail');
				}
			}
		}

		/**
		 * Sucht Dateinamen in allen Unterverzeichnissen
		 * @param array $param
		 * @return void
		 */
		private function search_file_name_subfolder($param)
		{
			$cdb = new MySql;

			$file_name = $param['file_name'];
			$ob_folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];
			$mime_typ = $param['mime_typ'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$f_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$ob_folder_id AND anzeigen='1'");
			while($f_result = $cdb->fetch_array($f_sql))
			{
				$tmp_file_id_array = array();

				$where_string = ' AND t2.folder_id='.$f_result['folder_id'];

				if ($project_id > 0)
				{
					$where_string .= ' AND t2.projekt_id='.$project_id;
				}

				if (!empty($mime_typ))
				{
					//nur ein teil des MIME Typs vorhanden
					if (substr($mime_typ, -1) == '%')
					{
						$tmp_len = strlen($mime_typ) - 1;
						$where_string .= " AND SUBSTRING(t1.mime_type, 1, $tmp_len)='".substr($mime_typ, 0, $tmp_len)."'";
					}
					//Ganzer MIME Type vorhanden
					else
					{
						$where_string .= " AND t1.mime_type='$mime_typ'";
					}
				}

				if (!empty($file_date))
				{
					if ($file_date_type == 'before')
					{
						$file_date_sql = $file_date.'235959';
						$where_string .= " AND t1.save_time<='$file_date_sql'";
					}
					else
					{
						$file_date_sql = $file_date.'000000';
						$where_string .= " AND t1.save_time>='$file_date_sql'";
					}
				}
				$where_string .= " AND t1.anzeigen='1'";

				//Exakte Suche
				$sql = $cdb->select("SELECT t1.file_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.org_name='$file_name' $where_string");
				while($result = $cdb->fetch_array($sql))
				{
					$tmp_file_id_array[] = $result['file_id'];
					$this->count_result($result['file_id'], 'file_name');
				}

				//Teilweise uebereinstimmung
				$sql = $cdb->select("SELECT t1.file_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.org_name LIKE '%$file_name%' $where_string");
				while($result = $cdb->fetch_array($sql))
				{
					//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
					if (!in_array($result['file_id'], $tmp_file_id_array))
					{
						$this->count_result($result['file_id'], 'file_name_detail');
					}
				}

				//Selbstaufruf fuer weitere unterverzeichnisse
				$tmp_param = array('file_name' => $file_name, 'fid_int' => $f_result['folder_id'], 'pid_int' => $project_id, 'mime_typ' => $mime_typ);
				$this->search_file_name_subfolder($tmp_param );
			}
		}

		/**
		 * Sucht Linknamen im aktuellen Verzeichnis
		 * @param array $param
		 * @return void
		 */
		private function search_link_name($param)
		{
			$cdb = new MySql;

			$link_name = $param['file_name'];
			$folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$tmp_link_id_array = array();
			$where_string = '';

			if ($folder_id > 0)
			{
				$where_string .= ' AND t2.folder_id='.$folder_id;
			}

			if ($project_id > 0)
			{
				$where_string .= ' AND t2.projekt_id='.$project_id;
			}

			if (!empty($file_date))
			{
				if ($file_date_type == 'before')
				{
					$file_date_sql = $file_date.'235959';
					$where_string .= " AND t1.save_time<='$file_date_sql'";
				}
				else
				{
					$file_date_sql = $file_date.'000000';
					$where_string .= " AND t1.save_time>='$file_date_sql'";
				}
			}

			$where_string .= " AND t1.anzeigen='1'";

			//Exakte Suche
			$sql = $cdb->select("SELECT t1.link_id FROM fom_link t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								WHERE (t1.name='$link_name' OR t1.link='$link_name') $where_string");
			while ($result = $cdb->fetch_array($sql))
			{
				$tmp_link_id_array[] = $result['link_id'];
				$this->count_result($result['link_id'], 'link_name', 'link');
			}

			//Teilweise uebereinstimmung
			$sql = $cdb->select("SELECT t1.link_id FROM fom_link t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								WHERE (t1.name LIKE '%$link_name%' OR t1.link LIKE '%$link_name%') $where_string");
			while ($result = $cdb->fetch_array($sql))
			{
				//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
				if (!in_array($result['link_id'], $tmp_link_id_array))
				{
					$this->count_result($result['link_id'], 'link_name_detail', 'link');
				}
			}
		}

		/**
		 * Sucht linknamen in allen Unterverzeichnissen
		 * @param array $param
		 * @return void
		 */
		private function search_link_name_subfolder($param)
		{
			$cdb = new MySql;

			$link_name = $param['file_name'];
			$ob_folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$f_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$ob_folder_id AND anzeigen='1'");
			while($f_result = $cdb->fetch_array($f_sql))
			{
				$tmp_link_id_array = array();

				$where_string = ' AND t2.folder_id='.$f_result['folder_id'];

				if ($project_id > 0)
				{
					$where_string .= ' AND t2.projekt_id='.$project_id;
				}

				if (!empty($file_date))
				{
					if ($file_date_type == 'before')
					{
						$file_date_sql = $file_date.'235959';
						$where_string .= " AND t1.save_time<='$file_date_sql'";
					}
					else
					{
						$file_date_sql = $file_date.'000000';
						$where_string .= " AND t1.save_time>='$file_date_sql'";
					}
				}

				$where_string .= " AND t1.anzeigen='1'";

				//Exakte Suche
				$sql = $cdb->select("SELECT t1.link_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE (t1.name='$link_name' OR t1.link='$link_name') $where_string");
				while($result = $cdb->fetch_array($sql))
				{
					$tmp_link_id_array[] = $result['link_id'];
					$this->count_result($result['link_id'], 'link_name', 'link');
				}

				//Teilweise uebereinstimmung
				$sql = $cdb->select("SELECT t1.link_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE (t1.name LIKE '%$link_name%' OR t1.link LIKE '%$link_name%') $where_string");
				while($result = $cdb->fetch_array($sql))
				{
					//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
					if (!in_array($result['link_id'], $tmp_link_id_array))
					{
						$this->count_result($result['link_id'], 'link_name_detail', 'link');
					}
				}

				//Selbstaufruf fuer weitere unterverzeichnisse
				$tmp_param = array('file_name' => $link_name, 'fid_int' => $f_result['folder_id'], 'pid_int' => $project_id);
				$this->search_link_name_subfolder($tmp_param );
			}
		}

		/**
		 * Fuehrt eine Suche nach einem oder mehreren Woertern durch
		 * @param array $word_list_array
		 * @param array $param
		 * @return void
		 */
		private function search_word($word_list_array, $param)
		{
			$cdb = new MySql;

			$folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];
			$mime_typ = $param['mime_typ'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$tmp_file_id_array = array();
			$tmp_link_id_array = array();

			$where_string = '';
			$where_mime_string = '';
			if ($folder_id > 0)
			{
				$where_string .= ' AND t4.folder_id='.$folder_id;
			}
			if ($project_id > 0)
			{
				$where_string .= ' AND t4.projekt_id='.$project_id;
			}

			if (!empty($mime_typ))
			{
				//nur ein teil des MIME Typs vorhanden
				if (substr($mime_typ, -1) == '%')
				{
					$tmp_len = strlen($mime_typ) - 1;
					$where_mime_string = " AND SUBSTRING(t3.mime_type, 1, $tmp_len)='".substr($mime_typ, 0, $tmp_len)."'";
				}
				//Ganzer MIME Type vorhanden
				else
				{
					$where_mime_string = " AND t3.mime_type='$mime_typ'";
				}
			}

			if (!empty($file_date))
			{
				if ($file_date_type == 'before')
				{
					$file_date_sql = $file_date.'235959';
					$where_string .= " AND t3.save_time<='$file_date_sql'";
				}
				else
				{
					$file_date_sql = $file_date.'000000';
					$where_string .= " AND t3.save_time>='$file_date_sql'";
				}
			}
			$where_string .= " AND t3.anzeigen='1'";

			for($i = 0; $i < count($word_list_array); $i++)
			{
				//exakte uebereinstimmung in Dateien finden
				$sql = $cdb->select("SELECT DISTINCT(t1.file_id), t1.tagging FROM fom_search_word_file t1
									LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
									LEFT JOIN fom_files t3 ON t1.file_id=t3.file_id
									LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
									WHERE t2.word='$word_list_array[$i]' $where_string $where_mime_string");
				while($result = $cdb->fetch_array($sql))
				{
					$tmp_file_id_array[] = $result['file_id'];
					//Exakte uebereinstimmung
					if ($result['tagging'] == '0')
					{
						$this->count_result($result['file_id'], 'word');
					}
					else
					{
						$this->count_result($result['file_id'], 'word_tagging');
					}
				}

				//exakte uebereinstimmung in Links finden
				$sql = $cdb->select("SELECT DISTINCT(t1.link_id), t1.tagging FROM fom_search_word_link t1
									LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
									LEFT JOIN fom_link t3 ON t1.link_id=t3.link_id
									LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
									WHERE t2.word='$word_list_array[$i]' $where_string");
				while($result = $cdb->fetch_array($sql))
				{
					$tmp_link_id_array[] = $result['link_id'];
					//Exakte uebereinstimmung
					if ($result['tagging'] == '0')
					{
						$this->count_result($result['link_id'], 'word', 'link');
					}
					else
					{
						$this->count_result($result['link_id'], 'word_tagging', 'link');
					}
				}

				//Teilweise uebereinstimmung in Dateien und Links finden
				$word_sql = $cdb->select("SELECT word_id FROM fom_search_word WHERE word LIKE '$word_list_array[$i]%'");
				while($word_result = $cdb->fetch_array($word_sql))
				{
					//Teilweise uebereinstimmung Dateien
					$sql = $cdb->select("SELECT DISTINCT(t1.file_id), t1.tagging FROM fom_search_word_file t1
										LEFT JOIN fom_files t3 ON t1.file_id=t3.file_id
										LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
										WHERE t1.word_id=".$word_result['word_id'].' '.$where_string.' '. $where_mime_string);
					while($result = $cdb->fetch_array($sql))
					{
						//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
						if (!in_array($result['file_id'], $tmp_file_id_array))
						{
							if ($result['tagging'] == '0')
							{
								$this->count_result($result['file_id'], 'word_detail');
							}
							else
							{
								$this->count_result($result['file_id'], 'word_tagging_detail');
							}
						}
					}

					//Teilweise uebereinstimmung Links
					$sql = $cdb->select("SELECT DISTINCT(t1.link_id), t1.tagging FROM fom_search_word_link t1
										LEFT JOIN fom_link t3 ON t1.link_id=t3.link_id
										LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
										WHERE t1.word_id=".$word_result['word_id'].' '.$where_string);
					while($result = $cdb->fetch_array($sql))
					{
						//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
						if (!in_array($result['link_id'], $tmp_link_id_array))
						{
							if ($result['tagging'] == '0')
							{
								$this->count_result($result['link_id'], 'word_detail', 'link');
							}
							else
							{
								$this->count_result($result['link_id'], 'word_tagging_detail', 'link');
							}
						}
					}
				}
			}
		}

		/**
		 * Fuehrt eine Suche nach einem oder mehreren Woertern in Unterverzeichnissen durch
		 * @param array $word_list_array
		 * @param array $param
		 * @return void
		 */
		private function search_word_subfolder($word_list_array, $param)
		{
			$cdb = new MySql;

			$ob_folder_id = $param['fid_int'];
			$project_id = $param['pid_int'];
			$mime_typ = $param['mime_typ'];

			if (isset($param['file_date']))
			{
				$file_date = str_replace('-', '', $param['file_date']);
				$file_date_type = $param['file_date_type'];
			}
			else
			{
				$file_date = '';
				$file_date_type = '';
			}

			$tmp_file_id_array = array();
			$tmp_link_id_array = array();

			$f_sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$ob_folder_id AND anzeigen='1'");
			while($f_result = $cdb->fetch_array($f_sql))
			{
				$where_string = ' AND t4.folder_id='.$f_result['folder_id'];
				$where_mime_string = '';
				if ($project_id > 0)
				{
					$where_string .= ' AND t4.projekt_id='.$project_id;
				}
				if (!empty($mime_typ))
				{
					//nur ein teil des MIME Typs vorhanden
					if (substr($mime_typ, -1) == '%')
					{
						$tmp_len = strlen($mime_typ) - 1;
						$where_mime_string .= " AND SUBSTRING(t3.mime_type, 1, $tmp_len)='".substr($mime_typ, 0, $tmp_len)."'";
					}
					//Ganzer MIME Type vorhanden
					else
					{
						$where_mime_string .= " AND t3.mime_type='$mime_typ'";
					}
				}
				if (!empty($file_date))
				{
					if ($file_date_type == 'before')
					{
						$file_date_sql = $file_date.'235959';
						$where_string .= " AND t3.save_time<='$file_date_sql'";
					}
					else
					{
						$file_date_sql = $file_date.'000000';
						$where_string .= " AND t3.save_time>='$file_date_sql'";
					}
				}
				$where_string .= " AND t3.anzeigen='1'";

				for($i = 0; $i < count($word_list_array); $i++)
				{
					//exakte uebereinstimmung von Dateien
					$sql = $cdb->select("SELECT DISTINCT(t1.file_id), t1.tagging FROM fom_search_word_file t1
										LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
										LEFT JOIN fom_files t3 ON t1.file_id=t3.file_id
										LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
										WHERE t2.word='$word_list_array[$i]' $where_string $where_mime_string");
					while($result = $cdb->fetch_array($sql))
					{
						$tmp_file_id_array[] = $result['file_id'];
						//Exakte uebereinstimmung
						if ($result['tagging'] == '0')
						{
							$this->count_result($result['file_id'], 'word');
						}
						else
						{
							$this->count_result($result['file_id'], 'word_tagging');
						}
					}

					//exakte uebereinstimmung von Links
					$sql = $cdb->select("SELECT DISTINCT(t1.link_id), t1.tagging FROM fom_search_word_link t1
										LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
										LEFT JOIN fom_link t3 ON t1.link_id=t3.link_id
										LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
										WHERE t2.word='$word_list_array[$i]' $where_string");
					while($result = $cdb->fetch_array($sql))
					{
						$tmp_link_id_array[] = $result['link_id'];
						//Exakte uebereinstimmung
						if ($result['tagging'] == '0')
						{
							$this->count_result($result['link_id'], 'word', 'link');
						}
						else
						{
							$this->count_result($result['link_id'], 'word_tagging', 'link');
						}
					}

					//Teilweise uebereinstimmung
					$word_sql = $cdb->select("SELECT word_id FROM fom_search_word WHERE word LIKE '$word_list_array[$i]%'");
					while($word_result = $cdb->fetch_array($word_sql))
					{
						//Teilweise uebereinstimmung von Dateien
						$sql = $cdb->select("SELECT DISTINCT(t1.file_id), t1.tagging FROM fom_search_word_file t1
											LEFT JOIN fom_files t3 ON t1.file_id=t3.file_id
											LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
											WHERE t1.word_id=".$word_result['word_id'].' '.$where_string.' '.$where_mime_string);
						while($result = $cdb->fetch_array($sql))
						{
							//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
							if (!in_array($result['file_id'], $tmp_file_id_array))
							{
								if ($result['tagging'] == '0')
								{
									$this->count_result($result['file_id'], 'word_detail');
								}
								else
								{
									$this->count_result($result['file_id'], 'word_tagging_detail');
								}
							}
						}

						//Teilweise uebereinstimmung von Links
						$sql = $cdb->select("SELECT DISTINCT(t1.link_id), t1.tagging FROM fom_search_word_link t1
											LEFT JOIN fom_link t3 ON t1.link_id=t3.link_id
											LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
											WHERE t1.word_id=".$word_result['word_id'].' '.$where_string);
						while($result = $cdb->fetch_array($sql))
						{
							//Keine Doppeltzaehlung wenn die Exakte Suche Bereits ein Resultat hatte
							if (!in_array($result['link_id'], $tmp_link_id_array))
							{
								if ($result['tagging'] == '0')
								{
									$this->count_result($result['link_id'], 'word_detail', 'link');
								}
								else
								{
									$this->count_result($result['link_id'], 'word_tagging_detail', 'link');
								}
							}
						}
					}
				}
				//Selbstaufruf fuer weitere unterverzeichnisse
				$tmp_param = array('fid_int' => $f_result['folder_id'], 'pid_int' => $project_id, 'mime_typ' => $mime_typ);
				$this->search_word_subfolder($word_list_array, $tmp_param);
			}
		}

		/**
		 * Zaehlt die Suchergebnisse
		 * @param int $id
		 * @param string $typ, Index von $this->search_counter
		 * @return void
		 */
		private function count_result($id, $typ, $data_type = 'file')
		{
			$data_type = trim(strtolower($data_type));

			if ($data_type == 'file')
			{
				$id = 'FILE'.$id;
			}
			elseif ($data_type == 'link')
			{
				$id = 'LINK'.$id;
			}

			if (!isset($this->search_result[$id]))
			{
				$this->search_result[$id] = $this->search_counter[$typ];
			}
			else
			{
				$this->search_result[$id] += $this->search_counter[$typ];
			}
		}

		public function clear_cache()
		{
			$cdb = new MySql;

			$cdb->delete("DELETE FROM fom_search_cache WHERE search_time<'".date('YmdHis', time() - $this->setup_array['cache_time'])."'");
		}

		/**
		 * Kleine Optische Spielerei fuer die Suchuebersicht
		 * @param int $relevance
		 * @return string
		 */
		public function get_relevance_chart($relevance)
		{
			//Eigentlich garkeine Relevanz die Datei wird nur angezeigt weil es SubDateien gibt
			if ($relevance == -1)
			{
				return '<span class="relevance_chart_empty">'.get_img('_spacer.gif', '', '', 'image', 0, '', '', 50, 5).'</span>';
			}
			else
			{
				$return_string = '';
				$max = intval($this->search_counter['file_name'] + $this->search_counter['word'] + 10);

				$ref = round(50 * $relevance / $max, 0);

				if ($ref > 50)
				{
					$ref = 50;
				}

				if ($ref > 0)
				{
					return '<span class="relevance_chart">'.get_img('_spacer.gif', '', '', 'image', 0, '', '', $ref, 5).'</span>';
				}
				else
				{
					return get_img('_spacer.gif', '', '', 'image', 0, '', '', 1, 3);
				}
			}
		}

		/**
		 * Sortiert das Sucharray nach PRIMARY und SUB Datei
		 * @return array
		 */
		private function order_search_array()
		{
			$cdb = new MySql;

			$tmp_result_array = $this->search_result;
			$new_result_array = array();

			foreach($tmp_result_array as $id => $relevanz)
			{
				//Dateidaten ausgeben
				if (strpos($id, 'FILE') !== false)
				{
					$file_id = intval(str_replace('FILE', '', $id));

					$sql = $cdb->select('SELECT file_type FROM fom_files WHERE file_id='.$file_id);
					while ($result = $cdb->fetch_array($sql))
					{
						//PRIMAEY Datei
						if ($result['file_type'] == 'PRIMARY')
						{
							if (isset($new_result_array['FILE'.$file_id]['relevanz']))
							{
								$new_result_array['FILE'.$file_id]['relevanz'] = $relevanz;
							}
							else
							{
								$new_result_array['FILE'.$file_id] = array('relevanz' => $relevanz, 'type' => 'file', 'id' => $file_id);
							}
						}
						//SubDateien
						elseif ($result['file_type'] == 'SUB')
						{
							$pri_sql = $cdb->select('SELECT file_id FROM fom_sub_files WHERE subfile_id='.$file_id);
							$pri_result = $cdb->fetch_array($pri_sql);

							//PRIMAY Datei in Array aufnehmen und mit einer Relevanz von -1 versehen
							if (!isset($new_result_array['FILE'.$pri_result['file_id']]['relevanz']))
							{
								$new_result_array['FILE'.$pri_result['file_id']] = array('relevanz' => -1, 'type' => 'file', 'id' => $pri_result['file_id']);
							}

							if (isset($new_result_array['FILE'.$pri_result['file_id']]['sub_file']))
							{
								$new_result_array['FILE'.$pri_result['file_id']]['sub_file'] += array($file_id => $relevanz, 'type' => 'file', 'id' => $pri_result['file_id']);
							}
							else
							{
								$new_result_array['FILE'.$pri_result['file_id']]['sub_file'] = array($file_id => $relevanz, 'type' => 'file', 'id' => $pri_result['file_id']);
							}
						}
					}
				}
				//Link
				elseif (strpos($id, 'LINK') !== false)
				{
					$link_id = intval(str_replace('LINK', '', $id));

					if (isset($new_result_array['LINK'.$link_id]['relevanz']))
					{
						$new_result_array['LINK'.$link_id]['relevanz'] = $relevanz;
					}
					else
					{
						$new_result_array['LINK'.$link_id] = array('relevanz' => $relevanz, 'type' => 'link', 'id' => $link_id);
					}
				}
			}

			return $new_result_array;
		}

		public function get_mime_types($folder_id, $project_id = 0, $subfolder = true)
		{
			$this->search_mime_types($folder_id, $project_id = 0, $subfolder = true);

			if (!empty($this->mime_array))
			{
				array_multisort($this->tmp_array['mime_sort'], SORT_ASC, SORT_STRING, $this->mime_array);
				return $this->mime_array;
			}
			else
			{
				return array();
			}
		}
		private function search_mime_types($folder_id, $project_id = 0, $subfolder = true)
		{
			$cdb = new MySql();
			$gt = new Tree();

			if ($folder_id > 0)
			{
				$where_dok_typ = 'WHERE t1.folder_id='.$folder_id;
			}
			elseif($project_id > 0)
			{
				$where_dok_typ = 'WHERE t2.projekt_id='.$project_id;
			}
			else
			{
				$where_dok_typ = '';
			}
			$sql = $cdb->select("SELECT t1.save_name, t1.mime_type FROM fom_files t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								$where_dok_typ GROUP BY t1.mime_type");
			while($result = $cdb->fetch_array($sql))
			{
				$tmp_typ = $gt->GetFileType('', $result['mime_type'], 'array');

				if (!empty($tmp_typ['mime']))
				{
					$mime_hash = md5($tmp_typ['mime']);
					if (!isset($this->mime_array[$mime_hash]))
					{
						$ex = strtolower($gt->GetFileExtension($result['save_name']));

						$this->tmp_array['mime_sort'][$mime_hash] = $ex;
						$this->mime_array[$mime_hash] = array('mime' => $tmp_typ['mime'], 'name' => $tmp_typ['name'], 'extension' => $ex);
					}
				}
			}

			if ($folder_id > 0)
			{
				$sql = $cdb->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$folder_id AND anzeigen='1'");
				while ($result = $cdb->fetch_array($sql))
				{
					$this->get_mime_types($result['folder_id'], 0, $subfolder);
				}
			}
		}

		public function get_az_signs($folder_id = 0, $project_id = 0, $document_type_id = 0)
		{
			$cdb = new MySql;

			$return_array = array();
			$compare_array = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

			$where_string = "WHERE t1.tagging='1'";

			if ($folder_id > 0)
			{
				$where_string .= ' AND t4.folder_id='.$folder_id;
			}
			if ($project_id > 0)
			{
				$where_string .= ' AND t4.projekt_id='.$project_id;
			}
			if ($document_type_id > 0)
			{
				$where_string .= ' AND t5.document_type_id='.$document_type_id;
			}

			$where_string .= " AND t3.anzeigen='1'";

			$sql = $cdb->select("SELECT LEFT(t2.word, 1) AS sign FROM fom_search_word_file t1
								LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
								LEFT JOIN fom_files t3 ON t1.file_id=t3.file_id
								LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
								LEFT JOIN fom_document_type_file t5 ON t1.file_id=t5.file_id
								$where_string GROUP BY LEFT(t2.word, 1)");
			while($result = $cdb->fetch_array($sql))
			{
				if (in_array($result['sign'], $compare_array))
				{
					$return_array[] = $result['sign'];
				}
				elseif($result['sign'] == 'ä')
				{
					$return_array[] = 'a';
				}
				elseif($result['sign'] == 'ü')
				{
					$return_array[] = 'u';
				}
				elseif($result['sign'] == 'ö')
				{
					$return_array[] = 'o';
				}
				elseif($result['sign'] == 'ß')
				{
					$return_array[] = 's';
				}
				else
				{
					$return_array[] = '@';
				}
			}

			$return_array = array_unique($return_array);
			sort($return_array);
			return $return_array;
		}

		/**
		 * Erstellt eine Liste mit Suchbegriffen zu einem Zeichen bzw. fuer alle
		 * @param string $sign
		 * @param int $folder_id
		 * @param int $project_id
		 * @return array
		 */
		public function get_az_words($sign = '', $folder_id = 0, $project_id = 0, $document_type_id = 0)
		{
			$cdb = new MySql;

			$return_array = array();
			$compare_array = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

			$sign = htmlentities(utf8_decode($sign), ENT_QUOTES);

			$where_string = "WHERE t1.tagging='1'";

			if (!empty($sign))
			{
				$where_string .= " AND LEFT(t2.word, 1)='$sign'";
			}
			if ($folder_id > 0)
			{
				$where_string .= ' AND t4.folder_id='.$folder_id;
			}
			if ($project_id > 0)
			{
				$where_string .= ' AND t4.projekt_id='.$project_id;
			}
			if ($document_type_id > 0)
			{
				$where_string .= ' AND t5.document_type_id='.$document_type_id;
			}

			$where_string .= " AND t3.anzeigen='1'";

			$sql = $cdb->select("SELECT DISTINCT(t2.word) FROM fom_search_word_file t1
								LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
								LEFT JOIN fom_files t3 ON t1.file_id=t3.file_id
								LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
								LEFT JOIN fom_document_type_file t5 ON t1.file_id=t5.file_id
								$where_string");
			while($result = $cdb->fetch_array($sql))
			{
				$tmp_sign = strtolower(substr(html_entity_decode($result['word'], ENT_QUOTES), 0, 1));

				if (in_array($tmp_sign, $compare_array))
				{
					$return_array[$tmp_sign][] = $result['word'];
				}
				elseif($tmp_sign == 'ä')
				{
					$return_array['a'][] = $result['word'];
				}
				elseif($tmp_sign == 'ü')
				{
					$return_array['u'][] = $result['word'];
				}
				elseif($tmp_sign == 'ö')
				{
					$return_array['o'][] = $result['word'];
				}
				elseif($tmp_sign == 'ß')
				{
					$return_array['s'][] = $result['word'];
				}
				else
				{
					$return_array['@'][] = $result['word'];
				}
			}
			return $return_array;
		}

		/**
		 * Erstellt ein array mit Dateiinformaltionen zu einem Suchbegriff
		 *
		 * @param string $word
		 * @param boole $only_tagging
		 * @param int $folder_id
		 * @param int $project_id
		 * @return array
		 */
		public function get_az_files($word, $only_tagging = true, $folder_id = 0, $project_id = 0, $document_type_id = 0)
		{
			$cdb = new MySql;
			$dl = new Download;

			$return_array = array();
			$word = htmlentities(utf8_decode($word), ENT_QUOTES);

			//Suche nach nur einem Zeichen
			if (strlen($word) == 1)
			{
				$where_string = "WHERE LEFT(t2.word, 1)='$word'";
			}
			else
			{
				$where_string = "WHERE t2.word='$word'";
			}

			if ($folder_id > 0)
			{
				$where_string .= ' AND t4.folder_id='.$folder_id;
			}
			if ($project_id > 0)
			{
				$where_string .= ' AND t4.projekt_id='.$project_id;
			}
			if ($only_tagging == true)
			{
				$where_string .= " AND t1.tagging='1'";
			}
			if ($document_type_id > 0)
			{
				$where_string .= ' AND t5.document_type_id='.$document_type_id;
			}
			$where_string .= " AND t3.anzeigen='1'";

			//exakte uebereinstimmung
			$sql = $cdb->select("SELECT DISTINCT(t1.file_id), t3.org_name, t3.save_name, t3.mime_type, t3.save_time, t3.bemerkungen FROM fom_search_word_file t1
								LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
								LEFT JOIN fom_files t3 ON t1.file_id=t3.file_id
								LEFT JOIN fom_folder t4 ON t3.folder_id=t4.folder_id
								LEFT JOIN fom_document_type_file t5 ON t1.file_id=t5.file_id
								$where_string");
			while($result = $cdb->fetch_array($sql))
			{
				//Downloadticket erstellen
				$dl_result = $dl->insert_download($result['file_id'], date('YmdHis', time() + 3600));

				if ($dl_result['result'] == true)
				{
					$download_pfad = $dl_result['download'];
				}
				else
				{
					$download_pfad = '';
				}

				$return_array[] = array('file_id'	=> $result['file_id'],
										'file_name'	=> $result['org_name'],
										'save_name'	=> $result['save_name'],
										'mime_type'	=> $result['mime_type'],
										'save_time'	=> $result['save_time'],
										'comment'	=> $result['bemerkungen'],
										'download'	=> $download_pfad);
			}
			return $return_array;
		}
	}
?>