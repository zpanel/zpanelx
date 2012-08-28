<?php

/**
 * cp1251 encoding functions
 *
 * takes a string of unicode entities and converts it to a cp1251 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp1251.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to cp1251
 * @param string $string text with numeric unicode entities
 * @return string cp1251 encoded text
 */
function charset_encode_cp1251 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetocp1251('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetocp1251(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return cp1251 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_cp1251
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/cp1251.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string cp1251 character
 */
function unicodetocp1251($var) {

    $cp1251chars=array('160' => "\xA0",
                       '164' => "\xA4",
                       '166' => "\xA6",
                       '167' => "\xA7",
                       '169' => "\xA9",
                       '171' => "\xAB",
                       '172' => "\xAC",
                       '173' => "\xAD",
                       '174' => "\xAE",
                       '176' => "\xB0",
                       '177' => "\xB1",
                       '181' => "\xB5",
                       '182' => "\xB6",
                       '183' => "\xB7",
                       '187' => "\xBB",
                       '1025' => "\xA8",
                       '1026' => "\x80",
                       '1027' => "\x81",
                       '1028' => "\xAA",
                       '1029' => "\xBD",
                       '1030' => "\xB2",
                       '1031' => "\xAF",
                       '1032' => "\xA3",
                       '1033' => "\x8A",
                       '1034' => "\x8C",
                       '1035' => "\x8E",
                       '1036' => "\x8D",
                       '1038' => "\xA1",
                       '1039' => "\x8F",
                       '1040' => "\xC0",
                       '1041' => "\xC1",
                       '1042' => "\xC2",
                       '1043' => "\xC3",
                       '1044' => "\xC4",
                       '1045' => "\xC5",
                       '1046' => "\xC6",
                       '1047' => "\xC7",
                       '1048' => "\xC8",
                       '1049' => "\xC9",
                       '1050' => "\xCA",
                       '1051' => "\xCB",
                       '1052' => "\xCC",
                       '1053' => "\xCD",
                       '1054' => "\xCE",
                       '1055' => "\xCF",
                       '1056' => "\xD0",
                       '1057' => "\xD1",
                       '1058' => "\xD2",
                       '1059' => "\xD3",
                       '1060' => "\xD4",
                       '1061' => "\xD5",
                       '1062' => "\xD6",
                       '1063' => "\xD7",
                       '1064' => "\xD8",
                       '1065' => "\xD9",
                       '1066' => "\xDA",
                       '1067' => "\xDB",
                       '1068' => "\xDC",
                       '1069' => "\xDD",
                       '1070' => "\xDE",
                       '1071' => "\xDF",
                       '1072' => "\xE0",
                       '1073' => "\xE1",
                       '1074' => "\xE2",
                       '1075' => "\xE3",
                       '1076' => "\xE4",
                       '1077' => "\xE5",
                       '1078' => "\xE6",
                       '1079' => "\xE7",
                       '1080' => "\xE8",
                       '1081' => "\xE9",
                       '1082' => "\xEA",
                       '1083' => "\xEB",
                       '1084' => "\xEC",
                       '1085' => "\xED",
                       '1086' => "\xEE",
                       '1087' => "\xEF",
                       '1088' => "\xF0",
                       '1089' => "\xF1",
                       '1090' => "\xF2",
                       '1091' => "\xF3",
                       '1092' => "\xF4",
                       '1093' => "\xF5",
                       '1094' => "\xF6",
                       '1095' => "\xF7",
                       '1096' => "\xF8",
                       '1097' => "\xF9",
                       '1098' => "\xFA",
                       '1099' => "\xFB",
                       '1100' => "\xFC",
                       '1101' => "\xFD",
                       '1102' => "\xFE",
                       '1103' => "\xFF",
                       '1105' => "\xB8",
                       '1106' => "\x90",
                       '1107' => "\x83",
                       '1108' => "\xBA",
                       '1109' => "\xBE",
                       '1110' => "\xB3",
                       '1111' => "\xBF",
                       '1112' => "\xBC",
                       '1113' => "\x9A",
                       '1114' => "\x9C",
                       '1115' => "\x9E",
                       '1116' => "\x9D",
                       '1118' => "\xA2",
                       '1119' => "\x9F",
                       '1168' => "\xA5",
                       '1169' => "\xB4",
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
                       '8364' => "\x88",
                       '8470' => "\xB9",
                       '8482' => "\x99");

    if (array_key_exists($var,$cp1251chars)) {
        $ret=$cp1251chars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
