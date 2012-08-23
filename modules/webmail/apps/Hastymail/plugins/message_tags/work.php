<?php

/*  work.php: Plugin file responsible for the backend processing
    Copyright (C) 2002-2010  Hastymail Development group

    This file is part of Hastymail.

    Hastymail is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Hastymail is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hastymail; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

* $Id:$
*/

/* message view page */
function message_tags_message_page_selected($tools) {
    if ($tools->get_setting('tags_disabled')) {
        return;
    }
    $tags = hm_new('tags', $tools);
    $mailbox = $tools->get_mailbox();
    $uid = $tools->get_current_message_uid();
    $tags->get_tags_for_message($uid);
    if (isset($_SESSION['tag_page_list']) && !empty($_SESSION['tag_page_list'])) {
        if (isset($tags->tag_map[$mailbox][$uid])) {
            $res = get_tag_msg_list($_SESSION['tag_page_list'], $tools, $tags);
            if (isset($res['header_data']) && !empty($res['header_data'])) {
                $tools->add_to_store('tag_header_list', $res['header_data']);
            }
        }
    }
    $tools->imap_select_mailbox($mailbox);
}

/* mailbox view page */
function message_tags_mailbox_page_selected($tools) {
    if ($tools->get_setting('tags_disabled')) {
        return;
    }
    $tags = hm_new('tags', $tools);
    $tags->get_tags_for_mailbox($tools->get_mailbox());
}

/* check for deferred reload */
function message_tags_after_imap_action($tools) {
    if (!$tools->logged_in()) {
        return;
    }
    if ($tools->get_setting('tags_disabled')) {
        return;
    }
    $tags = hm_new('tags', $tools);
    $tags->check_deferred_reload();
}
/* settings updated */
function message_tags_update_settings($tools) {
    if (isset($_POST['tags_dsp']) && intval($_POST['tags_dsp']) < 4 && intval($_POST['tags_dsp']) > -1) {
        $tools->save_options_page_setting('tags_dsp', intval($_POST['tags_dsp']));
    }
    else {
        $tools->save_options_page_setting('tags_dsp', 0);
    }
    if (isset($_POST['disable_tags']) && $_POST['disable_tags'] == 1) {
        $tools->save_options_page_setting('tags_disabled', 1);
    }
    else {
        $tools->save_options_page_setting('tags_disabled', 0);
    }
}

/* when a message is being acted on */
function message_tags_imap_action($tools, $args) {
    list($action, $mailbox, $uids, $destination) = $args;
    if (!in_array($action , array('DELETE', 'MOVE', 'COPY', 'EXPUNGE'))) {
        return;
    }
    $tags = hm_new('tags', $tools);
    $tagged_uids = array();
    foreach ($uids as $uid) {
        if (!isset($tags->tag_map[$mailbox][$uid])) {
            continue;
        }
        $tagged_uids[] = $uid;
    }
    if (!empty($tagged_uids)) {
        switch ($action) {
            case 'DELETE':
            case 'EXPUNGE':
                $tags->adjust_tag_map_for_delete($mailbox, $tagged_uids);
                break;
            case 'COPY':
                $tags->adjust_tag_map_for_move_or_copy($mailbox, $tagged_uids, $destination, true);
                break;
            case 'MOVE':
                $tags->adjust_tag_map_for_move_or_copy($mailbox, $tagged_uids, $destination);
                break;
        }
        $tags->save_settings();
    }
}

