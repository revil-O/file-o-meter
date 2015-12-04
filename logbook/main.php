<?php
	/**
	 * Logbook overview
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage logbook
	 */

	$kal = new Kalender();

	if (!isset($_POST['filter_userid_int']))
	{
		$_POST['filter_userid_int'] = 0;
	}
	if (!isset($_POST['filter_anzahl_int']))
	{
		$_POST['filter_anzahl_int'] = 20;
	}
	if (!isset($_POST['filter_login_date_from_string']))
	{
		$_POST['filter_login_date_from_string'] = '';
	}
	if (!isset($_POST['filter_login_date_to_string']))
	{
		$_POST['filter_login_date_to_string'] = '';
	}
	if (!isset($_POST['filter_logintype_string']))
	{
		$_POST['filter_logintype_string'] = '';
	}
?>
<script type="text/javascript">
	function confirm_log_del(form_name)
	{
		if (confirm("<?php get_text(365, 'echo', 'decode_off');////Möchten Sie diesen Eintrag wirklich löschen? ?>"))
		{
			document.forms[form_name].submit();
		}
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(360);//Logbook ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<form action="index.php<?php echo $gv->create_get_string('?fileinc=main'); ?>" method="post" name="form_search" accept-charset="UTF-8">
				<input type="hidden" name="searchform" value="1" />
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="border">
					<colgroup>
						<col width="10%" />
						<col width="20%" />
						<col width="25%" />
						<col width="20%" />
						<col width="25%" />
					</colgroup>
					<tr class="table_tr_bg_2">
						<td class="content_header_1"><?php get_text(364); /*Anzahl*/?></td>
						<td class="content_header_2" colspan="4"><?php get_text(183);//Search options ?></td>
					</tr>
					<tr>
						<td class="filter_td_1"><input type="radio" name="filter_anzahl_int" value="20"<?php if ($_POST['filter_anzahl_int'] == 20){echo ' checked="checked"';} ?> /> 20</td>
						<td class="filter_td_2"><?php echo get_text(113, 'retrun').', '.get_text(112, 'retrun');//nachname, vorname ?>:</td>
						<td class="filter_td_2">
							<select name="filter_userid_int" class="ipt_150">
								<option value="">- <?php get_text('all');//All ?> -</option>
								<?php
									$sql = $cdb->select('SELECT t1.user_id, t2.nachname, t2.vorname FROM fom_log_login t1
														LEFT JOIN fom_user t2 ON t1.user_id=t2.user_id
														GROUP BY t1.user_id ORDER BY t2.nachname ASC, t2.vorname ASC');
									while ($result = $cdb->fetch_array($sql))
									{
										if ($_POST['filter_userid_int'] == $result['user_id'])
										{
											$selected = ' selected="selected"';
										}
										else
										{
											$selected = '';
										}
										echo '<option value="'.$result['user_id'].'"'.$selected.'>'.$result['nachname'].', '.$result['vorname'].'</option>';
									}
								?>
							</select>
						</td>
						<td class="filter_td_2"><?php get_text(371);//Logindatum von ?>:</td>
						<td class="filter_td_2">
							<input type="text" name="filter_login_date_from_string" id="filter_login_date_from_string" value="<?php echo $_POST['filter_login_date_from_string']; ?>" class="ipt_150" />
							<?php echo get_img('calendar.png', get_text('calendar','return'), get_text('calendar','return'), 'image', 0, '', 'onclick="open_calendar(\'form_search\', \'filter_login_date_from_string\', \''.$GLOBALS['user_language'].'\');"');//Calendar ?>
						</td>
					</tr>
					<tr>
						<td class="filter_td_1"><input type="radio" name="filter_anzahl_int" value="40"<?php if ($_POST['filter_anzahl_int'] == 40){echo ' checked="checked"';} ?> /> 40</td>
						<td class="filter_td_2"><?php get_text(375);//Logintyp ?>:</td>
						<td class="filter_td_2">
							<select name="filter_logintype_string" class="ipt_150">
								<option value="">- <?php get_text('all');//All ?> -</option>
								<option value="local"<?php if($_POST['filter_logintype_string'] == 'local'){echo ' selected="selected"';} ?>><?php get_text(373);//Lokales Login  ?></option>
								<option value="webservice"<?php if($_POST['filter_logintype_string'] == 'webservice'){echo ' selected="selected"';} ?>><?php get_text(374);//Webservice Login  ?></option>
							</select>
						</td>
						<td class="filter_td_2"><?php get_text(372);//Logindatum bis ?>:</td>
						<td class="filter_td_2">
							<input type="text" name="filter_login_date_to_string" id="filter_login_date_to_string" value="<?php echo $_POST['filter_login_date_to_string']; ?>" class="ipt_150" />
							<?php echo get_img('calendar.png', get_text('calendar','return'), get_text('calendar','return'), 'image', 0, '', 'onclick="open_calendar(\'form_search\', \'filter_login_date_to_string\', \''.$GLOBALS['user_language'].'\');"');//Calendar ?>
						</td>
					</tr>
					<tr>
						<td class="filter_td_1"><input type="radio" name="filter_anzahl_int" value="60"<?php if ($_POST['filter_anzahl_int'] == 60){echo ' checked="checked"';} ?> /> 60</td>
						<td class="filter_td_2" colspan="3">&nbsp;</td>
						<td class="filter_td_2" align="right">
							<input type="submit" value="<?php get_text('search');//Search ?>" />&nbsp;
						</td>
					</tr>
				</table>
			</form>
			<?php
				$where_array = array();
				if ($_POST['filter_userid_int'] > 0)
				{
					$where_array[] = 't1.user_id='.$_POST['filter_userid_int'];
				}
				if (!empty($_POST['filter_login_date_from_string']))
				{
					$iso_date = $kal->free_to_iso($_POST['filter_login_date_from_string']);
					if ($iso_date != '0000-00-00')
					{
						$str_date = str_replace('-', '', $iso_date);

						$where_array[] = 'LEFT(t1.login_time, 8)>='.$str_date;
					}
				}
				if (!empty($_POST['filter_login_date_to_string']))
				{
					$iso_date = $kal->free_to_iso($_POST['filter_login_date_to_string']);
					if ($iso_date != '0000-00-00')
					{
						$str_date = str_replace('-', '', $iso_date);

						$where_array[] = 'LEFT(t1.login_time, 8)<='.$str_date;
					}
				}
				if (!empty($_POST['filter_logintype_string']))
				{
					if ($_POST['filter_logintype_string'] == 'local')
					{
						$where_array[] = "t1.login_type='local'";
					}
					else
					{
						$where_array[] = "t1.login_type='webservice'";
					}
				}
				$where = '';
				for ($i = 0; $i < count($where_array); $i++)
				{
					if ($i == 0)
					{
						$where .= 'WHERE '.$where_array[$i];
					}
					else
					{
						$where .= ' AND '.$where_array[$i];
					}
				}

				$sql = $cdb->select("SELECT COUNT(log_id ) AS count_id FROM fom_log_login t1 $where");
				$count_result = $cdb->fetch_array($sql);

				//Blaetternfunktion
				if (!isset($_GET['blseite']))
				{
					$current_page = 1;
				}
				else
				{
					$current_page = $_GET['blseite'];
				}

				$bl = page_scroll($current_page, $count_result['count_id'], $_POST['filter_anzahl_int'], $_POST,'index.php');
				echo '<br />'.$bl['txt'].'<br />';
			?>
			<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
				<tr>
					<td class="content_header_1" width="30%"><?php echo get_text(112, 'retrun').' '.get_text(113, 'retrun');//vorname nachname ?></td>
					<td class="content_header_2" width="15%"><?php get_text(367);//Login ?></td>
					<td class="content_header_2" width="15%"><?php get_text(368);//Logout ?></td>
					<td class="content_header_2" width="15%"><?php get_text(369);//Dauer ?></td>
					<td class="content_header_2" width="15%"><?php get_text(370);//IP ?></td>
					<td class="content_header_2" width="10%" align="center"><?php get_text('actions');//Actions ?></td>
				</tr>
				<?php
					$edit_logbook = false;
					if ($ac->chk('_LOGBOOK_V', 'w'))
					{
						$edit_logbook = true;
					}
					$log_id_string = '';

					$style = 1;
					$count = 0;
					$sql = $db->select('SELECT t1.log_id, t1.login_time, t1.logout_time, t1.ip, t1.login_type, t2.vorname, t2.nachname FROM fom_log_login t1
										LEFT JOIN fom_user t2 ON t1.user_id=t2.user_id
										'.$where.' ORDER BY t1.login_time DESC'.$bl['limit']);
					while($result = $db->fetch_array($sql))
					{
						if (empty($log_id_string))
						{
							$log_id_string = $result['log_id'];
						}
						else
						{
							$log_id_string .= ','.$result['log_id'];
						}
				?>
						<tr class="content_tr_<?php echo $style; ?>">
							<td>
								<?php
									if ($result['login_type'] == 'local')
									{
										echo get_img('user.png', get_text(373,'return'), get_text(373,'return'));//Lokales Login
									}
									else
									{
										echo get_img('world_link.png', get_text(374,'return'), get_text(374,'return'));//Webservice Login
									}
									echo '&nbsp;'.$result['nachname'].', '.$result['vorname'];
								?>
							</td>
							<td><?php echo $kal->win_to_time($result['login_time']); ?></td>
							<td>
								<?php
									if (!empty($result['logout_time']))
									{
										echo $kal->win_to_time($result['logout_time']);
									}
								?>
							</td>
							<td>
								<?php
									if (!empty($result['login_time']) and !empty($result['logout_time']))
									{
										$login = $kal->win_to_time($result['login_time'], 'unix');
										$logout = $kal->win_to_time($result['logout_time'], 'unix');

										$dif = $logout - $login;

										//Stundenausgabe
										if ($dif > 3600)
										{
											echo round($dif / 3600, 2).' h';
										}
										else
										{
											echo round($dif / 60, 2).' min';
										}
									}
								?>
							</td>
							<td><?php echo $result['ip']; ?></td>
							<td align="center">
								<?php
									if ($edit_logbook === true)
									{
								?>
										<form action="index.php<?php echo $gv->create_get_string('?fileinc=main'); ?>" name="log_del_<?php echo $result['log_id']; ?>" method="post" accept-charset="UTF-8">
											<input type="hidden" name="job_string" value="del_log" />
											<input type="hidden" name="logid_int" value="<?php echo $result['log_id']; ?>" />
											<?php $reload->create();?>
											<?php echo get_img('page_delete.png', get_text('del','return'), get_text('del','return'), 'image_button', 0, '', 'onclick="confirm_log_del(document.log_del_'.$result['log_id'].'.name);"');//del ?>
										</form>
								<?php
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
						$count++;
					}
					if ($count == 0)
					{
						echo '<tr class="content_tr_1"><td colspan="6">'.get_text(220, 'return').'</td></tr>';
					}
				?>
			</table>
			<?php
				if ($count > 0 and $edit_logbook === true)
				{
			?>
					<br />
					<form action="index.php<?php echo $gv->create_get_string('?fileinc=main'); ?>" name="log_del_all" method="post" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="del_log_all" />
						<input type="hidden" name="logid_string" value="<?php echo $log_id_string; ?>" />
						<?php $reload->create();?>
						<a href="javascript:confirm_log_del('log_del_all');"><?php echo get_img('delete.png', get_text('del','return'), get_text('del','return'), 'image_button');//del ?>&nbsp;<?php get_text(376);//Aktuelle Auswahl Löschen ?></a>
					</form>
			<?php
				}
			?>
		</td>
	</tr>
</table>