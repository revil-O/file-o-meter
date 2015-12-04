<?php
	/**
	 * delete link form
	 * @package file-o-meter
	 * @subpackage folder
	 */

	if (isset($_GET['linkid_int']) and $_GET['linkid_int'] > 0)
	{
		$sql = $db->select('SELECT file_id, name, link, bemerkungen, link_type FROM fom_link WHERE link_id='.$_GET['linkid_int']);
		$result = $db->fetch_array($sql);

		if ($result['link_type'] == 'INTERNAL')
		{
			$file_sql = $db->select('SELECT org_name, bemerkungen FROM fom_files WHERE file_id='.$result['file_id']);
			$file_result = $db->fetch_array($file_sql);
		}
?>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(295);//Link loeschen ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<form method="post" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="del_link" />
						<input type="hidden" name="linkid_int" value="<?php echo $_GET['linkid_int']; ?>" />
						<?php $reload->create();?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="20%" />
								<col width="80%" />
							</colgroup>
							<?php
								//Externer Link
								if ($result['link_type'] == 'EXTERNAL')
								{
							?>
									<tr class="content_tr_1">
										<td colspan="2" class="error"><strong><?php get_text(296);//Moechten Sie diesen Link wirklich loeschen? ?></strong></td>
									</tr>
									<tr class="content_tr_1">
										<td><strong><?php get_text(294);//Linkname ?>:</strong></td>
										<td><?php echo $result['name']; ?></td>
									</tr>
									<tr class="content_tr_1">
										<td><strong><?php get_text(289);//Link ?>:</strong></td>
										<td><?php echo $result['link']; ?></td>
									</tr>
									<tr class="content_tr_1">
										<td><strong><?php get_text('description');//Description ?>:</strong></td>
										<td><?php echo $result['bemerkungen']; ?></td>
									</tr>
							<?php
								}
								else
								{
							?>
									<tr class="content_tr_1">
										<td colspan="2" class="error"><strong><?php get_text(296);//Moechten Sie diesen Link wirklich loeschen? ?></strong></td>
									</tr>
									<tr class="content_tr_1">
										<td><strong><?php get_text('filename');//Filename ?>:</strong></td>
										<td><?php echo $file_result['org_name']; ?></td>
									</tr>
									<tr class="content_tr_1">
										<td><strong><?php get_text('description');//Description ?>:</strong></td>
										<td><?php echo $file_result['bemerkungen']; ?></td>
									</tr>
							<?php
								}
							?>
							<tr class="content_tr_1">
								<td colspan="2" align="center"><input type="submit" value="<?php get_text('del');//Loeschen ?>" /></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>