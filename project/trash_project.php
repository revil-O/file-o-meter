<?php
	/**
	 * displays the contents of trash objects
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage folder
	 */

	if ($ac->chk('project', 'w', $_GET['pid_int']))
	{
		$gt = new Tree();
?>
		<script type="text/javascript">
			function set_file_checkbox()
			{
				var checked_boole = false;

				if (document.getElementById("file_all_int").checked == true)
				{
					checked_boole = true;
				}

				for(var i = 0; i < document.getElementsByName("file_id_array[]").length; i++)
				{
					document.getElementsByName("file_id_array[]")[i].checked = checked_boole;
				}
			}
			function chk_trash_file()
			{
				var file_id_exists = false;

				for(var i = 0; i < document.getElementsByName("file_id_array[]").length; i++)
				{
					if (document.getElementsByName("file_id_array[]")[i].checked == true)
					{
						file_id_exists = true;
						break;
					}
				}

				if(!file_id_exists)
				{
					alert("<?php get_text(389, 'echo', 'decode_off'); //Waehlen Sie min. eine Datei aus! ?>");
					return false;
				}
				else
				{
					if (confirm("<?php get_text(390, 'echo', 'decode_off'); //Moechten Sie wirklich die ausgewaehlten Dateien loeschen! ?>"))
					{
						document.trash_file.submit();
					}
					else
					{
						return false;
					}
				}
			}

			function set_folder_checkbox()
			{
				var checked_boole = false;

				if (document.getElementById("folder_all_int").checked == true)
				{
					checked_boole = true;
				}

				for(var i = 0; i < document.getElementsByName("folder_id_array[]").length; i++)
				{
					document.getElementsByName("folder_id_array[]")[i].checked = checked_boole;
				}
			}
			function chk_trash_folder()
			{
				var folder_id_exists = false;

				for(var i = 0; i < document.getElementsByName("folder_id_array[]").length; i++)
				{
					if (document.getElementsByName("folder_id_array[]")[i].checked == true)
					{
						folder_id_exists = true;
						break;
					}
				}

				if(!folder_id_exists)
				{
					alert("<?php get_text(391, 'echo', 'decode_off'); //Waehlen Sie min. ein Verzeichnis aus! ?>");
					return false;
				}
				else
				{
					if (confirm("<?php get_text(392, 'echo', 'decode_off'); //Moechten Sie wirklich die ausgewaehlten Verzeichnisse inkl. Inhalt loeschen! ?>"))
					{
						document.trash_folder.submit();
					}
					else
					{
						return false;
					}
				}
			}

			function set_link_checkbox()
			{
				var checked_boole = false;

				if (document.getElementById("link_all_int").checked == true)
				{
					checked_boole = true;
				}

				for(var i = 0; i < document.getElementsByName("link_id_array[]").length; i++)
				{
					document.getElementsByName("link_id_array[]")[i].checked = checked_boole;
				}
			}
			function chk_trash_link()
			{
				var link_id_exists = false;

				for(var i = 0; i < document.getElementsByName("link_id_array[]").length; i++)
				{
					if (document.getElementsByName("link_id_array[]")[i].checked == true)
					{
						link_id_exists = true;
						break;
					}
				}

				if(!link_id_exists)
				{
					alert("<?php get_text(393, 'echo', 'decode_off'); //Waehlen Sie min. einen Link aus! ?>");
					return false;
				}
				else
				{
					if (confirm("<?php get_text(394, 'echo', 'decode_off'); //Möchten Sie wirklich die ausgewählten Links löschen! ?>"))
					{
						document.trash_link.submit();
					}
					else
					{
						return false;
					}
				}
			}
		</script>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(395);//Mülleimer ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?fileinc=project'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<h1><?php get_text(396);//Verzeichnisuebersicht ?></h1>
					<form action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fileinc=trash_projekt'); ?>" name="trash_folder" method="post">
						<input type="hidden" name="job_string" value="del_folder" />
						<?php $reload->create();?>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="30%" />
								<col width="30%" />
								<col width="40%" />
							</colgroup>
							<tr>
								<td class="content_header_2" style="padding: 2px;"><?php get_text(397);//Verzeichnisname ?></td>
								<td class="content_header_2" style="padding: 2px;"><?php get_text('description');//Description ?></td>
								<td class="content_header_2" style="padding: 2px;"><?php get_text(398);//Pfad ?></td>
							</tr>
							<?php
								$style = 1;
								$count = 0;
								$count_file_size = 0;
								$sql = $cdb->select("SELECT folder_id, folder_name, bemerkungen FROM fom_folder WHERE projekt_id=".$_GET['pid_int']." AND anzeigen='0' ORDER BY folder_name ASC");
								while ($result = $cdb->fetch_array($sql))
								{
									$count++;
							?>
									<tr class="content_tr_<?php echo $style; ?>">
										<td style="padding: 2px;">
											<input type="checkbox" name="folder_id_array[]" value="<?php echo $result['folder_id']; ?>" checked="checked" />
											&nbsp;<?php echo $result['folder_name']; ?>
										</td>
										<td style="padding: 2px;"><?php echo $result['bemerkungen']; ?></td>
										<td style="padding: 2px;"><?php echo $gt->get_folder_pfad_from_folder($result['folder_id']); ?></td>
									</tr>
							<?php
									if ($style == 1)
									{
										$style++;
									}
									else
									{
										$style = 1;
									}
								}
								if ($count == 0)
								{
							?>
									<tr class="content_tr_1">
										<td style="padding: 2px;" colspan="3"><?php get_text('no_data'); //No entries found! ?></td>
									</tr>
							<?php
								}
								else
								{
							?>
									<tr>
										<td class="content_header_2" style="padding: 2px;" colspan="3">
											<input type="checkbox" name="folder_all_int" id="folder_all_int" value="1" checked="checked" onchange="set_folder_checkbox();" />
											<a href="#" onclick="chk_trash_folder();">&nbsp;<?php echo get_img('delete.png', '', ''); ?>&nbsp;<?php get_text(399); //Alle Ausgewaehlten Verzeichnisse inkl. Inhalt entgueltig Loeschen ?></a>
										</td>
									</tr>
							<?php
								}
							?>
						</table>
					</form>
					<h1><?php get_text(166); //Dateiuebersicht ?></h1>
					<form action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fileinc=trash_projekt'); ?>" name="trash_file" method="post">
						<input type="hidden" name="job_string" value="del_file" />
						<?php $reload->create();?>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="30%" />
								<col width="30%" />
								<col width="30%" />
								<col width="10%" />
							</colgroup>
							<tr>
								<td class="content_header_2" style="padding: 2px;"><?php get_text('filename');//Filename ?></td>
								<td class="content_header_2" style="padding: 2px;"><?php get_text('description');//Description ?></td>
								<td class="content_header_2" style="padding: 2px;"><?php get_text(398); //Pfad ?></td>
								<td class="content_header_2" style="padding: 2px;" align="right"><?php get_text(164); //Dateigroesse ?>&nbsp;&nbsp;</td>
							</tr>
							<?php
								$style = 1;
								$count = 0;
								$count_file_size = 0;
								$sql = $cdb->select("SELECT t1.file_id, t1.org_name, t1.save_name, t1.mime_type, t1.file_size, t1.bemerkungen FROM fom_files t1
														LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
														WHERE t1.anzeigen='0' AND t2.projekt_id=".$_GET['pid_int']." ORDER BY t1.org_name ASC");
								while ($result = $cdb->fetch_array($sql))
								{
									$count++;
							?>
									<tr class="content_tr_<?php echo $style; ?>">
										<td style="padding: 2px;" onmouseover="get_thumbnail(<?php echo $result['file_id']; ?>);" onmouseout="hidden_thumbnail(<?php echo $result['file_id']; ?>);">
											<input type="checkbox" name="file_id_array[]" value="<?php echo $result['file_id']; ?>" checked="checked" />
											<?php echo $gt->GetFileType($result['save_name'], $result['mime_type']).'&nbsp;';?>
											&nbsp;<?php echo $result['org_name']; ?>
											<div id="get_thumbnail_<?php echo $result['file_id']; ?>" style="position:absolute; z-index: 1; padding: 2px; border: solid 1px; margin-left: 250px; display: none;">&nbsp;</div>
										</td>
										<td style="padding: 2px;"><?php echo $result['bemerkungen']; ?></td>
										<td style="padding: 2px;"><?php echo $gt->get_folder_pfad_from_file($result['file_id']); ?></td>
										<td align="right">
											<?php
												if ($result['file_size'] > 0)
												{
													$count_file_size += $result['file_size'];
													echo number_format($result['file_size'] / 1024 / 1024, 2, '.', '').' MB';
												}
												else
												{
													echo '0.00 MB';
												}
											?>&nbsp;&nbsp;
										</td>
									</tr>
							<?php
									if ($style == 1)
									{
										$style++;
									}
									else
									{
										$style = 1;
									}
								}
								if ($count == 0)
								{
							?>
									<tr class="content_tr_1">
										<td style="padding: 2px;" colspan="4"><?php get_text('no_data'); //No entries found! ?></td>
									</tr>
							<?php
								}
								else
								{
							?>
									<tr>
										<td class="content_header_2" style="padding: 2px;" colspan="3">
											<input type="checkbox" name="file_all_int" id="file_all_int" value="1" checked="checked" onchange="set_file_checkbox();" />
											<a href="#" onclick="chk_trash_file();">&nbsp;<?php echo get_img('delete.png', '', ''); ?>&nbsp;<?php get_text(400); //Alle Ausgewaehlten Dateien entgueltig Loeschen ?></a>
										</td>
										<td class="content_header_2" style="padding: 2px;" align="right"><?php echo number_format($count_file_size / 1024 / 1024, 2, '.', '').' MB'; ?>&nbsp;&nbsp;</td>
									</tr>
							<?php
								}
							?>
						</table>
					</form>
					<h1><?php get_text(401); //Linkuebersicht ?></h1>
					<form action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fileinc=trash_projekt'); ?>" name="trash_link" method="post">
						<input type="hidden" name="job_string" value="del_link" />
						<?php $reload->create();?>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="30%" />
								<col width="30%" />
								<col width="40%" />
							</colgroup>
							<tr>
								<td class="content_header_2" style="padding: 2px;"><?php get_text('filename');//Filename ?></td>
								<td class="content_header_2" style="padding: 2px;"><?php get_text('description');//Description ?></td>
								<td class="content_header_2" style="padding: 2px;"><?php get_text(398); //Pfad ?></td>
							</tr>
							<?php
								$style = 1;
								$count = 0;
								$sql = $cdb->select("SELECT t1.link_id, t1.file_id, t1.name, t1.link, t1.bemerkungen, t1.link_type FROM fom_link t1
													LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
													WHERE t1.anzeigen='0' AND t2.projekt_id=".$_GET['pid_int']." ORDER BY t1.link ASC");
								while ($result = $cdb->fetch_array($sql))
								{
									//interner link
									if ($result['link_type'] == 'INTERNAL')
									{
										$sub_sql = $cdb->select('SELECT * FROM fom_files WHERE file_id='.$result['file_id']);
										$sub_result = $cdb->fetch_array($sub_sql);
									}
									$count++;
							?>
									<tr class="content_tr_<?php echo $style; ?>">
										<?php
											$name_string = '';

											if ($result['link_type'] == 'INTERNAL')
											{
												$name_string = $sub_result['org_name'];
											}
											else
											{
												if (!empty($result['name']))
												{
													$name_string = $result['name'];
												}
												else
												{
													$name_string = $result['link'];
												}
											}
										?>
										<td style="padding: 2px;"<?php if ($result['link_type'] == 'INTERNAL') { echo ' onmouseover="get_thumbnail('.$result['file_id'].');" onmouseout="hidden_thumbnail('.$result['file_id'].');"';}?>>
											<input type="checkbox" name="link_id_array[]" value="<?php echo $result['link_id']; ?>" checked="checked" />
											<?php
												if ($result['link_type'] == 'INTERNAL')
												{
													echo $gt->GetFileType($sub_result['save_name'], $sub_result['mime_type']).'&nbsp;';
												}

												echo $gt->GetFileType('', 'LINK').'&nbsp;';

												echo $name_string.'&nbsp;';

												if ($result['link_type'] == 'INTERNAL')
												{
													echo '<div id="get_thumbnail_'.$result['file_id'].'" style="position:absolute; z-index: 1; padding: 2px; border: solid 1px; margin-left: 250px; display: none;">&nbsp;</div>';
												}
											?>
										</td>
										<td style="padding: 2px;"><?php echo $result['bemerkungen']; ?></td>
										<td style="padding: 2px;"><?php echo $gt->get_folder_pfad_from_link($result['link_id']); ?></td>
									</tr>
							<?php
									if ($style == 1)
									{
										$style++;
									}
									else
									{
										$style = 1;
									}
								}
								if ($count == 0)
								{
							?>
									<tr class="content_tr_1">
										<td style="padding: 2px;" colspan="3"><?php get_text('no_data'); //No entries found! ?></td>
									</tr>
							<?php
								}
								else
								{
							?>
									<tr>
										<td class="content_header_2" style="padding: 2px;" colspan="3">
											<input type="checkbox" name="link_all_int" id="link_all_int" value="1" checked="checked" onchange="set_link_checkbox();" />
											<a href="#" onclick="chk_trash_link();">&nbsp;<?php echo get_img('delete.png', '', ''); ?>&nbsp;<?php get_text(402); //Alle Ausgewaehlten Links entgueltig Loeschen ?></a>
										</td>
									</tr>
							<?php
								}
							?>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>