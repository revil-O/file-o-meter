<?php
	/**
	 * data import
	 * @package file-o-meter
	 * @subpackage folder
	 */

	if (isset($_GET['step']) and $_GET['step'] == 3)
	{
		//Importklasse
		$im = new Import;

		//Keine Verzeichnis vorhanden
		if (!isset($_POST['folder']) or !is_array($_POST['folder']))
		{
			$_POST['folder'] = array();
		}
		//Keine Dateien vorhanden
		if (!isset($_POST['files']) or !is_array($_POST['files']))
		{
			$_POST['files'] = array();
		}

		//Verzeichnisse und Dateien auslesen
		$result = $im->read_import_folder($_GET['fid_int'], $_POST['folder'], $_POST['files']);

		//Automatische Namensanpassung
		if (isset($_POST['rename']) and $_POST['rename'] == 1)
		{
			$hidden = '<input type="hidden" name="rename" value="1" />';
			$rename = true;
		}
		else
		{
			$hidden = '';
			$rename = false;
		}

		//Pruefen ob Versionsoptionen eingeblendet werden muessen
		$v_result = $im->show_import_option($result, $rename, $_GET['fid_int'], $_GET['pid_int']);
?>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text('access_di');//Data import ?> - <?php get_text('step');//Step?> 3</td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<form method="post" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="import_data" />
						<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
						<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
						<?php
							foreach($_POST['folder'] as $h)
							{
								$hidden .= '<input type="hidden" name="folder[]" value="'.$h.'" />';
							}

							foreach($_POST['files'] as $h)
							{
								$hidden .= '<input type="hidden" name="files[]" value="'.$h.'" />';
							}
							echo $hidden;
							$reload->create();
						?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="100%">
							</colgroup>

							<tr>
								<td class="content_header_1"><?php get_text(203);//Version options ?></td>
							</tr>
							<?php
								if ($v_result === true)
								{
							?>
									<tr>
										<td>
											<input type="radio" name="add_version_int" value="1" checked="checked" />
											<?php get_text(204);//Replace current files by newer versions. ?>
										</td>
									</tr>
							<?php
								}
							?>
							<tr>
								<td>
									<input type="checkbox" name="del_file_folder_int" value="1" />
									<?php get_text(205);//Delete existing files and subfolders of the targetfolder, if they are not part of the importdata? ?>
								</td>
							</tr>
							<tr>
								<td align="center">
									<br />
									<?php get_text(206);//Attention: The importprocedure can take several minutes. ?><br /><br />
									<input type="submit" value="<?php get_text(207);//Start import ?>" />
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
	//Daten ueberpruefen
	elseif (isset($_GET['step']) and $_GET['step'] == 2)
	{
		$error_msg = array();
		//Keine Verzeichnisse bzw. Dateien ausgewaehlt
		if ((!isset($_POST['folder']) or count($_POST['folder']) == 0) and (!isset($_POST['files']) or count($_POST['files']) == 0))
		{
			$error_msg['ERROR'][] = get_text(208,'return');//You have no sourcedata selected for the import!
		}
		//Keine Fehler
		if (!isset($error_msg['ERROR']) or count($error_msg['ERROR']) == 0)
		{
			//Importklasse
			$im = new Import;
			//Keine Verzeichnis vorhanden
			if (!isset($_POST['folder']) or !is_array($_POST['folder']))
			{
				$_POST['folder'] = array();
			}
			//Keine Dateien vorhanden
			if (!isset($_POST['files']) or !is_array($_POST['files']))
			{
				$_POST['files'] = array();
			}
			//Verzeichnisse und Dateien auslesen
			$result = $im->read_import_folder($_GET['fid_int'], $_POST['folder'], $_POST['files']);
			//Importdaten Pruefen
			$error_msg = $im->chk_import_data($result);
		}
?>
		<script type="text/javascript">
			<!--
				function set_rename()
				{
					if(document.data_import.rename.checked == true)
					{
						var chk = confirm("<?php get_text(209, 'echo', 'decode_off');//Attention: The automatic adaptation of file and folder names can lead to unexpected results! ?>\n<?php get_text(210, 'echo', 'decode_off');//For instance filenames will automatically be reduced to a maximum length of 30 characters.?>\n<?php get_text(211, 'echo', 'decode_off');//If there are multiple files within the same folder...?>");

						if(chk == true)
						{
							document.data_import.rename.checked = true;
						}
						else
						{
								document.data_import.rename.checked = false;
						}
					}

					if (document.data_import.rename.checked == true)
					{
						document.data_import.data_import_submit.disabled = false;
					}
					else
					{
						document.data_import.data_import_submit.disabled = true;
					}
				}
			// -->
		</script>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text('access_di');//Data import ?> - <?php get_text('step');//Step?> 2</td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc=import_data'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<?php
						if (isset($error_msg['ERROR']) or isset($error_msg['WARNING']))
						{
					?>
							<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
								<tr>
									<td class="content_header_1" width="90%"><?php get_text('error_message');//Error message?></td>
									<td class="content_header_2" width="10%"><?php get_text('type');//Type?></td>
								</tr>
								<?php
									$style = one_or_two(2);
									if (isset($error_msg['ERROR']))
									{
										for($i = 0; $i < count($error_msg['ERROR']); $i++)
										{
											echo '<tr class="content_tr_'.$style.'"><td>'.$error_msg['ERROR'][$i].'</td><td class="error">'.get_text(214, 'return').'</td></tr>'."\n";//Error
											$style = one_or_two($style);
										}
									}
									if (isset($error_msg['WARNING']))
									{
										for($i = 0; $i < count($error_msg['WARNING']); $i++)
										{
											echo '<tr class="content_tr_'.$style.'"><td>'.$error_msg['WARNING'][$i].'</td><td class="warnung">'.get_text(215, 'return').'</td></tr>'."\n";//Warning
											$style = one_or_two($style);
										}
									}
								?>
							</table>
					<?php
						}
						else
						{
					?>
							<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
								<tr>
									<td class="content_header_1" width="100%"><?php get_text('notice');//Notice ?></td>
								</tr>
								<tr>
									<td class="meldung"><?php get_text(217);//No errors found. ?></td>
								</tr>
							</table>
					<?php
						}

						//Hiddenstring erstellen
						$hidden = '';
						foreach($_POST['folder'] as $h)
						{
							$hidden .= '<input type="hidden" name="folder[]" value="'.$h.'" />';
						}

						foreach($_POST['files'] as $h)
						{
							$hidden .= '<input type="hidden" name="files[]" value="'.$h.'" />';
						}
					?>
					<br />
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr valign="top">
							<td width="50%" align="left">
								<form method="post" action="index.php<?php echo $gv->create_get_string('?fileinc=import_data&amp;pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;step=2'); ?>" accept-charset="UTF-8">
									<?php echo $hidden; ?>
									<input type="submit" value="<?php get_text(222);//Examine data again ?>" title="<?php get_text(218);//This procedure can take some minutes depending on the amaount of data! ?>" />
								</form>
							</td>
							<td width="50%" align="right">
								<?php
									if (!isset($error_msg['ERROR']))
									{
								?>
										<form name="data_import" method="post" action="index.php<?php echo $gv->create_get_string('?fileinc=import_data&amp;pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;step=3'); ?>" accept-charset="UTF-8">
											<?php echo $hidden; ?>
											<input type="submit" value="<?php get_text(207);//Start import ?>" name="data_import_submit" title="<?php get_text(218);//This procedure can take some minutes depending on the amaount of data! ?>" <?php if (isset($error_msg['WARNING'])){echo ' disabled="disabled"';} ?> />
											<?php
												if (isset($error_msg['WARNING']))
												{
											?>
													<br /><input type="checkbox" name="rename" value="1" onchange="set_rename();" /> <?php get_text(219);//Adapt file- and foldernames automatically ?>
											<?php
												}
											?>
										</form>
								<?php
									}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
<?php

	}
	//Zu Importierende Daten aussuchen
	elseif (!isset($_GET['step']) or $_GET['step'] == 1)
	{
?>
		<script type="text/javascript">
		<!--
			function checked_all()
			{
				var typ = false;

				if(document.data_import.folder_all.checked == true)
				{
					typ = true;
				}

				for(var i = 0; i < document.forms["data_import"].elements.length; i++)
				{
					if(document.forms["data_import"].elements[i].type == "checkbox")
					{
						document.forms["data_import"].elements[i].checked = typ;
					}
				}
			}
			function chk()
			{
				var count = 0;
				for(var i = 0; i < document.forms["data_import"].elements.length; i++)
				{
					if(document.forms["data_import"].elements[i].type == "checkbox")
					{
						if(document.forms["data_import"].elements[i].checked == true)
						{
							count++;
						}
					}
				}

				if(count > 0)
				{
					return true;
				}
				else
				{
					alert("<?php get_text(223, 'echo', 'decode_off');//Please select at least one folder or one file for the import! ?>");
					return false;
				}
			}
		//-->
		</script>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text('access_di');//Data import ?> - <?php get_text('step');//Step?> 1</td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br />
					<form name="data_import" method="post" action="index.php<?php echo $gv->create_get_string('?fileinc=import_data&amp;pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;step=2'); ?>" onsubmit="return chk();" accept-charset="UTF-8">
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="15%" />
								<col width="50%" />
								<col width="35%" />
							</colgroup>
							<tr>
								<td class="content_header_1">&nbsp;</td>
								<td class="content_header_2"><?php get_text(84);//Name?></td>
								<td class="content_header_2"><?php get_text('type');//Type?></td>
							</tr>
								<?php
									$folder = FOM_ABS_PFAD.'files/imex/'.USER_ID.'/';
									$import_data = array();

									if (file_exists($folder) and is_dir($folder))
									{
										if ($h = opendir($folder))
										{
											$count = 0;
											while (($f = readdir($h)) !== false)
											{
												if ($f != '.' and $f != '..')
												{
													if (is_dir($folder.$f))
													{
														$import_data['folder'][] = $f;
													}
													elseif (is_file($folder.$f))
													{
														$import_data['file'][] = $f;
													}
												}
											}
										}
									}
									$count = 0;
									$style = 1;
									if (isset($import_data['folder']) and count($import_data['folder']) > 0)
									{
										for ($i = 0; $i < count($import_data['folder']); $i++)
										{
											echo '<tr class="content_tr_'.$style.'"><td align="center"><input type="checkbox" name="folder[]" value="'.$import_data['folder'][$i].'" /></td>';

											if (strlen($import_data['folder'][$i]) > 35)
											{
												echo '<td title="'.$import_data['folder'][$i].'">'.substr($import_data['folder'][$i], 0, 32).'...</td><td>'.get_text('folder', 'return').'</td></tr>';//Folder
											}
											else
											{
												echo '<td>'.$import_data['folder'][$i].'</td><td>'.get_text('folder', 'return').'</td></tr>';//Folder
											}
											$style = one_or_two($style);
											$count++;
										}
									}
									//Dateien nicht auf die Projektebene Kopieren
									if (isset($_GET['fid_int']) and $_GET['fid_int'] > 0 and isset($import_data['file']) and count($import_data['file']) > 0)
									{
										for ($i = 0; $i < count($import_data['file']); $i++)
										{
											echo '<tr class="content_tr_'.$style.'"><td align="center"><input type="checkbox" name="files[]" value="'.$import_data['file'][$i].'" /></td>';

											if (strlen($import_data['file'][$i]) > 35)
											{
												echo '<td title="'.$import_data['file'][$i].'">'.substr($import_data['file'][$i], 0, 30).'...'.substr($import_data['file'][$i], -4).'</td><td>'.get_text('file', 'return').'</td></tr>';//File
											}
											else
											{
												echo '<td>'.$import_data['file'][$i].'</td><td>'.get_text('file', 'return').'</td></tr>';//File
											}
											$style = one_or_two($style);
											$count++;
										}
									}

									if ($count == 0)
									{
										echo '<tr><td colspan="3">'.get_text(220, 'return').'</td></tr>';//No data available
									}
									else
									{
										echo '<tr class="content_tr_'.$style.'"><td align="center"><input type="checkbox" name="folder_all" value="1" onchange="checked_all();" /></td><td colspan="2"><strong>'.get_text(221, 'return').'</strong></td></tr>';//Select all
									}
								?>
						</table>
						<br />
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="50%">&nbsp;</td>
								<td width="50%" align="right">
									<input type="submit" value="<?php get_text(195);//Next: Examine data ?>" title="<?php get_text(218);//This procedure can take some minutes depending on the amaount of data! ?>" />
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>