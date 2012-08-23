<?php
include("../lang/eng/dates.inc"); // <-- do not delete
/********************************************************
	lang/jp/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/
$lang_months[1] = "１月";
$lang_months[2] = "２月";
$lang_months[3] = "３月";
$lang_months[4] = "４月";
$lang_months[5] = "５月";
$lang_months[6] = "６月";
$lang_months[7] = "７月";
$lang_months[8] = "８月";
$lang_months[9] = "９月";
$lang_months[10] = "１０月";
$lang_months[11] = "１１月";
$lang_months[12] = "１２月";

$lang_datetime["short_mon"] = $lang_months;			

$lang_datetime["dsow"] = array ("日","月","火","水","木","金","土");
$lang_datetime["dsowl"] = array ("日","月","火","水","木","金","土");
$lang_datetime["dsow_short"] = array ("日","月","火","水","木","金","土");

$lang_datetime["today"] = "今日 %t";
$lang_datetime["lastweek"] = "%t (%w)";
$lang_datetime["thisyear"] = "%m月%d日";
$lang_datetime["prevyears"] = "%y年%m月%d日";
$lang_datetime["monthyear"] = "%y年%m";
$lang_datetime["dsow_short"] = $lang_datetime["dsow"];
$lang_datetime["verbal"] = "%y年%m%d日";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%m%d日";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 12;			// 12 or 24
$lang_datetime["ampm"]["am"] = "am";
$lang_datetime["ampm"]["pm"] = "pm";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";

?>