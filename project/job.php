<?php
	/**
	 * this file contains all actions for the project-folder
	 * @package file-o-meter
	 * @subpackage project
	 */

	if ($_POST['job_string'] == 'add_project')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['projectname_string']) or empty($_POST['projectname_string'])) {$pflichtfelder++;}
			if (!isset($_POST['fileserver_string']) or empty($_POST['fileserver_string'])) {$pflichtfelder++;}


			if ($pflichtfelder == 0)
			{
				if ($db->insert("INSERT INTO fom_projekte (projekt_name) VALUES ('".$_POST['projectname_string']."')"))
				{
					if ($db->get_affected_rows() == 1)
					{
						$project_id = $db->get_last_insert_id();

						$file_server_pfad = html_entity_decode($_POST['fileserver_string'], ENT_QUOTES, 'UTF-8');

						if ($cdb->insert("INSERT INTO fom_file_server (projekt_id, name, typ, pfad) VALUES ($project_id, 'Lokales Dateisystem', 'local', '$file_server_pfad')"))
						{
							//Vollzugriff fuer die Anlegende Benutzergruppe
							$ac->insert_admin($project_id);

							$meldung['ok'][] = get_text(96,'return');//The dataset was created.
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
	elseif ($_POST['job_string'] == 'edit_project')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['projectname_string']) or empty($_POST['projectname_string']))	{$pflichtfelder++;}
			if (!isset($_POST['pid_int']))														{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if ($db->update("UPDATE fom_projekte SET
								projekt_name='".$_POST['projectname_string']."'
								WHERE projekt_id=".$_POST['pid_int']))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
					$GLOBALS['FOM_VAR']['fileinc'] = '';
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
	//Verzeichnisse endgültig loeschen
	elseif ($_POST['job_string'] == 'del_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['folder_id_array']) or empty($_POST['folder_id_array']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$ffd = new FileFolderDel();

				foreach ($_POST['folder_id_array'] as $folder_id)
				{
					if (is_numeric($folder_id) and $folder_id > 0)
					{
						$ffd->folder_kill(0, $folder_id);
					}
				}

				$meldung['ok'][] = get_text(366, 'return');//The entry has been deleted.
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
	//Dateien endgueltig loeschen
	elseif ($_POST['job_string'] == 'del_file')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['file_id_array']) or empty($_POST['file_id_array']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$ffd = new FileFolderDel();

				foreach ($_POST['file_id_array'] as $file_id)
				{
					if (is_numeric($file_id) and $file_id > 0)
					{
						$ffd->file_kill(0, 0, $file_id);
					}
				}

				$meldung['ok'][] = get_text(366, 'return');//The entry has been deleted.
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
	//Link endgueltig loeschen
	elseif ($_POST['job_string'] == 'del_link')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['link_id_array']) or empty($_POST['link_id_array']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$ffd = new FileFolderDel();

				foreach ($_POST['link_id_array'] as $link_id)
				{
					if (is_numeric($link_id) and $link_id > 0)
					{
						$ffd->link_kill(0, 0, $link_id);
					}
				}

				$meldung['ok'][] = get_text(366, 'return');//The entry has been deleted.
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
	//Projekt loeschen
	elseif ($_POST['job_string'] == 'del_project')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['pid_int']) or empty($_POST['pid_int']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if ($cdb->update("UPDATE fom_projekte SET anzeigen='0' WHERE projekt_id=".$_POST['pid_int']))
				{
					$meldung['ok'][] = get_text(366, 'return');//The entry has been deleted.
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
	//Projekt wiederherstellen
	elseif ($_POST['job_string'] == 'restore_project')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['pid_int']) or empty($_POST['pid_int']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if ($cdb->update("UPDATE fom_projekte SET anzeigen='1' WHERE projekt_id=".$_POST['pid_int']))
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
	//Projekt entgueltig loeschen
	elseif ($_POST['job_string'] == 'kill_project')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			if (!isset($_POST['pid_int']) or empty($_POST['pid_int']))	{$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$ffd = new FileFolderDel();

				$ffd->project_kill($_POST['pid_int']);

				$meldung['ok'][] = get_text(366, 'return');//The entry has been deleted.
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
?>