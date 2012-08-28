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

function js_help_init($tools) {
    if ($tools->get_page() != 'options') {
        return;
    }
    $tools->register_ajax_callback('get_help', 1, false);
    $tools->add_js_event_handler('options_form', 'onmouseover', 'js_help');
    $tools->add_js_event_handler('options_form', 'onmouseout', 'clear_help');
    $tools->add_inline_js('var jh_timeout_id = 0;
    function clear_help(e) {
        document.getElementById("js_help_div").style.display = "none";
        targ = get_target(e);
        if (targ.className == "opt_leftcol") {
            if (jh_timeout_id) {
                clearTimeout(jh_timeout_id);
                jh_timeout_id = 0;
            }
            targ.style.fontSize = "100%";
            targ.style.backgroundColor = "transparent";
            targ.style.border = "solid 1px transparent";
        }
    }
    function callback_js_help_get_help(output) {
        if (output) {
            document.getElementById("js_help_div").style.display = "block";
            document.getElementById("js_help_div").innerHTML = output;
        }
    }
    function get_target(e) {
        var targ;
        if (!e) { e = window.event; }
        if (e.target) { targ = e.target; }
        else { if (e.srcElement) { targ = e.srcElement; } }
        if (targ.nodeType == 3) { targ = targ.parentNode; }
        return targ;
    }
    function js_help(e) {
        var targ_string;
        targ = get_target(e);
        if (targ.className == "opt_leftcol") {
            if (targ.firstChild.nodeType == 3 && targ.innerHTML) {
                targ.style.backgroundColor = "#f5f5f5";
                targ.style.border = "solid 1px #ccc";
                if (jh_timeout_id) {
                    clearTimeout(jh_timeout_id);
                    jh_timeout_id = 0;
                }
                targ_string = targ.innerHTML;
                if (targ_string) {
                    targ_string = targ_string.replace(/<span.+/, "");
                    var lookup_function = function() {hm_ajax_js_help_get_help(targ_string, false);};
                    jh_timeout_id = setTimeout(lookup_function, 250);
                }
            }
        }
    }');
}
?>
