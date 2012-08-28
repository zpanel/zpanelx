<?php

/**
 * decode/iso8859-1.php
 *
 * This file contains iso-8859-1 decoding function that is needed to read
 * iso-8859-1 encoded mails in non-iso-8859-1 locale.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: iso_8859_1.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage decode
 */

/**
 * Decode iso8859-1 string
 * @param string $string Encoded string
 * @return string $string Decoded string
 */
function charset_decode_iso_8859_1 ($string) {
    // don't do decoding when there are no 8bit symbols
    if (! sq_is8bit($string,'iso-8859-1'))
        return $string;

    $string = preg_replace("/([\201-\237])/e","'&#' . ord('\\1') . ';'",$string);

    /* I don't want to use 0xA0 (\240) in any ranges. RH73 may dislike it */
    $string = str_replace("\240", '&#160;', $string);

    $string = preg_replace("/([\241-\377])/e","'&#' . ord('\\1') . ';'",$string);
    return $string;
}

