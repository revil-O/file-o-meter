<?php
	/**
	 * Mail Notification class
	 *
	 * E-Mail Benachrichtigungsklasse
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de>
	 * @copyright Copyright (C) 2012  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * Mail Notification class for the projects
	 * @package file-o-meter
	 * @subpackage class
	 */
	class MailNotification
	{
		public $trigger_array = array();
		public $trigger_past_array = array();
		public $all_triggerevents_array = array();
		public $no_triggerevents_array = array();

		public function __construct()
		{
			//Default Trigger array
			//enthaelt alle triggerevents die verarbeitet werden
			$this->trigger_array = array('folder_add'		=> get_text(150, 'return'), //Verzeichnis anlegen
										'folder_edit'		=> get_text(162, 'return'), //Verzeichnis bearbeiten
										'folder_copy'		=> get_text(229, 'return'), //Verzeichnis kopieren
										'folder_move'		=> get_text(230, 'return'), //Verzeichnis verschieben
										'folder_del'		=> get_text(159, 'return'), //Verzeichnis loeschen
										'file_add'			=> get_text(152, 'return'), //Datei hinzufuegen
										'file_edit'			=> get_text(161, 'return'), //Datei bearbeiten
										'file_copy'			=> get_text(226, 'return'), //Datei kopieren
										'file_move'			=> get_text(227, 'return'), //Datei verschieben
										'file_add_version'	=> get_text(133, 'return'), //Version anlegen
										'file_checkin'		=> get_text(172, 'return'), //Datei einchecken
										'file_checkout'		=> get_text(171, 'return'), //Datei auschecken
										'file_del'			=> get_text(157, 'return'), //Datei loeschen
										'link_add'			=> get_text(288, 'return'), //Link hinzufuegen
										'link_edit'			=> get_text(297, 'return'), //Link bearbeiten
										'link_del'			=> get_text(295, 'return')); //Link loeschen

			$this->trigger_past_array = array('folder_add'		=> get_text(407, 'return'), //Verzeichnis angelegt
											'folder_edit'		=> get_text(408, 'return'), //Verzeichnis bearbeitet
											'folder_copy'		=> get_text(409, 'return'), //Verzeichnis kopiert
											'folder_move'		=> get_text(410, 'return'), //Verzeichnis verschoben
											'folder_del'		=> get_text(411, 'return'), //Verzeichnis geloescht
											'file_add'			=> get_text(412, 'return'), //Datei hinzugefuegt
											'file_edit'			=> get_text(413, 'return'), //Datei bearbeitet
											'file_copy'			=> get_text(414, 'return'), //Datei kopiert
											'file_move'			=> get_text(415, 'return'), //Datei verschoben
											'file_add_version'	=> get_text(416, 'return'), //Version angelegt
											'file_checkin'		=> get_text(417, 'return'), //Datei eingecheckt
											'file_checkout'		=> get_text(418, 'return'), //Datei ausgecheckt
											'file_del'			=> get_text(419, 'return'), //Datei geloescht
											'link_add'			=> get_text(420, 'return'), //Link hinzugefuegt
											'link_edit'			=> get_text(421, 'return'), //Link bearbeitet
											'link_del'			=> get_text(422, 'return')); //Link geloescht


			//Erstellt ein array in dem alle Triggerevents aktiv sind
			foreach ($this->trigger_array as $index => $txt)
			{
				$this->all_triggerevents_array[$index] = 1;
				$this->no_triggerevents_array[$index] = 0;
			}
		}

		/**
		 * erstellt ein array mit allen zu loggenden events
		 * @param int $project_id
		 * @param int $user_id
		 * @return array
		 */
		public function get_trigger_events($project_id, $user_id = 0)
		{
			$cdb = new MySql();

			if ($user_id == 0)
			{
				$user_id = USER_ID;
			}

			$sql = $cdb->select('SELECT user_id, mn_setup FROM fom_mn_setup WHERE user_id='.$user_id.' AND projekt_id='.$project_id);
			$result = $cdb->fetch_array($sql);

			if (isset($result['user_id']) and $result['user_id'] > 0)
			{
				$trigger_array = @unserialize($result['mn_setup']);

				if (is_array($trigger_array))
				{
					return $trigger_array;
				}
				else
				{
					return $this->no_triggerevents_array;
				}
			}
			else
			{
				return $this->no_triggerevents_array;
			}
		}

		/**
		 * Speichert fuer einen User in einem Projekt alle moeglichen Trigger
		 * @param int $project_id
		 * @param int $user_id
		 */
		public function insert_all_trigger_events($project_id, $user_id)
		{
			$cdb = new MySql();

			$sql = $cdb->select('SELECT user_id FROM fom_mn_setup WHERE user_id='.$user_id.' AND projekt_id='.$project_id);
			$result = $cdb->fetch_array($sql);

			//Noch kein Eintrag vorhanden
			if (!isset($result['user_id']) or empty($result['user_id']))
			{
				if ($cdb->insert("INSERT INTO fom_mn_setup (user_id, projekt_id, mn_setup) VALUES ($user_id, $project_id, '".serialize($this->all_triggerevents_array)."')"))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Datensatz bereits vorhanden
			else
			{
				return true;
			}
		}

		/**
		 * Entfernt ein Trigger Event
		 * @param int $project_id
		 * @param int $user_id
		 * @return boole
		 */
		public function delete_trigger_events($project_id, $user_id)
		{
			$cdb = new MySql();

			if ($cdb->delete('DELETE FROM fom_mn_setup WHERE user_id='.$user_id.' AND projekt_id='.$project_id))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Prueft ob zum uebergebenen Projekt und Event ein Trigger vorhanden ist
		 * @param int $project_id
		 * @param string $event_string
		 * @return boole
		 */
		public function chk_event($project_id, $event_string)
		{
			$cdb = new MySql();

			$sql = $cdb->select('SELECT user_id FROM fom_mn_setup WHERE projekt_id='.$project_id);
			while ($result = $cdb->fetch_array($sql))
			{
				$trigger_event_array = $this->get_trigger_events($project_id, $result['user_id']);

				if (isset($trigger_event_array[$event_string]) and $trigger_event_array[$event_string] == 1)
				{
					return true;
				}
			}

			return false;
		}

		/**
		 * Traegt aenderung der Trigger Events ein
		 * @param int $project_id
		 * @param int $trigger_array
		 * @return boole
		 */
		public function update_trigger_events($project_id, $trigger_array)
		{
			$cdb = new MySql();

			$sql = $cdb->select('SELECT user_id, mn_setup FROM fom_mn_setup WHERE user_id='.USER_ID.' AND projekt_id='.$project_id);
			$result = $cdb->fetch_array($sql);

			if (isset($result['user_id']) and $result['user_id'] > 0)
			{
				if ($cdb->update("UPDATE fom_mn_setup SET mn_setup='".serialize($trigger_array)."' WHERE user_id=".USER_ID.' AND projekt_id='.$project_id))
				{
					return true;
				}
			}
			else
			{
				if ($cdb->insert("INSERT INTO fom_mn_setup (user_id, projekt_id, mn_setup) VALUES (".USER_ID.", $project_id, '".serialize($trigger_array)."')"))
				{
					return true;
				}
			}
			return false;
		}

		/**
		 * Speicht die Events in die log Tabelle
		 * @param int $project_id
		 * @param int $id
		 * @param string $event_string
		 * @param string $org_name
		 * @return void
		 */
		public function log_trigger_events($project_id, $id, $event_string, $org_name = '')
		{
			if ($project_id == 0)
			{
				$project_id = $this->get_project_id($id, $event_string);
			}

			if ($project_id > 0)
			{
				if ($this->chk_event($project_id, $event_string) == true)
				{
					$cdb = new MySql();

					$cdb->insert("INSERT INTO fom_mn_log (id, user_id, org_name, event, event_time) VALUES ($id, ".USER_ID.", '$org_name', '$event_string', '".date('YmdHis')."')");
				}
			}
		}

		/**
		 * Gibt die Projekt ID zu einer Datei, Link oder Verzeichniss zurueck
		 * @param int $id
		 * @param string $event_string
		 * @return int
		 */
		private function get_project_id($id, $event_string)
		{
			$cdb = new MySql();

			if (substr($event_string, 0, 4) == 'file')
			{
				$sql = $cdb->select('SELECT t2.projekt_id FROM fom_files t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.file_id='.$id);
				$result = $cdb->fetch_array($sql);

				if (isset($result['projekt_id']) and $result['projekt_id'] > 0)
				{
					return $result['projekt_id'];
				}
				else
				{
					return 0;
				}
			}
			elseif (substr($event_string, 0, 4) == 'link')
			{
				$sql = $cdb->select('SELECT t2.projekt_id FROM fom_link t1
									LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
									WHERE t1.link_id='.$id);
				$result = $cdb->fetch_array($sql);

				if (isset($result['projekt_id']) and $result['projekt_id'] > 0)
				{
					return $result['projekt_id'];
				}
				else
				{
					return 0;
				}
			}
			elseif (substr($event_string, 0, 4) == 'fold')
			{
				$sql = $cdb->select('SELECT projekt_id FROM fom_folder t2 WHERE folder_id='.$id);
				$result = $cdb->fetch_array($sql);

				if (isset($result['projekt_id']) and $result['projekt_id'] > 0)
				{
					return $result['projekt_id'];
				}
				else
				{
					return 0;
				}
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Versendet die E-Mailbenachrichtigungen
		 * @return void
		 */
		public function send_mn()
		{
			$cdb	= new MySql();
			$mail	= new Mailer();
			$kal	= new Kalender();
			$gt		= new Tree();
			$ac		= new Access();

			//tmp Tabelle als zwischenspeicher erstellen
			if ($cdb->query("CREATE TEMPORARY TABLE fom_mn_tmp (
						pfad varchar(255) default NULL,
						org_name varchar(255) default NULL,
						event varchar(50) default NULL,
						event_time varchar(14) default NULL,
						user_name varchar(255) default NULL,
						projekt_id int(10) unsigned default NULL)") !== false)
			{

				//Speichert alle log IDs fuer das spaetere loeschen
				$log_id_array = array();

				//alle Verzeichnislogs auslesen
				$sql = $cdb->select("SELECT t1.log_id, t1.org_name, t1.event, t1.event_time, t2.folder_id, t2.projekt_id, t3.vorname, t3.nachname FROM fom_mn_log t1
									LEFT JOIN fom_folder t2 ON t1.id=t2.folder_id
									LEFT JOIN fom_user t3 ON t1.user_id=t3.user_id
									WHERE LEFT(t1.event, 4)='fold'");
				while ($result = $cdb->fetch_array($sql))
				{
					if (isset($result['folder_id']) and !empty($result['folder_id']))
					{
						$log_id_array[] = $result['log_id'];
						$tmp_pfad = $gt->get_folder_pfad_from_folder($result['folder_id']);

						$cdb->insert("INSERT INTO fom_mn_tmp (pfad, org_name, event, event_time, user_name, projekt_id) VALUES ('".$tmp_pfad."', '".$result['org_name']."', '".$result['event']."', '".$result['event_time']."', '".$result['nachname'].', '.$result['vorname']."', ".$result['projekt_id'].")");
					}
				}

				//alle Dateilogs auslesen
				$sql = $cdb->select("SELECT t1.log_id, t1.org_name, t1.event, t1.event_time, t2.file_id, t3.vorname, t3.nachname, t4.projekt_id FROM fom_mn_log t1
									LEFT JOIN fom_files t2 ON t1.id=t2.file_id
									LEFT JOIN fom_user t3 ON t1.user_id=t3.user_id
									LEFT JOIN fom_folder t4 ON t2.folder_id=t4.folder_id
									WHERE LEFT(t1.event, 4)='file'");
				while ($result = $cdb->fetch_array($sql))
				{
					if (isset($result['file_id']) and !empty($result['file_id']))
					{
						$log_id_array[] = $result['log_id'];
						$tmp_pfad = $gt->get_folder_pfad_from_file($result['file_id']);

						$cdb->insert("INSERT INTO fom_mn_tmp (pfad, org_name, event, event_time, user_name, projekt_id) VALUES ('".$tmp_pfad."', '".$result['org_name']."', '".$result['event']."', '".$result['event_time']."', '".$result['nachname'].', '.$result['vorname']."', ".$result['projekt_id'].")");
					}
				}

				//alle Linklogs auslesen
				$sql = $cdb->select("SELECT t1.log_id, t1.org_name, t1.event, t1.event_time, t2.link_id, t3.vorname, t3.nachname, t4.projekt_id FROM fom_mn_log t1
									LEFT JOIN fom_link t2 ON t1.id=t2.link_id
									LEFT JOIN fom_user t3 ON t1.user_id=t3.user_id
									LEFT JOIN fom_folder t4 ON t2.folder_id=t4.folder_id
									WHERE LEFT(t1.event, 4)='link'");
				while ($result = $cdb->fetch_array($sql))
				{
					if (isset($result['link_id']) and !empty($result['link_id']))
					{
						$log_id_array[] = $result['log_id'];

						$tmp_pfad = $gt->get_folder_pfad_from_link($result['link_id']);

						$cdb->insert("INSERT INTO fom_mn_tmp (pfad, org_name, event, event_time, user_name, projekt_id) VALUES ('".$tmp_pfad."', '".$result['org_name']."', '".$result['event']."', '".$result['event_time']."', '".$result['nachname'].', '.$result['vorname']."', ".$result['projekt_id'].")");
					}
				}

				//benachrichtigungsarray
				$mn_user_array = array();

				//alle User mit E-Mailbenachrichtigungen
				$mn_sql = $cdb->select("SELECT t1.* FROM fom_mn_setup t1
										LEFT JOIN fom_projekte t2 ON t1.projekt_id=t2.projekt_id
										LEFT JOIN fom_user t3 ON t1.user_id=t3.user_id
										WHERE t3.login_aktiv='1' ORDER BY t2.projekt_name ASC");
				while ($mn_result = $cdb->fetch_array($mn_sql))
				{
					$mn_exists = false;
					//Alle Usergruppen des Users Suchen
					$mn_g_sql = $cdb->select('SELECT usergroup_id FROM fom_user_membership WHERE user_id='.$mn_result['user_id']);
					while ($mn_g_result = $cdb->fetch_array($mn_g_sql))
					{
						if ($ac->mn_exists($mn_result['projekt_id'], $mn_g_result['usergroup_id']))
						{
							$mn_exists = true;
							break;
						}
					}

					if ($mn_exists)
					{
						$mn_setup = @unserialize($mn_result['mn_setup']);

						if (is_array($mn_setup))
						{
							//alle Logevent des Projektes
							$sql = $cdb->select('SELECT * FROM fom_mn_tmp WHERE projekt_id='.$mn_result['projekt_id'].' ORDER BY pfad ASC, event_time ASC');
							while ($result = $cdb->fetch_array($sql))
							{
								//logevent aktiv
								if (isset($mn_setup[$result['event']]) and $mn_setup[$result['event']] == 1)
								{
									$mn_user_array[$mn_result['user_id']][] = $result;
								}
							}
						}
					}
				}

				foreach ($mn_user_array as $user_id => $mn_array)
				{
					$body = '';
					$sql = $cdb->select('SELECT email FROM fom_user WHERE user_id='.$user_id);
					$result = $cdb->fetch_array($sql);

					if (!empty($result['email']) and is_array($mn_array))
					{
						for ($i = 0; $i < count($mn_array); $i++)
						{
							//pfad und eventtyp vorhanden
							if (!empty($mn_array[$i]['pfad']) and isset($this->trigger_past_array[$mn_array[$i]['event']]))
							{
								//emailtyp html
								if ($GLOBALS['setup_array']['mail']['mailtyp'] == 'html')
								{
									//verzeichnishintergrundfarbe
									if (substr($mn_array[$i]['event'], 0, 4) == 'fold')
									{
										$body .= '<tr style="background-color:#BFD870;">';
									}
									//Dateihintergrundfarbe
									elseif (substr($mn_array[$i]['event'], 0, 4) == 'file')
									{
										$body .= '<tr style="background-color:#F0F0F0;">';
									}
									//Linkhintergrundfarbe
									else
									{
										$body .= '<tr>';
									}

									//Pfad
									$body .= '<td>'.$mn_array[$i]['pfad'];
									//originalname bei bearbeitungen
									if (!empty($mn_array[$i]['org_name']))
									{
										$body .= ' ('.$mn_array[$i]['org_name'].')';
									}
									$body .= '</td>';
									//eventtyp
									$body .= '<td>'.$this->trigger_past_array[$mn_array[$i]['event']].'</td>';
									//bearbeitungszeit
									$body .= '<td>'.$kal->win_to_time($mn_array[$i]['event_time'], 'all').'</td>';
									//bearbeiter
									$body .= '<td>'.$mn_array[$i]['user_name'].'</td>';
									$body .= '</tr>'."\r\n";
								}
								//emailtyp text
								else
								{
									//Pfad
									$body .= html_entity_decode($mn_array[$i]['pfad'], ENT_QUOTES, 'UTF-8');
									//originalname bei bearbeitungen
									if (!empty($mn_array[$i]['org_name']))
									{
										$body .= html_entity_decode(' ('.$mn_array[$i]['org_name'].')', ENT_QUOTES, 'UTF-8');
									}
									//eventtyp
									$body .= "\t\t".html_entity_decode($this->trigger_past_array[$mn_array[$i]['event']], ENT_QUOTES, 'UTF-8');
									//bearbeitungszeit
									$body .= "\t\t".$kal->win_to_time($mn_array[$i]['event_time'], 'all');
									//bearbeiter
									$body .= "\t\t".html_entity_decode($mn_array[$i]['user_name'], ENT_QUOTES, 'UTF-8')."\r\n";
								}
							}
						}

						if (!empty($body))
						{
							//emailtyp html
							if ($GLOBALS['setup_array']['mail']['mailtyp'] == 'html')
							{
								$col = '<colgroup><col width="40%" /><col width="20%" /><col width="20%" /><col width="20%" /></colgroup>';
								$tr_header = '<tr><td><strong>'.get_text(398, 'return')/*Pfad*/.'</strong></td><td><strong>'.get_text(86, 'return')/*Aktionen*/.'</strong></td><td><strong>'.get_text(423, 'return')/*Aenderungszeit*/.'</strong></td><td><strong>'.get_text(1, 'return')/*Benutzername*/.'</strong></td></tr>';
								$body = '<table cellpadding="2" cellspacing="0" border="0" width="100%">'."\r\n".$col."\r\n".$tr_header."\r\n".$body.'</table>';

								$mail->send_html_mail(get_text(403, 'return'),//E-Mail Benachrichtigung
													$body,
													$result['email']);
							}
							//emailtyp text
							else
							{
								$mail->send_text_mail(get_text(403, 'return'),//E-Mail Benachrichtigung
													$body,
													$result['email']);
							}
						}
					}
				}

				for ($i = 0; $i < count($log_id_array); $i++)
				{
					$cdb->delete('DELETE FROM fom_mn_log WHERE log_id='.$log_id_array[$i].' LIMIT 1');
				}
			}
		}
	}
?>