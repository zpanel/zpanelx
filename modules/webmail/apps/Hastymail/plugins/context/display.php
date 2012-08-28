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

function context_message_headers_bottom($tools) {
    global $page_id;
    require($tools->include_path.'settings.php');
    $data = '<complex-'.$page_id.'><tr><td></td><td colspan="2" id="context_row" style="padding: 5px; padding-bottom: 8px; padding-left: 0px;"><div style="display: none;" id="context_store"></div>';
    foreach ($context_btn as $index => $btn) {
        $data .= '<input type="submit" title="'.$tools->str[$btn['title']].'" onmousedown="context_search('.$index.')" id="'.$btn['id'].'" name="'.$btn['id'].
            '" class="disabled_button" style="font-size: 8pt;" disabled="disabled" value="'.$btn['value'].'" /> ';
    }
    $data .= '<input type="submit" onclick="return false" title="'.$tools->str[5].'" value="?" style="background: none; border: none; padding: 0px; color: #999; font-size: 90%; background-color: transparent;" /> &#160;';
    $data .= '</td></tr></complex-'.$page_id.'>';
    return $data;
}

?>
