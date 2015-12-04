<?php
	/**
	 * main class for all date- and calendar-classes, other classes should inherit from this parent class using the extends keyword
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de>
	 * @copyright Copyright (C) 2012  docemos GmbH
	 * @package file-o-meter
	 *
	 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
	 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 * You should have received a copy of the GNU General Public License along with this program; if not, see http://www.gnu.org/licenses/.
	 */

	/**
	 * main class for all date- and calendar-classes, other classes should inherit from this parent class using the extends keyword
	 * @package file-o-meter
	 * @subpackage classes
	 */
	class Kalender
	{
		/**
		 * Speichert allgemeine Einstellungen
		 */
		public $setup_array = array();

		/**
		 * Speichert Temporaere Daten		 *
		 */
		private $tmp_array = array();

		public function __construct()
		{
			//Zeitzone definieren
			//FIXME: die folgende Zeile muss einkommentiert werden, wenn sie NICHT in den Grundeinstellungen des Projekts vorkommt
			//date_default_timezone_set('Europe/Berlin');

			//FIXME: Das Sollte aus den Grundeinstellungen kommen
			//Ansonsten koennen nachfolgende Datumsformate uebergeben werden
			//siehe http://de3.php.net/manual/de/function.date.php
			// d
			// m
			// Y
			$this->setup_array['date_format']					= 'FREE';

			//Allgemeine Datumsausgabe
			if (!empty($this->setup_array['date_format']))
			{
				//FREE ist ein pseudonym fuer das Deutsche Datumsformat z.B. 15.03.2008
				if (strtoupper($this->setup_array['date_format']) == 'FREE')
				{
					$this->setup_array['date_format'] 				= 'd.m.Y';
				}
				//ISO ist ein pseudonym fuer das ISO Datumsformat z.B. 2008-03-15
				elseif (strtoupper($this->setup_array['date_format']) == 'ISO')
				{
					$this->setup_array['date_format']				= 'Y-m-d';
				}
			}
			//sollte nichts definiert sein wird standardmaessig ISO geladen
			else
			{
				$this->setup_array['date_format'] 					= 'Y-m-d';
			}

			$this->setup_array['current_timestamp']					= time();
			$this->setup_array['current_date_iso']					= date('Y-m-d', $this->setup_array['current_timestamp']);
			$this->setup_array['current_date']						= date($this->setup_array['date_format'], $this->setup_array['current_timestamp']);
			$this->setup_array['current_day']						= date('d', $this->setup_array['current_timestamp']);
			$this->setup_array['current_day_number']				= date('w', $this->setup_array['current_timestamp']);
			$this->setup_array['days_in_month']						= date('t', $this->setup_array['current_timestamp']);
			$this->setup_array['current_week_number']				= date('W', $this->setup_array['current_timestamp']);
			$this->setup_array['current_month']						= date('m', $this->setup_array['current_timestamp']);
			$this->setup_array['current_year']						= date('Y', $this->setup_array['current_timestamp']);
			$this->setup_array['current_win_timestamp']				= date('YmdHis', $this->setup_array['current_timestamp']);

			//Wochentage an denen Wochenende ist
			//ACHTUNG: Sonntag ist 7 NICHT 0
			$this->setup_array['weekend_days']						= array(
																			//1/*Montag*/,
																			//2/*Dienstag*/,
																			//3/*Mittwoch*/,
																			//4/*Donnerstag*/,
																			//5/*Freitag*/,
																			6/*Samstag*/,
																			7/*Sonntag*/
																			);

			//Kurzbezeichnungen der Monate (dienen als Key fuer die Texttabelle)
			$this->setup_array['month_array'] 						= array(
																			1 => 'januar',
																			2 => 'februar',
																			3 => 'maerz',
																			4 => 'april',
																			5 => 'mai',
																			6 => 'juni',
																			7 => 'juli',
																			8 => 'august',
																			9 => 'september',
																			10 => 'oktober',
																			11 => 'november',
																			12 => 'dezember');
		}

		/**
		 * Konvertiert ein Datum vom Aktuell eingestellten Datumsformat ins ISO Format
		 *
		 * @param string $date
		 * @return string
		 */
		public function free_to_iso($date)
		{
			$result = $this->explode_date($date, $this->setup_array['date_format']);

			if ($result !== false)
			{
				return $result[0].'-'.$result[1].'-'.$result[2];
			}
			else
			{
				return '0000-00-00';
			}
		}

		/**
		 * Konvertiert ein Datum von d.m.Y zu Y-m-d
		 *
		 * @param string $date
		 * @return string
		 */
		public function iso_to_free($date)
		{
			$result = $this->explode_date($date, 'Y-m-d');

			if ($result !== false)
			{
				//Datumsformat
				$tmp_date = $this->setup_array['date_format'];
				//Jahr
				$tmp_date = str_replace('Y', $result[0], $tmp_date);
				//Monat
				$tmp_date = str_replace('m', $result[1], $tmp_date);
				//Tag
				$tmp_date = str_replace('d', $result[2], $tmp_date);
				return $tmp_date;
			}
			else
			{
				return '';
			}
		}

		/**
		 * Wandelt einen Windowstimestamp in ein beliebig anderes Fromat
		 *
		 * @param string $win_time
		 * @param string $format
		 * @param int $sub_start
		 * @param int $sub_len
		 * @return string
		 */
		public function win_to_time($win_time, $format = 'all', $sub_start = 0, $sub_len = 0)
		{
			$return = '';
			if (!empty($win_time))
			{
				$Y = substr($win_time, 0, 4);
				$m = substr($win_time, 4, 2);
				$d = substr($win_time, 6, 2);
				$H = substr($win_time, 8, 2);
				$i = substr($win_time, 10, 2);
				$s = substr($win_time, 12, 2);

				//Datum und Uhrzeit
				if ($format == 'all')
				{
					$return = $this->iso_to_free($Y.'-'.$m.'-'.$d);
					$return .= ' '.$H.':'.$i.':'.$s;
				}
				//Datum
				elseif ($format == 'date')
				{
					$return = $this->iso_to_free($Y.'-'.$m.'-'.$d);
				}
				//Uhrzeit
				elseif ($format == 'time')
				{
					$return = $H.':'.$i.':'.$s;
				}
				//Unixtimestamp
				elseif ($format == 'unix')
				{
					$return = mktime($H, $i, $s, $m, $d, $Y);
				}

				if ($sub_start != 0 and $sub_len != 0)
				{
					return substr($return, $sub_start, $sub_len);
				}
				else
				{
					return $return;
				}
			}
			else
			{
				return $return;
			}
		}

		/**
		 * Erstellt aus dem eingestellten Datumsformat einen bezeichnenden Textstring
		 * z.B. Y-m-d -> JJJJ-MM-DD
		 *
		 * @return string
		 */
		public function get_date_format()
		{
			if (!empty($this->setup_array['date_format']))
			{
				$search_array = array('d', 'm', 'Y');
				//						DD						MM						JJJJ
				$replace_array = array(get_text(361, 'return'), get_text(362, 'return'), get_text(363, 'return'));

				return str_replace($search_array, $replace_array, $this->setup_array['date_format']);
			}
			else
			{
				return '';
			}
		}

		/**
		 * Gibt diverse Informationen zum uebergebenen Datum zurueck
		 *
		 * @param string $date
		 * @param string $date_format, uebergebenes Datumsformat. Ist $date_format empty wird standardmaessig $setup_array['date_format'] verwendet
		 * @return array
		 */
		public function read_date_info($date, $date_format = '')
		{
			$return_array = array();

			//uebergebenes Datumsformat festlegen
			if (strtoupper($date_format) == 'FREE')
			{
				$date_format = 'd.m.Y';
			}
			elseif (strtoupper($date_format) == 'ISO')
			{
				$date_format = 'Y-m-d';
			}
			elseif (empty($date_format))
			{
				$date_format = $this->setup_array['date_format'];
			}

			$date_result = $this->explode_date($date, $date_format);

			if ($date_result !== false)
			{

				$day = $date_result[2];
				$month = $date_result[1];
				$year = $date_result[0];

				$date_timestamp = mktime(0, 0, 0, $month, $day, $year);
				$date_new = date($date_format, $date_timestamp);

				//Wenn keine Fehler aufgetretten sind sollte hier genau das selbe raus kommen
				if ($date == $date_new)
				{
					$return_array['date']			= $date_new;
					$return_array['timestamp']		= $date_timestamp;
					$return_array['iso']			= date('Y-m-d', $date_timestamp);
					$return_array['day']			= date('d', $date_timestamp);
					$return_array['day_number']		= date('w', $date_timestamp);
					$return_array['days_in_month']	= date('t', $date_timestamp);
					$return_array['week_number']	= date('W', $date_timestamp);
					$return_array['month']			= date('m', $date_timestamp);
					$return_array['year']			= date('Y', $date_timestamp);
					$return_array['win_timestamp']	= date('YmdHis', $date_timestamp);

					return $return_array;
				}
				else
				{
					return $return_array;
				}
			}
			else
			{
				return $return_array;
			}
		}

		/**
		 * Zerlegt ein Datum in seine Bestandteile
		 *
		 * @param string $date
		 * @param string $date_format, Formatstring des uebergebenen Datums
		 * @return mixed uebergibt false im Fehlerfall und array($year, $month, $day) bei erfolg
		 */
		public function explode_date($date, $date_format)
		{
			$day = '';
			$month = '';
			$year = '';
			$tmp_date = $date;
			//Tag, Monat, Jahr suchen
			for ($i = 0; $i < strlen($date_format); $i++)
			{
				if ($date_format[$i] == 'd')
				{
					$day = substr($tmp_date, 0, 2);
					$tmp_date = substr($tmp_date, 2);
				}
				elseif ($date_format[$i] == 'm')
				{
					$month = substr($tmp_date, 0, 2);
					$tmp_date = substr($tmp_date, 2);
				}
				elseif ($date_format[$i] == 'Y')
				{
					$year = substr($tmp_date, 0, 4);
					$tmp_date = substr($tmp_date, 4);
				}
				else
				{
					$tmp_date = substr($tmp_date, 1);
				}
			}

			//pruefen ob Tag, Monats und Jahresangaben vorhanden
			if (!empty($day) and !empty($month) and !empty($year))
			{
				//Datumswerte pruefen
				if (@checkdate($month, $day, $year))
				{
					return array($year, $month, $day);
				}
			}
			return false;
		}

		/**
		 * pruefen ob Wochenende ist
		 *
		 * @param string $date, ISO Datum
		 * @return boole
		 */
		public function is_weekend($date, $day_number = 'NULL')
		{
			if ($day_number != 'NULL')
			{
				//Sonntag in 7 aendern
				if ($day_number == 0)
				{
					$day_number = 7;
				}

				if (in_array($day_number, $this->setup_array['weekend_days']))
				{
					return true;
				}
			}
			elseif (!empty($date))
			{
				$date_info = $this->read_date_info($date, 'ISO');
				//Sonntag in 7 aendern
				if ($date_info['day_number'] == 0)
				{
					$date_info['day_number'] = 7;
				}

				if (in_array($date_info['day_number'], $this->setup_array['weekend_days']))
				{
					return true;
				}
			}
			return false;
		}

		/**
		 * prueft ob das uebergebene Datum ein ISO Datum ist. Liefert bei erfolg das Original ISO Datum zurueck ansonsten false
		 *
		 * @param string $date
		 * @return mixed
		 */
		public function is_iso_date($date)
		{
			if (strlen($date) == 10)
			{
				$year = intval(substr($date, 0, 4));
				$month = substr($date, 5, 2);
				$day = substr($date, 8, 2);
				if (@checkdate($month, $day, $year))
				{
					return $date;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		/**
		 * Addiert oder Subtraiert Tage, Monate oder Jahre von einem Datum
		 *
		 * @param string $date
		 * @param int $year
		 * @param int $month
		 * @param int $day
		 * @return array
		 */
		public function date_calculator($date, $year, $month, $day)
		{
			//pruefen ob ein datum uebergeben wurde wenn nicht aktuelles verwenden
			if (empty($date))
			{
				$date = $this->setup_array['current_date_iso'];
			}
			//pruefen ob das uebergebene Datum ein ISO Datum ist
			if ($this->is_iso_date($date) !== false)
			{
				//Allgemeine Informationen zum uebergebenen Datum auslesen
				$date_info_array = $this->read_date_info($date, 'ISO');

				//pruefen ob ein Jahr angegeben wurde
				if (!empty($year))
				{
					$new_year = $date_info_array['year'] + intval($year);
				}
				else
				{
					$new_year = $date_info_array['year'];
				}

				//pruefen ob ein Monat angegeben wurde
				if (!empty($month))
				{
					$new_month = $date_info_array['month'] + intval($month);
				}
				else
				{
					$new_month = $date_info_array['month'];
				}

				//pruefen ob ein Tag angegeben wurde
				if (!empty($day))
				{
					$new_day = $date_info_array['day'] + intval($day);
				}
				else
				{
					$new_day = $date_info_array['day'];
				}
				//aus den geaenderten datumsangaben einen neuen timestamp erstellen
				$new_timestamp = mktime(0, 0, 0, $new_month, $new_day, $new_year);

				//neues datum erstellen
				$new_date_array = array();
				$new_date_array[0] = date('Y-m-d', $new_timestamp);
				$new_date_array[1] = $new_timestamp;
				$new_date_array[2] = $this->read_date_info($new_date_array[0], 'ISO');

				return $new_date_array;
			}
			return array('0000-00-00', '', '');
		}

		/**
		 * Erstellt ein Array mit Datumswerten die zwischen den zwei uebergebenen Datumswerten liegen
		 *
		 * @param string $start
		 * @param string $end
		 * @return array
		 */
		public function get_difference($start, $end)
		{
			$return_array = array();

			$tmp_date = $start;
			$while = true;

			while ($while === true)
			{
				$date_info_array = $this->read_date_info($tmp_date, 'ISO');

				$return_array[] = array('iso'	=> $date_info_array['iso'],
										'free'	=> $date_info_array['date'],
										'stamp'	=> $date_info_array['timestamp']);

				$next_date = $this->date_calculator($tmp_date, '', '', 1);

				$tmp_date = $next_date[0];

				if (str_replace('-', '', $tmp_date) > str_replace('-', '', $end))
				{
					$while = false;
				}
			}
			return $return_array;
		}

		/**
		 * Gibt zu einem Key einen Text zurueck
		 *
		 * @param mixed $key
		 * @return string
		 */
		public function get_kalender_text($key, $text_typ = '')
		{
			if (empty($text_typ))
			{
				if (is_numeric($key))
				{
					return get_text($key, 'return');
				}
				else
				{
					return get_text('', 'return', 'decode_on', $key);
				}
			}
			elseif ($text_typ == 'month_name')
			{
				$month = intval($key);

				if ($month > 0 and $month <= 12)
				{
					return get_text($this->setup_array['month_array'][$month], 'return', 'decode_on');
				}
			}
			return '';
		}
	}
?>