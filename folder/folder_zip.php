<?php
	if (isset($_POST['pid_int']) and $_POST['pid_int'] > 0 and isset($_POST['fid_int']) and $_POST['fid_int'] > 0)
	{
		ini_set('memory_limit', -1);
		set_time_limit(0);

		$show_header = 'n';
		require_once('../inc/include.php');

		if ($ac->chk('project', 'r', $_POST['pid_int']))
		{
			$sql = $cdb->select('SELECT * FROM fom_folder WHERE folder_id='.$_POST['fid_int']);
			$result = $cdb->fetch_array($sql);

			$save_pfad = FOM_ABS_PFAD.'files/tmp/'.USER_ID.'_'.time().'/';
			$ex_setup = array('del_exists_folder_int'	=> 0,
							'version_string'			=> 'current');

			$ep = new Export();

			$ep->setup_array['abs_pfad'] = $save_pfad;
			$ep->setup_array['abs_pfad_len'] = strlen($save_pfad);

			@mkdir($save_pfad, 0774);

			if (file_exists($save_pfad))
			{
				$ep->export_data($_POST['fid_int'], $_POST['pid_int'], $ex_setup);

				if (file_exists($save_pfad.$result['folder_name'].'/'))
				{
					$zip = new PclZip($save_pfad.$result['folder_name'].'.zip');
					$zip_file = $zip->create($save_pfad.$result['folder_name'].'/', PCLZIP_OPT_REMOVE_PATH, $save_pfad, PCLZIP_OPT_NO_COMPRESSION);

					if (file_exists($save_pfad.$result['folder_name'].'.zip'))
					{
						header("Content-Type: application/octetstream");
						header('Content-Disposition: attachment; filename="'.$result['folder_name'].'.zip"');
						header("Content-Transfer-Encoding: binary");
						header("Cache-Control: post-check=0, pre-check=0");
						header("Content-Length: {".filesize($save_pfad.$result['folder_name'].'.zip')."}");
						readfile($save_pfad.$result['folder_name'].'.zip');
					}
					else
					{
						get_text(249);//The file could not be found!
					}
				}
				else
				{
					get_text('error');//An error has occurred!
				}
			}
			else
			{
				get_text('error');//An error has occurred!
			}
		}
		else
		{
			get_text('error');//An error has occurred!
		}
	}
	else
	{
		get_text('error');//An error has occurred!
	}
?>