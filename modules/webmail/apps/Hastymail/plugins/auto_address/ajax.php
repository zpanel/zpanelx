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

function ajax_auto_address_get_addys($to_string, $fld_id, $tools) {
    $max_merge = 2000;
    $token = false;
    $matching = array();
    $output = '';
    $min_chars = $tools->get_setting('auto_address_min_chars');
    if (!$min_chars) {
        $min_chars = 2;
    }
    $search_fld = $tools->get_setting('auto_address_search_fld');
    if (!$search_fld) {
        $search_fld = 3;
    }
    $chars = array();
    $chars[] = strrpos($to_string, ',');
    $chars[] = strrpos($to_string, ';');
    sort($chars);
    if ($chars[1]) {
        $token = trim(substr($to_string, $chars[1]), ';, ');
    }
    elseif ($chars[0]) {
        $token = trim(substr($to_string, $chars[0]), ';, ');
    }
    else {
        $token = trim($to_string);
    }
    if ($token) {
        $source_res = array();
        $local_source_type = $tools->get_setting('auto_address_source_type');
        $max_results = $tools->get_setting('auto_address_max_results');
        if (!$max_results) {
            $max_results = 10;
        }
        if (!$local_source_type) { 
            if (isset($_SESSION['contact_sources']) && count($_SESSION['contact_sources']) > 1) {
                foreach ($_SESSION['contact_sources'] as $vals) {   
                    if ($vals['source'] == 'ldap') {
                        $source_res[] = $tools->get_contact_list('sort_name', 1, $vals['source'], $token.'*', $max_merge, true);
                    }
                    else {
                        $source_res[] = $tools->get_contact_list('sort_name', 1, $vals['source'], '^'.$token, $max_merge, true);
                    }
                }
                $res = merge_source_res($source_res);
            }
            else {
                $res = $tools->get_contact_list('sort_name', 1, 'local', '^'.$token, $max_merge, true);
            }
        }
        else {
            $res = $tools->get_contact_list('sort_name', 1, 'local', '^'.$token, $max_merge, true);
        }
    }
    if ($token && isset($res[0]) && !empty($res[0])) {
        $token_len = strlen($token);
        foreach ($res[0] as $contact) {
            if ($search_fld == 3 || $search_fld == 1) {
                if (strtolower(substr($contact['name'], 0, $token_len)) == strtolower($token)) {
                    $matching[] = array(trim($contact['name']), trim($contact['email']));
                }
            }
            if ($search_fld == 3 || $search_fld == 2) {
                if (strtolower(substr($contact['email'], 0, $token_len)) == strtolower($token)) {
                    $found = false;
                    foreach ($matching as $vals) {
                        if (trim($vals[1]) == trim($contact['email'])) {
                            $found = true;  
                            break;
                        }
                    }
                    if (!$found) {
                        $matching[] = array(trim($contact['name']), trim($contact['email']));
                    }
                }
            }
        }
    }
    if (!empty($matching)) {
        $id = 0;
        $max = count($matching) - 1;
        $limit = $max_results - 1;
        foreach($matching as $v) {
            $output .= '<a onkeyup="select_addy_opt(event, '.$id.', \''.$fld_id.'\');" id="addy_opt'.
                $fld_id.$id.'" href="javascript:void(set_addy_val(\''.
                str_replace(array('"', "'"), array('', '\\\''), $v[0]).'\', \''.
                str_replace(array('"', "'"), array('', '\\\''), $v[1]).'\', \''.$fld_id.'\'))">'.$v[0].' '.$v[1].'</a>';
            if ($id == $limit) {
                break;
            }
            $id++;
        }
    }
    return $fld_id.'^^^^auto_addy_res^^^^'.$output.'^^^^auto_addy_res^^^^'.$token;
}
function merge_source_res($source_res) {
    $res_list = array();
    foreach ($source_res as $vals) {
        if (isset($vals[0]) && is_array($vals[0]) && !empty($vals[0])) {
            foreach ($vals[0] as $contact) {
                $res_list[] = $contact;
            }
        }
    }
    usort($res_list, 'sort_source_res');
    return array($res_list, false);
}
function sort_source_res($a, $b) {
    if (strcasecmp($a['name'], $b['name']) < 0) {
        return false;
    }
    else {
        return true;
    }
}

?>
