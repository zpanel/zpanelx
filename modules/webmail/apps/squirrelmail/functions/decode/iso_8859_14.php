<?php

/**
 * decode/iso8859-14.php
 *
 * This file contains iso-8859-14 decoding function that is needed to read
 * iso-8859-14 encoded mails in non-iso-8859-14 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-14.TXT
 *
 *   Name:             ISO/IEC 8859-14:1998 to Unicode
 *   Unicode version:  3.0
 *   Table version:    1.0
 *   Table format:     Format A
 *   Date:             1999 July 27
 *   Authors:          Markus Kuhn <mkuhn@acm.org>
 *                     Ken Whistler <kenw@sybase.com>
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
 * @version $Id: iso_8859_14.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-14 encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_14 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-14'))
        return $string;

    $iso8859_14 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#7682;',
        "\xA2" => '&#7683;',
        "\xA3" => '&#163;',
        "\xA4" => '&#266;',
        "\xA5" => '&#267;',
        "\xA6" => '&#7690;',
        "\xA7" => '&#167;',
        "\xA8" => '&#7808;',
        "\xA9" => '&#169;',
        "\xAA" => '&#7810;',
        "\xAB" => '&#7691;',
        "\xAC" => '&#7922;',
        "\xAD" => '&#173;',
        "\xAE" => '&#174;',
        "\xAF" => '&#376;',
        "\xB0" => '&#7710;',
        "\xB1" => '&#7711;',
        "\xB2" => '&#288;',
        "\xB3" => '&#289;',
        "\xB4" => '&#7744;',
        "\xB5" => '&#7745;',
        "\xB6" => '&#182;',
        "\xB7" => '&#7766;',
        "\xB8" => '&#7809;',
        "\xB9" => '&#7767;',
        "\xBA" => '&#7811;',
        "\xBB" => '&#7776;',
        "\xBC" => '&#7923;',
        "\xBD" => '&#7812;',
        "\xBE" => '&#7813;',
        "\xBF" => '&#7777;',
        "\xC0" => '&#192;',
        "\xC1" => '&#193;',
        "\xC2" => '&#194;',
        "\xC3" => '&#195;',
        "\xC4" => '&#196;',
        "\xC5" => '&#197;',
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
        "\xD0" => '&#372;',
        "\xD1" => '&#209;',
        "\xD2" => '&#210;',
        "\xD3" => '&#211;',
        "\xD4" => '&#212;',
        "\xD5" => '&#213;',
        "\xD6" => '&#214;',
        "\xD7" => '&#7786;',
        "\xD8" => '&#216;',
        "\xD9" => '&#217;',
        "\xDA" => '&#218;',
        "\xDB" => '&#219;',
        "\xDC" => '&#220;',
        "\xDD" => '&#221;',
        "\xDE" => '&#374;',
        "\xDF" => '&#223;',
        "\xE0" => '&#224;',
        "\xE1" => '&#225;',
        "\xE2" => '&#226;',
        "\xE3" => '&#227;',
        "\xE4" => '&#228;',
        "\xE5" => '&#229;',
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
        "\xF0" => '&#373;',
        "\xF1" => '&#241;',
        "\xF2" => '&#242;',
        "\xF3" => '&#243;',
        "\xF4" => '&#244;',
        "\xF5" => '&#245;',
        "\xF6" => '&#246;',
        "\xF7" => '&#7787;',
        "\xF8" => '&#248;',
        "\xF9" => '&#249;',
        "\xFA" => '&#250;',
        "\xFB" => '&#251;',
        "\xFC" => '&#252;',
        "\xFD" => '&#253;',
        "\xFE" => '&#375;',
        "\xFF" => '&#255;'
    );

    $string = str_replace(array_keys($iso8859_14), array_values($iso8859_14), $string);

    return $string;
}
