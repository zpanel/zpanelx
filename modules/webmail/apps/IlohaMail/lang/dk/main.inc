<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
        lang/dk/main.inc
        (C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

        This file is part of IlohaMail, and released under GPL.
        See COPYING, or http://www.fsf.org/copyleft/gpl.html

        Translated by Torben Rybner, Ziberex (try@ziberex.dk)

********************************************************/

#function formatStatusString($num, $folder, $type){
#        $str="";
#        if ($type=="delete"){
#                $str = "Slet $num meddelelse".($num>1?"r":"");
#        }else if ($type == "move"){
#                $str = "Flyttet $num meddelelse".($num>1?"r":"")." til $folder";
#        }
#        
#        return $str;
#}

$mainMessages["delete"] = "Slet %n message(s)";
$mainMessages["move"] = "Flyttede %n message(s) til %f";

$mainStrings[0] = "Viser %p af %n meddelelser";
$mainStrings[1] = " meddelelser fundet";
$mainStrings[2] = "Forrige";
$mainStrings[3] = "";
$mainStrings[4] = "Vis n&aelig;ste";
$mainStrings[5] = "";
$mainStrings[6] = "Emne";
$mainStrings[7] = "Til";
$mainStrings[8] = "Fra";
$mainStrings[9] = "Dato";
$mainStrings[10] = "Slet";
$mainStrings[11] = "T&oslash;m papirkurv";
$mainStrings[12] = "Flyt";
$mainStrings[13] = "S&oslash;g";
$mainStrings[14] = "St&oslash;rrelse";
$mainStrings[15] = "Ikke navngivet";
$mainStrings[16] = "Vis";
$mainStrings[17] = "Opdater";
$mainStrings[18] = "Slet alle";
$mainStrings[19] = "Annuller";
$mainStrings[20] = "Marker valgte som %b";
$mainStrings[21] = "L&aelig;st";
$mainStrings[22] = "Ul&aelig;st";
$mainStrings[23] = "V&aelig;lg...";


$mainErrors[0] = "Ingen meddelelser fundet i denne folder";
$mainErrors[1] = "Ingen meddelelser i denne folder";
$mainErrors[2] = "Kunne ikke flytte meddelelse til papirkurven.";
$mainErrors[3] = "Kunne ikke indikere, at meddelelsen er slettet.";
$mainErrors[4] = "Kunne ikke flytte meddelelser.";
$mainErrors[5] = "Papirkurv ikke specificeret.";
$mainErrors[6] = "Kunne ikke t&oslash;mme papirkurven";
$mainErrors[7] = "Er du sikker p&aring;, at du vil slette ALLE meddelelser i %f?";
$mainErrors[8] = "Klik p&aring; pilene lige under brevhovedkolonnen for at flytte dem.<br>N&aring;r du er f&aelig;rdig, lukker du blot vinduet.";



?>
