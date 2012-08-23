<?php
/********************************************************
	lang/tr/defaultFolders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	Turkish Translation by Ufuk Yuzereroglu <ufuk@yuzereroglu.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/defaultFolders.inc");

$sentbox=$my_prefs["sent_box_name"];
$trashname=$my_prefs["trash_name"];
$defaults["INBOX"] = "Gelen Kutusu";
$defaults[$sentbox] = "Giden Kutusu";
$defaults[$trashname] = "Çöp Kutusu";

?>
