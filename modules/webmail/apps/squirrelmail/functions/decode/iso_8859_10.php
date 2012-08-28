<?php

/**
 * decode/iso8859-10.php
 *
 * This file contains iso-8859-10 decoding function that is needed to read
 * iso-8859-10 encoded mails in non-iso-8859-10 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-10.TXT
 *
 *   Name:             ISO/IEC 8859-10:1998 to Unicode
 *   Unicode version:  3.0
 *   Table version:    1.1
 *   Table format:     Format A
 *   Date:             1999 October 11
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
 * @version $Id: iso_8859_10.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso-8859-10 encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_10 ($string) {
     // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-10'))
        return $string;

    $iso8859_10 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#260;',
        "\xA2" => '&#274;',
        "\xA3" => '&#290;',
        "\xA4" => '&#298;',
        "\xA5" => '&#296;',
        "\xA6" => '&#310;',
        "\xA7" => '&#167;',
        "\xA8" => '&#315;',
        "\xA9" => '&#272;',
        "\xAA" => '&#352;',
        "\xAB" => '&#358;',
        "\xAC" => '&#381;',
        "\xAD" => '&#173;',
        "\xAE" => '&#362;',
        "\xAF" => '&#330;',
        "\xB0" => '&#176;',
        "\xB1" => '&#261;',
        "\xB2" => '&#275;',
        "\xB3" => '&#291;',
        "\xB4" => '&#299;',
        "\xB5" => '&#297;',
        "\xB6" => '&#311;',
        "\xB7" => '&#183;',
        "\xB8" => '&#316;',
        "\xB9" => '&#273;',
        "\xBA" => '&#353;',
        "\xBB" => '&#359;',
        "\xBC" => '&#382;',
        "\xBD" => '&#8213;',
        "\xBE" => '&#363;',
        "\xBF" => '&#331;',
        "\xC0" => '&#256;',
        "\xC1" => '&#193;',
        "\xC2" => '&#194;',
        "\xC3" => '&#195;',
        "\xC4" => '&#196;',
        "\xC5" => '&#197;',
        "\xC6" => '&#198;',
        "\xC7" => '&#302;',
        "\xC8" => '&#268;',
        "\xC9" => '&#201;',
        "\xCA" => '&#280;',
        "\xCB" => '&#203;',
        "\xCC" => '&#278;',
        "\xCD" => '&#205;',
        "\xCE" => '&#206;',
        "\xCF" => '&#207;',
        "\xD0" => '&#208;',
        "\xD1" => '&#325;',
        "\xD2" => '&#332;',
        "\xD3" => '&#211;',
        "\xD4" => '&#212;',
        "\xD5" => '&#213;',
        "\xD6" => '&#214;',
        "\xD7" => '&#360;',
        "\xD8" => '&#216;',
        "\xD9" => '&#370;',
        "\xDA" => '&#218;',
        "\xDB" => '&#219;',
        "\xDC" => '&#220;',
        "\xDD" => '&#221;',
        "\xDE" => '&#222;',
        "\xDF" => '&#223;',
        "\xE0" => '&#257;',
        "\xE1" => '&#225;',
        "\xE2" => '&#226;',
        "\xE3" => '&#227;',
        "\xE4" => '&#228;',
        "\xE5" => '&#229;',
        "\xE6" => '&#230;',
        "\xE7" => '&#303;',
        "\xE8" => '&#269;',
        "\xE9" => '&#233;',
        "\xEA" => '&#281;',
        "\xEB" => '&#235;',
        "\xEC" => '&#279;',
        "\xED" => '&#237;',
        "\xEE" => '&#238;',
        "\xEF" => '&#239;',
        "\xF0" => '&#240;',
        "\xF1" => '&#326;',
        "\xF2" => '&#333;',
        "\xF3" => '&#243;',
        "\xF4" => '&#244;',
        "\xF5" => '&#245;',
        "\xF6" => '&#246;',
        "\xF7" => '&#361;',
        "\xF8" => '&#248;',
        "\xF9" => '&#371;',
        "\xFA" => '&#250;',
        "\xFB" => '&#251;',
        "\xFC" => '&#252;',
        "\xFD" => '&#253;',
        "\xFE" => '&#254;',
        "\xFF" => '&#312;'
    );

    $string = str_replace(array_keys($iso8859_10), array_values($iso8859_10), $string);

    return $string;
}

