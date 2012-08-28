<?php
/********************************************************
	lang/eng/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "Leden";
$lang_months[2] = "Únor";
$lang_months[3] = "Bøezen";
$lang_months[4] = "Duben";
$lang_months[5] = "Kvìten";
$lang_months[6] = "Èerven";
$lang_months[7] = "Èervenec";
$lang_months[8] = "Srpen";
$lang_months[9] = "Záøí";
$lang_months[10] = "Øíjen";
$lang_months[11] = "Listopad";
$lang_months[12] = "Prosinec";

$lang_datetime["short_mon"][1] = "Led";
$lang_datetime["short_mon"][2] = "Úno";
$lang_datetime["short_mon"][3] = "Bøe";
$lang_datetime["short_mon"][4] = "Dub";
$lang_datetime["short_mon"][5] = "Kvì";
$lang_datetime["short_mon"][6] = "Èen";
$lang_datetime["short_mon"][7] = "Èec";
$lang_datetime["short_mon"][8] = "Srp";
$lang_datetime["short_mon"][9] = "Záø";
$lang_datetime["short_mon"][10] = "Øíj";
$lang_datetime["short_mon"][11] = "Lis";
$lang_datetime["short_mon"][12] = "Pro";

$lang_datetime["dsow"][0] = "Ne";
$lang_datetime["dsow"][1] = "Po";
$lang_datetime["dsow"][2] = "Út";
$lang_datetime["dsow"][3] = "St";
$lang_datetime["dsow"][4] = "Èt";
$lang_datetime["dsow"][5] = "Pá";
$lang_datetime["dsow"][6] = "So";

$lang_datetime["dsowl"][0] = "Nedìle";
$lang_datetime["dsowl"][1] = "Pondìlí";
$lang_datetime["dsowl"][2] = "Úterý";
$lang_datetime["dsowl"][3] = "Støeda";
$lang_datetime["dsowl"][4] = "Ètvrtek";
$lang_datetime["dsowl"][5] = "Pátek";
$lang_datetime["dsowl"][6] = "Sobota";
			
$lang_datetime["dsow_short"] = array("N", "P", "Ú", "S", "È", "P", "S");
$lang_datetime["today"] = "Dnes %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%d/%m";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%d/%m/%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%d %m, %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%d %m";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 24;			// 12 or 24
$lang_datetime["ampm"]["am"] = "dop.";
$lang_datetime["ampm"]["pm"] = "odp.";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h";
?>