<?php

/*  url_action_class.php: Process $_GET vars
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
function url_action_profile($get) {
    global $user;
    if ($user->logged_in) {
        do_work_hook('profile_page_start');
        $user->page_data['profile_link_class'] ='current_page';
        $user->dsp_page = 'profile';
        $user->page_title .= ' | Profile |';
    }
}
function url_action_thread_view($get) {
    global $imap;
    global $user;
    if ($user->logged_in) {
        do_work_hook('thread_view_start');
        if (isset($get['mailbox'])) {
            if (isset($_SESSION['folders'][$get['mailbox']])) {
                $mailbox = $get['mailbox'];
                if (isset($get['uid'])) {
                    $uid = (int) $get['uid'];
                    $status = $imap->select_mailbox($mailbox, 'THREAD_R');
                    if ($status) { 
                        $data = $_SESSION['uid_cache'][$mailbox]['thread_data'];
                        if (isset($data[$uid])) {
                            $thread_id = $data[$uid]['thread'];
                            $thread = array();
                            $uids = array();
                            foreach ($data as $index => $vals) {
                                if (!$index) {
                                    continue;
                                }
                                if ($vals['thread'] == $thread_id) {
                                    $uids[] = $index;
                                    $thread[$index] = $vals;
                                    if (count($uids) > 499) {
                                        $this->errors[] = $user->str[385];
                                        break;
                                    }
                                }
                            }
                            $thread_headers = $imap->get_mailbox_page($mailbox, $uids, false);
                            $user->page_data['header_list'] = $thread_headers;
                            $user->page_data['thread_data'] = $thread;
                            $user->page_data['thread_count'] = count($thread);
                            $user->page_data['thread_uid'] = $thread_id;
                            if (isset($thread_headers[$thread_id]['subject'])) {
                                $user->page_data['thread_subject'] = $thread_headers[$thread_id]['subject'];
                            }
                            else {
                                $user->page_data['thread_subject'] = 'No Subject';
                            }
                            $user->page_data['toggle_all'] = false;
                            $user->page_data['sort_by'] = 'THREAD_R';
                            $user->page_data['mailbox_page'] = 1;
                            $user->page_data['filter_by'] = 'ALL';
                            $user->dsp_page = 'thread_view';
                            $user->page_title = 'Thread view';
                        }
                    }
                }
            }
        }
    }
}
function url_action_logout($get) {
    global $user;
    do_work_hook('logged_out');
    $user->dsp_page = 'logout';
    $user->page_title .= ' | '.$user->str[5].' |';
}
function url_action_folders($get) {
    global $user;
    if ($user->logged_in) {
        do_work_hook('folders_page_start');
        $user->page_data['filter_by'] = false;
        $user->page_data['folder_link_class'] ='current_page';
        $user->dsp_page = 'folders';
        $user->page_title .= ' | '.$user->str[7].' |';
        $user->page_data['folders'] = $_SESSION['folders'];
    }
}
function url_action_about($get) {
    global $user;
    global $imap;
    global $conf;
    global $hastymail_version;
    global $hm_utils_mod;
    if ($user->logged_in) {
        do_work_hook('about_page_start');
        $user->page_data['about_link_class'] ='current_page';
        $user->dsp_page = 'about';
        $user->page_title .= ' | '.$user->str[2].' |';
        $user->page_data['version'] = $hastymail_version;
        if (isset($_SESSION['imap_banner'])) {
            $user->page_data['banner'] = $_SESSION['imap_banner'];
        }
        else {
            $user->page_data['banner'] = '';
        }
        $user->page_data['caps'] = $_SESSION['imap_capability'];
        $user->page_data['imap_server'] = $imap->server;
        $user->page_data['server_time'] = date("r"); 
        $user->page_data['folders'] = $_SESSION['folders'];
        if (isset($conf['plugins'])) {
            $user->page_data['plugins'] = implode(', ', $conf['plugins']);
        }
        else {
            $user->page_data['plugins'] = '';
        }
        if ($imap->use_folder_cache) {
            $user->page_data['fcache_flag'] = $user->str[534];
        }
        else {
            $user->page_data['fcache_flag'] = $user->str[535];
        }
        if ($imap->use_uid_cache) {
            $user->page_data['ucache_flag'] = $user->str[534];
        }
        else {
            $user->page_data['ucache_flag'] = $user->str[535];
        }
        if ($imap->use_header_cache) {
            $user->page_data['hcache_flag'] = $user->str[534];
        }
        else {
            $user->page_data['hcache_flag'] = $user->str[535];
        }
        if ($hm_utils_mod) {
            $user->page_data['mod_util_flag'] = $user->str[534];
        }
        else {
            $user->page_data['mod_util_flag'] = $user->str[535];
        }
        if ($user->ajax_enabled) {
            $user->page_data['ajax_flag'] = $user->str[534];
        }
        else {
            $user->page_data['ajax_flag'] = $user->str[535];
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $user->page_data['host'] = $_SERVER['HTTP_HOST'];
        }
        else {
            $user->page_data['host'] = $user->str[500];
        }
        if (isset($_SERVER['SERVER_ADMIN'])) {
            $user->page_data['admin'] = $_SERVER['SERVER_ADMIN'];
        }
        else {
            $user->page_data['admin'] = $user->str[500];
        }
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $user->page_data['server'] = $_SERVER['SERVER_SOFTWARE'];
        }
        else {
            $user->page_data['server'] = $user->str[500];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user->page_data['browser'] = $_SERVER['HTTP_USER_AGENT'];
        }
        else {
            $user->page_data['browser'] = $user->str[500];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $user->page_data['ip'] = $_SERVER['REMOTE_ADDR'];
        }
        else {
            $user->page_data['ip'] = $user->str[500];
        }
    }
}
}

class site_page_misc extends site_page {
function print_folder_controls() {
    global $imap;
    $exclude_list = array('INBOX');
    foreach ($imap->folder_namespace as $vals) {
        if (!in_array($vals['prefix'], $exclude_list)) {
            $exclude_list[] = $vals['prefix'];
        }
    }
    $list_type = 'all';
    if (isset($imap->folder_list_restricted) && $imap->folder_list_restricted) {
        $list_type = 'noselect';
    } 
    $data = '<tr><td>'.$this->user->str[250].'</td><td><input type="text" name="folder_name" value="" /> '.$this->user->str[260].' <select name="parent_folder_name"><option value="">&#160;</option>'.
            $this->print_folder_option_list($this->pd['folders'], false, 0, array(), true, true, $list_type).'</select></td><td><input type="submit" name="add_folder" value="'.$this->user->str[254].'" />'.
            '</td></tr>';
    if ($list_type == 'noselect') {
        $data .= '<tr><td></td><td colspan="2">For folders instead of messages'./*$this->user->str[]*/' <input type="checkbox" value="1" name="force_subfolder" /></td></tr>';
    }
    $data .= '<tr><td>'.$this->user->str[251].'</td><td><select name="delete_folder_name">'.$this->print_folder_option_list($this->pd['folders'],
            false, 0, array(), true, false, 'no_kids').'</select></td><td><input type="submit" name="delete_folder" onclick="return hm_confirm(\''.$this->user->str[261].
            '\');" value="'.$this->user->str[59].'" /></td></tr><tr><td>'.$this->user->str[252].'</td><td><select name="old_folder_name">'.
            $this->print_folder_option_list($this->pd['folders'], false, 0, array(), true, false, 'custom', $exclude_list).
            '</select> '.$this->user->str[538].' <input type="text" name="new_folder_name" value="" /></td><td><input type="submit" name="rename_folder" value="'.$this->user->str[255].'" /></td></tr>';
    return $data;
}
function print_folder_page_options() {
    $data = '';
    $i = 0;
    $n = 1;
    foreach ($this->pd['folders'] as $vals) {
        if (!isset($vals['realname'])) {
            continue;
        }
        $this->pd['sort_by'] = '';
        if (isset($_SESSION['folder_place_holders']) && in_array($vals['realname'], $_SESSION['folder_place_holders'])) {
            $disabled = 'disabled="disabled" ';
        }
        else {
            $disabled = '';
        }
        $data.= '<tr><td ';
        $class= '';
        if (isset($this->pd['settings']['hidden_folders']) && in_array($vals['realname'], $this->pd['settings']['hidden_folders'])) {
            $class = 'class="hidden_folder"';
            $data .= $class;
        }
        $data .= '>'.$this->user->htmlsafe($vals['realname'], false, false, true).'</td>
        <td '.$class.'><input '.$disabled.' type="checkbox" ';
        if (isset($this->pd['settings']['hidden_folders']) && in_array($vals['realname'], $this->pd['settings']['hidden_folders'])) {
            $data .= 'checked="checked" ';
        }
        $data .= 'name="hidden[]" id="hidden_'.$n.'" value="'.$this->user->htmlsafe($vals['realname'], false, false, true).'" /></td>
        <td '.$class.'><input '.$disabled.' type="checkbox" ';
        if (isset($this->pd['settings']['folder_check']) && in_array($vals['realname'], $this->pd['settings']['folder_check'])) {
            $data .= 'checked="checked" ';
        }
        if (isset($this->pd['settings']['sort_by'][$vals['realname']])) {
            $this->pd['sort_by'] = $this->pd['settings']['sort_by'][$vals['realname']];
        }
        else {
            $this->pd['sort_by'] ='ARRIVAL';
        }
        $data .= 'name="check_for_new[]" id="check_for_new_'.$n.'" value="'.$this->user->htmlsafe($vals['realname'], false, false, true).'" /></td>
        <td '.$class.'><input type="hidden" name="mailbox_index['.$i.']" value="'.$this->user->htmlsafe($vals['realname'], false, false, true).
        '" />'.$this->print_sort_form($i, $disabled).'</td>
        </tr>';
        $i++;
        $n++;
    }
    return $data;
}
function print_profile_form() {
    global $no_profiles;
    $data = '<div>';
    $profiles = $this->pd['settings']['profiles'];
    $count = count($this->pd['settings']['profiles']);
    if ($no_profiles) {
        $count = 1;
    }
    for($i=0;$i<$count;$i++) {
    $profile_address = '';
    $profile_name = '';
    $profile_sig = '';
    $auto_sig = false;
    $profile_reply_to = '';
    $default = false;
    if (isset($profiles[$i]['profile_name'])) {
        $profile_name = $this->user->htmlsafe($profiles[$i]['profile_name'], 'UTF-8');
    }
    if (isset($profiles[$i]['profile_address'])) {
        $profile_address = $this->user->htmlsafe($profiles[$i]['profile_address'], 'UTF-8');
    }
    if (isset($profiles[$i]['profile_sig'])) {
        $profile_sig = $this->user->htmlsafe($profiles[$i]['profile_sig'], 'UTF-8');
    }
    if (isset($profiles[$i]['default']) && $profiles[$i]['default']) {
        $default = 1;
    }
    if (isset($profiles[$i]['profile_reply_to']) && $profiles[$i]['profile_reply_to']) {
        $profile_reply_to = $this->user->htmlsafe($profiles[$i]['profile_reply_to']);
    }
    if (isset($profiles[$i]['auto_sig']) && $profiles[$i]['auto_sig']) {
        $auto_sig = 1;
    }
    if (!empty($this->user->form_vals) && isset($this->user->form_vals['profile_id'])
        && $this->user->form_vals['profile_id'] == $i) {
        foreach ($this->user->form_vals as $n => $v) {
            $$n = $this->user->htmlsafe($v);
        }
    }
    $data .= '<form method="post" action="?page=profile"><h4>Identity '.(1 + $i).'</h4><input type="hidden" name="profile_count" value="'.$count.'" />'.
             '<input type="hidden" name="profile_id" value="'.$i.'" /><table cellpadding="0" cellspacing="0"><tr><th>'.$this->user->str[143].'</th><td><input type="text" '.
             'class="profile_text" name="profile_name" value="'.$profile_name.'" /></td></tr>';
    $data .= '<tr><th>'.$this->user->str[12].'</th><td>';
    if (!$no_profiles) {
        $data .= '<input class="profile_text" type="text" name="profile_address" value="'.$profile_address.'" />';
        $data .= '</td></tr>';
        $data .= '<tr><th>'.$this->user->str[237].':</th><td><input class="profile_text" type="text" '.
                'name="profile_reply_to" value="'.$profile_reply_to.'" /></td></tr>';
    }
    else {
        $data .= $profile_address;
        $data .= '</td></tr>';
    }
             /*<tr><th>'.$this->user->str[238].'</th><td><select name="profile_vcard"><option value="">'.$this->user->str[242].'</option></select></td></tr>'.*/
    $data .= '<tr><th>'.$this->user->str[239].'</th><td><textarea cols="70" style="font-family: monospace" rows="5" name="profile_sig">'.$profile_sig.'</textarea></td></tr>'.
             '<tr><th>'.$this->user->str[240].'</th><td><input type="checkbox" name="auto_sig" value="1" ';
    if ($auto_sig) { $data .= 'checked="checked" '; }
    $data .= '/></td></tr>';
    if (!$no_profiles) {
        $data .= '<tr><th>'.$this->user->str[241].'</th><td><input type="radio" style="background: none; border: none;" name="profile_default" value="1" ';
        if ($default) { $data .= 'checked="checked" '; }
        $data .= '/></td></tr>';
    }
    $data .= '</table><div class="profile_buttons"><input type="submit" name="update_profile" value="'.$this->user->str[193].'" /> &#160;';
    if ($i > 0) {
        $data .= '<input type="submit" name="remove_profile" value="'.$this->user->str[59].'" /> &#160;';
    }
    $data .= '</div>';
    if ($i != $count - 1) {
        $data .= '</form>';
    }
    }
    $data .= '<div class="profile_buttons">';
    if (!$no_profiles) {
        $data .= '<input type="submit" name="add_profile" value="'.$this->user->str[243].'" /> ';
    }
    $data .= '&#160;&#160; <a href="?page=options">'.$this->user->str[244].'</a></div></form></div>';
    return $data;
}
}
?>
