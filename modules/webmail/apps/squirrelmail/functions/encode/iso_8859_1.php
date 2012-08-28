<?php

/**
 * iso-8859-1 encoding functions
 *
 * takes a string of unicode entities and converts it to a iso-8859-1 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_8859_1.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to iso-8859-1
 * @param string $string text with numeric unicode entities
 * @return string iso-8859-1 encoded text
 */
function charset_encode_iso_8859_1 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetoiso88591('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetoiso88591(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return iso-8859-1 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_iso_8859_1
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/iso_8859_1.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string iso-8859-1 character
 */
function unicodetoiso88591($var) {

    if ($var < 256) {
        $ret = chr ($var);
    } else {
        $ret='?';
    }
    return $ret;
}
