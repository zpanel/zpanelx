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

function filters_menu($tools, $args) {
    if ($tools->get_setting('filter_menu')) {
        if (isset($args['filters_menu'])) {
            return $args['filters_menu'];
        }
        return ' <a class="filters_link" href="?page=filters">'.$tools->str[25].'</a>&nbsp; ';
    }
}
function filters_mailbox_options_table($tools) {
    $data = '<tr><td class="opt_leftcol"><a href="?page=filters">'.$tools->str[24].'</a></td></tr>';
    return $data;
}
function filters_mailbox_controls_1($tools) {
    return filters_mailbox_controls_2($tools);
}
function filters_mailbox_controls_2($tools) {
    $data = '<input type="submit" name="plugin_filter" value="'.$tools->str[27].'" />';
    return $data;
}

function filters_new_page_controls($tools) {
    $data = '<input type="submit" name="plugin_filter" value="'.$tools->str[27].'" />';
    return $data;
}

?>
