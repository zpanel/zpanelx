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
class fw_post_action_compose extends fw_user_action_with_post {
function set_post_page_vars() {
    global $user;
    $forms = array(
    'hide_contacts' => array(
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
    ),
    'contact_search' => array(
        'contact_source' => array('string', 0, 'Contact Source'),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, $user->str[14]),
    ),
    'contact_browse' => array(
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'prev_contact_page' => array(
        'contacts' => array('array', 0, $user->str[8]),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'next_contact_page' => array(
        'contacts' => array('array', 0, $user->str[8]),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'add_to' => array(
        'contacts' => array('array', 0, $user->str[8]),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'add_cc' => array(
        'contacts' => array('array', 0, $user->str[8]),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'add_bcc' => array(
        'contacts' => array('array', 0, $user->str[8]),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'compose_save' => array(
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'compose_sign' => array(
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'attach_vcard' => array(
        'contacts' => array('array', 0, $user->str[8]),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'change_contact_source' => array(
        'contact_source' => array('string', 1, 'Contact Source'),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'compose_send' => array(
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'delete_attachment' => array(
        'attachment_id' => array('array', 0, 'Attachment ID'),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    'compose_attach' => array(
        'attachment_mime' => array('int', 0, 'Type'),
        'compose_to' => array('string', 0, 'To'),
        'contacts_visible' => array('int', 0, 'Contacts visible'),
        'compose_from' => array('int', 1, 'From'),
        'compose_cc' => array('string', 0, 'Cc'),
        'compose_bcc' => array('string', 0, 'Bcc'),
        'compose_subject' => array('string', 0, $user->str[13]),
        'compose_message' => array('string', 0, $user->str[14]),
        'search_contacts' => array('string', 0, 'Search keywords'),
        'compose_in_reply_to' => array('string', 0, 'In Rely To'),
        'compose_references' => array('string', 0, 'References'),
        'reply_box' => array('string', 0, 'Reply message folder'),
        'reply_uid' => array('int', 0, 'Reply message uid'),
        'compose_mdn' => array('int', 0, 'MDN'),
        'compose_skip_sent' => array('int', 0, 'Skip Sent'),
        'compose_priority' => array('int', 0, 'Priority'),
        'compose_session' => array('int', 1, 'Session'),
        'message_id' => array('string', 0, 'Message ID'),
    ),
    ); return $forms;
}
function form_action_change_contact_source($form, $post) {
    global $user;
    if ($user->logged_in) {
        $this->errors[] = $user->str[338]; 
        $user->page_data['contact_browse'] = true;
        $this->form_vals = $post;
        $user->page_data['active_contact_source'] = $post['contact_source'];
    }
}
function form_action_next_contact_page($form, $post) {
    global $user;
    if ($user->logged_in) {
        $this->errors[] = $user->str[339]; 
        $user->page_data['contact_browse'] = true;
        $this->form_vals = $post;
        if (isset($_SESSION['contact_list_page'])) {
            $user->page_data['contact_list_page'] = $_SESSION['contact_list_page'] + 1;
        }
    }
}
function form_action_prev_contact_page($form, $post) {
    global $user;
    if ($user->logged_in) {
        $this->errors[] =  $user->str[339];
        $user->page_data['contact_browse'] = true;
        $this->form_vals = $post;
        if (isset($_SESSION['contact_list_page'])) {
            $user->page_data['contact_list_page'] = $_SESSION['contact_list_page'] - 1;
        }
    }
}
function form_action_compose_save($form, $post) {
    global $user;
    global $hastymail_version;
    global $imap;
    global $conf;
    global $include_path;
    global $fd;
    global $message;
    $path = $conf['attachments_path'];
    $status = false;
    if ($user->logged_in) {
        $this->form_vals = $post;
        $select_res = false;
        if (isset($_SESSION['user_settings']['draft_folder'])) {
            $mailbox = $_SESSION['user_settings']['draft_folder'];
            $select_res = $imap->select_mailbox($mailbox, false, false, true);
        }
        else {
            $mailbox = 'INBOX';
            $this->errors[] = $user->str[342];
        }
        require_once($include_path.'lib'.$fd.'smtp_class.php');
        $message = hm_new('mime', $post['compose_session']);
        if (isset($post['compose_to'])) {
            $message->to = $post['compose_to'];
        }
        if (isset($post['compose_cc'])) {
            $message->cc = $post['compose_cc'];
        }
        if (isset($post['compose_references'])) {
            $message->references = $post['compose_references'];
        }
        if (isset($post['compose_in_reply_to'])) {
            $message->in_reply_to = $post['compose_in_reply_to'];
        }
        if (isset($post['message_id'])) {
            $message->message_id = $post['message_id'];
        }
        $existing_id = false;
        if ($select_res) {
            $search_res = $imap->simple_search('header message-id', false, $message->message_id);
            if (isset($search_res[0])) {
                $existing_id = $search_res[0];
            }
        }
        if (isset($post['compose_from'])) {
            if (isset($_SESSION['user_settings']['profiles'][$post['compose_from']])) {
                $from_atts = $_SESSION['user_settings']['profiles'][$post['compose_from']];
                $message->from = '"'.$from_atts['profile_name'].'" <'.$from_atts['profile_address'].'> ';
                $message->from_address = $from_atts['profile_address'];
                if (isset($from_atts['profile_reply_to']) && $from_atts['profile_reply_to']) {
                    $message->reply_to = '<'.$from_atts['profile_address'].'>';
                }
            }
        }
        if ($message->from_address) {
            $message->subject = $post['compose_subject'];
            $message->body = $post['compose_message'];
            if (!isset($_SESSION['user_settings']['compose_hide_mailer']) ||
                !$_SESSION['user_settings']['compose_hide_mailer']) {
                $message->set_header('x_Mailer', $hastymail_version);
            }
            $priortiy = 0;
            if (isset($post['compose_priority']) && $post['compose_priority']) {
                $priortiy = (int) $post['compose_priority'];
            }
            if ($priortiy && $priortiy != 3) {
                $message->set_header('x_Priority', $priortiy);
            }
            if (isset($post['compose_mdn']) && $post['compose_mdn']) {
                $message->set_header('disposition_Notification_To', $message->from_address);
            }
            do_work_hook('message_save', array($message->body));
            $status = stream_imap_append($message, $post['compose_session'], $mailbox);
            if ($status && $existing_id) {
                $this->perform_imap_action('DELETE', $mailbox, array($existing_id), false, false);
                $this->perform_imap_action('EXPUNGE', $mailbox, array($existing_id), false, false);
                $this->errors = array();
            }
            $this->errors[] = $user->str[343];
        }
    }
}
function form_action_attach_vcard($form, $post) {
    global $user;
    global $conf;
    global $include_path;
    global $fd;
    $path = $conf['attachments_path'];
    if ($user->logged_in) {
        if (isset($post['contacts']) && is_array($post['contacts']) && !empty($post['contacts'])) {
            require_once($include_path.'lib'.$fd.'vcard.php');
            $vcard = hm_new('vcard');
            $vcard->get_card_list();
            foreach ($post['contacts'] as $string) {
                $found_id = false;
                foreach ($vcard as $vals) {
                    if (!is_array($vals)) {
                        continue;
                    }
                    foreach ($vals as $card_id => $atts) {
                        $name = '';
                        $email = '';
                        if (is_array($atts)) {
                            foreach ($atts as $props) {
                                if ($props['name'] == 'EMAIL') {
                                    $email = '<'.trim($props['value']).'>';
                                }
                                if ($props['name'] == 'FN') {
                                    $name = '"'.trim($props['value']).'"';
                                }
                            }
                        }
                        if (trim(stripslashes($string)) == $name.' '.$email || (!$name && stripslashes($string) == $email)) {
                            $found_id = $card_id;
                            break 2;
                        }
                    }
                }
            }
                if ($found_id) {
                    list($filename, $body) = $vcard->export_card($found_id);
                    $output_id = md5(uniqid(rand(),1));
                    if (substr($path, -1) != $fd) {
                        $output_name = $path.$fd.$output_id;
                    }
                    else {
                        $output_name = $path.$output_id;
                    }
                    $output_file = fopen($output_name, 'w+');
                    if (is_resource($output_file)) {
                        $file_content = chunk_split(base64_encode($body));
                        $size = strlen($file_content);
                        fwrite($output_file, $file_content);
                        $attributes = array('time' => time(), 'realname' => $filename, 'filename' => $output_id, 'size' => $size, 'type' => 'text/x-vcard');
                        $_SESSION['attachments'][$post['compose_session']][$output_id] = $attributes;
                    }
                }
            $this->errors[] = $user->str[344];
            $this->form_vals = $post;
        }
        else {
            $this->errors[] = $user->str[345];
            $this->form_vals = $post;
        }
    }
}
function form_action_compose_sign($form, $post) {
    global $user;
    if ($user->logged_in) {
        if (isset($post['compose_from'])) {
            $from_id = (int) $post['compose_from'];
        }
        if (isset($_SESSION['user_settings']['profiles'][$from_id]['profile_sig'])) {
            $post['compose_message'] .= "\r\n\r\n".$_SESSION['user_settings']['profiles'][$from_id]['profile_sig'];
        }
        $this->errors[] = $user->str[346];
        $this->form_vals = $post;
    }
}
function add_address($name, $post) {
    global $user;
    if ($user->logged_in) {
        $fld = '';
        if (isset($post['compose_'.$name])) {
            $fld = $post['compose_'.$name];
        }
        if (trim($fld)) {
            $fld .= ', ';
        }
        if (isset($post['contacts']) && is_array($post['contacts']) && !empty($post['contacts'])) {
            foreach ($post['contacts'] as $val) {
                if ($this->gpc) {
                    $fld .= stripslashes($val).', ';
                }
                else {
                    $fld .= $val.', ';
                }
            }
        }
        $fld = rtrim($fld, ', ');
        if ($fld) {
            $post['compose_'.$name] = $fld;
            $this->form_vals = $post;
            $this->errors[] = $user->str[348];
            $user->page_data['contact_browse'] = true;
        }
        else {
            $user->page_data['contact_browse'] = true;
        }
    }
}
function form_action_add_cc($form, $post) {
    $this->add_address('cc', $post);
}
function form_action_add_bcc($form, $post) {
    $this->add_address('bcc', $post);
}
function form_action_add_to($form, $post) {
    $this->add_address('to', $post);
}
function form_action_contact_search($form, $post) {
    global $user;
    global $include_path;
    global $conf;
    global $fd;
    if ($user->logged_in) {
        if (trim($post['search_contacts'])) {
            $search = trim($post['search_contacts']);
        }
        else {
            $search = false;
        }
        if (isset($post['contact_source'])) {
            $source = $post['contact_source'];
        }
        else {
            $source = 0;
        }
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        list ($abook, $total) = $vcard->get_quick_list('sort_name', 0, $source, $search);
        $user->page_data['contact_list'] = $abook;
        $user->page_data['contact_browse'] = true;
        $this->form_vals = $post;
        if (!empty($abook)) {
            $this->errors[] = $user->str[351].': '.count($abook);
        }
        else {
            $user->page_data['contact_list'] = $abook;
            $this->errors[] = $user->str[352];
            $user->page_data['contact_list_page'] = 1;
            $this->form_vals = $post;
        }
    }
}
function form_action_hide_contacts($form, $post) {
    global $user;
    if ($user->logged_in) {
        $user->page_data['contact_browse'] = false;
        $this->form_vals = $post;
        $this->errors[] = $user->str[353];
        $this->form_redirect = true;
    }
}
function form_action_contact_browse($form, $post) {
    global $user;
    if ($user->logged_in) {
        $user->page_data['contact_browse'] = true;
        $this->form_vals = $post;
        $this->errors[] = $user->str[354];
    }
}
function form_action_compose_send($form, $post) {
    global $user;
    global $imap;
    global $conf;
    global $message;
    global $hastymail_version;
    global $smtp;
    global $include_path;
    global $fd;
    $path = $conf['attachments_path'];
    if ($user->logged_in) {
        if (isset($post['contacts_visible']) && $post['contacts_visible']) {
            $user->page_data['contact_browse'] = true;
        }
        require_once($include_path.'lib'.$fd.'smtp_class.php');
        $message = hm_new('mime', $post['compose_session']);
                if (!isset($_SESSION['user_settings']['compose_hide_mailer']) ||
                    !$_SESSION['user_settings']['compose_hide_mailer']) {
                    $message->set_header('x_Mailer', $hastymail_version);
                }
        if (isset($post['compose_to'])) {
            $message->to = $post['compose_to'];
        }
        if (isset($post['compose_cc'])) {
            $message->cc = $post['compose_cc'];
        }
        if (isset($post['compose_bcc'])) {
            $message->bcc = $post['compose_bcc'];
        }
        if (isset($post['compose_references'])) {
            $message->references = $post['compose_references'];
        }
        if (isset($post['compose_in_reply_to'])) {
            $message->in_reply_to = $post['compose_in_reply_to'];
        }
        if (isset($post['message_id'])) {
            $message->message_id = $post['message_id'];
        }
        if (isset($post['compose_from'])) {
            if (isset($_SESSION['user_settings']['profiles'][$post['compose_from']])) {
                $from_atts = $_SESSION['user_settings']['profiles'][$post['compose_from']];
                $message->from = '"'.$from_atts['profile_name'].'" <'.$from_atts['profile_address'].'> ';
                $message->from_address = $from_atts['profile_address'];
                if (isset($from_atts['profile_reply_to']) && $from_atts['profile_reply_to']) {
                    $message->reply_to = '<'.$from_atts['profile_address'].'>';
                }
            }
        }
        if ($message->from_address) {
            $recipients = $message->get_recipient_addresses();
            if (!empty($recipients)) {
                $message->subject = $post['compose_subject'];
                $message->body = $post['compose_message'];
                $priortiy = 0;
                if (isset($post['compose_priority']) && $post['compose_priority']) {
                    $priortiy = (int) $post['compose_priority'];
                }
                if ($priortiy && $priortiy != 3) {
                    $message->set_header('x_Priority', $priortiy);
                }
                if (isset($post['compose_mdn']) && $post['compose_mdn']) {
                    $message->set_header('disposition_Notification_To', $message->from_address);
                }
                $smtp_auth = false;
                $smtp_user = false;
                $smtp_pass = false;
                if (isset($conf['smtp_authentication_type'])) {
                    switch (strtolower($conf['smtp_authentication_type'])) {
                        case 'plain':
                        case 'login':
                        case 'cram-md5':
                            $pass_bits = $user->string_decrypt($_SESSION['user_data']['pass']);
                            if (is_array($pass_bits) && isset($pass_bits[1])) {
                                $smtp_pass = $pass_bits[1];
                                $smtp_auth = $conf['smtp_authentication_type'];
                                $smtp_user = $_SESSION['user_data']['username'];
                            }
                            break;
                        case 'user':
                            if (isset($_SESSION['user_settings']['smtp_auth']) && $_SESSION['user_settings']['smtp_auth'] != 'none') {
                                $smtp_auth = $_SESSION['user_settings']['smtp_auth'];
                                $smtp_pass = $_SESSION['user_settings']['smtp_pass'];
                                $smtp_user = $_SESSION['user_settings']['smtp_user'];
                            }
                            break;
                    }
                }
                $smtp = hm_new('smtp');
                if ($smtp_auth) {
                    $smtp->auth = $smtp_auth;
                    $smtp->password = $smtp_pass;
                    $smtp->username = $smtp_user;
                }
                $res = $smtp->connect();
                if (!$res) {
                    do_work_hook('message_send', array($message->body));
                    $res = $smtp->send_message($message->from_address, $recipients, $message);
                    if ($res) {
                        if ($smtp->smtp_err) {
                            $res .= '<br />'.$smtp->smtp_err;
                        }
                        $this->form_vals = $post;
                        $this->errors[] = $res;
                    }
                    else {
                        $user->page_data['sent'] = 1;
                        $this->errors[] = $user->str[355];
                        $this->form_redirect = true;
                        if (isset($post['compose_in_reply_to']) && $post['compose_in_reply_to']) {
                            $this->perform_imap_action('ANSWERED', $post['reply_box'], array($post['reply_uid']), false, false);
                        }
                    }
                    if (!$res && !isset($post['compose_skip_sent']) || (isset($post['compose_skip_sent']) && !$post['compose_skip_sent'])) {
                        $sent_folder = $_SESSION['user_settings']['sent_folder'];
                        if (isset($_SESSION['sent_folder_override']) && isset($_SESSION['folders'][$_SESSION['sent_folder_override']])) {
                            $sent_folder = $_SESSION['sent_folder_override'];
                            unset($_SESSION['sent_folder_override']);
                        }
                        if (isset($sent_folder) && isset($_SESSION['folders'][$sent_folder])) {
                            $email = $message->output_imap_message();
                            $size = $message->get_imap_message_size(strlen($email));
                            if ($imap->append_start($sent_folder, $size)) {
                                $imap->append_feed($email);
                                if (isset($_SESSION['attachments'][$post['compose_session']]) && !empty($_SESSION['attachments'][$post['compose_session']])) {
                                    foreach ($_SESSION['attachments'][$post['compose_session']] as $i => $v) {
                                        $headers = $message->build_part_header($v['realname'], $v['type'], $v['encoding']);
                                        if (substr($path, -1) != $fd) {
                                            $filename = $path.$fd.$i;
                                        }
                                        else {
                                            $filename = $path.$i;
                                        }
                                        if (is_readable($filename)) {
                                            $imap->append_feed($headers);
                                            $input_file = fopen($filename, 'r');
                                            if (is_resource($input_file)) {
                                                while (!feof($input_file)) {
                                                    $string = fgets($input_file, 1024);
                                                    if ($string) {
                                                        $imap->append_feed(rtrim($string, "\r\n"));
                                                    }
                                                }
                                                fclose($input_file);
                                            }
                                        }
                                    }
                                    $imap->append_feed('--'.$message->boundry.'--');
                                }
                                $status = $imap->append_end();
                            }
                        }
                    }
                    if (!$res && isset($_SESSION['user_settings']['draft_folder']) && isset($post['message_id']) && $post['message_id'] &&
                        isset($_SESSION['user_settings']['delete_draft']) && $_SESSION['user_settings']['delete_draft'] &&
                        $_SESSION['user_settings']['draft_folder']) {
                        $trash_folder = false;
                        if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
                            $trash_folder = $_SESSION['user_settings']['trash_folder'];
                        }
                        $select_res = $imap->select_mailbox($_SESSION['user_settings']['draft_folder'], false, false, true);
                        if ($select_res) {
                            $search_res = $imap->simple_search('header message-id', false, $post['message_id']);
                            if (isset($search_res[0])) {
                                $this->perform_imap_action('DELETE', $_SESSION['user_settings']['draft_folder'], array($search_res[0]), $trash_folder, false);
                            }
                        }
                    }
                    do_work_hook('compose_after_send');
                }
                else {
                    $this->errors[] = $res;
                    $this->form_vals = $post;
                }
                if (!$res) {
                    unset_attachments($post['compose_session']);
                }
                $smtp->disconnect();
            }
            else {
                $this->form_vals = $post;
                $this->errors[] = $user->str[356];
            }
        }
        else {
            $this->form_vals = $post;
            $this->errors[] = $user->str[357];
        }
    }
}
function form_action_delete_attachment($form, $post) {
    global $user;
    global $conf;
    global $fd;
    $path = $conf['attachments_path'];
    if ($user->logged_in) {
        $cnt = 0;
        if (isset($post['attachment_id']) && is_array($post['attachment_id']) && !empty($post['attachment_id'])) {
            foreach ($post['attachment_id'] as $v) {
                $id = stripslashes($v);
                if (isset($_SESSION['attachments'][$post['compose_session']][$id])) {
                    unset($_SESSION['attachments'][$post['compose_session']][$id]);
                    $cnt++;
                    if (substr($path, -1) != $fd) {   
                        $filename = $path.$fd.$id;
                    }   
                    else {      
                        $filename = $path.$id;
                    }   
                    if (is_readable($filename)) {
                        unlink($filename);
                    }
                }
            }
        }
        $this->errors[] = $user->str[349].': '.$cnt;
        $this->form_vals = $post;
    }
}
function form_action_compose_attach($form, $post) {
    global $user;
    if ($user->logged_in) {
        $type = false;
        if (isset($post['attachment_mime']) && $post['attachment_mime']) {
            $mime = (int) $post['attachment_mime'];
            if ($mime == 1) {
                $type = 'text/plain';
            }
            elseif ($mime == 2) {
                $type = 'application/octet-stream';
            }
        }
        if (isset($post['compose_session'])) {
            $c_session = $post['compose_session'];
        }
        else {
            $c_session = 1;
        }
        $res = $this->add_attachment('attachment_file', $type, $c_session);
        if ($res) {
            $this->errors[] = $res;
        }
        else {
            $this->errors[] = $user->str[350];
        }
        $this->form_vals = $post;
    }
}
}?>
