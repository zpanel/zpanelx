<?php

/**
 * Prevents users from reposting their form data after a successful logout.
 *
 * Derived from webmail.php by Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: redirect.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the redirect page */
define('PAGE_NAME', 'redirect');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/i18n.php');
require_once(SM_PATH . 'functions/strings.php');
require_once(SM_PATH . 'functions/prefs.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/plugin.php');
require_once(SM_PATH . 'functions/constants.php');
require_once(SM_PATH . 'functions/page_header.php');

// Disable Browser Caching
//
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');
$location = get_location();

sqsession_is_active();

sqsession_unregister ('user_is_logged_in');
sqsession_register ($base_uri, 'base_uri');

/* get globals we me need */
sqGetGlobalVar('login_username', $login_username);
sqGetGlobalVar('secretkey', $secretkey);
sqGetGlobalVar('js_autodetect_results', $js_autodetect_results);
if(!sqGetGlobalVar('squirrelmail_language', $squirrelmail_language) || $squirrelmail_language == '') {
    $squirrelmail_language = $squirrelmail_default_language;
}

if (!sqgetGlobalVar('mailtodata', $mailtodata)) {
    $mailtodata = '';
}


/* end of get globals */

set_up_language($squirrelmail_language, true);
/* Refresh the language cookie. */
sqsetcookie('squirrelmail_language', $squirrelmail_language, time()+2592000, $base_uri);

if (!isset($login_username)) {
    include_once(SM_PATH .  'functions/display_messages.php' );
    logout_error( _("You must be logged in to access this page.") );
    exit;
}

if (!sqsession_is_registered('user_is_logged_in')) {
    do_hook ('login_before');

    /**
     * Regenerate session id to make sure that authenticated session uses
     * different ID than one used before user authenticated.  This is a
     * countermeasure against session fixation attacks.
     * NB: session_regenerate_id() was added in PHP 4.3.2 (and new session
     *     cookie is only sent out in this call as of PHP 4.3.3), but PHP 4
     *     is not vulnerable to session fixation problems in SquirrelMail
     *     because it prioritizes $base_uri subdirectory cookies differently
     *     than PHP 5, which is otherwise vulnerable.  If we really want to,
     *     we could define our own session_regenerate_id() when one does not
     *     exist, but there seems to be no reason to do so.
     */
    if (function_exists('session_regenerate_id')) {
        session_regenerate_id();

        // re-send session cookie so we get the right parameters on it
        // (such as HTTPOnly, if necessary - PHP doesn't do this itself
        sqsetcookie(session_name(),session_id(),false,$base_uri);
    }

    $onetimepad = OneTimePadCreate(strlen($secretkey));
    $key = OneTimePadEncrypt($secretkey, $onetimepad);
    sqsession_register($onetimepad, 'onetimepad');

    /* remove redundant spaces */
    $login_username = trim($login_username);

    /* Verify that username and password are correct. */
    if ($force_username_lowercase) {
        $login_username = strtolower($login_username);
    }

    $imapConnection = sqimap_login($login_username, $key, $imapServerAddress, $imapPort, 0);

    $sqimap_capabilities = sqimap_capability($imapConnection);
    sqsession_register($sqimap_capabilities, 'sqimap_capabilities');
    $delimiter = sqimap_get_delimiter ($imapConnection);

    sqimap_logout($imapConnection);
    sqsession_register($delimiter, 'delimiter');

    $username = $login_username;
    sqsession_register ($username, 'username');
    sqsetcookie('key', $key, 0, $base_uri);

    $is_login_verified_hook = TRUE;
    do_hook ('login_verified');
    $is_login_verified_hook = FALSE;

}

/* Set the login variables. */
$user_is_logged_in = true;
$just_logged_in = true;

/* And register with them with the session. */
sqsession_register ($user_is_logged_in, 'user_is_logged_in');
sqsession_register ($just_logged_in, 'just_logged_in');

/* parse the accepted content-types of the client */
$attachment_common_types = array();
$attachment_common_types_parsed = array();
sqsession_register($attachment_common_types, 'attachment_common_types');
sqsession_register($attachment_common_types_parsed, 'attachment_common_types_parsed');


if ( sqgetGlobalVar('HTTP_ACCEPT', $http_accept, SQ_SERVER) &&
    !isset($attachment_common_types_parsed[$http_accept]) ) {
    attachment_common_parse($http_accept);
}

/* Complete autodetection of Javascript. */
$javascript_setting = getPref
    ($data_dir, $username, 'javascript_setting', SMPREF_JS_AUTODETECT);
$js_autodetect_results = (isset($js_autodetect_results) ?
    $js_autodetect_results : SMPREF_JS_OFF);
/* See if it's set to "Always on" */
$js_pref = SMPREF_JS_ON;
if ($javascript_setting != SMPREF_JS_ON){
    if ($javascript_setting == SMPREF_JS_AUTODETECT) {
        if ($js_autodetect_results == SMPREF_JS_OFF) {
            $js_pref = SMPREF_JS_OFF;
        }
    } else {
        $js_pref = SMPREF_JS_OFF;
    }
}
/* Update the prefs */
setPref($data_dir, $username, 'javascript_on', $js_pref);

/* Compute the URL to forward the user to. */
$redirect_url = 'webmail.php';

if ( sqgetGlobalVar('session_expired_location', $session_expired_location, SQ_SESSION) ) {
    sqsession_unregister('session_expired_location');
    if ( $session_expired_location == 'compose' ) {
        $compose_new_win = getPref($data_dir, $username, 'compose_new_win', 0);
        if ($compose_new_win) {
            // do not prefix $location here because $session_expired_location is set to the PAGE_NAME
            // of the last page
            $redirect_url = $session_expired_location . '.php';
        } else {
            $redirect_url = 'webmail.php?right_frame=' . urlencode($session_expired_location . '.php');
        }
    } else if ($session_expired_location != 'webmail'
            && $session_expired_location != 'left_main') {
        $redirect_url = 'webmail.php?right_frame=' . urlencode($session_expired_location . '.php');
    }
    unset($session_expired_location);
}

if($mailtodata != '') {
    $redirect_url  = $location . '/webmail.php?right_frame=compose.php&mailtodata=';
    $redirect_url .= urlencode($mailtodata);
}



/* Write session data and send them off to the appropriate page. */
session_write_close();
header("Location: $redirect_url");

/* --------------------- end main ----------------------- */

function attachment_common_parse($str) {
    global $attachment_common_types, $attachment_common_types_parsed;

    $attachment_common_types_parsed[$str] = true;

    /*
     * Replace ", " with "," and explode on that as Mozilla 1.x seems to
     * use "," to seperate whilst IE, and earlier versions of Mozilla use
     * ", " to seperate
     */

    $str = str_replace( ', ' , ',' , $str );
    $types = explode(',', $str);

    foreach ($types as $val) {
        // Ignore the ";q=1.0" stuff
        if (strpos($val, ';') !== false)
            $val = substr($val, 0, strpos($val, ';'));

        if (! isset($attachment_common_types[$val])) {
            $attachment_common_types[$val] = true;
        }
    }
    sqsession_register($attachment_common_types, 'attachment_common_types');
}

