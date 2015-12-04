-- Als CHARSET sollte UTF-8 gewaehlt werden
-- 2012-07-10

-- Create Table fom_access

CREATE TABLE `fom_access` (
  `type` varchar(10) NOT NULL,
  `id` int(10) unsigned default '0',
  `user_id` int(10) unsigned default '0',
  `usergroup_id` int(10) unsigned default '0',
  `access` text,
  KEY `access_index` (`type`,`id`,`user_id`,`usergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert Zugriffseinstellungen';

-- Create Table fom_backup

CREATE TABLE `fom_backup` (
  `backup_id` int(10) unsigned NOT NULL auto_increment,
  `backup_time` varchar(14) default NULL,
  `filename` varchar(50) default NULL,
  `filesize` float default NULL,
  `type` varchar(50) default NULL,
  `beschreibung` varchar(255) default NULL,
  PRIMARY KEY  (`backup_id`),
  KEY `time_index` (`backup_time`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle erstellten Backups';

-- Create Table fom_document_type

CREATE TABLE `fom_document_type` (
  `document_type_id` int(10) unsigned NOT NULL auto_increment,
  `document_type` varchar(255) default NULL,
  PRIMARY KEY  (`document_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Dokumententypen';

-- Create Table fom_document_type_file

CREATE TABLE `fom_document_type_file` (
  `document_type_id` int(10) unsigned NOT NULL default '0',
  `file_id` int(10) unsigned default '0',
  KEY `document_type_index` (`file_id`,`document_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert sie zuordnung zwischen Datei und Dokumententyp';

-- Create Table fom_download

CREATE TABLE `fom_download` (
  `file_id` int(10) unsigned NOT NULL default '0',
  `md5` char(32) NOT NULL,
  `only_current_version` set('0','1') default '1',
  `save_time` char(14) default '00000000000000',
  `public` set('0','1') default '0',
  `expire` char(14) default '00000000000000',
  `downloads` int(10) unsigned NOT NULL default '0',
  KEY `file_id` (`file_id`),
  KEY `public_index` (`public`),
  KEY `expire_index` (`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Enthaelt alle oeffentlichen Downloadauftraege';

-- Create Table fom_file_job_copy

CREATE TABLE `fom_file_job_copy` (
  `file_id` int(10) unsigned NOT NULL default '0',
  `save_name` varchar(40) default NULL,
  `save_time` varchar(14) NOT NULL default '00000000000000',
  `job_time` varchar(14) NOT NULL default '00000000000000',
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='enthaelt alle dateien die noch auf einen Fileserver Kopierte';

-- Create Table fom_file_job_index

CREATE TABLE `fom_file_job_index` (
  `job_id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned default '0',
  `link_id` int(10) unsigned default '0',
  `save_name` varchar(40) default NULL,
  `last_page` int(10) unsigned NOT NULL default '0',
  `save_time` varchar(14) NOT NULL default '00000000000000',
  `job_time` varchar(14) NOT NULL default '00000000000000',
  PRIMARY KEY  (`job_id`),
  KEY `file_id` (`file_id`),
  KEY `savetime_index` (`save_time`(6))
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Dateien die noch indiziert werden muessen';

-- Create Table fom_file_job_tn

CREATE TABLE `fom_file_job_tn` (
  `job_id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL default '0',
  `save_name` varchar(40) default NULL,
  `save_time` varchar(14) default NULL,
  `job_time` varchar(14) NOT NULL default '00000000000000',
  PRIMARY KEY  (`job_id`),
  KEY `file_id` (`file_id`),
  KEY `savetime_index` (`save_time`(6))
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Create Table fom_file_lock

CREATE TABLE `fom_file_lock` (
  `file_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  KEY `file_id` (`file_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert welche Dateien ausgecheckt sind';

-- Create Table fom_file_server

CREATE TABLE `fom_file_server` (
  `file_server_id` int(10) unsigned NOT NULL auto_increment,
  `projekt_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) default NULL,
  `typ` varchar(50) default NULL,
  `pfad` varchar(250) default NULL,
  `setup` text,
  PRIMARY KEY  (`file_server_id`),
  KEY `projekt_id_index` (`projekt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Fileserverliste fue die Speicherung der Dateien';

-- Create Table fom_file_subversion

CREATE TABLE `fom_file_subversion` (
  `sub_fileid` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `org_name` varchar(50) default NULL,
  `save_name` varchar(40) default NULL,
  `md5_file` varchar(32) default NULL,
  `mime_type` varchar(50) default NULL,
  `file_size` float unsigned default NULL,
  `save_time` varchar(14) NOT NULL default '00000000000000',
  `file_type` varchar(7) NOT NULL default 'PRIMARY',
  PRIMARY KEY  (`sub_fileid`),
  KEY `versions_index` (`file_id`,`save_time`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert subversionen von Dateien';

-- Create Table fom_files

CREATE TABLE `fom_files` (
  `file_id` int(10) unsigned NOT NULL auto_increment,
  `folder_id` int(10) unsigned NOT NULL default '0',
  `file_server_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `org_name` varchar(255) default NULL,
  `org_name_no_iso` varchar(255) default NULL,
  `save_name` varchar(255) default NULL,
  `md5_file` varchar(32) default NULL,
  `mime_type` varchar(50) default NULL,
  `file_size` int(10) unsigned NOT NULL default '0',
  `save_time` varchar(14) NOT NULL default '00000000000000',
  `bemerkungen` text,
  `tagging` varchar(255) default NULL,
  `file_type` varchar(7) NOT NULL default 'PRIMARY',
  `anzeigen` set('0','1') default '1',
  PRIMARY KEY  (`file_id`),
  KEY `SaveFileName` (`save_name`(10)),
  KEY `folder_id_index` (`folder_id`),
  KEY `OrgFileNameIso` (`org_name`(20)),
  KEY `OrgFileNameNoIso` (`org_name_no_iso`(20))
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Dateien';

-- Create Table fom_folder

CREATE TABLE `fom_folder` (
  `folder_id` int(10) unsigned NOT NULL auto_increment,
  `projekt_id` int(10) unsigned default NULL,
  `folder_name` varchar(255) default NULL,
  `bemerkungen` text,
  `ob_folder` int(11) default NULL,
  `ebene` tinyint(3) unsigned default NULL,
  `anzeigen` set('0','1') default '1',
  PRIMARY KEY  (`folder_id`),
  KEY `project_id_index` (`projekt_id`),
  KEY `ob_folder_index` (`ob_folder`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='VerzeichnisTabelle';

-- Create Table fom_languages

CREATE TABLE `fom_languages` (
  `language_id` int(10) unsigned NOT NULL auto_increment,
  `language_name` varchar(30) default NULL,
  `column_fom_text` varchar(10) default NULL,
  `always_visible` char(1) default 'n',
  `visible` char(1) default NULL,
  PRIMARY KEY  (`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='languages';

-- Create Table fom_link

CREATE TABLE `fom_link` (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `folder_id` int(10) unsigned NOT NULL default '0',
  `file_server_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `file_id` int(10) unsigned NOT NULL default '0' COMMENT 'nur bei internen links',
  `name` varchar(255) default NULL,
  `link` varchar(255) default NULL,
  `md5_link` char(32) default NULL,
  `save_time` char(14) default NULL,
  `bemerkungen` text,
  `tagging` varchar(255) default NULL,
  `link_type` set('EXTERNAL','INTERNAL') NOT NULL default 'EXTERNAL',
  `anzeigen` set('0','1') NOT NULL default '1',
  PRIMARY KEY  (`link_id`),
  KEY `folder_id_index` (`folder_id`),
  KEY `show_index` (`anzeigen`),
  KEY `type_index` (`link_type`),
  KEY `file_id_index` (`file_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Links';

-- Create Table fom_log_login

CREATE TABLE `fom_log_login` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `login_time` varchar(14) default NULL,
  `logout_time` varchar(14) default NULL,
  `ip` varchar(29) default NULL,
  `login_session` varchar(32) default NULL,
  `login_type` set('local','webservice') NOT NULL default 'local',
  PRIMARY KEY  (`log_id`),
  KEY `session_index` (`login_session`),
  KEY `login_time_index` (`login_time`(8)),
  KEY `user_id_index` (`user_id`),
  KEY `login_type_index` (`login_type`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Login und Logout Aktivitaeten';

-- Create Table fom_mn_log

CREATE TABLE `fom_mn_log` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `id` int(10) unsigned default NULL,
  `user_id` int(10) unsigned default NULL,
  `org_name` varchar(255) default NULL,
  `event` varchar(50) default NULL,
  `event_time` varchar(14) default NULL,
  PRIMARY KEY  (`log_id`),
  KEY `event_index` (`event`(4))
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert alle Events die per E-Mailbenachrichtigung versend';

-- Create Table fom_mn_setup

CREATE TABLE `fom_mn_setup` (
  `user_id` int(10) unsigned default NULL,
  `projekt_id` int(10) unsigned default NULL,
  `mn_setup` text,
  UNIQUE KEY `user_project_index` (`user_id`,`projekt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert bei welchem event der User eine Mailbenachrichtigu';

-- Create Table fom_projekte

CREATE TABLE `fom_projekte` (
  `projekt_id` int(10) unsigned NOT NULL auto_increment,
  `projekt_name` varchar(30) default NULL,
  `anzeigen` set('0','1') default '1',
  PRIMARY KEY  (`projekt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Projekttabelle';

-- Create Table fom_reload

CREATE TABLE `fom_reload` (
  `reload_id` char(32) default NULL,
  `expire_time` char(14) default NULL,
  KEY `reload_id_index` (`reload_id`),
  KEY `expire_time_index` (`expire_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert alle Reload-Keys';

-- Create Table fom_search_cache

CREATE TABLE `fom_search_cache` (
  `seach_key` varchar(32) default NULL,
  `search_result` text,
  `search_time` varchar(14) default NULL,
  KEY `seach_key` (`seach_key`,`search_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Zwischenspeicherung von Suchergebnissen';

-- Create Table fom_search_stopword

CREATE TABLE `fom_search_stopword` (
  `word` varchar(75) default NULL,
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stopwortliste fur die Suchfunktion';

-- Create Table fom_search_word

CREATE TABLE `fom_search_word` (
  `word_id` int(10) unsigned NOT NULL auto_increment,
  `word` varchar(75) default NULL,
  PRIMARY KEY  (`word_id`),
  KEY `word` (`word`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Enthaelt alle Worter nach denen gesucht werden kann';

-- Create Table fom_search_word_az_file

CREATE TABLE `fom_search_word_az_file` (
  `word_id` int(10) unsigned NOT NULL default '0',
  `file_id` int(10) unsigned NOT NULL default '0',
  `sub_fileid` int(10) unsigned NOT NULL default '0',
  `sign` char(1) default NULL,
  KEY `sign_index` (`sign`),
  KEY `file_index` (`file_id`),
  KEY `sub_file_idex` (`sub_fileid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert die AZ Registerzuordnungen';

-- Create Table fom_search_word_az_link

CREATE TABLE `fom_search_word_az_link` (
  `word_id` int(10) unsigned NOT NULL default '0',
  `link_id` int(10) unsigned NOT NULL default '0',
  `sign` char(1) default NULL,
  KEY `sign_index` (`sign`),
  KEY `link_index` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert die AZ Registerzuordnungen';

-- Create Table fom_search_word_file

CREATE TABLE `fom_search_word_file` (
  `word_id` int(10) unsigned NOT NULL default '0',
  `file_id` int(10) unsigned default '0',
  `sub_fileid` int(10) unsigned default '0',
  `tagging` set('0','1') NOT NULL default '0',
  KEY `word_id` (`word_id`,`file_id`,`sub_fileid`,`tagging`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Enthaelt die zuordnung zwischen suchwoertern und Dateien';

-- Create Table fom_search_word_link

CREATE TABLE `fom_search_word_link` (
  `word_id` int(10) unsigned NOT NULL,
  `link_id` int(10) unsigned default '0',
  `tagging` set('1','0') NOT NULL default '0',
  KEY `word_id` (`word_id`,`link_id`,`tagging`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Create Table fom_session

CREATE TABLE `fom_session` (
  `sess_key` varchar(32) NOT NULL,
  `sess_value` mediumtext,
  `sess_expiry` varchar(14) default NULL,
  KEY `sess_key` (`sess_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert alle Sessiondaten';

-- Create Table fom_setup

CREATE TABLE `fom_setup` (
  `setup_id` tinyint(3) unsigned NOT NULL auto_increment,
  `backup` text,
  `mail` text,
  `main_language_id` int(11) NOT NULL default '1',
  `contact` text,
  `fom_version` varchar(12) default NULL,
  `fom_title` varchar(30) default NULL,
  `date_format` varchar(10) default NULL,
  `template` varchar(10) default NULL,
  `other_settings` text,
  PRIMARY KEY  (`setup_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Setupeinstellungen';

-- Create Table fom_sub_files

CREATE TABLE `fom_sub_files` (
  `file_id` int(10) unsigned NOT NULL default '0',
  `subfile_id` int(10) unsigned NOT NULL default '0',
  KEY `file_id` (`file_id`,`subfile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert die Verknuepfung zwischen File und SubFile';

-- Create Table fom_text

CREATE TABLE `fom_text` (
  `text_id` int(10) unsigned NOT NULL auto_increment,
  `text_key` varchar(50) default NULL,
  `category` varchar(35) default NULL,
  `comment` text,
  `language_1` text,
  `language_2` text,
  PRIMARY KEY  (`text_id`),
  KEY `scan_str` (`text_key`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='text phrases for all languages';

-- Create Table fom_user

CREATE TABLE `fom_user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `vorname` varchar(50) default NULL,
  `nachname` varchar(50) default NULL,
  `email` varchar(75) default NULL,
  `loginname` varchar(50) default NULL,
  `pw` varchar(32) default NULL,
  `session_key` varchar(32) default NULL,
  `login_ip` varchar(23) default NULL,
  `login_trials` tinyint(3) unsigned NOT NULL default '0',
  `timeout` varchar(10) default NULL,
  `language_id` int(11) unsigned NOT NULL default '1',
  `login_aktiv` set('0','1') default '1',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Usertabelle';

-- Create Table fom_user_group

CREATE TABLE `fom_user_group` (
  `usergroup_id` int(10) unsigned NOT NULL auto_increment,
  `usergroup` varchar(50) default NULL,
  PRIMARY KEY  (`usergroup_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Speichert die Usergruppen';

-- Create Table fom_user_membership

CREATE TABLE `fom_user_membership` (
  `user_id` int(10) NOT NULL default '0',
  `usergroup_id` int(10) NOT NULL default '0',
  KEY `select_index` (`user_id`,`usergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='stores the group-memberships for each user';

-- Create Table fom_webservice_access

CREATE TABLE `fom_webservice_access` (
  `ws_key` char(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `expire` char(14) default NULL,
  KEY `select_index` (`ws_key`,`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='speichert die keys fuer den webservice zugriff';

-- Volle Zugriffsrechte fuer die erste Benutzergruppe
INSERT INTO fom_access (type, id, user_id, usergroup_id, access) VALUES ('_USER_V', 0, 0, 1, 'a:11:{s:1:"r";b:1;s:1:"w";b:1;s:1:"d";b:0;s:2:"vo";b:0;s:2:"va";b:0;s:2:"dl";b:0;s:2:"as";b:0;s:2:"di";b:0;s:2:"de";b:0;s:3:"ocf";b:0;s:2:"mn";b:0;}');

INSERT INTO fom_access (type, id, user_id, usergroup_id, access) VALUES ('_USER_G', 0, 0, 1, 'a:11:{s:1:"r";b:1;s:1:"w";b:1;s:1:"d";b:0;s:2:"vo";b:0;s:2:"va";b:0;s:2:"dl";b:0;s:2:"as";b:0;s:2:"di";b:0;s:2:"de";b:0;s:3:"ocf";b:0;s:2:"mn";b:0;}');

INSERT INTO fom_access (type, id, user_id, usergroup_id, access) VALUES ('_PROJECT_V', 0, 0, 1, 'a:11:{s:1:"r";b:1;s:1:"w";b:1;s:1:"d";b:0;s:2:"vo";b:0;s:2:"va";b:0;s:2:"dl";b:0;s:2:"as";b:0;s:2:"di";b:0;s:2:"de";b:0;s:3:"ocf";b:0;s:2:"mn";b:0;}');

INSERT INTO fom_access (type, id, user_id, usergroup_id, access) VALUES ('_SETUP_V', 0, 0, 1, 'a:11:{s:1:"r";b:1;s:1:"w";b:1;s:1:"d";b:0;s:2:"vo";b:0;s:2:"va";b:0;s:2:"dl";b:0;s:2:"as";b:0;s:2:"di";b:0;s:2:"de";b:0;s:3:"ocf";b:0;s:2:"mn";b:0;}');

INSERT INTO fom_access (type, id, user_id, usergroup_id, access) VALUES ('_LOGBOOK_V', 0, 0, 1, 'a:11:{s:1:"r";b:1;s:1:"w";b:1;s:1:"d";b:0;s:2:"vo";b:0;s:2:"va";b:0;s:2:"dl";b:0;s:2:"as";b:0;s:2:"di";b:0;s:2:"de";b:0;s:3:"ocf";b:0;s:2:"mn";b:0;}');

INSERT INTO fom_user_group (usergroup_id, usergroup) VALUES (1, 'ADMIN');

INSERT INTO fom_setup VALUES (1, 'a:5:{s:11:"aktiv_boole";b:1;s:16:"mail_aktiv_boole";b:0;s:15:"mail_link_boole";b:0;s:18:"mail_adress_string";s:0:"";s:10:"time_array";a:8:{s:2:"mo";i:3;s:2:"di";i:3;s:2:"mi";i:3;s:2:"do";i:3;s:2:"fr";i:3;s:2:"sa";i:3;s:2:"so";i:3;s:3:"all";i:3;}}', 'a:11:{s:4:"from";s:23:"noreply@file-o-meter.de";s:8:"fromname";s:12:"File-O-Meter";s:7:"altbody";s:54:"Bitte aktivieren Sie die Darstellung von HTML E-Mails!";s:8:"sendtype";s:8:"sendmail";s:8:"sendmail";s:0:"";s:8:"smtphost";s:0:"";s:8:"smtpport";s:0:"";s:10:"smtpsecure";s:0:"";s:8:"smtpauth";s:0:"";s:8:"smtpuser";s:0:"";s:6:"smtppw";s:0:"";}', 2, 'a:5:{s:10:"first_name";s:0:"";s:9:"last_name";s:0:"";s:5:"email";s:0:"";s:5:"phone";s:0:"";s:5:"handy";s:0:"";}', '0.6b', 'File-O-Meter 0.6b', 'DD.MM.YYYY', 'default', 'a:2:{s:7:"ex_prog";a:3:{s:8:"antiword";s:0:"";s:4:"xpdf";s:0:"";s:11:"ghostscript";s:0:"";}s:7:"logbook";a:1:{s:5:"login";b:1;}}');

INSERT INTO fom_languages (language_id, language_name, column_fom_text, always_visible, visible) VALUES (1, 'Deutsch', '1', 'j', 'j'), (2, 'English', '2', 'n', 'j');

-- Insert for fom_text

INSERT INTO `fom_text` VALUES (1, 'username', 'login', NULL, 'Benutzername', 'Username'),
(2, 'pw', 'login', NULL, 'Passwort', 'Password'),
(3, NULL, 'login', NULL, 'Anmelden', 'Login'),
(4, NULL, 'system', NULL, 'Ansprechpartner', 'Contact person'),
(5, 'tel', 'system', NULL, 'Telefon', 'Phone'),
(6, 'handy', 'system', NULL, 'Handy', 'Mobile'),
(7, 'email', 'system', NULL, 'E-Mail', 'E-Mail'),
(8, NULL, 'error_message', NULL, 'Bitte geben Sie das Passwort an.', 'Please enter the password.'),
(9, NULL, 'error_message', NULL, 'Bitte geben Sie den Benutzernamen an.', 'Please enter the username.'),
(10, NULL, 'error_message', NULL, 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es zu einem sp&auml;teren Zeitpunkt noch einmal.', 'An error has occurred. Please retry it later.'),
(11, NULL, 'error_message', NULL, 'Zu den angegebenen Logindaten ist kein Benutzerkonto vorhanden.', 'There is no useraccount matching your logindata.'),
(12, NULL, 'error_message', NULL, 'Ihr Benutzerkonto wurde deaktiviert. Bitte wenden Sie sich an den Systemadministrator.', 'Your account was disabled. Please contact your systemadministrator.'),
(13, NULL, 'error_message', NULL, 'Ihr Benutzerkonto wurde bis [var]timeout[/var] deaktiviert.', 'Your account was disabled till [var]timeout[/var].'),
(14, 'januar', 'calendar', NULL, 'Januar', 'January'),
(15, 'februar', 'calendar', NULL, 'Februar', 'February'),
(16, 'maerz', 'calendar', NULL, 'M&auml;rz', 'March'),
(17, 'april', 'calendar', NULL, 'April', 'April'),
(18, 'mai', 'calendar', NULL, 'Mai', 'May'),
(19, 'juni', 'calendar', NULL, 'Juni', 'June'),
(20, 'juli', 'calendar', NULL, 'Juli', 'July'),
(21, 'august', 'calendar', NULL, 'August', 'August'),
(22, 'september', 'calendar', NULL, 'September', 'September'),
(23, 'oktober', 'calendar', NULL, 'Oktober', 'October'),
(24, 'november', 'calendar', NULL, 'November', 'November'),
(25, 'dezember', 'calendar', NULL, 'Dezember', 'December'),
(26, 'kw', 'calendar', 'Abkürzung für Kalenderwoche', 'Kw', 'Cw'),
(27, 'kalenderwoche', 'calendar', NULL, 'Kalenderwoche', 'Calendarweek'),
(28, 'montag', 'calendar', NULL, 'Montag', 'Monday'),
(29, 'dienstag', 'calendar', NULL, 'Dienstag', 'Tuesday'),
(30, 'mittwoch', 'calendar', NULL, 'Mittwoch', 'Wednesday'),
(31, 'donnerstag', 'calendar', NULL, 'Donnerstag', 'Thursday'),
(32, 'freitag', 'calendar', NULL, 'Freitag', 'Friday'),
(33, 'samstag', 'calendar', NULL, 'Samstag', 'Saturday'),
(34, 'sonntag', 'calendar', NULL, 'Sonntag', 'Sunday'),
(35, 'heute', 'calendar', NULL, 'Heute', 'Today'),
(36, 'mo', 'calendar', 'Abkürzung für Montag', 'Mo', 'Mo'),
(37, 'di', 'calendar', 'Abkürzung für Dienstag', 'Di', 'Tue'),
(38, 'mi', 'calendar', 'Abkürzung für Mittwoch', 'Mi', 'Wed'),
(39, 'do', 'calendar', 'Abkürzung für Donnerstag', 'Do', 'Thu'),
(40, 'fr', 'calendar', 'Abkürzung für Freitag', 'Fr', 'Fri'),
(41, 'sa', 'calendar', 'Abkürzung für Samstag', 'Sa', 'Sat'),
(42, 'so', 'calendar', 'Abkürzung für Sonntag', 'So', 'Sun'),
(43, NULL, 'login', NULL, 'Sie sind eingeloggt als', 'You are logged in as'),
(44, NULL, 'login', NULL, 'Logout in', 'Logout in'),
(45, 'logout', 'login', NULL, 'Abmelden', 'Logout'),
(46, 'back', 'system', NULL, 'zur&uuml;ck', 'back'),
(47, NULL, 'system', NULL, 'Erfolgsmeldungen', 'Success messages'),
(48, NULL, 'system', NULL, 'Fehlermeldungen', 'Error messages'),
(49, 'ja', 'system', NULL, 'ja', 'yes'),
(50, 'nein', 'system', NULL, 'nein', 'no'),
(51, 'save', 'system', NULL, 'Speichern', 'Save'),
(52, 'edit', 'system', NULL, 'Bearbeiten', 'Edit'),
(53, 'del', 'system', NULL, 'L&ouml;schen', 'Delete'),
(54, 'please_select', 'system', NULL, 'Bitte w&auml;hlen', 'Please select'),
(55, NULL, 'system', NULL, 'Benutzerverwaltung', 'Useraccount management'),
(56, NULL, 'system', NULL, 'Benutzergruppenverwaltung', 'Usergroup management'),
(57, NULL, 'system', NULL, 'Projektverwaltung', 'Project management'),
(58, NULL, 'system', NULL, 'Grundeinstellungen', 'Basic setup'),
(59, 'backup', 'system', NULL, 'Backup', 'Backup'),
(60, NULL, 'system', NULL, 'Automatisches Backup aktiv', 'Automatic backup enabled'),
(61, NULL, 'system', NULL, 'E-Mail Benachrichtigung senden', 'Send E-Mail notification'),
(62, NULL, 'system', NULL, 'Downloadlink mitsenden', 'Send downloadlink'),
(63, NULL, 'system', NULL, 'Empf&auml;nger (E-Mail)', 'Recipient (E-Mail)'),
(64, NULL, 'system', NULL, 'Backup-Zeiten', 'Backup dates'),
(65, 'daily', 'system', NULL, 't&auml;glich', 'daily'),
(66, NULL, NULL, NULL, 'Absender (E-Mail)', 'Sender (E-Mail)'),
(67, NULL, 'system', NULL, 'Absender (Name)', 'Sender (Name)'),
(68, NULL, 'system', 'Alternativer Textinhalt für den Mailbody', 'Alternativer Textinhalt', 'Alternative Textcontent'),
(69, NULL, 'system', 'Default-Text für den alternativen E-Mail Body', 'Bitte aktivieren Sie die Darstellung von HTML E-Mails in Ihrem E-Mail Programm!', 'Please enable HTML E-Mails in your E-Mail client software!'),
(70, NULL, 'system', NULL, 'Mail Transfer Agent', 'Mail transfer agent'),
(71, 'smtp', 'system', 'E-Mail Postausgangstyp', 'SMTP', 'SMTP'),
(72, 'sendmail', 'system', 'E-Mail Postausgangstyp', 'Sendmail', 'Sendmail'),
(73, NULL, 'system', NULL, 'Sendmailpfad', 'Sendmailpath'),
(74, NULL, 'system', NULL, 'SMTP-Server', 'SMTP-Server'),
(75, NULL, 'system', NULL, 'SMTP-Port', 'SMTP-Port'),
(76, NULL, 'system', NULL, 'SMTP-Sicherheit', 'SMTP-Security'),
(77, 'keine', 'system', NULL, 'keine', 'none'),
(78, NULL, 'system', NULL, 'SMTP-Authentifizierung', 'SMTP-Authentication'),
(79, NULL, 'system', NULL, 'SMTP-Benutzer', 'SMTP-User'),
(80, NULL, 'system', NULL, 'SMTP-Passwort', 'SMTP-Password'),
(81, 'uhr', 'system', 'bei Zeitangaben, z.B. 10 Uhr', 'Uhr', 'o&#039;clock'),
(82, NULL, 'system', NULL, 'Dokumententypen', 'Document types'),
(83, NULL, 'system', NULL, 'Anzahl der Dokumententypen', 'Number of document types'),
(84, NULL, 'system', NULL, 'Bezeichnung', 'Name'),
(85, NULL, 'system', NULL, 'Dokumententyp', 'Document type'),
(86, 'actions', 'system', NULL, 'Aktionen', 'Actions'),
(87, 'no_data', 'system', NULL, 'Kein Eintrag vorhanden!', 'No entries found!'),
(88, NULL, 'system', NULL, 'Dokumententyp bearbeiten', 'Edit document type'),
(89, NULL, 'system', NULL, 'Neuen Dokumententyp anlegen', 'Add document type'),
(90, NULL, 'error_message', NULL, 'Bitte geben Sie eine Bezeichnung f&uuml;r den Dokumententyp an!', 'Please enter a document type name!'),
(91, NULL, 'error_message', NULL, 'Bitte geben Sie eine E-Mail Adresse an!', 'Please enter an E-Mail address!'),
(92, NULL, 'error_message', NULL, 'Bitte geben Sie mindestens eine Backupzeit an!', 'Please enter at least one backup date!'),
(93, 'error', 'error_message', NULL, 'Es ist ein Fehler aufgetreten!', 'An error has occurred!'),
(94, 'error_code', 'error_message', NULL, 'Fehlercode', 'Error code'),
(95, NULL, 'error_message', NULL, 'Bitte f&uuml;llen Sie alle Pflichtfelder aus!', 'Please complete all mandatory fields!'),
(96, NULL, 'success_message', NULL, 'Der Datensatz wurde angelegt.', 'The dataset was created.'),
(97, NULL, 'success_message', NULL, 'Die &Auml;nderungen wurden gespeichert.', 'The changes were successfully saved.'),
(98, 'reload', 'error_message', NULL, 'Eine Reloadsperre hat das erneute Eintragen verhindert!', 'A reload blockade prevented double data entry!'),
(99, NULL, 'error_message', NULL, 'Der angegebene Verzeichnisname wird bereits verwendet!', 'The specified foldername already exists!'),
(100, NULL, 'error_message', NULL, 'Der angegebene Dateiname wird bereits verwendet!', 'The specified filename already exists!'),
(101, NULL, 'success_message', NULL, 'Das Verzeichnis wurde gel&ouml;scht.', 'The folder was deleted.'),
(102, NULL, 'error_message', NULL, 'Das Verzeichnis ist nicht leer!', 'The folder is not empty!'),
(103, NULL, 'success_message', NULL, 'Die Datei wurde gespeichert.', 'The file was successfully saved.'),
(104, NULL, 'success_message', NULL, 'Die Datei wurde gel&ouml;scht.', 'The file was deleted.'),
(105, NULL, 'success_message', NULL, 'Die Datei wurde ausgecheckt.', 'The file was checked out.'),
(106, NULL, 'success_message', NULL, 'Die Datei wurde eingecheckt.', 'The file was checked in.'),
(107, NULL, 'success_message', NULL, 'Der Importvorgang wurde erfolgreich durchgef&uuml;hrt.', 'Data import successfully finished.'),
(108, NULL, 'success_message', NULL, 'Der Exportvorgang wurde erfolgreich durchgef&uuml;hrt!', 'Data export successfully finished.'),
(109, NULL, 'success_message', 'Mit Doppelpunkt am Ende! Es wurde folgender Downloadlink erstellt:', 'Es wurde folgender Downloadlink erstellt:', 'The following downloadlink was created:'),
(110, NULL, 'success_message', NULL, 'Das Benutzerkonto wurde angelegt.', 'The useraccount was created.'),
(111, NULL, 'error_message', NULL, 'Bitte geben Sie eine Benutzergruppe an!', 'Please specify an usergroup!'),
(112, 'firstname', 'system', NULL, 'Vorname', 'First name'),
(113, 'lastname', 'system', NULL, 'Nachname', 'Last name'),
(114, 'usergroup', 'system', NULL, 'Benutzergruppe', 'Usergroup'),
(115, NULL, 'system', NULL, 'Benutzerkonto anlegen', 'Create useraccount'),
(116, NULL, 'system', NULL, 'Benutzerkonto bearbeiten', 'Edit useraccount'),
(117, NULL, 'system', NULL, 'Leer lassen, um den derzeitigen Wert beizubehalten.', 'Leave blank to retain current value.'),
(118, NULL, 'system', NULL, 'Benutzerkonto aktiv', 'Useraccount enabled'),
(119, NULL, 'system', NULL, 'Benutzergruppe hinzuf&uuml;gen', 'Add usergroup'),
(120, NULL, 'system', NULL, 'Projektverzeichnis', 'Project folder'),
(121, NULL, 'system', NULL, 'Anzahl der zugeordneten Benutzer', 'Number of assigned users'),
(122, 'show', 'system', NULL, 'Anzeigen', 'Show'),
(123, NULL, 'system', NULL, 'Benutzergruppe bearbeiten', 'Edit usergroup'),
(124, 'project', 'system', NULL, 'Projekt', 'Project'),
(125, NULL, 'system', NULL, 'Projekt hinzuf&uuml;gen', 'Add project'),
(126, NULL, 'system', NULL, 'Achtung: Das Projekt erscheint erst im Verzeichnisbaum, wenn es den gew&uuml;nschten Benutzergruppen zugeordnet wurde! Die Zuordnung von Projekten zu Benutzergruppen erfolgt im Men&uuml;punkt &quot;Benutzergruppenverwaltung&quot;.', 'Attention: The project will not appear within the directory tree unless it was assigned to the desired usergroups! The assignment of projects and usergroups can be specified under menu item &quot;Usergroup management&quot;'),
(127, NULL, 'system', NULL, 'Dateiserver des Projektes', 'Fileserver of the project'),
(128, NULL, 'system', NULL, 'Projekt bearbeiten', 'Edit project'),
(129, 'access_r', 'access_authorization', NULL, 'Lesen', 'Read'),
(130, 'access_w', 'access_authorization', NULL, 'Schreiben', 'Write'),
(131, 'access_d', 'access_authorization', NULL, 'L&ouml;schen', 'Delete'),
(132, 'access_vo', 'access_authorization', NULL, 'Versions&uuml;bersicht', 'Version overview'),
(133, 'access_va', 'access_authorization', NULL, 'Version anlegen', 'Add version'),
(134, 'access_dl', 'access_authorization', NULL, 'Downloadlink erstellen', 'Create downloadlink'),
(135, 'access_as', 'access_authorization', NULL, 'Zugriffssteuerung bearbeiten', 'Edit access control'),
(136, 'access_di', 'access_authorization', NULL, 'Datenimport', 'Data import'),
(137, 'access_de', 'access_authorization', NULL, 'Datenexport', 'Data export'),
(138, 'access_ocf', 'access_authorization', NULL, 'Check-in/Check-out Status bearbeiten', 'Edit check-in/check-out status'),
(139, 'file', 'system', NULL, 'Datei', 'File'),
(140, 'version', 'system', NULL, 'Version', 'Version'),
(141, NULL, 'system', NULL, 'Immer die aktuellste Version verwenden.', 'Use always the newest version.'),
(142, NULL, 'system', NULL, 'Nur diese Version zum Download anbieten.', 'Provide only this version for download.'),
(143, NULL, 'system', NULL, 'Kein Zeitlimit', 'No timelimit'),
(144, NULL, 'system', NULL, 'Verf&uuml;gbar bis', 'Available till'),
(145, 'calendar', 'calendar', NULL, 'Kalender', 'Calendar'),
(146, NULL, 'system', NULL, 'Derzeitige Datei', 'Current file'),
(147, NULL, 'system', NULL, 'Die derzeitige Datei wird durch eine neue Version ersetzt. Die bisherige Datei bleibt in der Versionshistorie verf&uuml;gbar.', 'The current file will be replaced by a new version. The old file remains available within the versionhistory.'),
(148, 'folder', 'system', NULL, 'Verzeichnis', 'Folder'),
(149, 'description', 'system', NULL, 'Beschreibung', 'Description'),
(150, NULL, 'system', NULL, 'Verzeichnis anlegen', 'Create folder'),
(151, NULL, 'error_message', NULL, 'Bitte w&auml;hlen Sie eine Datei aus!', 'Please select a file!'),
(152, NULL, 'system', NULL, 'Datei hinzuf&uuml;gen', 'Add file'),
(153, NULL, 'system', NULL, 'Suchbegriffe', 'Keywords'),
(154, NULL, 'system', NULL, 'Keine Auswahl', 'No selection'),
(155, NULL, 'system', NULL, 'Mehrfachauswahl mit gedr&uuml;ckter STRG-Taste', 'Hold CTRL-Key for multiple selection'),
(156, NULL, 'system', NULL, 'SubDatei hinzuf&uuml;gen', 'Add subfile'),
(157, NULL, 'system', NULL, 'Datei l&ouml;schen', 'Delete file'),
(158, NULL, 'system', NULL, 'M&ouml;chten Sie diese Datei wirklich l&ouml;schen?', 'Do you really want to delete this file?'),
(159, NULL, 'system', NULL, 'Verzeichnis l&ouml;schen', 'Delete folder'),
(160, NULL, 'system', NULL, 'M&ouml;chten Sie dieses Verzeichnis wirklich l&ouml;schen?', 'Do you really want to delete this folder?'),
(161, NULL, 'system', NULL, 'Datei bearbeiten', 'Edit file'),
(162, NULL, 'system', NULL, 'Verzeichnis bearbeiten', 'Edit folder'),
(163, 'filename', 'system', NULL, 'Dateiname', 'Filename'),
(164, 'filesize', 'system', NULL, 'Dateigr&ouml;&szlig;e', 'Filesize'),
(165, NULL, 'system', NULL, 'Hochgeladen am', 'Uploaded on'),
(166, NULL, 'system', NULL, 'Datei&uuml;bersicht', 'File overview'),
(167, NULL, 'system', NULL, 'Hochgeladen von', 'Uploaded by'),
(168, NULL, 'system', NULL, 'Versionshistorie', 'Version history'),
(169, 'download', 'system', NULL, 'Download', 'Download'),
(170, NULL, 'system', NULL, 'SubDateien einblenden', 'Show subfiles'),
(171, NULL, 'system', NULL, 'Datei auschecken', 'Checkout file'),
(172, NULL, 'system', NULL, 'Datei einchecken', 'Checkin file'),
(173, NULL, 'system', NULL, 'M&ouml;chten Sie diese Datei wirklich auschecken?', 'Do you really want to checkout this file?'),
(174, NULL, 'system', NULL, 'M&ouml;chten Sie diese Datei wirklich einchecken?', 'Do you really want to checkin this file?'),
(175, NULL, 'system', NULL, 'Achtung: Diese Datei wurde nicht von Ihnen ausgecheckt!', 'Attention: You haven&#039;t checked out this file!'),
(176, NULL, 'system', 'Mit Doppelpunkt am Ende:', 'Bei der Bearbeitung von Zugriffsrechten sind folgende Beschr&auml;nkungen zu beachten:', 'Please regard the following restrictions for the configuration of access authorizations:'),
(177, NULL, 'system', NULL, 'Man darf sich selbst keine Zugriffsrechte gew&auml;hren, &uuml;ber die man nicht verf&uuml;gt.', 'It is not possible to grant authorizations to oneself, which one does not have.'),
(178, NULL, 'system', NULL, 'Man darf sich selbst keine Zugriffsrechte entziehen.', 'It is not possible to revoke access authorizations from oneself.'),
(179, NULL, 'system', NULL, 'Man darf nur Benutzergruppen bearbeiten, die &uuml;ber die gleichen oder weniger Zugriffsrechte verf&uuml;gen.', 'One may only edit usergroups which are provided with the same or fewer access authorizations. '),
(180, NULL, 'system', NULL, 'Man darf keine Rechte fremder Gruppen bearbeiten, &uuml;ber die man selbst nicht verf&uuml;gt.', 'It is not possible to edit access authorizations of other usergroups, which one does not have.'),
(181, NULL, 'system', NULL, 'Benutzergruppe ausw&auml;hlen', 'Select usergroup'),
(182, NULL, 'system', NULL, 'Zugriffsrechte', 'Access authorizations'),
(183, NULL, 'system', NULL, 'Suchoptionen', 'Search options'),
(184, NULL, 'system', NULL, 'Enthaltener Text', 'Contained text'),
(185, 'all', 'system', NULL, 'Alle', 'All'),
(186, NULL, 'system', 'Inklusive Unterordner', 'Inkl. Unterordner', 'Incl. subfolders'),
(187, 'search', 'system', NULL, 'Suchen', 'Search'),
(188, NULL, 'system', NULL, 'Relevanz', 'Relevance'),
(189, NULL, 'system', NULL, 'Exportoptionen', 'Export options'),
(190, NULL, 'system', NULL, 'Der Datenexport verl&auml;uft in zwei Arbeitsschritten. Im ersten Schritt werden die Exportdaten untersucht. Wenn keine Fehler gefunden werden, k&ouml;nnen die Daten anschlie&szlig;end im zweiten Schritt exportiert werden.', 'The data export procedure is devided into two steps. In the first step export data will be examined. If no errors are found, the data can be exported afterwards in the second step.'),
(191, NULL, 'system', NULL, 'Nur aktuellste Dateiversion exportieren', 'Export only the newest version'),
(192, NULL, 'system', NULL, 'Nur Dateien exportieren, die bis vor dem folgendem Datum hochgeladen wurden.', 'Export only files which were uploaded before the following date.'),
(193, NULL, 'system', NULL, 'Nur Dateien mit folgenden Dateiendungen exportieren', 'Export only files with the following file extensions'),
(194, NULL, 'system', NULL, 'Dateien mit den folgenden Dateiendungen nicht exportieren', 'Don&#039;t export files with the following file extensions'),
(195, NULL, 'system', NULL, 'Weiter: Daten untersuchen', 'Next: Examine data'),
(196, NULL, 'system', NULL, 'Bereits existierende Verzeichnisse werden gel&ouml;scht!', 'Already existing folders will be deleted!'),
(197, NULL, 'system', NULL, 'Es wurden keine Fehler gefunden. Sie k&ouml;nnen den Export nun starten.', 'No errors found. You may start the export now.'),
(198, NULL, 'error_message', NULL, 'Es ist ein Fehler in den Konfigurationseinstellungen aufgetreten!', 'A configuration error has occurred!'),
(199, NULL, 'error_message', NULL, 'Es darf nur eine Exportoption f&uuml;r die Dateiendung gew&auml;hlt werden.', 'There is only one exportoption allowed for the fileextension.'),
(200, NULL, 'error_message', NULL, 'Bitte geben Sie ein Datum an!', 'Please specify a date!'),
(201, NULL, 'system', NULL, 'Export starten', 'Start export'),
(202, 'step', 'system', NULL, 'Schritt', 'Step'),
(203, NULL, 'system', NULL, 'Versionsoptionen', 'Version options'),
(204, NULL, 'system', NULL, 'Vorhandene Dateien durch aktuelle Version ersetzen.', 'Replace current files by newer versions.'),
(205, NULL, 'system', NULL, 'Bereits vorhandene Dateien und Verzeichnisse im Zielverzeichnis l&ouml;schen, wenn sie nicht in den Importdaten enthalten sind?', 'Delete existing files and subfolders of the targetfolder, if they are not part of the importdata?'),
(206, NULL, NULL, NULL, 'Achtung: Der Importvorgang kann mehrere Minuten dauern.', 'Attention: The importprocedure can take several minutes.'),
(207, NULL, 'system', NULL, 'Import starten', 'Start import'),
(208, NULL, 'system', NULL, 'Sie haben keine Quelldaten f&uuml;r den Import ausgew&auml;hlt!', 'You have no sourcedata selected for the import!'),
(209, NULL, 'system', NULL, 'Achtung: Das automatische Anpassen von Datei- und Verzeichnisnamen kann zu unvorhersehbaren Ergebnissen f&uuml;hren!', 'Attention: The automatic adaptation of file and folder names can lead to unexpected results!'),
(210, NULL, 'system', NULL, 'Beispielsweise werden Dateinamen automatisch auf eine Maximall&auml;nge von 30 Zeichen reduziert.', 'For instance filenames will automatically be reduced to a maximum length of 30 characters.'),
(211, NULL, 'system', NULL, 'Existieren mehrere Dateien innerhalb eines Verzeichnisses, deren Dateinamen auf den ersten 30 Zeichen identisch sind, wird nur die erste Datei importiert. Alle anderen Dateien werden ignoriert!', 'If there are multiple files within the same folder, whose filenames are identical on the first 30 characters, only the first file will be imported. All other files will be ignored.'),
(212, 'error_message', 'system', NULL, 'Fehlermeldung', 'Error message'),
(213, 'type', 'system', NULL, 'Typ', 'Type'),
(214, NULL, 'system', NULL, 'Fehler', 'Error'),
(215, NULL, 'system', NULL, 'Warnung', 'Warning'),
(216, 'notice', 'system', NULL, 'Hinweis', 'Notice'),
(217, NULL, 'system', NULL, 'Es wurden keine Fehler gefunden.', 'No errors found.'),
(218, NULL, 'system', NULL, 'Dieser Vorgang kann je nach Datenmenge einige Minuten dauern!', 'This procedure can take some minutes depending on the amaount of data!'),
(219, NULL, 'system', NULL, 'Datei- und Verzeichnisnamen automatisch anpassen', 'Adapt file- and foldernames automatically'),
(220, NULL, 'system', NULL, 'Keine Daten vorhanden', 'No data available'),
(221, NULL, 'system', NULL, 'Alle ausw&auml;hlen', 'Select all'),
(222, NULL, 'system', NULL, 'Daten erneut pr&uuml;fen', 'Examine data again'),
(223, NULL, 'system', NULL, 'Bitte w&auml;hlen Sie mindestens ein Verzeichnis oder eine Datei f&uuml;r den Import aus!', 'Please select at least one folder or one file for the import!'),
(224, NULL, 'error_message', NULL, 'Bild nicht gefunden', 'Image not found'),
(225, NULL, 'system', NULL, 'Verzeichnis &ouml;ffnen', 'Open folder'),
(226, NULL, 'system', NULL, 'Datei kopieren', 'Copy file'),
(227, NULL, 'system', NULL, 'Datei verschieben', 'Move file'),
(228, 'close', 'system', NULL, 'Schlie&szlig;en', 'Close'),
(229, NULL, 'system', NULL, 'Verzeichnis kopieren', 'Copy folder'),
(230, NULL, 'system', NULL, 'Verzeichnis verschieben', 'Move folder'),
(231, 'paste', 'system', NULL, 'Einf&uuml;gen', 'Paste'),
(232, NULL, 'system', NULL, 'Dateiupload', 'Fileupload'),
(233, 'turnover_page', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'Seite', 'Page'),
(234, 'turnover_of', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'von', 'of'),
(235, 'turnover_results', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'Ergebnisse', 'Results'),
(236, 'turnover_to', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'bis', 'to'),
(237, 'turnover_first', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'erste Seite', 'first page'),
(238, 'turnover_prev', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'eine Seite zur&uuml;ck', 'previous page'),
(239, 'turnover_next', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'eine Seite weiter', 'next page'),
(240, 'turnover_last', 'turnover', 'Dieser Text ist Teil der Blättern-Funktion.', 'letzte Seite', 'last page'),
(241, NULL, 'system', NULL, 'Dateien kopieren', 'Copy files'),
(242, NULL, 'system', NULL, 'Dateien verschieben', 'Move files'),
(243, NULL, 'system', NULL, 'Verzeichnisse kopieren', 'Copy folders'),
(244, NULL, 'system', NULL, 'Verzeichnisse verschieben', 'Move folders'),
(245, NULL, 'error_message', NULL, 'Das Verzeichnis &quot;[var]foldername[/var]&quot; konnte nicht erstellt werden!', 'Folder &quot;[var]foldername[/var]&quot; could not be created!'),
(246, NULL, 'error_message', NULL, 'Der Pfad f&uuml;r das Verzeichnis &quot;[var]foldername[/var]&quot; ist zu lang! Die maximale Pfadl&auml;nge betr&auml;gt 255 Zeichen.', 'The path for the folder &quot;[var]foldername[/var]&quot; exceeds the allowed length! The maximum path length is 255 characters.'),
(247, NULL, 'error_message', NULL, 'Der Pfad f&uuml;r die Datei &quot;[var]filename[/var]&quot; ist zu lang!', 'The path for the file &quot;[var]filename[/var]&quot; exceeds the allowed length!'),
(248, NULL, 'error_message', NULL, 'Die Datei konnte nicht gespeichert werden!', 'The file could not be saved!'),
(249, NULL, 'error_message', NULL, 'Die Datei konnte nicht gefunden werden!', 'The file could not be found!'),
(250, NULL, 'error_message', NULL, 'Die &Auml;nderungen konnten nicht gespeichert werden!', 'The changes could not be saved!');

-- Insert for fom_text

INSERT INTO `fom_text` VALUES (251, NULL, 'error_message', NULL, 'Die Datei konnte nicht indiziert werden!', 'The file could not be indexed!'),
(252, NULL, 'error_message', NULL, 'Die Datei wurde bereits importiert!', 'The file was already imported!'),
(253, NULL, 'error_message', NULL, 'Ung&uuml;ltiger Dateiname!', 'Invalid filename!'),
(254, NULL, 'error_message', NULL, 'Die hochgeladene Datei &uuml;berschreitet die maximale Dateigr&ouml;&szlig;e!', 'The uploaded file exceeds the maximum filesize!'),
(255, NULL, 'error_message', NULL, 'Dateiupload unvollst&auml;ndig!', 'Fileupload incomplete!'),
(256, NULL, 'error_message', NULL, 'Es wurde keine Datei hochgeladen!', 'There was no file uploaded!'),
(257, NULL, 'error_message', NULL, 'Der Dateiname &quot;[var]filename[/var]&quot; entspricht nicht dem ISO-9660 Standard! Eine automatische &Auml;nderung in &quot;[var]filename_new[/var]&quot; ist m&ouml;glich.', 'The filename &quot;[var]filename[/var]&quot; is not ISO-9660 compliant! An automatic adaption to &quot;[var]filename_new[/var]&quot; is possible.'),
(258, NULL, 'error_message', NULL, 'Der Verzeichnisname &quot;[var]foldername[/var]&quot; entspricht nicht dem ISO-9660 Standard! Eine automatische &Auml;nderung in &quot;[var]foldername_new[/var]&quot; ist m&ouml;glich.', 'The foldername &quot;[var]foldername[/var]&quot; is not ISO-9660 compliant! An automatic adaption to &quot;[var]foldername_new[/var]&quot; is possible.'),
(259, NULL, 'error_message', NULL, 'Es konnte keine Verbindung zur Datenbank hergestellt werden.', 'The connection to the database could not be established.'),
(260, NULL, 'error_message', NULL, 'Es wurden keine Verbindungsdaten zum Datenbankserver gefunden.', 'No connection data found for the database server.'),
(261, NULL, 'error_message', NULL, 'Der MySql-Query ist fehlerhaft.', 'Incorrect MySQL-Query.'),
(262, NULL, 'error_message', NULL, 'Unerlaubter MySql-Query.', 'Unauthorized MySql-Query.'),
(263, NULL, 'error_message', NULL, 'Die angegebene Datenbank &quot;[var]database[/var]&quot; konnte nicht gefunden werden.', 'The specified database &quot;[var]database[/var]&quot; could not be found.'),
(264, NULL, 'error_message', NULL, 'Es konnte keine Verbindung zum Datenbankserver &quot;[var]dbserver[/var]&quot; hergestellt werden.', 'The connection to the database server &quot;[var]dbserver[/var]&quot; could not be established.'),
(265, NULL, 'mime_type', NULL, 'OpenDocument Datenbank', 'OpenDocument Database'),
(266, NULL, 'mime_type', NULL, 'OpenDocument Tabellendokument', 'OpenDocument Spreadsheet'),
(267, NULL, 'mime_type', NULL, 'OpenDocument Zeichnung', 'OpenDocument Drawing'),
(268, NULL, 'mime_type', NULL, 'OpenDocument Pr&auml;sentation', 'OpenDocument Presentation'),
(269, NULL, 'mime_type', NULL, 'OpenDocument Formel', 'OpenDocument Formula'),
(270, NULL, 'mime_type', NULL, 'Sonstiges', 'Miscellaneous'),
(271, NULL, 'system', NULL, 'Hauptsprache', 'Main language'),
(272, 'language', 'system', NULL, 'Sprache', 'Language'),
(273, NULL, 'system', NULL, 'Datenbank-Backup', 'Database backup'),
(274, NULL, 'system', NULL, 'Das Datenbank-Backup wurde erfolgreich durchgef&uuml;hrt.', 'The database backup was successful.'),
(275, NULL, 'system', NULL, 'Unter folgendem Link k&ouml;nnen Sie sich die Backup-Datei herunterladen.', 'Please use the following link to download the backup file.'),
(276, NULL, 'system', NULL, 'Dateiinformationen anzeigen', 'Show file information'),
(277, NULL, 'error_message', NULL, 'Falscher Datentyp der Variable &quot;[var]varname[/var]&quot; - [var]datatype[/var] erwartet!', 'Wrong data type of variable &quot;[var]varname[/var]&quot; - [var]datatype[/var] expected!'),
(278, NULL, 'error_message', NULL, 'Der Datentyp der Variable &quot;[var]varname[/var]&quot; wird in [var]datatype[/var] ge&auml;ndert!', 'The data type of variable &quot;[var]varname[/var]&quot; will be changed to [var]datatype[/var]!'),
(279, NULL, 'error_message', NULL, 'Der Datentyp der Variable [var]varname[/var] konnte nicht ermittelt werden!', 'The data type of variable [var]varname[/var] could not be identified!'),
(280, NULL, 'error_message', NULL, 'Dateiimport fehlgeschlagen:', 'File import failed:'),
(281, 'date_format', 'system', NULL, 'Datumsformat', 'Date format'),
(282, 'template', 'system', NULL, 'Template', 'Template'),
(283, 'db_title', 'system', NULL, 'Datenbanktitel', 'Database title'),
(284, 'YYYY-MM-DD', 'date_format', NULL, 'JJJJ-MM-TT', 'YYYY-MM-DD'),
(285, 'DD.MM.YYYY', 'date_format', NULL, 'TT.MM.JJJJ', 'DD.MM.YYYY'),
(286, 'MM/DD/YYYY', 'date_format', NULL, 'MM/TT/JJJJ', 'MM/DD/YYYY'),
(287, NULL, 'system', NULL, 'Bitte geben Sie einen Link an!', 'Please enter a link!'),
(288, NULL, 'system', NULL, 'Link hinzuf&uuml;gen', 'Add link'),
(289, NULL, 'system', NULL, 'Link', 'Link'),
(290, NULL, 'system', NULL, 'Internet Link', 'Internet Link'),
(291, NULL, 'system', NULL, 'SSL Internet Link', 'SSL Internet Link'),
(292, NULL, 'system', NULL, 'Laufwerkspfad', 'Local path'),
(293, NULL, 'system', NULL, 'FTP Link', 'FTP Link'),
(294, NULL, 'system', NULL, 'Linkname', 'Linkname'),
(295, NULL, 'system', NULL, 'Link l&ouml;schen', 'Delete link'),
(296, NULL, 'system', NULL, 'M&ouml;chten Sie diesen Link wirklich l&ouml;schen?', 'Do you really want to delete this link?'),
(297, NULL, 'system', NULL, 'Link bearbeiten', 'Edit link'),
(298, NULL, 'system', NULL, 'Der Link wurde gespeichert.', 'The link was successfully saved.'),
(299, NULL, 'system', NULL, 'Der Link wurde gel&ouml;scht.', 'The link was deleted.'),
(300, NULL, 'system', NULL, 'Dateiverkn&uuml;pfung', 'Filelink'),
(301, NULL, 'system', NULL, '&Ouml;ffnen', 'Open'),
(302, NULL, 'system', NULL, 'Bitte aktivieren Sie Cookies in Ihrem Internetbrowser!', 'Please enable cookies in your internet browser!'),
(303, NULL, 'system', NULL, 'Anfangsbuchstaben verwenden', 'Use first character'),
(304, NULL, 'system', NULL, 'Zahlen', 'Numbers'),
(305, NULL, 'system', NULL, 'Buchstaben', 'Alphabetic characters'),
(306, NULL, 'system', NULL, 'Sonderzeichen', 'Special char'),
(307, NULL, 'system', NULL, 'Standardupload', 'Standard upload'),
(308, NULL, 'system', NULL, 'A-Z Register Upload', 'A-Z Register Upload'),
(309, NULL, 'system', NULL, 'Keywords f&uuml;r das A-Z Register definieren', 'Define keywords for the A-Z register'),
(310, NULL, 'system', NULL, 'Zeichen', 'Character'),
(311, NULL, 'system', NULL, 'Wort', 'Word'),
(312, NULL, 'system', NULL, 'Benutzergruppenrechte auf Projektebene bearbeiten', 'Edit usergroup authorizations for projects'),
(313, NULL, 'system', NULL, 'Benutzergruppenrechte auf Verzeichnis- / Datei- / Linkebene  bearbeiten', 'Edit usergroup authorizations for folders / files / links'),
(314, NULL, 'system', NULL, 'Benutzerrechte auf Verzeichnis- / Datei- / Linkebene  bearbeiten', 'Edit user authorizations for folders / files / links'),
(315, NULL, 'system', NULL, 'M&ouml;chten Sie die Zugriffsrechte wirklich l&ouml;schen?', 'Do you really want to delete these authorizations?'),
(316, NULL, 'system', NULL, 'Zugriffsrechte f&uuml;r Benutzer', 'User access authorizations'),
(317, NULL, 'system', NULL, 'Benutzer', 'User'),
(318, NULL, 'system', NULL, 'Zugriffsrechte l&ouml;schen', 'Delete access authorizations'),
(319, NULL, 'system', 'Zugriffsrechte für die Benutzergruppe [Benutzergruppenname]', 'Zugriffsrechte f&uuml;r die Benutzergruppe', 'Access authorizations for usergroup'),
(320, NULL, 'system', NULL, 'Willkommen', 'Welcome'),
(321, NULL, 'system', NULL, 'Willkommen in File-O-Meter', 'Welcome to File-O-Meter'),
(322, NULL, 'system', NULL, 'Das OpenSource DMS File-O-Meter ist ein webbasiertes Dokumentenmanagementsystem zur Ablage und Archivierung von Dokumenten und Dateien. File-O-Meter dient als Plattform zur Zusammenarbeit (Collaboration) und zum Datenaustausch. Die Ablagestruktur untergliedert sich in Projekte. Innerhalb eines Projektverzeichnisses befinden sich die zugeh&ouml;rigen Unterverzeichnisse und Dokumente.', 'The open source application File-O-Meter is a web-based Document Management System for filing and archiving documents and files. File-O-Meter provides a corporate platform for cooperation, collaboration and data sharing. The filing system is structured into projects. Within a project folder there are the concerning subfolders and documents.'),
(323, NULL, 'system', NULL, 'Sollten Sie im Men&uuml; Ihre gew&uuml;nschten Projektordner oder Verzeichnisse vermissen, wenden Sie sich bitte an den f&uuml;r das DMS verantwortlichen Projektleiter. In diesem Fall wurden die entsprechenden Ordner m&ouml;glicherweise noch nicht angelegt oder Sie verf&uuml;gen nicht &uuml;ber die erforderlichen Zugriffsrechte.', 'Please contact the project leader, responsible for the DMS, if you are missing your wanted project folders or directories within the menu. In this case the concerning folders have to be created first or you are not authorized to access the data at this time.'),
(324, NULL, 'system', NULL, 'Externe Anwendungen', 'External applications'),
(325, NULL, 'system', NULL, 'Bitte geben Sie Ihre Installationspfade zu den folgenden Anwendungen an. Achtung, geben Sie immer nur den Pfad zum Installationsverzeichnis an, nicht den Pfad zur Programmdatei. Zum Beispiel /yourpath/antiword/ nicht /yourpath/antiword/antiword. Achten Sie darauf, dass alle Pfadangaben mit einem / bzw. \ enden!', 'Please enter your installation paths to the following applications. Attention, please make sure to enter the path to the installation directory, not to the program file. For instance /yourpath/antiword/ but not /yourpath/antiword/antiword. All path values have to end with a slash / or backslash \!'),
(326, NULL, 'system', NULL, 'Besuchen Sie [var]website[/var] f&uuml;r weitere Informationen &uuml;ber [var]program[/var].', 'Visit [var]website[/var] for more information about [var]program[/var].'),
(327, NULL, 'system', NULL, 'Dateiliste erstellen', 'Create filelist'),
(328, NULL, 'system', NULL, 'Dateiliste von &quot;[path]&quot;', 'Filelist from &quot;[path]&quot;'),
(329, NULL, 'system', NULL, 'Datum', 'Date'),
(330, NULL, 'system', NULL, 'Dateiliste', 'Filelist'),
(331, NULL, 'system', NULL, 'Dokument', 'Document'),
(332, NULL, 'system', NULL, 'Erstellt von', 'Created by'),
(333, NULL, 'system', NULL, 'Suche im Verzeichnis &quot;[path]&quot;', 'Search in directory &quot;[path]&quot;'),
(334, NULL, 'system', NULL, 'Suche im Projekt &quot;[path]&quot;', 'Search in project &quot;[path]&quot;'),
(335, NULL, 'system', NULL, 'Erstellungsdatum', 'Creation date'),
(336, NULL, 'system', NULL, 'vor', 'before'),
(337, NULL, 'system', NULL, 'nach', 'after'),
(338, NULL, 'system', NULL, 'PDF Erstellen', 'Create PDF'),
(339, NULL, 'system', NULL, 'Suchergebnis', 'Search Results'),
(340, NULL, 'system', 'nach dem 01.02.2010', 'nach dem', 'after the'),
(341, NULL, 'system', 'vor dem 01.02.2010', 'vor dem', 'before the'),
(342, NULL, 'success_message', NULL, '[var]anzahl_ok[/var] Dateien wurden erfolgreich hochgeladen!', '[var]anzahl_ok[/var] files were successfully saved.'),
(343, NULL, 'error_message', NULL, '[var]anzahl_error[/var] Datei(en) konnte(n) nicht hochgeladen werden!', '[var]anzahl_error[/var] file(s) could not be saved!'),
(344, NULL, 'system', NULL, 'Mehrfach-Dateiupload', 'Multiple fileupload'),
(345, NULL, 'system', NULL, 'Ihr Browser unterst&uuml;tzt diese Funktion leider nicht. Bitte versuchen Sie es statt dessen mit Firefox 3, Google Chrome oder Safari 4.', 'You browser doesn&#039;t support native upload. Try Firefox 3, Google Chrome or Safari 4.'),
(346, NULL, 'multiupload', NULL, 'Dateien ausw&auml;hlen', 'Select files'),
(347, NULL, 'multiupload', NULL, 'F&uuml;gen Sie Dateien zur Warteschlange hinzu und klicken Sie auf Start.', 'Add files to the upload queue and click the start button.'),
(348, NULL, 'multiupload', NULL, 'Status', 'Status'),
(349, NULL, 'multiupload', 'Dateigr&ouml;&szlig;e', 'Gr&ouml;&szlig;e', 'Size'),
(350, NULL, 'multiupload', 'Drag & Drop Beschreibung', 'Ziehen Sie die Dateien hier herein.', 'Drag files here.'),
(351, NULL, 'multiupload', NULL, 'Dateien hinzuf&uuml;gen', 'Add files'),
(352, NULL, 'multiupload', NULL, 'Start', 'Start upload'),
(353, NULL, 'system', NULL, 'Derzeitige Server-Einstellung (php.ini)', 'Current server value (php.ini)'),
(354, NULL, 'system', NULL, 'Passwort-Wiederholung', 'Repeat password'),
(355, NULL, 'system', NULL, 'Derzeitiges Passwort', 'Current password'),
(356, NULL, 'system', NULL, 'Neues Passwort', 'New password'),
(357, NULL, 'system', NULL, 'Passwort &auml;ndern', 'Change password'),
(358, NULL, 'system', NULL, 'Ihre Passwortangaben stimmen nicht &uuml;berein!', 'The entered passwords are not equal!'),
(359, NULL, 'system', NULL, 'Derzeitiges Passwort beibehalten', 'Retain current password'),
(360, NULL, 'system', NULL, 'Logbuch', 'Logbook'),
(361, NULL, 'calendar', NULL, 'TT', 'DD'),
(362, NULL, 'calendar', NULL, 'MM', 'MM'),
(363, NULL, 'calendar', NULL, 'JJJJ', 'YYYY'),
(364, NULL, 'system', NULL, 'Anzahl', 'Number'),
(365, NULL, 'system', NULL, 'M&ouml;chten Sie diesen Eintrag wirklich l&ouml;schen?', 'Do you really want to delete this entry?'),
(366, NULL, 'success_message', NULL, 'Der Eintrag wurde gel&ouml;scht.', 'The entry has been deleted.'),
(367, NULL, 'system', NULL, 'Login', 'Login'),
(368, NULL, 'system', NULL, 'Logout', 'Logout'),
(369, NULL, 'system', NULL, 'Dauer', 'Duration'),
(370, NULL, 'system', 'IP Adresse', 'IP', 'IP'),
(371, NULL, 'system', NULL, 'Logindatum von', 'Login date from'),
(372, NULL, 'system', NULL, 'Logindatum bis', 'Login date to'),
(373, NULL, 'system', NULL, 'Lokales Login', 'Local Login'),
(374, NULL, 'system', NULL, 'Webservice Login', 'Webservice Login'),
(375, NULL, 'system', NULL, 'Logintyp', 'Logintype'),
(376, NULL, 'system', NULL, 'Aktuelle Auswahl L&ouml;schen', 'Delete current selection'),
(377, NULL, 'system', NULL, 'Verzeichnis download', 'Download directory'),
(378, NULL, 'system', NULL, 'M&ouml;chten Sie wirklich das Verzeichnis Downloaden?', 'Do you really want to download this directory?'),
(379, NULL, 'system', NULL, 'Achtung: je nach gr&ouml;&szlig;e des Verzeichnisses kann der Vorgang mehrere Minuten dauern!', 'Note: depending on the size of the directory, the process may take several minutes!'),
(380, NULL, 'system', NULL, 'Zip &amp; Start Download', 'Zip &amp; Start Download'),
(381, NULL, 'system', NULL, 'Projekt l&ouml;schen', 'Delete project'),
(382, NULL, 'system', NULL, 'M&ouml;chten Sie wirklich dieses Projekt l&ouml;schen?', 'Do you really want to delete this project?'),
(383, NULL, 'system', NULL, 'M&ouml;chten Sie wirklich dieses Projekt inkl. dem gesamten Inhalt endg&uuml;ltig l&ouml;schen?', 'Do you really want to delete permanently this project?'),
(384, NULL, 'system', NULL, 'Gel&ouml;schte Objekte anzeigen', 'Show deleted items'),
(385, NULL, 'system', NULL, 'Keine Gel&ouml;schten Objekte vorhanden', 'No deleted objects exist'),
(386, NULL, 'system', NULL, 'Projekt wiederherstellen', 'project restore'),
(387, NULL, 'system', NULL, 'Projekt entg&uuml;ltig l&ouml;schen', 'delete permanently this project'),
(388, NULL, 'system', NULL, 'M&ouml;chten Sie wirklich dieses Projekt wiederherstellen?', 'Would you really restore this project?'),
(389, NULL, 'system', NULL, 'W&auml;hlen Sie min. eine Datei aus!', 'Select one or more files!'),
(390, NULL, 'system', NULL, 'M&ouml;chten Sie wirklich die ausgew&auml;hlten Dateien l&ouml;schen!', 'Would you really delete the selected files!'),
(391, NULL, 'system', NULL, 'W&auml;hlen Sie min. ein Verzeichnis aus!', 'Select one or more folder!'),
(392, NULL, 'system', NULL, 'M&ouml;chten Sie wirklich die ausgew&auml;hlten Verzeichnisse inkl. Inhalt l&ouml;schen!', 'Would you really delete the selected directories incl. content!'),
(393, NULL, 'system', NULL, 'W&auml;hlen Sie min. einen Link aus!', 'Select at least a link!'),
(394, NULL, 'system', NULL, 'M&ouml;chten Sie wirklich die ausgew&auml;hlten Links l&ouml;schen!', 'Would you really delete the selected links!'),
(395, NULL, 'system', NULL, 'M&uuml;lleimer', 'Trash'),
(396, NULL, 'system', NULL, 'Verzeichnis&uuml;bersicht', 'Directory overview'),
(397, NULL, 'system', NULL, 'Verzeichnisname', 'directory Name'),
(398, NULL, 'system', NULL, 'Pfad', 'path'),
(399, NULL, 'system', NULL, 'Alle Ausgew&auml;hlten Verzeichnisse inkl. Inhalt entg&uuml;ltig L&ouml;schen', 'delete all selected directories incl. content'),
(400, NULL, 'system', NULL, 'Alle Ausgew&auml;hlten Dateien entg&uuml;ltig L&ouml;schen', 'delete all selected files'),
(401, NULL, 'system', NULL, 'Link&uuml;bersicht', 'Link overview'),
(402, NULL, 'system', NULL, 'Alle Ausgew&auml;hlten Links entg&uuml;ltig L&ouml;schen', 'delete all selected links'),
(403, 'access_mn', 'system', NULL, 'E-Mail Benachrichtigung', 'Mail Notification'),
(404, NULL, 'system', NULL, 'E-Mail Typ', 'E-mail type'),
(405, NULL, 'system', NULL, 'Text E-Mail', 'Text e-mail'),
(406, NULL, 'system', NULL, 'HTML E-Mail', 'HTML e-mail'),
(407, NULL, 'system', NULL, 'Verzeichnis angelegt', 'directory created'),
(408, NULL, 'system', NULL, 'Verzeichnis bearbeitet', 'edited directory'),
(409, NULL, 'system', NULL, 'Verzeichnis kopiert', 'directory copy'),
(410, NULL, 'system', NULL, 'Verzeichnis verschoben', 'moved directory'),
(411, NULL, 'system', NULL, 'Verzeichnis gel&ouml;scht', 'directory is deleted'),
(412, NULL, 'system', NULL, 'Datei hinzugef&uuml;gt', 'file added'),
(413, NULL, 'system', NULL, 'Datei bearbeitet', 'edited file'),
(414, NULL, 'system', NULL, 'Datei kopiert', 'file copied'),
(415, NULL, 'system', NULL, 'Datei verschoben', 'file is moved'),
(416, NULL, 'system', NULL, 'Version angelegt', 'version is created'),
(417, NULL, 'system', NULL, 'Datei eingecheckt', 'File is checked'),
(418, NULL, 'system', NULL, 'Datei ausgecheckt', 'File is checked out'),
(419, NULL, 'system', NULL, 'Datei gel&ouml;scht', 'file is deleted'),
(420, NULL, 'system', NULL, 'Link hinzugef&uuml;gt', 'link added'),
(421, NULL, 'system', NULL, 'Link bearbeitet', 'link edited'),
(422, NULL, 'system', NULL, 'Link gel&ouml;scht', 'link deleted'),
(423, NULL, 'system', NULL, '&Auml;nderungszeit', 'change time');