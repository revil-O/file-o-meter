<?php
	/**
	 * calendar
	 * this is the basic framework of the HTML PopUp-Calendar for date-selections
	 * @package file-o-meter
	 * @subpackage inc
	 */

	define('FOM_LOGIN_SITE', 'true');
	require_once('include.php');

	//per get uebergebene sprache fuer den kalender benutzen
	if (isset($_GET['language_id']) && is_numeric($_GET['language_id']) && $_GET['language_id'] > 0)
	{
		$GLOBALS['user_language'] = $_GET['language_id'];
	}

	$cal = new Calendar;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="<?php echo '../template/'.$setup_array['template'].'/calendar.css?fom_v='.FOM_VERSION; ?>" rel="stylesheet" type="text/css" media="all">
<script type="text/javascript" src="<?php echo FOM_ABS_URL.'inc/calendar.js?fom_v='.FOM_VERSION; ?>"></script>
<script type="text/javascript">
	<!--
		function add_value(date)
		{
			window.opener.document.<?php echo $_GET['formname'].'.'.$_GET['ipt']; ?>.value = date;
			window.close();
		}
	 -->
</script>
<title><?php get_text('calendar');//Calendar ?></title>
</head>
<body>
<?php
	//wird fuer die ausgabe des bereits gewaehlten datums benoetigt
	if(!isset($_GET['iso_date']))
	{
		$_GET['iso_date'] = '';
	}
	//pruefen welcher monta/jahr gewaehlt ist
	if(isset($_POST['year']))
	{
		if(strlen($_POST['month']) == 1)
		{
			$_POST['month'] = '0'.$_POST['month'];
		}
		$selected_date = $_POST['year'].'-'.$_POST['month'].'-01';
	}
	elseif(!empty($_GET['value']))
	{
		$selected_date = $cal->check_iso_date($cal->format_date($_GET['value'],'ISO'));
		if($selected_date == '0000-00-00')
		{
			$selected_date = $cal->current_date();
		}
		$_GET['iso_date'] = $selected_date;
	}
	else
	{
		$selected_date = $cal->current_date();
	}

	//kalender klasse starten
	$result = $cal->month_detail($selected_date);
	//wird nur bei print_r am ende benoetigt
	//$result2 = $result;
?>
<form method="post" action="calendar.php<?php echo $cal->get_to_string($_GET);//alle $_GET Variablen anfuegen ?>" accept-charset="UTF-8">
	<table cellpadding="0" cellspacing="0" border="0" class="table" align="center">
		<tr>
			<td colspan="7" class="header_1"><?php echo $cal->show_current_date(); ?></td>
			<td class="header_2"><a href="javascript:close()">X</a></td>
		</tr>
		<tr>
			<td class="date_1">&nbsp;</td>
			<td colspan="2" class="date_1">
				<a href="javascript:add_value('<?php echo $cal->format_date($cal->current_date(),'FREE'); ?>')"><?php echo $cal->lng(28); ?></a>
			</td>
			<td colspan="2" class="date_1">
				<select name="month" class="select" onChange="submit()">
					<?php $cal->select_month($selected_date); ?>
				</select>
			</td>
			<td colspan="2" class="date_1">
				<select name="year" class="select" onChange="submit()">
					<?php $cal->select_year($selected_date);?>
				</select>
			</td>
			<td class="date_2">&nbsp;</td>
		</tr>
		<tr>
			<td class="name_of_day" title="<?php echo $cal->lng(13); ?>" width="32"><?php echo $cal->lng(12); ?></td>
			<td class="<?php $cal->switch_style(1,'','j');?>" title="<?php echo $cal->lng(15); ?>" width="32"><?php echo $cal->lng(14); ?></td>
			<td class="<?php $cal->switch_style(2,'','j');?>" title="<?php echo $cal->lng(17); ?>" width="32"><?php echo $cal->lng(16); ?></td>
			<td class="<?php $cal->switch_style(3,'','j');?>" title="<?php echo $cal->lng(19); ?>" width="32"><?php echo $cal->lng(18); ?></td>
			<td class="<?php $cal->switch_style(4,'','j');?>" title="<?php echo $cal->lng(21); ?>" width="32"><?php echo $cal->lng(20); ?></td>
			<td class="<?php $cal->switch_style(5,'','j');?>" title="<?php echo $cal->lng(23); ?>" width="32"><?php echo $cal->lng(22); ?></td>
			<td class="<?php $cal->switch_style(6,'','j');?>" title="<?php echo $cal->lng(25); ?>" width="32"><?php echo $cal->lng(24); ?></td>
			<td class="<?php $cal->switch_style(7,'','j');?>" title="<?php echo $cal->lng(27); ?>" width="32"><?php echo $cal->lng(26); ?></td>
		</tr>
<?php

	$day_count = 1;
	//abarbeitung der wochen je monat <tr> ausgabe
	foreach($result[0] as $v)
	{
		echo '<tr class="bg_content">';
		echo '<td class="week_number">'.$v.'</td>';
		//tage je woche ausgeben
		for($i=1;$i<=7;$i++)
		{
			//pruefen ob der Wert vorhanden ist
			if(isset($result[1][$day_count]['day_number']))
			{
				if($result[1][$day_count]['day_number'] == $i)
				{
					//bereits vorgegebene Datumsangaben aus dem input markieren
					if($result[1][$day_count]['iso_date'] == $_GET['iso_date'])
					{
						$show_date = '<strong>'.$result[1][$day_count]['day'].'</strong>';
					}
					else
					{
						$show_date = $result[1][$day_count]['day'];
					}
					//tag ausgabe
					echo '<td align="center"><div class="'.$result[1][$day_count]['style'].'" onMouseOver="switch_style(\''.$result[1][$day_count]['aktiv_style'].'\',\'n'.$result[1][$day_count]['day'].'\');show_date(\''.$result[1][$day_count]['free_date'].'\')" onMouseOut="switch_style(\''.$result[1][$day_count]['style'].'\',\'n'.$result[1][$day_count]['day'].'\')" onClick="add_value(\''.$result[1][$day_count]['free_date'].'\')" id="n'.$result[1][$day_count]['day'].'">'.$show_date.'</div></td>';
					$day_count++;
				}
				else
				{
					echo '<td align="center">&nbsp;</td>';
				}
			}
			else
			{
				echo '<td align="center">&nbsp;</td>';
			}
		}
		echo "</tr>\r\n";
	}
?>
		<tr>
			<td colspan="8" class="footer" id="show_date"><?php echo $cal->format_date($selected_date,'FREE'); ?></td>
		</tr>
	</table>
</form>
</body>
</html>