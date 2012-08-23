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
$search_dates=array("n&atilde;o importa"=>"ignore", "est&aacute; em"=>"ON", "&eacute; posterior a"=>"SINCE", "&eacute; anterior a"=>"BEFORE");
$search_fields=array("selecione"=>"-", "De"=>"FROM", "Para"=>"TO", "Assunto"=>"SUBJECT","Corpo da mensagem"=>"BODY");
$search_str["str"] = "Pesquisar na pasta %folders <br>\n";
$search_str["str"] .= "onde o campo %fields cont&eacute;m %value <br>\n";
$search_str["str"] .= "e a data %dateops %m/%d/%y <br>\n";
$search_str["submit"] = "pesquisar";
$search_str["title"] = "Pesquisar";
?>
