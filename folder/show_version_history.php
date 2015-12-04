<?php
	/**
	 * displays the version history of a file
	 * @package file-o-meter
	 * @subpackage folder
	 */

	if (isset($_GET['fileid_int']))
	{
		$vh = new VersionHistory;
		$cal = new Calendar;

		$result = $vh->get_file_overview($_GET['fileid_int']);

		if ($result['result'] == true)
		{
?>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr valign="middle">
					<td class="main_table_header" width="100%"><?php get_text(168);//Version history ?> "<?php echo $result['data'][0]['org_name']; ?>"</td>
				</tr>
				<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
				<tr>
					<td colspan="2" class="main_table_content">
						<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<tr>
								<td class="content_header_1" width="10%" align="center"><?php get_text('actions');//Actions ?></td>
								<td class="content_header_2" width="30%"><?php get_text('filename');//Filename ?></td>
								<td class="content_header_2" width="10%"><?php get_text('filesize');//Filesize ?></td>
								<td class="content_header_2" width="25%"><?php get_text(165);//Uploaded on ?></td>
								<td class="content_header_2" width="25%"><?php get_text(167);//Uploaded by ?></td>
							</tr>
							<?php
								$style = 1;
								for($i = 0; $i < count($result['data']); $i++)
								{
							?>
									<tr class="content_tr_<?php echo $style; ?>">
										<td align="center">
											<?php
												//Aktuelle Version
												if ($i == 0)
												{
													echo '<a href="'.FOM_ABS_URL.'inc/download.php'.$gv->create_get_string('?fileid_int='.$result['data'][$i]['file_id'].'&amp;pid_int='.$_GET['pid_int']).'">'.get_img('disk.png', get_text('download','return'), get_text('download','return')).'</a>';//Download
												}
												//Subversionen
												else
												{
													echo '<a href="'.FOM_ABS_URL.'inc/download.php'.$gv->create_get_string('?fileid_int='.$result['data'][$i]['subfile_id'].'&amp;pid_int='.$_GET['pid_int'].'&amp;typ_string=subversion').'">'.get_img('disk.png', get_text('download','return'), get_text('download','return')).'</a>';//Download
												}
											?>
										</td>
										<td><?php echo $result['data'][$i]['org_name']; ?></td>
										<td><?php echo $result['data'][$i]['file_size']; ?></td>
										<td><?php echo $cal->GetWinTime($result['data'][$i]['save_time'], 'all'); ?></td>
										<td><?php echo $result['data'][$i]['user']; ?></td>
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
<?php
		}
	}
?>