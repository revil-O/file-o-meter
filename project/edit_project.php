<?php
	/**
	 * edit project form
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
				<td class="main_table_header" width="100%"><?php get_text(128);//Edit project?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?fileinc=project'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<form method="post" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int']); ?>" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="edit_project" />
						<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
						<?php $reload->create();?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<tr class="content_tr_1">
								<td width="30%"><strong><?php get_text('project');//Project ?>:*</strong></td>
								<td width="70%"><input type="text" name="projectname_string" class="ipt_200" value="<?php echo html_entity_decode($result['projekt_name'], ENT_QUOTES, 'UTF-8'); ?>" /></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text(127);//Fileserver of the project ?>:</strong></td>
								<td><?php echo $result['pfad']; ?></td>
							</tr>
							<tr class="content_tr_1">
								<td colspan="20" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>