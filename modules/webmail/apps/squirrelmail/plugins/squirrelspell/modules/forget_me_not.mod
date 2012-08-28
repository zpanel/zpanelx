<?php
/**
 * forget_me_not.mod
 * ------------------
 * Squirrelspell module
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This module saves the added words into the user dictionary. Called
 * after CHECK_ME module.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: forget_me_not.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

global $SQSPELL_VERSION, $SQSPELL_APP_DEFAULT;

$words = $_POST['words'];
$sqspell_use_app = $_POST['sqspell_use_app'];

/**
 * Because of the nature of Javascript, there is no way to efficiently
 * pass an array. Hence, the words will arrive as a string separated by
 * "%". To get the array, we explode the "%"'s.
 * Dirty: yes. Is there a better solution? Let me know. ;)
 */
$new_words = ereg_replace("%", "\n", $words);
/**
 * Load the user dictionary and see if there is anything in it.
 */
$words=sqspell_getWords();
if (!$words){
  /**
   * First time.
   */
  $words_dic="# SquirrelSpell User Dictionary $SQSPELL_VERSION\n# Last "
     . "Revision: " . date("Y-m-d")
     . "\n# LANG: $SQSPELL_APP_DEFAULT\n# $SQSPELL_APP_DEFAULT\n";
  $words_dic .= $new_words . "# End\n";
} else {
  /**
   * Do some fancy stuff in order to save the dictionary and not mangle the
   * rest.
   */
  $langs=sqspell_getSettings($words);
  $words_dic = "# SquirrelSpell User Dictionary $SQSPELL_VERSION\n# "
     . "Last Revision: " . date("Y-m-d") . "\n# LANG: " . join(", ", $langs)
     . "\n";
  for ($i=0; $i<sizeof($langs); $i++){
    $lang_words=sqspell_getLang($words, $langs[$i]);
    if ($langs[$i]==$sqspell_use_app){
      if (!$lang_words) {
	$lang_words="# $langs[$i]\n";
      }
      $lang_words .= $new_words;
    }
    $words_dic .= $lang_words;
  }
  $words_dic .= "# End\n";
}

/**
 * Write out the file
 */
sqspell_writeWords($words_dic);
/**
 * display the splash screen, then close it automatically after 2 sec.
 */
$onload = "setTimeout('self.close()', 2000)";
$msg = '<form onsubmit="return false"><div align="center">'
   . '<input type="submit" value="  '
   . _("Close") . '  " onclick="self.close()" /></div></form>';
sqspell_makeWindow($onload, _("Personal Dictionary Updated"), null, $msg);
