<?php

/*  config.php: Plugin file responsible for defining how the plugin interacts with Hastymail 
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


$message_tags_hooks = array(
    'display_hooks' => array(
        'msglist_after_subject',
        'message_headers_bottom',
        'folder_list_top',
        'folder_list_bottom',
        'general_options_table',
        'menu',
    ),
    'work_hooks' => array(
        'update_settings',
        'imap_action',
        'init',
        'mailbox_page_selected',
        'message_page_selected',
        'after_imap_action'
    ),
    'page_hook' => true,
);
$message_tags_langs = array(
    'en_US' => array(
        0 => 'Tags',
        1 => 'Add tags',
        2 => 'Edit tags',
        3 => 'None',
        4 => 'Message tag list display location',
        5 => 'Above the folder list',
        6 => 'Below the folder list',
        7 => 'Replace the folder list',
        8 => 'Disable message tags'
    ),
);

?>
