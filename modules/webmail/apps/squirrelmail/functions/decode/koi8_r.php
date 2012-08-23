<?php

/**
 * decode/koi8-r.php
 *
 * This file contains koi8-r decoding function that is needed to read
 * koi8-r encoded mails in non-koi8-r locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MISC/KOI8-R.TXT
 *
 * Name:             KOI8-R (RFC1489) to Unicode
 * Unicode version:  3.0
 * Table version:    1.0
 * Table format:     Format A
 * Date:             18 August 1999
 * Authors:          Helmut Richter <richter@lrz.de>
 *
 * Copyright (c) 1991-1999 Unicode, Inc.  All Rights reserved.
 *
 * This file is provided as-is by Unicode, Inc. (The Unicode Consortium).
 * No claims are made as to fitness for any particular purpose.  No
 * warranties of any kind are expressed or implied.  The recipient
 * agrees to determine applicability of information provided.  If this
 * file has been provided on optical media by Unicode, Inc., the sole
 * remedy for any claim will be exchange of defective media within 90
 * days of receipt.
 *
 * Unicode, Inc. hereby grants the right to freely use the information
 * supplied in this file in the creation of products supporting the
 * Unicode Standard, and to make copies of this file in any form for
 * internal or external distribution as long as this notice remains
 * attached.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: koi8_r.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode koi8r strings
 * @param string $string Encoded string
 * @return string Decoded string
 */
function charset_decode_koi8_r ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'koi8-r'))
        return $string;

    $koi8r = array(
        "\x80" => '&#9472;',
        "\x81" => '&#9474;',
        "\x82" => '&#9484;',
        "\x83" => '&#9488;',
        "\x84" => '&#9492;',
        "\x85" => '&#9496;',
        "\x86" => '&#9500;',
        "\x87" => '&#9508;',
        "\x88" => '&#9516;',
        "\x89" => '&#9524;',
        "\x8A" => '&#9532;',
        "\x8B" => '&#9600;',
        "\x8C" => '&#9604;',
        "\x8D" => '&#9608;',
        "\x8E" => '&#9612;',
        "\x8F" => '&#9616;',
        "\x90" => '&#9617;',
        "\x91" => '&#9618;',
        "\x92" => '&#9619;',
        "\x93" => '&#8992;',
        "\x94" => '&#9632;',
        "\x95" => '&#8729;',
        "\x96" => '&#8730;',
        "\x97" => '&#8776;',
        "\x98" => '&#8804;',
        "\x99" => '&#8805;',
        "\x9A" => '&#160;',
        "\x9B" => '&#8993;',
        "\x9C" => '&#176;',
        "\x9D" => '&#178;',
        "\x9E" => '&#183;',
        "\x9F" => '&#247;',
        "\xA0" => '&#9552;',
        "\xA1" => '&#9553;',
        "\xA2" => '&#9554;',
        "\xA3" => '&#1105;',
        "\xA4" => '&#9555;',
        "\xA5" => '&#9556;',
        "\xA6" => '&#9557;',
        "\xA7" => '&#9558;',
        "\xA8" => '&#9559;',
        "\xA9" => '&#9560;',
        "\xAA" => '&#9561;',
        "\xAB" => '&#9562;',
        "\xAC" => '&#9563;',
        "\xAD" => '&#9564;',
        "\xAE" => '&#9565;',
        "\xAF" => '&#9566;',
        "\xB0" => '&#9567;',
        "\xB1" => '&#9568;',
        "\xB2" => '&#9569;',
        "\xB3" => '&#1025;',
        "\xB4" => '&#9570;',
        "\xB5" => '&#9571;',
        "\xB6" => '&#9572;',
        "\xB7" => '&#9573;',
        "\xB8" => '&#9574;',
        "\xB9" => '&#9575;',
        "\xBA" => '&#9576;',
        "\xBB" => '&#9577;',
        "\xBC" => '&#9578;',
        "\xBD" => '&#9579;',
        "\xBE" => '&#9580;',
        "\xBF" => '&#169;',
        "\xC0" => '&#1102;',
        "\xC1" => '&#1072;',
        "\xC2" => '&#1073;',
        "\xC3" => '&#1094;',
        "\xC4" => '&#1076;',
        "\xC5" => '&#1077;',
        "\xC6" => '&#1092;',
        "\xC7" => '&#1075;',
        "\xC8" => '&#1093;',
        "\xC9" => '&#1080;',
        "\xCA" => '&#1081;',
        "\xCB" => '&#1082;',
        "\xCC" => '&#1083;',
        "\xCD" => '&#1084;',
        "\xCE" => '&#1085;',
        "\xCF" => '&#1086;',
        "\xD0" => '&#1087;',
        "\xD1" => '&#1103;',
        "\xD2" => '&#1088;',
        "\xD3" => '&#1089;',
        "\xD4" => '&#1090;',
        "\xD5" => '&#1091;',
        "\xD6" => '&#1078;',
        "\xD7" => '&#1074;',
        "\xD8" => '&#1100;',
        "\xD9" => '&#1099;',
        "\xDA" => '&#1079;',
        "\xDB" => '&#1096;',
        "\xDC" => '&#1101;',
        "\xDD" => '&#1097;',
        "\xDE" => '&#1095;',
        "\xDF" => '&#1098;',
        "\xE0" => '&#1070;',
        "\xE1" => '&#1040;',
        "\xE2" => '&#1041;',
        "\xE3" => '&#1062;',
        "\xE4" => '&#1044;',
        "\xE5" => '&#1045;',
        "\xE6" => '&#1060;',
        "\xE7" => '&#1043;',
        "\xE8" => '&#1061;',
        "\xE9" => '&#1048;',
        "\xEA" => '&#1049;',
        "\xEB" => '&#1050;',
        "\xEC" => '&#1051;',
        "\xED" => '&#1052;',
        "\xEE" => '&#1053;',
        "\xEF" => '&#1054;',
        "\xF0" => '&#1055;',
        "\xF1" => '&#1071;',
        "\xF2" => '&#1056;',
        "\xF3" => '&#1057;',
        "\xF4" => '&#1058;',
        "\xF5" => '&#1059;',
        "\xF6" => '&#1046;',
        "\xF7" => '&#1042;',
        "\xF8" => '&#1068;',
        "\xF9" => '&#1067;',
        "\xFA" => '&#1047;',
        "\xFB" => '&#1064;',
        "\xFC" => '&#1069;',
        "\xFD" => '&#1065;',
        "\xFE" => '&#1063;',
        "\xFF" => '&#1066;'
    );

    $string = str_replace(array_keys($koi8r), array_values($koi8r), $string);

    return $string;
}
