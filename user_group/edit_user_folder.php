<?php
	/**
	 * edit user folder/file Access
	 * @package file-o-meter
	 * @subpackage user_group
	 */

	if (isset($_GET['ugid_int']))
	{
		$acces_array = array();
		$sort_array = array();

		$user_sql = $cdb->select('	SELECT DISTINCT(t1.user_id), t2.vorname, t2.nachname FROM fom_user_membership t1
									LEFT JOIN fom_user t2 ON t1.user_id=t2.user_id
									WHERE t1.usergroup_id='.$_GET['ugid_int']);
		while ($user_result = $cdb->fetch_array($user_sql))
		{
			$sql = $cdb->select('SELECT * FROM fom_access WHERE user_id='.$user_result['user_id']);
			while ($result = $cdb->fetch_array($sql))
			{
				$tmp_access = @unserialize($result['access']);
				if (is_array($tmp_access))
				{
					$tmp_access = $ac->verify_access($tmp_access);
				}

				if ($result['type'] == 'FOLDER')
				{
					$sub_sql = $cdb->select('SELECT projekt_id FROM fom_folder WHERE folder_id='.$result['id']);
					$sub_result = $cdb->fetch_array($sub_sql);

					$tmp_pfad = $gt->get_folder_pfad_from_folder($result['id']);
					$sort_array[] = $tmp_pfad;
					$acces_array[] = array(	'type'			=> 'FOLDER',
											'folder_id'		=> $result['id'],
											'project_id'	=> $sub_result['projekt_id'],
											'user_id'		=> $user_result['user_id'],
											'pfad'			=> $tmp_pfad,
											'user'			=> $user_result['nachname'].' '.$user_result['vorname'],
											'access'		=> $tmp_access);
				}
				elseif ($result['type'] == 'FILE')
				{
					$sub_sql = $cdb->select('SELECT t1.folder_id, t2.projekt_id FROM fom_files t1
											LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
											WHERE t1.file_id='.$result['id']);
					$sub_result = $cdb->fetch_array($sub_sql);

					$tmp_pfad = $gt->get_folder_pfad_from_file($result['id']);
					$sort_array[] = $tmp_pfad;
					$acces_array[] = array(	'type'			=> 'FILE',
											'file_id'		=> $result['id'],
											'folder_id'		=> $sub_result['folder_id'],
											'project_id'	=> $sub_result['projekt_id'],
											'user_id'		=> $user_result['user_id'],
											'pfad'			=> $tmp_pfad,
											'user'			=> $user_result['nachname'].' '.$user_result['vorname'],
											'access'		=> $tmp_access);
				}
				elseif ($result['type'] == 'LINK')
				{
					$sub_sql = $cdb->select('SELECT t1.folder_id, t2.projekt_id FROM fom_link t1
											LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
											WHERE t1.link_id='.$result['id']);
					$sub_result = $cdb->fetch_array($sub_sql);

					$tmp_pfad = $gt->get_folder_pfad_from_link($result['id']);
					$sort_array[] = $tmp_pfad;
					$acces_array[] = array(	'type'			=> 'LINK',
											'link_id'		=> $result['id'],
											'folder_id'		=> $sub_result['folder_id'],
											'project_id'	=> $sub_result['projekt_id'],
											'user_id'		=> $user_result['user_id'],
											'pfad'			=> $tmp_pfad,
											'user'			=> $user_result['nachname'].' '.$user_result['vorname'],
											'access'		=> $tmp_access);
				}
			}
		}

		array_multisort($sort_array, $acces_array);

		$access = array();
?>
		<script type="text/javascript">
			function confirm_del()
			{
				if (confirm("<?php get_text(315, 'echo', 'decode_off');//Moechten Sie die Zugriffsrechte wirklich loeschen? ?>") == false)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		</script>

		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(316);//Zugriffsrechte fuer Benutzer ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?fileinc=usergroup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
						<tr>
							<td class="content_header_1" width="40%"><?php get_text('folder');//Folder ?> / <?php get_text('file');//File ?></td>
							<td class="content_header_2" width="20%"><?php get_text(317);//User ?></td>
							<td class="content_header_2" width="30%"><?php get_text(182);//Access authorizations ?></td>
							<td class="content_header_2" width="10%" align="center"><?php get_text('actions');//Actions ?></td>
						</tr>
						<?php
							$style = 1;
							$access_list = $ac->get_access_list();
							$count = 0;
							foreach ($acces_array as $access_data)
							{
						?>
								<tr class="content_tr_<?php echo $style; ?>" valign="top">
									<td><a href="../folder/index.php<?php echo $gv->create_get_string('?fileinc=&amp;pid_int='.$access_data['project_id'].'&amp;fid_int='.$access_data['folder_id']); ?>"><?php echo $access_data['pfad']; ?></a></td>
									<td><?php echo $access_data['user']; ?></td>
									<td colspan="2">
										<table cellpadding="0" cellspacing="0" border="0" width="100%">
											<tr valign="top">
												<td width="80%">
													<form method="post" action="index.php<?php echo $gv->create_get_string('?fileinc=edit_user_folder&amp;ugid_int='.$_GET['ugid_int']); ?>" accept-charset="UTF-8">
														<div style="float: left; width: 90%;">
															<input type="hidden" name="job_string" value="edit_user_folder" />
															<input type="hidden" name="type_string" value="<?php echo $access_data['type']; ?>" />
															<?php
																$reload->create();

																if ($access_data['type'] == 'FOLDER')
																{
																	echo '<input type="hidden" name="id_int" value="'.$access_data['folder_id'].'" />';
																}
																elseif ($access_data['type'] == 'FILE')
																{
																	echo '<input type="hidden" name="id_int" value="'.$access_data['file_id'].'" />';
																}
																elseif ($access_data['type'] == 'LINK')
																{
																	echo '<input type="hidden" name="id_int" value="'.$access_data['link_id'].'" />';
																}
															?>
															<input type="hidden" name="user_id_int" value="<?php echo $access_data['user_id']; ?>" />
															<?php
																foreach ($access_data['access'] as $access_index => $access_value)
																{
																	if ($access_value == true)
																	{
																		$checked = ' checked="checked"';
																	}
																	else
																	{
																		$checked = '';
																	}

																	echo '<input type="checkbox" name="access_array['.$access_index.']" value="1"'.$checked.' /> '.$access_list[$access_index].'<br />';
																}
															?>
														</div>
														<div style="float: left; text-align: right;">
															<input type="image" src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/famfamfam/page_white_key.png'; ?>" style="border: 0px;" title="<?php get_text('save');//Save ?>" />
														</div>
													</form>
												</td>
												<td width="10%">
													<form method="post" action="index.php<?php echo $gv->create_get_string('?fileinc=edit_user_folder&amp;ugid_int='.$_GET['ugid_int']); ?>" onsubmit="return confirm_del();" accept-charset="UTF-8">
														<input type="hidden" name="job_string" value="del_user_folder" />
														<input type="hidden" name="type_string" value="<?php echo $access_data['type']; ?>" />
														<?php
															$reload->create();

															if ($access_data['type'] == 'FOLDER')
															{
																echo '<input type="hidden" name="id_int" value="'.$access_data['folder_id'].'" />';
															}
															elseif ($access_data['type'] == 'FILE')
															{
																echo '<input type="hidden" name="id_int" value="'.$access_data['file_id'].'" />';
															}
															elseif ($access_data['type'] == 'LINK')
															{
																echo '<input type="hidden" name="id_int" value="'.$access_data['link_id'].'" />';
															}
														?>
														<input type="hidden" name="user_id_int" value="<?php echo $access_data['user_id']; ?>" />
														<input type="image" src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/famfamfam/page_white_delete.png'; ?>" style="border: 0px;" title="<?php get_text(318);//Zugriffsrechte loeschen ?>" />
													</form>
												</td>
											</tr>
										</table>
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
								$count++;
							}
							if ($count == 0)
							{
								echo '<tr><td colspan="4" class="content_tr_1">'.get_text(87, 'return').'</td></tr>';//Kein Eintrag vorhanden
							}
						?>
					</table>
				</td>
			</tr>
		</table>
<?php
	}
?>