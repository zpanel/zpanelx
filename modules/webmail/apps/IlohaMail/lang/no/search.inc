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
	$search_fields=array("[ -velg- ]"=>"-", "Fra"=>"FROM", "Til"=>"TO", "Emne"=>"SUBJECT","Melding"=>"BODY");
	$search_dates=array("ikke trengs"=>"ignore", "er akkurat"=>"ON", "er etter"=>"SINCE", "er før"=>"BEFORE");

	$search_str["title"] = "Søk";
	$search_str["str"] = "Søk i mapper %folders <br>\n";
	$search_str["str"] .= "Der %fields inneholder %value <br>\n";
	$search_str["str"] .= "og datoen %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "Søk";
?>