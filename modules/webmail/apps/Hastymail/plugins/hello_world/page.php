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
function url_action_hello_world($tools, $get, $post) {
    
    /* if the user is not logged in resturn a page not found page */
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }

    /* get the current mailbox if any */

    $mailbox = $tools->get_mailbox();
    
    /* set the current mailbox */

    if ($mailbox) {
        $tools->set_mailbox($mailbox);
    }

    /* send the $mailbox value to the print function */

    return $mailbox;
}

/*  PRINT FUNCTION
    Second of 2 required functions for plugins that have their own pages.
    The name of this function is always print_<plugin name>
    and it's called when the URL "page" variable is set to "hello_world" such as:

    http://hastymail.org?page=hello_world

    The single input argument is whatever was returned from the url_action_<plugin name>
    function. Output from this function should be bulit into a string then retrned when
    complete. If present a css file will automatically be included for you to use to style
    the output. The css file should be located in a sub-directory called "css" and be named
    with the plugin name like so:

    plugins/<plugin name>/css/<plugin name>.css

    for this plugin the file is located at:
    plugins/hello_world/css/hello_world.css
*/
function print_hello_world($mailbox, $tools) {

    /* build some XHTML */

    $data = '
        <div id="hello_world">'.$tools->str[2].'<br /><br />
            <span>
                '.$tools->str[3].'
                <a href="?page=mailbox&amp;mailbox='.urlencode($mailbox).'">'.$tools->display_safe($mailbox).'</a>
            <br /><br /><a href="" onclick="hm_ajax_hello_world_test(1, 2); return false;">'.$tools->str[4].'</a>
            </span>
        </div>';

    /* return it to the content area of the page */

    return $data;
}
?>
