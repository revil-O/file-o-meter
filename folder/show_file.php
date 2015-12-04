<?php
	/**
	 * show file information
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$sql = $db->select('SELECT org_name, bemerkungen, tagging FROM fom_files WHERE file_id='.$_GET['fileid_int']);
	$result = $db->fetch_array($sql);
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(276);//Show file information ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
				<tr class="content_tr_1">
					<td colspan="2"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
				</tr>
				<tr class="content_tr_1">
					<td width="20%"><strong><?php get_text('file');//File ?>:</strong></td>
					<td width="80%"><?php echo $result['org_name']; ?></td>
				</tr>
				<tr class="content_tr_1" valign="top">
					<td><strong><?php get_text(153);//Keywords ?>:</strong></td>
					<td><?php echo html_entity_decode($result['tagging'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr class="content_tr_1" valign="top">
					<td><strong><?php get_text(85);//Document type ?>:</strong></td>
					<td>
						<?php
							$sub_sql = $cdb->select('SELECT * FROM fom_document_type ORDER BY document_type ASC');
							while($sub_result = $cdb->fetch_array($sub_sql))
							{
								$s_sql = $cdb->select('SELECT document_type_id FROM fom_document_type_file WHERE document_type_id='.$sub_result['document_type_id'].' AND file_id='.$_GET['fileid_int']);
								$s_result = $cdb->fetch_array($s_sql);

								if (isset($s_result['document_type_id']) and $s_result['document_type_id'] > 0)
								{
									echo $sub_result['document_type'].'<br />';
								}
							}
						?>
					</td>
				</tr>
				<tr class="content_tr_1" valign="top">
					<td><strong><?php get_text('description');//Description ?>:</strong></td>
					<td><?php echo html_entity_decode($result['bemerkungen'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>