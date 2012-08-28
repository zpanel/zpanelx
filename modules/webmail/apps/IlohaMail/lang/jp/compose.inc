<?php
include("../lang/eng/compose.inc"); // <-- do not delete
/********************************************************
	lang/jp/compose.inc
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$composeHStrings[0] = "題名";
$composeHStrings[1] = "From";
$composeHStrings[2] = "To";
$composeHStrings[3] = "CC";
$composeHStrings[4] = "BCC";
$composeHStrings[5] = "日付け";


$composeStrings[0] = "メッセージ作成";
$composeStrings[1] = "送信";
$composeStrings[2] = "アップロード";
$composeStrings[3] = "署名";
$composeStrings[4] = "添付";
$composeStrings[5] = "アドレス帳";
$composeStrings[6] = "リストを隠す";
$composeStrings[7] = "メッセージ本文:";
$composeStrings[8] = "追加";
$composeStrings[9] = "%d付けの%sのメールによると：\n";
$composeStrings[10] = "---以下原文---\n";
$composeStrings[11] = "キャンセル";


$composeErrors[0] = "件名欄が空白です。もう一度「".$composeStrings[1]."」ボタンにクリックするとそのまま送信します。";
$composeErrors[1] = "送信者のメールアドレスが指定されていません。あなたのメールアドレスを記入してください。";
$composeErrors[2] = "宛先が指定されていません。宛先のメールアドレスを記入してください。";
$composeErrors[3] = "ファイルのアップロードに失敗しました";
$composeErrors[4] = "ファイルがアップロードされませんでした";
$composeErrors[5] = "スパム防止のため、送り先のメールアドレスはメールにつき%1件（ログイン毎、計%2件）、送信作業は%3秒に１回に限られています。";

?>