<?php

/**
 * in_the_pink.php
 * Name:    In the Pink
 * Date:    October 20, 2001
 * Comment: This theme generates random colors, featuring a reddish
 *          background with dark text.
 *
 * @author Jorey Bump
 * @copyright 2000-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: in_the_pink.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage themes
 */

/* seed the random number generator */
sq_mt_randomize();

for ($i = 0; $i <= 16; $i++) {
    /* background/foreground toggle */
    if ($i == 0 or $i == 3 or $i == 4 or $i == 5 or $i == 9 or $i == 10 or $i == 12 or $i == 16) {
        /* background */
        $r = mt_rand(248,255);
        $b = mt_rand(140,255);
        $g = mt_rand(128,$b);
    } else {
        /* text */
        $b = mt_rand(2,128);
        $r = mt_rand(1,$b);
        $g = mt_rand(0,$r);
    }

    /* set array element as hex string with hashmark (for HTML output) */
    $color[$i] = sprintf('#%02X%02X%02X',$r,$g,$b);
}
