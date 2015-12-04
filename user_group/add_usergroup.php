<?php
	/**
	 * add usergroup
	 * @package file-o-meter
	 * @subpackage user_group
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(119);//Add usergroup?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=usergroup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form method="post" action="index.php<?php echo $gv->create_get_string(); ?>" name="add_usergroup" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="add_usergroup" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr class="content_tr_1">
						<td width="40%"><strong><?php get_text('usergroup');//Usergroup ?>:*</strong></td>
						<td width="60%"><input type="text" name="groupnname_string" class="ipt_200" /></td>
					</tr>
					<?php
						//Andere moeglich Zugriffsrechte
						$other_access_options = $ac->get_other_access_options();

						foreach($other_access_options as $i => $v)
						{
							echo '<tr class="content_tr_1">
									<td><strong>'.$v.':</strong></td>
									<td>
										<input type="checkbox" name="other_options['.$i.'][r]" value="1" /> '.get_text('show','return').'&nbsp;
										<input type="checkbox" name="other_options['.$i.'][w]" value="1" /> '.get_text('edit','return').'
									</td>
								</tr>';
						}

						echo '<tr class="content_tr_1"><td colspan="2"><hr /></td></tr>';

						//Liste der moeglichen zugriffsrechte
						$access_list = $ac->get_access_list();

						$sql = $db->select("SELECT * FROM fom_projekte WHERE anzeigen='1' ORDER BY projekt_name ASC");
						while($result = $db->fetch_array($sql))
						{
					?>
							<tr class="content_tr_1" valign="top">
								<td><strong><?php get_text(120);//Project folder ?> <u><?php echo $result['projekt_name']; ?></u>:</strong></td>
								<td>
									<?php
										foreach($access_list as $i => $v)
										{
											echo '<input type="checkbox" name="project['.$result['projekt_id'].']['.$i.']" value="1" /> '.$v.'<br />';
										}
									?>
								</td>
							</tr>
							<tr class="content_tr_1" valign="top"><td colspan="2">&nbsp;</td></tr>
					<?php
						}
					?>
					<tr class="content_tr_1">
						<td colspan="20" align="center"><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>