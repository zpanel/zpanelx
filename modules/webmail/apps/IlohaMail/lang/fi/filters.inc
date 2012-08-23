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
$fltr["filter"] = "Suodatin";
$fltr["conditions"] = "Toimintaehdot";
$fltr["actions"] = "Toiminnat";
$fltr["auto"] = "K&auml;yt&auml; automaattisesti saapuneisiin viesteihin";
$fltr["deletemsg"] = "Poista viesti";
$fltr["&"] = "ja";
$fltr["|"] = "tai";
$fltr["^"] = "alkaa";
$fltr["<"] = "sis&auml;lt&auml;&auml;";
$fltr["$"] = "loppuu";
$fltr["="] = "kyll&auml;";
$fltr["!"] = "ei";
$fltr["field"] = "Kentt&auml;";
$fltr["condition"] = "Ehto";
$fltr["action"] = "Toiminta";
$fltr["name"] = "Suodattimen nimi";
$fltr["sort_order"] = "Prioriteetti";
$fltr["edit"] = "muokkaa";
$fltr["add"] = "Tallenna"; // add
$fltr["edit_filter"] = "Tallenna"; // add
$fltr["delete"] = "Poista";
$fltr["filters"] = "Suodattimet";
$fltr["new"] = "Uusi suodatin";
$fltr["status"] = "Tila";
$fltr["stat_en"] = "k&auml;yt&ouml;ss&auml;";
$fltr["stat_dis"] = "ei k&auml;yt&ouml;ss&auml;";
$fltr["is_active"] = "k&auml;yt&auml; t&auml;t&auml; suodatinta";
$fltr["other"] = "Muu kentt&auml;";
$fltr["move"] = "Siirr&auml; kansioon ";
$fltr["copy"] = "Kopioi kansioon ";
$fltr["do_nothing"] = "Ei toimintaa";
$fltr["add_cond"] = "Lis&auml;&auml; ehto";
$fltr["remove_cond"] = "Poista ehto";
$fltr["condition_str"] = "%b %c %t";

$f_hlp["priority"] = "(suuremman prioriteetin suodattimet k&auml;sitell&auml;&auml;n ensin)";

$f_err["no_name"] = "Kirjoita suodattimelle nimi\n";
$f_err["no_act"] = "Valitse v&auml;hint&auml;&auml;n yksi toiminta\n";
$f_err["no_cond"] = "M&auml;&auml;rit&auml; v&auml;hint&auml;&auml;n yksi toimintaehto\n";
$f_err["inv_param"] = "Toiminta-asetuksissa on vikaa.\n";
$f_err["inv_sort"] = "Prioriteetin t&auml;ytyy olla numero.\n";

?>
