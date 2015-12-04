<?php
	/**
	 * edit date format
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text('date_format');//Date format ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="db_title" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="date_format" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="25%" />
						<col width="75%" />
					</colgroup>
					<?php
						$sql = $cdb->select('SELECT date_format FROM fom_setup WHERE setup_id=1');
						$result = $cdb->fetch_array($sql);
					?>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('date_format');//Date format ?>:</strong></td>
						<td>
							<select name="date_format" id="date_format" class="ipt_200">
								<option value="YYYY-MM-DD"<?php if ($result['date_format'] == 'YYYY-MM-DD'){echo ' selected="selected"';} ?>><?php echo get_text(284);//YYYY-MM-DD ?></option>
								<option value="DD.MM.YYYY"<?php if ($result['date_format'] == 'DD.MM.YYYY'){echo ' selected="selected"';} ?>><?php echo get_text(285);//DD.MM.YYYY ?></option>
								<option value="MM/DD/YYYY"<?php if ($result['date_format'] == 'MM/DD/YYYY'){echo ' selected="selected"';} ?>><?php echo get_text(286);//MM/DD/YYYY ?></option>
							</select>
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