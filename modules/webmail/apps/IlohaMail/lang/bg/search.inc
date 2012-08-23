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
	$search_fields=array("избери"=>"-", "От"=>"FROM", "До"=>"TO", "Тема"=>"SUBJECT","Тяло на съобщението"=>"BODY");
	$search_dates=array("няма значение"=>"ignore", "е включен"=>"ON", "е след"=>"SINCE", "е преди"=>"BEFORE");

	$search_str["title"] = "Търсене";
	$search_str["str"] = "Търси в папка %folders <br>\n";
	$search_str["str"] .= "където поле %fields съдържа %value <br>\n";
	$search_str["str"] .= "и датата %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "търси";
?>
