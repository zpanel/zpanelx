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
function notices_general_options_table($tools) {
    $str = $tools->str;
    require_once($tools->include_path.'sounds.php');
    $enable_sound = $tools->get_setting('notices_enable_sound');
    $enable_popup = $tools->get_setting('notices_enable_popup');
    $sound_file   = $tools->get_setting('notices_sound_file');
    $data = '<tr><td class="opt_leftcol">'.$str[4].' <span class="js1">*</span></td><td><input type="checkbox" ';
    if ($enable_sound == 1) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="notices_enable_sound" value="1" /> &nbsp;';
    $data .= '<select id="notices_sound_file" name="notices_sound_file">';
    foreach ($sounds as $name => $file) {
        $data .= '<option ';
        if ($sound_file == $file) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$file.'">'.$name.'</option>';
    }
    $data .= '</select> &nbsp;<a href="javascript:test_sound()">Test</a></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$str[5].' <span class="js1">*</span></td><td><input type="checkbox" ';
    if ($enable_popup == 1) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="notices_enable_popup" value="1" /> &nbsp;';
    $data .= '</td></tr>';
    return $data;
}
?>
