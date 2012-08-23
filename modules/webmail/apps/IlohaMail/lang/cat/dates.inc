<?php
/********************************************************
        lang/eng/dates.inc
        (C) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

        This file is part of IlohaMail, and released under GPL.
        See COPYING, or http://www.fsf.org/copyleft/gpl.html

        Translated: David Gimeno i Ayuso info@sima-pc.com

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/dates.inc");

$lang_months[1] = "gener";
$lang_months[2] = "febrer";
$lang_months[3] = "mar&ccedil;";
$lang_months[4] = "abril";
$lang_months[5] = "maig";
$lang_months[6] = "juny";
$lang_months[7] = "juliol";
$lang_months[8] = "agost";
$lang_months[9] = "setembre";
$lang_months[10] = "octubre";
$lang_months[11] = "novembre";
$lang_months[12] = "desembre";

$lang_datetime["short_mon"][1] = "gen";
$lang_datetime["short_mon"][2] = "feb";
$lang_datetime["short_mon"][3] = "mar";
$lang_datetime["short_mon"][4] = "abr";
$lang_datetime["short_mon"][5] = "mai";
$lang_datetime["short_mon"][6] = "jun";
$lang_datetime["short_mon"][7] = "jul";
$lang_datetime["short_mon"][8] = "ago";
$lang_datetime["short_mon"][9] = "set";
$lang_datetime["short_mon"][10] = "oct";
$lang_datetime["short_mon"][11] = "nov";
$lang_datetime["short_mon"][12] = "des";

$lang_datetime["dsow"][0] = "diu";
$lang_datetime["dsow"][1] = "dil";
$lang_datetime["dsow"][2] = "dit";
$lang_datetime["dsow"][3] = "dic";
$lang_datetime["dsow"][4] = "dij";
$lang_datetime["dsow"][5] = "div";
$lang_datetime["dsow"][6] = "dis";

$lang_datetime["dsowl"][0] = "diumenge";
$lang_datetime["dsowl"][1] = "dilluns";
$lang_datetime["dsowl"][2] = "dimarts";
$lang_datetime["dsowl"][3] = "dimecres";
$lang_datetime["dsowl"][4] = "dijous";
$lang_datetime["dsowl"][5] = "divendres";
$lang_datetime["dsowl"][6] = "dissabte";

$lang_datetime["dsow_short"] = array("dg", "dl", "dt", "dc", "dj", "dv", "ds");
$lang_datetime["today"] = "Avui %t";           //e.g. "Today 12:02"
$lang_datetime["lastweek"] = "%w %t";           //e.g. "Wed 12:00"
$lang_datetime["thisyear"] = "%d/%m";           //e.g. "2/13"
$lang_datetime["prevyears"] = "%d/%m/%y";       //e.g. "1/1/2100"
$lang_datetime["monthyear"] = "%m %y";          //e.g. "January 2002"
$lang_datetime["verbal"] = "%d %m del %y";         //e.g. "Jan 1, 1900"
$lang_datetime["verbal_short"] = "%d %m";       //e.g. "Jan 1"
$lang_datetime["hour_system"] = 24;                     // 12 or 24
$lang_datetime["ampm"]["am"] = "";
$lang_datetime["ampm"]["pm"] = "";
$lang_datetime["time_format"] = "%h:%m%a";      // %h=hour, %m=minutes, %a="am:pm"
$lang_datetime["hour_format"] = "%h%a";
?>
