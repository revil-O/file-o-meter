<?php
	/**
	 * data export
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$ep = new Export;

	if (isset($_GET['step']) and $_GET['step'] == 2)
	{
		//Setuparray erstellen
		$export_setup = $ep->get_setup_array($_POST);

		//Speichert alle Fehlermeldungen
		$error_array = array();

		//Fehler in den Setupeinstellungen
		if ($export_setup['error'] === true)
		{
			$error_array[] = get_text(198,'return');//A configuration error has occurred!
		}
		elseif (isset($_POST['pid_int']) and isset($_POST['fid_int']))
		{
			$error_array = $ep->chk_export_data($_POST['fid_int'], $_POST['pid_int'], $export_setup);
		}
		else
		{
			$error_array[] = get_text('error','return');//An error has occurred!
		}
?>
		<form method="post" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" name="form_export" onsubmit="return chk_data();" accept-charset="UTF-8">
			<input type="hidden" name="job_string" value="export_data" />
			<input type="hidden" name="pid_int" value="<?php echo $_POST['pid_int']; ?>" />
			<input type="hidden" name="fid_int" value="<?php echo $_POST['fid_int']; ?>" />
			<?php
				foreach($export_setup as $i => $v)
				{
					if ($i != 'error')
					{
						if (is_array($v))
						{
							foreach($v as $av)
							{
								echo '<input type="hidden" name="setup['.$i.'][]" value="'.$av.'" />'."\n";
							}
						}
						else
						{
							echo '<input type="hidden" name="setup['.$i.']" value="'.$v.'" />'."\n";
						}
					}
				}
				$reload->create();
			?>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr valign="middle">
					<td class="main_table_header" width="100%"><?php get_text('access_de');//Data export ?> - <?php get_text('step');//Step?> 2</td>
				</tr>
				<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
				<tr>
					<td colspan="2" class="main_table_content">
						<a href="index.php<?php echo $gv->create_get_string('?step=1&amp;pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<tr>
								<td class="content_header_1" width="100%"><?php get_text(48);//Error messages ?></td>
							</tr>
							<?php
								if (count($error_array) > 0)
								{
									for($i = 0; $i < count($error_array); $i++)
									{
										echo '<tr><td>'.$error_array[$i].'</tr></td>';
									}
								}
								else
								{
							?>
									<tr><td><?php get_text(197);//No errors found. You may start the export now. ?></td></tr>
									<tr><td align="center"><br /><input type="submit" value="<?php get_text(201);//Start export?>" /></td></tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
			</table>
		</form>
<?php
	}
	//Erster Arbeitsschritt beim Export
	elseif (!isset($_GET['step']) or $_GET['step'] == 1)
	{
?>
		<script type="text/javascript">
			function set_version_time()
			{
				if (document.form_export.version_string[0].checked == true)
				{
					document.form_export.version_date_string.readOnly = true;
					document.form_export.version_date_string.value = "";
				}
				else
				{
					document.form_export.version_date_string.readOnly = false;
				}
			}

			function set_ex_typ()
			{
				if(document.form_export.ex_typ_string[0].checked == true)
				{
					document.form_export.without_extention_string.value = "";
					document.form_export.without_extention_string.readOnly = true;
					document.form_export.only_extention_string.readOnly = false;
				}
				else
				{
					document.form_export.only_extention_string.value = "";
					document.form_export.only_extention_string.readOnly = true;
					document.form_export.without_extention_string.readOnly = false;
				}
			}

			function opencalendar()
			{
				if(document.form_export.version_string[1].checked == true)
				{
					open_calendar('form_export','version_date_string',<?php if (isset($GLOBALS['user_language'])){echo $GLOBALS['user_language'];} ?>);
				}
			}

			function chk_data()
			{
				var only_ex = document.form_export.only_extention_string.value.toLowerCase();
				var without_ex = document.form_export.without_extention_string.value.toLowerCase();

				if(only_ex != "" && without_ex != "")
				{
					alert("<?php get_text(199, 'echo', 'decode_off');//There is only one exportoption allowed for the fileextension. ?>");
					document.form_export.without_extention_string.focus();
				}

				if(document.form_export.version_string[1].checked == true && document.form_export.version_date_string.value == "")
				{
					alert("<?php get_text(200, 'echo', 'decode_off');//Please specify a date! ?>");
					document.form_export.version_date_string.focus();
					return false;
				}

				return true;
			}
		</script>
		<form method="post" action="index.php<?php echo $gv->create_get_string('?step=2&amp;pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" name="form_export" onsubmit="return chk_data();" accept-charset="UTF-8">
			<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
			<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr valign="middle">
					<td class="main_table_header" width="100%"><?php get_text('access_de');//Data export ?> - <?php get_text('step');//Step?> 1</td>
				</tr>
				<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
				<tr>
					<td colspan="2" class="main_table_content">
						<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<tr>
								<td class="content_header_1" width="100%"><?php get_text(189);//Export options ?></td>
							</tr>
							<tr>
								<td><?php get_text(190);//The data export procedure is devided into two steps ...?><br /><br /></td>
							</tr>
							<?php
								if (!$ep->chk_export_folder($_GET['fid_int'], $_GET['pid_int']))
								{
							?>
									<tr>
										<td>
											<input type="radio" name="del_exists_folder_int" value="1" checked="checked" /> <strong><?php get_text(196);//Already existing folders will be deleted! ?></strong>
										</td>
									</tr>
							<?php
								}
							?>
							<tr>
								<td>
									<input type="radio" name="version_string" value="current" onclick="set_version_time();" checked="checked" /> <?php get_text(191);//Export only the newest version ?><br />
									<input type="radio" name="version_string" value="old" onclick="set_version_time();" /> <?php get_text(192);//Export only files which were uploaded before the following date. ?> <input type="text" name="version_date_string" id="version_date_string" class="ipt_100" readonly="readonly" /> <?php echo get_img('calendar.png', get_text('calendar','return'), get_text('calendar','return'), 'image', 0, '', 'onclick="opencalendar();"');//Calendar ?> TT.MM.YYYY
								</td>
							</tr>
							<tr>
								<td><input type="radio" name="ex_typ_string" value="only" onclick="set_ex_typ();" checked="checked" /> <?php get_text(193);//Export only files with the following file extensions ?> <input type="text" name="only_extention_string" class="ipt_100" /></td>
							</tr>
							<tr>
								<td><input type="radio" name="ex_typ_string" value="without" onclick="set_ex_typ();" /> <?php get_text(194);//Don't export files with the following file extensions ?> <input type="text" name="without_extention_string" class="ipt_100" readonly="readonly" /></td>
							</tr>
							<tr>
								<td align="center">
									<br /><input type="submit" value="<?php get_text(195);//Next: Examine data ?>" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
<?php
	}
?>