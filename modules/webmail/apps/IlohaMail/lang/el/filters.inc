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
$fltr["filter"] = "Φίλτρο";
$fltr["conditions"] = "Συνθήκες";
$fltr["actions"] = "Ενέργειες";
$fltr["auto"] = "Αυτόματη εφαρμογή στα εισερχόμενα";
$fltr["deletemsg"] = "Διαγραφή επιστολής";
$fltr["&"] = "και";
$fltr["|"] = "ή";
$fltr["^"] = "ξεκινάει με";
$fltr["<"] = "περιέχει";
$fltr["$"] = "τελειώνει με";
$fltr["="] = "";
$fltr["!"] = "δεν";
$fltr["field"] = "Πεδίο";
$fltr["condition"] = "Συνθήκη";
$fltr["action"] = "Ενέργεια";
$fltr["name"] = "Όνομα";
$fltr["sort_order"] = "Πρωτεραιότητα";
$fltr["edit"] = "σύνταξη";
$fltr["add"] = "Προσθήκη Φίλτρου";
$fltr["edit_filter"] = "Σύνταξη Φίλτρου";
$fltr["delete"] = "Διαγραφή";
$fltr["filters"] = "Φίλτρα";
$fltr["new"] = "Νέο Φίλτρο";
$fltr["status"] = "Κατάσταση";
$fltr["stat_en"] = "ενεργοποιημένο";
$fltr["stat_dis"] = "απενεργοποιημένο";
$fltr["is_active"] = "το φίλτρο είναι ενεργοποιημένο";
$fltr["other"] = "¶λλο";
$fltr["move"] = "Μετακίνηση στο";
$fltr["copy"] = "Αντιγραφή στο";
$fltr["do_nothing"] = "Καμμία ενέργεια";
$fltr["add_cond"] = "Προσθήκη Συνθήκης";
$fltr["remove_cond"] = "Μετακίνηση Συνθήκης";
$fltr["condition_str"] = "%b %c %t";

$f_hlp["priority"] = "(Υψηλότερες πρωτεραιότητες τρέχουν πρώτες.)";

$f_err["no_name"] = "Παρακαλώ δώστε ένα όνομα γι'αυτό τον κανόνα φίλτρου\n";
$f_err["no_act"] = "Παρακαλώ επιλέξτε μία ή περισσότερες ενέργειες γι'αυτό τον κανόνα\n";
$f_err["no_cond"] = "Παρακαλώ καθορίστε μία ή περισσότερες συνθήκες γι'αυτό τον κανόνα\n";
$f_err["inv_param"] = "Μη έγκυρες παράμετροι για τις επιλεγμένες ενέργειες\n";
$f_err["inv_sort"] = "Η σειρά ταξινόμησης πρέπει να είναι αριθμητική\n";

?>
