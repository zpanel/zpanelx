<?php
/********************************************************
	lang/eng/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "Sausis";
$lang_months[2] = "Vasaris";
$lang_months[3] = "Kovas";
$lang_months[4] = "Balandis";
$lang_months[5] = "Geguþë";
$lang_months[6] = "Birþelis";
$lang_months[7] = "Liepa";
$lang_months[8] = "Rugpjûtis";
$lang_months[9] = "Rugsëjis";
$lang_months[10] = "Spalis";
$lang_months[11] = "Lapkritis";
$lang_months[12] = "Gruodis";

$lang_datetime["short_mon"][1] = "Sau";
$lang_datetime["short_mon"][2] = "Vas";
$lang_datetime["short_mon"][3] = "Kov";
$lang_datetime["short_mon"][4] = "Bal";
$lang_datetime["short_mon"][5] = "Geg";
$lang_datetime["short_mon"][6] = "Bir";
$lang_datetime["short_mon"][7] = "Lie";
$lang_datetime["short_mon"][8] = "Rgp";
$lang_datetime["short_mon"][9] = "Rgs";
$lang_datetime["short_mon"][10] = "Spa";
$lang_datetime["short_mon"][11] = "Lap";
$lang_datetime["short_mon"][12] = "Grd";

$lang_datetime["dsow"][0] = "Sek";
$lang_datetime["dsow"][1] = "Pir";
$lang_datetime["dsow"][2] = "Ant";
$lang_datetime["dsow"][3] = "Tre";
$lang_datetime["dsow"][4] = "Ket";
$lang_datetime["dsow"][5] = "Pen";
$lang_datetime["dsow"][6] = "Ðeð";

$lang_datetime["dsowl"][0] = "Sekmadienis";
$lang_datetime["dsowl"][1] = "Pirmadienis";
$lang_datetime["dsowl"][2] = "Antradienis";
$lang_datetime["dsowl"][3] = "Treèiadienis";
$lang_datetime["dsowl"][4] = "Ketvirtadienis";
$lang_datetime["dsowl"][5] = "Penktadienis";
$lang_datetime["dsowl"][6] = "Ðeðtadienis";
			
$lang_datetime["dsow_short"] = array("S", "P", "A", "T", "K", "P", "Ð");
$lang_datetime["today"] = "Ðiandien %t";	//pvz.: "Ðiandien 12:02"
$lang_datetime["lastweek"] = "%w %t";		//pvz.: "Tre 12:00"
$lang_datetime["thisyear"] = "%m/%d";		//pvz.: "2/13"
$lang_datetime["prevyears"] = "%m/%d/%y"; 	//pvz.: "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//pvz.: "Sausis 2002"
$lang_datetime["verbal"] = "%m %d, %y";  	//pvz.: "Sau 1, 1900"
$lang_datetime["verbal_short"] = "%m %d";  	//pvz.: "Sau 1"
$lang_datetime["hour_system"] = 12;			// 12 arba 24
$lang_datetime["ampm"]["am"] = "prieðpiet";
$lang_datetime["ampm"]["pm"] = "popiet";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";
?>