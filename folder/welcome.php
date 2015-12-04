<?php
	/**
	 * provides a list of all projects
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage project
	 */
?>
	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(320);//Welcome ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<img src="<?php echo '../template/'.$setup_array['template'].'/dms_logo.png'; ?>" alt="" width="180" height="68" border="0" style="float:right; margin-bottom:15px; margin-left:15px;" />
			<strong><?php get_text(321);//Willkommen in File-O-Meter ?></strong><br /><br />
			<?php get_text(322);//Das OpenSource DMS File-O-Meter ist ein... ?>
			<br /><br />
			<?php get_text(323);//Sollten Sie im Menue Ihre gewuenschten Projektordner... ?>
		</td>
	</tr>
</table>