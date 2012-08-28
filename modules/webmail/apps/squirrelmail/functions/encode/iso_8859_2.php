<?php

/**
 * iso-8859-2 encoding functions
 *
 * takes a string of unicode entities and converts it to a iso-8859-2 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_8859_2.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to iso-8859-2
 * @param string $string text with numeric unicode entities
 * @return string iso-8859-2 encoded text
 */
function charset_encode_iso_8859_2 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetoiso88592('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetoiso88592(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return iso-8859-2 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_iso_8859_2
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/iso_8859_2.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string iso-8859-2 character
 */
function unicodetoiso88592($var) {

    $iso88592chars=array('160' => "\xA0",
                        '164' => "\xA4",
                        '167' => "\xA7",
                        '168' => "\xA8",
                        '173' => "\xAD",
                        '176' => "\xB0",
                        '180' => "\xB4",
                        '184' => "\xB8",
                        '193' => "\xC1",
                        '194' => "\xC2",
                        '196' => "\xC4",
                        '199' => "\xC7",
                        '201' => "\xC9",
                        '203' => "\xCB",
                        '205' => "\xCD",
                        '206' => "\xCE",
                        '211' => "\xD3",
                        '212' => "\xD4",
                        '214' => "\xD6",
                        '215' => "\xD7",
                        '218' => "\xDA",
                        '220' => "\xDC",
                        '221' => "\xDD",
                        '223' => "\xDF",
                        '225' => "\xE1",
                        '226' => "\xE2",
                        '228' => "\xE4",
                        '231' => "\xE7",
                        '233' => "\xE9",
                        '235' => "\xEB",
                        '237' => "\xED",
                        '238' => "\xEE",
                        '243' => "\xF3",
                        '244' => "\xF4",
                        '246' => "\xF6",
                        '247' => "\xF7",
                        '250' => "\xFA",
                        '252' => "\xFC",
                        '253' => "\xFD",
                        '258' => "\xC3",
                        '259' => "\xE3",
                        '260' => "\xA1",
                        '261' => "\xB1",
                        '262' => "\xC6",
                        '263' => "\xE6",
                        '268' => "\xC8",
                        '269' => "\xE8",
                        '270' => "\xCF",
                        '271' => "\xEF",
                        '272' => "\xD0",
                        '273' => "\xF0",
                        '280' => "\xCA",
                        '281' => "\xEA",
                        '282' => "\xCC",
                        '283' => "\xEC",
                        '313' => "\xC5",
                        '314' => "\xE5",
                        '317' => "\xA5",
                        '318' => "\xB5",
                        '321' => "\xA3",
                        '322' => "\xB3",
                        '323' => "\xD1",
                        '324' => "\xF1",
                        '327' => "\xD2",
                        '328' => "\xF2",
                        '336' => "\xD5",
                        '337' => "\xF5",
                        '340' => "\xC0",
                        '341' => "\xE0",
                        '344' => "\xD8",
                        '345' => "\xF8",
                        '346' => "\xA6",
                        '347' => "\xB6",
                        '350' => "\xAA",
                        '351' => "\xBA",
                        '352' => "\xA9",
                        '353' => "\xB9",
                        '354' => "\xDE",
                        '355' => "\xFE",
                        '356' => "\xAB",
                        '357' => "\xBB",
                        '366' => "\xD9",
                        '367' => "\xF9",
                        '368' => "\xDB",
                        '369' => "\xFB",
                        '377' => "\xAC",
                        '378' => "\xBC",
                        '379' => "\xAF",
                        '380' => "\xBF",
                        '381' => "\xAE",
                        '382' => "\xBE",
                        '711' => "\xB7",
                        '728' => "\xA2",
                        '729' => "\xFF",
                        '731' => "\xB2",
                        '733' => "\xBD");


    if (array_key_exists($var,$iso88592chars)) {
        $ret=$iso88592chars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
