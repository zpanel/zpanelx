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
	$search_fields=array("Selezionare un valore"=>"-", "Da"=>"FROM", "A"=>"TO", "Oggetto"=>"SUBJECT","Corpo del messaggio"=>"BODY");
	$search_dates=array("non contiene"=>"ignora", "inizia con"=>"ON", "finisce con"=>"SINCE", "fino a"=>"BEFORE");

	$search_str["title"] = "Ricerca";
	$search_str["str"] = "Ricerca  in cartelle %folders <br>\n";
	$search_str["str"] .= "dove il campo %fields contiene %value <br>\n";
	$search_str["str"] .= "e la data %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "ricerca";
?>
