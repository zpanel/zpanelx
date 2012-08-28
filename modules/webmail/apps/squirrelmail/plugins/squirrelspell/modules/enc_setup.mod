<?php
/**
 * enc_setup.mod
 * --------------
 * Squirrelspell module
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This module shows the user a nice invitation to encrypt or decypt
 * his/her personal dictionary and explains the caveats of such a decision.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: enc_setup.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

global $SQSPELL_CRYPTO;

/**
 * Set up some i18n'able wrappers for javascript.
 */
$msg = '<script type="text/javascript"><!--'."\n"
    . 'var ui_makesel = "' . _("Please make your selection first.") . "\";\n"
    . 'var ui_encrypt = "'
    . _("This will encrypt your personal dictionary and store it in an encrypted format. Proceed?")
    . "\";\n"
    . 'var ui_decrypt = "'
    . _("This will decrypt your personal dictionary and store it in a plain text format. Proceed?")
    . "\";\n"
    . "//-->\n</script>";

$words=sqspell_getWords();
/**
 * When getting the user dictionary, the SQSPELL_CRYPTO flag will be
 * set to "true" if the dictionary is encrypted, or "false" if it is
 * in plain text.
 */
if ($SQSPELL_CRYPTO){
    /**
     * Current format is encrypted.
     * Unfortunately, the following text needs to be all on one line,
     * unless someone fixes xgettext. ;(
     */
    $msg .=
        '<p>'
        . _("Your personal dictionary is currently encrypted.")
        . '</p><p>'
        . _("This helps protect your privacy in case the web-mail system gets compromized and your personal dictionary ends up stolen. It is currently encrypted with the password you use to access your mailbox, making it hard for anyone to see what is stored in your personal dictionary.")
        . '</p><p>'
        . '<strong>' . _("ATTENTION:") . '</strong><br />'
        . _("If you forget your password, your personal dictionary will become unaccessible, since it can no longer be decrypted. If you change your mailbox password, SquirrelSpell will recognize it and prompt you for your old password in order to re-encrypt the dictionary with a new key.")
        . '</p>'
        . '<form method="post" onsubmit="return checkMe()">'
        . '<input type="hidden" name="MOD" value="crypto" />'
        . '<p align="center"><input type="checkbox" name="action" '
        . 'value="decrypt" /> '
        . _("Please decrypt my personal dictionary and store it in a clear-text format." )
        . '</p>'
        . '<p align="center"><input type="submit" value=" '
        . _("Change crypto settings")
        . ' " /></p>'
        . '</form>';
} else {
    /**
     * Current format is clear text.
     * Unfortunately, the following text needs to be all on one line,
     * unless someone fixes xgettext. ;(
     */
    $msg .=
        '<p>'
        . _("Your personal dictionary is currently not encrypted.")
        . '</p><p>'
        . _("You may wish to encrypt your personal dictionary to protect your privacy in case the webmail system gets compromized and your personal dictionary file gets stolen. When encrypted, the file's contents look garbled and are hard to decrypt without knowing the correct key (which is your mailbox password).")
        . '</p><p>'
        . '<strong>' . _("ATTENTION:") . '</strong><br />'
        . _("If you decide to encrypt your personal dictionary, you must remember that it gets &quot;hashed&quot; with your mailbox password. If you forget your mailbox password and the administrator changes it to a new value, your personal dictionary will become useless and will have to be created anew. However, if you or your system administrator change your mailbox password but you still have the old password at hand, you will be able to enter the old key to re-encrypt the dictionary with the new value.")
        . '</p>'
        . '<form method="post" onsubmit="return checkMe()">'
        . '<input type="hidden" name="MOD" value="crypto" />'
        . '<p align="center"><input type="checkbox" name="action" '
        . 'value="encrypt" /> '
        . _("Please encrypt my personal dictionary and store it in an encrypted format.")
        . '</p>'
        . '<p align="center"><input type="submit" value=" '
        . _("Change crypto settings") . ' " /></p>'
        . '</form>';
}
sqspell_makePage(_("Personal Dictionary Crypto Settings"),
        "crypto_settings.js", $msg);

/**
 * For Emacs weenies:
 * Local variables:
 * mode: php
 * End:
 * vim: syntax=php
 */

?>