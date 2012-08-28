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


function js_notice_init($tools) {
    $tools->add_style('<style type="text/css">#notices{display: none;}</style>');
    $tools->add_inline_js('setTimeout(check_notice, 1);
    function show_notice_div() {
        document.getElementById("js_notices").style.display = "block";
        return false;
    }
    function hide_notice_div() {
        document.getElementById("js_notices").style.display = "none";
        return false;
    }
    function check_notice() {
        if (document.getElementById("js_notices")) {
            if (document.getElementById("js_notice_trigger").value == 1) {
                show_notice_div();
                setTimeout(hide_notice_div, '.set_delay_time().');
            }
            else {
                hide_notice_div();
            }
        }
        else {
            setTimeout(check_notice, 200);
        }
    }
    function send_message(msg) {
        if (document.getElementById("js_notices")) {
            document.getElementById("js_notices").style.display = "block";
            innerXHTML(msg, document.getElementById("js_notices"));
        }
        document.getElementById("send_btn").style.visibility = "hidden";
        document.getElementById("send_btn2").style.visibility = "hidden";
    }');
}
function set_delay_time() {
    global $page_start;
    $default_delay = 5;
    list($dec, $secs) = explode(' ', $page_start);
    list($cdec, $csecs) = explode(' ', microtime());
    if ($csecs - $secs > $default_delay) {
        $res = $csecs - $secs;
    }
    else {
        $res = $default_delay;
    }
    return $res*1000;
}
?>
