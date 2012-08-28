<?php
/*  options.php: Options page template 
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


<!-- title and outer div -->

<div id="options_page"><?php echo do_display_hook('options_page_top') ?>
<h2 id="mailbox_title2">
    <?php echo $pd->user->str[4] ?>
</h2>
<?php echo do_display_hook('options_page_title_row') ?>
<div id="options_form">
    <form method="post" action="?page=options">
        <?php echo do_display_hook('options_form_top') ?>


<!-- general options -->

        <h4 style="clear: both;"><?php echo $pd->user->str[187] ?></h4>
        <table cellpadding="0" cellspacing="0" class="options_table">
            <?php echo $pd->print_general_options() ?>
        </table>


<!-- folder options -->

        <h4><?php echo $pd->user->str[188] ?></h4>
        <table cellpadding="0" cellspacing="0" class="options_table">
            <?php echo $pd->print_folder_options() ?>
        </table>


<!-- message view options -->

        <h4><?php echo $pd->user->str[189] ?></h4>
        <table cellpadding="0" cellspacing="0" class="options_table">
            <?php echo $pd->print_message_view_options() ?>
        </table>


<!-- mailbox view options -->

        <h4><?php echo $pd->user->str[190] ?></h4>
        <table cellpadding="0" cellspacing="0" class="options_table">
            <?php echo $pd->print_mailbox_options() ?>
        </table>


<!-- new mail page options -->

        <h4><?php echo $pd->user->str[191] ?></h4>
        <table cellpadding="0" cellspacing="0" class="options_table">
            <?php echo $pd->print_new_page_options() ?>
        </table>


<!-- compose page options -->

        <h4><?php echo $pd->user->str[192] ?></h4>
        <table cellpadding="0" cellspacing="0" class="options_table">
            <?php echo $pd->print_compose_options() ?>
        </table>
    </form>
    <div id="footnote">
         <span class="js1">*</span>  &#160;&#160;&#160;<?php echo $pd->user->str[194] ?><br />
         <span class="js2">**</span> <?php echo $pd->user->str[195] ?><br /> 
    </div>
</div>
<?php echo do_display_hook('options_page_bottom') ?>
</div>
