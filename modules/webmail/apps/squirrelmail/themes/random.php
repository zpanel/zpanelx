<?php

/**
 * Name:    Random Theme Every Login
 * Date:    December 24, 2001
 * Comment: Guess what this does!
 *
 * @author Tyler Akins
 * @copyright 2000-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: random.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage themes
 */

/** Initialize the random number generator */
sq_mt_randomize();

require_once(SM_PATH . 'functions/global.php');

global $theme;

if (!sqsession_is_registered('random_theme_good_theme')) {
    $good_themes = array();
    foreach ($theme as $data) {
        if (substr($data['PATH'], -18) != '/themes/random.php') {
            $good_themes[] = $data['PATH'];
        }
    }
    if (count($good_themes) == 0) {
        $good_themes[] = '../themes/default.php';
    }
    $which = mt_rand(0, count($good_themes));
    $random_theme_good_theme = $good_themes[$which];
    // remove current sm_path from theme name
    $path=preg_quote(SM_PATH,'/');
    $random_theme_good_theme=preg_replace("/^$path/",'',$random_theme_good_theme);
    // store it in session
    sqsession_register($random_theme_good_theme, 'random_theme_good_theme');
} else {
    // get random theme stored in session
    sqgetGlobalVar('random_theme_good_theme',$random_theme_good_theme);
}

@include_once (SM_PATH . $random_theme_good_theme);
