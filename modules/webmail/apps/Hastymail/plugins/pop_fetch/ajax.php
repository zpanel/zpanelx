<?php

/*  ajax.php: Plugin file responsible for handling ajax callbacks
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
function ajax_pop_fetch_fetch_mail($method, $tools) {
    require_once($tools->include_path.'pop3_class.php');
    require_once($tools->include_path.'settings.php');
    $accounts = $tools->get_setting('pop_fetch_accounts');
    $uid_map = get_pop_fetch_map($tools, $uid_map_dir);
    $map_sig = md5(serialize($uid_map));
    $res = 0;
    foreach ($accounts as $vals) {
        list($cnt, $uid_map) = pop3_session($tools, $vals[6], $vals[7], $vals[3], $vals[2], $vals[4], $vals[5], $vals[9], $vals[8], $vals[0], $uid_map);
        $res += $cnt;
    }
    if (md5(serialize($uid_map)) != $map_sig) {
        save_pop_fetch_map($tools, $uid_map, $uid_map_dir);
    }
    return $method.'^^^'.$res;
}
?>
