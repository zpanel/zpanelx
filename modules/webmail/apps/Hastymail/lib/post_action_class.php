<?php

/*  post_action_class.php: Process submitted POST forms
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

class fw_user_action_with_post extends fw_user_action_page {

function set_post_vars() {
    global $user;
    $str = ($user->str);
    $forms = array(
    'toggle_all_button' => array(
        'mailboxes' => array('array', 0, 'Mailbox List'),
        'uids' => array('array', 0, $str[438]),
    ),
    'sort_contacts' => array(
        'contact_sort' => array('string', 1, 'Sort Type'),
    ),
    'add_message_contact' => array(
        'a_email' => array('email', 1, 'Contact Email'),
    ),
    'remove_new_page_folder' => array(
        'remove_folder' => array('string', 1, 'Folder Name'),
    ),
    'add_new_page_folder' => array(
        'new_page_folder' => array('string', 1, 'Folder Name'),
    ),
    'update_folder_options' => array(
        'hidden'                 => array('array', 0, 'Hidden folders'),
        'check_for_new'          => array('array', 0, 'New page folders'),
        'sort_by'                => array('array', 0, 'Sort options'),
        'mailbox_index'          => array('array', 1, 'Mailbox Index'),
    ),
    'undelete_message' => array(
        'uids'                   => array('array', 0, $str[438]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'delete_message' => array(
        'uids'                   => array('array', 0, $str[439]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'expunge_messages' => array(
        'mailboxes'              => array('array', 0, 'Mailbox List'),
        'uids'                   => array('array', 0, $str[440]),
        'current_mailbox'        => array('string', 1, 'Current Mailbox'),
    ),
    'flag_message' => array(
        'uids'                   => array('array', 0, $str[441]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'unflag_message' => array(
        'uids'                   => array('array', 0, $str[442]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'read_message' => array(
        'uids'                   => array('array', 0, $str[443]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'unread_message' => array(
        'uids'                   => array('array', 0, $str[444]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'move_message' => array(
        'uids'                   => array('array', 0, $str[446]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'destination_folder'     => array('string', 1, 'Destination Folder'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'empty_trash' => array(
        'current_mailbox'        => array('string', 1, 'Mailbox name'),
    ),
    'copy_message' => array(
        'uids'                   => array('array', 0, $str[445]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'destination_folder'     => array('string', 1, 'Destination Folder'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'move_message2' => array(
        'uids'                   => array('array', 0, $str[446]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'destination_folder2'     => array('string', 1, 'Destination Folder'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'copy_message2' => array(
        'uids'                   => array('array', 0, $str[445]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'destination_folder2'    => array('string', 1, 'Destination Folder'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
    ),
    'attach_message' => array(
        'uids'                   => array('array', 0, $str[445]),
        'full_mailbox'           => array('string', 0, 'Entire Mailbox'),
        'mailboxes'              => array('array', 0, 'Mailbox List'),
        'attach_new_win'         => array('int', 0, 'New window flag'),
    ),
    'search_mailbox' => array(
        'current_mailbox'        => array('string', 1, 'Current mailbox'),
        'search_fld'             => array('int', 1, 'Search field'),
        'search_location'        => array('int', 1, 'Search location'),
        'mailbox_search_words'   => array('string', 1, 'Search words'),
    ),
    'up_action' => array(
        'prev_next_action' => array('string', 0, 'Action'),
        'prev_next_folder' => array('string', 0, 'Folder'),
        'uid' => array('int', 1, 'Message ID'),
        'prev_uid' => array('string', 0, 'Previous ID'),
        'prev_uid_page' => array('int', 0, 'Previous Page'),
        'next_uid_page' => array('int', 0, 'Next Page'),
        'next_uid' => array('string', 0, 'Next ID'),
        'sort_by' => array('string', 0, 'Sort By'),
        'mailbox' => array('string', 1, 'Mailbox'),
        'filter_by' => array('string', 0, 'Filter By'),
        'mailbox_page' => array('string', 0, 'Mailbox Page'),
     ),
    'prev_action' => array(
        'prev_next_action' => array('string', 0, 'Action'),
        'prev_next_folder' => array('string', 0, 'Folder'),
        'prev_uid_page' => array('int', 0, 'Previous Page'),
        'next_uid_page' => array('int', 0, 'Next Page'),
        'uid' => array('int', 1, 'Message ID'),
        'prev_uid' => array('string', 0, 'Previous ID'),
        'next_uid' => array('string', 0, 'Next ID'),
        'sort_by' => array('string', 0, 'Sort By'),
        'filter_by' => array('string', 0, 'Filter By'),
        'mailbox_page' => array('string', 0, 'Mailbox Page'),
        'mailbox' => array('string', 1, 'Mailbox'),
     ),
    'next_action' => array(
        'prev_next_action' => array('string', 0, 'Action'),
        'prev_next_folder' => array('string', 0, 'Folder'),
        'prev_uid_page' => array('int', 0, 'Previous Page'),
        'next_uid_page' => array('int', 0, 'Next Page'),
        'uid' => array('int', 1, 'Message ID'),
        'prev_uid' => array('string', 0, 'Previous ID'),
        'mailbox' => array('string', 1, 'Mailbox'),
        'next_uid' => array('string', 0, 'Next ID'),
        'sort_by' => array('string', 0, 'Sort By'),
        'filter_by' => array('string', 0, 'Filter By'),
        'mailbox_page' => array('string', 0, 'Mailbox Page'),
     ),
    );
    if ($user->sub_class_names['post']) {
        $page_forms = $this->set_post_page_vars();
        foreach ($page_forms as $index => $vals) {
            $forms[$index] = $vals;
        }
        unset($page_forms);
    }
    return $forms;
}
function form_action_delete_message($form, $post) {
    global $user;
    global $imap;
    $trash_folder = false;
    if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
        $trash_folder = $_SESSION['user_settings']['trash_folder'];
    }
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('DELETE', $box, $uids, $trash_folder, false);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_undelete_message($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('UNDELETE', $box, $uids, false, false);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_flag_message($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('FLAG', $box, $uids, false, false);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_unflag_message($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('UNFLAG', $box, $uids, false, false);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_attach_message($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (isset($_SESSION['compose_sessions'])) {
            $c_session = count($_SESSION['compose_sessions']) + 1;
            $_SESSION['compose_sessions'][$c_session] = time();
        }
        else {
            $_SESSION['compose_sessions'] = array(1 => time());
            $c_session = 1;
        }
        foreach ($box_uids as $mailbox => $vals) {
            $status = $imap->select_mailbox($mailbox, false);
            if ($status) {
                foreach ($vals as $uid) {
                    $msg_headers = $imap->get_message_headers($uid, 0);
                    $filename = false;
                    foreach ($msg_headers as $vals) {
                        if (strtolower($vals[0]) == 'subject') {
                            $filename = substr(preg_replace("/\s+/", '_', trim(preg_replace("/[^a-zA-Z0-9 ]/", ' ', $vals[1]))), 0, 200).'.mime';
                            break;
                        }
                    }
                    if (!$filename) {
                        $filename = 'message.mime';
                    }
                    add_forwarded_attachments(array(0 => array('encoding' => 'none', 'type' => 'message',
                        'subtype' => 'rfc822', 'filename' => $filename)), $uid, $c_session);
                }
            }
        }
        $user->redirect_page = 'compose&compose_session='.$c_session;
        if (isset($post['attach_new_win'])) {
            $user->redirect_page .= '&new_window=1&refresh_parent=1';
        }
        $this->form_redirect = true;
    }
}
function form_action_read_message($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('READ', $box, $uids, false, false);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_expunge_messages($form, $post) {
    global $user;
    global $imap;
    if (isset($_SESSION['user_settings']['selective_expunge']) && $_SESSION['user_settings']['selective_expunge']) {
        $all = false;
    }
    else {
        $all = true;
    }
    if ($user->logged_in) {
        $uids = array();
        if (!$all) {
            if (isset($post['uids']) && is_array($post['uids'])) {
                foreach ($post['uids'] as $v) {
                    if ($user->user_action->match_int($v)) {
                        if (isset($post['mailboxes'][$v])) {
                            $uids[$post['mailboxes'][$v]][] = $v;
                        }
                    }
                }
            }
        }
        $boxes = array_unique($post['mailboxes']);
        if (is_array($boxes) && !empty($boxes)) {
            foreach ($boxes as $box) {
                if ($all) {
                    $this->perform_imap_action('EXPUNGE', $box, array(), false, false);
                }
                elseif (isset($uids[$box])) {
                    $box_uids = $uids[$box];
                    $this->perform_imap_action('EXPUNGE', $box, $box_uids, false, false);
                }
            }
        }
        //$this->form_redirect = true;
    }
}
function form_action_unread_message($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('UNREAD', $box, $uids, false, false);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_move_message($form, $post) {
    global $user;
    global $imap;
    $trash_folder = false;
    if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
        $trash_folder = $_SESSION['user_settings']['trash_folder'];
    }
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('MOVE', $box, $uids, $trash_folder, $post['destination_folder']);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_move_message2($form, $post) {
    global $user;
    global $imap;
    $post['destination_folder'] = $post['destination_folder2'];
    return $this->form_action_move_message($form, $post);
}
function form_action_copy_message($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $box_uids = $this->get_posted_boxes($post);
        if (!empty($box_uids)) {
            foreach ($box_uids as $box => $uids) {
                $this->perform_imap_action('COPY', $box, $uids, false, $post['destination_folder']);
            }
        }
        $this->form_redirect = true;
    }
}
function form_action_copy_message2($form, $post) {
    global $user;
    global $imap;
    $post['destination_folder'] = $post['destination_folder2'];
    return $this->form_action_copy_message($form, $post);
}
function prev_next_action($action, $form, $post) {
    global $user;
    if (isset($post['prev_next_action'])) {
        if (isset($post['prev_next_folder'])) {
            $prev_next_folder = $post['prev_next_folder'];
        }
        else {
            $prev_next_folder = false;
        }
        switch ($post['prev_next_action']) {
            case 'copy':
                if ($prev_next_folder) {
                    $this->perform_imap_action('COPY', $post['mailbox'], array($post['uid']), false, $prev_next_folder);
                }
                break;
            case 'move':
                if ($prev_next_folder) {
                    $this->perform_imap_action('MOVE', $post['mailbox'], array($post['uid']), false, $prev_next_folder);
                }
                break;
            case 'expunge':
                $trash_folder = false;
                if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
                    $trash_folder = $_SESSION['user_settings']['trash_folder'];
                }
                $this->perform_imap_action('EXPUNGE', $post['mailbox'], array($post['uid']), $trash_folder, $prev_next_folder);
                break;
            case 'delete':
                $trash_folder = false;
                if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
                    $trash_folder = $_SESSION['user_settings']['trash_folder'];
                }
                $this->perform_imap_action('DELETE', $post['mailbox'], array($post['uid']), $trash_folder, $prev_next_folder);
                break;
            case 'unflag':
                $this->perform_imap_action('UNFLAG', $post['mailbox'], array($post['uid']), false, $prev_next_folder);
                break;
            case 'flag':
                $this->perform_imap_action('FLAG', $post['mailbox'], array($post['uid']), false, $prev_next_folder);
                break;
            case 'unread':
                $this->perform_imap_action('UNREAD', $post['mailbox'], array($post['uid']), false, $prev_next_folder);
                break;
        }
    }
    $mailbox = $post['mailbox'];
    $page = 'message';
    $uid = $post['uid'];
    $mailbox_page = 1;
    switch ($action) {
        case 'next':
                $uid = $post['next_uid'];
                $mailbox_page = $post['next_uid_page'];
            break;
        case 'prev':
                $uid = $post['prev_uid'];
                $mailbox_page = $post['prev_uid_page'];
            break;
        case 'up':
                $page = 'mailbox';
                $mailbox_page = $post['mailbox_page'];
            break;
    }
    if (isset($_GET['new_window']) && $_GET['new_window']) {
        $new_window = '&new_window=1&parent_refresh=1';
    }
    else {
        $new_window = '';
    }
    $user->page_data['prev_next_action_url'] = '?page='.$page.'&mailbox='.$mailbox.
        '&uid='.$uid.'&mailbox_page='.$post['mailbox_page'].$new_window;
    $this->form_redirect = true;
}
function form_action_next_action($form, $post) {
    global $user;
    if ($user->logged_in) {
        $this->prev_next_action('next', $form, $post);
    }
}
function form_action_prev_action($form, $post) {
    global $user;
    if ($user->logged_in) {
        $this->prev_next_action('prev', $form, $post);
    }
}
function form_action_up_action($form, $post) {
    global $user;
    if ($user->logged_in) {
        $this->prev_next_action('up', $form, $post);
    }
}
function form_action_add_message_contact($form, $post) {
    global $user;
    if ($user->logged_in) {
        $user->page_data['message_contact'] = $post['a_email'];
    }
}
function form_action_remove_new_page_folder($form, $post) {
    global $user;
    if ($user->logged_in) {
        $folder = $post['remove_folder'];
        if (isset($_SESSION['user_settings']['folder_check'])) {
            $new_folders = array();
            foreach ($_SESSION['user_settings']['folder_check'] as $v) {
                if ($v != $folder) {
                    $new_folders[] = $v;
                }
            }
            $_SESSION['user_settings']['folder_check'] = $new_folders;
            $this->write_settings();
            $this->form_redirect = true;
        }
    }
}
function form_action_add_new_page_folder($form, $post) {
    global $user;
    if ($user->logged_in && isset($_SESSION['folders'][$post['new_page_folder']])) {
        $folder = $post['new_page_folder'];
        if (isset($_SESSION['user_settings']['folder_check']) && !in_array($folder, $_SESSION['user_settings']['folder_check'])) {
            $folders = $_SESSION['user_settings']['folder_check'];
            $folders[] = $folder;
            usort($folders, 'new_folder_sort');
            $_SESSION['user_settings']['folder_check'] = $folders;
            $this->write_settings();
            $this->form_redirect = true;
        }
    }
}
function form_action_reset_search($form, $post) {
    global $user;
    if ($user->logged_in) {
        if (isset($_SESSION['search_results'][$post['current_mailbox']])) {
            unset($_SESSION['search_results'][$post['current_mailbox']]);
            $this->errors[] = $user->str[347];
            $this->form_redirect = true;
        }
    }
}
function form_action_toggle_all_button($form, $post) {
    if (isset($post['uids'])) {
        $_SESSION['toggle_uids'] = $post['uids'];
    }
    if (isset($post['mailboxes'])) {
        $_SESSION['toggle_boxes'] = $post['mailboxes'];
    }
    $_SESSION['toggle_all'] = 1;
}
function get_posted_boxes($post) {
    global $user;
    $box_uids = array();
    $uids = array();
    if (isset($post['mailboxes'])) {
        if (isset($post['uids'])) {
            foreach ($post['uids'] as $v) {
                if ($user->user_action->match_int($v)) {
                    if (isset($post['mailboxes'][$v])) {
                        $uids[$post['mailboxes'][$v]][] = $v;
                    }
                }
            }
        }
        $boxes = array_unique($post['mailboxes']);
        if (is_array($boxes) && !empty($boxes)) {
            if (count($boxes) == 1 && isset($post['full_mailbox']) && $post['full_mailbox'] == 1) {
                $box = array_pop($boxes);
                if (isset($_SESSION['uid_cache'][$box]['uids'])) {
                    $box_uids[$box] = $_SESSION['uid_cache'][$box]['uids'];
                }
            }
            else {
                foreach ($boxes as $box) {
                    if (isset($uids[$box])) {
                        $box_uids[$box] = $uids[$box];
                    }
                }
            }
        }
    }
    return $box_uids;
}
}?>
