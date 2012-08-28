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
		$str = " $num Nachricht".($num>1?"en":"")." gel&ouml;scht";
	}else if ($type == "move"){
		$str = " $num Nachricht".($num>1?"en":"")." nach $folder verschoben";
	}
	
	return $str;
}

$mainStrings[0] = "Zeige %p von insgesamt %n Nachrichten";
$mainStrings[1] = " Nachrichten gefunden";
$mainStrings[2] = "Vorherige";
$mainStrings[3] = " Nachrichten";
$mainStrings[4] = "N&auml;chste";
$mainStrings[5] = " Nachrichten";
$mainStrings[6] = "Betreff";
$mainStrings[7] = "An";
$mainStrings[8] = "Absender";
$mainStrings[9] = "Datum";
$mainStrings[10] = "L&ouml;schen";
$mainStrings[11] = "Papierkorb leeren";
$mainStrings[12] = "Verschieben";
$mainStrings[13] = "Suche";
$mainStrings[14] = "Gr&ouml;&szlig;e";
$mainStrings[15] = "kein Titel";
$mainStrings[16] = "Anzeigen";
$mainStrings[17] = "Aktualisieren";
$mainStrings[18] = "Alle l&ouml;schen";
$mainStrings[19] = "Abbrechen";
$mainStrings[20] = "Markieren als %b";
$mainStrings[21] = "gelesen";
$mainStrings[22] = "ungelesen";
$mainStrings[23] = "Auswahl...";
$mainStrings[24] = "Filter anwenden";
$mainStrings[25] = "Markiere";
$mainStrings[26] = "wichtig";


$mainErrors[0] = "Keine Nachrichten in diesem Ordner gefunden";
$mainErrors[1] = "Keine Nachrichten in diesem Ordner";
$mainErrors[2] = "Nachrichten konnten nicht in den Papierkorb verschoben werden.";
$mainErrors[3] = "Nachrichten konnten nicht als gel&ouml;scht markiert werden.";
$mainErrors[4] = "Nachrichten konnten nicht verschoben werden.";
$mainErrors[5] = "Ordner f&uuml;r den Papierkorb ist nicht definiert.";
$mainErrors[6] = "Konnte Papierkorb nicht leeren.";
$mainErrors[7] = "Wollen Sie wirklich ALLE Nachrichten in %f l&ouml;schen?";
$mainErrors[8] = "Klicken Sie auf die Pfeile unter den Spaltenk&ouml;pfen um die Reihenfolge zu &auml;ndern.<br>Schlie&szlig;en Sie das Fenster um die &Auml;nderungen zu &uuml;bernehmen.";

?>
