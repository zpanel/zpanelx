<?php
/*  folders.php: Folders page template 
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
}?>


<!-- outer folders div -->

<div id="folder_page"><?php echo do_display_hook('folders_page_top') ?>
    <h2 id="mailbox_title2"><?php echo $pd->user->str[7] ?></h2>


<!-- folder controls (create/delete/rename) -->

    <h4 style="clear:both;"><?php echo $pd->user->str[253] ?></h4>
    <form method="post" action="?page=folders">
        <table id="folder_controls" cellpadding="0" cellspacing="0">
            <?php echo $pd->print_folder_controls() ?>
        </table><?php echo do_display_hook('folder_controls_bottom') ?><br />


<!-- folder options table -->

        <h4 style="clear:both;"><?php echo $pd->user->str[4] ?></h4>
        <?php echo do_display_hook('folder_options_top') ?>
        <input type="hidden" id="page_count" name="page_count" value="<?php echo count($pd->pd['folders']) ?>" />
        <table style="clear: both;" cellpadding="0" cellspacing="0" id="folder_options_table">
        <tr><td colspan="4" align="right"><input type="submit" value="<?php echo $pd->user->str[193] ?>" name="update_folder_options" /></td></tr>
        <tr><th><?php echo $pd->user->str[256] ?></th><th><?php echo $pd->user->str[257] ?>
            <a style="padding-left: 5px;" href="javascript:toggle_all(false, false, 'hidden_')">X</a></th>
            <th><?php echo $pd->user->str[258] ?>
            <a style="padding-left: 5px;" href="javascript:toggle_all(false, false, 'check_for_new_')">X</a></th>
            <th><?php echo $pd->user->str[259] ?></th></tr>
            <?php echo $pd->print_folder_page_options() ?>
        <tr><td colspan="4" align="right"><input type="submit" value="<?php echo $pd->user->str[193] ?>" name="update_folder_options" /></td></tr>
        </table>
        <?php echo do_display_hook('folder_options_bottom') ?>
    </form>
    <?php echo do_display_hook('folders_page_bottom') ?>
</div>
