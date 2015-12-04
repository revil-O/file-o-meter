<?php
	/**
	 * edit access control (access authorization management for files and folders)
	 * @package file-o-meter
	 * @subpackage folder
	 */

	if (isset($_GET['fid_int']))
	{
		$user_group_access_array = array();
		$fileid_int = 0;
		$linkid_int = 0;

		$access_type = '';
		//Datei
		if (isset($_GET['fileid_int']) and !empty($_GET['fileid_int']))
		{
			$user_group_access_array = $ac->get_access('file', $_GET['fileid_int']);
			$fileid_int = $_GET['fileid_int'];
			$access_type = 'file';
		}
		//Link
		elseif (isset($_GET['linkid_int']) and !empty($_GET['linkid_int']))
		{
			$user_group_access_array = $ac->get_access('link', $_GET['linkid_int']);
			$linkid_int = $_GET['linkid_int'];
			$access_type = 'link';
		}
		//Verzeichnis
		elseif (isset($_GET['fid_int']) and !empty($_GET['fid_int']))
		{
			$user_group_access_array = $ac->get_access('folder', $_GET['fid_int']);
			$access_type = 'folder';
		}
		//Projekt
		elseif (isset($_GET['pid_int']) and !empty($_GET['pid_int']))
		{
			$user_group_access_array = $ac->get_access('project', $_GET['pid_int']);
			$access_type = 'project';
		}
		else
		{
			$user_group_access_array = array();
		}

		//Usergruppenid suchen
		if (isset($_GET['ugid_int']) and !empty($_GET['ugid_int']))
		{
			$selected_ugid_int = $_GET['ugid_int'];
		}
		else
		{
			$selected_ugid_int = 0;
		}
?>
		<script type="text/javascript">
				function user_group_refresh_site()
				{
					var ug_id;

					for (var i = 0; i < document.form_as.usergroup_int.length; i++)
					{
						if (document.form_as.usergroup_int.options[i].selected == true)
						{
							ug_id = document.form_as.usergroup_int.options[i].value;
							break;
						}
					}
					location.replace("<?php echo 'index.php?fileinc=edit_as&pid_int='.$_GET['pid_int'].'&fid_int='.$_GET['fid_int'].'&fileid_int='.$fileid_int.'&linkid_int='.$linkid_int.'&ugid_int=';?>" + ug_id + "&" + JS_GV_INDEX + "=" + JS_GV_KEY);
				}
		</script>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text('access_as');//Edit access control ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<form name="form_as" method="post" action="index.php<?php echo $gv->create_get_string('?fileinc=&amp;pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;ugid_int='.$selected_ugid_int.'&amp;fileid_int='.$fileid_int); ?>" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="edit_as" />
						<input type="hidden" name="ugid_int" value="<?php echo $selected_ugid_int; ?>" />
						<input type="hidden" name="access_type_string" value="<?php echo $access_type; ?>" />
						<?php
							$reload->create();

							if ($access_type == 'file')
							{
								echo '<input type="hidden" name="access_type_id" value="'.$fileid_int.'" />';
							}
							elseif ($access_type == 'link')
							{
								echo '<input type="hidden" name="access_type_id" value="'.$linkid_int.'" />';
							}
							elseif ($access_type == 'folder')
							{
								echo '<input type="hidden" name="access_type_id" value="'.$_GET['fid_int'].'" />';
							}
							elseif ($access_type == 'project')
							{
								echo '<input type="hidden" name="access_type_id" value="'.$_GET['pid_int'].'" />';
							}
						?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<tr class="content_tr_1">
								<td colspan="2">
									<?php get_text(176);//Please regard the following restrictions for the ... ?>
									<ul>
										<li><?php get_text(177);//It is not possible to grant authorizations to ... ?></li>
										<li><?php get_text(178);//It is not possible to revoke ... ?></li>
										<li><?php get_text(179);//One may only edit usergroups which ... ?></li>
										<li><?php get_text(180);//It is not possible to edit access authorizations of other usergroups ... ?></li>
									</ul>
								</td>
							</tr>
							<tr class="content_tr_1" valign="top">
								<td width="40%"><strong><?php get_text(181);//Select usergroup ?>:</strong></td>
								<td width="60%">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td width="50%">
												<strong><?php get_text(114);//Benutzergruppe ?></strong><br />
												<select name="usergroup_int" size="6" class="ipt_150" onchange="user_group_refresh_site();">
													<?php
														$save_group_access = array();
														$ugsql = $db->select("SELECT usergroup_id, usergroup FROM fom_user_group ORDER BY usergroup ASC");
														while($ugresult = $db->fetch_array($ugsql))
														{
															$show_usergroup = false;
															//Der aktuelle user ist dieser Usergruppe zugeordnet
															//Der User darf seine eigene Usergruppe nicht bearbeiten
															if (isset($user_group_access_array['usergroup_access']) and isset($user_group_access_array['usergroup_access'][$ugresult['usergroup_id']]))
															{
																$show_usergroup = false;
															}
															else
															{
																$ac->set_foreign_key(0, array($ugresult['usergroup_id']));
																$group_access_array = array();
																//Datei
																if (isset($_GET['fileid_int']) and !empty($_GET['fileid_int']))
																{
																	$group_access_array = $ac->get_access('file', $_GET['fileid_int']);
																}
																//Verzeichnis
																elseif (isset($_GET['fid_int']) and !empty($_GET['fid_int']))
																{
																	$group_access_array = $ac->get_access('folder', $_GET['fid_int']);
																}
																//Projekt
																elseif (isset($_GET['pid_int']) and !empty($_GET['pid_int']))
																{
																	$group_access_array = $ac->get_access('project', $_GET['pid_int']);
																}

																if (!empty($group_access_array))
																{
																	$save_group_access[$ugresult['usergroup_id']] = $group_access_array;
																	$show_usergroup = $ac->compare_acces_arrays($user_group_access_array, $group_access_array);

																}
															}

															if ($show_usergroup == true)
															{
																if ($selected_ugid_int == $ugresult['usergroup_id'])
																{
																	$selected = ' selected="selected"';
																}
																else
																{
																	$selected = '';
																}

																echo '<option value="'.$ugresult['usergroup_id'].'"'.$selected.'>'.$ugresult['usergroup'].'</option>';
															}
														}
														//ACHTUNG wichtig da sonst die Access Klasse mit den Falschen Benutzergruppen Arbeitet
														$ac->set_foreign_key();
													?>
												</select>
											</td>
											<td width="50%">
												<?php
													if ($selected_ugid_int > 0)
													{
												?>
														<strong><?php echo get_text(113, 'return').' '.get_text(112, 'return');//Nachname Vorname ?></strong><br />
														<select name="userid_int" size="6" class="ipt_150">
															<option value="0" selected="selected">Alle Benutzer</option>
															<?php
																$sql = $db->select('SELECT t2.user_id, t2.vorname, t2.nachname FROM fom_user_membership t1
																					LEFT JOIN fom_user t2 ON t1.user_id=t2.user_id
																					WHERE t1.usergroup_id='.$selected_ugid_int.' ORDER BY t2.nachname, t2.vorname ASC');
																while ($result = $db->fetch_array($sql))
																{
																	echo '<option value="'.$result['user_id'].'">'.$result['nachname'].' '.$result['vorname'].'</option>';
																}
															?>
														</select>
												<?php
													}
													else
													{
														echo '&nbsp;';
													}
												?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr class="content_tr_1"><td colspan="2"><hr /></td></tr>
							<tr class="content_tr_1" valign="top">
								<td><strong><?php get_text('usergroup');//Usergroup ?>:</strong></td>
								<td>
									<?php
										$sql = $db->select('SELECT usergroup FROM fom_user_group WHERE usergroup_id='.$selected_ugid_int);
										$result = $db->fetch_array($sql);
										echo $result['usergroup'];
									?>
								</td>
							</tr>
							<tr class="content_tr_1" valign="top">
								<td><strong><?php get_text('project');//Project ?>:</strong></td>
								<td>
									<?php
										$sql = $db->select("SELECT projekt_name FROM fom_projekte WHERE projekt_id=".$_GET['pid_int']);
										$result = $db->fetch_array($sql);
										echo $result['projekt_name'];
									?>
								</td>
							</tr>
							<?php
								//Liste der moeglichen zugriffsrechte
								$access_list = $ac->get_access_list();
							?>
								<tr class="content_tr_1" valign="top">
									<td><strong><?php get_text(182);//Access authorizations ?>:</strong></td>
									<td>
										<?php
											if (isset($save_group_access[$selected_ugid_int]))
											{
												$simplify_access = $ac->simplify_access_array($save_group_access[$selected_ugid_int]);
												foreach($access_list as $i => $v)
												{
													//E-Mailbenachrichtigung nur auf Projektebene einstellbar
													if ($i != 'mn')
													{
														if ($simplify_access[$i] == true)
														{
															$checked = ' checked="checked"';
														}
														else
														{
															$checked = '';
														}

														if ($ac->compare_acces_arrays($user_group_access_array, $save_group_access[$selected_ugid_int], $i))
														{
															echo '<input type="checkbox" name="access_array['.$i.']" value="1"'.$checked.' /> '.$v."<br />\n";
														}
													}
												}
											}
										?>
									</td>
								</tr>
							<tr class="content_tr_1">
								<td colspan="20" align="center"><br /><br /><input type="submit" value="<?php get_text('save');//Speichern ?>"<?php if (empty($selected_ugid_int)){echo ' disabled="disabled"';}?> /></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>