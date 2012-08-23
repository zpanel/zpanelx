<?php
/**
 * edit_dic.mod
 * -------------
 * Squirrelspell module
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This module lets the user edit his/her personal dictionary.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: edit_dic.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

global $color;
/**
 * Get the user dictionary and see if it's empty or not.
 */
$words=sqspell_getWords();
if (!$words){
  /**
   * Agt. Smith: "You're empty."
   * Neo: "So are you."
   */
  sqspell_makePage(_("Personal Dictionary"), null, 
		   '<p>' . _("No words in your personal dictionary.") 
		   . '</p>');
} else {
  /**
   * We're loaded with booty.
   */
  $pre_msg = '<p>' 
     . _("Please check any words you wish to delete from your dictionary.") 
     . "</p>\n";
  $pre_msg .= "<table border=\"0\" width=\"95%\" align=\"center\">\n";
  /**
   * Get how many dictionaries this user has defined.
   */
  $langs=sqspell_getSettings($words);
  for ($i=0; $i<sizeof($langs); $i++){
    /**
     * Get all words from this language dictionary.
     */
    $lang_words = sqspell_getLang($words, $langs[$i]);
    if ($lang_words){
      /**
       * There are words in this dictionary. If this is the first
       * language we're processing, prepend the output with the
       * "header" message.
       */
      if (!isset($msg) || !$msg) {
	$msg = $pre_msg;
      }
      $msg .= "<tr bgcolor=\"$color[0]\" align=\"center\"><th>"
	 . sprintf( _("%s dictionary"), $langs[$i] ) . '</th></tr>'
	 . '<tr><td align="center">'
	 . '<form method="post">'
	 . '<input type="hidden" name="MOD" value="forget_me" />'
	 . '<input type="hidden" name="sqspell_use_app" value="' 
	 . $langs[$i] . '" />'
	 . '<table border="0" width="95%" align="center">'
	 . '<tr>'
	 . "<td valign=\"top\">\n";
      $words_ary=explode("\n", $lang_words);
      /**
       * There are two lines we need to remove:
       * 1st:  # Language
       * last: # End
       */
      array_pop($words_ary);
      array_shift($words_ary);
      /**
       * Do some fancy stuff to separate the words into three 
       * columns.
       */
      for ($j=0; $j<sizeof($words_ary); $j++){
	if ($j==intval(sizeof($words_ary)/3) 
	    || $j==intval(sizeof($words_ary)/3*2)){
	  $msg .= "</td><td valign=\"top\">\n";
	}
	$msg .= '<input type="checkbox" name="words_ary[]" '
	   . 'value="' . htmlspecialchars($words_ary[$j]) . '" /> '
	   . htmlspecialchars($words_ary[$j])."<br>\n";
      }
      $msg .= '</td></tr></table></td></tr>'
	 . "<tr bgcolor=\"$color[0]\" align=\"center\"><td>"
	 . '<input type="submit" value="' . _("Delete checked words") 
	 . '" /></form>'
	 . '</td></tr><tr><td><hr />'
	 . "</td></tr>\n";
    }
  }
  /**
   * Check if all dictionaries were empty.
   */
  if (empty($msg)) {
    $msg = '<p>' . _("No words in your personal dictionary.") . '</p>';
  } else {
    $msg .= '</table>';
  }
  sqspell_makePage(_("Edit your Personal Dictionary"), null, $msg);
}

/**
 * For Emacs weenies:
 * Local variables:
 * mode: php
 * End:
 * vim: syntax=php
 */

