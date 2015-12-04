<?php
	define('FOM_LOGIN_SITE', 'true');
	/*
	 *  WSDL Cache abschalten
	 */
	//Aktiviert oder deaktiviert das WSDL-Caching
	ini_set('soap.wsdl_cache_enabled', 0);
	//Bestimmt den Verzeichnisnamen, in dem die SOAP-Extension Cache-Dateien ablegt
	//ini_set('soap.wsdl_cache_dir', '/tmp');
	//Bestimmt die Anzahl der Sekunden (time to live), waehrend derer die Cache-Dateien anstelle der originalen verwendet werden
	ini_set('soap.wsdl_cache_ttl', 0);
	//Wenn soap.wsdl_cache_enabled eingeschaltet ist, bestimmt diese Einstellung die Art des Cachings. Dies kann einer der folgenden Werte sein: WSDL_CACHE_NONE (0), WSDL_CACHE_DISK (1), WSDL_CACHE_MEMORY (2) oder WSDL_CACHE_BOTH (3). Der Wert kann ausserdem mittels des options -Arrays im Konstruktor von SoapClient oder SoapServer bestimmt werden.
	//ini_set('soap.wsdl_cache', 1);
	//Maximale Anzahl der in-memory zwischengespeicherten WSDL-Dateien. Werden in einem vollen Memorycache weitere Dateien abgelegt, so werden dafuer die aeltesten Dateien geloescht.
	//ini_set('soap.wsdl_cache_limit', 5);

	if (class_exists('SoapServer'))
	{
		require_once ('../inc/include.php');

		$server = new SoapServer(FOM_ABS_URL.'web_services/fom.wsdl');

		//Speichert alle functionsnamen die per SOAP Server erreichbar sein sollen
		$soap_server_functions = array();

		//Alle Webservices zu Verzeichnisen
		require_once ('fom_folder.php');
		//Alle Webservices zu Dateien
		require_once ('fom_file.php');
		//Alle Webservices zu Links
		require_once ('fom_link.php');
		//Alle Webservices zum A-Z Register
		require_once ('fom_az_register.php');
		//Alle Webservices zu Sonstigem
		require_once ('fom_other.php');

		$server->addFunction($soap_server_functions);
		$server->handle();
	}
?>