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

function ldap_addressbook_register_contacts_source($tools) {
    require($tools->include_path.'settings.php');
    $tools->register_contacts_source($contact_label, 'ldap');
}
function ldap_addressbook_compose_contact_list($tools, $args) {
    require($tools->include_path.'settings.php');
    global $user;
    if (isset($args[0]) && trim($args[0])) {
        $filter = $args[0];
    }
    else {
        $filter = '';
    }
    if (isset($args[1]) && $args[1]) {
        $filter_literal = true;
    }
    else {
        $filter_literal = false;
    }
    $res = array();
    if (!function_exists('ldap_connect')) {
        $tools->send_notice('No server LDAP support, disabling ldap addressbook plugin');
        return;
    }
    if ($ldap_ssl) {
        $ldap_server = 'ldaps://'.$ldap_server;
    }

    # WISC - make sure that we can actually connect (and get a response)
    $fp = fsockopen($ldap_server, $ldap_port, $errno, $errstr, 1);
    if (!$fp) {
        $tools->send_notice('Problem connecting to LDAP server');
        return;
    } 
    fclose($fp);
    # end WISC customization

    if ($ldap_server && $ldap_port && ($ldapp = ldap_connect($ldap_server, $ldap_port))) {
        $bind = false;
        if ($ldap_version_3) {
            ldap_set_option($ldapp, LDAP_OPT_PROTOCOL_VERSION, 3);
        }
        if ($ldap_auth) {
            if (isset($rdn_format) && trim($rdn_format)) {
                $ldap_rdn = str_replace(array('%u', '%b'), array($_SESSION['user_data']['username'],
                            $ldap_base_dn), $rdn_format);
            }
            else {
                $ldap_rdn = $_SESSION['user_data']['username'];
            }
            if ($ldap_bind_pass) {
                $ldap_pass = $ldap_bind_pass;
            }
            else {
                $pass_bits = $user->string_decrypt($_SESSION['user_data']['pass']);
                if (is_array($pass_bits) && isset($pass_bits[1])) {
                    $ldap_pass = $pass_bits[1];
                }
            }
            if ($ldap_pass) {
                $bind = @ldap_bind($ldapp, $ldap_rdn, $ldap_pass);
            }
        }
        else {
            $bind = @ldap_bind($ldapp);
        }
        if ($bind) {
            if ($filter) {
                if ($filter_literal) {
                    $ldap_search_filter = '(|(cn='.$filter.')(mail='.$filter.')(sn='.$filter.'))';
                }
                else {
                    $filter = str_replace(array('*', '(', ')', "\\", "\0"),
                          array('\0x2a', '\0x28', '\0x29', '\0x5c', '\0x00'), $filter);
                    $ldap_search_filter = '(|(cn=*'.$filter.'*)(mail=*'.$filter.'*)(sn=*'.$filter.'*))';
                }
            }
            else {
                $ldap_search_filter = '(mail=*)';
            }
            if ($ldap_search_term) {
                    $ldap_search_filter = '(&('.$ldap_search_term.')'.$ldap_search_filter.')';
            }
            $flds = $ldap_name_flds;
            array_push($flds, 'mail');
            ldap_set_option($ldapp, LDAP_OPT_REFERRALS, 0);
            $ldap_attr = @ldap_search($ldapp, $ldap_base_dn, $ldap_search_filter, $flds);
            $info = @ldap_get_entries($ldapp, $ldap_attr);
            ldap_close($ldapp);
            if (is_array($info)) {
                array_shift($info);
                foreach ($info as $array) {
                    if (isset($array['mail']) && !empty($array['mail'])) {
                        foreach ($array['mail'] as $index => $val) {
                            if (is_numeric($index) && strstr($val, '@')) {
                                $name = '';
                                foreach ($ldap_name_flds as $id) {
                                    if (isset($array[$id][0])) {
                                        $name .= ' '.$array[$id][0];
                                    }
                                } 
                                $res[] = array('source' => 'ldap', 'email' => $val, 'name' => trim($name));
                            }
                        }
                    }
                }
            }
        }
        if (is_resource($ldapp)) {
            ldap_close($ldapp);
        }
    }
    if (!empty($res)) {
        $tools->merge_contacts_source($res);
    }
}
?>
