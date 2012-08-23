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

function spam_folder_init($tools) {
    $tools->save_to_global_store('help_strings',$tools->str);
    if (isset($_POST['spam_folder_expunge'])) {
        clear_spam_folder($tools);
    }
}
function spam_folder_update_settings($tools) {
    $folders = $tools->imap_get_folders();
    if (isset($_POST['spam_folder']) && isset($folders[$_POST['spam_folder']])) {
        $tools->save_setting('spam_folder', $_POST['spam_folder']);
    }
    else {
        $tools->save_setting('spam_folder', '');
    }
    if (isset($_POST['spam_age_limit'])) {
        $age = intval($_POST['spam_age_limit']);
        if ($age > 0) {
            $tools->save_setting('spam_age_limit', $age);
        }
        else {
            $tools->save_setting('spam_age_limit', '');
        }
    }
}
function spam_folder_mailbox_controls_1($tools) {
    $res = '';
    if ($tools->get_page() == 'mailbox' && $tools->get_mailbox() == $tools->get_setting('spam_folder')) {
        $res = '<input type="submit" onclick="return hm_confirm(\''.$tools->str[3].'\');" class="empty_trash_btn" name="spam_folder_expunge" value="'.$tools->str[2].'" />';
    }
    return $res;
}
function spam_folder_before_logout($tools) {
    $spam = $tools->get_setting('spam_folder');
    $age = $tools->get_setting('spam_age_limit');
    if ($spam && $age && $tools->imap_select_mailbox($spam)) {
        $res = $tools->imap_search_mailbox(build_search_cmd($age));
        if (count($res)) {
            $tools->imap_delete_messages($spam, $res, true, true);
            $tools->imap_expunge_mailbox($spam, true, $res);
        }
    }
}

function clear_spam_folder($tools) {
    $mailbox = $tools->get_mailbox();
    if ($tools->imap_select_mailbox($mailbox)) {
        $tools->imap_delete_messages($mailbox, $tools->imap_get_mailbox_uids($mailbox), true);
        $tools->imap_expunge_mailbox($mailbox, false, array(), true);
    }
}
function build_search_cmd($val) {
    $res = 'SENTBEFORE ';
    $now = time();
    $diff = ($val - 1)*60*60*24;
    $new_time = $now - $diff;
    $res .= @date("d-M-Y", $new_time);
    return $res;
}
?>
