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

/* insert javascript and style */
function select_range_init($tools) {
    $list_page = false;
    $select_mode = 'shift_click';
    switch ($tools->get_page()) {
        case 'mailbox':
            $tools->add_js_event_handler('mbx_table', 'onmouseup', 'check_selection');
            $list_page = true;
            break;
        case 'search':
            $tools->add_js_event_handler('search_page_inner', 'onmouseup', 'check_selection');
            $list_page = true;
            break;
        case 'new':
            $tools->add_js_event_handler('new_page', 'onmouseup', 'check_selection');
            $list_page = true;
            break;
        default:
            break;
    }
    if ($list_page) {
        switch ($select_mode) {
            case 'shift_click':
                $tools->add_inline_js('
                    var regex = /^message_(\d+)$/;
                    var range_start = false;
                    function check_selection(e) {
                        try {
                        if (e && e.shiftKey && e.target.id && e.target.id.match(regex) && e.target.checked == false) {
                            if (range_start && range_start != e.target.id) {
                                var end_index = e.target.id.match(regex)[1]*1;
                                var begin_index = range_start.match(regex)[1]*1;
                                var start = 0;
                                var end = 0;
                                if (end_index > begin_index) {
                                    start = begin_index;
                                    end = end_index;
                                }
                                else {
                                    start = end_index;
                                    end = begin_index;
                                }
                                console.log("end: "+end_index+" start:"+begin_index);
                                for (var i = (start + 1); i < end; i++) {
                                    var cbox = document.getElementById("message_"+i);
                                    if (!cbox.checked) {
                                        cbox.checked = true;
                                        highlight_row(cbox);
                                    }
                                }
                            }
                            range_start = e.target.id;
                        }
                        else if (e.target.id && e.target.id.match(regex) && e.target.checked == false) {
                            range_start = e.target.id;
                            console.log(range_start);
                        }}
                        catch (e) {}
                    }
                ');
            break;
        default:
            break;
        }
    }
}
