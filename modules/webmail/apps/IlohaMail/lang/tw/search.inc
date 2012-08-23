<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	Traditional Chinese (BIG5), for use in Taiwan, 
	Hong Kong, and Overseas Chinese communities
	Translator: Danny Lin <danny@groopie.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("選擇"=>"-", "寄件人"=>"FROM", "收件人"=>"TO", "信件標題"=>"SUBJECT","信件本文"=>"BODY");
	$search_dates=array("忽略"=>"ignore", "等於"=>"ON", "在此日期後"=>"SINCE", "在此日期前"=>"BEFORE");

	$search_str["title"] = "搜尋";
	$search_str["str"] = "搜尋於<b>%folders</b>信件匣中<br>\n";
	$search_str["str"] .= "在<b>%fields</b>欄位中包含<b>%value</b>字串<br>\n";
	$search_str["str"] .= "並且發信日期為<b>%dateops %y年%m月%d日</b> 的信件<br>\n";
	$search_str["submit"] = "立即搜尋";
?>
