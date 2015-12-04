/**
* Upload
* 
* Gibt die Aktuelle groesse der Datei zurueck die gerade Hochgeladen wird.
* !!Achtung es muss einen html tag mit der id="ShowUploadStatus" existieren!!
* @version $Id
*/

/**
* Speichert die Dateigroesse. Wird fuer die Berechnung der Uploadgeschwindigkeit benoetigt.
*/
LAST_FILESIZE = 0;
/**
* Startet die Function GetUploadFileSize im 1sek takt. Es muss nur die Function gestartet werden alle nachfolgenden Function werden von dieser gestartet. Aufruf per onSubmit='StartUploadFileProzess'.
* @return void
* @function
*/
function StartUploadFileProzess()
{
	window.setInterval("GetUploadFileSize()",1000);	
}
/**
* Gibt die aktuelle Dateigroesse und Uploadgeschwindigkeit zurueck
* !! Achtung Variable JS_ABS_URL kommt von Extern !!
* @return string
* @function
*/
function GetUploadFileSize()
{
	var xmlHttp = null;
	var tmp_filesize = 0;
	var uploadspeed = "";
	
	// Mozilla, Opera, Safari sowie Internet Explorer 7
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
	if(xmlHttp)
	{
		xmlHttp.open('GET',JS_ABS_URL + 'inc/xml_get_upload_file_size.php?' + JS_GV_INDEX + '=' + JS_GV_KEY, true);	
		
		xmlHttp.onreadystatechange = function()
		{
			if(xmlHttp.readyState == 4)
			{
				//aktuelle Dateigroesse auslesen
				tmp_filesize = parseInt(Get_XML_FileSize(xmlHttp.responseXML));
				
				if(tmp_filesize > 0)
				{
					if(LAST_FILESIZE > 0)
					{
						//Uplloadspeed berechnen
						uploadspeed = parseInt((tmp_filesize - LAST_FILESIZE) * 8);
						uploadspeed = uploadspeed / 1024;
						uploadspeed = uploadspeed * 10;
						uploadspeed = Math.round(uploadspeed);
						uploadspeed = uploadspeed / 10;
						uploadspeed = " - " + uploadspeed + " kbit/s";
					}
					
					var size_out = RoundFilesize(tmp_filesize);
					document.getElementById("ShowUploadStatus").innerHTML = size_out + uploadspeed;
					
					LAST_FILESIZE = tmp_filesize;
				}
				else
				{
					document.getElementById("ShowUploadStatus").innerHTML = "&nbsp;";	
				}
			}
		}
		xmlHttp.send(null);
	}
}
/**
* Rundet die Dateigroesse und wechselt je nach groesse die Einheit.
* @return string
* @function
*/
function RoundFilesize(SIZE)
{
	var einheit = "";
	if(SIZE > 1048576)
	{
		einheit = " MB";
		SIZE = SIZE / 1024 / 1024;
	}
	else
	{
		if(SIZE > 1024)
		{
			einheit = " KB";
			SIZE = SIZE / 1024;
		}
		else
		{
			einheit = " Byte";
		}
	}
	
	SIZE = SIZE * 100;
	SIZE = Math.round(SIZE);
	SIZE = SIZE / 100;
	return SIZE + einheit;
}
/**
* Liest die Daten aus der XML Datei.
* @return int
* @function
*/
function Get_XML_FileSize(FILEDATA)
{
	var data = FILEDATA.getElementsByTagName('data');
	var value = "";
	with(data[0].childNodes[0])
	{
		if(nodeName == "value")
		{
			value = firstChild.nodeValue;
		}
	}
	return value;
}