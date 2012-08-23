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
	$search_fields=array("\<Kies\>"=>"-", "Van"=>"FROM", "Aan"=>"TO", "Onderwerp"=>"SUBJECT","Berichttekst"=>"BODY");
	$search_dates=array("niet uitmaakt"=>"ignore", "gelijk is aan"=>"ON", "is na"=>"SINCE", "is voor"=>"BEFORE");

	$search_str["title"] = "Zoeken";
	$search_str["str"] = "Zoeken in de map %folders <br>\n";
	$search_str["str"] .= "waar het %fields-veld %value bevat,<br>\n";
	$search_str["str"] .= "en de datum %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "zoeken";
?>
