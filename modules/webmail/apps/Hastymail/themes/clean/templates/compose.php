<?php
/*  compose.php: Compose page template 
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


<!-- outer compose page div -->

<div id="compose_page<?php if ($pd->pd['new_window']) { echo '_win'; } ?>"><?php echo do_display_hook('compose_top') ?>
    <form enctype="multipart/form-data" method="post" action="?page=compose&amp;compose_session=<?php echo $pd->pd['compose_session'] ?>&amp;mailbox=<?php echo urlencode($pd->pd['mailbox']).$pd->pd['new_window_arg'] ?>">

<!-- compose form -->

    <?php echo $pd->print_compose_form() ?>
    </form>
    <?php echo do_display_hook('compose_bottom') ?>
</div>
