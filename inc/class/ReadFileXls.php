<?php
	/**
	 * reads *.xls files
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * reads *.xls files
	 * @package file-o-meter
	 * @subpackage class
	 */
	class ReadFileXls
	{
		public function read_sheetfile($file, $file_type)
		{
			$return_string = '';
			$read_file = false;

			$excel_obj = PHPExcel_IOFactory::createReader($file_type);//'Excel5'
			$excel_obj->setReadDataOnly(true);

			$xls = $excel_obj->load($file);

			$sheet_count = $xls->getSheetCount();

			for ($active_sheet = 0; $active_sheet < $sheet_count; $active_sheet++)
			{
				$actvie_worksheet = $xls->setActiveSheetIndex($active_sheet);
				$hr = $actvie_worksheet->getHighestRow();
				$hc = $actvie_worksheet->getHighestColumn();

				for ($row = 1; $row <= $hr; ++$row)
				{
					for ($col = "A"; $col <= $hc; $col++)
					{
						$value = trim($actvie_worksheet->getCell($col.$row)->getValue());
						if (!empty($value))
						{
							$read_file = true;
							$return_string .= ' '.$value;
						}
					}
				}
			}

			if ($read_file == true)
			{
				return $return_string;
			}
			else
			{
				return false;
			}
		}

		/**
	 	 * Liest einen Xls Datei ein und gibt den String zurueck
	 	 * @param int $job_id
	 	 * @return string
	 	 */
	 	public function read_file($job_id)
		{
			$cdb = new MySql;
			if (class_exists('PHPExcel_IOFactory'))
			{
				$sql = $cdb->select('SELECT file_id, save_name FROM fom_file_job_index WHERE job_id='.$job_id);
				$result = $cdb->fetch_array($sql);

				//Pruefen ob XLS Datei vorhanden ist
				if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name']))
				{
					$ex = strtolower(substr($result['save_name'], -4));

					$read_result = false;

					if ($ex == '.xls')
					{
						$read_result = $this->read_sheetfile(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'], 'Excel5');
					}
					elseif ($ex == '.ods')
					{
						$read_result = $this->read_sheetfile(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'], 'OOCalc');
					}
					elseif ($ex == 'xlsx')
					{
						$read_result = $this->read_sheetfile(FOM_ABS_PFAD.'files/tmp/index_job/'.$result['save_name'], 'Excel2007');
					}

					if ($read_result !== false)
					{
						//tmporaeredaten loeschen
						$this->delete_job($job_id, $result['save_name']);
						return $read_result;
					}
					else
					{
						return '';
					}
				}
				else
				{
					return '';
				}
			}
			else
			{
				return '';
			}
		}

		/**
		 * entfernt einen Jobauftragaus der Tabelle und alle dazugehoerigen Dateien
		 */
		private function delete_job($job_id, $file_name)
		{
			$cdb = new MySql;

			$cdb->delete('DELETE FROM fom_file_job_index WHERE job_id='.$job_id.' LIMIT 1');

			//Datendatei loeschen
			if (file_exists(FOM_ABS_PFAD.'files/tmp/index_job/'.$file_name))
			{
				@unlink(FOM_ABS_PFAD.'files/tmp/index_job/'.$file_name);
			}
		}
	}
?>