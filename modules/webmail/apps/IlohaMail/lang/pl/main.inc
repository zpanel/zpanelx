<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/pl/main.inc
	(C)2002 Marcin Piliszczuk <marti@net26.pl>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "Usuniêto $num wiadomo¶".($num>1?"ci":"æ");
	}else if ($type == "move"){
		$str = "Przeniesiono $num wiadomo¶".($num>1?"ci":"æ")." do $folder";
	}
	
	return $str;
}

$mainStrings[0] = "Wy¶wietlonych %p z %n wiadomo¶ci";
$mainStrings[1] = " wiadomo¶ci";
$mainStrings[2] = "Poprzednia";
$mainStrings[3] = " wiad.";
$mainStrings[4] = "Nastêpna";
$mainStrings[5] = " wiad.";
$mainStrings[6] = "Temat";
$mainStrings[7] = "Do";
$mainStrings[8] = "Od";
$mainStrings[9] = "Data";
$mainStrings[10] = "Usuñ";
$mainStrings[11] = "Opró¿nij kosz";
$mainStrings[12] = "Plik";
$mainStrings[13] = "Szukaj";
$mainStrings[14] = "Rozmiar";
$mainStrings[15] = "bez tytu³u";
$mainStrings[16] = "Poka¿";
$mainStrings[17] = "Pobierz nowe";
$mainStrings[18] = "Usuñ wszystkie";
$mainStrings[19] = "Anuluj";


$mainErrors[0] = "Brak wiadomo¶ci w tym folderze";
$mainErrors[1] = "Brak wiadomo¶ci.";
$mainErrors[2] = "Nie mo¿na przenieœæ wiadomo¶ci do kosza.";
$mainErrors[3] = "Nie mo¿na oznaczyæ wiadomo¶ci jako usuniêtych.";
$mainErrors[4] = "Nie mo¿na przenieœæ wiadomoœci.";
$mainErrors[5] = "Nie okre¶lono folderu dla kosza.";
$mainErrors[6] = "Nie mo¿na opró¿niæ kosza";
$mainErrors[7] = "Czy na pewno chcesz usun±æ WSZYSTKIE wiadomo¶ci w %f?";



?>