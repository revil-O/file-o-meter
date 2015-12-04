<?php
	/**
	 * provides a list of all projects
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage project
	 */

	$ffd = new FileFolderDel();
?>
	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(57);//Project management ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="100%" align="right">
						<?php
							if ($ac->chk('_PROJECT_V', 'w'))
							{
								echo '<a href="index.php'.$gv->create_get_string('?fileinc=add_projekt').'">'.get_img('page_add.png').' '.get_text(125,'return').' &raquo;</a><br /><br />';//Add project
							}
							else
							{
								echo '&nbsp;<br />';
							}
						?>
					</td>
				</tr>
			</table>
			<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
				<tr>
					<td class="content_header_1" width="90%"><?php get_text('project');//Project ?></td>
					<td class="content_header_2" width="10%"><?php get_text('actions');//Actions ?></td>
				</tr>
				<?php
					$style = 1;
					$sql = $db->select("SELECT * FROM fom_projekte ORDER BY projekt_name ASC");
					while($result = $db->fetch_array($sql))
					{
				?>
						<tr class="content_tr_<?php echo $style; ?>">
							<td><?php echo $result['projekt_name']; ?></td>
							<td>
								<?php
									if ($ac->chk('_PROJECT_V', 'w'))
									{
								?>
										<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$result['projekt_id'].'&amp;fileinc=edit_projekt'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a>
								<?php
										if ($ffd->deleted_object_exists($result['projekt_id']))
										{
								?>
											<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$result['projekt_id'].'&amp;fileinc=trash_projekt'); ?>"><?php echo get_img('bin.png', get_text(384, 'return'), get_text(384, 'return'));//Gel&ouml;schte Objekte anzeiegn ?></a>
								<?php
										}
										else
										{
											echo get_img('bin_closed.png', get_text(385, 'return'), get_text(385, 'return'));//Keine Gel&ouml;schten Objekte vorhanden
										}

										if ($result['anzeigen'] == 1)
										{
								?>
											<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$result['projekt_id'].'&amp;fileinc=del_projekt'); ?>"><?php echo get_img('drive_delete.png', get_text(381, 'return'), get_text(381, 'return'));//Projekt l&ouml;schen ?></a>
								<?php
										}
										else
										{
								?>
											<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$result['projekt_id'].'&amp;fileinc=restore_projekt'); ?>"><?php echo get_img('drive_add.png', get_text(386, 'return'), get_text(386, 'return'));//Projekt wiederherstellen ?></a>
											<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$result['projekt_id'].'&amp;fileinc=kill_projekt'); ?>"><?php echo get_img('delete.png', get_text(387, 'return'), get_text(387, 'return'));//Projekt entg&uuml;ltig l&ouml;schen ?></a>
								<?php
										}
									}
									else
									{
										echo '&nbsp;';
									}
								?>
							</td>
						</tr>
				<?php
						if ($style == 1)
						{
							$style++;
						}
						else
						{
							$style = 1;
						}
					}
				?>
			</table>
		</td>
	</tr>
</table>