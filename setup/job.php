<?php
	/**
	 * this file contains all actions for the setup-folder
	 * @package file-o-meter
	 * @subpackage setup
	 */

	if ($_POST['job_string'] == 'backup')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$backup_setup = array();
			//Automatisches Backup Aktiv
			$backup_setup['aktiv_boole'] = false;
			//Benachrichtigungs E-Mail senden
			$backup_setup['mail_aktiv_boole'] = false;
			//Download Link Senden
			$backup_setup['mail_link_boole'] = false;
			//Empfaenger E-Mailadresse
			$backup_setup['mail_adress_string'] = '';
			//Zeiteinstellungen
			$backup_setup['time_array'] = array('mo'	=> '--',
												'di'	=> '--',
												'mi'	=> '--',
												'do'	=> '--',
												'fr'	=> '--',
												'sa'	=> '--',
												'so'	=> '--',
												'all'	=> '--',);

			//Backup Aktiv
			if (isset($_POST['aktiv_int']) and $_POST['aktiv_int'] == true)
			{
				$backup_setup['aktiv_boole'] = true;

				//Benachrichtigungsmail aktiv
				if (isset($_POST['mail_aktiv_int']) and $_POST['mail_aktiv_int'] == true)
				{
					$backup_setup['mail_aktiv_boole'] = true;

					//Downloadlink aktiv
					if (isset($_POST['mail_link_int']) and $_POST['mail_link_int'] == true)
					{
						$backup_setup['mail_link_boole'] = true;
					}
					//Empfaengeradresse
					if (isset($_POST['mail_adress_string']) and !empty($_POST['mail_adress_string']))
					{
						$backup_setup['mail_adress_string'] = $_POST['mail_adress_string'];
					}
				}

				//Taegliches Backup
				if (isset($_POST['time_array']['all']) and $_POST['time_array']['all'] != '--')
				{
					$backup_setup['time_array']['mo'] = $_POST['time_array']['all'];
					$backup_setup['time_array']['di'] = $_POST['time_array']['all'];
					$backup_setup['time_array']['mi'] = $_POST['time_array']['all'];
					$backup_setup['time_array']['do'] = $_POST['time_array']['all'];
					$backup_setup['time_array']['fr'] = $_POST['time_array']['all'];
					$backup_setup['time_array']['sa'] = $_POST['time_array']['all'];
					$backup_setup['time_array']['so'] = $_POST['time_array']['all'];
					$backup_setup['time_array']['all'] = $_POST['time_array']['all'];
				}
				else
				{
					$tmp_day_array = array('mo', 'di', 'mi', 'do', 'fr', 'sa', 'so');

					foreach($tmp_day_array as $day)
					{
						if (isset($_POST['time_array'][$day]) and $_POST['time_array'][$day] != '--')
						{
							$backup_setup['time_array'][$day] = $_POST['time_array'][$day];
						}
					}
				}
			}

			$backup_setup_string = serialize($backup_setup);

			if ($cdb->update("UPDATE fom_setup SET backup='$backup_setup_string' WHERE setup_id=1"))
			{
				$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'mail')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$tmp_mail_array = array();
			$tmp_mail_array['from']			= '';
			$tmp_mail_array['fromname']		= '';
			$tmp_mail_array['altbody']		= '';
			$tmp_mail_array['sendtype']		= 'sendmail';
			$tmp_mail_array['sendmail']		= '';
			$tmp_mail_array['smtphost']		= '';
			$tmp_mail_array['smtpport']		= 25;
			$tmp_mail_array['smtpsecure']	= '';
			$tmp_mail_array['smtpauth']		= false;
			$tmp_mail_array['smtpuser']		= '';
			$tmp_mail_array['smtppw']		= '';
			$tmp_mail_array['mailtyp']		= 'html';

			if (isset($_POST['from']))
			{
				$tmp_mail_array['from'] = $_POST['from'];
			}
			if (isset($_POST['fromname']))
			{
				$tmp_mail_array['fromname'] = $_POST['fromname'];
			}
			if (isset($_POST['altbody']))
			{
				$tmp_mail_array['altbody'] = $_POST['altbody'];
			}
			if (isset($_POST['sendtype']))
			{
				$tmp_mail_array['sendtype'] = $_POST['sendtype'];
			}
			if (isset($_POST['sendmail']))
			{
				$tmp_mail_array['sendmail'] = $_POST['sendmail'];
			}
			if (isset($_POST['smtphost']))
			{
				$tmp_mail_array['smtphost'] = $_POST['smtphost'];
			}
			if (isset($_POST['smtpport']))
			{
				$tmp_mail_array['smtpport'] = $_POST['smtpport'];
			}
			if (isset($_POST['smtpsecure']))
			{
				$tmp_mail_array['smtpsecure'] = $_POST['smtpsecure'];
			}
			if (isset($_POST['smtpauth']))
			{
				if ($_POST['smtpauth'] == 1)
				{
					$tmp_mail_array['smtpauth'] = true;
				}
				else
				{
					$tmp_mail_array['smtpauth'] = false;
				}
			}
			if (isset($_POST['smtpuser']))
			{
				$tmp_mail_array['smtpuser'] = $_POST['smtpuser'];
			}
			if (isset($_POST['smtppw']))
			{
				$tmp_mail_array['smtppw'] = $_POST['smtppw'];
			}

			if (isset($_POST['mailtyp']))
			{
				$tmp_mail_array['mailtyp'] = $_POST['mailtyp'];
			}

			$tmp_mail_string = serialize($tmp_mail_array);

			if ($cdb->update("UPDATE fom_setup SET mail='$tmp_mail_string' WHERE setup_id=1"))
			{
				$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'add_document_type')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['document_type_name_string']) and !empty($_POST['document_type_name_string']))
			{
				if ($db->insert("INSERT INTO fom_document_type (document_type) VALUES ('".$_POST['document_type_name_string']."')"))
				{
					if ($db->get_affected_rows() == 1)
					{
						$meldung['ok'][] = get_text(96,'return');//The dataset was created.
					}
					else
					{
						$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
					}
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'edit_document_type')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['document_type_name_string']) and !empty($_POST['document_type_name_string']) and isset($_POST['dtid_int']) and !empty($_POST['dtid_int']))
			{
				if ($db->insert("UPDATE fom_document_type SET document_type='".$_POST['document_type_name_string']."' WHERE document_type_id=".$_POST['dtid_int']." LIMIT 1"))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'language')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['main_language_id_int']) and !empty($_POST['main_language_id_int']))
			{
				if ($db->insert("UPDATE fom_setup SET main_language_id='".$_POST['main_language_id_int']."' WHERE setup_id=1 LIMIT 1"))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.

					//spracheinstellungen aktualisieren, damit die seite nach einer aenderung gleich in der richtigen sprache angezeigt wird
					$refresh_language_setup = true;
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'db_title')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['db_title']) and !empty($_POST['db_title']))
			{
				if ($db->insert("UPDATE fom_setup SET fom_title='".mysql_real_escape_string($_POST['db_title'])."' WHERE setup_id=1 LIMIT 1"))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'date_format')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['date_format']) and !empty($_POST['date_format']))
			{

				//datumsformat pruefen
				$allowed_date_formats = array();
				array_push($allowed_date_formats, 'YYYY-MM-DD', 'DD.MM.YYYY', 'MM/DD/YYYY');

				if (!in_array($_POST['date_format'], $allowed_date_formats))
				{
					$_POST['date_format'] = 'YYYY-MM-DD';
				}


				if ($db->insert("UPDATE fom_setup SET date_format='".$_POST['date_format']."' WHERE setup_id=1 LIMIT 1"))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'contact')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{

			//Kontaktdaten in Setup speichern
			$kontakt_array = array();

			if (isset($_POST['kontakt_vorname_string']) and !empty($_POST['kontakt_vorname_string']))
			{
				$kontakt_array['first_name'] = mysql_real_escape_string($_POST['kontakt_vorname_string']);
			}
			else
			{
				$kontakt_array['first_name'] = '';
			}

			if (isset($_POST['kontakt_nachname_string']) and !empty($_POST['kontakt_nachname_string']))
			{
				$kontakt_array['last_name'] = mysql_real_escape_string($_POST['kontakt_nachname_string']);
			}
			else
			{
				$kontakt_array['last_name'] = '';
			}

			if (isset($_POST['kontakt_mail_string']) and !empty($_POST['kontakt_mail_string']))
			{
				$kontakt_array['email'] = mysql_real_escape_string($_POST['kontakt_mail_string']);
			}
			else
			{
				$kontakt_array['email'] = '';
			}

			if (isset($_POST['kontakt_tel_string']) and !empty($_POST['kontakt_tel_string']))
			{
				$kontakt_array['phone'] = mysql_real_escape_string($_POST['kontakt_tel_string']);
			}
			else
			{
				$kontakt_array['phone'] = '';
			}

			if (isset($_POST['kontakt_handy_string']) and !empty($_POST['kontakt_handy_string']))
			{
				$kontakt_array['handy'] = mysql_real_escape_string($_POST['kontakt_handy_string']);
			}
			else
			{
				$kontakt_array['handy'] = '';
			}

			if ($db->insert("UPDATE fom_setup SET contact='".serialize($kontakt_array)."' WHERE setup_id=1 LIMIT 1"))
			{
				$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}

		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'ex_prog')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$antiword = '';
			$xpdf = '';
			$gs = '';

			if (isset($_POST['ex_prog_antiword_string']) and !empty($_POST['ex_prog_antiword_string']))
			{
				if (substr($_POST['ex_prog_antiword_string'], -1) == '/' or substr($_POST['ex_prog_antiword_string'], -1) == "\\")
				{
					$antiword = $_POST['ex_prog_antiword_string'];
				}
			}

			if (isset($_POST['ex_prog_xpdf_string']) and !empty($_POST['ex_prog_xpdf_string']))
			{
				if (substr($_POST['ex_prog_xpdf_string'], -1) == '/' or substr($_POST['ex_prog_xpdf_string'], -1) == "\\")
				{
					$xpdf = $_POST['ex_prog_xpdf_string'];
				}
			}

			if (isset($_POST['ex_prog_ghostscript_string']) and !empty($_POST['ex_prog_ghostscript_string']))
			{
				if (substr($_POST['ex_prog_ghostscript_string'], -1) == '/' or substr($_POST['ex_prog_ghostscript_string'], -1) == "\\")
				{
					$gs = $_POST['ex_prog_ghostscript_string'];
				}
			}

			$ex_prog_array = array(	'antiword'		=> $antiword,
									'xpdf'			=> $xpdf,
									'ghostscript'	=> $gs);

			$sql = $db->select('SELECT other_settings FROM fom_setup WHERE setup_id=1');
			$other_setup = $db->fetch_array($sql);

			if (!empty($other_setup['other_settings']))
			{
				$other_setup = @unserialize($other_setup['other_settings']);

				if (!is_array($other_setup))
				{
					$other_setup = array();
				}
			}
			else
			{
				$other_setup = array();
			}

			$other_setup['ex_prog'] = $ex_prog_array;

			if ($db->insert("UPDATE fom_setup SET other_settings='".serialize($other_setup)."' WHERE setup_id=1 LIMIT 1"))
			{
				$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'logbook')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			if (isset($_POST['log_login_int']) and ($_POST['log_login_int'] == 0 or $_POST['log_login_int'] == 1))
			{
				$sql = $db->select('SELECT other_settings FROM fom_setup WHERE setup_id=1');
				$other_setup = $db->fetch_array($sql);

				if (!empty($other_setup['other_settings']))
				{
					$other_setup = @unserialize($other_setup['other_settings']);

					if (!is_array($other_setup))
					{
						$other_setup = array();
					}
				}
				else
				{
					$other_setup = array();
				}

				if ($_POST['log_login_int'] == 0)
				{
					$other_setup['logbook']['login'] = false;
				}
				else
				{
					$other_setup['logbook']['login'] = true;
				}

				if ($db->insert("UPDATE fom_setup SET other_settings='".serialize($other_setup)."' WHERE setup_id=1 LIMIT 1"))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}


	/**
	 * Das Array mit den Grundeinstellungen aktualisieren, falls aenderungen vorgenommen wurden
	 */

	$sql = $cdb->select('SELECT * FROM fom_setup WHERE setup_id=1');
	$result = $cdb->fetch_array($sql);

	foreach($result as $i => $v)
	{
		//ID enthaelt ja keine einstellungen
		if ($i == 'backup' or $i == 'mail')
		{
			$setup_array[$i] = unserialize($v);
		}
	}

?>