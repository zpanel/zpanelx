<?php
/********************************************************
	lang/tr/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	Turkish Translation by Ufuk Yuzereroglu <ufuk@yuzereroglu.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "Ocak";
$lang_months[2] = "Subat";
$lang_months[3] = "Mart";
$lang_months[4] = "Nisan";
$lang_months[5] = "Mayis";
$lang_months[6] = "Haziran";
$lang_months[7] = "Temmuz";
$lang_months[8] = "Agustos";
$lang_months[9] = "Eylül";
$lang_months[10] = "Ekim";
$lang_months[11] = "Kasim";
$lang_months[12] = "Aralik";

$lang_datetime["short_mon"][1] = "Ock";
$lang_datetime["short_mon"][2] = "Sbt";
$lang_datetime["short_mon"][3] = "Mrt";
$lang_datetime["short_mon"][4] = "Nsn";
$lang_datetime["short_mon"][5] = "Mys";
$lang_datetime["short_mon"][6] = "Hzr";
$lang_datetime["short_mon"][7] = "Tmz";
$lang_datetime["short_mon"][8] = "Ags";
$lang_datetime["short_mon"][9] = "Eyl";
$lang_datetime["short_mon"][10] = "Ekm";
$lang_datetime["short_mon"][11] = "Ksm";
$lang_datetime["short_mon"][12] = "Arl";

$lang_datetime["dsow"][0] = "Pzr";
$lang_datetime["dsow"][1] = "Pzt";
$lang_datetime["dsow"][2] = "Sal";
$lang_datetime["dsow"][3] = "Çrs";
$lang_datetime["dsow"][4] = "Prs";
$lang_datetime["dsow"][5] = "Cma";
$lang_datetime["dsow"][6] = "Cmt";

$lang_datetime["dsowl"][0] = "Pazar";
$lang_datetime["dsowl"][1] = "Pazartesi";
$lang_datetime["dsowl"][2] = "Sali";
$lang_datetime["dsowl"][3] = "Çarsamba";
$lang_datetime["dsowl"][4] = "Persembe";
$lang_datetime["dsowl"][5] = "Cuma";
$lang_datetime["dsowl"][6] = "Cumartesi";
			
$lang_datetime["dsow_short"] = array("P", "S", "Ç", "P", "C", "C", "P");
$lang_datetime["today"] = "Bugün %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%m/%d";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%m/%d/%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%m %d, %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%m %d";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 12;			// 12 or 24
$lang_datetime["ampm"]["am"] = "am";
$lang_datetime["ampm"]["pm"] = "pm";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=saat, %m=dakika, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";
?>
