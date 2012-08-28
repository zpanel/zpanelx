<?php
/********************************************************
	lang/nn_NO/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translation by Leif Halvard Silli <hyperlekken@lenk.no>

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "januar";
$lang_months[2] = "februar";
$lang_months[3] = "mars";
$lang_months[4] = "april";
$lang_months[5] = "mai";
$lang_months[6] = "juni";
$lang_months[7] = "juli";
$lang_months[8] = "august";
$lang_months[9] = "september";
$lang_months[10] = "oktober";
$lang_months[11] = "november";
$lang_months[12] = "desember";

$lang_datetime["short_mon"][1] = "jan";
$lang_datetime["short_mon"][2] = "feb";
$lang_datetime["short_mon"][3] = "mar";
$lang_datetime["short_mon"][4] = "apr";
$lang_datetime["short_mon"][5] = "mai";
$lang_datetime["short_mon"][6] = "jun";
$lang_datetime["short_mon"][7] = "jul";
$lang_datetime["short_mon"][8] = "aug";
$lang_datetime["short_mon"][9] = "sep";
$lang_datetime["short_mon"][10] = "okt";
$lang_datetime["short_mon"][11] = "nov";
$lang_datetime["short_mon"][12] = "des";

$lang_datetime["dsow"][0] = "sun";
$lang_datetime["dsow"][1] = "mon";
$lang_datetime["dsow"][2] = "tys";
$lang_datetime["dsow"][3] = "ons";
$lang_datetime["dsow"][4] = "tor";
$lang_datetime["dsow"][5] = "fre";
$lang_datetime["dsow"][6] = "lau";

$lang_datetime["dsowl"][0] = "sundag(en)";
$lang_datetime["dsowl"][1] = "mondag(en)";
$lang_datetime["dsowl"][2] = "tysdag(en)";
$lang_datetime["dsowl"][3] = "onsdag(en)";
$lang_datetime["dsowl"][4] = "torsdag(en)";
$lang_datetime["dsowl"][5] = "fredag(en)";
$lang_datetime["dsowl"][6] = "laurdag(en)";
			
$lang_datetime["dsow_short"] = array("S", "M", "T", "O", "T", "F", "L");
$lang_datetime["today"] = "Idag %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%d.%m";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%d.%m.%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%d. %m %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%d. %m";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 24;			// 12 or 24
$lang_datetime["ampm"]["am"] = " um fyremiddagen";
$lang_datetime["ampm"]["pm"] = " um ettermiddagen";
$lang_datetime["time_format"] = "%h:%m";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h";

?>