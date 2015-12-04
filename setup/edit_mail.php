<?php
	/**
	 * edit email settings
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<script type="text/javascript">
	function set_mailserver()
	{
		if (document.mail.sendtype.value == "sendmail")
		{
			document.mail.sendmail.disabled = false;

			document.mail.smtphost.value = "";
			document.mail.smtpport.value = "";
			document.mail.smtpsecure.value = "";
			document.mail.smtpauth.value = "";
			document.mail.smtpuser.value = "";
			document.mail.smtppw.value = "";

			document.mail.smtphost.disabled = true;
			document.mail.smtpport.disabled = true;
			document.mail.smtpsecure.disabled = true;
			document.mail.smtpauth[0].disabled = true;
			document.mail.smtpauth[1].disabled = true;
			document.mail.smtpuser.disabled = true;
			document.mail.smtppw.disabled = true;
		}
		else
		{
			document.mail.sendmail.value = "";
			document.mail.sendmail.disabled = true;

			document.mail.smtphost.disabled = false;
			document.mail.smtpport.disabled = false;
			document.mail.smtpsecure.disabled = false;
			document.mail.smtpauth[0].disabled = false;
			document.mail.smtpauth[1].disabled = false;
			document.mail.smtpuser.disabled = false;
			document.mail.smtppw.disabled = false;
		}

		set_smtp_auth();
	}
	function set_smtp_auth()
	{
		if (document.mail.sendtype.value == "sendmail")
		{
			document.mail.smtpuser.value = "";
			document.mail.smtppw.value = "";

			document.mail.smtpuser.disabled = true;
			document.mail.smtppw.disabled = true;
		}
		else
		{
			if (document.mail.smtpauth[0].checked == true)
			{
				document.mail.smtpuser.disabled = false;
				document.mail.smtppw.disabled = false;
			}
			else
			{
				document.mail.smtpuser.value = "";
				document.mail.smtppw.value = "";

				document.mail.smtpuser.disabled = true;
				document.mail.smtppw.disabled = true;
			}
		}
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text('email');//E-Mail ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="mail" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="mail" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="25%" />
						<col width="75%" />
					</colgroup>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(66);//Sender (E-Mail) ?>:</strong></td>
						<td>
							<input type="text" name="from" class="ipt_200" value="<?php echo $setup_array['mail']['from']; ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(67);//Sender (Name) ?>:</strong></td>
						<td>
							<input type="text" name="fromname" class="ipt_200" value="<?php echo $setup_array['mail']['fromname']; ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(68);//Alternative Textcontent ?>:</strong></td>
						<td>
							<input type="text" name="altbody" class="ipt_200" value="<?php echo $setup_array['mail']['altbody']; ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(70);//Mail transfer agent ?>:</strong></td>
						<td>
							<select name="sendtype" class="ipt_100" onchange="set_mailserver();">
								<option value="sendmail"<?php if ($setup_array['mail']['sendtype'] == 'sendmail'){echo ' selected="selected"';} ?>><?php get_text('sendmail');//Sendmail ?></option>
								<option value="smtp"<?php if ($setup_array['mail']['sendtype'] == 'smtp'){echo ' selected="selected"';} ?>><?php get_text('smtp');//SMTP ?></option>
							</select>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(73);//Sendmailpath ?>:</strong></td>
						<td>
							<input type="text" name="sendmail" class="ipt_200" value="<?php echo $setup_array['mail']['sendmail']; ?>" />
							&nbsp;
							<?php get_text(353);//Current server value (php.ini) ?>:
							&nbsp;
							<?php echo ini_get('sendmail_path');?>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(74);//SMTP-Server ?>:</strong></td>
						<td>
							<input type="text" name="smtphost" class="ipt_200" value="<?php echo $setup_array['mail']['smtphost']; ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(75);//SMTP-Port ?>:</strong></td>
						<td>
							<input type="text" name="smtpport" class="ipt_100" value="<?php echo $setup_array['mail']['smtpport']; ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(76);//SMTP-Sicherheit ?>:</strong></td>
						<td>
							<select name="smtpsecure" class="ipt_100">
								<option value=""><?php get_text('keine');//none?></option>
								<option value="ssl"<?php if ($setup_array['mail']['smtpsecure'] == 'ssl'){echo ' selected="selected"';} ?>>SSL</option>
								<option value="tls"<?php if ($setup_array['mail']['smtpsecure'] == 'tls'){echo ' selected="selected"';} ?>>TLS</option>
							</select>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(78);//SMTP-Authentication ?>:</strong></td>
						<td>
							<input type="radio" name="smtpauth" value="1"<?php if ($setup_array['mail']['smtpauth'] == true){echo ' checked="checked"';}?> onclick="set_smtp_auth();" /> <?php get_text('ja');//yes ?> <input type="radio" name="smtpauth" value="0"<?php if ($setup_array['mail']['smtpauth'] == false){echo ' checked="checked"';}?> onclick="set_smtp_auth();" /> <?php get_text('nein');//no ?>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(79);//SMTP-Benutzer ?>:</strong></td>
						<td>
							<input type="text" name="smtpuser" class="ipt_200" value="<?php echo $setup_array['mail']['smtpuser']; ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(80);//SMTP-Password ?>:</strong></td>
						<td>
							<input type="text" name="smtppw" class="ipt_200" value="<?php echo $setup_array['mail']['smtppw']; ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(404);//E-Mail Typ ?>:</strong></td>
						<td>
							<input type="radio" name="mailtyp" value="txt"<?php if ($setup_array['mail']['mailtyp'] == 'txt'){echo ' checked="checked"';}?> /> <?php get_text(405);//Text e-mail ?>
							<input type="radio" name="mailtyp" value="html"<?php if ($setup_array['mail']['mailtyp'] == 'html'){echo ' checked="checked"';}?> /> <?php get_text(406);//HTML e-mail ?>
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
