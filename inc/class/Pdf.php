<?php
	/**
	 * TOM-specific extension for TCPDF
	 * @author Soeren Pieper <soeren.pieper@docemos.de>
	 * @copyright Copyright (C) 2012  docemos GmbH
	 * @package file-o-meter
	 *
	 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
	 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 * You should have received a copy of the GNU General Public License along with this program; if not, see http://www.gnu.org/licenses/.
	 */

	/**
	 * PDF class for a better output of table-based PDF files
	 *
	 * ATTENTION: this class extends the tcpdf class, so tcpdf is mandatory
	 * see /tcpdf/tcpdf.php or http://www.tcpdf.org
	 * @package file-o-meter
	 * @subpackage classes
	 */
	require_once(FOM_ABS_PFAD.'inc/class/tcpdf/config/lang/eng.php');

	class PDF extends TCPDF
	{
		private $setup_array = array();

		/**
		 * @param array $settings Array mit Seiteneinstellungswerten
		 * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li></ul>
		 * @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
		 * @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		 * @param boolean $unicode TRUE means that the input text is unicode (default = true)
		 * @param String $encoding charset encoding; default is UTF-8
		 * @param boolean $diskcache if TRUE reduce the RAM memory usage by caching temporary data on filesystem (slower).
		 * @see inc/classes/tcpdf/TCPDF#__construct()
		 */
		public function __construct($settings, $orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false)
		{
			parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);

			//Versionspruefung
			if (defined('PDF_PRODUCER'))
			{
				$this->setup_array['TCPDF_VERSION'] = str_replace('..', '', preg_replace("@[^0-9\.]@i", '', PDF_PRODUCER));
				$this->setup_array['TCPDF_VERSION'] = str_replace('.', '', substr($this->setup_array['TCPDF_VERSION'], 0, 2));

				if (!is_numeric($this->setup_array['TCPDF_VERSION']))
				{
					$this->setup_array['TCPDF_VERSION'] = 4;
				}
			}
			else
			{
				$this->setup_array['TCPDF_VERSION'] = 4;
			}

			//Fonteinstellungen
			if (isset($GLOBALS['setup_array']['pdf_font']) and !empty($GLOBALS['setup_array']['pdf_font']))
			{
				$this->SetFont($GLOBALS['setup_array']['pdf_font'], '', 7);
				$this->setup_array['font_name'] = $GLOBALS['setup_array']['pdf_font'];
			}
			else
			{
				$this->SetFont('helvetica', '', 7);
				$this->setup_array['font_name'] = 'helvetica';
			}


			//einstellungen uebernehmen
			$this->setup_array['settings'] = $settings;

			$this->calculate_page_size($orientation, $format);

			//Seitengroesse berechnen
			$page_width = $this->get_page_size();

			//max. Logogroessen im Header
			//ein logo
			if ($this->setup_array['settings']['header']['logo_type'] == 'a' or $this->setup_array['settings']['header']['logo_type'] == 'b' or $this->setup_array['settings']['header']['logo_type'] == 'c')
			{
				$this->setup_array['logo']['header_size'] = array(	'mm_w'	=> $page_width,
																	'mm_h'	=> 20);
			}
			//zwei logos
			elseif ($this->setup_array['settings']['header']['logo_type'] == 'd')
			{
				$this->setup_array['logo']['header_size'] = array(	'mm_w'	=> round($page_width / 2 -10),
																	'mm_h'	=> 20);
			}
			//drei logos
			elseif ($this->setup_array['settings']['header']['logo_type'] == 'e')
			{
				$this->setup_array['logo']['header_size'] = array(	'mm_w'	=> round($page_width / 3 - 20),
																	'mm_h'	=> 20);
			}
			//vier logos
			elseif ($this->setup_array['settings']['header']['logo_type'] == 'f')
			{
				$this->setup_array['logo']['header_size'] = array(	'mm_w'	=> round($page_width / 4 - 30),
																	'mm_h'	=> 20);
			}

			//max. Logogroessen im Footerbereich
			$this->setup_array['logo']['footer_size'] = array(	'mm_w'	=> 40,
																'mm_h'	=> 8);

			//Logoverzeichnis
			$this->setup_array['logo']['folder'] = FOM_ABS_PFAD.'files/logo/';

			//Header Logonamen
			if (isset($GLOBALS['setup_array']['pdf_logo']) and !empty($GLOBALS['setup_array']['pdf_logo']))
			{
				$this->setup_array['logo']['header_img'] = $GLOBALS['setup_array']['pdf_logo'];
			}
			else
			{
				$this->setup_array['logo']['header_img'] = array('logo_default.jpg', 'logo_default.jpg', 'logo_default.jpg', 'logo_default.jpg');
			}

			//Footer Logoname
			if (file_exists($this->setup_array['logo']['folder'].'logo_default.jpg'))
			{
				$this->setup_array['logo']['footer_img'] = array('logo_default.jpg');
			}
			else
			{
				$this->setup_array['logo']['footer_img'] = array('pdf_logo_1.jpg');
			}

			//Logogroessen berechnen
			$this->get_image_size();

			//Footereinstellungen
			$this->setup_array['footer']['file'] = FOM_ABS_PFAD.'inc/pdf_footer.php';
			$this->setup_array['footer']['link'] = 'http://www.file-o-meter.com';
			$this->setup_array['footer']['copyright'] = 'file-o-meter © Docemos GmbH 2012';

			//headereinstellungen
			$this->setup_array['header']['file'] = FOM_ABS_PFAD.'inc/pdf_header.php';

			//Allgemeine Einstellungen
			if (defined('USER_ID'))
			{
				$cdb = new MySql;

				$sql = $cdb->select('SELECT vorname, nachname FROM fom_user WHERE user_id='.USER_ID);
				$result = $cdb->fetch_array($sql);
				$this->SetCreator(mb_convert_encoding(html_entity_decode($result['vorname'].' '.$result['nachname'], ENT_QUOTES), 'UTF-8'));
			}
			else
			{
				$this->SetCreator(mb_convert_encoding('FOM', 'UTF-8'));
			}

			//Allgemeine PDF Eigenschaften
			$this->SetAuthor(mb_convert_encoding('FOM', 'UTF-8'));
			$this->SetTitle(mb_convert_encoding('FOM PDF Report', 'UTF-8'));
			$this->SetSubject(mb_convert_encoding('FOM PDF Report', 'UTF-8'));
			$this->SetKeywords(mb_convert_encoding('TCPDF, PDF, FOM, Report', 'UTF-8'));

			//Seitenraender
			$this->SetMargins($settings['page']['margin']['left'], $this->get_margin_top(), $settings['page']['margin']['right']);

			//Seitenraender nach oben bzw. unten fuer den Header bzw. Footer
			$this->SetHeaderMargin($settings['header']['margin']);
			$this->SetFooterMargin($settings['footer']['margin']);

			$this->SetAutoPageBreak(true, $settings['page']['margin']['bottom']);

			$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
			$this->setLanguageArray($GLOBALS['l']);
			$this->setImageScale(1);
		}

		/**
		 * Gibt die X Position zurueck
		 * @param float $x
		 * @return float
		 */
		public function _GetX($x = 0)
		{
			if ($this->setup_array['TCPDF_VERSION'] > 4 and $x > 0)
			{
				return $x;
			}
			else
			{
				return $this->GetX();
			}
		}

		/**
		 * Gibt die Y Position zurueck
		 * @param float $y
		 * @return float
		 */
		public function _GetY($y = 0)
		{
			if ($this->setup_array['TCPDF_VERSION'] > 4 and $y > 0)
			{
				return $y;
			}
			else
			{
				return $this->GetY();
			}
		}

		/**
		 * Ermittelt den Seitenrand (oben) fuer den Content
		 * @return float
		 */
		private function get_margin_top()
		{
			$top = 0;
			//Haedersteienrand
			$top += $this->setup_array['settings']['header']['margin'];
			//Logohoehe
			$top += $this->get_max_header_logo_height();
			//Headerhoehe
			if ($this->setup_array['settings']['header']['type'] == 1)
			{
				$top += 14;
			}
			elseif ($this->setup_array['settings']['header']['type'] == 2)
			{
				$top += 4;
			}
			//FIXME Schlechte Variante da immer alle hoehen aus der pdf_header.php nachgetragen werden muessen
			else
			{
				$top += 10;
			}
			//Zwischenraum zwischen logo und Header
			$top += 4;
			return $top;
		}

		/**
		 * Ermittelt den Seitenrand (unten) fuer den content
		 * @return float
		 */
		private function get_margin_bottom()
		{
			//FIXME Schlechte Variante da immer alle hoehen aus der pdf_footer.php nachgetragen werden muessen
			return 15;
		}

		/**
		 * Gibt die Verfuegbare groesse des Contents zurueck
		 * @param string $type
		 * @return float
		 */
		public function get_max_writable_height()
		{
			$page_size = $this->get_page_size('height_with_margin');
			//$page_size -= $this->get_margin_top();
			$page_size -= $this->setup_array['settings']['page']['margin']['bottom'];
			//$page_size -= $this->get_margin_bottom();
			return $page_size;
		}

		/**
		 * Ermittelt die Groesse der PDF Seite nach Abzug der Seitenraender in mm.
		 * @param string $orientation (Hoch- oder Querformat)
		 * @param string $format Papierformat (A5, A4, ...)
		 * @return unknown_type
		 */
		private function calculate_page_size($orientation, $format)
		{
			$format = strtoupper($format);
			$orientation = strtoupper($orientation);

			$w = 0;
			$h = 0;
			if ($format == 'A5')
			{
				$w = 148;
				$h = 210;
			}
			elseif ($format == 'A4')
			{
				$w = 210;
				$h = 297;
			}
			elseif ($format == 'A3')
			{
				$w = 297;
				$h = 420;
			}
			elseif ($format == 'A2')
			{
				$w = 420;
				$h = 594;
			}
			elseif ($format == 'A1')
			{
				$w = 594;
				$h = 841;
			}
			elseif ($format == 'A0')
			{
				$w = 841;
				$h = 1189;
			}

			//Seitenraender abziehen
			if ($w > 0 and $h > 0)
			{
				if ($orientation == 'P')
				{
					$this->setup_array['page']['width'] = $w - $this->lMargin - $this->rMargin;
					$this->setup_array['page']['width_with_margin'] = $w;

					$this->setup_array['page']['height'] = $h - $this->tMargin - $this->bMargin;
					$this->setup_array['page']['height_with_margin'] = $h;
				}
				else
				{
					$this->setup_array['page']['width'] = $h - $this->lMargin - $this->rMargin;
					$this->setup_array['page']['width_with_margin'] = $h;

					$this->setup_array['page']['height'] = $w - $this->tMargin - $this->bMargin;
					$this->setup_array['page']['height_with_margin'] = $w;
				}
			}
		}

		/**
		 * Gibt die Beschreibbare groesse der PDF Datei zurueck. Siehe calculate_page_size()
		 * @param string $type
		 * @return mixed
		 */
		public function get_page_size($type = 'width')
		{
			if (isset($this->setup_array['page'][$type]))
			{
				return $this->setup_array['page'][$type];
			}
			else
			{
				return false;
			}
		}

		/**
		 * Ermittelt die DPI zahl zu einer jpg Datei
		 * @param string $jpg
		 * @return int
		 */
		private function get_dpi($jpg)
		{
			$md5 = md5('dpi'.$jpg);

			//fuer den fall das die selbe Datei bereits ausgelesen wurde
			if (isset($this->setup_array['tmp'][$md5]))
			{
				return $this->setup_array['tmp'][$md5];
			}
			else
			{
				$fh = @fopen($jpg, 'rb');
				$header = fread($fh, 16);
				fclose($fh);
				$result = unpack('x14/ndpi', $header);

				//klappt nicht immer
				if (isset($result['dpi']) and $result['dpi'] > 0)
				{
					$this->setup_array['tmp'][$md5] = $result['dpi'];
					return $result['dpi'];
				}
				else
				{
					$this->setup_array['tmp'][$md5] = 72;
					return 72;
				}
			}
		}

		/**
		 * Gibt die hoehe des hoehsten Headerlogos zurueck
		 * @return int
		 */
		private function get_max_header_logo_height()
		{
			if ($this->setup_array['settings']['header']['logo_type'] == 'a' or $this->setup_array['settings']['header']['logo_type'] == 'b' or $this->setup_array['settings']['header']['logo_type'] == 'c')
			{
				$logo_1 = $this->get_image_size('header_img_1');
				return $logo_1[1];
			}
			//zwei logos
			elseif ($this->setup_array['settings']['header']['logo_type'] == 'd')
			{
				$logo_1 = $this->get_image_size('header_img_1');
				$logo_2 = $this->get_image_size('header_img_2');
				return max($logo_1[1], $logo_2[1]);
			}
			//drei logos
			elseif ($this->setup_array['settings']['header']['logo_type'] == 'e')
			{
				$logo_1 = $this->get_image_size('header_img_1');
				$logo_2 = $this->get_image_size('header_img_2');
				$logo_3 = $this->get_image_size('header_img_3');
				return max($logo_1[1], $logo_2[1], $logo_3[1]);
			}
			//vier logos
			elseif ($this->setup_array['settings']['header']['logo_type'] == 'f')
			{
				$logo_1 = $this->get_image_size('header_img_1');
				$logo_2 = $this->get_image_size('header_img_2');
				$logo_3 = $this->get_image_size('header_img_3');
				$logo_4 = $this->get_image_size('header_img_4');
				return max($logo_1[1], $logo_2[1], $logo_3[1], $logo_4[1]);
			}
		}

		/**
		 * Ermittelt die Logogroessen und fuehrt eine Zwischenspeicherung durch oder gibt die Logogroesse zurueck.
		 * get_image_size('all') Fuehrt eine Groessenermittlung fuer alle zu verwendenden Logos durch und wird beim Klassenstart ausgefuerhrt, die Ergenisse werden zwischengespeichert.
		 * @param string $file_type
		 * @return mixed Kann void oder ein array sein. Das Array hat die Werte [0] = Breite, [1] = Hoehe
		 */
		private function get_image_size($file_type = 'all')
		{
			//Groessen aller Logos ermittel
			if ($file_type == 'all')
			{
				//ein Logo
				if ($this->setup_array['settings']['header']['logo_type'] == 'a' or $this->setup_array['settings']['header']['logo_type'] == 'b' or $this->setup_array['settings']['header']['logo_type'] == 'c')
				{
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
				}
				//zwei logos
				elseif ($this->setup_array['settings']['header']['logo_type'] == 'd')
				{
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][1])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
				}
				//drei logos
				elseif ($this->setup_array['settings']['header']['logo_type'] == 'e')
				{
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][1])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][2])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][2], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
				}
				//vier logos
				elseif ($this->setup_array['settings']['header']['logo_type'] == 'f')
				{
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][1])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][2])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][2], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
					$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][3])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][3], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
				}

				//Footer Logo
				$this->setup_array['tmp'][md5('footer_img'.$this->setup_array['logo']['footer_img'][0])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['footer_img'][0], $this->setup_array['logo']['footer_size']['mm_w'], $this->setup_array['logo']['footer_size']['mm_h']);
			}
			//Einzelne Logos abfragen
			else
			{
				//erstes Logo
				if ($file_type == 'header_img_1')
				{
					if (isset($this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])]))
					{
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])];
					}
					else
					{
						$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][0], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][0])];
					}
				}
				//zweites Logo
				elseif ($file_type == 'header_img_2')
				{
					if (isset($this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][1])]))
					{
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][1])];
					}
					else
					{
						$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][1])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][1], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][1])];
					}
				}
				//drittes Logo
				elseif ($file_type == 'header_img_3')
				{
					if (isset($this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][2])]))
					{
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][2])];
					}
					else
					{
						$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][2])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][2], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][2])];
					}
				}
				//viertes logo
				elseif ($file_type == 'header_img_4')
				{
					if (isset($this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][3])]))
					{
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][3])];
					}
					else
					{
						$this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][3])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['header_img'][3], $this->setup_array['logo']['header_size']['mm_w'], $this->setup_array['logo']['header_size']['mm_h']);
						return $this->setup_array['tmp'][md5('header_img'.$this->setup_array['logo']['header_img'][3])];
					}
				}
				//footer logo
				elseif ($file_type == 'footer_img_1')
				{
					if (isset($this->setup_array['tmp'][md5('footer_img'.$this->setup_array['logo']['footer_img'][0])]))
					{
						return $this->setup_array['tmp'][md5('footer_img'.$this->setup_array['logo']['footer_img'][0])];
					}
					else
					{
						$this->setup_array['tmp'][md5('footer_img'.$this->setup_array['logo']['footer_img'][0])] = $this->resize_image($this->setup_array['logo']['folder'].$this->setup_array['logo']['footer_img'][0], $this->setup_array['logo']['footer_size']['mm_w'], $this->setup_array['logo']['footer_size']['mm_h']);
						return $this->setup_array['tmp'][md5('footer_img'.$this->setup_array['logo']['footer_img'][0])];
					}
				}
			}
		}

		/**
		 * Vergleicht die Original Logogroesse mit den max. Logogroessen angaben
		 * @param string $file
		 * @param int $w
		 * @param int $h
		 * @return array
		 */
		public function resize_image($file, $w, $h)
		{
			if (strtolower(substr($file, -3)) == 'jpg')
			{
				$dpi = $this->get_dpi($file);
			}
			else
			{
				$dpi = 72;
			}

			$img_size = $this->get_new_imagesize($file, 0, 0, $dpi);

			if ($img_size[0] > $w)
			{
				$img_size = $this->get_new_imagesize($file, $w, 0, $dpi);

				if ($img_size[1] > $h)
				{
					$img_size = $this->get_new_imagesize($file, 0, $h, $dpi);
				}
			}
			elseif ($img_size[1] > $h)
			{
				$img_size = $this->get_new_imagesize($file, 0, $h, $dpi);

				if ($img_size[0] > $w)
				{
					$img_size = $this->get_new_imagesize($file, $w, 0, $dpi);
				}
			}
			return $img_size;
		}

		/**
		 * Ermittelt die Groesse zu einem Bild bzw. berechnet eine neue Bildgroesse
		 * @param string $file
		 * @param int $w
		 * @param int $h
		 * @param int $dpi
		 * @return array
		 */
		public function get_new_imagesize($file, $w = 0, $h = 0, $dpi = 72)
		{
			// get image dimensions
			$imsize = @getimagesize($file);
			if ($imsize === FALSE)
			{
				// encode spaces on filename
				$file = str_replace(' ', '%20', $file);
				$imsize = @getimagesize($file);
				if ($imsize === FALSE)
				{
					$this->Error('[Image] No such file or directory in '.$file);
				}
			}
			// get original image width and height in pixels
			list($pixw, $pixh) = $imsize;
			// calculate image width and height on document
			if (($w <= 0) AND ($h <= 0))
			{
				// convert image size to document unit
				$w = $this->pixelsToUnits($pixw);
				$h = $this->pixelsToUnits($pixh);
			}
			elseif ($w <= 0)
			{
				$w = $h * $pixw / $pixh;
			}
			elseif ($h <= 0)
			{
				$h = $w * $pixh / $pixw;
			}
			elseif (($w > 0) AND ($h > 0))
			{
				// scale image dimensions proportionally to fit within the ($w, $h) box
				if ((($w * $pixh) / ($h * $pixw)) < 1)
				{
					$h = $w * $pixh / $pixw;
				}
				else
				{
					$w = $h * $pixw / $pixh;
				}
			}
			return array(round($w), round($h));
		}

		/**
		 * Berechnet Milimeterangaben fur die PDF ausgabe um.
		 * @param int $mm
		 * @param int $dpi
		 * @return int
		 */
		public function get_mm_for_pdf($mm, $dpi = 72, $float = true)
		{
			if ($float == true)
			{
				return round($mm / 25.4 * $dpi, 2);
			}
			else
			{
				return round($mm / 25.4 * $dpi, 0);
			}
		}

		/**
		 * (non-PHPdoc)
		 * @see inc/classes/tcpdf/TCPDF#Header()
		 */
		public function Header()
		{
			require (FOM_ABS_PFAD.'inc/pdf_header.php');
		}

		/**
		 * (non-PHPdoc)
		 * @see inc/classes/tcpdf/TCPDF#Footer()
		 */
		public function Footer()
		{
			require (FOM_ABS_PFAD.'inc/pdf_footer.php');
		}
	}
?>