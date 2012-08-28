<?php

/**
 * search.php
 *
 * IMAP search page
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: search.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage search
 */

/** This is the search page */
define('PAGE_NAME', 'search');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/** SquirrelMail required files.
 */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/imap_search.php');
require_once(SM_PATH . 'functions/imap_mailbox.php');
require_once(SM_PATH . 'functions/strings.php');
require_once(SM_PATH . 'functions/forms.php');

global $allow_thread_sort;

/* get globals we may need */

sqgetGlobalVar('username', $username, SQ_SESSION);
sqgetGlobalVar('key', $key, SQ_COOKIE);
sqgetGlobalVar('delimiter', $delimiter, SQ_SESSION);
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);
sqgetGlobalVar('composenew' , $composenew, SQ_FORM);
sqgetGlobalVar('composesession' , $composesession , SQ_SESSION);

if (!sqgetGlobalVar('mailbox',$mailbox,SQ_GET)) {
    unset($mailbox);
}
if (!sqgetGlobalVar('submit',$submit,SQ_GET)) {
    $submit = '';
}
if (!sqgetGlobalVar('what',$what,SQ_GET)) {
    $what='';
}
if (! sqgetGlobalVar('where',$where,SQ_GET) ||
    ! in_array( $where, array('BODY','TEXT','SUBJECT','FROM','CC','TO'))) {
    // make sure that 'where' is one if standard IMAP SEARCH keywords
    if (isset($mailbox) && isSentMailbox($mailbox, TRUE))
        $where = 'TO';
    else
        $where = 'FROM';
}
if ( !sqgetGlobalVar('preselected', $preselected, SQ_GET) || !is_array($preselected)) {
  $preselected = array();
} else {
  $preselected = array_keys($preselected);
}
if (!sqgetGlobalVar('checkall',$checkall,SQ_GET)) {
    unset($checkall);
}
if (sqgetGlobalVar('count',$count,SQ_GET)) {
    $count = (int) $count;
} else {
    unset($count);
}
if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_GET)) {
    $submitted_token = '';
}
/* end of get globals */

/*  here are some functions, could go in imap_search.php
    this was here, pretty handy  */
function s_opt( $val, $sel, $tit ) {
    echo "            <option value=\"$val\"";
    if ( $sel == $val ) {
        echo ' selected="selected"';
    }
    echo  ">$tit</option>\n";
}

/*  function to get the recent searches and put them in the attributes array  */
function get_recent($username, $data_dir) {
    $attributes = array();
    $types = array('search_what', 'search_where', 'search_folder');
    $recent_count = getPref($data_dir, $username, 'search_memory', 0);
    for ($x=1;$x<=$recent_count;$x++) {
        reset($types);
        foreach ($types as $key) {
            $attributes[$key][$x] = getPref($data_dir, $username, $key.$x, "");
        }
    }
    return $attributes;
}

/*  function to get the saved searches and put them in the saved_attributes array  */
function get_saved($username, $data_dir) {
    $saved_attributes = array();
    $types = array('saved_what', 'saved_where', 'saved_folder');
    foreach ($types as $key) {
        for ($x=1;;$x++) {
            $prefval = getPref($data_dir, $username, $key."$x", "");
            if ($prefval == "") {
                break;
            } else {
                $saved_attributes[$key][$x] = $prefval;
            }
        }
    }
    return $saved_attributes;
}

/*  function to update recent pref arrays  */
function update_recent($what, $where, $mailbox, $username, $data_dir) {
    $attributes = array();
    $types = array('search_what', 'search_where', 'search_folder');
    $input = array($what, $where, $mailbox);
    $attributes = get_recent( $username, $data_dir);
    reset($types);
    $dupe = 'no';
    for ($i=1;$i<=count($attributes['search_what']);$i++) {
        if (isset($attributes['search_what'][$i])) {
            if ($what == $attributes['search_what'][$i] &&
                $where == $attributes['search_where'][$i] &&
                $mailbox == $attributes['search_folder'][$i]) {
                    $dupe = 'yes';
            }
        }
    }
    if ($dupe == 'no') {
        $i = 0;
        foreach ($types as $key) {
            array_push ($attributes[$key], $input[$i]);
            array_shift ($attributes[$key]);
            $i++;
        }
        $recent_count = getPref($data_dir, $username, 'search_memory', 0);
        $n=0;
        for ($i=1;$i<=$recent_count;$i++) {
            reset($types);
            foreach ($types as $key) {
                setPref($data_dir, $username, $key.$i, $attributes[$key][$n]);
            }
            $n++;
        }
    }
}

