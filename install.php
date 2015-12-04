<?php
	date_default_timezone_set('Europe/London');

	if (isset($_GET['lng']) and $_GET['lng'] == 'txt_en')
	{
		$lng_txt = 'txt_en';
	}
	else
	{
		$lng_txt = 'txt_de';
	}

	$install_version = '0.6b';

	$install_array = array();

	//Alle POST Index die beim beschreiben der config uebergeben werden
	$install_array['config_post_index'] = array('php_error',
												'time_zone',
												'db_server',
												'db_port',
												'db_socket',
												'db_user',
												'db_pw',
												'db_name',
												'mysql',
												'mysqldump',
												'url',
												'abspfad',
												'cookiename',
												'sessiontime',
												'maxlength_folder',
												'maxlength_file'
												);

	//Dateien Verzeichnisse fuer die waehrend der installation schreibrechte vorhanden sein muessen
	$install_array['writable_files'] = array(	'config/',
												'config/config.inc.php',
												'files/backup/',
												'files/imex/',
												'files/log/',
												'files/logo/',
												'files/tmp/',
												'files/upload/',
												'web_services/access.wsdl',
												'web_services/fom.wsdl');

	//include Pfad
	$install_array['include_pfad'] = 'inc/include.php';

	//Texte
	$install_array['txt_de'][0] = 'PHP-Fehlermeldungen anzeigen';
	$install_array['txt_en'][0] = 'show PHP error-messages';

	$install_array['txt_de'][1] = 'nein';
	$install_array['txt_en'][1] = 'no';

	$install_array['txt_de'][2] = 'ja';
	$install_array['txt_en'][2] = 'yes';

	$install_array['txt_de'][3] = '[ja] sollte nur bei Testinstallationen verwendet werden!';
	$install_array['txt_en'][3] = '[yes] should only be used for test installations!';

	$install_array['txt_de'][4] = 'Zeitzoneneinstellung';
	$install_array['txt_en'][4] = 'Timezone setup';

	$install_array['txt_de'][5] = 'MySql-Server';
	$install_array['txt_en'][5] = 'MySql-Server';

	$install_array['txt_de'][6] = 'z.B. localhost';
	$install_array['txt_en'][6] = 'e.g. localhost';

	$install_array['txt_de'][7] = 'MySql-Benutzername';
	$install_array['txt_en'][7] = 'MySql-Username';

	$install_array['txt_de'][8] = 'z.B. root';
	$install_array['txt_en'][8] = 'e.g. root';

	$install_array['txt_de'][9] = 'MySql-Passwort';
	$install_array['txt_en'][9] = 'MySql-Password';

	$install_array['txt_de'][10] = 'Datenbankname';
	$install_array['txt_en'][10] = 'Database name';

	$install_array['txt_de'][11] = 'z.B. fom';
	$install_array['txt_en'][11] = 'e.g. fom';

	$install_array['txt_de'][12] = 'MySQL Pfad';
	$install_array['txt_en'][12] = 'MySQL Path';

	$install_array['txt_de'][13] = 'MySQL Pfadangabe, z.B. bei Linux "usr/bin/mysql" oder bei Windows "C:/mysql/bin/mysql.exe". Es wird empfohlen, den Pfad zur mysql Datei anzugeben. Mit dieser werden SQL-Backups in die Datenbank eingespielt. Erfolgt keine Angabe ist das wiederherstellen von Backups &uuml;ber das System nicht m&ouml;glich! In diesem Fall mu&szlig; das SQL-Backup von Hand eingespielt werden.';
	$install_array['txt_en'][13] = 'MySQL Path, e.g. "usr/bin/mysql" for Linux or "C:/mysql/bin/mysql.exe" on a Windows server. It is recommended to specify the path to the mysql file, wich is responsible for the restauration of sql-backups into the database. The restauration of backups via this application is not possible, if no path is specified. In this case sql-backups have to be restored manually.';

	$install_array['txt_de'][14] = 'MySQL-Dump Pfad';
	$install_array['txt_en'][14] = 'MySQL-Dump Path';

	$install_array['txt_de'][15] = 'MySQL-Dump Pfadangabe, z.B. bei Linux "usr/bin/mysqldump" oder bei Windows "C:/mysql/bin/mysqldump.exe". Es wird empfohlen, den Pfad zur mysqldump Datei anzugeben. Wird kein Pfad angegeben, wird das Backup mithilfe eines PHP Skriptes erzeugt, dieses ist jedoch langsamer und unzuverl&auml;ssiger als die Verwendung von mysqldump.';
	$install_array['txt_en'][15] = 'MySQL-Dump Path, e.g. "usr/bin/mysqldump" for Linux or "C:/mysql/bin/mysqldump.exe" on a Windows server. It is recommended to specify the path to the mysqldump file. If no path is specified, backups will be performed via PHP script instead. But this way is slower and less reliable as the use of mysqldump.';

	$install_array['txt_de'][16] = 'URL zur Hauptebene';
	$install_array['txt_en'][16] = 'URL to root directory';

	$install_array['txt_de'][17] = 'URL zum Hauptverzeichnis, z.B. "http://www.mydomain.com/fom/". Achtung, die URL mu&szlig; immer mit einem "/" enden.';
	$install_array['txt_en'][17] = 'URL to root directory, e.g. "http://www.mydomain.com/fom/". Attention, the slash "/" at the end of the URL is required.';

	$install_array['txt_de'][18] = 'Absoluter Pfad zur Hauptebene';
	$install_array['txt_en'][18] = 'Absolute path to the root directoy';

	$install_array['txt_de'][19] = 'Absoluter Pfad zum Hauptverzeichnis, z.B. "/web/htdocs/fom/" oder "C:/web/htdocs/fom/". Achtung, der absolute Pfad mu&szlig; immer mit einem "/" enden.';
	$install_array['txt_en'][19] = 'Absolute path to the root directoy, e.g. "/web/htdocs/fom/" or "C:/web/htdocs/fom/". Attention, the absolute path has to end with a slash "/".';

	$install_array['txt_de'][20] = 'Cookiename';
	$install_array['txt_en'][20] = 'Cookie name';

	$install_array['txt_de'][21] = 'Einen eindeutigen Cookienamen ohne Sonderzeichen (au&szlig;er Unterstrich), z.B. domainname_fom';
	$install_array['txt_en'][21] = 'An unique cookie name without umlauts and special chars (except underscore), e.g. domainname_fom';

	$install_array['txt_de'][22] = 'Maximale Inaktivit&auml;tsdauer der User';
	$install_array['txt_en'][22] = 'Maximum user idle time';

	$install_array['txt_de'][23] = 'Angabe in Sekunden. Nach Ablauf der Zeit wird der betreffende Anwender automatisch ausgeloggt.';
	$install_array['txt_en'][23] = 'In seconds. The concerning user will be logged out automatically after expiration.';

	$install_array['txt_de'][24] = 'Mindestpasswortl&auml;nge';
	$install_array['txt_en'][24] = 'Minimum passwordlength';

	$install_array['txt_de'][25] = 'Der Wert sollte nicht unter 8 liegen';
	$install_array['txt_en'][25] = 'This value should not fall below 8';

	$install_array['txt_de'][26] = 'Anrede';
	$install_array['txt_en'][26] = 'Title';

	$install_array['txt_de'][27] = 'Bitte w&auml;hlen';
	$install_array['txt_en'][27] = 'Please Select';

	$install_array['txt_de'][28] = 'Herr';
	$install_array['txt_en'][28] = 'Mr.';

	$install_array['txt_de'][29] = 'Frau';
	$install_array['txt_en'][29] = 'Mrs.';

	$install_array['txt_de'][30] = 'Vorname';
	$install_array['txt_en'][30] = 'First name';

	$install_array['txt_de'][31] = 'Nachname';
	$install_array['txt_en'][31] = 'Last name';

	$install_array['txt_de'][32] = 'E-Mail';
	$install_array['txt_en'][32] = 'E-Mail';

	$install_array['txt_de'][33] = 'Geburtsdatum';
	$install_array['txt_en'][33] = 'Date of birth';

	$install_array['txt_de'][34] = 'JJJJ-MM-TT';
	$install_array['txt_en'][34] = 'YYYY-MM-DD';

	$install_array['txt_de'][35] = 'Urlaubstage';
	$install_array['txt_en'][35] = 'Leave days';

	$install_array['txt_de'][36] = 'Jahresurlaubsanspruch des Mitarbeiters.';
	$install_array['txt_en'][36] = 'Annual holiday entitlement of the employee.';

	$install_array['txt_de'][37] = 'Sollarbeitszeit pro Woche';
	$install_array['txt_en'][37] = 'Regular working hours per week';

	$install_array['txt_de'][38] = 'Sollarbeitszeit in Stunden pro Woche. Werden weniger Stunden je Woche gearbeitet, werden diese als Fehlstunden gerechnet.';
	$install_array['txt_en'][38] = 'Regular working hours per week. If the actual achieved hours fall below this value, the missing hours will be counted as miss-out time.';

	$install_array['txt_de'][39] = 'Maximale Arbeitszeit pro Woche';
	$install_array['txt_en'][39] = 'Maximum working hours per week';

	$install_array['txt_de'][40] = 'H&ouml;chststundenzahl je Woche. Werden mehr Stunden pro Woche gearbeitet, werden diese als &uuml;berstunden gerechnet.';
	$install_array['txt_en'][40] = 'Maximum working hours per week. If the actual achieved hours exceed this value, the additional hours will be counted as overtime.';

	$install_array['txt_de'][41] = 'Startdatum';
	$install_array['txt_en'][41] = 'Start date';

	$install_array['txt_de'][42] = 'Ab dem angegebenen Datum kann der Mitarbeiter Stundenzettel ausf&uuml;llen.';
	$install_array['txt_en'][42] = 'The employee is allowed to start editing of his time sheet from the specified date.';

	$install_array['txt_de'][43] = 'Benutzergruppe';
	$install_array['txt_en'][43] = 'Usergroup';

	$install_array['txt_de'][44] = 'Benutzername';
	$install_array['txt_en'][44] = 'Username';

	$install_array['txt_de'][45] = 'Neues Passwort';
	$install_array['txt_en'][45] = 'New password';

	$install_array['txt_de'][46] = 'Sprache';
	$install_array['txt_en'][46] = 'Language';

	$install_array['txt_de'][47] = 'Die Datenbank wurde angelegt!';
	$install_array['txt_en'][47] = 'The database was successfully created!';

	$install_array['txt_de'][48] = 'Die Datei [abs_pfad]config/config.inc.php" konnte nicht beschrieben werden!';
	$install_array['txt_en'][48] = 'The file [abs_pfad]config/config.inc.php" could not be written!';

	$install_array['txt_de'][49] = 'Die Tabellen konnten nicht erstellt werden!<br />[mysql_errno]: [mysql_error]';
	$install_array['txt_en'][49] = 'The database tables could not be created!<br />[mysql_errno]: [mysql_error]';

	$install_array['txt_de'][50] = 'Keine Verbindung zur Datenbank!';
	$install_array['txt_en'][50] = 'No connection to the database!';

	$install_array['txt_de'][51] = 'Der absolute Pfad ist falsch oder es sind nicht alle Dateien/Verzeichnisse auf dem Server vorhanden!';
	$install_array['txt_en'][51] = 'The absolute path is incorrect or there are not all files/folders available on the server!';

	$install_array['txt_de'][52] = 'Die Pfadangaben zu mysql bzw. mysqldump sind falsch!';
	$install_array['txt_en'][52] = 'The path-values to mysql and mysqldump respectively are incorrect!';

	$install_array['txt_de'][53] = 'Geben Sie f&uuml;r die Passwortl&auml;nge bzw. Inaktivit&auml;tsdauer eine Zahl an!';
	$install_array['txt_en'][53] = 'Please enter the values for minimum passwordlength and maximum user idle time!';

	$install_array['txt_de'][54] = 'Bitte alle Pflichtfelder ausf&uuml;llen!';
	$install_array['txt_en'][54] = 'Please fill out all required inputfields!';

	$install_array['txt_de'][55] = 'Der Datensatz wurde gespeichert.';
	$install_array['txt_en'][55] = 'The data record was successfully saved.';

	$install_array['txt_de'][56] = 'Es konnte kein Stundenzettel angelegt werden!';
	$install_array['txt_en'][56] = 'The timesheet could not be created!';

	$install_array['txt_de'][57] = 'Der Benutzer konnte nicht angelegt werden!';
	$install_array['txt_en'][57] = 'The useraccount could not be created!';

	$install_array['txt_de'][58] = 'Der angegebene Benutzername existiert bereits! Bitte w&auml;hlen Sie einen anderen Benutzernamen!';
	$install_array['txt_en'][58] = 'This username already exists! Please choose another one!';

	$install_array['txt_de'][59] = 'Die Datei /onfig/cryptpw_salt.php konnte nicht erstellt werden! Bitte &auml;ndern Sie gegebenenfalls vorr&uuml;bergehend die CHMOD Einstellungen f&uuml;r das Verzeichnis /inc/!';
	$install_array['txt_en'][59] = 'The file /config/cryptpw_salt.php could not be created! Please change temporarily the CHMOD settings for the /inc/ directoy, if necessary!';

	$install_array['txt_de'][60] = 'F&uuml;r die Darstellung wird ein Iconset von "FAMFAMFAM" ben&ouml;tigt. Sie k&ouml;nnen dieses unter http://www.famfamfam.com/lab/icons/silk/ in Version 1.3 downloaden. Kopieren Sie die Icons in den Ordner "template/default/pic/famfamfam/" und starten Sie die Installation neu.';
	$install_array['txt_en'][60] = 'The application requires the "FAMFAMFAM Silk Icons" iconset. Version 1.3 of this iconset is available at http://www.famfamfam.com/lab/icons/silk/ for download. Copy the icons into the "template/default/pic/famfamfam/" directory and restart the installation.';

	$install_array['txt_de'][61] = 'F&uuml;r die PDF-Erstellung wird die Klasse "tcpdf" ben&ouml;tigt. Sie k&ouml;nnen diese unter http://sourceforge.net/projects/tcpdf/ in Version 5.9.* downloaden. Kopieren Sie die Klasse inkl. font Ordner in den Ordner "inc/class/tcpdf/" und Starten Sie die Installation neu.';
	$install_array['txt_en'][61] = 'The class "tcpdf" is required for the creation of PDF files. Version 5.9.* is available at http://sourceforge.net/projects/tcpdf/ for download.';

	$install_array['txt_de'][62] = 'F&uuml;r den Mailversand wird die Klasse "PHPMailer" ben&ouml;tigt. Sie k&ouml;nnen diese unter http://sourceforge.net/projects/phpmailer/ in Version 5.1 downloaden. Kopieren Sie die Klassen in den Ordner "inc/classes/PHPMailer/" und starten Sie die Installation neu.';
	$install_array['txt_en'][62] = 'The "PHPMailer" class is required for sending E-Mails. It is downloadable at http://sourceforge.net/projects/phpmailer/ in Version 5.1. Copy all PHPMailer files and classes into the "inc/classes/PHPMailer/" subdirectory and restart the installation.';

	$install_array['txt_de'][63] = 'F&uuml;r die ZIP-Archiv Erstellung wird die Klasse "PclZip" ben&ouml;tigt. Sie k&ouml;nnen diese unter http://www.phpconcept.net in Version 2.8.* downloaden. Kopieren Sie die Klassen in den Ordner "inc/classes/zip/" und starten Sie die Installation neu.';
	$install_array['txt_en'][63] = 'The "PclZip" class is required for the creation of zip archives. It is downloadable at http://www.phpconcept.net in Version 2.8.*. Copy all PclZip files and classes into the "inc/classes/zip/" subdirectory and restart the installation.';

	$install_array['txt_de'][64] = 'Beachten Sie bitte die Lizenzbedingungen!';
	$install_array['txt_en'][64] = 'Please regard the terms of the licenses!';

	$install_array['txt_de'][65] = 'Das "[dir]" Verzeichnis wurde nicht gefunden! Laden Sie alle Verzeichnisse und Dateien auf Ihren Server!';
	$install_array['txt_en'][65] = 'The "[dir]" directory could not be found! Please upload all directories and files to your server!';

	$install_array['txt_de'][66] = 'Die Installation ist abgeschlossen.';
	$install_array['txt_en'][66] = 'Installation complete.';

	$install_array['txt_de'][67] = 'Bitte L&ouml;schen Sie die install.php und die FOM.sql Datei auf Ihrem Server.';
	$install_array['txt_en'][67] = 'Please delete the install.php and the FOM.sql file from your server.';

	$install_array['txt_de'][68] = 'Zum Schutz Ihrer Daten sollten Sie einen ".htaccess" Verzeichnisschutz auf den Order "files/" einrichten!';
	$install_array['txt_en'][68] = 'It is strongly recommended to establish a ".htaccess" security restriction on the "files/" directory to protect your data!';

	$install_array['txt_de'][69] = 'Zur Loginseite gelangen Sie &uuml;ber diesen Link. [Zur Loginseite]';
	$install_array['txt_en'][69] = 'Use this link to get to the login page. [To login page]';

	$install_array['txt_de'][70] = 'Lesen Sie nach dem Login unbedingt die Hilfe!';
	$install_array['txt_en'][70] = 'It is strongly recommended to read the instructions within the online-documentation!';

	$install_array['txt_de'][71] = 'Einige Funktionen dieser Anwendung erfordern einen Cronjob. Bitte richten Sie einen Cronjob ein, der 4 mal pro Stunde die Datei cj/cj.php ausf&uuml;hrt!';
	$install_array['txt_en'][71] = 'A cronjob is required for some functions of this application. Please configure a cronjob which executes the file cj/cj.php four times per hour!';

	$install_array['txt_de'][72] = 'MySQL-Port';
	$install_array['txt_en'][72] = 'MySQL-Port';

	$install_array['txt_de'][73] = 'z.B. 3306';
	$install_array['txt_en'][73] = 'e.g. 3306';

	$install_array['txt_de'][74] = 'MySQL-Socket';
	$install_array['txt_en'][74] = 'MySQL-Socket';

	$install_array['txt_de'][75] = 'z.B. :/mysql/mysql.sock';
	$install_array['txt_en'][75] = 'e.g. :/mysql/mysql.sock';

	$install_array['txt_de'][76] = 'Sie ben&ouml;tigen PHP 5 f&uuml;r eine Installation.';
	$install_array['txt_en'][76] = 'You need PHP 5 for an installation.';

	$install_array['txt_de'][77] = 'Maximale L&auml;nge von Verzeichnisnamen';
	$install_array['txt_en'][77] = 'Maximum length of foldernames';

	$install_array['txt_de'][78] = 'Maximale L&auml;nge von Dateinamen';
	$install_array['txt_en'][78] = 'Maximum length of filenames';

	$install_array['txt_de'][79] = 'ISO 9660 Level 1 erlaubt f&uuml;r Dateinamen (inkl. Dateiendung) 12 Zeichen und f&uuml;r Verzeichnisnamen 8 Zeichen.';
	$install_array['txt_en'][79] = 'ISO 9660 Level 1 allows for filenames (incl. extension) 12 Characters and for directories 8 Characters.';

	$install_array['txt_de'][80] = 'ISO 9660 Level 2 erlaubt f&uuml;r Dateinamen (inkl. Dateiendung) 31 Zeichen und f&uuml;r Verzeichnisnamen 31 Zeichen.';
	$install_array['txt_en'][80] = 'ISO 9660 Level 2 allows for filenames (incl. extension) 31 Characters and for directories 31 Characters.';

	$install_array['txt_de'][81] = 'Bitte geben Sie die maximale L&auml;nge f&uuml;r Dateinamen und Verzeichnisse an.';
	$install_array['txt_en'][81] = 'Please specify the maximum string length for filenames and directories.';

	$install_array['txt_de'][82] = 'h&ouml;chstens 250 Zeichen';
	$install_array['txt_en'][82] = 'max. 250 characters';

	$install_array['txt_de'][83] = 'TT.MM.JJJJ';
	$install_array['txt_en'][83] = 'DD.MM.YYYY';

	$install_array['txt_de'][84] = 'MM/TT/JJJJ';
	$install_array['txt_en'][84] = 'MM/DD/YYYY';

	$install_array['txt_de'][85] = 'Uploadverzeichnis des Servers';
	$install_array['txt_en'][85] = 'Upload directory of the server';

	$install_array['txt_de'][86] = 'Absoluter Pfad zum Verzeichnis mit Ausf&uuml;hrungsrechten';
	$install_array['txt_en'][86] = 'Absolute path to directory with execute authorization';

	$install_array['txt_de'][87] = 'Zum Beispiel "/tmp/"';
	$install_array['txt_en'][87] = 'e.g. "/tmp/"';

	$install_array['txt_de'][88] = 'Fehler beim Speichern der Konfigurationsdaten!"';
	$install_array['txt_en'][88] = 'Error while saving configuration data!"';

	$install_array['txt_de'][89] = 'F&uuml;r die PDF-Erstellung wird die Klasse "tcpdf" ben&ouml;tigt. Sie k&ouml;nnen diese unter http://www.tcpdf.org in Version 5.9.* downloaden. Kopieren Sie die Klasse in den Ordner "inc/class/tcpdf/" und starten Sie die Installation neu.';
	$install_array['txt_en'][89] = 'The class "tcpdf" is required for the creation of PDF files. Version 5.9.* is available at http://www.tcpdf.org for download. Copy all files in the folder "inc/class/tcpdf/" and restart the installation.';

	$install_array['txt_de'][90] = 'F&uuml;r die Datei oder das Verzeichnis "[file]" werden Schreibrechte ben&ouml;tigt.';
	$install_array['txt_en'][90] = 'Write permission is required for the following file or folder "[file]".';

	$install_array['txt_de'][91] = 'F&uuml;r die Datei oder das Verzeichnis "[file]" sollte das Schreibrecht entfernt werden.';
	$install_array['txt_en'][91] = 'Please remove write permission for the following file or folder "[file]".';

	$install_array['txt_de'][92] = 'F&uuml;r den Mehrfach-Dateiupload wird das Paket "Plupload" ben&ouml;tigt. Sie k&ouml;nnen dieses unter http://www.plupload.com in Version 1.5.* herunterladen. Entpacken Sie die zip-Datei in den Ordner "inc/classes/plupload/" und starten Sie die Installation neu.';
	$install_array['txt_en'][92] = 'The package "Plupload" is required for the multiple fileupload function. Version 1.5.* is available at http://www.plupload.com';

	$install_array['txt_de'][93] = 'Fehlermeldung';
	$install_array['txt_en'][93] = 'Error Message';

	$install_array['txt_de'][94] = 'Erfolgsmeldungen';
	$install_array['txt_en'][94] = 'Good news';

	$install_array['txt_de'][95] = 'Diesen Schritt wiederholen';
	$install_array['txt_en'][95] = 'Repeat this step';

	$install_array['txt_de'][96] = 'PHP Einstellungen';
	$install_array['txt_en'][96] = 'PHP Settings';

	$install_array['txt_de'][97] = 'MySQL Einstellungen';
	$install_array['txt_en'][97] = 'MySQL Settings';

	$install_array['txt_de'][98] = 'FOM Einstellungen';
	$install_array['txt_en'][98] = 'FOM Settings';

	$install_array['txt_de'][99] = 'Hinweis';
	$install_array['txt_en'][99] = 'Notice';

	$install_array['txt_de']['date_format'] = 'Datumsformat';
	$install_array['txt_en']['date_format'] = 'Date format';

	$install_array['txt_de']['date_format_desc'] = 'Bitte geben Sie an, in welchem Format Datumsangaben dargestellt werden sollen.';
	$install_array['txt_en']['date_format_desc'] = 'Please select the date format for the output of date values.';

	$install_array['txt_de']['db_install'] = 'Bitte geben Sie die Zugangsdaten f&uuml;r eine existierende MySQL-Datenbank an.';
	$install_array['txt_en']['db_install'] = 'Please specify access data for an existing MySQL database.';

	$install_array['txt_de']['useraccount_admin'] = 'Sie m&uuml;ssen ein Benutzerkonto mit Administratorrechten anlegen. Bitte geben Sie die gew&uuml;nschten Zugangsdaten ein:';
	$install_array['txt_en']['useraccount_admin'] = 'You have to create an administrator account. Please specify login details:';

	$install_array['txt_de']['main_language_desc'] = 'Bitte w&auml;hlen Sie die Voreinstellung f&uuml;r die Hauptsprache der Anwendung, insbesondere der Benutzeroberfl&auml;che und Systemmeldungen.<br />Unabh&auml;ngig von dieser Voreinstellung kann f&uuml;r jeden Benutzer eine pers&ouml;nliche Spracheinstellung festgelegt werden.';
	$install_array['txt_en']['main_language_desc'] = 'Please select the default main language for the application, especially graphical user interface and system messages.<br />Independent from this default setting it is possible to define a personal language for each user.';

	$install_array['txt_de']['main_language'] = 'Hauptsprache der Anwendung';
	$install_array['txt_en']['main_language'] = 'Application main language';

	$install_array['txt_de']['contact_desc'] = 'Bitte geben Sie die Kontaktdaten f&uuml;r einen Ansprechpartner an, an den sich Anwender bei Problemen wenden k&ouml;nnen.';
	$install_array['txt_en']['contact_desc'] = 'Please specify a contact person, which is responsible for user questions.';

	$install_array['txt_de']['tel'] = 'Telefon';
	$install_array['txt_en']['tel'] = 'Phone';

	$install_array['txt_de']['handy'] = 'Mobil';
	$install_array['txt_en']['handy'] = 'Mobile phone';

	$install_array['txt_de']['required_classes'] = 'Verschiedene Funktionen von File-O-Meter ben&ouml;tigen externe Klassen und Pakete. Folgende Klassen/Pakete werden verwendet:<br /><br />';
	$install_array['txt_de']['required_classes'] .= '<strong>PEAR Package</strong> - PEAR ist erh&auml;ltlich unter <a href="http://pear.php.net/" target="_blank">http://pear.php.net/</a><br />';
	$install_array['txt_de']['required_classes'] .= '<strong>PHP-ExcelReader</strong> - PHP-ExcelReader ist erh&auml;ltlich unter: <a href="https://sourceforge.net/projects/phpexcelreader/" target="_blank">https://sourceforge.net/projects/phpexcelreader/</a> Bitte beachten Sie unbedingt die Hinweise zur Installation im Sourceforge-Forum.<br />';
	$install_array['txt_de']['required_classes'] .= '<strong>Plupload</strong> - Plupload ist erh&auml;ltlich unter <a href="http://www.plupload.com/" target="_blank">http://www.plupload.com/</a><br />';

	$install_array['txt_en']['required_classes'] = 'File-O-Meter uses several external classes and packages. The following classes/packages are used:<br /><br />';
	$install_array['txt_en']['required_classes'] .= '<strong>PEAR package</strong> - PEAR is available here: http://pear.php.net/<br />';
	$install_array['txt_en']['required_classes'] .= '<strong>PHP-ExcelReader</strong> - PHP-ExcelReader is available here: https://sourceforge.net/projects/phpexcelreader/ Please visit the Sourceforge forum and pay attention to installation notes!<br />';
	$install_array['txt_en']['required_classes'] .= '<strong>Plupload</strong> - Plupload is available here: <a href="http://www.plupload.com/" target="_blank">http://www.plupload.com/</a><br />';


	//Verzeichnisse
	$install_array['external_files_exists'] = array('FAMFAMFAM'			=> 'template/default/pic/famfamfam/add.png',
													'PHPMailer_class'	=> 'inc/class/PHPMailer/class.phpmailer.php',
													'PHPMailer_pop'		=> 'inc/class/PHPMailer/class.pop3.php',
													'PHPMailer_smtp'	=> 'inc/class/PHPMailer/class.smtp.php',
													'tcpdf'				=> 'inc/class/tcpdf/tcpdf.php',
													'pclzip'			=> 'inc/class/zip/pclzip.lib.php',
													'plupload_1'		=> 'inc/class/plupload/js/plupload.js'
													);

	$install_array['external_files_message'] = array('FAMFAMFAM'	=> $install_array[$lng_txt][60]/*Fuer die Darstellung wird ein Iconset von "FAMFAMFAM" benuetigt. Sie koennen dieses unter http://www.famfamfam.com/lab/icons/silk/ in Version 1.3 downloaden. Kopieren Sie die Icons in den Ordner "template/default/pic/famfamfam/" und Starten Sie die Installation neu.*/,
													'tcpdf'			=> $install_array[$lng_txt][89]/*Für den Mailversand wird die Klasse "PHPMailer" benötigt. Sie können diese unter http://sourceforge.net/projects/phpmailer/ in Version 2.3 downloaden. Kopieren Sie die Klassen in den Ordner "inc/classes/PHPMailer/" und starten Sie die Installation neu.*/,
													'PHPMailer'		=> $install_array[$lng_txt][62]/*Fuer den Mailversand wird die Klasse "PHPMailer" benoetigt. Sie koennen diese unter http://sourceforge.net/projects/phpmailer/ in Version 2.3 downloaden. Kopieren Sie die Klassen in den Ordner "inc/classes/PHPMailer/" und Starten Sie die Installation neu.*/,
													'pclzip'		=> $install_array[$lng_txt][63]/*Fuer die ZIP Archiv erstellung wir die Klasse "PclZip" benoetigt. Sie koennen diese unter http://www.phpconcept.net in Version 2.6 downloaden. Kopieren Sie die Klassen in den Ordner "inc/classes/zip/" und Starten Sie die Installation neu.*/,
													'plupload'		=> $install_array[$lng_txt][92]/*The package "Plupload" is required for the multiple fileupload function. Version 1.2.3 is available at http://www.plupload.com*/,
													'all'			=> $install_array[$lng_txt][64]/*Beachten Sie bitte die Lizenzbedingungen des/der Anbieter/s!*/);

	$install_array['dir_exists'] = array(
											'cj/',
											'config/',
											'files/',
											'files/backup/',
											'files/imex/',
											'files/log/',
											'files/logo/',
											'files/tmp/',
											'files/upload/',
											'folder/',
											'inc/',
											'inc/class/',
											'project/',
											'report/',
											'setup/',
											'template/',
											'template/default/',
											'template/default/pic/',
											'template/default/pic/famfamfam/',
											'user/',
											'user_group/',
											'web_services/'
											);

	//Jobs
	if (isset($_POST['job_string']))
	{
		$message_array = array();

		//config.inc.php beschreiben
		if ($_POST['job_string'] == 'add_config')
		{

			//alle Postindexwerte
			$post_index_array = $install_array['config_post_index'];

			$error_variable = false;
			$error_int = false;

			if (!isset($_POST['tmp_upload_dir']) or empty($_POST['tmp_upload_dir']))		{$error_variable = true;}

			for ($i = 0; $i < count($post_index_array); $i++)
			{
				//POST ist da und hat einen wert
				if (isset($_POST[$post_index_array[$i]]))
				{
					$_POST[$post_index_array[$i]] = trim($_POST[$post_index_array[$i]]);
					//DB Port
					if ($post_index_array[$i] == 'db_port')
					{
						if (!empty($_POST[$post_index_array[$i]]))
						{
							//Bei 3306 leer lassen
							if ($_POST[$post_index_array[$i]] == '3306' or $_POST[$post_index_array[$i]] == ':3306')
							{
								$_POST[$post_index_array[$i]] = '';
							}

							//Doppelpunkt am anfang entfernen
							if (substr($_POST[$post_index_array[$i]], 0, 1) == ':')
							{
								$_POST[$post_index_array[$i]] = susbtr($_POST[$post_index_array[$i]], 1);
							}
						}
					}
					//DB Socket
					elseif ($post_index_array[$i] == 'db_socket')
					{
						if (isset($_POST['db_server']) and !empty($_POST[$post_index_array[$i]]))
						{
							$server_len = strlen($_POST['db_server']);

							//eventuell vorhandenen localhost oder 127.0.0.1 entfernen
							if ($_POST['db_server'] == substr($_POST[$post_index_array[$i]], 0 ,$server_len))
							{
								$_POST[$post_index_array[$i]] = substr($_POST[$post_index_array[$i]], $server_len);
							}
						}
						else
						{
							$_POST[$post_index_array[$i]] = '';
						}
					}
					elseif (!empty($_POST[$post_index_array[$i]]) or $_POST[$post_index_array[$i]] == "0")
					{
						//sind int werte
						if ($post_index_array[$i] == 'sessiontime')
						{
							if (!isset($_POST[$post_index_array[$i]]) or !is_numeric($_POST[$post_index_array[$i]]))
							{
								$error_int = true;
							}
						}
						//maximale stringlaenge fuer dateinamen und verzeichnisse
						if ($post_index_array[$i] == 'maxlength_folder' or $post_index_array[$i] == 'maxlength_file')
						{
							if (!isset($_POST[$post_index_array[$i]]) or !is_numeric($_POST[$post_index_array[$i]]))
							{
								$error_int = true;
							}
							elseif ($_POST[$post_index_array[$i]] > 250)
							{
								$_POST[$post_index_array[$i]] = 250;
							}
						}
						//bei pfadangaben gegebenenfalls / ans ende schreiben
						if ($post_index_array[$i] == 'url')
						{
							if (substr($_POST[$post_index_array[$i]], -1) != '/')
							{
								$_POST[$post_index_array[$i]] .= '/';
							}
						}
						//bei pfadangaben gegebenenfalls / ans ende schreiben
						if ($post_index_array[$i] == 'abspfad')
						{
							$_POST[$post_index_array[$i]] = str_replace('\\', '/', $_POST[$post_index_array[$i]]);
							$_POST[$post_index_array[$i]] = str_replace('//', '/', $_POST[$post_index_array[$i]]);

							if (substr($_POST[$post_index_array[$i]], -1) != '/')
							{
								$_POST[$post_index_array[$i]] .= '/';
							}
						}
					}
					//Mysql bzw. Mysqldump sind keine Pflichtfelder
					elseif ($post_index_array[$i] == 'mysql' or $post_index_array[$i] == 'mysqldump')
					{
						$_POST[$post_index_array[$i]] = '';
					}
					else
					{
						$error_variable = true;
					}
					//\entfernen
					if (get_magic_quotes_gpc() == 1)
					{
						$_POST[$post_index_array[$i]] = stripcslashes($_POST[$post_index_array[$i]]);
					}
				}
				else
				{
					$error_variable = true;
				}
			}

			if ($error_variable == false)
			{
				if ($error_int == false)
				{
					//mysql und mysqldump verwenden
					if (!empty($_POST['mysql']) and !empty($_POST['mysqldump']))
					{
						if (file_exists($_POST['mysql']) and file_exists($_POST['mysqldump']))
						{
							$dump_file_exsists = true;
						}
						else
						{
							$dump_file_exsists = false;
						}

					}
					else
					{
						$dump_file_exsists = true;
					}

					if ($dump_file_exsists)
					{
						if (file_exists($_POST['abspfad'].'config/') and file_exists($_POST['abspfad'].'FOM.sql'))
						{
							//MySQL Socket verwenden
							if (isset($_POST['db_socket']) and !empty($_POST['db_socket']))
							{
								if (substr($_POST['db_socket'], 0, 1) == ':')
								{
									$db_server = $_POST['db_server'].$_POST['db_socket'];
								}
								else
								{
									$_POST['db_socket'] = ':'.$_POST['db_socket'];
									$db_server = $_POST['db_server'].$_POST['db_socket'];
								}
							}
							//MySQL Port verwenden
							elseif (isset($_POST['db_port']) and !empty($_POST['db_port']))
							{
								if (substr($_POST['db_port'], 0, 1) == ':')
								{
									$_POST['db_port'] = substr($_POST['db_port'], 1);
									$db_server = $_POST['db_server'].':'.$_POST['db_port'];
								}
								else
								{
									$db_server = $_POST['db_server'].':'.$_POST['db_port'];
								}
							}
							else
							{
								$db_server = $_POST['db_server'];
							}

							$mysql_h = @mysql_connect($db_server, $_POST['db_user'], $_POST['db_pw']);
							if($mysql_h)
							{
								$db = @mysql_select_db($_POST['db_name'], $mysql_h);
								if ($db)
								{
									if ($h = @fopen($_POST['abspfad'].'config/config.inc.php', 'w'))
									{
										fwrite($h, "<?php\n");

										//php fehlermeldungen
										if ($_POST['php_error'] == 1)
										{
											fwrite($h, 'ini_set(\'error_reporting\', E_ALL);'."\n");
											fwrite($h, 'ini_set(\'display_errors\', 1);'."\n");
											fwrite($h, 'ini_set(\'display_startup_errors\', 1);'."\n");
											fwrite($h, 'ini_set(\'docref_root\', \'http://www.de.php.net/manual/de/\');'."\n");
											fwrite($h, 'ini_set(\'error_prepend_string\', \'<br /><b>FOM_Error:</b>\');'."\n");
										}
										else
										{
											fwrite($h, 'ini_set(\'error_reporting\', 0);'."\n");
											fwrite($h, 'ini_set(\'display_errors\', 0);'."\n");
											fwrite($h, 'ini_set(\'display_startup_errors\', 0);'."\n");
										}


										fwrite($h, 'ini_set(\'default_charset\', \'UTF-8\');'."\n");

										//Zeitzone
										fwrite($h, 'date_default_timezone_set(\''.$_POST['time_zone'].'\');'."\n");

										//DB Einstellungen
										fwrite($h, 'define(\'FOM_DB_PORT\', \''.$_POST['db_port'].'\');'."\n");
										fwrite($h, 'define(\'FOM_DB_SOCKET\', \''.$_POST['db_socket'].'\');'."\n");
										fwrite($h, 'define(\'FOM_DB_SERVER\', \''.$_POST['db_server'].'\');'."\n");
										fwrite($h, 'define(\'FOM_DB_USER\', \''.$_POST['db_user'].'\');'."\n");
										fwrite($h, 'define(\'FOM_DB_PW\', \''.$_POST['db_pw'].'\');'."\n");
										fwrite($h, 'define(\'FOM_DB_NAME\', \''.$_POST['db_name'].'\');'."\n");
										//Pfadinvormationen fuer MySql
										fwrite($h, 'define(\'FOM_MYSQL_EXEC\', \''.$_POST['mysql'].'\');'."\n");
										fwrite($h, 'define(\'FOM_MYSQL_DUMP\', \''.$_POST['mysqldump'].'\');'."\n");
										//Absoluter Pfad
										fwrite($h, 'define(\'FOM_ABS_URL\', \''.$_POST['url'].'\');'."\n");
										fwrite($h, 'define(\'FOM_ABS_PFAD\', \''.$_POST['abspfad'].'\');'."\n");
										//Session
										fwrite($h, 'define(\'FOM_SESSION_NAME\', \''.$_POST['cookiename'].'\');'."\n");
										fwrite($h, 'define(\'FOM_SESSION_MAX_LIFE\', \''.$_POST['sessiontime'].'\');'."\n");
										// Maximale Stringlaengen fuer Dateinamen und Verzeichnisse
										fwrite($h, 'define(\'FOM_MAX_LENGTH_FOLDER\', \''.$_POST['maxlength_folder'].'\');'."\n");
										fwrite($h, 'define(\'FOM_MAX_LENGTH_FILE\', \''.$_POST['maxlength_file'].'\');'."\n");
										//Uploadverzeichnis des Servers
										fwrite($h, 'define(\'TMP_UPLOAD_DIR\', \''.$_POST['tmp_upload_dir'].'\');'."\n");

										fwrite($h, "?>");
										fclose($h);

										@chmod($_POST['abspfad'].'config/config.inc.php', 0744);

										if (file_exists($_POST['abspfad'].'config/config.inc.php') and filesize($_POST['abspfad'].'config/config.inc.php') > 0)
										{
											require_once($_POST['abspfad'].'config/config.inc.php');
											require_once(FOM_ABS_PFAD.'inc/error_handler.php');
											require_once(FOM_ABS_PFAD.'inc/class/MySql.php');
											require_once(FOM_ABS_PFAD.'inc/class/MySqlBackup.php');

											$msb = new MySqlBackup();

											if (!empty($_POST['mysql']) and !empty($_POST['mysqldump']))
											{
												$restore_dump = $msb->restore_dump_with_mysqlexec($_POST['abspfad'].'FOM.sql');
											}
											else
											{
												$restore_dump = $msb->restore_dump_without_mysqlexec($_POST['abspfad'].'FOM.sql');
											}

											if ($restore_dump)
											{
												$message_array['ok'][] = $install_array[$lng_txt][47];//Die Datenbank wurde angelegt!
											}
											else
											{
												$_GET['step'] = 1;
												$message_array['error'][] = str_replace(array('[mysql_errno]', '[mysql_error]'), array(mysql_errno(), mysql_error()), $install_array[$lng_txt][49]);//'Die Tabellen konnten nicht erstellt werden!<br />'.mysql_errno().': '.mysql_error();
											}
										}
										else
										{
											$_GET['step'] = 1;
											$message_array['error'][] = str_replace('[abs_pfad]', $_POST['abspfad'], $install_array[$lng_txt][48]);//Die Datei "'.$_POST['abspfad'].'config/config.inc.php" konnte nicht beschrieben werden!
										}
									}
									else
									{
										$_GET['step'] = 1;
										$message_array['error'][] = str_replace('[abs_pfad]', $_POST['abspfad'], $install_array[$lng_txt][48]);//Die Datei "'.$_POST['abspfad'].'config/config.inc.php" konnte nicht beschrieben werden!
									}
								}
								else
								{
									$_GET['step'] = 1;
									$message_array['error'][] = $install_array[$lng_txt][50];//Keine Verbindung zur Datenbank!
								}
							}
							else
							{
								$_GET['step'] = 1;
								$message_array['error'][] = $install_array[$lng_txt][50];//Keine Verbindung zur Datenbank!
							}
						}
						else
						{
							$_GET['step'] = 1;
							$message_array['error'][] = $install_array[$lng_txt][51];//Der Absolute Pfad ist Falsch oder es sind nicht alle Dateien/Verzeichnisse auf dem Server vorhanden!
						}
					}
					else
					{
						$_GET['step'] = 1;
						$message_array['error'][] = $install_array[$lng_txt][52];//Die Pfadangaben zu mysql bzw. mysqldump sind falsch!
					}
				}
				else
				{
					$_GET['step'] = 1;
					$message_array['error'][] = $install_array[$lng_txt][53];//Geben Sie fuer die Passwortlaenge bzw. Inaktivitaetsdauer eine Zahl an!
				}
			}
			else
			{
				$_GET['step'] = 1;
				$message_array['error'][] = $install_array[$lng_txt][54];//Bitte alle Pflichtfelder ausfuellen!
			}
		}
		elseif($_POST['job_string'] == 'add_user')
		{
			define('FOM_LOGIN_SITE','true');
			require_once($install_array['include_pfad']);

			$cp = new CryptPw;

			$create_salt_array = $cp->get_salt_array();

			if (count($create_salt_array) > 0)
			{
				//Kontaktdaten in Setup speichern
				$kontakt_array = array();

				if (isset($_POST['kontakt_vorname_string']) and !empty($_POST['kontakt_vorname_string']))
				{
					$kontakt_array['first_name'] = mysql_real_escape_string($_POST['kontakt_vorname_string']);
				}
				else
				{
					$kontakt_array['first_name'] = '';
				}

				if (isset($_POST['kontakt_nachname_string']) and !empty($_POST['kontakt_nachname_string']))
				{
					$kontakt_array['last_name'] = mysql_real_escape_string($_POST['kontakt_nachname_string']);
				}
				else
				{
					$kontakt_array['last_name'] = '';
				}

				if (isset($_POST['kontakt_mail_string']) and !empty($_POST['kontakt_mail_string']))
				{
					$kontakt_array['email'] = mysql_real_escape_string($_POST['kontakt_mail_string']);
				}
				else
				{
					$kontakt_array['email'] = '';
				}

				if (isset($_POST['kontakt_tel_string']) and !empty($_POST['kontakt_tel_string']))
				{
					$kontakt_array['phone'] = mysql_real_escape_string($_POST['kontakt_tel_string']);
				}
				else
				{
					$kontakt_array['phone'] = '';
				}

				if (isset($_POST['kontakt_handy_string']) and !empty($_POST['kontakt_handy_string']))
				{
					$kontakt_array['handy'] = mysql_real_escape_string($_POST['kontakt_handy_string']);
				}
				else
				{
					$kontakt_array['handy'] = '';
				}


				$pflichtfeld = 0;

				if (!isset($_POST['vorname_string']) or empty($_POST['vorname_string']))		{$pflichtfeld++;}
				if (!isset($_POST['nachname_string']) or empty($_POST['nachname_string']))		{$pflichtfeld++;}
				if (!isset($_POST['mail_string']) or empty($_POST['mail_string']))				{$pflichtfeld++;}
				if (!isset($_POST['username_string']) or empty($_POST['username_string']))		{$pflichtfeld++;}
				if (!isset($_POST['pw_string']) or empty($_POST['pw_string']))					{$pflichtfeld++;}
				if (!isset($_POST['main_language_id']) or empty($_POST['main_language_id']))	{$pflichtfeld++;}

				if ($pflichtfeld == 0)
				{
					$sql = $cdb->select("SELECT user_id FROM fom_user WHERE LOWER(loginname)='".strtolower($_POST['username_string'])."'");
					$result = $cdb->fetch_array($sql);

					if (!isset($result['user_id']) or empty($result['user_id']))
					{

						$new_pw_sql = $cp->encode_pw($_POST['pw_string']);

						if ($cdb->insert("INSERT INTO fom_user (vorname, nachname, email, loginname, pw, language_id) VALUES ('".$_POST['vorname_string']."','".$_POST['nachname_string']."','".$_POST['mail_string']."','".$_POST['username_string']."','$new_pw_sql','".$_POST['sprache_int']."')"))
						{
							if ($cdb->get_affected_rows() == 1)
							{
								$last_insert_id = $cdb->get_last_insert_id();

								if ($last_insert_id > 0)
								{
									if ($cdb->insert("INSERT INTO fom_user_membership VALUES ($last_insert_id, 1)"))
									{
										if ($cdb->update("UPDATE fom_setup SET main_language_id='".$_POST['main_language_id']."', contact='".serialize($kontakt_array)."' WHERE setup_id=1"))
										{
											$message_array['ok'][] = $install_array[$lng_txt][55];//Der Datensatz wurde gespeichert.
										}
										else
										{
											$_GET['step'] = 2;
											$message_array['error'][] = $install_array[$lng_txt][88];//Fehler beim Speichern der Konfigurationsdaten!
										}
									}
									else
									{
										$_GET['step'] = 2;
										$message_array['error'][] = $install_array[$lng_txt][57];//Der Benutzer konnte nicht angelegt werden!
									}
								}
								else
								{
									$_GET['step'] = 2;
									$message_array['error'][] = $install_array[$lng_txt][57];//Der Benutzer konnte nicht angelegt werden!
								}
							}
							else
							{
								$_GET['step'] = 2;
								$message_array['error'][] = $install_array[$lng_txt][57];//Der Benutzer konnte nicht angelegt werden!
							}
						}
						else
						{
							$_GET['step'] = 2;
							$message_array['error'][] = $install_array[$lng_txt][57];//Der Benutzer konnte nicht angelegt werden!
						}
					}
					else
					{
						$_GET['step'] = 2;
						$message_array['error'][] = $install_array[$lng_txt][58];//Der angegebene Benutzername existiert bereits! Bitte waehlen Sie einen anderen Benutzernamen!
					}
				}
				else
				{
					$_GET['step'] = 2;
					$message_array['error'][] = $install_array[$lng_txt][54];//Bitte fuellen Sie alle Pflichtfelder aus!;
				}
			}
			else
			{
				$_GET['step'] = 2;
				$message_array['error'][] = $install_array[$lng_txt][59];//Die Datei /inc/cryptpw_salt.php konnte nicht erstellt werden! Bitte aendern Sie gegebenenfalls vorruebergehend die CHMOD Einstellungen des Verzeichnisses /inc/!
			}
		}
	}
	if (isset($_GET['step']) and $_GET['step'] >= 2)
	{
		if (!defined('FOM_LOGIN_SITE'))
		{
			define('FOM_LOGIN_SITE', 'true');
		}
		require_once($install_array['include_pfad']);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>File-O-Meter Installation <?php echo $install_version; ?></title>
<link rel="stylesheet" media="screen" type="text/css" href="template/default/screen.css" />
</head>
<body>
<?php
	//Sprache auswaehlen
	if (!isset($_GET['lng']) and !isset($_GET['step']))
	{
?>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%">File-O-Meter Installation <?php echo $install_version; ?></td>
			</tr>
			<tr><td><img src="template/default/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td class="main_table_content">
					<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
						<tr valign="top">
							<td width="50%" align="right"><a href="?lng=txt_de&amp;step=1"><br /><u>Deutsche Installation</u></a>&nbsp;&nbsp;<br /><br /></td>
							<td width="50%">&nbsp;&nbsp;<a href="?lng=txt_en&amp;step=1"><br /><u>English Installation</u></a><br /><br /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
<?php
	}
	else
	{
		if (!isset($message_array))
		{
			$message_array = array();
		}
		//
		//Verzeichnise Pruefen anfang
		//
		$folder_file_error = false;

		//externe klassen Pruefen
		$phpmailer_error = false;
		$plupload_error = false;
		foreach ($install_array['external_files_exists'] as $i => $v)
		{
			if (!file_exists($v))
			{
				$folder_file_error = true;

				if (substr($i, 0, 9) == 'PHPMailer')
				{
					if (!$phpmailer_error)
					{
						$phpmailer_error = true;
						$message_array['error'][] = $install_array['external_files_message']['PHPMailer'];
					}
				}
				elseif (substr($i, 0, 8) == 'plupload')
				{
					if (!$plupload_error)
					{
						$plupload_error = true;
						$message_array['error'][] = $install_array['external_files_message']['plupload'];
					}
				}
				else
				{
					$message_array['error'][] = $install_array['external_files_message'][$i];
				}
			}
		}
		//allgemeiner Text
		if ($folder_file_error === true)
		{
			$message_array['error'][] = $install_array['external_files_message']['all'];
		}

		//standardverzeichnisse Pruefen
		for ($i = 0; $i < count($install_array['dir_exists']); $i++)
		{
			if (!file_exists($install_array['dir_exists'][$i]))
			{
				$folder_file_error = true;
				$message_array['error'][] = str_replace('[dir]', $install_array['dir_exists'][$i], $install_array[$lng_txt][65]);//Das "'.$install_array['dir_exists'][$i].'" Verzeichnis wurde nicht gefunden! Laden Sie alle Verzeichnisse und Dateien auf Ihren Server!
			}
			elseif (substr($install_array['dir_exists'][$i], 0, 5) == 'files')
			{
				@chmod($install_array['dir_exists'][$i], 0744);
			}
		}
		//
		//Verzeichnise Pruefen ende
		//

		//Schreibrechte Pruefen
		foreach ($install_array['writable_files'] as $file)
		{
			if (!is_writable($file))
			{
				if (file_exists($file))
				{
					@chmod($file, 0777);

					if (!is_writable($file))
					{
						$folder_file_error = true;
						$folder_file_error = true;
						$message_array['error'][] = str_replace('[file]', $file, $install_array[$lng_txt][90]);
					}
				}
			}
		}

		//PHP Version Pruefen
		if (intval(substr(PHP_VERSION, 0, 1)) < 5)
		{
			$message_array['error'][] = $install_array[$lng_txt][76];
		}

		//
		//Erfolgsmeldungen bzw. Fehlermeldungen ausgeben anfang
		//
		if (isset($message_array) and count($message_array) > 0)
		{
			if (isset($message_array['error']))
			{
?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%">
					<tr valign="middle">
						<td class="error_table_header" width="100%"><?php echo $install_array[$lng_txt][93];//Fehlermeldungen ?></td>
					</tr>
					<tr><td><img src="template/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
					<tr>
						<td class="error_table_content">
							<ul>
							<?php
								foreach ($message_array['error'] as $v)
								{
									echo '<li class="error">'.$v.'</li>';
								}
							?>
							</ul>
							<div style="width: 100%; text-align: center;"><a href="install.php<?php echo '?lng='.$_GET['lng'].'&step='.$_GET['step']; ?>"><?php echo $install_array[$lng_txt][95]; ?></a></div>
						</td>
					</tr>
				</table><br />
<?php
			}
			elseif (isset($message_array['ok']))
			{
?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%">
					<tr valign="middle">
						<td class="good_table_header" width="100%"><?php echo $install_array[$lng_txt][94]; ?></td>
					</tr>
					<tr><td><img src="template/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
					<tr>
						<td class="good_table_content">
							<ul>
							<?php
								foreach ($message_array['ok'] as $v)
								{
									echo '<li class="meldung">'.$v.'</li>';
								}
							?>
							</ul>
						</td>
					</tr>
				</table><br />
<?php
			}
		}
		//
		//Erfolgsmeldungen bzw. Fehlermeldungen ausgeben ende
		//

		//alle Verzeichnisse / Dateien vorhanden sind
		if ($folder_file_error === false)
		{
			//erster installationsschritt
			if (isset($_GET['step']) and $_GET['step'] == 1)
			{
				if (!isset($_POST['php_error']))
				{
					$_POST['php_error'] = 0;
				}
				if (!isset($_POST['time_zone']))
				{
					$_POST['time_zone'] = date_default_timezone_get();
				}
				if (!isset($_POST['db_server']))
				{
					$_POST['db_server'] = '';
				}
				if (!isset($_POST['db_port']))
				{
					$_POST['db_port'] = '';
				}
				if (!isset($_POST['db_socket']))
				{
					$_POST['db_socket'] = '';
				}
				if (!isset($_POST['db_user']))
				{
					$_POST['db_user'] = '';
				}
				if (!isset($_POST['db_pw']))
				{
					$_POST['db_pw'] = '';
				}
				if (!isset($_POST['db_name']))
				{
					$_POST['db_name'] = '';
				}
				if (!isset($_POST['mysql']))
				{
					$_POST['mysql'] = '';
				}
				if (!isset($_POST['mysqldump']))
				{
					$_POST['mysqldump'] = '';
				}
				if (!isset($_POST['url']))
				{
					$_POST['url'] = '';
					//Aufgerufene URL mit der in der Config vergleichen und gegebenenfalls anpassen
					if (isset($_SERVER['SCRIPT_URI']) and !empty($_SERVER['SCRIPT_URI']))
					{
						$_POST['url'] = $_SERVER['SCRIPT_URI'];
					}
					elseif(isset($_SERVER['HTTP_HOST']) and isset($_SERVER['REQUEST_URI']))
					{
						$http = 'http://';

						if (isset($_SERVER['HTTPS']) and !empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off')
						{
							$http = 'https://';
						}
						$_POST['url'] = $http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					}

					if (!empty($_POST['url']))
					{
						$_POST['url'] = dirname($_POST['url']);

						if (substr($_POST['url'], -1) != '/')
						{
							$_POST['url'] .= '/';
						}
					}
				}
				if (!isset($_POST['abspfad']))
				{
					$_POST['abspfad'] = str_replace('\\', '/', dirname(__FILE__).'/');
				}
				if (!isset($_POST['cookiename']))
				{
					$_POST['cookiename'] = '';
				}
				if (!isset($_POST['sessiontime']))
				{
					$_POST['sessiontime'] = '3600';
				}
				if (!isset($_POST['tmp_upload_dir']))
				{
					$_POST['tmp_upload_dir'] = '';
				}
				if (!isset($_POST['abspfad_exec']))
				{
					$_POST['abspfad_exec'] = '';
				}
				if (!isset($_POST['maxlength_folder']))
				{
					$_POST['maxlength_folder'] = '';
				}
				if (!isset($_POST['maxlength_file']))
				{
					$_POST['maxlength_file'] = '';
				}

				$time_zone_array				= array();
				$time_zone_array['Africa']		= array('Africa/Abidjan', 'Africa/Accra', 'Africa/Addis_Ababa', 'Africa/Algiers', 'Africa/Asmara', 'Africa/Asmera',
														'Africa/Bamako', 'Africa/Bangui', 'Africa/Banjul', 'Africa/Bissau', 'Africa/Blantyre', 'Africa/Brazzaville',
														'Africa/Bujumbura', 'Africa/Cairo', 'Africa/Casablanca', 'Africa/Ceuta', 'Africa/Conakry', 'Africa/Dakar',
														'Africa/Dar_es_Salaam', 'Africa/Djibouti', 'Africa/Douala', 'Africa/El_Aaiun', 'Africa/Freetown', 'Africa/Gaborone',
														'Africa/Harare', 'Africa/Johannesburg', 'Africa/Kampala', 'Africa/Khartoum', 'Africa/Kigali', 'Africa/Kinshasa',
														'Africa/Lagos', 'Africa/Libreville', 'Africa/Lome', 'Africa/Luanda', 'Africa/Lubumbashi', 'Africa/Lusaka',
														'Africa/Malabo', 'Africa/Maputo', 'Africa/Maseru', 'Africa/Mbabane', 'Africa/Mogadishu', 'Africa/Monrovia',
														'Africa/Nairobi', 'Africa/Ndjamena', 'Africa/Niamey', 'Africa/Nouakchott', 'Africa/Ouagadougou', 'Africa/Porto-Novo',
														'Africa/Sao_Tome', 'Africa/Timbuktu', 'Africa/Tripoli', 'Africa/Tunis', 'Africa/Windhoek');
				$time_zone_array['America']		= array('America/Adak', 'America/Anchorage', 'America/Anguilla', 'America/Antigua', 'America/Araguaina', 'America/Argentina/Buenos_Aires',
														'America/Argentina/Catamarca', 'America/Argentina/ComodRivadavia', 'America/Argentina/Cordoba', 'America/Argentina/Jujuy',
														'America/Argentina/La_Rioja', 'America/Argentina/Mendoza', 'America/Argentina/Rio_Gallegos', 'America/Argentina/San_Juan',
														'America/Argentina/San_Luis', 'America/Argentina/Tucuman', 'America/Argentina/Ushuaia', 'America/Aruba',
														'America/Asuncion', 'America/Atikokan', 'America/Atka', 'America/Bahia', 'America/Barbados', 'America/Belem',
														'America/Belize', 'America/Blanc-Sablon', 'America/Boa_Vista', 'America/Bogota', 'America/Boise', 'America/Buenos_Aires',
														'America/Cambridge_Bay', 'America/Campo_Grande', 'America/Cancun', 'America/Caracas', 'America/Catamarca',
														'America/Cayenne', 'America/Cayman', 'America/Chicago', 'America/Chihuahua', 'America/Coral_Harbour', 'America/Cordoba',
														'America/Costa_Rica', 'America/Cuiaba', 'America/Curacao', 'America/Danmarkshavn', 'America/Dawson', 'America/Dawson_Creek',
														'America/Denver', 'America/Detroit', 'America/Dominica', 'America/Edmonton', 'America/Eirunepe', 'America/El_Salvador',
														'America/Ensenada', 'America/Fort_Wayne', 'America/Fortaleza', 'America/Glace_Bay', 'America/Godthab', 'America/Goose_Bay',
														'America/Grand_Turk', 'America/Grenada', 'America/Guadeloupe', 'America/Guatemala', 'America/Guayaquil', 'America/Guyana',
														'America/Halifax', 'America/Havana', 'America/Hermosillo', 'America/Indiana/Indianapolis', 'America/Indiana/Knox',
														'America/Indiana/Marengo', 'America/Indiana/Petersburg', 'America/Indiana/Tell_City', 'America/Indiana/Vevay', 'America/Indiana/Vincennes',
														'America/Indiana/Winamac', 'America/Indianapolis', 'America/Inuvik', 'America/Iqaluit', 'America/Jamaica',
														'America/Jujuy', 'America/Juneau', 'America/Kentucky/Louisville', 'America/Kentucky/Monticello', 'America/Knox_IN',
														'America/La_Paz', 'America/Lima', 'America/Los_Angeles', 'America/Louisville', 'America/Maceio', 'America/Managua',
														'America/Manaus', 'America/Marigot', 'America/Martinique', 'America/Mazatlan', 'America/Mendoza', 'America/Menominee',
														'America/Merida', 'America/Mexico_City', 'America/Miquelon', 'America/Moncton', 'America/Monterrey', 'America/Montevideo',
														'America/Montreal', 'America/Montserrat', 'America/Nassau', 'America/New_York', 'America/NipigonAmerica/Nome', 'America/Noronha',
														'America/North_Dakota/Center', 'America/North_Dakota/New_Salem', 'America/Panama', 'America/Pangnirtung', 'America/Paramaribo',
														'America/Phoenix', 'America/Port-au-Prince', 'America/Port_of_Spain', 'America/Porto_Acre', 'America/Porto_Velho',
														'America/Puerto_Rico', 'America/Rainy_River', 'America/Rankin_Inlet', 'America/Recife', 'America/Regina', 'America/Resolute',
														'America/Rio_Branco', 'America/Rosario', 'America/Santiago', 'America/Santo_Domingo', 'America/Sao_Paulo',
														'America/Scoresbysund', 'America/Shiprock', 'America/St_Barthelemy', 'America/St_Johns', 'America/St_Kitts', 'America/St_Lucia',
														'America/St_Thomas', 'America/St_Vincent', 'America/Swift_Current', 'America/Tegucigalpa', 'America/Thule', 'America/Thunder_Bay',
														'America/Tijuana', 'America/Toronto', 'America/Tortola', 'America/Vancouver', 'America/Virgin', 'America/Whitehorse',
														'America/Winnipeg', 'America/Yakutat', 'America/Yellowknife');
				$time_zone_array['Antarctica']	= array('Antarctica/Casey', 'Antarctica/Davis', 'Antarctica/DumontDUrville', 'Antarctica/Mawson', 'Antarctica/McMurdo',
														'Antarctica/Palmer', 'Antarctica/Rothera', 'Antarctica/South_Pole', 'Antarctica/Syowa', 'Antarctica/Vostok');
				$time_zone_array['Arctic']		= array('Arctic/Longyearbyen');
				$time_zone_array['Asia']		= array('Asia/Aden', 'Asia/Almaty', 'Asia/Amman', 'Asia/Anadyr', 'Asia/Aqtau', 'Asia/Aqtobe', 'Asia/Ashgabat',
														'Asia/Ashkhabad', 'Asia/Baghdad', 'Asia/Bahrain', 'Asia/Baku', 'Asia/Bangkok', 'Asia/Beirut', 'Asia/Bishkek',
														'Asia/Brunei', 'Asia/Calcutta', 'Asia/Choibalsan', 'Asia/Chongqing', 'Asia/Chungking', 'Asia/Colombo', 'Asia/Dacca',
														'Asia/Damascus', 'Asia/Dhaka', 'Asia/Dili', 'Asia/Dubai', 'Asia/Dushanbe', 'Asia/Gaza', 'Asia/Harbin', 'Asia/Ho_Chi_Minh',
														'Asia/Hong_Kong', 'Asia/Hovd', 'Asia/Irkutsk', 'Asia/Istanbul', 'Asia/Jakarta', 'Asia/Jayapura', 'Asia/Jerusalem', 'Asia/Kabul',
														'Asia/Kamchatka', 'Asia/Karachi', 'Asia/Kashgar', 'Asia/Katmandu', 'Asia/Kolkata', 'Asia/Krasnoyarsk', 'Asia/Kuala_Lumpur', 'Asia/Kuching',
														'Asia/Kuwait', 'Asia/Macao', 'Asia/Macau', 'Asia/Magadan', 'Asia/Makassar', 'Asia/Manila', 'Asia/Muscat', 'Asia/Nicosia', 'Asia/Novosibirsk',
														'Asia/Omsk', 'Asia/Oral', 'Asia/Phnom_Penh', 'Asia/Pontianak', 'Asia/Pyongyang', 'Asia/Qatar', 'Asia/Qyzylorda', 'Asia/Rangoon', 'Asia/Riyadh',
														'Asia/Saigon', 'Asia/Sakhalin', 'Asia/Samarkand', 'Asia/Seoul', 'Asia/Shanghai', 'Asia/Singapore', 'Asia/Taipei', 'Asia/Tashkent', 'Asia/Tbilisi',
														'Asia/Tehran', 'Asia/Tel_Aviv', 'Asia/Thimbu', 'Asia/Thimphu', 'Asia/Tokyo', 'Asia/Ujung_Pandang', 'Asia/Ulaanbaatar', 'Asia/Ulan_Bator',
														'Asia/Urumqi', 'Asia/Vientiane', 'Asia/Vladivostok', 'Asia/Yakutsk', 'Asia/Yekaterinburg', 'Asia/Yerevan');
				$time_zone_array['Atlantic']	= array('Atlantic/Azores', 'Atlantic/Bermuda', 'Atlantic/Canary', 'Atlantic/Cape_Verde', 'Atlantic/Faeroe', 'Atlantic/Faroe', 'Atlantic/Jan_Mayen',
														'Atlantic/Madeira', 'Atlantic/Reykjavik', 'Atlantic/South_Georgia', 'Atlantic/St_Helena', 'Atlantic/Stanley');
				$time_zone_array['Australia']	= array('Australia/ACT', 'Australia/Adelaide', 'Australia/Brisbane', 'Australia/Broken_Hill', 'Australia/Canberra', 'Australia/Currie',
														'Australia/Darwin', 'Australia/Eucla', 'Australia/Hobart', 'Australia/LHI', 'Australia/Lindeman', 'Australia/Lord_Howe', 'Australia/Melbourne',
														'Australia/North', 'Australia/NSW', 'Australia/Perth', 'Australia/Queensland', 'Australia/South', 'Australia/Sydney', 'Australia/Tasmania',
														'Australia/Victoria', 'Australia/West', 'Australia/Yancowinna');
				$time_zone_array['Europe']		= array('Europe/Amsterdam', 'Europe/Andorra', 'Europe/Athens', 'Europe/Belfast', 'Europe/Belgrade', 'Europe/Berlin', 'Europe/Bratislava', 'Europe/Brussels',
														'Europe/Bucharest', 'Europe/Budapest', 'Europe/Chisinau', 'Europe/Copenhagen', 'Europe/Dublin', 'Europe/Gibraltar', 'Europe/Guernsey', 'Europe/Helsinki',
														'Europe/Isle_of_Man', 'Europe/Istanbul', 'Europe/Jersey', 'Europe/Kaliningrad', 'Europe/Kiev', 'Europe/Lisbon', 'Europe/Ljubljana', 'Europe/London',
														'Europe/Luxembourg', 'Europe/Madrid', 'Europe/Malta', 'Europe/Mariehamn', 'Europe/Minsk', 'Europe/Monaco', 'Europe/Moscow', 'Europe/Nicosia', 'Europe/Oslo',
														'Europe/Paris', 'Europe/Podgorica', 'Europe/Prague', 'Europe/Riga', 'Europe/Rome', 'Europe/Samara', 'Europe/San_Marino', 'Europe/Sarajevo', 'Europe/Simferopol',
														'Europe/Skopje', 'Europe/Sofia', 'Europe/Stockholm', 'Europe/Tallinn', 'Europe/Tirane', 'Europe/Tiraspol', 'Europe/Uzhgorod', 'Europe/Vaduz', 'Europe/Vatican',
														'Europe/Vienna', 'Europe/Vilnius', 'Europe/Volgograd', 'Europe/Warsaw', 'Europe/Zagreb', 'Europe/Zaporozhye', 'Europe/Zurich');
				$time_zone_array['Indian']		= array('Indian/Antananarivo', 'Indian/Chagos', 'Indian/Christmas', 'Indian/Cocos', 'Indian/Comoro', 'Indian/Kerguelen', 'Indian/Mahe',
														'Indian/Maldives', 'Indian/Mauritius', 'Indian/Mayotte', 'Indian/Reunion');
				$time_zone_array['Pacific']		= array('Pacific/Apia', 'Pacific/Auckland', 'Pacific/Chatham', 'Pacific/Easter', 'Pacific/Efate', 'Pacific/Enderbury', 'Pacific/Fakaofo', 'Pacific/Fiji',
														'Pacific/Funafuti', 'Pacific/Galapagos', 'Pacific/Gambier', 'Pacific/Guadalcanal', 'Pacific/Guam', 'Pacific/Honolulu', 'Pacific/Johnston',
														'Pacific/Kiritimati', 'Pacific/Kosrae', 'Pacific/Kwajalein', 'Pacific/Majuro', 'Pacific/Marquesas', 'Pacific/Midway', 'Pacific/Nauru',
														'Pacific/Niue', 'Pacific/Norfolk', 'Pacific/Noumea', 'Pacific/Pago_Pago', 'Pacific/Palau', 'Pacific/Pitcairn', 'Pacific/Ponape', 'Pacific/Port_Moresby',
														'Pacific/Rarotonga', 'Pacific/Saipan', 'Pacific/Samoa', 'Pacific/Tahiti', 'Pacific/Tarawa', 'Pacific/Tongatapu', 'Pacific/Truk', 'Pacific/Wake', 'Pacific/Wallis', 'Pacific/Yap');

		?>
				<form action="install.php?step=2&amp;lng=<?php echo $lng_txt; ?>" method="post" accept-charset="UTF-8">
					<input type="hidden" name="job_string" value="add_config" />
					<table cellpadding="2" cellspacing="0" border="0" width="100%">
						<tr valign="middle">
							<td class="main_table_header" width="100%">File-O-Meter Installation <?php echo $install_version; ?></td>
						</tr>
						<tr><td><img src="template/default/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
						<tr>
							<td width="100%" class="main_table_content">
								<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
									<tr>
										<td width="100%">
											<fieldset>
		   										<legend><?php echo $install_array[$lng_txt][96]; //PHP Einstellungen ?></legend>
		   										<table cellpadding="2" cellspacing="0" border="0" width="100%">
													<colgroup>
														<col width="30%" />
														<col width="25%" />
														<col width="45%" />
													</colgroup>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][0];//PHP-Fehlermeldungen anzeigen ?>*:</strong></td>
														<td><input type="radio" name="php_error" value="0"<?php if ($_POST['php_error'] == 0){echo ' checked="checked"';} ?> /> <?php echo $install_array[$lng_txt][1];//nein ?> <input type="radio" name="php_error" value="1"<?php if ($_POST['php_error'] == 1){echo ' checked="checked"';} ?> /> <?php echo $install_array[$lng_txt][2];//ja ?></td>
														<td><?php echo $install_array[$lng_txt][3];//[ja] sollte nur bei testinstallationen verwendet werden! ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][4];//Zeitzoneneinstellung / time zone setup ?>:</strong></td>
														<td>
															<select name="time_zone" class="ipt_200">
																<?php
																	foreach ($time_zone_array as $area => $data)
																	{
																		echo '<optgroup label="'.$area.'">';
																		for ($i = 0; $i < count($data); $i++)
																		{
																			if ($_POST['time_zone'] == $data[$i])
																			{
																				echo '<option selected="selected">'.$data[$i].'</option>';
																			}
																			else
																			{
																				echo '<option>'.$data[$i].'</option>';
																			}
																		}
																		echo '</optgroup>';
																	}
																?>
															</select>
														</td>
														<td>&nbsp;</td>
													</tr>
												</table>
											</fieldset>
											<fieldset>
		   										<legend><?php echo $install_array[$lng_txt][97]; //MYSQL Einstellungen ?></legend>
		   										<table cellpadding="2" cellspacing="0" border="0" width="100%">
													<colgroup>
														<col width="30%" />
														<col width="25%" />
														<col width="45%" />
													</colgroup>
													<tr>
														<td colspan="3">
															<br /><br />
															<?php echo $install_array[$lng_txt]['db_install'];//Please specify access data for an existing MySQL database. ?>
															<br /><br />
														</td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][5];//MySql-Server ?>*:</strong></td>
														<td><input type="text" name="db_server" value="<?php echo $_POST['db_server']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][6];//z.B. localhost ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][72];//MySql-Port ?>:</strong></td>
														<td><input type="text" name="db_port" value="<?php echo $_POST['db_port']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][73];//z.B. 3306 ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][74];//MySql-Socket ?>:</strong></td>
														<td><input type="text" name="db_socket" value="<?php echo $_POST['db_socket']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][75];//z.B. localhost:/mysql/mysql.sock ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][7];//MySql-Benutzername ?>*:</strong></td>
														<td><input type="text" name="db_user" value="<?php echo $_POST['db_user']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][8];//z.B. root ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][9];//MySql-Passwort ?>*:</strong></td>
														<td><input type="password" name="db_pw" value="<?php echo $_POST['db_pw']; ?>" class="ipt_200" /></td>
														<td>&nbsp;</td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][10];//Datenbankname ?>*:</strong></td>
														<td><input type="text" name="db_name" value="<?php echo $_POST['db_name']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][11];//z.B. fom ?></td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr valign="top">
														<td><strong><?php echo $install_array[$lng_txt][12];//MySQL Pfad ?>:</strong></td>
														<td><input type="text" name="mysql" value="<?php echo $_POST['mysql']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][13];//MySQL Pfadangabe z.B. bei Linux "usr/bin/mysql" oder bei Windows "C:/mysql/bin/mysql.exe". ?></td>
													</tr>
													<tr>
														<td colspan="3">&nbsp;</td>
													</tr>
													<tr valign="top">
														<td><strong><?php echo $install_array[$lng_txt][14];//MySQL Dump Pfad ?>:</strong></td>
														<td><input type="text" name="mysqldump" value="<?php echo $_POST['mysqldump']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][15];//MySQL Dump Pfadangabe z.B. bei Linux "usr/bin/mysqldump" oder bei Windows "C:/mysql/bin/mysqldump.exe". ?></td>
													</tr>
												</table>
											</fieldset>
											<fieldset>
		   										<legend><?php echo $install_array[$lng_txt][98]; //FOM Einstellungen ?></legend>
		   										<table cellpadding="2" cellspacing="0" border="0" width="100%">
													<colgroup>
														<col width="30%" />
														<col width="25%" />
														<col width="45%" />
													</colgroup>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][16];//URL zur Hauptebene ?>*:</strong></td>
														<td><input type="text" name="url" value="<?php echo $_POST['url']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][17];//URL zum Hauptverzeichnis z.B. "http://www.mydomain.com/fom/". Achtung, die URL muss immer mit einem "/" enden. ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][18];//Absoluter Pfad zur Hauptebene ?>*:</strong></td>
														<td><input type="text" name="abspfad" value="<?php echo $_POST['abspfad']; ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][19];//Absoluter Pfad zum Hauptverzeichnis z.B. "/web/htdocs/fom/" oder "C:/web/htdocs/fom/". Achtung, der Absolute Pfad muss immer mit einem "/" enden. ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][85];//Upload directory of the server ?>*:</strong></td>
														<td>
															<?php
																if (empty($_POST['tmp_upload_dir']))
																{
																	if (ini_get('upload_tmp_dir') != '')
																	{
																		$_POST['tmp_upload_dir'] = ini_get('upload_tmp_dir');
																	}
																	elseif (isset($_ENV['TMPDIR']) and !empty($_ENV['TMPDIR']))
																	{
																		$_POST['tmp_upload_dir'] = realpath($_ENV['TMPDIR']);
																	}
																	elseif (isset($_ENV['TMP']) and !empty($_ENV['TMP']))
																	{
																		$_POST['tmp_upload_dir'] = realpath($_ENV['TMP']);
																	}
																	elseif (isset($_ENV['TEMP']) and !empty($_ENV['TEMP']))
																	{
																		$_POST['tmp_upload_dir'] = realpath($_ENV['TEMP']);
																	}
																	elseif (function_exists('sys_get_temp_dir') and sys_get_temp_dir() != '')
																	{
																		$_POST['tmp_upload_dir'] = realpath(sys_get_temp_dir());
																	}
																	elseif (strtoupper(substr(PHP_OS, 0, 3) == 'LIN'))
																	{
																		$_POST['tmp_upload_dir'] = '/tmp/';
																	}
																	elseif (strtoupper(substr(PHP_OS, 0, 3) == 'WIN'))
																	{
																		$_POST['tmp_upload_dir'] = "C:\\windows\\temp\\";
																	}

																	if (!empty($_POST['tmp_upload_dir']))
																	{
																		if (substr($_POST['tmp_upload_dir'], -1) != '/' and substr($_POST['tmp_upload_dir'], -1) != "\\")
																		{
																			if (strtoupper(substr(PHP_OS, 0,3) == 'WIN'))
																			{
																				$_POST['tmp_upload_dir'] .= "\\";
																			}
																			else
																			{
																				$_POST['tmp_upload_dir'] .= '/';
																			}
																		}
																	}

																	$_POST['tmp_upload_dir'] = str_replace('\\', '/', $_POST['tmp_upload_dir']);
																}
															?>
															<input type="text" name="tmp_upload_dir" value="<?php echo $_POST['tmp_upload_dir']; ?>" class="ipt_200" />
														</td>
														<td><?php echo $install_array[$lng_txt][87];//e.g. "/tmp/" ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][20];//Cookiename ?>*:</strong></td>
														<td><input type="text" name="cookiename" value="<?php if(empty($_POST['cookiename'])) {echo 'FOM'.substr(md5(uniqid(mt_rand(), true)), 0, 7);} else { echo $_POST['cookiename'];} ?>" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][21];//Einen eindeutigen Cookienamen ohne Sonderzeichen waehlen, z.B. domainname_fom ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][22];//Maximale Inaktivitaetsdauer der User ?>*:</strong></td>
														<td><input type="text" name="sessiontime" value="<?php echo $_POST['sessiontime']; ?>" maxlength="4" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][23];//Angabe in Sekunden ?></td>
													</tr>
													<tr>
														<td colspan="3">
															<br /><br />
															<?php echo $install_array[$lng_txt][81];//Please specify the maximum string length for filenames and directories. ?>
															<br /><br />
															<?php echo $install_array[$lng_txt][79];//ISO 9660 Level 1 allows for filenames (incl. extension) 12 Characters and for directories 8 Characters. ?>
															<br />
															<?php echo $install_array[$lng_txt][80];//ISO 9660 Level 2 allows for filenames (incl. extension) 31 Characters and for directories 31 Characters. ?>
															<br /><br />
														</td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][77];//Maximum length of foldernames ?>*:</strong></td>
														<td><input type="text" name="maxlength_folder" value="<?php if(empty($_POST['maxlength_folder'])){ echo 250;} else {echo $_POST['maxlength_folder'];} ?>" maxlength="3" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][82];//max. 250 characters ?></td>
													</tr>
													<tr>
														<td><strong><?php echo $install_array[$lng_txt][78];//Maximum length of filenames ?>*:</strong></td>
														<td><input type="text" name="maxlength_file" value="<?php if(empty($_POST['maxlength_file'])){ echo 250;} else {echo $_POST['maxlength_file'];} ?>" maxlength="3" class="ipt_200" /></td>
														<td><?php echo $install_array[$lng_txt][82];//max. 250 characters ?></td>
													</tr>
												</table>
											</fieldset>
										</td>
									</tr>
									<tr>
										<td align="center">
											<br />
											<input type="submit" value="Next" />
											<br /><br />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>
		<?php
			}
			elseif (isset($_GET['step']) and $_GET['step'] == 2)
			{
		?>
				<form action="install.php?step=3&amp;lng=<?php echo $lng_txt; ?>" method="post" accept-charset="UTF-8">
					<input type="hidden" name="job_string" value="add_user" />
					<input type="hidden" name="showreiko_string" value="n" />
					<input type="hidden" name="accountaktiv_string" value="j" />
					<table cellpadding="2" cellspacing="0" border="0" width="100%">
						<tr valign="middle">
							<td class="main_table_header" width="100%">File-O-Meter Installation <?php echo $install_version; ?></td>
						</tr>
						<tr><td><img src="template/default/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
						<tr>
							<td width="100%" class="main_table_content">
								<table cellpadding="2" cellspacing="0" width="100%" class="content_table">
									<colgroup>
										<col width="30%" />
										<col width="70%" />
									</colgroup>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td colspan="2">
											<?php echo $install_array[$lng_txt]['useraccount_admin'];//You have to create an administrator account. Please specify login details: ?>
										</td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][30];//Vorname ?>:*</strong></td>
										<td><input type="text" name="vorname_string" class="ipt_200" /></td>
									</tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][31];//Nachname ?>:*</strong></td>
										<td><input type="text" name="nachname_string" class="ipt_200" /></td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][32];//E-Mail ?>:*</strong></td>
										<td><input type="text" name="mail_string" class="ipt_200" /></td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][43];//Benutzergruppe ?>:</strong></td>
										<td>
											Admin
										</td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][44];//Benutzername ?>:*</strong></td>
										<td><input type="text" name="username_string" class="ipt_200" /></td>
									</tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][45];//Neues Passwort ?>:*</strong></td>
										<td><input type="password" name="pw_string" class="ipt_200" /></td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][46];//Sprache ?>:*</strong></td>
										<td>
											<select name="sprache_int" class="ipt_200">
												<?php
													$sql_2 = $cdb->select('SELECT language_id, language_name FROM fom_languages ORDER BY language_name ASC');
													while ($s_result = $cdb->fetch_array($sql_2))
													{
														echo '<option value="'.$s_result['language_id'].'">'.$s_result['language_name'].'</option>';
													}
												?>
											</select>
										</td>
									</tr>
									<tr><td colspan="2"><br /><hr /></td></tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td colspan="2">
											<?php echo $install_array[$lng_txt]['main_language_desc'];//Please select the default main language for the application... ?>
										</td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt]['main_language'];//Application main language ?>:*</strong></td>
										<td>
											<select name="main_language_id" class="ipt_200">
												<?php
													$sql_2 = $cdb->select('SELECT language_id, language_name FROM fom_languages ORDER BY language_name ASC');
													while ($s_result = $cdb->fetch_array($sql_2))
													{
														echo '<option value="'.$s_result['language_id'].'">'.$s_result['language_name'].'</option>';
													}
												?>
											</select>
										</td>
									</tr>
									<tr><td colspan="2"><br /><hr /></td></tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td colspan="2">
											<?php echo $install_array[$lng_txt]['contact_desc'];//Please specify a contact person, which is responsible for user questions. ?>
										</td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][30];//Vorname ?>:</strong></td>
										<td><input type="text" name="kontakt_vorname_string" class="ipt_200" /></td>
									</tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][31];//Nachname ?>:</strong></td>
										<td><input type="text" name="kontakt_nachname_string" class="ipt_200" /></td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt][32];//E-Mail ?>:</strong></td>
										<td><input type="text" name="kontakt_mail_string" class="ipt_200" /></td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt]['tel'];//Telefon ?>:</strong></td>
										<td><input type="text" name="kontakt_tel_string" class="ipt_200" /></td>
									</tr>
									<tr>
										<td class="padding_left_10"><strong><?php echo $install_array[$lng_txt]['handy'];//Mobile phone ?>:</strong></td>
										<td><input type="text" name="kontakt_handy_string" class="ipt_200" /></td>
									</tr>
									<tr><td colspan="2"><br /><br /></td></tr>
									<tr>
										<td colspan="2" align="center"><input type="submit" value="Next" /></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>
