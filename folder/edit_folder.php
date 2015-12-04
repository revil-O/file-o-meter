<?php
	/**
	 * edit folder form
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$sql = $db->select('SELECT folder_name, bemerkungen FROM fom_folder WHERE folder_id='.$_GET['fid_int']);
	$result = $db->fetch_array($sql);
?>
<script type="text/javascript">
	function chk_form()
	{
		if(document.form_editfolder.foldername_string.value == "")
		{
			alert("<?php get_text(95, 'echo', 'decode_off');//Please complete all mandatory fields! //PFLICHTFELDER ?>");
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
		<td class="main_table_header" width="100%"><?php get_text(162);//Edit folder ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" name="form_editfolder" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="edit_folder" />
				<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr class="content_tr_1">
						<td colspan="2"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
					</tr>
					<tr class="content_tr_1">
						<td width="20%"><strong><?php get_text('folder');//Folder ?>:*</strong></td>
						<td width="80%"><input type="text" name="foldername_string" class="ipt_200" value="<?php echo html_entity_decode($result['folder_name'], ENT_QUOTES, 'UTF-8'); ?>" /></td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('description');//Description ?>:</strong></td>
						<td><textarea name="foldercomment_string" class="ipt_200" rows="6" cols="50"><?php echo html_entity_decode($result['bemerkungen'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>