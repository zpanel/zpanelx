<?php

/**
 * decode/iso8859-4.php
 *
 * This file contains iso-8859-4 decoding function that is needed to read
 * iso-8859-4 encoded mails in non-iso-8859-4 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-4.TXT
 *
 *   Name:             ISO/IEC 8859-4:1998 to Unicode
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
 * @version $Id: iso_8859_4.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-4 string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_4 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-4'))
        return $string;

    $iso8859_4 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#260;',
        "\xA2" => '&#312;',
        "\xA3" => '&#342;',
        "\xA4" => '&#164;',
        "\xA5" => '&#296;',
        "\xA6" => '&#315;',
        "\xA7" => '&#167;',
        "\xA8" => '&#168;',
        "\xA9" => '&#352;',
        "\xAA" => '&#274;',
        "\xAB" => '&#290;',
        "\xAC" => '&#358;',
        "\xAD" => '&#173;',
        "\xAE" => '&#381;',
        "\xAF" => '&#175;',
        "\xB0" => '&#176;',
        "\xB1" => '&#261;',
        "\xB2" => '&#731;',
        "\xB3" => '&#343;',
        "\xB4" => '&#180;',
        "\xB5" => '&#297;',
        "\xB6" => '&#316;',
        "\xB7" => '&#711;',
        "\xB8" => '&#184;',
        "\xB9" => '&#353;',
        "\xBA" => '&#275;',
        "\xBB" => '&#291;',
        "\xBC" => '&#359;',
        "\xBD" => '&#330;',
        "\xBE" => '&#382;',
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
        "\xCF" => '&#298;',
        "\xD0" => '&#272;',
        "\xD1" => '&#325;',
        "\xD2" => '&#332;',
        "\xD3" => '&#310;',
        "\xD4" => '&#212;',
        "\xD5" => '&#213;',
        "\xD6" => '&#214;',
        "\xD7" => '&#215;',
        "\xD8" => '&#216;',
        "\xD9" => '&#370;',
        "\xDA" => '&#218;',
        "\xDB" => '&#219;',
        "\xDC" => '&#220;',
        "\xDD" => '&#360;',
        "\xDE" => '&#362;',
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
        "\xEF" => '&#299;',
        "\xF0" => '&#273;',
        "\xF1" => '&#326;',
        "\xF2" => '&#333;',
        "\xF3" => '&#311;',
        "\xF4" => '&#244;',
        "\xF5" => '&#245;',
        "\xF6" => '&#246;',
        "\xF7" => '&#247;',
        "\xF8" => '&#248;',
        "\xF9" => '&#371;',
        "\xFA" => '&#250;',
        "\xFB" => '&#251;',
        "\xFC" => '&#252;',
        "\xFD" => '&#361;',
        "\xFE" => '&#363;',
        "\xFF" => '&#729;'
    );

    $string = str_replace(array_keys($iso8859_4), array_values($iso8859_4), $string);

    return $string;
}
