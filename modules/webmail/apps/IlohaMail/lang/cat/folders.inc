<?php
/********************************************************
	lang/cat/folders.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

        Translated: David Gimeno i Ayuso info@sima-pc.com

********************************************************/
        //TRANSLATORS:  Uncomment next line for all non-English languages!!
        include("../lang/eng/folders.inc");

        $fl_str["folders"] = "Carpetes";
        $fl_str["manage"] = "Gestionar carpetes";

        $sentbox=$my_prefs["sent_box_name"];
        $trashname=$my_prefs["trash_name"];
        $defaults["INBOX"] = "Entrades";
        $defaults[$sentbox] = "Enviats";
        $defaults[$trashname] = "Paperera";
        $fstr["reload"] = "refrescar";
        $fstr["expunge"] = "ratllar";

?>
