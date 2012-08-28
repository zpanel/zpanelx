<?php

/*  ajax.php: Plugin file responsible for handling ajax callbacks
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

function ajax_message_tags_update_tag_list($tools) {
    return print_tag_list($tools, true);
}
function ajax_message_tags_save_tags($tag_str, $uid, $mailbox, $tools) {
    $tags = hm_new('tags', $tools);
    $error = '';
    $new = array();
    $old = array();
    list($valid, $invalid) = $tags->sanitize_tag_string($tag_str);
    if (isset($tags->tag_map[$mailbox][$uid])) {
        foreach ($valid as $tag) {
            if (!in_array($tag, $tags->tag_map[$mailbox][$uid])) {
                $new[] = $tag;
            }
        }
    }
    else {
        $new = $valid;
    }
    if (isset($tags->tag_map[$mailbox][$uid])) {
        foreach ($tags->tag_map[$mailbox][$uid] as $v) {
            if (!in_array($v, $valid)) {
                $old[] = $v;
            }
        }
    }
    $tags->set_tags($mailbox, $new, $old, $uid);
    if (join(' ', $valid) != $tag_str) {
        $error = 'Invalid or duplicate tags not saved^^^'.join(' ', $valid);
    }
    return $error;
}

?>
