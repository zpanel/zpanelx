<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	file
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	PURPOSE:
		Provide strings for "source/prefs.php" and "source/pref_colors.php"
		
********************************************************/

$prefHeaderStrings[0] = "設定";
$prefHeaderStrings[1] = "一般";
$prefHeaderStrings[2] = "表示色";
$prefHeaderStrings[3] = "個人情報";
$prefHeaderStrings[4] = "リスト表示";

											
$prefsStrings["0.0"] = "ユーザー";
$prefsStrings["0.1"] = "名前: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "「%s」ページで設定できます。";
$prefsStrings["0.4"] = "未指定";
$prefsStrings["1.0"] = "言語";
$prefsStrings["1.1"] = "インターフェース言語: ";
$prefsStrings["1.2"] = "標準文字コード: ";
$prefsStrings["1.3"] = "時差: ";
$prefsStrings["1.4"] =  "時間オプション: ";
$prefsStrings["1.5"] = array(12=>"12時間時計", 24=>"24時間時計");
$prefsStrings["2.0"] = "リスト表示";
$prefsStrings["2.1"] = "最高";
$prefsStrings["2.2"] = " 件ずつ表示";
$prefsStrings["2.3"] = "容量を表示";
$prefsStrings["2.4"] = "";
$prefsStrings["2.5"] = "順にソート";
$prefsStrings["2.6"] = "";
$prefsStrings["2.7"] = "表示";
$prefsStrings["2.8"] = "フォルダーリストを表示";
$prefsStrings["2.9"] = "フォルダリストの幅";
$prefsStrings["2.10"] = "%n秒置きに新着メールチェック";
$prefsStrings["2.11"] = "ツールバーをリストの%mに表示";
$prefsStrings["2.12"] = array("b"=>"下", "t"=>"上", "bt"=>"上と下");
$prefsStrings["2.13"] = "コラムの順序を変更: ";
$prefsStrings["2.14"] = "ここにクリック";
$prefsStrings["3.0"] = "オートセーブ機能";
$prefsStrings["3.1"] = "送信済みアイテムを保存";
$prefsStrings["3.2"] = "送信済みアイテムは ";
$prefsStrings["3.3"] = "フォルダに保存";
$prefsStrings["3.4"] = "自動的にゴミ箱を空にする";
$prefsStrings["3.5"] = "削除済みアイテムは";
$prefsStrings["3.6"] = "フォルダーに移動";
$prefsStrings["3.7"] = "ルートディレクトリ：";
$prefsStrings["3.8"] = "その他";
$prefsStrings["3.9"] = "登録されたフォルダのみ表示";
$prefsStrings["3.10"] = "ディスク使用量を%m";
$prefsStrings["3.11"] = array(""=>"表示しない", "m"=>"メッセージリストに表示", "f"=>"フォルダリストに表示");
$prefsStrings["4.0"] = "メッセージ表示";
$prefsStrings["4.1"] = "フレーム内で表示";
$prefsStrings["4.2"] = "引用文を識別";
$prefsStrings["4.3"] = "このオプションを選択すると処理に時間が掛かる事があります。";
$prefsStrings["4.4"] = "自動的にURLを認識";
$prefsStrings["4.5"] = "HTMLメールをフレーム内で表示";
$prefsStrings["4.6"] = "添付画像をフレーム内で表示";
$prefsStrings["4.7"] = "ナビゲーションリンクを表示";
$prefsStrings["4.8"] = "ナビゲーションリンクで閲覧したメッセージは「未読」のまま残す";
$prefsStrings["5.0"] = "署名";
$prefsStrings["5.1"] = "デフォルトで表示";
$prefsStrings["5.2"] = "メッセージ作成ウインドウで署名の有無を選択する事もできます";
$prefsStrings["6.0"] = "メッセージ作成／送信";
$prefsStrings["6.1"] = "アドレス帳を表示";
$prefsStrings["6.2"] = "CC/BCC欄を表示";
$prefsStrings["6.3"] = "送信後閉じる";
$prefsStrings["6.4"] = "新規メッセージはフレーム内で表示";
$prefsStrings["7.0.0"] = "ディスク／フォルダオプション";
$prefsStrings["7.0.1"] = "ディスク容量制限";
$prefsStrings["7.1"] = "未読メッセージの数を表示";
$prefsStrings["7.2"] = "フォルダリストを%n秒おきにリロード";

									
$sort_fields=array("DATE"=>"日付け", "SUBJECT"=>"件名", "SIZE"=>"容量");
$sort_orders=array("ASC"=>"昇順", "DESC"=>"降順");

$prefsButtonStrings = array( 0=> "更新", 1=> "すぐに反映", 2=>"キャンセル", 3=>"デフォルト");

$prefs_new_user = "ようこうそ！";
$prefs_new_user .= "以下の設定を行ってから「すぐに反映」ボタンにクリックしてください。";
?>