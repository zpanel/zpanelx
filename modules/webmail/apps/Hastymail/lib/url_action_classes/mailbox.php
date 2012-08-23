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
function url_action_mailbox($get) {
    global $user;
    global $imap;
    global $sort_types;
    global $client_sort_types;
    global $sticky_url;
    global $sort_filters;
    global $show_all_max;
    if ($user->logged_in) {
        do_work_hook('mailbox_page_start');
        $user->page_data['labels'] = array($user->str[13], $user->str[56], $user->str[58], $user->str[57]);
        if (isset($_SESSION['just_logged_in']) && $_SESSION['just_logged_in']) {
            if (isset($_SESSION['user_settings']['folder_check'])) {
                $imap->get_unseen_status($_SESSION['user_settings']['folder_check']);
            }
        }
        if ($user->dsp_page == 'search') {
            return $this->url_action_search($get);
        }
        $user->page_data['mailbox_link_class'] ='current_page';
        if (isset($_SESSION['search_terms'][0]['words'])) {
            $user->page_data['mailbox_search_words'] = $_SESSION['search_terms'][0]['words'];
        }
        else {
            $user->page_data['mailbox_search_words'] = '';
        }
        if (isset($_SESSION['search_terms'][0]['fld'])) {
            $user->page_data['fld_id'] = $_SESSION['search_terms'][0]['fld'];
        }
        else {
            $user->page_data['fld_id']= 0;
        }
        if (isset($_SESSION['search_terms'][0]['location'])) {
            $user->page_data['location_id'] = $_SESSION['search_terms'][0]['location'];
        }
        else {
            $user->page_data['location_id'] = 0;
        }
        if (isset($_SESSION['user_settings']['top_page_links']) && $_SESSION['user_settings']['top_page_links']) {
            $user->page_data['top_page_links'] = true;
        }
        else {
            $user->page_data['top_page_links'] = false;
        }
        $mailbox = 'INBOX';
        if (isset($get['mailbox'])) {
            if (isset($_SESSION['folders'][$get['mailbox']])) {
                $mailbox = $get['mailbox'];
            }
            elseif ($get['mailbox'] != $_SESSION['folders']['INBOX']['name']) {
                $this->errors[] = $user->str[11].': '.$user->htmlsafe($get['mailbox']);
            }
        }
        $filter_by = 'ALL';
        if (isset($get['filter_by'])) {
            if (isset($sort_filters[$get['filter_by']])) {
                $filter_by = $get['filter_by'];
            }
        }
        else {
            if (isset($_SESSION['user_settings']['hide_deleted_messages']) &&
                $_SESSION['user_settings']['hide_deleted_messages']) {
                $filter_by = 'UNDELETED';
            }
        }
        $user->page_data['filter_by'] = $filter_by;
        if (isset($_SESSION['user_settings']['sort_by'][$mailbox])) {
            $sort_by = $_SESSION['user_settings']['sort_by'][$mailbox];
        }
        else {
            $sort_by = 'ARRIVAL';
        }
        if (isset($_SESSION['session_sort'][$mailbox])) {
            $sort_by = $_SESSION['session_sort'][$mailbox];
        }

        if (isset($get['sort_by'])) {
            if (stristr($_SESSION['imap_capability'], 'SORT')) {
                $types = $sort_types;
            }
            else {
                $types = $client_sort_types;
            }
            if (isset($types[$get['sort_by']])) {
                $sort_by = $get['sort_by'];
                $_SESSION['session_sort'][$mailbox] = $sort_by;
            }
        }
        if (isset($_SESSION['user_settings']['sent_folder']) && $_SESSION['user_settings']['sent_folder'] == $mailbox) {
            $user->page_data['labels'][1] = $user->str[55];
        }
        $unseen_refresh = false;
        if (isset($get['track_mail'])) {
            $sticky_url = preg_replace("/\&amp;track_mail=(1|0)/", '', $sticky_url);
            if ($get['track_mail']) {
                if (isset($_SESSION['user_settings']['folder_check']) && is_array($_SESSION['user_settings']['folder_check'])) {
                    if (!in_array($mailbox, $_SESSION['user_settings']['folder_check'])) {
                        $folders = $_SESSION['user_settings']['folder_check'];
                        $folders[] = $mailbox;
                        usort($folders, 'new_folder_sort');
                        $_SESSION['user_settings']['folder_check'] = $folders;
                        $user->page_data['settings']['folder_check'] = $_SESSION['user_settings']['folder_check'];
                        $unseen_refresh = true;
                        $this->write_settings();
                    }
                }
                else {
                    $_SESSION['user_settings']['folder_check'] = array($mailbox);
                    $user->page_data['settings']['folder_check'] = array($mailbox);
                    $user->page_data['settings']['folder_check'] = $_SESSION['user_settings']['folder_check'];
                    $this->write_settings();
                }
            }
            else {
                if (isset($_SESSION['user_settings']['folder_check']) && is_array($_SESSION['user_settings']['folder_check']) &&
                    in_array($mailbox, $_SESSION['user_settings']['folder_check'])) {
                    $new_folders = array();
                    foreach ($_SESSION['user_settings']['folder_check'] as $v) {
                        if ($v != $mailbox) {
                            $new_folders[] = $v;
                        }
                    }
                    $_SESSION['user_settings']['folder_check'] = $new_folders;
                    $user->page_data['settings']['folder_check'] = $new_folders;
                    $this->write_settings();
                }
            }
        }
        $status = $imap->select_mailbox($mailbox, $sort_by, $unseen_refresh, false, $filter_by);
        do_work_hook('mailbox_page_selected');
        if ($status) {
            $page = 1;
            if (isset($get['mailbox_page'])) {
                $page = (int) $get['mailbox_page'];
                if (!$page) {
                    $page = 1;
                }
            } 
            if (isset($get['show_all_msg']) && $get['show_all_msg']) {
                $page = 1;
                $user->page_data['settings']['mailbox_per_page_count'] = $show_all_max;
                if (count($_SESSION['uid_cache'][$mailbox]['uids']) > $show_all_max) {
                    $this->errors[] = 'Only displaying first '.$show_all_max.' messages out of '.count($_SESSION['uid_cache'][$mailbox]['uids']);
                }
                $user->page_data['show_all_msg'] = 1;
            }
            else {
                $user->page_data['show_all_msg'] = 0;
            }
            list($page, $uids) = $this->build_page_uids($mailbox, $page, $user->page_data['settings']['mailbox_per_page_count'], $_SESSION['uid_cache'][$mailbox]['uids']);
            $total = $_SESSION['uid_cache'][$mailbox]['total'];
            $unread = $_SESSION['folders'][$mailbox]['status']['unseen'];
            $user->page_data['mailbox_page'] = $page;
            $user->page_data['folder_unread'] = $unread;
            $user->page_data['mailbox_range'] = ($user->page_data['settings']['mailbox_per_page_count']*($page - 1) + 1);
            if ($total < $user->page_data['mailbox_range'] - 1 + $user->page_data['settings']['mailbox_per_page_count']) {
                $user->page_data['mailbox_range'] .= ' - '.$total;
            }
            else {
                $user->page_data['mailbox_range'] .= ' - '.($user->page_data['mailbox_range'] - 1 + $user->page_data['settings']['mailbox_per_page_count']);
            }
            if (!empty($uids)) {
                /*if (!empty($_SESSION['uid_cache'][$mailbox]['thread_data'])) {
                    $total = count($_SESSION['uid_cache'][$mailbox]['threads']);
                    list($page, $uids) = $this->build_page_uids($mailbox, $page,
                    $user->page_data['settings']['mailbox_per_page_count'],
                    $_SESSION['uid_cache'][$mailbox]['threads']);
                }*/
                $user->page_data['header_list'] = $imap->get_mailbox_page($mailbox, $uids, $page);
                $user->page_data['page_count'] = count($user->page_data['header_list']);
                if (count($user->page_data['header_list']) > 14) {
                    $user->page_data['top_link'] = '<a href="'.$sticky_url.'#top">'.$user->str[186].'</a>';
                }
                $user->page_data['mailbox_total'] = $_SESSION['uid_cache'][$mailbox]['total'];
                $user->page_data['thread_data'] = $_SESSION['uid_cache'][$mailbox]['thread_data'];
                $user->page_data['page_links'] = build_page_links($page, $total, $user->page_data['settings']['mailbox_per_page_count'], '?page=mailbox&amp;sort_by='.$sort_by.
                                                 '&amp;filter_by='.$filter_by.'&amp;mailbox='.urlencode($mailbox));
            }
            else {
                $user->page_data['header_list'] = array();
                $user->page_data['page_links'] = '';
                $user->page_data['mailbox_total'] = 0;
            }
            $user->page_data['sort_by'] = $sort_by;
            $user->page_data['mailbox'] = $mailbox;
            $user->page_data['url_mailbox'] = urlencode($mailbox);
            if ($mailbox == 'INBOX') {
                $user->page_data['mailbox_dsp'] = $user->str[436];
            }
            else {
                $user->page_data['mailbox_dsp'] = $user->htmlsafe($mailbox, 0, 0, 1);
            }
            $user->dsp_page = 'mailbox';
            if (isset($_SESSION['search_results'])) {
                $user->page_data['search_results'] = $_SESSION['search_results'];
            }
            $user->page_data['folders'] = $_SESSION['folders'];
            $user->page_title .= ' | '.$user->str[22].' |';
            if (isset($_SESSION['frozen_folders'][$mailbox])) {
                $user->page_data['frozen_dsp'] = '<span id="frozen">(Mailbox Frozen)</span>';
            }
            else {
                $user->page_data['frozen_dsp'] = '';
            }
        }
        else {
            $this->errors[] = $user->str[387].': '.$user->htmlsafe($mailbox);
        }
    }
}
function build_page_uids($mailbox, $page, $per_page_count, $uids, $break=false) {
    $res = array();
    $start = ($page - 1)*$per_page_count;
    if ($start < 0) {
        $start = 0;
    }
    if (isset($uids[$start])) {
        $res = array_slice($uids, $start, $per_page_count);
    }
    elseif (!$break && !empty($uids)) {
        $max_page = ceil(count($uids)/$per_page_count);
        if ($max_page < $page && $max_page > 0) {
            return $this->build_page_uids($mailbox, $max_page, $per_page_count, $uids, true);
        }
    }
    return array($page, $res);
} 
}

