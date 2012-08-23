<?php

/**
 * empty_trash.php
 *
 * Handles deleting messages from the trash folder without
 * deleting subfolders.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: empty_trash.php 14119 2011-07-12 04:36:01Z pdontthink $
 * @package squirrelmail
 */

/** This is the empty_trash page */
define('PAGE_NAME', 'empty_trash');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/tree.php');

/* get those globals */

sqgetGlobalVar('username', $username, SQ_SESSION);
sqgetGlobalVar('key', $key, SQ_COOKIE);
sqgetGlobalVar('delimiter', $delimiter, SQ_SESSION);
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);

/* finished globals */

// first do a security check
if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_FORM))
    $submitted_token = '';
sm_validate_security_token($submitted_token, 3600, TRUE);

$imap_stream = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);

sqimap_mailbox_list($imap_stream);

$mailbox = $trash_folder;
$boxes = sqimap_mailbox_list($imap_stream);

/*
 * According to RFC2060, a DELETE command should NOT remove inferiors (sub folders)
 *    so lets go through the list of subfolders and remove them before removing the
 *    parent.
 */

/** First create the top node in the tree **/
$numboxes = count($boxes);
$foldersTree = array();
for ($i = 0; $i < $numboxes; $i++) {
    if (($boxes[$i]['unformatted'] == $mailbox) && (strlen($boxes[$i]['unformatted']) == strlen($mailbox))) {
        $foldersTree[0]['value'] = $mailbox;
        $foldersTree[0]['doIHaveChildren'] = false;
        continue;
    }
}
/*
 * Now create the nodes for subfolders of the parent folder
 * You can tell that it is a subfolder by tacking the mailbox delimiter
 *    on the end of the $mailbox string, and compare to that.
 */
$j = 0;
for ($i = 0; $i < $numboxes; $i++) {
    if (substr($boxes[$i]['unformatted'], 0, strlen($mailbox . $delimiter)) == ($mailbox . $delimiter)) {
        addChildNodeToTree($boxes[$i]['unformatted'], $boxes[$i]['unformatted-dm'], $foldersTree);
    }
}

// now lets go through the tree and delete the folders
walkTreeInPreOrderEmptyTrash(0, $imap_stream, $foldersTree);
sqimap_logout($imap_stream);

// close session properly before redirecting
session_write_close();

$location = get_location();
// force_refresh = 1 in case trash contains deleted mailboxes
header ("Location: $location/left_main.php?force_refresh=1");

