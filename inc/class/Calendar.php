<?php
	/**
	 * calendar-class
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * calendar-class
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Calendar
	{
		/**
		* Speichert die TEXTIDs bzw. TEXTKEYs aller verwendeten Texte
		* @var array
		*/
		public $txt = array(
							'januar',
							'februar',
							'maerz',
							'april',
							'mai',
							'juni',
							'juli',
							'august',
							'september',
							'oktober',
							'november',
							'dezember',
							'kw',
							'kalenderwoche',
							'mo',
							'montag',
							'di',
							'dienstag',
							'mi',
							'mittwoch',
							'do',
							'donnerstag',
							'fr',
							'freitag',
							'sa',
							'samstag',
							'so',
							'sonntag',
							'heute'
						);
		/**
		* Speichert den User Timestamp
		* @var int
		*/
		public $timestamp;
		/**
		* Speichert Zeitzone zur GMT in sek. -43200 - 43200
		* @var int
		*/
		public $time_zone_user = '3600'; //zeitzone in sek von gmt des users
		/**
		* Speichert die Tage an denen immer Feiertag ist 1= Montag 7 = Sonntag
		* @var array
		*/
		public $holiday_day_number = array(6,7);
		/**
		* Speichert das Datum im ISO Format von Feiertagen
		* @var array
		*/
		public $holiday_date = array();//iso datum YYYY-MM-DD
		/**
		* Gibt das Datums Ausgabeformat an
		* @var string
		*/
		//var $date_format = 'DD/MM/YYYY';
		public $date_format = FOM_DATE_FORMAT;

		/**
		* Diese Funktion errechnet den aktuellen Usertimestamp.
		* @return void
		* @function
		*/
		public function calendar()
		{
			$time_server = time();
			$time_zone_server = date("Z",time());

			$gmt_time = $time_server - $time_zone_server;
			$this->timestamp = $gmt_time + $this->time_zone_user;
		}
		/**
		* Diese Funktion erstellt ein array mit allen relevanten Monatsinformationen
		* @param string $f_date ISO Datumsformat YYYY-MM-DD
		* Rueckgabewert: Ist ein Array
		* Array
		* (
		*	enthaelt alle Wochen des jeweiligen monats
		*	[0] => Array
		*		(
		*			[0] => 18
		*			[1] => 19
		*			[2] => 20
		*			[3] => 21
		*			[4] => 22
		*		)
		*	enthaelt die jeweiligen Tage des Monats
		*	[1] => Array
		*		(
		*			[1] => Array
		*				(
		*					[year] => 2006
		*					[month] => 05
		*					[day] => 1
		*					[free_date] => 01/05/2006
		*					[iso_date] => 2006-05-01
		*					[day_number] => 1
		*					[week_number] => 18
		*					[today] => n
		*					[holiday] => n
		*					[style] => day
		*					[aktiv_style] => day_aktiv
		*				)
		*			[2] => Array
		*				(
		*					[year] => 2006
		*					[month] => 05
		*					[day] => 2
		*					[free_date] => 02/05/2006
		*					[iso_date] => 2006-05-02
		*					[day_number] => 2
		*					[week_number] => 18
		*					[today] => n
		*					[holiday] => n
		*					[style] => day
		*					[aktiv_style] => day_aktiv
		*				)
		* 				...
		* @return array
		* @function
		*/
		public function month_detail($f_date)
		{
			//Datum teilen
			$date = explode("-",$f_date);
			//timestamp des ersten Tages des Monats finden
			$first_timestamp = mktime(1,0,0,$date[1],1,$date[0]);
			//anzahl der Tage des Monats ermitteln
			$days_of_month = date("t",$first_timestamp);
			//aktuelles Datum im ISO Format finden
			$current_date = $this->current_date();

			//speichert alle tagesrelevanten daten
			$day_detail = array();
			//Speichert die Wochen im jeweiligen Monat
			$month_detail = array();
			$timestamp_of_day = $first_timestamp;
			//durchlauf fuer jeden tag des Monats
			for($i=1;$i<=$days_of_month;$i++)
			{
				//Tagesnummer speichern
				$day_number = date("w",$timestamp_of_day);
				if($day_number == 0)
				{
					//fuer sonntag anstelle der 0 die 7 vergeben
					$day_number = 7;
				}
				//Wochennummer speichern
				$week_number = date("W",$timestamp_of_day);
				if(!in_array($week_number,$month_detail))
				{
					array_push($month_detail,$week_number);
				}
				//Tag des Monats, 2-stellig mit fuehrender Null
				$day = date("d",$timestamp_of_day);
				//Tag des Monats ohne fuehrende Nullen
				$day2 = date("j",$timestamp_of_day);

				//Pruefen ob der Tag Heute ist
				if($current_date == $date[0].'-'.$date[1].'-'.$day)
				{
					$today = 'j';
				}
				else
				{
					$today = 'n';
				}
				//Pruefen ob der Tag ein Feiertag ist
				if($this->check_holiday($date[0].'-'.$date[1].'-'.$day,$day_number))
				{
					$holiday = 'j';
				}
				else
				{
					$holiday = 'n';
				}
				//Datum in Freies Format aendern
				$free_date = $this->format_date($date[0].'-'.$date[1].'-'.$day,'FREE');
				//Style vergeben
				$style = $this->switch_style($today,$holiday);

				//Tagesrelevante Daten in Array Speichern
				$day_detail[$i] = array('year'=>$date[0],'month'=>$date[1],'day'=>$day2,'free_date'=>$free_date,'iso_date'=>$date[0].'-'.$date[1].'-'.$day,'day_number'=>$day_number,'week_number'=>$week_number,'today'=>$today,'holiday'=>$holiday,'style'=>$style[0],'aktiv_style'=>$style[1]);

				//Tagestimestamp um 24h erhoehen
				$timestamp_of_day += 86400;
				//$return = array($month_detail,$day_detail);
			}
			return array($month_detail,$day_detail);
		}
		/**
		* Wandelt das Datumsformat ISO in FREE bzw. FREE in ISO
		* @param string $f_date Datum im ISO bzw. FREE Format
		* @param string $f_format FREE=Wandelt $f_date(ISO) in Free Format. ISO= Wandelt  $f_date(FREE) in ISO Format
		* @return string
		* @function
		*/
		public function format_date($f_date,$f_format)
		{
			//datum von FREE Format in ISO Format wandeln
			if($f_format == 'ISO')
			{
				if(!empty($f_date))
				{
					//format str laenge festellen
					$format_len = strlen(trim($this->date_format));

					//position der tage monate und jahre feststellen
					for($i=0;$i<$format_len;$i++)
					{
						$form_str = $this->date_format;
						$date_str = $form_str{$i};

						if($date_str == 'D')
						{
							$day_pos[] = $i;
						}
						elseif($date_str == 'M')
						{
							$month_pos[] = $i;
						}
						elseif($date_str == 'Y')
						{
							$year_pos[] = $i;
						}
					}
					//tag festetellen
					$day = '';
					foreach($day_pos as $v)
					{
						$day .= $f_date{$v};
					}
					//monat feststellen
					$month = '';
					foreach($month_pos as $v)
					{
						$month .= $f_date{$v};
					}
					//jahr feststellen
					$year = '';
					foreach($year_pos as $v)
					{
						$year .= $f_date{$v};
					}
					//Achtung bei zweistelligen Jahresangaben
					if(strlen($year) == '2')
					{
						$year = '20'.$year;
					}

					$newdate = $year.'-'.$month.'-'.$day;
					if($newdate == '--')
					{
						$newdate = '0000-00-00';
					}
					return $newdate;
				}
				else
				{
					return '0000-00-00';
				}
			}
			//Datum von ISO in FREE Format wandeln
			elseif($f_format == 'FREE')
			{
				if($f_date != '0000-00-00' and !empty($f_date))
				{
					$date_iso = explode("-",$f_date);
					$date_format = $this->date_format;
					if(substr_count($date_format,'Y') == 2)
					{
						$date_iso[0] = substr($date_iso[0], 2, 2);
						$date_format = str_replace('YY',$date_iso[0],$date_format);
					}
					else
					{
						$date_format = str_replace('YYYY',$date_iso[0],$date_format);
					}

					$date_format = str_replace('MM',$date_iso[1],$date_format);
					$date_format = str_replace('DD',$date_iso[2],$date_format);
					return $date_format;
				}
				else
				{
					return '';
				}
			}
		}
		/**
		* Prueft ob der angegebene Tag ein Feiertag ist
		* @param string $date ISO Datum YYYY-MM-DD
		* @param int $day_numer Tagesnummer 1= Montag 7 = Sonntag
		* @return bool
		* @function
		*/
		public function check_holiday($date,$day_numer)
		{
			if(in_array($date,$this->holiday_date) or in_array($day_numer,$this->holiday_day_number))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		/**
		* Prueft ein ISO Datumsformat auf Gueltigkeit
		* @param string $date ISO Datum
		* @return string
		* @function
		*/
		public function check_iso_date($date)
		{
			if(strlen($date) == 10)
			{
				$error = 'n';

				$split_date = explode('-',$date);
				if(!is_numeric($split_date[0]) or $split_date[0] < 1979 or $split_date[0] > 2037)
				{
					$error = 'j';
				}

				if($split_date[1] < 1 or $split_date[1] > 12)
				{
					$error = 'j';
				}

				if($split_date[2] < 1 or $split_date[2] > 31)
				{
					$error = 'j';
				}

				if($error == 'n')
				{
					return $date;
				}
				else
				{
					return '0000-00-00';
				}
			}
			else
			{
				return '0000-00-00';
			}
		}
		/**
		* Style vergabe fuer die jeweiligen Tage bzw. Wochenspalten
		* @param string $today j = Heute, bei $show_day=j nummerischer tag der Woche 1= Monatg 7= Sonntag
		* @param string $holiday j = Feiertag,
		* @param string $show_day j = Style fuer Wochenspalten,
		* @return mixed
		* @function
		*/
		public function switch_style($today,$holiday,$show_day='')
		{
			//Style ausgabe fuer die Kalendertage 1 -31
			if($show_day == '')
			{
				if($today == 'j')
				{
					$return = array('day_current','day_current');
				}
				elseif($holiday == 'j')
				{
					$return = array('day_red','day_aktiv_red');
				}
				else
				{
					$return = array('day','day_aktiv');
				}
				return $return;
			}
			//Styleausgabe fuer die Wochenspalten
			else
			{
				if($this->check_holiday('',$today))
				{
					if($today == 7)
					{
						echo 'name_of_day_red_2';
					}
					else
					{
						echo 'name_of_day_red';
					}
				}
				else
				{
					if($today == 7)
					{
						echo 'name_of_day_2';
					}
					else
					{
						echo 'name_of_day';
					}
				}
			}
		}
		/**
		* $_GET Array in String wandeln
		* @param array $get
		* @return string
		* @function
		*/
		public function get_to_string($get)
		{
			$string = '';
			if(is_array($get))
			{
				$for_count = 0;
				foreach($get as $i => $v)
				{
					if($for_count == 0)
					{
						$trenner = '?';
					}
					else
					{
						$trenner = '&amp;';
					}
					$string .= $trenner.$i.'='.$v;
					$for_count++;
				}
			}
			return $string;
		}
		/**
		* Ausgabe der <option></option> fuer <select name="month">
		* @param string $date Datum im ISO Format
		* @return string
		* @function
		*/
		public function select_month($date)
		{
			$date = explode("-",$date);
			for($i=0;$i<12;$i++)
			{
				if($date[1] == $i+1)
				{
					$selected = ' selected';
				}
				else
				{
					$selected = '';
				}
				$i2 = $i+1;
				echo '<option value="'.$i2.'"'.$selected.'>'.$this->lng($i).'</option>';
			}
		}
		/**
		* Ausgabe der <option></option> fuer <select name="year">
		* @param string $date Datum im ISO Format
		* @return string
		* @function
		*/
		public function select_year($date)
		{
			$y = array();
			$date = explode("-",$date);
			if($date[0] >= 1981 and $date[0] <= 2035)
			{
				$y[] = $date[0]-2;
				$y[] = $date[0]-1;
				$y[] = $date[0];
				$y[] = $date[0]+1;
				$y[] = $date[0]+2;
			}
			elseif($date[0] < 1981)
			{
				$y[] = 1979;
				$y[] = 1980;
				$y[] = 1981;
				$y[] = 1982;
				$y[] = 1983;
			}
			elseif($date[0] > 2035)
			{
				$y[] = 2033;
				$y[] = 2034;
				$y[] = 2035;
				$y[] = 2036;
				$y[] = 2037;
			}

			foreach($y as $v)
			{
				if($v == $date[0])
				{
					$selected = ' selected';
				}
				else
				{
					$selected = '';
				}
				echo '<option value="'.$v.'"'.$selected.'>'.$v.'</option>';
			}
		}
		/**
		* Gibt das Aktuelle ISO Datum bzw. Monat oder Jahr zurueck
		* @param string $typ
		* @return string
		* @function
		*/
		public function current_date($typ='')
		{
			if($typ == '')
			{
				return date("Y-m-d",$this->timestamp);
			}
			elseif($typ == 'm')
			{
				return date("m",$this->timestamp);
			}
			else
			{
				return date("Y",$this->timestamp);
			}
		}
		/**
		* Gibt das Aktuelle Datum im FREE Format zurueck
		* @return string
		* @function
		*/
		public function show_current_date()
		{
			return $this->format_date($this->current_date(),'FREE');
		}
		/**
		* Textausgabe
		* @param int $id Indexnummer des Array $this->txt
		* @return string
		* @function
		*/
		public function lng($id)
		{
			return get_text($this->txt[$id], 'return');
		}
		/**
		* Erstellt aus einem Wintimestamp ein Datum, Uhrzeit oder beides
		* @param string $WTS
		* @param string $TYP
		* @return string
		* @function
		*/
		public function GetWinTime($WTS,$TYP='all')
		{
			$return = '';
			if(!empty($WTS))
			{
				$Y = substr($WTS,0,4);
				$m = substr($WTS,4,2);
				$d = substr($WTS,6,2);
				$H = substr($WTS,8,2);
				$i = substr($WTS,10,2);
				$s = substr($WTS,12,2);

				if($TYP == 'all')
				{
					$return = $this->format_date($Y.'-'.$m.'-'.$d,'FREE');
					$return .= ' '.$H.':'.$i.':'.$s;
				}
				elseif($TYP == 'date')
				{
					$return = $this->format_date($Y.'-'.$m.'-'.$d,'FREE');
				}
				elseif($TYP == 'time')
				{
					$return = $H.':'.$i.':'.$s;
				}
				elseif($TYP == 'unix')
				{
					$return = mktime($H,$i,$s,$m,$d,$Y);
				}
				return $return;
			}
			else
			{
				return $return;
			}
		}
	}
?>