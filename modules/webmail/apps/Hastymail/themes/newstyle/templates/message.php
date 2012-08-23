<?php
/*  message.php: Message page template 
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


<!-- message page container, heading and meta info -->

<div id="mailbox_name">
    <?php echo $pd->pd['frozen_dsp'].$pd->user->str[322].'&#160; <b>'.($pd->pd['uid_index'] + 1).'</b> / '.$pd->pd['mailbox_total'].'
    &#160;&#160; '.$pd->user->str[321].' <b>'.$pd->pd['mailbox_page'].'</b>'.do_display_hook('message_meta') ?>
</div>
<div id="message_page"><?php echo do_display_hook('message_top') ?>

    <!-- previous and next dropdowns -->

    <div id="mailbox_title">
        <div id="prev_next">
            <?php echo $pd->print_message_prev_next() ?>
        </div>
    </div>

    <!-- message headers -->
    <div id="message_headers">
        <table cellpadding="0" cellspacing="0" width="100%">
        <?php echo $pd->print_message_headers() ?>


<!-- prev/up/next image links and option links -->

        <tr>
            <td>
            <div id="small_prev_next">
                <?php echo $pd->print_message_prev_next_small() ?>
            </div>
            </td>
            <td colspan="2">
            <div class="message_links">
                <?php echo $pd->print_message_links() ?>
            </div>
            </td>
        </tr>
        <?php echo do_display_hook('message_headers_bottom') ?>
        </table>
    </div>

<!-- individual message part headers (if any) -->

    <?php if (isset($pd->pd['message_part_headers'])) { echo '
    <div id="message_part_headers">
        '.$pd->print_part_headers().'
    </div>';
    } ?>

<!-- message body -->

      <?php echo $pd->print_message_body() ?>

<!-- message parts section -->

    <div id="message_parts">
        <a name="parts"></a>
        <h4 id="parts_heading">
            Message Parts
        </h4>
        <table cellpadding="0" cellspacing="0">
            <?php echo $pd->print_message_parts() ?>
    </table>


<!-- links to pages of the mailbox  -->

    <div id="page_links">
        <?php if (!$pd->new_window) { echo $pd->pd['page_links']; } ?>
    </div>
    </div>
    <?php echo do_display_hook('message_bottom') ?>
</div>