/*  function to forget a recent search  */
function forget_recent($forget_index, $username, $data_dir) {
    $attributes = array();
    $types = array('search_what', 'search_where', 'search_folder');
    $attributes = get_recent( $username, $data_dir);
    reset($types);
    foreach ($types as $key) {
        array_splice($attributes[$key], $forget_index - 1, 1);
        array_unshift($attributes[$key], '');
    }
    reset($types);
    $recent_count = getPref($data_dir, $username, 'search_memory', 0);
    $n=0;
    for ($i=1;$i<=$recent_count;$i++) {
        reset($types);
        foreach ($types as $key) {
            setPref($data_dir, $username, $key.$i, $attributes[$key][$n]);
        }
        $n++;
    }
}

/*  function to delete a saved search  */
function delete_saved($delete_index, $username, $data_dir) {
    $types = array('saved_what', 'saved_where', 'saved_folder');
    $attributes = get_saved($username, $data_dir);
    foreach ($types as $key) {
        array_splice($attributes[$key], $delete_index, 1);
    }
    reset($types);
    $n=0;
    $saved_count = count($attributes['saved_what']);
    $last_element = $saved_count + 1;
        for ($i=1;$i<=$saved_count;$i++) {
            reset($types);
            foreach ($types as $key) {
                setPref($data_dir, $username, $key.$i, $attributes[$key][$n]);
            }
        $n++;
        }
    reset($types);
    foreach($types as $key) {
    removePref($data_dir, $username, $key.$last_element);
    }
}

/*  function to save a search from recent to saved  */
function save_recent($save_index, $username, $data_dir) {
    $attributes = array();
    $types = array('search_what', 'search_where', 'search_folder');
    $saved_types = array(0 => 'saved_what', 1 => 'saved_where', 2 => 'saved_folder');
    $saved_array = get_saved($username, $data_dir);
    $save_index = $save_index -1;
    if (isset($saved_array['saved_what'])) {
        $saved_count = (count($saved_array['saved_what']) + 1);
    } else {
        // there are no saved searches. Function is used to save first search
        $saved_count = 1;
    }
    $attributes = get_recent ($username, $data_dir);
    $n = 0;
    foreach ($types as $key) {
        $slice = array_slice($attributes[$key], $save_index, 1);
        $name = $saved_types[$n];
        setPref($data_dir, $username, $name.$saved_count, $slice[0]);
        $n++;
    }
}

function printSearchMessages($msgs,$mailbox, $cnt, $imapConnection, $where, $what, $usecache = false, $newsort = false) {
    global $sort, $color, $allow_server_sort, $allow_server_thread;

    if ($cnt > 0) {
        if ((!empty($allow_server_sort) && $allow_server_sort) || (!empty($allow_server_thread) && $allow_server_thread)) {
            $msort = $msgs;
        } else {
            $msort = calc_msort($msgs, $sort, $mailbox);
        }

        if ( $mailbox == 'INBOX' ) {
            $showbox = _("INBOX");
        } else {
            $showbox = imap_utf7_decode_local($mailbox);
        }
        echo html_tag( 'div', '<b><big>' . _("Folder:") . ' '.
            htmlspecialchars($showbox) .'</big></b>','center') . "\n";

        $msg_cnt_str = get_msgcnt_str(1, $cnt, $cnt);
        $toggle_all = get_selectall_link(1, $sort);

        $safe_name = preg_replace("/[^0-9A-Za-z_]/", '_', $mailbox);
        $form_name = "FormMsgs" . $safe_name;
        echo '<form name="' . $form_name . '" method="post" action="move_messages.php">' ."\n" .
             '<input type="hidden" name="mailbox" value="'.htmlspecialchars($mailbox).'">' . "\n" .
             '<input type="hidden" name="startMessage" value="1">' . "\n" .
             addHidden('smtoken', sm_generate_security_token()) . "\n";

        echo '<table border="0" width="100%" cellpadding="0" cellspacing="0">';
        echo '<tr><td>';

        mail_message_listing_beginning($imapConnection, $mailbox, $sort,
                                       $msg_cnt_str, $toggle_all, 1);

        echo '</td></tr>';
        echo '<tr><td height="5" bgcolor="'.$color[4].'"></td></tr>';
        echo '<tr><td>';
        echo '    <table width="100%" cellpadding="1" cellspacing="0" align="center"'.' border="0" bgcolor="'.$color[9].'">';
        echo '     <tr><td>';
        echo '       <table width="100%" cellpadding="1" cellspacing="0" align="center" border="0" bgcolor="'.$color[5].'">';
        echo '<tr><td>';

        printHeader($mailbox, 6, $color, false);

        displayMessageArray($imapConnection, $cnt, 1,
            $msort, $mailbox, $sort, $color, $cnt, $where, $what);

        echo '</td></tr></table></td></tr></table>';
        mail_message_listing_end($cnt, '', $msg_cnt_str, $color);
        echo "\n</table></form>\n\n";
    }
}

