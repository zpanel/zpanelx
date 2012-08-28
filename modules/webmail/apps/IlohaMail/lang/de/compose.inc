<?php
include("../lang/eng/compose.inc"); // <-- do not delete
/********************************************************
	lang/eng/compose.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$composeHStrings[0] = "Betreff";
$composeHStrings[1] = "Von";
$composeHStrings[2] = "An";
$composeHStrings[3] = "CC";
$composeHStrings[4] = "BCC";
$composeHStrings[5] = "Datum";


$composeStrings[0] = "Neue Nachricht";
$composeStrings[1] = "Nachricht senden";
$composeStrings[2] = "Datei anh&auml;ngen";
$composeStrings[3] = "Signatur anf&uuml;gen";
$composeStrings[4] = "Anhang";
$composeStrings[5] = "Kontakte anzeigen";
$composeStrings[6] = "Kontakte ausblenden";
$composeStrings[7] = "Nachricht:";
$composeStrings[8] = "Hinzu";
$composeStrings[9] = "Am %d schrieb %s:\n";
$composeStrings[10] = "--- Urspruengliche Nachricht ---\n";
$composeStrings[11] = "Fenster schlie&szlig;en";

$composeStrings[12] = "Rechschreibpr&uuml;fung";
$composeStrings[13] = "&Auml;nderungen &uuml;bernehmen";
$composeStrings[14] = "keine &Auml;nderungen";
$composeStrings[15] = "M&ouml;gliche Rechtschreibfehler";
$composeStrings[16] = "&Uuml;bernommene Rechtschreib&auml;nderungen";
$composeStrings[17] = "ignoreren";
$composeStrings[18] = "l&ouml;schen";

$composeStrings["title"] = "Neue Nachricht";

$composeErrors[0] = "Das Betreff-Feld ist leer. Bitte geben Sie einen Betreff an oder klicken Sie erneut auf \"".$composeStrings[1]."\", um die Nachricht zu verschicken.";
$composeErrors[1] = "Das Absender-Feld ist leer. Bitte tragen Sie Ihre Email-Adresse ein.";
$composeErrors[2] = "Das Empf&auml;nger-Feld ist leer. bitte geben Sie eine Ziel-Adresse ein.";
$composeErrors[3] = "Hochladen der Datei fehlgeschlagen.";
$composeErrors[4] = "Keine Datei(en) empfangen.";
$composeErrors[5] = "Um Missbrauch (Spam) zu verhindern, darf Email nur an %1 Adressen (%2 insgesamt pro Sitzung) alle %3 Sekunden verschickt werden.";

$composeErrors[6] = "Keine Rechtschreibfehler gefunden";
$composeErrors[7] = "Rechschreibpr&uuml;fung f&uuml;r gew&auml;hlte Sprache nicht vorhanden.";
$composeErrors[8] = "(Benutzte Sprache: %s)";
?>
