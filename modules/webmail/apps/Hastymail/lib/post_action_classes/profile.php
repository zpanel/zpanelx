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
class fw_post_action_profile extends fw_user_action_with_post {
function set_post_page_vars() {
    global $user;
    $forms = array(
    'add_profile' => array(
        'profile_count' => array('int', 1, 'Profile count'),
    ),
    'remove_profile' => array(
        'profile_id' => array('int', 1, 'Profile ID'),
    ),
    'update_profile' => array(
        'profile_count' => array('int', 1, 'Profile count'),
        'profile_id' => array('int', 1, 'Profile ID'),
        'auto_sig'  => array('string', 0, 'auto-signature'),
        'profile_name' => array('string', 1, 'Name'),
        'profile_address' => array('email', 0, 'Address'),
        'profile_sig' => array('string', 0, 'Signature'),
        'profile_reply_to' => array('email', 0, 'Reply To'),
        'profile_default' => array('int', 0, 'Default'),
    ),
    ); return $forms;
}
function form_action_add_profile($form, $post) {
    global $user;
    if ($user->logged_in) {
        array_push($_SESSION['user_settings']['profiles'], array());
        $this->write_settings();
        $this->form_redirect = true;
    }
}
function form_action_remove_profile($form, $post) {
    global $user;
    global $user;
    if ($user->logged_in && $post['profile_id']) {
        if (isset($_SESSION['user_settings']['profiles'][$post['profile_id']])) {
            array_splice($_SESSION['user_settings']['profiles'], $post['profile_id'], 1);
            $this->write_settings();
            $this->form_redirect = true;
        }
    }
}
function form_action_update_profile($form, $post) {
    global $user;
    global $no_profiles;
    if ($user->logged_in) {
        $profiles = array();
        if (isset($_SESSION['user_settings']['profiles'])) {
            $profiles = $_SESSION['user_settings']['profiles'];
        }
        $name = $post['profile_name'];
        $id = $post['profile_id'];
        $address = false;
        if (isset($post['profile_address'])) {
            $address = $post['profile_address'];
        }
        elseif ($no_profiles) {
            $address = $profiles[$id]['profile_address'];
        }
        if (!$address) {
            $this->error = $user->str[47].': '.$user->str[12];
            return;
        }
        $sig = '';
        $reply_to = '';
        $auto = false;
        if (isset($post['profile_sig'])) {
            $sig = $post['profile_sig'];
        }
        if (isset($post['auto_sig'])) {
            $auto = true;
        }
        if (isset($post['profile_reply_to'])) {
            $reply_to = $post['profile_reply_to'];
        }
        $profiles[$id] = array('profile_name' => $name, 'profile_reply_to' => $reply_to, 'profile_address' => $address, 'profile_sig' => $sig, 'auto_sig' => $auto);
        $count = count($profiles);
        if (isset($post['profile_default']) && $post['profile_default']) {
            for ($i=0;$i<$count;$i++) {
                $profiles[$i]['default'] = 0;
            }
            $profiles[$id]['default'] = 1;
        }
        $_SESSION['user_settings']['profiles'] = $profiles;
        $this->write_settings();
        $this->form_redirect = true;
    }
}
}?>
