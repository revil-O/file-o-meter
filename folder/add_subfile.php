<?php
	/**
	 * add subfile
	 * @package file-o-meter
	 * @subpackage folder
	 */
	$fi = new FileInfo();
?>
<script type="text/javascript">
	function chk_form()
	{
		if(document.form_addsubfile.file.value == "")
		{
			alert("<?php get_text(151, 'echo', 'decode_off');//Please select a file! ?>");
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
		<td class="main_table_header" width="100%"><?php get_text(156);//Add subfile ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" name="form_addsubfile" enctype="multipart/form-data" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" onsubmit="return chk_form();" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="add_subfile" />
				<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
				<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
				<input type="hidden" name="fileid_int" value="<?php echo $_GET['fileid_int']; ?>" />
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $setup_array['upload_max_filesize']; ?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="20%" />
						<col width="80%" />
					</colgroup>
					<tr class="content_tr_1">
						<td colspan="2"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
					</tr>
					<tr class="content_tr_1">
						<td><strong><?php get_text('file');//File ?>:</strong></td>
						<td><input type="file" name="file" class="ipt_200" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;max. <?php echo $fi->get_html_filesize($setup_array['upload_max_filesize']); ?>&nbsp;&nbsp;<span id="ShowUploadStatus">&nbsp;</span></td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(153);//Keywords ?>:</strong></td>
						<td><textarea name="filesearch_string" class="ipt_200" rows="3" cols="50"></textarea></td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(85);//Document type ?>:</strong></td>
						<td>
							<select name="document_type[]" class="ipt_200" size="5" multiple="multiple">
								<option value="" selected="selected">- <?php get_text(154);//No selection ?> -</option>
								<?php
									$sql = $cdb->select('SELECT * FROM fom_document_type ORDER BY document_type  ASC');
									while($result = $cdb->fetch_array($sql))
									{
										echo '<option value="'.$result['document_type_id'].'">'.$result['document_type'].'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('description');//Description ?>:</strong></td>
						<td><textarea name="filecomment_string" class="ipt_200" rows="6" cols="50"></textarea></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" onclick="StartUploadFileProzess();" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>