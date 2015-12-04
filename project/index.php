<?php
	/**
	 * index file of the project folder
	 * @package file-o-meter
	 * @subpackage project
	 */

	require_once('../inc/include.php');

	if ($GLOBALS['FOM_VAR']['fileinc'] == 'add_projekt' and $ac->chk('_PROJECT_V', 'w'))
	{
		require_once('add_project.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_projekt' and $ac->chk('_PROJECT_V', 'w'))
	{
		require_once('edit_project.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'trash_projekt' and $ac->chk('_PROJECT_V', 'w'))
	{
		require_once('trash_project.php');
	}
	elseif (($GLOBALS['FOM_VAR']['fileinc'] == 'del_projekt' or $GLOBALS['FOM_VAR']['fileinc'] == 'kill_projekt') and $ac->chk('_PROJECT_V', 'w'))
	{
		require_once('del_project.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'restore_projekt' and $ac->chk('_PROJECT_V', 'w'))
	{
		require_once('restore_project.php');
	}
	elseif ($ac->chk('_PROJECT_V', 'r'))
	{
		require_once('main.php');
	}

	require_once(FOM_ABS_PFAD.'template/'.$setup_array['template'].'/footer.php');
?>