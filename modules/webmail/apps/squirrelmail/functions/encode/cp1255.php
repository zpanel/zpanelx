<?php

/**
 * cp1255 encoding functions
 *
 * takes a string of unicode entities and converts it to a cp1255 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp1255.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to cp1255
 * @param string $string text with numeric unicode entities
 * @return string cp1255 encoded text
 */
function charset_encode_cp1255 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetocp1255('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetocp1255(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return cp1255 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_cp1255
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/cp1255.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string cp1255 character
 */
function unicodetocp1255($var) {

    $cp1255chars=array('160' => "\xA0",
                       '161' => "\xA1",
                       '162' => "\xA2",
                       '163' => "\xA3",
                       '165' => "\xA5",
                       '166' => "\xA6",
                       '167' => "\xA7",
                       '168' => "\xA8",
                       '169' => "\xA9",
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
                       '187' => "\xBB",
                       '188' => "\xBC",
                       '189' => "\xBD",
                       '190' => "\xBE",
                       '191' => "\xBF",
                       '215' => "\xAA",
                       '247' => "\xBA",
                       '402' => "\x83",
                       '710' => "\x88",
                       '732' => "\x98",
                       '1456' => "\xC0",
                       '1457' => "\xC1",
                       '1458' => "\xC2",
                       '1459' => "\xC3",
                       '1460' => "\xC4",
                       '1461' => "\xC5",
                       '1462' => "\xC6",
                       '1463' => "\xC7",
                       '1464' => "\xC8",
                       '1465' => "\xC9",
                       '1467' => "\xCB",
                       '1468' => "\xCC",
                       '1469' => "\xCD",
                       '1470' => "\xCE",
                       '1471' => "\xCF",
                       '1472' => "\xD0",
                       '1473' => "\xD1",
                       '1474' => "\xD2",
                       '1475' => "\xD3",
                       '1488' => "\xE0",
                       '1489' => "\xE1",
                       '1490' => "\xE2",
                       '1491' => "\xE3",
                       '1492' => "\xE4",
                       '1493' => "\xE5",
                       '1494' => "\xE6",
                       '1495' => "\xE7",
                       '1496' => "\xE8",
                       '1497' => "\xE9",
                       '1498' => "\xEA",
                       '1499' => "\xEB",
                       '1500' => "\xEC",
                       '1501' => "\xED",
                       '1502' => "\xEE",
                       '1503' => "\xEF",
                       '1504' => "\xF0",
                       '1505' => "\xF1",
                       '1506' => "\xF2",
                       '1507' => "\xF3",
                       '1508' => "\xF4",
                       '1509' => "\xF5",
                       '1510' => "\xF6",
                       '1511' => "\xF7",
                       '1512' => "\xF8",
                       '1513' => "\xF9",
                       '1514' => "\xFA",
                       '1520' => "\xD4",
                       '1521' => "\xD5",
                       '1522' => "\xD6",
                       '1523' => "\xD7",
                       '1524' => "\xD8",
                       '8206' => "\xFD",
                       '8207' => "\xFE",
                       '8211' => "\x96",
                       '8212' => "\x97",
                       '8216' => "\x91",
                       '8217' => "\x92",
                       '8218' => "\x82",
                       '8220' => "\x93",
                       '8221' => "\x94",
                       '8222' => "\x84",
                       '8224' => "\x86",
                       '8225' => "\x87",
                       '8226' => "\x95",
                       '8230' => "\x85",
                       '8240' => "\x89",
                       '8249' => "\x8B",
                       '8250' => "\x9B",
                       '8362' => "\xA4",
                       '8364' => "\x80",
                       '8482' => "\x99");

    if (array_key_exists($var,$cp1255chars)) {
        $ret=$cp1255chars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
