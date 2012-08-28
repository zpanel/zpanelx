<?php
include("../lang/eng/dates.inc"); // <-- do not delete
/********************************************************
	lang/hu/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/
$lang_months[1] = "Január";
$lang_months[2] = "Február";
$lang_months[3] = "Március";
$lang_months[4] = "Április";
$lang_months[5] = "Május";
$lang_months[6] = "Június";
$lang_months[7] = "Júlis";
$lang_months[8] = "Augusztus";
$lang_months[9] = "Szeptember";
$lang_months[10] = "Október";
$lang_months[11] = "November";
$lang_months[12] = "December";

$lang_datetime["short_mon"][1] = "Jan";
$lang_datetime["short_mon"][2] = "Feb";
$lang_datetime["short_mon"][3] = "Már";
$lang_datetime["short_mon"][4] = "Ápr";
$lang_datetime["short_mon"][5] = "Máj";
$lang_datetime["short_mon"][6] = "Jún";
$lang_datetime["short_mon"][7] = "Júl";
$lang_datetime["short_mon"][8] = "Aug";
$lang_datetime["short_mon"][9] = "Szep";
$lang_datetime["short_mon"][10] = "Okt";
$lang_datetime["short_mon"][11] = "Nov";
$lang_datetime["short_mon"][12] = "Dec";

$lang_datetime["dsow"][0] = "V";
$lang_datetime["dsow"][1] = "H";
$lang_datetime["dsow"][2] = "K";
$lang_datetime["dsow"][3] = "Sze";
$lang_datetime["dsow"][4] = "Cs";
$lang_datetime["dsow"][5] = "P";
$lang_datetime["dsow"][6] = "Szo";

$lang_datetime["dsowl"][0] = "Vasárnap";
$lang_datetime["dsowl"][1] = "Hétfõ";
$lang_datetime["dsowl"][2] = "Kedd";
$lang_datetime["dsowl"][3] = "Szerda";
$lang_datetime["dsowl"][4] = "Csütörtök";
$lang_datetime["dsowl"][5] = "Péntek";
$lang_datetime["dsowl"][6] = "Szombat";
			
$lang_datetime["dsow_short"] = array("V", "H", "K", "Sze", "CS", "P", "Szo");
$lang_datetime["monthyear"] = "%y. %m";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%y. %m %d.";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%m %d.";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 24;			// 12 or 24
$lang_datetime["ampm"]["am"] = "am";
$lang_datetime["ampm"]["pm"] = "pm";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";

$lang_datetime["today"] = "Ma (%t)";
$lang_datetime["lastweek"] = "%w %t";
$lang_datetime["thisyear"] = "%m %d.";
$lang_datetime["prevyears"] = "%y. %m %d.";


?>