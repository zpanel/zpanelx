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
	$search_fields=array("select"=>"-", "From"=>"FROM", "To"=>"TO", "Subject"=>"SUBJECT","Message body"=>"BODY");
	$search_dates=array("doesn't matter"=>"ignore", "is on"=>"ON", "is after"=>"SINCE", "is before"=>"BEFORE");

	$search_str["title"] = "Search";
	$search_str["str"] = "Search in folder %folders <br>\n";
	$search_str["str"] .= "where the field %fields contains %value <br>\n";
	$search_str["str"] .= "and the date %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "search";
?>