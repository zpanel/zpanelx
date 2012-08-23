<?php

/**
 * right_main.php
 *
 * This is where the mailboxes are listed. This controls most of what
 * goes on in SquirrelMail.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: right_main.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the right_main page */
define('PAGE_NAME', 'right_main');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/date.php');
require_once(SM_PATH . 'functions/mime.php');
require_once(SM_PATH . 'functions/mailbox_display.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'functions/html.php');

/***********************************************************
 * incoming variables from URL:                            *
 *   $sort             Direction to sort by date           *
 *                        values:  0  -  descending order  *
 *                        values:  1  -  ascending order   *
 *   $startMessage     Message to start at                 *
 *    $mailbox          Full Mailbox name                  *
 *                                                         *
 * incoming from cookie:                                   *
 *    $key              pass                               *
 * incoming from session:                                  *
 *    $username         duh                                *
 *                                                         *
 ***********************************************************/

// Disable Browser Caching //
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');


/* lets get the global vars we may need */
sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
sqgetGlobalVar('base_uri',  $base_uri,      SQ_SESSION);

sqgetGlobalVar('mailbox',   $mailbox);
sqgetGlobalVar('lastTargetMailbox', $lastTargetMailbox, SQ_SESSION);
sqgetGlobalVar('numMessages'      , $numMessages,       SQ_SESSION);
sqgetGlobalVar('session',           $session,           SQ_GET);
sqgetGlobalVar('note',              $note,              SQ_GET);
sqgetGlobalVar('use_mailbox_cache', $use_mailbox_cache, SQ_GET);

if ( sqgetGlobalVar('startMessage', $temp) ) {
    $startMessage = (int) $temp;
}
if ( sqgetGlobalVar('PG_SHOWNUM', $temp) ) {
  $PG_SHOWNUM = (int) $temp;
}
if ( sqgetGlobalVar('PG_SHOWALL', $temp, SQ_GET) ) {
  $PG_SHOWALL = (int) $temp;
}
if ( sqgetGlobalVar('newsort', $temp, SQ_GET) ) {
  $newsort = (int) $temp;
}
if ( !sqgetGlobalVar('preselected', $preselected, SQ_GET) || !is_array($preselected)) {
  $preselected = array();
} else {
  $preselected = array_keys($preselected);
}
if ( sqgetGlobalVar('checkall', $temp, SQ_GET) ) {
  $checkall = (int) $temp;
}
if ( sqgetGlobalVar('set_thread', $temp, SQ_GET) ) {
  $set_thread = (int) $temp;
}
if ( !sqgetGlobalVar('composenew', $composenew, SQ_GET) ) {
    $composenew = false;
}
/* end of get globals */

/* Open a connection on the imap port (143) */

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);

if (isset($PG_SHOWALL)) {
    if ($PG_SHOWALL) {
       $PG_SHOWNUM=999999;
       $show_num=$PG_SHOWNUM;
       sqsession_register($PG_SHOWNUM, 'PG_SHOWNUM');
    }
    else {
       sqsession_unregister('PG_SHOWNUM');
       unset($PG_SHOWNUM);
    }
}
else if( isset( $PG_SHOWNUM ) ) {
    $show_num = $PG_SHOWNUM;
}

if (!isset($show_num) || empty($show_num) || ($show_num == 0)) {
    setPref($data_dir, $username, 'show_num' , 15);
    $show_num = 15;
}

if (isset($newsort) && $newsort != $sort) {
    setPref($data_dir, $username, 'sort', $newsort);
}



/* If the page has been loaded without a specific mailbox, */
/* send them to the inbox                                  */
if (!isset($mailbox)) {
    $mailbox = 'INBOX';
    $startMessage = 1;
}


if (!isset($startMessage) || ($startMessage == '')) {
    $startMessage = 1;
}

