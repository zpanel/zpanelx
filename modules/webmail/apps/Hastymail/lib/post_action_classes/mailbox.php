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
class fw_post_action_mailbox extends fw_user_action_with_post {
function set_post_page_vars() {
    global $user;
    $forms = array(
    'unfreeze_mailbox' => array(
        'mailbox'               => array('string', 1, $user->str[22]),
    ),
    'freeze_mailbox' => array(
        'mailbox'               => array('string', 1, $user->str[22]),
    ),
    'reset_search' => array(
        'current_mailbox'        => array('string', 1, 'Current mailbox'),
    ),
    ); return $forms;
}
function form_action_empty_trash($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder'] == $post['current_mailbox']) {
            $status = $imap->select_mailbox($post['current_mailbox'], false, false, true);
            if ($status) {
                $res1 = $imap->message_action(array(), 'DELETE', false, '1:*');
                $res2 = false;
                if ($res1) {
                    $res2 = $imap->message_action(array(), 'EXPUNGE', false);
                }
                if ($res2) {
                    $this->errors[] = 'Trash emptied';
                }
            }
        }
    }
}
function form_action_unfreeze_mailbox($form, $post) {
    global $user;
    if ($user->logged_in) {
        if (isset($_SESSION['frozen_folders'][$post['mailbox']])) {
            unset($_SESSION['frozen_folders'][$post['mailbox']]);
        }
        $this->errors[] = $user->str[340]; 
        $this->form_redirect = true;
    }
}
function form_action_freeze_mailbox($form, $post) {
    global $user;
    if ($user->logged_in) {
        $_SESSION['frozen_folders'][$post['mailbox']] = $post['mailbox'];
        $this->errors[]= $user->str[341];
        $this->form_redirect = true;
    }
}
function form_action_search_mailbox($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $res = array();
        $mailbox = $post['current_mailbox'];
        $fld_id = $post['search_fld'];
        $location_id = $post['search_location'];
        $keywords = $post['mailbox_search_words'];
        $fld = false;
        $uids = array();
        switch ($fld_id) {
            case 1:
                $fld = 'TEXT';
                break;
            case 2:
                $fld = 'FROM';
                break;
            case 3:
                $fld = 'SUBJECT';
                break;
            case 4:
                $fld = 'TO';
                break;
            case 5:
                $fld = 'CC';
                break;
            case 6:
                $fld = 'BODY';
                break;
        }
        switch ($location_id) {
            case 1:
                $page = 1;
                $cnt = $_SESSION['user_settings']['mailbox_per_page_count'];
                if (isset($_GET['mailbox_page'])) {
                    $page = (int) $_GET['mailbox_page'];
                    if ($page < 1) {
                        $page = 1;
                    }
                }
                $uids = array_slice($_SESSION['uid_cache'][$mailbox]['uids'], (($page - 1)*$cnt), $cnt);
                break;
            case 2:
                $uids = array();
                break;
            case 3: $uids = false;
                break;
        }
        $_SESSION['search_terms'][0]['location'] = $location_id;
        $_SESSION['search_terms'][0]['fld'] = $fld_id;
        $_SESSION['search_terms'][0]['words'] = $keywords;
        $res_cnt = 0;
        if (!is_array($uids)) {
            foreach ($_SESSION['folders'] as $vals) {
                $imap->select_mailbox($vals['realname'], false);
                $_SESSION['search_terms']['folders'][] = $vals['realname'];
                $result = $imap->simple_search($fld, $uids, $keywords);
                if (!empty($result)) {
                    $res_cnt += count($result);
                    $res[$vals['realname']] = $result;
                }
            }
        }
        else {
            $imap->select_mailbox($mailbox, false);
            $_SESSION['search_terms']['folders'] = array($mailbox);
            $result = $imap->simple_search($fld, $uids, $keywords);
            if (!empty($result)) {
                $res_cnt += count($result);
                $res[$mailbox] = $result;
            }
        }
        if (!empty($res) && ($location_id == 2 || $location_id == 3) && (!is_array($uids) || empty($uids))) {
            $user->redirect_page = 'search';
            $this->form_redirect = true;
        }
        elseif (empty($res)) {
            $this->errors[] = $user->str[373];
        }
        if (!empty($res)) {
            $this->errors[] = $user->str[372].': '.$res_cnt;
        }
        $_SESSION['search_total'] = $res_cnt;
        $_SESSION['search_results'] = $res;
    }
}
}?>
