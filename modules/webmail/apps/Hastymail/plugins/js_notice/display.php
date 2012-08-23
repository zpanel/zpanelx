<?php

/*  display.php: Plugin file responsible for the output of XHTML into existing Hastymail pages.
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

function js_notice_page_top($tools) {
    $notices = $tools->get_notices();
    $data = '';
    if (!empty($notices)) {
        $data .= '<input type="hidden" id="js_notice_trigger" value="1" />';
    }
    else {
        $data .= '<input type="hidden" id="js_notice_trigger" value="0" />';
    }
    $data .= '<div id="js_notices" ';
    if ($tools->is_new_window()) {
        $data .= 'class="js_notices_win" ';
    }
    $data .= ' style="display: none;">';
    foreach ($notices as $notice) {
        $data .= $notice.'<br />';
    }
    $data .= '<a style="position: absolute; right: 5px; top: 5px; font-size: 70%;" href="#" onclick="return hide_notice_div();">Close</a></div>';
    return $data;
}


?>
