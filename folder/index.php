<?php
	/**
	 * index file of the "folder" directory
	 * @package file-o-meter
	 * @subpackage folder
	 */

	require_once('../inc/include.php');

	if ($GLOBALS['FOM_VAR']['fileinc'] == 'search')
	{
		require_once('search.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'add_folder' and $ac->chk('project', 'w', $_GET['pid_int']))
	{
		require_once('add_folder.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_folder' and $ac->chk('folder', 'w', $_GET['fid_int']))
	{
		require_once('edit_folder.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'del_folder' and $ac->chk('folder', 'd', $_GET['fid_int']))
	{
		require_once('del_folder.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_as')
	{
		//Zugriffsrechte fuer Dateien
		if (isset($_GET['fileid_int']) and $_GET['fileid_int'] > 0 and $ac->chk('file', 'as', $_GET['fileid_int']))
		{
			require_once('edit_as.php');
		}
		//zugriffsteuerung fuer links
		elseif (isset($_GET['linkid_int']) and $_GET['linkid_int'] > 0 and $ac->chk('file', 'as', $_GET['linkid_int']))
		{
			require_once('edit_as.php');
		}
		//zugriffssteuerung fuer verzeichnisse
		elseif (isset($_GET['fid_int']) and $_GET['fid_int'] > 0 and $ac->chk('folder', 'as', $_GET['fid_int']))
		{
			require_once('edit_as.php');
		}
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'add_newfile' and $ac->chk('folder', 'w', $_GET['fid_int']))
	{
		require_once('add_newfile.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'multiupload' and $ac->chk('folder', 'w', $_GET['fid_int']))
	{
		require_once('multiupload.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'add_newlink' and $ac->chk('folder', 'w', $_GET['fid_int']))
	{
		require_once('add_newlink.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'add_subfile' and $ac->chk('folder', 'w', $_GET['fid_int']))
	{
		require_once('add_subfile.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'show_file' and $ac->chk('file', 'r', $_GET['fileid_int']))
	{
		require_once('show_file.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_file' and $ac->chk('file', 'w', $_GET['fileid_int']))
	{
		require_once('edit_file.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_link' and $ac->chk('folder', 'w', $_GET['fid_int']))
	{
		require_once('edit_link.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'del_file' and $ac->chk('file', 'd', $_GET['fileid_int']))
	{
		require_once('del_file.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'del_link' and $ac->chk('folder', 'd', $_GET['fid_int']))
	{
		require_once('del_link.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'add_fileversion' and $ac->chk('folder', 'va', $_GET['fid_int']))
	{
		require_once('add_fileversion.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'import_data' and $ac->chk('folder', 'di', $_GET['fid_int']))
	{
		require_once('data_import.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'export_data' and $ac->chk('folder', 'de', $_GET['fid_int']))
	{
		require_once('data_export.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'add_download' and $ac->chk('folder', 'd', $_GET['fid_int']))
	{
		require_once('add_download.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'show_version_history' and $ac->chk('folder', 'vo', $_GET['fid_int']))
	{
		require_once('show_version_history.php');
	}
	elseif (($GLOBALS['FOM_VAR']['fileinc'] == 'checkout_file' or $GLOBALS['FOM_VAR']['fileinc'] == 'checkin_file') and $ac->chk('file', 'w', $_GET['fileid_int']))
	{
		require_once('edit_lock_file.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'edit_useraccount')
	{
		require_once('edit_useraccount.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'folder_download' and $ac->chk('project', 'r', $_GET['pid_int']))
	{
		require_once('folder_download.php');
	}
	elseif ($GLOBALS['FOM_VAR']['fileinc'] == 'mail_notification' and $ac->chk('project', 'mn', $_GET['pid_int']))
	{
		require_once('edit_mn.php');
	}
	elseif (isset($_GET['pid_int']) and $ac->chk('project', 'r', $_GET['pid_int']))
	{
		require_once('main.php');
	}
	else
	{
		require_once('welcome.php');
	}
	require_once(FOM_ABS_PFAD.'template/'.$setup_array['template'].'/footer.php');
?>