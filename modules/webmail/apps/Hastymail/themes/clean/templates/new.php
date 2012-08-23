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
global $page_id;
global $msg_list_flds;
$msg_list_flds = array('checkbox_cell', 'image_cell', 'subject_cell', 'indicators_cell', 'from_cell', 'date_cell', 'size_cell');
?>

<!-- out new page div and heading -->

<div>
<?php echo do_display_hook('new_page_top') ?>
<div>

<!-- Message controls -->

     <form method="post" id="msg_controls_form1" action="?page=new" >
            <div class="message_controls">
                <!-- <input type="hidden" id="page_count" name="page_count" value="<?php echo $pd->pd['grand_total'] ?>" />-->
                <input onclick="toggle_all(); return false;" id="toggle_all_button" type="submit" name="toggle_all_button" value="X" />
                <?php echo do_display_hook('new_page_controls').$pd->print_message_controls() ?>
            </div>


<!-- main news page table of messages -->

        <div id="new_page">
            <div>
                <div id="new_page_inner">
                    <div><?php echo $pd->print_new_content($msg_list_flds, true, false) ?></div>
                </div>
            </div>
        </div>
        <div id="edit_new_page">
            <?php echo $pd->print_edit_new_page_form() ?>
        </div>
       <?php echo do_display_hook('new_page_bottom') ?>
    </form>
    </div>
</div>
