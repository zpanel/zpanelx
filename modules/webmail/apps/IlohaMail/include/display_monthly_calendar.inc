<?php
/********************************************************
	include/display_monthly_calendar.inc
	
	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Display monthly calendar
	PRE-CONDITIONS:
		$month
		$year

********************************************************/

//full english months, for generating date string (excessive?)
$eng_month_a = array("January", "February", "March", "April", "May", "June", "July",
					"August", "September", "October", "November", "December");

//generate string for first day of month
$month_str = $eng_month_a[$month-1];
$date_str = "1 $month_str $year";

//convert string to timestamp
$time = strtotime($date_str);

//day of week month starts on
$dow = date("w", $time);

//number of days in month
$num_days = date("t", $time);

//format heading
$heading = $lang_datetime["monthyear"];
$heading = str_replace("%m", $lang_months[$month], $heading);
$heading = str_replace("%y", $year, $heading);

if (($month < 10) && (strlen($month)==1)) $mon_str = "0".$month;
else $mon_str = $month;
?>
<center>
<!--
<table width="80%" border="0">
	<tr>
	<td width="15%" align="center"><a href=<?php echo "calendar.php?user=$user&month=$month&year=$prev_year"?>>&lt;&lt;</a></td>
	<td width="15%" align="center"><a href=<?php echo "calendar.php?user=$user&month=$prev_month&year=".($month==1?$prev_year:$year)?>>&lt;</a></td>
	<td width="40%" align="center"><h2><?php echo $heading?></h2></td>
	<td width="15%" align="center"><a href=<?php echo "calendar.php?user=$user&month=$next_month&year=".($month==12?$next_year:$year)?>>&gt;</a></td>
	<td width="15%" align="center"><a href=<?php echo "calendar.php?user=$user&month=$month&year=$next_year"?>>&gt;&gt;</a></td>
</table>
//-->
<?php
echo "<span class=mainLight><b>".$heading."</b></span>";
?>
<table border="0" cellspacing="1" cellpadding="3" bgcolor="<?php echo $my_colors["main_head_bg"]?>">
	<?php
	echo "<tr>";
	for ($x = 0; $x<7; $x++){
		echo "<td valign=\"top\" align=\"center\" bgcolor=\"".$my_colors["main_hilite"]."\" width=\"14%\">";
		echo $lang_datetime["dsow_short"][$x];
		echo "</td>";
	}
	echo "</tr>\n";
		
	if ($dow > 0){
		echo "<tr>";
		for ($x = 0; $x < $dow; $x++){
			echo "<td valign=\"top\" bgcolor=\"".$my_colors["main_hilite"]."\" width=\"14%\">";
			echo "</td>";
		}
	}
	for ($x = 1; $x <= $num_days; $x++){
		if (($x < 10) && (strlen($x)==1)) $day_str = "0".$x;
		else $day_str = $x;
		
		$disp_date = $year.$mon_str.$day_str;
		$url = "calendar.php?user=$user&date=$disp_date";
		
		if ($dow==0) echo "<tr>";
		echo "<td valign=\"top\" bgcolor=\"".$my_colors["main_bg"]."\" width=\"14%\">";
		echo "<a href=\"$url\">".($disp_date==$date?"<b>":"")."$x".($disp_date==$date?"<b>":"")."</a>";
		echo "</td>";
		//showCell($x, $my_colors["main_bg"]);
		if ($dow==6) echo "</tr>\n";
		$dow = ($dow + 1) % 7;
	}
	if ($dow > 0){
		for ($x = $dow; $x < 7; $x++){
			echo "<td valign=\"top\" bgcolor=\"".$my_colors["main_hilite"]."\" width=\"14%\">";
			echo "</td>";
		}
		echo "</tr>\n";
	}
	?>
</table>
