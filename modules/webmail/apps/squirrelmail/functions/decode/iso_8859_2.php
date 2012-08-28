<?php

/**
 * decode/iso8859-2.php
 *
 * This file contains iso-8859-2 decoding function that is needed to read
 * iso-8859-2 encoded mails in non-iso-8859-2 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-2.TXT
 *
 *   Name:             ISO 8859-2:1999 to Unicode
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
 * @version $Id: iso_8859_2.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-2 encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_2 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-2'))
        return $string;

    $iso8859_2 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#260;',
        "\xA2" => '&#728;',
        "\xA3" => '&#321;',
        "\xA4" => '&#164;',
        "\xA5" => '&#317;',
        "\xA6" => '&#346;',
        "\xA7" => '&#167;',
        "\xA8" => '&#168;',
        "\xA9" => '&#352;',
        "\xAA" => '&#350;',
        "\xAB" => '&#356;',
        "\xAC" => '&#377;',
        "\xAD" => '&#173;',
        "\xAE" => '&#381;',
        "\xAF" => '&#379;',
        "\xB0" => '&#176;',
        "\xB1" => '&#261;',
        "\xB2" => '&#731;',
        "\xB3" => '&#322;',
        "\xB4" => '&#180;',
        "\xB5" => '&#318;',
        "\xB6" => '&#347;',
        "\xB7" => '&#711;',
        "\xB8" => '&#184;',
        "\xB9" => '&#353;',
        "\xBA" => '&#351;',
        "\xBB" => '&#357;',
        "\xBC" => '&#378;',
        "\xBD" => '&#733;',
        "\xBE" => '&#382;',
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

    $string = str_replace(array_keys($iso8859_2), array_values($iso8859_2), $string);

    return $string;
}
