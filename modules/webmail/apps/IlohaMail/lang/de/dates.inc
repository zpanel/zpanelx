<?php
/********************************************************
	lang/eng/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "Januar";
$lang_months[2] = "Februar";
$lang_months[3] = "M&auml;rz";
$lang_months[4] = "April";
$lang_months[5] = "Mai";
$lang_months[6] = "Juni";
$lang_months[7] = "Juli";
$lang_months[8] = "August";
$lang_months[9] = "September";
$lang_months[10] = "Oktober";
$lang_months[11] = "November";
$lang_months[12] = "Dezember";

$lang_datetime["short_mon"][1] = "Jan";
$lang_datetime["short_mon"][2] = "Feb";
$lang_datetime["short_mon"][3] = "Mrz";
$lang_datetime["short_mon"][4] = "Apr";
$lang_datetime["short_mon"][5] = "Mai";
$lang_datetime["short_mon"][6] = "Jun";
$lang_datetime["short_mon"][7] = "Jul";
$lang_datetime["short_mon"][8] = "Aug";
$lang_datetime["short_mon"][9] = "Sep";
$lang_datetime["short_mon"][10] = "Okt";
$lang_datetime["short_mon"][11] = "Nov";
$lang_datetime["short_mon"][12] = "Dez";

$lang_datetime["dsow"][0] = "So";
$lang_datetime["dsow"][1] = "Mo";
$lang_datetime["dsow"][2] = "Di";
$lang_datetime["dsow"][3] = "Mi";
$lang_datetime["dsow"][4] = "Do";
$lang_datetime["dsow"][5] = "Fr";
$lang_datetime["dsow"][6] = "Sa";

$lang_datetime["dsowl"][0] = "Sonntag";
$lang_datetime["dsowl"][1] = "Montag";
$lang_datetime["dsowl"][2] = "Dienstag";
$lang_datetime["dsowl"][3] = "Mittwoch";
$lang_datetime["dsowl"][4] = "Donnerstag";
$lang_datetime["dsowl"][5] = "Freitag";
$lang_datetime["dsowl"][6] = "Samstag";
			
$lang_datetime["dsow_short"] = array("S", "M", "D", "M", "D", "F", "S");
$lang_datetime["today"] = "Heute %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%d.%m";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%d.%m.%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%d. %m %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%d %m";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 24;			// 12 or 24
$lang_datetime["ampm"]["am"] = "am";
$lang_datetime["ampm"]["pm"] = "pm";
$lang_datetime["time_format"] = "%h:%m %a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h %a";
?>
