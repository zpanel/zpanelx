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

class fw_user_action extends fw_user_input {
var $page_data;
var $notices;

/* primary methods */
function fw_user_action() {
    $this->notices = array();
    $this->page_data = array();
}
function set_get_vars() {
    global $user;
    $vars = array(
    'page' => array('string', 'Page'),
    'rs' => array('string', 'Ajax call'),
    'mailbox' => array('string', $user->str[22]),
    'mailbox_page' => array('int', 'Page'),
    'sort_by' => array('string', 'Sort by'),
    'filter_by' => array('string', 'Filter by'),
    'uid' => array('int', 'UID'), 
    'reply_part' => array('string', 'Message Part'),
    'reset_results' => array('true', 'Reset Search results'),
    'show_external_images' => array('true', 'Show External Images'),
    'reply_type' => array('string', 'Reply Type'),
    'delete_id' => array('int', 'Delete ID'), 
    'compose_session' => array('string', 'Compose Session'),
    'flag_id' => array('int', 'Flag ID'), 
    'unread_id' => array('int', 'Unread ID'), 
    'inline_html' => array('true', 'Inline HTML'),
    'message_part' => array('string', 'Message Part'),
    'download' => array('true', 'Download Part'),
    'show_image' => array('true', 'Show Image'),
    'framed_part' => array('true', 'Framed Message Part'),
    'full_headers' => array('true', 'Full Headers'),
    'full_header' => array('int', 'Full Header Value'),
    'full_part_header' => array('int', 'Full Header Value'),
    'small_headers' => array('true', 'Small Headers'),
    'raw_view' => array('true', 'Raw View'),
    'print_view' => array('true', 'Print View'),
    'toggle_folder' => array('true', 'Toggle Folder'),
    'filename'=> array('string', 'Filename'),
    'to' => array('email', $user->str[55]),
    'show_previous_options' => array('true', 'Show Image'),
    'show_up_options' => array('true', 'Show up options'),
    'show_next_options' => array('true', 'Show next options'),
    'advanced_view' => array('true', 'Advanced Search'),
    'simple_view' => array('true', 'Simple Search'),
    'reset_search' => array('true', 'Reset Search'),
    'thumbnail' => array('true', 'Thumbnail'),
    'edit_card' => array('int', 'Card ID'),
    'card_detail' => array('int', 'Card ID'),
    'download_card' => array('string', 'Card Id'),
    'attachment_id' => array('string', 'Attachment ID'),
    'find_response' => array('true', 'Find Response'),
    'current_uid' => array('int', 'Current UID'),
    'response_id' => array('string', 'Response ID'),
    'import_card_attachment' => array('true', 'Import Card'),
    'contacts_page' => array('int', 'Contacts page'),
    'folder_state' => array('true', 'Folder state'),
    'track_mail' => array('int', 'Track Folder'),
    'url' => array('string', 'Mailto: URL'),
    'show_all_msg' => array('true', 'Show All Messages'),
    'show_all_mp' => array('true', 'Show all message parts'),
    'edit_group' => array('string', 'Contact group'),
    );
    return $vars;
}
function process_get_vals($get) {
    global $user;
    global $conf;
    global $tools;
    global $hastymail_version;
    global $imap;
    global $app_pages;
    global $include_path;
    global $fd;
    if ($user->is_ajax) {
        return;
    }
    if (is_array($get) && !empty($get)) {
        if (isset($get['mailbox']) && isset($_SESSION['folders'][$get['mailbox']])) {
            $user->page_data['mailbox'] = $get['mailbox'];
            $user->page_data['url_mailbox'] = urlencode($get['mailbox']);
        }
        if (isset($get['page'])) {
            switch (true) {
                case $get['page'] == 'logout':
                    $this->url_action_login($get);
                    break;
                case in_array($get['page'], $app_pages) && method_exists($this, 'url_action_'.$get['page']):
                    $this->{'url_action_'.$get['page']}($get);
                    break;
                default:
                    $found = false;
                    $page_hooks = array();
                    if (isset($_SESSION['plugins']['page_hooks'])) {
                        $page_hooks = $_SESSION['plugins']['page_hooks'];
                    }
                    else {
                        if (!$user->logged_in) {
                            $plugins = get_plugins(true, true); 
                            if (isset($plugins['page_hooks'])) {
                                $page_hooks = $plugins['page_hooks'];
                            }
                        }
                    }
                    if (!empty($page_hooks)) {
                        foreach ($page_hooks as $plugin) {
                            if ($plugin == $get['page']) {
                                $file = 'plugins'.$fd.$plugin.$fd.'page.php';
                                require_check($file);
                                if (is_readable($file)) {
                                    require_once($include_path.$file);
                                }
                                $function_name = 'url_action_'.$plugin;
                                if (function_exists($function_name)) {
                                    $user->dsp_page = $plugin;
                                    if (!$user->logged_in) {
                                        $tools[$plugin] = hm_new('plugin_tools', $plugin);
                                    }
                                    $user->page_data['plugin_data'][$plugin] = $function_name($tools[$plugin], $_GET, $_POST);
                                    if (is_readable('plugins/'.$plugin.'/css/'.$plugin.'.css')) {
                                        $user->page_data['plugin_css'][] = 'plugins/'.$plugin.'/css/'.$plugin.'.css';
                                    }
                                    $found = true;
                                    break;
                                }
                            }
                        }
                    }
                    if (!$found) {
                        $this->url_action_not_found($get);
                        $user->page_data['top_link'] = '';
                    }
                    break;
            }
        }
        else {
            $this->url_action_not_found($get);
            $user->page_data['top_link'] = '';
        }
    }
    else {
        if ($user->logged_in) {
            $this->url_action_mailbox($get);
        }
        else {
            $this->url_action_login($get);
        }
    }
    if (isset($_SESSION['just_logged_in']) && $_SESSION['just_logged_in']) {
        if (isset($_SESSION['updated'])) {
            $this->errors[] = sprintf($user->str[532], $hastymail_version);
            unset($_SESSION['updated']);
        }
        else {
            $this->errors[] = $user->str[383].' '.$user->htmlsafe($_SESSION['user_data']['username']);
        }
        do_work_hook('just_logged_in');
        if (isset($_SESSION['user_settings']['new_user']) && $_SESSION['user_settings']['new_user']) {
            $this->autocreate_folders();
            $this->autocreate_profile($conf);
            do_work_hook('first_time_login');
            $this->write_settings(true);
            $user->page_data['settings'] = $_SESSION['user_settings'];
            $imap->get_folders(true);
            $user->page_data['folders'] = $_SESSION['folders'];
        }
        if (isset($_SESSION['user_settings']['profiles'][0]) && empty($_SESSION['user_settings']['profiles'][0]) &&
            count($_SESSION['user_settings']['profiles']) == 1) {
            $this->errors[] = $user->str[384];
        }
        $this->set_default_profile($conf);
    }
}
function set_default_profile($conf) {
    if (isset($conf['alt_imap_profiles']) && $conf['alt_imap_profiles']) {
        $email = false;
        $username = $_SESSION['user_data']['username'];
        if (isset($_SESSION['imap_index']) && isset($conf['alt_'.$_SESSION['imap_index'].'_default_email_address']) &&
            $conf['alt_'.$_SESSION['imap_index'].'_default_email_address']) {
            $email = $conf['alt_'.$_SESSION['imap_index'].'_default_email_address'];
        }
        elseif (isset($conf['user_defaults']['email_address']) && $conf['user_defaults']['email_address']) {
            $email = $conf['user_defaults']['email_address'];
        }
        elseif (!$email && strpos($username, '@') !== false) {
            $email = $username;
        }
        if ($email) {
            $email = trim(str_replace('%u', $_SESSION['user_data']['username'], $email));
            if (isset($conf['percent_d_host']) && trim($conf['percent_d_host']) && strstr($email, '%d')) {
                $domain = $user->get_domain($conf['host_name'], $conf['percent_d_host']);
                $email = str_replace('%d', $domain, $email);
            }
            $found = false;
            if (isset($_SESSION['user_settings']['profiles'])) {
                foreach ($_SESSION['user_settings']['profiles'] as $index => $vals) {
                    if (isset($vals['profile_address']) && $vals['profile_address'] == $email) {
                        $found = $index;
                    }
                }
            }
            if ($found === false) {
                $_SESSION['user_settings']['profiles'][] = array(
                    'profile_address' => $email,
                    'profile_name' => $_SESSION['user_data']['username'],
                    'profile_default' => 1,
                );
            }
            else {
                for ($i=0;$i<count($_SESSION['user_settings']['profiles']);$i++) {
                    $_SESSION['user_settings']['profiles'][$i]['profile_default'] = 0;
                }
                $_SESSION['user_settings']['profiles'][$found]['profile_default'] = 1;
            }
        }
    }
}
function autocreate_profile($conf) {
    global $user;
    $email = false;
    $username = $_SESSION['user_data']['username'];
    if (isset($conf['user_defaults']['email_address']) && $conf['user_defaults']['email_address']) {
        $email = $conf['user_defaults']['email_address'];
        if (strpos($username, '@') !== false) {
            $username = substr($username, 0, strpos($username, '@'));
        }
        $email = trim(str_replace('%u', $username, $email));
        if (isset($conf['percent_d_host']) && trim($conf['percent_d_host']) && strstr($email, '%d')) {
            $domain = $user->get_domain($conf['host_name'], $conf['percent_d_host']);
            $email = str_replace('%d', $domain, $email);
        }
    }
    if (!$email && strpos($username, '@') !== false) {
        $email = $username;
    }
    if ($email) {
        if (isset($_SESSION['user_settings']['profiles'][0]) && empty($_SESSION['user_settings']['profiles'][0]) &&
            count($_SESSION['user_settings']['profiles']) == 1) {
            $_SESSION['user_settings']['profiles'][0] = array(
                'profile_address' => $email,
                'profile_name' => $_SESSION['user_data']['username'],
                'profile_default' => 1,
            );
        }
    }
}
function default_page_data() {
    global $imap;
    global $conf;
    global $user;
    global $tools;
    global $start_pages;
    global $hm_tags;
    if (!isset($user->dsp_page)) {
        $user->dsp_page = 'not_found';
        $user->page_data['top_link'] = '';
    }
    if (isset($_SESSION['last_message_read'])) {
        $user->page_data['last_message_read'] = $_SESSION['last_message_read'];
    }
    else {
        $user->page_data['last_message_read'] = array();
    }
    $user->page_data['site_title'] = $conf['page_title'];
    $user->page_data['sort_by'] = 'ARRIVAL';
    $user->page_data['top_link'] = '';
    $user->page_data['plugin_js'] = array();
    $user->page_data['inline_plugin_js'] = array();
    $user->page_data['plugin_style'] = array();
    $user->page_data['base_href'] = $conf['http_prefix'].'://'.$conf['host_name'].$conf['url_base'];
    $user->page_data['theme'] = 'default';
    $user->page_data['new_window'] = false;
    $user->page_data['new_window_arg'] = false;
    $user->page_data['parent_refresh'] = false;
    if ($user->logged_in) {
        if (isset($_SESSION['frozen_folders'])) {
            $user->page_data['frozen_folders'] = $_SESSION['frozen_folders'];
            if ((isset($_GET['mailbox']) && isset($_SESSION['frozen_folders'][$_GET['mailbox']])) ||
               (isset($_POST['mailbox']) && isset($_SESSION['frozen_folders'][$_POST['mailbox']]))) {
                $imap->read_only = true;
            }
        }
        else {
            $user->page_data['frozen_folders'] = array();
        }
        $this->set_user_config();
        $imap->get_folders();
        if (!isset($_SESSION['user_settings']['profiles'])) {
            $user->page_data['settings']['profiles'] = array(array());
            $_SESSION['user_settings']['profiles'] = array(array());
        }
        $theme = 'default';
        if (isset($user->page_data['settings']['theme'])) {
            $user_theme = $user->page_data['settings']['theme'];
            if (isset($conf['site_themes'][$user_theme])) {
                if ($conf['site_themes'][$user_theme]['css']) {
                    $theme = $user_theme;
                }    
            }
        }
        $user->page_data['theme'] = $theme;
        $user->page_data['new_link_class'] = '';
        $user->page_data['search_link_class'] = '';
        $user->page_data['options_link_class'] = '';
        $user->page_data['profile_link_class'] = '';
        $user->page_data['folder_link_class'] = '';
        $user->page_data['compose_link_class'] = '';
        $user->page_data['contacts_link_class'] = '';
        $user->page_data['folders'] =& $_SESSION['folders'];
        $user->page_data['mailbox'] = 'INBOX';
        $user->page_data['url_mailbox'] = 'INBOX';
        $user->page_data['simple_mode'] = false;
        if (isset($user->page_data['settings']['compose_window']) && $user->page_data['settings']['compose_window']) {
            $sid = '';
            if (!$user->use_cookies) {
                $sid = '&amp;PHPSESSID='.session_id();
            } 
            $user->page_data['compose_onclick'] = 'open_window(\'?page=compose&amp;new_window=1'.$sid.'\', 900, 950); return false;';
        }
        else {
            $user->page_data['compose_onclick'] = false;
        }   
        if (isset($_GET['parent_refresh'])) {
            $user->page_data['parent_refresh'] = true;
        }
        if (isset($_GET['new_window'])) {
            $user->page_data['new_window'] = true;
            $user->page_data['new_window_arg'] = '&amp;new_window=1';
        }
        if (isset($_SESSION['destination_folder'])) {
            $user->page_data['current_destination'] = $_SESSION['destination_folder'];
        }
        else {
            $user->page_data['current_destination'] = false;
        }
        $user->page_data['imap_capability'] = $_SESSION['imap_capability'];
        if (isset($_SESSION['plugin_list'])) {
            foreach ($_SESSION['plugin_list'] as $v) {
                $tools[$v] = hm_new('plugin_tools', $v);
            }
        }
        do_work_hook('init');
        if (!isset($user->page_data['plugin_ajax'])) {
            $user->page_data['plugin_ajax'] = array();
        }
        if (!empty($user->page_data['plugin_ajax'])) {
            foreach ($user->page_data['plugin_ajax'] as $i => $v) {
                $_SESSION['plugin_ajax'][$i] = $v;
            }
        }
        if (($user->user_agent_class == 'palm' || $user->user_agent_class == 'simple') &&
            isset($_SESSION['user_settings']['auto_switch_simple_mode']) && $_SESSION['user_settings']['auto_switch_simple_mode']) {
            $user->page_data['simple_mode'] = true;
            $hm_tags['complex'] = true;
            $hm_tags['simple'] = false;
        }
        elseif (isset($_SESSION['user_settings']['display_mode']) && $_SESSION['user_settings']['display_mode'] != 1) {
            switch ($_SESSION['user_settings']['display_mode']) {
                case 2:
                    $user->page_data['simple_mode'] = true;
                    $hm_tags['complex'] = true;
                    $hm_tags['simple'] = false;
                    break;
            }
        }
        if (isset($_GET['folder_state'])) { 
            $f_id = (int) $_GET['folder_state'];
            if (strlen($f_id) > 1) {
                $state = substr($f_id, -1);
                $f_id = substr($f_id, 0, -1);
                if (!$state) {
                    $state = true;
                }
                else {
                    $state = false;
                }
                $_SESSION['folder_state'][$f_id] = $state;
            }
        }
        purge_old_attachments(); 
    }
    else {
        if (isset($conf['site_theme'])) {
            $theme = $conf['site_theme'];
            if (isset($conf['site_themes'][$theme])) {
                $user->page_data['settings']['theme'] = $theme;
                $user->page_data['theme'] = $theme;
            }
        }
        do_work_hook('logged_out_init', array(), get_plugins(true, true));
    }
    if (!$user->logged_in && ($user->user_agent_class == 'simple' || $user->user_agent_class == 'palm')) {
        $hm_tags['complex'] = true;
        $hm_tags['simple'] = false;
    }
    if ($user->just_logged_in && !isset($_SESSION['total_unread'])) {
        $_SESSION['total_unread'] = 0;
        if (isset($_SESSION['user_settings']['start_page']) && $_SESSION['user_settings']['start_page'] != 'mailbox'
            && $_SESSION['user_settings']['start_page'] != 'new' && isset($start_pages[$_SESSION['user_settings']['start_page']])) {
            if (isset($_SESSION['user_settings']['folder_check']) && is_array($_SESSION['user_settings']['folder_check'])) {
                $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
            }
            else {
                $_SESSION['total_unread'] = 0;
            }
        }
    }
}
function logout_actions() {
    global $user;
    $this->set_user_config(true);
    $user->set_timezone();
    do_work_hook('before_logout');
    if (isset($_SESSION['user_settings']['expunge_on_exit']) && $_SESSION['user_settings']['expunge_on_exit']) {
        $this->perform_imap_action('EXPUNGE', 'INBOX', array(), false, false, false, true);
    }
    $user->page_data['settings'] = array();
    $_SESSION['user_settings'] = array();
    unset($_SESSION['user_settings']);
    $user->user_session->close_session();
}

/* helper functions */
function set_user_config($force=false) {
    global $user;
    global $imap;
    global $conf;
    if ($force || $user->logged_in) {
        if ($user->settings_storage == 'db') {
            $this->get_settings_db();
        }
        else {
            $this->get_settings_file();
        }
        if (isset($conf['user_defaults'])) {
            foreach ($conf['user_defaults'] as $i => $v) {
                if (!isset($user->page_data['settings'][$i])) {
                    $user->page_data['settings'][$i] = $v;
                }
            }
        }
        if (!isset($conf['site_themes'][$user->page_data['settings']['theme']])) {
            $uesr->page_data['settings']['theme'] = 'default';
        }
        $font_size = 100;
        if (isset($user->page_data['settings']['font_size'])) {
            $font_size = (int) $user->page_data['settings']['font_size'];
        }
        if (!$font_size) {
            $user->page_data['settings']['font_size'] = 100;
        }
        else {
            $user->page_data['settings']['font_size'] = $font_size;
        }
        if (!isset($user->page_data['settings']['small_headers']) || !is_array($user->page_data['settings']['small_headers'])) {
            $user->page_data['settings']['small_headers'] = array('subject', 'to', 'cc', 'from', 'date');
        }
        $_SESSION['user_settings'] = $user->page_data['settings'];
    }
    do_work_hook('set_config_value');
}
function get_settings_file() {
    global $user;
    global $conf;
    global $fd;
    $settings_path = $conf['settings_path'];
    if (isset($conf['user_defaults'])) {
        $user->page_data['settings'] = $conf['user_defaults'];
    }
    else {
        $user->page_data['settings'] = array();
    }
    if (isset($_SESSION['user_settings'])) {
        $user->page_data['settings'] = $_SESSION['user_settings'];
    }
    else {
        if ($user->just_logged_in) {
            $username = $_SESSION['user_data']['username'];
            if (substr($settings_path, -1) != $fd) {
                $filename = $settings_path.$fd.$username.'.settings';
            }
            else {
                $filename = $settings_path.$username.'.settings';
            }
            if (is_readable($filename)) {
                $data = trim(implode('', file($filename)));
                $data = @unserialize($data);
                if (is_array($data)) {
                    $user->page_data['settings'] = $data;
                }
                else {
                    $this->errors[] = $user->str[392].': '.$filename;
                }
            }
            else {
                $user->page_data['settings']['new_user'] = true;
            }
        }
    }
}
function get_settings_db() {
    global $dbase;
    global $user;
    if (isset($_SESSION['user_settings'])) {
        $user->page_data['settings'] = $_SESSION['user_settings'];
    }
    else {
        if (!is_object($dbase)) {
            $this->errors[] = $user->str[393];
            return;
        }
        else {
            $sql = 'select * from user_setting where username='.$dbase->qt($user->username);
            $res = $dbase->select($sql);
            if (isset($res[0]['settings'])) {
                $settings = @unserialize($res[0]['settings']);
                if (is_array($settings)) {
                    $user->page_data['settings'] = $settings;
                }
                else {
                    $this->errors[] = $user->str[394].': '.$res[0]['id'];
                }
            }
            else {
                $user->page_data['settings']['new_user'] = true;
            }
        }
    }
}
function write_settings($quiet=false) {
    global $user;
    if (isset($_SESSION['user_settings']['new_user'])) {
        unset($_SESSION['user_settings']['new_user']);
    }
    if ($user->settings_storage == 'db') {
        $this->write_settings_db($quiet);
    }
    else {
        $this->write_settings_file($quiet);
    }
}
function write_settings_file($quiet) {
    global $conf;
    global $user;
    global $fd;
    $settings_path = $conf['settings_path'];
    $username = $_SESSION['user_data']['username'];
    if (substr($settings_path, -1) != $fd) {
        $filename = $settings_path.$fd.$username.'.settings';
    }
    else {
        $filename = $settings_path.$username.'.settings';
    }
    $handle = @fopen($filename, "w");
    if (@fwrite($handle, serialize($_SESSION['user_settings']))) {
        @fclose($handle);
        if (!$quiet) {
            $this->errors[] = $user->str[411];
        }
    }
    else {
        if (!$quiet) {
            $this->errors[] = $user->str[412];
        }
    }
}
function write_settings_db($quiet) {
    global $dbase;
    global $user;
    if (!is_object($dbase)) {
        if (!$quiet) {
            $this->errors[] = $user->str[393];
        }
        return;
    }
    else {
        $exists = $dbase->select('select id from user_setting where username='.$dbase->qt($user->username));
        if (isset($exists[0]['id'])) {
            $sql = 'update user_setting set settings='.$dbase->qt(serialize($_SESSION['user_settings'])).' where id='.$exists[0]['id'];
            $res = $dbase->update($sql);
        }
        else {
            $sql = 'insert into user_setting (username, settings) values('.$dbase->qt($user->username).', '.$dbase->qt(serialize($_SESSION['user_settings'])).')';
            $res = $dbase->insert($sql);
        }
        if ($res) {
            if (!$quiet) {
                $this->errors[] = $user->str[411];
            }
        }
    }
}
function find_message_part($struct, $part, $type='text', $subtype=false, $viewable=array(), $level=0) {
    global $message_part_types;
    $res = array();
    if (!is_array($struct) || empty($struct)) {
        return array($res, $viewable);
    }
    $subs = array();
    foreach ($struct as $id => $vals) {
        if ($part && $id == $part) {
            $vals['imap_id'] = $id;
            $res = $vals;
        }
        elseif (!$part && isset($vals['type']) && $vals['type'] == $type) {
            if ($vals['disposition'] != 'attachment') {
                if ($subtype) {
                    if ($subtype == $vals['subtype']) {
                        $vals['imap_id'] = $id;
                        $res = $vals;
                    }
                }
                else {
                    $vals['imap_id'] = $id;
                    $res = $vals;
                }
            }
        }
        if (!$part && isset($vals['type']) && $vals['type'] == 'text' && isset($message_part_types[$vals['type'].'/'.$vals['subtype']])) {
            $viewable[] = $id;
        }
        if (isset($vals['subs'])) {
            $subs[] = $vals['subs'];
        }
        if (!empty($res)) {
            break;
        }
    }
    if ($level == 0 && empty($res) && !empty($viewable)) {
        if ($struct[$viewable[0]]['disposition'] != 'attachment') {
            $res = $struct[$viewable[0]];
            $res['imap_id'] = $viewable[0];
        }
    }
    if (empty($res) && !empty($subs)) {
        $level++;
        foreach ($subs as $v) {
            list($res, $viewable) =  $this->find_message_part($v, $part, $type, $subtype, $viewable, $level);
            if (!empty($res)) {
                break;
            }
        }
    } 
    return array($res, $viewable);
}
function perform_imap_action($action, $mailbox, $uids, $trash_folder, $destination, $uid_string=false, $force=false, $silent=false) {
    global $imap;
    global $user;
    $errors = array();
    do_work_hook('imap_action', array($action, $mailbox, $uids, $destination));
    if ($uid_string) {
        $uids = array();
    }
    if (isset($_SESSION['frozen_folders'][$mailbox])) {
        $errors[] = $user->str[395].': '.$user->htmlsafe($mailbox);
        return;
    }
    if ($destination && isset($_SESSION['frozen_folders'][$mailbox])) {
        $errors[] = $user->str[395].': '.$user->htmlsafe($mailbox);
        return;
    }
    if ($imap->read_only) {
        $errors[] = $user->str[396];
        return;
    }
    $bail = false;
    $status = $imap->select_mailbox($mailbox, false, false, true);
    if (empty($_POST) && isset($_SESSION['uid_cache'][$mailbox]['uids'])) {
        $bail = true;
        foreach ($uids as $v) {
            if (in_array($v, $_SESSION['uid_cache'][$mailbox]['uids'])) {
                $bail = false;
                break;
            }
        }
    }
    if ($bail && !$force) {
        return;
    }
    if ($status) {
        switch ($action) {
            case 'READ':
                $status = $imap->message_action($uids, 'READ', false, $uid_string); 
                if ($status) {
                    $errors[] = $user->str[397].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                    $_SESSION['header_cache_refresh'][$mailbox] = 1;
                }
                break;
            case 'FLAG':
                $status = $imap->message_action($uids, 'FLAG', false, $uid_string); 
                if ($status) {
                    $errors[] = $user->str[398].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                    $_SESSION['header_cache_refresh'][$mailbox] = 1;
                }
                break;
            case 'UNFLAG':
                $status = $imap->message_action($uids, 'UNFLAG', false, $uid_string); 
                if ($status) {
                    $errors[] = $user->str[399].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                    $_SESSION['header_cache_refresh'][$mailbox] = 1;
                }
                break;
            case 'ANSWERED':
                $status = $imap->message_action($uids, 'ANSWERED', false, $uid_string); 
                if ($status) {
                    $_SESSION['header_cache_refresh'][$mailbox] = 1;
                }
                break;
            case 'UNREAD':
                $status = $imap->message_action($uids, 'UNREAD', false, $uid_string); 
                if ($status) {
                    $errors[] = $user->str[400].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                    $_SESSION['header_cache_refresh'][$mailbox] = 1;
                }
                break;
            case 'DELETE':
                if ($trash_folder && $trash_folder != $mailbox) {
                    $status = $imap->message_action($uids, 'COPY', $trash_folder, $uid_string); 
                }
                else {
                    $status = true;
                }
                if ($status) {
                    $status = $imap->message_action($uids, 'DELETE', false, $uid_string); 
                    if ($status && $trash_folder) {
                        $status = $imap->message_action($uids, 'EXPUNGE', false, $uid_string); 
                        if ($status) {
                            $errors[] = $user->str[402].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                        }
                        else {
                            $errors[] = $user->str[404];
                        }
                    }
                    else {
                        if ($status) {
                            $errors[] = $user->str[402].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                            $_SESSION['header_cache_refresh'][$mailbox] = 1;
                        }
                        else {
                            $errors[] = $user->str[405];
                        }
                    }
                }
                else {
                    $errors[] = $user->str[406];
                }
                break;
            case 'UNDELETE':
                $status = $imap->message_action($uids, 'UNDELETE', false, $uid_string); 
                if ($status) {
                    $errors[] = $user->str[403].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                    $_SESSION['header_cache_refresh'][$mailbox] = 1;
                }
                break;
            case 'COPY':
                $status = $imap->message_action($uids, 'COPY', $destination, $uid_string); 
                if ($status) {
                    $errors[] = $user->str[407].': '.count($uids).' ('.$user->htmlsafe($mailbox).' -&gt; '.$user->htmlsafe($destination).')';
                    $_SESSION['destination_folder'] = $destination;
                }
                else {
                    $errors[] = $user->str[408];
                }
                break;
            case 'MOVE':
                $status = $imap->message_action($uids, 'COPY', $destination, $uid_string); 
                if ($status) {
                    $status = $imap->message_action($uids, 'DELETE', false, $uid_string);
                    if ($status) {
                        $status = $imap->message_action($uids, 'EXPUNGE', false, $uid_string);
                        if ($status) {
                            $errors[] = $user->str[409].': '.count($uids).' ('.$user->htmlsafe($mailbox).' -&gt; '.$user->htmlsafe($destination).')';
                            $_SESSION['destination_folder'] = $destination;
                            $_SESSION['header_cache_refresh'][$mailbox] = 1;
                        }
                        else {
                            $errors[] = $user->str[404];
                        }
                    }
                    else {
                        $errors[] = $user->str[405];
                    }
                }
                else {
                    $errors[] = $user->str[408];
                }
                break;
            case 'EXPUNGE':
                $status = $imap->message_action($uids, 'EXPUNGE', false, $uid_string); 
                if ($status && !$force) {
                    if (isset($_SESSION['user_settings']['selective_expunge']) && $_SESSION['user_settings']['selective_expunge']) {
                        $errors[] = $user->str[410].': '.count($uids).' ('.$user->htmlsafe($mailbox).')';
                    }
                    else {
                        $errors[] = $user->str[410].' ('.$user->htmlsafe($mailbox).')';
                    }
                }
                elseif (!$force) {
                    $errors[] = $user->str[404];
                }

                default:
                break;
        }
    }
    else {
        if (!$force) {
            $errors[] = $user->str[387].': '.$user->htmlsafe($mailbox);
        }
    }
    if (!$silent) {
        if (!is_array($this->errors)) {
            $this->errors = array();
        }
        $this->errors = array_merge($this->errors, $errors);
    }
    do_work_hook('after_imap_action', array($action, $mailbox, $uids, $destination));
}
function get_struct_part($struct, $filename, $fuid=false) {
    $res = false;
    $atts = array();
    foreach ($struct as $i => $vals) {
        if (isset($vals['filename']) && $vals['filename'] == $filename) {
            $res = $i;
            $atts = $vals;
        }
        elseif (isset($vals['name']) && $vals['name'] == $filename) {
            $res = $i;
            $atts = $vals;
        }
        elseif (isset($vals['id']) && strstr($vals['id'], $filename)) {
            $res = $i;
            $atts = $vals;
        }
        elseif (isset($vals['description']) && $vals['description'] == $filename) {
            $res = $i;
            $atts = $vals;
        }
        elseif (isset($vals['subs']) && is_array($vals['subs']) && !empty($vals['subs'])) {
            list($atts, $res) = $this->get_struct_part($vals['subs'], $filename, $fuid);
        }
        if ($res && $fuid) {
            if (!preg_match("/\.$fuid$/", $res)) {
                $res = false;
            }
        }
        if ($res) {
            return array($atts, $res);
        }
    }
    return array($atts, $res);
}
function autocreate_folders() {
    global $conf;
    global $imap;
    $folders = array();
    if (isset($conf['auto_create_sent']) && $conf['auto_create_sent'] &&
        isset($conf['sent_folder']) && $conf['sent_folder']) {
        $folders['sent'] = $conf['sent_folder'];
    }
    if (isset($conf['auto_create_drafts']) && $conf['auto_create_drafts'] &&
        isset($conf['drafts_folder']) && $conf['drafts_folder']) {
        $folders['draft'] = $conf['drafts_folder'];
    }
    if (isset($conf['auto_create_trash']) && $conf['auto_create_trash'] &&
        isset($conf['trash_folder']) && $conf['trash_folder']) {
        $folders['trash'] = $conf['trash_folder'];
    }
    foreach ($folders as $i => $v) {
        $tmp_name = $imap->prep_folder_name($v, $imap->folder_prefix, false, false);
        if (isset($_SESSION['folders'][$tmp_name])) {
            $_SESSION['user_settings'][$i.'_folder'] = $tmp_name;
            continue;
        }
        else {
            $res = $imap->create_folder($imap->folder_prefix, $v, false);
            $_SESSION['user_settings'][$i.'_folder'] = $tmp_name;
        }
    }
}
function url_action_inline_image($get) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $mailbox = false;
        if (isset($get['mailbox'])) {
            if (isset($_SESSION['folders'][$get['mailbox']])) {
                $mailbox = $get['mailbox'];
            }
        }
        if ($mailbox && isset($get['uid'])) {
            $id = (int) $get['uid'];
            if ($id) {
                if ($get['filename']) {
                    $fuid = false;
                    $filename = $get['filename'];
                    if (preg_match("/^(\d+)_multipart\?(\d+)_(.+)$/", $filename, $matches)) {
                        $filename = $matches[3];
                        $fuid = $matches[2];
                    }
                    $status = $imap->select_mailbox($mailbox, false, false, true);
                    if ($status && $filename) {
                        $struct = $imap->get_message_structure($id);
                        list($atts, $num) = $this->get_struct_part($struct, $filename, $fuid);
                        if ($num && !empty($atts)) {
                            $data = $imap->get_message_part($id, $num);
                            if (isset($atts['encoding']) && strtolower($atts['encoding']) == 'base64') {
                                $data = base64_decode($data);
                            }
                            elseif (isset($atts['encoding']) && strtolower($atts['encoding']) == 'quoted-printable') {
                                $data = quoted_printable_decode($data);
                            }
                            if ($data) {
                                ob_end_clean();
                                $size = (int) $atts['size'];
                                header("Content-Type: ".$atts['type']);
                                header("Content-Length: ".strlen($data));
                                echo $data;
                                $imap->disconnect();
                                $user->clean_up();
                                exit;
                            }
                        }
                    }
                }
            }
        }
    }
}
function url_action_not_found($get) {
    global $user;
    $user->dsp_page = 'not_found';
    $user->page_title .= ' | Not Found |';
    $user->page_data['top_link'] = '';
    if ($user->logged_in) {
        do_work_hook('not_found_start');
        $user->page_data['folders'] = $_SESSION['folders'];
    }
}
function url_action_login($get) {
    global $user;
    global $imap;
    $user->dsp_page = 'login';
    if ($user->logged_in) {
        $this->url_action_mailbox($get);
    }
    else {
        $user->page_title .= $user->str[6].' |';
    }
}
} ?>
