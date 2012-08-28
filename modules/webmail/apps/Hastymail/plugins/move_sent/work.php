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

function move_sent_compose_after_send($tools) {
    if (isset($_POST['move_reply']) // user requested move
        && $_POST['sent_box'] != $_POST['reply_box'] // source and destination differ
        && $_POST['sent_box'] != $_SESSION['user_settings']['sent_folder'] // destination is not the default sent_folder
        && isset($_POST['reply_uid']) && $_POST['reply_uid'] // reply_uid is set i.e. it's really a reply
        && isset($_POST['reply_box']) && isset($_SESSION['folders'][$_POST['reply_box']]) // source folder is set and exists
        && isset($_POST['sent_box'])  && isset($_SESSION['folders'][$_POST['sent_box']])) {
        $tools->imap_move_messages($_POST['reply_box'], array($_POST['reply_uid']), $_POST['sent_box']);
    }
}
function move_sent_message_send($tools) {
    if (isset($_POST['sent_box'])) {
        $tools->override_sent_folder($_POST['sent_box']);
    }
}
function move_sent_init($tools) {
    $opts = $tools->str;
    $tools->save_to_global_store('help_strings', $opts);
    if ($tools->get_page() != 'compose') {
        return;
    }
    $tools->add_style('
        <style type="text/css">
        #move_sent_replied_too{ margin-left: 15px; }
        </style>
    ');
}
function move_sent_update_settings($tools) {
    if (isset($_POST['move_sent_enabled']) && $_POST['move_sent_enabled']) {
        $tools->save_options_page_setting('move_sent_enabled', 1);
    }
    else {
        $tools->save_options_page_setting('move_sent_enabled', 0);
    }
}
?>
