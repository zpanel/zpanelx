<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/pt/search.inc
	(C)2003 Darwings <darwings@lisboa.alface.de || asas@netcabo.pt>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/
	// TRANSLATORS: Bla Bla :D


	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("selecionar"=>"-", "De"=>"FROM", "Para"=>"TO", "Assunto"=>"SUBJECT","Corpo da Mensagem"=>"BODY");
	$search_dates=array("não interessa"=>"ignore", "em"=>"ON", "depois de"=>"SINCE", "antes de"=>"BEFORE");

	$search_str["title"] = "Procurar";
	$search_str["str"] = "Procurar na pasta %folders <br>\n";
	$search_str["str"] .= "aonde o campo %fields contem %value <br>\n";
	$search_str["str"] .= "e a data %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "Procurar";
?>
