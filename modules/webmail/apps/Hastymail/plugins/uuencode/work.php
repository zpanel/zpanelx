<?php

/*  work.php
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
function uuencode_page_end($tools) {
    require_once($tools->include_path.'include.php');
    if ($tools->get_page() == 'message' && strstr($tools->get_url(), 'uuencode=1')) {
        if (preg_match('/filename=([^&]+)/', $tools->get_url(), $matches)) {
            $filename = $matches[1];
            $attachment = get_attachments($tools->get_current_message(), $filename);
            $data = '';
            if (isset($attachment[$filename])) {
                if (function_exists('convert_uudecode')) {
                    $data = convert_uudecode($attachment[$filename]);
                }
                else {
                    $data = manual_convert_uudecode($attachment[$filename]);
                }
            }
            if ($data) {
                header("Content-Type: application/octet-stream");
                header("Pragma: public");
                header("Expires: 0");
                header('Cache-Control: must-revalidate');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                ob_end_clean();
                echo $data;
                exit;
            }
            $tools->send_notice('Could not download attachment');
        }
    }
}
?>
