<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/eng/main.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "Delete $num message".($num>1?"s":"");
	}else if ($type == "move"){
		$str = "Moved $num message".($num>1?"s":"")." to $folder";
	}
	
	return $str;
}

$mainStrings[0] = "Afiseaza %p din %n";
$mainStrings[1] = " mesaje gasite";
$mainStrings[2] = "Precedentele";
$mainStrings[3] = " mesaje";
$mainStrings[4] = "Urmatoarele";
$mainStrings[5] = " mesaje";
$mainStrings[6] = "Subiect";
$mainStrings[7] = "Catre";
$mainStrings[8] = "De la";
$mainStrings[9] = "Data";
$mainStrings[10] = "Sterge";
$mainStrings[11] = "Stergere definitiva";
$mainStrings[12] = "Fisier";
$mainStrings[13] = "Cautare";
$mainStrings[14] = "Marime";
$mainStrings[15] = "fara_titlu";
$mainStrings[16] = "Afisare";
$mainStrings[17] = "Verifica mesaje noi";
$mainStrings[18] = "Sterge tot";
$mainStrings[19] = "Anulare";


$mainErrors[0] = "Nu s-au gasit mesaje in acest director";
$mainErrors[1] = "Nu sunt mesaje in acest director";
$mainErrors[2] = "Nu se pot muta mesajele la <b>Mesaje sterse</b>.";
$mainErrors[3] = "Nu se pot marca mesajele ca mesaje sterse.";
$mainErrors[4] = "Nu se pot muta mesajele.";
$mainErrors[5] = "Directorul <b>Mesaje Sterse</b> nu este specificat.";
$mainErrors[6] = "Nu se poate goli directorul <b>Mesaje Sterse</b>";
$mainErrors[7] = "Sunteti sigur ca doriti sa stergeti toate mesajele in %f?";



?>