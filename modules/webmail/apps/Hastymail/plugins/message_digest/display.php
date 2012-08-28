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
function message_digest_message_headers_bottom($tools) {
    $data = '';
    $msgs = $tools->get_from_store('message_digest_msgs');
    $vals = $tools->get_from_store('message_digest_vals');
    $date_format_2 = false;
    if (isset($_SESSION['user_settings']['mailbox_date_format'])) {
        $date_format = $_SESSION['user_settings']['mailbox_date_format'];
        if ($date_format != 'r' && $date_format != 'h') {
            if (isset($_SESSION['user_settings']['mailbox_date_format_2'])) {
                $date_format_2 = $_SESSION['user_settings']['mailbox_date_format_2'];
            }
        }
        elseif ($date_format == 'h') {
            $date_format = false;
        }
    }
    else {
        $date_format = false;
    }
    $trim_len = $tools->get_setting('trim_from_fld');
    if (!$trim_len) {
        $trim_len = 0;
    }
    if ($msgs && $vals) {
        $data = '<tr><td colspan="3"><div class="digest_div"><table class="digest_table" cellpadding="0" cellspacing="0" width="100%">';
        foreach ($msgs as $index => $msg) {
            $data .= '<tr><td width="1%">';
            if ($vals['mpart'] == $index) {
                $data .= '<span>&gt;</span>&#160;</td><td>';
            }
            else {
                $data .= '<span style="visibility: hidden;">&gt;</span>&#160;</td><td>';
            }
            $data .= '<a href="'.$vals['url'].'&amp;message_part='.$index.'">'.$tools->display_safe($msg['subject'], false, true).'</a></td>';
            $data .= '<td nowrap="nowrap">'.trim_htmlstr($tools->display_safe($msg['from'], false, true), $trim_len).'</td>';
            $data .= '<td class="dt_cell">'.print_time2($msg['date'], $date_format, $date_format_2).'</td></tr>';
        }
        $data .= '</table></div></td></tr>';
    }
    return $data;
}
function message_digest_message_options_table($tools) {
    $enable_digest_display = $tools->get_setting('enable_digest_display');
    $data = '<tr><td class="opt_leftcol">'.$tools->str[1].'</td><td><input type="checkbox" ';
    if ($enable_digest_display) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="enable_digest_display" value="1" /></td></tr>';
    return $data;
}
?>
