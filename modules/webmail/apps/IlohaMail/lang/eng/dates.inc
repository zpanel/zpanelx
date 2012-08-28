<?php
/********************************************************
	lang/eng/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
//include("../lang/eng/dates.inc");

$lang_months[1] = "January";
$lang_months[2] = "February";
$lang_months[3] = "March";
$lang_months[4] = "April";
$lang_months[5] = "May";
$lang_months[6] = "June";
$lang_months[7] = "July";
$lang_months[8] = "August";
$lang_months[9] = "September";
$lang_months[10] = "October";
$lang_months[11] = "November";
$lang_months[12] = "December";

$lang_datetime["short_mon"][1] = "Jan";
$lang_datetime["short_mon"][2] = "Feb";
$lang_datetime["short_mon"][3] = "Mar";
$lang_datetime["short_mon"][4] = "Apr";
$lang_datetime["short_mon"][5] = "May";
$lang_datetime["short_mon"][6] = "Jun";
$lang_datetime["short_mon"][7] = "Jul";
$lang_datetime["short_mon"][8] = "Aug";
$lang_datetime["short_mon"][9] = "Sep";
$lang_datetime["short_mon"][10] = "Oct";
$lang_datetime["short_mon"][11] = "Nov";
$lang_datetime["short_mon"][12] = "Dec";

$lang_datetime["dsow"][0] = "Sun";
$lang_datetime["dsow"][1] = "Mon";
$lang_datetime["dsow"][2] = "Tue";
$lang_datetime["dsow"][3] = "Wed";
$lang_datetime["dsow"][4] = "Thu";
$lang_datetime["dsow"][5] = "Fri";
$lang_datetime["dsow"][6] = "Sat";

$lang_datetime["dsowl"][0] = "Sunday";
$lang_datetime["dsowl"][1] = "Monday";
$lang_datetime["dsowl"][2] = "Tuesday";
$lang_datetime["dsowl"][3] = "Wednesday";
$lang_datetime["dsowl"][4] = "Thursday";
$lang_datetime["dsowl"][5] = "Friday";
$lang_datetime["dsowl"][6] = "Saturday";
			
$lang_datetime["dsow_short"] = array("S", "M", "T", "W", "T", "F", "S");
$lang_datetime["today"] = "Today %t";		//e.g. "Today 12:02"
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