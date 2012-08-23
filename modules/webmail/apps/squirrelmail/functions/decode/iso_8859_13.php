<?php

/**
 * decode/iso8859-13.php
 *
 * This file contains iso-8859-13 decoding function that is needed to read
 * iso-8859-13 encoded mails in non-iso-8859-13 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-13.TXT
 *
 *   Name:             ISO/IEC 8859-13:1998  to Unicode
 *   Unicode version:  3.0
 *   Table version:    1.0
 *   Table format:     Format A
 *   Date:             1999 July 27
 *   Authors:          Ken Whistler <kenw@sybase.com>
 *
 * Original copyright:
 *  Copyright (c) 1999 Unicode, Inc.  All Rights reserved.
 *
 *  This file is provided as-is by Unicode, Inc. (The Unicode Consortium).
 *  No claims are made as to fitness for any particular purpose.  No
 *  warranties of any kind are expressed or implied.  The recipient
 *  agrees to determine applicability of information provided.  If this
 *  file has been provided on optical media by Unicode, Inc., the sole
 *  remedy for any claim will be exchange of defective media within 90
 *  days of receipt.
 *
 *  Unicode, Inc. hereby grants the right to freely use the information
 *  supplied in this file in the creation of products supporting the
 *  Unicode Standard, and to make copies of this file in any form for
 *  internal or external distribution as long as this notice remains
 *  attached.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_8859_13.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-13
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_13 ($string) {
     // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-13'))
        return $string;

    $iso8859_13 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#8221;',
        "\xA2" => '&#162;',
        "\xA3" => '&#163;',
        "\xA4" => '&#164;',
        "\xA5" => '&#8222;',
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
        "\xB4" => '&#8220;',
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
        "\xFF" => '&#8217;'
    );

    $string = str_replace(array_keys($iso8859_13), array_values($iso8859_13), $string);

    return $string;
}

