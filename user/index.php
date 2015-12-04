<?php
	/**
	 * index file of the user folder
	 * @package file-o-meter
	 * @subpackage user
	 */

	require_once('../inc/include.php');

	if ($GLOBALS['FOM_VAR']['fileinc'] == 'add_user' and $ac->chk('_USER_V', 'w'))
	{
		require_once('add_user.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_user' and $ac->chk('_USER_V', 'w'))
	{
		require_once('edit_user.php');
	}
	elseif ($ac->chk('_USER_V', 'r'))
	{
		require_once('main.php');
	}
	require_once(FOM_ABS_PFAD.'template/'.$setup_array['template'].'/footer.php');
?>