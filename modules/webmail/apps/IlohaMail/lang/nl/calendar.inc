<?php
/********************************************************
	lang/<lang>/calendar.inc
	(C)2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/calendar.inc");

$calStr["add_schedule"] = "Herinnering toevoegen";
$calStr["edit_schedule"] = "Herinnering wijzigen";
$calStr["weekof"] = "Week van: %d";
$calStr["title"] = "Titel:";
$calStr["color"] = "Kleur:";
$calStr["colors"] = array("#990000"=>"Donker rood", "#FF0000"=>"Rood", "#000099"=>"Donker blauw", "#0000FF"=>"Blauw", 
					"#006600"=>"Donker groen", "#00FF00"=>"Groen", "#9900FF"=>"Paars", "#00FFFF"=>"Cyan",
					"#FF6600"=>"Oranje", "#FFFF00"=>"Geel", "#FF00FF"=>"Magenta", ""=>"Geen kleur");
$calStr["starts"] = "Begint op:";
$calStr["ends"] = "Eindigt op:";
$calStr["from"] = "van:";
$calStr["until"] = "tot:";
$calStr["repeat_on"] = "Herhaal op";
$calStr["of"] = "van elke";
$calStr["weeks"] = array(1=>"eerste week", 2=>"tweede week", 3=>"derde week", 4=>"vierde week"); 
$calStr["week_blurb"] = "...van elke maand (tot de einddatum).<br>Wanneer niets wordt geselecteerd, wordt de herinnering in elke week herhaald.";
$calStr["monthly"] = "Maandelijks herhalen";
$calStr["yearly"] = "Jaarlijks herhalen";
$calStr["place"] = "Plaats:";
$calStr["description"] = "Beschrijving:";
$calStr["add"] = "Toevoegen";
$calStr["update"] = "Aanpassen";
$calStr["Delete"] = "Verwijderen";
?>
