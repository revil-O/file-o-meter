<?php
	/**
	 * edit database title
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text('db_title');//Database title ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="db_title" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="db_title" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="25%" />
						<col width="75%" />
					</colgroup>
					<?php
						$sql = $cdb->select('SELECT fom_title FROM fom_setup WHERE setup_id=1');
						$result = $cdb->fetch_array($sql);
					?>
					<tr class="content_tr_1" valign="top">
						<td><strong><?php get_text('db_title');//Database title ?>:</strong></td>
						<td>
							<input type="text" name="db_title" class="ipt_200" value="<?php echo $result['fom_title']; ?>" />
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