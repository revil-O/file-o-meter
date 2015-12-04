<?php
	/**
	 * this include-file manages file-downloads
	 * @package file-o-meter
	 * @subpackage inc
	 */

	//Externer Download
	if (isset($_GET['typ_string']) and $_GET['typ_string'] == 'ex' or $_GET['typ_string'] == 'backup')
	{
		define('FOM_LOGIN_SITE', 'true');
	}

	$show_header = 'n';
	require_once('include.php');

	//Externer Download
	if (isset($_GET['typ_string']) and $_GET['typ_string'] == 'ex')
	{
		if (isset($_GET['fileid_int']) and isset($_GET['key_string']))
		{
			$dl = new Download;

			$result = $dl->get_download($_GET['fileid_int'], $_GET['key_string']);

			if ($result['result'] == true)
			{
				if (isset($_GET['mime_type']) and $_GET['mime_type'] != 'octet')
				{
					header("Content-Type: ".$result['mime_type']);
				}
				else
				{
					header("Content-Type: application/octetstream");
				}
				header('Content-Disposition: attachment; filename="'.html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8').'"');
				header("Content-Transfer-Encoding: binary");
				header("Cache-Control: post-check=0, pre-check=0");
				header("Content-Length: {$result['size']}");
				readfile($result['pfad']);
			}
			else
			{
				//Vielleicht ein Sperrcounter?
				get_text(249);//The file could not be found!
			}
		}
	}
	elseif (isset($_GET['typ_string']) and $_GET['typ_string'] == 'backup')
	{
		if (isset($_GET['fileid_int']) and isset($_GET['key_string']))
		{
			$dl = new Download;

			$result = $dl->get_backup_download($_GET['fileid_int'], $_GET['key_string']);

			if ($result['result'] == true)
			{
				header("Content-Type: application/octetstream");
				header('Content-Disposition: attachment; filename="'.html_entity_decode($result['backup_name'], ENT_QUOTES, 'UTF-8').'"');
				header("Content-Transfer-Encoding: binary");
				header("Cache-Control: post-check=0, pre-check=0");
				header("Content-Length: {$result['size']}");
				readfile($result['pfad']);
			}
			else
			{
				//Vielleicht ein Sperrcounter?
				get_text(249);//The file could not be found!
			}
		}
	}
	//oeffnen eines Dokuments
	elseif (isset($_GET['typ_string']) and $_GET['typ_string'] == 'open')
	{
		if (isset($_GET['fileid_int']) and isset($_GET['pid_int']))
		{
			$sql = $db->select("SELECT t1.org_name, t1.save_name, t1.mime_type,t1.save_time, t2.typ, t2.pfad, t2.setup FROM fom_files t1
								LEFT JOIN fom_file_server t2 ON t1.file_server_id=t2.file_server_id
								WHERE file_id='".$_GET['fileid_int']."'");
			$result = $db->fetch_array($sql);

			if (!empty($result['org_name']) and !empty($result['save_name']))
			{
				if ($result['typ'] == 'local')
				{
					$save_pfad = $result['pfad'].$_GET['pid_int'].'/'.substr($result['save_time'],0,6).'/'.$result['save_name'];
					if (file_exists($save_pfad))
					{
						$size = filesize($save_pfad);
						header("Content-Type: ".$result['mime_type']);
						header('Content-Disposition: attachment; filename="'.html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8').'"');
						header("Content-Transfer-Encoding: binary");
						header("Cache-Control: post-check=0, pre-check=0");
						header("Content-Length: {$size}");
						readfile($save_pfad);
					}
					else
					{
						get_text(249);//The file could not be found!
					}
				}
				else
				{
					//FIXME: hier muesste ein FTP Download rein
				}
			}
			else
			{
				//Vielleicht ein Sperrcounter?
				get_text(249);//The file could not be found!
			}
		}
		else
		{
			//Vielleicht ein Sperrcounter?
			get_text(249);//The file could not be found!
		}
	}
	//Download einer Subversion
	elseif (isset($_GET['fileid_int']) and isset($_GET['pid_int']) and isset($_GET['typ_string']) and $_GET['typ_string'] == 'subversion')
	{
		$sql = $db->select("SELECT t1.org_name, t1.save_name, t1.save_time, t3.typ, t3.pfad, t3.setup FROM fom_file_subversion t1
							LEFT JOIN fom_files t2 ON t1.file_id=t2.file_id
							LEFT JOIN fom_file_server t3 ON t2.file_server_id=t3.file_server_id
							WHERE t1.sub_fileid='".$_GET['fileid_int']."'");
		$result = $db->fetch_array($sql);

		if (!empty($result['org_name']) and !empty($result['save_name']))
		{
			if ($result['typ'] == 'local')
			{
				$save_pfad = $result['pfad'].$_GET['pid_int'].'/'.substr($result['save_time'],0,6).'/'.$result['save_name'];
				if (file_exists($save_pfad))
				{
					$size = filesize($save_pfad);
					header("Content-Type: application/octetstream");
					header('Content-Disposition: attachment; filename="'.html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8').'"');
					header("Content-Transfer-Encoding: binary");
					header("Cache-Control: post-check=0, pre-check=0");
					header("Content-Length: {$size}");
					readfile($save_pfad);
				}
				else
				{
					get_text(249);//The file could not be found!
				}
			}
			else
			{
				//FIXME: hier muesste ein FTP Download rein
			}
		}
		else
		{
			//Vielleicht ein Sperrcounter?
			get_text(249);//The file could not be found!
		}
	}
	//File aus der Doku Downloaden
	//FIXME: Das sollte noch etwas individueller angepasst werden
	elseif (isset($_GET['fileid_int']) and isset($_GET['pid_int']))
	{
		$sql = $db->select("SELECT t1.org_name, t1.save_name, t1.save_time, t2.typ, t2.pfad, t2.setup FROM fom_files t1
							LEFT JOIN fom_file_server t2 ON t1.file_server_id=t2.file_server_id
							WHERE t1.file_id='".$_GET['fileid_int']."'");
		$result = $db->fetch_array($sql);

		if (!empty($result['org_name']) and !empty($result['save_name']))
		{
			if ($result['typ'] == 'local')
			{
				$save_pfad = $result['pfad'].$_GET['pid_int'].'/'.substr($result['save_time'],0,6).'/'.$result['save_name'];
				if (file_exists($save_pfad))
				{
					$size = filesize($save_pfad);
					header("Content-Type: application/octetstream");
					header('Content-Disposition: attachment; filename="'.html_entity_decode($result['org_name'], ENT_QUOTES, 'UTF-8').'"');
					header("Content-Transfer-Encoding: binary");
					header("Cache-Control: post-check=0, pre-check=0");
					header("Content-Length: {$size}");
					readfile($save_pfad);
				}
				else
				{
					get_text(249);//The file could not be found!
				}
			}
			else
			{
				//FIXME: hier muesste ein FTP Download rein
			}
		}
		else
		{
			//Vielleicht ein Sperrcounter?
			get_text(249);//The file could not be found!
		}
	}
	else
	{
		//Vielleicht ein Sperrcounter?
		get_text(249);//The file could not be found!
	}
?>