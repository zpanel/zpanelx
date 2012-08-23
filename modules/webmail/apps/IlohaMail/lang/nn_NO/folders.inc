<?php
/********************************************************
	lang/nn_NO/folders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translation by Leif Halvard Silli <hyperlekken@lenk.no>

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/folders.inc");

	$fl_str["folders"] = "Brevpermar";
	$fl_str["manage"] = "Handtering av brevpermane";
	
	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"] = "Innkomne";
	$defaults[$sentbox] = "Sendte";
	$defaults[$trashname] = "S&oslash;pli";

    // from english version
  $fstr["reload"] = "uppdatera";
	$fstr["expunge"] = "t&oslash;m";
    // from english version

?>