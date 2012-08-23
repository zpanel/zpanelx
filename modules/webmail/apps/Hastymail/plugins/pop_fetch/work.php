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

function pop_fetch_init($tools) {
    require_once($tools->include_path.'pop3_class.php');
    if ($tools->is_new_window()) {
        return;
    }
    $interval = $tools->get_setting('pop_update_interval');
    if ($interval) {
        $interval = ($interval*1000) - 5000;
    }
    $folder = $tools->get_setting('pop_folder_tree');
    if ($folder || $interval) {
        $tools->register_ajax_callback('fetch_mail', 1, false);
        $tools->add_inline_js('
        var pop_clock = "";
        function callback_pop_fetch_fetch_mail(output) {
            var bits = output.split("^^^");
            if (bits.length == 2) {
                if (bits[1] != 0) {
                    update_page(do_folder_dropdown, page_title);
                    reset_timer();
                }
                if (pop_clock) {
                    document.getElementById("clock_div").innerHTML = pop_clock.replace("&nbsp;", "&#160;");
                }
                if (bits[0] == "auto") {
                    setTimeout(auto_pop_check, '.$interval.');
                }
                else {
                    revert_fetch_link();
                }
            }
        }
        function pop_result(cnt) {
            
        }
        ');
    }
    if ($interval) {
        $tools->add_inline_js('
        function auto_pop_check() {
            if (document.getElementById("clock_div")) {
                pop_clock = document.getElementById("clock_div").innerHTML;
                display_notice(false, "Fetching messages...");
            }
            hm_ajax_pop_fetch_fetch_mail("auto");
        }
        setTimeout(auto_pop_check, '.$interval.');
        ');
    }
    if ($folder) {
        $tools->add_inline_js('
        function fetch_mail() {
            document.getElementById("fetch_link").innerHTML = "<i style=\"font-size: 80%; '.
            'padding-left: 30px;\">Fetching...</i>";
            hm_ajax_pop_fetch_fetch_mail("link");
            if (document.getElementById("clock_div")) {
                pop_clock = document.getElementById("clock_div").innerHTML;
                display_notice(false, "Fetching messages...");
            }
            return false;
        }
        function revert_fetch_link() {
            document.getElementById("fetch_link").innerHTML = "<a onclick=\"fetch_mail(); '.
            'return false;\" style=\"font-size: 80%; padding-left: 30px;\" href=\"'.$tools->get_url().'\">Fetch Mail</a>";
        }
        ');
    }
}
?>
