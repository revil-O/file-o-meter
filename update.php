<?php
	$update_from = '0.5b';
	$update_to = '0.6b';

	if (isset($_GET['step']) and $_GET['step'] == 2)
	{
		define('FOM_LOGIN_SITE', 'true');
		require_once('inc/include.php');

		$tbl_array = array();
		$sql = $cdb->select('SHOW TABLES');
		while ($tbl_result = $cdb->fetch_row($sql))
		{
			$tbl_array[] = $tbl_result[0];
		}

		//SQL Upadtes durchfuehren
		$sql_txt_array = array();
		$sql_txt_array[] = array('text_id' => '10', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt noch einmal.', 'language_2' => 'An error has occurred. Please retry it later.');
		$sql_txt_array[] = array('text_id' => '16', 'text_key' => 'maerz', 'category' => 'calendar', 'comment' => '', 'language_1' => 'M&auml;rz', 'language_2' => 'March');
		$sql_txt_array[] = array('text_id' => '46', 'text_key' => 'back', 'category' => 'system', 'comment' => '', 'language_1' => 'zur&uuml;ck', 'language_2' => 'back');
		$sql_txt_array[] = array('text_id' => '53', 'text_key' => 'del', 'category' => 'system', 'comment' => '', 'language_1' => 'L&ouml;schen', 'language_2' => 'Delete');
		$sql_txt_array[] = array('text_id' => '54', 'text_key' => 'please_select', 'category' => 'system', 'comment' => '', 'language_1' => 'Bitte w&auml;hlen', 'language_2' => 'Please select');
		$sql_txt_array[] = array('text_id' => '63', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Empf&auml;nger (E-Mail)', 'language_2' => 'Recipient (E-Mail)');
		$sql_txt_array[] = array('text_id' => '65', 'text_key' => 'daily', 'category' => 'system', 'comment' => '', 'language_1' => 't&auml;glich', 'language_2' => 'daily');
		$sql_txt_array[] = array('text_id' => '90', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Bitte geben Sie eine Bezeichnung f&uuml;r den Dokumententyp an!', 'language_2' => 'Please enter a document type name!');
		$sql_txt_array[] = array('text_id' => '95', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Bitte f&uuml;llen Sie alle Pflichtfelder aus!', 'language_2' => 'Please complete all mandatory fields!');
		$sql_txt_array[] = array('text_id' => '97', 'text_key' => '', 'category' => 'success_message', 'comment' => '', 'language_1' => 'Die &Auml;nderungen wurden gespeichert.', 'language_2' => 'The changes were successfully saved.');
		$sql_txt_array[] = array('text_id' => '101', 'text_key' => '', 'category' => 'success_message', 'comment' => '', 'language_1' => 'Das Verzeichnis wurde gel&ouml;scht.', 'language_2' => 'The folder was deleted.');
		$sql_txt_array[] = array('text_id' => '104', 'text_key' => '', 'category' => 'success_message', 'comment' => '', 'language_1' => 'Die Datei wurde gel&ouml;scht.', 'language_2' => 'The file was deleted.');
		$sql_txt_array[] = array('text_id' => '107', 'text_key' => '', 'category' => 'success_message', 'comment' => '', 'language_1' => 'Der Importvorgang wurde erfolgreich durchgef&uuml;hrt.', 'language_2' => 'Data import successfully finished.');
		$sql_txt_array[] = array('text_id' => '108', 'text_key' => '', 'category' => 'success_message', 'comment' => '', 'language_1' => 'Der Exportvorgang wurde erfolgreich durchgef&uuml;hrt!', 'language_2' => 'Data export successfully finished.');
		$sql_txt_array[] = array('text_id' => '119', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Benutzergruppe hinzuf&uuml;gen', 'language_2' => 'Add usergroup');
		$sql_txt_array[] = array('text_id' => '125', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Projekt hinzuf&uuml;gen', 'language_2' => 'Add project');
		$sql_txt_array[] = array('text_id' => '126', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Achtung: Das Projekt erscheint erst im Verzeichnisbaum, wenn es den gew&uuml;nschten Benutzergruppen zugeordnet wurde! Die Zuordnung von Projekten zu Benutzergruppen erfolgt im Men&uuml;punkt &quot;Benutzergruppenverwaltung&quot;.', 'language_2' => 'Attention: The project will not appear within the directory tree unless it was assigned to the desired usergroups! The assignment of projects and usergroups can be specified under menu item &quot;Usergroup management&quot;');
		$sql_txt_array[] = array('text_id' => '131', 'text_key' => 'access_d', 'category' => 'access_authorization', 'comment' => '', 'language_1' => 'L&ouml;schen', 'language_2' => 'Delete');
		$sql_txt_array[] = array('text_id' => '132', 'text_key' => 'access_vo', 'category' => 'access_authorization', 'comment' => '', 'language_1' => 'Versions&uuml;bersicht', 'language_2' => 'Version overview');
		$sql_txt_array[] = array('text_id' => '144', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verf&uuml;gbar bis', 'language_2' => 'Available till');
		$sql_txt_array[] = array('text_id' => '147', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Die derzeitige Datei wird durch eine neue Version ersetzt. Die bisherige Datei bleibt in der Versionshistorie verf&uuml;gbar.', 'language_2' => 'The current file will be replaced by a new version. The old file remains available within the versionhistory.');
		$sql_txt_array[] = array('text_id' => '151', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Bitte w&auml;hlen Sie eine Datei aus!', 'language_2' => 'Please select a file!');
		$sql_txt_array[] = array('text_id' => '152', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei hinzuf&uuml;gen', 'language_2' => 'Add file');
		$sql_txt_array[] = array('text_id' => '155', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Mehrfachauswahl mit gedr&uuml;ckter STRG-Taste', 'language_2' => 'Hold CTRL-Key for multiple selection');
		$sql_txt_array[] = array('text_id' => '156', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'SubDatei hinzuf&uuml;gen', 'language_2' => 'Add subfile');
		$sql_txt_array[] = array('text_id' => '157', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei l&ouml;schen', 'language_2' => 'Delete file');
		$sql_txt_array[] = array('text_id' => '158', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie diese Datei wirklich l&ouml;schen?', 'language_2' => 'Do you really want to delete this file?');
		$sql_txt_array[] = array('text_id' => '159', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis l&ouml;schen', 'language_2' => 'Delete folder');
		$sql_txt_array[] = array('text_id' => '160', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie dieses Verzeichnis wirklich l&ouml;schen?', 'language_2' => 'Do you really want to delete this folder?');
		$sql_txt_array[] = array('text_id' => '164', 'text_key' => 'filesize', 'category' => 'system', 'comment' => '', 'language_1' => 'Dateigr&ouml;&szlig;e', 'language_2' => 'Filesize');
		$sql_txt_array[] = array('text_id' => '166', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei&uuml;bersicht', 'language_2' => 'File overview');
		$sql_txt_array[] = array('text_id' => '173', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie diese Datei wirklich auschecken?', 'language_2' => 'Do you really want to checkout this file?');
		$sql_txt_array[] = array('text_id' => '174', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie diese Datei wirklich einchecken?', 'language_2' => 'Do you really want to checkin this file?');
		$sql_txt_array[] = array('text_id' => '176', 'text_key' => '', 'category' => 'system', 'comment' => 'Mit Doppelpunkt am Ende:', 'language_1' => 'Bei der Bearbeitung von Zugriffsrechten sind folgende Beschr&auml;nkungen zu beachten:', 'language_2' => 'Please regard the following restrictions for the configuration of access authorizations:');
		$sql_txt_array[] = array('text_id' => '177', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Man darf sich selbst keine Zugriffsrechte gew&auml;hren, &uuml;ber die man nicht verf&uuml;gt.', 'language_2' => 'It is not possible to grant authorizations to oneself, which one does not have.');
		$sql_txt_array[] = array('text_id' => '179', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Man darf nur Benutzergruppen bearbeiten, die &uuml;ber die gleichen oder weniger Zugriffsrechte verf&uuml;gen.', 'language_2' => 'One may only edit usergroups which are provided with the same or fewer access authorizations. ');
		$sql_txt_array[] = array('text_id' => '180', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Man darf keine Rechte fremder Gruppen bearbeiten, &uuml;ber die man selbst nicht verf&uuml;gt.', 'language_2' => 'It is not possible to edit access authorizations of other usergroups, which one does not have.');
		$sql_txt_array[] = array('text_id' => '181', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Benutzergruppe ausw&auml;hlen', 'language_2' => 'Select usergroup');
		$sql_txt_array[] = array('text_id' => '190', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Der Datenexport verl&auml;uft in zwei Arbeitsschritten. Im ersten Schritt werden die Exportdaten untersucht. Wenn keine Fehler gefunden werden, k&ouml;nnen die Daten anschlie&szlig;end im zweiten Schritt exportiert werden.', 'language_2' => 'The data export procedure is devided into two steps. In the first step export data will be examined. If no errors are found, the data can be exported afterwards in the second step.');
		$sql_txt_array[] = array('text_id' => '196', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Bereits existierende Verzeichnisse werden gel&ouml;scht!', 'language_2' => 'Already existing folders will be deleted!');
		$sql_txt_array[] = array('text_id' => '197', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Es wurden keine Fehler gefunden. Sie k&ouml;nnen den Export nun starten.', 'language_2' => 'No errors found. You may start the export now.');
		$sql_txt_array[] = array('text_id' => '199', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Es darf nur eine Exportoption f&uuml;r die Dateiendung gew&auml;hlt werden.', 'language_2' => 'There is only one exportoption allowed for the fileextension.');
		$sql_txt_array[] = array('text_id' => '205', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Bereits vorhandene Dateien und Verzeichnisse im Zielverzeichnis l&ouml;schen, wenn sie nicht in den Importdaten enthalten sind?', 'language_2' => 'Delete existing files and subfolders of the targetfolder, if they are not part of the importdata?');
		$sql_txt_array[] = array('text_id' => '208', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Sie haben keine Quelldaten f&uuml;r den Import ausgew&auml;hlt!', 'language_2' => 'You have no sourcedata selected for the import!');
		$sql_txt_array[] = array('text_id' => '209', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Achtung: Das automatische Anpassen von Datei- und Verzeichnisnamen kann zu unvorhersehbaren Ergebnissen f&uuml;hren!', 'language_2' => 'Attention: The automatic adaptation of file and folder names can lead to unexpected results!');
		$sql_txt_array[] = array('text_id' => '210', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Beispielsweise werden Dateinamen automatisch auf eine Maximall&auml;nge von 30 Zeichen reduziert.', 'language_2' => 'For instance filenames will automatically be reduced to a maximum length of 30 characters.');
		$sql_txt_array[] = array('text_id' => '221', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Alle ausw&auml;hlen', 'language_2' => 'Select all');
		$sql_txt_array[] = array('text_id' => '222', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Daten erneut pr&uuml;fen', 'language_2' => 'Examine data again');
		$sql_txt_array[] = array('text_id' => '223', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Bitte w&auml;hlen Sie mindestens ein Verzeichnis oder eine Datei f&uuml;r den Import aus!', 'language_2' => 'Please select at least one folder or one file for the import!');
		$sql_txt_array[] = array('text_id' => '225', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis &ouml;ffnen', 'language_2' => 'Open folder');
		$sql_txt_array[] = array('text_id' => '228', 'text_key' => 'close', 'category' => 'system', 'comment' => '', 'language_1' => 'Schlie&szlig;en', 'language_2' => 'Close');
		$sql_txt_array[] = array('text_id' => '231', 'text_key' => 'paste', 'category' => 'system', 'comment' => '', 'language_1' => 'Einf&uuml;gen', 'language_2' => 'Paste');
		$sql_txt_array[] = array('text_id' => '238', 'text_key' => 'turnover_prev', 'category' => 'turnover', 'comment' => 'Dieser Text ist Teil der Blättern-Funktion.', 'language_1' => 'eine Seite zur&uuml;ck', 'language_2' => 'previous page');
		$sql_txt_array[] = array('text_id' => '246', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Der Pfad f&uuml;r das Verzeichnis &quot;[var]foldername[/var]&quot; ist zu lang! Die maximale Pfadl&auml;nge betr&auml;gt 255 Zeichen.', 'language_2' => 'The path for the folder &quot;[var]foldername[/var]&quot; exceeds the allowed length! The maximum path length is 255 characters.');
		$sql_txt_array[] = array('text_id' => '247', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Der Pfad f&uuml;r die Datei &quot;[var]filename[/var]&quot; ist zu lang!', 'language_2' => 'The path for the file &quot;[var]filename[/var]&quot; exceeds the allowed length!');
		$sql_txt_array[] = array('text_id' => '250', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Die &Auml;nderungen konnten nicht gespeichert werden!', 'language_2' => 'The changes could not be saved!');
		$sql_txt_array[] = array('text_id' => '253', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Ung&uuml;ltiger Dateiname!', 'language_2' => 'Invalid filename!');
		$sql_txt_array[] = array('text_id' => '254', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Die hochgeladene Datei &uuml;berschreitet die maximale Dateigr&ouml;&szlig;e!', 'language_2' => 'The uploaded file exceeds the maximum filesize!');
		$sql_txt_array[] = array('text_id' => '255', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Dateiupload unvollst&auml;ndig!', 'language_2' => 'Fileupload incomplete!');
		$sql_txt_array[] = array('text_id' => '257', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Der Dateiname &quot;[var]filename[/var]&quot; entspricht nicht dem ISO-9660 Standard! Eine automatische &Auml;nderung in &quot;[var]filename_new[/var]&quot; ist m&ouml;glich.', 'language_2' => 'The filename &quot;[var]filename[/var]&quot; is not ISO-9660 compliant! An automatic adaption to &quot;[var]filename_new[/var]&quot; is possible.');
		$sql_txt_array[] = array('text_id' => '258', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Der Verzeichnisname &quot;[var]foldername[/var]&quot; entspricht nicht dem ISO-9660 Standard! Eine automatische &Auml;nderung in &quot;[var]foldername_new[/var]&quot; ist m&ouml;glich.', 'language_2' => 'The foldername &quot;[var]foldername[/var]&quot; is not ISO-9660 compliant! An automatic adaption to &quot;[var]foldername_new[/var]&quot; is possible.');
		$sql_txt_array[] = array('text_id' => '268', 'text_key' => '', 'category' => 'mime_type', 'comment' => '', 'language_1' => 'OpenDocument Pr&auml;sentation', 'language_2' => 'OpenDocument Presentation');
		$sql_txt_array[] = array('text_id' => '274', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Das Datenbank-Backup wurde erfolgreich durchgef&uuml;hrt.', 'language_2' => 'The database backup was successful.');
		$sql_txt_array[] = array('text_id' => '275', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Unter folgendem Link k&ouml;nnen Sie sich die Backup-Datei herunterladen.', 'language_2' => 'Please use the following link to download the backup file.');
		$sql_txt_array[] = array('text_id' => '278', 'text_key' => '', 'category' => 'error_message', 'comment' => '', 'language_1' => 'Der Datentyp der Variable &quot;[var]varname[/var]&quot; wird in [var]datatype[/var] ge&auml;ndert!', 'language_2' => 'The data type of variable &quot;[var]varname[/var]&quot; will be changed to [var]datatype[/var]!');
		$sql_txt_array[] = array('text_id' => '288', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Link hinzuf&uuml;gen', 'language_2' => 'Add link');
		$sql_txt_array[] = array('text_id' => '295', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Link l&ouml;schen', 'language_2' => 'Delete link');
		$sql_txt_array[] = array('text_id' => '296', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie diesen Link wirklich l&ouml;schen?', 'language_2' => 'Do you really want to delete this link?');
		$sql_txt_array[] = array('text_id' => '299', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Der Link wurde gel&ouml;scht.', 'language_2' => 'The link was deleted.');
		$sql_txt_array[] = array('text_id' => '300', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Dateiverkn&uuml;pfung', 'language_2' => 'Filelink');
		$sql_txt_array[] = array('text_id' => '301', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => '&Ouml;ffnen', 'language_2' => 'Open');
		$sql_txt_array[] = array('text_id' => '309', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Keywords f&uuml;r das A-Z Register definieren', 'language_2' => 'Define keywords for the A-Z register');
		$sql_txt_array[] = array('text_id' => '315', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie die Zugriffsrechte wirklich l&ouml;schen?', 'language_2' => 'Do you really want to delete these authorizations?');
		$sql_txt_array[] = array('text_id' => '316', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Zugriffsrechte f&uuml;r Benutzer', 'language_2' => 'User access authorizations');
		$sql_txt_array[] = array('text_id' => '318', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Zugriffsrechte l&ouml;schen', 'language_2' => 'Delete access authorizations');
		$sql_txt_array[] = array('text_id' => '319', 'text_key' => '', 'category' => 'system', 'comment' => 'Zugriffsrechte für die Benutzergruppe [Benutzergruppenname]', 'language_1' => 'Zugriffsrechte f&uuml;r die Benutzergruppe', 'language_2' => 'Access authorizations for usergroup');
		$sql_txt_array[] = array('text_id' => '322', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Das OpenSource DMS File-O-Meter ist ein webbasiertes Dokumentenmanagementsystem zur Ablage und Archivierung von Dokumenten und Dateien. File-O-Meter dient als Plattform zur Zusammenarbeit (Collaboration) und zum Datenaustausch. Die Ablagestruktur untergliedert sich in Projekte. Innerhalb eines Projektverzeichnisses befinden sich die zugeh&ouml;rigen Unterverzeichnisse und Dokumente.', 'language_2' => 'The open source application File-O-Meter is a web-based Document Management System for filing and archiving documents and files. File-O-Meter provides a corporate platform for cooperation, collaboration and data sharing. The filing system is structured into projects. Within a project folder there are the concerning subfolders and documents.');
		$sql_txt_array[] = array('text_id' => '323', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Sollten Sie im Men&uuml; Ihre gew&uuml;nschten Projektordner oder Verzeichnisse vermissen, wenden Sie sich bitte an den f&uuml;r das DMS verantwortlichen Projektleiter. In diesem Fall wurden die entsprechenden Ordner m&ouml;glicherweise noch nicht angelegt oder Sie verf&uuml;gen nicht &uuml;ber die erforderlichen Zugriffsrechte.', 'language_2' => 'Please contact the project leader, responsible for the DMS, if you are missing your wanted project folders or directories within the menu. In this case the concerning folders have to be created first or you are not authorized to access the data at this time.');
		$sql_txt_array[] = array('text_id' => '325', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Bitte geben Sie Ihre Installationspfade zu den folgenden Anwendungen an. Achtung, geben Sie immer nur den Pfad zum Installationsverzeichnis an, nicht den Pfad zur Programmdatei. Zum Beispiel /yourpath/antiword/ nicht /yourpath/antiword/antiword. Achten Sie darauf, dass alle Pfadangaben mit einem / bzw. \ enden!', 'language_2' => 'Please enter your installation paths to the following applications. Attention, please make sure to enter the path to the installation directory, not to the program file. For instance /yourpath/antiword/ but not /yourpath/antiword/antiword. All path values have to end with a slash / or backslash \!');
		$sql_txt_array[] = array('text_id' => '326', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Besuchen Sie [var]website[/var] f&uuml;r weitere Informationen &uuml;ber [var]program[/var].', 'language_2' => 'Visit [var]website[/var] for more information about [var]program[/var].');
		$sql_txt_array[] = array('text_id' => '345', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Ihr Browser unterst&uuml;tzt diese Funktion leider nicht. Bitte versuchen Sie es statt dessen mit Firefox 3, Google Chrome oder Safari 4.', 'language_2' => 'You browser doesn&#039;t support native upload. Try Firefox 3, Google Chrome or Safari 4.');
		$sql_txt_array[] = array('text_id' => '346', 'text_key' => '', 'category' => 'multiupload', 'comment' => '', 'language_1' => 'Dateien ausw&auml;hlen', 'language_2' => 'Select files');
		$sql_txt_array[] = array('text_id' => '347', 'text_key' => '', 'category' => 'multiupload', 'comment' => '', 'language_1' => 'F&uuml;gen Sie Dateien zur Warteschlange hinzu und klicken Sie auf Start.', 'language_2' => 'Add files to the upload queue and click the start button.');
		$sql_txt_array[] = array('text_id' => '349', 'text_key' => '', 'category' => 'multiupload', 'comment' => 'Dateigr&ouml;&szlig;e', 'language_1' => 'Gr&ouml;&szlig;e', 'language_2' => 'Size');
		$sql_txt_array[] = array('text_id' => '351', 'text_key' => '', 'category' => 'multiupload', 'comment' => '', 'language_1' => 'Dateien hinzuf&uuml;gen', 'language_2' => 'Add files');
		$sql_txt_array[] = array('text_id' => '360', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Logbuch', 'language_2' => 'Logbook');
		$sql_txt_array[] = array('text_id' => '361', 'text_key' => '', 'category' => 'calendar', 'comment' => '', 'language_1' => 'TT', 'language_2' => 'DD');
		$sql_txt_array[] = array('text_id' => '362', 'text_key' => '', 'category' => 'calendar', 'comment' => '', 'language_1' => 'MM', 'language_2' => 'MM');
		$sql_txt_array[] = array('text_id' => '363', 'text_key' => '', 'category' => 'calendar', 'comment' => '', 'language_1' => 'JJJJ', 'language_2' => 'YYYY');
		$sql_txt_array[] = array('text_id' => '364', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Anzahl', 'language_2' => 'Number');
		$sql_txt_array[] = array('text_id' => '365', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?', 'language_2' => 'Do you really want to delete this entry?');
		$sql_txt_array[] = array('text_id' => '366', 'text_key' => '', 'category' => 'success_message', 'comment' => '', 'language_1' => 'Der Eintrag wurde gel&ouml;scht.', 'language_2' => 'The entry has been deleted.');
		$sql_txt_array[] = array('text_id' => '367', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Login', 'language_2' => 'Login');
		$sql_txt_array[] = array('text_id' => '368', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Logout', 'language_2' => 'Logout');
		$sql_txt_array[] = array('text_id' => '369', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Dauer', 'language_2' => 'Duration');
		$sql_txt_array[] = array('text_id' => '370', 'text_key' => '', 'category' => 'system', 'comment' => 'IP Adresse', 'language_1' => 'IP', 'language_2' => 'IP');
		$sql_txt_array[] = array('text_id' => '371', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Logindatum von', 'language_2' => 'Login date from');
		$sql_txt_array[] = array('text_id' => '372', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Logindatum bis', 'language_2' => 'Login date to');
		$sql_txt_array[] = array('text_id' => '373', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Lokales Login', 'language_2' => 'Local Login');
		$sql_txt_array[] = array('text_id' => '374', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Webservice Login', 'language_2' => 'Webservice Login');
		$sql_txt_array[] = array('text_id' => '375', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Logintyp', 'language_2' => 'Logintype');
		$sql_txt_array[] = array('text_id' => '376', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Aktuelle Auswahl L&ouml;schen', 'language_2' => 'Delete current selection');
		$sql_txt_array[] = array('text_id' => '377', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis download', 'language_2' => 'Download directory');
		$sql_txt_array[] = array('text_id' => '378', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie wirklich das Verzeichnis Downloaden?', 'language_2' => 'Do you really want to download this directory?');
		$sql_txt_array[] = array('text_id' => '379', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Achtung: je nach gr&ouml;&szlig;e des Verzeichnisses kann der Vorgang mehrere Minuten dauern!', 'language_2' => 'Note: depending on the size of the directory, the process may take several minutes!');
		$sql_txt_array[] = array('text_id' => '380', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Zip &amp; Start Download', 'language_2' => 'Zip &amp; Start Download');
		$sql_txt_array[] = array('text_id' => '381', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Projekt l&ouml;schen', 'language_2' => 'Delete project');
		$sql_txt_array[] = array('text_id' => '382', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie wirklich dieses Projekt l&ouml;schen?', 'language_2' => 'Do you really want to delete this project?');
		$sql_txt_array[] = array('text_id' => '383', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie wirklich dieses Projekt inkl. dem gesamten Inhalt endg&uuml;ltig l&ouml;schen?', 'language_2' => 'Do you really want to delete permanently this project?');
		$sql_txt_array[] = array('text_id' => '384', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Gel&ouml;schte Objekte anzeigen', 'language_2' => 'Show deleted items');
		$sql_txt_array[] = array('text_id' => '385', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Keine Gel&ouml;schten Objekte vorhanden', 'language_2' => 'No deleted objects exist');
		$sql_txt_array[] = array('text_id' => '386', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Projekt wiederherstellen', 'language_2' => 'project restore');
		$sql_txt_array[] = array('text_id' => '387', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Projekt entg&uuml;ltig l&ouml;schen', 'language_2' => 'delete permanently this project');
		$sql_txt_array[] = array('text_id' => '388', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie wirklich dieses Projekt wiederherstellen?', 'language_2' => 'Would you really restore this project?');
		$sql_txt_array[] = array('text_id' => '389', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'W&auml;hlen Sie min. eine Datei aus!', 'language_2' => 'Select one or more files!');
		$sql_txt_array[] = array('text_id' => '390', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie wirklich die ausgew&auml;hlten Dateien l&ouml;schen!', 'language_2' => 'Would you really delete the selected files!');
		$sql_txt_array[] = array('text_id' => '391', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'W&auml;hlen Sie min. ein Verzeichnis aus!', 'language_2' => 'Select one or more folder!');
		$sql_txt_array[] = array('text_id' => '392', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie wirklich die ausgew&auml;hlten Verzeichnisse inkl. Inhalt l&ouml;schen!', 'language_2' => 'Would you really delete the selected directories incl. content!');
		$sql_txt_array[] = array('text_id' => '393', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'W&auml;hlen Sie min. einen Link aus!', 'language_2' => 'Select at least a link!');
		$sql_txt_array[] = array('text_id' => '394', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&ouml;chten Sie wirklich die ausgew&auml;hlten Links l&ouml;schen!', 'language_2' => 'Would you really delete the selected links!');
		$sql_txt_array[] = array('text_id' => '395', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'M&uuml;lleimer', 'language_2' => 'Trash');
		$sql_txt_array[] = array('text_id' => '396', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis&uuml;bersicht', 'language_2' => 'Directory overview');
		$sql_txt_array[] = array('text_id' => '397', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnisname', 'language_2' => 'directory Name');
		$sql_txt_array[] = array('text_id' => '398', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Pfad', 'language_2' => 'path');
		$sql_txt_array[] = array('text_id' => '399', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Alle Ausgew&auml;hlten Verzeichnisse inkl. Inhalt entg&uuml;ltig L&ouml;schen', 'language_2' => 'delete all selected directories incl. content');
		$sql_txt_array[] = array('text_id' => '400', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Alle Ausgew&auml;hlten Dateien entg&uuml;ltig L&ouml;schen', 'language_2' => 'delete all selected files');
		$sql_txt_array[] = array('text_id' => '401', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Link&uuml;bersicht', 'language_2' => 'Link overview');
		$sql_txt_array[] = array('text_id' => '402', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Alle Ausgew&auml;hlten Links entg&uuml;ltig L&ouml;schen', 'language_2' => 'delete all selected links');
		$sql_txt_array[] = array('text_id' => '403', 'text_key' => 'access_mn', 'category' => 'system', 'comment' => '', 'language_1' => 'E-Mail Benachrichtigung', 'language_2' => 'Mail Notification');
		$sql_txt_array[] = array('text_id' => '404', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'E-Mail Typ', 'language_2' => 'E-mail type');
		$sql_txt_array[] = array('text_id' => '405', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Text E-Mail', 'language_2' => 'Text e-mail');
		$sql_txt_array[] = array('text_id' => '406', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'HTML E-Mail', 'language_2' => 'HTML e-mail');
		$sql_txt_array[] = array('text_id' => '407', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis angelegt', 'language_2' => 'directory created');
		$sql_txt_array[] = array('text_id' => '408', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis bearbeitet', 'language_2' => 'edited directory');
		$sql_txt_array[] = array('text_id' => '409', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis kopiert', 'language_2' => 'directory copy');
		$sql_txt_array[] = array('text_id' => '410', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis verschoben', 'language_2' => 'moved directory');
		$sql_txt_array[] = array('text_id' => '411', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Verzeichnis gel&ouml;scht', 'language_2' => 'directory is deleted');
		$sql_txt_array[] = array('text_id' => '412', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei hinzugef&uuml;gt', 'language_2' => 'file added');
		$sql_txt_array[] = array('text_id' => '413', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei bearbeitet', 'language_2' => 'edited file');
		$sql_txt_array[] = array('text_id' => '414', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei kopiert', 'language_2' => 'file copied');
		$sql_txt_array[] = array('text_id' => '415', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei verschoben', 'language_2' => 'file is moved');
		$sql_txt_array[] = array('text_id' => '416', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Version angelegt', 'language_2' => 'version is created');
		$sql_txt_array[] = array('text_id' => '417', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei eingecheckt', 'language_2' => 'File is checked');
		$sql_txt_array[] = array('text_id' => '418', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei ausgecheckt', 'language_2' => 'File is checked out');
		$sql_txt_array[] = array('text_id' => '419', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Datei gel&ouml;scht', 'language_2' => 'file is deleted');
		$sql_txt_array[] = array('text_id' => '420', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Link hinzugef&uuml;gt', 'language_2' => 'link added');
		$sql_txt_array[] = array('text_id' => '421', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Link bearbeitet', 'language_2' => 'link edited');
		$sql_txt_array[] = array('text_id' => '422', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => 'Link gel&ouml;scht', 'language_2' => 'link deleted');
		$sql_txt_array[] = array('text_id' => '423', 'text_key' => '', 'category' => 'system', 'comment' => '', 'language_1' => '&Auml;nderungszeit', 'language_2' => 'change time');

		for ($i = 0; $i < count($sql_txt_array); $i++)
		{
			if (isset($sql_txt_array[$i]['text_id']))
			{
				//Pruefen ob bereits vorhanden
				$sql = $cdb->select('SELECT text_id FROM fom_text WHERE text_id='.$sql_txt_array[$i]['text_id']);
				$result = $cdb->fetch_array($sql);

				//text noch nicht vorhanden
				if (!isset($result['text_id']) or empty($result['text_id']))
				{
					$txt_exists = false;
				}
				else
				{
					$txt_exists = true;
				}

				$column_string = '';
				$value_string = '';
				$sql_string = '';
				foreach ($sql_txt_array[$i] as $column => $value)
				{
					if ($txt_exists == false)
					{
						if (!empty($column_string))
						{
							$column_string .= ', ';
						}

						$column_string .= $column;

						if (!empty($value_string))
						{
							$value_string .= ', ';
						}

						if ($column == 'text_key' or $column == 'category' or $column == 'comment')
						{
							if (!empty($value))
							{
								$value_string .= "'".$value."'";
							}
							else
							{
								$value_string .= "NULL";
							}
						}
						else
						{
							$value_string .= "'".$value."'";
						}
					}
					else
					{
						if ($column != 'text_id')
						{
							if (!empty($sql_string))
							{
								$sql_string .= ', ';
							}

							if ($column == 'text_key' or $column == 'category' or $column == 'comment')
							{
								if (!empty($value))
								{
									$sql_string .= $column."='".$value."'";
								}
								else
								{
									$sql_string .= $column."=NULL";
								}
							}
							else
							{
								$sql_string .= $column."='".$value."'";
							}
						}
					}
				}

				if ($txt_exists == false)
				{
					$sql_string = 'INSERT INTO fom_text ('.$column_string.') VALUES ('.$value_string.')';
				}
				else
				{
					$sql_string = 'UPDATE fom_text SET '.$sql_string.' WHERE text_id='.$sql_txt_array[$i]['text_id'].' LIMIT 1';
				}

				if ($cdb->query($sql_string) === false)
				{
					$sql_error_array[] = $sql_string;
				}
			}
		}

		$tbl_array = array();
		$sql = $cdb->select('SHOW TABLES');
		while ($tbl_result = $cdb->fetch_row($sql))
		{
			$tbl_array[] = $tbl_result[0];
		}

		//neue Tabellen erstellen
		if (!in_array('fom_log_login', $tbl_array))
		{
			if ($cdb->query("CREATE TABLE fom_log_login (
							  log_id int(10) unsigned NOT NULL auto_increment,
							  user_id int(10) unsigned NOT NULL default '0',
							  login_time varchar(14) default NULL,
							  logout_time varchar(14) default NULL,
							  ip varchar(29) default NULL,
							  login_session varchar(32) default NULL,
							  login_type set('local','webservice') NOT NULL default 'local',
							  PRIMARY KEY  (log_id),
							  KEY session_index (login_session),
							  KEY login_time_index (login_time(8)),
							  KEY user_id_index (user_id),
							  KEY login_type_index (login_type)
							) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Login und Logout Aktivitaeten'") === false)
			{
				$sql_error_array[] = "CRECREATE TABLE fom_log_login (
									  log_id int(10) unsigned NOT NULL auto_increment,
									  user_id int(10) unsigned NOT NULL default '0',
									  login_time varchar(14) default NULL,
									  logout_time varchar(14) default NULL,
									  ip varchar(29) default NULL,
									  login_session varchar(32) default NULL,
									  login_type set('local','webservice') NOT NULL default 'local',
									  PRIMARY KEY  (log_id),
									  KEY session_index (login_session),
									  KEY login_time_index (login_time(8)),
									  KEY user_id_index (user_id),
									  KEY login_type_index (login_type)
									) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Login und Logout Aktivitaeten'";
			}
		}

		if (!in_array('fom_mn_log', $tbl_array))
		{
			if ($cdb->query("CREATE TABLE fom_mn_log (
							 log_id int(10) unsigned NOT NULL auto_increment,
							 id int(10) unsigned default NULL,
							 user_id int(10) unsigned default NULL,
							 org_name varchar(255) default NULL,
							 event varchar(50) default NULL,
							 event_time varchar(14) default NULL,
							 PRIMARY KEY  (log_id),
							 KEY event_index (event(4))
							) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Events die per E-Mailbenachrichtigung versend'") === false)
			{
				$sql_error_array[] = "CREATE TABLE fom_mn_log (
									 log_id int(10) unsigned NOT NULL auto_increment,
									 id int(10) unsigned default NULL,
									 user_id int(10) unsigned default NULL,
									 org_name varchar(255) default NULL,
									 event varchar(50) default NULL,
									 event_time varchar(14) default NULL,
									 PRIMARY KEY  (log_id),
									 KEY event_index (event(4))
									) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Events die per E-Mailbenachrichtigung versend'";
			}
		}

		if (!in_array('fom_mn_setup', $tbl_array))
		{
			if ($cdb->query("CREATE TABLE fom_mn_setup (
							 user_id int(10) unsigned default NULL,
							 projekt_id int(10) unsigned default NULL,
							 mn_setup text,
							 UNIQUE KEY user_project_index (user_id,projekt_id)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert bei welchem event der User eine Mailbenachrichtigu'") === false)
			{
				$sql_error_array[] = "CREATE TABLE fom_mn_setup (
									 user_id int(10) unsigned default NULL,
									 projekt_id int(10) unsigned default NULL,
									 mn_setup text,
									 UNIQUE KEY user_project_index (user_id,projekt_id)
									) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert bei welchem event der User eine Mailbenachrichtigu'";
			}
		}

		//Tabellen aendern
		$column_sql = $cdb->query("SHOW COLUMNS FROM fom_projekte WHERE Field='anzeigen'");
		$column_result = $cdb->fetch_array($column_sql);

		if (!isset($column_result['Field']) or empty($column_result['Field']))
		{
			if ($cdb->query("ALTER TABLE fom_projekte ADD anzeigen SET('0', '1') NULL DEFAULT '1'") === false)
			{
				$sql_error_array[] = "ALTER TABLE fom_projekte ADD anzeigen SET('0', '1') NULL DEFAULT '1'";
			}
		}

		//Vollzugriff fuer Logbuch
		$sql = $cdb->select("SELECT type FROM fom_access WHERE type='_LOGBOOK_V' AND usergroup_id=1");
		$result = $cdb->fetch_array($sql);

		$other_access_array = array('r'		=>	true,	//Read
									'w'		=>	true,	//Write
									'd'		=>	false,	//Delete
									'vo'	=>	false,	//Version overview
									'va'	=>	false,	//Add version
									'dl'	=>	false,	//Create downloadlink
									'as'	=>	false,	//Edit access control
									'di'	=>	false,	//Data import
									'de'	=>	false,	//Data export
									'ocf'	=>	false,//Edit check-in/check-out status
									'mn'	=>	false);	//E-Mail Benachrichtigung

		$other_access_string = serialize($other_access_array);

		if (!isset($result['type']) or empty($result['type']))
		{
			if (!$cdb->insert("INSERT INTO fom_access (type, id, user_id, usergroup_id, access) VALUES ('_LOGBOOK_V', 0, 0, 1, '$other_access_string')"))
			{
				$sql_error_array[] = "INSERT INTO fom_access (type, id, user_id, usergroup_id, access) VALUES ('_LOGBOOK_V', 0, 0, 1, '$other_access_string')";
			}
		}
		else
		{
			if (!$cdb->insert("UPDATE fom_access SET access='$other_access_string' WHERE type='_LOGBOOK_V' AND usergroup_id=1"))
			{
				$sql_error_array[] = "UPDATE fom_access SET access='$other_access_string' WHERE type='_LOGBOOK_V' AND usergroup_id=1";
			}
		}

		//Fom Version aendern
		$cdb->update("UPDATE fom_setup SET fom_version='$update_to' WHERE setup_id=1");

		$file_error_array = array();

		if (!file_exists(FOM_ABS_PFAD.'files/log/mail/'))
		{
			if (!@mkdir(FOM_ABS_PFAD.'files/log/mail/'))
			{
				$file_error_array[] = '<strong>DE:</strong>&nbsp;Das Verzeichnis "'.FOM_ABS_PFAD.'files/log/mail/" konnte nicht erstellt werden!&nbsp;&nbsp;<strong>EN:</strong>The directory "'.FOM_ABS_PFAD.'files/log/mail/" was not created!';
			}
		}

		//Webservice anpassen
		if (file_exists(FOM_ABS_PFAD.'web_services/access.wsdl') and file_exists(FOM_ABS_PFAD.'web_services/fom.wsdl'))
		{
			$is_writable = false;
			$change_chmod = false;
			if (!is_writable(FOM_ABS_PFAD.'web_services/fom.wsdl') or !is_writable(FOM_ABS_PFAD.'web_services/access.wsdl'))
			{
				@chmod(FOM_ABS_PFAD.'web_services/fom.wsdl', 0777);
				@chmod(FOM_ABS_PFAD.'web_services/access.wsdl', 0777);

				$change_chmod = true;

				if (is_writable(FOM_ABS_PFAD.'web_services/fom.wsdl') and is_writable(FOM_ABS_PFAD.'web_services/access.wsdl'))
				{
					$is_writable = true;
				}
			}
			else
			{
				$is_writable = true;
			}

			if ($is_writable == true)
			{
				$fom_wsdl = file_get_contents(FOM_ABS_PFAD.'web_services/fom.wsdl');
				$access_wsdl = file_get_contents(FOM_ABS_PFAD.'web_services/access.wsdl');

				$fom_replace = '<soap:address location="'.FOM_ABS_URL.'web_services/fom.php"/>';
				$access_replace = '<soap:address location="'.FOM_ABS_URL.'web_services/access.php"/>';

				$new_fom_wsdl = preg_replace('/<soap:address location="(.*)"\/>/', $fom_replace, $fom_wsdl);
				$new_access_wsdl = preg_replace('/<soap:address location="(.*)"\/>/', $access_replace, $access_wsdl);

				$write_wsdl = true;
				if ($fom_wsdl != $new_fom_wsdl)
				{
					if (file_put_contents(FOM_ABS_PFAD.'web_services/fom.wsdl', $new_fom_wsdl) === false)
					{
						$write_wsdl = false;
					}
				}
				if ($access_wsdl != $new_access_wsdl)
				{
					if (file_put_contents(FOM_ABS_PFAD.'web_services/access.wsdl', $new_access_wsdl) === false)
					{
						$write_wsdl = false;
					}
				}

				if ($change_chmod == true)
				{
					@chmod(FOM_ABS_PFAD.'web_services/fom.wsdl', 0744);
					@chmod(FOM_ABS_PFAD.'web_services/access.wsdl', 0744);
				}

				if ($write_wsdl == false)
				{
					$file_error_array[] = '<strong>DE:</strong>&nbsp;Die Dateien "web_services/access.wsdl" und "web_services/fom.wsdl" konnten nicht beschrieben werden. Bitte pr&uuml;fen Sie, dass die Dateien vorhanden und beschreibbar sind (CHMOD).&nbsp;&nbsp;<strong>EN:</strong>&nbsp;Unable to modify the files "web_services/access.wsdl" and "web_services/fom.wsdl". Please check that these files are available and writeable (CHMOD).';
				}
			}
			else
			{
				$file_error_array[] = '<strong>DE:</strong>&nbsp;Die Dateien "web_services/access.wsdl" und "web_services/fom.wsdl" konnten nicht beschrieben werden. Bitte Pr&uuml;fen Sie, dass die Dateien vorhanden und beschreibbar sind (CHMOD).&nbsp;&nbsp;<strong>EN:</strong>&nbsp;Unable to modify the files "web_services/access.wsdl" and "web_services/fom.wsdl". Please check that these files are available and writeable (CHMOD).';
			}
		}
		else
		{
			$file_error_array[] = '<strong>DE:</strong>&nbsp;Die Dateien "web_services/access.wsdl" und "web_services/fom.wsdl" konnten nicht beschrieben werden. Bitte Pr&uuml;fen Sie, dass die Dateien vorhanden und beschreibbar sind (CHMOD).&nbsp;&nbsp;<strong>EN:</strong>&nbsp;Unable to modify the files "web_services/access.wsdl" and "web_services/fom.wsdl". Please check that these files are available and writeable (CHMOD).';
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>File-O-Meter Update <?php echo $update_to; ?></title>
<link rel="stylesheet" media="screen" type="text/css" href="template/default/screen.css" />
</head>
<body>
	<?php
		if (!isset($_GET['step']) or $_GET['step'] == 1)
		{
	?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="middle">
					<td class="info_table_header" width="100%">File-O-Meter Update <?php echo $update_to; ?></td>
				</tr>
				<tr><td><img src="template/default/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
				<tr>
					<td class="info_table_content">
						<ul>
							<li><strong>DE:</strong>
								<ul>
									<li>Sie k&ouml;nnen alle &Auml;nderungen in der Datei changelog.txt nachlesen. Diese Anleitung bezieht sich auf das Update von <?php echo $update_from; ?> auf <?php echo $update_to; ?></li>
									<li>Beachten Sie auch die Hinweise aus der readme.txt</li>
									<li>Erstellen Sie eine Sicherungskopie ihrer aktuellen Installation. Achten Sie besonders darauf, dass die Dateien/Ordner config/config.php, config/cryptpw_salt.php und files/* gesichert sind.</li>
									<li>Laden Sie alle neuen FOM Dateien auf Ihren Server.</li>
									<li>Achtung - &Uuml;berschreiben bzw. ersetzen Sie <strong>nicht</strong> nachfolgende Dateien/Ordner:
										<ul>
											<li>config/*</li>
											<li>files/*</li>
										</ul>
									</li>
									<li>Wenn Sie alle oben beschriebenen Schritte durchgef&uuml;hrt haben, klicken Sie auf den Button "<a href="update.php?step=2"><u>n&auml;chster Schritt</u></a>"</li>
								</ul>
							</li>
						</ul>
						<br /><br />
						<ul>
							<li><strong>EN:</strong>
								<ul>
									<li>All changes can be found within the file changelog.txt. This manual applies to the update from version <?php echo $update_from; ?> to <?php echo $update_to; ?></li>
									<li>Please pay attention to the notes in the readme.txt</li>
									<li>Please create a backup of your current installation. Please pay attention to backup primarily the following folders/files: config/config.php, config/cryptpw_salt.php and files/*.</li>
									<li>Upload all files to your server.</li>
									<li>Attention - do <strong>not</strong> overwrite the following files/folders:
										<ul>
											<li>config/*</li>
											<li>files/*</li>
										</ul>
									</li>
									<li>If you have accomplished all steps described above you may click the button "<a href="update.php?step=2"><u>next step</u></a>"</li>
								</ul>
							</li>
						</ul>
					</td>
				</tr>
			</table>
	<?php
		}
		elseif (isset($_GET['step']) and $_GET['step'] == 2)
		{
			$error_exists = false;
			$error_string = '';
			if (!empty($sql_error_array))
			{
				$error_exists = true;

				$error_string .= '<ul><strong>DE:</strong>&nbsp;Nachfolgende SQL-Fehler sind aufgetreten. Bitte pr&uuml;fen Sie die SQL-Syntax zu Ihrer MySql-Version und f&uuml;hren Sie die SQL-Befehle gegebenenfalls von Hand aus.<br /><strong>EN:</strong>&nbsp;The following SQL errors have occurred. Please check the SQL syntax for your MySQL version and execute the concerning SQL commandos manually if necessary.';

				foreach ($sql_error_array as $sql)
				{
					$error_string .= '<li>'.$sql.'</li>';
				}
				$error_string .= '</ul><br />';
			}

			if (!empty($file_error_array))
			{
				$error_exists = true;

				$error_string .= '<ul><strong>DE:</strong>&nbsp;Nachfolgende Datei- oder Verzeichnisfehler sind aufgetreten.<br /><strong>EN:</strong>&nbsp;The following file- or folder-errors have occurred.';

				foreach ($file_error_array as $file)
				{
					$error_string .= '<li>'.$file.'</li>';
				}
				$error_string .= '</ul><br /><br />';
			}

			//Fehler vorhanden
			if ($error_exists)
			{
				$error_string .= '<ul><strong>DE:</strong>&nbsp;Es ist min. ein Fehler aufgetreten. Bitte f&uuml;hren Sie die Anweisungen in den Fehlermeldungen aus. Es wird empfohlen den Installationsschritt so oft zu wiederholen bis keine Fehlermeldung mehr erscheint.
									<li><a href="update.php?step=2"><u>Installationsschritt wiederholen</u></a></li>
									</ul>';
				$error_string .= '<ul><strong>EN:</strong>&nbsp;At least 1 error has occurred. Please follow the instructions within the error messages. We recommend to rerun this installation step until all error messages are eliminated.
									<li><a href="update.php?step=2"><u>repeat installation step</u></a></li>
									</ul>';

?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%">
					<tr valign="middle">
						<td class="error_table_header" width="100%">Fehlermeldung / Error Message</td>
					</tr>
					<tr><td><img src="template/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
					<tr>
						<td class="error_table_content">
							<?php echo $error_string; ?>
						</td>
					</tr>
				</table>
<?php
			}
			else
			{
?>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr valign="middle">
						<td class="good_table_header" width="100%">Erfolgsmeldungen / Good news</td>
					</tr>
					<tr><td><img src="template/default/pic/_spacer.gif" width="1" height="4" border="0" alt="" /></td></tr>
					<tr>
						<td class="good_table_content">
							<ul>
								<li><strong>DE:</strong>&nbsp;Das Update ist abgeschlossen. Sie k&ouml;nnen jetzt zur <a href="index.php"><u>Loginseite</u></a> wechseln.</li>
								<li><strong>EN:</strong>&nbsp;The update-procedure is finished. You may change now to the <a href="index.php"><u>login page</u></a>.</li>
								<li>
									<ul>
										<li><strong>DE:</strong> Sie k&ouml;nnen die Datei "update.php" jetzt l&ouml;schen.</li>
										<li><strong>DE:</strong> Sie k&ouml;nnen das Verzeichnis "inc/class/pear/" l&ouml;schen.</li>
										<li><strong>EN:</strong> Finally you also may delete this file (update.php).</li>
										<li><strong>EN:</strong> You can delete the directory "inc / class / php /" delete.</li>
									</ul>
								</li>
							</ul>
						</td>
					</tr>
				</table>
<?php
			}
		}
	?>
</body>
</html>
