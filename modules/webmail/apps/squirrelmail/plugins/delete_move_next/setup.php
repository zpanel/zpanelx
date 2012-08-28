<?php

/**
 * setup.php
 *
 * delete_move_next
 *   deletes or moves currently displayed message and displays
 *   next or previous message.
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage delete_move_next
 */

//FIXME: all functionality needs to be moved out of the setup.php file!

/**
 * Initialize the plugin
 * @return void
 */
function squirrelmail_plugin_init_delete_move_next() {
    global $squirrelmail_plugin_hooks;

    $squirrelmail_plugin_hooks['html_top']['delete_move_next'] = 'delete_move_next_action';
    $squirrelmail_plugin_hooks['right_main_after_header']['delete_move_next'] = 'delete_move_next_action';
    $squirrelmail_plugin_hooks['read_body_bottom']['delete_move_next'] = 'delete_move_next_read_b';
    $squirrelmail_plugin_hooks['read_body_menu_bottom']['delete_move_next'] = 'delete_move_next_read_t';
    $squirrelmail_plugin_hooks['options_display_inside']['delete_move_next'] = 'delete_move_next_display_inside';
    $squirrelmail_plugin_hooks['options_display_save']['delete_move_next'] = 'delete_move_next_display_save';
    $squirrelmail_plugin_hooks['loading_prefs']['delete_move_next'] = 'delete_move_next_loading_prefs';
}

/* fixes the sort_array for the prev_del/next_del links when 
 * using server side sorting or thread sorting 
 */

function fix_sort_array () {
    global $username, $data_dir, $allow_server_sort, $allow_thread_sort,
    $thread_sort_messages, 
    $mailbox, $imapConnection, $sort, $uid_support, $mbx_response;

    // Got to grab this out of prefs, since it isn't saved from mailbox_view.php
    if ($allow_thread_sort) {
        $thread_sort_messages = getPref($data_dir, $username, "thread_$mailbox",0);
    }

    switch (true) {
      case ($allow_thread_sort && $thread_sort_messages):
          $server_sort_array = get_thread_sort($imapConnection);
          break;
      case ($allow_server_sort):
          $server_sort_array = sqimap_get_sort_order($imapConnection, $sort, $mbx_response);
          break;
      case ($uid_support):
          $server_sort_array = sqimap_get_php_sort_order($imapConnection, $mbx_response);
          break;
      default:
          break;
    }
}

/*
 * Warning: this function relies on the internal representation of
 * of the message cache for the current mailbox. As such, it is fragile
 * because the underlying implementation can change. I will present it
 * to the squirrelmail maintainers as a proposed addition to the API,
 * perhaps even as inline code to sqimap_mailbox_expunge(). In the 
 * meantime, you have been warned. [alane@geeksrus.net 2001/05/06]
 */

function delete_move_del_arr_elem($arr, $index) {
    $tmp = array();
    $j = 0;
    foreach ($arr as $v) {
        if ($j != $index) {
           $tmp[] = $v;
         }
         $j++;
    }
    return $tmp;
}

function delete_move_show_msg_array() {
    global $msort, $msgs;
    $keys = array_keys($msort);
    for ($i = 0; $i < count($keys); $i++) {
        echo '<p>key ' . $keys[$i] . ' msgid ' . $msgs[$keys[$i]]['ID'] . '</p>';
    }
}

function delete_move_expunge_from_all($id) {
    global $msgs, $msort, $sort, $imapConnection, $mailbox, $uid_support;
    $delAt = -1;

    if(isset($msort) && count($msort) > 0) {
        for ($i = 0; $i < count($msort); $i++) {
            if ($msgs[$i]['ID'] == $id) {
                $delAt = $i;
            } elseif ($msgs[$i]['ID'] > $id) {
                if (!$uid_support) {
                   $msgs[$i]['ID']--;
                }
            }
        }

        $msgs = delete_move_del_arr_elem($msgs, $delAt);
        $msort = delete_move_del_arr_elem($msort, $delAt);
        if ($sort < 6) {
            if ($sort % 2) {
                asort($msort);
            } else {
                arsort($msort);
            }
        }
        sqsession_register($msgs, 'msgs');
        sqsession_register($msort, 'msort');
    }

    sqimap_mailbox_expunge($imapConnection, $mailbox, true);
}

