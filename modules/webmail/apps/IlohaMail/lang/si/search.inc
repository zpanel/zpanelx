<?php
/********************************************************
	lang/si/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/search.inc");

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("select"=>"-", "Od"=>"FROM", "Za"=>"TO", "Tema"=>"SUBJECT","Besedilo sporoèila"=>"BODY");
	$search_dates=array("vseeno"=>"ignore", "je vkljuèen/o=>"ON", "za"=>"SINCE", "pred"=>"BEFORE");
	$search_str["title"] = "Iskanje";
	$search_str["str"] = "Iskanje v mapah %folders <br>\n";
	$search_str["str"] .= "kjer polja %fields vsebujejo %value <br>\n";
	$search_str["str"] .= "in je datum enak %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "išèi";
?>