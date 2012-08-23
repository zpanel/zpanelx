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
	$search_fields=array("select"=>"-", "De"=>"FROM", "À"=>"TO", 
"Sujet"=>"SUBJECT","Corps du message"=>"BODY");
	$search_dates=array("sans importance"=>"ignore", "est à partir de"=>"ON", "est 
après"=>"SINCE", "est avant"=>"BEFORE");

	$search_str["title"] = "Recherche";
	$search_str["str"] = "Chercher dans le dossier %folders <br>\n";
	$search_str["str"] .= "où le champ %fields contient %value <br>\n";
	$search_str["str"] .= "et la date %dateops %d/%m/%y <br>\n";
	$search_str["submit"] = "Chercher";
?>
