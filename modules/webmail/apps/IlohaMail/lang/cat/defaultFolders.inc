<?php
/********************************************************
	lang/cat/defaultFolders.inc
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

        Translated: David Gimeno i Ayuso info@sima-pc.com

********************************************************/
        //TRANSLATORS:  Uncomment next line for all non-English languages!!
        include("../lang/eng/defaultFolders.inc");

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
        $defaults["INBOX"] = "Entrades";
        $defaults[$sentbox] = "Enviats";
        $defaults[$trashname] = "Paperera";

?>
