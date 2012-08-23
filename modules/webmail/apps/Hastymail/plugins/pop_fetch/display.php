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

function pop_fetch_menu($tools, $args) {
    if (isset($args['pop_fetch_menu'])) {
        return $args['pop_fetch_menu'];
    }
    return ' <a class="pop_fetch_link" href="?page=pop_fetch">Fetch</a>&#160; ';
}
function pop_fetch_folder_list_bottom($tools) {
    if ($tools->get_setting('pop_folder_tree')) {
        return '<div id="fetch_link" style="padding: 0px; padding-bottom: 10px; margin-top: -10px;"><a onclick="fetch_mail(); return false;" '.
            'style="font-size: 80%; padding-left: 15px;" href="'.$tools->get_url().
            '">Fetch Mail</a></div>';
    }
}
?>
