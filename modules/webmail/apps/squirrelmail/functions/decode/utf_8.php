<?php

/**
 * functions/decode/utf-8.php - utf-8 decoding functions
 *
 * This file contains utf-8 decoding function that is needed to read
 * utf-8 encoded mails in non-utf-8 locale.
 *
 * Every decoded character consists of n bytes. First byte is octal
 * 300-375, other bytes - always octals 200-277.
 *<pre>
 * Ranges (first byte):
 *                oct     dec    hex
 * Two byte   - 300-337 192-223 C0-DF
 * Three byte - 340-357 224-239 E0-EF
 * Four byte  - 360-367 240-247 F0-F7
 * Five byte  - 370-373 248-251 F8-FB
 * Six byte   - 374-375 252-253 FC-FD
 *
 * \a\b characters are decoded to html code calculated with formula:
 *  octdec(a-300)*64 + octdec(b-200)
 *
 * \a\b\c characters are decoded to html code calculated with formula:
 *  octdec(a-340)*64^2 + octdec(b-200)*64 + octdec(c-200)
 *
 * \a\b\c\d characters are decoded to html code calculated with formula:
 *  octdec(a-360)*64^3 + octdec(b-200)*64^2 +
 *  + octdec(c-200)*64 + octdec(d-200)
 *
 * \a\b\c\d\e characters are decoded to html code calculated with formula:
 *  octdec(a-370)*64^4 + octdec(b-200)*64^3 +
 *  + octdec(c-200)*64^2 + octdec(d-200)*64 + octdec(e-200)
 *
 * \a\b\c\d\e\f characters are decoded to html code calculated with formula:
 *  octdec(a-374)*64^5 + octdec(b-200)*64^4 + octdec(c-200)*64^3 +
 *  + octdec(d-200)*64^2 + octdec(e-200)*64 + octdec(f-200)
 *</pre>
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: utf_8.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode utf-8 strings
 * @param string $string Encoded string
 * @return string Decoded string
 */
function charset_decode_utf_8 ($string) {
    global $squirrelmail_language;

    // Japanese translation uses mbstring function to read utf-8
    if ($squirrelmail_language == 'ja_JP')
        return $string;

    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'utf-8'))
        return $string;

    // decode six byte unicode characters
    /* (i think currently there is no such symbol)
    $string = preg_replace("/([\374-\375])([\200-\277])([\200-\277])([\200-\277])([\200-\277])([\200-\277])/e",
    "'&#'.((ord('\\1')-252)*1073741824+(ord('\\2')-200)*16777216+(ord('\\3')-200)*262144+(ord('\\4')-128)*4096+(ord('\\5')-128)*64+(ord('\\6')-128)).';'",
    $string);
    */

    // decode five byte unicode characters
    /* (i think currently there is no such symbol)
    $string = preg_replace("/([\370-\373])([\200-\277])([\200-\277])([\200-\277])([\200-\277])/e",
    "'&#'.((ord('\\1')-248)*16777216+(ord('\\2')-200)*262144+(ord('\\3')-128)*4096+(ord('\\4')-128)*64+(ord('\\5')-128)).';'",
    $string);
    */

    // decode four byte unicode characters
    $string = preg_replace("/([\360-\367])([\200-\277])([\200-\277])([\200-\277])/e",
    "'&#'.((ord('\\1')-240)*262144+(ord('\\2')-128)*4096+(ord('\\3')-128)*64+(ord('\\4')-128)).';'",
    $string);

    // decode three byte unicode characters
    $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
    "'&#'.((ord('\\1')-224)*4096+(ord('\\2')-128)*64+(ord('\\3')-128)).';'",
    $string);

    // decode two byte unicode characters
    $string = preg_replace("/([\300-\337])([\200-\277])/e",
    "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
    $string);

    // remove broken unicode
    $string = preg_replace("/[\200-\237]|\240|[\241-\377]/",'?',$string);

    return $string;
}
