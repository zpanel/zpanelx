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

$quota_hooks = array(
    'work_hooks'        => array('init', 'update_settings'),
    'display_hooks'     => array('mailbox_search', 'mailbox_meta', 'general_options_table', 'folder_list_top', 'folder_list_bottom'),
    'page_hook'         => false,
);
$quota_langs = array(
    'en_US' => array(
        1 => 'Enable mailbox quota display',
        2 => 'Mailbox top',
        3 => 'Folder list top',
        4 => 'Folder list bottom', 
        5 => 'None',
        6 => 'Mailbox bottom',
    ),
    'pl_PL' => array(
        1 => 'Włącz pokazywanie wielkości skrzynki',
        2 => 'Na górze skrzynki',
        3 => 'Nad listą folderów',
        4 => 'Pod listą folderów',
        5 => 'Brak',
        6 => 'Na dole skrzynki',
    ),
);
?>
