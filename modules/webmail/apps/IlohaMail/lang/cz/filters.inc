<?php
/********************************************************
	lang/eng/filters.inc
	(C)2001-2004 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/filters.inc");

# prefs.inc:$prefHeaderStrings[4] = $filter_title["filters"] = "Filters";
	

/*
$condition[] fields: "operand", "field", "bool", "condition", "subject"
*/

$fltr["filter"] = "Filtry";
$fltr["conditions"] = "Podmínky";
$fltr["actions"] = "Akce";
$fltr["auto"] = "Automaticky aplikovat na doruèenou po¹tu";
$fltr["deletemsg"] = "Smazat zprávu";
$fltr["&"] = "a";
$fltr["|"] = "nebo";
$fltr["^"] = "zaèíná";
$fltr["<"] = "obsahuje";
$fltr["$"] = "konèí";
$fltr["="] = "";
$fltr["!"] = "ne";
$fltr["field"] = "Pole";
$fltr["condition"] = "Podmínka";
$fltr["action"] = "Akce";
$fltr["name"] = "Jméno";
$fltr["sort_order"] = "Priorita";
$fltr["edit"] = "upravit";
$fltr["add"] = "Pøidat filtr";
$fltr["edit_filter"] = "Upravit filtr";
$fltr["delete"] = "Smazat";
$fltr["filters"] = "Filtry";
$fltr["new"] = "Nový filtr";
$fltr["status"] = "Stav";
$fltr["stat_en"] = "povolený";
$fltr["stat_dis"] = "zakázaný";
$fltr["is_active"] = "filter je povolený";
$fltr["other"] = "Ostatní";
$fltr["move"] = "Pøesunout do";
$fltr["copy"] = "Zkopírovat do";
$fltr["do_nothing"] = "Nedìlat nic";
$fltr["add_cond"] = "Pøidat podmínku";
$fltr["remove_cond"] = "Smazat podmínku";
$fltr["condition_str"] = "%b %c %t";

$f_hlp["priority"] = "(Nejdøíve se aplikuje vy¹¹í priorita.)";

$f_err["no_name"] = "Zadejte prosím jméo pro toto filtrovací pravidlo\n";
$f_err["no_act"] = "Vyberte prosím jedno nebo více pravidel pro tento filtr\n";
$f_err["no_cond"] = "Vyberte prosím jednu nebo více podmínek pro tento filtr\n";
$f_err["inv_param"] = "Neplatné parametry pro vybranou akci\n";
$f_err["inv_sort"] = "Priorita øazení musí být èíslo\n";

?>
