<?php
	/**
	 * loginpage
	 * index-file of the root-folder with login-form
	 * @package file-o-meter
	 */

	define('FOM_LOGIN_SITE', 'true');

	require_once('inc/include.php');

	//Aufgerufene URL mit der in der Config vergleichen und gegebenenfalls anpassen
	if (isset($_SERVER['SCRIPT_URI']) and !empty($_SERVER['SCRIPT_URI']))
	{
		if(FOM_ABS_URL != substr($_SERVER['SCRIPT_URI'], 0, strlen(FOM_ABS_URL)))
		{
			header("Location: ".FOM_ABS_URL);
			exit();
		}
	}
	elseif(isset($_SERVER['HTTP_HOST']) and isset($_SERVER['REQUEST_URI']))
	{
		$http = 'http://';

		if (isset($_SERVER['HTTPS']) and !empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off')
		{
			$http = 'https://';
		}

		if(FOM_ABS_URL != substr($http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 0, strlen(FOM_ABS_URL)))
		{
			header("Location: ".FOM_ABS_URL);
			exit();
		}
	}

	if (isset($_POST['job']) and $_POST['job'] == 'login')
	{
		if (isset($_POST['username_string']) and !empty($_POST['username_string']) and isset($_POST['pw_string']) and !empty($_POST['pw_string']))
		{
			//Array fuer Fehler- und erfolgsmeldungen
			$meldungen = array();

			$result = $login->chk_login($_POST['pw_string'], $_POST['username_string']);

			if ($result['login'])
			{
				$get_string = '?action=login&'.$gv->get_index_name().'='.$result['global_var'];

				header("Location: folder/index.php$get_string");
				exit();
			}
			else
			{
				if (isset($result['error']) and is_array($result['error']))
				{
					foreach($result['error'] as $v)
					{
						if ($v == 1){$meldungen[] = get_text(8,'return'); /*Please enter the password.*/}
						elseif ($v == 2){$meldungen[] = get_text(9,'return'); /*Please enter the username.*/}
						elseif ($v == 3 or $v == 7){$meldungen[] = get_text(10,'return'); /*Es ist ein Fehler aufgetreten. Bitte versuchen Sie es zu einem spaeteren Zeitpunkt noch einmal.*/}
						elseif ($v == 4){$meldungen[] = get_text(11,'return'); /*Zu den angegebenen Logindaten ist kein Benutzeraccount vorhanden.*/}
						elseif ($v == 5){$meldungen[] = get_text(12,'return'); /*Ihr Account wurde deaktiviert. Bitte wenden Sie sich an den Systemadministrator.*/}
						elseif ($v == 6){$meldungen[] = get_text(13,'return','decode_on',array('timeout'=>date("H:i",$result['timeout']) )); /*Ihr Account wurde bis [var]timeout[/var] deaktiviert.*/}
					}
				}
			}
		}
	}
	//$this->import_error['error'][] = get_text(124,'return','decode_on','',array('error_line'=>$error_line)); /* Der Import wurde in Zeile [var]error_line[/var] abgebrochen! */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/screen.css?fom_v='.FOM_VERSION; ?>" />
<title><?php echo $setup_array['site_titel']; ?></title>
</head>
<body>
<table cellpadding="2" cellspacing="0" border="0" width="70%">
	<colgroup>
		<col width="40%" />
		<col width="60%" />
	</colgroup>
	<tr>
		<td class="border" style="padding:10px;"><img src="<?php echo 'template/'.$setup_array['template'].'/dms_logo.png'; ?>" alt="" width="180" height="68" border="0" /></td>
		<td>
			<?php
				if (isset($meldungen) and count($meldungen) > 0)
				{
					foreach($meldungen as $v)
					{
			?>
						<div class="error" style="padding:10px;">
							<?php echo $v; ?>
						</div>
						<br />
			<?php
					}
				}
				else
				{
					echo '&nbsp;';
				}
			?>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td class="border" style="padding:10px;">
			<form method="post" action="index.php" accept-charset="UTF-8">
				<input type="hidden" name="job" value="login" />
				<table cellpadding="2" cellspacing="2" border="0" width="30%" align="center">
					<tr>
						<td><strong><?php get_text(1);//Benutzername ?>:</strong></td>
						<td>
							<input type="text" name="username_string" class="ipt_150" />
						</td>
					</tr>
					<tr>
						<td><strong><?php get_text(2);//Passwort ?>:</strong></td>
						<td>
							<input type="password" name="pw_string" class="ipt_150" />
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right">
							<input type="submit" value="<?php get_text(3);//Login ?>" /><br />
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
	<?php
		$sql = $db->select("SELECT t1.contact FROM fom_setup t1");
		$result = $db->fetch_array($sql);

		$kontakt_array = unserialize($result['contact']);
	?>
	<tr>
		<td class="border" style="padding:10px;">
			<strong><?php get_text(4);//Ansprechpartner ?>:</strong><br />
			<?php
				if (isset($kontakt_array['first_name']) and !empty($kontakt_array['first_name']) and isset($kontakt_array['last_name']) and !empty($kontakt_array['last_name']))
				{
					echo $kontakt_array['first_name'].' '.$kontakt_array['last_name'];
				}
			?>
			<br /><br />
			<strong><?php get_text('tel');//Telefon ?>:</strong> <?php if (isset($kontakt_array['email']) and !empty($kontakt_array['email'])){echo $kontakt_array['email'];} ?><br />
			<strong><?php get_text('handy');//Handy ?>:</strong> <?php if (isset($kontakt_array['phone']) and !empty($kontakt_array['phone'])){echo $kontakt_array['phone'];} ?><br />
			<strong><?php get_text('email');//E-Mail ?>:</strong> <?php if (isset($kontakt_array['handy']) and !empty($kontakt_array['handy'])){echo $kontakt_array['handy'];} ?>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>