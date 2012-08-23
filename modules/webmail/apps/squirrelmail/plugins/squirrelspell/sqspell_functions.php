<?php
/**
 * sqspell_functions.php
 * ----------------------
 * All SquirrelSpell-wide functions are in this file.
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: sqspell_functions.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

/**
 * This function is the GUI wrapper for the options page. SquirrelSpell
 * uses it for creating all Options pages.
 *
 * @param  string $title     The title of the page to display
 * @param  string $scriptsrc This is used to link a file.js into the
 *                    <script src="file.js"></script> format. This
 *                    allows to separate javascript from the rest of the
 *                    plugin and place it into the js/ directory.
 * @param  string $body      The body of the message to display.
 * @return            void
 */
function sqspell_makePage($title, $scriptsrc, $body){
  global $color, $SQSPELL_VERSION;

  if (! sqgetGlobalVar('MOD', $MOD, SQ_GET) ) {
      $MOD = 'options_main';
  }

  displayPageHeader($color, 'None');
  echo "&nbsp;<br />\n";
  /**
   * Check if we need to link in a script.
   */
  if($scriptsrc) {
    echo "<script type=\"text/javascript\" src=\"js/$scriptsrc\"></script>\n";
  }
  echo html_tag( 'table', '', 'center', '', 'width="95%" border="0" cellpadding="2" cellspacing="0"' ) . "\n"
    . html_tag( 'tr', "\n" .
          html_tag( 'td', '<strong>' . $title .'</strong>', 'center', $color[9] )
      ) . "\n"
    . html_tag( 'tr', "\n" .
          html_tag( 'td', '<hr />', 'left' )
      ) . "\n"
    . html_tag( 'tr', "\n" .
          html_tag( 'td', $body, 'left' )
      ) . "\n";
  /**
   * Generate a nice "Return to Options" link, unless this is the
   * starting page.
   */
  if ($MOD != "options_main"){
    echo html_tag( 'tr', "\n" .
                html_tag( 'td', '<hr />', 'left' )
            ) . "\n"
      . html_tag( 'tr', "\n" .
            html_tag( 'td', '<a href="sqspell_options.php">'
                . _("Back to &quot;SpellChecker Options&quot; page")
                . '</a>',
            'center' )
        ) . "\n";
  }
  /**
   * Close the table and display the version.
   */
  echo html_tag( 'tr', "\n" .
              html_tag( 'td', '<hr />', 'left' )
          ) . "\n"
    . html_tag( 'tr',
          html_tag( 'td', 'SquirrelSpell ' . $SQSPELL_VERSION, 'center', $color[9] )
      ) . "\n</table>\n";
  echo '</body></html>';
}

/**
 * Function similar to the one above. This one is a general wrapper
 * for the Squirrelspell pop-up window. It's called form nearly
 * everywhere, except the check_me module, since that one is highly
 * customized.
 *
 * @param  string $onload    Used to indicate and pass the name of a js function
 *                    to call in a <body onload="function()" for automatic
 *                    onload script execution.
 * @param  string $title     Title of the page.
 * @param  string $scriptsrc If defined, link this javascript source page into
 *                    the document using <script src="file.js"> format.
 * @param  string $body      The content to include.
 * @return            void
 */
function sqspell_makeWindow($onload, $title, $scriptsrc, $body){
  global $color, $SQSPELL_VERSION;

  displayHtmlHeader($title,
      ($scriptsrc ? "\n<script type=\"text/javascript\" src=\"js/$scriptsrc\"></script>\n" : ''));

  echo "<body text=\"$color[8]\" bgcolor=\"$color[4]\" link=\"$color[7]\" "
      . "vlink=\"$color[7]\" alink=\"$color[7]\"";
  /**
   * Provide an onload="jsfunction()" if asked to.
   */
  if ($onload) {
      echo " onload=\"$onload\"";
  }
  /**
   * Draw the rest of the page.
   */
  echo ">\n"
    . html_tag( 'table', "\n" .
          html_tag( 'tr', "\n" .
              html_tag( 'td', '<strong>' . $title . '</strong>', 'center', $color[9] )
          ) . "\n" .
          html_tag( 'tr', "\n" .
              html_tag( 'td', '<hr />', 'left' )
          ) . "\n" .
          html_tag( 'tr', "\n" .
              html_tag( 'td', $body, 'left' )
          ) . "\n" .
          html_tag( 'tr', "\n" .
              html_tag( 'td', '<hr />', 'left' )
          ) . "\n" .
          html_tag( 'tr', "\n" .
              html_tag( 'td', 'SquirrelSpell ' . $SQSPELL_VERSION, 'center', $color[9] )
          ) ,
      '', '', 'width="100%" border="0" cellpadding="2"' )
    . "</body>\n</html>\n";
}

