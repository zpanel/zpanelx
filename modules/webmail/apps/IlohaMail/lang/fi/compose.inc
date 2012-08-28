<?php
/********************************************************
	lang/eng/compose.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/compose.inc");

$composeHStrings[0] = "Aihe";
$composeHStrings[1] = "L&auml;hett&auml;j&auml;";
$composeHStrings[2] = "Vastaanottaja";
$composeHStrings[3] = "CC";
$composeHStrings[4] = "BCC";
$composeHStrings[5] = "P&auml;iv&auml;m&auml;&auml;r&auml;";


$composeStrings[0] = "Kirjoita viesti";
$composeStrings[1] = "L&auml;het&auml; viesti";
$composeStrings[2] = "Lis&auml;&auml;"; // liitteen uploadaus
$composeStrings[3] = "Lis&auml;&auml; allekirjoitus";
$composeStrings[4] = "Liitteet";
$composeStrings[5] = "Yhteystiedot";
$composeStrings[6] = "Piilota yhteystiedot";
$composeStrings[7] = "Viestin teksti:";
$composeStrings[8] = "Lis&auml;&auml;";
$composeStrings[9] = "%s kirjoitti %d:\n";
$composeStrings[10] = "--- Alkuper&auml;inen viesti ---\n";
$composeStrings[11] = "Sulje ikkuna";

$composeStrings[12] = "Etsi kirjoitusvirheit&auml;";
$composeStrings[13] = "Tee muutokset";
$composeStrings[14] = "Ei muutoksia";
$composeStrings[15] = "Mahdollisia kirjoitusvirheit&auml;:";
$composeStrings[16] = "Vaihdettiin seuraavat sanat:";
$composeStrings[17] = "ohita";
$composeStrings[18] = "poista";
$composeStrings[19] = 'BCC itselle';

$composeStrings["title"] = "Kirjoita viesti";

$composeErrors[0] = "Aihekentt&auml; on tyhj&auml;. Kirjoita aihe tai paina \"".$composeStrings[1]."\" uudestaan.";
$composeErrors[1] = "L&auml;hett&auml;j&auml;kentt&auml; on tyhj&auml;.  Kirjoita siihen s&auml;hk&ouml;postiosoitteesi.";
$composeErrors[2] = "Vastaanottajakentt&auml; on tyhj&auml;. Kirjoita tai valitse vastaanottajan osoite.";
$composeErrors[3] = "Tiedoston l&auml;hetys ei onnistunut";
$composeErrors[4] = "No files received.";
$composeErrors[5] = "Roskapostituksen est&auml;miseksi t&auml;st&auml; voi l&auml;hett&auml;&auml; viestej&auml; vain %1 osoitteeseen (yhteens&auml; %2 per session) %3 sekunnin aikana.";

$composeErrors[6] = "Kirjoitusvirheit&auml; ei l&ouml;ytynyt.";
$composeErrors[7] = "Oikeinkirjoituksen tarkistusta ei ole t&auml;lle kielelle";
$composeErrors[8] = "(Sanasto: %s)";
?>
