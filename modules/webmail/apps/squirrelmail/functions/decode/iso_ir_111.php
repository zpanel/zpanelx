<?php

/**
 * decode/iso-ir-111.php
 *
 * This file contains iso-ir-111 decoding function that is needed to read
 * iso-ir-111 encoded mails in non-iso-ir-111 locale.
 *
 * Original data taken from:
 *  http://crl.nmsu.edu/~mleisher/csets/ISOIR111.TXT
 *
 * Original ID: Id: ISOIR111.TXT,v 1.2 1999/08/23 18:34:15 mleisher Exp
 *   Name:             ISO IR 111/ECMA Cyrillic to Unicode 2.1 mapping table.
 * Typed in by hand from
 *    http://www.fingertipsoft.com/ref/cyrillic/charsets.html
 * Author: Mark Leisher <mleisher@crl.nmsu.edu>
 * Date: 05 March 1998
 *
 * Original copyright:
 * Copyright 1999 Computing Research Labs, New Mexico State University
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the ""Software""),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED ""AS IS"", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL
 * THE COMPUTING RESEARCH LAB OR NEW MEXICO STATE UNIVERSITY BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
 * OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_ir_111.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso-ir-111 encoded strings
 * @param string $string Encoded string
 * @return string Decoded string
 */
function charset_decode_iso_ir_111 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-ir-111'))
        return $string;

    $iso_ir_111 = array(
        "\xA0" => '&#160;',
        "\xA1" => '&#1106;',
        "\xA2" => '&#1107;',
        "\xA3" => '&#1105;',
        "\xA4" => '&#1108;',
        "\xA5" => '&#1109;',
        "\xA6" => '&#1110;',
        "\xA7" => '&#1111;',
        "\xA8" => '&#1112;',
        "\xA9" => '&#1113;',
        "\xAA" => '&#1114;',
        "\xAB" => '&#1115;',
        "\xAC" => '&#1116;',
        "\xAD" => '&#173;',
        "\xAE" => '&#1118;',
        "\xAF" => '&#1119;',
        "\xB0" => '&#8470;',
        "\xB1" => '&#1026;',
        "\xB2" => '&#1027;',
        "\xB3" => '&#1025;',
        "\xB4" => '&#1028;',
        "\xB5" => '&#1029;',
        "\xB6" => '&#1030;',
        "\xB7" => '&#1031;',
        "\xB8" => '&#1032;',
        "\xB9" => '&#1033;',
        "\xBA" => '&#1034;',
        "\xBB" => '&#1035;',
        "\xBC" => '&#1036;',
        "\xBD" => '&#164;',
        "\xBE" => '&#1038;',
        "\xBF" => '&#1039;',
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

    $string = str_replace(array_keys($iso_ir_111), array_values($iso_ir_111), $string);

    return $string;
}
