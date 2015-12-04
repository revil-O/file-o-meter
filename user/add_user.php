<?php
	/**
	 * create useraccount
	 * @package file-o-meter
	 * @subpackage user
	 */
?>
<script type="text/javascript">
	function chk_form()
	{
		if(document.add_user.loginname_string.value == "")
		{
			alert("<?php get_text(9, 'echo', 'decode_off');//Please enter the username. ?>");
			document.add_user.loginname_string.focus();
			return false;
		}
		if(document.add_user.pw_string.value == "")
		{
			alert("<?php get_text(8, 'echo', 'decode_off');//Please enter the password. ?>");
			document.add_user.pw_string.focus();
			return false;
		}
		if(document.add_user.email_string.value == "")
		{
			alert("<?php get_text(91, 'echo', 'decode_off');//Please enter an E-Mail address! ?>");
			document.add_user.email_string.focus();
			return false;
		}
		if(document.getElementById("usergroup_id_ary").value == "")
		{
			alert("<?php get_text(111, 'echo', 'decode_off');//Please specify an usergroup! ?>");
			document.add_user.usergroup_id_ary.focus();
			return false;
		}
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(115);//Create useraccount ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=user'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" action="index.php<?php echo $gv->create_get_string(); ?>" name="add_user" onsubmit="return chk_form();" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="add_user" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr class="content_tr_1">
						<td width="30%"><strong><?php get_text(1);//Username ?>:*</strong></td>
						<td width="70%"><input type="text" name="loginname_string" class="ipt_200" /></td>
					</tr>
					<tr class="content_tr_1">
						<td><strong><?php get_text(2);//Password ?>:*</strong></td>
						<td><input type="text" name="pw_string" class="ipt_200" /></td>
					</tr>
					<tr class="content_tr_1">
						<td><strong><?php get_text('email');//E-Mail ?>:*</strong></td>
						<td><input type="text" name="email_string" class="ipt_200" /></td>
					</tr>
					<tr class="content_tr_1">
						<td><strong><?php get_text('firstname');//First name ?>:</strong></td>
						<td><input type="text" name="vorname_string" class="ipt_200" /></td>
					</tr>

					<tr class="content_tr_1">
						<td><strong><?php get_text('lastname');//Last name ?>:</strong></td>
						<td><input type="text" name="nachname_string" class="ipt_200" /></td>
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
										echo '<option value="'.$ugresult['usergroup_id'].'">'.$ugresult['usergroup'].'</option>';
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
									$sql = $cdb->select('SELECT main_language_id FROM fom_setup WHERE setup_id=1');
									$result = $cdb->fetch_array($sql);

									if (!isset($result['main_language_id']) or empty($result['main_language_id']))
									{
										$main_language_id = 1;
									}
									else
									{
										$main_language_id = $result['main_language_id'];
									}

									$sql = $db->select("SELECT * FROM fom_languages WHERE visible='j' ORDER BY language_name ASC");
									while($result = $db->fetch_array($sql))
									{
										if ($result['language_id'] == $main_language_id)
										{
											$selected = ' selected="selected"';
											$main_language_string = ' ('.get_text(271, 'return').')';//Main language
										}
										else
										{
											$selected = '';
											$main_language_string = '';
										}

										echo '<option value="'.$result['language_id'].'"'.$selected.'>'.$result['language_name'].$main_language_string.'</option>';
									}
								?>
							</select>
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