function delete_move_next_action() {

    if ( sqgetGlobalVar('unread_id', $unread_id, SQ_GET) ) {
        delete_move_next_unread();
    } else if ( sqgetGlobalVar('delete_id', $delete_id, SQ_GET) ) {
        delete_move_next_delete();
        fix_sort_array();
    } else if ( sqgetGlobalVar('move_id', $move_id, SQ_POST) ) {
        delete_move_next_move();
        fix_sort_array();
    }
}

function delete_move_next_read_t() {

    global $delete_move_next_t;

    if($delete_move_next_t == 'on') {
        delete_move_next_read('top');
    }
}

function delete_move_next_read_b() {

    global $delete_move_next_b;

    if ($delete_move_next_b != 'off') {
        delete_move_next_read('bottom');
    }
}

function delete_move_next_read($currloc) {
    global $delete_move_next_formATtop, $delete_move_next_formATbottom,
           $color, $where, $what, $currentArrayIndex, $passed_id,
           $mailbox, $sort, $startMessage, $delete_id, $move_id,
           $imapConnection, $auto_expunge, $move_to_trash, $mbx_response,
           $uid_support, $passed_ent_id, $delete_move_next_show_unread;

    $urlMailbox = urlencode($mailbox);

    if (!isset($passed_ent_id)) $passed_ent_id = 0;

    if (!(($where && $what) || ($currentArrayIndex == -1)) && !$passed_ent_id) {
        $next = findNextMessage($passed_id);
        $prev = findPreviousMessage($mbx_response['EXISTS'], $passed_id);
        $prev_if_del = $prev;
        $next_if_del = $next;
        if (!$uid_support && ($auto_expunge || $move_to_trash)) {
            if ($prev_if_del > $passed_id) {
                $prev_if_del--;
            }
            if ($next_if_del > $passed_id) {
                $next_if_del--;
            }
        }

        /* Base is illegal within documents 
        * $location = get_location();
        * echo "<base href=\"$location/\">" . */
        echo '<table cellspacing="0" width="100%" border="0" cellpadding="2">'.
             '<tr>'.
                 "<td bgcolor=\"$color[9]\" width=\"100%\" align=\"center\"><small>";

        if ($prev > 0){
            echo "<a href=\"read_body.php?passed_id=$prev_if_del&amp;mailbox=$urlMailbox&amp;sort=$sort&amp;startMessage=$startMessage&amp;show_more=0&amp;delete_id=$passed_id&amp;smtoken=" . sm_generate_security_token() . "\">" . _("Delete &amp; Prev") . "</a>" . "&nbsp;|&nbsp;";
            if ($delete_move_next_show_unread == 'on') {
                echo "<a href=\"read_body.php?passed_id=$prev_if_del&amp;mailbox=$urlMailbox&amp;sort=$sort&amp;startMessage=$startMessage&amp;show_more=0&amp;unread_id=$passed_id&amp;smtoken=" . sm_generate_security_token() . "\">" . _("Unread &amp; Prev") . "</a>" . "&nbsp;|&nbsp;";
            }
        }
        else {
            echo _("Delete &amp; Prev") . "&nbsp;|&nbsp;";
            if ($delete_move_next_show_unread == 'on') {
                echo _("Unread &amp; Prev") . "&nbsp;|&nbsp;";
            }
        }
        if ($next > 0){
            if ($delete_move_next_show_unread == 'on') {
                echo "<a href=\"read_body.php?passed_id=$next_if_del&amp;mailbox=$urlMailbox&amp;sort=$sort&amp;startMessage=$startMessage&amp;show_more=0&amp;unread_id=$passed_id&amp;smtoken=" . sm_generate_security_token() . "\">" . _("Unread &amp; Next") . "</a>&nbsp;|&nbsp;";
            }
            echo "<a href=\"read_body.php?passed_id=$next_if_del&amp;mailbox=$urlMailbox&amp;sort=$sort&amp;startMessage=$startMessage&amp;show_more=0&amp;delete_id=$passed_id&amp;smtoken=" . sm_generate_security_token() . "\">" . _("Delete &amp; Next") . "</a>";
        } else {
            if ($delete_move_next_show_unread == 'on') {
                echo _("Unread &amp; Next") . "&nbsp;|&nbsp;";
            }
            echo _("Delete &amp; Next");
        }
        echo '</small></td></tr>';

        if ($next_if_del < 0) {
            $next_if_del = $prev_if_del;
        }
        if (($delete_move_next_formATtop == 'on') && ($currloc == 'top')) {
            if ($next_if_del > 0) {
                delete_move_next_moveNextForm($next_if_del);
            } else {
                delete_move_next_moveRightMainForm();
            }
        }
        if (($delete_move_next_formATbottom != 'off') && ($currloc == 'bottom')) {
            if ($next_if_del > 0) {
                delete_move_next_moveNextForm($next_if_del);
            } else {
                delete_move_next_moveRightMainForm();
            }
        }
        echo '</table>';
    }
}

