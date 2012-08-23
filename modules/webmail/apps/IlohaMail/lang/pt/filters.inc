<?php
/********************************************************
	lang/eng/filters.inc
	(C)2001-2004 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/filters.inc");

$fltr["filter"] = "Filter";
$fltr["conditions"] = "Conditions";
$fltr["actions"] = "Actions";
$fltr["auto"] = "Automatically apply to inbox";
$fltr["deletemsg"] = "Delete message";
$fltr["&"] = "and";
$fltr["|"] = "or";
$fltr["^"] = "begin with";
$fltr["<"] = "contain";
$fltr["$"] = "end with";
$fltr["="] = "does";
$fltr["!"] = "doesn't";
$fltr["field"] = "Field";
$fltr["condition"] = "Condition";
$fltr["action"] = "Action";
$fltr["name"] = "Name";
$fltr["sort_order"] = "Priority";
$fltr["edit"] = "edit";
$fltr["add"] = "Add Filter";
$fltr["edit_filter"] = "Edit Filter";
$fltr["delete"] = "Delete";
$fltr["filters"] = "Filters";
$fltr["new"] = "New Filter";
$fltr["status"] = "Status";
$fltr["stat_en"] = "enabled";
$fltr["stat_dis"] = "disabled";
$fltr["is_active"] = "filter is enabled";
$fltr["other"] = "Other";
$fltr["move"] = "Move to";
$fltr["copy"] = "Copy to";
$fltr["do_nothing"] = "Do nothing";
$fltr["add_cond"] = "Add Condition";
$fltr["remove_cond"] = "Remove Condition";
$fltr["condition_str"] = "%b %c %t";

$f_hlp["priority"] = "(Higher priorities run first.)";

$f_err["no_name"] = "Please enter a name for this filter rule\n";
$f_err["no_act"] = "Please select one or more actions for this rule\n";
$f_err["no_cond"] = "Please specifiy one or more conditions for this rule\n";
$f_err["inv_param"] = "Invalid parameters for selected actions\n";
$f_err["inv_sort"] = "Sort order must be numeric\n";

?>