/* ------------------------ main ------------------------ */

/*  reset these arrays on each page load just in case  */
$attributes = array ();
$saved_attributes = array ();
$search_all = 'none';
$perbox_count = array ();
$recent_count = getPref($data_dir, $username, 'search_memory', 0);

/*  get mailbox names  */
$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$boxes = sqimap_mailbox_list($imapConnection);

/*  set current mailbox to INBOX if none was selected or if page
    was called to search all folders.  */
if ( !isset($mailbox) || $mailbox == 'None' || $mailbox == '' ) {
    $mailbox = $boxes[0]['unformatted'];
}
if ($mailbox == 'All Folders') {
    $search_all = 'all';
}

// the preg_match() is a fix for Dovecot wherein UIDs can be bigger than
// normal integers - this isn't in 1.4 yet, but when adding new code, why not...
if (sqgetGlobalVar('unread_passed_id', $unread_passed_id, SQ_GET)
 && preg_match('/^[0-9]+$/', $unread_passed_id)) {
    sqimap_mailbox_select($imapConnection, $mailbox);
    sqimap_toggle_flag($imapConnection, $unread_passed_id, '\\Seen', false, true);
}

if (isset($composenew) && $composenew) {
    $comp_uri = "../src/compose.php?mailbox=". urlencode($mailbox).
        "&amp;session=$composesession&amp";
    displayPageHeader($color, $mailbox, "comp_in_new('$comp_uri');", false);
} else {
    displayPageHeader($color, $mailbox);
}
/*  See how the page was called and fire off correct function  */
if (empty($submit) && !empty($what)) {
    $submit = _("Search");
}

// need to verify security token if user wants to do anything
if (!empty($submit)) {
    sm_validate_security_token($submitted_token, 3600, TRUE);
}

if ($submit == _("Search") && !empty($what)) {
    if ($recent_count > 0) {
        update_recent($what, $where, $mailbox, $username, $data_dir);
    }
}
elseif ($submit == 'forget' && isset($count)) {
    forget_recent($count, $username, $data_dir);
}
elseif ($submit == 'save' && isset($count)) {
    save_recent($count, $username, $data_dir);
}
elseif ($submit == 'delete' && isset($count)) {
    delete_saved($count, $username, $data_dir);
}

do_hook('search_before_form');

echo html_tag( 'table',
         html_tag( 'tr', "\n" .
             html_tag( 'td', '<b>' . _("Search") . '</b>', 'center', $color[0] )
         ) ,
     '', '', 'width="100%"') . "\n";

/*  update the recent and saved searches from the pref files  */
$attributes = get_recent($username, $data_dir);
$saved_attributes = get_saved($username, $data_dir);
if (isset($saved_attributes['saved_what'])) {
    $saved_count = count($saved_attributes['saved_what']);
} else {
    $saved_count = 0;
}
$count_all = 0;

