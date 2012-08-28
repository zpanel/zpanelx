<?php

/**
 * kind_of_blue.php
 * Name:    Kind of Blue
 * Date:    October 20, 2001
 * Comment: This theme generates random colors, featuring a
 *          light bluish background with dark text.
 *
 * @author Jorey Bump
 * @copyright 2000-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: kind_of_blue.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage themes
 */

/** seed the random number generator */
sq_mt_randomize();

for ($i = 0; $i <= 16; $i++) {
    /* background/foreground toggle */
    if ($i == 0 or $i == 3 or $i == 4 or $i == 5 or $i == 9 or $i == 10 or $i == 12 or $i == 16) {
        /* background */
        $b = mt_rand(248,255);
        $r = mt_rand(180,255);
        $g = mt_rand(178,$r);
    } else {
        /* text */
        $cmin = 0;
        $cmax = 128;

        /** generate random color **/
        $b = mt_rand($cmin,$cmax);
        $g = mt_rand($cmin,$cmax);
        $r = mt_rand($cmin,$cmax);
    }

    /* set array element as hex string with hashmark (for HTML output) */
    $color[$i] = sprintf('#%02X%02X%02X',$r,$g,$b);
}
