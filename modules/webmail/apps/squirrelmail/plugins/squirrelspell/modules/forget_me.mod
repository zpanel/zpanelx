<?php
/**
 * forget_me.mod
 * --------------
 * Squirrelspell module
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This module deletes the words from the user dictionary. Called
 * after EDIT_DIC module.
 *
 *
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: forget_me.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

global $SQSPELL_VERSION;

$words_ary = $_POST['words_ary'];
$sqspell_use_app = $_POST['sqspell_use_app'];

/**
 * If something needs to be deleted, then $words_ary will be
 * non-zero length.
 */
if (sizeof($words_ary)){
  $words=sqspell_getWords();
  $lang_words = sqspell_getLang($words, $sqspell_use_app);
  $msg = '<p>'
     . sprintf(_("Deleting the following entries from %s dictionary:"), '<strong>'.$sqspell_use_app.'</strong>')
     . '</p>'
     . "<ul>\n";
  for ($i=0; $i<sizeof($words_ary); $i++){
    /**
     * Remove word by word...
     */
    $lang_words=str_replace("$words_ary[$i]\n", "", $lang_words);
    $msg .= '<li>' . htmlspecialchars($words_ary[$i]) . "</li>\n";
  }
  $new_words_ary=split("\n", $lang_words);
  /**
   * Wipe this lang, if only 2 members in array (no words left).
   * # Language
   * # End
   */
  if (sizeof($new_words_ary)<=2) {
    $lang_words='';
  }
  $new_lang_words = $lang_words;
  /**
   * Write the dictionary back to the disk.
   */
  $langs=sqspell_getSettings($words);
  $words_dic = "# SquirrelSpell User Dictionary $SQSPELL_VERSION\n# "
     . "Last Revision: " . date("Y-m-d") . "\n# LANG: "
     . join(", ", $langs) . "\n";
  for ($i=0; $i<sizeof($langs); $i++){
    /**
     * Only rewrite the contents of the selected language.
     * Otherwise just write the contents back.
     */
    if ($langs[$i]==$sqspell_use_app) {
      $lang_words = $new_lang_words;
    } else {
      $lang_words = sqspell_getLang($words, $langs[$i]);
    }
    if ($lang_words) {
      $words_dic .= $lang_words;
    }
  }
  $words_dic .= "# End\n";
  sqspell_writeWords($words_dic);
  $msg .= '</ul><p>' . _("All done!") . "</p>\n";
  sqspell_makePage(_("Personal Dictionary Updated"), null, $msg);
} else {
  /**
   * Click on some words first, Einstein!
   */
  sqspell_makePage(_("Personal Dictionary"), null,
          '<p>' . _("No changes requested.") . '</p>');
}

/**
 * For Emacs weenies:
 * Local variables:
 * mode: php
 * End:
 * vim: syntax=php
 */
?>