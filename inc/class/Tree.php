<?php
	/**
	 * treeview-class
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/*
		CREATE TABLE `folder` (
		`folder_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`projekt_id` INT UNSIGNED NOT NULL ,
		`folder_name` VARCHAR( 30 ) NOT NULL ,
		`bemerkungen` TEXT NULL ,
		`ob_folder` INT UNSIGNED NULL ,
		`ebene` TINYINT UNSIGNED NULL ,
		`anzeigen` TINYINT UNSIGNED NULL DEFAULT '1'
		) ENGINE = MYISAM COMMENT = 'Verzeichnistabelle';

		CREATE TABLE `files` (
		`file_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`folder_id` INT UNSIGNED NOT NULL ,
		`user_id` INT UNSIGNED NULL ,
		`org_name` VARCHAR( 31 ) NULL ,
		`save_name` VARCHAR( 31 ) NULL ,
		`mime_type` VARCHAR( 50 ) NULL ,
		`file_size` FLOAT UNSIGNED NULL ,
		`save_time` VARCHAR( 14 ) NULL ,
		`bemerkungen` TEXT NULL ,
		`anzeigen` TINYINT UNSIGNED NULL DEFAULT '1'
		) ENGINE = MYISAM COMMENT = 'Dateien';
	*/

	/**
	 * treeview-class
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Tree
	{
		public $FolderArray = array();//Speichert alle Verzeichnis der reihe nach
		public $OpenFolderArray = array(); //Speichert alle geoeffneten Verzeichnise
		public $PageName = '../folder/index.php'; //Seitenname wird fuer die links benoetigt
		public $Images = array(); //Speichert die bilder fuer die Verzeichnisbaum darstellung
		public $PROJEKT_ID = ''; //Speichert die Projektid
		public $SaveData = array(); //Speichert alle Variablen die bei einem neustart geloescht werden
		public $tmp_array = array();

		private function SetVariablesDefault()
		{
			//$this->Images = array('plus'=>'<img src="'.FOM_ABS_URL.'pic/plus.gif" alt="" width="13" height="13" border="0" />','minus'=>'<img src="'.FOM_ABS_URL.'pic/minus.gif" alt="" width="13" height="13" border="0" />','open'=>'<img src="'.FOM_ABS_URL.'pic/open.gif" alt="" width="16" height="13" border="0" />','close'=>'<img src="'.FOM_ABS_URL.'pic/close.gif" alt="" width="16" height="13" border="0" />','s_spacer'=>'<img src="'.FOM_ABS_URL.'pic/spacer.gif" alt="" width="13" height="13" border="0" />','l_spacer'=>'<img src="'.FOM_ABS_URL.'pic/spacer.gif" alt="" width="16" height="13" border="0" />');
			$this->Images = array('plus'		=> get_img('bullet_toggle_plus.png'),
									'minus'		=> get_img('bullet_toggle_minus.png'),
									'open'		=> get_img('folder_go.png'),
									'close'		=> get_img('folder.png'),
									'empty'		=> get_img('folder_delete.png'),
									's_spacer'	=> get_img('_spacer.gif', '', '', 'image', 0, '' , '', 16, 16),
									'l_spacer'	=> get_img('_spacer.gif', '', '', 'image', 0, '' , '', 19, 16));
			$this->FolderArray = array();
			$this->OpenFolderArray = array();
			$this->PROJEKT_ID = '';
		}
		private function SaveVariables()
		{
			$this->SaveData[$this->PROJEKT_ID]['FolderArray'] = $this->FolderArray;
			$this->SaveData[$this->PROJEKT_ID]['OpenFolderArray'] = $this->OpenFolderArray;
		}

		/**
		* Gibt die Angelegten Projekte zurueck
		* @param string $RETURNMODE
		* @return mixed
		* @funcrion
		*/
		public function ShowProjekts($RETURNMODE = 'SHOW')
		{
			$db = new MySql;

			$return = array();

			$sql = $db->select("SELECT * FROM fom_projekte ORDER BY projekt_name ASC");
			while($result = $db->fetch_array($sql))
			{
				if ($GLOBALS['ac']->chk('project', 'r', $result['projekt_id']))
				{
					$sql_count = $db->select("SELECT folder_id FROM fom_folder WHERE projekt_id=".$result['projekt_id']." AND anzeigen='1'");
					$count_result = $db->fetch_array($sql_count);

					if ($count_result['folder_id'] > 0)
					{
						$return[] = array('pid'=>$result['projekt_id'], 'name'=>$result['projekt_name'], 'sub'=>'1');
					}
					else
					{
						$return[] = array('pid'=>$result['projekt_id'], 'name'=>$result['projekt_name'], 'sub'=>'0');
					}
				}
			}

			if ($RETURNMODE == 'SHOW')
			{
				foreach($return as $v)
				{
					if ($v['sub'] == 1)
					{
						echo '<a href="'.$this->PageName.$GLOBALS['gv']->create_get_string('?pid_int='.$v['pid']).'">'.$v['name'].'</a><br />';
					}
					else
					{
						echo '<a href="'.$this->PageName.$GLOBALS['gv']->create_get_string('?pid_int='.$v['pid']).'">'.$v['name'].'</a><br />';
					}
				}
			}
			else
			{
				return $return;
			}
		}

		/**
		* Gibt den Verzeichnisbaum aus
		* @param int $FID, Verzeichnis-ID
		* @Param int $PID, Projekt-ID
		* @param string $RETURNMODE, Ausgabemodus
		*/
		public function ShowFolder($PID, $FID = 0, $RETURNMODE = 'SHOW')
		{
			$db = new MySql;

			//Leer alle Globalen Variablen, wird fuer die Ausgabe von mehreren Verzeichnisbaeumen benoetigt
			$this->SetVariablesDefault();
			$this->PROJEKT_ID = $PID;

			if ($FID == 0)
			{
				$sql = $db->select("SELECT folder_id, folder_name, ebene FROM fom_folder WHERE projekt_id=$PID AND ob_folder=0 AND anzeigen='1' ORDER BY folder_name ASC");
				while($result = $db->fetch_array($sql))
				{
					//Pruefen ob unterverzeichnisse vorhanden sind
					if ($this->CheckSubFolder($result['folder_id']))
					{
						$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$PID, 'ebene'=>$result['ebene'], 'status'=>'close', 'sub'=>'1', 'name'=>$result['folder_name']);
					}
					else
					{
						if ($this->CheckFileLinkInFolder($result['folder_id']))
						{
							$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$PID, 'ebene'=>$result['ebene'], 'status'=>'close', 'sub'=>'0', 'name'=>$result['folder_name']);
						}
						else
						{
							$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$PID, 'ebene'=>$result['ebene'], 'status'=>'empty', 'sub'=>'0', 'name'=>$result['folder_name']);
						}
					}
				}
			}
			else
			{
				//Speichert alle offenen Verzeichnise
				$this->GetOpenFolder($FID);
				//Liest den Verzeichnisbaum inkl. aller geoeffneten Verzeichnise aus
				$this->ReadFolder();

			}

			//Variablen fuer eine Spaetere Verarbeitung Speichern
			$this->SaveVariables();

			if($RETURNMODE != 'SHOW')
			{
				return $this->FolderArray;
			}
			else
			{
				return $this->GetHtmlFolder();
			}
		}

		/**
		* Liest alle Verzeichnisse aus
		* @param int $ID, OberverzeichnisID
		* @return void
		* @function
		*/
		public function ReadFolder($ID = 0)
		{
			$db = new MySql;
			if($ID == 0)
			{
				$where = 'WHERE projekt_id='.$this->PROJEKT_ID." AND ob_folder=0 AND anzeigen='1'";
			}
			else
			{
				$where = 'WHERE ob_folder='.$ID." AND anzeigen='1'";
			}

			//Alle Verzeichnisse Auflisten die das selbe Oberverzeichnis haben
			$sql = $db->select("SELECT folder_id, folder_name, ebene FROM fom_folder $where ORDER BY folder_name ASC");
			while($result = $db->fetch_array($sql))
			{
				//Pruefen ob das aktuelle Verzeichnis als Geoeffnet gespeichert ist
				if (in_array($result['folder_id'], $this->OpenFolderArray))
				{
					//Pruefen ob das Verzeichnis unterverzeichnis hat
					if ($this->CheckSubFolder($result['folder_id']))
					{
						$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$this->PROJEKT_ID, 'ebene'=>$result['ebene'], 'status'=>'open', 'sub'=>'1', 'name'=>$result['folder_name']);
					}
					else
					{
						$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$this->PROJEKT_ID, 'ebene'=>$result['ebene'], 'status'=>'open', 'sub'=>'0', 'name'=>$result['folder_name']);
					}

					//Funktion erneut starten um alle unterverzeichnis zu speichern
					$this->ReadFolder($result['folder_id']);
				}
				else
				{
					//Pruefen ob das Verzeichnis Unterverzeichnise hat
					if ($this->CheckSubFolder($result['folder_id']))
					{
						$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$this->PROJEKT_ID, 'ebene'=>$result['ebene'], 'status'=>'close', 'sub'=>'1', 'name'=>$result['folder_name']);
					}
					else
					{
						if ($this->CheckFileLinkInFolder($result['folder_id']))
						{
							$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$this->PROJEKT_ID, 'ebene'=>$result['ebene'], 'status'=>'close', 'sub'=>'0', 'name'=>$result['folder_name']);
						}
						else
						{
							$this->FolderArray[] = array('fid'=>$result['folder_id'], 'pid'=>$this->PROJEKT_ID, 'ebene'=>$result['ebene'], 'status'=>'empty', 'sub'=>'0', 'name'=>$result['folder_name']);
						}
					}
				}
			}
		}

		/**
		* Gibt alle geoeffneten Verzeichnisse zurueck
		* @param int $FID
		* @return array
		* @function
		*/
		public function GetOpenFolder($FID)
		{
			$db = new MySql;

			$sql = $db->select('SELECT folder_id, ob_folder FROM fom_folder WHERE folder_id='.$FID);
			$result = $db->fetch_array($sql);

			$this->OpenFolderArray[] = $result['folder_id'];

			if ($result['ob_folder'] > 0)
			{
				$this->GetOpenFolder($result['ob_folder']);
			}
		}

		/**
		* Erstellt die HTML Ausgabe eines Verzeichnisbaums aus einem Array
		* @param int $PID
		* @return string
		* @function
		*/
		private function GetHtmlFolder()
		{
			$db = new MySql;

			//Projektnamen ausgeben
			$sql = $db->select('SELECT * FROM fom_projekte WHERE projekt_id='.$this->PROJEKT_ID);
			$projekt = $db->fetch_array($sql);

			echo '<a href="'.$this->PageName.$GLOBALS['gv']->create_get_string('?pid_int='.$projekt['projekt_id'].'&amp;fileinc=').'" style="white-space: nowrap;">'.get_img('drive_network.png').' <strong>'.$projekt['projekt_name'].'</strong></a><br />';

			//Pruefen ob Verzeichnisse zum Projekt existieren
			if (count($this->FolderArray) > 0)
			{
				foreach($this->FolderArray as $v)
				{
					echo '<a href="'.$this->PageName.$GLOBALS['gv']->create_get_string('?pid_int='.$v['pid'].'&amp;fid_int='.$v['fid'].'&amp;fileinc=').'" style="white-space: nowrap;">'.$this->GetTreeImage($v['status'],$v['sub'],$v['ebene']).' '.$v['name'].'</a><br />';
				}
			}
		}

		/**
		* Gibt Bilder fuer den Verzeichnisbaum aus
		* @param string $STATUS
		* @param string $SUB
		* @param int $EBENE
		* @return string
		* @function
		*/
		private function GetTreeImage($STATUS, $SUB, $EBENE)
		{
			$return = '';
			//je nach ebene spacer einfuegen
			for($i=0;$i<$EBENE;$i++)
			{
				$return .= $this->Images['l_spacer'];
			}
			//keine unterverzeichnise vorhanden
			if ($SUB == 0)
			{
				$return .= $this->Images['s_spacer'];
				if ($STATUS == 'close')
				{
					$return .= $this->Images['close'];
				}
				elseif ($STATUS == 'empty')
				{
					$return .= $this->Images['empty'];
				}
				else
				{
					$return .= $this->Images['open'];
				}

			}
			else//unterverzeichnise vorhanden
			{
				if ($STATUS == 'close')
				{
					$return .= $this->Images['plus'];
					$return .= $this->Images['close'];
				}
				elseif ($STATUS == 'empty')
				{
					$return .= $this->Images['plus'];
					$return .= $this->Images['empty'];
				}
				else
				{
					$return .= $this->Images['minus'];
					$return .= $this->Images['open'];
				}
			}

			return $return;
		}

		/**
		* Prueft ob zum angegebenen Verzeichnis ein Unterverzeichnis existiert
		* @param int $FID
		* @return boole
		* @function
		*/
		private function CheckSubFolder($FID)
		{
			$db = new MySql;
			$sql = $db->select("SELECT folder_id FROM fom_folder WHERE ob_folder=$FID AND anzeigen='1'");
			$result = $db->fetch_array($sql);

			if ($result['folder_id'] > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		private function CheckFileLinkInFolder($FID)
		{
			$db = new MySql;
			$sql = $db->select("SELECT file_id FROM fom_files WHERE folder_id=$FID AND anzeigen='1'");
			$result = $db->fetch_array($sql);

			if ($result['file_id'] > 0)
			{
				return true;
			}
			else
			{
				$sql = $db->select("SELECT link_id FROM fom_link WHERE folder_id=$FID AND anzeigen='1'");
				$result = $db->fetch_array($sql);

				if ($result['link_id'] > 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		/**
		* Gibt den aktuellen Verzeichnispfad inkl. Projektnamen aus
		* @param int $PID
		* @return string
		* @function
		*/
		public function GetFolderPfad($PID)
		{
			$db = new MySql;
			$sql = $db->select('SELECT projekt_name FROM fom_projekte WHERE projekt_id='.$PID);
			$result = $db->fetch_array($sql);

			$return = '<strong><a href="index.php'.$GLOBALS['gv']->create_get_string('?pid_int='.$PID.'&amp;fileinc=').'">'.$result['projekt_name'].'</a></strong>/ ';

			if (count($this->SaveData[$PID]['OpenFolderArray']) > 0)
			{
				$array = array_reverse($this->SaveData[$PID]['OpenFolderArray']);

				foreach($array as $v)
				{
					$sql = $db->select('SELECT folder_id, folder_name FROM fom_folder WHERE folder_id='.$v);
					$result = $db->fetch_array($sql);

					$return .= '<a href="index.php'.$GLOBALS['gv']->create_get_string('?pid_int='.$PID.'&amp;fid_int='.$result['folder_id'].'&amp;fileinc=').'">'.$result['folder_name'].'/</a> ';
				}
			}

			return $return;
		}

		/**
		* Gibt einen neuen eindeutigen Dateinamen zurueck
		* @return string
		* @function
		*/
		public function GetNewFileName()
		{
			$db = new MySql;

			$filename = md5(uniqid(rand()));

			$sql = $db->select("SELECT file_id FROM fom_files WHERE save_name='$filename%'");
			$result = $db->fetch_array($sql);

			if ($result['file_id'] > 0)
			{
				return $this->GetNewFileName();
			}
			else
			{
				return $filename;
			}
		}

		/**
		* Gibt die Dateiendung einer Datei zurueck. Die endung wird ohne . zurueck gegeben
		* @param string $FILENAME
		* @return string
		* @function
		*/
		public function GetFileExtension($FILENAME)
		{
			$ex = explode('.',$FILENAME);
			if (count($ex) > 0)
			{
				return $ex[count($ex)-1];
			}
			else
			{
				return '';
			}
		}

		/**
		* Gibt zum jeweiligen Dateityp ein Bild zurueck
		* @param string $FILE
		* @param string $MIME
		* @param string $LINK
		* @return string
		* @function
		*/
		public function GetFileType($FILE, $MIME, $return = 'image')
		{
			$ex = strtolower($this->GetFileExtension($FILE));

			//mime-typen fuer dwg-dateien
			$mime_array_dwg = array();
			$mime_array_dwg[] = 'application/acad';
			$mime_array_dwg[] = 'application/x-acad';
			$mime_array_dwg[] = 'application/autocad_dwg';
			$mime_array_dwg[] = 'image/x-dwg';
			$mime_array_dwg[] = 'application/dwg';
			$mime_array_dwg[] = 'application/x-dwg';
			$mime_array_dwg[] = 'application/x-autocad';
			$mime_array_dwg[] = 'image/vnd.dwg';
			$mime_array_dwg[] = 'drawing/dwg';


			if (substr($MIME, 0, 5) == 'video')
			{
				if ($return == 'image' or $ex = 'mpg' or $ex == 'mpg4' or $ex == 'avi' or $ex == 'mov')
				{
					return get_img('_doc_media.gif', 'Video', 'Video');
				}
				else
				{
					return array('mime' => 'video%', 'name' => 'Video');
				}
			}
			elseif (substr($MIME, 0, 5) == 'audio' or $ex == 'mp3' or $ex == 'wav')
			{
				if ($return == 'image')
				{
					return get_img('_doc_media.gif', 'Audio', 'Audio');
				}
				else
				{
					return array('mime' => 'audio%', 'name' => 'Audio');
				}
			}
			elseif (substr($MIME, 0, 5) == 'image' or $ex == 'gif' or $ex == 'jpg' or $ex == 'jpeg' or $ex == 'jpe' or $ex == 'png' or $ex == 'bmp')
			{
				if ($return == 'image')
				{
					return get_img('_doc_img.gif', 'Image', 'Image');
				}
				else
				{
					return array('mime' => 'image%', 'name' => 'Image');
				}
			}
			elseif (substr($MIME, 0, 4) == 'text')
			{
				if ($return == 'image')
				{
					return get_img('_doc_txt.gif', 'Text', 'Text');
				}
				else
				{
					return array('mime' => 'text%', 'name' => 'Text');
				}
			}
			elseif ($MIME == 'application/pdf' or $ex == 'pdf')
			{
				if ($return == 'image')
				{
					return get_img('_doc_pdf.gif', 'PDF', 'PDF');
				}
				else
				{
					return array('mime' => 'application/pdf', 'name' => 'PDF');
				}
			}
			elseif ($MIME == 'application/zip' or $ex == 'zip' or $ex == '7z' or $ex == 'gz' or $ex == 'tar')
			{
				if ($return == 'image')
				{
					return get_img('_doc_zip.gif', 'ZIP', 'ZIP');
				}
				else
				{
					return array('mime' => 'application/zip', 'name' => 'ZIP');
				}
			}
			elseif ($MIME == 'application/gzip')
			{
				if ($return == 'image')
				{
					return get_img('_doc_zip.gif', 'ZIP', 'ZIP');
				}
				else
				{
					return array('mime' => 'application/gzip', 'name' => 'ZIP');
				}
			}
			elseif ($MIME == 'application/mspowerpoint' or $ex == 'ppt' or $ex == 'pptx')
			{
				if ($return == 'image')
				{
					return get_img('_doc_powerpoint.gif', 'MS-Powerpoint', 'MS-Powerpoint');
				}
				else
				{
					return array('mime' => 'application/mspowerpoint', 'name' => 'MS-Powerpoint');
				}
			}
			elseif ($MIME == 'application/msoutlook' or $ex == 'msg')
			{
				if ($return == 'image')
				{
					return get_img('_doc_outlook.gif', 'MS-Outlook', 'MS-Outlook');
				}
				else
				{
					return array('mime' => 'application/msoutlook', 'name' => 'MS-Outlook');
				}
			}
			elseif ($MIME == 'application/msexcel' or $ex == 'xls' or $ex == 'xlsx')
			{
				if ($return == 'image')
				{
					return get_img('_doc_excel.gif', 'MS-Excel', 'MS-Excel');
				}
				else
				{
					return array('mime' => 'application/msexcel', 'name' => 'MS-Excel');
				}
			}
			elseif ($MIME == 'application/msaccess' or $ex == 'mdb' or $ex == 'accdb')
			{
				if ($return == 'image')
				{
					return get_img('_doc_access.gif', 'MS-Access', 'MS-Access');
				}
				else
				{
					return array('mime' => 'application/msaccess', 'name' => 'MS-Access');
				}
			}
			elseif ($MIME == 'application/mspublisher' or $ex == 'pub')
			{
				if ($return == 'image')
				{
					return get_img('_doc_publisher.gif', 'MS-Publisher', 'MS-Publisher');
				}
				else
				{
					return array('mime' => 'application/mspublisher', 'name' => 'MS-Publisher');
				}
			}
			elseif ($MIME == 'application/msword' or $ex == 'doc' or $ex == 'docx' or $MIME == 'application/rtf')
			{
				if ($return == 'image')
				{
					return get_img('_doc_word.gif', 'MS-Word', 'MS-Word');
				}
				else
				{
					return array('mime' => 'application/msword', 'name' => 'MS-Word');
				}
			}
			elseif ($MIME == 'application/photoshop' or $ex == 'psd')
			{
				if ($return == 'image')
				{
					return get_img('_doc_photoshop.gif', 'Adobe Photoshop', 'Adobe Photoshop');
				}
				else
				{
					return array('mime' => 'application/photoshop', 'name' => 'Adobe Photoshop');
				}
			}
			elseif ($MIME == 'application/octet-stream' or $ex == 'exe')
			{
				if ($return == 'image')
				{
					return get_img('_doc_exe.gif', 'EXE', 'EXE');
				}
				else
				{
					return array('mime' => 'application/octet-stream', 'name' => 'EXE');
				}
			}
			elseif ($MIME == 'application/xhtml+xml' or $MIME == 'application/xml')
			{
				if ($return == 'image')
				{
					return get_img('_doc_htm.gif', 'HTML XHTML', 'HTML XHTML');
				}
				else
				{
					return array('mime' => 'application/xhtml+xml', 'name' => 'HTML XHTML');
				}
			}
			elseif ($MIME == 'application/vnd.sun.xml.base' or $ex == 'odb')
			{
				if ($return == 'image')
				{
					return get_img('_doc_oob.gif', get_text(265, 'return'), get_text(265, 'return'));//OpenDocument Database
				}
				else
				{
					return array('mime' => 'application/vnd.sun.xml.base', 'name' => get_text(265, 'return'));//OpenDocument Database
				}
			}
			elseif ($MIME == 'application/vnd.oasis.opendocument.spreadsheet' or $ex == 'ods')
			{
				if ($return == 'image')
				{
					return get_img('_doc_ods.gif', get_text(266, 'return'), get_text(266, 'return'));//OpenDocument Spreadsheet
				}
				else
				{
					return array('mime' => 'application/vnd.oasis.opendocument.spreadsheet', 'name' => get_text(266, 'return'));//OpenDocument Spreadsheet
				}
			}
			elseif ($MIME == 'application/vnd.oasis.opendocument.graphics' or $ex == 'odg')
			{
				if ($return == 'image')
				{
					return get_img('_doc_ood.gif', get_text(267, 'return'), get_text(267, 'return'));//OpenDocument Drawing
				}
				else
				{
					return array('mime' => 'application/vnd.oasis.opendocument.graphics', 'name' => get_text(267, 'return'));//OpenDocument Drawing
				}
			}
			elseif ($MIME == 'application/vnd.oasis.opendocument.presentation' or $ex == 'odp')
			{
				if ($return == 'image')
				{
					return get_img('_doc_ooi.gif', get_text(268, 'return'), get_text(268, 'return'));//OpenDocument Presentation
				}
				else
				{
					return array('mime' => 'application/vnd.oasis.opendocument.presentation', 'name' => get_text(268, 'return'));//OpenDocument Presentation
				}
			}
			elseif ($MIME == 'application/vnd.oasis.opendocument.formula' or $ex == 'odf')
			{
				if ($return == 'image')
				{
					return get_img('_doc_ooc.gif', get_text(269, 'return'), get_text(269, 'return'));//OpenDocument Formula
				}
				else
				{
					return array('mime' => 'application/vnd.oasis.opendocument.formula', 'name' => get_text(269, 'return'));//OpenDocument Formula
				}
			}
			elseif ($MIME == 'application/vnd.oasis.opendocument.text' or $ex == 'odt')
			{
				if ($return == 'image')
				{
					return get_img('_doc_oow.gif', 'OpenDocument Text', 'OpenDocument Text');
				}
				else
				{
					return array('mime' => 'application/vnd.oasis.opendocument.text', 'name' => 'OpenDocument Text');
				}
			}
			elseif ($MIME == 'application/vnd.google-earth.kmz' or $ex == 'kmz')
			{
				if ($return == 'image')
				{
					return get_img('_doc_google_earth.gif', 'Google Earth', 'Google Earth');
				}
				else
				{
					return array('mime' => 'application/vnd.google-earth.kmz', 'name' => 'Google Earth');
				}
			}
			elseif ($MIME == 'application/vnd.google-earth.kml+xml' or $ex == 'kml')
			{
				if ($return == 'image')
				{
					return get_img('_doc_google_earth.gif', 'Google Earth', 'Google Earth');
				}
				else
				{
					return array('mime' => 'application/vnd.google-earth.kml+xml', 'name' => 'Google Earth');
				}
			}
			elseif (in_array($MIME, $mime_array_dwg) or $ex == 'dwg')
			{
				if ($return == 'image')
				{
					return get_img('_doc_dwg.gif', 'CAD Drawing', 'CAD Drawing');
				}
				else
				{
					$tmp_array_key = array_search($MIME, $mime_array_dwg);
					return array('mime' => $mime_array_dwg[$tmp_array_key], 'name' => 'CAD Drawing');
				}
			}
			//fuer die links
			elseif ($MIME == 'LINK')
			{
				if ($return == 'image')
				{
					return get_img('link.png', 'Link', 'Link');
				}
				else
				{
					return array('mime' => 'LINK', 'name' => 'Link');
				}
			}
			else
			{
				if ($return == 'image')
				{
					return get_img('_doc_empty.gif', get_text(270, 'return'), get_text(270, 'return'));//Miscellaneous
				}
				else
				{
					return array('mime' => '', 'name' => get_text(270, 'return'));//Miscellaneous
				}
			}
		}

		/**
		 * Erstellt ein Array mit Verzeichnissen und Unterverzeichnissen
		 * @param int $project_id
		 * @param int $folder_id
		 * @return array
		 */
		public function get_folder($project_id, $folder_id = 0)
		{
			$db = new MySql;

			$where = "WHERE projekt_id=$project_id";

			if ($folder_id > 0)
			{
				$where .= " AND folder_id=$folder_id";
			}
			else
			{
				$where .= " AND ob_folder=0";
			}
			$where .= " AND anzeigen='1'";

			$sql = $db->select("SELECT * FROM fom_folder $where ORDER BY folder_name ASC");
			while ($folder_result = $db->fetch_array($sql))
			{
				$this->tmp_array[$project_id][$folder_result['folder_id']] = $folder_result;

				$sub_sql = $db->select("SELECT folder_id FROM fom_folder WHERE ob_folder=".$folder_result['folder_id']." AND anzeigen='1' ORDER BY folder_name ASC");
				while ($sub_result = $db->fetch_array($sub_sql))
				{
					$this->get_folder($project_id, $sub_result['folder_id']);
				}
			}
		}

		public function get_folder_pfad_from_project($id)
		{
			return $this->get_folder_pfad($id);
		}

		public function get_folder_pfad_from_folder($id)
		{
			return $this->get_folder_pfad(0, $id);
		}

		public function get_folder_pfad_from_file($id)
		{
			return $this->get_folder_pfad(0, 0, $id);
		}

		public function get_folder_pfad_from_link($id)
		{
			return $this->get_folder_pfad(0, 0, 0, $id);
		}

		private function get_folder_pfad($project_id, $folder_id = 0, $file_id = 0, $link_id = 0, $pfad = '')
		{
			$cdb = new MySql;

			if (!empty($project_id))
			{
				$sql = $cdb->select('SELECT projekt_name FROM fom_projekte WHERE projekt_id='.$project_id);
				$result = $cdb->fetch_array($sql);

				return $result['projekt_name'].'/ '.$pfad;
			}
			elseif (!empty($folder_id))
			{
				$sql = $cdb->select('SELECT projekt_id, folder_name, ob_folder FROM fom_folder WHERE folder_id='.$folder_id);
				$result = $cdb->fetch_array($sql);

				$pfad = $result['folder_name'].'/ '.$pfad;

				if (!empty($result['ob_folder']))
				{
					return $this->get_folder_pfad(0, $result['ob_folder'], 0, 0, $pfad);
				}
				else
				{
					return $this->get_folder_pfad($result['projekt_id'], 0, 0, 0, $pfad);
				}
			}
			elseif (!empty($file_id))
			{
				$sql = $cdb->select('SELECT folder_id, org_name FROM fom_files WHERE file_id='.$file_id);
				$result = $cdb->fetch_array($sql);

				$pfad = $result['org_name'];
				return $this->get_folder_pfad(0, $result['folder_id'], 0, 0, $pfad);
			}
			elseif (!empty($link_id))
			{
				$sql = $cdb->select('SELECT folder_id, link FROM fom_link WHERE link_id='.$link_id);
				$result = $cdb->fetch_array($sql);

				$pfad = $result['link'];
				return $this->get_folder_pfad(0, $result['folder_id'], 0, 0, $pfad);
			}
		}
	}
?>