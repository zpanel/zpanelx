<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/ga/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("select"=>"-", "De"=>"FROM", "Para"=>"TO", "Asunto"=>"SUBJECT","Corpo da Mensaxe"=>"BODY");
	$search_dates=array("non importa"=>"ignore", "sexa"=>"ON", "sexa posterior ó"=>"SINCE", "sexa anterior ó"=>"BEFORE");

	$search_str["title"] = "Buscar";
	$search_str["str"] = "Buscar na carpeta %folders <br>\n";
	$search_str["str"] .= "donde o campo %fields conteña %value <br>\n";
	$search_str["str"] .= "e a data %dateops %d/%m/%y <br>\n";
	$search_str["submit"] = "buscar";
?>
