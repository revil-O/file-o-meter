<?php
	/**
	 * edit contact person
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(4);//Contact person ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="contact" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="contact" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="25%" />
						<col width="75%" />
					</colgroup>
					<?php
						$sql = $db->select("SELECT t1.contact FROM fom_setup t1");
						$result = $db->fetch_array($sql);

						$kontakt_array = unserialize($result['contact']);
					?>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('firstname');//First name ?>:</strong></td>
						<td>
							<input type="text" name="kontakt_vorname_string" class="ipt_200" value="<?php if (isset($kontakt_array['first_name']) and !empty($kontakt_array['first_name'])){echo $kontakt_array['first_name'];} ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('lastname');//Last name ?>:</strong></td>
						<td>
							<input type="text" name="kontakt_nachname_string" class="ipt_200" value="<?php if (isset($kontakt_array['last_name']) and !empty($kontakt_array['last_name'])){echo $kontakt_array['last_name'];} ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('email');//E-Mail ?>:</strong></td>
						<td>
							<input type="text" name="kontakt_mail_string" class="ipt_200" value="<?php if (isset($kontakt_array['email']) and !empty($kontakt_array['email'])){echo $kontakt_array['email'];} ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('tel');//Phone ?>:</strong></td>
						<td>
							<input type="text" name="kontakt_tel_string" class="ipt_200" value="<?php if (isset($kontakt_array['phone']) and !empty($kontakt_array['phone'])){echo $kontakt_array['phone'];} ?>" />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('handy');//Mobile ?>:</strong></td>
						<td>
							<input type="text" name="kontakt_handy_string" class="ipt_200" value="<?php if (isset($kontakt_array['handy']) and !empty($kontakt_array['handy'])){echo $kontakt_array['handy'];} ?>" />
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