<?php
/********************************************************
	lang/<lang>/calendar.inc
	(C)2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/calendar.inc");

$calStr["add_schedule"] = "Lis&auml;&auml; tapahtuma";
$calStr["edit_schedule"] = "Muuta tapahtumaa";
$calStr["weekof"] = "Viikko: %d";
$calStr["title"] = "Otsikko:";
$calStr["color"] = "V&auml;ri:";

//for $calStr['color_names'], translate strings
//on the right hand side of equal sign
$calStr["color_names"]['Dark Red'] = 'Tummanpunainen';
$calStr["color_names"]['Red'] = 'Red';
$calStr["color_names"]['Deep Blue'] = 'Syv&auml; sininen';
$calStr["color_names"]['Blue'] = 'Sininen';
$calStr["color_names"]['Dark Green'] = 'Tummanvihre&auml;';
$calStr["color_names"]['Green'] = 'Vihre&auml;';
$calStr["color_names"]['Purple'] = 'Purple';
$calStr["color_names"]['Cyan'] = 'Cyan';
$calStr["color_names"]['Orange'] = 'Oranssi';
$calStr["color_names"]['Yellow'] = 'Keltainen';
$calStr["color_names"]['Magenta'] = 'Magenta';
$calStr["color_names"]['No Color'] = 'Ei v&auml;ri&auml;';

$calStr["starts"] = "Alkaa:";
$calStr["ends"] = "Loppuu:";
$calStr["from"] = "mist&auml;:";
$calStr["until"] = "mihin:";
$calStr["repeat_on"] = "P&auml;iv&auml;t";
$calStr["of"] = "kuukausittain";
$calStr["weeks"] = array(1=>"ensimm&auml;isell&auml;", 2=>"toisella", 3=>"kolmannella", 4=>"nelj&auml;nnell&auml;"); 
$calStr["week_blurb"] = "...viikolla (lopetusp&auml;iv&auml;&auml;n asti).<br>Ilman rasteja toistetaan joka viikko.";
$calStr["monthly"] = "Toistetaan kuukausittain";
$calStr["yearly"] = "Toistetaan vuosittain";
$calStr["place"] = "Paikka:";
$calStr["description"] = "Seloste:";
$calStr["add"] = "Lis&auml;&auml;";
$calStr["update"] = "Muuta";
$calStr["Delete"] = "Poista";
$calStr["go"] = "N&auml;yt&auml;";
$calStr["disabled"] = "Kalenteri on poistettu k&auml;yt&ouml;st&auml;.";
$calStr["public"] = "N&auml;yt&auml; tapahtuma kaikille";
$calStr['no schedules'] = '<p>T&auml;ll&auml; viikolla ei ole tapahtumia. <br>Voit lis&auml;t&auml; niit&auml; painamalla p&auml;iv&auml;m&auml;&auml;r&auml;st&auml; yl&auml;puolelta.';
$calStr['all day'] = "All Day";
$calStr['is all day'] = 'Koko p&auml;iv&auml;n kest&auml;v&auml; tapahtuma';
?>
