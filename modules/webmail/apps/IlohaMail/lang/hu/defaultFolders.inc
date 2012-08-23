<?php
include("../lang/eng/defaultFolders.inc"); // <-- do not delete
/********************************************************
	lang/hu/defaultFolders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Postaláda";
	$defaults[$sentbox] = "Elküldött üzenetek";
	$defaults[$trashname] = "Szemetes";

?>