/**
 * This function does the encryption and decryption of the user
 * dictionary. It is only available when PHP is compiled with
 * mcrypt support (--with-mcrypt). See doc/CRYPTO for more
 * information.
 *
 * @param  $mode  A string with either of the two recognized values:
 *                "encrypt" or "decrypt".
 * @param  $ckey  The key to use for processing (the user's password
 *                in our case.
 * @param  $input Content to decrypt or encrypt, according to $mode.
 * @return        encrypted/decrypted content, or "PANIC" if the
 *                process bails out.
 */
function sqspell_crypto($mode, $ckey, $input){
  /**
   * Double-check if we have the mcrypt_generic function. Bail out if
   * not so.
   */
  if (!function_exists('mcrypt_generic')) {
    return 'PANIC';
  }
  /**
   * Setup mcrypt routines.
   */
  $td = mcrypt_module_open(MCRYPT_Blowfish, "", MCRYPT_MODE_ECB, "");
  $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
  mcrypt_generic_init($td, $ckey, $iv);
  /**
   * See what we have to do depending on $mode.
   * 'encrypt' -- Encrypt the content.
   * 'decrypt' -- Decrypt the content.
   */
  switch ($mode){
  case 'encrypt':
    $crypto = mcrypt_generic($td, $input);
    break;
  case 'decrypt':
    $crypto = mdecrypt_generic($td, $input);
    /**
     * See if it decrypted successfully. If so, it should contain
     * the string "# SquirrelSpell". If not, then bail out.
     */
    if (!strstr($crypto, "# SquirrelSpell")){
      $crypto='PANIC';
    }
    break;
  }
  /**
   * Finish up the mcrypt routines and return the processed content.
   */
  mcrypt_generic_deinit ($td);
  mcrypt_module_close ($td);
  return $crypto;
}

/**
 * This function transparently upgrades the 0.2 dictionary format to the
 * 0.3 format, since user-defined languages have been added in 0.3 and
 * the new format keeps user dictionaries selection in the file.
 *
 * This function will be retired soon, as it's been a while since anyone
 * has been using SquirrelSpell-0.2.
 *
 * @param  $words_string Contents of the 0.2-style user dictionary.
 * @return               Contents of the 0.3-style user dictionary.
 */
function sqspell_upgradeWordsFile($words_string){
  global $SQSPELL_APP_DEFAULT, $SQSPELL_VERSION;
  /**
   * Define just one dictionary for this user -- the default.
   * If the user wants more, s/he can set them up in personal
   * preferences. See doc/UPGRADING for more info.
   */
  $new_words_string =
     substr_replace($words_string,
                    "# SquirrelSpell User Dictionary $SQSPELL_VERSION\n# "
                    . "Last Revision: " . date("Y-m-d")
                    . "\n# LANG: $SQSPELL_APP_DEFAULT\n# $SQSPELL_APP_DEFAULT",
                    0, strpos($words_string, "\n")) . "# End\n";
  sqspell_writeWords($new_words_string);
  return $new_words_string;
}

/**
 * Right now it just returns an array with the dictionaries
 * available to the user for spell-checking. It will probably
 * do more in the future, as features are added.
 *
 * @param string $words The contents of the user's ".words" file.
 * @return array a strings array with dictionaries available
 *                to this user, e.g. {"English", "Spanish"}, etc.
 */
function sqspell_getSettings($words){
  global $SQSPELL_APP, $SQSPELL_APP_DEFAULT;
  /**
   * Check if there is more than one dictionary configured in the
   * system config.
   */
  if (sizeof($SQSPELL_APP) > 1){
    /**
     * Now load the user prefs. Check if $words was empty -- a bit of
     * a dirty fall-back. TODO: make it so this is not required.
     */
    if(!$words){
      $words=sqspell_getWords();
    }
    if ($words){
      /**
       * This user has a ".words" file.
       * Find which dictionaries s/he wants to use and load them into
       * the $langs array.
       */
      preg_match("/# LANG: (.*)/i", $words, $matches);
      $langs=explode(", ", $matches[1]);
    } else {
      /**
       * User doesn't have a personal dictionary. Grab the default
       * system setting.
       */
      $langs[0]=$SQSPELL_APP_DEFAULT;
    }
  } else {
    /**
     * There is no need to read the ".words" file as there is only one
     * dictionary defined system-wide.
     */
    $langs[0]=$SQSPELL_APP_DEFAULT;
  }
  return $langs;
}

