<?php
/********************************************************
	lang/nn_NO/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translation by Leif Halvard Silli <hyperlekken@lenk.no>

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Val";
$prefHeaderStrings[1] = "&Aring;lment";
$prefHeaderStrings[3] = "Tilnamn";
$prefHeaderStrings[2] = "Utsj&aring;nad";

											
$prefsStrings["0.0"] = "Fyrevalt tilnamn";
$prefsStrings["0.1"] = "Namn: ";
$prefsStrings["0.2"] = "E-posttilskrift: ";
$prefsStrings["0.3"] = "Endringar kan gjerast p&aring; sida for \"%s\".";
$prefsStrings["0.4"] = "Uspesifisera";
$prefsStrings["1.0"] = "Stadsetning";
$prefsStrings["1.1"] = "M&aring;lvald: ";
$prefsStrings["1.2"] = "Teiknkoding: ";
$prefsStrings["1.3"] = "Tidssona: ";
$prefsStrings["1.4"] = "Klokkeval: ";
$prefsStrings["1.5"] = array(12=>"12-timarsklokka", 24=>"24-timarsklokka");
$prefsStrings["2.0"] = "Brevyversyni";
$prefsStrings["2.1"] = "Tak med upp mot ";
$prefsStrings["2.2"] = " brev um gongen";
$prefsStrings["2.3"] = "Tak med storleiken";
$prefsStrings["2.4"] = "Rada brevi etter ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Rekkjefylgd: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Vis permyversynet";
$prefsStrings["2.9"] = "Permyversyn";
$prefsStrings["2.10"] = "Sj&aring; etter post kvart %n sekund";
$prefsStrings["2.11"] = "Syn verktylina p&aring; %m";
$prefsStrings["2.12"] = array("b"=>"botnen", "t"=>"toppen", "bt"=>"botn og topp");
$prefsStrings["2.13"] = "For umflytjing p&aring; kolonnor: ";
$prefsStrings["2.14"] = "klikka her ";
$prefsStrings["3.0"] = "Brevhandtering";
$prefsStrings["3.1"] = "Lagra all utgangande post.";
$prefsStrings["3.2"] = "Lagra utposten i: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "T&oslash;m s&oslash;pli strakst nokot hamnar der.";
$prefsStrings["3.5"] = "Kasta brevs&oslash;pel i: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Hovud-/rotperm: ";
$prefsStrings["3.8"] = "Annan";
$prefsStrings["3.9"] = "L&oslash;yn permar ute av bruk";
$prefsStrings["3.10"] = "Syn tilgjengeleg lagerplass %m";
$prefsStrings["3.11"] = array(""=>"ikkje syn", "m"=>"i brevyversynet", "f"=>"i permyversynet");
$prefsStrings["4.0"] = "Brevopning";
$prefsStrings["4.1"] = "Brev vert opna i same vindauga";
$prefsStrings["4.2"] = "Hermingar vert fargelagde";
$prefsStrings["4.3"] = "(N&aring;r valet er p&aring;slege, kan det gjera systemet treigt.)";
$prefsStrings["4.4"] = "URLar gjerest um til lenker";
$prefsStrings["4.5"] = "Vevbrev skal opnast i html-l&aelig;gje";
$prefsStrings["4.6"] = "Bil&aelig;te vert synte inne i teksti";
$prefsStrings["4.7"] = "Syn lenker til &aring; bledja med.";
$prefsStrings["4.8"] = "Bledjing mogelegt (brevi vert ikkje merkte med &laquo;opna&ldquo;) .";
$prefsStrings["5.0"] = "Signatur";
$prefsStrings["5.1"] = "Signaturen er fyrevalt &aring; takast med ";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Brevark";
$prefsStrings["6.1"] = "Syn adresseboki";
$prefsStrings["6.2"] = "Syn felti &laquo;Avrit &aring;t&raquo;/&laquo;L&oslash;yndeavrit &aring;t&raquo;";
$prefsStrings["6.3"] = "Lat att etter sending";
$prefsStrings["6.4"] = "Brevarket i same r&aring;ma som brevyversyni";
$prefsStrings["7.0.0"] = "Skive- &amp; permval";
$prefsStrings["7.0.1"] = "Hermingsval";
$prefsStrings["7.1"] = "Syn tal uopna brev";
$prefsStrings["7.2"] = "Friska upp permyversynet kvart %n sekund";

// added based on english version

$pref_col_title = "Flyt um kolonnone";
$pref_colstr["order"] = "Ordna";
$pref_colstr["field"] = "Felt";
$pref_col_label['c'] = "Avkryssing";
$pref_col_label['a'] = "Vedlegg";
$pref_col_label['m'] = "Fanor/stoder";

// end added based on english version

$sort_fields=array("DATE"=>"Tidspunkt", "SUBJECT"=>"Emne", "SIZE"=>"Storleik");
$sort_orders=array("ASC"=>"Aukande", "DESC"=>"Minkande");

$prefsButtonStrings = array( 0=> "Uppdatera", 1=>"Set i verk", 2=> "Brjot av", 3=> "I fyrevalsl&aelig;gje");

$prefs_new_user = "Du tykkjest ha logga deg inn hit for fyrste gong... Ver velkomen!<br>";
$prefs_new_user .= "Ver greid og gakk gjenom fylgjande innstillingar fyre du trykkjer p&aring; \"Set i verk\"-knappen.";

?>