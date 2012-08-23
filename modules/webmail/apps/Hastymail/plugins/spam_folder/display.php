<?php

/*  display.php: Plugin file responsible for the output of XHTML into existing Hastymail pages.
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

function spam_folder_folder_options_table($tools) {
    $mailbox = $tools->get_setting('spam_folder');
    $age = $tools->get_setting('spam_age_limit');
    $folder_opts = $tools->print_folder_dropdown($tools->imap_get_folders(), array($mailbox), true, false, 'selectable', array(), false, array(), true);
    $res = '<tr><td class="opt_leftcol">'.$tools->str[0].'</td><td><select name="spam_folder"><option value="">None</option>'.$folder_opts.'</select></td></tr>';
    $res .= '<tr><td class="opt_leftcol">'.$tools->str[1].'</td><td><input type="text" value="'.
        $tools->display_safe($age).'" size="5" name="spam_age_limit" /></td></tr>';
    return $res;
}

?>
