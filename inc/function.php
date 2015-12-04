<?php
	/**
	 * this include-file contains a collection of useful functions
	 * @package file-o-meter
	 * @subpackage inc
	 */

	/**
	 * Nur fuer testzwecke
	 * @param $var
	 * @return void
	 */
	function print_r_pre($var)
	{
		echo '<pre>';
			print_r($var);
		echo '</pre>';
	}

	/**
	 * Umgeht den Fehler der empty() Funktion beim int 0 wert
	 * @param mixed $var
	 * @return boole
	 */
	function _empty($var)
	{
		if (empty($var))
		{
			if ("$var" === "0")
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Gibt eine 1 oder zwei zurueck
	 *
	 * @param int $int
	 * @return int
	 */
	function one_or_two($int)
	{
		if ($int == 1)
		{
			return 2;
		}
		else
		{
			return 1;
		}
	}

	/**
	* Sprachen aus den Texte-Tabelle auslesen. Nur Systemtexte.
	* @param mixed $TXTID kann eine numerische ID (int) oder $TXTKEY (string) enthalten
	* @param string $RETURN
	* @param strind $RETRUNMODE
	* @param array $VARIABLE, enthaelt Werte die in den Textstring eingefuegt werden sollen
	* @return string
	* @function
	*/
	function get_text($TXTID,$RETURN='echo',$RETRUNMODE='decode_on',$VARIABLE=array())
	{
		$gtxt = new GetText;

		//check $TXTID: numeric = $TXTID, string = $TXTKEY
		if (!empty($TXTID) && !is_numeric($TXTID))
		{
			$TXTKEY = $TXTID;
			$TXTID = '';
		}
		elseif (is_numeric($TXTID))
		{
			$TXTKEY = '';
		}

		$txt = $gtxt->Get_Text($TXTID,$TXTKEY,'return',$RETRUNMODE,$VARIABLE);

		if($RETURN == 'echo')
		{
			echo $txt;
		}
		else
		{
			return $txt;
		}
	}

	/**
	 * Erstellt einen HTML String fuer die ausgabe eines Images
	 *
	 * @param string $img
	 * @param string $titel
	 * @param string $alt
	 * @param string $class
	 * @param int $border
	 * @param string $id
	 * @param string $js
	 * @return string
	 */
	function get_img($img, $titel = '', $alt = '', $class = 'image', $border = 0, $id = '', $js = '', $w = 0, $h = 0)
	{
		if (substr($img, 0, 1) == '_')
		{
			$img_pfad = FOM_ABS_PFAD.'template/'.$GLOBALS['setup_array']['template'].'/pic/';
			$img_url = FOM_ABS_URL.'template/'.$GLOBALS['setup_array']['template'].'/pic/';
		}
		else
		{
			$img_pfad = FOM_ABS_PFAD.'template/'.$GLOBALS['setup_array']['template'].'/pic/famfamfam/';
			$img_url = FOM_ABS_URL.'template/'.$GLOBALS['setup_array']['template'].'/pic/famfamfam/';
		}

		if (file_exists($img_pfad.$img))
		{
			$img_size_array = @getimagesize($img_pfad.$img);

			$att_string = '';

			$att_string .= ' alt="'.$alt.'"';

			if (!empty($titel))
			{
				$att_string .= ' title="'.$titel.'"';
			}

			if (!empty($class))
			{
				$att_string .= ' class="'.$class.'"';
			}
			if (is_numeric($border))
			{
				$att_string .= ' border="'.$border.'"';
			}
			if (!empty($id))
			{
				$att_string .= ' id="'.$id.'"';
			}
			if (!empty($js))
			{
				$att_string .= ' '.$js;
			}
			if ($w == 0)
			{
				return '<img src="'.$img_url.$img.'" '.$img_size_array[3].$att_string.' />';
			}
			else
			{
				return '<img src="'.$img_url.$img.'" '.'width="'.$w.'" height="'.$h.'"'.$att_string.' />';
			}
		}
		else
		{
			return '<img src="'.FOM_ABS_URL.'template/'.$GLOBALS['setup_array']['template'].'/pic/_spacer.gif" width="16" height="16" alt="'.get_text(224, 'return').'" />';//Image not found
		}
	}

	/**
	 * Erstellt das Aktionsmenue fuer Links
	 * @param int $link_id
	 * @param int $project_id
	 * @param int $folder_id
	 * @param string $link
	 * @param int $file_id
	 * @return string
	 */
	function link_action_menue($link_id, $project_id, $folder_id, $link, $file_id = 0)
	{
		$style = one_or_two(2);
		$return_string = '<div onclick="display_link_action_menue('.$link_id.');">';
		$return_string .= 	get_img('brick_add.png', '', '', 'image_button');
		$return_string .= 	'<div id="link_action_menue_'.$link_id.'" class="file_action_menue" style="display:none;">';


		//Interner link
		if ($file_id > 0)
		{
			$cdb = new MySql();

			$sql = $cdb->select("SELECT t1.folder_id, t2.projekt_id FROM fom_files t1
								LEFT JOIN fom_folder t2 ON t1.folder_id=t2.folder_id
								WHERE t1.file_id=$file_id");
			$result = $cdb->fetch_array($sql);

			$return_string .= '<div class="action_menue_line_'.$style.'"><a href="'.FOM_ABS_URL.'folder/index.php'.$GLOBALS['gv']->create_get_string('?pid_int='.$result['projekt_id'].'&amp;fid_int='.$result['folder_id'].'&amp;fileinc=').'">'.get_img('link_go.png', get_text(301, 'return')).' '.get_text(301, 'return').'</a></div>';//Oeffnen
			$style = one_or_two($style);

			//Downloadlink erstellen
			if ($GLOBALS['ac']->chk('link', 'dl', $link_id))
			{
				$return_string .= '<div class="action_menue_line_'.$style.'"><a href="'.FOM_ABS_URL.'inc/download.php'.$GLOBALS['gv']->create_get_string('?fileid_int='.$file_id.'&amp;pid_int='.$project_id).'">'.get_img('disk.png', get_text('download', 'return')).' '.get_text('download', 'return').'</a></div>';//Download
				$style = one_or_two($style);
			}

			//Loeschen
			if ($GLOBALS['ac']->chk('link', 'd', $link_id))
			{
				$return_string .= '<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=del_link&amp;linkid_int='.$link_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('link_delete.png', get_text('del', 'return')).' '.get_text('del', 'return').'</a></div>';//Lleschen
				$style = one_or_two($style);
			}
		}
		//Externer Link
		elseif (!empty($link))
		{
			$return_string .= '<div class="action_menue_line_'.$style.'"><a href="'.$link.'" target="_blank">'.get_img('link_go.png', get_text(301, 'return')).' '.get_text(301, 'return').'</a></div>';//Oeffnen
			$style = one_or_two($style);
			if ($GLOBALS['ac']->chk('link', 'w', $link_id))
			{
				$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=edit_link&amp;linkid_int='.$link_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('link_edit.png', get_text('edit', 'return')).' '.get_text('edit', 'return').'</a></div>';//Bearbeiten
				$style = one_or_two($style);

				if ($GLOBALS['ac']->chk('link', 'd', $link_id))
				{
					$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=del_link&amp;linkid_int='.$link_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('link_delete.png', get_text('del', 'return')).' '.get_text('del', 'return').'</a></div>';//Loeschen
					$style = one_or_two($style);
				}
				//Datei-Zugriffsrechte bearbeiten
				if ($GLOBALS['ac']->chk('link', 'as', $link_id))
				{
					$return_string .= '<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=edit_as&amp;linkid_int='.$link_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('eye.png', get_text('access_as', 'return')).' '.get_text('access_as', 'return').'</a></div>';//Edit access control
					$style = one_or_two($style);
				}
			}
		}


		$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="javascript:hidden_link_action_menue();">'.get_img('cancel.png', get_text('close', 'return')).' '.get_text('close', 'return').'</a></div>';//Close

		$return_string .= 	'</div>
						</div>';
		return $return_string;
	}

	/**
	 * Erstellt das Aktionsmenue fuer eine Datei
	 * @param int $file_id
	 * @param int $project_id
	 * @param int $folder_id
	 * @param array $other, sonstige einstellungen
	 * @return string
	 */
	function file_action_menue($file_id, $project_id, $folder_id, $other = array())
	{
		$cdb = new MySql();

		//Ausckeckstatus Pruefen
		$check_sql = $cdb->select('SELECT user_id FROM fom_file_lock WHERE file_id='.$file_id);
		$check_result = $cdb->fetch_array($check_sql);

		$edit_file = true;
		$edit_check_status = false;
		$file_is_checked_out = false;

		//Datei ist ausgecheckt
		if (isset($check_result['user_id']) and $check_result['user_id'] > 0)
		{
			//Die Datei ist von jemanden ausgecheckt
			$file_is_checked_out = true;

			//Der Aktuelle User hat die Datei nicht ausgecheckt
			if ($check_result['user_id'] != USER_ID)
			{
				$edit_file = false;
			}
			elseif ($check_result['user_id'] == USER_ID and $GLOBALS['ac']->chk('file', 'w', $file_id) == true)
			{
				//Der Aktuelle User hat die Datei ausgcheckt kann sie also auch weider einchecken
				$edit_check_status = true;
			}
		}
		//Datei ist nicht ausgecheckt kann also von jedem mit schreibrechten ausgecheckt werden
		elseif ($GLOBALS['ac']->chk('file', 'w', $file_id) == true)
		{
			$edit_check_status = true;
		}
		//Der User hat die Rechte den Auscheckstatus zu ueberschreiben
		if ($GLOBALS['ac']->chk('file', 'ocf', $file_id))
		{
			$edit_check_status = true;
		}

		$style = one_or_two(2);

		$return_string = '<div onclick="display_file_action_menue('.$file_id.');">';
		$return_string .= 	get_img('brick_add.png', '', '', 'image_button');
		$return_string .= 	'<div id="file_action_menue_'.$file_id.'" class="file_action_menue" style="display:none;">';


		//Bei Suchergebnissen Link fuer Verzeichnisoeffnen anzeigen
		if (isset($other['filter_subfolder_int']) and $other['filter_subfolder_int'] == 1)
		{
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fid_int='.$folder_id.'&amp;pid_int='.$project_id.'&amp;fileinc=').'">'.get_img('folder_go.png', get_text(225, 'return')).' '.get_text(225, 'return').'</a></div>';//Open folder
			$style = one_or_two($style);
		}

		//Download
		$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="'.FOM_ABS_URL.'inc/download.php'.$GLOBALS['gv']->create_get_string('?fileid_int='.$file_id.'&amp;pid_int='.$project_id).'">'.get_img('disk.png', get_text('download', 'return')).' '.get_text('download', 'return').'</a></div>';//Download
		$style = one_or_two($style);

		//Downloadlink erstellen
		if ($GLOBALS['ac']->chk('file', 'dl', $file_id))
		{
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=add_download&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('disk_multiple.png', get_text('access_dl', 'return')).' '.get_text('access_dl', 'return').'</a></div>';//Create downloadlink
			$style = one_or_two($style);
		}

		//Datei anzeigen (Leserechte)
		if ($GLOBALS['ac']->chk('file', 'r', $file_id))
		{
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=show_file&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('page.png', get_text(276, 'return')).' '.get_text(276, 'return').'</a></div>';//Show file information
			$style = one_or_two($style);
		}

		//Datei bearbeiten ist erlaubt
		if ($edit_file === true)
		{
			if ($GLOBALS['ac']->chk('file', 'w', $file_id))
			{
				//Datei bearbeiten
				$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=edit_file&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('page_edit.png', get_text(161, 'return')).' '.get_text(161, 'return').'</a></div>';//Edit file
				$style = one_or_two($style);
				if ($GLOBALS['ac']->chk('file', 'd', $file_id))
				{
					//Datei loeschen
					$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=del_file&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('page_delete.png', get_text(157, 'return')).' '.get_text(157, 'return').'</a></div>';//Delete file
					$style = one_or_two($style);
				}

				//Subdateien anlegen
				if (!isset($other['file_type']) or $other['file_type'] == 'PRIMARY')
				{
					$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=add_subfile&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('page_refresh.png', get_text(156, 'return')).' '.get_text(156, 'return').'</a></div>';//Add subfile
					$style = one_or_two($style);
				}
			}

			//Datei Kopieren / Verschieben
			if (!isset($other['file_type']) or $other['file_type'] == 'PRIMARY')
			{
				$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="javascript:file_folder_id_to_cookie('.$file_id.',\'FOM_FileCopy_string\')">'.get_img('page_copy.png', get_text(226, 'return')).' '.get_text(226, 'return').'</a></div>';//Copy file
				$style = one_or_two($style);
				$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="javascript:file_folder_id_to_cookie('.$file_id.',\'FOM_FileMove_string\')">'.get_img('page_go.png', get_text(227, 'return')).' '.get_text(227, 'return').'</a></div>';//Move file
				$style = one_or_two($style);
			}
			//Verknuepfung erstellen
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="javascript:file_folder_id_to_cookie('.$file_id.',\'FOM_FileLink_string\')">'.get_img('link_add.png', get_text(288, 'return')).' '.get_text(288, 'return').'</a></div>';//Link hinzufuegen
			$style = one_or_two($style);
		}

		//Datei ist nicht ausgecheckt und der user hat schreibrechte
		//Auscheckicon anzeigen
		if ($file_is_checked_out === false and $GLOBALS['ac']->chk('file', 'w', $file_id) == true)
		{
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=checkout_file&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('lock.png', get_text(171, 'return')).' '.get_text(171, 'return').'</a></div>';//Checkout file
			$style = one_or_two($style);
		}
		//Datei wurde ausgecheckt
		elseif ($file_is_checked_out === true)
		{
			//User darf Datei einchecken
			if ($edit_check_status === true)
			{
				$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=checkin_file&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('lock_open.png', get_text(172, 'return')).' '.get_text(172, 'return').'</a></div>';//Checkin file
				$style = one_or_two($style);
			}
			//anzeigen das diese Datei ausgecheckt wurde und nicht bearbeitet werden kann
			else
			{
				$return_string .= 	'<div class="action_menue_line_'.$style.'">'.get_img('lock_delete.png', get_text(105, 'return')).' '.get_text(105, 'return').'</div>';//The file was checked out.
				$style = one_or_two($style);
			}
		}

		//Dateiversion anzeigen
		if ($GLOBALS['ac']->chk('file', 'vo', $file_id))
		{
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=show_version_history&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('book_open.png', get_text(168, 'return')).' '.get_text(168, 'return').'</a></div>';//Version history
			$style = one_or_two($style);
		}
		//Dateiversion anlegen
		if ($GLOBALS['ac']->chk('file', 'va', $file_id))
		{
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=add_fileversion&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('page_add.png', get_text('access_va', 'return')).' '.get_text('access_va', 'return').'</a></div>';//Add version
			$style = one_or_two($style);
		}

		//Datei-Zugriffsrechte bearbeiten
		if ($GLOBALS['ac']->chk('file', 'as', $file_id))
		{
			$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=edit_as&amp;fileid_int='.$file_id.'&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('eye.png', get_text('access_as', 'return')).' '.get_text('access_as', 'return').'</a></div>';//Edit access control
			$style = one_or_two($style);
		}


		$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="javascript:hidden_file_action_menue();">'.get_img('cancel.png', get_text('close', 'return')).' '.get_text('close', 'return').'</a></div>';//Close

		$return_string .= 	'</div>
						</div>';
		return $return_string;
	}

	/**
	 * Erstellt das Aktionsmenue fuer ein Verzeichnis
	 * @param int $project_id
	 * @param int $folder_id
	 * @param array $setup_array
	 * @return string
	 */
	function folder_action_menue($project_id, $folder_id, $setup_array)
	{
		$cdb = new MySql;
		$style = one_or_two(2);
		$return_string = '';

			$return_string = '<div onclick="display_folder_action_menue();">';
			$return_string .= 	get_img('brick_add.png', '', '', 'image_button');
			$return_string .= 	'<div id="folder_action_menue" class="folder_action_menue" style="display:none;">';
			//Verzeichnis durchsuchen
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=search&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('zoom.png', get_text('search', 'return')).' '.get_text('search', 'return').'</a></div>';//Search
			$style = one_or_two($style);

			//Verzeichnis anlegen
			if ($folder_id > 0)
			{
				if ($GLOBALS['ac']->chk('folder', 'w', $folder_id))
				{
					$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=add_folder&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('folder_add.png', get_text(150, 'return')).' '.get_text(150, 'return').'</a></div>';//Create folder
					$style = one_or_two($style);
				}
			}
			elseif ($project_id > 0)
			{
				if ($GLOBALS['ac']->chk('project', 'w', $project_id))
				{
					$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=add_folder&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('folder_add.png', get_text(150, 'return')).' '.get_text(150, 'return').'</a></div>';//Create folder
					$style = one_or_two($style);
				}
			}
			if (isset($setup_array['paste_job']) and $setup_array['paste_job'] == true)
			{
				$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="javascript:display_paste_option();">'.get_img('page_white_paste.png', get_text('paste', 'return')).' '.get_text('paste', 'return').'</a></div>';//Paste
				$style = one_or_two($style);
			}
			if ($folder_id > 0)
			{
				//Verzeichnis bearbeiten
				if ($GLOBALS['ac']->chk('folder', 'w', $folder_id))
				{
					$return_string .=	 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=edit_folder&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('folder_edit.png', get_text(162, 'return')).' '.get_text(162, 'return').'</a></div>';//Edit folder
					$style = one_or_two($style);
					$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=add_newfile&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('add.png', get_text(232, 'return')).' '.get_text(232, 'return').'</a></div>';//Fileupload
					$style = one_or_two($style);

					if (plupload_exists())
					{
						$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=multiupload&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('arrow_join.png', get_text(232, 'return')).' '.get_text(344, 'return').'</a></div>';//Multiple fileupload
						$style = one_or_two($style);
					}

					$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=add_newlink&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('link_add.png', get_text(288, 'return')).' '.get_text(288, 'return').'</a></div>';//Link hinzufuegen
					$style = one_or_two($style);

					if ($GLOBALS['ac']->chk('folder', 'd', $folder_id))
					{
						$return_string .=	 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=del_folder&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('folder_delete.png', get_text(159, 'return')).' '.get_text(159, 'return').'</a></div>';//Delete folder
						$style = one_or_two($style);
					}
					$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="javascript:file_folder_id_to_cookie('.$folder_id.',\'FOM_FolderCopy_string\');">'.get_img('folder_page.png', get_text(229, 'return')).' '.get_text(229, 'return').'</a></div>';//Copy folder
					$style = one_or_two($style);
					$return_string .=	 	'<div class="action_menue_line_'.$style.'"><a href="javascript:file_folder_id_to_cookie('.$folder_id.',\'FOM_FolderMove_string\');">'.get_img('folder_go.png', get_text(230, 'return')).' '.get_text(230, 'return').'</a></div>';//Move folder
					$style = one_or_two($style);

					if ($GLOBALS['ac']->chk('folder', 'as', $folder_id))
					{
						$return_string .= 		'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=edit_as&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('eye.png', get_text('access_as', 'return')).' '.get_text('access_as', 'return').'</a></div>';//Edit access control
						$style = one_or_two($style);
					}
				}
			}

			if (importable_data_exists() === true)
			{
				//Datenimport
				if ($GLOBALS['ac']->chk('folder', 'di', $folder_id))
				{
					$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=import_data&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('database_go.png', get_text('access_di', 'return')).' '.get_text('access_di', 'return').'</a></div>';//Data import
					$style = one_or_two($style);
				}
			}
			//Datenexport
			if ($GLOBALS['ac']->chk('folder', 'de', $folder_id) and file_exists(FOM_ABS_PFAD.'files/imex/'.USER_ID.'/'))
			{
				$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=export_data&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('database_save.png', get_text('access_de', 'return')).' '.get_text('access_de', 'return').'</a></div>';//Data export
				$style = one_or_two($style);
			}

			if ($folder_id > 0)
			{
				//Folder Save (zip and download)
				$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=folder_download&amp;pid_int='.$project_id.'&amp;fid_int='.$folder_id).'">'.get_img('disk_multiple.png', get_text(377, 'return')).' '.get_text(377, 'return').'</a></div>';//Verzeichnis Download
				$style = one_or_two($style);
			}
			//Nur auf Projektebene
			elseif ($project_id > 0)
			{
				if ($GLOBALS['ac']->chk('project', 'mn', $project_id))
				{
					$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="index.php'.$GLOBALS['gv']->create_get_string('?fileinc=mail_notification&amp;pid_int='.$project_id).'">'.get_img('email.png', get_text(403, 'return')).' '.get_text(403, 'return').'</a></div>';//E-Mailbenachrichtigung
					$style = one_or_two($style);
				}
			}
			//PDF Report erstellen
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="../report/pdf_filelist.php'.$GLOBALS['gv']->create_get_string('?pid_int='.$project_id.'&amp;fid_int='.$folder_id).'" target="_blank">'.get_img('page_white_acrobat.png', get_text('327', 'return')).' '.get_text('327', 'return').'</a></div>';//Dateiliste erstellen
			$style = one_or_two($style);
			$return_string .= 	'<div class="action_menue_line_'.$style.'"><a href="javascript:hidden_folder_action_menue();">'.get_img('cancel.png', get_text('close', 'return')).' '.get_text('close', 'return').'</a></div>';//Close
			$return_string .= '	</div>
								</div>';
		return $return_string;
	}

	/**
	 * Prueft ob importierbare Daten vorhanden sind
	 * @return boole
	 */
	function importable_data_exists()
	{
		$folder = FOM_ABS_PFAD.'files/imex/'.USER_ID.'/';

		if (file_exists($folder) and is_dir($folder))
		{
			if ($h = opendir($folder))
			{
				$count = 0;
				while (($f = readdir($h)) !== false)
				{
					if ($f != '.' and $f != '..')
					{
						$count++;
					}
				}

				if ($count > 0)
				{
					return true;
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
		else
		{
			return false;
		}
	}

	/**
	 * Prueft ob plupload zur Verfügung steht
	 * @return boole
	 */
	function plupload_exists()
	{
		if (file_exists(FOM_ABS_PFAD.'inc/class/plupload'))
		{
			if ($h = opendir(FOM_ABS_PFAD.'inc/class/plupload'))
			{
				$file_count = 0;
				while (false !== ($file = readdir($h)))
				{
					if ($file != "." and $file != "..")
					{
						$file_count++;
					}
				}

				if ($file_count > 0)
				{
					return true;
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
		else
		{
			return false;
		}
	}

	/**
	* Prueft ob eine Variable einen Wert enthaelt oder leer ist. Entgegen der empty() Funktion wird bei einem Wert von 0 True zurueckgeliefert
	* @param mixed $V
	* @return boole
	* @function
	*/
	function void($V)
	{
		$V = trim($V);

		if(empty($V))
		{
			if($V == 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	/**
	* Blaetternfunktion
	* @param int $current_page, Aktuelle Seitenzahl
	* @param int $data_count, Gesamtzahl aller Datensaetze
	* @param int $page_max, Anzahl an ausgaben pro Seite
	* @param array $variable, Array mit Variablen die als Get an die links angehaengt werden sollen z.B. $_POST
	* @param string $path, Pfad wo die einzelnen Links hingehen sollen
	* @return array
	*/
	function page_scroll($current_page, $data_count, $page_max, $variable, $path)
	{
		$return = array();
		$return['txt'] = '';
		//variablen in get string aendern
		if(is_array($variable) and count($variable) > 0)
		{
			$get_variablen = '';
			foreach($variable as $i => $v)
			{
				if(!empty($v) and $i != 'submit' and $i != 'blseite')
				{
					if(empty($get_variablen))
					{
						$get_variablen = '?'.$i.'='.$v;
					}
					else
					{
						$get_variablen .= '&amp;'.$i.'='.$v;
					}
				}
			}
		}

		//gesamtseiten zahl ermitteln
		if($data_count > 0)
		{
			$gesamt_seiten_int = ceil($data_count / $page_max);
		}
		else
		{
			$gesamt_seiten_int = 0;
		}
		if($gesamt_seiten_int == 0)
		{
			$gesamt_seiten_int = 1;
			$startergebnis = 0;
		}
		else
		{
			$startergebnis = $current_page * $page_max - $page_max + 1;
		}

		//ergebnisse der seite errechnen
		$endergebnis = $current_page * $page_max;
		if($endergebnis > $data_count)
		{
			$endergebnis = $data_count;
		}

		$return['txt'] .= get_text('turnover_page', 'return');//Page
		$return['txt'] .= '&nbsp;'.$current_page.'&nbsp;';
		$return['txt'] .= get_text('turnover_of', 'return');//of
		$return['txt'] .= '&nbsp;'.$gesamt_seiten_int.'&nbsp;-&nbsp;';
		$return['txt'] .= get_text('turnover_results', 'return');//Results
		$return['txt'] .= '&nbsp;'.$startergebnis.'&nbsp;';
		if($startergebnis > 0)
		{
			$return['txt'] .= get_text('turnover_to', 'return');//bis
			$return['txt'] .= '&nbsp;'.$endergebnis.'&nbsp;';
			$return['txt'] .= get_text('turnover_of', 'return');//of
			$return['txt'] .= '&nbsp;'.$data_count.'<br />';
		}

		if(isset($get_variablen) and !empty($get_variablen))
		{
			$get_variablen .= '&amp;blseite=';
		}
		else
		{
			$get_variablen = '?blseite=';
		}

		//erste seite und eine seite zurueck
		if($current_page != 1)
		{
			//get string fuer link bearbeiten
			$letzte_seite = $current_page - 1;
			$return['txt'] .= '<a href="'.$path.$GLOBALS['gv']->create_get_string($get_variablen.'1').'"><strong>['.get_text('turnover_first', 'return').']</strong></a>';//first page
			$return['txt'] .= '<a href="'.$path.$GLOBALS['gv']->create_get_string($get_variablen.$letzte_seite).'">&nbsp;<strong>['.get_text('turnover_prev', 'return').']</strong></a>';//previous page
		}
		//blaettern link
		$anzahl_seiten_vor_und_nach_aktueller_seite = 5;
		$start_seiten_anzeige = $current_page - $anzahl_seiten_vor_und_nach_aktueller_seite;
		$ende_seiten_anzeige = $current_page + $anzahl_seiten_vor_und_nach_aktueller_seite;

		if($gesamt_seiten_int > 1)
		{
			$for_anzahl = 1;
			$start_punkt = 'j';
			$end_punkte = 'j';
			for($i = 1; $i <= $gesamt_seiten_int; $i++)
			{
				if($i < $start_seiten_anzeige and $start_punkt == 'j')
				{
					$return['txt'] .= ' ...';
					$start_punkt = 'n';
				}
				if($i >= $start_seiten_anzeige and $i <= $ende_seiten_anzeige)
				{
					if($for_anzahl == 1)
					{
						$return['txt'] .= '&nbsp;';
					}
					if($i == $current_page)
					{
						$return['txt'] .= '<strong><u>'.$i.'</u></strong> ';
					}
					else
					{
						$return['txt'] .= '<a href="'.$path.$GLOBALS['gv']->create_get_string($get_variablen.$i).'"><strong>'.$i.'</strong></a> ';
					}
					$for_anzahl++;
				}
				if($i > $ende_seiten_anzeige and $end_punkte == 'j')
				{
					$return['txt'] .= '... ';
					$end_punkte = 'n';
				}
			}

		}
		//letzte seite und eine seite weiter
		if($current_page != $gesamt_seiten_int)
		{
			$eine_seite_weiter = $current_page + 1;
			$return['txt'] .= '<a href="'.$path.$GLOBALS['gv']->create_get_string($get_variablen.$eine_seite_weiter).'"><strong>['.get_text('turnover_next', 'return').']</strong></a>';//next page
			$return['txt'] .= '&nbsp;<a href="'.$path.$GLOBALS['gv']->create_get_string($get_variablen.$gesamt_seiten_int).'"><strong>['.get_text('turnover_last', 'return').']</strong></a><br />';//last page
		}
		if($gesamt_seiten_int > 1 and $current_page == $gesamt_seiten_int)
		{
			$return['txt'] .= '<br />';
		}
		elseif($data_count == 0)
		{
			$return['txt'] .= '<br />';
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//LIMIT erstellen
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$limit_start = $current_page * $page_max - $page_max;
		$return['limit'] = " LIMIT $limit_start, $page_max";

		return $return;
	}
?>