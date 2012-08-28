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

function login_alias_on_login($tools) {

    $username = $_POST['user'];
    $alias = get_alias($username, $tools);
    if ($alias) {
        $_POST['user'] = $alias;
    }
}
function get_alias($username, $tools) {
    require($tools->include_path.'settings.php');
    $alias = false;
    switch ($storage_type) {
        case 'file':
            $alias = file_lookup($username, $file_location, $tools);
            break;
        case 'db':
            $alias = db_lookup($username, $table_name, $tools);
            break;
    }
    return $alias;
}
function db_lookup($user, $table, $tools) {
    if ($tools->get_db()) {
        $res = $tools->db_query('select imap_name from '.$table.' where login_name='.$tools->db_quote($user));
        if (isset($res[0]['imap_name'])) {
            return $res[0]['imap_name'];
        }
    }
    return false;
}
function file_lookup($user, $file, $tools) {
    if (is_readable($file)) {
        $file_lines = file($file);
        foreach ($file_lines as $line) {
            if (strpos($line, ' ') !== false) {
                list($user_name, $imap_name) = split(' ', $line, 2);
                if (trim($user_name) == $user) {
                    return trim($imap_name);
                }
            }
        }
    }
    return false;
}
?>
