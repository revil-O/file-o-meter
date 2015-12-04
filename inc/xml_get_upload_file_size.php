<?php
	/**
	* provides the filesize of the file which is currently uploading.
	* Gibt die Aktuelle groesse der Datei zurueck die gerade Hochgeladen wird.
	* !!ACHTUNG die Variable $GLOBALS['FOM_VAR']['TmpUploadFileName'] sollte nach jedem Uploadvorgang geloescht werden!!
	* @package file-o-meter
	* @subpackage inc
	*/

	define('FOM_LOGIN_SITE', 'true');
	require_once('include.php');

	if(isset($GLOBALS['FOM_VAR']) and isset($GLOBALS['FOM_VAR']['TmpUploadFileName']) and file_exists($GLOBALS['FOM_VAR']['TmpUploadFileName']))
	{
		$filesize = filesize($GLOBALS['FOM_VAR']['TmpUploadFileName']);
	}
	else
	{
		if(is_dir($setup_array['tmp_upload_folder']))
		{
			$file_list = glob($setup_array['tmp_upload_folder'].'[p][h][p]*');
			if(count($file_list) == 1)
			{
				$GLOBALS['FOM_VAR']['TmpUploadFileName'] = $file_list[0];
				$filesize = filesize($file_list[0]);
			}
		}
	}


	header('Content-type: application/xml');
	header('Expires: Mon, 31 Jul 1999 09:00:00 GMT');//Achtung muss in der Vergangenheit liegen
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	echo '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
	echo "<root>\n";

	if(isset($filesize) and $filesize > 0)
	{
		echo '<data><value>'.$filesize.'</value></data>'."\n";
	}
	else
	{
		echo '<data><value>0</value></data>'."\n";
	}
	echo '</root>';
?>