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
    <?php if ($pd->pd['top_page_links']) {echo '
    <div id="page_links" class="top_page_links">
        '.$pd->pd['page_links'].'
    </div>'; }?>

<div id="mailbox_page"><?php echo do_display_hook('mailbox_top') ?>
    <h2 id="mailbox_title">
        <?php echo $pd->pd['mailbox_dsp'].'
        <span id="mailbox_meta">
            '.$pd->pd['frozen_dsp'].' '.$pd->user->str[41].': <b>'.$pd->pd['mailbox_total'].'</b>, '.$pd->user->str[40].': <b>'.$pd->pd['mailbox_page'].'</b>
            &nbsp;('.$pd->pd['mailbox_range'].')<span class="folder_unread"> '.$pd->user->str[34].' <b>'.$pd->pd['folder_unread'].'</b></span>
        </span>'.do_display_hook('mailbox_meta') ?>
    </h2>
    <div id="sort_form">
        <form method="get" action="">
            <?php echo $pd->print_sort_form().do_display_hook('mailbox_sort_form') ?>
        </form>
        <?php echo $pd->print_freeze(); ?>
    </div>
    <?php if (!empty($pd->pd['header_list'])) { ?>


<!-- main form, main table, message controls -->

    <form method="post" id="msg_controls_form1" action="<?php echo $sticky_url ?>">
        <div id="mbx_outer">
            <div class="message_controls">
                <?php echo do_display_hook('mailbox_controls_1').$pd->print_message_controls() ?>
            </div>
            <div id="mbx_inner">
                <table cellpadding="0" id="mbx_table" cellspacing="0" width="100%" >
                    <tr>


<!-- mailbox list headers -->
    
                        <?php echo $pd->print_mailbox_list_headers() ?>
                    </tr>
    

<!-- mailbox table rows -->

                <?php echo $pd->print_mailbox_list() ?>
                </table>
            </div>
            <div class="message_controls2">
                <div id="mailbox_search_form">


<!-- bottom message controls -->

                <?php if ($pd->pd['settings']['mailbox_controls_bottom']) { echo '
                    <div class="mc_inner">'.
                        do_display_hook('mailbox_controls_2').$pd->print_message_controls2().'
                    </div>'; }?>


<!-- search form -->

                    <?php echo $pd->print_mailbox_search().do_display_hook('mailbox_search') ?>
                    <div id="track_folder"><?php echo $pd->print_track_mailbox_link() ?></div>
                </div>
            </div>
        </div>
    <?php echo do_display_hook('mailbox_bottom') ?>
    </form>


<!-- Links to Mailbox pages if any -->

    <div id="page_links">
        <?php echo $pd->pd['page_links'] ?>
    </div>

    <?php } else { 


/* empty mailbox */
    
    echo '
    <div id="empty_mailbox">
        '.$pd->pd['mailbox_dsp'].' is empty 
    </div>';
    } ?>
</div>
