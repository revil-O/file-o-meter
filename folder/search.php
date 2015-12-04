<?php
	/**
	 * search form
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$gt = new Tree;
	$se = new Search;
	$cal = new Calendar;
	//FileInfo Klasse
	$fi = new FileInfo();

	if (!isset($_POST['filter_file_name_string']))
	{
		$_POST['filter_file_name_string'] = '';
	}
	if (!isset($_POST['filter_file_data_string']))
	{
		$_POST['filter_file_data_string'] = '';
	}
	if (!isset($_POST['filter_doctyp_string']))
	{
		$_POST['filter_doctyp_string'] = '';
	}
	if (!isset($_POST['filter_subfolder_int']))
	{
		if (isset($_GET['fid_int']) and $_GET['fid_int'] > 0)
		{
			if (isset($_POST['searchform']) and $_POST['searchform'] == 1)
			{
				$_POST['filter_subfolder_int'] = 0;
			}
			else
			{
				$_POST['filter_subfolder_int'] = 1;
			}
		}
		//Projektsuche da wird natuerlich in allen Verzeichnissen gesucht
		else
		{
			$_POST['filter_subfolder_int'] = 1;
		}
	}
	if (!isset($_POST['filter_mimetyp_string']))
	{
		$_POST['filter_mimetyp_string'] = '';
	}
	if (!isset($_POST['filter_file_date_string']))
	{
		$_POST['filter_file_date_string'] = '';
	}
	if (!isset($_POST['filter_file_date_type_string']))
	{
		$_POST['filter_file_date_type_string'] = 'after';
	}

	//Headerueberschrift
	if (isset($_GET['fid_int']) and $_GET['fid_int'] > 0)
	{
		$sql = $cdb->select('SELECT folder_name FROM fom_folder WHERE folder_id='.$_GET['fid_int']);
		$result = $cdb->fetch_array($sql);
		$header_line = str_replace('[path]', $result['folder_name'], get_text(333, 'return'));//Suche im Verzeichnis ""
	}
	elseif(isset($_GET['pid_int']) and $_GET['pid_int'] > 0)
	{
		$sql = $cdb->select('SELECT projekt_name FROM fom_projekte WHERE projekt_id='.$_GET['pid_int']);
		$result = $cdb->fetch_array($sql);
		$header_line = str_replace('[path]', $result['projekt_name'], get_text(334, 'return'));//Suche im Projekt ""
	}
	else
	{
		$header_line = get_text(187, 'return');//Suchen
	}
?>
<script type="text/javascript">
	function chk_file_data()
	{
		var value = document.form_search.filter_mimetyp_string.value;
		//FIXME: Das muss noch besser geloest werden
		if(value == "application/pdf" || value == "application/vnd.oasis.opendocument.text" || value == "application/msword" || value == "application/vnd.oasis.opendocument.spreadsheet")
		{
			document.form_search.filter_file_data_string.readOnly = false;
		}
		else
		{
			document.form_search.filter_file_data_string.readOnly = true;
			document.form_search.filter_file_data_string.value = "";
		}
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php echo $header_line; ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<form action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" method="post" name="form_search" accept-charset="UTF-8">
				<input type="hidden" name="searchform" value="1" />
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="border">
					<colgroup>
						<col width="20%" />
						<col width="30%" />
						<col width="20%" />
						<col width="30%" />
					</colgroup>
					<tr class="table_tr_bg_2">
						<td class="content_header_1" colspan="4"><?php get_text(183);//Search options ?></td>
					</tr>
					<tr>
						<td class="filter_td_2"><?php get_text('filename');//Filename ?>:</td>
						<td class="filter_td_1">
							<input type="text" name="filter_file_name_string" value="<?php echo $_POST['filter_file_name_string']; ?>" class="ipt_150" />
						</td>
						<td class="filter_td_2"><?php get_text(184);//Contained text ?>:</td>
						<td class="filter_td_2">
							<input type="text" name="filter_file_data_string" value="<?php echo $_POST['filter_file_data_string']; ?>" class="ipt_150" />
						</td>
					</tr>
					<tr>
						<td class="filter_td_2"><?php get_text(85);//Document type ?>:</td>
						<td class="filter_td_1">
							<select name="filter_mimetyp_string" class="ipt_150" onchange="chk_file_data();">
								<option value="">- <?php get_text('all');//All ?> -</option>
								<?php
									$mime_sub_folder = false;
									$mime_folder_id = 0;
									$mime_project_id = 0;
									if ($_POST['filter_subfolder_int'] == 1)
									{
										$mime_sub_folder = true;
									}
									if (isset($_GET['fid_int']) and $_GET['fid_int'] > 0)
									{
										$mime_folder_id = $_GET['fid_int'];
									}
									if(isset($_GET['pid_int']) and $_GET['pid_int'] > 0)
									{
										$mime_project_id = $_GET['pid_int'];
									}
									$mime_result = $se->get_mime_types($mime_folder_id, $mime_project_id, $mime_sub_folder);

									if (is_array($mime_result) and !empty($mime_result))
									{
										foreach ($mime_result as $mime_data)
										{
											if ($_POST['filter_mimetyp_string'] == $mime_data['mime'])
											{
												$selected = ' selected="selected"';
											}
											else
											{
												$selected = '';
											}

											echo '<option value="'.$mime_data['mime'].'"'.$selected.'>(*.'.$mime_data['extension'].') '.$mime_data['name'].'</option>';
										}
									}
								?>
							</select>
						</td>
						<td class="filter_td_2"><?php get_text(186);//Incl. subfolders ?>:</td>
						<td class="filter_td_2">
							<input type="checkbox" name="filter_subfolder_int" value="1"<?php if ($_POST['filter_subfolder_int'] == 1){echo ' checked="checked"';} ?> />
						</td>
					</tr>
					<tr>
						<td class="filter_td_2"><?php get_text(335);//Erstellungsdatum ?>:</td>
						<td class="filter_td_1" colspan="3">
							<input type="text" name="filter_file_date_string" id="filter_file_date_string" value="<?php echo $_POST['filter_file_date_string']; ?>" class="ipt_150" />
							<?php echo get_img('calendar.png', get_text('calendar','return'), get_text('calendar','return'), 'image', 0, '', 'onclick="open_calendar(\'form_search\', \'filter_file_date_string\', \''.$GLOBALS['user_language'].'\');"');//Calendar ?>
							<input type="radio" name="filter_file_date_type_string" value="before"<?php if ($_POST['filter_file_date_type_string'] == 'before'){echo ' checked="checked"';} ?> /> <?php get_text(336);//vor ?> <input type="radio" name="filter_file_date_type_string" value="after"<?php if ($_POST['filter_file_date_type_string'] == 'after'){echo ' checked="checked"';} ?> /> <?php get_text(337);//vor ?>
						</td>
					</tr>
					<tr>
						<td class="filter_td_2" colspan="3">&nbsp;</td>
						<td class="filter_td_2" align="right">
							<input type="submit" value="<?php get_text('search');//Search ?>" />&nbsp;
						</td>
					</tr>
				</table>
			</form>
			<?php
				//Suchanfrage zusammen stellen

				//Es sollte schon was zum suchen da sein
				if (!empty($_POST['filter_file_name_string']) or !empty($_POST['filter_file_data_string']) or !empty($_POST['filter_file_date_string']))
				{
					$serach_array = array();
					$serach_array['fid_int'] = $_GET['fid_int'];
					$serach_array['pid_int'] = $_GET['pid_int'];
					$serach_array['file_name'] = '';
					$serach_array['file_data'] = '';
					$serach_array['mime_typ'] = '';
					$serach_array['subfolder'] = '';

					//Dateiname
					if (!empty($_POST['filter_file_name_string']))
					{
						$serach_array['file_name'] = $_POST['filter_file_name_string'];
					}
					//Dateiinhalt
					if (!empty($_POST['filter_file_data_string']))
					{
						$serach_array['file_data'] = $_POST['filter_file_data_string'];
					}
					//Dateityp
					if (!empty($_POST['filter_mimetyp_string']))
					{
						$serach_array['mime_typ'] = $_POST['filter_mimetyp_string'];
					}
					//Unterverzeichnisse
					if (!empty($_POST['filter_subfolder_int']))
					{
						$serach_array['subfolder'] = $_POST['filter_subfolder_int'];
					}
					//Datumssuche
					if (!empty($_POST['filter_file_date_string']))
					{
						$file_date = $cal->check_iso_date($cal->format_date($_POST['filter_file_date_string'], 'ISO'));
						if (!empty($file_date) and $file_date != '0000-00-00')
						{
							$serach_array['file_date'] = $file_date;
							if ($_POST['filter_file_date_type_string'] == 'before')
							{
								$serach_array['file_date_type'] = 'before';
							}
							else
							{
								$serach_array['file_date_type'] = 'after';
							}
						}
					}

					//Suche Starten
					$search_result = $se->search($serach_array);
			?>
					<br />
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="content_table">
						<colgroup>
							<col width="5%" />
							<col width="5%" />
							<col width="50%" />
							<col width="15%" />
							<col width="10%" />
							<col width="15%" />
						</colgroup>
						<tr>
							<td class="content_header_1" align="center" colspan="2" style="padding: 2px;"><?php get_text('actions');//Actions ?></td>
							<td class="content_header_2" style="padding: 2px;"><?php get_text('filename');//Filename ?></td>
							<td class="content_header_2" style="padding: 2px;"><?php get_text(188);//Relevance ?></td>
							<td class="content_header_2" style="padding: 2px;"><?php get_text('filesize');//Filesize ?></td>
							<td class="content_header_2" style="padding: 2px;"><?php get_text(165);//Uploaded on ?></td>
						</tr>
						<?php
							$count = 0;
							$style = 1;

							foreach($search_result as $data_array)
							{
								$link_id = 0;
								$file_id = 0;
								if ($data_array['type'] == 'file')
								{
									$file_id = $data_array['id'];
									if (is_numeric($file_id) and $file_id > 0)
									{
										$sql = $db->select('SELECT * FROM fom_files WHERE file_id='.$file_id);
										$access = $ac->chk('file', 'r', $file_id);
									}
									else
									{
										$access = false;
									}
								}
								elseif ($data_array['type'] == 'link')
								{
									$link_id = $data_array['id'];
									if (is_numeric($link_id) and $link_id > 0)
									{
										$sql = $db->select('SELECT t1.*, t2.org_name, t2.save_name, t2.mime_type FROM fom_link t1
															LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
															WHERE t1.link_id='.$link_id);
										$access = $ac->chk('link', 'r', $link_id);
									}
									else
									{
										$access = false;
									}
								}

								//Leserechte Pruefen
								if ($access)
								{
									$result = $db->fetch_array($sql);
						?>
									<tr class="content_tr_<?php echo $style; ?>">
										<td align="right" style="padding: 2px;">
											<?php
												//Subfile Existiert
												if (isset($data_array['sub_file']))
												{
													echo get_img('page_save.png', get_text(170,'return'), get_text(170,'return'), 'image', '0', '', 'onclick="get_subfile('.$result['file_id'].')"');//Show subfiles
												}
												else
												{
													echo '&nbsp;';
												}
											?>
										</td>
										<td align="left" style="padding: 2px;">
											<?php
												if ($file_id > 0)
												{
													echo file_action_menue($file_id, $_GET['pid_int'], $result['folder_id'], array('filter_subfolder_int' => $_POST['filter_subfolder_int']));
												}
												elseif ($link_id > 0)
												{
													//interner link
													if ($result['file_id'] > 0)
													{
														echo link_action_menue($link_id, $_GET['pid_int'], $result['folder_id'], '', $result['file_id']);
													}
													else
													{
														echo link_action_menue($link_id, $_GET['pid_int'], $result['folder_id'], $result['link']);
													}
												}
											?>
										</td>
										<?php
											if ($file_id > 0)
											{
												$long_name = $result['org_name'];
												$short_name = $gt->GetFileType($result['save_name'], $result['mime_type']).'&nbsp;';

												if (strlen($long_name) <= 35)
												{
													$short_name .= $long_name;
												}
												else
												{
													$short_name .= substr($long_name, 0, 33).'...';
												}
											}
											elseif ($link_id > 0)
											{
												//interner Link
												if ($result['file_id'] > 0)
												{
													$long_name = $result['org_name'];
													$short_name = $gt->GetFileType($result['save_name'], $result['mime_type']).'&nbsp;';
													$short_name .= $gt->GetFileType('', 'LINK').'&nbsp;';
												}
												//externer link
												else
												{
													if (!empty($result['name']))
													{
														$long_name = $result['name'];
													}
													else
													{
														$long_name = $result['link'];
													}
													$short_name = $gt->GetFileType('', 'LINK').'&nbsp;';
												}

												if (strlen($long_name) <= 35)
												{
													$short_name .= $long_name;
												}
												else
												{
													$short_name .= substr($long_name, 0, 33).'...';
												}
											}
										?>
										<td title="<?php echo $long_name ;?>" style="padding: 2px;">
											<?php echo $short_name; ?>
										</td>
										<td style="padding: 2px;">
											<div class="relevance_chart_box"><?php echo $se->get_relevance_chart($data_array['relevanz']); ?></div>
										</td>
										<td title="<?php echo $result['bemerkungen']; ?>" style="padding: 2px;">
											<?php
												if (!isset($result['file_size']))
												{
													$result['file_size'] = 0;
												}
												echo $fi->get_html_filesize($result['file_size']);
											?>
										</td>
										<td style="padding: 2px;"><?php echo $cal->GetWinTime($result['save_time'],'date'); ?></td>
									</tr>
						<?php
									if (isset($data_array['sub_file']))
									{
										$sub_file_count = 0;
										foreach ($data_array['sub_file'] as $sub_file_id => $sub_file_relevanz)
										{
											//Leserechte Pruefen
											if (is_numeric($sub_file_id) and $sub_file_id > 0 and $ac->chk('file', 'r', $sub_file_id))
											{
												$sub_sql = $db->select('SELECT * FROM fom_files WHERE file_id='.$sub_file_id);
												$sub_result = $db->fetch_array($sub_sql);

												if ($sub_file_count == 0)
												{
						?>
													<tr id="<?php echo 'subfile_jsid_'.$file_id; ?>" class="content_tr_<?php echo $style; ?>">
														<td colspan="6">
															<table cellpadding="0" cellspacing="0" border="0" width="100%">
																<colgroup>
																	<col width="5%" />
																	<col width="5%" />
																	<col width="50%" />
																	<col width="15%" />
																	<col width="10%" />
																	<col width="15%" />
																</colgroup>
						<?php
												}
						?>
												<tr class="content_tr_<?php echo $style; ?>">
													<td style="padding: 2px;">&nbsp;</td>
													<td align="right" style="padding: 2px;"><?php echo file_action_menue($sub_file_id, $_GET['pid_int'], $sub_result['folder_id'], array('filter_subfolder_int' => $_POST['filter_subfolder_int'], 'file_type' => 'SUB')); ?></td>
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
													<td style="padding: 2px;">
														<div class="relevance_chart_box"><?php echo $se->get_relevance_chart($sub_file_relevanz); ?></div>
													</td>
													<td title="<?php echo $sub_result['bemerkungen']; ?>" style="padding: 2px;"><?php echo $fi->get_html_filesize($sub_result['file_size']); ?></td>
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
							if($count == 0)
							{
								echo '<tr><td colspan="5">'.get_text('no_data','return').'</td></tr>';//No entries found!
							}
						?>
					</table>
					<?php
						if($count > 0)
						{
					?>
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td align="center" width="100%">
										<form method="post" action="../report/pdf_search.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" target="_blank">
											<?php
												foreach ($_POST as $index => $value)
												{
													echo '<input type="hidden" name="'.$index.'" value="'.$value.'" />';
												}
											?>
											<br /><input type="submit" value="<?php get_text(338);//PDF Erstellen ?>" />
										</form>
									</td>
								</tr>
							</table>
			<?php
						}
					}
			?>
		</td>
	</tr>
</table>
