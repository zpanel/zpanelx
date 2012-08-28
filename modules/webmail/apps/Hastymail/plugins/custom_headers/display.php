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

function custom_headers_compose_options($tools) {
    $enabled = $tools->get_setting('custom_header_enabled');
    if ($enabled) {
        $data = '<tr><td class="aleft">'.$tools->str[2].'</td>
                <td style="white-space: nowrap; font-size: 90%; vertical-align: bottom;" colspan="2">
                <input type="text" name="custom_header_type" style="width: 310px;" /> 
                &#160;:&#160; <input type="text" name="custom_header_value" style="width: 310px;"/></td></tr>';
        return $data;
    }
}
function custom_headers_compose_options_table($tools) {
    $custom = $tools->get_setting('custom_header_enabled');
    $data = '<tr><td class="opt_leftcol">'.$tools->str[1].'</td>
             <td><input type="checkbox" name="custom_header_enabled" value="1" ';
    if ($custom) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr>';
    return $data;
}

?>
