<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/it/prefs.inc
	
	Italian Translation by Amos B. <mriya3@surfeu.ch>
	Original english file (C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Preferenze";
$prefHeaderStrings[1] = "Generale";
$prefHeaderStrings[2] = "Colori";
$prefHeaderStrings[3] = "Identità";

											
$prefsStrings["0.0"] = "Identit&agrave;";
$prefsStrings["0.1"] = "Nome: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "Le modifiche si effettuano nella pagina \"%s\".";
$prefsStrings["1.0"] = "Parametri internazionali";
$prefsStrings["1.1"] = "Lingua interfaccia: ";
$prefsStrings["1.2"] = "Set di caratteri: ";
$prefsStrings["1.3"] = "Fuso orario: ";
$prefsStrings["1.4"] = "Formato ore: ";
$prefsStrings["1.5"] = array(12=>"12 ore", 24=>"24 ore");
$prefsStrings["2.0"] = "Opzioni di visualizzazione";
$prefsStrings["2.1"] = "Mostra fino a ";
$prefsStrings["2.2"] = " elementi";
$prefsStrings["2.3"] = "Mostra dimensione";
$prefsStrings["2.4"] = "Ordinamento: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Ordina per: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Mostra lista cartelle";
$prefsStrings["2.9"] = "Larghezza colonna Lista Cartelle";
$prefsStrings["2.10"] = "Controlla nuovi messaggi ogni  %n secondi";
$prefsStrings["2.11"] = "Mostra tool bar  %m ";
$prefsStrings["2.12"] = array("b"=>"in basso", "t"=>"in alto", "bt"=>"in alto  e in basso");
$prefsStrings["2.13"] = "Ordinamento colonne: ";
$prefsStrings["2.14"] = "clicca qui";
$prefsStrings["3.0"] = "Salvataggio automatico";
$prefsStrings["3.1"] = "Salva email inviate.";
$prefsStrings["3.11"] = array(""=>"non mostrare", "m"=>"nella lista messaggi", "f"=>"nella lista cartelle");
$prefsStrings["3.2"] = "Salva i messaggi inviati nella cartella: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Svuota automaticamente cestino";
$prefsStrings["3.5"] = "Sposta messaggi eliminati nella cartella: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Cartella principale (radice): ";
$prefsStrings["3.8"] = "Altro";
$prefsStrings["3.9"] = "Nascondi cartelle non sottoscritte";
$prefsStrings["3.10"] = "Mostra spazio disco %m";
$prefsStrings["4.0"] = "Opzioni di visualizzazione messaggi";
$prefsStrings["4.1"] = "Visualizza in una frame";
$prefsStrings["4.2"] = "Colora citazioni";
$prefsStrings["4.3"] = "Attivare questa opzione potrebbe rallentare il sistema.";
$prefsStrings["4.4"] = "Converti URLs in collegamenti";
$prefsStrings["4.5"] = "Mostra messaggi HTML";
$prefsStrings["4.6"] = "Includi visualizzazione immagini";
$prefsStrings["4.7"] = "Mostra i link di navigazione";
$prefsStrings["4.8"] = "Non contrassegnare i messaggi durante l'esecuzione";
$prefsStrings["5.0"] = "Firma";
$prefsStrings["5.1"] = "Visualizza la firma";
$prefsStrings["5.2"] = "";


$prefsStrings["6.0"] = "Invio messaggi";
$prefsStrings["6.1"] = "Mostra contatti";
$prefsStrings["6.2"] = "Mostra i campi CC/BCC ";
$prefsStrings["6.3"] = "Chiudi  dopo l'invio";
$prefsStrings["6.4"] = "Scrivi messaggio all'interno del frame";
$prefsStrings["7.0.0"] = "Disco &amp; Cartelle Opzioni";
$prefsStrings["7.0.1"] = "Spazio disco opzioni";
$prefsStrings["7.1"] = "Mostra numero di email non lette";
$prefsStrings["7.2"] = "Aggiorna la lista cartelle ogni  %n secondi";

$pref_col_title = "Ordine Colonne";
$pref_colstr["order"] = "Ordine";
$pref_colstr["field"] = "Campo";
$pref_col_label['c'] = "Check box";
$pref_col_label['a'] = "Allegati";
$pref_col_label['m'] = "Flags/stato";


$sort_fields=array("DATE"=>"Data", "SUBJECT"=>"Oggetto", "SIZE"=>"Dimensione");
$sort_orders=array("ASC"=>"Ascendente", "DESC"=>"Descrescente");

$prefsButtonStrings = array( 0=> "Aggiorna", 1=>"Applica", 2=> "Cancella", 3=> "Predefiniti");

$prefs_new_user = "E' la vostra prima visita...benvenuto!<br>";
$prefs_new_user .= "Riempite questo formulario, per favore, e cliccate su \"Applica\"";

?>
