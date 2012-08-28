<?php
/********************************************************
	lang/eng/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Nastavení";
$prefHeaderStrings[1] = "Hlavní";
$prefHeaderStrings[3] = "Identity";
$prefHeaderStrings[2] = "Vzhled";

											
$prefsStrings["0.0"] = "Výchozí identita";
$prefsStrings["0.1"] = "Jméno: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "Zmìny mù¾ete provádìt na stránce \"%s\".";
$prefsStrings["0.4"] = "Neuvedeno";
$prefsStrings["1.0"] = "Národní nastavení";
$prefsStrings["1.1"] = "Jazyk rozhraní: ";
$prefsStrings["1.2"] = "Znaková sada: ";
$prefsStrings["1.3"] = "Èasová zóna: ";
$prefsStrings["1.4"] = "Zobrazení èasu: ";
$prefsStrings["1.5"] = array(12=>"12 hod. èas", 24=>"24 hod. èas");
$prefsStrings["2.0"] = "Nastavení pohledu seznam";
$prefsStrings["2.1"] = "Zobrazit a¾ ";
$prefsStrings["2.2"] = " polo¾ek";
$prefsStrings["2.3"] = "Zobrazit velikost";
$prefsStrings["2.4"] = "Tøídit podle: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Poøadí øazení: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Nastavení seznamu schránek";
$prefsStrings["2.9"] = "©íøka seznamu";
$prefsStrings["2.10"] = "Kontrolovat nové zprávy ka¾dých %n vteøin";
$prefsStrings["2.11"] = "Zobrazit panel nástrojù %m";
$prefsStrings["2.12"] = array("b"=>"dole", "t"=>"nahoøe", "bt"=>"nahoøe i dole");
$prefsStrings["2.13"] = "Pøeuspoøádat sloupce: ";
$prefsStrings["2.14"] = "klepnìte sem";
$prefsStrings["3.0"] = "Nastavení automatického ukládání";
$prefsStrings["3.1"] = "Ukládat odeslané zprávy.";
$prefsStrings["3.2"] = "Ukládat odeslané do schránky: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Automaticky vyprázdnit ko¹";
$prefsStrings["3.5"] = "Pøesunout vymazané do schránky: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Koøenová slo¾ka: ";
$prefsStrings["3.8"] = "Jiná";
$prefsStrings["3.9"] = "Skrýt neaktivované schránky";
$prefsStrings["3.10"] = "Zobrazit kvótu %m";
$prefsStrings["3.11"] = array(""=>"nezobrazovat", "m"=>"v seznamu zpráv", "f"=>"v seznamu schránek");
$prefsStrings["4.0"] = "Nastavení zobrazení zprávy";
$prefsStrings["4.1"] = "Zobrazovat v rámeèku";
$prefsStrings["4.2"] = "Obarvovat odsazení";
$prefsStrings["4.3"] = "Zapnutí tìchto voleb mù¾e zpomalit systém.";
$prefsStrings["4.4"] = "Pøevádìt URL na odkazy";
$prefsStrings["4.5"] = "Zobrazovat HTML zprávy";
$prefsStrings["4.6"] = "Zobrazovat obrázky ve zprávì";
$prefsStrings["4.7"] = "Zobrazovat navigaèní odkazy";
$prefsStrings["4.8"] = "Neoznaèovat zprávy pøi procházení";
$prefsStrings["5.0"] = "Podpis";
$prefsStrings["5.1"] = "Zobrazovat podpisy";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Vytváøení zpráv";
$prefsStrings["6.1"] = "Zobrazit kontakty";
$prefsStrings["6.2"] = "Zobrazit CC/BCC polo¾ky";
$prefsStrings["6.3"] = "Zavøít po odeslání";
$prefsStrings["6.4"] = "Vytvoøení zpráv uvnitø rámeèku";
$prefsStrings["7.0.0"] = "Nastavení disku &amp; schránek";
$prefsStrings["7.0.1"] = "Nastavení kvóty";
$prefsStrings["7.1"] = "Zobraz poèet nepøeètených zpráv";
$prefsStrings["7.2"] = "Obnovit seznam schránek ka¾dých %n vteøin";

$pref_col_title = "Uspoøádání sloupcù";
$pref_colstr["order"] = "Poøadí";
$pref_colstr["field"] = "Polo¾ka";
$pref_col_label['c'] = "Za¹krtávací políèko";
$pref_col_label['a'] = "Pøílohy";
$pref_col_label['m'] = "Pøíznaky/stav";

$sort_fields=array("DATE"=>"Datumu", "SUBJECT"=>"Pøedmìtu", "SIZE"=>"Velikosti");
$sort_orders=array("ASC"=>"Vzestupnì", "DESC"=>"Sestupnì");

$prefsButtonStrings = array( 0=> "Ulo¾it", 1=>"Pou¾ít", 2=> "Zru¹it", 3=> "Výchozí nastavení");

$prefs_new_user = "Zdá se ¾e jste se sem pøihlásili poprvé...vítejte!<br>";
$prefs_new_user .= "Prosím projdìte si následující nastavení, poté klepnìte na \"Pou¾ít\"";
?>