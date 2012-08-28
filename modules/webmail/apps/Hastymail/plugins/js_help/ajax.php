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
function ajax_js_help_get_help($opt_name, $tools) {
    $str = $tools->get_hm_strings();
    $opt_name2 = $tools->display_safe($opt_name);
    $opt_name3 = $tools->display_safe(decode_unicode_url($opt_name), 'UTF-8');
    $opt_name4 = decode_unicode_url($opt_name);
    $trans = array_keys($tools->str);
    $res = '';
    foreach ($trans as $i) {
        if (strstr($i, 'plugin')) {
            continue;
        }
        $v = $str[$i];
        if (matches($opt_name, $v) || matches($opt_name2, $v) || matches($opt_name3, $v)) {
            $res = sprintf($tools->str[$i], '<b>'.$v.'</b>');
            break;
        }
    }
    if (!$res) {
        $str_list = array();
        $plugin_vals = $tools->get_from_global_store('help_strings');
        foreach ($plugin_vals as $plugin => $vals) {
            if (isset($tools->str[$plugin.'_plugin'])) {
                foreach ($vals as $i => $v) {
                    if (matches($opt_name, $v) || matches($opt_name2, $v) ||
                        matches($opt_name3, $v) || matches($opt_name4, $v)) {
                        if (isset($tools->str[$plugin.'_plugin'][$i])) {
                            $res = sprintf($tools->str[$plugin.'_plugin'][$i], '<b>'.$v.'</b>');
                        }
                    }
                }
            }
        }
        if (!$res) {
            $res = 'Could not find that setting!';
        }
    }
    return $res;
}
function matches($string, $test_string) {
    if (trim($string) == trim($test_string)) {
        return true;
    }
    return false;
}
?>
