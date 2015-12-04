<?php
	/**
	 * del project form
	 * @package file-o-meter
	 * @subpackage project
	 */

	if (isset($_GET['pid_int']))
	{
		$sql = $db->select("SELECT t1.projekt_id, t1.projekt_name, t2.pfad FROM fom_projekte t1
							LEFT JOIN fom_file_server t2 ON t1.projekt_id=t2.projekt_id
							WHERE t1.projekt_id=".$_GET['pid_int']);
		$result = $db->fetch_array($sql);
?>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(386); //Projekt wiederherstellen ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?fileinc=project'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<form method="post" action="index.php<?php echo $gv->create_get_string('?fileinc=project&amp;pid_int='.$_GET['pid_int']); ?>" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="restore_project" />
						<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
						<?php $reload->create();?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="30%" />
								<col width="70%" />
							</colgroup>
							<tr class="content_tr_1">
								<td colspan="2"><strong><?php get_text(388); //Moechten Sie wirklich dieses Projekt wiederherstellen? ?></strong></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text('project');//Project ?>:</strong></td>
								<td><?php echo $result['projekt_name']; ?></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text(127);//Fileserver of the project ?>:</strong></td>
								<td><?php echo $result['pfad']; ?></td>
							</tr>
							<tr class="content_tr_1">
								<td colspan="20" align="center"><input type="submit" value="<?php get_text(386); //Projekt wiederherstellen ?>" /></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>