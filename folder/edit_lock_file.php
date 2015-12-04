<?php
	/**
	 * check-in/check-out files
	 * @package file-o-meter
	 * @subpackage folder
	 */

	$sql = $db->select('SELECT org_name FROM fom_files WHERE file_id='.$_GET['fileid_int']);
	$result = $db->fetch_array($sql);
	if (isset($_GET['fileinc']) and $GLOBALS['ac']->chk('file', 'w', $_GET['fileid_int']) == true)
	{
		//Ausckeckstatus pruefen
		$check_sql = $cdb->select('SELECT user_id FROM fom_file_lock WHERE file_id='.$_GET['fileid_int']);
		$check_result = $cdb->fetch_array($check_sql);

		$edit_file = true;
		$edit_check_status = false;
		$file_is_checked_out = false;

		//Datei ist ausgecheckt
		if (isset($check_result['user_id']) and $check_result['user_id'] > 0)
		{
			//Die Datei ist von jemanden ausgecheckt
			$file_is_checked_out = true;

			//Der aktuelle User hat die Datei nicht ausgecheckt
			if ($check_result['user_id'] != USER_ID)
			{
				$edit_file = false;
			}
			elseif ($check_result['user_id'] == USER_ID and $GLOBALS['ac']->chk('file', 'w', $_GET['fileid_int']) == true)
			{
				//Der aktuelle User hat die Datei ausgecheckt, kann sie also auch wieder einchecken
				$edit_check_status = true;
			}
		}
		//Datei ist nicht ausgecheckt, kann also von jedem mit schreibrechten ausgecheckt werden
		elseif ($GLOBALS['ac']->chk('file', 'w', $_GET['fileid_int']) == true)
		{
			$edit_check_status = true;
		}
		//Der User hat die Rechte den Auscheckstatus zu ueberschreiben
		if ($GLOBALS['ac']->chk('file', 'ocf', $_GET['fileid_int']))
		{
			$edit_check_status = true;
		}


		if ($_GET['fileinc'] == 'checkout_file' and $file_is_checked_out === false and $edit_check_status === true)
		{
			$header_text = get_text(171,'return');//Checkout file
			$content_text = get_text(173,'return');//Do you really want to checkout this file?
			$button_text = get_text(171,'return');//Checkout file
			$job = 'checkout_file';
		}
		elseif ($_GET['fileinc'] == 'checkin_file' and $file_is_checked_out === true)
		{
			$header_text = get_text(172,'return');//Checkin file
			$content_text = get_text(174,'return');//Do you really want to checkin this file?

			if ($GLOBALS['ac']->chk('file', 'ocf', $_GET['fileid_int']) and $check_result['user_id'] > 0 and $check_result['user_id'] != USER_ID)
			{
				$content_text .= '<br /><strong>'.get_text(175,'return').'</strong>';//Attention: You haven't checked out this file!
			}
			$button_text = get_text(172,'return');//Checkin file
			$job = 'checkin_file';
		}
		if (isset($header_text))
		{
?>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr valign="middle">
					<td class="main_table_header" width="100%"><?php echo $header_text; ?></td>
				</tr>
				<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
				<tr>
					<td colspan="2" class="main_table_content">
						<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
						<form method="post" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fileid_int='.$_GET['fileid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8">
							<input type="hidden" name="job_string" value="<?php echo $job; ?>" />
							<input type="hidden" name="fileid_int" value="<?php echo $_GET['fileid_int']; ?>" />
							<?php $reload->create();?>
							<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
								<tr class="content_tr_1">
									<td width="100%"><?php echo $gt->GetFolderPfad($_GET['pid_int']).$result['org_name'];?></td>
								</tr>
								<tr class="content_tr_1">
									<td><br />
										<?php echo $content_text; ?>
										<br />
										<strong><?php echo $result['org_name']; ?></strong>
									</td>
								</tr>
								<tr class="content_tr_1">
									<td colspan="2" align="center"><br /><input type="submit" value="<?php echo $button_text; ?>" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
<?php
		}
	}
?>