<?php
	/**
	 * delete folder form
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$sql = $db->select('SELECT folder_name FROM fom_folder WHERE folder_id='.$_GET['fid_int']);
	$result = $db->fetch_array($sql);
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(159);//Delete folder ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="del_folder" />
				<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr class="content_tr_1">
						<td colspan="2" class="error"><strong><?php get_text(160);//Do you really want to delete this folder? ?></strong></td>
					</tr>
					<tr class="content_tr_1">
						<td width="20%"><strong><?php get_text('folder');//Folder ?>:</strong></td>
						<td width="80%"><?php echo $result['folder_name']; ?></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center"><input type="submit" value="<?php get_text('del');//Loeschen ?>" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>