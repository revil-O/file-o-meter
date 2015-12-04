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
	$pdf_setup_array = array();

	$pdf_setup_array['header']['type'] = 1;
	$pdf_setup_array['header']['logo_type'] = 'a';
	$pdf_setup_array['header']['txt'][] = get_text(339, 'return', 'decode_pdf');//Suchergebnis
	if (isset($_POST['filter_file_name_string']) and !empty($_POST['filter_file_name_string']))
	{
		$pdf_setup_array['header']['txt'][] = $_POST['filter_file_name_string'];
	}
	else
	{
		$pdf_setup_array['header']['txt'][] = '-';
	}
	$pdf_setup_array['header']['txt'][] = get_text('filename', 'return', 'decode_pdf');//Filename;

	if (isset($_POST['filter_file_data_string']) and !empty($_POST['filter_file_data_string']))
	{
		$pdf_setup_array['header']['txt'][] = $_POST['filter_file_data_string'];
	}
	else
	{
		$pdf_setup_array['header']['txt'][] = '-';
	}
	$pdf_setup_array['header']['txt'][] = get_text(184, 'return', 'decode_pdf');//Contained text

	if (isset($_POST['filter_mimetyp_string']) and !empty($_POST['filter_mimetyp_string']))
	{
		$mime_type = $gt->GetFileType('', $_POST['filter_mimetyp_string'], 'array');

		if (isset($mime_type['name']))
		{
			$pdf_setup_array['header']['txt'][] = $mime_type['name'];
		}
		else
		{
			$pdf_setup_array['header']['txt'][] = '-';
		}
	}
	else
	{
		$pdf_setup_array['header']['txt'][] = '-';
	}
	$pdf_setup_array['header']['txt'][] = get_text(85, 'return', 'decode_pdf');//Document type;

	if (isset($_POST['filter_file_date_string']) and !empty($_POST['filter_file_date_string']))
	{
		if (isset($_POST['filter_file_date_type_string']) and $_POST['filter_file_date_type_string'] == 'after')
		{
			$pdf_setup_array['header']['txt'][] = get_text(340, 'return', 'decode_pdf').' '.$_POST['filter_file_date_string'];//nach dem
		}
		else
		{
			$pdf_setup_array['header']['txt'][] = get_text(341, 'return', 'decode_pdf').' '.$_POST['filter_file_date_string'];//vor dem
		}
	}
	else
	{
		$pdf_setup_array['header']['txt'][] = '-';
	}
	$pdf_setup_array['header']['txt'][] = get_text(329, 'return', 'decode_pdf');//Datum
	$pdf_setup_array['header']['margin'] = 5;

	$pdf_setup_array['footer']['type'] = 1;
	$pdf_setup_array['footer']['logo_count'] = 1;
	$pdf_setup_array['footer']['txt'][] = get_text(339, 'return', 'decode_pdf');//Suchergebnis
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
						<td width="'.$pdf->get_mm_for_pdf(197).'"><b>'.get_text('filename', 'return', 'decode_pdf')/*Filename*/.'</b></td>
						<td width="'.$pdf->get_mm_for_pdf(25).'"><b>'.get_text(188, 'return', 'decode_pdf')/*Relevance*/.'</b></td>
						<td width="'.$pdf->get_mm_for_pdf(25).'"><b>'.get_text('filesize', 'return', 'decode_pdf')/*Filesize*/.'</b></td>
						<td width="'.$pdf->get_mm_for_pdf(30).'"><b>'.get_text(165, 'return', 'decode_pdf')/*Uploaded on*/.'</b></td>
					</tr>
				</thead>';

	//Es sollte schon was zum suchen da sein
	if (!empty($_POST['filter_file_name_string']) or !empty($_POST['filter_file_data_string']) or !empty($_POST['filter_file_date_string']))
	{
		$serach_array = array();
		$serach_array['fid_int'] = $_GET['fid_int'];
		$serach_array['pid_int'] = $_GET['pid_int'];
		$serach_array['file_name'] = '';
		$serach_array['file_data'] = '';
		$serach_array['mime_typ'] = '';
		$serach_array['subfolder'] = '';

		//Dateiname
		if (!empty($_POST['filter_file_name_string']))
		{
			$serach_array['file_name'] = $_POST['filter_file_name_string'];
		}
		//Dateiinhalt
		if (!empty($_POST['filter_file_data_string']))
		{
			$serach_array['file_data'] = $_POST['filter_file_data_string'];
		}
		//Dateityp
		if (!empty($_POST['filter_mimetyp_string']))
		{
			$serach_array['mime_typ'] = $_POST['filter_mimetyp_string'];
		}
		//Unterverzeichnisse
		if (!empty($_POST['filter_subfolder_int']))
		{
			$serach_array['subfolder'] = $_POST['filter_subfolder_int'];
		}
		//Datumssuche
		if (!empty($_POST['filter_file_date_string']))
		{
			$file_date = $cal->check_iso_date($cal->format_date($_POST['filter_file_date_string'], 'ISO'));
			if (!empty($file_date) and $file_date != '0000-00-00')
			{
				$serach_array['file_date'] = $file_date;
				if ($_POST['filter_file_date_type_string'] == 'before')
				{
					$serach_array['file_date_type'] = 'before';
				}
				else
				{
					$serach_array['file_date_type'] = 'after';
				}
			}
		}

		//Suche Starten
		$search_result = $se->search($serach_array);

		$count = 0;
		$bg_color = 'E5E5E5';
		$pfad_array = array();
		foreach($search_result as $data_array)
		{
			$folder_pfad = '';
			$link_id = 0;
			$file_id = 0;
			if ($data_array['type'] == 'file')
			{
				$file_id = $data_array['id'];
				if (is_numeric($file_id) and $file_id > 0)
				{
					$sql = $db->select('SELECT * FROM fom_files WHERE file_id='.$file_id);
					$access = $ac->chk('file', 'r', $file_id);

					if (!isset($pfad_array['file'][$file_id]))
					{
						$folder_pfad = $gt->get_folder_pfad_from_file($file_id);
						$pfad_array['file'][$file_id] = $folder_pfad;
					}
					else
					{
						$folder_pfad = $pfad_array['file'][$file_id];
					}
				}
				else
				{
					$access = false;
				}
			}
			elseif ($data_array['type'] == 'link')
			{
				$link_id = $data_array['id'];
				if (is_numeric($link_id) and $link_id > 0)
				{
					$sql = $db->select('SELECT t1.*, t2.org_name, t2.save_name, t2.mime_type FROM fom_link t1
										LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
										WHERE t1.link_id='.$link_id);
					$access = $ac->chk('link', 'r', $link_id);

					if (!isset($pfad_array['link'][$link_id]))
					{
						$folder_pfad = $gt->get_folder_pfad_from_link($link_id);
						$pfad_array['link'][$link_id] = $folder_pfad;
					}
					else
					{
						$folder_pfad = $pfad_array['link'][$link_id];
					}
				}
				else
				{
					$access = false;
				}
			}

			//Leserechte Pruefen
			if ($access)
			{
				$result = $db->fetch_array($sql);
				$tbl .= '<tr style="background-color:#'.$bg_color.';color:#000000;">
							<td width="'.$pdf->get_mm_for_pdf(197).'">';

				$tbl .= $folder_pfad.'</td>';

				if ($data_array['relevanz'] == -1)
				{
					$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'">'.str_replace(FOM_ABS_URL, '../', get_img('_relevance_empty.png', '', '', '', 0, '', '', $pdf->get_mm_for_pdf(23, 72, false), 5)).'</td>';
				}
				else
				{
					$max = intval($se->search_counter['file_name'] + $se->search_counter['word'] + 10);

					$ref = round(23 * $data_array['relevanz'] / $max, 0);

					if ($ref > 23)
					{
						$ref = 23;
					}

					if ($ref > 0)
					{
						$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'">'.str_replace(FOM_ABS_URL, '../', get_img('_relevance.png', '', '', '', 0, '', '',  $pdf->get_mm_for_pdf($ref, 72, false), 5)).'</td>';
					}
					else
					{
						$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'"></td>';
					}
				}
				if (isset($result['file_size']))
				{
					$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'">'.$fi->get_html_filesize($result['file_size']).'</td>';
				}
				else
				{
					$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'"></td>';
				}
				$tbl .= '<td width="'.$pdf->get_mm_for_pdf(30).'">'.$cal->GetWinTime($result['save_time'],'date').'</td>';
				$tbl .= '</tr>';

				//Subdateien
				if (isset($data_array['sub_file']))
				{
					$sub_file_count = 0;
					foreach ($data_array['sub_file'] as $sub_file_id => $sub_file_relevanz)
					{
						//Leserechte Pruefen
						if (is_numeric($sub_file_id) and $sub_file_id > 0 and $ac->chk('file', 'r', $sub_file_id))
						{
							$sub_sql = $db->select('SELECT * FROM fom_files WHERE file_id='.$sub_file_id);
							$sub_result = $db->fetch_array($sub_sql);

							if (!isset($pfad_array['file'][$sub_result['file_id']]))
							{
								$folder_pfad = $gt->get_folder_pfad_from_file($sub_result['file_id']);
								$pfad_array['file'][$sub_result['file_id']] = $folder_pfad;
							}
							else
							{
								$folder_pfad = $pfad_array['file'][$sub_result['file_id']];
							}

							$tbl .= '<tr style="background-color:#'.$bg_color.';color:#000000;">
										<td width="'.$pdf->get_mm_for_pdf(197).'">';
							$tbl .= $folder_pfad.'</td>';

							if ($sub_file_relevanz == -1)
							{
								$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'">'.str_replace(FOM_ABS_URL, '../', get_img('_relevance_empty.png', '', '', '', 0, '', '', $pdf->get_mm_for_pdf(23, 72, false), 5)).'</td>';
							}
							else
							{
								$max = intval($se->search_counter['file_name'] + $se->search_counter['word'] + 10);

								$ref = round(23 * $sub_file_relevanz / $max, 0);

								if ($ref > 23)
								{
									$ref = 23;
								}

								if ($ref > 0)
								{
									$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'">'.str_replace(FOM_ABS_URL, '../', get_img('_relevance.png', '', '', '', 0, '', '', $pdf->get_mm_for_pdf($ref, 72, false), 5)).'</td>';
								}
								else
								{
									$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'"></td>';
								}
							}

							//$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'">'.$data_array['relevanz'].'</td>';
							$tbl .= '<td width="'.$pdf->get_mm_for_pdf(25).'">'.$fi->get_html_filesize($sub_result['file_size']).'</td>';
							$tbl .= '<td width="'.$pdf->get_mm_for_pdf(30).'">'.$cal->GetWinTime($sub_result['save_time'],'date').'</td>';
							$tbl .= '</tr>';
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
				$count++;
			}
		}
		if($count == 0)
		{
			$tbl .= '<tr style="background-color:#E5E5E5;color:#000000;">
						<td width="'.$pdf->get_mm_for_pdf(182).'" colspan="4">'.get_text('no_data', 'return', 'decode_pdf').'</td>
					</tr>';
		}

	}
	//die suchparameter sind fehlerhaft
	else
	{
		$tbl .= '<tr style="background-color:#E5E5E5;color:#000000;">
					<td width="'.$pdf->get_mm_for_pdf(182).'" colspan="4">'.get_text('no_data', 'return', 'decode_pdf').'</td>
				</tr>';
	}

	$tbl .= '</table>';
	$pdf->writeHTML($tbl, true, false, false, false, '');

	$pdf->Output('search_result.pdf', 'I');
?>