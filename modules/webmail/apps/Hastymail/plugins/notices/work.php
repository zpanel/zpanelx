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
function notices_options_page_start($tools) {
    $js = 'function test_sound() {
            var sound_file;
            var select = document.getElementById("notices_sound_file");
            sound_file = select.options[select.selectedIndex].value;
            var notice_opts = "width=200,height=100,toolbar=no,location=no,directories=no,";
            notice_opts += "status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no";
            var message_box = window.open("plugins/notices/test_sound.php?sound_file=" + escape(sound_file) , "notice_window", notice_opts);
        }';
    $tools->add_inline_js($js);
}
function notices_update_settings($tools) {
    if (isset($_POST['notices_enable_sound']) && $_POST['notices_enable_sound']) {
        $tools->save_options_page_setting('notices_enable_sound', 1);
    }
    else {
        $tools->save_options_page_setting('notices_enable_sound', 0);
    }
    if (isset($_POST['notices_enable_popup']) && $_POST['notices_enable_popup']) {
        $tools->save_options_page_setting('notices_enable_popup', 1);
    }
    else {
        $tools->save_options_page_setting('notices_enable_popup', 0);
    }
    if (isset($_POST['notices_sound_file']) && $_POST['notices_sound_file']) {
        $tools->save_options_page_setting('notices_sound_file', $_POST['notices_sound_file']);
    }
    else {
        $tools->save_options_page_setting('notices_enable_sound', '');
    }
}
function notices_init($tools) {
    $opts = array($tools->str[4], $tools->str[5]);
    $tools->save_to_global_store('help_strings', $opts);
    $notice = 'false';
    $enable_sound = $tools->get_setting('notices_enable_sound');
    $enable_popup = $tools->get_setting('notices_enable_popup');
    $sound_file = $tools->get_setting('notices_sound_file');
    $alert = '';
    $tools->add_inline_js('function get_notice_bg() {
        var body = document.body;
        var bg;
        if (document.defaultView) {
            if (document.defaultView.getComputedStyle) {
                bg = document.defaultView.getComputedStyle(body,"").getPropertyValue("background-color");
            }
        }
        else {
            if (body.currentStyle["backgroundColor"]) {
                bg = body.currentStyle["backgroundColor"];
            }
        }
        return bg;
    }
    function get_notice_fg() {
        var body = document.body;
        var fg;
        if (document.defaultView) {
            if (document.defaultView.getComputedStyle) {
                fg = document.defaultView.getComputedStyle(body,"").getPropertyValue("color");
            }
        }
        else {
            if (body.currentStyle["color"]) {
                fg = body.currentStyle["color"];
            }
        }
        return fg;
    }');
    if (!$enable_popup && !$enable_sound) {
        return;
    }
    if ($enable_sound && $sound_file) {
        $tools->add_js_tag('<script type="text/javascript" src="plugins/notices/script/soundmanager2-min.js"></script>');
        $alert .= 'notice_sound();';
    }
    if ($enable_popup) {
        $alert .= 'notice_window();';
    }
    if (isset($_SESSION['total_unread'])) {
        if (isset($_SESSION['notice_unread'])) {
            if ($_SESSION['total_unread'] > $_SESSION['notice_unread']) {
                $notice = 'true';
            }
        }
        $_SESSION['notice_unread'] = $_SESSION['total_unread'];
    }
    if ($notice == 'true') {
        $play = 'true';
    }
    else {
        $play = 'false';
    }
    $js = 'var auto_notice = '.$notice.';
    var notice_seconds = 0;
    var notice_total = 0;
    var notice_current = 0;
    var notice_unread;
    var notice_title;
    var notice_reg = /^(\d+)/;
    var notice_start = false;';
    if ($enable_sound) {
        $js .= '
        soundManager.debugMode = false;
        soundManager.url = "plugins/notices/swf/";
        soundManager.onload = function() {
            var mySoundObject = soundManager.createSound({
                id: "mySound",
                autoPlay: '.$play.',
                autoLoad: true,
                url: "plugins/notices/sounds/'.$sound_file.'"
            });
        };';
    }
    $js .= '
    function check_for_new() {
        notice_seconds += 1;
        notice_title = document.title;
        notice_unread = notice_reg.exec(notice_title);
        if (notice_unread.length > 0) {
            notice_current = notice_unread[0];
            if (notice_start) {
                if (notice_current > notice_total) {
                    '.$alert.'
                }
            }
            notice_start = true;
            notice_total = notice_current;
        }
        self.setTimeout("check_for_new()", 2000);
    }
    function set_notice_title() {
        return "'.$tools->str[3].'";
    }
    function set_notice_string() {
        return "'.$tools->str[2].'";
    }';
    if ($enable_sound) {
        $js .= '
        function notice_sound() {
            soundManager.play("mySound");
        }';
    }
    if ($enable_popup) {
        $js .= '
        function notice_window() {
            var notice_opts = "width=300,height=100,toolbar=no,location=no,directories=no,";
            notice_opts += "status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no";
            var message_box = window.open("plugins/notices/new_message_window.html", "notice_window", notice_opts);
        }';
    }
    $js .= '
    check_for_new();
    if (auto_notice) {
        '.$alert.'
    }';
    $tools->add_inline_js($js);
}
?>
