<?php
/*  contacts.php: Contacts page template 
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


<!-- Main contacts page div and heading -->

<div id="contacts_page"><?php echo do_display_hook('contacts_page_top') ?>

<!-- Contact detail if selected -->

    <?php if ($pd->pd['show_card_detail']) { echo do_display_hook('contact_detail_top').$pd->print_contact_detail().do_display_hook('contact_detail_bottom'); } ?>


<!-- quick links to the import and add/edit forms -->

        <div id="contact_links">
            <a href="?page=contacts&amp;mailbox=<?php echo urlencode($pd->pd['mailbox']) ?>#importcontact"><?php echo $pd->user->str[146] ?></a>
            <a href="?page=contacts&amp;mailbox=<?php echo urlencode($pd->pd['mailbox']) ?>#contactform"><?php echo $pd->user->str[147] ?></a>
            <a href="?page=contacts&amp;mailbox=<?php echo urlencode($pd->pd['mailbox']) ?>&amp;download_card=all"><?php echo $pd->user->str[148] ?></a>
            <a href="?mailbox=<?php echo urlencode($pd->pd['mailbox']) ?>&amp;page=contact_groups"><?php echo 'Groups' ?></a>
            <?php echo do_display_hook('contacts_quick_links') ?>
        </div>


<!-- Existing contacts list -->

    <h4>
       <?php echo $pd->pd['list_label'].' ('.$pd->pd['card_total'].')'.' &nbsp;'.$pd->user->str[321].' '.$pd->pd['contacts_page'] ?> 
    </h4>

<!-- Contacts sort form -->

    <?php echo $pd->print_sort_contacts() ?>


<!-- Contacts table -->

    <?php echo do_display_hook('existing_contacts_top') ?>
    <table id="contacts_table" cellpadding="0" cellspacing="0">
    <?php echo $pd->print_contact_list() ?>
    </table>
    <?php echo do_display_hook('existing_contacts_bottom') ?>


<!-- Import contacts form -->

    <?php echo $pd->print_import_contact() ?>


<!-- Add/edit contact form -->

    <?php echo $pd->print_vcard_form() ?>
    <?php echo do_display_hook('contacts_page_bottom') ?>
</div>
