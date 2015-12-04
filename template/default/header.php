<?php
	/**
	 * HTML Header for all pages
	 * @package file-o-meter
	 * @subpackage template
	 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="<?php echo '../template/'.$setup_array['template'].'/screen.css?fom_v='.FOM_VERSION; ?>" />
<?php
	if ((isset($_GET['fileinc']) and $_GET['fileinc'] == 'multiupload') or (isset($GLOBALS['FOM_VAR']['fileinc']) and $GLOBALS['FOM_VAR']['fileinc'] == 'multiupload'))
	{
		echo '<link rel="stylesheet" type="text/css" href="../template/'.$setup_array['template'].'/plupload.css?fom_v='.FOM_VERSION.'" />';
	}
?>
<title><?php echo $setup_array['site_titel']; ?></title>
<script type="text/javascript">
	window.defaultStatus = "<?php echo $setup_array['site_titel']; ?>";
	JS_ABS_URL = "<?php echo FOM_ABS_URL; ?>";
	JS_GV_INDEX = "<?php echo $gv->get_index_name(); ?>";
	JS_GV_KEY = "<?php echo $gv->get_key(); ?>";
</script>
<script type="text/javascript" src="<?php echo FOM_ABS_URL.'inc/js_general.js?fom_v='.FOM_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo FOM_ABS_URL.'inc/calendar.js?fom_v='.FOM_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo FOM_ABS_URL.'inc/js_upload_file_size.js?fom_v='.FOM_VERSION; ?>"></script>
</head>
<body>
	<table width="100%"	border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td width="1%"><img src="<?php echo '../template/'.$setup_array['template'].'/dms_logo.png'; ?>" alt="" width="180" height="68" border="0" /></td>
		<td rowspan="2" width="69%">&nbsp;</td>
		<td rowspan="2" width="30%" align="right">
			<?php
				$sql = $db->select("SELECT vorname, nachname FROM fom_user WHERE user_id=".USER_ID);
				$result = $db->fetch_array($sql);
			?>
			<div class="font_10"><?php get_text(43);//Sie sind eingeloggt als ?>: <?php echo $result['vorname'].' '.$result['nachname']; ?></div>
			<div class="font_10"><?php get_text(44);//Logout in ?>: <span class="font_10" id="countdown">00:00</span></div>
			<div class="default_link">
				<a href="../folder/index.php<?php echo $gv->create_get_string('?fileinc=edit_useraccount'); ?>"><?php get_text(116);//Edit useraccount ?> &raquo;</a><br />
				<a href="index.php<?php echo $gv->create_get_string('?action=logout'); ?>"><?php get_text('logout');//Logout ?> &raquo;</a>
			</div>
		</td>
	</tr>
	<tr>
		<td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" alt="" width="240" height="10" border="0" /></td>
	</tr>
	<tr valign="top">
		<td>
			<?php
				if ($ac->chk('_USER_V', 'r') or $ac->chk('_USER_G', 'r') or $ac->chk('_PROJECT_V', 'r') or $ac->chk('_SETUP_V', 'r') or $ac->chk('_LOGBOOK_V', 'r'))
				{
			?>
					<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
						<tr class="content_tr_1">
							<td width="100%">
								<?php
									if ($ac->chk('_USER_V', 'r'))
									{
										echo '<a href="../user/index.php'.$gv->create_get_string('?fileinc=user').'">'.get_text(55, 'return').'</a><br />'; //Benutzerverwaltung
									}
									if ($ac->chk('_USER_G', 'r'))
									{
										echo '<a href="../user_group/index.php'.$gv->create_get_string('?fileinc=usergroup').'">'.get_text(56, 'return').'</a><br />'; //Benutzergruppenverwaltung
									}
									if ($ac->chk('_PROJECT_V', 'r'))
									{
										echo '<a href="../project/index.php'.$gv->create_get_string('?fileinc=project').'">'.get_text(57, 'return').'</a><br />'; //Projektverwaltung
									}
									if ($ac->chk('_SETUP_V', 'w'))
									{
										echo '<a href="../setup/index.php'.$gv->create_get_string('?fileinc=setup').'">'.get_text(58, 'return').'</a><br />'; //Grundeinstellungen
									}
									if ($ac->chk('_LOGBOOK_V', 'r'))
									{
										echo '<a href="../logbook/index.php'.$gv->create_get_string('?fileinc=main').'">'.get_text(360, 'return').'</a><br />'; //Logbuch
									}
								?>
							</td>
						</tr>
					</table>
					<br />
			<?php
				}
			?>
			<div style="overflow:scroll; height:100%; width:240px; min-height:500px; border:solid 1px; border-color:#015212; padding:2px;" class="content_tr_1">
				<?php
					if (isset($_GET['fid_int']))
					{
						$f_id = $_GET['fid_int'];
					}
					else
					{
						$f_id = 0;
					}

					$sql = $db->select("SELECT projekt_id FROM fom_projekte WHERE anzeigen='1' ORDER BY projekt_name ASC");
					while($result = $db->fetch_array($sql))
					{
						if ($ac->chk('project', 'r', $result['projekt_id']))
						{
							$gt->ShowFolder($result['projekt_id'], $f_id);
						}
					}
				?>
			</div>
		</td>
		<td colspan="2" class="content">
			<?php
				if (isset($meldung))
				{
					//Erfolgsmeldungen
					if (isset($meldung['ok']) and count($meldung['ok']) > 0)
					{
			?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr valign="middle">
								<td class="good_table_header" width="100%"><?php get_text(47);//Erfolgsmeldungen ?></td>
							</tr>
							<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
							<tr>
								<td class="good_table_content">
									<ul>
									<?php
										foreach($meldung['ok'] as $v)
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
					//Fehlermeldungen
					elseif (isset($meldung['error']) and count($meldung['error']) > 0)
					{
			?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr valign="middle">
								<td class="error_table_header" width="100%"><?php get_text(48);//Fehlermeldungen ?></td>
							</tr>
							<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
							<tr>
								<td class="error_table_content">
									<ul>
									<?php
										foreach($meldung['error'] as $v)
										{
											echo '<li class="error">'.$v.'</li>';
										}
									?>
									</ul>
								</td>
							</tr>
						</table><br />
			<?php
					}
				}
			?>