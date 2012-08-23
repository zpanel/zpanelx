<?php
/////////////////////////////////////////////////////////
//	
//	source/edit_calendar.php
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE: source/edit_calendar.php
	PURPOSE:
		Provide an interface for viewing/adding/updating calendar items.
	PRE-CONDITIONS:
		$user - Session ID
		[$edit] - $id of item to modify or update (-1 means "new")
	POST-CONDITIONS:
		POST's data to calendar.php, which makes the requested changes.
	COMMENTS:
		This program is essentially a wrapper/shell for other scripts that provide
		actual functionality.

********************************************************/

function ShowTimeWiget($hour_name, $hour, $minute_name, $minute){
	global $my_prefs, $lang_datetime;
	
	$system = $my_prefs["clock_system"];
	$ampm = $lang_datetime["ampm"];
	$format = $lang_datetime["hour_format"];
	
	echo "<select name=\"$hour_name\">\n";
	for ($i=0;$i<24;$i++){
		echo "<option value=\"$i\" ".($i==$hour?"SELECTED":"").">";
		echo LangFormatIntTime($i."00", $system, $ampm, $format)."\n";
	}
	echo "</select>\n";
	echo ":<select name=\"$minute_name\">\n";
	for ($i=0;$i<60;$i=$i+5){
		echo "<option ".($i==$minute?"SELECTED":"").">".($i<10?"0":"")."$i\n";
	}
	echo "</select>\n";
}

include("../include/super2global.inc");
include("../include/header_main.inc");
include("../lang/".$my_prefs["lang"]."dates.inc");
include("../lang/".$my_prefs["lang"]."calendar.inc");

//include calendar commons
include("../include/calendar.inc");

//authenticate
include_once("../include/icl.inc");
$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
if ($conn){
	iil_Close($conn);
}else{
	echo "Authentication failed.";
	echo "</html>\n";
	exit;
}


//open backend connection
include_once("../conf/db_conf.php");
include_once("../include/idba.$DB_TYPE.inc");
include_once("../include/array2sql.inc");

$db = new idba_obj;
if (!$db->connect()){
	echo "DB connection failed.";
	exit;
}


//do calendar stuff
if (isset($date)){
	$start_year = substr($date, 0, 4);
	$start_month = substr($date, 4, 2);
	$start_day = substr($date, 6, 2);
			
	$end_year = $start_year;
	$end_month = $start_month;
	$end_day = $start_day;
}

	if ($edit>0){
		$backend_result = false;
    	$backend_query = "SELECT * FROM $DB_CALENDAR_TABLE WHERE userID='$session_dataID' and id='$edit'";;
		$backend_result = $db->query($backend_query);
				
		if (($backend_result) && ($db->num_rows($backend_result)>0)){
			$data = $db->fetch_row($backend_result); 
			extract($data);
			//while ( list($var,$val) = each($data) ) $$var=$val;
			
			$start_hour = (int)($beginTime / 100);
			$start_minute = $beginTime % 100;

			$end_hour = (int)($endTime / 100);
			$end_minute = $endTime % 100;
			
			$start_year = substr($beginDate, 0, 4);
			$start_month = substr($beginDate, 4, 2);
			$start_day = substr($beginDate, 6, 2);

			$end_year = substr($endDate, 0, 4);
			$end_month = substr($endDate, 4, 2);
			$end_day = substr($endDate, 6, 2);

			if (!empty($pattern)){
				$words = explode(" ", $pattern);
				while( list($k,$w)=each($words) ){
					if ($w[0]=="d") $dowpat=$w;
					if ($w[0]=="w") $wompat=$w;
				}

				//days of week
				$dowpat = substr($dowpat, 2);
				$dows = explode(",", $dowpat);
				while( list($k, $d)=each($dows) ) $repeat_d[$d]=1;

				//weeks in month
				$woms = explode(",", $wompat);
				while( list($k, $d)=each($woms) ) $repeat_w[$d[1]]=1;
				
				if (strpos($pattern, "m:")!==false) $repeat_monthly = 1;
				if (strpos($pattern, "y:")!==false) $repeat_yearly = 1;
			}
		}else{
			echo $error;
			if (empty($error)) "Invalid item, or access denied";
			$edit="";
		}
	}

$cal_colors = array("#990000"=>"Dark Red", "#FF0000"=>"Red", "#000099"=>"Deep Blue", "#0000FF"=>"Blue", 
					"#006600"=>"Dark Green", "#00FF00"=>"Green", "#9900FF"=>"Purple", "#00FFFF"=>"Cyan",
					"#FF6600"=>"Orange", "#FFFF00"=>"Yellow", "#FF00FF"=>"Magenta", ""=>"No color");
$cal_colors = $calStr["colors"];

?>
<table width="100%" cellpadding=2 cellspacing=0><tr bgcolor="<?php echo $my_colors["main_head_bg"]?>">
<td align=left valign=bottom>
<span class="bigTitle">
<?php echo ($edit>0?$calStr["edit_schedule"]:$calStr["add_schedule"]); ?>
</span>
</td></tr></table>	

<FORM ACTION="calendar.php" METHOD=POST>
	<input type="hidden" name="user" value="<?php echo $user; ?>">
	<input type="hidden" name="delete_item" value="<?php echo $edit; ?>">	
	<input type="hidden" name="edit" value="<?php echo $edit; ?>">
