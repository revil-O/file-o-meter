<?php
	/**
	 * index file of the logbook folder
	 * @package file-o-meter
	 * @subpackage logbook
	 */

	require_once('../inc/include.php');

	if ($ac->chk('_SETUP_V', 'w'))
	{
		require_once('main.php');
	}
	require_once(FOM_ABS_PFAD.'template/'.$setup_array['template'].'/footer.php');
?>