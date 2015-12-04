<?php
	/**
	 * edit link
	 * @package file-o-meter
	 * @subpackage folder
	 */

	if (isset($_GET['linkid_int']) and $_GET['linkid_int'] > 0)
	{
		$sql = $db->select('SELECT name, link, bemerkungen, tagging FROM fom_link WHERE link_id='.$_GET['linkid_int']);
		$result = $db->fetch_array($sql);

		if (defined('FOM_AZ_REGISTER') and FOM_AZ_REGISTER == true)
		{
			$sub_sql = $cdb->select('SELECT word_id FROM fom_search_word_az_link WHERE link_id='.$_GET['linkid_int']);
			$sub_result = $cdb->fetch_array($sub_sql);

			if (isset($sub_result['word_id']) and !empty($sub_result['word_id']))
			{
				$az_words_exist = true;
			}
			else
			{
				$az_words_exist = false;
			}
		}
		else
		{
			$az_words_exist = false;
		}

		if (strpos($result['link'], 'ftp://') !== false)
		{
			$protokoll = 'ftp://';
		}
		elseif (strpos($result['link'], 'file:///') !== false)
		{
			$protokoll = 'file:///';
		}
		elseif (strpos($result['link'], 'https://') !== false)
		{
			$protokoll = 'https://';
		}
		else
		{
			$protokoll = 'http://';
		}
?>
		<script type="text/javascript">
			function chk_form()
			{
				if(document.form_editlink.link_string.value == "")
				{
					alert("<?php get_text(287, 'echo', 'decode_off');//Bitte geben Sie einen Link an! ?>");
					return false;
				}
				else
				{
					return true;
				}
			}

			function set_protokoll()
			{
				document.form_editlink.tmp_protokoll_string.value = document.form_editlink.protokoll_string.value;
			}
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

					document.form_addnewlink.filesearch_string.value = "";
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
		</script>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr valign="middle">
				<td class="main_table_header" width="100%"><?php get_text(297);//Link bearbeiten ?></td>
			</tr>
			<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
			<tr>
				<td colspan="2" class="main_table_content">
					<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
					<form method="post" name="form_editlink" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" onsubmit="return chk_form();" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="edit_link" />
						<input type="hidden" name="linkid_int" value="<?php echo $_GET['linkid_int']; ?>" />
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
											<input type="radio" name="az_register" value="0"<?php if (!$az_words_exist){echo ' checked="checked"';}?> onchange="set_key_words(0);" /> <?php get_text(307);//Standard upload ?><br />
											<input type="radio" name="az_register" value="1"<?php if ($az_words_exist){echo ' checked="checked"';}?> onchange="set_key_words(1);" /> <?php get_text(308);//A-Z Register Upload ?>
										</td>
									</tr>
							<?php
								}
							?>
							<tr class="content_tr_1">
								<td><strong><?php get_text(289);//Link ?>:*</strong></td>
								<td>
									<input type="text" name="tmp_protokoll_string" class="ipt_50" readonly="readonly" value="<?php echo $protokoll; ?>" />&nbsp;
									<input type="text" name="link_string" class="ipt_200" maxlength="250" value="<?php echo substr($result['link'], strlen($protokoll)); ?>" />
									<select name="protokoll_string" class="ipt_100" onchange="set_protokoll();">
										<option value="http://"<?php if ($protokoll == 'http://'){echo ' selected="selected"';} ?>><?php get_text(290);//Internet Link ?></option>
										<option value="https://"<?php if ($protokoll == 'https://'){echo ' selected="selected"';} ?>><?php get_text(291);//SSL Internet Link ?></option>
										<option value="file:///"<?php if ($protokoll == 'file:///'){echo ' selected="selected"';} ?>><?php get_text(292);//Laufwerkspfad ?></option>
										<option value="ftp://"<?php if ($protokoll == 'ftp://'){echo ' selected="selected"';} ?>><?php get_text(293);//FTP Link ?></option>
									</select>
								</td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text(294);//Linkname ?>:</strong></td>
								<td>
									<input type="text" name="link_name_string" class="ipt_200" maxlength="250" value="<?php echo $result['name']; ?>" />
								</td>
							</tr>
							<tr class="content_tr_1" valign="top" id="keyword_tr">
								<td><strong><?php get_text(153);//Keywords ?>:</strong></td>
								<td><textarea name="tagging_string" rows="3" cols="50" class="ipt_200"><?php echo $result['tagging'];?></textarea></td>
							</tr>
							<?php
								if (defined('FOM_AZ_REGISTER') and FOM_AZ_REGISTER == true)
								{
							?>
									<tr class="content_tr_1" valign="top" id="az_keyword_tr"<?php if (!$az_words_exist){' style="display: none;"';} ?>>
										<td colspan="2">
											<strong><?php get_text(309);//Define keywords for the A-Z register ?></strong><br />
											<table cellpadding="0" cellspacing="0" border="0" width="100%">
												<tr>
													<td width="20%">&nbsp;</td>
													<td width="20%"><strong><?php get_text(310);//Character ?></strong></td>
													<td width="25%"><strong><?php get_text(311);//Word ?></strong></td>
													<td width="35%">&nbsp;</td>
												</tr>
												<?php
													$sign_array = array();

													$sub_sql = $cdb->select('SELECT t1.sign, t2.word FROM fom_search_word_az_link t1
																			LEFT JOIN fom_search_word t2 ON t1.word_id=t2.word_id
																			WHERE t1.link_id='.$_GET['linkid_int'].' ORDER BY t1.sign ASC');
													while ($sub_result = $cdb->fetch_array($sub_sql))
													{
														$sign_array[] = array('sign'	=> $sub_result['sign'],
																				'word'	=> $sub_result['word']);
													}

													$sign_array_count = count($sign_array);
												?>
												<tr id="az_search_tr" valign="top">
													<td>&nbsp;</td>
													<td id="az_sign_td">
														<?php
															for ($j = 0; $j < $sign_array_count; $j++)
															{
														?>
																<select name="az_sign_array[]" class="ipt_150">
																	<option value="empty"><?php get_text(303);//Use first character ?></option>
																	<optgroup label="<?php get_text(304);//Numbers ?>">
																		<?php
																			for ($i = 0; $i < 10; $i++)
																			{
																				if ("$i" === strval($sign_array[$j]['sign']))
																				{
																					$selected = ' selected="selected"';
																				}
																				else
																				{
																					$selected = '';
																				}
																				echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
																			}
																		?>
																	</optgroup>
																	<optgroup label="<?php get_text(305);//Alphabetic characters ?>">
																		<?php
																			$sign = "A";
																			for ($i = 0; $i < 26; $i++)
																			{
																				if (strtolower($sign) == $sign_array[$j]['sign'])
																				{
																					$selected = ' selected="selected"';
																				}
																				else
																				{
																					$selected = '';
																				}
																				echo '<option value="'.$sign.'"'.$selected.'>'.$sign.'</option>';
																				$sign++;
																			}
																		?>
																	</optgroup>
																	<optgroup label="<?php get_text(306);//Special char ?>">
																		<option value="@"<?php if ($sign_array[$j]['sign'] == '@'){echo ' selected="selected"';} ?>>@</option>
																	</optgroup>
																</select><br />
														<?php
															}
															if ($sign_array_count == 0)
															{
														?>
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
														<?php
															}
														?>
													</td>
													<td id="az_search_td">
														<?php
															for ($j = 0; $j < $sign_array_count; $j++)
															{
														?>
																<input type="text" name="az_search_array[]" class="ipt_200" value="<?php echo $sign_array[$j]['word']; ?>" /><br />
														<?php
															}
															if ($sign_array_count == 0)
															{
														?>
																<input type="text" name="az_search_array[]" class="ipt_200" /><br />
														<?php
															}
														?>
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
								<td><strong><?php get_text('description');//Description ?>:</strong></td>
								<td><textarea name="linkcomment_string" rows="6" cols="50" class="ipt_200"><?php echo $result['bemerkungen'];?></textarea></td>
							</tr>
							<tr class="content_tr_1">
								<td colspan="2" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
<?php
	}
?>