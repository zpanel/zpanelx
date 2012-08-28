<?php

/**
 * decode/iso8859-8.php
 *
 * This file contains iso-8859-8 decoding function that is needed to read
 * iso-8859-8 encoded mails in non-iso-8859-8 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-8.TXT
 *
 *   Name:             ISO/IEC 8859-8:1999 to Unicode
 *   Unicode version:  3.0
 *   Table version:    1.1
 *   Table format:     Format A
 *   Date:             2000-Jan-03
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
 * @version $Id: iso_8859_8.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-8 encoded strings
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_8 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-8'))
        return $string;

    $iso8859_8 = array(
        "\xA0" => '&#160;',
        "\xA2" => '&#162;',
        "\xA3" => '&#163;',
        "\xA4" => '&#164;',
        "\xA5" => '&#165;',
        "\xA6" => '&#166;',
        "\xA7" => '&#167;',
        "\xA8" => '&#168;',
        "\xA9" => '&#169;',
        "\xAA" => '&#215;',
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
        "\xBA" => '&#247;',
        "\xBB" => '&#187;',
        "\xBC" => '&#188;',
        "\xBD" => '&#189;',
        "\xBE" => '&#190;',
        "\xDF" => '&#8215;',
        "\xE0" => '&#1488;',
        "\xE1" => '&#1489;',
        "\xE2" => '&#1490;',
        "\xE3" => '&#1491;',
        "\xE4" => '&#1492;',
        "\xE5" => '&#1493;',
        "\xE6" => '&#1494;',
        "\xE7" => '&#1495;',
        "\xE8" => '&#1496;',
        "\xE9" => '&#1497;',
        "\xEA" => '&#1498;',
        "\xEB" => '&#1499;',
        "\xEC" => '&#1500;',
        "\xED" => '&#1501;',
        "\xEE" => '&#1502;',
        "\xEF" => '&#1503;',
        "\xF0" => '&#1504;',
        "\xF1" => '&#1505;',
        "\xF2" => '&#1506;',
        "\xF3" => '&#1507;',
        "\xF4" => '&#1508;',
        "\xF5" => '&#1509;',
        "\xF6" => '&#1510;',
        "\xF7" => '&#1511;',
        "\xF8" => '&#1512;',
        "\xF9" => '&#1513;',
        "\xFA" => '&#1514;',
        "\xFD" => '&#8206;',
        "\xFE" => '&#8207;'
    );

    $string = str_replace(array_keys($iso8859_8), array_values($iso8859_8), $string);

    return $string;
}
