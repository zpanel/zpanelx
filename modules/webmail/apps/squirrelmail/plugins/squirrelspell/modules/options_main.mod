<?php
/**
 * options_main.mod
 * ----------------
 * Squirrelspell module
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Default page called when accessing SquirrelSpell's options.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: options_main.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

global $SQSPELL_APP;
$msg = '<p>'
  . _("Please choose which options you wish to set up:")
  . '</p>'
  . '<ul>'
  . '<li><a href="sqspell_options.php?MOD=edit_dic">'
  . _("Edit your personal dictionary") . '</a></li>';
/**
 * See if more than one dictionary is defined system-wide.
 * If so, let the user choose his preferred ones.
 */
if (sizeof($SQSPELL_APP)>1) {
  $msg .= '<li><a href="sqspell_options.php?MOD=lang_setup">'
    . _("Set up international dictionaries")
    . "</a></li>\n";
}
/**
 * See if MCRYPT is available.
 * If so, let the user choose whether s/he wants to encrypt the
 * personal dictionary file.
 */
if (function_exists("mcrypt_generic")) {
  $msg .= '<li><a href="sqspell_options.php?MOD=enc_setup">'
    . _("Encrypt or decrypt your personal dictionary")
    . "</a></li>\n";
} else {
  $msg .= '<li>'
    . _("Personal dictionary encryption options are not available") 
    . '</li>';
}
$msg .= "</ul>\n";
sqspell_makePage( _("SquirrelSpell Options Menu"), null, $msg);

/**
 * For Emacs weenies:
 * Local variables:
 * mode: php
 * End:
 * vim: syntax=php
 */
?>