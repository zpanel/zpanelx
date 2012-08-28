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

$message_digest_hooks = array(
    'work_hooks'        => array('init', 'message_page_start', 'update_settings'),
    'display_hooks'     => array('message_headers_bottom', 'message_options_table'),
    'page_hook'         => false,
);
$message_digest_langs = array(
    'en_US' => array(
        1 => 'Enable enhanced digest message display',
    ),
);
?>
