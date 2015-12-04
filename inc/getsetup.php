<?php
	/**
	 * provides a Setup-Array
	 * this include-file reads all configuration-settings from the database
	 * @package file-o-meter
	 * @subpackage inc
	 */

	$setup_array = array();

	$sql = $cdb->select('SELECT * FROM fom_setup WHERE setup_id=1');
	$result = $cdb->fetch_array($sql);

	foreach($result as $i => $v)
	{
		//ID enthaelt ja keine einstellungen
		if ($i == 'backup' or $i == 'mail' or $i == 'other_settings')
		{
			$setup_array[$i] = unserialize($v);
		}
	}
	//LoginCookie verwenden
	//FIXME das muss aus dem setup kommen
	define('FOM_LOGIN_COOKIE', true);

	//Versionsnummer
	define('FOM_VERSION', $result['fom_version']);

	//A-Z Register Funktionen Aktivieren
	define('FOM_AZ_REGISTER', false);

	$setup_array['template'] = $result['template'];

	//Seitenstatus
	$setup_array['site_titel'] = $result['fom_title'];

	//Date format
	define('FOM_DATE_FORMAT', $result['date_format']);

	//Maximale Dateigroesse fuer Uploads ueber das HTML Forumlar
	$tmp_max_size = ini_get('upload_max_filesize');
	if (substr($tmp_max_size, -1) == 'K')
	{
		$tmp_max_size = intval($tmp_max_size) * 1024;
	}
	elseif(substr($tmp_max_size, -1) == 'M')
	{
		$tmp_max_size = intval($tmp_max_size) * 1048576;
	}
	elseif(substr($tmp_max_size, -1) == 'G')
	{
		$tmp_max_size = intval($tmp_max_size) * 1073741824;
	}
	$setup_array['upload_max_filesize'] = $tmp_max_size;

	//Angabe ob Dateinamen der ISO 9660 entsprechen sollen
	$setup_array['iso_9660'] = true;
	//Angabe ob Dateinamen geaendert werden sollen wenn die nicht der ISO 9660 entsprechen
	$setup_array['iso_9660_edit_filename'] = true;
	//Gibt die max. Anzahl an Seiten an die bei einem Dokument mit einem Durchlauf indiziert werden sollen
	$setup_array['index_job_max_page'] = 100;
	//Mindestlaenge eines Wortes was indiziert werden soll
	//Alle woerter die kuerzer sind werden bei einer Suchanfrage nicht beruecksichtigt
	$setup_array['index_min_len'] = 4;

	//Uploadverzeichnis des Servers
	$setup_array['tmp_upload_folder'] = TMP_UPLOAD_DIR;

	if (!isset($setup_array['mail']['mailtyp']))
	{
		$setup_array['mail']['mailtyp'] = 'html';
	}

	//Setuparray als gloabl fuer die Verwendung in Klassen und Funktionen
	$GLOBALS['setup_array'] = $setup_array;

	//Externe Programme
	if (isset($setup_array['other_settings']) and isset($setup_array['other_settings']['ex_prog']))
	{
		if (isset($setup_array['other_settings']['ex_prog']['antiword']))
		{
			define('FOM_ABS_PFAD_EXEC_ANTIWORD', $setup_array['other_settings']['ex_prog']['antiword']);
		}
		else
		{
			define('FOM_ABS_PFAD_EXEC_ANTIWORD', '');
		}

		if (isset($setup_array['other_settings']['ex_prog']['xpdf']))
		{
			define('FOM_ABS_PFAD_EXEC_XPDF', $setup_array['other_settings']['ex_prog']['xpdf']);
		}
		else
		{
			define('FOM_ABS_PFAD_EXEC_XPDF', '');
		}

		if (isset($setup_array['other_settings']['ex_prog']['ghostscript']))
		{
			define('FOM_ABS_PFAD_EXEC_GHOSTSCRIPT', $setup_array['other_settings']['ex_prog']['ghostscript']);
		}
		else
		{
			define('FOM_ABS_PFAD_EXEC_GHOSTSCRIPT', '');
		}
	}
	else
	{
		define('FOM_ABS_PFAD_EXEC_ANTIWORD', '');
		define('FOM_ABS_PFAD_EXEC_XPDF', '');
		define('FOM_ABS_PFAD_EXEC_GHOSTSCRIPT', '');
	}

	//Nicht auf Logonseite durchfuehren
	if (!defined('FOM_LOGIN_SITE') or FOM_LOGIN_SITE != 'true')
	{
		//Klasse fuer das Pruefen von Zugriffsrechten
		$ac = new Access;
	}
?>