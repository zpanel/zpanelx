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
	$search_fields=array("pasirinkti"=>"-", "Nuo"=>"FROM", "Kam"=>"TO", "Tema"=>"SUBJECT","Laiðko turinys"=>"BODY");
	$search_dates=array("nesvarbu"=>"ignore", "yra"=>"ON", "yra nuo"=>"SINCE", "yra prieð"=>"BEFORE");

	$search_str["title"] = "Paieðka";
	$search_str["str"] = "Ieðkoti aplanke %folders <br>\n";
	$search_str["str"] .= "kur laukas %fields turi %value <br>\n";
	$search_str["str"] .= "ir data %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "ieðkoti";
?>