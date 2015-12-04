<?php
	/**
	 * edit automatic backup settings
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<script type="text/javascript">
	function aktiv_backup()
	{
		if(document.backup.aktiv_int[0].checked == true)
		{
			document.backup.mail_aktiv_int.disabled = false;
			document.backup.mail_link_int.disabled = false;
			document.backup.mail_adress_string.disabled = false;

			document.backup.elements["time_array[mo]"].disabled = false;
			document.backup.elements["time_array[di]"].disabled = false;
			document.backup.elements["time_array[mi]"].disabled = false;
			document.backup.elements["time_array[do]"].disabled = false;
			document.backup.elements["time_array[fr]"].disabled = false;
			document.backup.elements["time_array[sa]"].disabled = false;
			document.backup.elements["time_array[so]"].disabled = false;
			document.backup.elements["time_array[all]"].disabled = false;
		}
		else
		{
			document.backup.mail_aktiv_int.checked = false;
			document.backup.mail_link_int.checked = false;
			document.backup.mail_adress_string.value = "";

			document.backup.mail_aktiv_int.disabled = true;
			document.backup.mail_link_int.disabled = true;
			document.backup.mail_adress_string.disabled = true;

			document.backup.elements["time_array[mo]"].value = "--";
			document.backup.elements["time_array[di]"].value = "--";
			document.backup.elements["time_array[mi]"].value = "--";
			document.backup.elements["time_array[do]"].value = "--";
			document.backup.elements["time_array[fr]"].value = "--";
			document.backup.elements["time_array[sa]"].value = "--";
			document.backup.elements["time_array[so]"].value = "--";
			document.backup.elements["time_array[all]"].value = "--";

			document.backup.elements["time_array[mo]"].disabled = true;
			document.backup.elements["time_array[di]"].disabled = true;
			document.backup.elements["time_array[mi]"].disabled = true;
			document.backup.elements["time_array[do]"].disabled = true;
			document.backup.elements["time_array[fr]"].disabled = true;
			document.backup.elements["time_array[sa]"].disabled = true;
			document.backup.elements["time_array[so]"].disabled = true;
			document.backup.elements["time_array[all]"].disabled = true;
		}
	}
	function set_time(typ)
	{
		if (typ == "all")
		{
			if (document.backup.elements["time_array[all]"].value != "--")
			{
				document.backup.elements["time_array[mo]"].value = document.backup.elements["time_array[all]"].value;
				document.backup.elements["time_array[di]"].value = document.backup.elements["time_array[all]"].value;
				document.backup.elements["time_array[mi]"].value = document.backup.elements["time_array[all]"].value;
				document.backup.elements["time_array[do]"].value = document.backup.elements["time_array[all]"].value;
				document.backup.elements["time_array[fr]"].value = document.backup.elements["time_array[all]"].value;
				document.backup.elements["time_array[sa]"].value = document.backup.elements["time_array[all]"].value;
				document.backup.elements["time_array[so]"].value = document.backup.elements["time_array[all]"].value;
			}
		}
		else
		{
			document.backup.elements["time_array[all]"].value = "--";
		}
	}
	function chk_form()
	{
		if (document.backup.aktiv_int[0].checked == true)
		{
			if (document.backup.mail_aktiv_int.checked == true && document.backup.mail_adress_string.value == "")
			{
				alert("<?php get_text(91, 'echo', 'decode_off');//Please enter an E-Mail address! ?>");
				document.backup.mail_adress_string.focus();
				return false;
			}

			if (document.backup.elements["time_array[mo]"].value == "--" && document.backup.elements["time_array[di]"].value == "--" && document.backup.elements["time_array[mi]"].value == "--" && document.backup.elements["time_array[do]"].value == "--" && document.backup.elements["time_array[fr]"].value == "--" && document.backup.elements["time_array[sa]"].value == "--" && document.backup.elements["time_array[so]"].value == "--" && document.backup.elements["time_array[all]"].value == "--")
			{
				alert("<?php get_text(92, 'echo', 'decode_off');//Please enter at least one backup date! ?>");
				document.backup.elements["time_array[all]"].focus();
				return false;
			}
		}
		return true;
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text('backup');//Backup ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="backup" onsubmit="return chk_form();" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="backup" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="25%" />
						<col width="75%" />
					</colgroup>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(60);//Automatic backup enabled ?>:</strong></td>
						<td>
							<input type="radio" name="aktiv_int" value="1" onclick="aktiv_backup();"<?php if ($setup_array['backup']['aktiv_boole'] == true){echo ' checked="checked"';} ?> /> <?php get_text('ja');//yes ?><br />
							<input type="radio" name="aktiv_int" value="0" onclick="aktiv_backup();"<?php if ($setup_array['backup']['aktiv_boole'] == false){echo ' checked="checked"';} ?> /> <?php get_text('nein');//no ?>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('email');//E-Mail ?>:</strong></td>
						<td>
							<input type="checkbox" name="mail_aktiv_int" value="1"<?php if ($setup_array['backup']['mail_aktiv_boole'] == true){echo ' checked="checked"';} ?> /> <?php get_text(61);//Send E-Mail notification ?><br />
							<input type="checkbox" name="mail_link_int" value="1"<?php if ($setup_array['backup']['mail_link_boole'] == true){echo ' checked="checked"';} ?> /> <?php get_text(62);//Send downloadlink ?><br />
							<input type="text" name="mail_adress_string" value="<?php if (!empty($setup_array['backup']['mail_adress_string'])){echo $setup_array['backup']['mail_adress_string'];} ?>" class="ipt_200" /> <?php get_text(63);//Recipient (E-Mail) ?>
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text(64);//Backup dates ?>:</strong></td>
						<td>
							<table cellpadding="2" cellspacing="0" width="100%">
								<colgroup>
									<col width="25%" />
									<col width="75%" />
								</colgroup>
							<?php
								$day_array = array('mo'		=>	get_text('montag','return'),		//Monday
													'di'	=>	get_text('dienstag','return'),		//Tuesday
													'mi'	=>	get_text('mittwoch','return'),		//Wednesday
													'do'	=>	get_text('donnerstag','return'),	//Thursday
													'fr'	=>	get_text('freitag','return'),		//Friday
													'sa'	=>	get_text('samstag','return'),		//Saturday
													'so'	=>	get_text('sonntag','return'),		//Sunday
													'all'	=>	get_text('daily','return')			//daily
													);

								foreach($day_array as $i => $v)
								{
									echo '<tr><td>'.$v.':</td>';
									echo '<td><select name="time_array['.$i.']" onchange="set_time(\''.$i.'\');">';

									for ($j = 0; $j <= 23; $j++)
									{
										if ($j == 0)
										{
											echo '<option value="--">--</option>';
										}

										if ($j < 10)
										{
											$tmp_time = '0'.$j;
										}
										else
										{
											$tmp_time = $j;
										}

										if ($setup_array['backup']['time_array'][$i] == $j && $setup_array['backup']['time_array'][$i] != '--')
										{
											echo '<option value="'.$j.'" selected="selected">'.$tmp_time.' '.get_text('uhr','return').'</option>';//o'clock
										}
										else
										{
											echo '<option value="'.$j.'">'.$tmp_time.' '.get_text('uhr','return').'</option>';//o'clock
										}
									}
									echo '</select></td></tr>'."\n";
								}
							?>
							</table>
						</td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="2" align="center">
							<br />
							<input type="submit" value="<?php get_text('save');//Speichern ?>" />
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>