<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/eng/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Einstellungen";
$prefHeaderStrings[1] = "Allgemein";
$prefHeaderStrings[2] = "Farben";
$prefHeaderStrings[3] = "Identit&auml;ten";
$prefHeaderStrings[4] = "Filter";
											
$prefsStrings["0.0"] = "Identit&auml;t";
$prefsStrings["0.1"] = "Name: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "&Auml;nderungen k&ouml;nnen auf der \"%s\"-Seite vorgenommen werde.";
$prefsStrings["0.4"] = "Nicht gesetzt";
$prefsStrings["1.0"] = "Sprache und Zeichensatz";
$prefsStrings["1.1"] = "Sprache: ";
$prefsStrings["1.2"] = "Zeichensatz: ";
$prefsStrings["1.3"] = "Zeitzone: ";
$prefsStrings["1.4"] = "Zeitanzeige: ";
$prefsStrings["1.5"] = array(12=>"12 Stunden", 24=>"24 Stunden");
$prefsStrings["2.0"] = "Listenansicht";
$prefsStrings["2.1"] = "Zeige bis zu ";
$prefsStrings["2.2"] = " Eintr&auml;ge";
$prefsStrings["2.3"] = "Gr&ouml;&szlig;e anzeigen";
$prefsStrings["2.4"] = "Sortiere nach: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Sortierung: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Ordnerliste anzeigen";
$prefsStrings["2.9"] = "Ordnerlistengr&ouml;&szlig;e ";
$prefsStrings["2.10"] = "Alle %n Sekunden auf neue Nachrichten pr&uuml;fen";
$prefsStrings["2.11"] = "Navigationsleiste anzeigen %m";
$prefsStrings["2.12"] = array("b"=>"unten", "t"=>"oben", "bt"=>"oben und unten");
$prefsStrings["2.13"] = "Spalten neu anordnen: ";
$prefsStrings["2.14"] = "hier klicken";
$prefsStrings["3.0"] = "Automatisches Speichern";
$prefsStrings["3.1"] = "Gesendete Nachrichten speichern.";
$prefsStrings["3.2"] = "Gesendete Nachrichten in Ordner ";
$prefsStrings["3.3"] = " ablegen.";
$prefsStrings["3.4"] = "Papierkorb automatisch leeren.";
$prefsStrings["3.5"] = "Gel&ouml;schte Nachrichten in Ordner ";
$prefsStrings["3.6"] = " ablegen.";
$prefsStrings["3.7"] = "Wurzel-Verzeichnis: ";
$prefsStrings["3.8"] = "Anderes Verzeichnis";
$prefsStrings["3.9"] = "Nur best&auml;tigte Ordner anzeigen";
$prefsStrings["3.10"] = "Quota anzeigen %m";
$prefsStrings["3.11"] = array(""=>"nicht anzeigen", "m"=>"in Nachrichtenliste", "f"=>"in Ordnerliste");
$prefsStrings["4.0"] = "Nachrichtendarstellung";
$prefsStrings["4.1"] = "In Frame anzeigen";
$prefsStrings["4.2"] = "Farbig zitieren";
$prefsStrings["4.3"] = "(Diese Option verlangsamt m&ouml;glicherweise das System.)";
$prefsStrings["4.4"] = "URLs als Links darstellen";
$prefsStrings["4.5"] = "HTML-Nachrichten anzeigen";
$prefsStrings["4.6"] = "Bilder direkt im Text zeigen";
$prefsStrings["4.7"] = "Navigationslinks anzeigen";
$prefsStrings["4.8"] = "Nachrichten nicht markieren beim Durchwandern.";
$prefsStrings["5.0"] = "Signatur";
$prefsStrings["5.1"] = "Signatur standardm&auml;&szlig;ig einf&uuml;gen";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Nachrichten erstellen";
$prefsStrings["6.1"] = "Zeige Kontakte";
$prefsStrings["6.2"] = "Zeige CC/BCC-Felder";
$prefsStrings["6.3"] = "Nach dem Senden schlie&szlig;en";
$prefsStrings["6.4"] = "Nachrichtenerstellung im Frame";
$prefsStrings["7.0.0"] = "Ordner Optionen";
$prefsStrings["7.0.1"] = "Quota Optionen";
$prefsStrings["7.1"] = "Zeige Anzahl der ungelesenen Nachrichten";
$prefsStrings["7.2"] = "Ordnerliste alle %n Sekunden neu laden";

$pref_col_title = "Spalten neu anordnen";
$pref_colstr["order"] = "Reihenfolge";
$pref_colstr["field"] = "Feld";
$pref_col_label['c'] = "Markierungsbox";
$pref_col_label['a'] = "Dateianh&auml;ge";
$pref_col_label['m'] = "Flags/Status";

$sort_fields=array("DATE"=>"Datum", "SUBJECT"=>"Betreff", "SIZE"=>"Gr&ouml;&szlig;e");
$sort_orders=array("ASC"=>"aufsteigend", "DESC"=>"absteigend");

$prefsButtonStrings = array( 0=> "Speichern", 1=>"Speichern", 2=> "Abbrechen", 3=> "Standardeinstellungen");

$prefs_new_user = "Sie scheinen sich das erste Mal eingeloggt zu haben. Willkommen!<br>";
$prefs_new_user .= "Bitte nehmen Sie sich einen Moment Zeit, um die folgenden Einstellungen zu &uuml;berpr&uuml;fen und ggf. anzupassen, und dr&uuml;cken Sie anschliessend auf \"Speichern\".";
?>
