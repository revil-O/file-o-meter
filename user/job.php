<?php
	/**
	 * this file contains all actions for the user-folder
	 * @package file-o-meter
	 * @subpackage user
	 */

	if ($_POST['job_string'] == 'add_user')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['loginname_string']) or empty($_POST['loginname_string']))	{$pflichtfelder++;}
			if (!isset($_POST['pw_string']) or empty($_POST['pw_string']))					{$pflichtfelder++;}
			if (!isset($_POST['email_string']) or empty($_POST['email_string']))			{$pflichtfelder++;}
			if (!isset($_POST['usergroup_id_ary']) or !is_array($_POST['usergroup_id_ary']) or count($_POST['usergroup_id_ary']) == 0)	{$pflichtfelder++;}
			if (!isset($_POST['language_int']) or empty($_POST['language_int']))			{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$cp = new CryptPw;
				$pw = $cp->encode_pw($_POST['pw_string']);

				if ($db->insert("INSERT INTO fom_user (vorname, nachname, email, loginname, pw, language_id) VALUES ('".$_POST['vorname_string']."', '".$_POST['nachname_string']."', '".$_POST['email_string']."', '".$_POST['loginname_string']."', '$pw', '".$_POST['language_int']."')"))
				{
					if ($db->get_affected_rows() == 1)
					{
						$last_user_id = $db->get_last_insert_id();

						//userverzeichnis fuer im und export anlegen
						if (file_exists(FOM_ABS_PFAD.'files/imex') and is_writable(FOM_ABS_PFAD.'files/imex'))
						{
							@mkdir(FOM_ABS_PFAD.'files/imex/'.$last_user_id);
						}

						//zugehoerige benutzergruppen speichern
						$insert_error_counter = 0;

						foreach($_POST['usergroup_id_ary'] as $usergroup_id_new)
						{
							if ($db->insert("INSERT INTO fom_user_membership (user_id, usergroup_id) VALUES ('".$last_user_id."','".$usergroup_id_new."')"))
							{
								//alles ok
							}
							else
							{
								$insert_error_counter++;
							}
						}

						if ($insert_error_counter == 0)
						{
							$meldung['ok'][] = get_text(110,'return');//The useraccount was created.
							$GLOBALS['FOM_VAR']['fileinc'] = '';
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
	elseif ($_POST['job_string'] == 'edit_user')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['email_string']) or empty($_POST['email_string']))	{$pflichtfelder++;}
			if (!isset($_POST['usergroup_id_ary']) or !is_array($_POST['usergroup_id_ary']) or count($_POST['usergroup_id_ary']) == 0)	{$pflichtfelder++;}
			if (!isset($_POST['uid_int']) and $_POST['uid_int'] == 0)				{$pflichtfelder++;}
			if (!isset($_POST['language_int']) or empty($_POST['language_int']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if (isset($_POST['pw_string']) and !empty($_POST['pw_string']))
				{
					$cp = new CryptPw;

					$pw = "pw='".$cp->encode_pw($_POST['pw_string'])."',";
				}
				else
				{
					$pw = '';
				}

				if ($db->update("UPDATE fom_user SET
								vorname='".$_POST['vorname_string']."',
								nachname='".$_POST['nachname_string']."',
								email='".$_POST['email_string']."',
								$pw
								login_aktiv='".$_POST['loginaktiv_int']."',
								language_id='".$_POST['language_int']."'
								WHERE user_id=".$_POST['uid_int']))
				{
					if ($db->update("DELETE FROM fom_user_membership WHERE user_id=".$_POST['uid_int']))
					{

						$insert_error_counter = 0;

						foreach($_POST['usergroup_id_ary'] as $usergroup_id_new)
						{
							if ($db->insert("INSERT INTO fom_user_membership (user_id, usergroup_id) VALUES ('".$_POST['uid_int']."','".$usergroup_id_new."')"))
							{
								//alles ok
							}
							else
							{
								$insert_error_counter++;
							}
						}

						if ($insert_error_counter == 0)
						{

							$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.

							//spracheinstellungen aktualisieren, damit die seite nach einer aenderung gleich in der richtigen sprache angezeigt wird
							if (USER_ID == $_POST['uid_int'])
							{
								$refresh_language_setup = true;
							}

							$GLOBALS['FOM_VAR']['fileinc'] = '';
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
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return').$xxx, WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
?>