/* Saved Search Table */
if ($saved_count > 0) {
    echo "<br />\n"
    . html_tag( 'table', '', 'center', $color[9], 'width="95%" cellpadding="1" cellspacing="1" border="0"' )
    . html_tag( 'tr',
          html_tag( 'td', '<b>'._("Saved Searches") . '</b>', 'center' )
      )
    . html_tag( 'tr' )
    . html_tag( 'td' )
    . html_tag( 'table', '', 'center', '', 'width="100%" cellpadding="2" cellspacing="2" border="0"' );
    for ($i=0; $i < $saved_count; ++$i) {
        if ($i % 2) {
            echo html_tag( 'tr', '', '', $color[0] );
        } else {
            echo html_tag( 'tr', '', '', $color[4] );
        }
        echo html_tag( 'td', htmlspecialchars(imap_utf7_decode_local($saved_attributes['saved_folder'][$i + 1])), 'left', '', 'width="35%"' )
        . html_tag( 'td', htmlspecialchars($saved_attributes['saved_what'][$i + 1]), 'left' )
        . html_tag( 'td', htmlspecialchars($saved_attributes['saved_where'][$i + 1]), 'center' )
        . html_tag( 'td', '', 'right' )
        .   '<a href="search.php'
        .     '?mailbox=' . urlencode($saved_attributes['saved_folder'][$i + 1])
        .     '&amp;what=' . urlencode($saved_attributes['saved_what'][$i + 1])
        .     '&amp;where=' . urlencode($saved_attributes['saved_where'][$i + 1])
        .     '&amp;smtoken=' . sm_generate_security_token()
        .   '">' . _("edit") . '</a>'
        .   '&nbsp;|&nbsp;'
        .   '<a href="search.php'
        .     '?mailbox=' . urlencode($saved_attributes['saved_folder'][$i + 1])
        .     '&amp;what=' . urlencode($saved_attributes['saved_what'][$i + 1])
        .     '&amp;where=' . urlencode($saved_attributes['saved_where'][$i + 1])
        .     '&amp;submit=Search_no_update'
        .     '&amp;smtoken=' . sm_generate_security_token()
        .   '">' . _("search") . '</a>'
        .   '&nbsp;|&nbsp;'
        .   "<a href=\"search.php?count=$i&amp;submit=delete&amp;smtoken=" . sm_generate_security_token() .'">'
        .     _("delete")
        .   '</a>'
        . '</td></tr>';
    }
    echo "</table></td></tr></table>\n";
}

if ($recent_count > 0) {
    echo "<br />\n"
       . html_tag( 'table', '', 'center', $color[9], 'width="95%" cellpadding="1" cellspacing="1" border="0"' )
       . html_tag( 'tr',
             html_tag( 'td', '<b>' . _("Recent Searches") . '</b>', 'center' )
         )
       . html_tag( 'tr' )
       . html_tag( 'td' )
       . html_tag( 'table', '', 'center', '', 'width="100%" cellpadding="0" cellspacing="0" border="0"' );
    for ($i=1; $i <= $recent_count; ++$i) {
            if (isset($attributes['search_folder'][$i])) {
            if ($attributes['search_folder'][$i] == "") {
                $attributes['search_folder'][$i] = "INBOX";
            }
            }
            if ($i % 2) {
                echo html_tag( 'tr', '', '', $color[0] );
            } else {
                echo html_tag( 'tr', '', '', $color[0] );
            }
            if (isset($attributes['search_what'][$i]) &&
                !empty($attributes['search_what'][$i])) {
            echo html_tag( 'td', htmlspecialchars(imap_utf7_decode_local($attributes['search_folder'][$i])), 'left', '', 'width="35%"' )
               . html_tag( 'td', htmlspecialchars($attributes['search_what'][$i]), 'left' )
               . html_tag( 'td', htmlspecialchars($attributes['search_where'][$i]), 'center' )
               . html_tag( 'td', '', 'right' )
               .   "<a href=\"search.php?count=$i&amp;submit=save&amp;smtoken=" . sm_generate_security_token() . '">'
               .     _("save")
               .   '</a>'
               .   '&nbsp;|&nbsp;'
               .   '<a href="search.php'
               .     '?mailbox=' . urlencode($attributes['search_folder'][$i])
               .     '&amp;what=' . urlencode($attributes['search_what'][$i])
               .     '&amp;where=' . urlencode($attributes['search_where'][$i])
               .     '&amp;submit=Search_no_update'
               .     '&amp;smtoken=' . sm_generate_security_token()
               .   '">' . _("search") . '</a>'
               .   '&nbsp;|&nbsp;'
               .   "<a href=\"search.php?count=$i&amp;submit=forget&amp;smtoken=" . sm_generate_security_token() . '">'
               .     _("forget")
               .   '</a>'
               . '</td></tr>';
        }
        }
    echo '</table></td></tr></table><br />';
}

/** FIXME: remove or fix it. $newsort is not set and not extracted from request
if (isset($newsort)) {
    $sort = $newsort;
    sqsession_register($sort, 'sort');
}*/

/*********************************************************************
 * Check to see if we can use cache or not. Currently the only time  *
 * when you will not use it is when a link on the left hand frame is *
 * used. Also check to make sure we actually have the array in the   *
 * registered session data.  :)                                      *
 *********************************************************************/

/** FIXME: remove or fix it. $use_mailbox_cache is not set and not extracted from request
if (! isset($use_mailbox_cache)) {
    $use_mailbox_cache = 0;
}*/

/* There is a problem with registered vars in 4.1 */
/*
if( substr( phpversion(), 0, 3 ) == '4.1'  ) {
    $use_mailbox_cache = FALSE;
}
*/

