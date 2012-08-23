<?php
/*  search.php: Search page template 
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
<div id="search_page"><?php echo do_display_hook('search_page_top') ?>
    <h2 id="mailbox_title2">
        <?php echo $pd->user->str[9] ?>
        <span id="mailbox_meta">
            &#160;<?php echo $pd->user->str[89].': '.$pd->pd['search_total'] ?>
            &#160; &#160;<a href="?page=search&amp;mailbox=<?php echo urlencode($pd->pd['mailbox']) ?>&amp;reset_results=1"><?php echo $pd->user->str[91] ?></a>
            &#160; &#160;<?php if ($pd->pd['search_total'] > 15) echo '<a href="'.$sticky_url.'#search_form">Search Form</a>' ?>
        </span>
        <?php echo do_display_hook('search_result_meta') ?>
    </h2>
    <div style="clear: both;">


<!-- Message controls -->

        <form method="post" action="?page=search" >
            <?php if (!empty($pd->pd['search_results'])) { ?>
            <div id="search_page_controls">
                <div class="message_controls">
                    <?php echo $pd->print_message_controls() ?>
                    <?php echo do_display_hook('search_result_controls') ?>
                </div>
            </div>
            <?php } ?>
            <div id="search_res">
                <?php echo $pd->print_search_res() ?>
            </div>
            <?php echo do_display_hook('search_result_bottom') ?>
        </form>
    <div id="search_form">
        <form method="post" action="?page=search">
            <?php echo do_display_hook('search_form_top') ?>
            <?php echo $pd->print_search_form() ?>
            <?php echo do_display_hook('search_form_bottom') ?>
        </form>
    </div>
    </div>
    <?php echo do_display_hook('search_page_bottom') ?>
</div>
