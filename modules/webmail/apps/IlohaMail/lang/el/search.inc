<?php
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:  Uncomment next line for all non-English languages!!
	include("../lang/eng/search.inc");

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("select"=>"-", "Από"=>"FROM", "Πρός"=>"TO", "Θέμα"=>"SUBJECT","Σώμα μηνύματος"=>"BODY");
	$search_dates=array("δεν πειράζει"=>"ignore", "είναι ενεργό"=>"ON", "είναι μετά"=>"SINCE", "είναι πρίν"=>"BEFORE");

	$search_str["title"] = "Αναζήτηση";
	$search_str["str"] = "Αναζήτηση στο φάκελο %folders <br>\n";
	$search_str["str"] .= "όπου το field %fields περιέχει %value <br>\n";
	$search_str["str"] .= "και η ημερομηνία %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "αναζήτηση";
?>