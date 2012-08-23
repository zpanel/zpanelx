<?php

/**
 * decode/iso8859-9.php
 *
 * This file contains iso-8859-9 decoding function that is needed to read
 * iso-8859-9 encoded mails in non-iso-8859-9 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-9.TXT
 *
 *   Name:             ISO/IEC 8859-9:1999 to Unicode
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
 * @version $Id: iso_8859_9.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-9 encoded strings
 * @param string $string Encoded string
 * @return string Decoded string
 */
function charset_decode_iso_8859_9 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-9'))
        return $string;

    $iso8859_9 = array(
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
       "\xD0" => '&#286;',
       "\xD1" => '&#209;',
       "\xD2" => '&#210;',
       "\xD3" => '&#211;',
       "\xD4" => '&#212;',
       "\xD5" => '&#213;',
       "\xD6" => '&#214;',
       "\xD7" => '&#215;',
       "\xD8" => '&#216;',
       "\xD9" => '&#217;',
       "\xDA" => '&#218;',
       "\xDB" => '&#219;',
       "\xDC" => '&#220;',
       "\xDD" => '&#304;',
       "\xDE" => '&#350;',
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
       "\xF0" => '&#287;',
       "\xF1" => '&#241;',
       "\xF2" => '&#242;',
       "\xF3" => '&#243;',
       "\xF4" => '&#244;',
       "\xF5" => '&#245;',
       "\xF6" => '&#246;',
       "\xF7" => '&#247;',
       "\xF8" => '&#248;',
       "\xF9" => '&#249;',
       "\xFA" => '&#250;',
       "\xFB" => '&#251;',
       "\xFC" => '&#252;',
       "\xFD" => '&#305;',
       "\xFE" => '&#351;',
       "\xFF" => '&#255;'
    );

    $string = str_replace(array_keys($iso8859_9), array_values($iso8859_9), $string);

    return $string;
}