/* decide if we are thread sorting or not */
if (!empty($allow_thread_sort) && ($allow_thread_sort == TRUE)) {
    if (isset($set_thread)) {
        if ($set_thread == 1) {
            setPref($data_dir, $username, "thread_$mailbox", 1);
            $thread_sort_messages = '1';
        }
        elseif ($set_thread == 2)  {
            setPref($data_dir, $username, "thread_$mailbox", 0);
            $thread_sort_messages = '0';
        }
    }
    else {
        $thread_sort_messages = getPref($data_dir, $username, "thread_$mailbox");
    }
}
else {
    $thread_sort_messages = 0;
}

sqimap_mailbox_select($imapConnection, $mailbox);

// the preg_match() is a fix for Dovecot wherein UIDs can be bigger than
// normal integers - this isn't in 1.4 yet, but when adding new code, why not...
if (sqgetGlobalVar('unread_passed_id', $unread_passed_id, SQ_GET)
 && preg_match('/^[0-9]+$/', $unread_passed_id)) {
    sqimap_toggle_flag($imapConnection, $unread_passed_id, '\\Seen', false, true);
}

if ($composenew) {
    $comp_uri = SM_PATH . 'src/compose.php?mailbox='. urlencode($mailbox).
        "&session=" .urlencode($session);
    displayPageHeader($color, $mailbox, "comp_in_new('$comp_uri');", false);
} else {
    displayPageHeader($color, $mailbox);
}

do_hook('right_main_after_header');
if (isset($note)) {
    echo html_tag( 'div', '<b>' . htmlspecialchars($note) .'</b>', 'center' ) . "<br />\n";
}

if ( sqgetGlobalVar('just_logged_in', $just_logged_in, SQ_SESSION) ) {
    if ($just_logged_in == true) {
        $just_logged_in = false;
        sqsession_register($just_logged_in, 'just_logged_in');

        if (strlen(trim($motd)) > 0) {
            echo html_tag( 'table',
                        html_tag( 'tr',
                            html_tag( 'td',
                                html_tag( 'table',
                                    html_tag( 'tr',
                                        html_tag( 'td', $motd, 'center' )
                                    ) ,
                                '', $color[4], 'width="100%" cellpadding="5" cellspacing="1" border="0"' )
                             )
                        ) ,
                    'center', $color[9], 'width="70%" cellpadding="0" cellspacing="3" border="0"' );
        }
    }
}

if (isset($newsort)) {
    $sort = $newsort;
    sqsession_register($sort, 'sort');
}

/*********************************************************************
 * Check to see if we can use cache or not. Currently the only time  *
 * when you will not use it is when a link on the left hand frame is *
 * used. Also check to make sure we actually have the array in the   *
 * registered session data.  :)                                      *
 *********************************************************************/
if (! isset($use_mailbox_cache)) {
    $use_mailbox_cache = 0;
}


if ($use_mailbox_cache && sqsession_is_registered('msgs')) {
    showMessagesForMailbox($imapConnection, $mailbox, $numMessages, $startMessage, $sort, $color, $show_num, $use_mailbox_cache);
} else {
    if (sqsession_is_registered('msgs')) {
        unset($msgs);
    }

    if (sqsession_is_registered('msort')) {
        unset($msort);
    }

    if (sqsession_is_registered('numMessages')) {
        unset($numMessages);
    }

    $numMessages = sqimap_get_num_messages ($imapConnection, $mailbox);

    // set 8th argument to false in order to make sure that cache is not used.
    showMessagesForMailbox($imapConnection, $mailbox, $numMessages,
                           $startMessage, $sort, $color, $show_num,
                           false);

    if (sqsession_is_registered('msgs') && isset($msgs)) {
        sqsession_register($msgs, 'msgs');
    }

    if (sqsession_is_registered('msort') && isset($msort)) {
        sqsession_register($msort, 'msort');
    }

    sqsession_register($numMessages, 'numMessages');
}
do_hook('right_main_bottom');
sqimap_logout ($imapConnection);

echo '</body></html>';

