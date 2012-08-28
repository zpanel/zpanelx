<?php

/**
 * koi8-r encoding functions
 *
 * takes a string of unicode entities and converts it to a koi8-r encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: koi8_r.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to koi8-r
 * @param string $string text with numeric unicode entities
 * @return string koi8-r encoded text
 */
function charset_encode_koi8_r ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetokoi8r('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetokoi8r(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return koi8-r symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_koi8_r
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/koi8_r.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string koi8-r character
 */
function unicodetokoi8r($var) {

    $koi8rchars=array('160' => "\x9A",
                      '169' => "\xBF",
                      '176' => "\x9C",
                      '178' => "\x9D",
                      '183' => "\x9E",
                      '247' => "\x9F",
                      '1025' => "\xB3",
                      '1040' => "\xE1",
                      '1041' => "\xE2",
                      '1042' => "\xF7",
                      '1043' => "\xE7",
                      '1044' => "\xE4",
                      '1045' => "\xE5",
                      '1046' => "\xF6",
                      '1047' => "\xFA",
                      '1048' => "\xE9",
                      '1049' => "\xEA",
                      '1050' => "\xEB",
                      '1051' => "\xEC",
                      '1052' => "\xED",
                      '1053' => "\xEE",
                      '1054' => "\xEF",
                      '1055' => "\xF0",
                      '1056' => "\xF2",
                      '1057' => "\xF3",
                      '1058' => "\xF4",
                      '1059' => "\xF5",
                      '1060' => "\xE6",
                      '1061' => "\xE8",
                      '1062' => "\xE3",
                      '1063' => "\xFE",
                      '1064' => "\xFB",
                      '1065' => "\xFD",
                      '1066' => "\xFF",
                      '1067' => "\xF9",
                      '1068' => "\xF8",
                      '1069' => "\xFC",
                      '1070' => "\xE0",
                      '1071' => "\xF1",
                      '1072' => "\xC1",
                      '1073' => "\xC2",
                      '1074' => "\xD7",
                      '1075' => "\xC7",
                      '1076' => "\xC4",
                      '1077' => "\xC5",
                      '1078' => "\xD6",
                      '1079' => "\xDA",
                      '1080' => "\xC9",
                      '1081' => "\xCA",
                      '1082' => "\xCB",
                      '1083' => "\xCC",
                      '1084' => "\xCD",
                      '1085' => "\xCE",
                      '1086' => "\xCF",
                      '1087' => "\xD0",
                      '1088' => "\xD2",
                      '1089' => "\xD3",
                      '1090' => "\xD4",
                      '1091' => "\xD5",
                      '1092' => "\xC6",
                      '1093' => "\xC8",
                      '1094' => "\xC3",
                      '1095' => "\xDE",
                      '1096' => "\xDB",
                      '1097' => "\xDD",
                      '1098' => "\xDF",
                      '1099' => "\xD9",
                      '1100' => "\xD8",
                      '1101' => "\xDC",
                      '1102' => "\xC0",
                      '1103' => "\xD1",
                      '1105' => "\xA3",
                      '8729' => "\x95",
                      '8730' => "\x96",
                      '8776' => "\x97",
                      '8804' => "\x98",
                      '8805' => "\x99",
                      '8992' => "\x93",
                      '8993' => "\x9B",
                      '9472' => "\x80",
                      '9474' => "\x81",
                      '9484' => "\x82",
                      '9488' => "\x83",
                      '9492' => "\x84",
                      '9496' => "\x85",
                      '9500' => "\x86",
                      '9508' => "\x87",
                      '9516' => "\x88",
                      '9524' => "\x89",
                      '9532' => "\x8A",
                      '9552' => "\xA0",
                      '9553' => "\xA1",
                      '9554' => "\xA2",
                      '9555' => "\xA4",
                      '9556' => "\xA5",
                      '9557' => "\xA6",
                      '9558' => "\xA7",
                      '9559' => "\xA8",
                      '9560' => "\xA9",
                      '9561' => "\xAA",
                      '9562' => "\xAB",
                      '9563' => "\xAC",
                      '9564' => "\xAD",
                      '9565' => "\xAE",
                      '9566' => "\xAF",
                      '9567' => "\xB0",
                      '9568' => "\xB1",
                      '9569' => "\xB2",
                      '9570' => "\xB4",
                      '9571' => "\xB5",
                      '9572' => "\xB6",
                      '9573' => "\xB7",
                      '9574' => "\xB8",
                      '9575' => "\xB9",
                      '9576' => "\xBA",
                      '9577' => "\xBB",
                      '9578' => "\xBC",
                      '9579' => "\xBD",
                      '9580' => "\xBE",
                      '9600' => "\x8B",
                      '9604' => "\x8C",
                      '9608' => "\x8D",
                      '9612' => "\x8E",
                      '9616' => "\x8F",
                      '9617' => "\x90",
                      '9618' => "\x91",
                      '9619' => "\x92",
                      '9632' => "\x94");

    if (array_key_exists($var,$koi8rchars)) {
        $ret=$koi8rchars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
