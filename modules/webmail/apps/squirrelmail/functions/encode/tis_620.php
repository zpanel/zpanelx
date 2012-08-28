<?php

/**
 * tis-620 encoding functions
 *
 * takes a string of unicode entities and converts it to a tis-620 encoded string
 * Unsupported characters are replaced with ?.
 *
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: tis_620.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage encode
 */

/**
 * Converts string to tis-620
 * @param string $string text with numeric unicode entities
 * @return string tis-620 encoded text
 */
function charset_encode_tis_620 ($string) {
   // don't run encoding function, if there is no encoded characters
   if (! preg_match("'&#[0-9]+;'",$string) ) return $string;

    $string=preg_replace("/&#([0-9]+);/e","unicodetotis620('\\1')",$string);
    // $string=preg_replace("/&#[xX]([0-9A-F]+);/e","unicodetotis620(hexdec('\\1'))",$string);

    return $string;
}

/**
 * Return tis-620 symbol when unicode character number is provided
 *
 * This function is used internally by charset_encode_tis_620
 * function. It might be unavailable to other SquirrelMail functions.
 * Don't use it or make sure, that functions/encode/tis_620.php is
 * included.
 *
 * @param int $var decimal unicode value
 * @return string tis-620 character
 */
function unicodetotis620($var) {

    $tis620chars=array('3585' => "\xA1",
                       '3586' => "\xA2",
                       '3587' => "\xA3",
                       '3588' => "\xA4",
                       '3589' => "\xA5",
                       '3590' => "\xA6",
                       '3591' => "\xA7",
                       '3592' => "\xA8",
                       '3593' => "\xA9",
                       '3594' => "\xAA",
                       '3595' => "\xAB",
                       '3596' => "\xAC",
                       '3597' => "\xAD",
                       '3598' => "\xAE",
                       '3599' => "\xAF",
                       '3600' => "\xB0",
                       '3601' => "\xB1",
                       '3602' => "\xB2",
                       '3603' => "\xB3",
                       '3604' => "\xB4",
                       '3605' => "\xB5",
                       '3606' => "\xB6",
                       '3607' => "\xB7",
                       '3608' => "\xB8",
                       '3609' => "\xB9",
                       '3610' => "\xBA",
                       '3611' => "\xBB",
                       '3612' => "\xBC",
                       '3613' => "\xBD",
                       '3614' => "\xBE",
                       '3615' => "\xBF",
                       '3616' => "\xC0",
                       '3617' => "\xC1",
                       '3618' => "\xC2",
                       '3619' => "\xC3",
                       '3620' => "\xC4",
                       '3621' => "\xC5",
                       '3622' => "\xC6",
                       '3623' => "\xC7",
                       '3624' => "\xC8",
                       '3625' => "\xC9",
                       '3626' => "\xCA",
                       '3627' => "\xCB",
                       '3628' => "\xCC",
                       '3629' => "\xCD",
                       '3630' => "\xCE",
                       '3631' => "\xCF",
                       '3632' => "\xD0",
                       '3633' => "\xD1",
                       '3634' => "\xD2",
                       '3635' => "\xD3",
                       '3636' => "\xD4",
                       '3637' => "\xD5",
                       '3638' => "\xD6",
                       '3639' => "\xD7",
                       '3640' => "\xD8",
                       '3641' => "\xD9",
                       '3642' => "\xDA",
                       '3647' => "\xDF",
                       '3648' => "\xE0",
                       '3649' => "\xE1",
                       '3650' => "\xE2",
                       '3651' => "\xE3",
                       '3652' => "\xE4",
                       '3653' => "\xE5",
                       '3654' => "\xE6",
                       '3655' => "\xE7",
                       '3656' => "\xE8",
                       '3657' => "\xE9",
                       '3658' => "\xEA",
                       '3659' => "\xEB",
                       '3660' => "\xEC",
                       '3661' => "\xED",
                       '3662' => "\xEE",
                       '3663' => "\xEF",
                       '3664' => "\xF0",
                       '3665' => "\xF1",
                       '3666' => "\xF2",
                       '3667' => "\xF3",
                       '3668' => "\xF4",
                       '3669' => "\xF5",
                       '3670' => "\xF6",
                       '3671' => "\xF7",
                       '3672' => "\xF8",
                       '3673' => "\xF9",
                       '3674' => "\xFA",
                       '3675' => "\xFB");

    if (array_key_exists($var,$tis620chars)) {
        $ret=$tis620chars[$var];
    } else {
        $ret='?';
    }
    return $ret;
}
