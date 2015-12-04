<?php
	/**
	 * edit usergroup
	 * @package file-o-meter
	 * @subpackage user_group
	 */

	if (isset($_GET['ugid_int']))
	{
		$sql = $db->select("SELECT usergroup FROM fom_user_group WHERE usergroup_id=".$_GET['ugid_int']);
		$result = $db->fetch_array($sql);

		if (!empty($result['access']))
		{
			$access = unserialize($result['access']);
		}
		else
		{
			$access = array();
		}
?>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(123);//Edit usergroup?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?fileinc=usergroup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<form method="post" action="index.php<?php echo $gv->create_get_string('?ugid_int='.$_GET['ugid_int']); ?>" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="edit_usergroup" />
						<input type="hidden" name="ugid_int" value="<?php echo $_GET['ugid_int']; ?>" />
						<?php $reload->create();?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<tr class="content_tr_1">
								<td width="40%"><strong><?php get_text('usergroup');//Usergroup ?>:*</strong></td>
								<td width="60%"><input type="text" name="groupnname_string" class="ipt_200" value="<?php echo html_entity_decode($result['usergroup'], ENT_QUOTES, 'UTF-8'); ?>" /></td>
							</tr>
							<?php
								//Andere moeglich Zugriffsrechte
								$other_access_options = $ac->get_other_access_options();

								foreach($other_access_options as $i => $v)
								{
									//Zugriffsrechte auslesen
									$ac_sql = $cdb->select("SELECT access FROM fom_access WHERE type='$i' AND usergroup_id=".$_GET['ugid_int']);
									$ac_result = $cdb->fetch_array($ac_sql);

									//Zugriffsrechte vorhanden
									if (!empty($ac_result['access']))
									{
										//array erstellen
										$access_array = unserialize($ac_result['access']);

										//Lesende zugriffsrechte pruefen
										if (isset($access_array['r']) and $access_array['r'] == true)
										{
											$read_checked = ' checked="checked"';
										}
										else
										{
											$read_checked = '';
										}

										//Schreibende zugriffrechte pruefen
										if (isset($access_array['w']) and $access_array['w'] == true)
										{
											$write_checked = ' checked="checked"';
										}
										else
										{
											$write_checked = '';
										}
									}
									else
									{
										$read_checked = '';
										$write_checked = '';
									}

									echo '<tr class="content_tr_1">
											<td><strong>'.$v.':</strong></td>
											<td>
												<input type="checkbox" name="other_options['.$i.'][r]" value="1"'.$read_checked.' /> '.get_text('show','return').'&nbsp;
												<input type="checkbox" name="other_options['.$i.'][w]" value="1"'.$write_checked.' /> '.get_text('edit','return').'
											</td>
										</tr>';
								}

								echo '<tr class="content_tr_1"><td colspan="2"><hr /></td></tr>';

								//Liste der moeglichen zugriffsrechte
								$access_list = $ac->get_access_list();

								$sql = $db->select("SELECT * FROM fom_projekte WHERE anzeigen='1' ORDER BY projekt_name ASC");
								while($result = $db->fetch_array($sql))
								{
									//Zugriffsrechte auslesen
									$ac_sql = $cdb->select("SELECT access FROM fom_access WHERE type='PROJECT' AND id=".$result['projekt_id']." AND usergroup_id=".$_GET['ugid_int']);
									$ac_result = $cdb->fetch_array($ac_sql);

									//Zugriffsrechte vorhanden
									if (!empty($ac_result['access']))
									{
										//array erstellen
										$access_array = unserialize($ac_result['access']);
									}
									else
									{
										$access_array = array();
									}
							?>
									<tr class="content_tr_1" valign="top">
										<td><strong><?php get_text(120);//Project folder ?> <u><?php echo $result['projekt_name']; ?></u>:</strong></td>
										<td>
											<?php
												foreach($access_list as $i => $v)
												{
													if (isset($access_array[$i]) and $access_array[$i] == true)
													{
														$checked = ' checked="checked"';
													}
													else
													{
														$checked = '';
													}
													echo '<input type="checkbox" name="project['.$result['projekt_id'].']['.$i.']" value="1"'.$checked.' /> '.$v.'<br />';
												}
											?>
										</td>
									</tr>
									<tr class="content_tr_1" valign="top"><td colspan="2">&nbsp;</td></tr>
							<?php
								}
							?>
							<tr class="content_tr_1">
								<td colspan="20" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>