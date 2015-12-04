<?php
	/**
	 * displays the contents of a folder
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$rxls = new ReadFileXls();

	if ($ac->chk('project', 'r', $_GET['pid_int']))
	{
		//FileInfo Klasse
		$fi = new FileInfo();
		//SubFile Klasse
		$sf = new SubFile();
		//JS COOKIE Jobs
		$ffcm = new FileFolderCopyMove;
		$js_cookie_job = $ffcm->create_js_cookie_array();
?>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(166);//File overview ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
						<tr valign="top">
							<td width="70%"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
							<td width="10%">
								<?php
									//Kopier bzw. Einfuegen Job vorhanden
									if ($js_cookie_job['result'] == true)
									{
										echo $ffcm->get_paste_option($js_cookie_job);
									}
									else
									{
										echo '&nbsp;';
									}
								?>
							</td>
							<td width="20%" align="right">
								<?php echo folder_action_menue($_GET['pid_int'], $f_id, array('paste_job' => $js_cookie_job['result'])); ?>
							</td>
						</tr>
					</table>
					<br />
					<?php
						if ($f_id >0)
						{
							$cal = new Calendar;

							//Verzeichniskommentare ausgeben
							$sql = $db->select('SELECT bemerkungen FROM fom_folder WHERE folder_id='.$f_id);
							$f_result = $db->fetch_array($sql);
							if (!empty($f_result['bemerkungen']))
							{
					?>
								<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
									<tr>
										<td width="100%">
											<strong><?php get_text('description');//Description ?>:</strong><br />
											<?php echo $f_result['bemerkungen'];?>
										</td>
									</tr>
								</table>
								<br />
					<?php
							}
					?>
							<table cellpadding="0" cellspacing="0" border="0" width="100%" class="content_table">
								<colgroup>
									<col width="5%" />
									<col width="5%" />
									<col width="30%" />
									<col width="35%" />
									<col width="10%" />
									<col width="15%" />
								</colgroup>
								<tr>
									<td class="content_header_1" style="padding: 2px;" colspan="2" align="center"><?php get_text('actions');//Actions ?></td>
									<td class="content_header_2" style="padding: 2px;"><?php get_text('filename');//Filename ?></td>
									<td class="content_header_2" style="padding: 2px;"><?php get_text('description');//Description ?></td>
									<td class="content_header_2" style="padding: 2px;"><?php get_text('filesize');//Filesize ?></td>
									<td class="content_header_2" style="padding: 2px;"><?php get_text(165);//Uploaded on ?></td>
								</tr>
					<?php
								$count = 0;
								$style = 1;

								$sql = $cdb->select("(SELECT file_id, 0 AS link_id, org_name AS name, 0 AS link, save_name, mime_type, file_size, save_time, bemerkungen, 0 AS link_type FROM fom_files WHERE folder_id=$f_id AND anzeigen='1' AND file_type='PRIMARY')
													UNION
													(SELECT file_id, link_id, name, link, 0 AS save_name, 0 AS mime_type, 0 AS file_size, save_time, bemerkungen, link_type FROM fom_link WHERE folder_id=$f_id AND anzeigen='1')
													ORDER BY name ASC");
								while($result = $cdb->fetch_array($sql))
								{
									//$result = $data_array[$i];

									//Dateien ausgeben
									if ($result['link_id'] == 0)
									{
										//Leserechte Pruefen
										if ($ac->chk('file', 'r', $result['file_id']))
										{
											$sub_file_exists = $sf->sub_file_exists($result['file_id']);
						?>
											<tr class="content_tr_<?php echo $style; ?>">
												<td align="right" style="padding: 2px;">
													<?php
														//Subfile existiert
														if ($sub_file_exists === true)
														{
															echo get_img('page_save.png', get_text(170,'return'), get_text(170,'return'), 'image', '0', '', 'onclick="get_subfile('.$result['file_id'].')"');//Show subfiles
														}
													?>
												</td>
												<td align="left" style="padding: 2px;">
													<?php echo file_action_menue($result['file_id'], $_GET['pid_int'], $f_id); ?>
												</td>
												<td title="<?php echo $result['name'];?>" style="padding: 2px;" onmouseover="get_thumbnail(<?php echo $result['file_id']; ?>);" onmouseout="hidden_thumbnail(<?php echo $result['file_id']; ?>);">
													<?php
														echo $gt->GetFileType($result['save_name'], $result['mime_type']).'&nbsp;';

														if (strlen($result['name']) <= 35)
														{
															$tmp_file_name = $result['name'];
														}
														else
														{
															$tmp_file_name = substr($result['name'],0,33).'...';
														}

														if ($fi->get_file_exists($result['file_id']))
														{
															echo $tmp_file_name;
														}
														else
														{
															echo '<strike>'.$tmp_file_name.'</strike>';
														}
													?>
													<div id="get_thumbnail_<?php echo $result['file_id']; ?>" style="position:absolute; z-index: 1; padding: 2px; border: solid 1px; margin-left: 250px; display: none;">&nbsp;</div>
												</td>
												<td title="<?php echo $result['bemerkungen']; ?>" style="padding: 2px;">
						<?php
													if (strlen(html_entity_decode($result['bemerkungen'], ENT_QUOTES, 'UTF-8')) <= 40)
													{
														echo $result['bemerkungen'];
													}
													else
													{
														echo substr(html_entity_decode($result['bemerkungen'], ENT_QUOTES, 'UTF-8') ,0 ,37).'...';
													}
						?>
												</td>
												<td style="padding: 2px;"><?php echo $fi->get_html_filesize($result['file_size']); ?></td>
												<td style="padding: 2px;"><?php echo $cal->GetWinTime($result['save_time'],'date'); ?></td>
											</tr>
						<?php
											//Subfile existiert
											if ($sub_file_exists === true)
											{
												$sub_file_count = 0;
												$sub_sql = $cdb->select('SELECT t2.* FROM fom_sub_files t1
																		LEFT JOIN fom_files t2 ON t1.subfile_id=t2.file_id
																		WHERE t1.file_id='.$result['file_id']." AND t2.anzeigen='1'");
												while ($sub_result = $cdb->fetch_array($sub_sql))
												{
													//Leserechte Pruefen
													if ($ac->chk('file', 'r', $sub_result['file_id']))
													{
														if ($sub_file_count == 0)
														{
						?>
															<tr id="<?php echo 'subfile_jsid_'.$result['file_id']; ?>" class="content_tr_<?php echo $style; ?>" style="display: none;">
																<td colspan="6">
																	<table cellpadding="0" cellspacing="0" border="0" width="100%">
																		<colgroup>
																			<col width="5%" />
																			<col width="5%" />
																			<col width="30%" />
																			<col width="35%" />
																			<col width="10%" />
																			<col width="15%" />
																		</colgroup>
						<?php
														}
						?>
																		<tr>
																			<td style="padding: 2px;">&nbsp;</td>
																			<td align="right" style="padding: 2px;">
																				<?php echo file_action_menue($sub_result['file_id'], $_GET['pid_int'], $f_id, array('file_type' => 'SUB')); ?>
																			</td>
																			<td title="<?php echo $sub_result['org_name'];?>" style="padding: 2px;">&nbsp;&nbsp;
						<?php
																				echo $gt->GetFileType($sub_result['save_name'], $sub_result['mime_type']).'&nbsp;';

																				if (strlen($sub_result['org_name']) <= 35)
																				{
																					echo $sub_result['org_name'];
																				}
																				else
																				{
																					echo substr($sub_result['org_name'],0,33).'...';
																				}
						?>
																			</td>
																			<td title="<?php echo $sub_result['bemerkungen']; ?>" style="padding: 2px;">
						<?php
																				if (strlen(html_entity_decode($sub_result['bemerkungen'], ENT_QUOTES, 'UTF-8')) <= 40)
																				{
																					echo $sub_result['bemerkungen'];
																				}
																				else
																				{
																					echo substr(html_entity_decode($sub_result['bemerkungen'], ENT_QUOTES, 'UTF-8') , 0, 37).'...';
																				}
						?>
																			</td>
																			<td style="padding: 2px;"><?php echo $fi->get_html_filesize($sub_result['file_size']); ?></td>
																			<td style="padding: 2px;"><?php echo $cal->GetWinTime($sub_result['save_time'],'date'); ?></td>
																		</tr>
						<?php
														$sub_file_count++;
													}
												}
												if ($sub_file_count > 0)
												{
													echo '</table></td></tr>';
												}
											}

											$style = one_or_two($style);
											$count++;
										}
									}
									//Links ausgeben
									elseif (isset($result['link_id']))
									{
										$link_acces = false;

										//interner link
										if ($result['link_type'] == 'INTERNAL')
										{
											$link_acces = $ac->chk('link', 'r', $result['link_id']);

											$sub_sql = $cdb->select('SELECT * FROM fom_files WHERE file_id='.$result['file_id']);
											$sub_result = $cdb->fetch_array($sub_sql);
										}
										//externer link
										else
										{
											$link_acces = $ac->chk('link', 'r', $result['link_id']);
											$result['file_id'] = 0;
										}

										//Leserechte Pruefen
										if ($link_acces)
										{
							?>
											<tr class="content_tr_<?php echo $style; ?>">
												<td align="right" style="padding: 2px;">
													&nbsp;
												</td>
												<td align="left" style="padding: 2px;">
													<?php
														echo link_action_menue($result['link_id'], $_GET['pid_int'], $f_id, $result['link'], $result['file_id']);
													?>
												</td>
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
												<td title="<?php echo $name_string; ?>" style="padding: 2px;"<?php if ($result['link_type'] == 'INTERNAL') { echo ' onmouseover="get_thumbnail('.$result['file_id'].');" onmouseout="hidden_thumbnail('.$result['file_id'].');"';}?>>
													<?php
														if ($result['link_type'] == 'INTERNAL')
														{
															echo $gt->GetFileType($sub_result['save_name'], $sub_result['mime_type']).'&nbsp;';
														}

														echo $gt->GetFileType('', 'LINK').'&nbsp;';

														if (strlen($name_string) <= 35)
														{
															echo $name_string;
														}
														else
														{
															echo substr($name_string, 0, 33).'...';
														}

														if ($result['link_type'] == 'INTERNAL')
														{
															echo '<div id="get_thumbnail_'.$result['file_id'].'" style="position:absolute; z-index: 1; padding: 2px; border: solid 1px; margin-left: 250px; display: none;">&nbsp;</div>';
														}
													?>
												</td>
												<?php
													$bemerkungen_string = '';
													if ($result['link_type'] == 'INTERNAL')
													{
														$bemerkungen_string = $sub_result['bemerkungen'];
													}
													else
													{
														$bemerkungen_string = $result['bemerkungen'];
													}
												?>
												<td title="<?php echo $bemerkungen_string; ?>" style="padding: 2px;">
						<?php
													if (strlen(html_entity_decode($bemerkungen_string, ENT_QUOTES, 'UTF-8')) <= 40)
													{
														echo $bemerkungen_string;
													}
													else
													{
														echo substr(html_entity_decode($bemerkungen_string, ENT_QUOTES, 'UTF-8') ,0 ,37).'...';
													}
							?>
													</td>
													<td style="padding: 2px;">
							<?php
														if ($result['file_id'] > 0)
														{
															echo $fi->get_html_filesize($sub_result['file_size']);
														}
							?>
													</td>
													<td style="padding: 2px;"><?php echo $cal->GetWinTime($result['save_time'],'date'); ?></td>
												</tr>
							<?php
											$style = one_or_two($style);
											$count++;
										}
									}
								}

								if ($count == 0)
								{
									echo '<tr><td colspan="6">'.get_text('no_data','return').'</td></tr>';//No entries found!
								}
					?>
							</table>
<?php
						}
?>
				</td>
			</tr>
		</table>
<?php
	}
?>