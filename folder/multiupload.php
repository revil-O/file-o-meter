<?php
	/**
	 * add file
	 * @package file-o-meter
	 * @subpackage folder
	 */
	$fi = new FileInfo();
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo FOM_ABS_URL;?>inc/class/plupload/js/plupload.js"></script>
<script type="text/javascript" src="<?php echo FOM_ABS_URL;?>inc/class/plupload/js/plupload.gears.js"></script>
<script type="text/javascript" src="<?php echo FOM_ABS_URL;?>inc/class/plupload/js/plupload.html5.js"></script>
<script type="text/javascript" src="<?php echo FOM_ABS_URL;?>inc/class/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<?php
	// berechnung der max. zulässigen filesize für uploads
	if (isset($GLOBALS['setup_array']['upload_max_filesize']) && $GLOBALS['setup_array']['upload_max_filesize'] > 0)
	{
		$tmp_max_filesize = $GLOBALS['setup_array']['upload_max_filesize'] / 1024 / 1024;

		//min. 1 mb
		if ($tmp_max_filesize < 1)
		{
			$tmp_max_filesize = 1;
		}
	}
	else
	{
		//wenn keine angaben verfügbar: 2mb
		$tmp_max_filesize = 2;
	}

	//kommastellen abschneiden, nur volle mb anzeigen
	$tmp_max_filesize = number_format($tmp_max_filesize, 0, '', '');

	//cunksize anpassen
	if ($tmp_max_filesize < 2)
	{
		$tmp_chunksize = $tmp_max_filesize;
	}
	else
	{
		$tmp_chunksize = $tmp_max_filesize - 1;
	}

	$tmp_chunksize .= 'mb';
	$tmp_max_filesize .= 'mb';
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr valign="middle">
		<td class="main_table_header" width="100%"><?php get_text(344);//Multiple fileupload ?></td>
	</tr>
	<tr><td><img src="<?php echo FOM_ABS_URL.'template/'.$setup_array['template'].'/pic/_spacer.gif'; ?>" width="1" height="4" border="0" alt="" /></td></tr>
	<tr>
		<td colspan="2" class="main_table_content">
			<a href="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int'].'&amp;fileinc='); ?>">&laquo; <?php get_text('back');//zurueck ?></a><br /><br />

			<form method="post" name="form_multiupload" id="form_multiupload" action="index.php<?php echo $gv->create_get_string('?pid_int='.$_GET['pid_int'].'&amp;fid_int='.$_GET['fid_int']); ?>" accept-charset="UTF-8">
				<input type="hidden" name="job_string" value="multiupload" />
				<input type="hidden" name="pid_int" value="<?php echo $_GET['pid_int']; ?>" />
				<input type="hidden" name="fid_int" value="<?php echo $_GET['fid_int']; ?>" />
				<?php $reload->create();?>
				<div id="html5_uploader" style="width: 100%; height: 330px;"><?php get_text(345);//You browser doesn't support native upload. Try Firefox 3 or Safari 4. ?></div>
				<br style="clear: both" />
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
var file_finished_uploading_count = -1;
$(function() {
	// Setup html5 version
	$("#html5_uploader").pluploadQueue({
		// General settings
		runtimes : 'html5',
		url : 'upload.php?'+JS_GV_INDEX+'='+JS_GV_KEY,
		max_file_size : '<?php echo $tmp_max_filesize; ?>',
		chunk_size : '<?php echo $tmp_chunksize; ?>',
		unique_names : true,
		init : {
			UploadProgress: function(up, file)
			{
				// Called while a file is being uploaded
			},
			FileUploaded: function(up, file, info)
			{
				// Called when a file has finished uploading
				file_finished_uploading_count++;
			},
			StateChanged: function(up)
			{
				// Called when the state of the queue is changed
				if (up.state == plupload.STARTED)
				{
				}
				else if (up.state == plupload.STOPPED)
				{
					//Bugfix for plupload
					chk_upload();
				}
			},
			UploadProgress: function(up, file)
			{
				 // Called while a file is being uploaded
			},
			Error: function(up, args)
			{
				 // Called when a error has occured
			}
		}
	});
});

function chk_upload()
{
	var last_file_exists = false;
	for (var i = 0; i < document.form_multiupload.length; ++i)
	{
		if (document.form_multiupload.elements[i].type == 'hidden')
		{
			if (document.form_multiupload.elements[i].name == 'html5_uploader_'+file_finished_uploading_count+'_status')
			{
				if (document.form_multiupload.elements[i].value == 'done')
				{
					last_file_exists = true;
				}
			}
		}
	}

	if (last_file_exists)
	{
		document.form_multiupload.submit();
	}
	else
	{
		window.setTimeout("chk_upload()", 500);
	}
}

	$(document).ready(function(){
	$('div.plupload_header_title').html('<?php get_text(346);//Select files ?>');
	$('div.plupload_header_text').html('<?php get_text(347);//Add files to the upload queue and click the start button. ?>');
	$('div.plupload_file_name:eq(0)').html('<?php get_text(163);//Filename ?>');
	$('div.plupload_file_status:eq(0)').html('<?php get_text(348);//Status ?>');
	$('div.plupload_file_size:eq(0)').html('<?php get_text(349);//Size ?>');
	$('li.plupload_droptext').html('<?php get_text(350);//Drag files here. ?>');
	$("a:contains('Add files')").html('<?php get_text(351);//Add files ?>');
	$("a:contains('Start upload')").html('<?php get_text(352);//Start upload ?>');
});
</script>