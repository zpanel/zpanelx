<?php
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:  Uncomment next line for all non-English languages!!
	//include("../lang/eng/search.inc");

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("ÇäÊÎÇÈ"=>"-", "ÇÒ"=>"FROM", "Èå"=>"TO", "ãæÖæÚ"=>"SUBJECT","ãÊä äÇãå"=>"BODY");
	$search_dates=array("ãåã äíÓÊ"=>"ignore", "ÏÑ ÊÇÑíÎ"=>"ON", "ÈÚÏ ÇÒ"=>"SINCE", "ŞÈá ÇÒ"=>"BEFORE");

	$search_str["title"] = "ÌÓÊÌæ";
	$search_str["str"] = "ÌÓÊÌæ ÏÑ æÔå %folders <br>\n";
	$search_str["str"] .= "ÌÇíí ßå ÑÔÊåí %fields ÍÇæí Çíä ãŞÏÇÑ ÇÓÊ %value <br>\n";
	$search_str["str"] .= "æ ÊÇÑíÎ %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "ÌÓÊÌæ";
?>