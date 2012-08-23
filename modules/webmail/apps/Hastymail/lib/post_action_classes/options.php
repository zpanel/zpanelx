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
class fw_post_action_options extends fw_user_action_with_post {
function set_post_page_vars() {
    global $user;
    $forms = array(
    'update_settings' => array(
        'show_folder_list'       => array('true', 0, 'Show Folder List'),
        'disable_folder_icons'   => array('true', 0, 'Disable Folder Icons'),
        'folder_style'           => array('int', 1, 'Folder Style'),
        'folder_detail'          => array('int', 1, 'Folder Detail'),
        'ajax_update_interval'   => array('int', 0, 'Folder Update Interval'),
        'lang'                   => array('string', 1, 'Language'),
        'theme'                  => array('string', 1, 'Theme'),
        'font_size'              => array('int', 1, 'Font Size'),
        'timezone'               => array('string', 0, 'Timezone'),
        'text_links'             => array('true', 0, 'Clickable links'),
        'text_email'             => array('true', 0, 'Clickable emails'),
        'hl_reply'               => array('true', 0, 'Highlight reply'),
        'mailbox_per_page_count' => array('int_nonzero', 1, 'Messages per page'),
        'font_family'            => array('string', 1, 'Font Family'),
        'trash_folder'           => array('string', 0, 'Trash Folder'),
        'sent_folder'            => array('string', 0, 'Sent Folder'),
        'draft_folder'           => array('string', 0, 'Draft Folder'),
        'new_page_refresh'       => array('int', 0, 'New page refresh'),
        'small_headers'          => array('array', 0, 'Small Headers'),
        'full_headers_default'   => array('true', 0, 'Full Headers'),
        'enable_delete_warning'  => array('true', 0, 'Delete confirmation'),
        'mailbox_controls_bottom'=> array('true', 0, 'Show message controls below list'),
        'html_first'             => array('true', 0, 'HTML first'),
        'remote_image'           => array('true', 0, 'Remote Images'),
        'subscribed_only'        => array('true', 0, 'Subscribed folders only'),
        'full_mailbox_option'    => array('true', 0, 'Show full mailbox option'),
        'mailbox_update'         => array('true', 0, 'Update mailbox display'),
        'disable_list_icons'     => array('true', 0, 'Disable list icons'),
        'disable_checked_js'     => array('true', 0, 'Disable selected highlight'),
        'default_message_action' => array('string', 0, 'Default message action'),
        'image_thumbs'           => array('true', 0, 'Image attachment thumbnails'),
        'display_mode'           => array('int', 1, 'Display Mode'),
        'compose_text_format'    => array('int', 1, 'Text Format'),
        'compose_hide_mailer'    => array('true', 0, 'Hide User Agent'),
        'compose_window'         => array('true', 0, 'Compose in a new window'),
        'close_on_send'          => array('true', 0, 'Close new window on send'),
        'message_window'         => array('true', 0, 'Read in a new window'),
        'new_window_icon'        => array('true', 0, 'Read message in new window icon'),
        'delete_draft'           => array('true', 0, 'Delete Draft'),
        'compose_text_encoding'  => array('int', 1, 'Text Encoding'),
        'smtp_auth'              => array('string', 0, 'SMTP Authentaction'),
        'smtp_user'              => array('string', 0, 'SMTP Username'),
        'smtp_pass'              => array('string', 0, 'SMTP Password'),
        'hide_folder_on_empty'   => array('true', 0, 'Hide empty new page folders'),
        'mailbox_freeze'         => array('true', 0, 'Show mailbox freeze option'),
        'always_expunge'         => array('true', 0, 'Always show expunge option'),
        'hide_deleted_messages'  => array('true', 0, 'Hide deleted messages'),
        'selective_expunge'      => array('true', 0, 'Only expunge selected messages'),
        'top_page_links'         => array('true', 0, 'Top Page Links'),
        'compose_autosave'       => array('int', 0, 'Compose Autosave'),
        'date_format'            => array('string', 0, 'Date Format'),
        'time_format'            => array('string', 0, 'Time Format'),
        'mailbox_date_format'    => array('string', 0, 'Mailbox time format'),
        'mailbox_date_format_2'  => array('string', 0, 'Mailbox time format'),
        'auto_switch_simple_mode'=> array('true', 0, 'Auto switch display mode'),
        'short_message_parts'    => array('true', 0, 'Minimize message parts display'),
        'expunge_on_exit'        => array('true', 0, 'Expunge INBOX on exit'),
        'start_page'             => array('string', 0, 'First page after login'),
        'trim_from_fld'          => array('int', 0, 'From field length'),
        'trim_subject_fld'       => array('int', 0, 'Subject field length'),
    ),
    ); return $forms;
}
function form_action_update_settings($form, $post) {
    global $user;
    global $imap;
    global $conf;
    global $smtp_auth_mechs;
    global $date_formats;
    global $time_formats;
    global $hm_tags;
    global $start_pages;
    global $max_msg_per_page;
    global $langs;
    global $prev_next_actions;
    $folder_refresh = false;
    $font_families = array( 'monospace', 'serif', 'sans-serif', 'cursive', 'fantasy');
    if ($user->logged_in) {
        $settings = array();
        if (isset($conf['user_defaults'])) {
            $settings = $conf['user_defaults'];
        }
        if (isset($post['lang']) && isset($langs[$post['lang']])) {
            $settings['lang'] = $post['lang'];
        }
        else {
            $settings['lang'] = 'en_US';
        }
        if (isset($post['start_page']) && isset($start_pages[$post['start_page']])) {
            $settings['start_page'] = $post['start_page'];
        }
        else {
            $settings['start_page'] = 'mailbox';
        }
        if (isset($post['enable_delete_warning']) && $post['enable_delete_warning']) {
            $settings['enable_delete_warning'] = 1;
        }
        else {
            $settings['enable_delete_warning'] = 0;
        }
        if (isset($post['mailbox_controls_bottom']) && $post['mailbox_controls_bottom']) {
            $settings['mailbox_controls_bottom'] = 1;
        }
        else {
            $settings['mailbox_controls_bottom'] = 0;
        }
        if (isset($post['full_headers_default']) && $post['full_headers_default']) {
            $settings['full_headers_default'] = 1;
        }
        else {
            $settings['full_headers_default'] = 0;
        }
        if (isset($post['small_headers'])) {
            $settings['small_headers'] = $post['small_headers'];
        }
        if (isset($_SESSION['user_settings']['sort_by'])) {
            $settings['sort_by'] = $_SESSION['user_settings']['sort_by'];
        }
        if (isset($_SESSION['user_settings']['hidden_folders'])) {
            $settings['hidden_folders'] = $_SESSION['user_settings']['hidden_folders'];
        }
        if (isset($_SESSION['user_settings']['folder_check'])) {
            $settings['folder_check'] = $_SESSION['user_settings']['folder_check'];
        }
        if (isset($post['compose_text_format'])) {
            $settings['compose_text_format'] = $post['compose_text_format'];
        }
        else {
            $settings['compose_text_format'] = 0;
        }
        if (isset($post['compose_text_encoding'])) {
            $settings['compose_text_encoding'] = $post['compose_text_encoding'];
        }
        else {
            $settings['compose_text_encoding'] = 0;
        }
        if (isset($post['compose_window']) && $post['compose_window']) {
            $settings['compose_window'] = 1;
        }
        else {
            $settings['compose_window'] = 0;
        }
        if (isset($post['close_on_send']) && $post['close_on_send']) {
            $settings['close_on_send'] = 1;
        }
        else {
            $settings['close_on_send'] = 0;
        }
        if (isset($post['message_window']) && $post['message_window']) {
            $settings['message_window'] = 1;
        }
        else {
            $settings['message_window'] = 0;
        }
        if (isset($post['new_window_icon']) && $post['new_window_icon']) {
            $settings['new_window_icon'] = 1;
        }
        else {
            $settings['new_window_icon'] = 0;
        }
        if (isset($post['compose_hide_mailer']) && $post['compose_hide_mailer']) {
            $settings['compose_hide_mailer'] = 1;
        }
        else {
            $settings['compose_hide_mailer'] = 0;
        }
        if (isset($post['delete_draft']) && $post['delete_draft']) {
            $settings['delete_draft'] = 1;
        }
        else {
            $settings['delete_draft'] = 0;
        }
        if (isset($post['mailbox_update']) && $post['mailbox_update']) {
            $settings['mailbox_update'] = 1;
        }
        else {
            $settings['mailbox_update'] = 0;
        }
        if (isset($post['disable_checked_js']) && $post['disable_checked_js']) {
            $settings['disable_checked_js'] = 1;
        }
        else {
            $settings['disable_checked_js'] = 0;
        }
        if (isset($post['disable_list_icons']) && $post['disable_list_icons']) {
            $settings['disable_list_icons'] = 1;
        }
        else {
            $settings['disable_list_icons'] = 0;
        }
        if (isset($post['full_mailbox_option']) && $post['full_mailbox_option']) {
            $settings['full_mailbox_option'] = 1;
        }
        else {
            $settings['full_mailbox_option'] = 0;
        }
        if (isset($post['top_page_links']) && $post['top_page_links']) {
            $settings['top_page_links'] = 1;
        }
        else {
            $settings['top_page_links'] = 0;
        }
        if (isset($post['selective_expunge']) && $post['selective_expunge']) {
            $settings['selective_expunge'] = 1;
        }
        else {
            $settings['selective_expunge'] = 0;
        }
        if (isset($post['always_expunge']) && $post['always_expunge']) {
            $settings['always_expunge'] = 1;
        }
        else {
            $settings['always_expunge'] = 0;
        }
        if (isset($post['hide_deleted_messages']) && $post['hide_deleted_messages']) {
            $settings['hide_deleted_messages'] = 1;
        }
        else {
            $settings['hide_deleted_messages'] = 0;
        }
        if (isset($post['mailbox_freeze']) && $post['mailbox_freeze']) {
            $settings['mailbox_freeze'] = 1;
        }
        else {
            $settings['mailbox_freeze'] = 0;
            if (isset($_SESSION['frozen_folders'])) {
                unset($_SESSION['frozen_folders']);
            }
        }
        if (isset($post['hide_folder_on_empty']) && $post['hide_folder_on_empty']) {
            $settings['hide_folder_on_empty'] = 1;
        }
        else {
            $settings['hide_folder_on_empty'] = 0;
        }
        if (isset($post['smtp_auth']) && in_array($post['smtp_auth'], $smtp_auth_mechs)) {
            $settings['smtp_auth'] = $post['smtp_auth'];
            if ($settings['smtp_auth'] != 'none') {
                if (isset($post['smtp_user']) && trim($post['smtp_user']) && isset($post['smtp_pass'])
                    && trim($post['smtp_pass'])) {
                    $settings['smtp_user'] = $post['smtp_user'];
                    $settings['smtp_pass'] = $post['smtp_pass'];
                }
                else {
                    $this->errors[] = $user->str[374];
                    $settings['smtp_auth'] = 'none';
                }
            }
        }
        $settings['font_family'] = 'monospace';
        if (in_array($post['font_family'], $font_families)) {
            $settings['font_family'] = $post['font_family'];
        }
        if (isset($conf['site_themes'][$post['theme']])) {
            $settings['theme'] = $post['theme'];
        }
        else {
            $settings['theme'] = 'default';
        }
        $settings['subscribed_only'] = 0;
        if (isset($post['subscribed_only'])) {
            $settings['subscribed_only'] = true;
        }
        $settings['remote_image'] = 0;
        if (isset($post['remote_image'])) {
            $settings['remote_image'] = true;
        }
        $settings['default_message_action'] = '';
        if (isset($prev_next_actions[$post['default_message_action']])) {
            $settings['default_message_action'] = $post['default_message_action'];
        }
        $settings['short_message_parts'] = 0;
        if (isset($post['short_message_parts'])) {
            $settings['short_message_parts'] = true;
        }
        $settings['html_first'] = 0;
        if (isset($post['html_first'])) {
            $settings['html_first'] = true;
        }
        $settings['hl_reply'] = 0;
        if (isset($post['hl_reply'])) {
            $settings ['hl_reply'] = true;
        }
        $settings['text_email'] = 0;
        if (isset($post['text_email'])) {
            $settings ['text_email'] = true;
        }
        $settings['text_links'] = 0;
        if (isset($post['text_links'])) {
            $settings ['text_links'] = true;
        }
        $mailbox_per_page_count = (int) $post['mailbox_per_page_count'];
        if ($mailbox_per_page_count > $max_msg_per_page) {
            if (strstr($user->str[375], '%s')) {
                $this->errors[] = sprintf($user->str[375], $max_msg_per_page);
            }
            else {
                $this->errors[] = $user->str[375];
            }
            $mailbox_per_page_count = $max_msg_per_page;
        }
        $settings['mailbox_per_page_count'] = $mailbox_per_page_count;
        $settings['folder_style'] = $post['folder_style'];
        if (isset($post['timezone'])) {
            $settings['timezone'] = $post['timezone'];
        }
        else {
            $settings['timezone'] = false;
        }
        if (isset($post['trim_subject_fld']) && intval($post['trim_subject_fld'])) {
            $settings['trim_subject_fld'] = intval($post['trim_subject_fld']);
        }
        else {
            $settings['trim_subject_fld'] = 0;
        }
        if (isset($post['trim_from_fld']) && intval($post['trim_from_fld'])) {
            $settings['trim_from_fld'] = intval($post['trim_from_fld']);
        }
        else {
            $settings['trim_from_fld'] = 0;
        }
        $font_size = (int) $post['font_size'];
        if ($font_size >= 50 && $font_size <= 200) {
            $settings['font_size'] = $font_size;
        }
        else {
            $settings['font_size'] = 100;
        }
        $settings['image_thumbs'] = 0;
        if (isset($post['image_thumbs']) && $post['image_thumbs']) {
            $settings['image_thumbs'] = 1;
        }
        if (isset($post['display_mode']) && $post['display_mode'] == 2) {
            $settings['display_mode'] = 2;
            $hm_tags['complex'] = true;
            $hm_tags['simple'] = false;
        }
        else {
            $settings['display_mode'] = 1;
            $hm_tags['complex'] = false;
            $hm_tags['simple'] = true;
        }
        $settings['folder_detail'] = $post['folder_detail'];
        if (isset($post['show_folder_list']) && $post['show_folder_list']) {
            $settings['show_folder_list'] = 1;
        }
        else {
            if (isset($_SESSION['hide_folder_list'])) {
                unset($_SESSION['hide_folder_list']);
            }
            $settings['show_folder_list'] = 0;
        }
        if (isset($post['disable_folder_icons']) && $post['disable_folder_icons']) {
            $settings['disable_folder_icons'] = 1;
        }
        else {
            $settings['disable_folder_icons'] = 0;
        }
        if (isset($post['auto_switch_simple_mode']) && $post['auto_switch_simple_mode']) {
            $settings['auto_switch_simple_mode'] = 1;
        }
        else {
            $settings['auto_switch_simple_mode'] = 0;
        }
        if (isset($post['expunge_on_exit']) && $post['expunge_on_exit']) {
            $settings['expunge_on_exit'] = 1;
        }
        else {
            $settings['expunge_on_exit'] = 0;
        }
        if (isset($post['time_format'])) {
            if (isset($time_formats[$post['time_format']])) {
                $settings['time_format'] = $post['time_format'];
            }
            elseif($post['time_format'] = 'none') {
                $settings['time_format'] = '';
            }
        }
        else {
            $settings['time_format'] = 'h:i:s: A';
        }
        if (isset($post['date_format']) && isset($date_formats[$post['date_format']])) {
            $settings['date_format'] = $post['date_format'];
            if ($post['date_format'] == 'r') {
                $settings['time_format'] = false;
            }
        }
        else {
            $settings['date_format'] = 'm/d/y';
        }
        if (isset($post['mailbox_date_format_2']) && isset($time_formats[$post['mailbox_date_format_2']])) {
            $settings['mailbox_date_format_2'] = $post['mailbox_date_format_2'];
        }
        else {
            $settings['mailbox_date_format_2'] = false;
        }
        if (isset($post['mailbox_date_format']) && (isset($date_formats[$post['mailbox_date_format']]) || $post['mailbox_date_format'] == 'h')) {
            $settings['mailbox_date_format'] = $post['mailbox_date_format'];
        }
        else {
            $settings['mailbox_date_format'] = 'h';
        }
        if ($settings['mailbox_date_format'] == 'h' || $settings['mailbox_date_format'] == 'r') {
            $settings['mailbox_date_format_2'] = false;
        }
        if (isset($post['sent_folder']) && $post['sent_folder']) {
            $settings['sent_folder'] = $post['sent_folder'];
            if (!isset($_SESSION['user_settings']['sent_folder']) || (isset($_SESSION['user_settings']['sent_folder']) && $_SESSION['user_settings']['sent_folder'] != $settings['sent_folder'])) {
                $folder_refresh = true;
            }
        }
        elseif (isset($_SESSION['user_settings']['sent_folder'])) {
            $folder_refresh = true;
        }
        if (isset($post['draft_folder']) && $post['draft_folder']) {
            $settings['draft_folder'] = $post['draft_folder'];
            if (!isset($_SESSION['user_settings']['draft_folder']) || (isset($_SESSION['user_settings']['draft_folder']) && $_SESSION['user_settings']['draft_folder'] != $settings['draft_folder'])) {
                $folder_refresh = true;
            }
        }
        elseif (isset($_SESSION['user_settings']['draft_folder'])) {
            $folder_refresh = true;
        }
        if (isset($post['trash_folder']) && $post['trash_folder']) {
            $settings['trash_folder'] = $post['trash_folder'];
            if (!isset($_SESSION['user_settings']['trash_folder']) || (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder'] != $settings['trash_folder'])) {
                $folder_refresh = true;
            }
        }
        elseif (isset($_SESSION['user_settings']['trash_folder'])) {
            $folder_refresh = true;
        }
        $settings['dropdown_ajax'] = 0;
        $settings['folder_list_ajax'] = 0;
        if (isset($post['ajax_update_interval'])) {
            $int = (int) $post['ajax_update_interval'];
            if ($int >= 30 && $int <= 1200) {
                $settings['dropdown_ajax'] = 1;
                $settings['ajax_update_interval'] = $int;
                if ($settings['show_folder_list']) {
                    $settings['folder_list_ajax'] = 1;
                }
            }
            else {
                $settings['ajax_update_interval'] = 0;
            }
        }
        $settings['new_page_refresh'] = 0;
        if (isset($post['new_page_refresh'])) {
            $int = (int) $post['new_page_refresh'];
            if ($int >=30 && $int <= 1200) {
                $settings['new_page_refresh'] = $int;
            }
        }
        $settings['compose_autosave'] = 0;
        if (isset($post['compose_autosave'])) {
            $int = (int) $post['compose_autosave'];
            if ($int >= 60 && $int <= 1200) {
                $settings['compose_autosave'] = $int;
            }
        }
        if (isset($_SESSION['user_settings']['folder_check'])) {
            $settings['folder_check'] = $_SESSION['user_settings']['folder_check'];
        }
        if (isset($_SESSION['user_settings']['profiles'])) {
            $settings['profiles'] = $_SESSION['user_settings']['profiles'];
        }
        else {
            $settings['profiles'] = array(array());
        }
        foreach ($_SESSION['user_settings'] as $i => $v) {
            if (!isset($form[$i]) && !isset($settings[$i])) {
                $settings[$i] = $v;
            }
        }
        $_SESSION['user_settings'] = $settings;
        do_work_hook('update_settings');
        if (isset($_SESSION['plugin_settings'])) {
            foreach ($_SESSION['plugin_settings'] as $i => $v) {
                $_SESSION['user_settings'][$i] = $v;
            }
            unset($_SESSION['plugin_settings']);
        }
        if ($folder_refresh) {
            $imap->get_folders(true);
        }
        $this->write_settings();
        foreach ($_SESSION['folders'] as $vals) {
            if (isset($_SESSION['header_cache'][$vals['name']])) {
                $_SESSION['header_cache_refresh'][$vals['name']] = 1;
            }
            if (isset($_SESSION['uid_cache'][$vals['name']])) {
                $_SESSION['uid_cache_refresh'][$vals['name']] = 1;
            }
        }
        $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
        $user->page_data['settings'] = $_SESSION['user_settings'];
        $user->set_timezone();
        $this->form_redirect = true;
    }
}
} ?>
