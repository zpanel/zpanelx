<?php
/* modified from the Sajax PHP/AJAX include library:
   (c) copyright 2005 modernmethod, inc
*/

function handle_client_request() {
    global $user;
    global $imap;
    global $conf;
    global $include_path;
    global $fd;
    global $valid_ajax_callbacks;
    if (!isset($_POST['rs'])) {
        return;
    }
    ob_end_clean();
    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    $func_name = $_POST["rs"];
    if (isset($_POST['rsargs'])) {
        $args = $_POST["rsargs"];
        $caller = array_shift($args);
    }
    else {
        $args = array();
    }
    $valid_func = false;
    if (in_array($func_name, $valid_ajax_callbacks)) {
        $valid_func = true;
    }
    elseif (isset($_SESSION['plugin_ajax']) && isset($_SESSION['plugin_ajax'][str_replace('ajax_', '', $func_name)])) {
        $valid_func = true;
        $vals = $_SESSION['plugin_ajax'][str_replace('ajax_', '', $func_name)];
        if (is_readable('plugins'.$fd.$vals['plugin'].$fd.'ajax.php')) {
            require_once($include_path.'plugins'.$fd.$vals['plugin'].$fd.'ajax.php');
            $args[] = hm_new('plugin_tools', $caller);
        }
    }
    if (!$valid_func) {
        echo "-:".$user->htmlsafe($func_name)." not callable";
    }
    else {
        echo "+:";
        $result = call_user_func_array($func_name, $args);
        echo "var res = " . trim(prep_ajax_result($result)) . "; res;";
    }
    $user->clean_up(); 
    $imap->disconnect();
    exit;
}
function prep_ajax_result($value) {
    global $hm_tags;
    $type = gettype($value);
    if ($type == "boolean") {
        return ($value) ? "Boolean(true)" : "Boolean(false)";
    } 
    elseif ($type == "integer") {
        return "parseInt($value)";
    } 
    elseif ($type == "double") {
        return "parseFloat($value)";
    } 
    else {
        foreach ($hm_tags as $id => $tag) {
            $value = remove_tags($value, $id, $tag);
        } 
        $val = str_replace("\\", "\\\\", $value);
        $val = str_replace("\r", "\\r", $val);
        $val = str_replace("\n", "\\n", $val);
        $val = str_replace("'", "\\'", $val);
        $val = str_replace('"', '\\"', $val);
        $esc_val = $val;
        $s = "'$esc_val'";
        return $s;
    }
}
function ajax_save_outgoing_message($subject, $body, $to, $cc, $from, $id, $reply_to, $refs, $priortiy, $mdn, $c_session, $content_type) {
    global $user;
    global $hastymail_version;
    global $imap;
    global $conf;
    global $include_path;
    global $fd;
    global $message;
    if ($user->user_action->gpc) {
        $to = stripslashes($to);
        $cc = stripslashes($cc);
        $from = stripslashes($from);
        $body = stripslashes($body);
        $subject = stripslashes($subject);
    }
    $path = $conf['attachments_path'];
    if ($user->logged_in) {
        $_SESSION['compose_sessions'][$c_session] = time();
        if (trim($body)) {
            if (isset($_SESSION['user_settings']['draft_folder'])) {
                $mailbox = $_SESSION['user_settings']['draft_folder'];
                $select_res = $imap->select_mailbox($mailbox, false, false, true);
            }
            else {
                $mailbox = 'INBOX';
            }
            require_once($include_path.'lib'.$fd.'smtp_class.php');
            $message = hm_new('mime', $c_session);
            if ($to) {
                $message->to = $to;
            }
            if ($cc) {
                $message->cc = $cc;
            }
            if ($refs) {
                $message->references = $refs;
            }
            if ($reply_to) {
                $message->in_reply_to = $reply_to;
            }
            if ($id) {
                $message->message_id = $id;
            }
            if (isset($_SESSION['user_settings']['profiles'][$from])) {
                $from_atts = $_SESSION['user_settings']['profiles'][$from];
                $message->from = '"'.$from_atts['profile_name'].'" <'.$from_atts['profile_address'].'> ';
                $message->from_address = $from_atts['profile_address'];
                if (isset($from_atts['profile_reply_to']) && $from_atts['profile_reply_to']) {
                    $message->reply_to = '<'.$from_atts['profile_address'].'>';
                }
            }
            $existing_id = false;
            if ($select_res) {
                $search_res = $imap->simple_search('header message-id', false, $message->message_id);
                if (isset($search_res[0])) {
                    $existing_id = $search_res[0];
                }
            }
            if ($message->from_address) {
                $message->subject = decode_unicode_url($subject);
                $message->body = decode_unicode_url($body);
                if (!isset($_SESSION['user_settings']['compose_hide_mailer']) ||
                    !$_SESSION['user_settings']['compose_hide_mailer']) {
                    $message->set_header('x_Mailer', $hastymail_version);
                }
                if ($priortiy && $priortiy != 3) {
                    $message->set_header('x_Priority', $priortiy);
                }
                if ($mdn) {
                    $message->set_header('disposition_Notification_To', $message->from_address);
                }
                do_work_hook('message_save', array($message->body));
                $status = stream_imap_append($message, $c_session, $mailbox);
                if ($status && $existing_id) {
                    $imap->message_action(array($existing_id), 'DELETE');
                    $imap->message_action(array($existing_id), 'EXPUNGE');
                    $_SESSION['uid_cache_refresh'][$mailbox] = 1;
                    $_SESSION['header_cache_refresh'][$mailbox] = 1;
                }
            }
        }
    }
    if (isset($message->message_id)) {
        return $message->message_id;
    }
    else {
        return '';
    }
}
function ajax_next_contacts() {
    global $user;
    global $include_path;
    global $conf;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        $page = 1;
        if (isset($_SESSION['contact_list_page'])) {
            $page = $_SESSION['contact_list_page'];
        }
        $page++;
        $user->page_data['contact_list_page'] = $page;
        if (isset($_SESSION['active_contact_source'])) {
            $source = $_SESSION['active_contact_source'];
        }
        else {
            $source = 0;
        }
        list($user->page_data['contact_list'], $user->page_data['contact_list_total']) = $vcard->get_quick_list('sort_name', $page, $source);
        if ($user->sub_class_names['url']) {
            $class_name = 'site_page_'.$user->sub_class_names['url'];
            $pd = hm_new($class_name);
        }
        else {
            $pd = hm_new('site_page');
        }
        return $pd->print_contact_select_box();
    }
}
function ajax_prev_contacts() {
    global $user;
    global $include_path;
    global $conf;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        $page = 1;
        if (isset($_SESSION['contact_list_page'])) {
            $page = $_SESSION['contact_list_page'];
        }
        $page--;
        if ($page < 1) {
            $page = 1;
        }
        if (isset($_SESSION['active_contact_source'])) {
            $source = $_SESSION['active_contact_source'];
        }
        else {
            $source = 0;
        }
        $user->page_data['contact_list_page'] = $page;
        list($user->page_data['contact_list'], $user->page_data['contact_list_total']) = $vcard->get_quick_list('sort_name', $page, $source);
        if ($user->sub_class_names['url']) {
            $class_name = 'site_page_'.$user->sub_class_names['url'];
            $pd = hm_new($class_name);
        }
        else {
            $pd = hm_new('site_page');
        }
        return $pd->print_contact_select_box();
    }
}
function ajax_save_folder_state($id) {
    global $user;
    if ($user->logged_in) {
        $state = false;
        if (isset($_SESSION['folder_state'][$id])) {
            $state = $_SESSION['folder_state'][$id];
        }
        if ($state) {
            $state = false;
        }
        else {
            $state = true;
        }
        $_SESSION['folder_state'][$id] = $state;
    }
}
function ajax_save_folder_vis_state($state) {
    global $user;
    if ($user->logged_in) {
        $_SESSION['hide_folder_list'] = $state;
    }
}
function ajax_update_page($mailbox, $page_id, $title, $new=false, $folder_list=false, $mailbox_page=false, $sort_by=false, $filter_by=false, $show_all=false, $force=false) {
    global $force_page_update;
    $res = array();
    if ($mailbox_page == -1) {
        $class_name = 'site_page_new';
    }
    else {
        $class_name = 'site_page_mailbox';
    }
    $pd = hm_new($class_name);
    $continue = true;
    $clock = false;
    $unread = false;
    $new_page = false;
    $tree = false;
    $dropdown = false;
    $mailbox_html = false;
    $mailbox_meta = false;
    if ($new) {
        $quick = true;
        list($mailbox_meta, $new_page) = refresh_new_page($page_id, $pd);
        if (!$new_page) {
            $continue = false;
        }
    }
    else {
        $quick = false;
    }
    if ($continue || $force) {
        list($dropdown, $clock, $unread) = update_dropdown($mailbox, $quick, $page_id, $pd);
        if (!$dropdown) {
            $continue = false;
        }
    }
    $title = update_title($title);
    if ($folder_list && ($force || $continue)) {
        $tree = update_folder_list($mailbox, $pd);
    }
    if (!$clock) {
        $clock = $pd->print_clock();
    }
    if ($mailbox_page != -1) {
        if (!$continue) {
            $period = $_SESSION['user_settings']['ajax_update_interval'];
            if (!isset($_SESSION['page_refresh_count'])) {
                $_SESSION['page_refresh_count'] = 1;
            }
            else {
                $_SESSION['page_refresh_count']++;
            }
            $count = $_SESSION['page_refresh_count'];
            if ($count*$period >= $force_page_update) {
                list($mailbox_meta, $mailbox_html) = update_mailbox_page($pd, $mailbox_page, $mailbox, $page_id, $sort_by, $filter_by, $show_all, $force);
                $_SESSION['page_refresh_count'] = 0;
            }
        }
        if ($continue || $force) {
            list($mailbox_meta, $mailbox_html) = update_mailbox_page($pd, $mailbox_page, $mailbox, $page_id, $sort_by, $filter_by, $show_all, $force);
            $_SESSION['page_refresh_count'] = 0;
        }
    }
    return implode('^^'.$page_id.'^^', array($new_page, $dropdown, $clock, $unread, $title, $tree, $mailbox_html, $mailbox_meta));
}
function update_mailbox_page($pd, $mailbox_page, $mailbox, $page_id, $sort_by, $filter_by, $show_all, $force) {
    global $user;
    global $conf;
    if ($user->logged_in) {
        if ($show_all && $show_all != 'false') {
            $show_all_msg = 1;
        }
        else {
            $show_all_msg = 0;
        }
        $user->user_action->url_action_mailbox(array('show_all_msg' => $show_all_msg, 'mailbox' => $mailbox, 'mailbox_page' => $mailbox_page,
            'sort_by' => $sort_by, 'filter_by' => $filter_by));
        $meta = $pd->pd['mailbox_dsp'].' <span id="mailbox_meta">'.$pd->pd['frozen_dsp'].' '.$pd->user->str[41].': '.
                $pd->pd['mailbox_total'].', '.$pd->user->str[40].': '.$pd->pd['mailbox_page'].' ('.
                $pd->pd['mailbox_range'].')<span class="folder_unread"> '.$pd->user->str[34].' '.$pd->pd['folder_unread'].
                '</span></span>'.do_display_hook('mailbox_meta');
        $pre = '<table cellpadding="0" id="mbx_table" cellspacing="0" width="100%" >';
        if (!isset($disable_list_heading) || $disable_list_heading == true) {
            $pre .= '
                <tr>'.$pd->print_mailbox_list_headers().'</tr>';
        }
        return array($meta, $pre.$pd->print_mailbox_list().'</table>');
    }
}
function update_title($title) {
    global $user;
    global $conf;
    $res = '';
    if ($user->logged_in) {
        $res = $_SESSION['total_unread'].' '.$user->str[10].$title.' '.$conf['page_title'];
    }
    return $res;
}
function refresh_new_page($page_id, $pd) {
    global $user;
    global $imap;
    global $force_page_update;
    if ($user->logged_in) {
        $new_unseen_status = $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
        $unchanged = true;
        if (isset($_SESSION['page_id']) && $_SESSION['page_id'] && $page_id && $_SESSION['page_id'] == $page_id) {
            if (isset($_SESSION['unseen_status'])) {
                foreach ($_SESSION['unseen_status'] as $folder => $vals) {
                    if (isset($new_unseen_status[$folder]) && ($new_unseen_status[$folder][0] != $vals[0] || $new_unseen_status[$folder][1] != $vals[1])) {
                        $unchanged = false;
                        break;
                    }
                }
            } 
            if ($unchanged) {
                $period = $_SESSION['user_settings']['new_page_refresh'];
                if (!isset($_SESSION['page_refresh_count'])) {
                    $_SESSION['page_refresh_count'] = 1;
                }
                else {
                    $_SESSION['page_refresh_count']++;
                }
                $count = $_SESSION['page_refresh_count'];
                if ($count*$period <= $force_page_update) {
                    return array('', '');
                }
                else {
                    $_SESSION['page_refresh_count'] = 0;
                }
            }
        }
        if ($page_id) {
            $_SESSION['page_id'] = $page_id;
        }
        $user->user_action->url_action_new($_GET);
        $_SESSION['unseen_status'] = $new_unseen_status;
        $meta = $pd->user->str[245].' <div>Found '.$pd->pd['grand_total'].' messages in '.$pd->pd['unread_folder_count'].' folders</div>'; /* needs translated */
        return array($meta, $pd->print_new_content());
    }
}
function update_folder_list($mailbox, $pd) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        $pd->pd['mailbox'] = $mailbox;
        $user->page_data['folders'] = $_SESSION['folders'];
        return $pd->print_folder_list($_SESSION['folders']);
    }
}
function update_dropdown($mailbox, $quick=false, $page_id=false, $pd) {
    global $user;
    global $imap;
    if ($user->logged_in) {
        if (!$quick) { 
            $new_unseen_status = $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
            $unchanged = true;
            if (isset($_SESSION['page_id']) && $_SESSION['page_id'] && $page_id && $_SESSION['page_id'] == $page_id) {
                if (isset($_SESSION['unseen_status'])) {
                    foreach ($_SESSION['unseen_status'] as $folder => $vals) {
                        if (isset($new_unseen_status[$folder]) && $new_unseen_status[$folder][0] != $vals[0] || $new_unseen_status[$folder][1] != $vals[1]) {
                            $unchanged = false;
                            break;
                        }
                    }
                } 
                if ($unchanged) {
                    return array(false, false, false);
                }
            }
            if ($page_id) {
                $_SESSION['page_id'] = $page_id;
            }
            $_SESSION['unseen_status'] = $new_unseen_status;
        }
        $pd->pd['mailbox'] = $mailbox;
        $user->page_data['folders'] = $_SESSION['folders'];
        return array($pd->print_folder_dropdown($_SESSION['folders']), $pd->print_clock(),
               '<a href="?page=new&amp;mailbox='.urlencode($mailbox).'" class="unread_link" title="'.sprintf($pd->user->str[537], $_SESSION['total_unread']).'">'.sprintf($pd->user->str[537], $_SESSION['total_unread']).'</a>');
    }
}
?>