/* Search Form */
echo html_tag( 'div', '<b>' . _("Current Search") . '</b>', 'left' ) . "\n"
   . '<form action="search.php" name="s">'
   . addHidden('smtoken', sm_generate_security_token())
   . html_tag( 'table', '', '', '', 'width="95%" cellpadding="0" cellspacing="0" border="0"' )
   . html_tag( 'tr' )
   . html_tag( 'td', '', 'left' )
   . '<select name="mailbox">'
   . '<option value="All Folders"';
   if ($mailbox == 'All Folders') {
       echo ' selected="selected"';
   }
   echo '>[ ' . _("All Folders") . " ]</option>\n";

   $show_selected = array(strtolower($mailbox));
   echo sqimap_mailbox_option_list($imapConnection, $show_selected, 0, $boxes);

   echo '         </select>'.
        "       </td>\n";

// FIXME: explain all str_replace calls.
$what_disp = str_replace(',', ' ', $what);
$what_disp = str_replace('\\\\', '\\', $what_disp);
$what_disp = str_replace('\\"', '"', $what_disp);
$what_disp = str_replace('"', '&quot;', $what_disp);

echo html_tag( 'td', '<input type="text" size="35" name="what" value="' . $what_disp . '" />' . "\n", 'center' )
     . html_tag( 'td', '', 'right' )
     . "<select name=\"where\">";
s_opt( 'BODY', $where, _("Body") );
s_opt( 'TEXT', $where, _("Everywhere") );
s_opt( 'SUBJECT', $where, _("Subject") );
s_opt( 'FROM', $where, _("From") );
s_opt( 'CC', $where, _("Cc") );
s_opt( 'TO', $where, _("To") );
echo "         </select>\n" .
     "        </td>\n".
     html_tag( 'td', '<input type="submit" name="submit" value="' . _("Search") . '" />' . "\n", 'center', '', 'colspan="3"' ) .
     "     </tr>\n".
     "   </table>\n".
     "</form>\n";


do_hook('search_after_form');

flush();

/*
    search all folders option still in the works. returns a table for each
    folder it finds a match in.
*/

$old_value = 0;
if ($allow_thread_sort == TRUE) {
    $old_value = $allow_thread_sort;
    $allow_thread_sort = FALSE;
}

if ($search_all == 'all') {
    $mailbox == '';
    $boxcount = count($boxes);
    echo '<br /><center><b>' .
         _("Search Results") .
         "</b></center><br />\n";
    for ($x=0;$x<$boxcount;$x++) {
        if (!in_array('noselect', $boxes[$x]['flags'])) {
            $mailbox = $boxes[$x]['unformatted'];
            if (($submit == _("Search") || $submit == 'Search_no_update') && !empty($what)) {
                sqimap_mailbox_select($imapConnection, $mailbox);
                $msgs = sqimap_search($imapConnection, $where, $what, $mailbox, $color, 0, $search_all, $count_all);
                $count_all = count($msgs);
                printSearchMessages($msgs, $mailbox, $count_all, $imapConnection,
                                    $where, $what, false, false);
                array_push($perbox_count, $count_all);
            }
        }
    }
    for ($i=0;$i<count($perbox_count);$i++) {
        if ($perbox_count[$i]) {
            $count_all = true;
            break;
        }
    }
    if (!$count_all) {
       echo '<br /><center>' . _("No Messages Found") . '</center>';
    }
}

/*  search one folder option  */
else {
    if (($submit == _("Search") || $submit == 'Search_no_update') && !empty($what)) {
        echo '<br />'
        . html_tag( 'div', '<b>' . _("Search Results") . '</b>', 'center' ) . "\n";
        sqimap_mailbox_select($imapConnection, $mailbox);
        $msgs = sqimap_search($imapConnection, $where, $what, $mailbox, $color, 0, $search_all, $count_all);
        if (count($msgs)) {
            printSearchMessages($msgs, $mailbox, count($msgs), $imapConnection,
                                $where, $what, false, false);
        } else {
            echo '<br /><center>' . _("No Messages Found") . '</center>';
        }
    }
}

/*  must have search terms to search  */
if ($submit == _("Search") && empty($what)) {
        echo '<br />'
        . html_tag( 'div', '<b>' . _("Please enter something to search for") . '</b>', 'center' ) . "\n";
}

$allow_thread_sort = $old_value;


do_hook('search_bottom');
sqimap_logout($imapConnection);
echo '</body></html>';
