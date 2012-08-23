<?php
include("../lang/eng/folders.inc"); // <-- do not delete
/********************************************************	lang/nl/folders.inc
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	
	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/
$fl_str["folders"]="Mappen";
$fl_str["manage"]="Beheer mappen";

$sentbox=$my_prefs["sent_box_name"];
$trashname=$my_prefs["trash_name"];
$defaults["INBOX"] = "Inbox";
$defaults[$sentbox] = "Verzonden items";
$defaults[$trashname] = "Prullenbak";
$fstr["reload"] = "herladen";
$fstr["expunge"] = "legen";
?>