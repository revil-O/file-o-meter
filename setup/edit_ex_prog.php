<?php
	/**
	 * edit external programme
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(324);//Externe Anwendungen ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="ex_prog" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="ex_prog" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<colgroup>
						<col width="15%" />
						<col width="25%" />
						<col width="60%" />
					</colgroup>
					<tr class="content_tr_1" valign="top">
						<td colspan="3">
							<?php get_text(325);//Please enter your installation paths to... ?>
							<br /><br />
						</td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong>Antiword:</strong></td>
						<td>
							<input type="text" name="ex_prog_antiword_string" class="ipt_200" value="<?php echo FOM_ABS_PFAD_EXEC_ANTIWORD; ?>" />
						</td>
						<td><a href="http://www.winfield.demon.nl/" target="_blank"><?php get_text(326,'echo','decode_on',array('website'=>'http://www.winfield.demon.nl', 'program'=>'Antiword'));//Visit [var]website[/var] for more information about [var]program[/var]. ?></a></td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong>xpdf:</strong></td>
						<td>
							<input type="text" name="ex_prog_xpdf_string" class="ipt_200" value="<?php echo FOM_ABS_PFAD_EXEC_XPDF; ?>" />
						</td>
						<td><a href="http://www.foolabs.com/xpdf/" target="_blank"><?php get_text(326,'echo','decode_on',array('website'=>'http://www.foolabs.com/xpdf', 'program'=>'xpdf'));//Visit [var]website[/var] for more information about [var]program[/var]. ?></a></td>
					</tr>
					<tr class="content_tr_1" valign="top">
						<td><strong>Ghostscript:</strong></td>
						<td>
							<input type="text" name="ex_prog_ghostscript_string" class="ipt_200" value="<?php echo FOM_ABS_PFAD_EXEC_GHOSTSCRIPT; ?>" />
						</td>
						<td><a href="http://www.ghostscript.com/" target="_blank"><?php get_text(326,'echo','decode_on',array('website'=>'http://www.ghostscript.com', 'program'=>'Ghostscript'));//Visit [var]website[/var] for more information about [var]program[/var]. ?></a></td>
					</tr>
					<tr class="content_tr_1">
						<td colspan="3" align="center">
							<br />
							<input type="submit" value="<?php get_text('save');//Speichern ?>" />
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>