<?php

/*  page.php: Plugin file responsible for handling plugin specific pages 
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


/*
    URL_ACTION function 

    First of 2 required functions for plugins that have their own pages.
    The name of this function is always url_action_<plugin name>
    and it's called when the URL "page" variable is set to "hello_world" such as:

    http://hastymail.org?page=hello_world

    It has one default argument that is the $_GET array. It will have at least 1 name
    value pair, with a name of "page" and a value of <plugin name>.
    Anything this function returns is passed to the print_<plugin name> function (which is the
    second required function for plugins with their own pages). In the following
    I use an array called $page_data which gets passed to the print_<plugin name> function.
*/
function url_action_context($tools, $get, $post) {
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }
}

function print_hello_world($mailbox, $tools) {
}
?>