class site_page_mailbox extends site_page {
function print_freeze() {
    global $page_id;
    if (!isset($this->pd['settings']['mailbox_freeze']) || !$this->pd['settings']['mailbox_freeze']) {
        return;
    }
    $data = '<complex-'.$page_id.'><form method="post" action=""><input type="hidden" name="mailbox" value="'.$this->user->htmlsafe($this->pd['mailbox']).'" />
             &#160;';
    if (isset($this->pd['frozen_folders'][$this->pd['mailbox']])) {
        $data .= '<input type="submit" name="unfreeze_mailbox" value="'.$this->user->str[30].'" />';
    }
    else {
        $data .= '<input type="submit" name="freeze_mailbox" value="'.$this->user->str[26].'" />';
    }
    $data .= '</form></complex-'.$page_id.'>';
    return $data;
}
function print_mailbox_search() {
    global $page_id;
    $flds = array(1 => $this->user->str[112], 2 => $this->user->str[107], 3 => $this->user->str[108], 4 => $this->user->str[109], 
                  5 => $this->user->str[110], 6 => $this->user->str[111]);
    $locations = array(2 => $this->user->str[298], 1 => $this->user->str[297], 3 => $this->user->str[299]);
    $data = '<complex-'.$page_id.'>'.$this->user->str[9].' <select id="search_fld" name="search_fld">';
    foreach ($flds as $i => $v) {
        $data .= '<option ';
        if ($this->pd['fld_id'] == $i)  { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select> <select id="search_location" name="search_location">';
    foreach ($locations as $i => $v) {
        $data .= '<option ';
        if ($this->pd['location_id'] == $i)  { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select> '.$this->user->str[24].' <input type="text" id="mailbox_search_words" onkeypress="return check_search_submit(event);" name="mailbox_search_words" value="'.
             $this->user->htmlsafe($this->pd['mailbox_search_words']).'" /><input id="search_button" type="submit" name="search_mailbox" value="'.$this->user->str[25].'" />';
    if (isset($this->pd['search_results'][$this->pd['mailbox']])) {
        $data .= ' &#160;<input type="submit" name="reset_search" value="Clear" />';
    }
    $data .= '</complex-'.$page_id.'>';
    return $data; 
}
function print_track_mailbox_link() {
    global $sticky_url;
    if (isset($this->pd['settings']['folder_check']) && is_array($this->pd['settings']['folder_check']) &&
        in_array($this->pd['mailbox'], $this->pd['settings']['folder_check'])) {
        $data = '<a href="'.$sticky_url.'&amp;track_mail=0">'.$this->user->str[31].'</a>';
    }
    else {
        $data = '<a href="'.$sticky_url.'&amp;track_mail=1">'.$this->user->str[32].'</a>';
    }
    return $data;
}
}
?>
