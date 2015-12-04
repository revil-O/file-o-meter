<?php
	/**
	 * document type overview and add/edit forms
	 * @package file-o-meter
	 * @subpackage setup
	 */
?>
<script type="text/javascript">
	function chk_form()
	{
		if (document.document_type.document_type_name_string.value == "")
		{
			alert("<?php get_text(90, 'echo', 'decode_off');//Please enter a document type name! ?>");
			document.document_type.document_type_name_string.focus();
			return false;
		}
		return true;
	}
</script>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(82);//Dokumententypen ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?fileinc=setup'); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />
			<?php
				//Neuen Dokumententyp anlegen
				if (!isset($_GET['dtid_int']))
				{
			?>
					<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="document_type" onsubmit="return chk_form();" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="add_document_type" />
						<?php $reload->create();?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="25%" />
								<col width="75%" />
							</colgroup>
							<tr class="content_tr_1" valign="top">
								<td colspan="2"><strong><u><?php get_text(89);//Add document type ?>:</u></strong></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text(85);//Document type ?>:</strong></td>
								<td><input type="text" name="document_type_name_string" class="ipt_200" /></td>
							</tr>
							<tr class="content_tr_1">
								<td colspan="2" align="center"><br /><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
							</tr>
						</table>
					</form>
			<?php
				}
				//Existierenden Bearbeiten
				else
				{
					$sql = $cdb->select('SELECT * FROM fom_document_type WHERE document_type_id='.$_GET['dtid_int']);
					$result = $cdb->fetch_array($sql);

			?>
					<form action="index.php<?php echo $gv->create_get_string(); ?>" method="post" name="document_type" onsubmit="return chk_form();" accept-charset="UTF-8">
						<input type="hidden" name="job_string" value="edit_document_type" />
						<input type="hidden" name="dtid_int" value="<?php echo $result['document_type_id']; ?>" />
						<?php $reload->create();?>
						<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
							<colgroup>
								<col width="25%" />
								<col width="75%" />
							</colgroup>
							<tr class="content_tr_1" valign="top">
								<td colspan="2"><strong><u><?php get_text(88);//Edit document type ?>:</u></strong></td>
							</tr>
							<tr class="content_tr_1">
								<td><strong><?php get_text(85);//Document type ?>:</strong></td>
								<td><input type="text" name="document_type_name_string" value="<?php echo $result['document_type']; ?>" class="ipt_200" /></td>
							</tr>
							<tr class="content_tr_1">
								<td colspan="2" align="center"><br /><input type="submit" value="<?php get_text('save');//Speichern ?>" /></td>
							</tr>
						</table>
					</form>
			<?php
				}
			?>
			<br />
			<table cellpadding="2" cellspacing="0" border="0" width="100%" class="content_table">
				<tr>
					<td class="content_header_1" width="90%"><?php get_text(85);//Document type ?></td>
					<td class="content_header_2" width="10%" align="center"><?php get_text('actions');//Actions ?></td>
				</tr>
				<?php
					$count = 0;
					$style = 1;
					$sql = $cdb->select('SELECT * FROM fom_document_type ORDER BY document_type ASC');
					while($result = $cdb->fetch_array($sql))
					{
				?>
						<tr class="content_tr_<?php echo $style; ?>">
							<td><?php echo $result['document_type']; ?></td>
							<td align="center"><a href="index.php<?php echo $gv->create_get_string('?dtid_int='.$result['document_type_id']); ?>"><?php echo get_img('page_edit.png', get_text('edit','return'), get_text('edit','return'));//edit ?></a></td>
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
					if ($count == 0)
					{
						echo '<tr><td colspan="2">'.get_text('no_data','return').'</td></tr>';//No entries found!
					}
				?>
			</table>
		</td>
	</tr>
</table>