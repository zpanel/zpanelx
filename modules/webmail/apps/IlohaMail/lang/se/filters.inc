<?php
/********************************************************
	lang/eng/filters.inc
	(C)2001-2004 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	Translated by Stefan Pettersson <stefan@pezzen.org>

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/filters.inc");

 # prefs.inc:$prefHeaderStrings[4] = $filter_title["filters"] = "Filter";

/*
$condition[] fields: "operand", "field", "bool", "condition", "subject"
*/
$fltr["filter"] = "Filter";
$fltr["conditions"] = "Villkor";
$fltr["actions"] = "Åtgärder";
$fltr["auto"] = "Kör automatiskt i inkorgen";
$fltr["deletemsg"] = "Radera Meddelande";
$fltr["&"] = "och";
$fltr["|"] = "eller";
$fltr["^"] = "börjar med";
$fltr["<"] = "innehåller";
$fltr["$"] = "slutar med";
$fltr["="] = "Meddelanden som";
$fltr["!"] = "Meddelanden som inte";
$fltr["field"] = "Fält";
$fltr["condition"] = "Villkor";
$fltr["action"] = "Åtgärd";
$fltr["name"] = "Namn";
$fltr["sort_order"] = "Prioritet";
$fltr["edit"] = "Redigera";
$fltr["add"] = "Lägg till filter";
$fltr["edit_filter"] = "Redigera filter";
$fltr["delete"] = "Ta bort";
$fltr["filters"] = "Filter";
$fltr["new"] = "Nytt Filter";
$fltr["status"] = "Status";
$fltr["stat_en"] = "Aktiv";
$fltr["stat_dis"] = "Deaktiverad";
$fltr["is_active"] = "filtret är aktivt";
$fltr["other"] = "Annat";
$fltr["move"] = "Flytta till";
$fltr["copy"] = "Kopiera till";
$fltr["do_nothing"] = "Gör ingenting";
$fltr["add_cond"] = "Lägg till villkor";
$fltr["remove_cond"] = "Ta bort villkor";
$fltr["condition_str"] = "%b %c %t";

$f_hlp["priority"] = "(Högre prioriteringar körs först.)";

$f_err["no_name"] = "Namnge det här filtret\n";
$f_err["no_act"] = "Välj en eller flera åtgärder för det här filtret\n";
$f_err["no_cond"] = "Välj en eller flera villkor för det här filtret\n";
$f_err["inv_param"] = "Ogiltiga parametrar för filtret\n";$f_err["inv_sort"] = "Prioriterinsordningen måste vara numerisk\n";

?>