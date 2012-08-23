<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
    lang/dk/prefs.inc
    (C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

    This file is part of IlohaMail, and released under GPL.
    See COPYING, or http://www.fsf.org/copyleft/gpl.html

    Translated by Torben Rybner, Ziberex (try@ziberex.dk)

********************************************************/

$prefHeaderStrings[0] = "Indstillinger";
$prefHeaderStrings[1] = "Generelt";
$prefHeaderStrings[3] = "Identiteter";
$prefHeaderStrings[2] = "Udseende";

                                            
$prefsStrings["0.0"] = "Standardidentitet";
$prefsStrings["0.1"] = "Navn: ";
$prefsStrings["0.2"] = "Mail-adresse: ";
$prefsStrings["0.3"] = "Rettelser hertil sker under \"%s\".";
$prefsStrings["0.4"] = "Uspecificeret";
$prefsStrings["1.0"] = "Sprog &amp; tid";
$prefsStrings["1.1"] = "Brugergr&aelig;nseflade: ";
$prefsStrings["1.2"] = "Tegns&aelig;t: ";
$prefsStrings["1.3"] = "Tidszone: ";
$prefsStrings["1.4"] = "Kloktype: ";
$prefsStrings["1.5"] = array(12=>"12-timer klok", 24=>"24-timer klok");
$prefsStrings["2.0"] = "Meddelelseslisten";
$prefsStrings["2.1"] = "Vis op til ";
$prefsStrings["2.2"] = " meddelelser";
$prefsStrings["2.3"] = "Vis st&oslash;rrelse";
$prefsStrings["2.4"] = "Sorter efter: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Sorteringsr&aelig;kkef&oslash;lge: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Vis folderoversigt";
$prefsStrings["2.9"] = "Bredde af folderoversigt";
$prefsStrings["2.10"] = "Tjek for nye meddelelser hver %n sekunder";
$prefsStrings["2.11"] = "Vis v&aelig;rkt&oslash;jslinjen %m";
$prefsStrings["2.12"] = array("b"=>"forneden", "t"=>"foroven", "bt"=>"b&aring;de foroven og forneden");
$prefsStrings["2.13"] = "Rediger kolonner&aelig;kkef&oslash;lge: ";
$prefsStrings["2.14"] = "klik her";
$prefsStrings["3.0"] = "Lagring af meddelelser";
$prefsStrings["3.1"] = "Gem sendte meddelelser.";
$prefsStrings["3.2"] = "Gem sendte meddelelser i folder: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "T&oslash;m automatisk papirkurven";
$prefsStrings["3.5"] = "Flyt slettede meddelelser til: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Hovedfolder: ";
$prefsStrings["3.8"] = "Alternativ folder";
$prefsStrings["3.9"] = "Skjul afmeldte foldere";
$prefsStrings["3.10"] = "Vis kvote %m";
$prefsStrings["3.11"] = array(""=>"vis ikke", "m"=>"i meddelelseslisten", "f"=>"i folderoversigten");
$prefsStrings["4.0"] = "Meddelelsesvisning";
$prefsStrings["4.1"] = "Indlejr vinduet for meddelelsesvisning i hovedrammen";
$prefsStrings["4.2"] = "M&aelig;rk citater med farve";
$prefsStrings["4.3"] = "Aktivering af denne indstilling kan sl&oslash;ve systemet.";
$prefsStrings["4.4"] = "Konverter URL'er til links";
$prefsStrings["4.5"] = "Vis HTML-meddelelser";
$prefsStrings["4.6"] = "Vis billeder i teksten";
$prefsStrings["4.7"] = "Vis navigationslinks";
$prefsStrings["4.8"] = "Marker ikke meddelelser ved traversering";
$prefsStrings["5.0"] = "Signatur";
$prefsStrings["5.1"] = "Automatisk inds&aelig;t signatur";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Skrivning af nye meddelelser";
$prefsStrings["6.1"] = "Vis kontaktpersoner";
$prefsStrings["6.2"] = "Vis CC/BCC-felter";
$prefsStrings["6.3"] = "Luk vindue efter afsendelse";
$prefsStrings["6.4"] = "Indlejr vinduet for meddelelsesskrivning i hovedrammen";
$prefsStrings["7.0.0"] = "Diskplads &amp; foldere";
$prefsStrings["7.0.1"] = "Kvoteindstillinger";
$prefsStrings["7.1"] = "Vis antallet af ul&aelig;ste meddelelser";
$prefsStrings["7.2"] = "Genopfrisk folderoversigten hver %n sekunder";

$pref_col_title = "Rediger kolonner&aelig;kkef&oslash;lge";
$pref_colstr["order"] = "Orden";
$pref_colstr["field"] = "Felt";
$pref_col_label['c'] = "Tjekboks";
$pref_col_label['a'] = "Bilag";
$pref_col_label['m'] = "Statusflag";

$sort_fields=array("DATE"=>"Dato", "SUBJECT"=>"Emne", "SIZE"=>"St&oslash;rrelse");
$sort_orders=array("ASC"=>"Stigende", "DESC"=>"Faldende");

$prefsButtonStrings = array( 0=> "Opdater", 1=>"O.k.", 2=> "Annuller", 3=> "Standardindstillinger");

$prefs_new_user = "Du logger tilsyneladende p&aring; for f&oslash;rste gang... Velkommen!<br>";
$prefs_new_user .= "Du b&oslash;r tilpasse indstillingerne og derp&aring; klikke p&aring; \"O.k.\"";
?>
