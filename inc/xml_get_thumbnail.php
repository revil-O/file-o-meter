<?php
	/**
	* get thumbnail infos
	* @package file-o-meter
	* @subpackage inc
	*/

	define('FOM_LOGIN_SITE', 'true');
	require_once('include.php');

	//fileid vorhanden
	if (isset($_GET['fileid_int']) and is_numeric($_GET['fileid_int']))
	{
		$tn = new Thumbnail();
		$result = $tn->search_thumbnail($_GET['fileid_int']);

		//kein thumbnail vorhanden
		if ($result === false)
		{
			$thumbnail = 'false';
			$thumbnail_width = 0;
			$thumbnail_height = 0;
		}
		else
		{
			$thumbnail = 'true';
			$thumbnail_width = $result['width'];
			$thumbnail_height = $result['height'];
		}
	}
	else
	{
		$thumbnail = 'false';
		$thumbnail_width = 0;
		$thumbnail_height = 0;
	}

	header('Content-type: application/xml');
	header('Expires: Mon, 31 Jul 1999 09:00:00 GMT');//Achtung muss in der Vergangenheit liegen
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	echo "<root>\n";
		echo '<data>'."\n";
		echo '<thumbnail>'.$thumbnail.'</thumbnail>'."\n";
		echo '<width>'.$thumbnail_width.'</width>'."\n";
		echo '<height>'.$thumbnail_height.'</height>'."\n";
		echo '</data>'."\n";
	echo '</root>';
?>