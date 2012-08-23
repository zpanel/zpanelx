<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/tw/main.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	Simplified Chinese (GB2312), for use in China, 
	Singapore & Malaysia Chinese communities
	Translator: Danny Lin <danny@groopie.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "删除 $num 封邮件".($num>1?"":"");
	}else if ($type == "move"){
		$str = "搬移 $num 封邮件".($num>1?"":"")." 至 $folder";
	}
	
	return $str;
}

$mainStrings[0] = "阅读：第 %p 封/共 %n 封";
$mainStrings[1] = " 封邮件";
$mainStrings[2] = "上一封";
$mainStrings[3] = " 封";
$mainStrings[4] = "下一封";
$mainStrings[5] = " 封";
$mainStrings[6] = "信申标题";
$mainStrings[7] = "收件人";
$mainStrings[8] = "寄件人";
$mainStrings[9] = "日期";
$mainStrings[10] = "删除";
$mainStrings[11] = "清空回收站";
$mainStrings[12] = "文件";
$mainStrings[13] = "查找";
$mainStrings[14] = "大小";
$mainStrings[15] = "未命名";
$mainStrings[16] = "显示";
$mainStrings[17] = "检查新信";
$mainStrings[18] = "清空";
$mainStrings[19] = "取消";


$mainErrors[0] = "此邮件箱中没有任何邮件。";
$mainErrors[1] = "此邮件箱中没有任何邮件。";
$mainErrors[2] = "无法搬移邮件至回收站";
$mainErrors[3] = "无法标记邮件为已删除。";
$mainErrors[4] = "无法搬移邮件。";
$mainErrors[5] = "尚未指定回收站";
$mainErrors[6] = "无法清空回收站";
$mainErrors[7] = "你确实要清空所有在 %f 中的邮件？";



?>
