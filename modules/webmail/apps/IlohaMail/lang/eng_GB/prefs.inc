<?php
/********************************************************
	lang/gb/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	(C)2003 Lovell Fuller <iloha@lovell.info>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
//include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Preferences";
$prefHeaderStrings[1] = "General";
$prefHeaderStrings[3] = "Identities";
$prefHeaderStrings[2] = "Appearance";

											
$prefsStrings["0.0"] = "Default Identity";
$prefsStrings["0.1"] = "Name: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "Changes can be made in the \"%s\" page.";
$prefsStrings["0.4"] = "Unspecified";
$prefsStrings["1.0"] = "International";
$prefsStrings["1.1"] = "Interface: ";
$prefsStrings["1.2"] = "Character Set: ";
$prefsStrings["1.3"] = "Time Zone: ";
$prefsStrings["1.4"] = "Time Option: ";
$prefsStrings["1.5"] = array(12=>"12 hour clock", 24=>"24 hour clock");
$prefsStrings["2.0"] = "List View Options";
$prefsStrings["2.1"] = "Show up to ";
$prefsStrings["2.2"] = " items";
$prefsStrings["2.3"] = "Show size";
$prefsStrings["2.4"] = "Sort by: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Sort order: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Show folder list";
$prefsStrings["2.9"] = "Folder list width";
$prefsStrings["2.10"] = "Check for new messages every %n seconds";
$prefsStrings["2.11"] = "Show tool bar at %m";
$prefsStrings["2.12"] = array("b"=>"bottom", "t"=>"top", "bt"=>"top and bottom");
$prefsStrings["2.13"] = "Rearrange columns: ";
$prefsStrings["2.14"] = "click here";
$prefsStrings["3.0"] = "Auto-Save Options";
$prefsStrings["3.1"] = "Save sent items.";
$prefsStrings["3.2"] = "Save sent messages in folder: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Automatically delete Rubbish";
$prefsStrings["3.5"] = "Move deleted items to folder: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Root Directory: ";
$prefsStrings["3.8"] = "Other";
$prefsStrings["3.9"] = "Hide unsubscribed folders";
$prefsStrings["3.10"] = "Show quota %m";
$prefsStrings["3.11"] = array(""=>"don't show", "m"=>"in message listing", "f"=>"in folder list");
$prefsStrings["4.0"] = "Message View Options";
$prefsStrings["4.1"] = "View in frame";
$prefsStrings["4.2"] = "Colour quotes";
$prefsStrings["4.3"] = "Turning on this option may slow down the system.";
$prefsStrings["4.4"] = "Convert URLs to links";
$prefsStrings["4.5"] = "Show HTML messages";
$prefsStrings["4.6"] = "Show images inline";
$prefsStrings["4.7"] = "Show navigation links";
$prefsStrings["4.8"] = "Don't mark messages when traversing";
$prefsStrings["5.0"] = "Signature";
$prefsStrings["5.1"] = "Show signature by default";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Compose";
$prefsStrings["6.1"] = "Show contacts";
$prefsStrings["6.2"] = "Show CC/BCC fields";
$prefsStrings["6.3"] = "Close after sending";
$prefsStrings["6.4"] = "Compose inside frame";
$prefsStrings["7.0.0"] = "Disk &amp; Folder Options";
$prefsStrings["7.0.1"] = "Quota Options";
$prefsStrings["7.1"] = "Show num unread";
$prefsStrings["7.2"] = "Refresh folder list every %n seconds";


$sort_fields=array("DATE"=>"Date", "SUBJECT"=>"Subject", "SIZE"=>"Size");
$sort_orders=array("ASC"=>"Ascending", "DESC"=>"Descending");

$prefsButtonStrings = array( 0=> "Update", 1=>"Apply", 2=> "Cancel", 3=> "Defaults");

$prefs_new_user = "You seem to be logging in for the first time...welcome!<br>";
$prefs_new_user .= "Please go through the following settings, then press \"Apply\"";
?>