<?php
	/**
	* PDF-Header
	* this is the header of all PDF files
	* @package file-o-meter
	* @subpackage inc
	*/

	$page_width = $this->get_page_size();

	//Logoausgabe
	//ein logo linksbuendig
	if ($this->setup_array['settings']['header']['logo_type'] == 'a' and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0]))
	{
		$img_size_1 = $this->get_image_size('header_img_1');
		//$img_size_1 = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->GetX(), $this->GetY(), $img_size_1[0], $img_size_1[1]);
		$this->Ln($img_size_1[1] + 2);
	}
	//Ein Logo zentriert
	elseif ($this->setup_array['settings']['header']['logo_type'] == 'b' and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0]))
	{
		$img_size_1 = $this->get_image_size('header_img_1');
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->GetX() + ($page_width / 2) - ($img_size_1[0] / 2), $this->GetY(), $img_size_1[0], $img_size_1[1]);
		$this->Ln($img_size_1[1] + 2);
	}
	//Ein logo Rechtsbuendig
	elseif ($this->setup_array['settings']['header']['logo_type'] == 'c' and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0]))
	{
		$img_size_1 = $this->get_image_size('header_img_1');
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->GetX() + $page_width - $img_size_1[0], $this->GetY(), $img_size_1[0], $img_size_1[1]);
		$this->Ln($img_size_1[1] + 2);
	}
	//zwei logos Links- und Rechtsbuendig
	elseif ($this->setup_array['settings']['header']['logo_type'] == 'd' and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0]) and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1]))
	{
		$img_size_1 = $this->get_image_size('header_img_1');
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->GetX(), $this->GetY(), $img_size_1[0], $img_size_1[1]);

		$img_size_2 = $this->get_image_size('header_img_2');
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1], $this->GetX() + $page_width - $img_size_2[0], $this->GetY(), $img_size_2[0], $img_size_2[1]);

		$this->Ln($img_size_1[1] + 2);
	}
	//drei logos Links- Zentruert und Rechtsbuendig
	elseif ($this->setup_array['settings']['header']['logo_type'] == 'e' and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0]) and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1]) and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][2]))
	{
		$img_size_1 = $this->get_image_size('header_img_1');
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->GetX(), $this->GetY(), $img_size_1[0], $img_size_1[1]);

		$img_size_2 = $this->get_image_size('header_img_2');
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1], $this->GetX() + ($page_width / 2) - ($img_size_2[0] / 2), $this->GetY(), $img_size_2[0], $img_size_2[1]);

		$img_size_3 = $this->get_image_size('header_img_3');
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][2], $this->GetX() + $page_width - $img_size_3[0], $this->GetY(), $img_size_3[0], $img_size_3[1]);

		$this->Ln($img_size_1[1] + 2);
	}
	//vier logos gleichmaessig verteilt
	elseif ($this->setup_array['settings']['header']['logo_type'] == 'f' and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0]) and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1]) and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][2]) and file_exists($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][3]))
	{
		$img_size_1 = $this->get_image_size('header_img_1');
		$img_size_2 = $this->get_image_size('header_img_2');
		$img_size_3 = $this->get_image_size('header_img_3');
		$img_size_4 = $this->get_image_size('header_img_4');

		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->GetX(), $this->GetY(), $img_size_1[0], $img_size_1[1]);
		$x_spacer = ($page_width  - $img_size_1[0] - $img_size_2[0] - $img_size_3[0] - $img_size_4[0]) / 3;
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1], $this->GetX() + $img_size_1[0] + $x_spacer, $this->GetY(), $img_size_2[0], $img_size_2[1]);
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][2], $this->GetX() + $img_size_1[0] + $img_size_2[0] + $x_spacer + $x_spacer, $this->GetY(), $img_size_3[0], $img_size_3[1]);
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][3], $this->GetX() + $page_width - $img_size_4[0], $this->GetY(), $img_size_4[0], $img_size_4[1]);

		$this->Ln($img_size_1[1] + 2);
	}

	/*
		--------------------------------------------------------------------
		| headername														|
		--------------------------------------------------------------------
		| txt_0			txt_3		txt_5		txt_7						|
		| -----			-----		-----		-----						|
		| txt_1			txt_2		txt_6		txt_8						|
		--------------------------------------------------------------------
	 */
	if ($this->setup_array['settings']['header']['type'] == 1)
	{
		$block_width = round(($page_width - 10) / 4, 0);

		$this->SetFont($this->setup_array['font_name'], 'B', 8);

		//Dokument
		if (isset($this->setup_array['settings']['header']['txt'][0]))
		{
			$this->MultiCell($page_width, 4, '  '.$this->setup_array['settings']['header']['txt'][0], 1, 'L');
		}
		else
		{
			$this->MultiCell($page_width, 4, '', 1, 'L');
		}

		//Rahmen
		$this->Rect($this->GetX(), $this->GetY(), $page_width, 10);

		$x = $this->GetX();
		$y = $this->GetY();

		//Dateiname
		if (isset($this->setup_array['settings']['header']['txt'][1]) and isset($this->setup_array['settings']['header']['txt'][2]))
		{
			$this->Text($x + 2, $y + 1, $this->setup_array['settings']['header']['txt'][1]);
		}
		//Enthaltener Text
		if (isset($this->setup_array['settings']['header']['txt'][3]) and isset($this->setup_array['settings']['header']['txt'][4]))
		{
			$this->Text($x + 4 + $block_width, $y + 1, $this->setup_array['settings']['header']['txt'][3]);
		}
		//Dokumententyp
		if (isset($this->setup_array['settings']['header']['txt'][5]) and isset($this->setup_array['settings']['header']['txt'][6]))
		{
			$this->Text($x + 6 + $block_width + $block_width, $y + 1, $this->setup_array['settings']['header']['txt'][5]);
		}
		//Datum
		if (isset($this->setup_array['settings']['header']['txt'][7]) and isset($this->setup_array['settings']['header']['txt'][8]))
		{
			$this->Text($x + 8 + $block_width + $block_width + $block_width, $y + 1, $this->setup_array['settings']['header']['txt'][7]);
		}

		//Dateiname
		if (isset($this->setup_array['settings']['header']['txt'][1]) and isset($this->setup_array['settings']['header']['txt'][2]))
		{
			$this->Line($x + 2, $y + 5, $x + $block_width, $y + 5);
		}
		//Enthaltener Text
		if (isset($this->setup_array['settings']['header']['txt'][3]) and isset($this->setup_array['settings']['header']['txt'][4]))
		{
			$this->Line($x + 4 + $block_width, $y + 5, $x + 4 + $block_width + $block_width, $y + 5);
		}
		//Dokumententyp
		if (isset($this->setup_array['settings']['header']['txt'][5]) and isset($this->setup_array['settings']['header']['txt'][6]))
		{
			$this->Line($x + 6 + $block_width + $block_width, $y + 5, $x + 6 + $block_width + $block_width + $block_width, $y + 5);
		}
		//Datum
		if (isset($this->setup_array['settings']['header']['txt'][7]) and isset($this->setup_array['settings']['header']['txt'][8]))
		{
			$this->Line($x + 8 + $block_width + $block_width + $block_width, $y + 5, $x + 8 + $block_width + $block_width + $block_width + $block_width, $y + 5);
		}

		//Schriftgroesse aendern
		$this->SetFont($this->setup_array['font_name'], '', 6);

		//Dateiname
		if (isset($this->setup_array['settings']['header']['txt'][1]) and isset($this->setup_array['settings']['header']['txt'][2]))
		{
			$this->Text($x + 2, $y + 6, $this->setup_array['settings']['header']['txt'][2]);
		}
		//Enthaltener Text
		if (isset($this->setup_array['settings']['header']['txt'][3]) and isset($this->setup_array['settings']['header']['txt'][4]))
		{
			$this->Text($x + 4 + $block_width, $y + 6, $this->setup_array['settings']['header']['txt'][4]);
		}
		//Dokumententyp
		if (isset($this->setup_array['settings']['header']['txt'][5]) and isset($this->setup_array['settings']['header']['txt'][6]))
		{
			$this->Text($x + 6 + $block_width + $block_width, $y + 6, $this->setup_array['settings']['header']['txt'][6]);
		}
		//Datum
		if (isset($this->setup_array['settings']['header']['txt'][7]) and isset($this->setup_array['settings']['header']['txt'][8]))
		{
			$this->Text($x + 8 + $block_width + $block_width + $block_width, $y + 6, $this->setup_array['settings']['header']['txt'][8]);
		}
	}
	/*
		------------------------------------
		| headername						|
		------------------------------------
	 */
	elseif ($this->setup_array['settings']['header']['type'] == 2)
	{
		$this->SetFont($this->setup_array['font_name'], 'B', 8);

		//Dokument
		if (isset($this->setup_array['settings']['header']['txt'][0]))
		{
			$this->MultiCell($page_width, 4, '  '.$this->setup_array['settings']['header']['txt'][0], 1, 'L');
		}
		else
		{
			$this->MultiCell($page_width, 4, '', 1, 'L');
		}
	}
?>