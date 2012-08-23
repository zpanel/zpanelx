<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("kiválaszt"=>"-", "Feladó"=>"FROM", "Címzett"=>"TO", "Tárgy"=>"SUBJECT","Levél törzs"=>"BODY");
	$search_dates=array("nem számít"=>"ignore", "az napi"=>"ON", "utáni"=>"SINCE", "elõtti"=>"BEFORE");

	$search_str["title"] = "Keresés";
	$search_str["str"] = "Keresés mappában: %folders <br>\n";
	$search_str["str"] .= "ahol a %fields  mezõ tartalmazza: %value <br>\n";
	$search_str["str"] .= "és a dátum %y. %d %m. %dateops <br>\n";
	$search_str["submit"] = "keres";
?>