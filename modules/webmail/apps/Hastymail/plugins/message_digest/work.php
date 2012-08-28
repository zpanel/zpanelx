<?php

/*  work.php
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
function message_digest_init($tools) {
    $opts = $tools->str;
    $tools->save_to_global_store('help_strings', $opts);
    if ($tools->get_page() != 'message' || !$tools->get_setting('enable_digest_display')) {
        return;
    }
    $tools->add_style('
        <style type="text/css">
        .digest_table td{ padding: 1px !important; font-size: 90%; border: none !important; }
        .digest_table{ border: none !important; padding-top: 5px; padding-left: 10px; padding-bottom: 10px; }
        .dt_cell span{white-space: nowrap !important;}
        .digest_div{}
        </style>
    ');
}
function message_digest_message_page_start($tools) {
    if ($tools->get_page() != 'message' || !$tools->get_setting('enable_digest_display')) {
        return;
    }
    $res = $tools->imap_select_mailbox($tools->get_mailbox());
    $uid = $tools->get_current_message_uid();
    $headers = $tools->imap_get_message_headers($uid, 0);
    foreach ($headers as $vals) {
        if (strtolower($vals[0]) == 'content-type' && (stristr($vals[1], 'multipart/mixed') || stristr($vals[1], 'multipart/digest'))) {
            $struct = $tools->imap_get_message_structure($uid);
            $msg_parts = get_message_parts($struct);
            if (isset($_GET['message_part'])) {
                $mpart = $_GET['message_part'];
            }
            else {
                $mpart = 0;
            }
            $msg_url = '?page=message&amp;uid='.$uid.'&amp;mailbox='.$tools->get_mailbox();
            if (isset($_GET['sort_by'])) {
                $msg_url .= '&amp;sort_by='.$_GET['sort_by'];
            }
            if (isset($_GET['filter_by'])) {
                $msg_url .= '&amp;filter_by='.$_GET['filter_by'];
            }
            if (isset($_GET['mailbox_page'])) {
                $msg_url .= '&amp;mailbox_page='.$_GET['mailbox_page'];
            }
            $msg_atts = array('url' => $msg_url, 'mpart' => $mpart);
            $tools->add_to_store('message_digest_msgs', $msg_parts);
            $tools->add_to_store('message_digest_vals', $msg_atts);
        }
    }
}
function message_digest_update_settings($tools) {
    if (isset($_POST['enable_digest_display']) && $_POST['enable_digest_display']) {
        $tools->save_options_page_setting('enable_digest_display', 1);
    }
    else {
        $tools->save_options_page_setting('enable_digest_display', 0);
    }
}
function get_message_parts($struct, $list=array()) {
    if (isset($_SESSION['user_settings']['html_first']) && $_SESSION['user_settings']['html_first']) {
        $type = 'html';
    }
    else {
        $type = 'text';
    }
    foreach ($struct as $vals) {
        if (is_array($vals) && isset($vals['subject']) && isset($vals['date']) && isset($vals['from']) && isset($vals['subs']) && is_array($vals['subs'])) {
            $msg_vals = array('subject' => $vals['subject'], 'date' => $vals['date'], 'from' => $vals['from']);
            $index = get_primary_msg_part($vals['subs'], $type);
            if ($index) {
                $list[$index] = $msg_vals;
            }
        }
        if (is_array($vals) && isset($vals['subs']) && is_array($vals['subs'])) {
            $list = get_message_parts($vals['subs'], $list);
        }
    }
    return $list;
}
function get_primary_msg_part($struct, $type) {
    $res = 0;
    $text = false;
    $html = false; 
    foreach ($struct as $index => $vals) {
        if (!isset($vals['type']) && !isset($vals['subtype'])) {
            continue;
        }
        if ($vals['type'] == 'text' && $vals['subtype'] == 'plain') {
            $text = $index;
        }
        if ($vals['type'] == 'text' && $vals['subtype'] == 'html') {
            $html = $index;
        }
    }
    if ($type == 'html' && $html) {
        return $html;
    }
    elseif ($text) {
        return $text;
    }
    return $res;
}
?>
