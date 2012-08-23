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
function sort_messages($data, $mailbox) {
    $res = array();
    if (isset($_SESSION['user_settings']['sort_by'][$mailbox])) {
        $sort = $_SESSION['user_settings']['sort_by'][$mailbox];
    }
    else {
        $sort = 'ARRIVAL';
    }
    switch ($sort) {
        case 'FROM':
        case 'SUBJECT':
        case 'CC':
        case 'TO':
        case 'SIZE':
        case 'R_FROM':
        case 'R_SUBJECT':
        case 'R_CC':
        case 'R_TO':
        case 'R_SIZE':
            natcasesort($data);
            break;

        case 'DATE':
            usort($data, 'sort_date');
            break;
        case 'R_DATE':
            usort($data, 'sort_date_r');
            break;
        case 'R_ARRIVAL':
            usort($data, 'sort_idate_r');
            break;
        case 'ARRIVAL':
        default:
            usort($data, 'sort_idate');
            break;
    }
    return $data;
}
function url_action_new($get) {
    global $user;
    global $imap;
    global $sticky_url;
    if ($user->logged_in) {
        do_work_hook('new_page_start');
        $user->page_data['new_link_class'] ='current_page';
        $user->dsp_page = 'new';
        $user->page_data['mailbox_page'] = 1;
        $user->page_data['sort_by'] = 'ARRIVAL';
        $new_page_data = array();
        $grand_total = 0;
        $unread_folder_count = 0;
        $configured_folders = 0;
        if (isset($_SESSION['user_settings']['folder_check'])) {
            $_SESSION['unseen_status'] = $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
            $configured_folders = count($_SESSION['user_settings']['folder_check']);
            foreach ($_SESSION['user_settings']['folder_check'] as $v) {
                $new_page_data[$v] = array();
                list($total, $uids) = $imap->select_mailbox($v, false, true);
                if ($total) {
                    if (!empty($uids)) {
                        if (isset($_SESSION['frozen_folders'][$v])) {
                            $new_uids = array();    
                            foreach ($uids as $uid) {
                                if (in_array($uid, $_SESSION['uid_cache'][$v]['uids'])) {
                                    $new_uids[] = $uid;
                                }
                            }
                            $uids = $new_uids;
                        }
                        $total = count($uids);
                        $new_page_data[$v] = array('total' => $total, 'headers' => $this->sort_messages($imap->get_mailbox_page($v, $uids, false), $v));
                    }
                    $unread_folder_count++;
                    $grand_total += $total;
                }
            }
        }
        if ($grand_total > 14) {
            $user->page_data['top_link'] = '<a href="'.$sticky_url.'#top">'.$user->str[186].'</a>';
        }
        //$_SESSION['total_unread'] = $grand_total;
        $user->page_title .= ' | New Mail |';
        $user->page_data['grand_total'] = $grand_total;
        $user->page_data['configured_folders'] = $configured_folders;
        $user->page_data['unread_folder_count'] = $unread_folder_count;
        $user->page_data['new_page_data'] = $new_page_data;
        $user->page_data['folders'] = $_SESSION['folders'];
    }
}
}

