<?php
	/**
	 * Webservice fuer die Authentifizierung von Webservice anfragen
	 */

	define('FOM_LOGIN_SITE', 'true');
	// WSDL Cache abschalten
	ini_set('soap.wsdl_cache_enabled', false);
	ini_set('soap.wsdl_cache_ttl', 10);
	ini_set('soap.wsdl_cache_limit', 1);

	require_once('../inc/include.php');

	/**
	 * Prueft ob zu den uebergebenen Benutzerdaten ein Aktiver Benutzeraccount vorhanden ist
	 *
	 * @param string $pw
	 * @param string $user
	 * @return string
	 */
	function ws_login($pw, $user)
	{
		$sl = new Login;
		$cp = new CryptPw;

		return $sl->webservice_login($cp->encode_pw($pw), $user);
	}

	/**
	 * Prueft den Uebergebenen WS Key
	 *
	 * @param string $ws_key
	 * @return string
	 */
	function chk_ws_key($ws_key)
	{
		$sl = new Login;

		return $sl->webservice_key($ws_key);
	}

	if (class_exists('SoapServer'))
	{
		$server = new SoapServer(FOM_ABS_URL.'web_services/access.wsdl');
		$server->addFunction(array('ws_login', 'chk_ws_key'));
		$server->handle();
	}
?>