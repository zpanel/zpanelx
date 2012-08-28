<?php

/**
 * decode/cp1258.php
 *
 * This file contains cp1258 decoding function that is needed to read
 * cp1258 encoded mails in non-cp1258 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/WINDOWS/CP1258.TXT
 *
 *   Name:     cp1258 to Unicode table
 *   Unicode version: 2.0
 *   Table version: 2.01
 *   Table format:  Format A
 *   Date:          04/15/98
 *   Contact:       cpxlate@microsoft.com
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp1258.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decde a cp1258-encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_cp1258 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'windows-1258'))
        return $string;

    $cp1258 = array(
        "\x80" => '&#8364;',
        "\x81" => '&#65533;',
        "\x82" => '&#8218;',
        "\x83" => '&#402;',
        "\x84" => '&#8222;',
        "\x85" => '&#8230;',
        "\x86" => '&#8224;',
        "\x87" => '&#8225;',
        "\x88" => '&#710;',
        "\x89" => '&#8240;',
        "\x8A" => '&#65533;',
        "\x8B" => '&#8249;',
        "\x8C" => '&#338;',
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
        "\x98" => '&#732;',
        "\x99" => '&#8482;',
        "\x9A" => '&#65533;',
        "\x9B" => '&#8250;',
        "\x9C" => '&#339;',
        "\x9D" => '&#65533;',
        "\x9E" => '&#65533;',
        "\x9F" => '&#376;',
        "\xA0" => '&#160;',
        "\xA1" => '&#161;',
        "\xA2" => '&#162;',
        "\xA3" => '&#163;',
        "\xA4" => '&#164;',
        "\xA5" => '&#165;',
        "\xA6" => '&#166;',
        "\xA7" => '&#167;',
        "\xA8" => '&#168;',
        "\xA9" => '&#169;',
        "\xAA" => '&#170;',
        "\xAB" => '&#171;',
        "\xAC" => '&#172;',
        "\xAD" => '&#173;',
        "\xAE" => '&#174;',
        "\xAF" => '&#175;',
        "\xB0" => '&#176;',
        "\xB1" => '&#177;',
        "\xB2" => '&#178;',
        "\xB3" => '&#179;',
        "\xB4" => '&#180;',
        "\xB5" => '&#181;',
        "\xB6" => '&#182;',
        "\xB7" => '&#183;',
        "\xB8" => '&#184;',
        "\xB9" => '&#185;',
        "\xBA" => '&#186;',
        "\xBB" => '&#187;',
        "\xBC" => '&#188;',
        "\xBD" => '&#189;',
        "\xBE" => '&#190;',
        "\xBF" => '&#191;',
        "\xC0" => '&#192;',
        "\xC1" => '&#193;',
        "\xC2" => '&#194;',
        "\xC3" => '&#258;',
        "\xC4" => '&#196;',
        "\xC5" => '&#197;',
        "\xC6" => '&#198;',
        "\xC7" => '&#199;',
        "\xC8" => '&#200;',
        "\xC9" => '&#201;',
        "\xCA" => '&#202;',
        "\xCB" => '&#203;',
        "\xCC" => '&#768;',
        "\xCD" => '&#205;',
        "\xCE" => '&#206;',
        "\xCF" => '&#207;',
        "\xD0" => '&#272;',
        "\xD1" => '&#209;',
        "\xD2" => '&#777;',
        "\xD3" => '&#211;',
        "\xD4" => '&#212;',
        "\xD5" => '&#416;',
        "\xD6" => '&#214;',
        "\xD7" => '&#215;',
        "\xD8" => '&#216;',
        "\xD9" => '&#217;',
        "\xDA" => '&#218;',
        "\xDB" => '&#219;',
        "\xDC" => '&#220;',
        "\xDD" => '&#431;',
        "\xDE" => '&#771;',
        "\xDF" => '&#223;',
        "\xE0" => '&#224;',
        "\xE1" => '&#225;',
        "\xE2" => '&#226;',
        "\xE3" => '&#259;',
        "\xE4" => '&#228;',
        "\xE5" => '&#229;',
        "\xE6" => '&#230;',
        "\xE7" => '&#231;',
        "\xE8" => '&#232;',
        "\xE9" => '&#233;',
        "\xEA" => '&#234;',
        "\xEB" => '&#235;',
        "\xEC" => '&#769;',
        "\xED" => '&#237;',
        "\xEE" => '&#238;',
        "\xEF" => '&#239;',
        "\xF0" => '&#273;',
        "\xF1" => '&#241;',
        "\xF2" => '&#803;',
        "\xF3" => '&#243;',
        "\xF4" => '&#244;',
        "\xF5" => '&#417;',
        "\xF6" => '&#246;',
        "\xF7" => '&#247;',
        "\xF8" => '&#248;',
        "\xF9" => '&#249;',
        "\xFA" => '&#250;',
        "\xFB" => '&#251;',
        "\xFC" => '&#252;',
        "\xFD" => '&#432;',
        "\xFE" => '&#8363;',
        "\xFF" => '&#255;'
    );

    $string = str_replace(array_keys($cp1258), array_values($cp1258), $string);

    return $string;
}

