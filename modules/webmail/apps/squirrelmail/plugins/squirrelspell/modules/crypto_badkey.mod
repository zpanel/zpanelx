<?php
/**
 * crypto_badkey.mod
 * ------------------
 * Squirrelspell module
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This module tries to decrypt the user dictionary with a newly provided
 * old password, or erases the file if everything else fails. :(
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: crypto_badkey.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

global $SCRIPT_NAME;

$delete_words = $_POST['delete_words'];
if(isset($_POST['old_key'])) {
    $old_key = $_POST['old_key'];
}

if ($delete_words=='ON'){
  /**
   * $delete_words is passed via the query_string. If it's set, then
   * the user asked to delete the file. Erase the bastard and hope
   * this never happens again.
   */
  sqspell_deleteWords();
  /**
   * See where we were called from -- pop-up window or options page
   * and call whichever wrapper is appropriate.
   * I agree, this is dirty. TODO: make it so it's not dirty.
   */
  if (strstr($SCRIPT_NAME, 'sqspell_options')){
    $msg='<p>' . _("Your personal dictionary was erased.") . '</p>';
    sqspell_makePage(_("Dictionary Erased"), null, $msg);
  } else {
    /**
     * The _("Your....") has to be on one line. Otherwise xgettext borks
     * on getting the strings.
     */
    $msg = '<p>'
       . _("Your personal dictionary was erased. Please close this window and click \"Check Spelling\" button again to start your spellcheck over.")
       . '</p> '
       . '<p align="center"><form>'
       . '<input type="button" value=" '
       . _("Close this Window") . ' " onclick="self.close()">'
       . '</form></p>';
    sqspell_makeWindow(null, _("Dictionary Erased"), null, $msg);
  }
  exit;
}

if ($old_key){
  /**
   * User provided another key to try and decrypt the dictionary.
   * Call sqspell_getWords. If this key fails, the function will
   * handle it.
   */
  $words=sqspell_getWords();
  /**
   * It worked! Pinky, you're a genius!
   * Write it back this time encrypted with a new key.
   */
  sqspell_writeWords($words);
  /**
   * See where we are and call a necessary GUI-wrapper.
   * Also dirty. TODO: Make this not dirty.
   */
  if (strstr($SCRIPT_NAME, 'sqspell_options')){
    $msg = '<p>'
       . _("Your personal dictionary was re-encrypted successfully. Now return to the &quot;SpellChecker options&quot; menu and make your selection again." )
       . '</p>';
    sqspell_makePage(_("Successful re-encryption"), null, $msg);
  } else {
    $msg = '<p>'
        . _("Your personal dictionary was re-encrypted successfully. Please close this window and click \"Check Spelling\" button again to start your spellcheck over.")
        . '</p><form><p align="center"><input type="button" value=" '
        . _("Close this Window") . ' "'
        . 'onclick="self.close()" /></p></form>';
    sqspell_makeWindow(null, _("Dictionary re-encrypted"), null, $msg);
  }
  exit;
}

/**
 * For Emacs weenies:
 * Local variables:
 * mode: php
 * End:
 * vim: syntax=php
 */
?>