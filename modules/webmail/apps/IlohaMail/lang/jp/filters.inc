<?php
/********************************************************
	lang/eng/filters.inc
	(C)2001-2004 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/filters.inc");

$fltr["filter"] = "フィルター";
$fltr["conditions"] = "フィルター条件";
$fltr["actions"] = "アクション";
$fltr["auto"] = "自動的に適用";
$fltr["deletemsg"] = "該当項目を消去";
$fltr["&"] = "さらに";
$fltr["|"] = "もしくは";
$fltr["^"] = "で始まる";
$fltr["<"] = "を含む";
$fltr["$"] = "で終わる";
$fltr["="] = "該当する";
$fltr["!"] = "該当しない";
$fltr["field"] = "ヘッダー項目";
$fltr["condition"] = "条件";
$fltr["action"] = "アクション";
$fltr["name"] = "名前";
$fltr["sort_order"] = "順番";
$fltr["edit"] = "編集";
$fltr["add"] = "新規フィルター";
$fltr["edit_filter"] = "編集";
$fltr["delete"] = "削除";
$fltr["filters"] = "フィルター";
$fltr["new"] = "新規フィルター";
$fltr["status"] = "ステータス";
$fltr["stat_en"] = "オン";
$fltr["stat_dis"] = "オフ";
$fltr["is_active"] = "作動中";
$fltr["other"] = "その他";
$fltr["move"] = "移動する：";
$fltr["copy"] = "コピーする：";
$fltr["do_nothing"] = "動作なし";
$fltr["add_cond"] = "条件を追加";
$fltr["remove_cond"] = "条件を削除";
$fltr["condition_str"] = "「%t　%c」に%b";

$f_hlp["priority"] = "(高い順に適用します)";

$f_err["no_name"] = "このフィルターの名前を指定してください\n";
$f_err["no_act"] = "条件に合った項目に対するアクションを指定してください\n";
$f_err["no_cond"] = "フィルター条件を指定してください\n";
$f_err["inv_param"] = "選択されたアクションの設定が不適切です\n";
$f_err["inv_sort"] = "順番は数値で表してください\n";

?>
