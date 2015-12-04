<?php
	/**
	 * edit useraccount
	 * @package file-o-meter
	 * @subpackage user
	 */

	if (isset($_GET['uid_int']))
	{
		$sql = $db->select("SELECT * FROM fom_user WHERE user_id=".$_GET['uid_int']);
		$result = $db->fetch_array($sql);


		$array_membership = array();

		$sql_membership = $db->select("SELECT usergroup_id FROM fom_user_membership WHERE user_id=".$_GET['uid_int']);
		while($result_membership = $db->fetch_array($sql_membership))
		{
			$array_membership[] = $result_membership['usergroup_id'];
		}
?>
		<script type="text/javascript">
			function chk_form()
			{
				if(document.edit_user.email_string.value == "")
				{
					alert("<?php get_text(91, 'echo', 'decode_off');//Please enter an E-Mail address! ?>");
					document.edit_user.email_string.focus();
					return false;
				}

				if(document.getElementById("usergroup_id_ary").value == "")
				{
					alert("<?php get_text(111, 'echo', 'decode_off');//Please specify an usergroup! ?>");
					document.edit_user.usergroup_id_ary.focus();
					return false;
				}
			}
		</script>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(116);//Edit useraccount ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?fileinc=user'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<form method="post" action="index.php<?php echo $gv->create_get_string(); ?>" name="edit_user" onsubmit="return chk_form();" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="edit_user" />
						<input type="hidden" name="uid_int" value="<?php echo $_GET['uid_int']; ?>" />
						<?php $reload->create();?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<tr class="content_tr_1">
								<td width="30%"><strong><?php get_text(1);//Username ?>:</strong></td>
								<td width="70%"><?php echo $result['loginname']; ?></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text(2);//Password ?>:</strong></td>
								<td><input type="text" name="pw_string" class="ipt_150" /> <?php get_text(117);//Leave blank to retain current value. ?></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text('email');//E-Mail ?>:*</strong></td>
								<td><input type="text" name="email_string" value="<?php echo html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8'); ?>" class="ipt_150" /></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text('firstname');//First name ?>:</strong></td>
								<td><input type="text" name="vorname_string" value="<?php echo html_entity_decode($result['vorname'], ENT_QUOTES, 'UTF-8'); ?>" class="ipt_150" /></td>
							</tr>

							<tr class="content_tr_1">
								<td><strong><?php get_text('lastname');//Last name ?>:</strong></td>
								<td><input type="text" name="nachname_string" value="<?php echo html_entity_decode($result['nachname'], ENT_QUOTES, 'UTF-8'); ?>" class="ipt_150" /></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text('usergroup');//Usergroup ?>:*</strong></td>
								<td>
									<select name="usergroup_id_ary[]" id="usergroup_id_ary" class="ipt_150" size="5" multiple="multiple">
										<option value=""><?php get_text('please_select');//Please select ?></option>
										<?php
											$ugsql = $db->select("SELECT usergroup_id, usergroup FROM fom_user_group ORDER BY usergroup ASC");

											while($ugresult = $db->fetch_array($ugsql))
											{
												if (in_array($ugresult['usergroup_id'], $array_membership))
												{
													$select = ' selected="selected"';
												}
												else
												{
													$select = '';
												}
												echo '<option value="'.$ugresult['usergroup_id'].'"'.$select.'>'.$ugresult['usergroup'].'</option>';
											}
										?>
									</select>
								</td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text('language');//Language ?>:*</strong></td>
								<td>
									<select name="language_int" class="ipt_200">
										<?php
											//main_language id und namen auslesen
											$sql_setup_lng = $cdb->select('SELECT main_language_id FROM fom_setup WHERE setup_id=1');
											$result_setup_lng = $cdb->fetch_array($sql_setup_lng);

											if (!isset($result_setup_lng['main_language_id']) or empty($result_setup_lng['main_language_id']))
											{
												$compare_language_id = 1;
											}
											else
											{
												$compare_language_id = $result_setup_lng['main_language_id'];
											}

											//userlanguage id auslesen
											if (isset($result['language_id']) && $result['language_id'] > 0)
											{
												$compare_language_id = $result['language_id'];
											}

											$sql_languages = $db->select("SELECT * FROM fom_languages WHERE visible='j' ORDER BY language_name ASC");
											while($result_languages = $db->fetch_array($sql_languages))
											{
												if ($result_languages['language_id'] == $compare_language_id)
												{
													$selected = ' selected="selected"';
												}
												else
												{
													$selected = '';
												}

												//hinweis ausgeben, wenn es sich um die hauptsprache handelt
												if (isset($result_setup_lng['main_language_id']) && $result_languages['language_id'] === $result_setup_lng['main_language_id'])
												{
													$main_language_string = ' ('.get_text(271, 'return').')';//Main language
												}
												else
												{
													$main_language_string = '';
												}

												echo '<option value="'.$result_languages['language_id'].'"'.$selected.'>'.$result_languages['language_name'].$main_language_string.'</option>';
											}
										?>
									</select>
								</td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text(118);//Useraccount enabled ?>:*</strong></td>
								<td>
									<input type="radio" name="loginaktiv_int" value="1"<?php if ($result['login_aktiv'] == '1'){echo ' checked="checked"';}?> /> <?php get_text('ja');//yes ?> <input type="radio" name="loginaktiv_int" value="0"<?php if ($result['login_aktiv'] != '1'){echo ' checked="checked"';}?> /> <?php get_text('nein');//no ?>
								</td>
							</tr>
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