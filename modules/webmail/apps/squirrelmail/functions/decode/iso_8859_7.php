<?php

/**
 * decode/iso8859-7.php
 *
 * This file contains iso-8859-7 decoding function that is needed to read
 * iso-8859-7 encoded mails in non-iso-8859-7 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-7.TXT
 *
 *   Name:             ISO 8859-7:1987 to Unicode
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
 * @version $Id: iso_8859_7.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-7 encoded strings
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_7 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-7'))
        return $string;

    $iso8859_7 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#8216;',
        "\xA2" => '&#8217;',
        "\xA3" => '&#163;',
        "\xA6" => '&#166;',
        "\xA7" => '&#167;',
        "\xA8" => '&#168;',
        "\xA9" => '&#169;',
        "\xAB" => '&#171;',
        "\xAC" => '&#172;',
        "\xAD" => '&#173;',
        "\xAF" => '&#8213;',
        "\xB0" => '&#176;',
        "\xB1" => '&#177;',
        "\xB2" => '&#178;',
        "\xB3" => '&#179;',
        "\xB4" => '&#900;',
        "\xB5" => '&#901;',
        "\xB6" => '&#902;',
        "\xB7" => '&#183;',
        "\xB8" => '&#904;',
        "\xB9" => '&#905;',
        "\xBA" => '&#906;',
        "\xBB" => '&#187;',
        "\xBC" => '&#908;',
        "\xBD" => '&#189;',
        "\xBE" => '&#910;',
        "\xBF" => '&#911;',
        "\xC0" => '&#912;',
        "\xC1" => '&#913;',
        "\xC2" => '&#914;',
        "\xC3" => '&#915;',
        "\xC4" => '&#916;',
        "\xC5" => '&#917;',
        "\xC6" => '&#918;',
        "\xC7" => '&#919;',
        "\xC8" => '&#920;',
        "\xC9" => '&#921;',
        "\xCA" => '&#922;',
        "\xCB" => '&#923;',
        "\xCC" => '&#924;',
        "\xCD" => '&#925;',
        "\xCE" => '&#926;',
        "\xCF" => '&#927;',
        "\xD0" => '&#928;',
        "\xD1" => '&#929;',
        "\xD3" => '&#931;',
        "\xD4" => '&#932;',
        "\xD5" => '&#933;',
        "\xD6" => '&#934;',
        "\xD7" => '&#935;',
        "\xD8" => '&#936;',
        "\xD9" => '&#937;',
        "\xDA" => '&#938;',
        "\xDB" => '&#939;',
        "\xDC" => '&#940;',
        "\xDD" => '&#941;',
        "\xDE" => '&#942;',
        "\xDF" => '&#943;',
        "\xE0" => '&#944;',
        "\xE1" => '&#945;',
        "\xE2" => '&#946;',
        "\xE3" => '&#947;',
        "\xE4" => '&#948;',
        "\xE5" => '&#949;',
        "\xE6" => '&#950;',
        "\xE7" => '&#951;',
        "\xE8" => '&#952;',
        "\xE9" => '&#953;',
        "\xEA" => '&#954;',
        "\xEB" => '&#955;',
        "\xEC" => '&#956;',
        "\xED" => '&#957;',
        "\xEE" => '&#958;',
        "\xEF" => '&#959;',
        "\xF0" => '&#960;',
        "\xF1" => '&#961;',
        "\xF2" => '&#962;',
        "\xF3" => '&#963;',
        "\xF4" => '&#964;',
        "\xF5" => '&#965;',
        "\xF6" => '&#966;',
        "\xF7" => '&#967;',
        "\xF8" => '&#968;',
        "\xF9" => '&#969;',
        "\xFA" => '&#970;',
        "\xFB" => '&#971;',
        "\xFC" => '&#972;',
        "\xFD" => '&#973;',
        "\xFE" => '&#974;'
    );

    $string = str_replace(array_keys($iso8859_7), array_values($iso8859_7), $string);

    return $string;
}