<?php
			}
			elseif (isset($_GET['step']) and $_GET['step'] == 3)
			{
?>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr valign="middle">
						<td class="info_table_header" width="100%"><?php echo $install_array[$lng_txt][99]; //Hinweis ?></td>
					</tr>
					<tr><td><img src="template/default/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
					<tr>
						<td class="info_table_content">
							<ul>
								<li><?php echo $install_array[$lng_txt][66];//Die Installation ist abgeschlossen. ?></li>
								<li><?php echo $install_array[$lng_txt][67];//Bitte Loeschen Sie diese und die FOM.sql Datei auf Ihrem Server. ?></li>
								<?php
									foreach ($install_array['writable_files'] as $file)
									{
										//Nicht das files Verzeichnis auflisten hier werden die schreibrechte weiterhin benötigt
										if (substr($file, 0, 5) != 'files' and is_writable($file))
										{
											echo '<li>'.str_replace('[file]', $file, $install_array[$lng_txt][91]).'</li>';//Für Nachfolgende Datei/Verzeichnis "[file]" sollte das Schreibrecht entfernt werden.
										}
									}
								?>
								<li><?php echo $install_array[$lng_txt][68];//Zum Schutz Ihrer Daten sollten Sie einen .htaccess Verzeichnisschutz auf den Order "files/" einrichten! ?></li>
								<li><?php echo $install_array[$lng_txt][71];//Einige Funktionen dieser Anwendung erfordern einen Cronjob.....?></li>
								<li><a href="index.php"><?php echo $install_array[$lng_txt][69];//Zur Loginseite gelangen Sie ueeber diesen Link. ?></a></li>
							</ul>
							<div style="width: 100%; text-align: center;"><u><a href="index.php"><?php echo $install_array[$lng_txt][69];//Zur Loginseite gelangen Sie ueber diesen Link. ?></a></u></div>
						</td>
					</tr>
				</table>
				<br /><br />
<?php
			}
		}
	}
?>
</body>
</html>