<?php
/********************************************************
	lang/si/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Nastavitve";
$prefHeaderStrings[1] = "Glavno";
$prefHeaderStrings[3] = "Identitete";
$prefHeaderStrings[2] = "Prikaz";

											
$prefsStrings["0.0"] = "Privzeta identiteta";
$prefsStrings["0.1"] = "Ime: ";
$prefsStrings["0.2"] = "E-pošta: ";
$prefsStrings["0.3"] = "Spremembe se lahko zgodijo na strani \"%s\".";
$prefsStrings["0.4"] = "Nedoloèeno";
$prefsStrings["1.0"] = "Mednarodno";
$prefsStrings["1.1"] = "Vmesnik: ";
$prefsStrings["1.2"] = "Nabor znakov: ";
$prefsStrings["1.3"] = "Èasovna cona: ";
$prefsStrings["1.4"] = "Èasovne nastavitve: ";
$prefsStrings["1.5"] = array(12=>"12 ur", 24=>"24 ur");
$prefsStrings["2.0"] = "Prikaz nastavitev pogleda";
$prefsStrings["2.1"] = "Pokaži vse do ";
$prefsStrings["2.2"] = " elementov";
$prefsStrings["2.3"] = "Prikaži velikost";
$prefsStrings["2.4"] = "Sortiraj po: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Vrstni red sortiranja: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Pokaži vsebino mape";
$prefsStrings["2.9"] = "Velikost seznama map";
$prefsStrings["2.10"] = "Vsakih %n sekund preveri za nova sporoèila";
$prefsStrings["2.11"] = "Na %m prikaži orodno vrstico";
$prefsStrings["2.12"] = array("b"=>"dno", "t"=>"vrh", "bt"=>"dno in vrh");
$prefsStrings["2.13"] = "Ponastavi stolpce: ";
$prefsStrings["2.14"] = "kliknite tukaj";
$prefsStrings["3.0"] = "Samodejno shrani nastavitve";
$prefsStrings["3.1"] = "Shrani poslana sporoèila.";
$prefsStrings["3.2"] = "Shrani poslana sporoèila v mapo: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Samodejno izbriši smeti";
$prefsStrings["3.5"] = "Premakni izbrane mape v imenik: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Zaèetni imenik: ";
$prefsStrings["3.8"] = "Drugo";
$prefsStrings["3.9"] = "Skrij nepodpisane imenike";
$prefsStrings["3.10"] = "Pokaži citate %m";
$prefsStrings["3.11"] = array(""=>"ne pokaži", "m"=>"v seznamu sporoèil", "f"=>"v seznamu map");
$prefsStrings["4.0"] = "Nastavitve prikaza sporoèil";
$prefsStrings["4.1"] = "Prikaži v okvirjih";
$prefsStrings["4.2"] = "Barvaj citate";
$prefsStrings["4.3"] = "Vklop te nastavitve lahko upoèasni sistem.";
$prefsStrings["4.4"] = "Pretvori URL naslove v povezave";
$prefsStrings["4.5"] = "Prikaz HTML sporoèil";
$prefsStrings["4.6"] = "Prikaz slik znotraj sporoèil";
$prefsStrings["4.7"] = "Prikaz navigacijskih povezav";
$prefsStrings["4.8"] = "Ne oznaèi sporoèil, ko preusmerjaš";
$prefsStrings["5.0"] = "Podpis";
$prefsStrings["5.1"] = "Prikaz sporoèil po privzetem";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Sestavljanje sporoèila";
$prefsStrings["6.1"] = "Prikaz stikov";
$prefsStrings["6.2"] = "Prikaz CC/BCC polj";
$prefsStrings["6.3"] = "Zapri po pošiljanju";
$prefsStrings["6.4"] = "Sestavi znotraj okvirja";
$prefsStrings["7.0.0"] = "Disk &amp; lastnosti imenika";
$prefsStrings["7.0.1"] = "Nastavitve kvote";
$prefsStrings["7.1"] = "Prikaži število neprebranih sporoèil";
$prefsStrings["7.2"] = "Vsakih %n sekund osveži vsebino sporoèil";

$pref_col_title = "Ponastavi stolpce";
$pref_colstr["order"] = "Uredi";
$pref_colstr["field"] = "Polje";
$pref_col_label['c'] = "Okence za odkljukati";
$pref_col_label['a'] = "Priloge";
$pref_col_label['m'] = "Zastavice/status";

$sort_fields=array("DATE"=>"Datum", "SUBJECT"=>"Tema", "SIZE"=>"Velikost");
$sort_orders=array("ASC"=>"Narašèajoèe", "DESC"=>"Padajoèe");

$prefsButtonStrings = array( 0=> "Obnovi", 1=>"Dodaj spremembe", 2=> "Preklièi", 3=> "Privzeto");

$prefs_new_user = "Ob vaši prvi prijavi vam želimo izreèi dobrodošlico!<br>";
$prefs_new_user .= "Prosimo, pojdite skozi sledeèe nastavitve in pritisnite \"Apply\"";
?>