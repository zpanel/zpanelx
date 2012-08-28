<?php
include("../lang/eng/folders.inc"); // <-- do not delete
/********************************************************
	lang/ga/folders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	$fl_str["folders"] = "Carpetas";
	$fl_str["manage"] = "Xestión Carpetas";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Bandexa de Entrada";
	$defaults[$sentbox] = "Enviados";
	$defaults[$trashname] = "Eliminados";
	$fstr["reload"] = "recargar";
        $fstr["expunge"] = "valeirar";


?>
