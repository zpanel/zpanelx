<?php

/**
 * Name:    Greenhouse Effect
 * Date:    October 20, 2001
 * Comment: This theme generates random colors, featuring a
 *          light greenish background.
 *
 * @author Joey Bump
 * @copyright 2000-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: greenhouse_effect.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage themes
 */

/** seed the random number generator **/
sq_mt_randomize();

for ($i = 0; $i <= 16; $i++) {
    /* background/foreground toggle **/
    if ($i == 0 || $i == 3 || $i == 4 || $i == 5
         || $i == 9 || $i == 10 || $i == 12 || $i == 16) {
        /* background */
        $g = mt_rand(248,255);
        $r = mt_rand(110,248);
        $b = mt_rand(109,$r);
    } else {
        /* text */
        $cmin = 0;
        $cmax = 96;

        /** generate random color **/
        $b = mt_rand($cmin,$cmax);
        $g = mt_rand($cmin,$cmax);
        $r = mt_rand($cmin,$cmax);
    }

    /** set array element as hex string with hashmark (for HTML output) **/
    $color[$i] = sprintf('#%02X%02X%02X',$r,$g,$b);
}
