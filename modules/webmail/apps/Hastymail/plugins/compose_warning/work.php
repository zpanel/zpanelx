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
function compose_warning_init($tools) {
    $opts = array($tools->get_string(230), $tools->get_string(231), $tools->get_string(425));
    $tools->save_to_global_store('help_strings', $opts);
}
function compose_warning_update_settings($tools) {
    if (isset($_POST['compose_exit_warn']) && $_POST['compose_exit_warn']) {
        $tools->save_options_page_setting('compose_exit_warn', 1);
    }
    else {
        $tools->save_options_page_setting('compose_exit_warn', 0);
    }
    if (isset($_POST['compose_confirm_subject']) && $_POST['compose_confirm_subject']) {
        $tools->save_options_page_setting('compose_confirm_subject', 1);
    }
    else {
        $tools->save_options_page_setting('compose_confirm_subject', 0);
    }
    if (isset($_POST['compose_confirm_send']) && $_POST['compose_confirm_send']) {
        $tools->save_options_page_setting('compose_confirm_send', 1);
    }
    else {
        $tools->save_options_page_setting('compose_confirm_send', 0);
    }
}
function compose_warning_compose_page_start($tools) {
    $js = '';
    $warn = false;
    $confirm_send = 'return true';
    $confirm_subj = 'return true';
    $confirm_exit = 'return true;';
    $tools->add_js_event_handler('send_btn', 'onclick', 'compose_warning_handler');
    $tools->add_js_event_handler('send_btn2', 'onclick', 'compose_warning_handler');
    if ($tools->get_setting('compose_exit_warn')) {
        $warn = true;
        $confirm_exit = '
            var confirm_out = false;
            var submit_page;
            if (targ.href || targ.parentNode.href) { 
                if (!targ.onclick) {
                    if (document.getElementById("compose_message").value) {
                        confirm_out = true;
                    }
                }
            }
            if (confirm_out) {
                submit_page = confirm("Are you sure you want to exit the compose page?");
                return submit_page;
            }
            return true;';
    }
    if ($tools->get_setting('compose_confirm_subject')) {
        $warn = true;
        $confirm_subj = '
            if (targ.name == "compose_send") {
                if (!document.getElementById("compose_subject").value) {
                    submit_page = confirm(document.getElementById("compose_warning_msg_1").value);
                    if (!submit_page) {
                        if (document.getElementById("js_notices")) {
                            innerXHTML("", document.getElementById("js_notices"));
                            document.getElementById("js_notices").style.display = "none";
                        }
                        else {
                            innerXHTML("", document.getElementById("notices"));
                        }
                        document.getElementById("send_btn").style.visibility = "visible";
                        document.getElementById("send_btn2").style.visibility = "visible";
                    }
                    return submit_page;
                }
            }
            return true;';
    }
    if ($tools->get_setting('compose_confirm_send')) {
        $warn = true;
        $confirm_send = '
            if (targ.name == "compose_send") {
                submit_page =  confirm(document.getElementById("compose_warning_msg_2").value);
                if (!submit_page) {
                    if (document.getElementById("js_notices")) {
                        innerXHTML("", document.getElementById("js_notices"));
                        document.getElementById("js_notices").style.display = "none";
                    }
                    else {
                        innerXHTML("", document.getElementById("notices"));
                    }
                    document.getElementById("send_btn").style.visibility = "visible";
                    document.getElementById("send_btn2").style.visibility = "visible";
                }
                return submit_page;
            }
            return true';
    }
    if ($warn) {
        $js = 'function compose_warning_handler(e) {
        var result = true;
        var targ;
        if (!e) { e = window.event; }
        if (e.target) { targ = e.target; }
        else { if (e.srcElement) { targ = e.srcElement; } }
        if (targ.nodeType == 3) { targ = targ.parentNode; }
        function confirm_send(targ) {
            '.$confirm_send.'
        }
        function confirm_subj(targ) {
            '.$confirm_subj.'
        }
        function confirm_exit(targ) {
            '.$confirm_exit.'
        }
        result = confirm_send(targ);
        if (result) {
            result = confirm_subj(targ);
        }
        if (result) {
            result = confirm_exit(targ);
        }
        return result;
    };';
        $tools->add_inline_js($js);
    }   
}
?>
