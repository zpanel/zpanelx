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
?>


<!--  mailbox heading, sort form, and meta information -->

<!-- page links (optional -->

<div id="mailbox_page"><?php echo do_display_hook('mailbox_top') ?>
    <?php if (!empty($pd->pd['header_list'])) { ?>


<!-- main form, main table, message controls -->

        <div id="mbx_outer">
                <table cellpadding="0" cellspacing="0" id="mbx_top" width="100%" >
                    <tr>


<!-- Toggle all link -->
    
                        <th colspan="5" class="mailbox_header_left">
                            <?php global $page_id; echo '<complex-'.$page_id.'>
                            <input type="hidden" id="page_count" name="page_count" value="'.$pd->pd['page_count'].'" />
                            <input onclick="toggle_all(); return false;" id="toggle_all_button" type="submit" name="toggle_all_button" value="X" />
                            </complex-'.$page_id.'>' ?>
    <div id="sort_form">
        <form method="get" action="">
            <?php echo $pd->print_sort_form().do_display_hook('mailbox_sort_form') ?>
        </form>
    </div>


<!-- search form -->

                <div id="mailbox_search_form">
                    <?php echo $pd->print_mailbox_search().do_display_hook('mailbox_search') ?>
                </div>
    </th>
                    </tr>
    <?php if ($pd->pd['top_page_links']) {echo '
    <tr><th class="tplinks"> 
    <div id="page_links" class="top_page_links">
        '.$pd->pd['page_links'].'
    </div></th></tr>'; }?>
                </table>

            <form method="post" id="msg_controls_form1" action="<?php echo $sticky_url ?>">
            <div class="message_controls">
                    <div class="mc_inner">
                <?php echo do_display_hook('mailbox_controls_1').$pd->print_message_controls() ?>
                    </div>
            </div>

            <div id="mbx_inner">
                <table cellpadding="0" id="mbx_table" cellspacing="0" width="100%" >

<!-- mailbox table rows -->

                <?php echo $pd->print_mailbox_list() ?>
                </table>

<!-- bottom message controls -->

                <?php if ($pd->pd['settings']['mailbox_controls_bottom']) { echo '
            <div class="message_controls2">
                    <div class="mc_inner">'.
                        do_display_hook('mailbox_controls_2').$pd->print_message_controls2().'
                    </div></div>'; }?>
        <?php echo '
    </div>
        </form>
        <div class="btm_row">
        <span id="mailbox_meta">
            '.$pd->pd['frozen_dsp'].' '.$pd->user->str[41].': <b>'.$pd->pd['mailbox_total'].'</b>, '.$pd->user->str[40].': <b>'.$pd->pd['mailbox_page'].'</b>
            &nbsp;('.$pd->pd['mailbox_range'].')<span class="folder_unread"> '.$pd->user->str[34].' <b>'.$pd->pd['folder_unread'].'</b></span>
        </span>'.do_display_hook('mailbox_meta') ?>
        <!-- <?php echo $pd->print_freeze(); ?>
                    <div id="track_folder"><?php echo $pd->print_track_mailbox_link() ?></div> -->
    <div id="page_links">
        <?php echo $pd->pd['page_links'] ?>
    </div>
    </div>
        </div>
    <?php echo do_display_hook('mailbox_bottom') ?>


<!-- Links to Mailbox pages if any -->


    <?php } else { 


/* empty mailbox */
    
    echo '
    <div id="empty_mailbox">
        '.$pd->user->str[432].': '.$pd->pd['mailbox_dsp'].' 
    </div>';
    } ?>
</div>
