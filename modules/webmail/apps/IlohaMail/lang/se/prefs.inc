<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/se/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translated by Markus Svensson <markus.svensson@linux.nu>	

********************************************************/

$prefHeaderStrings[0] = "Inställningar";
$prefHeaderStrings[1] = "Generella";
$prefHeaderStrings[3] = "Identiteter";
$prefHeaderStrings[2] = "Färger";

											
$prefsStrings["0.0"] = "Identitet";
$prefsStrings["0.1"] = "Namn: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "Ändringar kan göras på \"%s\" sidan.";
$prefsStrings["0.4"] = "Ospecificerad";
$prefsStrings["1.0"] = "Internationella";
$prefsStrings["1.1"] = "Gränssnitt: ";
$prefsStrings["1.2"] = "Bokstavsuppsättning: ";
$prefsStrings["1.3"] = "Tidszon: ";
$prefsStrings["1.4"] = "Tidsinställning: ";
$prefsStrings["1.5"] = array(12=>"12-timmars klocka", 24=>"24-timmars klocka");
$prefsStrings["2.0"] = "Egenskaper för Listvy";
$prefsStrings["2.1"] = "Visa upp till ";
$prefsStrings["2.2"] = " poster";
$prefsStrings["2.3"] = "Visa storlek";
$prefsStrings["2.4"] = "Sortera efter: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Sorteringsordning: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Visa mapplista";
$prefsStrings["2.9"] = "Bredd på mapplista";
$prefsStrings["2.10"] = "Hämta nya meddelande var %n sekund";
$prefsStrings["2.11"] = "Visa verktygsrad vid %m";
$prefsStrings["2.12"] = array("b"=>"botten", "t"=>"toppen", "bt"=>"toppen och botten");
$prefsStrings["2.13"] = "Arrangera om kolumner: ";
$prefsStrings["2.14"] = "klicka här";
$prefsStrings["3.0"] = "Egenskaper för Auto-Spara";
$prefsStrings["3.1"] = "Spara skickade poster.";
$prefsStrings["3.2"] = "Spara skickade meddelande i mapp: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Ta bort skräp automatiskt";
$prefsStrings["3.5"] = "Flytta borttagna poster till: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Rotbibliotek: ";
$prefsStrings["3.8"] = "Annan";
$prefsStrings["3.9"] = "Göm mappar med uppsagda prenumerationer";
$prefsStrings["3.10"] = "Visa diskkvota %m";
$prefsStrings["3.11"] = array(""=>"visa inte", "m"=>"i meddelandelistan", "f"=>"i mapplistan");
$prefsStrings["4.0"] = "Egenskaper för Meddelandevy";
$prefsStrings["4.1"] = "Visa i ram";
$prefsStrings["4.2"] = "Färga citeringar";
$prefsStrings["4.3"] = "Denna egenskap kan göra systemet långsammare.";
$prefsStrings["4.4"] = "Konvertera URLs till länkar";
$prefsStrings["4.5"] = "Visa HTML meddelanden";
$prefsStrings["4.6"] = "Visa bilder i rad";
$prefsStrings["4.7"] = "Visa navigationslänkar";
$prefsStrings["4.8"] = "Markera inte meddelanden under navigation";
$prefsStrings["5.0"] = "Signatur";
$prefsStrings["5.1"] = "Visa signatur som standard";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Författa";
$prefsStrings["6.1"] = "Visa kontakter";
$prefsStrings["6.2"] = "Visa CC/BCC fält";
$prefsStrings["6.3"] = "Stäng efter skicka";
$prefsStrings["6.4"] = "Författa inom ramen";
$prefsStrings["7.0.0"] = "Inställningar för disk &amp; mapp";
$prefsStrings["7.0.1"] = "Inställningar för diskkvota";
$prefsStrings["7.1"] = "Visa antal olästa";
$prefsStrings["7.2"] = "Uppdatera mapplistan var %n sekund";




$sort_fields=array("DATE"=>"Datum", "SUBJECT"=>"Ämne", "SIZE"=>"Storlek");
$sort_orders=array("ASC"=>"Ökande", "DESC"=>"Minskande");

$prefsButtonStrings = array( 0=> "Uppdatera", 1=>"Verkställ", 2=> "Avbryt", 3=> "Standardvärden");

$prefs_new_user = "Det verkar som du loggar in för första gången...välkommen!<br>";
$prefs_new_user .= "Gå igenom följande inställningar, tryck därefter \"Verkställ\"";
?>