<?php

/*  url_action_class.php: Process $_GET values
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

class fw_user_action_page extends fw_user_action {
function url_action_message($get) {
    global $sort_types;
    global $client_sort_types;
    global $max_read_length;
    global $user;
    global $imap;
    global $include_path;
    global $message_part_types;
    global $override_missing_mime_header;
    global $add_contact_headers;
    global $conf;
    global $sort_filters;
    global $fd;
    global $sticky_url;
    $user->page_data['top_link'] = '<a href="'.$sticky_url.'#top">'.$user->str[186].'</a>';
    if ($user->logged_in) {
        if (isset($_SESSION['iframe_content']) && $_SESSION['iframe_content'] && isset($get['inline_html']) && $get['inline_html']) {
            ob_clean();
            echo $_SESSION['iframe_content'];
            unset($_SESSION['iframe_content']);
            exit;
        }
        do_work_hook('message_page_start');
        $user->page_data['unseen_uids'] = 0;
        $user->page_data['show_previous_options'] = 0;
        $user->page_data['show_up_options'] = 0;
        $user->page_data['show_next_options'] = 0;
        $user->page_data['full_header'] = -1;
        $user->page_data['full_part_header'] = -1;
        $user->page_data['message_part_headers'] = array();
        $user->page_data['sort_filters'] = array( 'ALL' => 'All messages',
            'UNSEEN' => 'Unread', 'SEEN' => 'Read', 'FLAGGED' => 'Flagged', 'UNFLAGGED' => 'Unflagged',
            'ANSWERED' => 'Answered', 'UNANSWERED' => 'Unanswered', 'DELETED' => 'Deleted', 'UNDELETED' => 'Not Deleted');

        $user->page_data['message_link_class'] ='current_page';
        $mailbox = false;
        if (isset($get['mailbox'])) {
            if (isset($_SESSION['folders'][$get['mailbox']])) {
                $mailbox = $get['mailbox'];
            }
        }
        if (isset($get['full_part_header'])) {
            $user->page_data['full_part_header'] = intval($get['full_part_header']);
        }
        if (isset($get['full_header'])) {
            $user->page_data['full_header'] = intval($get['full_header']);
        }
        if (isset($get['find_response']) && isset($get['current_uid']) && isset($get['response_id'])) {
            $uid = false;
            if (isset($_SESSION['user_settings']['sent_folder'])) {
                $select_res = $imap->select_mailbox($_SESSION['user_settings']['sent_folder'], false, false, true);
                if ($select_res) {
                    $search_res = $imap->simple_search('header in-reply-to', false, $get['response_id']);
                    if (isset($search_res[0])) {
                        $get['uid'] = $search_res[0];
                        $uid = $get['uid'];
                        $mailbox = $_SESSION['user_settings']['sent_folder'];
                        $_GET['mailbox'] = $mailbox;
                        $this->errors[] = $user->str[388];
                    }
                }
            }
            if (!$uid) {
                $get['uid'] = $get['current_uid'];
                $this->errors[] = $user->str[389];
            }
        }
        if ($mailbox && isset($get['uid'])) {
            if (isset($_SESSION['frozen_folders'][$mailbox])) {
                $user->page_data['frozen_dsp'] = '<span id="frozen">(Mailbox Frozen)</span>';
            }
            else {
                $user->page_data['frozen_dsp'] = '';
            }
            $id = (int) $get['uid'];
            if ($id) {
                $sort_by = 'ARRIVAL';
                if (isset($get['sort_by'])) {
                    if (stristr($_SESSION['imap_capability'], 'SORT')) {
                        $types = $sort_types;
                    }
                    else {
                        $types = $client_sort_types;
                    }
                    if (isset($types[$get['sort_by']])) {
                        $sort_by = $get['sort_by'];
                    }
                }
                $filter_by = 'ALL';
                if (isset($get['filter_by'])) {
                    if (isset($sort_filters[$get['filter_by']])) {
                        $filter_by = $get['filter_by'];
                    }
                }
                $user->page_data['filter_by'] = $filter_by;
                $user->page_data['sort_by'] = $sort_by;
                if ($imap->select_mailbox($mailbox, $sort_by, false, true, $filter_by)) {
                    do_work_hook('message_page_selected');
                    $user->page_data['mailbox'] = $mailbox;
                    if ($mailbox == 'INBOX') {
                        $user->page_data['mailbox_dsp'] = $user->str[436];
                    }
                    else {
                        $user->page_data['mailbox_dsp'] = $user->htmlsafe($mailbox, 0, 0, 1);
                    }
                    $user->page_data['url_mailbox'] = urlencode($mailbox);
                    if ((!isset($get['show_all_mp']) || !$get['show_all_mp']) &&
                        isset($_SESSION['user_settings']['short_message_parts']) && $_SESSION['user_settings']['short_message_parts']) {
                        if (isset($_SESSION['user_settings']['html_first']) && $_SESSION['user_settings']['html_first']) {
                            $mfilter = 'html';
                        }
                        else {
                            $mfilter = 'plain';
                        }
                    }
                    else {
                        $mfilter = false;
                    }
                    if ((!isset($get['show_all_mp']) || !$get['show_all_mp'])) {
                        $user->page_data['show_all_mp'] = false;
                    }
                    else {
                        $user->page_data['show_all_mp'] = true;
                    }
                    if ($mfilter) {
                        list($struct, $user->page_data['filtered_mp']) = $imap->get_message_structure($id, $mfilter);
                    }
                    else {
                        $user->page_data['filtered_mp'] = 0;
                        $struct = $imap->get_message_structure($id);
                    }
                    $flat_list = $this->get_flat_part_list($struct);
                    $user->page_data['part_nav_list'] = $flat_list;
                    if (isset($get['message_part'])) {
                        $mpart = $get['message_part'];
                    }
                    else {
                        $mpart = 0;
                    }
                    $sort_by = 'ARRIVAL';
                    if (!isset($get['show_image']) && !isset($get['download']) || isset($get['framed_part'])) {
                        $user->page_data['full_message_headers'] = $imap->get_message_headers($id, false);
                    }
                    if ($override_missing_mime_header) {
                        $struct = $this->override_missing_mime_header($struct, $user->page_data['full_message_headers']);
                    }
                    if (isset($_SESSION['user_settings']['html_first']) && $_SESSION['user_settings']['html_first']) {
                        list($message_data, $viewable) = $this->find_message_part($struct, $mpart, 'text', 'html');
                        if (empty($message_data) && !empty($viewable)) {
                            list($message_data, $viewable) = $this->find_message_part($struct, $viewable[0]);
                        }
                    }
                    else {
                        list($message_data, $viewable) = $this->find_message_part($struct, $mpart, 'text', 'plain');
                        if (empty($message_data) && !empty($viewable)) {
                            list($message_data, $viewable) = $this->find_message_part($struct, $viewable[0]);
                        }
                    }
                    if (isset($get['raw_view']) && $get['raw_view']) {
                        $raw = 1;
                        $message_data['imap_id'] = 0;
                        $message_data['type'] = 'text';
                        $message_data['subtype'] = 'plain';
                    }
                    else {
                        $raw = 0;
                    }
                    if ((empty($message_data) && !empty($user->page_data['full_message_headers']))) {
                        $message_data['imap_id'] = 0;
                        $message_data['type'] = 'text';
                        $message_data['subtype'] = 'plain';
                        $user->page_data['broken_msg'] = true;
                    }
                    $user->page_data['raw_view'] = $raw;
                    $user->page_data['message_struct'] = $struct;
                    $user->page_data['message_uid'] = $id;
                    $user->page_data['message_part'] = $mpart;
                    $count = $_SESSION['uid_cache'][$mailbox]['total'];
                    $count = $_SESSION['uid_cache'][$mailbox]['total'];
                    $page = 1;
                    if (isset($get['mailbox_page'])) {
                        $page = (int) $get['mailbox_page'];
                        if (!$page) {
                            $page = 1;
                        }
                    } 
                    $user->page_data['previous_uid'] = false;
                    $user->page_data['uid_index'] = false;
                    $user->page_data['next_uid'] = false;
                    for ($i=0;$i<$count;$i++) {
                        if ($id == $_SESSION['uid_cache'][$mailbox]['uids'][$i]) {
                            $page = floor($i/$user->page_data['settings']['mailbox_per_page_count']) + 1;
                            $user->page_data['uid_index'] = $i;
                            if (isset($_SESSION['uid_cache'][$mailbox]['uids'][($i + 1)])) {
                                $user->page_data['next_uid'] = $_SESSION['uid_cache'][$mailbox]['uids'][($i + 1)];
                                $user->page_data['next_uid_page'] = floor(($i + 1)/$user->page_data['settings']['mailbox_per_page_count']) + 1;
                            }
                            if (isset($_SESSION['uid_cache'][$mailbox]['uids'][($i - 1)])) {
                                $user->page_data['previous_uid'] = $_SESSION['uid_cache'][$mailbox]['uids'][($i - 1)];
                                $user->page_data['prev_uid_page'] = floor(($i - 1)/$user->page_data['settings']['mailbox_per_page_count']) + 1;
                            }
                            break;
                        }
                    }
                    $user->page_data['show_small_headers'] = 0;
                    $user->page_data['show_full_headers'] = 0;
                    if (isset($get['full_headers']) && $get['full_headers']) {
                        $user->page_data['show_full_headers'] = 1;
                    }
                    elseif (isset($get['small_headers']) && $get['small_headers']) {
                        $user->page_data['show_small_headers'] = 1;
                    }
                    if (!empty($message_data)) {
                        $user->page_data['message_part'] = $message_data['imap_id'];
                        $type = strtolower($message_data['type'].'/'.$message_data['subtype']);
                        $user->page_data['charset'] = 'us-ascii';
                        if (isset($message_data['charset'])) {
                            $user->page_data['charset'] = strtolower($message_data['charset']);
                        }
                        $user->page_data['raw_message_type'] = $type;
                        if (isset($message_part_types[$type]) || isset($get['download'])) {
                            $user->page_data['message_type'] = false;
                            if (isset($message_part_types[$type])) {
                                $user->page_data['message_type'] = $message_part_types[$type];
                            }
                            if (isset($get['show_image']) && strtolower($message_data['type'] == 'image')) {
                                $data = $imap->get_message_part($id, $message_data['imap_id']);
                                if (isset($message_data['encoding']) && strtolower($message_data['encoding']) == 'base64') {
                                    $data = base64_decode($data);
                                }
                                elseif (isset($message_data['encoding']) && strtolower($message_data['encoding']) == 'quoted-printable') {
                                    $data = quoted_printable_decode($data);
                                }
                                ob_end_clean();
                                if ($data) {
                                    if (isset($get['thumbnail']) && $get['thumbnail'] && function_exists('imagecreatefromstring')) {
                                        $im = @imagecreatefromstring($data);
                                        $width = imagesx($im);
                                        $height = imagesy($im);
                                        $max_width = 80;
                                        $max_height = 60;
                                        if ($width > $max_width) {
                                            $new_width = $max_width;
                                            $new_height = ($new_width*$height)/$width;
                                            if ($new_height > $max_height) {
                                                $new_height = $max_height;
                                                $new_width = ($new_height*$width)/$height;
                                            }
                                        }
                                        elseif ($height > $max_height) {
                                            $new_height = $max_height;
                                            $new_width = ($new_height*$width)/$height;
                                        }
                                        else {
                                            $new_height = $height;
                                            $new_width = $width;
                                        }
                                        if (!$new_height || !$new_width) {
                                            $new_height = 50;
                                            $new_width = 50;
                                        }
                                        $im2 = @imagecreatetruecolor($new_width, $new_height);
                                        imagecolortransparent($im2, imagecolorallocate($im2, 0, 0, 0));
                                        imagealphablending($im2, false);
                                        imagesavealpha($im2, true);
                                        if ($im2 !== false && $im !== false) {
                                            imagecopyresampled($im2, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                            imagepng($im2);
                                        }
                                        $imap->disconnect();
                                        $user->clean_up();
                                        exit;
                                    }
                                    $size = (int) $message_data['size'];
                                    header("Content-Type: ".$type);
                                    header("Content-Length: ".strlen($data));
                                    echo $data;
                                    $imap->disconnect();
                                    $user->clean_up();
                                    exit;
                                }
                            }
                            elseif (isset($get['framed_part'])) {
                                if (isset($message_data['filename']) && $message_data['filename']) {
                                    $name = $message_data['filename'];
                                }
                                elseif (isset($message_data['name']) && $message_data['name']) {
                                    $name = $message_data['name'];
                                }
                                else {
                                    $name = 'message_'.$message_data['imap_id'];
                                }
                                $exten = get_mimetype_extension($type);
                                if (strtolower(substr($name, -4)) != $exten) {
                                    $name .= $exten;
                                }
                                $encoding = false;
                                if (isset($message_data['encoding']) && strtolower($message_data['encoding']) == 'base64') {
                                    $encoding = 'base64_decode';
                                }
                                elseif (isset($message_data['encoding']) && strtolower($message_data['encoding'] == 'quoted-printable')) {
                                    $encoding =  'quoted_decode';
                                }
                                $left_over = '';
                                $read_size = 0;
                                $lit_size = $imap->get_message_part_start($id, $message_data['imap_id']);
                                header("Content-Type: $type");
                                header('Content-Transfer-Encoding: binary');
                                header('Accept-Ranges: bytes');
                                header("Pragma: public");
                                header("Expires: 0");
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                ob_end_clean();
                                while ($data = $imap->get_message_part_line()) {
                                    $read_size += strlen($data);
                                    if ($read_size > $lit_size) {
                                        $extra = $read_size - $lit_size;
                                        if (strlen($data) > $extra) {
                                            $data = substr($data, 0, (strlen($data) - $extra));
                                        }
                                        else {
                                            $data = false;
                                        }
                                    }
                                    if ($data && $encoding == 'base64_decode') {
                                        $data = base64_decode($data);
                                    }
                                    elseif ($data && $encoding == 'quoted_decode') {
                                        $data = $user->user_action->quoted_decode($data);
                                    }
                                    if ($data) {
                                        echo $data;
                                        flush();
                                    }
                                    $data = false;
                                }
                                $imap->disconnect();
                                $user->clean_up();
                                exit;
                            }
                            elseif (isset($get['download'])) {
                                if (isset($message_data['filename']) && $message_data['filename']) {
                                    $name = $message_data['filename'];
                                }
                                elseif (isset($message_data['name']) && $message_data['name']) {
                                    $name = $message_data['name'];
                                }
                                else {
                                    $name = 'message_'.$message_data['imap_id'];
                                }
                                $exten = get_mimetype_extension($type);
                                if (strtolower(substr($name, -4)) != $exten) {
                                    $name .= $exten;
                                }
                                $encoding = false;
                                if (isset($message_data['encoding']) && strtolower($message_data['encoding']) == 'base64') {
                                    $encoding = 'base64_decode';
                                }
                                elseif (isset($message_data['encoding']) && strtolower($message_data['encoding'] == 'quoted-printable')) {
                                    $encoding =  'quoted_decode';
                                }
                                $left_over = '';
                                $read_size = 0;
                                $lit_size = $imap->get_message_part_start($id, $message_data['imap_id']);
                                header("Content-Type: text"); //$type");
                                header('Content-Transfer-Encoding: binary');
                                header('Accept-Ranges: bytes');
                                if (isset($message_data['att_size']) && intval($message_data['att_size']) > 0) {
                                    header('Content-Length: '.$message_data['att_size']);
                                }
                                elseif (!$encoding && intval($lit_size) > 0) {
                                    header('Content-Length: '.$lit_size);
                                }
                                header("Pragma: public");
                                header("Expires: 0");
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Content-Disposition: attachment; filename="'.$name.'"');
                                ob_end_clean();
                                while ($data = $imap->get_message_part_line()) {
                                    $read_size += strlen($data);
                                    if ($read_size > $lit_size) {
                                        $extra = $read_size - $lit_size;
                                        if (strlen($data) > $extra) {
                                            $data = substr($data, 0, (strlen($data) - $extra));
                                        }
                                        else {
                                            $data = false;
                                        }
                                    }
                                    if ($data && $encoding == 'base64_decode') {
                                        $data = base64_decode($data);
                                    }
                                    elseif ($data && $encoding == 'quoted_decode') {
                                        $data = $user->user_action->quoted_decode($data);
                                    }
                                    if ($data) {
                                        echo $data;
                                    }
                                    $data = false;
                                }
                                $imap->disconnect();
                                $user->clean_up();
                                ob_flush();
                                flush();
                                exit;
                            }
                            else {
                                if (isset($_SESSION['header_cache'][$mailbox][$page][$id]['flags'])) {
                                    if (!stristr($_SESSION['header_cache'][$mailbox][$page][$id]['flags'], 'seen')) {
                                        if (isset($_SESSION['folders'][$mailbox]['status']['unseen']) && $_SESSION['folders'][$mailbox]['status']['unseen'] > 0) {
                                            $_SESSION['folders'][$mailbox]['status']['unseen'] -= 1;
                                            $user->page_data['folders'] = $_SESSION['folders'];
                                        }
                                    }
                                    if (!stristr($_SESSION['header_cache'][$mailbox][$page][$id]['flags'], 'Seen')) {
                                        $_SESSION['header_cache'][$mailbox][$page][$id]['flags'] .= ' \Seen';
                                        if (isset($_SESSION['total_unread']) && $_SESSION['total_unread'] > 0 &&
                                            isset($_SESSION['user_settings']['folder_check']) && is_array($_SESSION['user_settings']['folder_check']) &&
                                            in_array($mailbox, $_SESSION['user_settings']['folder_check'])) {
                                            $_SESSION['total_unread']--;
                                        }
                                    }
                                }
                                if (!isset($user->page_data['full_message_headers'])) {
                                    $user->page_data['full_message_headers'] = $imap->get_message_headers($id, false);
                                }
                                $user->page_data['message_headers'] = $this->prep_headers($user->page_data['full_message_headers']);
                                if (!$user->page_data['charset']) {
                                    foreach ($user->page_data['message_headers'] as $vals) {
                                        if (strtolower($vals[0]) == 'content-type') {
                                            if (preg_match("/charset=([^\s;]+)/", $vals[1], $matches)) {
                                                $user->page_data['charset'] = trim(str_replace(array("'", '"'), '', $matches[1]));
                                            }
                                            break;
                                        }
                                    }
                                }

                                if (count($user->page_data['part_nav_list']) > 1) {
                                    $parent_id = 0;
                                    foreach ($flat_list as $vals) {
                                        if ($vals[0] == $message_data['imap_id'] && $vals[1]) {
                                            $parent_id = $vals[1];
                                            break;
                                        }
                                    }
                                    if ($parent_id && $parent_id != 1) {
                                        $user->page_data['message_part_headers'] = $this->prep_headers($imap->get_message_headers($id, $parent_id));
                                    }
                                }
                                if ($raw) {
                                    $user->page_data['message_data'] = $imap->get_message_part($id, false, $raw, $max_read_length);
                                    if (isset($user->page_data['message_part_headers'])) {
                                        unset($user->page_data['message_part_headers']);
                                    }
                                    $user->page_data['part_nav_list'] = array();
                                }
                                elseif ($message_part_types[$type] == 'text' || $message_part_types[$type] == 'html') {
                                    $user->page_data['message_data'] = $imap->get_message_part($id, $message_data['imap_id'], $raw, $max_read_length);
                                    if ($imap->max_read) {
                                        $this->errors[] = $user->str[390];
                                        $imap->max_read = false;
                                    }
                                    if ($message_part_types[$type] == 'html') {
                                        if (isset($get['show_external_images'])) {
                                            if ($get['show_external_images']) {
                                                $user->page_data['show_external_images'] = true;
                                            }
                                            else { 
                                                $user->page_data['show_external_images'] = false;
                                            }
                                        }
                                    }
                                    if (!$raw) {
                                        if (isset($message_data['encoding']) && strtolower($message_data['encoding']) == 'base64') {
                                            $user->page_data['message_data'] = base64_decode($user->page_data['message_data']);
                                        }
                                        elseif (isset($message_data['encoding']) && strtolower($message_data['encoding'] == 'quoted-printable')) {
                                            $user->page_data['message_data'] = $user->user_action->quoted_decode($user->page_data['message_data']);
                                        }
                                    }
                                    if (isset($conf['html_message_iframe']) && $conf['html_message_iframe'] && $message_part_types[$type] == 'html') {
                                        if ($user->sub_class_names['url']) {
                                            $class_name = 'site_page_'.$user->sub_class_names['url'];
                                            $pd = hm_new($class_name);
                                        }
                                        else {
                                            $pd = hm_new('site_page');
                                        }
                                        $_SESSION['iframe_content'] = $pd->print_message_iframe_content();
                                    }
                                    if (strstr($type, 'x-vcard')) {
                                        require_once($include_path.'lib'.$fd.'vcard.php');
                                        $vcard = hm_new('vcard');
                                        $vcard->import_card(explode("\r\n", $user->page_data['message_data']));
                                        if (is_array($vcard->card) && !empty($vcard->card)) {
                                            $user->page_data['card_detail'] = $vcard->card;
                                            $_SESSION['import_card_detail'] = $vcard->card;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!isset($user->page_data['full_message_headers'])) {
                        $user->page_data['full_message_headers'] = $imap->get_message_headers($id, false);
                    }
                    if (!isset($user->page_data['charset'])) {
                        $user->page_data['charset'] = false;
                        if (isset($user->page_data['message_headers'])) {
                            foreach ($user->page_data['message_headers'] as $vals) {
                                if (strtolower($vals[0]) == 'content-type') {
                                    if (preg_match("/charset=([^ ]+)/", $vals[1], $matches)) {
                                        $user->page_data['charset'] = trim(str_replace(array("'", '"'), '', $matches[1]));
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    $user->page_data['mailbox_page'] = $page;
                    $user->page_data['mailbox_total'] = $_SESSION['uid_cache'][$mailbox]['total'];
                    $user->page_data['page_links'] = build_page_links($page, $_SESSION['uid_cache'][$mailbox]['total'],
                                                     $user->page_data['settings']['mailbox_per_page_count'], '?page=mailbox&amp;sort_by='.$sort_by.
                                                     '&amp;filter_by='.$filter_by.'&amp;mailbox='.urlencode($mailbox), $user->str[88]);
                    $user->dsp_page = 'message';
                    $_SESSION['last_message_read'][$mailbox] = $id;
                    if (isset($get['print_view']) && $get['print_view']) {
                        ob_clean();
                        if ($user->sub_class_names['url']) {
                            $class_name = 'site_page_'.$user->sub_class_names['url'];
                            $pd = hm_new($class_name);
                        }
                        else {
                            $pd = hm_new('site_page');
                        }
                        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                              <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
                                <head><title>Message Print View</title><base href="'.$pd->pd['base_href'].'" /><title id="title">'.$pd->user->page_title.'</title>
                                <style type="text/css">table {padding:10px;margin-left:-10px;padding-bottom:20px;}
                                table td {padding-left:10px;} table th {text-align:left;font-weight:normal;}
                                pre {white-space: pre-wrap; white-space: -moz-pre-wrap !important; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;}
                                </style></head>
                            <body style="background: none; background-image: none; background-color: #fff; color: #000; margin: 30px;">'.
                            '<table>'.$pd->print_message_headers().'</table>';
                            if (isset($pd->pd['message_part_headers'])) {
                                '<table>'.$pd->print_part_headers().'</table>';
                            }
                        //$pd->pd['raw_view'] = true;
                        switch ($user->page_data['message_type']) {
                            case 'text':
                            case 'image':
                            case 'html':
                                echo $pd->{'print_message_'.$user->page_data['message_type']}();
                                break;
                            default:
                                echo '<div style="text-align: center; margin-top: 100px;">Unsupported MIME type: '.
                                        $user->htmlsafe($user->page_data['raw_message_type']).'</div>';
                                break;
                        }
                        echo '
                        </body>
                        </html>'; 
                        exit;
                    }
                    if (isset($get['show_up_options'])) {
                        $user->page_data['show_up_options'] = 1;
                    }
                    if (isset($get['show_next_options'])) {
                        $user->page_data['show_next_options'] = 1;
                    }
                    if (isset($get['show_previous_options'])) {
                        $user->page_data['show_previous_options'] = 1;
                    }
                    $new_contacts = array();
                    if (isset($user->page_data['full_message_headers'])) {
                        foreach($user->page_data['full_message_headers'] as $vals) {
                            $i = $vals[0];
                            $v = $vals[1];
                            if (!in_array(strtolower($i), $add_contact_headers)) {
                                continue;
                            }
                            if (strstr($v, ' ')) {
                                $bits = explode(' ', trim($v));
                            }
                            elseif (strstr($v, ',')) {
                                $bits = explode(',', trim($v));
                            }
                            else {
                                $bits = array($v);
                            }
                            foreach ($bits as $val) {
                                $val = rtrim(ltrim($val, '<,'), '>,');
                                if ($this->match_email($val)) {
                                    $new_contacts[] = $val;
                                }
                            }
                        }
                    }
                    $user->page_data['new_contacts'] = $new_contacts;
                    list($total_unread, $user->page_data['unseen_uids']) = $imap->get_mailbox_unseen($mailbox);
                    if ($total_unread) {
                        $user->page_data['unseen_uids'][] = $user->page_data['message_uid'];
                        $user->page_data['unseen_uids'] = $this->sort_uid_list($user->page_data['unseen_uids'], $mailbox);
                    }
                    if (isset($_SESSION['search_results'][$mailbox])) {
                        $user->page_data['search_results'] = $this->sort_uid_list($_SESSION['search_results'][$mailbox], $mailbox);
                    }
                    if (isset($_SESSION['uid_cache'][$mailbox]['thread_data'])) {
                        $user->page_data['thread_data'] = $_SESSION['uid_cache'][$mailbox]['thread_data'];
                    }
                    $user->page_data['folders'] = $_SESSION['folders'];
                    $user->page_title .= ' | Message |';
                }
            }
        }
    }
}
function sort_uid_list($unseen_uids, $mailbox) {
    if (isset($_SESSION['uid_cache'][$mailbox]['uids'])) {
        $res = array();
        $all_cnt = count($_SESSION['uid_cache'][$mailbox]['uids']);
        $unseen_cnt = count($unseen_uids);
        $unseen_keys = array_flip($unseen_uids);
        $cnt = 0;
        foreach ($_SESSION['uid_cache'][$mailbox]['uids'] as $i => $v) {
            if (isset($unseen_keys[$v])) {
                $res[] = $v;
                $cnt++;
            }
            if ($cnt == $unseen_cnt) {
                break;
            }
        }
    }
    else {
        $res = $uids;
    }
    return $res;
}
function prep_headers($headers) {
    global $user;
    $header_flags = false;
    $short_list = $_SESSION['user_settings']['small_headers'];
    $data = '';
    if ($user->page_data['raw_view']) {
        $res = array();
    }
    elseif ($_SESSION['user_settings']['full_headers_default'] && !$user->page_data['show_small_headers']) {
        $res = $headers;
    }
    elseif ($user->page_data['show_full_headers']) {
        $res = $headers;
    }
    elseif (count($headers) == 1 && $headers[0][0] == 'Flags') {
        $res = array ();
    }
    else {
        $res = array();
        foreach ($short_list as $v) {
            $found = false;
            foreach ($headers as $vals) {
                if (strtolower($vals[0]) == $v) {
                    $res[] = array($vals[0], $vals[1]);
                    $found = true;
                    break;
                }
                elseif ($vals[0] == 'Flags') {
                    $header_flags = $vals[1];
                    if ($v == 'IMAP message flags') {
                        $res[] = array($vals[0], $vals[1]);
                        break;
                    }
                }
            }
        }
    }
    if ($header_flags) {
        $user->page_data['header_flags'] = $header_flags;
    }
    return $res;
}
function get_flat_part_list($struct, $list=array(), $parent=0) {
    global $message_part_types;
    foreach ($struct as $i => $v) {
        if (isset($v['type']) && $v['subtype']) {
            if (isset($message_part_types[strtolower($v['type'].'/'.$v['subtype'])])) {
                $list[] = array($i, $parent);
            }
        }
        if (isset($v['subs']) && is_array($v['subs'])) {
            $list = $this->get_flat_part_list($v['subs'], $list, $i);
        }
    }
    return $list;
}
function get_prev_next_uids($uids, $current) {
    $count = count($uids);
    $index = false;
    $next = false;
    $prev = false;
    if (!empty($uids)) {
        for ($i=0;$i<$count;$i++) {
            if ($uids[$i] == $current) {
                $index = $i;
                break;
            }
        }
        if ($index !== false) {
            if (isset($uids[($index - 1)])) {
                $prev = $uids[($index - 1)];
            }
            if (isset($uids[($index + 1)])) {
                $next = $uids[($index + 1)];
            }
        }
    }
    return array($prev, $next);
}
function override_missing_mime_header($struct, $headers) {
    if (count($struct) != 1) {
        return $struct;
    }
    $found = false;
    $ctype = false;
    foreach ($headers as $vals) {
        if (strtolower($vals[0]) == 'mime-version') {
            $found = true;
        }
        if (strtolower($vals[0]) == 'content-type') {
            $ctype = $vals[1];
            if ($found) {
                break;
            }
        }
    }
    if (!$found) {
        if (!strstr(strtolower($ctype), 'text/plain') && isset($struct[1]['type']) && isset($struct[1]['subtype']) &&
            strtolower($struct[1]['type'].$struct[1]['subtype']) == 'textplain') {
            if (preg_match("/([a-z\-]+)\/([a-z\-]+)/i", $ctype, $matches)) {
                if (count($matches) > 2) {
                    $struct[1]['type'] = $matches[1];
                    $struct[1]['subtype'] = $matches[2];
                }
            }
        }
    }
    return $struct;
}
}

class site_page_message extends site_page {
function print_message_text() {
    global $simple_msg_font_size;
    $data = '<div id="message_text" style="font-family: '.$this->pd['settings']['font_family'].';">';
    if ($this->pd['raw_view']) {
        $data .= '<div id="raw_message_text"><pre id="msg_pre">'.$this->user->htmlsafe($this->pd['message_data'], false, false, false, false, false, true).'</pre></div>';
    }
    elseif (isset($this->pd['card_detail'])) {
        $data .= $this->print_contact_detail(true);
    }
    else {
        $data .= '<pre ';
        if ($simple_msg_font_size && isset($this->pd['settings']['display_mode']) && $this->pd['settings']['display_mode'] == 2) {
            $data .= 'style="font-size: '.$simple_msg_font_size.'pt !important;" ';
        }
        $data .= 'id="msg_pre">'.prep_text_part($this->pd['message_data'], $this->pd['charset']).'</pre>';
    }
    $data .= '</div>';
    return $data;
}
function print_message_html($clean=false) {
    global $simple_msg_font_size;
    $override = false;
    if (isset($this->pd['settings']['remote_image']) && $this->pd['settings']['remote_image']) {
        $image_replace = false;
    }
    else {
        $image_replace = true;
    }
    if (isset($this->pd['show_external_images']) && $this->pd['show_external_images']) {
        $image_replace = false;
        $override = true;
    }
    $data = '';
    if (!$clean) {
        $data .= '<div id="message_html" ';
        if ($simple_msg_font_size && isset($this->pd['settings']['display_mode']) && $this->pd['settings']['display_mode'] == 2) {
            $data .= 'style="font-size: '.$simple_msg_font_size.'pt !important;" ';
        }
        $data .= '><table cellpadding="0" cellspacing="0" width="100%"><tr><td>';
    }
    $data .= $this->user->htmlclean(prep_html_part($this->pd['message_data'], $this->pd['message_uid'], $this->pd['mailbox'],
        $image_replace, $override), array(), false, $this->pd['charset']);
    if (!$clean) {
        $data .= '</td></tr></table></div>';
    }
    return $data;
}
function print_message_image() {
    if (!$this->user->use_cookies) {
        $sess = '&PHPSESSID='.session_id();
    }
    else {
        $sess = '';
    }
    $data = '<div id="message_image"><img src="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;message_part='.
             $this->pd['message_part'].'&amp;show_image=1&amp;mailbox='.$this->pd['mailbox'].$sess.'" /></div>';
    return $data;
}
function print_message_parts_inner($parts, $url_base, $level=0) {
    global $message_part_types;
    global $page_id;
    if ((!isset($this->pd['show_all_mp']) || !$this->pd['show_all_mp']) &&
        isset($this->pd['settings']['short_message_parts']) && $this->pd['settings']['short_message_parts']) {
        $small = true;
    }
    else {
        $small = false;
    }
    $data = '';
    if (!is_array($parts) || empty($parts)) {
        return;
    }
    if (isset($this->pd['settings']['trim_from_fld']) && $this->pd['settings']['trim_from_fld']) {
        $trim_len = $this->pd['settings']['trim_from_fld'];
    }
    else {
        $trim_len = 0;
    }
    foreach ($parts as $id => $vals) {
        if ($small && is_array($vals) && isset($vals['subs']) && !empty($vals['subs'])) {
            if (isset($vals['subtype']) && $vals['subtype'] != 'alternative') {
                $this->pd['filtered_mp'] += 1;
            }
            $level += 1;
            $data .= $this->print_message_parts_inner($vals['subs'], $url_base, ($level + 1), $small);
            $level -= 1;
            continue;
        }
        if ($small && isset($_SESSION['inline_images'][$this->pd['mailbox']][$this->pd['message_uid']]) &&
            !empty($_SESSION['inline_images'][$this->pd['mailbox']][$this->pd['message_uid']])) {
            $inline_images = $_SESSION['inline_images'][$this->pd['mailbox']][$this->pd['message_uid']];
            $found = false;
            foreach ($inline_images as $name) {
                if (isset($vals['filename']) && $vals['filename'] == $name) {
                    $found = true;
                    break;
                }
                elseif (isset($vals['name']) && $vals['name'] == $name) {
                    $found = true;
                    break;
                }
                elseif (isset($vals['id']) && strstr($vals['id'], $name)) {
                    $found = true;
                    break;
                }
                elseif (isset($vals['description']) && $vals['description'] == $name) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $this->pd['filtered_mp'] += 1;
                continue;
            }
        }
        if (isset($vals['type']) && isset($vals['subtype']) && count($vals) > 2) {
            $current = false;
            if (!isset($vals['charset'])) {
                $vals['charset'] = false;
            }
            $pad = str_repeat('&#160;', 7*$level);
            if ($id == $this->pd['message_part']) {
                $current = true;
                $pre = '<complex-'.$page_id.'><div class="current_part">-&gt;</div></complex-'.$page_id.'>
                        <simple-'.$page_id.'>-&gt;</simple-'.$page_id.'>';
            }
            else {
                $pre = '<complex-'.$page_id.'><div class="current_part" style="visibility: hidden;">-&gt;</div></complex-'.$page_id.'>';
            }
            if ($vals['subtype'] != 'rfc822' && $level > 0) {
                $xtra_class = 'inner_part';
            }
            else {
                $xtra_class = '';
            }
            if (isset($message_part_types[strtolower($vals['type'].'/'.$vals['subtype'])])) {
                $data .= '<tr ';
                if ($current) { $data .= 'class="current_part_row" '; }
                $data .= '><td nowrap="nowrap" class="view_cell '.$xtra_class.'">'.$pre;
                $data .=' <a href="'.$url_base.'&amp;message_part='.$id.$this->pd['new_window_arg'].'">'.$this->user->str[85].'</a> ';
                if ($message_part_types[strtolower($vals['type'].'/'.$vals['subtype'])] == 'frame') {
                    $data .=' &#160;<span style="position: relative; padding-right: 12px;"><a target="_blank" href="'.$url_base.
                            '&amp;framed_part=1&amp;message_part='.$id.'" title="'.$this->user->str[530].'">'.
                            '<span style="position: absolute; top: 0px;" class="new_window_icon"></span></a></span> ';
                }
                $data .= '&#160;| &#160;';
                $data .= '<a href="'.$url_base.'&amp;message_part='.$id.
                         '&amp;download=1">'.$this->user->str[86].'</a>';
                $data .= '</td>';
            }
            else {
                $data .= '<tr><td nowrap="nowrap" class="view_cell '.$xtra_class.'">'.$pre.
                         ' <a href="'.$url_base.'&amp;message_part='.$id.'&amp;download=1">'.$this->user->str[86].'</a></td>';
            }
            $data .= '<td class="small_cell '.$xtra_class.'">'.$pad.$this->user->htmlsafe($vals['type']).' / '.$this->user->htmlsafe($vals['subtype']).'</td>';
            if (isset($vals['filename']) && trim($vals['filename'])) {
                $vals['name'] = $this->user->htmlsafe($vals['filename'], $vals['charset'], true);
            }
            elseif (!isset($vals['name']) || !trim($vals['name'])) {
                $vals['name'] = 'message_'.$id;
            }
            $data .= '<td class="filename_cell '.$xtra_class.'">'.$this->user->htmlsafe($vals['name'], $vals['charset'], true);
            if (isset($message_part_types[strtolower($vals['type'].'/'.$vals['subtype'])]) && strtolower($vals['type']) == 'image') {
                if (isset($this->pd['settings']['image_thumbs']) && $this->pd['settings']['image_thumbs']) {
                    if (!$this->user->use_cookies) {
                        $sess = '&PHPSESSID='.session_id();
                    }
                    else {
                        $sess = '';
                    }
                    $data .= '<br /><img src="?page=message&amp;thumbnail=1&amp;rand='.$page_id.'&amp;show_image=1&amp;mailbox='.
                          urlencode($this->pd['mailbox']).'&amp;uid='.$this->pd['message_uid'].'&amp;message_part='.$id.$sess.'" alt="attached_image" />';
                }
            } 
            $data .= '</td><td class="description_cell '.$xtra_class.'">';
            $meta = '';
            if (isset($vals['subject']) && $vals['subject']) {
                $meta .= '<b>'.$this->user->str[13].'</b>: '.$this->user->htmlsafe(stripslashes($vals['subject']), $vals['charset'], true).'<br />';
                if (isset($vals['from']) && $vals['from']) {
                    $meta .= '<b>'.$this->user->str[56].'</b>: '.trim_htmlstr($this->user->htmlsafe(stripslashes($vals['from']), $vals['charset'], true), $trim_len).'<br />';
                }
            }
            if (isset($vals['description']) && $vals['description'] && strtoupper($vals['description']) != 'NIL') {
                $meta .= $this->user->htmlsafe(stripslashes($vals['description']), $vals['charset'], true);
            }
            if (isset($vals['att_size']) && intval($vals['att_size']) > 0) {
                $size = $vals['att_size'];
            }
            else {
                $size = $vals['size'];
            }
            $data .= $meta.'</td><td class="small_cell '.$xtra_class.'">'.$this->user->htmlsafe($vals['charset']).'</td><td class="small_cell '.
                     $xtra_class.'">'.$this->user->htmlsafe($vals['encoding']).'</td><td class="small_cell '.$xtra_class.'">'.format_size($size/1024).'</td></tr>';
        }
        if (isset($vals['subs']) && !empty($vals['subs'])) {
            if (count($vals) == 1) {
                $level_offset = 0;
            }
            else {
                $level_offset = 1;
            }
            $data .= $this->print_message_parts_inner($vals['subs'], $url_base, ($level + $level_offset), $small);
        }
    }
    return $data;
}
function print_message_parts() {
    $data = '<tr><th></th><th>'.$this->user->str[81].'</th><th>'.$this->user->str[43].'</th><th>'.$this->user->str[82].'</th><th>'.
            $this->user->str[83].'</th><th>'.$this->user->str[84].'</th><th>'.$this->user->str[57].'</th></tr>';
    $url_base = '?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].
                '&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['mailbox_page'];
    if (isset($this->pd['show_all_mp']) && $this->pd['show_all_mp']) {
        $data .= $this->print_message_parts_inner($this->pd['message_struct'], $url_base.'&amp;show_all_mp=1');
    }
    else {
        $data .= $this->print_message_parts_inner($this->pd['message_struct'], $url_base);
    }
    if (isset($this->pd['settings']['short_message_parts']) && $this->pd['settings']['short_message_parts']) {
        if ($this->pd['filtered_mp']) {
            $data .= '<tr><td class="view_cell"><a href="'.$url_base.'&amp;show_all_mp=1'.$this->pd['new_window_arg'].
                    '#parts">'.$this->user->str[456].'</a></td></tr>';
        }
        elseif (isset($this->pd['show_all_mp']) && $this->pd['show_all_mp']) {
            $data .= '<tr><td class="view_cell"><a href="'.$url_base.$this->pd['new_window_arg'].
                 '#parts">'.$this->user->str[457].'</a></td></tr>';
        }
    }
    $data .= do_display_hook('message_parts_table');
    return $data;
}
function print_message_headers($rows=0) {
    global $max_header_length;
    global $sticky_url;
    $short_list = array_flip($_SESSION['user_settings']['small_headers']);
    $data = do_display_hook('message_headers_top');
    $index = 0;
    if (isset($this->pd['message_headers'])) {
        foreach ($this->pd['message_headers'] as $index => $vals) {
            $name = $this->user->htmlsafe($vals[0], $this->pd['charset'], true);
            switch (strtolower($name)) {
                case 'subject':
                    $name = $this->user->str[13];
                    break;
                case 'from':
                    $name = $this->user->str[56];
                    break;
                case 'to':
                    $name = $this->user->str[55];
                    break;
                case 'date':
                    $name = $this->user->str[58];
                    break;
            }
            if (isset($short_list[strtolower($name)])) {
                unset($short_list[strtolower($name)]);
            }
            elseif (strtolower($name) == 'flags' and isset($short_list['IMAP message flags'])) {
                unset($short_list['IMAP message flags']);
            }
            $val = $this->user->htmlsafe($vals[1], $this->pd['charset'], true);
            if ($this->pd['full_header'] == $index) {
                $val .= ' &#160;<a href="'.preg_replace('/\&amp;full_header\=\d+/', '', $sticky_url).'">'.$this->user->str[522].'</a>';
            }
            elseif (htmlstrlen($val) > $max_header_length) {
                $val = trim_htmlstr($val, $max_header_length).' &#160;<a href="'.
                    preg_replace('/\&amp;full_header\=\d+/', '', $sticky_url).
                    '&amp;full_header='.$index.'">'.$this->user->str[521].'</a>';
            }
            $data .= '<tr><th>'.$name.': </th><td ';
            if ($index >= $rows) {
                $data .= 'colspan="2" ';
            }
            if (strtolower($vals[0]) == 'subject') {
                $data .= 'class="subject_cell" ';
            }
            if (strtolower($vals[0]) == 'date' && $vals[1] && !$this->pd['show_full_headers']) {
                $data .= '>'.date('r', strtotime((trim($val))));
                $data .= ' &#160;&#160; ('.print_time(strtotime($vals[1]), $vals[1]).')';
            }
            else {
                $data .= '>'.$val;
            }
            $data .= '</td></tr>';
        }
    }
    if (!empty($short_list) && (!isset($this->pd['raw_view']) || !$this->pd['raw_view'])) {
        foreach ($short_list as $k => $v) {
            $data .= '<tr><th>'.ucfirst($k).':</th><td ';
            if (($index+$k) >= $rows) {
                $data .= 'colspan="2" ';
            }
            $data .= '></td></tr>';
        }
    }
    if (!isset($this->pd['raw_view']) || !$this->pd['raw_view']) {
        $data .= $this->print_add_contact_form();
    }
    return $data;
}
function print_message_prev_next_part() {
    global $conf;
    global $sticky_url;
    $data = '';
    $prev_part = false;
    $next_part = false;
    $part = $this->pd['message_part'];

    if (isset($this->pd['part_nav_list']) && !empty($this->pd['part_nav_list'])) {
        $count = count($this->pd['part_nav_list']);
        $parts = $this->pd['part_nav_list'];
        for ($i=0;$i<$count;$i++) {
            if ($part == $parts[$i][0]) {
                if (isset($parts[($i - 1)][0])) {
                    $prev_part = $parts[($i - 1)][0];
                }
                if (isset($parts[($i + 1)][0])) {
                    $next_part = $parts[($i + 1)][0];
                }
                break;
            }
        }
    }
    if (isset($this->pd['show_all_mp']) && $this->pd['show_all_mp']) {
        $smp_arg = '&amp;show_all_mp=1';
    }
    else {
        $smp_arg = '';
    }
    $data .= '<table class="mprev_next" cellpadding="0" cellspacing="0"><tr><td>';
    $data .= '<span class="message_parts_heading"><a href="'.$sticky_url.'#parts">'.$this->user->str[80].'</a></span></td><td>';
    if ($prev_part) {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].
                '&amp;filter_by='.$this->pd['filter_by'].'&amp;mailbox='.urlencode($this->pd['mailbox']).
                '&amp;message_part='.$prev_part.$this->pd['new_window_arg'].$smp_arg.
                 '"><span class="pbutton">&#160;</span></a>';
    }
    else {
        $data .= '<a><span class="disabled_button pbutton">&#160;</span></a>';
    }
    $data .= '</td><td>';
    if ($next_part) {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].
                 '&amp;filter_by='.$this->pd['filter_by'].'&amp;mailbox='.urlencode($this->pd['mailbox']).
                 '&amp;message_part='.$next_part.$this->pd['new_window_arg'].$smp_arg.
                 '"><span class="nbutton">&#160;</span></a>';
    }
    else {
        $data .= '<a><span class="disabled_button nbutton">&#160;</span></a>';
    }
    $data .= '</td></tr></table>';
    return $data;
}
function print_message_prev_next_small() {
    global $conf;
    global $page_id;
    $data = '';
    if ($this->pd['new_window_arg']) {
        $new_window_arg = $this->pd['new_window_arg'].'&amp;parent_refresh=1';
    }
    else {
        $new_window_arg = '';
    }
    $data .= '<table cellpadding="0" cellspacing="0" class="sm_prev_next"><tr><td>';
    if ($this->pd['previous_uid']) {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['previous_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].
                '&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['prev_uid_page'].$new_window_arg.'"><complex-'.$page_id.'><span '.
                'class="prev_button">&#160;</span></complex-'.$page_id.'><simple-'.$page_id.'> &lt; </simple-'.$page_id.'></a>';
    }
    else {
        $data .= '<complex-'.$page_id.'><a><span class="prev_button disabled_button">&#160;</span></a></complex-'.$page_id.'>';
    }
    $data .= '</td><td>';
    if (!$this->new_window) {
        $data .= '<a href="?page=mailbox&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].'&amp;mailbox='.urlencode($this->pd['mailbox']).
             '&amp;mailbox_page='.urlencode($this->pd['mailbox_page']).'"><complex-'.$page_id.'><span class="up_button">&#160;</span>'.
             '</complex-'.$page_id.'><simple-'.$page_id.'> up </simple-'.$page_id.'></a>';
    }
    $data .= '</td><td>';
    if ($this->pd['next_uid']) {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['next_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].
                '&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['next_uid_page'].$new_window_arg.'"><complex-'.$page_id.'><span '.
                ' class="next_button">&#160;</span></complex-'.$page_id.'><simple-'.$page_id.'> &gt; </simple-'.$page_id.'></a>';
    }
    else {
        $data .= '<complex-'.$page_id.'><a><span class="next_button disabled_button">&#160;</span></a></complex-'.$page_id.'>';
    }
    $data .= '</td></tr></table>';
    return $data;
}
function print_message_prev_next() {
    global $conf;
    global $prev_next_actions;
    global $sticky_url;
    global $page_id;
    if (isset($this->pd['prev_uid_page'])) {
        $prev_uid_page = $this->pd['prev_uid_page'];
    }
    else {
        $prev_uid_page = $this->pd['mailbox_page'];
    }
    if (isset($this->pd['next_uid_page'])) {
        $next_uid_page = $this->pd['next_uid_page'];
    }
    else {
        $next_uid_page = $this->pd['mailbox_page'];
    }
    $data = '<complex-'.$page_id.'>';
    $data .= '<div id="prev_next_form">';
    if ($this->new_window && !strstr($sticky_url, 'new_window')) {
        $form_url = $sticky_url.$this->pd['new_window_arg'].'&amp;parent_refresh=1';
    }
    else {
        $form_url = $sticky_url;
    }
    $data .= '<form method="post" action="'.$form_url.'"';
    $data .= ' onsubmit="return check_prev_next_del(\''.$this->user->str[64].'\');" ';
    $data .= '>';
    $data .= '<input type="hidden" name="uid" value="'.$this->pd['message_uid'].'" />';
    $data .= '<input type="hidden" name="prev_uid" value="'.$this->pd['previous_uid'].'" />';
    $data .= '<input type="hidden" name="mailbox" value="'.$this->user->htmlsafe($this->pd['mailbox']).'" />';
    $data .= '<input type="hidden" name="next_uid" value="'.$this->pd['next_uid'].'" />';
    $data .= '<input type="hidden" name="sort_by" value="'.$this->pd['sort_by'].'" />';
    $data .= '<input type="hidden" name="filter_by" value="'.$this->pd['filter_by'].'" />';
    $data .= '<input type="hidden" name="mailbox_page" value="'.$this->pd['mailbox_page'].'" />';
    $data .= '<input type="hidden" name="prev_uid_page" value="'.$prev_uid_page.'" />';
    $data .= '<input type="hidden" name="next_uid_page" value="'.$next_uid_page.'" />';
    $data .= '<table><tr>';
    $data .= '<td align="right"><input title="'.$this->user->str[452].'" value="" type="submit" ';
    if (!$this->pd['previous_uid']) {
        $data .= 'class="pbutton disabled_button" disabled="disabled" ';
    }
    else {
        $data .= 'class="pbutton" ';
    }
    $data .= 'name="prev_action" /></td><td>';
    if (!$this->new_window) {
        $data .= '<input type="submit" title="'.$this->user->str[454].'" value="" name="up_action" class="ubutton" /></td><td>';
    }
    $data .= '<input type="submit" value="" title="'.$this->user->str[453].'" ';
    if (!$this->pd['next_uid']) {
        $data .= 'class="nbutton disabled_button" disabled="disabled" ';
    }
    else {
        $data .= 'class="nbutton" ';
    }
    $data .= 'name="next_action" /></td>';
    $data .= '<td colspan="3" align="center"> &nbsp; and &nbsp; <select onchange="disable_destination();" id="prev_next_action" name="prev_next_action">';
    $selected = false;
    if (isset($this->pd['settings']['default_message_action'])) {
        $selected = $this->pd['settings']['default_message_action'];
    }
    foreach ($prev_next_actions as $i => $v) {
        if (trim($v)) {
            $v = $this->user->str[$v];
        }
        $data .= '<option ';
        if ($i == $selected) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select>&#160;'.$this->user->str[538].': &#160;<select ';
    if ($selected != 'move' && $selected != 'copy') {
        $data .= 'disabled="disabled" ';
    }
    $data .= 'id="prev_next_folder" name="prev_next_folder">'.
              $this->print_folder_option_list($this->pd['folders'], false, 0, array($this->pd['current_destination']), true, true).'</select>';
    $data .= '</td>';
    $data .= '</tr></table>';
    $data .= '</form>';
    $data .= '</div>';
    $data .= '</complex-'.$page_id.'>';
    $data .= '<simple-'.$page_id.'><form method="post" action="'.$form_url.'">';
    $data .= '<input type="hidden" name="uid" value="'.$this->pd['message_uid'].'" />';
    $data .= '<input type="hidden" name="prev_uid" value="'.$this->pd['previous_uid'].'" />';
    $data .= '<input type="hidden" name="mailbox" value="'.$this->user->htmlsafe($this->pd['mailbox']).'" />';
    $data .= '<input type="hidden" name="next_uid" value="'.$this->pd['next_uid'].'" />';
    $data .= '<input type="hidden" name="sort_by" value="'.$this->pd['sort_by'].'" />';
    $data .= '<input type="hidden" name="filter_by" value="'.$this->pd['filter_by'].'" />';
    $data .= '<input type="hidden" name="mailbox_page" value="'.$this->pd['mailbox_page'].'" />';
    $data .= '<input type="hidden" name="prev_uid_page" value="'.$prev_uid_page.'" />';
    $data .= '<input type="hidden" name="prev_next_action" value="delete" />';
    $data .= '<input type="submit" name="up_action_x" value="Delete" />';
    $data .= '</form></simple-'.$page_id.'>';
    $data .= do_display_hook('message_prev_next_links');
    return $data;
}
function print_message_body() {
    global $page_id;
    global $conf;
    global $user;
    global $sticky_url;
    $data = do_display_hook('message_body_top');
    if (!isset($this->pd['message_type']) || isset($this->pd['broken_msg'])) {
        if (isset($this->pd['message_part']) && $this->pd['message_part']) {
            $data .= '<div id="message_unkown">'.$this->user->str[528].'</div>';
        }
        else {
            $data .= '<div id="message_unkown">'.$this->user->str[529].'</div>';
        }
    }
    else {
        if ($this->pd['raw_view']) {
            $this->pd['message_type'] = 'text';
        }
        $data .= '<simple-'.$page_id.'><br /></simple-'.$page_id.'>';
        switch ($this->pd['message_type']) {
            case 'text':
            case 'image':
                $data .= $this->{'print_message_'.$this->pd['message_type']}();
                break;
            case 'html':
                if (isset($conf['html_message_iframe']) && $conf['html_message_iframe']) {
                    if (isset($this->pd['simple_mode']) && $this->pd['simple_mode']) {
                        $data .= $this->{'print_message_'.$this->pd['message_type']}();
                    }
                    else {
                        $data .= $this->print_message_iframe();
                    }
                }
                else {
                    $data .= $this->{'print_message_'.$this->pd['message_type']}();
                }
                break;
            case 'frame':
                $data .= '<iframe id="msg_iframe" name="msg_iframe" frameborder="0" src="'.$sticky_url.'&amp;framed_part=1">';
                $data .= '</iframe>';
                break;
            default:
                $data .= '<div id="message_unkown">Unsupported MIME type: '.
                         $this->user->htmlsafe($this->pd['raw_message_type']).'</div>';
                break;
        }
    }
    $data .= do_display_hook('message_body_bottom');
    return $data;
}
function print_message_links() {
    $list_link = false;
    global $conf;
    global $page_id;
    foreach ($this->pd['full_message_headers'] as $vals) {
        if (strtolower($vals[0]) == 'list-id') {
            $list_link = $vals[1];
            break;
        }
    }
    $part = 1;
    if (isset($this->pd['message_part'])) {
        $part = $this->pd['message_part'];
    }
    if (isset($this->pd['full_message_headers'])) {
        $mid = false;
        foreach ($this->pd['full_message_headers'] as $v) {
            if (strtolower($v[0]) == 'message-id') {
                $mid = $v[1];
                break;
            }
        }
    }
    if (!$this->user->use_cookies) {
        $sess = '&amp;PHPSESSID='.session_id();
    }
    else {
        $sess = '';
    }
    $data = '';
    $hrefs[] = '?page=compose&amp;compose_session=new&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reply_part='.$part.'&amp;reply_type=reply&amp;uid='.$this->pd['message_uid'];
    $hrefs[] = '?page=compose&amp;compose_session=new&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reply_part='.$part.'&amp;reply_type=all&amp;uid='.$this->pd['message_uid'];
    $hrefs[] = '?page=compose&amp;compose_session=new&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reply_part='.$part.'&amp;reply_type=list&amp;uid='.$this->pd['message_uid'];
    $hrefs[] = '?page=compose&amp;compose_session=new&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reply_part='.$part.'&amp;reply_type=forward&amp;uid='.$this->pd['message_uid'];
    $hrefs[] = '?page=compose&amp;compose_session=new&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reply_part='.$part.'&amp;reply_type=forward_attach&amp;uid='.$this->pd['message_uid'];
    $hrefs[] = '?page=compose&amp;compose_session=new&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reply_part='.$part.'&amp;reply_type=resume&amp;uid='.$this->pd['message_uid'];
    $hrefs[] = '?page=compose&amp;compose_session=new&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reply_part='.$part.'&amp;reply_type=new&amp;uid='.$this->pd['message_uid'];
    foreach ($hrefs as $href) {
        if (isset($this->pd['settings']['compose_window']) && $this->pd['settings']['compose_window']) {
            $onclicks[] = 'onclick="open_window(\''.$href.'&amp;new_window=1'.$sess.'\', 900, 950); return false;" ';
        }
        else {
            if ($this->new_window) {
                $onclicks[] = 'onclick="open_parent_window(\''.$href.$sess.'\'); return false;" ';
            }
            else {
                $onclicks[] = '';
            }
        }
    }
    $data .= '<a '.$onclicks[0].'href="'.$hrefs[0].'">'.$this->user->str[70].'</a> <a '.$onclicks[1].'href="'.$hrefs[1].'">'.$this->user->str[71].'</a> ';
    if ($list_link) {
        $data .= '<a '.$onclicks[2].'href="'.$hrefs[2].'" title="'.$this->user->htmlsafe($list_link).'">'.$this->user->str[37].'</a> ';
    }
    $data .= '<a '.$onclicks[3].'href="'.$hrefs[3].'">'.$this->user->str[72].'</a> ';
    $data .= '<a '.$onclicks[4].'href="'.$hrefs[4].'">'.$this->user->str[60].'</a> ';
    if (isset($this->pd['settings']['draft_folder']) && $this->pd['mailbox'] == $this->pd['settings']['draft_folder']) {
        $data .= '<a '.$onclicks[5].'href="'.$hrefs[5].'">'.$this->user->str[74].'</a>';
    }
    else {
        $data .= '<a '.$onclicks[6].'href="'.$hrefs[6].'">'.$this->user->str[73].'</a>';
    }
    $data .= '&#160;||&#160; ';
    if ((isset($this->pd['show_full_headers']) && $this->pd['show_full_headers']) ||
        ($this->pd['settings']['full_headers_default'] && !$this->pd['show_small_headers'])) {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].'&amp;mailbox='.
                 urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['mailbox_page'].'&amp;message_part='.$this->pd['message_part'].'&amp;small_headers=1'.
                 $this->pd['new_window_arg'].'">'.$this->user->str[69].'</a> ';
    }
    else {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].'&amp;message_part='.
                 $this->pd['message_part'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['mailbox_page'].'&amp;full_headers=1'.
                 $this->pd['new_window_arg'].'">'.$this->user->str[75].'</a> ';
    }
    if ($this->pd['raw_view']) {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].'&amp;message_part='.
                 $this->pd['message_part'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['mailbox_page'].
                 $this->pd['new_window_arg'].'">'.$this->user->str[87].'</a> ';
    }
    else {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].'&amp;message_part='.
                 $this->pd['message_part'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['mailbox_page'].'&amp;raw_view=1'.
                 $this->pd['new_window_arg'].'">'.$this->user->str[76].'</a> ';
    }
    if ($this->dsp_page == 'print_view') {
        $data .= '<a href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].'&amp;message_part='.
                 $this->pd['message_part'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['mailbox_page'].
                 $this->pd['new_window_arg'].'">'.$this->user->str[87].'</a> ';
    }
    else {
        $data .= '<a target="_blank" href="?page=message&amp;uid='.$this->pd['message_uid'].'&amp;message_part='.$this->pd['message_part'].'&amp;sort_by='.$this->pd['sort_by'].
                 '&amp;filter_by='.$this->pd['filter_by'].'&amp;message_part='.$this->pd['message_part'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.
                 $this->pd['mailbox_page'].'&amp;print_view=1'.$this->pd['new_window_arg'].'">'.$this->user->str[77].'</a> ';
    }
    if (stristr($this->pd['imap_capability'], 'THREAD') && !$this->new_window) {
        $data .= '<a href="?page=thread_view&amp;uid='.$this->pd['message_uid'].'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].'&amp;message_part='.
                 $this->pd['message_part'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;mailbox_page='.$this->pd['mailbox_page'].'&amp;print_view=1" onclick="display_notice(false, \'Searching for thread members\');">'.
                 $this->user->str[78].'</a> ';
    }
    if ($mid) {
        if (isset($this->pd['header_flags']) && stristr($this->pd['header_flags'], 'answered')) {
            $data .= '<a href="?page=message&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;current_uid='.$this->pd['message_uid'].'&amp;response_id='.urlencode($mid).'&amp;find_response=1'.$this->pd['new_window_arg'].'">'.$this->user->str[79].'</a> ';
        }
    }
    $data .= do_display_hook('message_links');
    if (isset($this->pd['thread_data'])) {
        if (isset($this->pd['thread_data'])) {
        }
    }
    if (isset($this->pd['unseen_uids']) && !empty($this->pd['unseen_uids'])) {
        list($unread_prev, $unread_next) = $this->user->user_action->get_prev_next_uids($this->pd['unseen_uids'], $this->pd['message_uid']);
        if ($unread_prev || $unread_next) {
            $data .= '<complex-'.$page_id.'><div id="unread_links">';
            $data .= '<table cellpadding="0" cellspacing="0" class="mprev_next"><tr><td>';
            if (!$this->new_window) {
                $new_window_arg = '';
                $data .= '<a href="?page=/new&amp;mailbox='.urlencode($this->pd['mailbox']).'">'.
                    $this->user->str[34].'</a> ('.(count($this->pd['unseen_uids']) - 1).') : ';
            }
            else {
                $new_window_arg = $this->pd['new_window_arg'].'&amp;parent_refresh=1';
                $data .= $this->user->str[34].' ('.(count($this->pd['unseen_uids']) - 1).') : ';
            }
            $data .= '</td><td>';
            if ($unread_prev) {
                $data .= '<a href="?page=message&amp;uid='.$unread_prev.'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].
                         '&amp;mailbox='.urlencode($this->pd['mailbox']).$new_window_arg.'"><span class="pbutton">&#160;</span></a> ';
            }
            else {
                $data .= '<a><span class="pbutton disabled_button">&#160;</span></a> ';
            }
            $data .= '</td><td>';
            if ($unread_next) {
                $data .= '<a href="?page=message&amp;uid='.$unread_next.'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].
                         '&amp;mailbox='.urlencode($this->pd['mailbox']).$new_window_arg.'"><span class="nbutton">&#160;</span></a>';
            }
            else {
                $data .= '<a><span class="nbutton disabled_button">&#160;</span></a>';
            }
            $data .= '</td></tr></table>';
            $data .= '</div></complex-'.$page_id.'>';
        }
    }
    if (isset($this->pd['search_results'])) {
        list($search_prev, $search_next) = $this->user->user_action->get_prev_next_uids($this->pd['search_results'], $this->pd['message_uid']);
        If ($search_prev || $search_next) {
            $data .= '<div id="search_links">';
            $data .= '<table cellpadding="0" cellspacing="0" class="mprev_next"><tr><td>';
            if (!$this->new_window) {
                $data .= '<a href="?page=search&amp;mailbox='.urlencode($this->pd['mailbox']).'">'.$this->user->str[419].'</a>: ';
                $new_window_arg = '';
            }
            else {
                $data .= $this->user->str[419].' ';
                $new_window_arg = $this->pd['new_window_arg'].'&amp;parent_refresh=1';
            }
            $data .= '</td><td>';
            if ($search_prev) {
                $data .= '<a href="?page=message&amp;uid='.$search_prev.'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].
                         '&amp;mailbox='.urlencode($this->pd['mailbox']).$new_window_arg.'"><complex-'.$page_id.'><span class="pbutton">&#160;</span>'.
                         '<simple-'.$page_id.'> &lt; </simple-'.$page_id.'></a>';
            }
            else {
                $data .= '<complex-'.$page_id.'><a><span class="disabled_button pbutton">&#160;</span></a> </complex-'.$page_id.'>';
            }
            $data .= '</td><td>';
            if ($search_next) {
                $data .= '<a href="?page=message&amp;uid='.$search_next.'&amp;sort_by='.$this->pd['sort_by'].'&amp;filter_by='.$this->pd['filter_by'].
                         '&amp;mailbox='.urlencode($this->pd['mailbox']).$new_window_arg.'"><complex-'.$page_id.'><span class="nbutton">&#160;</span>'.
                         '</complex-'.$page_id.'><simple-'.$page_id.'> &lt; </simple-'.$page_id.'></a>';
            }
            else {
                $data .= '<complex-'.$page_id.'><a><span class="disabled_button nbutton">&#160;</span></a></complex-'.$page_id.'>';
            }
            $data .= '</td></tr></table>';
            $data .= '</div>';
        }
    }
    return $data;
}
function print_add_contact_form() {
    global $page_id;
    $data = '<complex-'.$page_id.'>';
    if (isset($this->pd['new_contacts']) && !empty($this->pd['new_contacts'])) {
        $data .= '<tr><th>'.$this->user->str[8].': </th><td><div id="message_contacts"><form method="post" action="?page=contacts&amp;mailbox='.urlencode($this->pd['mailbox']).'#contactform">';
        $data .= ' <select name="a_email">';
        foreach ($this->pd['new_contacts'] as $v) {
            $data .= '<option value="'.$this->user->htmlsafe($v).'">'.$this->user->htmlsafe($v).'</option>';
        }
        $data .= '</select><input type="submit" name="add_message_contact" value="'.$this->user->str[147].'" /></form></div></td><td></td></tr>';
    }
    $data .= '</complex-'.$page_id.'>';
    return $data;
}
function print_part_headers() {
    global $page_id;
    global $max_header_length;
    global $sticky_url;
    $data = '<simple-'.$page_id.'><tr><td><br /></td></tr></simple-'.$page_id.'>'.do_display_hook('message_part_headers_top').'<complex-'.$page_id.'>';
    $rows = '';
    foreach ($this->pd['message_part_headers'] as $i => $vals) {
        $name = $this->user->htmlsafe($vals[0], $this->pd['charset'], true);
        $val = $this->user->htmlsafe($vals[1], $this->pd['charset'], true);
        if ($this->pd['full_part_header'] == $i) {
            $val .= ' &#160;<a href="'.preg_replace('/\&amp;full_part_header\=\d+/', '', $sticky_url).'">'.$this->user->str[522].'</a>';
        }
        elseif (htmlstrlen($val) > $max_header_length) {
            $val = trim_htmlstr($val, $max_header_length).' &#160;<a href="'.
                preg_replace('/\&amp;full_part_header\=\d+/', '', $sticky_url).
                '&amp;full_part_header='.$i.'">'.$this->user->str[521].'</a>';
        }
        $rows .= '<tr><th>'.$name.': </th><td ';
        if (strtolower($vals[0]) == 'subject') {
            $rows .= 'class="subject_cell" ';
        }
        if (strtolower($vals[0]) == 'date' && $vals[1] && !$this->pd['show_full_headers']) {
            $rows .= '>'.date('r', strtotime((trim($val))));
            $rows .= ' &#160;&#160; ('.print_time(strtotime($vals[1]), $vals[1]).')';
        }
        else {
            $rows .= '>'.$val;
        }
        $rows .= '<br /></td></tr>';
    }
    if ($rows) {
        $data .= '<table width="100%" cellpadding="0" cellspacing="0">'.$rows.'</table>';
    }
    if (isset($this->pd['part_nav_list']) && count($this->pd['part_nav_list']) > 1) {
        $data .= '<div id="prev_next_part">'.$this->print_message_prev_next_part().'</div>';
    }
    $data .= do_display_hook('message_part_headers_bottom').'</complex-'.$page_id.'>';
    return $data;
}
function print_message_iframe() {
    global $sticky_url;
    $data = '<iframe id="msg_iframe" name="msg_iframe" frameborder="0" src="'.$sticky_url.'&amp;inline_html=1">';
    $data .= '</iframe>';
    return $data;
}
function print_message_iframe_content() {
    $data = $this->print_message_html(true);
    $res = '';
    if (!stristr($data, '<body')) {
        $res = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><title>Message</title>';
        if (isset($this->pd['html_message_style']) && !empty($this->pd['html_message_style'])) {
            foreach ($this->pd['html_message_style'] as $v) {
                $res .= $v;
            }
        }
        else {
            $res .= '
            <style type="text/css">
            body {font-size: 10pt; font-family: '.$this->pd['settings']['font_family'].'; }
            p {padding: 1px; margin: 1px;}
            body, select, option, textarea, input { font-size: '.$this->pd['settings']['font_size'].'% }
            </style>';
        }
        $res .= '</head><body>'.$data.'</body></html>';
    }
    return $res;
    }
}
?>
