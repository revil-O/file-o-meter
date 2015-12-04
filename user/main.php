<?php
	/**
	 * useraccount management overview (userlist)
	 * this file contains the default-content of the index.php
	 * @package file-o-meter
	 * @subpackage user
	 */
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(55);//Useraccount management ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="100%" align="right">
						<?php
							if ($ac->chk('_USER_V', 'w'))
							{
								echo '<a href="index.php'.$gv->create_get_string('?fileinc=add_user').'">'.get_img('page_add.png').' '.get_text(115, 'return').' &raquo;</a><br /><br />';//Create useraccount
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
					<td class="content_header_1" width="25%"><?php get_text('lastname');//Last name ?>, <?php get_text('firstname');//First name ?></td>
					<td class="content_header_2" width="25%"><?php get_text('email');//E-Mail ?></td>
					<td class="content_header_2" width="20%"><?php get_text('usergroup');//Usergroup ?></td>
					<td class="content_header_2" width="20%"><?php get_text(118);//Useraccount enabled ?></td>
					<td class="content_header_2" width="10%" align="center"><?php get_text('actions');//Actions ?></td>
				</tr>
				<?php
					$style = 1;
					$sql = $db->select("SELECT user_id, vorname, nachname, email, login_aktiv FROM fom_user ORDER BY login_aktiv DESC, nachname ASC, vorname ASC");
					while($result = $db->fetch_array($sql))
					{
						$tmp_memberships = '';

						$sql_membership = $db->select("SELECT t1.usergroup_id, t2.usergroup FROM fom_user_membership t1
														LEFT JOIN fom_user_group t2 ON t1.usergroup_id=t2.usergroup_id
														WHERE t1.user_id=".$result['user_id']);
						while($result_membership = $db->fetch_array($sql_membership))
						{
							if (strlen($tmp_memberships) > 0)
							{
								$tmp_memberships .= ',<br />';
							}

							$tmp_memberships .= $result_membership['usergroup'];
						}
				?>
						<tr class="content_tr_<?php echo $style; ?>" valign="top">
							<td><?php echo $result['nachname'].', '.$result['vorname']; ?></td>
							<td><?php echo $result['email']; ?></td>
							<td><?php echo $tmp_memberships; ?></td>
							<td><?php if ($result['login_aktiv'] == '1'){echo get_text('ja');/*yes*/}else{echo get_text('nein');/*no*/} ?></td>
							<td align="center">
								<?php
									if ($ac->chk('_USER_V', 'w'))
									{
								?>
										<a href="index.php<?php echo $gv->create_get_string('?fileinc=edit_user&amp;uid_int='.$result['user_id']); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a>
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