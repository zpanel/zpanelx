<?php
/********************************************************
	lang/eng/compose.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/compose.inc");

$composeHStrings[0] = "Θέμα";
$composeHStrings[1] = "Από";
$composeHStrings[2] = "Πρός";
$composeHStrings[3] = "CC";
$composeHStrings[4] = "BCC";
$composeHStrings[5] = "Ημερομηνία";


$composeStrings[0] = "Σύνθεση μηνύματος";
$composeStrings[1] = "Αποστολή μηνύματος";
$composeStrings[2] = "Φόρτωση";
$composeStrings[3] = "Επισύναψη υπογραφής";
$composeStrings[4] = "Επισύναψη";
$composeStrings[5] = "Επαφές";
$composeStrings[6] = "Απόκρυψη επαφών";
$composeStrings[7] = "Σώμα μηνύματος:";
$composeStrings[8] = "Προσθήκη";
$composeStrings[9] = "Στις %d, %s γράψατε:\n";
$composeStrings[10] = "--- Προτώτυπο μήνυμα ---\n";
$composeStrings[11] = "Κλείσιμο Παραθύρου";

$composeStrings[12] = "Έλεγχος ορθογραφίας";
$composeStrings[13] = "Διόρθωση ορθογραφίας";
$composeStrings[14] = "Όχι αλλαγές";
$composeStrings[15] = "Πιθανά ορθογραφικά λάθη";
$composeStrings[16] = "Ορθογραφικές Αλλαγές";
$composeStrings[17] = "αγνόησε";
$composeStrings[18] = "διέγραψε";

$composeStrings["title"] = "Σύνθεση μηνύματος";

$composeErrors[0] = "Το πεδίο του θέματος είναι άδειο. Παρακαλώ προσθέστε θέμα ή κάντε click \"".$composeStrings[1]."\" ξανά για αποστολή.";
$composeErrors[1] = "Το πεδίο \"από\" είναι άδειο.  Παρακαλώ βάλτε τη διεύθυνση του email σας.";
$composeErrors[2] = "Το πεδίο \"πρός\" είναι άδειο.  Παρακαλώ διευκρινήστε την διεύθυνση αποστολής.";
$composeErrors[3] = "Η φόρτωση απέτυχε";
$composeErrors[4] = "Δεν παραλήφθηκαν αρχεία.";
$composeErrors[5] = "Για λόγους πρόληψης spam μπορείτε να στείλετε μόνο σε %1 ανθρώπους (%2 συνολικά ανα συνεδρία) κάθε %3 δευτερόλεπτα.";

$composeErrors[6] = "Δέν βρέθηκαν ορθογραφικά λάθη.";
$composeErrors[7] = "Η ορθογραφική διόρθωση δεν είναι διαθέσιμη για τη συγκεκριμένη γλώσσα.";
$composeErrors[8] = "(Χρησιμοποίηθηκε το %s λεξικό)";
?>