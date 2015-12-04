<?php
	class LinkJobs
	{
		private $error_array = array();
		private $tmp_data_array = array();

		/**
		 * Traegt einen neuen Externen Link ein
		 * @param int $folder_id
		 * @param int $project_id
		 * @param string $link
		 * @param string $protocol
		 * @param string $link_name
		 * @param string $tagging
		 * @param string $description
		 * @return boole
		 */
		public function insert_link($folder_id, $project_id, $link, $protocol, $link_name, $tagging, $description)
		{
			$cdb = new MySql();
			$mn = new MailNotification();

			$this->tmp_data_array['start_time'] = time();

			if (strtolower(substr($link, 0, strlen($protocol))) == strtolower($protocol))
			{
				$abs_link = $link;
			}
			else
			{
				$abs_link = $protocol.$link;
			}

			$md5 = md5($link);
			$file_server_id = $this->get_fileserver_id($project_id);

			//Link in die DB eintragen
			if ($cdb->insert("INSERT INTO fom_link (folder_id, file_server_id, user_id, name, link, md5_link, save_time, bemerkungen, tagging) VALUES ($folder_id, $file_server_id, ".USER_ID.", '$link_name', '$abs_link', '$md5', '".date('YmdHis')."', '$description' , '$tagging')"))
			{
				$link_id = $cdb->get_last_insert_id();

				$mn->log_trigger_events($project_id, $link_id, 'link_add');

				$this->tmp_data_array['last_insert_link_id'] = $link_id;

				//Tagging)
				if (!empty($tagging))
				{
					$this->insert_link_tagging($tagging, $link_id);
				}
				$this->insert_index_job($link_id, $abs_link);
				return true;
			}
			else
			{
				$this->error_array[] = get_text('error','return');//An error has occurred!
			}
			return false;
		}

		public function get_last_insert_link_id()
		{
			if (isset($this->tmp_data_array['last_insert_link_id']))
			{
				return $this->tmp_data_array['last_insert_link_id'];
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Traegt zu einem Link A-Z Register Keywords ein
		 * @param array $sign_array
		 * @param array $word_array
		 * @param int $linkid_int
		 * @return boole
		 */
		public function insert_az_link_register_keys($sign_array, $word_array, $linkid_int)
		{
			$cdb = new MySql();

			//sollte eingendlich immer gleich sein
			$count = max(count($sign_array), count($word_array));
			$insert_count = 0;

			if ($count > 0)
			{
				for ($i = 0; $i < $count; $i++)
				{
					//Anfangszeichen und Suchwort vorhanden
					if (isset($sign_array[$i]) and isset($word_array[$i]) and !empty($word_array[$i]))
					{
						$word = strtolower($word_array[$i]);
						$sign = strtolower($sign_array[$i]);

						$word_id = 0;
						$sql = $cdb->select("SELECT word_id FROM fom_search_word WHERE word='$word'");
						$result = $cdb->fetch_array($sql);

						if (isset($result['word_id']) and $result['word_id'] > 0)
						{
							$word_id = $result['word_id'];
						}
						else
						{
							if ($cdb->insert("INSERT INTO fom_search_word (word) VALUES ('$word')"))
							{
								$word_id = $cdb->get_last_insert_id();
							}
						}

						//wort gefunden
						if ($word_id > 0)
						{
							//anfangsbuchstaben verwenden
							if ($sign == 'empty')
							{
								$sign = substr($word, 0, 1);

							}
							elseif (empty($sign))
							{
								$sign = 0;
							}

							if ($cdb->insert("INSERT INTO fom_search_word_az_link (word_id, link_id, sign) VALUES ($word_id, $linkid_int, '$sign')"))
							{
								$insert_count++;
							}
						}
					}
				}
			}

			if ($insert_count > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function edit_link($link_id, $link, $protocol, $link_name, $tagging, $description)
		{
			$cdb = new MySql();
			$mn = new MailNotification();

			if (strtolower(substr($link, 0, strlen($protocol))) == strtolower($protocol))
			{
				$abs_link = $link;
			}
			else
			{
				$abs_link = $protocol.$link;
			}

			$md5 = md5($link);

			//Link in die DB eintragen
			if ($cdb->update("UPDATE fom_link SET user_id=".USER_ID.",
													name='$link_name',
													link='$abs_link',
													md5_link='$md5',
													save_time='".date('YmdHis')."',
													bemerkungen='$description',
													tagging='$tagging'
													WHERE link_id=$link_id"))
			{
				$mn->log_trigger_events(0, $link_id, 'link_edit');

				//Tagging)
				if (!empty($tagging))
				{
					$this->del_link_tagging($link_id);
					$this->insert_link_tagging($tagging, $link_id);
				}
				return true;
			}
			else
			{
				$this->error_array[] = get_text('error','return');//An error has occurred!
			}
			return false;
		}

		public function refresh_internal_linkname($file_id, $file_name)
		{
			$cdb = new MySql();

			$sql = $cdb->update("UPDATE fom_link SET name='$file_name' WHERE file_id=$file_id");
		}

		public function get_error()
		{
			return $this->error_array;
		}

		/**
		 * Entfernt alle Suchberiffe zu einem Link aus dem Index
		 * @param $link_id
		 * @return unknown_type
		 */
		private function del_link_tagging($link_id)
		{
			//FIXME
		}

		/**
		 * Suchbegriffe zu einem Link in den Index eintragen
		 * @param string $tagging
		 * @param int $link_id
		 * @return void
		 */
		private function insert_link_tagging($tagging, $link_id)
		{
			$rl = new ReadLink();

			$tagging = html_entity_decode($tagging, ENT_QUOTES);
			$tagging = strtolower($tagging);
			$word_array = $rl->clear_string($tagging);

			$rl->insert_link_word_array($word_array, $link_id);
		}

		private function insert_index_job($link_id, $link)
		{
			$cdb = new MySql();
			//nur externe Links
			if (!empty($link))
			{
				if (function_exists('parse_url'))
				{
					$link_data_array = parse_url($link);

					//FIXME FTP Fehlt noch
					if (isset($link_data_array['scheme']) and (strtolower($link_data_array['scheme']) == 'http' or strtolower($link_data_array['scheme']) == 'https') and isset($link_data_array['host']) and !empty($link_data_array['host']))
					{
						$url_is_file = false;
						$is_readable = true;
						$ex_string = 'txt';
						//unterverzeichnisse bzw. Dateien angegeben
						if (isset($link_data_array['path']) and !empty($link_data_array['path']))
						{
							$gt = new Tree;
							$ex_string = strtolower($gt->GetFileExtension($link_data_array['path']));

							if (!empty($ex_string))
							{
								$url_is_file = true;
								$readable_ex_array = array('pdf', 'doc', 'xls', 'odt', 'ods', 'xml', 'txt', 'htm', 'html', 'xhtml', 'php', 'css');

								//kein Lesbarer Dateityp
								if (!in_array($ex_string, $readable_ex_array))
								{
									$is_readable = false;
								}
							}
						}

						if ($is_readable)
						{
							$tmp_file_name = md5(uniqid()).'.'.$ex_string;

							if (function_exists('curl_init'))
							{
								$ch = curl_init($link);
								$fh = fopen(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_file_name, "w");

								curl_setopt($ch, CURLOPT_FILE, $fh);
								curl_setopt($ch, CURLOPT_HEADER, 0);

								curl_exec($ch);
								curl_close($ch);
								fclose($fh);

								if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$tmp_file_name))
								{
									if ($cdb->insert("INSERT INTO fom_file_job_index (link_id, save_name, last_page, save_time) VALUES ($link_id, '$tmp_file_name', 0, '".date('YmdHis')."')"))
									{
										$last_insert_id = $cdb->get_last_insert_id();

										if ($last_insert_id > 0)
										{
											$current_time = time();
											if ($current_time - $this->tmp_data_array['start_time'] < 10)
											{
												$this->create_link_index($last_insert_id);

											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		private function create_link_index($job_id)
		{
			$rf = new ReadFile();
			$rf->read_file($job_id);
		}

		public function get_fileserver_id($project_id)
		{
			$fj = new FileJobs();

			return $fj->get_fileserver_id($project_id);
		}

		/**
		 * Gibt die zugehoerige Projekt-ID zu einem Link oder Verzeichnis aus
		 * @param int $file_id
		 * @param int $folder_id
		 * @return int
		 */
		public function get_project_id($link_id = 0, $folder_id = 0)
		{
			$cdb = new MySql;

			if ($link_id > 0)
			{
				$sql = $cdb->select('SELECT t1.folder_id, t2.projekt_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.link_id='.$link_id);
				$result = $cdb->fetch_array($sql);

				if ($result['projekt_id'] > 0)
				{
					return $result['projekt_id'];
				}
				else
				{
					return $this->get_project_id(0, $result['folder_id']);
				}
			}
			elseif($folder_id > 0)
			{
				$sql = $cdb->select('SELECT projekt_id FROM fom_folder WHERE folder_id='.$folder_id);
				$result = $cdb->fetch_array($sql);

				if ($result['projekt_id'] > 0)
				{
					return $result['projekt_id'];
				}
				else
				{
					return 0;
				}
			}
		}
	}
?>