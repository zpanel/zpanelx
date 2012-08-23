<?php
/********************************************************
	lang/gb/compose.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
//include("../lang/eng/compose.inc");

$composeHStrings[0] = "Subject";
$composeHStrings[1] = "From";
$composeHStrings[2] = "To";
$composeHStrings[3] = "CC";
$composeHStrings[4] = "BCC";
$composeHStrings[5] = "Date";


$composeStrings[0] = "Compose Message";
$composeStrings[1] = "Send Message";
$composeStrings[2] = "Upload";
$composeStrings[3] = "Attach signature";
$composeStrings[4] = "Attach";
$composeStrings[5] = "Contacts";
$composeStrings[6] = "Hide contacts";
$composeStrings[7] = "Message body:";
$composeStrings[8] = "Add";
$composeStrings[9] = "On %d, %s wrote:\n";
$composeStrings[10] = "--- Original Message ---\n";
$composeStrings[11] = "Close Window";

$composeStrings[12] = "Check Spelling";
$composeStrings[13] = "Correct Spelling";
$composeStrings[14] = "No Changes";
$composeStrings[15] = "Possible Spelling Errors";
$composeStrings[16] = "Spelling Changes";
$composeStrings[17] = "ignore";
$composeStrings[18] = "delete";

$composeStrings["title"] = "Compose Message";

$composeErrors[0] = "The subject field is empty. Please enter a subject or click \"".$composeStrings[1]."\" again to send.";
$composeErrors[1] = "The \"from\" field is empty.  Please enter your email address.";
$composeErrors[2] = "The \"To\" field is empty.  Please specify the destination address.";
$composeErrors[3] = "Upload failed";
$composeErrors[4] = "No files received.";
$composeErrors[5] = "For spam prevention reasons, you may only send to %1 people (%2 total per session) every %3 seconds.";

$composeErrors[6] = "No spelling errors found.";
$composeErrors[7] = "Spell checking not available for specified language.";
$composeErrors[8] = "(%s dictionary used)";
?>