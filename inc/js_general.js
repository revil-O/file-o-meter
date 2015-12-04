/**
 * fuehrt ein focus auf ein inputfeld durch
 * @param event id
 * @return void
 */
function focus_input(id)
{
	document.getElementById(id).focus();
}
/**
* XML Request erstellen
*/
function GetXmlRequest()
{
	if(typeof XMLHttpRequest != 'undefined')
	{
		xmlHttp = new XMLHttpRequest();
	}
	if(!xmlHttp)
	{
		// Internet Explorer 6 und aelter
		try
		{
			xmlHttp  = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xmlHttp  = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
				xmlHttp  = null;
			}
		}
	}
	return xmlHttp;
}

/**
 * Belndet das thumbnail aus
 * @param int file_id
 * @return void
 */
function hidden_thumbnail(file_id)
{
	document.getElementById("get_thumbnail_" + file_id).style.display = "none";
}

/**
 * Gibt ein Thumbnail zu einer Datei aus
 * @param int file_id
 * @return void
 */
var last_thumbnail_id = 0;
function get_thumbnail(file_id)
{
	if (last_thumbnail_id > 0)
	{
		document.getElementById("get_thumbnail_" + last_thumbnail_id).style.display = "none";
	}
	
	last_thumbnail_id = file_id;
	//Thumbnail wurde schoneinmal geladen
	if (document.getElementById("get_thumbnail_" + file_id).innerHTML != "&nbsp;")
	{
		document.getElementById("get_thumbnail_" + file_id).style.display = "";
	}
	else
	{
		var xmlHttp = GetXmlRequest();
		if(xmlHttp)
		{
			xmlHttp.open('GET', JS_ABS_URL + 'inc/xml_get_thumbnail.php?fileid_int=' + file_id + '&' + JS_GV_INDEX + '=' + JS_GV_KEY, true);	
			
			xmlHttp.onreadystatechange = function()
			{
				if(xmlHttp.readyState == 4)
				{
					if(xmlHttp.status == 200)
					{
						var FILEDATA = xmlHttp.responseXML;
						var data = FILEDATA.getElementsByTagName('data');
						
						var thumbnail_exists = "false";
						var thumbnail_width = 0;
						var thumbnail_height = 0;
						
						//eigendlich immer 0 aber kann ja noch werden
						for (var i = 0; i < data.length; i++)
						{
							for (var n = 0; n < data[i].childNodes.length; n++)
							{
								if (data[i].childNodes[n].nodeName == "thumbnail")
								{
									thumbnail_exists = data[i].childNodes[n].firstChild.nodeValue;
								}
								if (data[i].childNodes[n].nodeName == "width")
								{
									thumbnail_width = data[i].childNodes[n].firstChild.nodeValue;
								}
								if (data[i].childNodes[n].nodeName == "height")
								{
									thumbnail_height = data[i].childNodes[n].firstChild.nodeValue;
								}
							}
						}
						
						if (thumbnail_exists != "false")
						{
							document.getElementById("get_thumbnail_" + file_id).innerHTML = '<img src="' + JS_ABS_URL + 'inc/get_thumbnail.php?fileid_int=' + file_id + '&' + JS_GV_INDEX + '=' + JS_GV_KEY + '" width="' + thumbnail_width + '" height="' + thumbnail_height + '" border="0" alt="" \/>';
							if (last_thumbnail_id == file_id)
							{
								document.getElementById("get_thumbnail_" + file_id).style.display = "";
							}
						}
					}
				}
			}
			xmlHttp.send(null);
		}
	}
}

/**
* Blendet das Link-Aktionsmenue ein und aus
* @param int file_int
*/
var last_link_action_menue = 0;
function display_link_action_menue(link_int)
{
	if(last_link_action_menue > 0)
	{
		document.getElementById("link_action_menue_" + last_link_action_menue).style.display = "none";
	}
	
	if(link_int != last_link_action_menue)
	{	
		last_link_action_menue = link_int;
		document.getElementById("link_action_menue_" + link_int).style.display = "";
		hidden_folder_action_menue();
		hidden_file_action_menue();
	}
	else
	{
		last_link_action_menue = 0;
		document.getElementById("link_action_menue_" + link_int).style.display = "none";
	}
}
/**
* Blendet das Link-Aktionsmenue aus
* @param int file_int
*/
function hidden_link_action_menue()
{
	if (last_link_action_menue > 0)
	{
		document.getElementById("link_action_menue_" + last_link_action_menue).style.display = "none";
		last_link_action_menue = 0;
	}
}

/**
* Blendet das File-Aktionsmenue ein und aus
* @param int file_int
*/
var last_file_action_menue = 0;
function display_file_action_menue(file_int)
{
	if(last_file_action_menue > 0)
	{
		document.getElementById("file_action_menue_" + last_file_action_menue).style.display = "none";
	}
	
	if(file_int != last_file_action_menue)
	{	
		last_file_action_menue = file_int;
		document.getElementById("file_action_menue_" + file_int).style.display = "";
		hidden_folder_action_menue();
		hidden_link_action_menue();
	}
	else
	{
		last_file_action_menue = 0;
		document.getElementById("file_action_menue_" + file_int).style.display = "none";
	}
}
/**
* Blendet das File-Aktionsmenue aus
* @param int file_int
*/
function hidden_file_action_menue()
{
	if (last_file_action_menue > 0)
	{
		document.getElementById("file_action_menue_" + last_file_action_menue).style.display = "none";
		last_file_action_menue = 0;
	}
}

