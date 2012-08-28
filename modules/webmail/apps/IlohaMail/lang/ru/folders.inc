<?php
include("../lang/eng/folders.inc"); // <-- do not delete
/********************************************************
	lang/ruw/folders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	$fl_str["folders"] = "Папки";
	$fl_str["manage"] = "правление папками";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Входящие";	
	$defaults[$sentbox] = "Отправленные";
	$defaults[$trashname] = "Корзина";
	
?>