<?php

/**
 * webmail.php -- Displays the main frameset
 *
 * This file generates the main frameset. The files that are
 * shown can be given as parameters. If the user is not logged in
 * this file will verify username and password.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: webmail.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the webmail page */
define('PAGE_NAME', 'webmail');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');

sqgetGlobalVar('username', $username, SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter, SQ_SESSION);
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);
sqgetGlobalVar('right_frame', $right_frame, SQ_GET);
if (sqgetGlobalVar('sort', $sort)) {
    $sort = (int) $sort;
}

if (sqgetGlobalVar('startMessage', $startMessage)) {
    $startMessage = (int) $startMessage;
}

if (!sqgetGlobalVar('mailbox', $mailbox)) {
    $mailbox = 'INBOX';
}

if(sqgetGlobalVar('mailtodata', $mailtodata)) {
    $mailtourl = 'mailtodata='.urlencode($mailtodata);
} else {
    $mailtourl = '';
}

// this value may be changed by a plugin, but initialize
// it first to avoid register_globals headaches
//
$right_frame_url = '';
do_hook('webmail_top');

/**
 * We'll need this to later have a noframes version
 *
 * Check if the user has a language preference, but no cookie.
 * Send him a cookie with his language preference, if there is
 * such discrepancy.
 */
$my_language = getPref($data_dir, $username, 'language');
if ($my_language != $squirrelmail_language) {
    sqsetcookie('squirrelmail_language', $my_language, time()+2592000, $base_uri);
}

set_up_language($my_language);

$output = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\">\n".
          "<html><head>\n" .
          "<meta name=\"robots\" content=\"noindex,nofollow\">\n" .
          "<title>$org_title</title>\n".
          "</head>";

$left_size = getPref($data_dir, $username, 'left_size');
$location_of_bar = getPref($data_dir, $username, 'location_of_bar');

if (isset($languages[$squirrelmail_language]['DIR']) &&
    strtolower($languages[$squirrelmail_language]['DIR']) == 'rtl') {
    $temp_location_of_bar = 'right';
} else {
    $temp_location_of_bar = 'left';
}

if ($location_of_bar == '') {
    $location_of_bar = $temp_location_of_bar;
}
$temp_location_of_bar = '';

if ($left_size == "") {
    if (isset($default_left_size)) {
         $left_size = $default_left_size;
    }
    else {
        $left_size = 200;
    }
}

if ($location_of_bar == 'right') {
    $output .= "<frameset cols=\"*, $left_size\" id=\"fs1\">\n";
}
else {
    $output .= "<frameset cols=\"$left_size, *\" id=\"fs1\">\n";
}

/*
 * There are three ways to call webmail.php
 * 1.  webmail.php
 *      - This just loads the default entry screen.
 * 2.  webmail.php?right_frame=right_main.php&sort=X&startMessage=X&mailbox=XXXX
 *      - This loads the frames starting at the given values.
 * 3.  webmail.php?right_frame=folders.php
 *      - Loads the frames with the Folder options in the right frame.
 *
 * This was done to create a pure HTML way of refreshing the folder list since
 * we would like to use as little Javascript as possible.
 *
 * The test for // should catch any attempt to include off-site webpages into
 * our frameset.
 *
 * Note that plugins are allowed to completely and freely override the URI
 * used for the "right" (content) frame, and they do so by modifying the 
 * global variable $right_frame_url.
 *
 */

if (empty($right_frame) || (strpos(urldecode($right_frame), '//') !== false)) {
    $right_frame = '';
}

if ( strpos($right_frame,'?') ) {
    $right_frame_file = substr($right_frame,0,strpos($right_frame,'?'));
} else {
    $right_frame_file = $right_frame;
}

if (empty($right_frame_url)) {
    switch($right_frame_file) {
        case 'right_main.php':
            $right_frame_url = "right_main.php?mailbox=".urlencode($mailbox)
                           . (!empty($sort)?"&amp;sort=$sort":'')
                           . (!empty($startMessage)?"&amp;startMessage=$startMessage":'');
            break;
        case 'options.php':
            $right_frame_url = 'options.php';
            break;
        case 'folders.php':
            $right_frame_url = 'folders.php';
            break;
        case 'compose.php':
            $right_frame_url = 'compose.php?' . $mailtourl;
            break;
        case '':
            $right_frame_url = 'right_main.php';
            break;
        default:
            $right_frame_url =  urlencode($right_frame);
            break;
    } 
} 

if ($location_of_bar == 'right') {
    $output .= "<frame src=\"$right_frame_url\" name=\"right\" frameborder=\"1\">\n" .
               "<frame src=\"left_main.php\" name=\"left\" frameborder=\"1\">\n";
}
else {
    $output .= "<frame src=\"left_main.php\" name=\"left\" frameborder=\"1\">\n".
               "<frame src=\"$right_frame_url\" name=\"right\" frameborder=\"1\">\n";
}
$ret = concat_hook_function('webmail_bottom', $output);
if($ret != '') {
    $output = $ret;
}
echo $output;
?>
</frameset>
</html>
