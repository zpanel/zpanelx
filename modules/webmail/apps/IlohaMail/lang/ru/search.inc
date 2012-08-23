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
	$search_fields=array("select"=>"-", "От"=>"FROM", "Кому"=>"TO", "Тема"=>"SUBJECT","Текст письма"=>"BODY");
	$search_dates=array("не важно"=>"ignore", "в"=>"ON", "после"=>"SINCE", "до"=>"BEFORE");

	$search_str["title"] = "Поиск";
	$search_str["str"] = "Поиск в папке %folders <br>\n";
	$search_str["str"] .= "где поле %fields содержит %value <br>\n";
	$search_str["str"] .= "и дата %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "Искать";
?>