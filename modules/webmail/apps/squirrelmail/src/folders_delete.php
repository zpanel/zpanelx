<?php

/**
 * folders_delete.php
 *
 * Deletes folders from the IMAP server. 
 * Called from the folders.php
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: folders_delete.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the folders_delete page */
define('PAGE_NAME', 'folders_delete');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/tree.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'functions/forms.php');

/*
 *  Incoming values:
 *     $mailbox - selected mailbox from the form
 */

/* globals */
sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
sqgetGlobalVar('mailbox',   $mailbox,       SQ_POST);
if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_POST)) {
    $submitted_token = '';
}
/* end globals */

if ($mailbox == '') {
    displayPageHeader($color, 'None');

    plain_error_message(_("You have not selected a folder to delete. Please do so.").
        '<br /><a href="../src/folders.php">'._("Click here to go back").'</a>.', $color);
    exit;
}

if ( sqgetGlobalVar('backingout', $tmp, SQ_POST) ) {
    $location = get_location();
    header ("Location: $location/folders.php");
    exit;
}

if( !sqgetGlobalVar('confirmed', $tmp, SQ_POST) ) {
    displayPageHeader($color, 'None');

    // get displayable mailbox format
    global $folder_prefix;
    if (substr($mailbox,0,strlen($folder_prefix))==$folder_prefix) {
        $mailbox_unformatted_disp = substr($mailbox, strlen($folder_prefix));
    } else {
        $mailbox_unformatted_disp = $mailbox;
    }

    echo '<br />' .
        html_tag( 'table', '', 'center', '', 'width="95%" border="0"' ) .
        html_tag( 'tr',
            html_tag( 'td', '<b>' . _("Delete Folder") . '</b>', 'center', $color[0] )
        ) .
        html_tag( 'tr' ) .
        html_tag( 'td', '', 'center', $color[4] ) .
        sprintf(_("Are you sure you want to delete %s?"), str_replace(array(' ','<','>'),array('&nbsp;','&lt;','&gt;'),imap_utf7_decode_local($mailbox_unformatted_disp))).
        addForm('folders_delete.php', 'post', '', '', '', '', TRUE)."<p>\n".
        addHidden('mailbox', $mailbox).
        addSubmit(_("Yes"), 'confirmed').
        addSubmit(_("No"), 'backingout').
        '</p></form><br /></td></tr></table>';

    exit;
}

// first, validate security token
sm_validate_security_token($submitted_token, 3600, TRUE);

$imap_stream = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);

$boxes = sqimap_mailbox_list ($imap_stream);
$numboxes = count($boxes);

global $delete_folder;

if (substr($mailbox, -1) == $delimiter)
    $mailbox_no_dm = substr($mailbox, 0, strlen($mailbox) - 1);
else
    $mailbox_no_dm = $mailbox;

/** lets see if we CAN move folders to the trash.. otherwise,
    ** just delete them **/
if ((isset($delete_folder) && $delete_folder) ||
    preg_match('/^' . preg_quote($trash_folder, '/') . '.+/i', $mailbox) ) {
    $can_move_to_trash = FALSE;
}

/* Otherwise, check if trash folder exits and support sub-folders */
else {
    for ($i = 0; $i < $numboxes; $i++) {
        if ($boxes[$i]['unformatted'] == $trash_folder) {
            $can_move_to_trash = !in_array('noinferiors', $boxes[$i]['flags']);
        }
    }
}

/** First create the top node in the tree **/
for ($i = 0; $i < $numboxes; $i++) {
    if (($boxes[$i]['unformatted-dm'] == $mailbox) && (strlen($boxes[$i]['unformatted-dm']) == strlen($mailbox))) {
        $foldersTree[0]['value'] = $mailbox;
        $foldersTree[0]['doIHaveChildren'] = false;
        continue;
    }
}

/* Now create the nodes for subfolders of the parent folder
   You can tell that it is a subfolder by tacking the mailbox delimiter
   on the end of the $mailbox string, and compare to that.  */
for ($i = 0; $i < $numboxes; $i++) {
    if (substr($boxes[$i]['unformatted'], 0, strlen($mailbox_no_dm . $delimiter)) == ($mailbox_no_dm . $delimiter)) {
        addChildNodeToTree($boxes[$i]["unformatted"], $boxes[$i]['unformatted-dm'], $foldersTree);
    }
}

/** Lets start removing the folders and messages **/
if (($move_to_trash == true) && ($can_move_to_trash == true)) { /** if they wish to move messages to the trash **/
    walkTreeInPostOrderCreatingFoldersUnderTrash(0, $imap_stream, $foldersTree, $mailbox);
    walkTreeInPreOrderDeleteFolders(0, $imap_stream, $foldersTree);
} else { /** if they do NOT wish to move messages to the trash (or cannot)**/
    walkTreeInPreOrderDeleteFolders(0, $imap_stream, $foldersTree);
}

/** Log out this session **/
sqimap_logout($imap_stream);

$location = get_location();
header ("Location: $location/folders.php?success=delete");

