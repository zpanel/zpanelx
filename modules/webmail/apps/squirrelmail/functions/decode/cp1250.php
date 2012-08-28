<?php

/**
 * decode/cp1250.php
 *
 * This file contains cp1250 decoding function that is needed to read
 * cp1250 encoded mails in non-cp1250 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/WINDOWS/CP1250.TXT
 *
 *  Name:     cp1250 to Unicode table
 *  Unicode version: 2.0
 *  Table version: 2.01
 *  Table format:  Format A
 *  Date:          04/15/98
 *  Contact:       cpxlate@microsoft.com
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp1250.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode a cp1250 string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_cp1250 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'windows-1250'))
        return $string;

    $cp1250 = array(
        "\x80" => '&#8364;',
        "\x81" => '&#65533;',
        "\x82" => '&#8218;',
        "\x83" => '&#65533;',
        "\x84" => '&#8222;',
        "\x85" => '&#8230;',
        "\x86" => '&#8224;',
        "\x87" => '&#8225;',
        "\x88" => '&#65533;',
        "\x89" => '&#8240;',
        "\x8A" => '&#352;',
        "\x8B" => '&#8249;',
        "\x8C" => '&#346;',
        "\x8D" => '&#356;',
        "\x8E" => '&#381;',
        "\x8F" => '&#377;',
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
        "\x9A" => '&#353;',
        "\x9B" => '&#8250;',
        "\x9C" => '&#347;',
        "\x9D" => '&#357;',
        "\x9E" => '&#382;',
        "\x9F" => '&#378;',
        "\xA0" => '&#160;',
        "\xA1" => '&#711;',
        "\xA2" => '&#728;',
        "\xA3" => '&#321;',
        "\xA4" => '&#164;',
        "\xA5" => '&#260;',
        "\xA6" => '&#166;',
        "\xA7" => '&#167;',
        "\xA8" => '&#168;',
        "\xA9" => '&#169;',
        "\xAA" => '&#350;',
        "\xAB" => '&#171;',
        "\xAC" => '&#172;',
        "\xAD" => '&#173;',
        "\xAE" => '&#174;',
        "\xAF" => '&#379;',
        "\xB0" => '&#176;',
        "\xB1" => '&#177;',
        "\xB2" => '&#731;',
        "\xB3" => '&#322;',
        "\xB4" => '&#180;',
        "\xB5" => '&#181;',
        "\xB6" => '&#182;',
        "\xB7" => '&#183;',
        "\xB8" => '&#184;',
        "\xB9" => '&#261;',
        "\xBA" => '&#351;',
        "\xBB" => '&#187;',
        "\xBC" => '&#317;',
        "\xBD" => '&#733;',
        "\xBE" => '&#318;',
        "\xBF" => '&#380;',
        "\xC0" => '&#340;',
        "\xC1" => '&#193;',
        "\xC2" => '&#194;',
        "\xC3" => '&#258;',
        "\xC4" => '&#196;',
        "\xC5" => '&#313;',
        "\xC6" => '&#262;',
        "\xC7" => '&#199;',
        "\xC8" => '&#268;',
        "\xC9" => '&#201;',
        "\xCA" => '&#280;',
        "\xCB" => '&#203;',
        "\xCC" => '&#282;',
        "\xCD" => '&#205;',
        "\xCE" => '&#206;',
        "\xCF" => '&#270;',
        "\xD0" => '&#272;',
        "\xD1" => '&#323;',
        "\xD2" => '&#327;',
        "\xD3" => '&#211;',
        "\xD4" => '&#212;',
        "\xD5" => '&#336;',
        "\xD6" => '&#214;',
        "\xD7" => '&#215;',
        "\xD8" => '&#344;',
        "\xD9" => '&#366;',
        "\xDA" => '&#218;',
        "\xDB" => '&#368;',
        "\xDC" => '&#220;',
        "\xDD" => '&#221;',
        "\xDE" => '&#354;',
        "\xDF" => '&#223;',
        "\xE0" => '&#341;',
        "\xE1" => '&#225;',
        "\xE2" => '&#226;',
        "\xE3" => '&#259;',
        "\xE4" => '&#228;',
        "\xE5" => '&#314;',
        "\xE6" => '&#263;',
        "\xE7" => '&#231;',
        "\xE8" => '&#269;',
        "\xE9" => '&#233;',
        "\xEA" => '&#281;',
        "\xEB" => '&#235;',
        "\xEC" => '&#283;',
        "\xED" => '&#237;',
        "\xEE" => '&#238;',
        "\xEF" => '&#271;',
        "\xF0" => '&#273;',
        "\xF1" => '&#324;',
        "\xF2" => '&#328;',
        "\xF3" => '&#243;',
        "\xF4" => '&#244;',
        "\xF5" => '&#337;',
        "\xF6" => '&#246;',
        "\xF7" => '&#247;',
        "\xF8" => '&#345;',
        "\xF9" => '&#367;',
        "\xFA" => '&#250;',
        "\xFB" => '&#369;',
        "\xFC" => '&#252;',
        "\xFD" => '&#253;',
        "\xFE" => '&#355;',
        "\xFF" => '&#729;'
    );

    $string = str_replace(array_keys($cp1250), array_values($cp1250), $string);

    return $string;
}

