<?php

/**
 * folders_rename_do.php
 *
 * Does the actual renaming of files on the IMAP server.
 * Called from the folders.php
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: folders_rename_do.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the folders_rename_do page */
define('PAGE_NAME', 'folders_rename_do');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/display_messages.php');

/* globals */
sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('orig',      $orig,          SQ_POST);
sqgetGlobalVar('old_name',  $old_name,      SQ_POST);
sqgetGlobalVar('new_name',  $new_name,      SQ_POST);
if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_POST)) {
    $submitted_token = '';
}
/* end globals */

// first, validate security token
sm_validate_security_token($submitted_token, 3600, TRUE);

$new_name = trim($new_name);

if (substr_count($new_name, '"') || substr_count($new_name, "\\") ||
    substr_count($new_name, $delimiter) || ($new_name == '')) {
    displayPageHeader($color, 'None');

    plain_error_message(_("Illegal folder name. Please select a different name.").
        '<br /><a href="../src/folders.php">'._("Click here to go back").'</a>.', $color);

    exit;
}

$orig = imap_utf7_encode_local($orig);
$old_name = imap_utf7_encode_local($old_name);
$new_name = imap_utf7_encode_local($new_name);

if ($old_name <> $new_name) {

    $imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);

    if (strpos($orig, $delimiter)) {
        $old_dir = substr($orig, 0, strrpos($orig, $delimiter));
    } else {
        $old_dir = '';
    }

    if ($old_dir != '') {
        $newone = $old_dir . $delimiter . $new_name;
    } else {
        $newone = $new_name;
    }

    // Renaming a folder doesn't rename the folder but leaves you unsubscribed
    //    at least on Cyrus IMAP servers.
    if (isset($isfolder)) {
        $newone = $newone.$delimiter;
        $orig = $orig.$delimiter;
    }
    sqimap_mailbox_rename( $imapConnection, $orig, $newone );

    // Log out this session 
    sqimap_logout($imapConnection);

}

header ('Location: ' . get_location() . '/folders.php?success=rename');

