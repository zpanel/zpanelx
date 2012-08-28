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
function uuencode_message_top($tools) {
    require_once($tools->include_path.'include.php');
    if ($tools->get_current_message_type() == 'text/plain') {
        $message = $tools->get_current_message();
        $attachments = get_attachments($message);
        if (strstr($tools->get_url(), 'raw')) {
            return;
        }
        foreach ($attachments as $i => $v) {
            $message = preg_replace("/begin \d+ $i.+?\nend/s", "[Uuencoded attachment: $i]", $message);
        }
        $tools->add_to_store('uuencode_attachments', $attachments);
        $tools->set_current_message($message);
    }
}
function uuencode_message_parts_table($tools) {
    $attachments = $tools->get_from_store('uuencode_attachments');
    if ($attachments && !empty($attachments)) {
        $data = '';
        $files = array();
        foreach ($attachments as $i => $v) {
            $files[] = '<tr><td class="view_cell"><a href="'.$tools->get_url().'&amp;uuencode=1&amp;filename='.$i.'">Download</a>'.
                       '</td><td></td><td>'.$i.'</td><td></td><td></td><td>uuencode</td><td></td></tr>';
        }
        $data .= join('', $files);
        return $data;
    }
    return;
}
?>
