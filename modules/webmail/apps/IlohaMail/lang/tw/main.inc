<?php
include("../lang/eng/main.inc"); // <-- do not delete
/********************************************************
	lang/tw/main.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	Traditional Chinese (BIG5), for use in Taiwan, 
	Hong Kong, and Overseas Chinese communities
	Translator: Danny Lin <danny@groopie.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

function formatStatusString($num, $folder, $type){
	$str="";
	if ($type=="delete"){
		$str = "刪除 $num 封信件".($num>1?"":"");
	}else if ($type == "move"){
		$str = "搬移 $num 封信件".($num>1?"":"")." 至 $folder";
	}
	
	return $str;
}

$mainStrings[0] = "閱讀：第 %p 封/共 %n 封";
$mainStrings[1] = " 封信件";
$mainStrings[2] = "上一封";
$mainStrings[3] = " 封";
$mainStrings[4] = "下一封";
$mainStrings[5] = " 封";
$mainStrings[6] = "信件標題";
$mainStrings[7] = "收件人";
$mainStrings[8] = "寄件人";
$mainStrings[9] = "日期";
$mainStrings[10] = "刪除";
$mainStrings[11] = "清空資源回收筒";
$mainStrings[12] = "移至";
$mainStrings[13] = "搜尋";
$mainStrings[14] = "大小";
$mainStrings[15] = "未命名";
$mainStrings[16] = "顯示";
$mainStrings[17] = "檢查新信";
$mainStrings[18] = "清空";
$mainStrings[19] = "取消";
$mainStrings[20] = "標示成 %b";
$mainStrings[21] = "已閱讀";
$mainStrings[22] = "未閱讀";
$mainStrings[23] = "選擇...";


$mainErrors[0] = "此信件匣中沒有任何信件。";
$mainErrors[1] = "此信件匣中沒有任何信件。";
$mainErrors[2] = "無法搬移信件至資源回收筒";
$mainErrors[3] = "無法標記信件為已刪除。";
$mainErrors[4] = "無法搬移信件。";
$mainErrors[5] = "尚未指定資源回收筒";
$mainErrors[6] = "無法清空資源回收筒";
$mainErrors[7] = "你確定要清空所有在 %f 中的信件？";
$mainErrors[8] = "在下方欄位中的箭形圖案上,選取項目來移動它們<br>當完成時關閉視窗";



?>