function get_move_target_list() {
    global $imapConnection, $lastTargetMailbox;
    if (isset($lastTargetMailbox) && !empty($lastTargetMailbox)) {
        echo sqimap_mailbox_option_list($imapConnection, array(strtolower($lastTargetMailbox)));
    }
    else {
        echo sqimap_mailbox_option_list($imapConnection);
    }
}

function delete_move_next_moveNextForm($next) {

    global $color, $where, $what, $currentArrayIndex, $passed_id,
           $mailbox, $sort, $startMessage, $delete_id, $move_id,
           $imapConnection;

    $urlMailbox = urlencode($mailbox);

    echo '<tr>'.
         "<td bgcolor=\"$color[9]\" width=\"100%\" align=\"center\">".
           "<form action=\"read_body.php?mailbox=$urlMailbox&amp;sort=$sort&amp;startMessage=$startMessage&amp;passed_id=$next\" method=\"post\"><small>".
            "<input type=\"hidden\" name=\"show_more\" value=\"0\">".
            "<input type=\"hidden\" name=\"move_id\" value=\"$passed_id\">".
            "<input type=\"hidden\" name=\"smtoken\" value=\"" . sm_generate_security_token() . "\">".
            _("Move to:") .
            ' <select name="targetMailbox">';
    get_move_target_list(); 
    echo    '</select> '.
            '<input type="submit" value="' . _("Move") . '">'.
            '</small>'.
           '</form>'.
         '</td>'.
         '</tr>';
}

function delete_move_next_moveRightMainForm() {

    global $color, $where, $what, $currentArrayIndex, $passed_id,
           $mailbox, $sort, $startMessage, $delete_id, $move_id,
           $imapConnection;

    $urlMailbox = urlencode($mailbox);

    echo '<tr>' .
            "<td bgcolor=\"$color[9]\" width=\"100%\" align=\"center\">".
            "<form action=\"right_main.php?mailbox=$urlMailbox&amp;sort=$sort&amp;startMessage=$startMessage\" method=\"post\"><small>" .
            "<input type=\"hidden\" name=\"move_id\" value=\"$passed_id\">".
            "<input type=\"hidden\" name=\"smtoken\" value=\"" . sm_generate_security_token() . "\">".
            _("Move to:") .
            ' <select name="targetMailbox">';
    get_move_target_list(); 
    echo    ' </select>' .
            '<input type=submit value="' . _("Move") . '">'.
            '</small>'.
         '</form>' .
         '</td>'.
         '</tr>';
}

function delete_move_next_unread() {
    global $imapConnection;

    sqgetGlobalVar('unread_id', $unread_id, SQ_GET);
    if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_GET)) {
        $submitted_token = '';
    }

    // first, validate security token
    sm_validate_security_token($submitted_token, 3600, TRUE);

    sqimap_toggle_flag($imapConnection, $unread_id, '\\Seen', false, true);
}

function delete_move_next_delete() {
    global $imapConnection, $auto_expunge;

    sqgetGlobalVar('delete_id', $delete_id, SQ_GET);
    sqgetGlobalVar('mailbox', $mailbox, SQ_GET);
    if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_GET)) {
        $submitted_token = '';
    }

    // first, validate security token
    sm_validate_security_token($submitted_token, 3600, TRUE);

    sqimap_msgs_list_delete($imapConnection, $mailbox, $delete_id);
    if ($auto_expunge) {
        delete_move_expunge_from_all($delete_id);
        // sqimap_mailbox_expunge($imapConnection, $mailbox, true);
    }
}

function delete_move_next_move() {
    global $imapConnection, $mailbox, $auto_expunge, $lastTargetMailbox;

    sqgetGlobalVar('move_id', $move_id, SQ_POST);
    sqgetGlobalVar('mailbox', $mailbox, SQ_FORM);
    sqgetGlobalVar('targetMailbox', $targetMailbox, SQ_POST);
    if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_POST)) {
        $submitted_token = '';
    }

    // first, validate security token
    sm_validate_security_token($submitted_token, 3600, TRUE);

    // Move message
    sqimap_msgs_list_move($imapConnection, $move_id, $targetMailbox);
    if ($auto_expunge) {
        delete_move_expunge_from_all($move_id);
        // sqimap_mailbox_expunge($imapConnection, $mailbox, true);
    }

    if ($targetMailbox != $lastTargetMailbox) {
        $lastTargetMailbox = $targetMailbox;
        sqsession_register($lastTargetMailbox, 'lastTargetMailbox');
    }
}

