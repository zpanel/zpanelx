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
function compose_warning_compose_options_table($tools) {
    $compose_subj = $tools->get_setting('compose_confirm_subject');
    $compose_send = $tools->get_setting('compose_confirm_send');
    $compose_exit = $tools->get_setting('compose_exit_warn');
    $data = '<tr><td class="opt_leftcol">'.$tools->get_string(230).' <span class="js1">*</span></td><td><input type="checkbox" ';
    if ($compose_send) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="compose_confirm_send" value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->get_string(231).' <span class="js1">*</span></td><td><input type="checkbox" ';
    if ($compose_subj) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="compose_confirm_subject" value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->get_string(425).' <span class="js1">*</span></td><td><input type="checkbox" ';
    if ($compose_exit) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="compose_exit_warn" value="1" /></td></tr>';
    return $data;
}
function compose_warning_compose_form_top($tools) {
    return '
        <input type="hidden" id="compose_warning_msg_1" value="'.$tools->get_string(413).'" />
        <input type="hidden" id="compose_warning_msg_2" value="'.$tools->get_string(414).'" />
    ';
}

?>
