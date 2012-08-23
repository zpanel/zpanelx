<?php

/**
 * decode/cp1257.php
 *
 * This file contains cp1257 decoding function that is needed to read
 * cp1257 encoded mails in non-cp1257 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/WINDOWS/CP1257.TXT
 *
 *  Name:     cp1257 to Unicode table
 *  Unicode version: 2.0
 *  Table version: 2.01
 *  Table format:  Format A
 *  Date:          04/15/98
 *  Contact:       cpxlate@microsoft.com
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp1257.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode cp1257-encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_cp1257 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'windows-1257'))
        return $string;

    $cp1257 = array(
        "\x80" => '&#8364;',
        "\x82" => '&#8218;',
        "\x84" => '&#8222;',
        "\x85" => '&#8230;',
        "\x86" => '&#8224;',
        "\x87" => '&#8225;',
        "\x89" => '&#8240;',
        "\x8B" => '&#8249;',
        "\x8D" => '&#168;',
        "\x8E" => '&#711;',
        "\x8F" => '&#184;',
        "\x91" => '&#8216;',
        "\x92" => '&#8217;',
        "\x93" => '&#8220;',
        "\x94" => '&#8221;',
        "\x95" => '&#8226;',
        "\x96" => '&#8211;',
        "\x97" => '&#8212;',
        "\x99" => '&#8482;',
        "\x9B" => '&#8250;',
        "\x9D" => '&#175;',
        "\x9E" => '&#731;',
        "\xA0" => '&#160;',
        "\xA2" => '&#162;',
        "\xA3" => '&#163;',
        "\xA4" => '&#164;',
        "\xA6" => '&#166;',
        "\xA7" => '&#167;',
        "\xA8" => '&#216;',
        "\xA9" => '&#169;',
        "\xAA" => '&#342;',
        "\xAB" => '&#171;',
        "\xAC" => '&#172;',
        "\xAD" => '&#173;',
        "\xAE" => '&#174;',
        "\xAF" => '&#198;',
        "\xB0" => '&#176;',
        "\xB1" => '&#177;',
        "\xB2" => '&#178;',
        "\xB3" => '&#179;',
        "\xB4" => '&#180;',
        "\xB5" => '&#181;',
        "\xB6" => '&#182;',
        "\xB7" => '&#183;',
        "\xB8" => '&#248;',
        "\xB9" => '&#185;',
        "\xBA" => '&#343;',
        "\xBB" => '&#187;',
        "\xBC" => '&#188;',
        "\xBD" => '&#189;',
        "\xBE" => '&#190;',
        "\xBF" => '&#230;',
        "\xC0" => '&#260;',
        "\xC1" => '&#302;',
        "\xC2" => '&#256;',
        "\xC3" => '&#262;',
        "\xC4" => '&#196;',
        "\xC5" => '&#197;',
        "\xC6" => '&#280;',
        "\xC7" => '&#274;',
        "\xC8" => '&#268;',
        "\xC9" => '&#201;',
        "\xCA" => '&#377;',
        "\xCB" => '&#278;',
        "\xCC" => '&#290;',
        "\xCD" => '&#310;',
        "\xCE" => '&#298;',
        "\xCF" => '&#315;',
        "\xD0" => '&#352;',
        "\xD1" => '&#323;',
        "\xD2" => '&#325;',
        "\xD3" => '&#211;',
        "\xD4" => '&#332;',
        "\xD5" => '&#213;',
        "\xD6" => '&#214;',
        "\xD7" => '&#215;',
        "\xD8" => '&#370;',
        "\xD9" => '&#321;',
        "\xDA" => '&#346;',
        "\xDB" => '&#362;',
        "\xDC" => '&#220;',
        "\xDD" => '&#379;',
        "\xDE" => '&#381;',
        "\xDF" => '&#223;',
        "\xE0" => '&#261;',
        "\xE1" => '&#303;',
        "\xE2" => '&#257;',
        "\xE3" => '&#263;',
        "\xE4" => '&#228;',
        "\xE5" => '&#229;',
        "\xE6" => '&#281;',
        "\xE7" => '&#275;',
        "\xE8" => '&#269;',
        "\xE9" => '&#233;',
        "\xEA" => '&#378;',
        "\xEB" => '&#279;',
        "\xEC" => '&#291;',
        "\xED" => '&#311;',
        "\xEE" => '&#299;',
        "\xEF" => '&#316;',
        "\xF0" => '&#353;',
        "\xF1" => '&#324;',
        "\xF2" => '&#326;',
        "\xF3" => '&#243;',
        "\xF4" => '&#333;',
        "\xF5" => '&#245;',
        "\xF6" => '&#246;',
        "\xF7" => '&#247;',
        "\xF8" => '&#371;',
        "\xF9" => '&#322;',
        "\xFA" => '&#347;',
        "\xFB" => '&#363;',
        "\xFC" => '&#252;',
        "\xFD" => '&#380;',
        "\xFE" => '&#382;',
        "\xFF" => '&#729;'
    );

    $string = str_replace(array_keys($cp1257), array_values($cp1257), $string);

    return $string;
}

