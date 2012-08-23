<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/hu/main.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "$num üzenet(ek) törlése".($num>1?"s":"");
	}else if ($type == "move"){
		$str = "$num üzenet áthelyezése".($num>1?"s":"")." to $folder";
	}
	
	return $str;
}

$mainStrings[0] = "Látható %p / %n";
$mainStrings[1] = " üzenet található";
$mainStrings[2] = "Elözö";
$mainStrings[3] = " üzenetek";
$mainStrings[4] = "Következö";
$mainStrings[5] = " üzenetek";
$mainStrings[6] = "Téma";
$mainStrings[7] = "Címzett";
$mainStrings[8] = "Feladó";
$mainStrings[9] = "Dátum ";
$mainStrings[10] = "Törlés";
$mainStrings[11] = "Szemetes törlése";
$mainStrings[12] = "Üzenet áthelyezése";
$mainStrings[13] = "Keresés";
$mainStrings[14] = "Méret";
$mainStrings[15] = "Cimtelen";
$mainStrings[16] = "Megmutat";
$mainStrings[17] = "Újra megnéz ";
$mainStrings[18] = "Mindent töröl";
$mainStrings[19] = "Mégse";
$mainStrings[20] = "Megjelöl mint %b";
$mainStrings[21] = "Olvasott";
$mainStrings[22] = "Olvasatlan";
$mainStrings[23] = "Kiválaszt...";


$mainErrors[0] = "Ebben a mappában nem található üzenet";
$mainErrors[1] = "Ebben a mappában nincs üzenet";
$mainErrors[2] = "Nem lehetet az üzenetet törölni.";
$mainErrors[3] = "Nem lehetet az üznetetet megjelölni.";
$mainErrors[4] = "Nem lehetett az üzenetet áthelyezni .";
$mainErrors[5] = "A szemetes nem lett kijelölve.";
$mainErrors[6] = "A szemetest nem lehetett üriteni";
$mainErrors[7] = "Biztos abban, hogy minden üzenetet törölni kíván a(z) %f mappából ?";
$mainErrors[8] = "Az oszlop mozgatásához kattints a fejlécnél található nyílra.<br>Ha kész vagy, egyszerûen zárd be az ablakot.";



?>