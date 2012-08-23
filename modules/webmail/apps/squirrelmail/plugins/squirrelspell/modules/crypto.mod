<?php
/**
 * crypto.mod
 * ---------------
 * Squirrelspell module
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This module handles the encryption/decryption of the user dictionary
 * if the user so chooses from the options page.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: crypto.mod 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

/**
 * Declaring globals for E_ALL
 */
global $SQSPELL_CRYPTO;

switch ($_POST['action']){
    case 'encrypt':
        /**
         * Let's encrypt the file and save it in an encrypted format.
         */
        $words=sqspell_getWords();
        /**
         * Flip the flag so the sqspell_writeWords function knows to encrypt
         * the message before writing it to the disk.
         */
        $SQSPELL_CRYPTO=true;
        /**
         * Call the function that does the actual encryption_decryption.
         */
        sqspell_writeWords($words);
        $msg='<p>'
            . _("Your personal dictionary has been encrypted and is now stored in an encrypted format.")
            . '</p>';
    break;
    case 'decrypt':
        /**
         * Let's decrypt the file and save it as plain text.
         */
        $words=sqspell_getWords();
        /**
         * Flip the flag and tell the sqspell_writeWords() function that we
         * want to save it plaintext.
         */
        $SQSPELL_CRYPTO=false;
        sqspell_writeWords($words);
        $msg='<p>'
            . _("Your personal dictionary has been decrypted and is now stored as plain text.")
            . '</p>';
    break;
    case '':
        /**
         * Wait, this shouldn't happen! :)
         */
        $msg = '<p>'._("No action requested.").'</p>';
    break;
}
sqspell_makePage( _("Personal Dictionary Crypto Settings"), null, $msg);

/**
 * For Emacs weenies:
 * Local variables:
 * mode: php
 * End:
 * vim: syntax=php
 */

?>