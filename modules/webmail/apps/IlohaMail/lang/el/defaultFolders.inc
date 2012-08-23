<?php
/********************************************************
	lang/eng/defaultFolders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:  Uncomment next line for all non-English languages!!
	include("../lang/eng/defaultFolders.inc");

	$sentbox=$my_prefs[";όνομα_απεσταλμένων"];
	$trashname=$my_prefs["όνομα_σκουπιδιών"];
	$defaults["INBOX"] = "Εισερχόμενα";
	$defaults[$sentbox] = "Απεσταλμένα";
	$defaults[$trashname] = "Σκουπίδια";

?>