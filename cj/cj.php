<?php
	/**
	 * cronjob
	 * this file should be executed periodically by a cronjob
	 * @package file-o-meter
	 * @subpackage cj
	 */

	ini_set('memory_limit', -1);
	define('FOM_LOGIN_SITE', 'true');

	require_once('../inc/include.php');

	$fj = new FileJobs;
	$search = new Search;
	$dl = new Download;
	$mysql_dump = new MySqlBackup;
	$mail = new Mailer;
	$cp = new CryptPw;
	$kal = new Kalender();

	//Backup
	if ($setup_array['backup']['aktiv_boole'] == true)
	{
		//Taegliches Backup
		if ($setup_array['backup']['time_array']['all'] != '--')
		{
			$backup_hour = $setup_array['backup']['time_array']['all'];
		}

		//Tageszeiten Pruefen
		if (!isset($backup_hour))
		{
			$day_array = array('so', 'mo', 'di', 'mi', 'do', 'fr', 'sa');

			if ($setup_array['backup']['time_array'][$day_array[date('w')]] != '--')
			{
				$backup_hour = $setup_array['backup']['time_array'][$day_array[date('w')]];
			}
		}

		//Pruefen ob Bakup in dieser Stunde durchgefuehrt werden soll
		if (isset($backup_hour) and intval(date('H')) == intval($backup_hour))
		{
			//Pruefen ob ein Backup in dieser Stunde bereits durchgefuehrt wurde
			//Dies verhindert, dass in der Backupstunde mehrere Dumps erstellt werden
			if (!$mysql_dump->dump_exists())
			{
				//Bakup durchfuehren
				$result = $mysql_dump->create_dump();

				//Backup erfolgreich
				if ($result === true)
				{
					$dump_info = $mysql_dump->get_dump_info();

					//Benachrichtigungsmail senden
					if ($setup_array['backup']['mail_aktiv_boole'] == true)
					{
						$subject = get_text(273, 'return');//Database backup
						$body = get_text(274, 'return');//The database backup was successful.

						//Download link senden
						if ($setup_array['backup']['mail_link_boole'] == true)
						{
							//html Mail
							if ($setup_array['mail']['mailtyp'] == 'html')
							{
								$body = htmlentities($body, ENT_QUOTES, 'UTF-8', false);
								$body .= "<br/>".htmlentities(get_text(275, 'return'), ENT_QUOTES, 'UTF-8', false)."<br/>";//Please use the following link to download the backup file.
							}
							//text Mail
							else
							{
								$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
								$body .= "\r\n".html_entity_decode(get_text(275, 'return'), ENT_QUOTES, 'UTF-8')."\r\n";//Please use the following link to download the backup file.
							}

							//Pruefen ob das Array zum Salzen von PW vorhanden ist
							//Sollte eigendlich nie der fall sein
							if (!isset($cryptpw_salt_array))
							{
								//Array Laden
								require_once($cp->salt_file);
							}

							$tmp_download_link = FOM_ABS_URL.'inc/download.php?typ_string=backup&fileid_int='.$dump_info['dump_id'].'&key_string='.md5($cryptpw_salt_array['sz'][0].md5($dump_info['file_name']).$cryptpw_salt_array['sz'][1].md5($dump_info['dump_typ']).$cryptpw_salt_array['sz'][2].md5($dump_info['file_size']).$cryptpw_salt_array['sz'][3].md5($dump_info['dump_id']).$cryptpw_salt_array['sz'][4].md5($dump_info['save_time']).$cryptpw_salt_array['sz'][5]);

							//html Mail
							if ($setup_array['mail']['mailtyp'] == 'html')
							{
								//Downloadlink erstellen
								$body .= '<a href="'.$tmp_download_link.'" target="_blank">'.get_text(169, 'return').'</a>';//download
							}
							//text Mail
							else
							{
								//Downloadlink erstellen
								$body .= $tmp_download_link;
							}
						}

						//Pruefen ob mehr als ein Empfaenger
						if (substr_count($setup_array['backup']['mail_adress_string'], ',') > 0)
						{
							$to_array = explode(',', $setup_array['backup']['mail_adress_string']);
						}
						//nur ein Empfaenger
						{
							$to_array = array($setup_array['backup']['mail_adress_string']);
						}

						//Email versenden
						for($i = 0; $i < count($to_array); $i++)
						{
							$to = trim($to_array[$i]);

							if (!empty($to))
							{
								if ($setup_array['mail']['mailtyp'] == 'html')
								{
									$mail->send_html_mail($subject, $body, $to);
								}
								else
								{
									$mail->send_text_mail($subject, $body, $to);
								}
							}
						}
					}

					//Tabellen mit ueberhang optimieren
					$sql = $cdb->query('SHOW TABLE STATUS');
					while($result = $cdb->fetch_array($sql))
					{
						if (isset($result['Data_free']) and $result['Data_free'] > 0)
						{
							$cdb->query('OPTIMIZE TABLE '.$result['Name']);
						}
					}
				}
			}
		}
	}

	//Mailnotification
	$mn->send_mn();

	//1 Uhr wird alles ausgefuehrt was irgendwie etwas mit loeschen zu tun hat
	if (date('G') == '1')
	{
		$log = new Logbook();
		$log->clear_login_session();
	}

	//Kopierauftraege erledigen
	$fj->copy_to_fileserver();

	//Tmp Verzeichnis von alten Dateien befreien
	$fj->clear_tmp_folder();
	//alte Indexauftraege loeschen
	$fj->clear_index_job();
	//alte Thumbnailjobs loeschen
	$fj->clear_thumbnail_job();

	//Reloadkeys Loeschen
	$reload->del_reload_id();

	//Suchcache leeren
	$search->clear_cache();

	//alte Downloadtickets loeschen
	$dl->del_download();

	//alte Sessions Loeschen
	$cdb->delete("DELETE FROM fom_session WHERE sess_expiry<'".date("YmdHis")."'");
	//alte Webservice Logins loeschen
	$cdb->delete("DELETE FROM fom_webservice_access WHERE expire<'".date("YmdHis")."'");


	//Dateien indizieren
	$fj->create_file_index();
	//Thumbnails erstellen
	$fj->create_thumbnail();

?>