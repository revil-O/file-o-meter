<?php
	/**
	 * create downloadlink form
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$sql = $cdb->select('SELECT file_id, org_name FROM fom_files WHERE file_id='.$_GET['fileid_int']." AND anzeigen='1'");
	$result = $cdb->fetch_array($sql);
?>
<script type="text/javascript">
	function set_time_limit()
	{
		if(document.form_adddownloadlink.date_nolimit_int.checked == true)
		{
			document.form_adddownloadlink.date_string.readOnly = true;
			document.form_adddownloadlink.date_string.value = "";
		}
		else
		{
			document.form_adddownloadlink.date_string.readOnly = false;
		}
	}

	function opencalendar()
	{
		if(document.form_adddownloadlink.date_nolimit_int.checked == false)
		{
			open_calendar('form_adddownloadlink','date_string',<?php if (isset($GLOBALS['user_language'])){echo $GLOBALS['user_language'];} ?>);
		}
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text('access_dl');//Create downloadlink ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" name="form_adddownloadlink" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="add_download" />
				<input type="hidden" name="fileid_int" value="<?php echo $result['file_id']; ?>" />
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
						<td><?php echo $result['org_name']; ?></td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('version');//Version ?>:</strong></td>
						<td>
							<input type="radio" name="version_int" value="1" checked="checked" /> <?php get_text(141);//Use always the newest version ?><br />
							<input type="radio" name="version_int" value="0" /> <?php get_text(142);//Provide only this version for download ?>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(144);//Available till ?>:</strong></td>
						<td>
							<input type="checkbox" name="date_nolimit_int" value="1" onclick="set_time_limit();" checked="checked" /> <?php get_text(143);//No timelimit ?><br />
							<input type="text" name="date_string" id="date_string" class="ipt_100" readonly="readonly" /> <?php echo get_img('calendar.png', get_text('calendar','return'), get_text('calendar','return'), 'image', 0, '', 'onclick="opencalendar();"');//Calendar ?> TT.MM.YYYY
						</td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>