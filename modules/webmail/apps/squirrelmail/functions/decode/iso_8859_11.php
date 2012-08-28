<?php

/**
 * decode/iso8859-11.php
 *
 * This file contains iso-8859-11 decoding function that is needed to read
 * iso-8859-11 encoded mails in non-iso-8859-11 locale.
 *
 * Original data taken from:
 *  ftp://ftp.unicode.org/Public/MAPPINGS/ISO8859/8859-11.TXT
 *
 *        Name:             ISO/IEC 8859-11:2001 to Unicode
 *        Unicode version:  3.2
 *        Table version:    1.0
 *        Table format:     Format A
 *        Date:             2002 October 7
 *        Authors:          Ken Whistler <kenw@sybase.com>
 *
 * Original copyright:
 *        Copyright (c) 1999 Unicode, Inc.  All Rights reserved.
 *
 *        This file is provided as-is by Unicode, Inc. (The Unicode Consortium).
 *        No claims are made as to fitness for any particular purpose.  No
 *        warranties of any kind are expressed or implied.  The recipient
 *        agrees to determine applicability of information provided.  If this
 *        file has been provided on optical media by Unicode, Inc., the sole
 *        remedy for any claim will be exchange of defective media within 90
 *        days of receipt.
 *
 *        Unicode, Inc. hereby grants the right to freely use the information
 *        supplied in this file in the creation of products supporting the
 *        Unicode Standard, and to make copies of this file in any form for
 *        internal or external distribution as long as this notice remains
 *        attached.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_8859_11.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-11 string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_11 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-11'))
        return $string;

    $iso8859_11 = array(
        "\xA0" => '&#160;',
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
        "\xFB" => '&#3675;'
    );

    $string = str_replace(array_keys($iso8859_11), array_values($iso8859_11), $string);

    return $string;
}

