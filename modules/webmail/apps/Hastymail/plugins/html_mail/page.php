<?php
/*  page.php: Plugin file responsible for handling plugin specific pages 
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

function url_action_html_mail($tools, $get, $post) {
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }
    $pd = array();
    $pd['size'] = $tools->get_setting('html_font_size');
    $pd['family'] = $tools->get_setting('html_font_family');
    $data = '';
    if ($pd['size']) {
        $data .= '
        body.mceContentBody {
            font-size: '.$pd['size'].';
        }';
    }
    if ($pd['family']) {
        $data .= '
        body.mceContentBody {
            font-family: '.$pd['family'].';
        }';
    }
    $data .= 'p{padding: 1px; margin: 1px;}';
    header('Content-type: text/css');
    echo $data;
    exit;
}
?>
