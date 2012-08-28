<?php
/*  thread_view.php: Thread view template
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
global $msg_list_flds;
?>


<div id="thread_view">
    <div id="mbx_outer">
    <form method="post" action="<?php echo $sticky_url ?>">
        <div class="message_controls">
            <?php echo $pd->print_message_controls() ?>
        </div>
        <div id="mbx_inner">
            <table cellpadding="0" id="mbx_table" cellspacing="0" width="100%" >
                <tr>
                    <?php echo $pd->print_mailbox_list_headers($msg_list_flds) ?>
                </tr>
                <?php echo $pd->print_mailbox_list($msg_list_flds) ?>
            </table>
        </div>
    </form>
    </div>
</div>
