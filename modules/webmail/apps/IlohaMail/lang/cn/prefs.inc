<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/tw/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	Simplified Chinese (GB2312), for use in China, 
	Singapore & Malaysia Chinese communities
	Translator: Danny Lin <danny@groopie.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "首选";
$prefHeaderStrings[1] = "常规设置";
$prefHeaderStrings[2] = "颜色设置";
$prefHeaderStrings[3] = "身份设置";
											
$prefsStrings["0.0"] = "个人身份（对外显示）";
$prefsStrings["0.1"] = "真实姓名：";
$prefsStrings["0.2"] = "Email 邮箱：";
$prefsStrings["1.0"] = "多国语言设置";
$prefsStrings["1.1"] = "界面语言：";
$prefsStrings["1.2"] = "邮件语言：";
$prefsStrings["1.3"] = "时区：";
$prefsStrings["1.4"] = "时间格式：";
$prefsStrings["2.0"] = "数据显示设置";
$prefsStrings["2.1"] = "每页显示最多 ";
$prefsStrings["2.2"] = " 封邮件";
$prefsStrings["2.3"] = "显示邮件大小";
$prefsStrings["2.4"] = "依此区域排序：";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "排序方式：";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "显示邮件箱列表";
$prefsStrings["2.9"] = "邮件箱列表宽度";
$prefsStrings["2.10"] = "每 %n 秒钟检查一次新进邮件";
$prefsStrings["2.11"] = "工具栏显示在 %m";
$prefsStrings["2.12"] = array("b"=>"页尾", "t"=>"页首", "bt"=>"页首及页尾各一份");
$prefsStrings["2.13"] = "调整显示区域：";
$prefsStrings["2.14"] = "点按此立即调整";
$prefsStrings["3.0"] = "自动保存设置";
$prefsStrings["3.1"] = "保存寄件备份。";
$prefsStrings["3.2"] = "保存寄件备份邮件箱：";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "自动清空回收站";
$prefsStrings["3.5"] = "搬移删除的邮件至邮件箱：";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "根目录：";
$prefsStrings["3.8"] = "其他";
$prefsStrings["3.9"] = "隐藏未订阅\邮件箱";
$prefsStrings["3.10"] = "显示空间限额 %m";
$prefsStrings["3.11"] = array(""=>"不显示", "m"=>"显示在邮件列表", "f"=>"显示在邮件箱列表");
$prefsStrings["4.0"] = "邮件显示设置";
$prefsStrings["4.1"] = "在栏框里显示";
$prefsStrings["4.2"] = "引用以其他颜色标记";
$prefsStrings["4.3"] = "打开本功能可能会使系统变慢";
$prefsStrings["4.4"] = "自动转换邮件中链接";
$prefsStrings["4.5"] = "显示 HTML 格式邮件";
$prefsStrings["4.6"] = "显示邮件附带图片";
$prefsStrings["4.7"] = "显示导引列";
$prefsStrings["4.8"] = "退信时不要标记邮件";
$prefsStrings["5.0"] = "签名档";
$prefsStrings["5.1"] = "默认加上签名档";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "编写邮件选项";
$prefsStrings["6.1"] = "显示联系人";
$prefsStrings["6.2"] = "显示副本/密件副本区域";
$prefsStrings["6.3"] = "邮件发送后关闭此窗口";
$prefsStrings["6.4"] = "在栏框里编写邮件";
$prefsStrings["7.0.0"] = "磁盘及邮件箱选项";
$prefsStrings["7.0.1"] = "磁盘空间管理选项";
$prefsStrings["7.1"] = "显示未读邮件封数";
$prefsStrings["7.2"] = "每 %n 秒钟更新一次邮件箱列表";


$sort_fields=array("DATE"=>"日期", "SUBJECT"=>"信申标题", "SIZE"=>"大小");
$sort_orders=array("ASC"=>"顺向排序", "DESC"=>"逆向排序");

$prefsButtonStrings = array( 0=> "保存设置", 1=>"更新", 2=> "取消", 3=> "设为默认");

$prefs_new_user = "欢迎光临，您应该是第一次登录本系统邮箱。<br>";
$prefs_new_user .= "麻烦您花费些许时钟设置以下选项，然后按下“更新”钮即可。";
?>
