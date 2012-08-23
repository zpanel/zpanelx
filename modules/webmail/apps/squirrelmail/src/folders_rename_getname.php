<?php

/**
 * folders_rename_getname.php
 *
 * Gets folder names and enables renaming
 * Called from folders.php
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: folders_rename_getname.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the folders_rename_getname page */
define('PAGE_NAME', 'folders_rename_getname');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/imap_mailbox.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'functions/forms.php');

/* get globals we may need */
sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
sqgetGlobalVar('old',       $old,           SQ_POST);
/* end of get globals */

if ($old == '') {
    displayPageHeader($color, 'None');

    plain_error_message(_("You have not selected a folder to rename. Please do so.").
        '<br /><a href="../src/folders.php">'._("Click here to go back").'</a>.', $color);
    exit;
}

if (substr($old, strlen($old) - strlen($delimiter)) == $delimiter) {
    $isfolder = TRUE;
    $old = substr($old, 0, strlen($old) - 1);
} else {
    $isfolder = FALSE;
}

$old = imap_utf7_decode_local($old);

// displayable mailbox format is without folder prefix on front
global $folder_prefix;
if (substr($old, 0, strlen($folder_prefix)) == $folder_prefix) {
    $displayable_old = substr($old, strlen($folder_prefix));
} else {
    $displayable_old = $old;
}

if (strpos($displayable_old, $delimiter)) {
    $old_name = substr($displayable_old, strrpos($displayable_old, $delimiter)+1);
    $parent = htmlspecialchars(substr($displayable_old, 
                                      0, 
                                      strrpos($displayable_old, $delimiter))
            . ' ' . $delimiter);
} else {
    $old_name = $displayable_old;
    $parent = '';
}


displayPageHeader($color, 'None');
echo '<br />' .
    html_tag( 'table', '', 'center', '', 'width="95%" border="0"' ) .
        html_tag( 'tr',
            html_tag( 'td', '<b>' . _("Rename a folder") . '</b>', 'center', $color[0] )
        ) .
        html_tag( 'tr' ) .
            html_tag( 'td', '', 'center', $color[4] ) .
            addForm('folders_rename_do.php', 'post', '', '', '', '', TRUE).
     _("New name:").
     '<br /><b>'. $parent . '</b>'.
     addInput('new_name', $old_name, 25) . '<br />' . "\n";
if ( $isfolder ) {
    echo addHidden('isfolder', 'true');
}
echo addHidden('orig', $old).
     addHidden('old_name', $old_name).
     '<input type="submit" value="'._("Submit")."\" />\n".
     '</form><br /></td></tr></table>';

