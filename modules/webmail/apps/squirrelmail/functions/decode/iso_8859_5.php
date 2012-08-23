<?php

/**
 * decode/iso8859-5.php
 *
 * This file contains iso-8859-5 decoding function that is needed to read
 * iso-8859-5 encoded mails in non-iso-8859-5 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-5.TXT
 *
 *   Name:             ISO 8859-5:1999 to Unicode
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
 * @version $Id: iso_8859_5.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-5 encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_5 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-5'))
        return $string;

    $iso8859_5 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#1025;',
        "\xA2" => '&#1026;',
        "\xA3" => '&#1027;',
        "\xA4" => '&#1028;',
        "\xA5" => '&#1029;',
        "\xA6" => '&#1030;',
        "\xA7" => '&#1031;',
        "\xA8" => '&#1032;',
        "\xA9" => '&#1033;',
        "\xAA" => '&#1034;',
        "\xAB" => '&#1035;',
        "\xAC" => '&#1036;',
        "\xAD" => '&#173;',
        "\xAE" => '&#1038;',
        "\xAF" => '&#1039;',
        "\xB0" => '&#1040;',
        "\xB1" => '&#1041;',
        "\xB2" => '&#1042;',
        "\xB3" => '&#1043;',
        "\xB4" => '&#1044;',
        "\xB5" => '&#1045;',
        "\xB6" => '&#1046;',
        "\xB7" => '&#1047;',
        "\xB8" => '&#1048;',
        "\xB9" => '&#1049;',
        "\xBA" => '&#1050;',
        "\xBB" => '&#1051;',
        "\xBC" => '&#1052;',
        "\xBD" => '&#1053;',
        "\xBE" => '&#1054;',
        "\xBF" => '&#1055;',
        "\xC0" => '&#1056;',
        "\xC1" => '&#1057;',
        "\xC2" => '&#1058;',
        "\xC3" => '&#1059;',
        "\xC4" => '&#1060;',
        "\xC5" => '&#1061;',
        "\xC6" => '&#1062;',
        "\xC7" => '&#1063;',
        "\xC8" => '&#1064;',
        "\xC9" => '&#1065;',
        "\xCA" => '&#1066;',
        "\xCB" => '&#1067;',
        "\xCC" => '&#1068;',
        "\xCD" => '&#1069;',
        "\xCE" => '&#1070;',
        "\xCF" => '&#1071;',
        "\xD0" => '&#1072;',
        "\xD1" => '&#1073;',
        "\xD2" => '&#1074;',
        "\xD3" => '&#1075;',
        "\xD4" => '&#1076;',
        "\xD5" => '&#1077;',
        "\xD6" => '&#1078;',
        "\xD7" => '&#1079;',
        "\xD8" => '&#1080;',
        "\xD9" => '&#1081;',
        "\xDA" => '&#1082;',
        "\xDB" => '&#1083;',
        "\xDC" => '&#1084;',
        "\xDD" => '&#1085;',
        "\xDE" => '&#1086;',
        "\xDF" => '&#1087;',
        "\xE0" => '&#1088;',
        "\xE1" => '&#1089;',
        "\xE2" => '&#1090;',
        "\xE3" => '&#1091;',
        "\xE4" => '&#1092;',
        "\xE5" => '&#1093;',
        "\xE6" => '&#1094;',
        "\xE7" => '&#1095;',
        "\xE8" => '&#1096;',
        "\xE9" => '&#1097;',
        "\xEA" => '&#1098;',
        "\xEB" => '&#1099;',
        "\xEC" => '&#1100;',
        "\xED" => '&#1101;',
        "\xEE" => '&#1102;',
        "\xEF" => '&#1103;',
        "\xF0" => '&#8470;',
        "\xF1" => '&#1105;',
        "\xF2" => '&#1106;',
        "\xF3" => '&#1107;',
        "\xF4" => '&#1108;',
        "\xF5" => '&#1109;',
        "\xF6" => '&#1110;',
        "\xF7" => '&#1111;',
        "\xF8" => '&#1112;',
        "\xF9" => '&#1113;',
        "\xFA" => '&#1114;',
        "\xFB" => '&#1115;',
        "\xFC" => '&#1116;',
        "\xFD" => '&#167;',
        "\xFE" => '&#1118;',
        "\xFF" => '&#1119;'
    );

    $string = str_replace(array_keys($iso8859_5), array_values($iso8859_5), $string);

    return $string;
}
