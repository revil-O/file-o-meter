<?php
	/**
	 * usergroup overview
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage user_group
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(56);//Usergroup management?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="100%" align="right">
						<?php
							if ($ac->chk('_USER_G', 'w'))
							{
								echo '<a href="index.php'.$gv->create_get_string('?fileinc=add_usergroup').'">'.get_img('page_add.png').' '.get_text(119,'return').' &raquo;</a><br /><br />';//Add usergroup
							}
							else
							{
								echo '&nbsp;<br />';
							}
						?>
					</td>
				</tr>
			</table>
			<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
				<tr>
					<td class="content_header_1" width="30%"><?php get_text('usergroup');//Usergroup ?></td>
					<td class="content_header_2" width="60%"><?php get_text(121);//Number of assigned users ?></td>
					<td class="content_header_2" width="10%" align="center"><?php get_text('actions');//Actions ?></td>
				</tr>
				<?php
					$style = 1;
					$sql = $db->select("SELECT * FROM fom_user_group ORDER BY usergroup ASC");
					while($result = $db->fetch_array($sql))
					{
						$c_sql = $db->select("SELECT COUNT(user_id) AS count_id FROM fom_user_membership WHERE usergroup_id=".$result['usergroup_id']);
						$c_result = $db->fetch_array($c_sql);
				?>
						<tr class="content_tr_<?php echo $style; ?>">
							<td><?php echo $result['usergroup']; ?></td>
							<td><?php echo $c_result['count_id']; ?></td>
							<td align="center">
								<?php
									if ($ac->chk('_USER_G', 'w'))
									{
								?>
										<a href="index.php<?php echo $gv->create_get_string('?fileinc=edit_usergroup&amp;ugid_int='.$result['usergroup_id']); ?>"><?php echo get_img('page_white_key.png', get_text(312,'return'), get_text(312,'return'));//Benutzergruppenrechte auf Projektebene bearbeiten ?></a>
										<a href="index.php<?php echo $gv->create_get_string('?fileinc=edit_usergroup_folder&amp;ugid_int='.$result['usergroup_id']); ?>"><?php echo get_img('group.png', get_text(313,'return'), get_text(313,'return'));//Benutzergruppenrechte auf Verzeichnis- / Datei- / Linkebene  bearbeiten ?></a>
										<a href="index.php<?php echo $gv->create_get_string('?fileinc=edit_user_folder&amp;ugid_int='.$result['usergroup_id']); ?>"><?php echo get_img('user.png', get_text(314,'return'), get_text(314,'return'));//Benutzerrechte auf Verzeichnis- / Datei- / Linkebene  bearbeiten ?></a>
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
					}
				?>
			</table>
		</td>
	</tr>
</table>