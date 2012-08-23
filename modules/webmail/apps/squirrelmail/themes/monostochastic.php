<?php

/**
 * monostochastic.php
 * Name:    Monostochastic
 * Date:    October 20, 2001
 * Comment: Generates random two-color frames, featuring either
 *          a dark or light background.
 *
 * @author Jorey Bump
 * @copyright 2000-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: monostochastic.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage themes
 */

/** seed the random number generator */
sq_mt_randomize();

/** light(1) or dark(0) background toggle **/
$bg = mt_rand(0,1);

/** range delimiter **/
$bgrd = $bg * 128;

/** background **/
$cmin_b = 0 + $bgrd;
$cmax_b = 127 + $bgrd;

/** generate random color **/
$rb = mt_rand($cmin_b,$cmax_b);
$gb = mt_rand($cmin_b,$cmax_b);
$bb = mt_rand($cmin_b,$cmax_b);

/** text **/
$cmin_t = 128 - $bgrd;
$cmax_t = 255 - $bgrd;

/** generate random color **/
$rt = mt_rand($cmin_t,$cmax_t);
$gt = mt_rand($cmin_t,$cmax_t);
$bt = mt_rand($cmin_t,$cmax_t);

/** set array element as hex string with hashmark (for HTML output) **/
for ($i = 0; $i <= 16; $i++) {
    if ($i == 0 or $i == 3 or $i == 4 or $i == 5 or $i == 9 or $i == 10 or $i == 12 or $i == 16) {
        $color[$i] = sprintf('#%02X%02X%02X',$rb,$gb,$bb);
    } else {
        $color[$i] = sprintf('#%02X%02X%02X',$rt,$gt,$bt);
    }
}
