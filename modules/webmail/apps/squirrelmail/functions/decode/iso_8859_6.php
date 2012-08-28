<?php

/**
 * decode/iso8859-6.php
 *
 * This file contains iso-8859-6 decoding function that is needed to read
 * iso-8859-6 encoded mails in non-iso-8859-6 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-6.TXT
 *
 *   Name:             ISO 8859-6:1999 to Unicode
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
 * @version $Id: iso_8859_6.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-6 strings
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_6 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-6'))
        return $string;

    $iso8859_6 = array(
        "\xA0" => '&#160;',
        "\xA4" => '&#164;',
        "\xAC" => '&#1548;',
        "\xAD" => '&#173;',
        "\xBB" => '&#1563;',
        "\xBF" => '&#1567;',
        "\xC1" => '&#1569;',
        "\xC2" => '&#1570;',
        "\xC3" => '&#1571;',
        "\xC4" => '&#1572;',
        "\xC5" => '&#1573;',
        "\xC6" => '&#1574;',
        "\xC7" => '&#1575;',
        "\xC8" => '&#1576;',
        "\xC9" => '&#1577;',
        "\xCA" => '&#1578;',
        "\xCB" => '&#1579;',
        "\xCC" => '&#1580;',
        "\xCD" => '&#1581;',
        "\xCE" => '&#1582;',
        "\xCF" => '&#1583;',
        "\xD0" => '&#1584;',
        "\xD1" => '&#1585;',
        "\xD2" => '&#1586;',
        "\xD3" => '&#1587;',
        "\xD4" => '&#1588;',
        "\xD5" => '&#1589;',
        "\xD6" => '&#1590;',
        "\xD7" => '&#1591;',
        "\xD8" => '&#1592;',
        "\xD9" => '&#1593;',
        "\xDA" => '&#1594;',
        "\xE0" => '&#1600;',
        "\xE1" => '&#1601;',
        "\xE2" => '&#1602;',
        "\xE3" => '&#1603;',
        "\xE4" => '&#1604;',
        "\xE5" => '&#1605;',
        "\xE6" => '&#1606;',
        "\xE7" => '&#1607;',
        "\xE8" => '&#1608;',
        "\xE9" => '&#1609;',
        "\xEA" => '&#1610;',
        "\xEB" => '&#1611;',
        "\xEC" => '&#1612;',
        "\xED" => '&#1613;',
        "\xEE" => '&#1614;',
        "\xEF" => '&#1615;',
        "\xF0" => '&#1616;',
        "\xF1" => '&#1617;',
        "\xF2" => '&#1618;'
    );

    $string = str_replace(array_keys($iso8859_6), array_values($iso8859_6), $string);

    return $string;
}
