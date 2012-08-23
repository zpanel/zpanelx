<?php
/********************************************************
	lang/eng/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Pirmumo nustatymai";
$prefHeaderStrings[1] = "Bendri";
$prefHeaderStrings[3] = "Profiliai";
$prefHeaderStrings[2] = "Iðvaizda";
$prefHeaderStrings[4] = "Filtrai";

											
$prefsStrings["0.0"] = "Pagrindinis profilis nutylint";
$prefsStrings["0.1"] = "Vardas: ";
$prefsStrings["0.2"] = "El. paðtas: ";
$prefsStrings["0.3"] = "Pakeitimai gali bûti padaryti \"%s\" puslapyje.";
$prefsStrings["0.4"] = "Nenurodyta";
$prefsStrings["1.0"] = "Tarptautinis";
$prefsStrings["1.1"] = "Aplinka: ";
$prefsStrings["1.2"] = "Simboliø rinkinys: ";
$prefsStrings["1.3"] = "Laiko zona: ";
$prefsStrings["1.4"] = "Laiko nustatymas: ";
$prefsStrings["1.5"] = array(12=>"12-os valandø laikrodis", 24=>"24-iø valandø laikrodis");
$prefsStrings["2.0"] = "Parodyti perþiûrëjimo nustatymus";
$prefsStrings["2.1"] = "Rodyti iki ";
$prefsStrings["2.2"] = " elementø";
$prefsStrings["2.3"] = "Rodyti dydá";
$prefsStrings["2.4"] = "Rûðiuoti pagal: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Rûðiavimo tvarka: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Rodyti aplankø sàraðà";
$prefsStrings["2.9"] = "Aplankø sàraðo dydþiai";
$prefsStrings["2.10"] = "Tikrinti naujai gautus laiðkus kas %n sekundþiø";
$prefsStrings["2.11"] = "Rodyti árankiø rinkiná %m";
$prefsStrings["2.12"] = array("b"=>"viduryje", "t"=>"virðuje", "bt"=>"virðuje ir viduryje");
$prefsStrings["2.13"] = "Pertvarkyti stulpelius: ";
$prefsStrings["2.14"] = "spausti èia";
$prefsStrings["2.15"] = "Rodyti filtrà/greitos paieðkos árankiø rinkiná";
$prefsStrings["3.0"] = "Automatinio iðsisaugojimo nustatymai";
$prefsStrings["3.1"] = "Iðsaugoti iðsiøstus laiðkus.";
$prefsStrings["3.2"] = "Iðsaugoti iðsiøstus laiðkus aplanke: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Automatiðkai iðvalyti ðiûkðliadëþæ";
$prefsStrings["3.5"] = "Perkelti iðtrintus elementus á aplankà: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Pagrindinë direktorija: ";
$prefsStrings["3.8"] = "Kita";
$prefsStrings["3.9"] = "Paslëpti neprisiraðytus aplankus";
$prefsStrings["3.10"] = "Rodyti kvotà %m";
$prefsStrings["3.11"] = array(""=>"nerodyti", "m"=>"laiðkø sàraðe", "f"=>"aplankø sàraðe");
$prefsStrings["4.0"] = "Laiðko rodymo nustatymai";
$prefsStrings["4.1"] = "Rodyti rëmuose";
$prefsStrings["4.2"] = "Nuspalvinti kvotas";
$prefsStrings["4.3"] = "Ájungus ðá nustatymà gali sulëtëti jûsø sistemos darbas.";
$prefsStrings["4.4"] = "Konvertuoti adresus á nuorodas";
$prefsStrings["4.5"] = "Rodyti HTML laiðkus";
$prefsStrings["4.6"] = "Rodyti paveikslëlius linijoje";
$prefsStrings["4.7"] = "Rodyti navigacijos nuorodas";
$prefsStrings["4.8"] = "Neþymëti laiðkø kai po juos vaikðèiojama";
$prefsStrings["5.0"] = "Paraðas";
$prefsStrings["5.1"] = "Rodyti paraðà nutylint";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Raðyti";
$prefsStrings["6.1"] = "Rodyti kontaktus";
$prefsStrings["6.2"] = "Rodyti Kam kopija/Kam nematoma kopija laukus";
$prefsStrings["6.3"] = "Uþdaryti po iðsiuntimo";
$prefsStrings["6.4"] = "Raðyti rëmo viduje";
$prefsStrings["6.5"] = "Teksto dëþës dydis: ";
$prefsStrings["7.0.0"] = "Disko &amp; Aplanko nustatymai";
$prefsStrings["7.0.1"] = "Kvotø nustatymai";
$prefsStrings["7.1"] = "Rodyti neperskaitytø kieká";
$prefsStrings["7.2"] = "Atnaujinti aplankø sàraðà kas %n sekundes";

$pref_col_title = "Pertvarkyti stulpelius";
$pref_colstr["order"] = "Tvarka";
$pref_colstr["field"] = "Laukas";
$pref_col_label['c'] = "Paþymëjimas";
$pref_col_label['a'] = "Prisegtos bylos";
$pref_col_label['m'] = "Vëliavëlës/statusas";

$sort_fields=array('DATE'=>'Iðsiuntimo laikas', 'SUBJECT'=>'Tema', 'SIZE'=>'Dydis');
$sort_fields['INTERNALDATE'] = 'Gavimo laikas';
$sort_orders=array("ASC"=>"Didëjanèia tvarka", "DESC"=>"Maþëjanèia tvarka");

$prefsButtonStrings = array( 0=> "Atnaujinti", 1=>"Taikyti", 2=> "Atðaukti", 3=> "Pagal nutylëjimà");

$prefs_new_user = "Jûs prisijungëte pirmà kartà...sveikiname!<br>";
$prefs_new_user .= "Praðome perþvelgti ðiuos nustatymus, tada spauskite \"Taikyti\"";
?>