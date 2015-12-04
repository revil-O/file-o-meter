<?php
	/**
	 * this include-file read the thumbnails
	 * @package file-o-meter
	 * @subpackage inc
	 */

	define('FOM_LOGIN_SITE', 'true');
	require_once('include.php');

	//Fileid vorhanden
	if (isset($_GET['fileid_int']) and is_numeric($_GET['fileid_int']))
	{
		//Pruefen ob ein Thumbnail vorhanden ist
		$tn = new Thumbnail();
		$result = $tn->search_thumbnail($_GET['fileid_int']);

		//error spacer ausgeben
		if ($result === false)
		{
			header ("Content-type: image/gif");
			$im = @imagecreatefromgif(FOM_ABS_PFAD.'template/default/pic/_spacer.gif');
			@imagegif($im);
		}
		//Thumbnail gefunden
		else
		{
			if ($result['ex'] == 'jpg' or $result['ex'] == 'jpe' or $result['ex'] == 'jpeg')
			{
				header ("Content-type: image/jpeg");
				$im = @imagecreatefromjpeg($result['pfad'].$result['name']);
				@imagejpeg($im);
			}
			elseif ($result['ex'] == 'png')
			{
				header ("Content-type: image/png");
				$im = @imagecreatefrompng($result['pfad'].$result['name']);
				@imagepng($im);
			}
			elseif ($result['ex'] == 'gif')
			{
				header ("Content-type: image/gif");
				$im = @imagecreatefromgif($result['pfad'].$result['name']);
				@imagegif($im);
			}
			//error spacer ausgeben
			else
			{
				header ("Content-type: image/gif");
				$im = @imagecreatefromgif(FOM_ABS_PFAD.'template/default/pic/_spacer.gif');
				@imagegif($im);
			}
		}
	}
	//error spacer ausgeben
	else
	{
		header ("Content-type: image/gif");
		$im = @imagecreatefromgif(FOM_ABS_PFAD.'template/default/pic/_spacer.gif');
		@imagegif($im);
	}
?>