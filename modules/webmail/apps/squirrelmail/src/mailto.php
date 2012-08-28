<?php

/**
 * mailto.php -- mailto: url handler
 *
 * This page facilitates handling mailto: links in SquirrelMail.  It checks 
 * to see if we're logged in, and if we are, it refers the user to the
 * compose screen (embedded in a normal, full SquirrelMail interface) with 
 * the mailto: data auto-populated in the corresponding fields.  If there
 * is no user currently logged in, the user is redirected to the login screen
 * first, but after login, the compose screen is shown with the correct
 * fields pre-populated.
 *
 * If the administrator desires, $compose_only can be set to TRUE, in which 
 * case only a compose screen will show, not embedded in the normal 
 * SquirrelMail interface.
 *
 * If the administrator wants to force a re-login every time a mailto: link
 * is clicked on (no matter if a user was already logged in), set $force_login
 * to TRUE.
 *
 * Use the following URI when configuring a computer to handle mailto: links
 * by using SquirrelMail:
 *
 *  http://<your server>/<squirrelmail base dir>/src/mailto.php?emailaddress=%1
 *
 * see ../contrib/squirrelmail.mailto.NT2KXP.reg for a Windows Registry file
 * that will set this up in the most robust manner.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: mailto.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the mailto page */
define('PAGE_NAME', 'mailto');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'functions/global.php');


// Force users to login each time?  Setting this to TRUE does NOT mean 
// that if no user is logged in that it won't require a correct login 
// first!  Instead, setting it to TRUE will log out anyone currently
// logged in and force a re-login.  Setting this to FALSE will still
// require a login if no one is logged in, but it will allow you to go
// directly to compose your message if you are already logged in.  
//
// Note, however, that depending on how the client browser manages 
// sessions and how the client operating system is set to handle 
// mailto: links, you may have to log in every time no matter what
// (IE under WinXP appears to pop up a new window and thus always 
// start a new session; Firefox under WinXP seems to start a new tab 
// which will find a current login if one exists). 
//
$force_login = FALSE;


// Open only the compose window, meaningless if $force_login is TRUE
//
$compose_only = FALSE;


// Disable Browser Caching
//
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');

$trtable = array('cc'           => 'cc',
                 'bcc'          => 'bcc',
                 'body'         => 'body',
                 'subject'      => 'subject');
$url = '';

$data = array();

if (sqgetGlobalVar('emailaddress', $emailaddress)) {
    $emailaddress = trim($emailaddress);
    if (stristr($emailaddress, 'mailto:')) {
        $emailaddress = substr($emailaddress, 7);
    }
    if (strpos($emailaddress, '?') !== FALSE) {
        list($emailaddress, $a) = explode('?', $emailaddress, 2);
        if (strlen(trim($a)) > 0) {
            $a = explode('=', $a, 2);
            $data[strtolower($a[0])] = $a[1];
        }
    }
    $data['to'] = $emailaddress;

    /* CC, BCC, etc could be any case, so we'll fix them here */
    foreach($_GET as $k=>$g) {
        $k = strtolower($k);
        if (isset($trtable[$k])) {
            $k = $trtable[$k];
            $data[$k] = $g;
        }
    }
}
sqsession_is_active();

if (!$force_login && sqsession_is_registered('user_is_logged_in')) {
    if ($compose_only) {
        $redirect = 'compose.php?mailtodata=' . urlencode(serialize($data));
    } else {
        $redirect = 'webmail.php?right_frame=compose.php&mailtodata=' . urlencode(serialize($data));
    }
} else {
    $redirect = 'login.php?mailtodata=' . urlencode(serialize($data));
}

session_write_close();
header('Location: ' . get_location() . '/' . $redirect);