/**
 * This function returns only user-defined dictionary words that correspond
 * to the requested language.
 *
 * @param  $words The contents of the user's ".words" file.
 * @param  $lang  Which language words to return, e.g. requesting
 *                "English" will return ONLY the words from user's
 *                English dictionary, disregarding any others.
 * @return        The list of words corresponding to the language
 *                requested.
 */
function sqspell_getLang($words, $lang){
  $start=strpos($words, "# $lang\n");
  /**
   * strpos() will return -1 if no # $lang\n string was found.
   * Use this to return a zero-length value and indicate that no
   * words are present in the requested dictionary.
   */
  if (!$start) return '';
  /**
   * The words list will end with a new directive, which will start
   * with "#". Locate the next "#" and thus find out where the
   * words end.
   */
  $end=strpos($words, "#", $start+1);
  $lang_words = substr($words, $start, $end-$start);
  return $lang_words;
}

/**
 * This function operates the user dictionary. If the format is
 * clear-text, then it just reads the file and returns it. However, if
 * the file is encrypted (well, "garbled"), then it tries to decrypt
 * it, checks whether the decryption was successful, troubleshoots if
 * not, then returns the clear-text dictionary to the app.
 *
 * @return the contents of the user's ".words" file, decrypted if
 *         necessary.
 */
function sqspell_getWords(){
  global $SQSPELL_WORDS_FILE, $SQSPELL_CRYPTO;
  $words="";
  if (file_exists($SQSPELL_WORDS_FILE)){
    /**
     * Gobble it up.
     */
    $fp=fopen($SQSPELL_WORDS_FILE, 'r');
    $words=fread($fp, filesize($SQSPELL_WORDS_FILE));
    fclose($fp);
  }
  /**
   * Check if this is an encrypted file by looking for
   * the string "# SquirrelSpell" in it (the crypto
   * function does that).
   */
  if ($words && !strstr($words, "# SquirrelSpell")){
    /**
     * This file is encrypted or mangled. Try to decrypt it.
     * If fails, complain loudly.
     *
     * $old_key would be a value submitted by one of the modules with
     * the user's old mailbox password. I admin, this is rather dirty,
     * but efficient. ;)
     */
    sqgetGlobalVar('key', $key, SQ_COOKIE);
    sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);

    sqgetGlobalVar('old_key', $old_key, SQ_POST);

    if ($old_key != '') {
        $clear_key=$old_key;
    } else {
      /**
       * Get user's password (the key).
       */
      $clear_key = OneTimePadDecrypt($key, $onetimepad);
    }
    /**
     * Invoke the decryption routines.
     */
    $words=sqspell_crypto("decrypt", $clear_key, $words);
    /**
     * See if decryption failed.
     */
    if ($words=="PANIC"){
      /**
       * AAAAAAAAAAAH!!!!! OK, ok, breathe!
       * Let's hope the decryption failed because the user changed his
       * password. Bring up the option to key in the old password
       * or wipe the file and start over if everything else fails.
       *
       * The _("SquirrelSpell...) line has to be on one line, otherwise
       * gettext will bork. ;(
       */
      $msg = html_tag( 'p', "\n" .
                     '<strong>' . _("ATTENTION:") . '</strong><br />'
                     .  _("SquirrelSpell was unable to decrypt your personal dictionary. This is most likely due to the fact that you have changed your mailbox password. In order to proceed, you will have to supply your old password so that SquirrelSpell can decrypt your personal dictionary. It will be re-encrypted with your new password after this. If you haven't encrypted your dictionary, then it got mangled and is no longer valid. You will have to delete it and start anew. This is also true if you don't remember your old password -- without it, the encrypted data is no longer accessible.") ,
                 'left' ) .  "\n"
	 . '<blockquote>' . "\n"
	 . '<form method="post" onsubmit="return AYS()">' . "\n"
	 . '<input type="hidden" name="MOD" value="crypto_badkey">' . "\n"
	 . html_tag( 'p',  "\n" .
	       '<input type="checkbox" name="delete_words" value="ON">'
	       . _("Delete my dictionary and start a new one") . '<br />'
	       . _("Decrypt my dictionary with my old password:")
	       . '<input name="old_key" size=\"10\">' ,
	   'left' ) . "\n"
	 . '</blockquote>' . "\n"
	 . html_tag( 'p', "\n" .
	       '<input type="submit" value="'
	       . _("Proceed") . ' &gt;&gt;">' ,
	   'center' ) . "\n"
	 . '</form>' . "\n";
      /**
       * Add some string vars so they can be i18n'd.
       */
      $msg .= "<script type='text/javascript'><!--\n"
	 . "var ui_choice = \"" . _("You must make a choice") ."\";\n"
	 . "var ui_candel = \"" . _("You can either delete your dictionary or type in the old password. Not both.") . "\";\n"
	 . "var ui_willdel = \"" . _("This will delete your personal dictionary file. Proceed?") . "\";\n"
	 . "//--></script>\n";
      /**
       * See if this happened in the pop-up window or when accessing
       * the SpellChecker options page.
       * This is a dirty solution, I agree. TODO: make this prettier.
       */
      global $SCRIPT_NAME;
      if (strstr($SCRIPT_NAME, "sqspell_options")){
	sqspell_makePage(_("Error Decrypting Dictionary"),
			  "decrypt_error.js", $msg);
      } else {
	sqspell_makeWindow(null, _("Error Decrypting Dictionary"),
			   "decrypt_error.js", $msg);
      }
      exit;
    } else {
      /**
       * OK! Phew. Set the encryption flag to true so we can later on
       * encrypt it again before saving to HDD.
       */
      $SQSPELL_CRYPTO=true;
    }
  } else {
    /**
     * No encryption is/was used. Set $SQSPELL_CRYPTO to false,
     * in case we have to save the dictionary later.
     */
    $SQSPELL_CRYPTO=false;
  }
  /**
   * Check if we need to upgrade the dictionary from version 0.2.x
   * This is going away soon.
   */
  if (strstr($words, "Dictionary v0.2")){
    $words=sqspell_upgradeWordsFile($words);
  }
  return $words;
}

