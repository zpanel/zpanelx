<?php

/*  work.php: Plugin file responsible for the backend processing
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
function imap_domain_map_logged_out_init($tools) {
    if (!$tools->logged_in()) {
        $tools->add_style('<style type="text/css">.alt_server{display: none;}</style>');
    }
}
function imap_domain_map_on_login($tools) {
    $imap_server = false;
    if (isset($_POST['user']) && strstr($_POST['user'], '@') &&
        strpos($_POST['user'], '@') < strlen($_POST['user'])) {
        $domain = substr($_POST['user'], (strpos($_POST['user'], '@') + 1));
        require($tools->include_path.'settings.php');
        foreach ($server_map as $server => $vals) {
            if (in_array($domain, $vals)) {
                $imap_server = $server;
                break;
            }
        }
        if ($imap_server) {
            if (isset($strip_domain) && $strip_domain) {
                $_POST['user'] = substr($_POST['user'], 0, (-1 - strlen($domain)));
            }
            $_POST['imap_server'] = $imap_server;
        }
    }
}
?>
