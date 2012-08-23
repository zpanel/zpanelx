<?php

/**
 * cp1256 encoding functions
 *
 * takes a string of unicode entities and converts it to a cp1256 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp1256.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to cp1256
 * @param string $string text with numeric unicode entities
 * @return string cp1256 encoded text
 */
function charset_encode_cp1256 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetocp1256('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetocp1256(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return cp1256 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_cp1256
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/cp1256.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string cp1256 character
 */
function unicodetocp1256($var) {

    $cp1256chars=array('160' => "\xA0",
                       '162' => "\xA2",
                       '163' => "\xA3",
                       '164' => "\xA4",
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
                       '215' => "\xD7",
                       '224' => "\xE0",
                       '226' => "\xE2",
                       '231' => "\xE7",
                       '232' => "\xE8",
                       '233' => "\xE9",
                       '234' => "\xEA",
                       '235' => "\xEB",
                       '238' => "\xEE",
                       '239' => "\xEF",
                       '244' => "\xF4",
                       '247' => "\xF7",
                       '249' => "\xF9",
                       '251' => "\xFB",
                       '252' => "\xFC",
                       '338' => "\x8C",
                       '339' => "\x9C",
                       '402' => "\x83",
                       '710' => "\x88",
                       '1548' => "\xA1",
                       '1563' => "\xBA",
                       '1567' => "\xBF",
                       '1569' => "\xC1",
                       '1570' => "\xC2",
                       '1571' => "\xC3",
                       '1572' => "\xC4",
                       '1573' => "\xC5",
                       '1574' => "\xC6",
                       '1575' => "\xC7",
                       '1576' => "\xC8",
                       '1577' => "\xC9",
                       '1578' => "\xCA",
                       '1579' => "\xCB",
                       '1580' => "\xCC",
                       '1581' => "\xCD",
                       '1582' => "\xCE",
                       '1583' => "\xCF",
                       '1584' => "\xD0",
                       '1585' => "\xD1",
                       '1586' => "\xD2",
                       '1587' => "\xD3",
                       '1588' => "\xD4",
                       '1589' => "\xD5",
                       '1590' => "\xD6",
                       '1591' => "\xD8",
                       '1592' => "\xD9",
                       '1593' => "\xDA",
                       '1594' => "\xDB",
                       '1600' => "\xDC",
                       '1601' => "\xDD",
                       '1602' => "\xDE",
                       '1603' => "\xDF",
                       '1604' => "\xE1",
                       '1605' => "\xE3",
                       '1606' => "\xE4",
                       '1607' => "\xE5",
                       '1608' => "\xE6",
                       '1609' => "\xEC",
                       '1610' => "\xED",
                       '1611' => "\xF0",
                       '1612' => "\xF1",
                       '1613' => "\xF2",
                       '1614' => "\xF3",
                       '1615' => "\xF5",
                       '1616' => "\xF6",
                       '1617' => "\xF8",
                       '1618' => "\xFA",
                       '1657' => "\x8A",
                       '1662' => "\x81",
                       '1670' => "\x8D",
                       '1672' => "\x8F",
                       '1681' => "\x9A",
                       '1688' => "\x8E",
                       '1705' => "\x98",
                       '1711' => "\x90",
                       '1722' => "\x9F",
                       '1726' => "\xAA",
                       '1729' => "\xC0",
                       '1746' => "\xFF",
                       '8204' => "\x9D",
                       '8205' => "\x9E",
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
                       '8364' => "\x80",
                       '8482' => "\x99");

    if (array_key_exists($var,$cp1256chars)) {
        $ret=$cp1256chars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
