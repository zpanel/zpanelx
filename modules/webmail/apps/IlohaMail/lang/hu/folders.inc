<?php
include("../lang/eng/folders.inc"); // <-- do not delete
/********************************************************
	lang/hu/folders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	echo "<p><b> Mapp�k </b>\n";
	echo "<br>[<a href=\"edit_folders.php?user=".$user."\" target=\"list2\"> Mapp�k rendez�se </a>]";
	echo "<br><br>";

	$fl_str["folders"] = "Mapp�k";
	$fl_str["manage"] = "Mapp�k kezel�se";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Postal�da ";
	$defaults[$sentbox] = "Elk�ld�tt �zenetek";
	$defaults[$trashname] = "T�r�lt �zenetek";
	$fstr["reload"] = "�jroolvas";
	$fstr["expunge"] = "ki�r�t";

?>