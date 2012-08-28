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

function move_sent_compose_after_options($tools) {
    $enabled = $tools->get_setting('move_sent_enabled');
    if ($enabled) {
	global $pd;
	$folders = $tools->imap_get_folders();
        $defaultSentFolder = $tools->get_setting('sent_folder');
        $defaultMoveReplied = 0;

        reset($folders);
        $firstFolder = current($folders);
	if (isset($folders[$pd->pd['mailbox']])) {
	    if ($pd->pd['mailbox'] != $firstFolder['realname']) {
	        // set the default folder to the replied message's folder if it's not the INBOX
	        $defaultSentFolder = $pd->pd['mailbox'];
	    }
	    // set the mark for replied messaged
            $defaultMoveReplied = 1;
	}
        $data = '<div id="move_sent_elements">';
        $data .= '<span id="move_sent_selection">';
        $data .= $tools->str['compose_save_sent_to'] . '&nbsp;&nbsp;<select name="sent_box">';
        $data .= $tools->print_folder_dropdown($folders, array($defaultSentFolder), true);
        $data .= '</select>';
        $data .= '</span>';

        if ($pd->pd['message_uid']) {
            $data .= '<span id="move_sent_replied_too">'.$tools->str['compose_move_replied_message_too'];
            $data .= '&nbsp;&nbsp;';
            $data .= '<input type="checkbox" name="move_reply" value="1"';
            if ($defaultMoveReplied) {
                $data .= ' checked="checked"';
            }
            $data .= ' />';
            $data .= '</span>';
        }
        $data .= '</div>';
        return $data;
    }
}
function move_sent_compose_options_table($tools) {
    $custom = $tools->get_setting('move_sent_enabled');
    $data = '<tr><td class="opt_leftcol">'.$tools->str['config_global_enable'].'</td>
             <td><input type="checkbox" name="move_sent_enabled" value="1" ';
    if ($custom) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr>';
    return $data;
}

?>
