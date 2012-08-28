<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/no/main.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translated by Tor Olsen <tor@monet.no>
********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "Slett $num melding".($num>1?"s":"");
	}else if ($type == "move"){
		$str = "Flyttet $num melding".($num>1?"s":"")." to $folder";
	}
	
	return $str;
}

$mainStrings[0] = "Viser %p of %n";
$mainStrings[1] = " meldinger funnet";
$mainStrings[2] = "Forrige";
$mainStrings[3] = " meld.";
$mainStrings[4] = "Neste";
$mainStrings[5] = " meld.";
$mainStrings[6] = "Emne";
$mainStrings[7] = "Til";
$mainStrings[8] = "Fra";
$mainStrings[9] = "Dato";
$mainStrings[10] = "Slett";
$mainStrings[11] = "Tøm søppel";
$mainStrings[12] = "Fil";
$mainStrings[13] = "Søk";
$mainStrings[14] = "Størrelse";
$mainStrings[15] = "navnløs";
$mainStrings[16] = "Vis";
$mainStrings[17] = "Sjekk nye";
$mainStrings[18] = "Slett Alle";
$mainStrings[19] = "Avbryt";
$mainStrings[20] = "Vis som %b";
$mainStrings[21] = "Lest";
$mainStrings[22] = "Ulest";
$mainStrings[23] = "Velg...";


$mainErrors[0] = "Ingen meldinger funnet i denne mappen";
$mainErrors[1] = "Ingen meldinger i denne mappen";
$mainErrors[2] = "Kunne ikke legge meldinger i søppelkorgen.";
$mainErrors[3] = "Kunne ikke merke meldingene som slettet.";
$mainErrors[4] = "Kunne ikke flytte meldingene.";
$mainErrors[5] = "Søppelkorg ikke spesifisert.";
$mainErrors[6] = "Kunne ikke tømme søppelkorg";
$mainErrors[7] = "Er du sikker du vil slette alle meldingene i %f?";
$mainErrors[8] = "Flytt kolonnetittelen ved å klikk på pilene under.<br>Lukk vinduet når du er ferdig.";



?>