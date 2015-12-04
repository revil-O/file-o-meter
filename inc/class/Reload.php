<?php
	/**
	 * form reload blocker (prevents multiple data entry - the form will be sent only one time)
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * form reload blocker (prevents multiple data entry - the form will be sent only one time)
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Reload
	{
		/**
		* Erstellt einen Eintrag mit einer eindeutigen ID und gibt diesen als hidden wert zurueck.
		* @param string $retrun, echo = direkte ausgabe per echo
		* @return string
		* @function
		*/
		public function create($return = 'echo')
		{
			$cdb = new MySql;

			$key = md5(uniqid(rand()));

			if($cdb->insert("INSERT INTO fom_reload (reload_id, expire_time) VALUES ('".$key."', '".date('YmdHis')."')"))
			{
				$this->del_reload_id();

				if($return == 'echo')
				{
					echo '<input type="hidden" name="reload_sperre_string" value="'.$key.'" />'."\r\n";
				}
				else
				{
					return '<input type="hidden" name="reload_sperre_string" value="'.$key.'" />';
				}
			}
			else
			{
				if($return == 'echo')
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
		* Prueft ob die uebergebene Id in der DB vorhanden ist und gibt bei erfolg TRUE zurueck.
		* @param string $key, Reload ID
		* @return bool
		* @function
		*/
		public function check($key)
		{
			$cdb = new MySql;
			$sql = $cdb->select("SELECT reload_id FROM fom_reload WHERE reload_id='$key'");
			$result = $cdb->fetch_array($sql);

			if(isset($result['reload_id']) and !empty($result['reload_id']))
			{
				$this->del_reload_id($key);
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		/**
		* Entfernt alle Reload ID`s die aelter als 30 min sind.
		* @param string $key, Reload ID
		* @function
		*/
		public function del_reload_id($key = '')
		{
			$cdb = new MySql;

			if(!empty($key))
			{
				$cdb->delete("DELETE FROM fom_reload WHERE reload_id='$key' LIMIT 1");
			}
			else
			{
				$del_time = date('YmdHis',time() - 1800);
				$cdb->delete("DELETE FROM fom_reload WHERE expire_time < '$del_time'");
			}
		}
	}
?>