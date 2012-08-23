<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translated by Torben Rybner, Ziberex (try@ziberex.dk)

********************************************************/

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("v&aelig;lg"=>"-", "Fra"=>"FROM", "Til"=>"TO", "Emne"=>"SUBJECT", "Meddelelsestekst"=>"BODY");
	$search_dates=array("ignoreres"=>"ignore", "er"=>"ON", "er efter"=>"SINCE", "er f&oslash;r"=>"BEFORE");

	$search_str["title"] = "S&oslash;g";
	$search_str["str"] = "S&oslash;g i folder %folders <br>\n";
	$search_str["str"] .= "hvor feltet %fields indeholder %value <br>\n";
	$search_str["str"] .= "og datoen %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "S&oslash;g";
?>
