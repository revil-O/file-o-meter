<?php
	/**
	* PDF-Footer
	* this footer appears at the end of each PDF file
	* @package file-o-meter
	* @subpackage inc
	*/


	$page_height = $this->get_page_size('height');
	$page_width = $this->get_page_size();


	if ($this->setup_array['settings']['footer']['type'] == 1)
	{
		$block_width_b = round(($page_width / 2 - 6) / 3 * 1.5, 0);
		$block_width_s = round(($page_width / 2 - 6) / 3 / 1.5, 0);
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		$this->SetFont($this->setup_array['font_name'], 'B', 8);

		//Rahmen
		$this->Rect($this->GetX(), $this->GetY(), $page_width, 10);

		$x = $this->GetX();
		$y = $this->GetY();

		if ($this->setup_array['TCPDF_VERSION'] == 4)
		{
			$y_diff = 3;
		}
		else
		{
			$y_diff = 0;
		}


		//Dokument
		if (isset($this->setup_array['settings']['footer']['txt'][0]) and isset($this->setup_array['settings']['footer']['txt'][1]))
		{
			$this->Text($x + 2, $y + 1, $this->setup_array['settings']['footer']['txt'][0]);
		}
		//Datum
		if (isset($this->setup_array['settings']['footer']['txt'][2]) and isset($this->setup_array['settings']['footer']['txt'][3]))
		{
			$this->Text($x + 4 + $block_width_b, $y + 1, $this->setup_array['settings']['footer']['txt'][2]);
		}
		//Seite
		if (isset($this->setup_array['settings']['footer']['txt'][4]) and isset($this->setup_array['settings']['footer']['txt'][5]))
		{
			$this->Text($x + 6 + $block_width_b + $block_width_s, $y + 1, $this->getAliasNumPage().' '.$this->setup_array['settings']['footer']['txt'][4].' '.$this->getAliasNbPages());
		}

		//Dokument
		if (isset($this->setup_array['settings']['footer']['txt'][0]) and isset($this->setup_array['settings']['footer']['txt'][1]))
		{
			$this->Line($x + 2, $y + 5, $x + $block_width_b, $y + 5);
		}
		//Datum
		if (isset($this->setup_array['settings']['footer']['txt'][2]) and isset($this->setup_array['settings']['footer']['txt'][3]))
		{
			$this->Line($x + 4 + $block_width_b, $y + 5, $x + 4 + $block_width_b + $block_width_s, $y + 5);
		}
		//Seite
		if (isset($this->setup_array['settings']['footer']['txt'][4]) and isset($this->setup_array['settings']['footer']['txt'][5]))
		{
			$this->Line($x + 6 + $block_width_b + $block_width_s, $y + 5, $x + 6 + $block_width_b + $block_width_s + $block_width_s, $y + 5);
		}

		$this->SetFont($this->setup_array['font_name'], '', 6);

		//Dokument
		if (isset($this->setup_array['settings']['footer']['txt'][0]) and isset($this->setup_array['settings']['footer']['txt'][1]))
		{
			$this->Text($x + 2, $y + 5, $this->setup_array['settings']['footer']['txt'][1]);
		}
		//Datum
		if (isset($this->setup_array['settings']['footer']['txt'][2]) and isset($this->setup_array['settings']['footer']['txt'][3]))
		{
			$this->Text($x + 4 + $block_width_b, $y + 5, $this->setup_array['settings']['footer']['txt'][3]);
		}
		//Seite
		if (isset($this->setup_array['settings']['footer']['txt'][4]) and isset($this->setup_array['settings']['footer']['txt'][5]))
		{
			$this->Text($x + 6 + $block_width_b + $block_width_s, $y + 5, $this->setup_array['settings']['footer']['txt'][5]);
		}

		//logogroesse anpassen
		$img_size = $this->get_image_size('footer_img_1');

		//© Text
		$copy_str = $this->setup_array['footer']['copyright'];
		$copy_str_width = $this->GetStringWidth($copy_str);

		//Link
		$this->Link($x + $page_width - $img_size[0] - $copy_str_width - 10, $y, $img_size[0] + $copy_str_width + 10, 10, $this->setup_array['footer']['link']);

		$this->Text($x + $page_width - $img_size[0] - $copy_str_width - 10, $y + 5, $copy_str);

		//Image
		$this->Image($this->setup_array['logo']['folder'].$this->setup_array['logo']['footer_img'][0], $x + $page_width - $img_size[0] - 5, $y + 1, $img_size[0], $img_size[1]);
	}
?>