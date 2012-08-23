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
	$search_fields=array("select"=>"-", "Absender"=>"FROM", "Empf&auml;nger"=>"TO", "Betreff"=>"SUBJECT","Text der Nachricht"=>"BODY");
	$search_dates=array("egal"=>"ignore", "der"=>"ON", "nach dem"=>"SINCE", "vor dem"=>"BEFORE");

	$search_str["title"] = "Suche";
	$search_str["str"] = "Suche in Ordner %folders <br>\n";
	$search_str["str"] .= "wobei das Feld %fields den Wert %value enth&auml;lt <br>\n";
	$search_str["str"] .= "und das Datum %dateops %d.%m.%y ist<br>\n";
	$search_str["submit"] = "Suche";
?>
