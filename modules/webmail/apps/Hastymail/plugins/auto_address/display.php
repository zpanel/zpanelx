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

function auto_address_compose_page_to_row($tools) {
    return '<tr><td style="padding: 0px;"></td><td style="padding: 0px;">'.
           '<div style="display: none;" id="compose_to_row">'.
           '<div id="compose_to_store"></div></div>'.
           '<input type="hidden" value="" name="auto_address_string" id="auto_address_string" /></td></tr>';
}
function auto_address_compose_page_cc_row($tools) {
    return '<tr><td style="padding: 0px;"></td><td style="padding: 0px;">'.
           '<div style="display: none;" id="compose_cc_row">'.
           '<div id="compose_cc_store"></div></div></td></tr>';
}
function auto_address_compose_page_bcc_row($tools) {
    return '<tr><td style="padding: 0px;"></td><td style="padding: 0px;">'.
           '<div style="display: none;" id="compose_bcc_row">'.
           '<div id="compose_bcc_store"></div></div></td></tr>';
}
function auto_address_compose_options_table($tools) {
    $source_type = $tools->get_setting('auto_address_source_type');
    $min_chars = $tools->get_setting('auto_address_min_chars');
    $max_results = $tools->get_setting('auto_address_max_results');
    $search_fld = $tools->get_setting('auto_address_search_fld');
    $search_flds = array(
        0 => 'None',
        1 => 'Name',
        2 => 'E-Mail',
        3 => 'Name and E-mail'
    );
    if (!$min_chars) {
        $min_chars = 2;
    }
    if (!$max_results) {
        $max_results = 10;
    }
    $data = '<tr><td class="opt_leftcol">'.$tools->str[1].' <span class="js1">*</span></td><td><select name="auto_address_search_fld">';
    foreach ($search_flds as $i => $v) {
        $data .= '<option value="'.$i.'" ';
        if ($i == $search_fld) {
            $data .= 'selected="selected" ';
        }
        $data .= '>'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->str[2].' <span class="js1">*</span></td><td><input type="checkbox" ';
    if ($source_type) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="auto_address_source_type" value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->str[3].' <span class="js1">*</span></td><td><select name="auto_address_min_chars">';
    foreach (array(1, 2, 3, 4, 5) as $v) {
        $data .= '<option value="'.$v.'" ';
        if ($v == $min_chars) {
            $data .= 'selected="selected" ';
        }
        $data .= '>'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->str[4].' <span class="js1">*</span></td><td><select name="auto_address_max_results">';
    foreach (array(5, 10, 15, 20) as $v) {
        $data .= '<option value="'.$v.'" ';
        if ($v == $max_results) {
            $data .= 'selected="selected" ';
        }
        $data .= '>'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    return $data;
}

?>
