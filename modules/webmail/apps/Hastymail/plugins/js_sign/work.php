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

function js_sign_init($tools) {
    if ($tools->get_page() != 'compose') {
        return;
    }
    $txt_pre = '';
    $tools->add_js_event_handler('compose_sign', 'onclick', 'js_sign_handler');
    $tools->add_js_event_handler('compose_sign2', 'onclick', 'js_sign_handler');
    $tools->register_ajax_callback('get_sig', 2, false);
    $js = 'function js_sign_handler(e) {
            var result = true;
            var targ;
            if (!e) { e = window.event; }
            if (e.target) { targ = e.target; }
            else { if (e.srcElement) { targ = e.srcElement; } }
            if (targ.nodeType == 3) { targ = targ.parentNode; }
            if (targ.name == "compose_sign") {
                get_sig();
                return false;
            }
        }
        function get_sig() {
            var from_el = document.getElementById("compose_from");
            var from_val = from_el.options[from_el.selectedIndex].value;
            hm_ajax_js_sign_get_sig(from_val, "sig_store");
            return false;
        }
        function callback_js_sign_get_sig(output) {
            if (output != "") {
                var type = "text";
                if (document.getElementById("compose_content_type")) {
                    type = document.getElementById("compose_content_type").value;
                }
                if (type == "text") {
                    txtArea = document.getElementById("compose_message");
                    var pos = get_cursor_pos(txtArea);
                    if (!pos && pos != 0) {
                        pos = txtArea.value.length;
                    }
                    var preText = txtArea.value.substr(0, pos);
                    var postText = txtArea.value.substr(pos);
                    var pre = "\n\n";
                    var post = "\n";
                    if (pos == 0 || preText.match(/\n\s*$/)) {
                        pre = "\n";
                    }
                    if (!postText.match(/^\s*\n/)) {
                        post = "\n\n";
                    }
                    txtArea.value = preText + pre + output + post + postText;
                }
                else if (type == "html") {
                    pre = "<br />";
                    post = "<br />";
                    output = output.replace(/\n/mg, "<br />");
                    tinyMCE.execInstanceCommand("compose_message","mceInsertContent",false,output);
                }
            }
        }
        function get_cursor_pos(el) { 
            if (el.selectionStart) { 
                return el.selectionStart; 
            }
            else if (document.selection) { 
                el.focus(); 
                var r = document.selection.createRange(); 
                if (r == null) { return 0; } 
                var re = el.createTextRange(), 
                rc = re.duplicate(); 
                re.moveToBookmark(r.getBookmark()); 
                rc.setEndPoint("EndToStart", re); 
                return rc.text.length; 
            }  
            return 0; 
        }';
    $tools->add_inline_js($js);
}
?>
