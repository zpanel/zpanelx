<?php
/********************************************************
	lang/eng/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Επιλογές";
$prefHeaderStrings[1] = "Γενικά";
$prefHeaderStrings[3] = "Ταυτότητες";
$prefHeaderStrings[2] = "Εμφάνιση";

											
$prefsStrings["0.0"] = "Προκαθορισμένη Ταυτότητα";
$prefsStrings["0.1"] = "Όνομα: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "Αλλαγές μπορούν να γίνουν στη σελίδα \"%s\".";
$prefsStrings["0.4"] = "Απροσδιόριστο";
$prefsStrings["1.0"] = "Διεθνές";
$prefsStrings["1.1"] = "Interface: ";
$prefsStrings["1.2"] = "Κωδικοσελίδα: ";
$prefsStrings["1.3"] = "Ζώνη Ώρας: ";
$prefsStrings["1.4"] = "Επιλογή Ώρας: ";
$prefsStrings["1.5"] = array(12=>"12ωρο ρολόι", 24=>"24ωρο ρολόι");
$prefsStrings["2.0"] = "Προβολή προτιμησεων εμφάνισης";
$prefsStrings["2.1"] = "Δείξε μέχρι ";
$prefsStrings["2.2"] = " στοιχεία";
$prefsStrings["2.3"] = "Εμφάνιση μεγέθους";
$prefsStrings["2.4"] = "Ταξινόμηση κατά: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Ταξινόμηση: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Προβολή λίστας φακλέλων";
$prefsStrings["2.9"] = "Πλάτος λίστας φακέλων";
$prefsStrings["2.10"] = "Έλεγχος για νέα μηνύματα ανα %n δευτερόλεπτα";
$prefsStrings["2.11"] = "Εμφάνιση γραμμής εργασιών σε %m";
$prefsStrings["2.12"] = array("b"=>"πάτο", "t"=>"κορυφή", "bt"=>"κορυφή και πάτο");
$prefsStrings["2.13"] = "Αναδιοργάνωση στηλών: ";
$prefsStrings["2.14"] = "κάντε click εδώ";
$prefsStrings["3.0"] = "Ρυθμίσεις αυτόματης αποθήκευσης";
$prefsStrings["3.1"] = "Αποθήκευση απεσταλμένων.";
$prefsStrings["3.2"] = "Αποθήκευση απεσταλμένων μηνυμάτων στον φάκελο: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Αυτόματος καθαρισμός απορριμάτων";
$prefsStrings["3.5"] = "Μετακίνηση σβησμένων στοιχείων στο φάκελο: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Ριζικός κατάλογος: ";
$prefsStrings["3.8"] = "Άλλο";
$prefsStrings["3.9"] = "Απόκρυψη μή εγγεγραμμένων φακέλων";
$prefsStrings["3.10"] = "Προβολή quota %m";
$prefsStrings["3.11"] = array(""=>"μή προβολή", "m"=>"στη λίστα μηνυμάτων", "f"=>"στη λίστα φακέλων");
$prefsStrings["4.0"] = "Προτιμήσεις εμφάνισης μηνυμάτων";
$prefsStrings["4.1"] = "Προβολή σε πλαίσιο";
$prefsStrings["4.2"] = "Χρωματισμός σχολίων";
$prefsStrings["4.3"] = "Ενεργοποιόντας αυτή τη δυνατότητα μπορεί να καθυστερεί το σύστημα.";
$prefsStrings["4.4"] = "Μετατροπή των URLs σε συνδέσεις";
$prefsStrings["4.5"] = "Προβολή HTML μηνυμάτων";
$prefsStrings["4.6"] = "Προβολή εικόνων εμβόλιμα";
$prefsStrings["4.7"] = "Προβολή πλοήγησης συνδέσεων";
$prefsStrings["4.8"] = "Μή σημείωση μηνυμάτων κατα την μετακίνηση σε αυτά";
$prefsStrings["5.0"] = "Υπογραφή";
$prefsStrings["5.1"] = "Προκαθορισμένη προβολή υπογραφής";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Σύνθεση";
$prefsStrings["6.1"] = "Προβολή επαφών";
$prefsStrings["6.2"] = "Προβολή πεδίων CC/BCC";
$prefsStrings["6.3"] = "Κλείσιμο μετά την αποστολή";
$prefsStrings["6.4"] = "Σύνθεση στο ίδιο πλάισιο";
$prefsStrings["7.0.0"] = "Disk &amp; Επιλογές Φακέλων";
$prefsStrings["7.0.1"] = "Επιλογές Quota";
$prefsStrings["7.1"] = "Προβολή αριθ. αδιάβαστων";
$prefsStrings["7.2"] = "Ανανέωση λίστας φακλέλων ανα y %n δευτερόλεπτα";

$pref_col_title = "Αναδιάταξη στηλών";
$pref_colstr["order"] = "Σειρά";
$pref_colstr["field"] = "Πεδίο";
$pref_col_label['c'] = "Τίκ";
$pref_col_label['a'] = "Συννημένα";
$pref_col_label['m'] = "Σημ/κατάσταση";

$sort_fields=array("DATE"=>"Ημερομηνία", "SUBJECT"=>"Θέμα", "SIZE"=>"Μέγεθος");
$sort_orders=array("ASC"=>"Αύξουσα", "DESC"=>"Φθίνουσα");

$prefsButtonStrings = array( 0=> "Ενημέρωση", 1=>"Εφαρμογή", 2=> "Ακύρωση", 3=> "Προκαθορισμένα");

$prefs_new_user = "Φένεται οτι δεν έχετε ξανασυνδεθεί ποτέ πρίν...καλωσήλθατε!<br>";
$prefs_new_user .= "Παρακαλώ εξετάστε τις παρακάτω ρυθμίσεις, και πατήστε \"Εφαρμογή\"";
?>