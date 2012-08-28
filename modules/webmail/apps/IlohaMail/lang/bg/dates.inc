<?php
/********************************************************
	lang/eng/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "Януари";
$lang_months[2] = "Февруари";
$lang_months[3] = "Март";
$lang_months[4] = "Април";
$lang_months[5] = "Май";
$lang_months[6] = "Юни";
$lang_months[7] = "Юли";
$lang_months[8] = "Август";
$lang_months[9] = "Септември";
$lang_months[10] = "Октомври";
$lang_months[11] = "Ноември";
$lang_months[12] = "Декември";

$lang_datetime["short_mon"][1] = "Яну";
$lang_datetime["short_mon"][2] = "Фев";
$lang_datetime["short_mon"][3] = "Мар";
$lang_datetime["short_mon"][4] = "Апр";
$lang_datetime["short_mon"][5] = "Май";
$lang_datetime["short_mon"][6] = "Юни";
$lang_datetime["short_mon"][7] = "Юли";
$lang_datetime["short_mon"][8] = "Авг";
$lang_datetime["short_mon"][9] = "Сеп";
$lang_datetime["short_mon"][10] = "Окт";
$lang_datetime["short_mon"][11] = "Ное";
$lang_datetime["short_mon"][12] = "Дек";

$lang_datetime["dsow"][0] = "Нед";
$lang_datetime["dsow"][1] = "Пон";
$lang_datetime["dsow"][2] = "Вто";
$lang_datetime["dsow"][3] = "Сря";
$lang_datetime["dsow"][4] = "Чет";
$lang_datetime["dsow"][5] = "Пет";
$lang_datetime["dsow"][6] = "Съб";

$lang_datetime["dsowl"][0] = "Неделя";
$lang_datetime["dsowl"][1] = "Понеделник";
$lang_datetime["dsowl"][2] = "Вторник";
$lang_datetime["dsowl"][3] = "Сряда";
$lang_datetime["dsowl"][4] = "Четвъртък";
$lang_datetime["dsowl"][5] = "Петък";
$lang_datetime["dsowl"][6] = "Събота";
			
$lang_datetime["dsow_short"] = array("С", "П", "В", "С", "Ч", "П", "С");
$lang_datetime["today"] = "Днес %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%m/%d";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%m/%d/%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%m %d, %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%m %d";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 12;			// 12 or 24
$lang_datetime["ampm"]["am"] = "am";
$lang_datetime["ampm"]["pm"] = "pm";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";
?>
