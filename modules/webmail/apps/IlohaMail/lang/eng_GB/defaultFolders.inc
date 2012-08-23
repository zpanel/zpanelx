<?php
/********************************************************
	lang/gb/defaultFolders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	(C)2003 Lovell Fuller <iloha@lovell.info>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:  Uncomment next line for all non-English languages!!
	//include("../lang/eng/defaultFolders.inc");

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Inbox";
	$defaults[$sentbox] = "Sent";
	$defaults[$trashname] = "Rubbish";

?>