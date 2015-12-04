<?php
	/**
	 * index file of the user_group folder
	 * @package file-o-meter
	 * @subpackage user_group
	 */

	require_once('../inc/include.php');

	if ($GLOBALS['FOM_VAR']['fileinc'] == 'add_usergroup' and $ac->chk('_USER_G', 'w'))
	{
		require_once('add_usergroup.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_usergroup' and  $ac->chk('_USER_G', 'w'))
	{
		require_once('edit_usergroup.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_usergroup_folder' and  $ac->chk('_USER_G', 'w'))
	{
		require_once('edit_usergroup_folder.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_user_folder' and  $ac->chk('_USER_G', 'w'))
	{
		require_once('edit_user_folder.php');
	}
	elseif ( $ac->chk('_USER_G', 'r'))
	{
		require_once('main.php');
	}
	require_once(FOM_ABS_PFAD.'template/'.$setup_array['template'].'/footer.php');
?>