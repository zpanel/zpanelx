<?php
include("../lang/eng/folders.inc"); // <-- do not delete
/*******************************************************
	lang/it/folders.inc
	
	Italian Translation by Amos B. <mriya3@surfeu.ch>
	Original english file (C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//	echo "<p><b>Cartelle</b>\n";
//	echo "<br>[<a href=\"edit_folders.php?user=".$user."\" target=\"list2\">Gestione cartelle</a>]";
//	echo "<br><br>";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Posta in arrivo";
	$defaults[$sentbox] = "Posta inviata";
	$defaults[$trashname] = "Cestino";
	$fstr["reload"] = "Aggiorna";
	$fstr["expunge"] = "svuota";

?>
