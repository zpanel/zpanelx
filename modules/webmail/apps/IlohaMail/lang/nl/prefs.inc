<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/nl/prefs.inc
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Voorkeuren";
$prefHeaderStrings[1] = "Algemeen";
$prefHeaderStrings[3] = "Identiteiten";
$prefHeaderStrings[2] = "Uiterlijk";

											
$prefsStrings["0.0"] = "Identiteit";
$prefsStrings["0.1"] = "Naam: ";
$prefsStrings["0.2"] = "E-mail: ";
$prefsStrings["0.3"] = "Wijzigingen kunnen worden gemaakt op de \"%s\" pagina.";
$prefsStrings["0.4"] = "Niet gespecificeerd";
$prefsStrings["1.0"] = "Internationaal";
$prefsStrings["1.1"] = "Interface: ";
$prefsStrings["1.2"] = "Karakter set: ";
$prefsStrings["1.3"] = "Tijdzone: ";
$prefsStrings["1.4"] = "Tijdweergave: ";
$prefsStrings["1.5"] = array(12=>"12 uurs klok", 24=>"24 uurs klok");
$prefsStrings["2.0"] = "Lijstweergave opties";
$prefsStrings["2.1"] = "Maximaal ";
$prefsStrings["2.2"] = " items weergeven.";
$prefsStrings["2.3"] = "Grootte weergeven.";
$prefsStrings["2.4"] = "Sorteer op: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Sorteervolgorde: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Mappenlijst weergeven.";
$prefsStrings["2.9"] = "Mappenlijst breedte";
$prefsStrings["2.10"] = "Elke %n seconden op nieuwe berichten controleren.";
$prefsStrings["2.11"] = "Werkbalk %m weergeven.";
$prefsStrings["2.12"] = array("b"=>"onderaan", "t"=>"bovenaan", "bt"=>"boven- en onderaan");
$prefsStrings["2.13"] = "Kolommen herschikken: ";
$prefsStrings["2.14"] = "klik hier";
$prefsStrings["3.0"] = "Automatisch-opslaan opties";
$prefsStrings["3.1"] = "Verstuurde berichten opslaan.";
$prefsStrings["3.2"] = "Verstuurde berichten opslaan in deze map: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Automatisch prullenbak legen.";
$prefsStrings["3.5"] = "Verwijderde berichten verplaatsen naar deze map: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Mappenpad: ";
$prefsStrings["3.8"] = "Anders";
$prefsStrings["3.9"] = "Mappen waar je niet op geabonneerd bent verbergen.";
$prefsStrings["3.10"] = "Vrije ruimte %m weergeven.";
$prefsStrings["3.11"] = array(""=>"nergens", "m"=>"in berichtenlijst", "f"=>"in mappenlijst");
$prefsStrings["4.0"] = "Berichtweergave opties";
$prefsStrings["4.1"] = "Berichten weergeven in een frame.";
$prefsStrings["4.2"] = "Citaten een afwijkende kleur geven.";
$prefsStrings["4.3"] = "Dit aanzetten kan de server vertragen.";
$prefsStrings["4.4"] = "Adressen omzetten naar verwijzingen.";
$prefsStrings["4.5"] = "HTML berichten weergeven.";
$prefsStrings["4.6"] = "Plaatjes in berichten weergeven.";
$prefsStrings["4.7"] = "Navigatie mogelijkheden weergeven.";
$prefsStrings["4.8"] = "Berichten niet markeren als je er doorheen loopt.";
$prefsStrings["5.0"] = "Handtekening";
$prefsStrings["5.1"] = "Handtekening toevoegen aan nieuwe berichten.";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Berichten opstellen";
$prefsStrings["6.1"] = "Adressenlijst weergeven.";
$prefsStrings["6.2"] = "CC/BCC velden weergeven.";
$prefsStrings["6.3"] = "Sluiten na verzenden.";
$prefsStrings["6.4"] = "Berichten opstellen in een frame.";
$prefsStrings["7.0.0"] = "Schijf &amp; map opties";
$prefsStrings["7.0.1"] = "Quotum opties";
$prefsStrings["7.1"] = "Het aantal ongelezen berichten weergeven.";
$prefsStrings["7.2"] = "Mappenlijst elke %n seconden verversen.";

$pref_col_title = "Kolommen herschikken";
$pref_colstr["order"] = "Positie";
$pref_colstr["field"] = "Veld";
$pref_col_label['c'] = "Aanvink vakje";
$pref_col_label['a'] = "Onderdelen";
$pref_col_label['m'] = "Status";

$sort_fields=array("DATE"=>"Datum", "SUBJECT"=>"Onderwerp", "SIZE"=>"Omvang");
$sort_orders=array("ASC"=>"Oplopend", "DESC"=>"Aflopend");

$prefsButtonStrings = array(0=>"Verversen", 1=>"Toepassen", 2=>"Herstellen", 3=>"Standaard instellingen");

$prefs_new_user = "Het lijkt er op dat u zich voor het eerst aanmeldt...welkom!<br>";
$prefs_new_user .= "Bekijk alstublieft deze instellingen, en klik op \"Toepassen\".";
?>
