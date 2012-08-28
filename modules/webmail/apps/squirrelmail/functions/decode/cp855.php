<?php

/**
 * decode/cp855.php
 *
 * This file contains cp855 decoding function that is needed to read
 * cp855 encoded mails in non-cp855 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/PC/CP855.TXT
 *   Name:     cp855_DOSCyrillic to Unicode table
 *   Unicode version: 2.0
 *   Table version: 2.00
 *   Table format:  Format A
 *   Date:          04/24/96
 *   Authors:       Lori Brownell <loribr@microsoft.com>
 *                  K.D. Chang    <a-kchang@microsoft.com>
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp855.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode a cp855-encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_cp855 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'ibm855'))
        return $string;

    $cp855 = array(
        "\x80" => '&#1106;',
        "\x81" => '&#1026;',
        "\x82" => '&#1107;',
        "\x83" => '&#1027;',
        "\x84" => '&#1105;',
        "\x85" => '&#1025;',
        "\x86" => '&#1108;',
        "\x87" => '&#1028;',
        "\x88" => '&#1109;',
        "\x89" => '&#1029;',
        "\x8a" => '&#1110;',
        "\x8b" => '&#1030;',
        "\x8c" => '&#1111;',
        "\x8d" => '&#1031;',
        "\x8e" => '&#1112;',
        "\x8f" => '&#1032;',
        "\x90" => '&#1113;',
        "\x91" => '&#1033;',
        "\x92" => '&#1114;',
        "\x93" => '&#1034;',
        "\x94" => '&#1115;',
        "\x95" => '&#1035;',
        "\x96" => '&#1116;',
        "\x97" => '&#1036;',
        "\x98" => '&#1118;',
        "\x99" => '&#1038;',
        "\x9a" => '&#1119;',
        "\x9b" => '&#1039;',
        "\x9c" => '&#1102;',
        "\x9d" => '&#1070;',
        "\x9e" => '&#1098;',
        "\x9f" => '&#1066;',
        "\xa0" => '&#1072;',
        "\xa1" => '&#1040;',
        "\xa2" => '&#1073;',
        "\xa3" => '&#1041;',
        "\xa4" => '&#1094;',
        "\xa5" => '&#1062;',
        "\xa6" => '&#1076;',
        "\xa7" => '&#1044;',
        "\xa8" => '&#1077;',
        "\xa9" => '&#1045;',
        "\xaa" => '&#1092;',
        "\xab" => '&#1060;',
        "\xac" => '&#1075;',
        "\xad" => '&#1043;',
        "\xae" => '&#171;',
        "\xaf" => '&#187;',
        "\xb0" => '&#9617;',
        "\xb1" => '&#9618;',
        "\xb2" => '&#9619;',
        "\xb3" => '&#9474;',
        "\xb4" => '&#9508;',
        "\xb5" => '&#1093;',
        "\xb6" => '&#1061;',
        "\xb7" => '&#1080;',
        "\xb8" => '&#1048;',
        "\xb9" => '&#9571;',
        "\xba" => '&#9553;',
        "\xbb" => '&#9559;',
        "\xbc" => '&#9565;',
        "\xbd" => '&#1081;',
        "\xbe" => '&#1049;',
        "\xbf" => '&#9488;',
        "\xc0" => '&#9492;',
        "\xc1" => '&#9524;',
        "\xc2" => '&#9516;',
        "\xc3" => '&#9500;',
        "\xc4" => '&#9472;',
        "\xc5" => '&#9532;',
        "\xc6" => '&#1082;',
        "\xc7" => '&#1050;',
        "\xc8" => '&#9562;',
        "\xc9" => '&#9556;',
        "\xca" => '&#9577;',
        "\xcb" => '&#9574;',
        "\xcc" => '&#9568;',
        "\xcd" => '&#9552;',
        "\xce" => '&#9580;',
        "\xcf" => '&#164;',
        "\xd0" => '&#1083;',
        "\xd1" => '&#1051;',
        "\xd2" => '&#1084;',
        "\xd3" => '&#1052;',
        "\xd4" => '&#1085;',
        "\xd5" => '&#1053;',
        "\xd6" => '&#1086;',
        "\xd7" => '&#1054;',
        "\xd8" => '&#1087;',
        "\xd9" => '&#9496;',
        "\xda" => '&#9484;',
        "\xdb" => '&#9608;',
        "\xdc" => '&#9604;',
        "\xdd" => '&#1055;',
        "\xde" => '&#1103;',
        "\xdf" => '&#9600;',
        "\xe0" => '&#1071;',
        "\xe1" => '&#1088;',
        "\xe2" => '&#1056;',
        "\xe3" => '&#1089;',
        "\xe4" => '&#1057;',
        "\xe5" => '&#1090;',
        "\xe6" => '&#1058;',
        "\xe7" => '&#1091;',
        "\xe8" => '&#1059;',
        "\xe9" => '&#1078;',
        "\xea" => '&#1046;',
        "\xeb" => '&#1074;',
        "\xec" => '&#1042;',
        "\xed" => '&#1100;',
        "\xee" => '&#1068;',
        "\xef" => '&#8470;',
        "\xf0" => '&#173;',
        "\xf1" => '&#1099;',
        "\xf2" => '&#1067;',
        "\xf3" => '&#1079;',
        "\xf4" => '&#1047;',
        "\xf5" => '&#1096;',
        "\xf6" => '&#1064;',
        "\xf7" => '&#1101;',
        "\xf8" => '&#1069;',
        "\xf9" => '&#1097;',
        "\xfa" => '&#1065;',
        "\xfb" => '&#1095;',
        "\xfc" => '&#1063;',
        "\xfd" => '&#167;',
        "\xfe" => '&#9632;',
        "\xff" => '&#160;'
    );

    $string = str_replace(array_keys($cp855), array_values($cp855), $string);

    return $string;
}

