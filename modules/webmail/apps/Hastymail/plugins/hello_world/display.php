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

/*  DISPLAY HOOKS FUNCTIONS
    For every display hook the plugin registers in config.php there must
    be a corresponding function in this file called <plugin name>_<hook name>
    Output from these functions should be built into a string and returned when complete.
*/


/*  The menu hook outputs between the "Compose" and "Logout" links in the main menu.
    See the docs/display_hooks.txt file for hook location descriptions
    The following adds a link to the menu to our "hello world" page.
*/  
function hello_world_menu($tools) {

    /* get the currently selected mailbox */

    $mailbox = $tools->get_mailbox();

    /* get the link name from the data store that we setup
       in hello_world_init in work.php */

    $link_name = $tools->get_from_store('link_name');

    /* build some XHTML to return */
    $data = '<a href="?page=hello_world&amp;mailbox='.urlencode($mailbox).'">'.$link_name.'</a>&#160; ';

    /* insert the XHTML at the hook point */

    return $data;
}

?>
