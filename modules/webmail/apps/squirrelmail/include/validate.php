<?php

/**
 * validate.php
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: validate.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/**
 * Make sure we have a page name
 *
 */
if ( !defined('PAGE_NAME') ) define('PAGE_NAME', NULL);


/** include the mime class before the session start ! otherwise we can't store
 * messages with a session_register.
 *
 * From http://www.php.net/manual/en/language.oop.serialization.php:
 *   In case this isn't clear:
 *   In 4.2 and below:
 *      session.auto_start and session objects are mutually exclusive.
 *
 * We need to load the classes before the session is started,
 * except that the session could be started automatically
 * via session.auto_start. So, we'll close the session,
 * then load the classes, and reopen the session which should
 * make everything happy.
 *
 * ** Note this means that for the 1.3.2 release, we should probably
 * recommend that people set session.auto_start=0 to avoid this altogether.
 */

session_write_close();

/**
 * Reset the $theme() array in case a value was passed via a cookie.
 * This is until theming is rewritten.
 */
global $theme;
unset($theme);
$theme=array();

/* SquirrelMail required files. */
require_once(SM_PATH . 'class/mime.class.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/i18n.php');
require_once(SM_PATH . 'functions/auth.php');

is_logged_in();

require_once(SM_PATH . 'include/load_prefs.php');
require_once(SM_PATH . 'functions/page_header.php');
require_once(SM_PATH . 'functions/prefs.php');

/* Set up the language (i18n.php was included by auth.php). */
global $username, $data_dir;
set_up_language(getPref($data_dir, $username, 'language'));

$timeZone = getPref($data_dir, $username, 'timezone');

/* Check to see if we are allowed to set the TZ environment variable.
 * We are able to do this if ...
 *   safe_mode is disabled OR
 *   safe_mode_allowed_env_vars is empty (you are allowed to set any) OR
 *   safe_mode_allowed_env_vars contains TZ
 */
$tzChangeAllowed = (!ini_get('safe_mode')) ||
                    !strcmp(ini_get('safe_mode_allowed_env_vars'),'') ||
                    preg_match('/^([\w_]+,)*TZ/', ini_get('safe_mode_allowed_env_vars'));

if ( $timeZone != SMPREF_NONE && ($timeZone != "")
    && $tzChangeAllowed ) {
    putenv("TZ=".$timeZone);
}

/**
 * php 5.1.0 added time zone functions. Set time zone with them in order
 * to prevent E_STRICT notices and allow time zone modifications in safe_mode.
 */
if (function_exists('date_default_timezone_set')) {
    if ($timeZone != SMPREF_NONE && $timeZone != "") {
        date_default_timezone_set($timeZone);
    } else {
        // interface runs on server's time zone. Remove php E_STRICT complains
        $default_timezone = @date_default_timezone_get();
        date_default_timezone_set($default_timezone);
    }
}

