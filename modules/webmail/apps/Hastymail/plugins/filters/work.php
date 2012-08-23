<?php

/*  work.php: Plugin file responsible for the backend processing
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

function filters_mailbox_page_start($tools) {
    $mailbox = $tools->get_mailbox();
    if (isset($_POST['plugin_filter']) || ($mailbox == 'INBOX' && $tools->get_setting('auto_filter'))) {
        $filters = $tools->get_setting('filters');
        if (is_array($filters)) {
            filter_folder($tools, $filters, $mailbox);
        }
    }
}
function filter_folder($tools, $filters, $mailbox, $empty_notice=true) {
    $matches = 0;
    $tools->imap_select_mailbox($mailbox, 'ARRIVAL', false, true);
    foreach ($filters as $vals) {
        if (isset($vals[5]) && is_array($vals[5]) && !empty($vals[5])) {
            if (!in_array($mailbox, $vals[5])) {
                continue;
            }
        }
        if ($vals[0]) {
            $terms = strtoupper($vals[1]).' "'.str_replace('"', '\"', $vals[0]).'" ';
        }
        else {
            $terms = 'ALL ';
        }
        if (isset($vals[4]) && $vals[4] != 0) {
            $terms .= build_time_string($vals[4]);
        }
        else {
            $terms = rtrim($terms);
        }
        $res = $tools->imap_search_mailbox($terms);
        $matches += count($res);
        if (is_array($res) && !empty($res)) {
            if (isset($vals[3]) && $vals[3] != 'move') {
                if ($vals[3] == 'flag') {
                    $tools->imap_flag_messages($mailbox, $res, 'FLAG');
                }
                elseif ($vals[3] == 'delete') {
                    $tools->imap_delete_messages($mailbox, $res);
                }
            }
            else {
                if ($mailbox != $vals[2]) {
                    $tools->imap_move_messages($mailbox, $res, $vals[2]);
                }
            }
        }
    }
    if ($matches == 0 && $empty_notice && isset($_POST['plugin_filter'])) {
        $tools->send_notice($tools->str[48]);
    }
    return $matches; 
}
function filters_new_page_start($tools) {
    if (isset($_POST['plugin_filter'])) {
        $filters = $tools->get_setting('filters');
        if (is_array($filters)) {
            if (isset($_SESSION['user_settings']['folder_check']) && is_array($_SESSION['user_settings']['folder_check']) && !empty($_SESSION['user_settings']['folder_check'])) {
                $res = 0;
                foreach ($_SESSION['user_settings']['folder_check'] as $v) {
                    $res += filter_folder($tools, $filters, $v, false);
                }
                if (!$res) {
                    $tools->send_notice($tools->str[48]);
                }
            }
        }
    }
}
function build_time_string($val) {
    $res = '';
    if ($val > 0) {
        $res .= 'SENTSINCE ';
        $val = 0 - $val;
    }
    else {
        $res .= 'SENTBEFORE ';
    }
    $now = time();
    $diff = $val*60*60*24;
    $new_time = $now + $diff;
    $res .= date("d-M-Y", $new_time);
    return $res;
}
?>
