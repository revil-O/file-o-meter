<?php
	/**
	 * subfile functions
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * subfile functions
	 * @package file-o-meter
	 * @subpackage class
	 */
	class SubFile
	{
		/**
		 * Prueft ob fuer die uebergebene Datei SubDateien Existieren
		 * @param int $fileid_int
		 * @return boole
		 */
		public function sub_file_exists($fileid_int)
		{
			$cdb = new MySql();
			$ac = new Access();

			$sql = $cdb->select("SELECT t1.subfile_id FROM fom_sub_files t1
								LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
								WHERE t1.file_id=$fileid_int AND t2.anzeigen='1'");
			while ($result = $cdb->fetch_array($sql))
			{
				if (!empty($result['subfile_id']))
				{
					if ($ac->chk('file', 'r', $result['subfile_id']))
					{
						return true;
					}
				}
			}
			return false;
		}
	}
?>