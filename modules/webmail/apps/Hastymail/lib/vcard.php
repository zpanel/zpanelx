<?php

/*  vcard.php: vcard class for handling contacts
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

class vcard {
    var $storage_type;
    var $card_properties;
    var $sort_fld;
    var $search_terms;
    var $card_list;
    var $card_groups;
    var $card_total;

    function vcard() {
        $this->sort_fld = false;
        $this->search_terms = false;
        $this->storage_type = 'file';
        $this->card = array();
        $this->card_list = array();
        $this->card_groups = array();
        $this->card_total = 0;
    }
    /* return a list of ids/names/emails */
    function get_quick_list($sort='sort_name', $page=1, $source='local', $filter=false, $page_size=false, $filter_regex=false, $groups=true) {
        global $contacts_per_page;
        if (!$page_size) {
            $page_size = $contacts_per_page;
        }
        $_SESSION['contact_sources'] = array(array('title' => 'Personal Addressbook', 'source' => 'local'));
        do_work_hook('register_contacts_source');
        if (!$filter && isset($_SESSION['active_contact_source']) && $_SESSION['active_contact_source'] === $source && 
            isset($_SESSION['contact_list']) && isset($_SESSION['contact_list_page']) && $_SESSION['contact_list_page'] == $page) {
            $res = $_SESSION['contact_list'];
        }
        else {
            $res = array();
            if (empty($this->card_list)) {
                $this->get_card_list();
            }
            foreach ($this->card_list as $id => $vals) {
                $email = array();
                $last_name = '';
                $name = '';
                foreach ($vals as $atts) {
                    if ($atts['name'] == 'EMAIL') {
                        $email[] = $atts['value'];
                    }
                    if ($atts['group'] == 'N' && $atts['name'] == 'FAMILY') {
                        $lastname = $atts['value'];
                    }
                    if ($atts['name'] == 'FN') {
                        $name = $atts['value'];
                    }
                }
                if (!empty($email)) {
                    foreach ($email as $v) {
                        $res[] = array('source' => 'local', 'email' => $v, 'last_name' => $last_name, 'name' => $name, 'id' => $id);
                    }
                }
            }
            if ($groups && isset($this->card_groups) && is_array($this->card_groups)){
                foreach ($this->card_groups as $v) {
                    $res[] = array('source' => 'local', 'email' => '&lt;'.join('&gt;, &lt;', $this->get_group_email_list($v)).'&gt;', 'name' => $v, 'id' => $v);
                }
            }
            $_SESSION['quick_list'] = array();
            if ($filter) {
                foreach ($res as $vals) {
                    if ($filter_regex) {
                        if (preg_match("/".$filter."/i", $vals['email']) || preg_match("/".$filter."/i", $vals['name'])) {
                            $_SESSION['quick_list'][] = $vals;
                        }
                    }
                    else {
                        if (stristr($vals['email'], $filter) || stristr($vals['name'], $filter)) {
                            $_SESSION['quick_list'][] = $vals;
                        }
                    }
                }
            }
            else {
                $_SESSION['quick_list'] = $res;
            }
            if ($source !== 'local') {
                do_work_hook('compose_contact_list', array($filter, $filter_regex));
            }
            $res = $_SESSION['quick_list'];
            unset($_SESSION['quick_list']);
            if ($source) {
                $temp = array();
                foreach ($res as $vals) {
                    if ($vals['source'] == $source) {
                        $temp[] = $vals;
                    }
                }
                $res = $temp;
            }
            usort($res, array($this, $sort));
            $total = count($res);
            $_SESSION['contact_list_total'] = $total;
            if ($page > 1) {
                $res = array_slice($res, ($page_size*($page - 1)), $page_size);
            }
            elseif ($page) {
                $res = array_slice($res, 0, $page_size);
            }
        }
        $_SESSION['contact_list'] = $res;
        $_SESSION['contact_list_page'] = $page;
        $_SESSION['active_contact_source']= $source;
        if (!isset($total)) {
            $total = $_SESSION['contact_list_total'];
        }
        return array($res, $total);
    }
    function sort_last_name($a, $b) {
        $val = strcasecmp($a['last_name'], $b['last_name']);
        if ($val == 0) {
            return 1;
        }
        else {
            return $val;
        }
    }
    function sort_email($a, $b) {
        $val = strcasecmp($a['email'], $b['email']);
        if ($val == 0) {
            return 1;
        }
        else {
            return $val;
        }
    }
    function sort_name($a, $b) {
        $val = strcasecmp($a['name'], $b['name']);
        if ($val == 0) {
            $val = strcasecmp($a['email'], $b['email']);
        }
        if ($val == 0) {
            return 1;
        }
        else {
            return $val;
        }
    }
    function get_card_list_file() {
        global $conf;
        global $fd;
        $settings_path = $conf['settings_path'];
        $data = array();
        $username = $_SESSION['user_data']['username'];
        if (substr($settings_path, -1) != $fd) {
            $filename = $settings_path.$fd.$username.'.contacts';
        }
        else {
            $filename = $settings_path.$username.'.contacts';
        }
        if (is_readable($filename)) {
            $data = trim(implode('', file($filename)));
            $data = @unserialize($data);
        }
        return $data;
    }
    function get_card_list_db() {
        global $dbase;
        global $user;
        $data = array();
        if (!is_object($dbase)) {
            $user->errors[] = $user->str[393];
        }
        else {
            $sql = 'select * from contacts where username='.$dbase->qt($user->username);
            $res = $dbase->select($sql);
            if (isset($res[0]['contacts'])) {
                $data = unserialize($res[0]['contacts']);
            }
        }
        return $data;
    }
    function get_group_email_list($group) {
        $emails = array();
        $groups = $this->get_group_members($group);
        if (isset($groups[$group])) {
            $members = $groups[$group];
        }
        else {
            return array();
        }
        foreach ($members as $card) {
            foreach ($card as $flds) {
                if ($flds['group'] == 'A' && $flds['name'] == 'EMAIL' && $flds['value']) {
                    $emails[] = $flds['value'];
                }
            }
        }
        return $emails;
    }
    function get_group_members($group=false) {
        $groups = array();
        foreach ($this->card_list as $i => $card) {
            foreach ($card as $flds) {
                if ($flds['name'] == 'GROUP') {
                    if ($group) {
                        if ($group == $flds['value']) {
                            $groups[$group][$i] = $card;
                        }
                    }
                    else {
                        $groups[$flds['value']][$i] = $card;
                    }
                }
            }
        }
        return $groups;
    }
    function remove_group_members($group) {
        $new_cards = array();
        foreach ($this->card_list as $id => $card) {
            $new_card = array();
            foreach ($card as $flds) {
                if ($flds['name'] == 'GROUP' && $flds['value'] == $group) {
                    continue;
                }
                else {
                    $new_card[] = $flds;
                }
            }
            $new_cards[$id] = $new_card;
        }
        $this->card_list = $new_cards;
    }
    function update_group_members($group, $ids) {
        if (empty($ids)) {
            return;
        }
        $new_cards = array();
        foreach ($this->card_list as $id => $card) {
            $new_card = $card;
            if (in_array($id, $ids)) {
                $new_card[] = array('group' => '', 'name' => 'GROUP', 'value' => $group, 'properties' => array());
            }
            $new_cards[$id] = $new_card;
        }
        $this->card_list = $new_cards;
    }
    function get_card_list($sort=false, $page=0) {
        global $conf;
        global $contacts_per_page;
        if (isset($conf['site_contacts_storage']) && $conf['site_contacts_storage'] == 'db') {
            $data = $this->get_card_list_db();
        }
        else {
            $data = $this->get_card_list_file();
        }
        if (is_array($data)) {
            if (count($data) == 2) {
                if (!is_array($data[0])) {
                    $data[0] = array();
                }
                $this->card_groups = $data[0];
                $this->card_list = $data[1];
            }
            else {
                $this->card_list = $data;
            }
        }
        if ($this->search_terms) {
            $filter_vals = array();
            foreach ($this->card_list as $i => $vals) {
                foreach($vals as $flds) {
                    if (($flds['name'] == 'EMAIL' && stristr($flds['value'], $this->search_terms)) ||
                        ($flds['name'] = 'FN' && stristr($flds['value'], $this->search_terms)) ) {
                        $filter_vals[$i] = $vals;
                        break;
                    }
                }
            }
            $this->card_list = $filter_vals;
        }
        if ($this->sort_fld) {
            uasort($this->card_list, array($this, 'full_sort_cards'));
        }
        $this->card_total = count($this->card_list);
        if ($page) {
            if ($page > 1) {
                $start = $contacts_per_page*($page -1);
                $offset = $contacts_per_page;
            }
            elseif ($page) {
                $start = 0;
                $offset = $contacts_per_page;
            }
            $temp = array();
            $i = 0;
            foreach ($this->card_list as $index => $vals) {
                $i++;
                if ($i > $start && $i <= ($start + $offset)) {
                    $temp[$index] = $vals;
                }
                elseif ($i > ($start + $offset)) {
                    break;
                }
            }
            $this->card_list = $temp;
            unset($temp);
        }
        $this->sanitize();
    }
    function sanitize() {
        $size = 0;
        $res = array();
        foreach ($this->card_list as $index => $vals) {
            $new_atts = array();
            foreach ($vals as $atts) {
                $size += count($atts);
                if (trim($atts['value'])) {
                    $new_atts[] = $atts;
                }
            }
            $res[$index] = $new_atts;
        }
        $this->card_list = $res;
    }
    function full_sort_cards($a, $b) {
        $c_1 = false;
        $c_2 = false;
        foreach ($a as $vals) {
            if ($vals['name'] == $this->sort_fld) {
                $c_1 = $vals['value'];
                break;
            }
        }
        foreach ($b as $vals) {
            if ($vals['name'] == $this->sort_fld) {
                $c_2 = $vals['value'];
            }
        }
        $val = strcasecmp($c_1, $c_2);
        if ($val == 0) {
            return 1;
        }
        else {
            return $val;
        }
    }
    /* import multiple cards */
    function import_multiple_cards($lines) {
        $card_lines = array();
        $card_data = false;
        $count = 0;
        foreach ($lines as $v) {
            if ($card_data) {
                $card_lines[] = $v;
            }
            if (strtoupper(trim($v)) == 'BEGIN:VCARD') {
                $card_lines = array();
                $card_data = true;
            }
            elseif (strtoupper(trim($v)) == 'END:VCARD') {
                if (count($card_lines) > 1) {
                    array_pop($card_lines);
                    $this->import_card($card_lines);
                    if (!empty($this->card)) {
                        $this->set_card();
                        $count++;
                    }
                    $card_data = false;
                }
            }
        }
        return $count;
    }
    /* import a vcf file */
    function import_card($lines) {
        $this->card = array();
        $tel_groups = array('A', 'B', 'C');
        $email_groups = array('A', 'B', 'C', 'D');
        foreach ($lines as $v) {
            $v = trim($v);
            $parts = preg_split("/(;|:)/", $v);
            if (isset($parts[0])) {
                switch (strtoupper($parts[0])) {
                    case 'EMAIL':
                        array_shift($parts);
                        foreach ($parts as $val) {
                            if (strstr($val, '@')) {
                                if (!empty($email_groups)) {
                                    $group = array_shift($email_groups);
                                    $this->card[] = array('group' => $group, 'name' => 'EMAIL', 'value' => $val, 'properties' => array());
                                }
                                break;
                            }
                        }
                        break;
                    case 'TEL':
                        array_shift($parts);
                        $count = count($parts) - 1;
                        $val = $parts[$count];
                        $prop = array();
                        if (substr($v, 3, 1) == ';') {
                            if (strstr($parts[0], ',')) {
                                $prop = explode(',', str_replace('TYPE=', '', strtoupper($parts[0])));
                            }
                            else {
                                $prop[] = str_replace('TYPE=', '', strtoupper($parts[0]));
                            }
                        }
                        if (!empty($tel_groups)) {
                            $group = array_shift($tel_groups);
                            $this->card[] = array('group' => $group, 'name' => 'TEL', 'value' => $val, 'properties' => $prop);
                        }
                        break;
                    case 'N':
                        array_shift($parts);
                        $prop = array();
                        if (substr($v, 1, 1) == ';') {
                            $prop[] = str_replace('CHARSET=', '', strtoupper($parts[0]));
                            array_shift($parts);
                        }
                        $name_parts = array('FAMILY', 'GIVEN', 'MIDDLE', 'SUFFIX', 'PREFIX');
                        foreach ($name_parts as $i => $val) {
                            if (isset($parts[$i])) {
                                $this->card[] = array('group' => 'N', 'name' => $val, 'value' => $parts[$i], 'properties' => $prop);
                            }
                        }
                        break;
                    case 'ORG':
                        array_shift($parts);
                        if (substr($v, 3, 1) == ';') {
                            $prop[] = str_replace('CHARSET=', '', strtoupper($parts[0]));
                            array_shift($parts);
                        }
                        foreach ($parts as $i => $v) {
                            if ($i == 0) {
                                $this->card[] = array('group' => 'ORG', 'name' => 'NAME', 'value' => $v, 'properties' => $prop);
                            }
                            if ($i == 1) {
                                $this->card[] = array('group' => 'ORG', 'name' => 'UNIT', 'value' => $v, 'properties' => $prop);
                            }
                            if ($i == 2) {
                                $this->card[] = array('group' => 'ORG', 'name' => 'TITLE', 'value' => $v, 'properties' => $prop);
                            }
                        }
                        break;
                    case 'FN':
                        array_shift($parts);
                        $prop = array();
                        if (substr($v, 2, 1) == ';') {
                            $prop[] = str_replace('CHARSET=', '', strtoupper($parts[0]));
                            array_shift($parts);
                        }
                        if (isset($parts[0])) {
                            $this->card[] = array('group' => '', 'name' => 'FN', 'value' => $parts[0], 'properties' => $prop);
                        }
                        break;
                    case 'ADR':
                        array_shift($parts);
                        $prop = array();
                        if (substr($v, 3, 1) == ';') {
                            if (strstr($parts[0], ',')) {
                                $prop = explode(',', str_replace('TYPE=', '', strtoupper($parts[0])));
                            }
                            else {
                                $prop[] = str_replace('TYPE=', '', strtoupper($parts[0]));
                            }
                            array_shift($parts);
                            if (preg_match("/^CHARSET=/", strtoupper($parts[0]))) {
                                $prop[] = str_replace('CHARSET=', '', strtoupper($parts[0]));
                                array_shift($parts);
                            }
                        }
                        $a_parts = array('POADDR', 'EXTADDR', 'STREET', 'LOCALITY', 'REGION', 'POSTALCODE', 'COUNTRYNAME');
                        foreach ($a_parts as $i => $val) {
                            if (isset($parts[$i])) {
                                $this->card[] = array('group' => 'ADR', 'name' => $val, 'value' => $parts[$i], 'properties' => $prop);
                            }
                        }
                        break;
                }
            }
        }
    }
    /* build internal vcard from $attributes list */
    function build_card($atts) {
        global $address_types;
        global $phone_types;
        $adr_type = 1;
        $ptype_a = 1;
        $ptype_b = 1;
        $ptype_c = 1;
        if (isset($atts['ADR.TYPE'])) {
            $adr_type = $atts['ADR.TYPE'];
            unset($atts['ADR.TYPE']);
        }
        if (isset($atts['A.TEL.TYPE'])) {
            $ptype_a = $atts['A.TEL.TYPE'];
            unset($atts['A.TEL.TYPE']);
        }
        if (isset($atts['B.TEL.TYPE'])) {
            $ptype_b = $atts['B.TEL.TYPE'];
            unset($atts['B.TEL.TYPE']);
        }
        if (isset($atts['C.TEL.TYPE'])) {
            $ptype_c = $atts['C.TEL.TYPE'];
            unset($atts['C.TEL.TYPE']);
        }
        foreach ($atts as $i => $v) {
            $group = '';
            $name = '';
            $attributes = array();
            if (preg_match("/^([a-z]+)\./i", $i, $matches)) {
                $group = $matches[1];
                $name = substr($i, (strlen($group) + 1));
            }
            else {
                $name = $i;
            }
            if ($group && $name == 'TEL') {
                $label = 'ptype_'.strtolower($group);
                if (isset($phone_types[$$label])) {
                    $attributes[] = $phone_types[$$label];
                }
            }
            if ($group == 'ADR') {
                if (isset($address_types[$adr_type])) {
                    $attributes[] = $address_types[$adr_type];
                }
            }
            $this->card[] = array('group' => $group, 'value' => $v, 'name' => $name, 'properties' => $attributes);
        }
    }
    /* set internal vcard to the properties of $id */
    function set_card($id=false) {
        if (!$id) {
            $this->card_list[(count($this->card_list) + 1)] = $this->card;
        }
        else {
            $this->card_list[$id] = $this->card;
        }
    }
    /* return vcard string */
    function export_card($id) {
        $vals = $this->card_list[$id];
        $start = "BEGIN:VCARD\r\nVERSION:2.1\r\n";
        $name = array(0 => false, 1 => false, 2 => false, 3 => false, 4 => false);
        $name_parts = array('FAMILY', 'GIVEN', 'MIDDLE', 'SUFFIX', 'PREFIX');
        $filename = ''; 
        $body = '';
        foreach ($vals as $atts) {
            if ($atts['group'] == 'N') {
                foreach ($name_parts as $i => $v) {
                    if ($atts['name'] == $v) {
                        $name[$i] = trim($atts['value']);
                        break;
                    }
                }
            }
        }
        $empty_name = true;
        foreach ($name as $v) {
            if ($v) {
                $empty_name = false;
                break;
            }
        }
        if (!$empty_name) {
            $name = rtrim(implode(';', $name), ';');
            $filename = str_replace(array('.', ';'), '_', $name).'.vcf';
            if ($name) {
                $body .= 'N:'.$name."\r\n"; 
            }
        }
        foreach ($vals as $atts) {
            if ($atts['name'] == 'FN') {
                $body .= 'FN:'.trim($atts['value'])."\r\n";
                break;
            }
        }
        $a_parts = array('POADDR', 'EXTADDR', 'STREET', 'LOCALITY', 'REGION', 'POSTALCODE', 'COUNTRYNAME');
        $address = array(0 => false, 1 => false, 2 => false, 3 => false, 4 => false, 5 => false, 6 => false);
        $a_type = false;
        foreach ($vals as $atts) {
            if ($atts['group'] == 'ADR') {
                if (!$a_type && isset($atts['properties'][0])) {
                    $a_type = $atts['properties'][0];
                }
                foreach ($a_parts as $i => $v) {
                    if ($atts['name'] == $v) {
                        $address[$i] = $atts['value'];
                        break;
                    }
                }
            }
        }
        $empty_address = true;
        foreach ($address as $v) {
            if ($v) {
                $empty_address = false;
                break;
            }
        }
        if (!$empty_address) {
            $address = rtrim(implode(';', $address), ';');
            if ($name) {
                if ($a_type) {
                    $body .= 'ADR;'.$a_type.':';
                }
                else {
                    $body .= 'ADR:';
                }
                $body .= trim($address)."\r\n"; 
            }
        }
        foreach ($vals as $atts) {
            if ($atts['name'] == 'TEL') {
                if (isset($atts['properties'][0])) {
                    $body .= 'TEL;'.$atts['properties'][0].':'.trim($atts['value'])."\r\n";
                }
                else {
                    $body .= 'TEL:'.trim($atts['value'])."\r\n";
                }
            }
        }
        $org_parts = array('NAME', 'UNIT', 'TITLE');
        $org = array();
        foreach ($vals as $atts) {
            if ($atts['group'] == 'ORG') {
                foreach ($org_parts as $i => $v) {
                    if ($atts['name'] == $v) {
                        $org[$i] = $atts['value'];
                        break;
                    }
                }
            }
        }
        if (!empty($org)) {
            $org = rtrim(implode(';', $org), ';');
            if ($org) {
                $body .= 'ORG:'.trim($org)."\r\n"; 
            }
        }
        $email = '';
        foreach ($vals as $atts) {
            if ($atts['name'] == 'EMAIL') {
                if (!$filename) {
                    $filename = str_replace(array('.', '@'), '_', $atts['value']).'.vcf';
                }
                $email .= 'EMAIL;INTERNET:'.trim($atts['value'])."\r\n";
            }
        }
        $body .= $email;
        $string = $start.$body."END:VCARD\r\n";
        return array($filename, $string);
    }
    /* return internal vcard array ($vcard->card) */
    function get_card($id=false) {
        if (!$id) {
            return $this->card;
        }
        else {
            if (isset($this->card_list[$id])) {
                return $this->card_list[$id];
            }
            else {
                return array();
            }
        }
    }
    /* save updated card list */
    function write_cards() {
        global $conf;
        if (isset($conf['site_contacts_storage']) && $conf['site_contacts_storage'] == 'db') {
            return $this->write_cards_db();
        }
        else {
            return $this->write_cards_file();
        }
    }
    /* write cards to a database */
    function write_cards_db() {
        global $conf;
        global $dbase;
        global $user;
        $return = false;
        if (is_object($dbase)) {
            $exists = $dbase->select('select id from contacts where username='.$dbase->qt($user->username));
            if (isset($exists[0]['id'])) {
                $sql = 'update contacts set contacts='.$dbase->qt(serialize(array($this->card_groups, $this->card_list))).' where id='.$exists[0]['id'];
                $dbase->update($sql);
                $res = 1;
            }
            else {
                $sql = 'insert into contacts (username, contacts) values('.$dbase->qt($user->username).', '.$dbase->qt(serialize(array($this->card_groups, $this->card_list))).')';
                $res = $dbase->insert($sql);
            }
            if ($res) {
                $return = true;
            }
            if (isset($_SESSION['contact_list'])) {
                unset($_SESSION['contact_list']);
            }
        }
        return $return;
    }
    /* write cards to a file */
    function write_cards_file() {
        global $conf;
        global $fd;
        $settings_path = $conf['settings_path'];
        $username = $_SESSION['user_data']['username'];
        if (substr($settings_path, -1) != $fd) {
            $filename = $settings_path.$fd.$username.'.contacts';
        }
        else {
            $filename = $settings_path.$username.'.contacts';
        }
        $handle = @fopen($filename, "w");
        if (@fwrite($handle, serialize(array($this->card_groups, $this->card_list)))) {
            @fclose($handle);
            $return = true;
        }
        else {
            $return = false;
        }
        if (isset($_SESSION['contact_list'])) {
            unset($_SESSION['contact_list']);
        }
        return $return;
    }
}
?>
