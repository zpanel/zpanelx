<?php

/**
 * iso-8859-9 encoding functions
 *
 * takes a string of unicode entities and converts it to a iso-8859-9 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_8859_9.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to iso-8859-9
 * @param string $string text with numeric unicode entities
 * @return string iso-8859-9 encoded text
 */
function charset_encode_iso_8859_9 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetoiso88599('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetoiso88599(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return iso-8859-9 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_iso_8859_9
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/iso_8859_9.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string iso-8859-9 character
 */
function unicodetoiso88599($var) {

    $iso88599chars=array('160' => "\xA0",
                         '161' => "\xA1",
                         '162' => "\xA2",
                         '163' => "\xA3",
                         '164' => "\xA4",
                         '165' => "\xA5",
                         '166' => "\xA6",
                         '167' => "\xA7",
                         '168' => "\xA8",
                         '169' => "\xA9",
                         '170' => "\xAA",
                         '171' => "\xAB",
                         '172' => "\xAC",
                         '173' => "\xAD",
                         '174' => "\xAE",
                         '175' => "\xAF",
                         '176' => "\xB0",
                         '177' => "\xB1",
                         '178' => "\xB2",
                         '179' => "\xB3",
                         '180' => "\xB4",
                         '181' => "\xB5",
                         '182' => "\xB6",
                         '183' => "\xB7",
                         '184' => "\xB8",
                         '185' => "\xB9",
                         '186' => "\xBA",
                         '187' => "\xBB",
                         '188' => "\xBC",
                         '189' => "\xBD",
                         '190' => "\xBE",
                         '191' => "\xBF",
                         '192' => "\xC0",
                         '193' => "\xC1",
                         '194' => "\xC2",
                         '195' => "\xC3",
                         '196' => "\xC4",
                         '197' => "\xC5",
                         '198' => "\xC6",
                         '199' => "\xC7",
                         '200' => "\xC8",
                         '201' => "\xC9",
                         '202' => "\xCA",
                         '203' => "\xCB",
                         '204' => "\xCC",
                         '205' => "\xCD",
                         '206' => "\xCE",
                         '207' => "\xCF",
                         '209' => "\xD1",
                         '210' => "\xD2",
                         '211' => "\xD3",
                         '212' => "\xD4",
                         '213' => "\xD5",
                         '214' => "\xD6",
                         '215' => "\xD7",
                         '216' => "\xD8",
                         '217' => "\xD9",
                         '218' => "\xDA",
                         '219' => "\xDB",
                         '220' => "\xDC",
                         '223' => "\xDF",
                         '224' => "\xE0",
                         '225' => "\xE1",
                         '226' => "\xE2",
                         '227' => "\xE3",
                         '228' => "\xE4",
                         '229' => "\xE5",
                         '230' => "\xE6",
                         '231' => "\xE7",
                         '232' => "\xE8",
                         '233' => "\xE9",
                         '234' => "\xEA",
                         '235' => "\xEB",
                         '236' => "\xEC",
                         '237' => "\xED",
                         '238' => "\xEE",
                         '239' => "\xEF",
                         '241' => "\xF1",
                         '242' => "\xF2",
                         '243' => "\xF3",
                         '244' => "\xF4",
                         '245' => "\xF5",
                         '246' => "\xF6",
                         '247' => "\xF7",
                         '248' => "\xF8",
                         '249' => "\xF9",
                         '250' => "\xFA",
                         '251' => "\xFB",
                         '252' => "\xFC",
                         '255' => "\xFF",
                         '286' => "\xD0",
                         '287' => "\xF0",
                         '304' => "\xDD",
                         '305' => "\xFD",
                         '350' => "\xDE",
                         '351' => "\xFE");


    if (array_key_exists($var,$iso88599chars)) {
        $ret=$iso88599chars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
