<?php
	/**
	 * add new version of a file
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$fi = new FileInfo();

	$sql = $db->select('SELECT org_name FROM fom_files WHERE file_id='.$_GET['fileid_int']);
	$result = $db->fetch_array($sql);
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text('access_va');//Add version ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" enctype="multipart/form-data" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileid_int='.$_GET['fileid_int']); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="add_fileversion" />
				<input type="hidden" name="fileid_int" value="<?php echo $_GET['fileid_int']; ?>" />
				<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr class="content_tr_1">
						<td colspan="2"><?php get_text(147);//The current file will be replaced by...?><br /><br /></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
					</tr>
					<tr class="content_tr_1">
						<td width="20%"><strong><?php get_text(146);//Current file ?>:</strong></td>
						<td width="80%"><?php echo $result['org_name']; ?></td>
					</tr>
					<tr class="content_tr_1">
						<td><strong><?php get_text('file');//File ?>:</strong></td>
						<td>
							<input type="file" name="file" class="ipt_200" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;max. <?php echo $fi->get_html_filesize($setup_array['upload_max_filesize']); ?>&nbsp;&nbsp;<span id="ShowUploadStatus">&nbsp;</span>
						</td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" onclick="StartUploadFileProzess();" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>