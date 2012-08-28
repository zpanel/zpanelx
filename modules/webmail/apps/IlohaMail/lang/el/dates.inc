<?php
/********************************************************
	lang/eng/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "Ιανουάριος";
$lang_months[2] = "Φεβρουάριος";
$lang_months[3] = "Μάρτιος";
$lang_months[4] = "Απρίλιος";
$lang_months[5] = "Μάιος";
$lang_months[6] = "Ιούνιος";
$lang_months[7] = "Ιούλιος";
$lang_months[8] = "Αύγουστος";
$lang_months[9] = "Σεπτέμβριος";
$lang_months[10] = "Οκτώβριος";
$lang_months[11] = "Νοέμβριος";
$lang_months[12] = "Δεκέμβριος";

$lang_datetime["short_mon"][1] = "Ιάν";
$lang_datetime["short_mon"][2] = "Φεβ";
$lang_datetime["short_mon"][3] = "Μαρ";
$lang_datetime["short_mon"][4] = "Απρ";
$lang_datetime["short_mon"][5] = "Μαι";
$lang_datetime["short_mon"][6] = "Ιον";
$lang_datetime["short_mon"][7] = "Ιολ";
$lang_datetime["short_mon"][8] = "Αύγ";
$lang_datetime["short_mon"][9] = "Σεπ";
$lang_datetime["short_mon"][10] = "Οκτ";
$lang_datetime["short_mon"][11] = "Νοε";
$lang_datetime["short_mon"][12] = "Δεκ";

$lang_datetime["dsow"][0] = "Κυρ";
$lang_datetime["dsow"][1] = "Δευ";
$lang_datetime["dsow"][2] = "Τρι";
$lang_datetime["dsow"][3] = "Τετ";
$lang_datetime["dsow"][4] = "Πεμ";
$lang_datetime["dsow"][5] = "Παρ";
$lang_datetime["dsow"][6] = "Σαβ";

$lang_datetime["dsowl"][0] = "Κυριακή";
$lang_datetime["dsowl"][1] = "Δευτέρα";
$lang_datetime["dsowl"][2] = "Τρίτη";
$lang_datetime["dsowl"][3] = "Τετάρτη";
$lang_datetime["dsowl"][4] = "Πέμπτη";
$lang_datetime["dsowl"][5] = "Παρασκευή";
$lang_datetime["dsowl"][6] = "Σάββατο";
			
$lang_datetime["dsow_short"] = array("Κ", "Δ", "Τ", "Τ", "Π", "Π", "Σ");
$lang_datetime["today"] = "Σήμερα %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%d/%m";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%d/%d/%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%d %m, %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%d %m";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 12;			// 12 or 24
$lang_datetime["ampm"]["am"] = "πμ";
$lang_datetime["ampm"]["pm"] = "μμ";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";
?>
