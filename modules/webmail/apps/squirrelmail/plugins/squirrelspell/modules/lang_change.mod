<?php
/**
 * lang_change.mod
 * ----------------
 * Squirrelspell module
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This module changes the international dictionaries selection
 * for the user. Called after LANG_SETUP module.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: lang_change.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_POST)) {
    $submitted_token = '';
}
sm_validate_security_token($submitted_token, 3600, TRUE);

global $SQSPELL_APP_DEFAULT;

$use_langs = $_POST['use_langs'];
$lang_default = $_POST['lang_default'];

$words = sqspell_getWords();
if (!$words) {
  $words = sqspell_makeDummy();
}
$langs = sqspell_getSettings($words);
if (sizeof($use_langs)){
  /**
   * See if the user clicked any options on the previous page.
   */
  if (sizeof($use_langs)>1){
    /**
     * See if s/he wants more than one dictionary.
     */
    if ($use_langs[0]!=$lang_default){
      /**
       * See if we need to juggle the order of the dictionaries
       * to make the default dictionary first in line.
       */
      if (in_array($lang_default, $use_langs)){
	/**
	 * See if the user was dumb and chose a default dictionary
	 * to be something other than the ones he selected.
	 */
	$hold = array_shift($use_langs);
	$lang_string = join(", ", $use_langs);
	$lang_string = str_replace("$lang_default", "$hold", $lang_string);
	$lang_string = $lang_default . ", " . $lang_string;
      } else {
	/**
	 * Yes, he is dumb.
	 */
	$lang_string = join(', ', $use_langs);
      }
    } else {
      /**
       * No need to juggle the order -- preferred is already first.
       */
      $lang_string = join(', ', $use_langs);
    }
  } else {
    /**
     * Just one dictionary, please.
     */
    $lang_string = $use_langs[0];
  }
  $lang_array = explode( ',', $lang_string );
  $dsp_string = '';
  foreach( $lang_array as $a) {
    $dsp_string .= _(htmlspecialchars(trim($a))) . _(", ");
  }
  $dsp_string = substr( $dsp_string, 0, -2 );
  $msg = '<p>'
    . sprintf(_("Settings adjusted to: %s with %s as default dictionary."), '<strong>'.$dsp_string.'</strong>', '<strong>'._(htmlspecialchars($lang_default)).'</strong>')
    . '</p>';
} else {
  /**
   * No dictionaries selected. Use system default.
   */
  $msg = '<p>'
    . sprintf(_("Using %s dictionary (system default) for spellcheck." ), '<strong>'.$SQSPELL_APP_DEFAULT.'</strong>')
    . '</p>';
  $lang_string = $SQSPELL_APP_DEFAULT;
}
$old_lang_string = join(", ", $langs);
$words = str_replace("# LANG: $old_lang_string", "# LANG: $lang_string",
		     $words);
/**
 * Write it down where the sun don't shine.
 */
sqspell_writeWords($words);
sqspell_makePage(_("International Dictionaries Preferences Updated"),
        null, $msg);

/**
 * For Emacs weenies:
 * Local variables:
 * mode: php
 * End:
 * vim: syntax=php
 */

