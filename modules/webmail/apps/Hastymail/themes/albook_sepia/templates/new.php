<?php
/*  new.php: New page template 
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

/* don't let this file be loaded by the browser directly */
if (!isset($pd) || !is_object($pd)) {
    exit;
}
?>


<!-- out new page div and heading -->

<div><?php echo do_display_hook('new_page_top') ?>
    <h2 id="mailbox_title2">
        <?php echo $pd->user->str[245] ?>
    </h2>
    <?php echo do_display_hook('new_page_title_row') ?>
    <div id="edit_new_page">
        <?php echo $pd->print_edit_new_page_form() ?>
    </div>
    <div style="clear: both;">


<!-- Message controls -->

     <form method="post" id="msg_controls_form1" action="?page=new" >
        <div id="new_page_controls">
            <div class="message_controls">
                <?php echo do_display_hook('new_page_controls').$pd->print_message_controls() ?>
            </div>
        </div>


<!-- main news page table of messages -->

        <div id="new_page">
        <div><div id="new_page_inner">
            <div><?php echo $pd->print_new_content() ?></div>
        </div></div>
        </div>
       <?php echo do_display_hook('new_page_bottom') ?>
    </form>
    </div>
</div>
