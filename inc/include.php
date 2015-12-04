<?php
	/**
	 * this file contains all includes for the project. it loads required classes and creates instances.
	 * @package file-o-meter
	 * @subpackage inc
	 */

	//Bei Jobauftraegen Ausfuehrungszeit erhoehen
	if (isset($_POST['job_string']) or (defined('FOM_LOGIN_SITE') and FOM_LOGIN_SITE == 'true'))
	{
		ini_set('max_execution_time', 600);
	}

	//Include der Servereinstellungen
	if (file_exists('config/config.inc.php'))
	{
		require_once('config/config.inc.php');
	}
	else
	{
		require_once('../config/config.inc.php');
	}

	define('FOM_GLOBAL_VAR_NAME', substr(strtoupper(md5(FOM_SESSION_NAME)), 0, 8));

	//Fehlerspeicherung
	require_once(FOM_ABS_PFAD.'inc/error_handler.php');

	//Allgemeine Meldungen
	$meldung = array();

	//PHPEXCEL define
	define('PHPEXCEL_ROOT', FOM_ABS_PFAD.'inc/class/PHPExcel/');

	/**
	 * Autoload fuer Klassen
	 *
	 * @param string $class
	 */
	function __autoload($class)
	{
		//ZIP Klasse
		if ($class == 'PclZip')
		{
			if (file_exists(FOM_ABS_PFAD.'inc/class/zip/pclzip.lib.php'))
			{
				require_once(FOM_ABS_PFAD.'inc/class/zip/pclzip.lib.php');
			}
		}
		//PHPMailer
		elseif ($class == 'PHPMailer')
		{
			if (file_exists(FOM_ABS_PFAD.'inc/class/PHPMailer/class.phpmailer.php'))
			{
				require_once(FOM_ABS_PFAD.'inc/class/PHPMailer/class.phpmailer.php');
			}
		}
		//TCPDF
		elseif ($class == 'TCPDF' and file_exists(FOM_ABS_PFAD.'inc/class/tcpdf/tcpdf.php'))
		{
			require_once(FOM_ABS_PFAD.'inc/class/tcpdf/tcpdf.php');
		}
		//Excel
		elseif (substr($class, 0, 8) == 'PHPExcel')
		{
			if ($class == 'PHPExcel' and file_exists(PHPEXCEL_ROOT.'PHPExcel.php'))
			{
				require_once(PHPEXCEL_ROOT.'PHPExcel.php');
			}
			else
			{
				$tmp_class_path = str_replace('_', '/', $class).'.php';
				if (file_exists(PHPEXCEL_ROOT.$tmp_class_path))
				{
					require(PHPEXCEL_ROOT.$tmp_class_path);
				}
			}
		}
		//Standardklassen
		else
		{
			if (file_exists(FOM_ABS_PFAD.'inc/class/'.$class.'.php'))
			{
				require_once(FOM_ABS_PFAD.'inc/class/'.$class.'.php');
			}
		}
	}


	/**
	 * prueft, ob user-sprache gesetzt ist - sonst wird default-sprache der db verwendet.
	 * setzt $GLOBALS['user_language'] mit der zu verwendenden language_id
	 */
	function set_userlanguage_global()
	{
		$cdb = new MySql();

		//pruefen, ob user-sprache gesetzt ist - sonst default-sprache der db verwenden
		if (defined('USER_ID'))
		{
			$sql_usersprache = $cdb->select('SELECT language_id FROM fom_user WHERE user_id='.USER_ID);
			$result_usersprache = $cdb->fetch_array($sql_usersprache);
		}

		if (isset($result_usersprache['language_id']) && $result_usersprache['language_id'] > 0)
		{
			$GLOBALS['user_language'] = $result_usersprache['language_id'];
		}
		else
		{
			$sql = $cdb->select('SELECT main_language_id FROM fom_setup WHERE setup_id=1');
			$result = $cdb->fetch_array($sql);

			if (!isset($result['main_language_id']) or empty($result['main_language_id']))
			{
				$main_language_id = 1;
			}
			else
			{
				$main_language_id = $result['main_language_id'];
			}

			$GLOBALS['user_language'] = $main_language_id;
		}
	}

	//Standardklassen
	$db		= new MySql;
	$cdb	= new MySql;
	//Achtung die Klasse GlobalVars() darf mit aussnahme der loginseite und loginklasse nur hier definiert werden
	$gv		= new GlobalVars();
	//$GLOBALS['gv'] = $gv;
	$gt		= new Tree;
	$login	= new Login;
	$reload	= new Reload;
	$chk_v	= new ChkVariable;


	//Nicht auf Logonseite durchfuehren
	if (!defined('FOM_LOGIN_SITE') or FOM_LOGIN_SITE != 'true')
	{
		//Erster aufruf nach Login
		//Die Action darf nur einmal durchgefuehrt werden dafuer dient $GLOBALS['FOM_VAR']['FOM_COOKIE_CHK']
		if (isset($_GET['action']) and $_GET['action'] == 'login')
		{
			if (isset($GLOBALS['FOM_VAR']['FOM_COOKIE_CHK']) and $GLOBALS['FOM_VAR']['FOM_COOKIE_CHK'] == 1)
			{
				$GLOBALS['FOM_VAR']['FOM_COOKIE_CHK'] = 0;

				//Pruefen ob ein Cookie vorhanden ist
				//Pruefen ob das Cookie en selben inhalt wie in der DB hat
				if (isset($_COOKIE[FOM_SESSION_NAME]) and isset($GLOBALS['FOM_VAR']['FOM_SESSION_COOKIE']) and $_COOKIE[FOM_SESSION_NAME] == $GLOBALS['FOM_VAR']['FOM_SESSION_COOKIE'])
				{
					//ACHTUNG diese Variable darf nicht mehr veraendert werden
					$GLOBALS['FOM_VAR']['FOM_COOKIE_EXISTS'] = 1;
				}
			}
		}

		//Logindaten Pruefen
		if (!$login->chk_login_key($gv->get_key()))
		{
			header('Location: '.FOM_ABS_URL.'index.php');
			exit();
		}

		//Logout
		if (isset($_GET['action']) and $_GET['action'] == 'logout')
		{
			$login->logout($gv->get_key());
			header('Location: '.FOM_ABS_URL.'index.php');
			exit();
		}
	}

	//Grundeinstellungem
	require_once(FOM_ABS_PFAD.'inc/getsetup.php');


	//setzt $GLOBALS['user_language'] mit der zu verwendenden language_id
	set_userlanguage_global();


	//Alle nicht in klassen gebundene Funktionen
	require_once(FOM_ABS_PFAD.'inc/function.php');

	//Prueft alle $_POST, $_GET und $_COOKIE werte
	$chk_v->chk_globals();

	$mn	= new MailNotification();

	//job datei includen fuer alle Aenderungen an den DB Daten
	if (isset($_POST['job_string']))
	{
		$jobpfad = getcwd();
		if (file_exists($jobpfad.'/job.php'))
		{
			require_once($jobpfad.'/job.php');
		}
		else
		{
			$meldung['error'][] = get_text('error','return').' (missing job-file)';//An error has occurred!
		}
	}

	//spracheinstellungen refreshen wenn noetig
	if (isset($refresh_language_setup) && $refresh_language_setup == true)
	{
		//setzt $GLOBALS['user_language'] mit der zu verwendenden language_id
		set_userlanguage_global();
	}

	//Nicht auf Logonseite durchfuehren
	if (!defined('FOM_LOGIN_SITE') or FOM_LOGIN_SITE != 'true')
	{
		if (!isset($show_header) or $show_header != 'n')
		{
			require_once(FOM_ABS_PFAD.'template/'.$setup_array['template'].'/header.php');
		}
	}

	if (isset($_GET['fileinc']))
	{
		$GLOBALS['FOM_VAR']['fileinc'] = $_GET['fileinc'];
	}
	if (!isset($GLOBALS['FOM_VAR']) or !isset($GLOBALS['FOM_VAR']['fileinc']))
	{
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
?>