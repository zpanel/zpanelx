<?php
include("../lang/eng/folders.inc"); // <-- do not delete
/********************************************************
	lang/hu/folders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	echo "<p><b> Mappák </b>\n";
	echo "<br>[<a href=\"edit_folders.php?user=".$user."\" target=\"list2\"> Mappák rendezése </a>]";
	echo "<br><br>";

	$fl_str["folders"] = "Mappák";
	$fl_str["manage"] = "Mappák kezelése";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Postaláda ";
	$defaults[$sentbox] = "Elküldött üzenetek";
	$defaults[$trashname] = "Törölt üzenetek";
	$fstr["reload"] = "újroolvas";
	$fstr["expunge"] = "kiûrít";

?>