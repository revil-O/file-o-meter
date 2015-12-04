<?php
	/**
	 * setup configuration overview
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text('58');//Basic setup ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
				<colgroup>
					<col width="20%" />
					<col width="80%" />
				</colgroup>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text('backup');//Backup ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=backup'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text(60);//Automatic backup enabled ?>:</strong></td>
								<td width="60%">
									<?php
										if ($setup_array['backup']['aktiv_boole'] == true)
										{
											get_text('ja');//yes
										}
										else
										{
											get_text('nein');//no
										}
									?>
								</td>
							</tr>
							<?php
								if ($setup_array['backup']['aktiv_boole'] == true)
								{
							?>
									<tr>
										<td><strong><?php get_text(61);//Send E-Mail notification ?>:</strong></td>
										<td>
											<?php
												if ($setup_array['backup']['mail_aktiv_boole'] == true)
												{
													get_text('ja');//yes
												}
												else
												{
													get_text('nein');//no
												}
											?>
										</td>
									</tr>
									<tr>
										<td><strong><?php get_text(62);//Send downloadlink ?>:</strong></td>
										<td>
											<?php
												if ($setup_array['backup']['mail_link_boole'] == true)
												{
													get_text('ja');//yes
												}
												else
												{
													get_text('nein');//no
												}
											?>
										</td>
									</tr>
									<tr>
										<td><strong><?php get_text(63);//Recipient (E-Mail) ?>:</strong></td>
										<td><?php echo $setup_array['backup']['mail_adress_string'];?></td>
									</tr>
									<tr valign="top">
										<td><strong><?php get_text(64);//Backup dates ?>:</strong></td>
										<td>
											<table cellpadding="2" cellspacing="0" border="0" width="100%">
												<colgroup>
													<col width="30%" />
													<col width="70%" />
												</colgroup>
												<?php
													if ($setup_array['backup']['time_array']['all'] != '--')
													{
												?>
														<tr>
															<td><?php get_text('daily');//daily ?></td>
															<td><?php echo $setup_array['backup']['time_array']['all']; ?> <?php get_text('uhr');//o'clock ?></td>
														</tr>
												<?php
													}
													else
													{
														$day_array = array('mo'		=>	get_text('montag','return'),		//Monday
																			'di'	=>	get_text('dienstag','return'),		//Tuesday
																			'mi'	=>	get_text('mittwoch','return'),		//Wednesday
																			'do'	=>	get_text('donnerstag','return'),	//Thursday
																			'fr'	=>	get_text('freitag','return'),		//Friday
																			'sa'	=>	get_text('samstag','return'),		//Saturday
																			'so'	=>	get_text('sonntag','return')		//Sunday
																			);

														foreach($day_array as $i => $v)
														{
															if ($setup_array['backup']['time_array'][$i] != '--')
															{
																echo '<tr><td>'.$v.'</td><td>'.$setup_array['backup']['time_array'][$i].' '.get_text('uhr','return').'</td></tr>';//o'clock
															}
														}
													}
												?>
											</table>
										</td>
									</tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text('email');//E-Mail ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=mail'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text(66);//Sender (E-Mail) ?>:</strong></td>
								<td width="60%"><?php echo $setup_array['mail']['from']; ?></td>
							</tr>
							<tr>
								<td><strong><?php get_text(67);//Sender (Name) ?>:</strong></td>
								<td><?php echo $setup_array['mail']['fromname']; ?></td>
							</tr>
							<tr valign="top">
								<td><strong><?php get_text(68);//Alternative Textcontent ?>:</strong></td>
								<td><?php echo $setup_array['mail']['altbody']; ?></td>
							</tr>
							<tr>
								<td><strong><?php get_text(70);//Mail transfer agent ?>:</strong></td>
								<td><?php echo $setup_array['mail']['sendtype']; ?></td>
							</tr>
							<?php
								if ($setup_array['mail']['sendtype'] == 'sendmail')
								{
							?>
									<tr>
										<td><strong><?php get_text(73);//Sendmailpath ?>:</strong></td>
										<td><?php echo $setup_array['mail']['sendmail']; ?></td>
									</tr>
							<?php
								}
								else
								{
							?>
									<tr>
										<td><strong><?php get_text(74);//SMTP-Server ?>:</strong></td>
										<td><?php echo $setup_array['mail']['smtphost']; ?></td>
									</tr>
									<tr>
										<td><strong><?php get_text(75);//SMTP-Port ?>:</strong></td>
										<td><?php echo $setup_array['mail']['smtpport']; ?></td>
									</tr>
									<tr>
										<td><strong><?php get_text(76);//SMTP-Sicherheit ?>:</strong></td>
										<td>
											<?php
												if (empty($setup_array['mail']['smtpsecure']))
												{
													get_text('keine');//none
												}
												else
												{
													echo strtoupper($setup_array['mail']['smtpsecure']);
												}
											?>
										</td>
									</tr>
									<tr>
										<td><strong><?php get_text(78);//SMTP-Authentication ?>:</strong></td>
										<td>
											<?php
												if ($setup_array['mail']['smtpauth'] == true)
												{
													get_text('ja');//yes
												}
												else
												{
													get_text('nein');//no
												}
											?>
										</td>
									</tr>
									<tr>
										<td><strong><?php get_text(79);//SMTP-Benutzer ?>:</strong></td>
										<td><?php echo $setup_array['mail']['smtpuser']; ?></td>
									</tr>
									<tr>
										<td><strong><?php get_text(80);//SMTP-Password ?>:</strong></td>
										<td>
											<?php
												for($i = 0; $i < strlen($setup_array['mail']['smtppw']); $i++)
												{
													echo '*';
												}
											?>

										</td>
									</tr>
							<?php
								}
							?>
							<tr>
								<td><strong><?php get_text(404);//E-Mail Typ ?>:</strong></td>
								<td>
									<?php
										if ($setup_array['mail']['mailtyp'] == 'txt')
										{
											get_text(405);//Text e-mail
										}
										else
										{
											get_text(406);//HTML e-mail
										}
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text(4);//Contact person ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=contact'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<?php
							$sql = $db->select("SELECT t1.contact FROM fom_setup t1");
							$result = $db->fetch_array($sql);

							$kontakt_array = unserialize($result['contact']);
						?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text('firstname');//First name ?>:</strong></td>
								<td width="60%"><?php if (isset($kontakt_array['first_name']) and !empty($kontakt_array['first_name'])){echo $kontakt_array['first_name'];} ?></td>
							</tr>
							<tr>
								<td><strong><?php get_text('lastname');//Last name ?>:</strong></td>
								<td><?php if (isset($kontakt_array['last_name']) and !empty($kontakt_array['last_name'])){echo $kontakt_array['last_name'];} ?></td>
							</tr>
							<tr>
								<td><strong><?php get_text('email');//E-Mail ?>:</strong></td>
								<td><?php if (isset($kontakt_array['email']) and !empty($kontakt_array['email'])){echo $kontakt_array['email'];} ?></td>
							</tr>
							<tr>
								<td><strong><?php get_text('tel');//Phone ?>:</strong></td>
								<td><?php if (isset($kontakt_array['phone']) and !empty($kontakt_array['phone'])){echo $kontakt_array['phone'];} ?></td>
							</tr>
							<tr>
								<td><strong><?php get_text('handy');//Mobile ?>:</strong></td>
								<td><?php if (isset($kontakt_array['handy']) and !empty($kontakt_array['handy'])){echo $kontakt_array['handy'];} ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text(82);//Dokumententypen ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=document_type'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text(83);//Anzahl der Dokumententypen ?>:</strong></td>
								<td width="60%">
									<?php
										$sql = $cdb->select('SELECT COUNT(document_type_id) AS count_id FROM fom_document_type');
										$result = $cdb->fetch_array($sql);
										echo $result['count_id'];
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text(271);//Main language ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=language'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text('language');//Language ?>:</strong></td>
								<td width="60%">
									<?php
										$sql = $cdb->select('SELECT main_language_id FROM fom_setup WHERE setup_id=1');
										$result = $cdb->fetch_array($sql);

										if (!isset($result['main_language_id']) or empty($result['main_language_id']))
										{
											$main_language_id = 1;
										}
										else
										{
											$main_language_id = $result['main_language_id'];
										}

										$sql = $cdb->select('SELECT language_name FROM fom_languages WHERE language_id='.$main_language_id);
										$result = $cdb->fetch_array($sql);
										echo $result['language_name'];
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text('date_format');//Date format ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=date_format'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text('date_format');//Date format ?>:</strong></td>
								<td width="60%">
									<?php
										$sql = $cdb->select('SELECT fom_version, date_format, fom_title, template FROM fom_setup WHERE setup_id=1');
										$result = $cdb->fetch_array($sql);
										echo get_text($result['date_format']);
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text('db_title');//Database title ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=db_title'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text('db_title');//Database title ?>:</strong></td>
								<td width="60%"><?php echo $result['fom_title']; ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text(324);//Externe Anwendungen ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=ex_prog'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong>Antiword:</strong></td>
								<td width="60%">
									<?php
										if (strlen(FOM_ABS_PFAD_EXEC_ANTIWORD) <= 30)
										{
											echo FOM_ABS_PFAD_EXEC_ANTIWORD;
										}
										else
										{
											echo '...'.substr(FOM_ABS_PFAD_EXEC_ANTIWORD, -30);
										}
									?>
								</td>
							</tr>
							<tr>
								<td><strong>xpdf:</strong></td>
								<td>
									<?php
										if (strlen(FOM_ABS_PFAD_EXEC_XPDF) <= 30)
										{
											echo FOM_ABS_PFAD_EXEC_XPDF;
										}
										else
										{
											echo '...'.substr(FOM_ABS_PFAD_EXEC_XPDF, -30);
										}
									?>
								</td>
							</tr>
							<tr>
								<td><strong>Ghostscript:</strong></td>
								<td>
									<?php
										if (strlen(FOM_ABS_PFAD_EXEC_GHOSTSCRIPT) <= 30)
										{
											echo FOM_ABS_PFAD_EXEC_GHOSTSCRIPT;
										}
										else
										{
											echo '...'.substr(FOM_ABS_PFAD_EXEC_GHOSTSCRIPT, -30);
										}
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text(360);//Logbook ?>:</strong></u></td>
					<td align="right"><a href="index.php<?php echo $gv->create_get_string('?fileinc=logbook'); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php echo get_text(367, 'retrun').' / '.get_text(368, 'retrun');//Login / Logout ?>:</strong></td>
								<td width="60%">
									<?php
										if (isset($setup_array['other_settings']['logbook']['login']))
										{
											if ($setup_array['other_settings']['logbook']['login'] == true)
											{
												get_text('ja');//yes
											}
											else
											{
												get_text('nein');//no
											}
										}
										else
										{
											get_text('ja');//yes
										}
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text('template');//Template ?>:</strong></u></td>
					<td>&nbsp;</td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text('template');//Template ?>:</strong></td>
								<td width="60%"><?php echo $result['template']; ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="content_tr_1">
					<td colspan="2"><hr /></td>
				</tr>
				<tr class="content_tr_1">
					<td><u><strong><?php get_text('version');//Version ?>:</strong></u></td>
					<td>&nbsp;</td>
				</tr>
				<tr class="content_tr_1">
					<td>&nbsp;</td>
					<td>
						<table cellpadding="2" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="40%"><strong><?php get_text('version');//Version ?>:</strong></td>
								<td width="60%"><?php echo FOM_VERSION; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>