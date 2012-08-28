<?php

/**
 * delete_message.php
 *
 * Deletes a meesage from the IMAP server
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: delete_message.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'functions/imap.php');

/* get globals */
sqgetGlobalVar('username', $username, SQ_SESSION);
sqgetGlobalVar('key', $key, SQ_COOKIE);
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);

sqgetGlobalVar('message', $message, SQ_GET);
sqgetGlobalVar('mailbox', $mailbox, SQ_GET);
if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_GET)) {
    $submitted_token = '';
}
/* end globals */

if (isset($_GET['saved_draft'])) {
    $saved_draft = urlencode($_GET['saved_draft']);
}
if (isset($_GET['mail_sent'])) {
    $mail_sent = urlencode($_GET['mail_sent']);
}
if (isset($_GET['where'])) {
    $where = urlencode($_GET['where']);
}
if (isset($_GET['what'])) {
    $what = urlencode($_GET['what']);
}
if (isset($_GET['sort'])) {
    $sort = (int) $_GET['sort'];
}
if (isset($_GET['startMessage'])) {
    $startMessage = (int) $_GET['startMessage'];
}

// first, validate security token
sm_validate_security_token($submitted_token, 3600, TRUE);

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);

sqimap_mailbox_select($imapConnection, $mailbox);

sqimap_msgs_list_delete($imapConnection, $mailbox, $message);
if ($auto_expunge) {
    sqimap_mailbox_expunge($imapConnection, $mailbox, true);
}
sqimap_logout($imapConnection);

if (!isset($saved_draft)) {
    $saved_draft = '';
}

if (!isset($mail_sent)) {
    $mail_sent = '';
}

$location = get_location();

if (isset($where) && isset($what)) {
    header("Location: $location/search.php?where=" . $where .
           '&smtoken=' . sm_generate_security_token() .
           '&what=' . $what . '&mailbox=' . urlencode($mailbox));
} else {
    if (!empty($saved_draft) || !empty($mail_sent)) {
          if ($compose_new_win == '1')
              header("Location: $location/compose.php?mail_sent=$mail_sent&saved_draft=$saved_draft");
          else
              header("Location: $location/right_main.php?mail_sent=$mail_sent&saved_draft=$saved_draft");
    }
    else {
        header("Location: $location/right_main.php?sort=$sort&startMessage=$startMessage&mailbox=" .
               urlencode($mailbox));
    }
}


