<?php
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	//TRANSLATORS:
	//in the follow arrays, translate key, not value
	//i.e.  translate "From" not "FROM".
	$search_fields=array("chọn"=>"-", "Từ"=>"FROM", "Gửi đến"=>"TO", "Chủ đề"=>"SUBJECT","Thơ tín"=>"BODY");
	$search_dates=array("Không thiết"=>"ignore", "Chính xác"=>"ON", "sau"=>"SINCE", "trước"=>"BEFORE");

	$search_str["title"] = "Tìm";
	$search_str["str"] = "Tìm trong hồ sơ %folders <br>\n";
	$search_str["str"] .= "Hiện diện trong: %fields Truy cập tìm %value <br>\n";
	$search_str["str"] .= "vào ngày %dateops %m/%d/%y <br>\n";
	$search_str["submit"] = "Tìm";
?>
