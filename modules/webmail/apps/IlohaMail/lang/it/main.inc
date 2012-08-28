<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/it/main.inc
	
	Italian Translation by Amos B. <mriya3@surfeu.ch>
	Original english file (C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "$num messagg".($num>1?"i":"o")." eliminat".($num>1?"i":"o");
	}else if ($type == "move"){
		$str = "$num spostat".($num>1?"i":"o")." in $folder";
	}
	
	return $str;
}

$mainStrings[0] = " %p di %n visualizzato/i";
$mainStrings[1] = " messaggi trovati";
$mainStrings[2] = "<-- ";
$mainStrings[3] = " mess. precedenti";
$mainStrings[4] = "";
$mainStrings[5] = " mess. successivi-->";
$mainStrings[6] = "Oggetto";
$mainStrings[7] = "A";
$mainStrings[8] = "Da";
$mainStrings[9] = "Data";
$mainStrings[10] = "Elimina";
$mainStrings[11] = "Svuota cestino";
$mainStrings[12] = "Sposta";
$mainStrings[13] = "Cerca";
$mainStrings[14] = "Dimensione";
$mainStrings[15] = "senza nome";
$mainStrings[16] = "Visualizza";
$mainStrings[17] = "Controlla nuova";
$mainStrings[18] = "Cancella tutti";
$mainStrings[19] = "Annulla";
$mainStrings[20] = "Segna come %b";
$mainStrings[21] = "Letto";
$mainStrings[22] = "Non letto";
$mainStrings[23] = "Seleziona...";


$mainErrors[0] = "Nessun messaggio trovato in questa cartella";
$mainErrors[1] = "Nessun messaggio in questa cartella";
$mainErrors[2] = "Impossibile spostare nel cestino.";
$mainErrors[3] = "Impossibile marcare messaggi come cancellati.";
$mainErrors[4] = "Impossibile muovere messaggi.";
$mainErrors[5] = "Cartella per il Cestino non specificata.";
$mainErrors[6] = "Impossibile svuotare il cestino";
$mainErrors[7] = "Sei sicuro di voler cancellare TUTTI i messaggi di %f?";



?>
