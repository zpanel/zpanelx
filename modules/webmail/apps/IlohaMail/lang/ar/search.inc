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
	$search_fields=array("ÇÎÊíÇÑ"=>"-", "ãä"=>"FROM", "Åáì"=>"TO", "ÇáãæÖæÚ"=>"SUBJECT","äÕ ÇáÑÓÇáÉ"=>"BODY");
	$search_dates=array("áÇ íåã"=>"ignore", "İí"=>"ON", "ÈÚÏ"=>"SINCE", "ŞÈá"=>"BEFORE");

	$search_str["title"] = "ÇÈÍË";
	$search_str["str"] = "ÇÈÍË İí Çáãáİ %folders <br>\n";
	$search_str["str"] .= "ÍíË ÇáÍŞá %fields íÍÊæí Úáì %value <br>\n";
	$search_str["str"] .= "æÇáÊÇÑíÎ %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "ÇÈÍË";
?>