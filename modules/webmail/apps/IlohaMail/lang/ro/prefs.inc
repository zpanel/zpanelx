<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/eng/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Preferinte";
$prefHeaderStrings[1] = "General";
$prefHeaderStrings[2] = "Culori";

											
$prefsStrings["0.0"] = "Identitate";
$prefsStrings["0.1"] = "Nume: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["1.0"] = "International";
$prefsStrings["1.1"] = "Interfata: ";
$prefsStrings["1.2"] = "Set Caractere: ";
$prefsStrings["1.3"] = "Zona Timp/Data: ";
$prefsStrings["2.0"] = "Optiuni vizualiare";
$prefsStrings["2.1"] = "Afisare pana la ";
$prefsStrings["2.2"] = " articole";
$prefsStrings["2.3"] = "Afisare marime";
$prefsStrings["2.4"] = "Sortare dupa: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Ordine sortare: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Afisare lista directoare";
$prefsStrings["3.0"] = "Optiuni Auto-Salvare";
$prefsStrings["3.1"] = "Salvare articole trimise.";
$prefsStrings["3.2"] = "Salvarea mesajelor trimise in directorul: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Stergerea automata a <b>Mesaje sterse</b>Automatically delete trash";
$prefsStrings["3.5"] = "Muta articole sterse in directorul: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Directorul Radacina: ";
$prefsStrings["3.8"] = "Altul";
$prefsStrings["4.0"] = "Optiuni vizualizare mesaj";
$prefsStrings["4.1"] = "Vizualizare in cadru (frame)";
$prefsStrings["4.2"] = "Coloreaza citatele";
$prefsStrings["4.3"] = "Selectarea acestor optiuni poate duce la incetinirea aplicatiei!";
$prefsStrings["4.4"] = "Convertire URL-uri in legaturi";
$prefsStrings["4.5"] = "Afisarea mesajelor HTML";
$prefsStrings["4.6"] = "Afisarea imaginilor in mesaj";
$prefsStrings["5.0"] = "Semnatura";
$prefsStrings["5.1"] = "Afisarea semnaturii implicita";
$prefsStrings["5.2"] = "";


$sort_fields=array("DATE"=>"Data", "SUBJECT"=>"Subiect", "SIZE"=>"Marime");
$sort_orders=array("ASC"=>"Ascendent", "DESC"=>"Descendent");

$prefsButtonStrings = array( 0=> "Update", 1=>"Aplica", 2=> "Anulare", 3=> "Implicit");

$prefs_new_user = "Se pare ca va logati pentru prima oara ...Bine ati venit!<br>";
$prefs_new_user .= "Va rugam sa faceti setarile dorite, dupa care apasati \"Apply\"";
?>