<table>
	<tr>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["title"]?></td>
		<td class=mainLight valign="top" valign="top"><input type="text" name="title" value="<?php echo $title ?>"></td>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["color"]?></td>
		<td class=mainLight valign="top" valign="top">
		<select name="color">
		<?php
		while ( list($value,$label)=each($cal_colors) ){
			echo "<option value=\"$value\" ".($value==$color?"SELECTED":"").">$label\n";
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["starts"]?></td>
		<td class=mainLight align="left" valign="top">
			<input type="text" name="start_month" value="<?php echo $start_month ?>" size=2>
			/<input type="text" name="start_day" value="<?php echo $start_day ?>" size=2>
			/<input type="text" name="start_year" value="<?php echo $start_year ?>" size=4>
		</td>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["ends"]?></td>
		<td class=mainLight align="left" valign="top">
			<input type="text" name="end_month" value="<?php echo $end_month ?>" size=2>
			/<input type="text" name="end_day" value="<?php echo $end_day ?>" size=2>
			/<input type="text" name="end_year" value="<?php echo $end_year ?>" size=4>
		</td>		
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["from"]?></td>
		<td class=mainLight align="left" valign="top">
			<?php
			ShowTimeWiget("start_hour", $start_hour, "start_minute", $start_minute);
			?>
		</td>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["until"]?></td>
		<td class=mainLight align="left" valign="top">
			<?php
			ShowTimeWiget("end_hour", $end_hour, "end_minute", $end_minute);
			?>
		</td>		
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top">&nbsp;</td>
		<td class=mainLight align="left" valign="top" colspan="3">
		<hr>
		</td>		
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["repeat_on"]?></td>
		<td class=mainLight align="left" valign="top">
			<input type="checkbox" name="repeat_d[0]" value=1 <?php echo ($repeat_d[0]?"CHECKED":"") ?>><?php echo $lang_datetime["dsowl"][0]?>
			<br><input type="checkbox" name="repeat_d[1]" value=1 <?php echo ($repeat_d[1]?"CHECKED":"") ?>><?php echo $lang_datetime["dsowl"][1]?>
			<br><input type="checkbox" name="repeat_d[2]" value=1 <?php echo ($repeat_d[2]?"CHECKED":"") ?>><?php echo $lang_datetime["dsowl"][2]?>
			<br><input type="checkbox" name="repeat_d[3]" value=1 <?php echo ($repeat_d[3]?"CHECKED":"") ?>><?php echo $lang_datetime["dsowl"][3]?>
			<br><input type="checkbox" name="repeat_d[4]" value=1 <?php echo ($repeat_d[4]?"CHECKED":"") ?>><?php echo $lang_datetime["dsowl"][4]?>
			<br><input type="checkbox" name="repeat_d[5]" value=1 <?php echo ($repeat_d[5]?"CHECKED":"") ?>><?php echo $lang_datetime["dsowl"][5]?>
			<br><input type="checkbox" name="repeat_d[6]" value=1 <?php echo ($repeat_d[6]?"CHECKED":"") ?>><?php echo $lang_datetime["dsowl"][6]?>
		</td>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["of"]?></td>
		<td class=mainLight align="left" valign="top">
			<input type="checkbox" name="repeat_w[1]" value="1" <?php echo ($repeat_w[1]?"CHECKED":"") ?>><?php echo $calStr["weeks"][1]?>
			<br><input type="checkbox" name="repeat_w[2]" value="1" <?php echo ($repeat_w[2]?"CHECKED":"") ?>><?php echo $calStr["weeks"][2]?>
			<br><input type="checkbox" name="repeat_w[3]" value="1" <?php echo ($repeat_w[3]?"CHECKED":"") ?>><?php echo $calStr["weeks"][3]?>
			<br><input type="checkbox" name="repeat_w[4]" value="1" <?php echo ($repeat_w[4]?"CHECKED":"") ?>><?php echo $calStr["weeks"][4]?>
			<br><?php echo $calStr["week_blurb"]?>
		</td>
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top">&nbsp;</td>
		<td class=mainLight align="left" valign="top" colspan="3">
		<hr>
		</td>		
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top"></td>
		<td class=mainLight align="left" valign="top" colspan="3">
			<input type="checkbox" name="repeat_monthly" value=1 <?php echo ($repeat_monthly?"CHECKED":"") ?>><?php echo $calStr["monthly"]?>
			<br><input type="checkbox" name="repeat_yearly" value=1 <?php echo ($repeat_yearly?"CHECKED":"") ?>><?php echo $calStr["yearly"]?>
		</td>
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top">&nbsp;</td>
		<td class=mainLight align="left" valign="top" colspan="3">
		<hr>
		</td>		
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["place"]?></td>
		<td class=mainLight align="left" valign="top" colspan="3">
			<textarea name="place" cols="50" rows="4"><?php echo htmlspecialchars($place) ?></textarea>
		</td>
	</tr>
	<tr>
		<td class=mainLight align="right" valign="top"><?php echo $calStr["description"]?></td>
		<td class=mainLight align="left" valign="top" colspan="3">
			<textarea name="description" cols="50" rows="6"><?php echo htmlspecialchars($description) ?></textarea>
		</td>
	</tr>
<table>
<table width="50%"><tr>
<td align="left"><input type="submit" name="edit_cal" value="<?php echo ($edit>0?$calStr["update"]:$calStr["add"])?>"></td>
<td align="right">
<?php
if ($edit>0) echo '<input type="submit" name="delete_cal" value="'.$calStr["Delete"].'">';
?>
</td>
</tr></table>
</FORM>
</body>
</html>