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
	$search_fields=array("valitse"=>"-", "l&auml;hett&auml;j&auml;"=>"FROM", "vastaanottaja"=>"TO", "aihe"=>"SUBJECT","viestin teksti"=>"BODY");
	$search_dates=array("on koska tahansa"=>"ignore", "on"=>"ON", "on my&ouml;hempi kuin"=>"SINCE", "on aiempi kuin"=>"BEFORE");

	$search_str["title"] = "Etsi";
	$search_str["str"] = "Etsi kansiosta %folders <br>\n";
	$search_str["str"] .= "viestej&auml;, joissa %fields sis&auml;lt&auml;&auml; tekstin %value <br>\n";
	$search_str["str"] .= "ja p&auml;iv&auml;m&auml;&auml;r&auml; %dateops %d.%m.%y <br>\n";
	$search_str["submit"] = "Etsi";
?>
