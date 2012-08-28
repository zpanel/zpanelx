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
function url_action_compose($get) {
    global $user;
    global $imap;
    global $conf;
    global $include_path;
    global $fd;
    if ($user->logged_in) {
        $compose_session = false;
        if (isset($get['compose_session']) && $get['compose_session'] == 'new') {
            if (isset($_SESSION['compose_sessions'])) {
                $compose_session = count($_SESSION['compose_sessions']) + 1;
                $_SESSION['compose_sessions'][$compose_session] = time();
            }
            else {
                $compose_session = 1;
                $_SESSION['compose_sessions'] = array(1 => time());
            }
        }
        elseif (isset($get['compose_session']) && intval($get['compose_session'])) {
            $compose_session = $get['compose_session'];
            $_SESSION['compose_sessions'][$compose_session] = time();
        }
        else {
            if (isset($_SESSION['compose_sessions'])) {
                $compose_session = count($_SESSION['compose_sessions']) + 1;
                $_SESSION['compose_sessions'][$compose_session] = time();
            }
            else {
                $compose_session = 1;
                $_SESSION['compose_sessions'] = array(1 => time());
            }
        }
        if (isset($get['thumbnail']) && isset($get['attachment_id'])) {      
            $path = $conf['attachments_path'];      
            if (isset($_SESSION['attachments'][$compose_session][$get['attachment_id']])) {   
                $vals = $_SESSION['attachments'][$compose_session][$get['attachment_id']];    
                if (substr($path, -1) != $fd) {   
                    $filename = $path.$fd.$get['attachment_id'];      
                }   
                else {      
                    $filename = $path.$get['attachment_id'];    
                }   
                if (is_readable($filename)) {   
                    ob_end_clean();     
                    $lines = file($filename);   
                    $data = '';     
                    foreach ($lines as $line) {     
                        $line = trim($line);    
                        $data .= base64_decode($line);      
                    }   
                    if ($data) {    
                        $im = imagecreatefromstring($data);     
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
                }   
            }   
            $imap->disconnect();    
            $user->clean_up();      
            exit;   
        }
        do_work_hook('compose_page_start');
        if (!isset($user->page_data['contact_list'])) {
            require_once($include_path.'lib'.$fd.'vcard.php');
            $vcard = hm_new('vcard');
            $page = 1;
            if (isset($user->page_data['contact_list_page'])) {
                $page = $user->page_data['contact_list_page'];
            }
            if (isset($user->page_data['active_contact_source'])) {
                $source = $user->page_data['active_contact_source'];
            }
            else {
                $source = 'local';
            }
            list($user->page_data['contact_list'], $user->page_data['contact_list_total']) = $vcard->get_quick_list('sort_name', $page, $source);
        }
        $profile_id = 0;
        foreach ($_SESSION['user_settings']['profiles'] as $i => $vals) {
            if (isset($vals['default']) && $vals['default']) {
                $profile_id = $i;
                break;
            }
        }
        $from = '';
        $to = '';
        $cc = '';
        $bcc = '';
        $subject = '';
        $message = '';
        $mailbox = false;
        $ctype = 'text';
        $type = '';
        $uid = 0;
        $message_id = '<'.md5(uniqid(rand(),1)).'@'.$conf['host_name'].'>';
        $part = 0;
        $pre = '';
        $in_reply_to = '';
        $refs = '';
        $charset = false;
        if (isset($get['mailbox'])) {
            if (isset($_SESSION['folders'][$get['mailbox']])) {
                $mailbox = $get['mailbox'];
            }
        }
        if (!$user->redirected && isset($get['uid'])) {
            $uid = (int) $get['uid'];
            if ($uid && $mailbox) {
                if (isset($get['reply_part'])) {
                    if (preg_match("/^\d([0-9\.])*$/", $get['reply_part'])) {
                        $part = $get['reply_part'];
                    }
                }
                if (isset($get['reply_type'])) {
                    switch ($get['reply_type']) {
                        case 'forward':
                        case 'forward_attach':
                        case 'all':
                        case 'new':
                        case 'resume':
                        case 'list':
                        case 'reply':
                            $type = $get['reply_type'];
                            break;
                    }
                }
                $status = $imap->select_mailbox($mailbox, false);
                if ($status) {
                    $struct = $imap->get_message_structure($uid);
                    list($message_data, $viewable) = $this->find_message_part($struct, $part);
                    $user->page_data['message_uid'] = $uid;
                    $user->page_data['message_part'] = $part;
                    if (!empty($message_data)) {
                        $user->page_data['charset'] = $message_data['charset'];
                    }
                    $main_headers = $imap->get_message_headers($uid, 0);
                    $headers = array();
                    if ($part && $part != 1) { 
                        $headers = $imap->get_message_headers($uid, $part);
                    }
                    $all_headers = array();
                    foreach ($main_headers as $vals) {
                        $name = strtolower($vals[0]);
                        $value = $vals[1];
                        $all_headers[$name] = $value;
                    }    
                    foreach ($headers as $vals) {
                        $name = strtolower($vals[0]);
                        $value = $vals[1];
                        $all_headers[$name] = $value;
                    }
                    switch ($type) {
                        case 'forward_attach':
                            $filename = false;
                            foreach ($main_headers as $vals) {
                                if (strtolower($vals[0]) == 'subject') {
                                    $filename = substr(preg_replace("/\s+/", '_', trim(preg_replace("/[^a-zA-Z0-9 ]/", ' ', $vals[1]))), 0, 200).'.mime';
                                    break;
                                }
                            }
                            if (!$filename) {
                                $filename = 'message.mime';
                            }
                            add_forwarded_attachments(array(0 => array(
                                'encoding' => 'none', 'type' => 'message', 'subtype' => 'rfc822', 'filename' => $filename)), $uid, $compose_session);
                            break;
                        case 'forward':
                            $fparts = $this->prep_forwarded_attachments($part, $struct);
                            if (!empty($fparts)) {
                                add_forwarded_attachments($fparts, $uid, $compose_session);
                            }
                            if (isset($all_headers['subject'])) {
                                $subject = 'Fw: '.$all_headers['subject'];
                            }
                            if ($message_data['type'] == 'text') {
                                $data = $this->decode_msg_body($message_data, $imap->get_message_part($uid, $part));
                                list($ctype, $message) = $this->format_msg_body($message_data['subtype'], $data);
                            }
                            $pre = "\r\n\r\nOriginal Message\r\n".
                                   "----------------\r\n";
                            if (isset($all_headers['subject'])) {
                                $pre .= 'Subject: '.$all_headers['subject']."\r\n";
                            }
                            if (isset($all_headers['from'])) {
                                $pre .= 'From: '.$all_headers['from']."\r\n";
                            }
                            if (isset($all_headers['date'])) {
                                $pre .= 'Date: '.$all_headers['date']."\r\n";
                            }
                            $pre .= "\r\n";
                            if ($ctype == 'html') {
                                $pre = str_replace("\r\n", '<br />', $pre);
                            }
                            break;
                        case 'new':
                        case 'resume':
                            if (isset($message_data['type']) && $message_data['type'] == 'text') {
                                $data = $this->decode_msg_body($message_data, $imap->get_message_part($uid, $part));
                                list($ctype, $message) = $this->format_msg_body($message_data['subtype'], $data);
                            }
                            if ($type == 'resume' && isset($all_headers['message-id'])) {
                                $message_id = $all_headers['message-id'];
                            }
                            if (isset($all_headers['cc'])) {
                                $cc = $all_headers['cc'];
                            }
                            if (isset($all_headers['subject'])) {
                                $subject = $all_headers['subject'];
                            }
                            if (isset($all_headers['to'])) {
                                $to = $all_headers['to'];
                            }
                            $fparts = $this->prep_forwarded_attachments($part, $imap->get_message_structure($uid));
                            if (!empty($fparts)) {
                                add_forwarded_attachments($fparts, $uid, $compose_session);
                            }
                            break;
                        case 'all':
                        case 'list':
                        case 'reply':
                            if (isset($all_headers['message-id'])) {
                                $in_reply_to = $all_headers['message-id'];
                                if (isset($all_headers['references'])) {
                                    $refs = trim(trim($all_headers['references']), ',').', '.$in_reply_to;
                                }
                                else {
                                    $refs = $in_reply_to;
                                }
                            }
                            if (isset($all_headers['subject'])) {
                                $subject = 'Re: '.preg_replace("/^re:/i", '', $all_headers['subject']);
                            }
                            if (isset($all_headers['reply-to'])) {
                                $to = $all_headers['reply-to'];
                            }
                            elseif (isset($all_headers['from'])) {
                                $to = $all_headers['from'];
                            }
                            elseif (isset($all_headers['sender'])) {
                                $to = $all_headers['sender'];
                            }
                            $to_address = false;
                            if (isset($all_headers['to'])) {
                                $to_address = $all_headers['to'];
                            }
                            elseif (isset($all_headers['cc'])) {
                                $to_address = $all_headers['cc'];
                            }
                            elseif (isset($all_headers['delivered-to'])) {
                                $to_address = $all_headers['delivered-to'];
                            }
                            $to = parse_address_fld($to, $message_data['charset']);
                            if ($to) {
                                $to = $to[0]['raw'];
                            }
                            if ($type == 'all') {
                                $to_vals = array();
                                $cc_vals = array();
                                $existing = array();
                                $p_addresses = array();
                                foreach ($_SESSION['user_settings']['profiles'] as $i => $vals) {
                                    if (isset($vals['profile_address']) && trim($vals['profile_address'])) {
                                        $p_addresses[] = str_replace(array('<', '>'), '', $vals['profile_address']);
                                    }
                                }
                                foreach (array('to', 'cc', 'from') as $fld) {
                                    if (isset($all_headers[$fld])) {
                                        foreach (parse_address_fld($all_headers[$fld], $message_data['charset']) as $vals) {
                                            if (in_array($vals['address'], $existing)) {
                                                continue;
                                            }
                                            if (in_array($vals['address'], $p_addresses)) {
                                                continue;
                                            }
                                            $existing[] = $vals['address'];
                                            if ($fld == 'cc') {
                                                $cc_vals[] = $vals['raw'];
                                            }
                                            else {
                                                $to_vals[] = $vals['raw'];
                                            }
                                        }
                                    }
                                }
                                if (!empty($cc_vals)) {
                                    $cc = implode(', ', $cc_vals);
                                }
                                if (!empty($to_vals)) {
                                    $to = implode(', ', $to_vals);
                                }
                            }
                            elseif ($type == 'list') {
                                if (isset($all_headers['list-post'])) {
                                    $to = preg_replace("/^mailto:/i", '', rtrim(ltrim($all_headers['list-post'], '<'), '>'));
                                }
                                elseif (isset($all_headers['list-id'])) {
                                    $to = preg_replace("/\./", '@', rtrim(ltrim($all_headers['list-id'], '<'), '>'), 1);
                                }
                            }
                            $profile_id = 0;
                            foreach ($_SESSION['user_settings']['profiles'] as $i => $vals) {
                                if (isset($vals['profile_address']) && stristr($to_address, $vals['profile_address'])) {
                                    $profile_id = $i;
                                    break;
                                }
                            } 
                            if ($message_data['type'] == 'text') {
                                $data = $this->decode_msg_body($message_data, $imap->get_message_part($uid, $part));
                                list($ctype, $data) = $this->format_msg_body($message_data['subtype'], $data);
                                $data = $this->prep_reply($ctype, $data);
                                if (isset($all_headers['from'])) {
                                    if (isset($all_headers['date'])) {
                                        $pre = 'On '.$all_headers['date'].' ';
                                    }
                                    $pre .= $all_headers['from']." wrote\r\n\r\n";
                                }
                                $message = $data;
                            }
                            break;
                    }
                }
            }
        }
        else {
            if (isset($get['to'])) {
                $to = $get['to'];
            }
	        if (isset($get['url'])) {
	            $url = urldecode($get['url']);
	            $url_a = parse_url($url);
	            if($url_a['scheme'] == 'mailto') {
	                $to = $url_a['path'];
	                parse_str($url_a['query'], $query_a);
		            if(isset($query_a['subject'])) {
		                $subject = $query_a['subject'];
		            }
		            if(isset($query_a['body'])) {
		                $message = $query_a['body'];
		            }
                    if(isset($query_a['cc'])) {
                        $cc = $query_a['cc'];
                    }
                    if(isset($query_a['bcc'])) {
                        $bcc = $query_a['bcc'];
                    }
                }
 	        }
	    }
        $user->page_data['profiles'] = $_SESSION['user_settings']['profiles'];
        $default_from = false;
        foreach ($user->page_data['profiles'] as $vals) {
            if (isset($vals['profile_address']) && $vals['profile_address']) {
                $default_from = $vals['profile_address'];
                if (isset($vals['default']) && $vals['default']) {
                    break;
                }
            }
        }
        if (!$default_from) {
            $this->errors[] = $user->str[391];
        }
        $user->dsp_page = 'compose';
        $user->page_data['compose_link_class'] = 'current_page';
        $user->page_data['ctype'] = $ctype;
        $user->page_data['profile_id'] = $profile_id;
        $user->page_data['message_id'] = $message_id;
        $user->page_data['mailbox'] = $mailbox;
        $user->page_data['reply_type'] = $type;
        $user->page_data['message_part'] = $part;
        $user->page_data['message_uid'] = $uid;
        $user->page_data['message'] = str_replace('&', '&amp;', $message);
        $user->page_data['subject'] = str_replace('&', '&amp;', $subject);
        $user->page_data['refs'] = $refs;
        $user->page_data['in_reply_to'] = $in_reply_to;
        $user->page_data['to'] = $to;
        $user->page_data['from'] = $from;
        $user->page_data['message_pre'] = $pre;
        $user->page_data['cc'] = $cc;
        $user->page_data['bcc'] = $bcc;
        $user->page_title .= ' | '.$user->str[3].' |';
        $user->page_data['compose_session'] = $compose_session;
        if (isset($_SESSION['attachments'][$compose_session]) && !empty($_SESSION['attachments'][$compose_session])) {
            $user->page_data['attachments'] = $_SESSION['attachments'][$compose_session];
        }
        $user->page_data['folders'] = $_SESSION['folders'];
    }
}
function format_msg_body($type, $data) {
    global $allowed_tag_list;
    global $user;
    $new_type = 'text';
    if ($type != 'text') {
        if (isset($user->page_data['plugin_compose_content_type']) && is_array($user->page_data['plugin_compose_content_type'])) {
            if (in_array($type, $user->page_data['plugin_compose_content_type'])) {
                $new_type = $type;
            }
        }
    }
    if ($new_type == 'text' && $type == 'html') {
        $data = html_2_text($data);
    }
    if ($new_type == 'html') {
        $data = str_replace("\r\n", ' ', $data);
        $data = filter_html($data, $allowed_tag_list);
    }
    return array($new_type, $data);
}
function add_attachment($name, $type=false, $c_session) {
    global $conf;
    global $fd;
    $path = $conf['attachments_path'];
    $error = 'An unknown error occured';
    if (isset($conf['attachments_path']) && is_writable($conf['attachments_path'])) {
        if (isset($_FILES[$name]) && !empty($_FILES[$name])) {
            $ufiles = $_FILES[$name];
            if (!$ufiles['error']) {
                $src = $ufiles['tmp_name'];
                if (filesize($src) > 0) {
                    $input_file = fopen($src, 'r');
                    if (is_resource($input_file)) {
                        $output_id = md5(uniqid(rand(),1));
                        if (substr($path, -1) != $fd) {
                            $output_name = $path.$fd.$output_id;
                        }
                        else {
                            $output_name = $path.$output_id;
                        }
                        $output_file = fopen($output_name, 'w+');
                        if (is_resource($output_file)) {
                            $size = 0;
                            $left_over = '';
                            while (!feof($input_file)) {
                                $clear = fgets($input_file, 1024);
                                if ($left_over) {
                                    $clear = $left_over.$clear;
                                }
                                $data = base64_encode($clear);
                                while ($data) {
                                    if (strlen($data) > 76) {
                                        fwrite($output_file, substr($data, 0, 76)."\r\n");
                                        $size += 78;
                                        $left_over = '';
                                        $data = substr($data, 76);
                                    }
                                    elseif (strlen($data) < 76) {
                                        $left_over = base64_decode($data);
                                        $data = '';
                                    }
                                    else {
                                        $left_over = base64_decode($data);
                                        $data = '';
                                    }
                                }
                            }
                            if ($left_over) {
                                $size += strlen(base64_encode($left_over)) + 2;
                                fwrite($output_file, base64_encode($left_over)."\r\n");
                            }
                            $filename = $ufiles['name'];
                            if (!$type) {
                                $type = $ufiles['type'];
                            }
                            $attributes = array('time' => time(), 'realname' => $filename,
                                        'encoding' => 'base64', 'filename' => $output_id,
                                        'size' => $size, 'type' => $type,
                            );
                            $_SESSION['attachments'][$c_session][$output_id] = $attributes;
                            $error = false;
                        }
                        else {
                            $error = 'Could not open target attachment file';
                        }
                    }
                    else {
                        $error = 'Unable to open uloaded file';
                    }
                }
                else {
                    $error = 'No file found to attach';
                }
            }
            else {
                switch ($ufiles['error']) {
                    case 4:
                        $error = 'No file found to attach';
                        break;
                    default:
                        $error = 'An error occured uploading the file';
                        break;
                }
            }
        }
        else {
            $error = 'No file found to attach';
        }
    }
    else {
        $error = 'No usable attachment directory found';
    }
    return $error;
}
function prep_reply($type, $string) {
    if ($type == 'html') {
        return '<div style="padding-left: 10px; border-left: solid 2px #0000FF;">'.$string.'</div>';
    }
    $string = str_replace("\t", "    ", $string);
    $lines = preg_split("(\n|\r\n)", $string);
    $new_lines = array();
    $max = 77;
    $leftover = false;
    foreach ($lines as $line) {
        if ($leftover) {
            $new_line = $pre.$leftover.' '.preg_replace("/^$pre/", '', $line);
            $leftover = false;
            if (!trim($line)) {
                $new_line .= "\r\n>";
            }
            $line = $new_line;
        }
        if (hm_strlen($line) + 2 > $max) {
            if (preg_match("/^\s*>+\s*\>*\s*/", $line, $matches)) {
                $split = "\r\n> ".$matches[0];
                $pre = $matches[0];
            }
            else {
                $pre = false;
                $split = "\r\n> ";
            }
            $line_bits = explode(chr(0), (wordwrap($line, $max, chr(0), true)));
            if (hm_strlen($line_bits[count($line_bits) - 1]) < $max - 10) {
                $leftover = $line_bits[count($line_bits) - 1];
                array_pop($line_bits);
            }
            $new_lines[] = '> '.join($split, $line_bits);
        }
        else {
            $new_lines[] = '> '.$line;
        }
    }
    $string = implode("\r\n", $new_lines)."\r\n\r\n";
    return $string;
}
function decode_msg_body($atts, $data) {
    global $user;
    if (isset($atts['encoding']) && strtolower($atts['encoding']) == 'base64') {
        $data = base64_decode($data);
    }
    elseif (isset($atts['encoding']) && strtolower($atts['encoding'] == 'quoted-printable')) {
        $data = $user->user_action->quoted_decode($data);
    }
    return $data;
}
function prep_forwarded_attachments($part, $struct) {
    $fparts = array();
    foreach ($struct as $id => $vals) {
        if (is_array($vals) && $id != $part) {
            if (isset($vals['subs']) && is_array($vals['subs']) && !empty($vals['subs'])) {
                foreach ($vals['subs'] as $v => $atts) {    
                    if (is_array($atts) && $v != $part) {
                        if (isset($vals['subs']['subtype']) && $vals['subs']['subtype'] == 'alternative') {
                            continue;
                        }
                        $fparts[$v] = $atts;
                    }
                }
            }
            else {
                if (isset($struct['subtype']) && $struct['subtype'] == 'alternative') {
                    continue;
                }
                $fparts[$id] = $vals;
            }
        }
    }
    return $fparts;
}
}
class site_page_compose extends site_page {
function print_compose_form() {
    global $message_part_types;
    global $page_id;
    $compose_to = '';
    $compose_priority = 3;
    $compose_skip_sent = false;
    $compose_mdn = false;
    $compose_cc = '';
    $compose_references = '';
    $compose_in_reply_to = '';
    $message_id = '';
    $compose_bcc = '';
    $compose_subject = '';
    $compose_message = '';
    if (!isset($this->pd['charset'])) {
        $this->pd['charset'] = 'UTF-8';
    }
    if (!empty($this->user->form_vals)) {
        foreach ($this->user->form_vals as $i => $v) {
            $$i = $v;
        }
        $compose_message = $this->user->htmlsafe($compose_message, $this->pd['charset'], false, false, false, false, true);
        if (isset($reply_uid)) {
            $this->pd['message_uid'] = $reply_uid;
        }
    }
    else {
        $compose_to = $this->pd['to'];
        $compose_cc = $this->pd['cc'];
        $message_id = $this->pd['message_id'];
        $compose_bcc = $this->pd['bcc'];
        $compose_in_reply_to = $this->pd['in_reply_to'];
        $compose_references = $this->pd['refs'];
        $compose_subject = $this->pd['subject'];
        $compose_message = $this->user->htmlsafe($this->pd['message_pre'], false, true, false, true).
                           $this->user->htmlsafe($this->pd['message'], $this->pd['charset'], false, false, false, false, true);
        if (isset($this->pd['settings']['profiles'][$this->pd['profile_id']]['auto_sig']) &&
            $this->pd['settings']['profiles'][$this->pd['profile_id']]['auto_sig']) {
            $compose_message .= "\r\n".$this->pd['settings']['profiles'][$this->pd['profile_id']]['profile_sig'];
        }
    }
    $data = '<div id="compose_form"><input type="hidden" id="compose_content_type" name="compose_content_type" value="'.$this->pd['ctype'].'" />
             <input type="hidden" id="message_id" name="message_id" value="'.$this->user->htmlsafe($message_id).'" />';
    $data .= '<input type="hidden" id="compose_session" name="compose_session" value="'.$this->pd['compose_session'].'" />';
    $data .= do_display_hook('compose_form_top');
    if (isset($this->pd['message_uid']) && isset($this->pd['mailbox'])) {
        $data .= '<input type="hidden" name="reply_box" value="'.$this->user->htmlsafe($this->pd['mailbox']).'" /><input type="hidden" name="reply_uid" value="'.
                 $this->pd['message_uid'].'" />';
    }
    $data .= '<input type="hidden" id="compose_in_reply_to" name="compose_in_reply_to" value="'.$this->user->htmlsafe($compose_in_reply_to).
             '" /><input type="hidden" name="compose_references" id="compose_references" value="'.$this->user->htmlsafe($compose_references).'" />';
    $data .= '<table cellpadding="0" cellspacing="0"><tr><td class="aleft">'.$this->user->str[8].':</td><td colspan="2">'.$this->print_contacts().'</td></tr>'.
             do_display_hook('compose_above_from').'<tr><td class="aleft">'.$this->user->str[56].':</td><td colspan="2">';
    $data .= '<select name="compose_from" id="compose_from">';
    foreach ($this->pd['profiles'] as $i => $vals) {
        if (!isset($vals['profile_address']) || !$vals['profile_address']) {
            continue;
        }
        if (!isset($vals['profile_name'])) {
            $vals['profile_name'] = '';
        }
        else {
            $vals['profile_name'] = '"'.$vals['profile_name'].'"';
        }
        $data .= '<option ';
        if ((isset($this->pd['profile_id']) && $this->pd['profile_id'] == $i) || (isset($compose_from) && $compose_from == $i)) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$this->user->htmlsafe($vals['profile_name']).'  &#160;&#160;&lt;'.$vals['profile_address'].'&gt;</option>';
    }
    $data .= '</select></td></tr><tr><td class="aleft">'.$this->user->str[55].':</td><td colspan="2"><input type="text" tabindex="1" name="compose_to" id="compose_to" class="address_fld" value="'.
             $this->user->htmlsafe($compose_to, false, true, false, true).'" /></td></tr>';
    $data .= do_display_hook('compose_page_to_row');
    $data .= '<tr><td class="aleft">'.$this->user->str[54].':</td><td colspan="2"><input type="text" tabindex="2" '.
             'name="compose_cc" id="compose_cc" class="address_fld" value="'.$this->user->htmlsafe($compose_cc, false, true, false, true).'" /></td></tr>';
    $data .= do_display_hook('compose_page_cc_row');
    $data .= '<tr><td class="aleft">'.
             $this->user->str[53].':</td><td colspan="2"><input type="text" tabindex="3" name="compose_bcc" id="compose_bcc" class="address_fld" value="'.$this->user->htmlsafe($compose_bcc,
             false, true, false, true).'" /></td></tr>';
    $data .= do_display_hook('compose_page_bcc_row');
    $data .= '<tr><td class="sleft">'.$this->user->str[13].':</td><td class="sright" colspan="2"><input type="text" tabindex="4" name="compose_subject" '.
             'class="address_fld" id="compose_subject" value="'.$this->user->htmlsafe($compose_subject, false, true, false, true).'" /></td></tr>'.
             do_display_hook('compose_options').'<tr><td class="aleft">'.$this->user->str[4].':</td>'.
             '<td class="compose_opts">'.$this->user->str[51].' &#160;<select class="priority" id="compose_priority" name="compose_priority">';
    for ($i=1;$i<6;$i++) {
        $data .= '<option ';
        if ($i == $compose_priority) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$i.'</option>';
    } 
    $data .= '</select> &#160; &#160;'.$this->user->str[52].' <input type="checkbox" value="1" ';
    if ($compose_mdn) { $data .= 'checked="checked" '; }
    $data .= 'name="compose_mdn" id="compose_mdn" /> &#160; &#160;'.$this->user->str[45].' <input type="checkbox" value="1" ';
    if ($compose_skip_sent) { $data .= 'checked="checked"'; }
    $data .= 'name="compose_skip_sent" />' . do_display_hook('compose_after_options');
    $data .= '</td><td class="send_row"><input type="submit" id="compose_sign" name="compose_sign" value="'.$this->user->str[17].'" /> &#160;<input type="submit" id="compose_save" name="compose_save" '.
             'value="'.$this->user->str[18].'" /> &#160;<input type="submit" tabindex="6" name="compose_send" value="'.$this->user->str[19].'" class="send_button" id="send_btn" /></td></tr>'.
             '<tr><td id="mleft">'.$this->user->str[14].':</td>'.
             '<td class="mright" colspan="2"><textarea tabindex="5" rows="30" style="font-family: monospace;" cols="79" id="compose_message" name="compose_message">'.
             $compose_message.'</textarea>'.
             '</td></tr>'.do_display_hook('compose_after_message').
             '<tr><complex-'.$page_id.'><td class="aleft">'.$this->user->str[44].'</td><td class="aright"><input type="file" name="attachment_file" size="30" /><br /><select name="attachment_mime">'.
             '<option value="0">Auto-detect</option><option value="1">Text/Plain</option><option value="2">Application/Octet-Stream</option></select>'.
             '<input class="attach" type="submit" name="compose_attach" value="'.$this->user->str[60].'" /></td></complex-'.$page_id.'><simple-'.$page_id.'><td></td></simple-'.$page_id.'><td class="send_row"><input id="compose_sign2" type="submit" name="compose_sign" value="'.$this->user->str[17].'" /> '.
             '&#160;<input type="submit" name="compose_save" value="'.$this->user->str[18].'" /> &#160;<input type="submit" name="compose_send" '.
             'value="'.$this->user->str[19].'" id="send_btn2" class="send_button" /></td></tr>';
    if (isset($this->pd['attachments'])) {
        $data .= '<tr><td></td><td colspan="2"><table id="attachment_table" cellpadding="0" cellspacing="0"><tr><th>'.$this->user->str[43].'</th><th>'.$this->user->str[42].'</th><th colspan="2">'.$this->user->str[57].'</th></tr>';
        foreach ($this->pd['attachments'] as $i => $vals) {
            $data .= '<tr><td><input name="attachment_id[]" type="checkbox" value="'.$this->user->htmlsafe($i).'" /> '.$this->user->htmlsafe($vals['realname']).'</td><td>'.
                     $this->user->htmlsafe($vals['type']).'</td><td>'.format_size($vals['size']/1024).'</td>';
            if (isset($message_part_types[strtolower($vals['type'])]) && strtolower(substr($vals['type'], 0, 5)) == 'image') {
                if (isset($this->pd['settings']['image_thumbs']) && $this->pd['settings']['image_thumbs']) {
                    if (!$this->user->use_cookies) {
                        $sess = '&PHPSESSID='.session_id();
                    }
                    else {
                        $sess = '';
                    }
                    $data .= '<td><img src="?page=compose&amp;compose_session='.$this->pd['compose_session'].
                             '&amp;thumbnail=1&amp;rand='.$page_id.'&amp;attachment_id='.urlencode($i).$sess.'" /></td>';
                }
            }
            $data .= '</tr>';
        }
        $data .= '<tr><td><input type="submit" name="delete_attachment" value="'.$this->user->str[59].'" /></td></tr></table></td></tr>';
    }
    $data .= '</table>'.do_display_hook('compose_form_bottom').'</div>';
    return $data;
}
function print_contact_select_box() {
    $data = '<select id="contacts" size="8" multiple="multiple" name="contacts[]">';
    foreach ($this->pd['contact_list'] as $vals) {
        if (substr($vals['email'], 0, 4) != '&lt;') {
            $email = '&lt;'.$vals['email'].'&gt;';
        }
        else {
            $email = $vals['email'];
        }
        $email = $this->user->htmlsafe($email, false, false, false, true);
        $data .= '<option value=\'';
        if ($vals['name']) {
            $data .= '"'.$this->user->htmlsafe($vals['name']).'" ';
        }
        $data .= $email.'\'>';
        if ($vals['name']) {
            $data .= $this->user->htmlsafe($vals['name']).' &#160;&#160;';
        }
        $data .= $email.'</option>';
    }
    $data .= '</select>';
    if (isset($this->pd['contact_list_total']) && $this->pd['contact_list_total'] > count($this->pd['contact_list'])) {
        $data .= $this->print_compose_contact_pages();
    }
    return $data;
}
function print_contacts() {
    $contact_sources = array();
    if (isset($_SESSION['contact_sources'])) {
        $contact_sources = $_SESSION['contact_sources'];
    }
    if (isset($this->user->form_vals['search_contacts']) && $this->user->form_vals['search_contacts']) {
        $search_contacts = $this->user->htmlsafe($this->user->form_vals['search_contacts']);
    }
    else {
        $search_contacts = '';
    }
    $data = '<div id="contact_search"><input type="text" class="contact_search" name="search_contacts" value="'.$search_contacts.'" />
             &#160;<input type="submit" name="contact_search" value="'.$this->user->str[9].'" /> &#160;<input type="submit" name="contact_browse" value="'.$this->user->str[23].'" '.
             'onclick="show_contacts(); return false;" /></div></td></tr><tr><td colspan="3">';
    $data .= '<div ';
    if (!isset($this->pd['contact_browse']) || !$this->pd['contact_browse']) {
        $data .= 'style="display: none;" ';
    }
    $data .= 'id="contacts_select">'.do_display_hook('compose_contacts_top').'<table cellpadding="0" cellspacing="0"><tr><td id="contact_buttons">'.
             '<input type="submit" onclick="add_address(\'compose_to\'); '.
             'return false;" name="add_to" value="'.$this->user->str[55].'" /><br /><input type="submit" onclick="add_address(\'compose_cc\'); return false;" name="add_cc" value="'.$this->user->str[54].'" /><br />'.
             '<input type="submit" onclick="add_address(\'compose_bcc\'); return false;" name="add_bcc" value="'.$this->user->str[53].'" /><br /><input type="submit" name="attach_vcard" value="'.$this->user->str[60].'" /><br />'.
             '<input type="submit" name="hide_contacts" value="'.$this->user->str[61].'" onclick="show_contacts(); return false;" /><br /></td><td>';
    if (count($contact_sources) > 1) {
        $data .= '<select id="contact_source" name="contact_source">';
        $data .= '<option value="0">'.$this->user->str[416].'</option>';
        foreach ($contact_sources as $v) {
            $data .= '<option ';
            if (isset($_SESSION['active_contact_source']) && $_SESSION['active_contact_source'] === $v['source']) {
                $data .= 'selected="selected" ';
            }
            $data .= 'value="'.$v['source'].'">'.$v['title'].'</option>';
        }
        $data .= '</select> &#160;<input id="source_update" type="submit" name="change_contact_source" value="'.$this->user->str[193].'" />';
    }
    $data .= '<div id="compose_contacts">';
    $data .= $this->print_contact_select_box();
    $data .= '</div></td></tr></table>'.do_display_hook('compose_contacts_bottom').'</div>';
    if (!isset($this->pd['contact_browse']) || !$this->pd['contact_browse']) {
        $data .= '<input type="hidden" id="contacts_visible" name="contacts_visible" value="0" />';
    }
    else {
        $data .= '<input type="hidden" id="contacts_visible" name="contacts_visible" value="1" />';
    }
    return $data;
}
function print_compose_contact_pages() {
    global $contacts_per_page;
    if (isset($this->pd['contact_list_page'])) {
        $page = $this->pd['contact_list_page'];
    }
    else {
        $page = 1;
    }
    $stop = ceil(($this->pd['contact_list_total']/$contacts_per_page));
    $data = '<div id="contact_page_links">';
    if ($page != 1) {
        $data .= '<input type="submit" name="prev_contact_page" ';
        if ($this->user->ajax_enabled) {
            $data .= 'onclick="get_contact_page(0); return false;" ';
        }
        $data .= 'value="&lt;" />';
    }
    else {
        $data .= '<input style="visibility: hidden;" type="submit" name="prev_contact_page" onclick="get_contact_page(0); return false;" value="&lt;" />';
    }
    if ($stop != 1) {
        $data .= ' '.$page.' ';
    }
    if ($page != $stop && $stop > 1) {
        $data .= '<input type="submit" ';
        if ($this->user->ajax_enabled) {
            $data .= 'onclick="get_contact_page(1); return false;" ';
        }
        $data .= 'name="next_contact_page" value="&gt;" />';
    }
    else {
        $data .= '<input style="visibility: hidden;" type="submit" onclick="get_contact_page(1); return false;" name="next_contact_page" value="&gt;" />';
    }
    $data .= '</div>';
    return $data;
}
}
?>