/**
 * Writes user dictionary into the $username.words file, then changes mask
 * to 0600. If encryption is needed -- does that, too.
 *
 * @param  $words The contents of the ".words" file to write.
 * @return        void
 */
function sqspell_writeWords($words){
  global $SQSPELL_WORDS_FILE, $SQSPELL_CRYPTO;
  /**
   * if $words is empty, create a template entry by calling the
   * sqspell_makeDummy() function.
   */
  if (!$words){
    $words=sqspell_makeDummy();
  }
  if ($SQSPELL_CRYPTO){
    /**
     * User wants to encrypt the file. So be it.
     * Get the user's password to use as a key.
     */
    sqgetGlobalVar('key', $key, SQ_COOKIE);
    sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);

    $clear_key=OneTimePadDecrypt($key, $onetimepad);
    /**
     * Try encrypting it. If fails, scream bloody hell.
     */
    $save_words = sqspell_crypto("encrypt", $clear_key, $words);
    if ($save_words == 'PANIC'){
      /**
       * AAAAAAAAH! I'm not handling this yet, since obviously
       * the admin of the site forgot to compile the MCRYPT support in
       * when upgrading an existing PHP installation.
       * I will add a handler for this case later, when I can come up
       * with some work-around... Right now, do nothing. Let the Admin's
       * head hurt.. ;)))
       */
    }
  } else {
    $save_words = $words;
  }
  /**
   * Do the actual writing.
   */
  $fp=fopen($SQSPELL_WORDS_FILE, "w");
  fwrite($fp, $save_words);
  fclose($fp);
  chmod($SQSPELL_WORDS_FILE, 0600);
}

function sqspell_deleteWords(){
  /**
   * So I open the door to my enemies,
   * and I ask can we wipe the slate clean,
   * but they tell me to please go...
   * uhm... Well, this just erases the user dictionary file.
   */
  global $SQSPELL_WORDS_FILE;
  if (file_exists($SQSPELL_WORDS_FILE)){
    unlink($SQSPELL_WORDS_FILE);
  }
}
/**
 * Creates an empty user dictionary for the sake of saving prefs or
 * whatever.
 *
 * @return The template to use when storing the user dictionary.
 */
function sqspell_makeDummy(){
  global $SQSPELL_VERSION, $SQSPELL_APP_DEFAULT;
  $words = "# SquirrelSpell User Dictionary $SQSPELL_VERSION\n"
     . "# Last Revision: " . date('Y-m-d')
     . "\n# LANG: $SQSPELL_APP_DEFAULT\n# End\n";
  return $words;
}

/**
 * This function checks for security attacks. A $MOD variable is
 * provided in the QUERY_STRING and includes one of the files from the
 * modules directory ($MOD.mod). See if someone is trying to get out
 * of the modules directory by providing dots, unicode strings, or
 * slashes.
 *
 * @param  string $rMOD the name of the module requested to include.
 * @return void, since it bails out with an access error if needed.
 */
function sqspell_ckMOD($rMOD){
  if (strstr($rMOD, '.')
      || strstr($rMOD, '/')
      || strstr($rMOD, '%')
      || strstr($rMOD, "\\")){
    echo _("Invalid URL");
    exit;
  }
}

/**
 * SquirrelSpell version. Don't modify, since it identifies the format
 * of the user dictionary files and messing with this can do ugly
 * stuff. :)
 */
$SQSPELL_VERSION="v0.3.8";
