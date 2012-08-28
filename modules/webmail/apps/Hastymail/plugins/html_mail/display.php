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

/*  DISPLAY HOOKS FUNCTIONS
    For every display hook the plugin registers in config.php there must
    be a corresponding function in this file called <plugin name>_<hook name>
    Output from these functions should be built into a string and returned when complete.
*/


/*  The menu hook outputs between the "Compose" and "Logout" links in the main menu.
    See the docs/display_hooks.txt file for hook location descriptions
    The following adds a link to the menu to our "hello world" page.
*/  
function html_mail_compose_after_options($tools) {
    $html_format_mail = $tools->get_setting('html_format_mail');
    $res = '&#160;&#160;';
    if ($tools->get_setting('html_mode_toggle')) {
        $res .= '<a href="javascript:htmlmode(';
        if ($html_format_mail) {
            $res .= 'false)" id="html_mode_link" >'.$tools->str[5].'</a>
                <input type="hidden" name="html_mail_mode_type" id="html_mail_mode_type" value="1" />';
        }
        else {
            $res .= 'true)" id="html_mode_link" >'.$tools->str[6].'</a>
                <input type="hidden" name="html_mail_mode_type" id="html_mail_mode_type" value="0" />';
        }
    }
    elseif ($html_format_mail) {
        $res .= '<input type="hidden" name="html_mail_mode_type" id="html_mail_mode_type" value="1" />';
    }
    return $res;
}
function html_mail_compose_options_table($tools) {
    $html_font_sizes = array('xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large');
    $html_font_families = array('Andale Mono', 'Arial', 'Arial Black', 'Book Antiqua',
        'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact', 'Symbol', 'Tahoma',
        'Terminal', 'Times New Roman', 'Trebuchet', 'Verdana', 'Webdings', 'Wingdings'
    );
    $html_format_mail = $tools->get_setting('html_format_mail');
    $html_font_size = $tools->get_setting('html_font_size');
    $html_font_family = $tools->get_setting('html_font_family');
    $html_mode_toggle = $tools->get_setting('html_mode_toggle');
    $data = '<tr><td class="opt_leftcol">'.$tools->str[1].'</td><td><input type="checkbox" ';
    if ($html_format_mail) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="html_format_mail" value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->str[4].'</td><td><input type="checkbox" ';
    if ($html_mode_toggle) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="html_mode_toggle" value="1" /></td></tr>';

    $data .= '<tr><td class="opt_leftcol">'.$tools->str[2].'</td><td><select name="html_font_size">';
    foreach ($html_font_sizes as $val) {
        $data .= '<option ';
        if ($html_font_size == $val) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$val.'">'.$val.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->str[3].'</td><td><select name="html_font_family">';
    foreach ($html_font_families as $val) {
        $data .= '<option ';
        if ($html_font_family == $val) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$val.'">'.$val.'</option>';
    }
    $data .= '</select></td></tr>';
    return $data;
}

?>
