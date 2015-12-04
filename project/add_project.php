<?php
	/**
	 * add project form
	 * @package file-o-meter
	 * @subpackage project
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(125);//Add project?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=project'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" action="index.php<?php echo $gv->create_get_string(); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="add_project" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr class="content_tr_1">
						<td colspan="20"><?php get_text(126);//Attention: The project will not appear...?><br /><br /></td>
					</tr>
					<tr class="content_tr_1">
						<td width="30%"><strong><?php get_text('project');//Project ?>:*</strong></td>
						<td width="70%"><input type="text" name="projectname_string" class="ipt_200" /></td>
					</tr>
					<tr class="content_tr_1">
						<td width="30%"><strong><?php get_text(127);//Fileserver of the project ?>:*</strong></td>
						<td width="70%"><input type="text" name="fileserver_string" class="ipt_200" value="<?php echo FOM_ABS_PFAD.'files/upload/'; ?>" readonly="readonly" /></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="20" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>