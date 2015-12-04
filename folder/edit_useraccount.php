<?php
	/**
	 * edit useraccount
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$sql = $db->select("SELECT loginname, language_id FROM fom_user WHERE user_id=".USER_ID);
	$result = $db->fetch_array($sql);

?>
<script type="text/javascript">
	function chk_form()
	{
		if (document.useraccount.change_pw[1].checked == true)
		{
			if(document.useraccount.current_pw_string.value == "")
			{
				alert("<?php get_text(8, 'echo', 'decode_off');//Please enter the password. ?>");
				document.useraccount.current_pw_string.focus();
				return false;
			}
			else
			{
				if(document.useraccount.pw_string.value == "")
				{
					alert("<?php get_text(8, 'echo', 'decode_off');//Please enter the password. ?>");
					document.useraccount.pw_string.focus();
					return false;
				}
				else
				{
					if(document.useraccount.pw_string.value != document.useraccount.pw2_string.value)
					{
						alert("<?php get_text(358, 'echo', 'decode_off');//The entered passwords are not equal! ?>");
						document.useraccount.pw_string.focus();
						return false;
					}
					else
					{
						return true;
					}
				}
			}
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
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="useraccount" onsubmit="return chk_form();" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="edit_useraccount" />
				<input type="hidden" name="uid_int" value="<?php echo USER_ID;?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="25%" />
						<col width="75%" />
					</colgroup>
					<tr class="content_tr_1"><td colspan="2">&nbsp;</td></tr>
					<tr class="content_tr_1">
						<td class="pad_25_left"><?php get_text(357);//Change password ?>:</td>
						<td>
							<input type="radio" name="change_pw" value="n" class="radio" checked="checked" />
							&nbsp;<?php get_text(359); //Derzeitiges Passwort beibehalten?>
						</td>
					</tr><tr class="content_tr_1">
						<td>&nbsp;</td>
						<td>
							<input type="radio" name="change_pw" value="j" class="radio" />
							&nbsp;<?php get_text(357); //Change password?>
						</td>
					</tr>
					<tr class="content_tr_1">
						<td class="pad_25_left"><?php get_text(1);//Username ?>:</td>
						<td><?php echo $result['loginname']; ?></td>
					</tr>
					<tr class="content_tr_1">
						<td class="pad_25_left"><?php get_text(355);//Current password ?>:</td>
						<td><input type="password" name="current_pw_string" class="ipt_150" /></td>
					</tr>
					<tr class="content_tr_1">
						<td class="pad_25_left"><?php get_text(356);//New password ?>:</td>
						<td><input type="password" name="pw_string" class="ipt_150" /></td>
					</tr>
					<tr class="content_tr_1">
						<td class="pad_25_left"><?php get_text(354);//Repeat password ?>:</td>
						<td><input type="password" name="pw2_string" class="ipt_150" /></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr class="content_tr_1">
						<td class="pad_25_left"><?php get_text('language');//Language ?>:</td>
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
						<td colspan="2" align="center">
							<br />
							<input type="submit" value="<?php get_text('save');//Speichern ?>" />
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>