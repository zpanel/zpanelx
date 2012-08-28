<?php
include("../lang/eng/defaultFolders.inc"); // <-- do not delete
/********************************************************
	lang/pl/defaultFolders.inc
	(C) 2002 Marcin Piliszczuk <marti@net26.pl>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Skrzynka odbiorcza";
	$defaults[$sentbox] = "Wys³ane";
	$defaults[$trashname] = "Kosz";

?>