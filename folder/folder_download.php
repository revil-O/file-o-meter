<?php
	/**
	 * download folder
	 * @package file-o-meter
	 * @subpackage folder
	 */
?>
<script type="text/javascript">
	function submit_form()
	{
		document.zip_download.submit();
		return true;
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(377); //Verzeichnis download ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" name="zip_download" action="folder_zip.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8" target="_blank">
				<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
				<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr class="content_tr_1">
						<td colspan="2" class="error">
							<strong><?php get_text(378); //Moechten Sie wirklich das Verzeichnis Downloaden? ?></strong><br />
							<strong><?php get_text(379); //Achtung: je nach gr&ouml;&szlig;e des Verzeichnisses kann der Vorgang mehrere Minuten dauern! ?></strong>
						</td>
					</tr>
					<tr class="content_tr_1">
						<td width="20%"><strong>><?php get_text(148); //Verzeichnis  ?>:</strong></td>
						<td width="80%"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center">
							<a href="<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>"  onclick="return submit_form();" class="submit_a"><?php get_text(380); //Zip &amp; Start Download ?></a>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>