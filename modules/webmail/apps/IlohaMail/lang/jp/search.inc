<?php
include("../lang/eng/search.inc"); // <-- do not delete
/********************************************************
	lang/jp/search.inc
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	PURPOSE:
		Provide interface for search function.

********************************************************/

	$search_fields=array("選択..."=>"-", "送信者"=>"FROM", "宛先"=>"TO", "件名"=>"SUBJECT","本文"=>"BODY");
	
	$search_dates=array("未指定"=>"ignore", "の"=>"SENTON", "以降の"=>"SENTSINCE", "以前の"=>"SENTBEFORE");

	$search_str["title"] = "検索";
	$search_str["str"] = "%folders の中で<br>\n";
	$search_str["str"] .= "%fields が %value を含み<br>\n";
	$search_str["str"] .= "日付けが %m/%d/%y %dateops の項目 <br>\n";
	$search_str["submit"] = "検索開始";
?>