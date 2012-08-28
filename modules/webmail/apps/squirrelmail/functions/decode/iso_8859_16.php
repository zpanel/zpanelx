<?php

/**
 * decode/iso8859-16.php
 *
 * This file contains iso-8859-16 decoding function that is needed to read
 * iso-8859-16 encoded mails in non-iso-8859-16 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-16.TXT
 *
 *   Name:             ISO/IEC 8859-16:2001 to Unicode
 *   Unicode version:  3.0
 *   Table version:    1.0
 *   Table format:     Format A
 *   Date:             2001 July 26
 *   Authors:          Markus Kuhn <mkuhn@acm.org>
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
 * @version $Id: iso_8859_16.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-16 string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_16 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-16'))
        return $string;

    $iso8859_16 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#260;',
        "\xA2" => '&#261;',
        "\xA3" => '&#321;',
        "\xA4" => '&#8364;',
        "\xA5" => '&#8222;',
        "\xA6" => '&#352;',
        "\xA7" => '&#167;',
        "\xA8" => '&#353;',
        "\xA9" => '&#169;',
        "\xAA" => '&#536;',
        "\xAB" => '&#171;',
        "\xAC" => '&#377;',
        "\xAD" => '&#173;',
        "\xAE" => '&#378;',
        "\xAF" => '&#379;',
        "\xB0" => '&#176;',
        "\xB1" => '&#177;',
        "\xB2" => '&#268;',
        "\xB3" => '&#322;',
        "\xB4" => '&#381;',
        "\xB5" => '&#8221;',
        "\xB6" => '&#182;',
        "\xB7" => '&#183;',
        "\xB8" => '&#382;',
        "\xB9" => '&#269;',
        "\xBA" => '&#537;',
        "\xBB" => '&#187;',
        "\xBC" => '&#338;',
        "\xBD" => '&#339;',
        "\xBE" => '&#376;',
        "\xBF" => '&#380;',
        "\xC0" => '&#192;',
        "\xC1" => '&#193;',
        "\xC2" => '&#194;',
        "\xC3" => '&#258;',
        "\xC4" => '&#196;',
        "\xC5" => '&#262;',
        "\xC6" => '&#198;',
        "\xC7" => '&#199;',
        "\xC8" => '&#200;',
        "\xC9" => '&#201;',
        "\xCA" => '&#202;',
        "\xCB" => '&#203;',
        "\xCC" => '&#204;',
        "\xCD" => '&#205;',
        "\xCE" => '&#206;',
        "\xCF" => '&#207;',
        "\xD0" => '&#272;',
        "\xD1" => '&#323;',
        "\xD2" => '&#210;',
        "\xD3" => '&#211;',
        "\xD4" => '&#212;',
        "\xD5" => '&#336;',
        "\xD6" => '&#214;',
        "\xD7" => '&#346;',
        "\xD8" => '&#368;',
        "\xD9" => '&#217;',
        "\xDA" => '&#218;',
        "\xDB" => '&#219;',
        "\xDC" => '&#220;',
        "\xDD" => '&#280;',
        "\xDE" => '&#538;',
        "\xDF" => '&#223;',
        "\xE0" => '&#224;',
        "\xE1" => '&#225;',
        "\xE2" => '&#226;',
        "\xE3" => '&#259;',
        "\xE4" => '&#228;',
        "\xE5" => '&#263;',
        "\xE6" => '&#230;',
        "\xE7" => '&#231;',
        "\xE8" => '&#232;',
        "\xE9" => '&#233;',
        "\xEA" => '&#234;',
        "\xEB" => '&#235;',
        "\xEC" => '&#236;',
        "\xED" => '&#237;',
        "\xEE" => '&#238;',
        "\xEF" => '&#239;',
        "\xF0" => '&#273;',
        "\xF1" => '&#324;',
        "\xF2" => '&#242;',
        "\xF3" => '&#243;',
        "\xF4" => '&#244;',
        "\xF5" => '&#337;',
        "\xF6" => '&#246;',
        "\xF7" => '&#347;',
        "\xF8" => '&#369;',
        "\xF9" => '&#249;',
        "\xFA" => '&#250;',
        "\xFB" => '&#251;',
        "\xFC" => '&#252;',
        "\xFD" => '&#281;',
        "\xFE" => '&#539;',
        "\xFF" => '&#255;'
    );

    $string = str_replace(array_keys($iso8859_16), array_values($iso8859_16), $string);

    return $string;
}
