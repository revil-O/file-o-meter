<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	ini_set('docref_root', 'http://www.de.php.net/manual/de/');
	ini_set('error_prepend_string', '<br /><b>FOM_Error:</b>');
	ini_set('default_charset', 'UTF-8');

	//Datenbankzugriff
	define('FOM_DB_PORT', '');
	define('FOM_DB_SOCKET', '');
	define('FOM_DB_SERVER', '');
	define('FOM_DB_USER', '');
	define('FOM_DB_PW', '');
	define('FOM_DB_NAME', '');

	define('FOM_MYSQL_EXEC', '');
	define('FOM_MYSQL_DUMP', '');

	//Pfad zum Verzeichnis mit Ausfuehrungsrechten
	define('FOM_ABS_PFAD_EXEC', '');

	//Pfad zum DMS
	define('FOM_ABS_PFAD', '');
	//URL zum DMS
	define('FOM_ABS_URL', '');
	//Sessionname
	define('FOM_SESSION_NAME', '');
	define('FOM_SESSION_MAX_LIFE', '');

	//Maximale Stringlaenge fuer Verzeichnisnamen
	define('FOM_MAX_LENGTH_FOLDER', '');

	//Maximale Stringlaenge fuer Dateinamen
	define('FOM_MAX_LENGTH_FILE', '');

	//Uploadverzeichnis des Servers
	define('TMP_UPLOAD_DIR', '');
?>