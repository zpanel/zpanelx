<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/tw/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	Traditional Chinese (BIG5), for use in Taiwan, 
	Hong Kong, and Overseas Chinese communities
	Translator: Danny Lin <danny@groopie.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "偏好設定";
$prefHeaderStrings[1] = "一般設定";
$prefHeaderStrings[2] = "顏色設定";
$prefHeaderStrings[3] = "身份設定";
											
$prefsStrings["0.0"] = "個人身份（對外顯示）";
$prefsStrings["0.1"] = "真實姓名：";
$prefsStrings["0.2"] = "Email 信箱：";
$prefsStrings["0.3"] = "可以由 \"%s\" 修改.";
$prefsStrings["0.4"] = "未設定";
$prefsStrings["1.0"] = "多國語系設定";
$prefsStrings["1.1"] = "界面語言：";
$prefsStrings["1.2"] = "信件語系：";
$prefsStrings["1.3"] = "時區：";
$prefsStrings["1.4"] = "時間格式：";
$prefsStrings["2.0"] = "資料顯示設定";
$prefsStrings["2.1"] = "每頁顯示最多 ";
$prefsStrings["2.2"] = " 封信件";
$prefsStrings["2.3"] = "顯示信件大小";
$prefsStrings["2.4"] = "依此欄位排序：";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "排序方式：";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "顯示信件匣清單";
$prefsStrings["2.9"] = "信件匣列表寬度";
$prefsStrings["2.10"] = "每 %n 秒鐘檢查一次新進信件";
$prefsStrings["2.11"] = "工具列顯示在 %m";
$prefsStrings["2.12"] = array("b"=>"頁尾", "t"=>"頁首", "bt"=>"頁首及頁尾各一份");
$prefsStrings["2.13"] = "調整顯示欄位：";
$prefsStrings["2.14"] = "點按此立即調整";
$prefsStrings["3.0"] = "自動儲存設定";
$prefsStrings["3.1"] = "儲存寄件備份。";
$prefsStrings["3.2"] = "儲存寄件備份信件匣：";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "自動清空資源回收筒";
$prefsStrings["3.5"] = "搬移刪除的信件至信件匣：";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "根目錄：";
$prefsStrings["3.8"] = "其他";
$prefsStrings["3.9"] = "隱藏未訂閱\信件匣";
$prefsStrings["3.10"] = "顯示空間限額 %m";
$prefsStrings["3.11"] = array(""=>"不顯示", "m"=>"顯示在信件列表", "f"=>"顯示在信件匣列表");
$prefsStrings["4.0"] = "信件顯示設定";
$prefsStrings["4.1"] = "在欄框裡顯示";
$prefsStrings["4.2"] = "引言以其他顏色標示";
$prefsStrings["4.3"] = "開啟本功能可能會使系統變慢";
$prefsStrings["4.4"] = "自動轉換信件中連結";
$prefsStrings["4.5"] = "顯示 HTML 格式信件";
$prefsStrings["4.6"] = "顯示信件附帶圖片";
$prefsStrings["4.7"] = "顯示導引列";
$prefsStrings["4.8"] = "退信時不要標記信件";
$prefsStrings["5.0"] = "簽名檔";
$prefsStrings["5.1"] = "預設加上簽名檔";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "編寫信件選項";
$prefsStrings["6.1"] = "顯示連絡人";
$prefsStrings["6.2"] = "顯示副本/密件副本欄位";
$prefsStrings["6.3"] = "信件寄出後關閉此視窗";
$prefsStrings["6.4"] = "在欄框裡編寫信件";
$prefsStrings["7.0.0"] = "磁碟及信件匣選項";
$prefsStrings["7.0.1"] = "磁碟空間管理選項";
$prefsStrings["7.1"] = "顯示未讀信件封數";
$prefsStrings["7.2"] = "每 %n 秒鐘更新一次信件匣列表";


$sort_fields=array("DATE"=>"日期", "SUBJECT"=>"信件標題", "SIZE"=>"大小");
$sort_orders=array("ASC"=>"順向排序", "DESC"=>"逆向排序");

$prefsButtonStrings = array( 0=> "儲存設定", 1=>"更新", 2=> "取消", 3=> "設為預設值");

$prefs_new_user = "歡迎光臨，您應該是第一次登入本系統信箱。<br>";
$prefs_new_user .= "麻煩您花費些許時間設定以下選項，然後按下「更新」鈕即可。";
?>
