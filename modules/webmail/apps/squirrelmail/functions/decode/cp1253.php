<?php

/**
 * decode/cp1253.php
 *
 * This file contains cp1253 decoding function that is needed to read
 * cp1253 encoded mails in non-cp1253 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/WINDOWS/CP1253.TXT
 *
 *   Name:     cp1253 to Unicode table
 *   Unicode version: 2.0
 *   Table version: 2.01
 *   Table format:  Format A
 *   Date:          04/15/98
 *   Contact:       cpxlate@microsoft.com
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp1253.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode cp1253-encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_cp1253 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'windows-1253'))
        return $string;

    $cp1253 = array(
        "\x80" => '&#8364;',
        "\x81" => '&#65533;',
        "\x82" => '&#8218;',
        "\x83" => '&#402;',
        "\x84" => '&#8222;',
        "\x85" => '&#8230;',
        "\x86" => '&#8224;',
        "\x87" => '&#8225;',
        "\x88" => '&#65533;',
        "\x89" => '&#8240;',
        "\x8A" => '&#65533;',
        "\x8B" => '&#8249;',
        "\x8C" => '&#65533;',
        "\x8D" => '&#65533;',
        "\x8E" => '&#65533;',
        "\x8F" => '&#65533;',
        "\x90" => '&#65533;',
        "\x91" => '&#8216;',
        "\x92" => '&#8217;',
        "\x93" => '&#8220;',
        "\x94" => '&#8221;',
        "\x95" => '&#8226;',
        "\x96" => '&#8211;',
        "\x97" => '&#8212;',
        "\x98" => '&#65533;',
        "\x99" => '&#8482;',
        "\x9A" => '&#65533;',
        "\x9B" => '&#8250;',
        "\x9C" => '&#65533;',
        "\x9D" => '&#65533;',
        "\x9E" => '&#65533;',
        "\x9F" => '&#65533;',
        "\xA0" => '&#160;',
        "\xA1" => '&#901;',
        "\xA2" => '&#902;',
        "\xA3" => '&#163;',
        "\xA4" => '&#164;',
        "\xA5" => '&#165;',
        "\xA6" => '&#166;',
        "\xA7" => '&#167;',
        "\xA8" => '&#168;',
        "\xA9" => '&#169;',
        "\xAA" => '&#65533;',
        "\xAB" => '&#171;',
        "\xAC" => '&#172;',
        "\xAD" => '&#173;',
        "\xAE" => '&#174;',
        "\xAF" => '&#8213;',
        "\xB0" => '&#176;',
        "\xB1" => '&#177;',
        "\xB2" => '&#178;',
        "\xB3" => '&#179;',
        "\xB4" => '&#900;',
        "\xB5" => '&#181;',
        "\xB6" => '&#182;',
        "\xB7" => '&#183;',
        "\xB8" => '&#904;',
        "\xB9" => '&#905;',
        "\xBA" => '&#906;',
        "\xBB" => '&#187;',
        "\xBC" => '&#908;',
        "\xBD" => '&#189;',
        "\xBE" => '&#910;',
        "\xBF" => '&#911;',
        "\xC0" => '&#912;',
        "\xC1" => '&#913;',
        "\xC2" => '&#914;',
        "\xC3" => '&#915;',
        "\xC4" => '&#916;',
        "\xC5" => '&#917;',
        "\xC6" => '&#918;',
        "\xC7" => '&#919;',
        "\xC8" => '&#920;',
        "\xC9" => '&#921;',
        "\xCA" => '&#922;',
        "\xCB" => '&#923;',
        "\xCC" => '&#924;',
        "\xCD" => '&#925;',
        "\xCE" => '&#926;',
        "\xCF" => '&#927;',
        "\xD0" => '&#928;',
        "\xD1" => '&#929;',
        "\xD2" => '&#65533;',
        "\xD3" => '&#931;',
        "\xD4" => '&#932;',
        "\xD5" => '&#933;',
        "\xD6" => '&#934;',
        "\xD7" => '&#935;',
        "\xD8" => '&#936;',
        "\xD9" => '&#937;',
        "\xDA" => '&#938;',
        "\xDB" => '&#939;',
        "\xDC" => '&#940;',
        "\xDD" => '&#941;',
        "\xDE" => '&#942;',
        "\xDF" => '&#943;',
        "\xE0" => '&#944;',
        "\xE1" => '&#945;',
        "\xE2" => '&#946;',
        "\xE3" => '&#947;',
        "\xE4" => '&#948;',
        "\xE5" => '&#949;',
        "\xE6" => '&#950;',
        "\xE7" => '&#951;',
        "\xE8" => '&#952;',
        "\xE9" => '&#953;',
        "\xEA" => '&#954;',
        "\xEB" => '&#955;',
        "\xEC" => '&#956;',
        "\xED" => '&#957;',
        "\xEE" => '&#958;',
        "\xEF" => '&#959;',
        "\xF0" => '&#960;',
        "\xF1" => '&#961;',
        "\xF2" => '&#962;',
        "\xF3" => '&#963;',
        "\xF4" => '&#964;',
        "\xF5" => '&#965;',
        "\xF6" => '&#966;',
        "\xF7" => '&#967;',
        "\xF8" => '&#968;',
        "\xF9" => '&#969;',
        "\xFA" => '&#970;',
        "\xFB" => '&#971;',
        "\xFC" => '&#972;',
        "\xFD" => '&#973;',
        "\xFE" => '&#974;',
        "\xFF" => '&#65533;'
    );

    $string = str_replace(array_keys($cp1253), array_values($cp1253), $string);

    return $string;
}

