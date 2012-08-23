<?php
/********************************************************
	lang/eng/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "Tammikuu";
$lang_months[2] = "Helmikuu";
$lang_months[3] = "Maaliskuu";
$lang_months[4] = "Huhtikuu";
$lang_months[5] = "Toukokuu";
$lang_months[6] = "Kes&auml;kuu";
$lang_months[7] = "Hein&auml;kuu";
$lang_months[8] = "Elokuu";
$lang_months[9] = "Syyskuu";
$lang_months[10] = "Lokakuu";
$lang_months[11] = "Marraskuu";
$lang_months[12] = "Joulukuu";

$lang_datetime["short_mon"][1] = "Tammi";
$lang_datetime["short_mon"][2] = "Helmi";
$lang_datetime["short_mon"][3] = "Maalis";
$lang_datetime["short_mon"][4] = "Huhti";
$lang_datetime["short_mon"][5] = "Touko";
$lang_datetime["short_mon"][6] = "Kes&auml;";
$lang_datetime["short_mon"][7] = "Hein&auml;";
$lang_datetime["short_mon"][8] = "Elo";
$lang_datetime["short_mon"][9] = "Syys";
$lang_datetime["short_mon"][10] = "Loka";
$lang_datetime["short_mon"][11] = "Marras";
$lang_datetime["short_mon"][12] = "Joulu";

$lang_datetime["dsow"][0] = "Su";
$lang_datetime["dsow"][1] = "Ma";
$lang_datetime["dsow"][2] = "Ti";
$lang_datetime["dsow"][3] = "Ke";
$lang_datetime["dsow"][4] = "To";
$lang_datetime["dsow"][5] = "Pe";
$lang_datetime["dsow"][6] = "La";

$lang_datetime["dsowl"][0] = "Sunnuntai";
$lang_datetime["dsowl"][1] = "Maanantai";
$lang_datetime["dsowl"][2] = "Tiistai";
$lang_datetime["dsowl"][3] = "Keskiviikko";
$lang_datetime["dsowl"][4] = "Torstai";
$lang_datetime["dsowl"][5] = "Perjantai";
$lang_datetime["dsowl"][6] = "Lauantai";
			
$lang_datetime["dsow_short"] = array("S", "M", "T", "K", "T", "P", "L");
$lang_datetime["today"] = "T&auml;n&auml;&auml;n %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%d.%m.";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%d.%m.%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%m %d, %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%m %d";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 24;			// 12 or 24
$lang_datetime["ampm"]["am"] = "am";
$lang_datetime["ampm"]["pm"] = "pm";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";
?>
