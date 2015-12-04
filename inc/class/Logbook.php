<?php
	/**
	 * recording of miscellaneous user-activities (logbook)
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de>
	 * @copyright Copyright (C) 2011  docemos GmbH
	 * @package file-o-meter
	 *
	 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
	 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 * You should have received a copy of the GNU General Public License along with this program; if not, see http://www.gnu.org/licenses/.
	 */

	/**
	 * recording of miscellaneous user-activities (logbook)
	 * @package file-o-meter
	 * @subpackage classes
	 */
	class Logbook
	{
		public $setup_array = array();

		public function __construct()
		{
			$this->setup_array['log_pfad'] = FOM_ABS_PFAD.'files/log/';
			//Pruefen ob Lobbuch aktiv ist
			if (isset($GLOBALS['setup_array']['other_settings']['logbook']['login']))
			{
				if ($GLOBALS['setup_array']['other_settings']['logbook']['login'] == true)
				{
					$this->setup_array['log_aktiv'] = true;
				}
				else
				{
					$this->setup_array['log_aktiv'] = false;
				}
			}
			else
			{
				$this->setup_array['log_aktiv'] = true;
			}
		}

		/**
		* Diese Funktion traegt Login bzw. Logout vorgaenge in die DB
		* @param int $user_id
		* @param int $action Gibt an ob es sich um ein Login = 1 oder Logout handelt = 0
		* @param string $login_session md5 schluessel der beim Login vergeben wird
		* @return void
		*/
		public function login_insert($user_id, $action, $login_session)
		{
			if ($this->setup_array['log_aktiv'] == true)
			{
				//SQL Klasse einbinden
				$cdb = new MySql();

				//Login, einfaches insert ohne pruefung auf bereits existierende Daten
				if ($action == 1)
				{
					$cdb->insert("INSERT INTO fom_log_login (user_id, login_time, ip, login_session) VALUES ($user_id, '".date("YmdHis")."', '".$_SERVER['REMOTE_ADDR']."', '$login_session')");
				}
				//Logout, mit der pruefung auf einen Logindatensatz, Theoretisch muss es immer einen geben, es sei den er wurde waehrend der User Online ist geloescht
				else
				{
					if (!empty($login_session))
					{
						//prueft ob ein Logindatensatz vorhanden ist. Sollte eigendlich immer der Fall sein.
						$sql = $cdb->select("SELECT COUNT(log_id) AS count_id FROM fom_log_login WHERE login_session='$login_session'");
						$result = $cdb->fetch_array($sql);

						//Es Sollte natuerlich nur ein Eintrag vorhanden sein
						if ($result['count_id'] == 1)
						{
							$cdb->update("UPDATE fom_log_login SET logout_time='".date("YmdHis")."', login_session=NULL WHERE login_session='$login_session'");
						}
					}
				}
			}
		}

		/**
		* Diese Funktion traegt ein Webservicelogin in die DB ein
		* @param int $user_id
		* @return void
		*/
		public function webservice_login_insert($user_id)
		{
			if ($this->setup_array['log_aktiv'] == true)
			{
				//SQL Klasse einbinden
				$cdb = new MySql();

				$cdb->insert("INSERT INTO fom_log_login (user_id, login_time, ip, login_type) VALUES ($user_id, '".date("YmdHis")."', '".$_SERVER['REMOTE_ADDR']."', 'webservice')");
			}
		}

		/**
		 * Enterfernt Loginsessioneintraege aus der Logbuchtabelle die aelter als ein Monat sind
		 * @return void
		 */
		public function clear_login_session()
		{
			//SQL Klasse einbinden
			$cdb = new MySql();
			//Kalenderklasse
			$kal = new Kalender();

			//Datum vor einem Monat finden
			$last_month = $kal->date_calculator('', '', -1, '');
			$last_month = $kal->is_iso_date($last_month[0]);

			if ($last_month !== false)
			{
				$last_month_string = str_replace('-', '', $last_month);

				$sql = $cdb->update("UPDATE fom_log_login SET login_session=NULL WHERE LEFT(login_time, 8) < '$last_month_string' AND login_session IS NOT NULL");
			}
		}

		/**
		 * Erstellt einen Logeintrag zu einer versendeten Mail
		 * @param string $type
		 * @param string $subject
		 * @param string $body
		 * @param mixed $to, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param array $attachment array(array('filepfad', 'filename'), array('filepfad')) filename muss nicht angegeben werden
		 * @param mixed $bcc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param mixed $cc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @return void
		 */
		public function insert_log_mail($type, $subject, $body, $to, $attachment, $bcc, $cc)
		{
			if ($this->setup_array['log_aktiv'] == true)
			{
				$log_file = date('Ymd').'.log';

				$open_file = false;
				//Datei existiert bereits. Datei oeffnen und Dateizeiger ans ende positionieren
				if (file_exists($this->setup_array['log_pfad'].'mail/'.$log_file))
				{
					if ($h = @fopen($this->setup_array['log_pfad'].'mail/'.$log_file, 'a'))
					{
						$open_file = true;
					}
				}
				//Datei existiert noch nicht. Datei anlegen und erste Zeile Schreiben
				else
				{
					if ($h = @fopen($this->setup_array['log_pfad'].'mail/'.$log_file, 'w'))
					{
						$open_file = true;
						fwrite($h, '<?xml version="1.0" encoding="utf-8" ?>'."\n");
					}
				}

				if ($open_file == true)
				{
					//Blockelement
					fwrite($h, '<mail_log>'."\n");

					//Zeit
					fwrite($h, "\t<time>".utf8_encode(date('YmdHis'))."</time>\n");

					//Userid wenn vorhanden. Wenn keine da sollte es immer der CJ sein
					if (defined('USER_ID'))
					{
						fwrite($h, "\t<user>".utf8_encode(USER_ID)."</user>\n");
					}
					else
					{
						fwrite($h, "\t<user />\n");
					}
					//Mailtyp. HTML oder Text
					fwrite($h, "\t<type>".utf8_encode($type)."</type>\n");

					//Betreff
					if (!empty($subject))
					{
						fwrite($h, "\t<subject>".utf8_encode($subject)."</subject>\n");
					}
					else
					{
						fwrite($h, "\t<subject />\n");
					}
					//Mailbody
					if (!empty($body))
					{
						fwrite($h, "\t<body>".utf8_encode(str_replace('<', '[', str_replace('>', ']',$body)))."</body>\n");
					}
					else
					{
						fwrite($h, "\t<body />\n");
					}

					//Empfaenger
					if (!empty($to))
					{
						if (is_array($to))
						{
							$tmp_to = '';
							foreach ($to as $to_mail)
							{
								if (empty($tmp_to))
								{
									$tmp_to = $to_mail;
								}
								else
								{
									$tmp_to .= ', '.$to_mail;
								}
							}
							$to = $tmp_to;
						}
						fwrite($h, "\t<to>".utf8_encode($to)."</to>\n");
					}
					else
					{
						fwrite($h, "\t<to />\n");
					}

					//Empfaenger
					if (!empty($bcc))
					{
						if (is_array($bcc))
						{
							$tmp_bcc = '';
							foreach ($bcc as $bcc_mail)
							{
								if (empty($tmp_bcc))
								{
									$tmp_bcc = $bcc_mail;
								}
								else
								{
									$tmp_bcc .= ', '.$bcc_mail;
								}
							}
							$bcc = $tmp_bcc;
						}
						fwrite($h, "\t<bcc>".utf8_encode($bcc)."</bcc>\n");
					}
					else
					{
						fwrite($h, "\t<bcc />\n");
					}

					//Empfaenger
					if (!empty($cc))
					{
						if (is_array($cc))
						{
							$tmp_cc = '';
							foreach ($cc as $cc_mail)
							{
								if (empty($tmp_cc))
								{
									$tmp_cc = $cc_mail;
								}
								else
								{
									$tmp_cc .= ', '.$cc_mail;
								}
							}
							$cc = $tmp_cc;
						}
						fwrite($h, "\t<cc>".utf8_encode($cc)."</cc>\n");
					}
					else
					{
						fwrite($h, "\t<cc />\n");
					}

					//Anhhaenge
					if (is_array($attachment) and count($attachment) > 0)
					{
						fwrite($h, "\t<attachment>".utf8_encode(@serialize($attachment))."</attachment>\n");
					}
					else
					{
						fwrite($h, "\t<attachment />\n");
					}

					//Blockelement
					fwrite($h, "</mail_log>\n");
					fclose($h);
				}
			}
		}
	}
?>