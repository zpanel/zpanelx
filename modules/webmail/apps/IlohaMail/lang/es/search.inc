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
	$search_fields=array("seleccionar"=>"-", "De"=>"FROM", "Para"=>"TO", "Tema"=>"SUBJECT","Cuerpo del Mensaje"=>"BODY");
	$search_dates=array("no importa"=>"ignore", "sea"=>"ON", "sea despues del"=>"SINCE", "este antes del"=>"BEFORE");

	$search_str["title"] = "Busqueda";
	$search_str["str"] = "Buscar en carpeta %folders <br>\n";
	$search_str["str"] .= "donde el campo %fields contenga %value <br>\n";
	$search_str["str"] .= "y la fecha %dateops %d/%m/%y <br>\n";
	$search_str["submit"] = "Buscar";
?>
