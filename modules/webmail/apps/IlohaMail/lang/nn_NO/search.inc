<?php
/********************************************************
	lang/nn_NO/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translation by Leif Halvard Silli <hyperlekken@lenk.no>

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/search.inc");

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("Vel"=>"-", "Fr&aring;"=>"FROM", "&Aring;t"=>"TO", "Emnet"=>"SUBJECT","Teksti"=>"BODY");
	$search_dates=array("hoppa yver"=>"ignore", "p&aring;"=>"ON", "sidan"=>"SINCE", "fyre"=>"BEFORE");

	$search_str["title"] = "S&oslash;k";
	$search_str["str"] = "Sokna i permen for %folders <br>\n";
	$search_str["str"] .= "millom brev der %fields inneheld %value <br>\n";
	$search_str["str"] .= "og som er utferda %dateops den %d.%m.%y <br>\n";
	$search_str["submit"] = "S&oslash;k";

?>