/**
* Blendet das Folder-Aktionsmenue ein
*/
var folder_action_menue_open = false;
function display_folder_action_menue()
{
	if (!folder_action_menue_open)
	{
		folder_action_menue_open = true;
		document.getElementById("folder_action_menue").style.display = "";
		hidden_file_action_menue();
		hidden_link_action_menue();
	}
	else
	{
		folder_action_menue_open = false;
		document.getElementById("folder_action_menue").style.display = "none";
	}
}
/**
* Blendet das Folder-Aktionsmenue aus
*/
function hidden_folder_action_menue()
{
	folder_action_menue_open = false;
	document.getElementById("folder_action_menue").style.display = "none";
}

/**
* Blendet Einfuegenoptionen ein
*/
function display_paste_option()
{
	if(typeof document.getElementById("paste_option") != "undefined")
	{
		document.getElementById("paste_option").style.display = "";
		hidden_folder_action_menue();
	}
}
/**
* Blendet Einfuegenoptionen aus
*/
function hidden_paste_option()
{
	document.getElementById("paste_option").style.display = "none";
}

/**
* Gibt das Value eines Cookieindex zurueck
* @param mixed
* @return string
*/
function get_cookie_value(index)
{
	if (navigator.cookieEnabled == true)
	{
		if(document.cookie)
		{
			var cookie_data = document.cookie.split(";");
			var cookie_index = '';
			var cookie_value = '';
			
			for(var i = 0; i < cookie_data.length; i++)
			{
				cookie_index = cookie_data[i].substr(0, cookie_data[i].search('='));
				if(cookie_index == index || cookie_index == " "+index)
				{
					cookie_value = cookie_data[i].substr(cookie_data[i].search('=') + 1, cookie_data[i].length);
					
					return cookie_value;
				}
			}
			
			return "";
		}
		else
		{
			return "";
		}
	}
	else
	{
		alert("Bitte aktivieren Sie Cookies in Ihrem Internetbrowser! / Please enable cookies in your internet browser!");
	}
}

/**
* Entfernt Cookies
* @return void
*/
function rm_cookie()
{
	var time = new Date();
	var expire = time .getTime() - (3600 * 1000);
	
	document.cookie = "FOM_FileLink_string=; expires=" + expire;
	document.cookie = "FOM_FileCopy_string=; expires=" + expire;
	document.cookie = "FOM_FileMove_string=; expires=" + expire;
	document.cookie = "FOM_FolderCopy_string=; expires=" + expire;
	document.cookie = "FOM_FolderMove_string=; expires=" + expire;
}

/**
* Element mit einer JsId ein bzw. ausblenden
*/
function display_hidden(js_id)
{
	if(document.getElementById(js_id).style.display == "none")
	{
		document.getElementById(js_id).style.display = "";
	}
	else
	{
		document.getElementById(js_id).style.display = "none";
	}
}

/**
* uebergibt die File bzw. Folder ID an ein Cookie
* @param int id
* @param string typ
*/
function file_folder_id_to_cookie(id, typ)
{
	var cookie_value = get_cookie_value(typ);
	
	if(cookie_value != "")
	{
		document.cookie = typ + "=" + cookie_value + "|" + id + ";";
	}
	else
	{
		document.cookie = typ + "=" + id + ";";
	}
	
	if(typ == "FOM_FileCopy_string" || typ == "FOM_FileMove_string" || typ == "FOM_FileLink_string")
	{
		hidden_file_action_menue();
	}
	else if(typ == "FOM_FolderCopy_string" || typ == "FOM_FolderMove_string")
	{
		hidden_folder_action_menue();
	}
}

/**
* Countdownfunktion
*/
var temp_current_time = new Date();
JS_COUNTDOWN_ENDE = parseInt(temp_current_time.getTime() / 1000) + parseInt(3600);
function countdown_logout()
{
	if(typeof JS_COUNTDOWN_ENDE != "undefined")
	{
		//aktuelles Datumsobjekt
		var current_time = new Date();
		//aktueller Timestamp
		var now = parseInt(current_time.getTime()/1000);
		
		if(now < JS_COUNTDOWN_ENDE)
		{
			var out = "";
			var diff = parseInt(JS_COUNTDOWN_ENDE - now);
			var mi = parseInt(diff/60);
			if(mi < 10)
			{
				out = "0";	
			}
			out += mi+":";
			var sek= diff%60;
			
			if(sek<10)
			{
				out+="0";
			}
			out+=sek;
			
			document.getElementById("countdown").innerHTML = out;
			setTimeout("countdown_logout()",1000);
		}
		else
		{
			document.getElementById("countdown").innerHTML = "00:00";
			
			setTimeout("countdown_logout_php()",5000);
		}
	}
}
/**
 * Blendet SubDateien auf den Uebersichtsseiten ein bzw. aus
 * @param int fid
 * @return
 */
function get_subfile(fid)
{
	if (document.getElementById("subfile_jsid_" + fid).style.display == "none")
	{
		document.getElementById("subfile_jsid_" + fid).style.display = "";
	}
	else
	{
		document.getElementById("subfile_jsid_" + fid).style.display = "none";
	}
}
/**
* Logout
*/
function countdown_logout_php()
{
	window.location.replace('index.php?action=logout&' + JS_GV_INDEX + '=' + JS_GV_KEY);		
}
window.onload=countdown_logout;