var LEFT = 100;
var TOP = 200;
function open_calendar(form_name,ipt,language_id_int)
{
	var INFO = window.open("../inc/calendar.php?formname="+form_name+"&ipt="+ipt+"&value="+document.getElementById(ipt).value+"&language_id="+language_id_int, ipt, "location=no,scrollbars=no,resizable=yes,toolbar=no,status=no,menubar=no,width=300,height=200,left="+LEFT+",top="+TOP);
	INFO.focus();
	LEFT = LEFT + 20;
	TOP = TOP + 20;
}
function switch_style(stylename, ID)
{
	document.getElementById(ID).className = stylename;
}

function show_date(text)
{
	document.getElementById("show_date").firstChild.nodeValue = text;
}