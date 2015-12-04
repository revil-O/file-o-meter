<?php
	/**
	 * edit logbook
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(360);//Logbook ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="logbook" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="logbook" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="15%" />
						<col width="85%" />
					</colgroup>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php echo get_text(367, 'retrun').' / '.get_text(368, 'retrun');//Login / Logout ?>:</strong></td>
						<td>
							<input type="radio" name="log_login_int" value="1"<?php if (!isset($setup_array['other_settings']['logbook']['login']) or $setup_array['other_settings']['logbook']['login'] == true){ echo ' checked="checked"';} ?> /><?php get_text('ja');//yes ?>
							<input type="radio" name="log_login_int" value="0"<?php if (isset($setup_array['other_settings']['logbook']['login']) and $setup_array['other_settings']['logbook']['login'] == false){ echo ' checked="checked"';} ?> /><?php get_text('nein');//no ?>
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