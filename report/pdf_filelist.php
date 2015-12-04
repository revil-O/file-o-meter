<?php
	/**
	 * PDF output
	 * @package file-o-meter
	 * @subpackage report
	 */
	$show_header = 'n';
	/**
	 * include-sammlung einbinden
	 */
	require_once('../inc/include.php');

	$gt = new Tree;
	$se = new Search;
	//FileInfo Klasse
	$fi = new FileInfo();
	$cal = new Calendar();
	//SubFile Klasse
	$sf = new SubFile();
	$vh = new VersionHistory();

	$folder_search = false;
	if (isset($_GET['pid_int']) and $_GET['pid_int'] > 0 and isset($_GET['fid_int']) and $_GET['fid_int'] > 0)
	{
		$gt->get_folder($_GET['pid_int'], $_GET['fid_int']);
		$folder_search = true;
	}
	elseif (isset($_GET['pid_int']) and $_GET['pid_int'] > 0)
	{
		$gt->get_folder($_GET['pid_int']);
		$folder_search = true;
	}

	if ($folder_search == true)
	{
		$folder_result = $gt->tmp_array;

		$pdf_setup_array['header']['type'] = 2;
		$pdf_setup_array['header']['logo_type'] = 'a';

		$pdf_title_pfad = '';
		if (isset($_GET['fid_int']) and $_GET['fid_int'] > 0)
		{
			$pdf_title_pfad = $gt->get_folder_pfad_from_folder($_GET['fid_int']);
		}
		elseif (isset($_GET['pid_int']) and $_GET['pid_int'] > 0)
		{
			$pdf_title_pfad = $gt->get_folder_pfad_from_project($_GET['pid_int']);
		}

		$pdf_title_pfad = mb_convert_encoding(html_entity_decode($pdf_title_pfad, ENT_QUOTES), 'UTF-8');

		$pdf_setup_array['header']['txt'][] = str_replace('[path]', $pdf_title_pfad, get_text(328, 'return', 'decode_pdf'));//Dateiliste von ""

		$pdf_setup_array['header']['margin'] = 5;

		$pdf_setup_array['footer']['type'] = 1;
		$pdf_setup_array['footer']['logo_count'] = 1;
		$pdf_setup_array['footer']['txt'][] = get_text(330, 'return', 'decode_pdf');//Dateiliste
		$pdf_setup_array['footer']['txt'][] = get_text(331, 'return', 'decode_pdf');//Dokument
		$pdf_setup_array['footer']['txt'][] = mb_convert_encoding($cal->format_date(date('Y-m-d'), 'FREE'), 'UTF-8');
		$pdf_setup_array['footer']['txt'][] = get_text(329, 'return', 'decode_pdf');//Datum
		$pdf_setup_array['footer']['txt'][] = get_text(234, 'return', 'decode_pdf');//von
		$pdf_setup_array['footer']['txt'][] = get_text(233, 'return', 'decode_pdf');//Seite

		$pdf_setup_array['footer']['margin'] = 10;

		$pdf_setup_array['page']['margin'] = array(	'left'	=> 10,
													'right'	=> 10,
													'bottom'=> 25);

		// create new PDF document
		$pdf = new Pdf($pdf_setup_array, 'L');

		// add a page
		$pdf->AddPage();

		//Tabellenkopf
		$tbl = '<table border="1" cellpadding="2" cellspacing="0">
					<thead>
						<tr style="background-color:#FFFFFF;color:#000000;">
							<td width="'.$pdf->get_mm_for_pdf(75).'"><b>'.get_text('filename', 'return', 'decode_pdf')/*Filename*/.'</b></td>
							<td width="'.$pdf->get_mm_for_pdf(25).'"><b>'.get_text('filesize', 'return', 'decode_pdf')/*Filesize*/.'</b></td>
							<td width="'.$pdf->get_mm_for_pdf(30).'"><b>'.get_text(165, 'return', 'decode_pdf')/*Uploaded on*/.'</b></td>
							<td width="'.$pdf->get_mm_for_pdf(30).'"><b>'.get_text(332, 'return', 'decode_pdf')/*Erstellt von*/.'</b></td>
							<td width="'.$pdf->get_mm_for_pdf(20).'"><b>'.get_text(140, 'return', 'decode_pdf')/*Version*/.'</b></td>
							<td width="'.$pdf->get_mm_for_pdf(97).'"><b>'.get_text(149, 'return', 'decode_pdf')/*Beschreibung*/.'</b></td>
						</tr>
					</thead>';

		$bg_color = 'E5E5E5';

		foreach ($folder_result as $project_folder)
		{
			foreach ($project_folder as $folder_id => $folder_data)
			{
				$count = 0;
				$tbl .= '<tr style="background-color:#'.$bg_color.';color:#000000;">
							<td width="'.$pdf->get_mm_for_pdf(277).'" colspan="6"><b>'.$gt->get_folder_pfad_from_folder($folder_id).'</b></td>
						</tr>';

				if ($bg_color == 'E5E5E5')
				{
					$bg_color = 'FFFFFF';
				}
				else
				{
					$bg_color = 'E5E5E5';
				}

				$sql = $cdb->select("(SELECT file_id, user_id, 0 AS link_id, org_name AS name, 0 AS link, save_name, mime_type, file_size, save_time, bemerkungen, 0 AS link_type FROM `fom_files` WHERE folder_id=$folder_id AND anzeigen='1' AND file_type='PRIMARY')
									UNION
									(SELECT file_id, user_id, link_id, name, link, 0 AS save_name, 0 AS mime_type, 0 AS file_size, save_time, bemerkungen, link_type FROM fom_link WHERE folder_id=$folder_id AND anzeigen='1')
									ORDER BY name ASC");
				while($result = $cdb->fetch_array($sql))
				{
					//ersteller auslesen
					$user_sql = $cdb->select('SELECT vorname, nachname FROM fom_user WHERE user_id='.$result['user_id']);
					$user_result = $cdb->fetch_array($user_sql);

					//Dateien ausgeben
					if ($result['link_id'] == 0)
					{
						//Leserechte Pruefen
						if ($ac->chk('file', 'r', $result['file_id']))
						{
							$count++;
							$sub_file_exists = $sf->sub_file_exists($result['file_id']);
							$tbl .= '<tr style="background-color:#'.$bg_color.';color:#000000;">
										<td width="'.$pdf->get_mm_for_pdf(75).'">'.$result['name'].'</td>
										<td width="'.$pdf->get_mm_for_pdf(25).'">'.$fi->get_html_filesize($result['file_size']).'</td>
										<td width="'.$pdf->get_mm_for_pdf(30).'">'.$cal->GetWinTime($result['save_time'],'date').'</td>
										<td width="'.$pdf->get_mm_for_pdf(30).'">'.mb_convert_encoding($user_result['nachname'].', '.$user_result['vorname'] ,'UTF-8').'</td>
										<td width="'.$pdf->get_mm_for_pdf(20).'">'.$vh->get_version_number($result['file_id']).'</td>
										<td width="'.$pdf->get_mm_for_pdf(97).'">'.html_entity_decode($result['bemerkungen'], ENT_QUOTES, 'UTF-8').'</td>
									</tr>';

							//Subfile existiert
							if ($sub_file_exists === true)
							{
								$sub_sql = $cdb->select('SELECT t2.*, t3.vorname, t3.nachname FROM fom_sub_files t1
														LEFT JOIN fom_files t2 ON t1.subfile_id=t2.file_id
														LEFT JOIN fom_user t3 ON t2.user_id=t3.user_id
														WHERE t1.file_id='.$result['file_id']." AND t2.anzeigen='1'");
								while ($sub_result = $cdb->fetch_array($sub_sql))
								{
									//Leserechte Pruefen
									if ($ac->chk('file', 'r', $sub_result['file_id']))
									{
										$count++;
										$tbl .= '<tr style="background-color:#'.$bg_color.';color:#000000;">
													<td width="'.$pdf->get_mm_for_pdf(75).'">&nbsp;&nbsp;&nbsp;&nbsp;'.$sub_result['org_name'].'</td>
													<td width="'.$pdf->get_mm_for_pdf(25).'">'.$fi->get_html_filesize($sub_result['file_size']).'</td>
													<td width="'.$pdf->get_mm_for_pdf(30).'">'.$cal->GetWinTime($sub_result['save_time'],'date').'</td>
													<td width="'.$pdf->get_mm_for_pdf(30).'">'.mb_convert_encoding($sub_result['nachname'].', '.$sub_result['vorname'] ,'UTF-8').'</td>
													<td width="'.$pdf->get_mm_for_pdf(20).'">'.$vh->get_version_number($sub_result['file_id']).'</td>
													<td width="'.$pdf->get_mm_for_pdf(97).'">'.html_entity_decode($sub_result['bemerkungen'], ENT_QUOTES, 'UTF-8').'</td>
												</tr>';
									}
								}
							}

							if ($bg_color == 'E5E5E5')
							{
								$bg_color = 'FFFFFF';
							}
							else
							{
								$bg_color = 'E5E5E5';
							}
						}
					}
					//Links ausgeben
					elseif (isset($result['link_id']))
					{
						$link_acces = false;

						//interner link
						if ($result['link_type'] == 'INTERNAL')
						{
							$link_acces = $ac->chk('link', 'r', $result['link_id']);

							$sub_sql = $cdb->select('SELECT * FROM fom_files WHERE file_id='.$result['file_id']);
							$sub_result = $cdb->fetch_array($sub_sql);
						}
						//externer link
						else
						{
							$link_acces = $ac->chk('link', 'r', $result['link_id']);
							$result['file_id'] = 0;
						}

						//Leserechte Pruefen
						if ($link_acces)
						{
							$count++;
							if ($result['link_type'] == 'INTERNAL')
							{
								$name_string = $sub_result['org_name'];
							}
							else
							{
								$name_string = $result['link'];
							}

							if ($result['link_type'] == 'INTERNAL')
							{
								$bemerkungen_string = $sub_result['bemerkungen'];
							}
							else
							{
								$bemerkungen_string = $result['bemerkungen'];
							}




							$tbl .= '<tr style="background-color:#'.$bg_color.';color:#000000;">
										<td width="'.$pdf->get_mm_for_pdf(75).'">'.$name_string.'</td>
										<td width="'.$pdf->get_mm_for_pdf(25).'">'.$fi->get_html_filesize($result['file_size']).'</td>
										<td width="'.$pdf->get_mm_for_pdf(30).'">'.$cal->GetWinTime($result['save_time'],'date').'</td>
										<td width="'.$pdf->get_mm_for_pdf(30).'">'.mb_convert_encoding($user_result['nachname'].', '.$user_result['vorname'] ,'UTF-8').'</td>
										<td width="'.$pdf->get_mm_for_pdf(20).'">-</td>
										<td width="'.$pdf->get_mm_for_pdf(97).'">'.html_entity_decode($bemerkungen_string, ENT_QUOTES, 'UTF-8').'</td>
									</tr>';
							if ($bg_color == 'E5E5E5')
							{
								$bg_color = 'FFFFFF';
							}
							else
							{
								$bg_color = 'E5E5E5';
							}
						}
					}
				}
				if ($count == 0)
				{
					$tbl .= '<tr style="background-color:#'.$bg_color.';color:#000000;">
								<td width="'.$pdf->get_mm_for_pdf(277).'" colspan="6">'.get_text('no_data', 'return', 'decode_pdf').'</td>
							</tr>';
					if ($bg_color == 'E5E5E5')
					{
						$bg_color = 'FFFFFF';
					}
					else
					{
						$bg_color = 'E5E5E5';
					}
				}
			}
		}

		$tbl .= '</table>';
		$pdf->writeHTML($tbl, true, false, false, false, '');

		$pdf->Output('filelist.pdf', 'I');
	}
?>