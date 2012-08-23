<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/se/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translated by Markus Svensson <markus.svensson@linux.nu>

********************************************************/

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("select"=>"-", "Från"=>"FROM", "Till"=>"TO", "Ärende"=>"SUBJECT","Meddelande"=>"BODY");
	$search_dates=array("spelar ingen roll"=>"ignore", "är på"=>"ON", "är efter"=>"SINCE", "är före"=>"BEFORE");

	$search_str["title"] = "Sök";
	$search_str["str"] = "Sök i mapp %folders <br>\n";
	$search_str["str"] .= "där fältet %fields innehåller %value <br>\n";
	$search_str["str"] .= "och datumet %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "sök";
?>