class site_page_new extends site_page {
function print_new_content() {
    global $page_id;
    #list($msg_list_flds, $headers, $onclick) = get_msg_list_settings(); 
    $n = 1;
    $data = '';
    $sid = '';
    if (!$this->user->use_cookies && isset($_GET['rs'])) {
        $sid = '&amp;PHPSESSID='.session_id();
    }
    if (!empty($this->pd['new_page_data'])) {
        if (isset($this->pd['settings']['hide_folder_on_empty']) && $this->pd['settings']['hide_folder_on_empty'] && $this->pd['grand_total'] == 0) {
            $data .= '<div id="no_new_mail">No Unread messages found</div>'; 
        }
        else {
        $data .= '<complex-'.$page_id.'><table class="unread_mailbox_table" cellpadding="0" cellspacing="0" width="100%">';
        if ($this->show_headers) {
            $data .= '<tr>'.$this->print_mailbox_list_headers().'</tr>';
        }
        $data .= '</complex-'.$page_id.'>';
        foreach ($this->pd['new_page_data'] as $i => $array) {
            if (!isset($this->pd['folders'][$i]) || !isset($this->pd['folders'][$i]['basename'])) {
                continue;
            }
            if (!isset($array['total'])) {
                $array['total'] = 0;
            }
            if ($array['total'] == 0 && isset($this->pd['settings']['hide_folder_on_empty']) && $this->pd['settings']['hide_folder_on_empty']) {
                continue;
            }
            $data .= '<complex-'.$page_id.'><tr><td colspan="'.count($this->msg_list_flds).'" class="mbx_title_cell">';
            if (isset($array['headers'])) {
                $data .= '<a class="toggle_folder" href="?page=new&amp;mailbox='.
                     urlencode($this->pd['mailbox']).'&amp;toggle_folder='.urlencode($i).'" onclick="toggle_all('.
                     $n.', '.($n + count($array['headers']) - 1).'); return false;">x</a>';
            }
            else {
                $data .= '<a class="toggle_folder" style="visibility: hidden">x</a>';
            }
            $data .= '</complex-'.$page_id.'><h4><a title="'.$this->user->htmlsafe($i, 0, 0, 1).'" href="?page=mailbox'.$sid.'&amp;mailbox='.
                     urlencode($i).'">'.$array['total'].'&#160;&#160;'.$this->user->htmlsafe($this->pd['folders'][$i]['realname'], 0, 0, 1).'</a></h4><complex-'.$page_id.'>';
            if (isset($this->pd['frozen_folders'][$i])) {
                $data .= '<span class="frozen">(Mailbox frozen)</span>';
            }
            $data .= '</td></tr></complex-'.$page_id.'>';
            if (!empty($array['headers'])) {
                $data .= $this->print_mailbox_list_rows($this->msg_list_flds, $array['headers'], $this->onclick, $i, $n, true);
                $n += count($array['headers']);
            }
            else {
                $data .= '<complex-'.$page_id.'><tr><td colspan="'.count($this->msg_list_flds).'" class="mbx_unread_cell"><div class="empty_unread">'.$this->user->str[249].'</div></td></tr></complex-'.$page_id.'>';
            }
        }
        $_SESSION['toggle_all'] = false;
        $_SESSION['toggle_uids'] = array();
        $_SESSION['toggle_boxes'] = array();
        $data .= '<complex-'.$page_id.'></table><input type="hidden" id="page_count" value="'.($n - 1).'" /></complex-'.$page_id.'>';
        $data .= '<span id="new_page_meta">Found <b>'.$this->pd['grand_total'].'</b> messages in <b>'.$this->pd['unread_folder_count'].'</b> folders</span>'; # NEEDS TRANSLATED
        }
    }
    else {
        $data .= '<div id="no_new_mail">No folders configured to be checked for new mail.</div>'; 
    }
    return $data;
}
function print_edit_new_page_form() {
    global $page_id;
    $exclude_list = array('INBOX');
    if (isset($this->pd['settings']['folder_check'])) {
        $exclude_list = $this->pd['settings']['folder_check'];
    }
    $include_list = array();
    foreach ($exclude_list as $v) {
        if (isset($this->pd['folders'][$v])) {
            $include_list[$v] = $this->pd['folders'][$v];
        }
    }
    $data = '<complex-'.$page_id.'><form method="post" action="?page=new&amp;mailbox='.urlencode($this->pd['mailbox']).'">'.$this->user->str[246].': <select name="new_page_folder">'.
             $this->print_folder_option_list($this->pd['folders'], false, 0, array(), false, false, 'custom', $exclude_list).'</select> &#160;'.
             '<input type="submit" name="add_new_page_folder" value="'.$this->user->str[147].'" />&#160; &#160; &#160; '.$this->user->str[247].': <select name="remove_folder">'.
             $this->print_folder_option_list($include_list, false, 0, array(), false, false, 'selectable', array(), true).
             '</select>&#160;<input type="submit" name="remove_new_page_folder" value="'.$this->user->str[248].'" /></form></complex-'.$page_id.'>'; 
    return $data;
}
}
?>