/* insert javascript and style */
function message_tags_init($tools) {
    $tools->save_to_global_store('help_strings',$tools->str);
    $tools->register_ajax_callback('save_tags', 3, false); 
    $tools->register_ajax_callback('update_tag_list', 0, 'folder_tag_list'); 
    if ($tools->get_setting('tags_dsp') == 3) {
        $style = '.folder_lists{display: none !important;}';
    }
    else {
        $style = '';
    }
    if ($tools->get_page() == 'message_tags') {
        $reload = 'window.location.href = window.location.href;';
    }
    else {
        $reload = '';
    }
    if (!$tools->get_setting('tags_disabled')) {
        $tools->add_style('
        <style type="text/css">
        .tag_cell {float: right; cursor: pointer; font-size: 90%;}
        #folder_tag_list{padding-left: 15px; padding-bottom: 5px; margin-bottom: 10px;}
        #folder_tag_list a{padding-left: 10px; text-decoration: none;}
        #folder_tag_list .tag_title{font-weight: bold; margin-left: -10px; padding-bottom: 3px;}
        .tag_image{float: right; display: block; width: 12px; height: 12px; background: transparent url("plugins/message_tags/tag-icon.png") top left no-repeat;}
        '.$style.'
        .message_page a span {float: none !important;}
        .message_page {float: none !important;}
        </style>');
    }
    if (in_array($tools->get_page(), array('message_tags', 'mailbox', 'new', 'search', 'message')) || ($tools->logged_in() && $tools->get_page() == 'login')) {
        $tools->add_inline_js('
        var last_dsp_cell = false;
        var last_tag_str = "";
        function edit_cell(tag_str, uid, mbox) {
            var input_cell = "<input class=\"tag_input\" onkeypress=\"return check_enter(event, \'"+uid+"\', this, \'"+esc_sq(mbox)+"\')\"";
            input_cell += " onblur=\"return show_tags(\'"+uid+"\', this, \'"+esc_sq(mbox)+"\', true);\" type=\"text\" value=\""+esc_dq(tag_str)+"\" />";
            return input_cell;
        }
        function callback_message_tags_save_tags(output) {
            if (output) {
                var bits = output.split("^^^");
                if (bits.length == 2) {
                    if (bits[1]) {
                        last_dsp_cell.innerHTML = bits[1];
                    }
                    else {
                        last_dsp_cell.innerHTML = "<span class=\"tag_image\" title=\"'.$tools->str[1].'\"></span>";
                    }
                    alert(bits[0]);
                }
                else {
                    alert(output);
                }
            }
            else {
                '.$reload.'
            }
            if (do_folder_list) {
                hm_ajax_message_tags_update_tag_list();
            }
        }
        function display_cell(uid, tag_str, mbox) {
            var dsp_cell = "<a onclick=\"return edit_tags(\'"+uid+"\', this, \'"+esc_sq(mbox)+"\');\">";
            if (tag_str === "") {
                dsp_cell += "<span class=\"tag_image\" title=\"'.$tools->str[1].'\"></span>";
            }
            else {
                dsp_cell += tag_str;
            }
            dsp_cell += "</a>";
            return dsp_cell;
        }
        function check_enter(event, uid, input_element, mbox) {
            if (event.keyCode == 13 || event.which == 13) { 
                return show_tags(uid, input_element, mbox);
            }
            return true;
        }
        function show_tags(uid, input_element, mbox, nosave) {
            input_element.onblur = false;
            var cell = input_element.parentNode;
            var tag_str = input_element.value;
            if (!nosave && tag_str != last_tag_str) {
                hm_ajax_message_tags_save_tags(tag_str, uid, mbox);
            }
            cell.innerHTML = display_cell(uid, tag_str, mbox);
            last_dsp_cell = cell.childNodes[0];
            return false;
        }
        function edit_tags(uid, link, mbox) {
            var tag_str = link.text;
            var cell = link.parentNode;
            cell.innerHTML = edit_cell(tag_str, uid, mbox);
            last_tag_str = tag_str;
            cell.childNodes[0].focus();
            return false;
        }
        ');
    }
}

/* print the tag list for the folder list area */
function print_tag_list($tools, $ajax=false) {
    $res = '';
    if (!$ajax) {
        $res .= '<div id="folder_tag_list">';
    }
    $res .= '<div class="tag_title"><a href="?page=message_tags">'.$tools->str[0].'</a></div>';
    $tags = hm_new('tags', $tools);
    foreach ($tags->tag_list as $tag => $folders) {
        $cnt = 0;
        foreach ($folders as $v) {
            $cnt += $v;
        }
        $res .= '<span class="tag_img"></span><a class="tag_link" href="?page=message_tags&amp;tag='.urlencode($tag).'">'.$tools->display_safe($tag).' ('.$cnt.')</a><br />'; 
    }
    if (!$ajax) {
        $res .= '</div>';
    }
    return $res;
}
/* build html friendly human readable tag string */
function dsp_tags($tools, $tlist, $tools) {
    $res = '';
    $cnt = count($tlist);
    foreach ($tlist as $k => $group) {
        $gcnt = count($group);
        $links = array();
        if ($gcnt > 1) {
            $res .= '(';
        }
        foreach ($group as $v) {
            if ($gcnt == 1 && $cnt == 1) {
                $links[] = '<a>'.$v.'</a>';
            }
            else {
                $links[] = '<a title="remove" href="?page=message_tags#" onclick="return remove_tag(\''.$tools->display_safe($v).'\');">'.$v.'</a>';
            }
        }
        $res .= implode(' and ', $links);
        if ($gcnt > 1) {
            $res .= ')';
        }
        if ($k != ($cnt - 1)) {
            $res .= ' or ';
        }
    }
    return $res;
}
function get_tag_msg_list($tag_list, $tools, $tags) {
    $uids_by_folder = array();
    $flat_list = array();
    $res = array();
    foreach ($tag_list as $group) {
        $folders = array();
        foreach ($group as $t) {
            $flat_list[] = $t;
            if (empty($folders)) {
                $folders = array_keys($tags->tag_list[$t]);
            }
            else {
                $new_folders = $folders;
                foreach ($new_folders as $i => $v) {
                    if (!isset($tags->tag_list[$t][$v])) {
                        unset($folders[$i]);
                    }
                }
            }
        }
        foreach ($folders as $v) {
            foreach($tags->tag_map[$v] as $uid => $tlist) {
                if (count(array_intersect($group, $tlist)) == count($group)) {
                    $uids_by_folder[$v][] = $uid;
                }
            }
        }
    }
    $res['tag_dsp'] = dsp_tags($tools, $tag_list, $tools);
    $res['tags'] = $flat_list;
    foreach ($uids_by_folder as $folder => $uids) {
        if ($tools->imap_select_mailbox($folder)) {
            $headers = $tools->imap_get_header_list($folder, $uids);
            foreach ($headers as $msg) {
                $msg['folder'] = $folder;
                $res['header_data'][] = $msg;
            }
        }
    }
    return $res;
}
/* tag utility class */
class tags {
    var $tag_list;
    var $tools;
    var $tag_source;
    var $tag_map;
    var $disable_cache;
    var $standard_flags;

    /* constructor */
    function tags($tools) {
        $this->tools = $tools;
        $this->tag_source = 'settings';
        $this->get_imap_mode();
        $this->disable_cache = false;
        $this->get_session_tag_data();
        $this->standard_flags = array('\seen' => 1, '\answered' => 1, '\flagged' => 1,
                                      '\draft' => 1, '\deleted' => 1, '\$mdnsent' => 1,
                                      '\recent' => 1);
    }
    /* keep the tag map in sync when deleting a message */
    function adjust_tag_map_for_delete($mailbox, $uids) {
        foreach ($uids as $uid) {
            if (isset($this->tag_map[$mailbox][$uid])) {
                $tlist = $this->tag_map[$mailbox][$uid];
                foreach ($tlist as $tag) {
                    if (isset($this->tag_list[$tag][$mailbox])) {
                        $this->tag_list[$tag][$mailbox]--;
                        if ($this->tag_list[$tag][$mailbox] == 0) {
                            unset($this->tag_list[$tag][$mailbox]); 
                        }
                        if (empty($this->tag_list[$tag])) {
                            unset($this->tag_list[$tag]);
                        }
                    }
                }
                unset($this->tag_map[$mailbox][$uid]);
            }
        }
    }
    /* get message ids for messages being moved */
    function get_message_ids($mailbox, $uids, $tags) {
        $msg_id_map = array();
        if ($this->tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true)) {
            foreach ($uids as $uid) {
                $vals = $this->tools->imap_get_message_headers($uid, 0);
                foreach ($vals as $headers) {
                    if (strtolower($headers[0]) == 'message-id') {
                        $msg_id_map[$uid] = array($headers[1], $tags[$uid]);
                    }
                }
            } 
        }
        return $msg_id_map;
    }
    /* check for and return deferred reload values */
    function check_deferred_reload() {
        $mailboxes = array();
        if (isset($_SESSION['message_tags_deferred_reload']) && is_array($_SESSION['message_tags_deferred_reload'])) {
            $mailboxes = $_SESSION['message_tags_deferred_reload'];
            unset($_SESSION['message_tags_deferred_reload']);
        }
        foreach ($mailboxes as $vals) {
            $mailbox = $vals[0];
            $ids = $vals[1];
            if (!empty($ids)) {
                if ($this->tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true)) {
                    foreach ($ids as $vals) { 
                        $id = $vals[0];
                        $tlist = $vals[1];
                        $res = $this->tools->imap_search_mailbox('(ALL HEADER "message-id" "'.$id.'")');
                        foreach ($res as $uid) {
                            $this->tag_map[$mailbox][$uid] = $tlist;
                        }
                    }
                }
            }
            elseif ($this->tag_source == 'imap') {
                if ($this->tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true)) {
                    $this->get_imap_tags($mailbox);
                }
            }
        }
        $this->save_settings();
    }
    /* keep the tag map in sync when moving/copying a message */
    function adjust_tag_map_for_move_or_copy($mailbox, $uids, $dest, $copy=false) {
        $tag_map = $this->tag_map;
        $tags = array();
        if (isset($tag_map[$mailbox])) {
            foreach ($tag_map[$mailbox] as $uid => $tlist) {
                if (in_array($uid, $uids)) {
                    $tags[$uid] = $tlist;
                    if (!$copy) {
                        unset($this->tag_map[$mailbox][$uid]);
                        if (empty($this->tag_map[$mailbox])) {
                            unset($this->tag_map[$mailbox]);
                        }
                    }
                    foreach ($tlist as $tag) {
                        if (isset($this->tag_list[$tag][$mailbox])) {
                            if (!$copy) {
                                $this->tag_list[$tag][$mailbox]--;
                                if ($this->tag_list[$tag][$mailbox] == 0) {
                                    unset($this->tag_list[$tag][$mailbox]); 
                                }
                            }
                            if (isset($this->tag_list[$tag][$dest])) {
                                $this->tag_list[$tag][$dest]++;
                            }
                            else {
                                $this->tag_list[$tag][$dest] = 1;
                            }
                        }
                    }
                    continue;
                }
            }
        }
        if ($this->tag_source == 'settings') {
            $msg_id_map = $this->get_message_ids($mailbox, $uids, $tags);
            $_SESSION['message_tags_deferred_reload'][] = array($dest, $msg_id_map);
        }
        else {
            $_SESSION['message_tags_deferred_reload'][] = array($dest, array());
        }
    }
    function get_session_tag_data() {
        $this->tag_list = false;
        $this->tag_map = false;
        if (isset($_SESSION['tag_list'])) {
            $this->tag_list = $_SESSION['tag_list'];
        }
        if (isset($_SESSION['tag_map'])) {
            $this->tag_map = $_SESSION['tag_map'];
        }
        if ($this->tag_map === false || $this->tag_list == false) {
            $this->get_settings();
        }
    }
    /* retreive the tag map from the php session */
    function get_session_tag_map($mailbox) {
        if (isset($map[$mailbox])) {
            return $map[$mailbox];
        }
        else {
            return array();
        }
        return $map;
    }
    /* determine if a mailbox state has changed */
    function msg_cache_check($mailbox) {
        if ($this->disable_cache) {
            return false;
        }
        $res = false;
        if (isset($_SESSION['folders'][$mailbox]['status']['uidnext'])) {
            if (isset($_SESSION['mailbox_uidnext'][$mailbox]) &&
                $_SESSION['mailbox_uidnext'][$mailbox] == $_SESSION['folders'][$mailbox]['status']['uidnext']) {
                $res = true;
            }
            else {
                $_SESSION['mailbox_uidnext'][$mailbox] = $_SESSION['folders'][$mailbox]['status']['uidnext'];
            }
        }
        return $res;
    }
    /* get tags for all the uids of a mailbox */
    function get_tags_for_mailbox($mailbox) {
        $res = false;
        if ($this->msg_cache_check($mailbox)) {
            $res = $this->get_session_tag_map($mailbox);
        }
        if ($res === false) {
            $this->tag_map[$mailbox] = $this->get_tags($mailbox);
            $this->save_settings();
        }
    }
    /* wrapper around tag source */
    function get_tags($mailbox) {
        $res = array();
        switch ($this->tag_source) {
            case 'settings':
                $res = $this->get_settings_tags($mailbox);
                break;
            case 'imap':
            default:
                $res = $this->get_imap_tags($mailbox);
                break;
        }
        foreach ($res as $uid => $tlist) {
            foreach ($tlist as $v) {
                if (!isset($this->tag_list[$v][$mailbox])) {
                    $this->tag_list[$v][$mailbox] = 1;
                }
            }
        }
        return $res;
    }
    /* get tag map from the user settings */
    function get_settings_tag_map() {
        $tag_map = $this->get_settings('map');
        if ($tag_map) {
            return $tag_map;
        }
        return array();
    }
    /* get tags for a mailbox when using settings mode */
    function get_settings_tags($mailbox) {
        if (isset($this->tag_map[$mailbox])) {
            return $this->tag_map[$mailbox];
        }
        return array();
    }
    /* compare single tag string to valid pattern */
    function valid_tag($tag) {
        if (preg_match("/^[a-zA-Z0-9_\-]{2,}$/", $tag) && !isset($this->standard_flags[strtolower($tag)])) {
            return true;
        }
        return false;
    }
    /* sanitize a string of tags */
    function sanitize_tag_string($str) {
        $tags = array();
        $invalid_tags = array();
        foreach (preg_split("/\s+/", $str) as $tag) {
            $tag = trim($tag);
            if ($this->valid_tag($tag)) {
                $tags[] = $tag;
            }
            else {
                $invalid_tags[] = $tag;
            }
        }
        return array(array_unique($tags), array_unique($invalid_tags));
    }
    /* save a tag */
    function set_tags($mailbox, $new_tags, $old_tags, $uid) {
        $this->set_tags_settings($mailbox, $new_tags, $old_tags, $uid);
        if ($this->tag_source == 'imap') {
            $this->set_tags_imap($mailbox, $new_tags, $old_tags, $uid);
        }
        $this->save_settings();
    }
    /* save a tag using the hm settings system */
    function set_tags_settings($mailbox, $new_tags, $old_tags, $uid) {
        if (isset($this->tag_map[$mailbox][$uid])) {
            $tags = $this->tag_map[$mailbox][$uid];
            $new = array();
            foreach ($tags as $tag) {
                if (!trim($tag)) {
                    continue;
                }
                if (!in_array($tag, $old_tags)) {
                    $new[] = $tag;
                }
            }
            foreach ($new_tags as $tag) {
                if (!trim($tag)) {
                    continue;
                }
                if (!in_array($tag, $new)) {
                    $new[] = $tag;
                }
            }
            if (!empty($new)) {
                $this->tag_map[$mailbox][$uid] = $new;
            }
            else {
                unset($this->tag_map[$mailbox][$uid]);
            }
        }
        else {
            if (!empty($new_tags)) {
                $this->tag_map[$mailbox][$uid] = $new_tags;
            }
        }
        if (isset($this->tag_map[$mailbox][$uid])) {
            foreach ($new_tags as $v) {
                if (!isset($this->tag_list[$v][$mailbox])) {
                    $this->tag_list[$v][$mailbox] = 1;
                }
                elseif (isset($this->tag_list[$v][$mailbox])) {
                    $this->tag_list[$v][$mailbox] += 1;
                }
            }
        }
        foreach ($old_tags as $v) {
            if (isset($this->tag_list[$v][$mailbox])) {
                $this->tag_list[$v][$mailbox] -= 1;
                if ($this->tag_list[$v][$mailbox] < 1) {
                    unset($this->tag_list[$v][$mailbox]);
                    if (empty($this->tag_list[$v])) {
                        unset($this->tag_list[$v]);
                    }
                }
            }
        }
    }
    /* determine if we can store tags in the imap server */
    function get_imap_mode() {
        if (isset($_SESSION['msg_tag_imap_mode'])) {
            if ($_SESSION['msg_tag_imap_mode']) {
                $this->tag_source = 'imap';
            }
        }
        else {
            $imap_mode = false;
            $data = $this->tools->imap_custom_command("SELECT INBOX\r\n", false);
            if ($data[1]) {
                foreach ($data[0] as $line) {
                    if (strpos($line, 'PERMANENTFLAGS') !== false) {
                        if (strpos($line, '\*') !== false) {
                            $imap_mode = true;
                            break;
                        }
                    }
                }
            }
            if ($imap_mode) {
                $this->tag_source = 'imap';
            }
            $_SESSION['msg_tag_imap_mode'] = $imap_mode;
        }
    }
    /* save a tag using IMAP flags */
    function set_tags_imap($mailbox, $new_tags, $old_tags, $uid) {
        $add_command = 'UID STORE '.$uid.' +FLAGS (';
        $del_command = 'UID STORE '.$uid.' -FLAGS (';
        foreach ($new_tags as $tag) {
            $add_command .= ' '.$tag;
        }
        foreach ($old_tags as $tag) {
            $del_command .= ' '.$tag;
        }
        $del_command .= ")\r\n";
        $add_command .= ")\r\n";
        $this->tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true);
        if (!empty($old_tags)) {
            $data = $this->tools->imap_custom_command($del_command, true);
        }
        if (!empty($new_tags)) {
            $data = $this->tools->imap_custom_command($add_command, true);
        }
    }
    /* use \* permanentflags to fetch custom tag values */
    function get_imap_tags($mailbox) {
        $res = array();
        $data = $this->tools->imap_custom_command("UID FETCH 1:* (FLAGS)\r\n", true);
        if ($data[1] && !empty($data[0])) {
            $res = $this->parse_flag_response($data[0], $mailbox);
        }
        return $res;
    }
    /* parse the IMAP fetch response and return the custom flag data */
    function parse_flag_response($data, $mailbox) {
        $uids = array();
        foreach ($data as $vals) {
            $cnt = count($vals);
            $uid = false;
            $flags = array();
            for ($i = 0; $i < $cnt; $i++) {
                $v = $vals[$i];
                if (strtoupper($v) == 'FLAGS' && ($i + 2) < $cnt) {
                    $i += 2;
                    while ($vals[$i] != ')' && $i < $cnt) {
                        if (!isset($this->standard_flags[strtolower($vals[$i])])) {
                            $flags[] = $vals[$i];
                        }
                        $i++;
                    }
                }
                if (strtoupper($v) == 'UID' && $i < $cnt) {
                    $uid = $vals[($i + 1)];
                }
            }
            if ($uid && !empty($flags)) {
                $uids[$uid] = $flags;
            }
        }
        return $uids;
    }
    /* get the tags for a single message */
    function get_tags_for_message($uid) {
        $tags = array();
        $mailbox = $this->tools->get_mailbox();
        if (!isset($this->tag_map[$mailbox])) {
            $this->tag_map[$mailbox] = $this->get_tags($mailbox);
        }
        if (isset($this->tag_map[$mailbox][$uid])) {
            $tags = $this->tag_map[$mailbox][$uid];
        }
        return $tags;
    }
    /* repair mismatched tag data if possible */
    function repair() {
        if ($this->tag_source == 'imap') {
            $this->tag_map = array();
            $this->tag_list = array();
            foreach ($this->tools->imap_get_folders() as $vals) {
                $folder = $vals['realname'];
                $this->tools->imap_select_mailbox($folder, 'ARRIVAL', false, true);
                $this->tag_map[$folder] = $this->get_tags($folder);
            }  
        }
        else {
            $this->tag_list = array();
            foreach ($this->tag_map as $folder => $vals) {
                foreach ($vals as $uid => $tlist) {
                    foreach ($tlist as $tag) {
                        if (!isset($this->tag_list[$tag][$folder])) {
                            $this->tag_list[$tag][$folder] = 1;
                        }
                        else {
                            $this->tag_list[$tag][$folder] += 1;
                        }
                    }
                }
            }
        }
        $this->save_settings();
    }
    /* reset all tags (for debugging) */
    function reset() {
        $this->tag_list = array();
        if ($folders && $this->tag_source == 'imap') {
            foreach ($this->tag_map as $folder => $vals) {
                $this->tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true);
                foreach ($vals as $uid => $tlist) {
                    $del_command = 'UID STORE '.$uid.' -FLAGS ( '.implode(' ', $tlist).")\r\n";
                    $data = $this->tools->imap_custom_command($del_command, true);
                }
            }
        }
        $this->tag_map = array();
        $this->save_settings();
    }
    /* load tags from settings file */
    function get_settings($return='all') {
        global $conf;
        $this->tag_list = array();
        $this->tag_map = array();
        $tag_dir = $conf['settings_path'];
        $file = $tag_dir.$_SESSION['user_data']['username'].'.tag_map';
        $data = @unserialize(join('', file($file)));
        if (is_array($data)) {
            $this->tag_list = $data[0];
            $this->tag_map = $data[1];
        }
        $res = array();
        switch ($return) {
            case 'list':
                $res = $this->tag_list;        
                break;
            case 'map':
                $res = $this->tag_map;        
                break;
            default:
                $res = array($this->tag_list, $this->tag_map);
                break;
        }
        return $res;
    }
    /* save tags to settings file */
    function save_settings() {
        global $conf;
        $_SESSION['tag_map'] = $this->tag_map;
        $_SESSION['tag_list'] = $this->tag_list;
        $tag_dir = $conf['settings_path'];
        $file = $tag_dir.$_SESSION['user_data']['username'].'.tag_map';
        if ($fh = @fopen($file, 'w')) {
            fwrite($fh, serialize(array($this->tag_list, $this->tag_map)));
            fclose($fh);
        }
    }
    /* rename a tag */
    function rename_tag($old, $new) {
        $folders = array_keys($this->tag_list[$old]);
        foreach ($folders as $v) {
            foreach($this->tag_map[$v] as $uid => $tlist) {
                if (in_array($old, $tlist)) {
                    $new_tlist = array();
                    foreach ($tlist as $t) {
                        if ($t == $old) {
                            $new_tlist[] = $new;
                        }
                        else {
                            $new_tlist[] = $t;
                        }
                        $this->tag_map[$v][$uid] = $new_tlist;
                        if (!isset($this->tag_list[$new][$v])) {
                            $this->tag_list[$new][$v] = 1;
                        }
                        elseif (isset($this->tag_list[$new][$v])) {
                            $this->tag_list[$new][$v] += 1;
                        }
                    }
                }
            }
        }
        unset($this->tag_list[$old]);
        if (!empty($folders) && $this->tag_source == 'imap') {
            foreach ($folders as $mailbox) {
                $uids = implode(',', array_keys($this->tag_map[$mailbox]));
                $del_command = 'UID STORE '.$uids.' -FLAGS ( '.$old.")\r\n";
                $add_command = 'UID STORE '.$uids.' +FLAGS ( '.$new.")\r\n";
                $this->tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true);
                $del_data = $this->tools->imap_custom_command($del_command, true);
                $add_data = $this->tools->imap_custom_command($add_command, true);
            }
        }
        $this->save_settings();
        return 'Tag Renamed';
    }
    /* delete a tag */
    function delete_tag($tag) {
        $folders = false;
        if (isset($this->tag_list[$tag])) {
            $folders = array_keys($this->tag_list[$tag]);
            unset($this->tag_list[$tag]);
            foreach ($folders as $v) {
                foreach($this->tag_map[$v] as $uid => $tlist) {
                    if (in_array($tag, $tlist)) {
                        $new_tlist = array();
                        foreach ($tlist as $t) {
                            if ($t == $tag) {
                                continue;
                            }
                            $new_tlist[] = $t;
                        }
                        if (empty($new_tlist)) {
                            unset($this->tag_map[$v][$uid]);
                        }
                        else {
                            $this->tag_map[$v][$uid] = $new_tlist;
                        }
                    }
                }
            }
        }
        if ($folders && $this->tag_source == 'imap') {
            $del_command = 'UID STORE ALL -FLAGS ( '.$tag.")\r\n";
            foreach ($folders as $mailbox) {
                $this->tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true);
                $data = $this->tools->imap_custom_command($del_command, true);
            }
        }
        $this->save_settings();
        return 'Tag Deleted';
    }
}
?>
