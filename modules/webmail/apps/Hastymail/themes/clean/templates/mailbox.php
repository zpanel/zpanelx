<?php
/*  mailbox.php: Mailbox page template 
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
global $sticky_url;
global $page_id;
$msg_list_flds = array('checkbox_cell', 'image_cell', 'subject_cell', 'indicators_cell', 'from_cell', 'date_cell', 'size_cell');
?>


<!--  mailbox heading, sort form, and meta information -->

<!-- page links (optional -->
    <?php if ($pd->pd['top_page_links']) {echo '
    <div id="page_links" class="top_page_links">
        '.$pd->pd['page_links'].'
    </div>'; }?>

<div id="mailbox_page"><?php echo do_display_hook('mailbox_top') ?>
    <?php if (!empty($pd->pd['header_list'])) { ?>


<!-- main form, main table, message controls -->

    <form method="post" id="msg_controls_form1" action="<?php echo $sticky_url ?>">
        <div id="mbx_outer">
            <div class="message_controls">
            <input type="hidden" id="page_count" name="page_count" value="<?php echo $pd->pd['page_count'] ?>" />
            <input onclick="toggle_all(); return false;" id="toggle_all_button" type="submit" name="toggle_all_button" value="X" />
                <?php echo do_display_hook('mailbox_controls_1').$pd->print_message_controls() ?>
            </div>
            <div id="mbx_inner">
                <?php echo '<complex-'.$page_id.'>'; ?>
                <table cellpadding="0" id="mbx_table" cellspacing="0" width="100%" >

<!-- mailbox table rows -->

                <?php echo '</complex-'.$page_id.'><simple-'.$page_id.'><br /></simple-'.$page_id.'>
                '.$pd->print_mailbox_list().
                '<complex-'.$page_id.'>'; ?>
                </table>
                <?php echo '</complex-'.$page_id.'>'; ?>
            </div>
            <div class="message_controls2">


<!-- bottom message controls -->

                <?php if ($pd->pd['settings']['mailbox_controls_bottom']) { echo '
                    <div class="mc_inner">'.
                    '<input onclick="toggle_all(); return false;" id="toggle_all_button" type="submit" name="toggle_all_button" value="X" />'.
                        do_display_hook('mailbox_controls_2').$pd->print_message_controls2().'
                    </div>'; }?>


<!-- search form -->
                <div id="mailbox_search_form">

                    <?php echo $pd->print_mailbox_search().do_display_hook('mailbox_search') ?>
                </div>
            </div>
    <div id="sort_form">
        <form method="get" action="">
            <?php echo $pd->print_sort_form().do_display_hook('mailbox_sort_form') ?>
        </form>
        <?php echo $pd->print_freeze(); ?>
    </div>
                <div id="track_folder"><?php echo $pd->print_track_mailbox_link() ?></div>
        </div>
    <?php echo do_display_hook('mailbox_bottom') ?>
    </form>
    <?php } else { 

/* empty mailbox */
    
    echo '
    <div id="empty_mailbox">
        '.$pd->user->str[432].': '.$pd->pd['mailbox_dsp'].' 
    </div>';
    } ?>
</div>
