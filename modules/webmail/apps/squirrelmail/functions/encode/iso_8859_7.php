<?php

/**
 * iso-8859-7 encoding functions
 *
 * takes a string of unicode entities and converts it to a iso-8859-7 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_8859_7.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to iso-8859-7
 * @param string $string text with numeric unicode entities
 * @return string iso-8859-7 encoded text
 */
function charset_encode_iso_8859_7 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetoiso88597('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetoiso88597(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return iso-8859-7 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_iso_8859_7
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/iso_8859_7.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string iso-8859-7 character
 */
function unicodetoiso88597($var) {

    $iso88597chars=array('160' => "\xA0",
                         '163' => "\xA3",
                         '166' => "\xA6",
                         '167' => "\xA7",
                         '168' => "\xA8",
                         '169' => "\xA9",
                         '171' => "\xAB",
                         '172' => "\xAC",
                         '173' => "\xAD",
                         '176' => "\xB0",
                         '177' => "\xB1",
                         '178' => "\xB2",
                         '179' => "\xB3",
                         '183' => "\xB7",
                         '187' => "\xBB",
                         '189' => "\xBD",
                         '900' => "\xB4",
                         '901' => "\xB5",
                         '902' => "\xB6",
                         '904' => "\xB8",
                         '905' => "\xB9",
                         '906' => "\xBA",
                         '908' => "\xBC",
                         '910' => "\xBE",
                         '911' => "\xBF",
                         '912' => "\xC0",
                         '913' => "\xC1",
                         '914' => "\xC2",
                         '915' => "\xC3",
                         '916' => "\xC4",
                         '917' => "\xC5",
                         '918' => "\xC6",
                         '919' => "\xC7",
                         '920' => "\xC8",
                         '921' => "\xC9",
                         '922' => "\xCA",
                         '923' => "\xCB",
                         '924' => "\xCC",
                         '925' => "\xCD",
                         '926' => "\xCE",
                         '927' => "\xCF",
                         '928' => "\xD0",
                         '929' => "\xD1",
                         '931' => "\xD3",
                         '932' => "\xD4",
                         '933' => "\xD5",
                         '934' => "\xD6",
                         '935' => "\xD7",
                         '936' => "\xD8",
                         '937' => "\xD9",
                         '938' => "\xDA",
                         '939' => "\xDB",
                         '940' => "\xDC",
                         '941' => "\xDD",
                         '942' => "\xDE",
                         '943' => "\xDF",
                         '944' => "\xE0",
                         '945' => "\xE1",
                         '946' => "\xE2",
                         '947' => "\xE3",
                         '948' => "\xE4",
                         '949' => "\xE5",
                         '950' => "\xE6",
                         '951' => "\xE7",
                         '952' => "\xE8",
                         '953' => "\xE9",
                         '954' => "\xEA",
                         '955' => "\xEB",
                         '956' => "\xEC",
                         '957' => "\xED",
                         '958' => "\xEE",
                         '959' => "\xEF",
                         '960' => "\xF0",
                         '961' => "\xF1",
                         '962' => "\xF2",
                         '963' => "\xF3",
                         '964' => "\xF4",
                         '965' => "\xF5",
                         '966' => "\xF6",
                         '967' => "\xF7",
                         '968' => "\xF8",
                         '969' => "\xF9",
                         '970' => "\xFA",
                         '971' => "\xFB",
                         '972' => "\xFC",
                         '973' => "\xFD",
                         '974' => "\xFE",
                         '8213' => "\xAF",
                         '8216' => "\xA1",
                         '8217' => "\xA2");


    if (array_key_exists($var,$iso88597chars)) {
        $ret=$iso88597chars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
