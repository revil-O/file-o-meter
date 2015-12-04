<?php
	/**
	 * output of text-phrases, stored in a MySQL DB
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * output of text-phrases, stored in a MySQL DB
	 * @package file-o-meter
	 * @subpackage class
	 */
	class GetText
	{
		/**
		* Liest einen Text aus der Datenbank und gibt diesen zurueck. Diese Function liest nur Systemtexte aus!
		* Die MySQL Klasse "MySql" wird fuer die Datenbankabfragen eingebunden.
		* @param int $TXTID, Text ID
		* @param string $TXTKEY
		* @param string $RETURN, echo oder return
		* @param string $DECODEMODE, charset fuer html_entity_decode
		* @param array $VARIABLE, enthaelt Werte die in den Textstring eingefuegt werden sollen
		* @return string
		*/
		public function Get_Text($TXTID, $TXTKEY = '', $RETURN = 'echo', $DECODEMODE = 'decode_off', $VARIABLE = array())
		{
			if ((is_numeric($TXTID) and $TXTID > 0) or !empty($TXTKEY))
			{
				//mysql klasse laden
				$cdb = new MySql;

				//Usersprache
				if (!isset($GLOBALS['user_language']) or empty($GLOBALS['user_language']))
				{
					$GLOBALS['user_language'] = 1;
				}

				//Text anhand der ID suchen
				if (is_numeric($TXTID) and $TXTID > 0)
				{
					$sql = $cdb->select('SELECT language_'.$GLOBALS['user_language'].' FROM fom_text WHERE text_id='.$TXTID);
					$result = $cdb->fetch_row($sql);
				}
				//Text anhand des textkeys suchen
				elseif (!empty($TXTKEY))
				{
					$sql = $cdb->select('SELECT language_'.$GLOBALS['user_language']." FROM fom_text WHERE text_key='$TXTKEY'");
					$result = $cdb->fetch_row($sql);
				}

				//text ist nicht leer
				if (isset($result[0]) and !empty($result[0]))
				{
					//html sonderzeichen z.B. &quot; in " wandeln
					if ($DECODEMODE == 'decode_off' or $DECODEMODE == 'decode_pdf')
					{
						$tmp_txt = html_entity_decode($result[0], ENT_QUOTES, 'UTF-8');
					}
					else
					{
						$tmp_txt = $result[0];
					}

					//eventuell vorhandene tags ersetzen
					$tmp_txt = $this->ReplaceTag($tmp_txt, $VARIABLE, $DECODEMODE);

					if ($RETURN == 'echo')
					{
						echo $tmp_txt;
					}
					else
					{
						return $tmp_txt;
					}
				}
				else
				{
					if ($RETURN == 'echo')
					{
						echo '';
					}
					else
					{
						return '';
					}
				}
			}
			else
			{
				if ($RETURN == 'echo')
				{
					echo '';
				}
				else
				{
					return '';
				}
			}
		}

		/**
		* Sucht nach eventuell vorhandenen Tags und ersetzt diese durch html Tags
		* @param string $TXT
		* @param array $VARIABLE
		* * @param string $DECODEMODE
		* @param int $WITH_VAR, 1 fuer mit Variablen umforumg 0 ohne
		* @return string
		*/
		public function ReplaceTag($TXT, $VARIABLE, $DECODEMODE, $WITH_VAR = 1)
		{
			$search_array = array('[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[br]');

			//fuer pdfausgabe alle tags entfernen
			if ($DECODEMODE == 'decode_pdf')
			{
				$replace_pdf_array = array('', '', '', '', '', '', "\n");
				$TXT = str_replace($search_array, $replace_pdf_array, $TXT);
			}
			//normale htmlausgabe
			else
			{
				$replace_array = array('<strong>', '</strong>', '<em>', '</em>', '<u>', '</u>', '<br />');
				$TXT = str_replace($search_array, $replace_array, $TXT);
			}
			//Variablenwerte einfuegen
			if ((substr_count($TXT, '[var]') > 0 or substr_count($TXT, '[/var]') > 0) and $WITH_VAR == 1)
			{
				$TXT = $this->ReplaceVarTag($TXT, $VARIABLE);
			}
			return $TXT;
		}

		/**
		* Sucht nach vorhandenen [var] Tags und ersetzt diese durch die variable
		* @param string $TXT
		* @param array $VARIABLE
		* @return string
		*/
		private function ReplaceVarTag($TXT, $VARIABLE)
		{
			if (count($VARIABLE) > 0)
			{
				$search_array = array();
				$replace_array = array();

				foreach ($VARIABLE as $i => $v)
				{
					$search_array[] = '[var]'.$i.'[/var]';
					$replace_array[] = $v;
				}
				$TXT = str_replace($search_array, $replace_array, $TXT);
			}
			if (substr_count($TXT, '[var]') == 0)
			{
				return $TXT;
			}
			//Vartags entfernen fuer die keine werte uebergeben wurden
			else
			{
				$TXT = preg_replace('@\[var\](.*?)\[\/var\]@s', '', $TXT);

				return $TXT;
			}
		}
	}
?>