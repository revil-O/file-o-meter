<?php
	/**
	 * edit mail notification form
	 * @package file-o-meter
	 * @subpackage folder
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(403);//E-Mail Benachrichtigung ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" name="form_editmn" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int']); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="edit_mn" />
				<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="20%" />
						<col width="80%" />
					</colgroup>
					<tr class="content_tr_1">
						<td colspan="2"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
					</tr>
					<?php
						$user_trigger_array = $mn->get_trigger_events($_GET['pid_int']);

						foreach ($mn->trigger_array as $index => $txt)
						{
					?>
							<tr class="content_tr_1">
								<td><strong><?php echo $txt; ?>:</strong></td>
								<td><input type="checkbox" name="<?php echo $index; ?>" value="1"<?php if (isset($user_trigger_array[$index]) and $user_trigger_array[$index] == 1){echo ' checked="checked"';} ?> /></td>
							</tr>
					<?php
						}
					?>
					<tr class="content_tr_1">
						<td colspan="2" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>