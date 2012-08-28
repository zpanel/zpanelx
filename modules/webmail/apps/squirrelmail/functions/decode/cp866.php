<?php

/**
 * decode/cp866.php
 *
 * This file contains cp866 decoding function that is needed to read
 * cp866 encoded mails in non-cp866 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/

    Name:     cp866_DOSCyrillicRussian to Unicode table
    Unicode version: 2.0
    Table version: 2.00
    Table format:  Format A
    Date:          04/24/96
    Authors:       Lori Brownell <loribr@microsoft.com>
                   K.D. Chang    <a-kchang@microsoft.com>
    The entries are in cp866_DOSCyrillicRussian order
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: cp866.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
*/

/**
 * Decode a cp866-encoded string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_cp866 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'ibm866'))
        return $string;

    $cp866 = array(
        "\x80" => '&#1040;',
        "\x81" => '&#1041;',
        "\x82" => '&#1042;',
        "\x83" => '&#1043;',
        "\x84" => '&#1044;',
        "\x85" => '&#1045;',
        "\x86" => '&#1046;',
        "\x87" => '&#1047;',
        "\x88" => '&#1048;',
        "\x89" => '&#1049;',
        "\x8a" => '&#1050;',
        "\x8b" => '&#1051;',
        "\x8c" => '&#1052;',
        "\x8d" => '&#1053;',
        "\x8e" => '&#1054;',
        "\x8f" => '&#1055;',
        "\x90" => '&#1056;',
        "\x91" => '&#1057;',
        "\x92" => '&#1058;',
        "\x93" => '&#1059;',
        "\x94" => '&#1060;',
        "\x95" => '&#1061;',
        "\x96" => '&#1062;',
        "\x97" => '&#1063;',
        "\x98" => '&#1064;',
        "\x99" => '&#1065;',
        "\x9a" => '&#1066;',
        "\x9b" => '&#1067;',
        "\x9c" => '&#1068;',
        "\x9d" => '&#1069;',
        "\x9e" => '&#1070;',
        "\x9f" => '&#1071;',
        "\xa0" => '&#1072;',
        "\xa1" => '&#1073;',
        "\xa2" => '&#1074;',
        "\xa3" => '&#1075;',
        "\xa4" => '&#1076;',
        "\xa5" => '&#1077;',
        "\xa6" => '&#1078;',
        "\xa7" => '&#1079;',
        "\xa8" => '&#1080;',
        "\xa9" => '&#1081;',
        "\xaa" => '&#1082;',
        "\xab" => '&#1083;',
        "\xac" => '&#1084;',
        "\xad" => '&#1085;',
        "\xae" => '&#1086;',
        "\xaf" => '&#1087;',
        "\xb0" => '&#9617;',
        "\xb1" => '&#9618;',
        "\xb2" => '&#9619;',
        "\xb3" => '&#9474;',
        "\xb4" => '&#9508;',
        "\xb5" => '&#9569;',
        "\xb6" => '&#9570;',
        "\xb7" => '&#9558;',
        "\xb8" => '&#9557;',
        "\xb9" => '&#9571;',
        "\xba" => '&#9553;',
        "\xbb" => '&#9559;',
        "\xbc" => '&#9565;',
        "\xbd" => '&#9564;',
        "\xbe" => '&#9563;',
        "\xbf" => '&#9488;',
        "\xc0" => '&#9492;',
        "\xc1" => '&#9524;',
        "\xc2" => '&#9516;',
        "\xc3" => '&#9500;',
        "\xc4" => '&#9472;',
        "\xc5" => '&#9532;',
        "\xc6" => '&#9566;',
        "\xc7" => '&#9567;',
        "\xc8" => '&#9562;',
        "\xc9" => '&#9556;',
        "\xca" => '&#9577;',
        "\xcb" => '&#9574;',
        "\xcc" => '&#9568;',
        "\xcd" => '&#9552;',
        "\xce" => '&#9580;',
        "\xcf" => '&#9575;',
        "\xd0" => '&#9576;',
        "\xd1" => '&#9572;',
        "\xd2" => '&#9573;',
        "\xd3" => '&#9561;',
        "\xd4" => '&#9560;',
        "\xd5" => '&#9554;',
        "\xd6" => '&#9555;',
        "\xd7" => '&#9579;',
        "\xd8" => '&#9578;',
        "\xd9" => '&#9496;',
        "\xda" => '&#9484;',
        "\xdb" => '&#9608;',
        "\xdc" => '&#9604;',
        "\xdd" => '&#9612;',
        "\xde" => '&#9616;',
        "\xdf" => '&#9600;',
        "\xe0" => '&#1088;',
        "\xe1" => '&#1089;',
        "\xe2" => '&#1090;',
        "\xe3" => '&#1091;',
        "\xe4" => '&#1092;',
        "\xe5" => '&#1093;',
        "\xe6" => '&#1094;',
        "\xe7" => '&#1095;',
        "\xe8" => '&#1096;',
        "\xe9" => '&#1097;',
        "\xea" => '&#1098;',
        "\xeb" => '&#1099;',
        "\xec" => '&#1100;',
        "\xed" => '&#1101;',
        "\xee" => '&#1102;',
        "\xef" => '&#1103;',
        "\xf0" => '&#1025;',
        "\xf1" => '&#1105;',
        "\xf2" => '&#1028;',
        "\xf3" => '&#1108;',
        "\xf4" => '&#1031;',
        "\xf5" => '&#1111;',
        "\xf6" => '&#1038;',
        "\xf7" => '&#1118;',
        "\xf8" => '&#176;',
        "\xf9" => '&#8729;',
        "\xfa" => '&#183;',
        "\xfb" => '&#8730;',
        "\xfc" => '&#8470;',
        "\xfd" => '&#164;',
        "\xfe" => '&#9632;',
        "\xff" => '&#160;'
    );

    $string = str_replace(array_keys($cp866), array_values($cp866), $string);

    return $string;
}
