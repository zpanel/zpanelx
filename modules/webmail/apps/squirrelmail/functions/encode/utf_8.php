<?php

/**
 * utf-8 encoding functions
 *
 * takes a string of unicode entities and converts it to a utf-8 encoded string
 * each unicode entitiy has the form &#nnn(nn); n={0..9} and can be displayed by utf-8 supporting
 * browsers. Ascii will not be modified.
 *
 * Original code is taken from www.php.net manual comments
 * Original author: ronen at greyzone dot com
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: utf_8.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to utf-8
 * @param string $string text with numeric unicode entities
 * @return string utf-8 encoded text
 */
function charset_encode_utf_8 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetoutf8('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetoutf8(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return utf8 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_utf_8
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/utf_8.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string utf8 character
 */
function unicodetoutf8($var) {

    if ($var < 128) {
        $ret = chr ($var);
    } else if ($var < 2048) {
        // Two byte utf-8
        $binVal = str_pad (decbin ($var), 11, "0", STR_PAD_LEFT);
        $binPart1 = substr ($binVal, 0, 5);
        $binPart2 = substr ($binVal, 5);

        $char1 = chr (192 + bindec ($binPart1));
        $char2 = chr (128 + bindec ($binPart2));
        $ret = $char1 . $char2;
    } else if ($var < 65536) {
        // Three byte utf-8
        $binVal = str_pad (decbin ($var), 16, "0", STR_PAD_LEFT);
        $binPart1 = substr ($binVal, 0, 4);
        $binPart2 = substr ($binVal, 4, 6);
        $binPart3 = substr ($binVal, 10);

        $char1 = chr (224 + bindec ($binPart1));
        $char2 = chr (128 + bindec ($binPart2));
        $char3 = chr (128 + bindec ($binPart3));
        $ret = $char1 . $char2 . $char3;
    } else if ($var < 2097152) {
        // Four byte utf-8
        $binVal = str_pad (decbin ($var), 21, "0", STR_PAD_LEFT);
        $binPart1 = substr ($binVal, 0, 3);
        $binPart2 = substr ($binVal, 3, 6);
        $binPart3 = substr ($binVal, 9, 6);
        $binPart4 = substr ($binVal, 15);

        $char1 = chr (240 + bindec ($binPart1));
        $char2 = chr (128 + bindec ($binPart2));
        $char3 = chr (128 + bindec ($binPart3));
        $char4 = chr (128 + bindec ($binPart4));
        $ret = $char1 . $char2 . $char3 . $char4;
    } else if ($var < 67108864) {
        // Five byte utf-8
        $binVal = str_pad (decbin ($var), 26, "0", STR_PAD_LEFT);
        $binPart1 = substr ($binVal, 0, 2);
        $binPart2 = substr ($binVal, 2, 6);
        $binPart3 = substr ($binVal, 8, 6);
        $binPart4 = substr ($binVal, 14,6);
        $binPart5 = substr ($binVal, 20);

        $char1 = chr (248 + bindec ($binPart1));
        $char2 = chr (128 + bindec ($binPart2));
        $char3 = chr (128 + bindec ($binPart3));
        $char4 = chr (128 + bindec ($binPart4));
        $char5 = chr (128 + bindec ($binPart5));
        $ret = $char1 . $char2 . $char3 . $char4 . $char5;
    } else if ($var < 2147483648) {
        // Six byte utf-8
        $binVal = str_pad (decbin ($var), 31, "0", STR_PAD_LEFT);
        $binPart1 = substr ($binVal, 0, 1);
        $binPart2 = substr ($binVal, 1, 6);
        $binPart3 = substr ($binVal, 7, 6);
        $binPart4 = substr ($binVal, 13,6);
        $binPart5 = substr ($binVal, 19,6);
        $binPart6 = substr ($binVal, 25);

        $char1 = chr (252 + bindec ($binPart1));
        $char2 = chr (128 + bindec ($binPart2));
        $char3 = chr (128 + bindec ($binPart3));
        $char4 = chr (128 + bindec ($binPart4));
        $char5 = chr (128 + bindec ($binPart5));
        $char6 = chr (128 + bindec ($binPart6));
        $ret = $char1 . $char2 . $char3 . $char4 . $char5 . $char6;
    } else {
        // there is no such symbol in utf-8
        $ret='?';
    }
    return $ret;
}