function delete_move_next_display_inside() {
    global $username,$data_dir, $delete_move_next_show_unread,
        $delete_move_next_t, $delete_move_next_formATtop,
        $delete_move_next_b, $delete_move_next_formATbottom;

    echo "<tr>" . html_tag('td',_("Delete/Unread/Move/Next Buttons:"),'right','','valign="top"') . "\n".
         '<td><input type="checkbox" name="delete_move_next_ti" id="delete_move_next_ti"';

    if ($delete_move_next_t == 'on') {
        echo ' checked="checked"';
    }
    echo '><label for="delete_move_next_ti"> ' . _("Display at top").
         '</label> <input type="checkbox" name="delete_move_next_formATtopi" id="delete_move_next_formATtopi"';

    if ($delete_move_next_formATtop == 'on') {
        echo ' checked="checked"';
    }
    echo '><label for="delete_move_next_formATtopi"> ' . _("with move option") . '</label><br>';

    echo '<input type="checkbox" name="delete_move_next_bi" id="delete_move_next_bi"';
    if($delete_move_next_b != 'off') {
        echo ' checked="checked"';
    }
    echo '><label for="delete_move_next_bi"> ' . _("Display at bottom") .
         '</label> <input type="checkbox" name="delete_move_next_formATbottomi" id="delete_move_next_formATbottomi"';

    if ($delete_move_next_formATbottom != 'off') {
        echo ' checked="checked"';
    }
    echo '><label for="delete_move_next_formATbottomi"> ' . _("with move option") . '</label><br>'.
         '<input type="checkbox" name="delete_move_next_show_unread" id="delete_move_next_show_unread"';
    if($delete_move_next_show_unread != 'off') {
        echo ' checked="checked"';
    }
    echo '><label for="delete_move_next_show_unread"> ' . _("Show unread options") .
         "</label></td></tr>\n";
}

function delete_move_next_display_save() {

    global $username,$data_dir;

    if ( sqgetGlobalVar('delete_move_next_ti', $delete_move_next_ti, SQ_POST) ) {
        setPref($data_dir, $username, 'delete_move_next_t', 'on');
    } else {
        setPref($data_dir, $username, 'delete_move_next_t', "off");
    }

    if ( sqgetGlobalVar('delete_move_next_formATtopi', $delete_move_next_formATtopi, SQ_POST) ) {
        setPref($data_dir, $username, 'delete_move_next_formATtop', 'on');
    } else {
        setPref($data_dir, $username, 'delete_move_next_formATtop', "off");
    }


    if ( sqgetGlobalVar('delete_move_next_bi', $delete_move_next_bi, SQ_POST) ) {
        setPref($data_dir, $username, 'delete_move_next_b', 'on');
    } else {
        setPref($data_dir, $username, 'delete_move_next_b', "off");
    }

    if ( sqgetGlobalVar('delete_move_next_formATbottomi', $delete_move_next_formATbottomi, SQ_POST) ) {
        setPref($data_dir, $username, 'delete_move_next_formATbottom', 'on');
    } else {
        setPref($data_dir, $username, 'delete_move_next_formATbottom', "off");
    }

    if ( sqgetGlobalVar('delete_move_next_show_unread', $delete_move_next_show_unread, SQ_POST) ) {
        setPref($data_dir, $username, 'delete_move_next_show_unread', 'on');
    } else {
        setPref($data_dir, $username, 'delete_move_next_show_unread', "off");
    }
}

function delete_move_next_loading_prefs() {
    global $username,$data_dir, $delete_move_next_show_unread,
           $delete_move_next_t, $delete_move_next_formATtop,
           $delete_move_next_b, $delete_move_next_formATbottom;

    $delete_move_next_t = getPref($data_dir, $username, 'delete_move_next_t');
    $delete_move_next_b = getPref($data_dir, $username, 'delete_move_next_b');
    $delete_move_next_formATtop = getPref($data_dir, $username, 'delete_move_next_formATtop');
    $delete_move_next_formATbottom = getPref($data_dir, $username, 'delete_move_next_formATbottom');
    $delete_move_next_show_unread = getPref($data_dir, $username, 'delete_move_next_show_unread');

}

