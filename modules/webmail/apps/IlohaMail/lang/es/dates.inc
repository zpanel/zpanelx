<?php
include("../lang/eng/dates.inc"); // <-- do not delete
/********************************************************
	lang/es/dates.inc
	(C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/
$lang_months[1] = "Enero";
$lang_months[2] = "Febrero";
$lang_months[3] = "Marzo";
$lang_months[4] = "Abril";
$lang_months[5] = "Mayo";
$lang_months[6] = "Junio";
$lang_months[7] = "Julio";
$lang_months[8] = "Agosto";
$lang_months[9] = "Septiembre";
$lang_months[10] = "Octubre";
$lang_months[11] = "Noviembre";
$lang_months[12] = "Diciembre";

$lang_datetime["short_mon"][1] = "Ene";
$lang_datetime["short_mon"][2] = "Feb";
$lang_datetime["short_mon"][3] = "Mar";
$lang_datetime["short_mon"][4] = "Abr";
$lang_datetime["short_mon"][5] = "May";
$lang_datetime["short_mon"][6] = "Jun";
$lang_datetime["short_mon"][7] = "Jul";
$lang_datetime["short_mon"][8] = "Ago";
$lang_datetime["short_mon"][9] = "Sep";
$lang_datetime["short_mon"][10] = "Oct";
$lang_datetime["short_mon"][11] = "Nov";
$lang_datetime["short_mon"][12] = "Dic";

$lang_datetime["dsow"][0] = "Dom";
$lang_datetime["dsow"][1] = "Lun";
$lang_datetime["dsow"][2] = "Mar";
$lang_datetime["dsow"][3] = "Mie";
$lang_datetime["dsow"][4] = "Jue";
$lang_datetime["dsow"][5] = "Vie";
$lang_datetime["dsow"][6] = "Sab";

$lang_datetime["dsowl"][0] = "Domingo";
$lang_datetime["dsowl"][1] = "Lunes";
$lang_datetime["dsowl"][2] = "Martes";
$lang_datetime["dsowl"][3] = "Miércoles";
$lang_datetime["dsowl"][4] = "Jueves";
$lang_datetime["dsowl"][5] = "Viernes";
$lang_datetime["dsowl"][6] = "Sábado";
			
$lang_datetime["dsow_short"] = array("D", "L", "M", "M", "J", "V", "S");
$lang_datetime["today"] = "Hoy %t";		//e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";		//e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%d/%m";		//e.g. "2/13"
$lang_datetime["prevyears"] = "%d/%m/%y"; 	//e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";		//e.g. "January 2002"
$lang_datetime["verbal"] = "%d %m %y";  	//e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%d %m";  	//e.g. "Jan 1"
$lang_datetime["hour_system"] = 12;			// 12 or 24
$lang_datetime["ampm"]["am"] = "am";
$lang_datetime["ampm"]["pm"] = "pm";
$lang_datetime["time_format"] = "%h:%m%a";	// %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";
?>