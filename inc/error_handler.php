<?php
	/**
	 * error handler / Anwendungsspezifische Fehlermeldung
	 * Die Datei beinhaltet Funktionen die zum anwendungsspezifischen Fehlerlog verwendet werden koennen.
	 * @author Soeren Pieper <soeren.pieper@docemos.de>
	 * @copyright 2008 docemos
	 * @package file-o-meter
	 * @subpackage inc
	 */

	/**
	 * Konstanten fuer das Errorlevel
	 */
	define('NOTICE', E_USER_NOTICE);
	define('WARNING', E_USER_WARNING);
	define('ERROR', E_USER_ERROR);
	$GLOBALS['error_line'] = 0;

	/**
	 * Funktion uebergibt die jeweilige Fehlermeldung an errorHandler()
	 * @param string $error_message
	 * @param int $error_typ
	 * @param int $error_line
	 * @return string
	 */
	function setError($error_message, $error_typ = NOTICE, $error_line)
	{
		$GLOBALS['error_line'] = $error_line;
		error_handler($error_typ, $error_message, '', $error_line);
		return $error_message;
	}

	/**
	 * Errorbehandlung
	 * Speicherung von eventuell auftrettenden Fehlern
	 * @param string $error_typ
	 * @param string $error_message
	 * @param string $error_file
	 * @param int $error_line
	 * @return void
	 * @TODO mail() Funktion sollte je nach Projekt angepasst werden
	 */
	function error_handler($error_typ = NOTICE, $error_message, $error_file = '', $error_line)
	{
		//Fehlertypen die Gespeichert werden sollen
		//$save_errors_array = array(WARNING, ERROR);
		//sollte nur waehrend der entwicklung einer Anwendung verwendet werden
		$save_errors_array = array(NOTICE, WARNING, ERROR);

		if (in_array($error_typ, $save_errors_array))
		{
			//Errortypen die Verarbeitet werden koennen
			$errortyp_array = array(NOTICE	=> 'Mitteilung - Skript wurde nicht abgebrochen.',
									WARNING	=> 'Warnung - Skript wurde nicht abgebrochen.',
									ERROR	=> 'Error - Skript wurde abgebrochen.');

			//Fehlermeldungen im XML Format erfassen
			$error_report_string = "\t<errorreport>\r\n";
			$error_report_string .= "\t\t<time>".utf8_encode(gmdate('YmdHis'))."</time>\r\n";
			$error_report_string .= "\t\t<server>".utf8_encode($_SERVER['SERVER_ADDR'])."</server>\r\n";
			$error_report_string .= "\t\t<phpversion>".PHP_VERSION."</phpversion>\r\n";
			$error_report_string .= "\t\t<os>".PHP_OS."</os>\r\n";
			$error_report_string .= "\t\t<type>".utf8_encode($errortyp_array[$error_typ])."</type>\r\n";
			if (!empty($error_message))
			{
				$error_report_string .= "\t\t<message>".utf8_encode($error_message)."</message>\r\n";
			}
			else
			{
				$error_report_string .= "\t\t<message />\r\n";
			}
			$error_report_string .= "\t\t<file>".utf8_encode($_SERVER['SCRIPT_FILENAME'])."</file>\r\n";
			$error_report_string .= "\t\t<line>".utf8_encode($GLOBALS['error_line'])."</line>\r\n";
			$error_report_string .= "\t</errorreport>\r\n";

			$pfad_logfolder_string = error_get_file_setup('pfad');
			$logfile_string = error_get_file_setup();

			$pfad_logfile_string = $pfad_logfolder_string.gmdate($logfile_string).'.log';

			//die angegebene Log Datei existiert noch nicht
			if (!file_exists($pfad_logfile_string))
			{
				if ($log_file_handle = fopen($pfad_logfile_string, 'w'))
				{
					//Achtung der Schliessende Haupttag </logroot> fehlt in den Logdateien er wird erst duch die Function closeErrorFiles() durch einen Cronjob erzeugt
					fwrite($log_file_handle, '<?xml version="1.0" encoding="utf-8"?>'."\r\n<errorlog>\r\n");
				}
			}
			else
			{
				$log_file_handle = fopen($pfad_logfile_string, 'a');
			}
			if (isset($log_file_handle))
			{
				fwrite($log_file_handle, $error_report_string);
				fclose($log_file_handle);
			}

			//FIXME: Hier sollte eine Mailklasse eingebunden werden!
			//gegebenenfalls Fehlermeldung per E-Mail versenden
			//FIXME: Sollte aus den Setupwerten kommen
			$send_mail_bool = true;
			//$send_mail_bool = false;
			if ($send_mail_bool === true)
			{
				//Bei welchen Fehlertypen soll eine E-Mail versendet werden.
				//FIXME: Sollte aus den Setupwerten kommen
				$send_errors_array = array(ERROR);
				if (in_array($error_typ, $send_errors_array))
				{
					//E-Mail versendet
					//FIXME: Sollte aus den Setupwerten kommen
					$to = '';	//recipient/mailempfaenger
					$subject = 'Errormessage';
					//mail($to, $subject, $error_report_string);
				}
			}
		}
	}

	/**
	 * Funktion gibt den Dateinamen (Speicherintervall) fuer Logdateien zurueck udn den Speicherpfad.
	 * @param string $typ
	 * @return string
	 */
	function error_get_file_setup($typ = '')
	{
		if ($typ == 'pfad')
		{
			//pfad zur logdatei
			return FOM_ABS_PFAD.'files/log/error/';
		}
		else
		{
			//Dateiname fuer die Logdatei. Der String wirde in der Funktion gmdate() verwendet.
			//return 'Ymd';//Taeglich eine neue Datei erstellen.
			//return 'YW';//Pro KW eine neue Datei erstellen.
			return 'Ym';//Pro Monat eine neue Datei erstellen.
			//return 'Y';//Pro Jahr eine neue Datei erstellen.
		}
	}

	/**
	 * Schreibt den Endtag einer Logdatei und setzt diese auf Leserechte.
	 * @return void
	 */
	function error_close_files()
	{
		//Pfad zu den Logdateien
		$pfad_logfolder_string = error_get_file_setup('pfad');
		//Namenszusammensetzung der Aktuellen Logdateien
		$logfile_current_string = gmdate(error_get_file_setup()).'.log';

		if ($folder_handle = opendir($pfad_logfolder_string))
		{
			//Alle Logdateien auslesen
			$filenames_array = array();
			while (false !== ($file = readdir($folder_handle)))
			{
				if (is_file($pfad_logfolder_string.$file) and is_writable($pfad_logfolder_string.$file) and $file != $logfile_current_string and substr($file, -3) == 'log')
				{
					$filenames_array[] = $file;
				}
			}
			//Pruefen ob Logdateien vorhanden sind
			if (count($filenames_array) > 0)
			{
				for($i = 0; $i <= count($filenames_array); $i++)
				{
					if ($h = fopen($pfad_logfolder_string.$filenames_array[$i], 'a+'))
					{
						fseek($h,-11,SEEK_END);
						$end_tag_string = fgets($h,1024);

						//Datei wurde bereits abgeschlossen
						if ($end_tag_string == '</errorlog>')
						{
							fclose($h);
							if(PHP_OS == 'LINUX')
							{
								chmod($pfad_logfolder_string.$filenames_array[$i], 0444);
							}
						}
						else
						{
							fseek($h,0,SEEK_END);
							fwrite($h,'</errorlog>');
							fclose($h);
							if (PHP_OS == 'LINUX')
							{
								chmod($pfad_logfolder_string.$filenames_array[$i], 0444);
							}
						}
					}
				}
			}
		}
	}
?>