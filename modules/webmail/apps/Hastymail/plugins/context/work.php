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


function context_init($tools) {

    if ($tools->get_page() != 'message') {
        return;
    }
    require($tools->include_path.'settings.php');
    $tools->register_ajax_callback('fetch', 1, 'context_store');
    $tools->add_js_event_handler('document.body', 'onmouseup', 'check_selection');
    $tools->add_js_event_handler('msg_iframe.document.body', 'onmouseup', 'check_selection');
    $id_array = array();
    foreach ($context_btn as $btn) {
        $id_array[] = '"'.$btn['id'].'"';
    }
    $id_str = join(',', $id_array);
    $js = 'function check_selection(e) {
            var i;
            var txt = get_selection()+"";
            var txtlen = txt.length;
            var btns = ['.$id_str.'];
            for (i=0;i<btns.length;i++) {
                if (document.getElementById(btns[i])) {
                    if (txtlen == 0) {
                        document.getElementById(btns[i]).disabled = true;
                        document.getElementById(btns[i]).className = "disabled_button" ;
                    }
                    else {
                        document.getElementById(btns[i]).disabled = false;
                        document.getElementById(btns[i]).className = "";
                    }
                }
            }
        }
        function get_selection() {
            var txt = "";
            if (window.getSelection) {
                txt = window.getSelection();
            }
            if (!txt.length && document.getSelection) {
                txt = document.getSelection();
            }
            if (!txt.length && document.selection) {
                txt = document.selection.createRange().text;
            }
            if (!txt.length) {
                try {
                    if (msg_iframe.window.getSelection) {
                        txt = msg_iframe.window.getSelection();
                    }
                    if (!txt.length && msg_iframe.document.getSelection) {
                        txt = msg_iframe.document.getSelection();
                    }
                    if (!txt.length && msg_iframe.document.selection) {
                        txt = msg_iframe.document.selection.createRange().text;
                    }
                } catch (e) {}
            }
            return txt;
        }
        function callback_context_fetch(output) {
            alert(output + "test");
        }
        function urlDecode(s) {
            return decodeURIComponent( s.replace( /\+/g, \'%20\' ).replace( /\%21/g, \'!\' ).replace( /\%27/g, "\'" ).replace( /\%28/g, \'(\' ).replace( /\%29/g, \')\' ).replace( /\%2A/g, \'*\' ).replace( /\%7E/g, \'~\' ) );
        }
        function context_search(location) {
            var txt = urlencode(get_selection());
            if (txt.length) {';
        foreach ($context_btn as $index => $btn) {
            $js .= '
                if (location == "'.$index.'") {
                    var url = "'.$btn['href'].'".replace(/\%q/, txt);
                    window.open(url, "_blank", "");
                }';
        }
        $js .= '
            }
            return false;
        }';
    $tools->add_inline_js($js);
}
?>
