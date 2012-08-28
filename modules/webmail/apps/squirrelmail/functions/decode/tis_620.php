<?php

/**
 * decode/tis620.php
 *
 * This file contains tis620 decoding function that is needed to read
 * tis620 encoded mails in non-tis620 locale.
 *
 * Original data taken from:
 *  http://www.inet.co.th/cyberclub/trin/thairef/tis620-iso10646.html
 *
 * Original copyright:
 *  Note: The information contained herein is provided as-is. It was
 *  complied from various references given at the end of the page.
 *  The author (trin@mozart.inet.co.th) believes all information
 *  presented here is accurate.
 *
 *     References
 *  1. [1]TIS 620-2533 Standard for Thai Character Codes for Computers
 *      (in Thai), [2]Thai Industrial Standards Institute
 *  2. [3]Thai Information Technology Standards, On-line resources at the
 *     National Electronics and Computer Technology Center (NECTEC)
 *  3. ISO/IEC 10646-1, [4]ISO/IEC JTC1/SC2
 *  4. [5]Thai block in Unicode 2.1, [6]Unicode Consortium
 *
 *  Links
 *  1. http://www.nectec.or.th/it-standards/std620/std620.htm
 *  2. http://www.tisi.go.th/
 *  3. http://www.nectec.or.th/it-standards/
 *  4. http://wwwold.dkuug.dk/JTC1/SC2/
 *  5. http://charts.unicode.org/Unicode.charts/normal/U0E00.html
 *  6. http://www.unicode.org/
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: tis_620.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode tis620 encoded strings
 * @param string $string Encoded string
 * @return string Decoded string
 */
function charset_decode_tis_620 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'tis-620'))
        return $string;

    $tis620 = array(
        "\xA0" => '&#65535;',
        "\xA1" => '&#3585;',
        "\xA2" => '&#3586;',
        "\xA3" => '&#3587;',
        "\xA4" => '&#3588;',
        "\xA5" => '&#3589;',
        "\xA6" => '&#3590;',
        "\xA7" => '&#3591;',
        "\xA8" => '&#3592;',
        "\xA9" => '&#3593;',
        "\xAA" => '&#3594;',
        "\xAB" => '&#3595;',
        "\xAC" => '&#3596;',
        "\xAD" => '&#3597;',
        "\xAE" => '&#3598;',
        "\xAF" => '&#3599;',
        "\xB0" => '&#3600;',
        "\xB1" => '&#3601;',
        "\xB2" => '&#3602;',
        "\xB3" => '&#3603;',
        "\xB4" => '&#3604;',
        "\xB5" => '&#3605;',
        "\xB6" => '&#3606;',
        "\xB7" => '&#3607;',
        "\xB8" => '&#3608;',
        "\xB9" => '&#3609;',
        "\xBA" => '&#3610;',
        "\xBB" => '&#3611;',
        "\xBC" => '&#3612;',
        "\xBD" => '&#3613;',
        "\xBE" => '&#3614;',
        "\xBF" => '&#3615;',
        "\xC0" => '&#3616;',
        "\xC1" => '&#3617;',
        "\xC2" => '&#3618;',
        "\xC3" => '&#3619;',
        "\xC4" => '&#3620;',
        "\xC5" => '&#3621;',
        "\xC6" => '&#3622;',
        "\xC7" => '&#3623;',
        "\xC8" => '&#3624;',
        "\xC9" => '&#3625;',
        "\xCA" => '&#3626;',
        "\xCB" => '&#3627;',
        "\xCC" => '&#3628;',
        "\xCD" => '&#3629;',
        "\xCE" => '&#3630;',
        "\xCF" => '&#3631;',
        "\xD0" => '&#3632;',
        "\xD1" => '&#3633;',
        "\xD2" => '&#3634;',
        "\xD3" => '&#3635;',
        "\xD4" => '&#3636;',
        "\xD5" => '&#3637;',
        "\xD6" => '&#3638;',
        "\xD7" => '&#3639;',
        "\xD8" => '&#3640;',
        "\xD9" => '&#3641;',
        "\xDA" => '&#3642;',
        "\xDB" => '&#65535;',
        "\xDC" => '&#65535;',
        "\xDD" => '&#65535;',
        "\xDE" => '&#65535;',
        "\xDF" => '&#3647;',
        "\xE0" => '&#3648;',
        "\xE1" => '&#3649;',
        "\xE2" => '&#3650;',
        "\xE3" => '&#3651;',
        "\xE4" => '&#3652;',
        "\xE5" => '&#3653;',
        "\xE6" => '&#3654;',
        "\xE7" => '&#3655;',
        "\xE8" => '&#3656;',
        "\xE9" => '&#3657;',
        "\xEA" => '&#3658;',
        "\xEB" => '&#3659;',
        "\xEC" => '&#3660;',
        "\xED" => '&#3661;',
        "\xEE" => '&#3662;',
        "\xEF" => '&#3663;',
        "\xF0" => '&#3664;',
        "\xF1" => '&#3665;',
        "\xF2" => '&#3666;',
        "\xF3" => '&#3667;',
        "\xF4" => '&#3668;',
        "\xF5" => '&#3669;',
        "\xF6" => '&#3670;',
        "\xF7" => '&#3671;',
        "\xF8" => '&#3672;',
        "\xF9" => '&#3673;',
        "\xFA" => '&#3674;',
        "\xFB" => '&#3675;',
        "\xFC" => '&#65535;',
        "\xFD" => '&#65535;',
        "\xFE" => '&#65535;',
        "\xFF" => '&#65535;'
        );

    $string = str_replace(array_keys($tis620), array_values($tis620), $string);

    return $string;
}
