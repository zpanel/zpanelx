<?php

// TRANSLATORS: Bla Bla :D
include("../lang/eng/folders.inc"); // <-- do not delete
/********************************************************
	lang/pt/folders.inc
	(C)2003 Darwings <darwings@lisboa.alface.de || asas@netcabo.pt>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

        $fl_str["folders"] = "Pastas";
        $fl_str["manage"] = "Gerir Pastas";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Entrada";
	$defaults[$sentbox] = "Enviado";
	$defaults[$trashname] = "Lixo";
        $fstr["reload"] = "reler";
        $fstr["expunge"] = "esvaziar";
?>
