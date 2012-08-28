<?php

/**
 * decode/koi8-u.php
 *
 * This file contains koi8-u decoding function that is needed to read
 * koi8-u encoded mails in non-koi8-u locale.
 *
 * Original data taken from rfc2319
 *
 * Original copyright:
 *
 * Copyright (C) The Internet Society (1998).  All Rights Reserved.
 *
 * This document and translations of it may be copied and furnished to
 * others, and derivative works that comment on or otherwise explain it
 * or assist in its implementation may be prepared, copied, published
 * and distributed, in whole or in part, without restriction of any
 * kind, provided that the above copyright notice and this paragraph are
 * included on all such copies and derivative works.  However, this
 * document itself may not be modified in any way, such as by removing
 * the copyright notice or references to the Internet Society or other
 * Internet organizations, except as needed for the purpose of
 * developing Internet standards in which case the procedures for
 * copyrights defined in the Internet Standards process must be
 * followed, or as required to translate it into languages other than
 * English.
 *
 * The limited permissions granted above are perpetual and will not be
 * revoked by the Internet Society or its successors or assigns.
 *
 * This document and the information contained herein is provided on an
 * "AS IS" basis and THE INTERNET SOCIETY AND THE INTERNET ENGINEERING
 * TASK FORCE DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING
 * BUT NOT LIMITED TO ANY WARRANTY THAT THE USE OF THE INFORMATION
 * HEREIN WILL NOT INFRINGE ANY RIGHTS OR ANY IMPLIED WARRANTIES OF
 * MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: koi8_u.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode koi8-u encoded strings
 * @param string $string Encoded string
 * @return string Decoded string
 */
function charset_decode_koi8_u ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'koi8-u'))
        return $string;

    $koi8u = array(
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
        "\xA4" => '&#1108;',
        "\xA5" => '&#9556;',
        "\xA6" => '&#1110;',
        "\xA7" => '&#1111;',
        "\xA8" => '&#9559;',
        "\xA9" => '&#9560;',
        "\xAA" => '&#9561;',
        "\xAB" => '&#9562;',
        "\xAC" => '&#9563;',
        "\xAD" => '&#1169;',
        "\xAE" => '&#9565;',
        "\xAF" => '&#9566;',
        "\xB0" => '&#9567;',
        "\xB1" => '&#9568;',
        "\xB2" => '&#9569;',
        "\xB3" => '&#1025;',
        "\xB4" => '&#1027;',
        "\xB5" => '&#9571;',
        "\xB6" => '&#1030;',
        "\xB7" => '&#1031;',
        "\xB8" => '&#9574;',
        "\xB9" => '&#9575;',
        "\xBA" => '&#9576;',
        "\xBB" => '&#9577;',
        "\xBC" => '&#9578;',
        "\xBD" => '&#1168;',
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

    $string = str_replace(array_keys($koi8u), array_values($koi8u), $string);

    return $string;
}
