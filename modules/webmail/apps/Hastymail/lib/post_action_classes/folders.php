<?php

/*  post_action_class.php: Process POST forms
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
class fw_post_action_folders extends fw_user_action_with_post {
function set_post_page_vars() {
    global $user;
    $forms = array(
    'rename_folder' => array(
        'old_folder_name'     => array('string', 1, 'Mailbox name'),
        'new_folder_name'     => array('string', 1, 'New Mailbox name'),
    ),
    'delete_folder' => array(
        'delete_folder_name'     => array('string', 1, 'Mailbox name'),
    ),
    'add_folder' => array(
        'parent_folder_name'     => array('string', 0, 'Parent'),
        'force_subfolder'        => array('true', 0, 'Force Subfolders'),
        'folder_name'            => array('string', 1, 'Name'),
    ),
    ); return $forms;
}
function form_action_update_folder_options($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $folders = $_SESSION['folders'];
        $hidden = array();
        if (isset($post['hidden']) && !empty($post['hidden'])) {
            foreach ($post['hidden'] as $val) {
                if (isset($folders[$val])) {
                    $hidden[] = $val;
                }
            }
        }
        $new_mail = array();
        if (isset($post['check_for_new']) && !empty($post['check_for_new'])) {
            foreach ($post['check_for_new'] as $val) {
                if (isset($folders[$val])) {
                    $new_mail[] = $val;
                }
            }
        }
        $sort_by = array();
        if (!empty($post['mailbox_index'])) {
            foreach ($post['mailbox_index'] as $i => $v) {
                if (isset($folders[$v])) {
                    if (isset($post['sort_by'][$i]) && $post['sort_by'][$i] != 'ARRIVAL') {
                        $sort_by[$v] = $post['sort_by'][$i];
                    }
                }
            }
        }
        $_SESSION['user_settings']['folder_check'] = $new_mail;
        $_SESSION['user_settings']['hidden_folders'] = $hidden;
        $_SESSION['user_settings']['sort_by'] = $sort_by;
        $user->page_data['settings'] = $_SESSION['user_settings'];
        $this->write_settings();
        $imap->get_folders(true);
        foreach ($_SESSION['folders'] as $vals) {
            if (isset($_SESSION['header_cache'][$vals['name']])) {
                $_SESSION['header_cache_refresh'][$vals['name']] = 1;
            }
            if (isset($_SESSION['uid_cache'][$vals['name']])) {
                $_SESSION['uid_cache_refresh'][$vals['name']] = 1;
            }
        }
        $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
    }
}
function form_action_delete_folder($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        if (!isset($_SESSION['folders'][$post['delete_folder_name']])) {
            $this->errors[] = $user->str[376].': '.$user->htmlsafe($post['delete_folder_name']);
            return;
        }
        $res = $imap->delete_folder($post['delete_folder_name']);
        if ($res) {
            $this->errors[] = $user->str[382].':<br />'.$res;
        }
        else {
            $this->errors[] = $user->str[377];
            $imap->get_folders(true);
            $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
        }
        $this->form_redirect = true;
    }
}
function form_action_rename_folder($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        if (!isset($_SESSION['folders'][$post['old_folder_name']])) {
            $this->errors[] = $user->str[376].': '.$user->htmlsafe($post['old_folder_name']);
            return;
        }
        $res = $imap->rename_folder($imap->folder_prefix, $post['old_folder_name'], $post['new_folder_name']);
        if ($res) {
            $this->errors[] = $user->str[378].':<br />'.$res;
        }
        else {
            $this->errors[] = $user->str[379];
            $imap->get_folders(true);
            $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
        }
        $this->form_redirect = true;
    }
}
function form_action_add_folder($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        if (isset($post['parent_folder_name'])) {
            $parent = $post['parent_folder_name'];
        }
        else {
            $parent = false;
        }
        if (isset($post['force_subfolder']) && $post['force_subfolder']) {
            $folder_name = $post['folder_name'].$_SESSION['imap_delimiter'];
        }
        else {
            $folder_name = $post['folder_name'];
        }
        $res = $imap->create_folder($imap->folder_prefix, $folder_name, $parent);
        if ($res) {
            $this->errors[] = $user->str[380].':<br />'.$res;
        }
        else {
            $this->errors[] = $user->str[381];
            $imap->get_folders(true);
            $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
        }
        $this->form_redirect = true;
    }
}
}?>
