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

function auto_address_init($tools) {
    $opts = $tools->str;
    $tools->save_to_global_store('help_strings', $opts);
    if ($tools->get_page() != 'compose') {
        return;
    }
    if (!$tools->get_setting('auto_address_search_fld')) {
        return;
    }
    $min_chars = $tools->get_setting('auto_address_min_chars');
    if (!$min_chars) {
        $min_chars = 2;
    }
    $tools->add_js_event_handler('compose_to', 'onkeyup', 'to_address_lookup');
    $tools->add_js_event_handler('compose_cc', 'onkeyup', 'cc_address_lookup');
    $tools->add_js_event_handler('compose_bcc', 'onkeyup', 'bcc_address_lookup');
    $tools->add_js_event_handler('compose_cc', 'onfocus', 'clear_all_addys');
    $tools->add_js_event_handler('compose_to', 'onfocus', 'clear_all_addys');
    $tools->add_js_event_handler('compose_bcc', 'onfocus', 'clear_all_addys');
    $tools->add_js_event_handler('compose_page', 'onclick', 'hide_addys');
    $tools->register_ajax_callback('get_addys', 2, false);
    $tools->add_style('
        <style type="text/css">
        #compose_to_store, #compose_cc_store, #compose_bcc_store{ margin-top: -10px; max-width: 500px; white-space: nowrap; overflow: hidden !important;}
        #compose_to_store, #compose_cc_store, #compose_bcc_store{ background-color: #efefef; border: solid 1px #ccc; border-top: none; position: absolute; padding: 5px; }
        #compose_to_store a, #compose_cc_store a, #compose_bcc_store a{ text-decoration: none; padding: 3px; display: block; }
        #compose_to_store a:hover, #compose_cc_store a:hover, #compose_bcc_store a:hover{ color: #000; background-color: #fff; }
        #compose_to_store a:active, #compose_cc_store a:active, #compose_bcc_store a:active{ color: #000; background-color: #fff; }
        #compose_to_store a:focus, #compose_cc_store a:focus, #compose_bcc_store a:focus{ color: #000; background-color: #fff; } </style>
    ');
    $tools->add_js_onload('
        if (document.getElementById("compose_to")) {
        document.forms[0].setAttribute("autocomplete", "off");
        document.getElementById("compose_to").setAttribute("autocomplete", "off");
        document.getElementById("compose_cc").setAttribute("autocomplete", "off");
        document.getElementById("compose_bcc").setAttribute("autocomplete", "off");
    }');
    $js = 'var time_out_id = 0;
        function clear_all_addys(e) {
            document.getElementById("compose_to_row").style.display = "none";
            document.getElementById("compose_cc_row").style.display = "none";
            document.getElementById("compose_bcc_row").style.display = "none";
        }
        function hide_addys(e) {
            var targ;
            if (!e) { e = window.event; }
            if (e.target) { targ = e.target; }
            else { if (e.srcElement) { targ = e.srcElement; } }
            if (targ.nodeType == 3) { targ = targ.parentNode; }
            if (targ.id != "compose_to" && targ.id != "compose_to_row" && targ.id != "compose_to_store") {
                document.getElementById("compose_to_row").style.display = "none";
            }
            if (targ.id != "compose_cc" && targ.id != "compose_cc_row" && targ.id != "compose_cc_store") {
                document.getElementById("compose_cc_row").style.display = "none";
            }
            if (targ.id != "compose_bcc" && targ.id != "compose_bcc_row" && targ.id != "compose_bcc_store") {
                document.getElementById("compose_bcc_row").style.display = "none";
            }
        }
        function get_keycode(e) {
            if( window.event ) {
                e = window.event;
            }
            if( typeof( e.keyCode ) == "number"  ) {
                e = e.keyCode;
            } else if( typeof( e.which ) == "number" ) {
                e = e.which;
            } else if( typeof( e.charCode ) == "number"  ) {
                e = e.charCode;
            }
            return e;
        }
        function check_keycode(e, fld_id) {
            if (e) {
                if (e == 40) {
                    select_addy(0, fld_id);
                    return false;
                }
                else if (e*1 > 90 || (e*1 != 8 && e*1 < 48)) {
                    return false;
                }
                return true;
            }
            return false;
        }
        function start_autocomplete(e, fld_id) {
            if (time_out_id) {
                clearTimeout(time_out_id);
                time_out_id = 0;
            }
            var lookup_function = function() {address_lookup(e, fld_id)};
            time_out_id = setTimeout(lookup_function, 150);
        }
        function to_address_lookup(e) {
            e = get_keycode(e);
            if (!check_keycode(e, "compose_to")) { return false; }
            start_autocomplete(e, "compose_to");
        }
        function cc_address_lookup(e) {
            e = get_keycode(e);
            if (!check_keycode(e, "compose_cc")) { return false; }
            start_autocomplete(e, "compose_cc");
        }
        function bcc_address_lookup(e) {
            e = get_keycode(e);
            if (!check_keycode(e, "compose_bcc")) { return false; }
            start_autocomplete(e, "compose_bcc");
        }
        function address_lookup(e, fld_id) {
            var to_string = document.getElementById(fld_id).value;
            re = new RegExp(/(^|\s)[^\s]{'.$min_chars.',}$/);
            re2 = new RegExp(/([^\s]+(,|;)\s+|^)([^\s]+\s+[^\s]+)/);
            if (to_string.match(re) || to_string.match(re2)) {
                hm_ajax_auto_address_get_addys(to_string, fld_id, fld_id + \'_store\');
            }
            else {
                document.getElementById(fld_id + "_row").style.display = "none";
            }
        }
        function callback_auto_address_get_addys(output) {
            sections = output.split("^^^^auto_addy_res^^^^");
            if (sections.length == 3) {
                var fld_id = sections[0];
                output = sections[1];
                var auto_address_string = sections[2];
                document.getElementById("auto_address_string").value = auto_address_string;
                document.getElementById(fld_id + "_store").innerHTML = output;
                if (output) {
                    document.getElementById(fld_id + "_row").style.display = "block";
                }
                else {
                    document.getElementById(fld_id + "_row").style.display = "none";
                }
            }
            else {
                document.getElementById(fld_id + "_row").style.display = "none";
            }
        }
        function select_addy(index, fld_id) {
            if (document.getElementById("addy_opt" + fld_id + index)) {
                document.getElementById("addy_opt" + fld_id + index).focus();
            }
        }
        function select_addy_opt(e, id, fld_id) {
            e = get_keycode(e);
            var next_id = (id*1 + 1);
            var prev_id = (id*1 - 1);
            if (e == 40 && document.getElementById("addy_opt" + fld_id + next_id)) {
                document.getElementById("addy_opt" + fld_id + next_id).focus();
            }
            if (e == 38 && document.getElementById("addy_opt" + fld_id + prev_id)) {
                document.getElementById("addy_opt" + fld_id + prev_id).focus();
            }
            else {
                if (e == 38 && prev_id < 0) {
                    document.getElementById(fld_id).focus();
                }
            }
            return false;
        }
        function set_addy_val(name, email, fld_id) {
            var addy_str = document.getElementById(fld_id).value;
            var len = document.getElementById("auto_address_string").value.length;
            addy_str = addy_str.substr(0, (addy_str.length - len));
            if (email.search(/(\<|\>)/) == -1) {
                email = "<" + email + ">";
            }
            if (name) {
                document.getElementById(fld_id).value = addy_str + \'"\' + name + \'" \' + email;
            }
            else {
                document.getElementById(fld_id).value = addy_str + email;
            }
            document.getElementById(fld_id).focus();
            document.getElementById(fld_id + "_row").style.display = "none";
        }';
    $tools->add_inline_js($js);
}
function auto_address_update_settings($tools) {
    if (isset($_POST['auto_address_source_type']) && $_POST['auto_address_source_type']) {
        $tools->save_options_page_setting('auto_address_source_type', 1);
    }
    else {
        $tools->save_options_page_setting('auto_address_source_type', 0);
    }
    if (isset($_POST['auto_address_min_chars']) && $_POST['auto_address_min_chars']) {
        if ($_POST['auto_address_min_chars'] < 6 && $_POST['auto_address_min_chars'] > 0) {
            $tools->save_options_page_setting('auto_address_min_chars', $_POST['auto_address_min_chars']);
        }
        else {
            $tools->save_options_page_setting('auto_address_min_chars', 2);
        }
    }
    else {
        $tools->save_options_page_setting('auto_address_min_chars', 2);
    }
    if (isset($_POST['auto_address_search_fld'])) {
        if (in_array($_POST['auto_address_search_fld'], array(0,1,2,3))) {
            $tools->save_options_page_setting('auto_address_search_fld', $_POST['auto_address_search_fld']);
        }
        else {
            $tools->save_options_page_setting('auto_address_search_fld', 0);
        }
    }
    else {
        $tools->save_options_page_setting('auto_address_search_fld', 0);
    }
    if (isset($_POST['auto_address_max_results']) && $_POST['auto_address_max_results']) {
        if (in_array($_POST['auto_address_max_results'], array(5, 10, 15, 20))) {
            $tools->save_options_page_setting('auto_address_max_results', $_POST['auto_address_max_results']);
        }
        else {
            $tools->save_options_page_setting('auto_address_max_results', 10);
        }
    }
    else {
        $tools->save_options_page_setting('auto_address_max_results', 10);
    }
}
?>
