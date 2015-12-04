<?php
	/**
	 * add file
	 * @package file-o-meter
	 * @subpackage folder
	 */
	$fi = new FileInfo();
?>
<script type="text/javascript">
	function set_key_words(type)
	{
		if (type == 0)
		{
			document.getElementById('keyword_tr').style.display = "";
			document.getElementById('az_keyword_tr').style.display = "none";
		}
		else
		{
			document.getElementById('keyword_tr').style.display = "none";
			document.getElementById('az_keyword_tr').style.display = "";

			document.form_addnewfile.filesearch_string.value = "";
		}
	}
	function add_az_keyword()
	{
		var az_sign_td = document.getElementById('az_sign_td').innerHTML;
		var az_search_td = document.getElementById('az_search_td').innerHTML;

		az_sign_td += '<select name="az_sign_array[]" class="ipt_150"><option value="empty"><?php get_text(303);//Use first character ?><\/option><optgroup label="<?php get_text(304);//Numbers ?>"><option value="0">0<\/option><option value="1">1<\/option><option value="2">2<\/option><option value="3">3<\/option><option value="4">4<\/option><option value="5">5<\/option><option value="6">6<\/option><option value="7">7<\/option><option value="8">8<\/option><option value="9">9<\/option><\/optgroup><optgroup label="<?php get_text(305);//Alphabetic characters ?>"><option value="A">A<\/option><option value="B">B<\/option><option value="C">C<\/option><option value="D">D<\/option><option value="E">E<\/option><option value="F">F<\/option><option value="G">G<\/option><option value="H">H<\/option><option value="I">I<\/option><option value="J">J<\/option><option value="K">K<\/option><option value="L">L<\/option><option value="M">M<\/option><option value="N">N<\/option><option value="O">O<\/option><option value="P">P<\/option><option value="Q">Q<\/option><option value="R">R<\/option><option value="S">S<\/option><option value="T">T<\/option><option value="U">U<\/option><option value="V">V<\/option><option value="W">W<\/option><option value="X">X<\/option><option value="Y">Y<\/option><option value="Z">Z<\/option><\/optgroup><optgroup label="<?php get_text(306);//Special char ?>"><option value="@">@<\/option><\/optgroup><\/select><br />';
		az_search_td += '<input type="text" name="az_search_array[]" class="ipt_200" /><br />';

		document.getElementById('az_sign_td').innerHTML = az_sign_td;
		document.getElementById('az_search_td').innerHTML = az_search_td;
	}
	function chk_form()
	{
		if(document.form_addnewfile.file.value == "")
		{
			alert("<?php get_text(151, 'echo', 'decode_off');//Please select a file! ?>");
			return false;
		}
		else
		{
			return true;
		}
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(152);//Add file ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" name="form_addnewfile" enctype="multipart/form-data" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="add_newfile" />
				<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
				<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $setup_array['upload_max_filesize']; ?>" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="20%" />
						<col width="80%" />
					</colgroup>
					<tr class="content_tr_1">
						<td colspan="2"><?php echo $gt->GetFolderPfad($_GET['pid_int']);?></td>
					</tr>
					<?php
						if (defined('FOM_AZ_REGISTER') and FOM_AZ_REGISTER == true)
						{
					?>
							<tr class="content_tr_1">
								<td colspan="2">
									<input type="radio" name="az_register" value="0" checked="checked" onchange="set_key_words(0);" /> <?php get_text(307);//Standard upload ?><br />
									<input type="radio" name="az_register" value="1" onchange="set_key_words(1);" /> <?php get_text(308);//A-Z Register Upload ?>
								</td>
							</tr>
					<?php
						}
					?>
					<tr class="content_tr_1">
						<td><strong><?php get_text('file');//File ?>:</strong></td>
						<td><input type="file" name="file" class="ipt_200" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;max. <?php echo $fi->get_html_filesize($setup_array['upload_max_filesize']); ?>&nbsp;&nbsp;<span id="ShowUploadStatus">&nbsp;</span></td>
					</tr>
					<tr class="content_tr_1" valign="top" id="keyword_tr">
						<td><strong><?php get_text(153);//Keywords ?>:</strong></td>
						<td><textarea name="filesearch_string" rows="3" cols="50" class="ipt_200"></textarea></td>
					</tr>
					<?php
						if (defined('FOM_AZ_REGISTER') and FOM_AZ_REGISTER == true)
						{
					?>
							<tr class="content_tr_1" valign="top" id="az_keyword_tr" style="display: none;">
								<td colspan="2">
									<strong><?php get_text(309);//Define keywords for the A-Z register ?></strong><br />
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td width="20%">&nbsp;</td>
											<td width="20%"><strong><?php get_text(310);//Character ?></strong></td>
											<td width="25%"><strong><?php get_text(311);//Word ?></strong></td>
											<td width="35%">&nbsp;</td>
										</tr>
										<tr id="az_search_tr" valign="top">
											<td>&nbsp;</td>
											<td id="az_sign_td">
												<select name="az_sign_array[]" class="ipt_150">
													<option value="empty"><?php get_text(303);//Use first character ?></option>
													<optgroup label="<?php get_text(304);//Numbers ?>">
														<?php
															for ($i = 0; $i < 10; $i++)
															{
																echo '<option value="'.$i.'">'.$i.'</option>';
															}
														?>
													</optgroup>
													<optgroup label="<?php get_text(305);//Alphabetic characters ?>">
														<?php
															$sign = "A";
															for ($i = 0; $i < 26; $i++)
															{
																echo '<option value="'.$sign.'">'.$sign.'</option>';
																$sign++;
															}
														?>
													</optgroup>
													<optgroup label="<?php get_text(306);//Special char ?>">
														<option value="@">@</option>
													</optgroup>
												</select><br />
											</td>
											<td id="az_search_td">
												<input type="text" name="az_search_array[]" class="ipt_200" /><br />
											</td>
											<td><strong onclick="add_az_keyword();" style="cursor: pointer;">&nbsp;+</strong></td>
										</tr>
									</table>
								</td>
							</tr>
					<?php
						}
					?>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(85);//Document type ?>:</strong></td>
						<td>
							<select name="document_type[]" class="ipt_200" size="5" multiple="multiple">
								<option value="" selected="selected">- <?php get_text(154);//No selection ?> -</option>
								<?php
									$sql = $cdb->select('SELECT * FROM fom_document_type ORDER BY document_type  ASC');
									while($result = $cdb->fetch_array($sql))
									{
										echo '<option value="'.$result['document_type_id'].'">'.$result['document_type'].'</option>';
									}
								?>
							</select>
							&nbsp;
							<?php get_text(155);//Hold CTRL-Key for multiple selection ?>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('description');//Description ?>:</strong></td>
						<td><textarea name="filecomment_string" rows="3" cols="50" class="ipt_200"></textarea></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" onclick="StartUploadFileProzess();" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>