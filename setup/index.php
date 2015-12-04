<?php
	/**
	 * index file of the setup folder
	 * @package file-o-meter
	 * @subpackage setup
	 */

	require_once('../inc/include.php');

	if ($ac->chk('_SETUP_V', 'w'))
	{
		if ($GLOBALS['FOM_VAR']['fileinc'] == 'backup')
		{
			require_once('edit_backup.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'mail')
		{
			require_once('edit_mail.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'document_type')
		{
			require_once('edit_document_type.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'language')
		{
			require_once('edit_language.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'db_title')
		{
			require_once('edit_title.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'date_format')
		{
			require_once('edit_dateformat.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'contact')
		{
			require_once('edit_contact.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'ex_prog')
		{
			require_once('edit_ex_prog.php');
		}
		elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'logbook')
		{
			require_once('edit_logbook.php');
		}
		else
		{
			require_once('main.php');
		}
	}
	require_once(FOM_ABS_PFAD.'template/'.$setup_array['template'].'/footer.php');
?>