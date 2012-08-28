<?php
/********************************************************
	lang/eng/folders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:  Uncomment next line for all non-English languages!!
	include("../lang/eng/folders.inc");

	$fl_str["folders"] = "Postikansiot";
	$fl_str["manage"] = "Muokkaa kansioita";
	
	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$draftbox=$my_prefs['draft_box_name'];
	$defaults["INBOX"] = "Saapuneet";
	$defaults[$sentbox] = "L&auml;hetetyt";
	$defaults[$trashname] = "Roskat";
	$defaults[$draftbox] = 'Keskener&auml;iset';
	$fstr["reload"] = "reload";
	$fstr["expunge"] = "tyhjenn&auml;";

?>
