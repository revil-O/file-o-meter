<?php
	/**
	 * edit language settings (application main language)
	 * @package file-o-meter
	 * @subpackage setup
	 */

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
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(271);//Main language ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="language" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="language" />
				<?php $reload->create();?>
				<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
					<tr>
						<td class="content_header_1" width="50%"><?php get_text('language');//Language ?></td>
						<td class="content_header_2" width="50%"><?php get_text(271);//Main language ?></td>
					</tr>
					<?php
						$count = 0;
						$style = 1;
						$sql = $cdb->select("SELECT * FROM fom_languages WHERE visible='j' ORDER BY language_name ASC");
						while($result = $cdb->fetch_array($sql))
						{
					?>
							<tr class="content_tr_<?php echo $style; ?>">
								<td><?php echo $result['language_name']; ?></td>
								<td><input type="radio" name="main_language_id_int" value="<?php echo $result['language_id']; ?>"<?php if ($main_language_id == $result['language_id']){echo ' checked="checked"';} ?> /></td>
							</tr>
					<?php
							$count++;
							if ($style == 1)
							{
								$style++;
							}
							else
							{
								$style = 1;
							}
						}
					?>
				</table>
				<div align="center"><br /><input type="submit" value="<?php get_text('save');//Speichern ?>" /></div>
			</form>
		</td>
	</tr>
</table>