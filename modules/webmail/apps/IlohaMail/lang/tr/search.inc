<?php
/********************************************************
	lang/tr/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	Turkish Translation by Ufuk Yuzereroglu <ufuk@yuzereroglu.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/search.inc");

//TRANSLATORS:
//in the follow arrays, translate key, not value
//i.e.  translate "From" not "FROM".
$search_fields=array("select"=>"-", "Kimden"=>"FROM", "Kime"=>"TO", "Konu"=>"SUBJECT","Mesaj"=>"BODY");
$search_dates=array("farketmez"=>"ignore", "tarihinde"=>"ON", "den sonra"=>"SINCE", "den önce"=>"BEFORE");

$search_str["title"] = "Arama";
$search_str["str"] = "%folders klasöründe ara<br>\n";
$search_str["str"] .= "%fields alani %value degerini içeriyorsa<br>\n";
$search_str["str"] .= "ve tarih %dateops %d/%m/%y <br>\n";
$search_str["submit"] = "ara";
?>
