<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	Simplified Chinese (GB2312), for use in China, 
	Singapore & Malaysia Chinese communities
	Translator: Danny Lin <danny@groopie.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("select"=>"-", "寄件人"=>"FROM", "收件人"=>"TO", "邮件标题"=>"SUBJECT","邮件本文"=>"BODY");
	$search_dates=array("忽略"=>"ignore", "is on"=>"ON", "在此日期后"=>"SINCE", "在此日期前"=>"BEFORE");

	$search_str["title"] = "查找";
	$search_str["str"] = "查找于<b>%folders</b>邮件箱中<br>\n";
	$search_str["str"] .= "在<b>%fields</b>区域中包含<b>%value</b>字符串<br>\n";
	$search_str["str"] .= "并且发信日期为<b>%dateops %m/%d/%y</b> 的邮件<br>\n";
	$search_str["submit"] = "立即查找";
?>
