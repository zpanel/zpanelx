<?php

/*  post_action_class.php: Process POST forms
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
class fw_post_action_contacts extends fw_user_action_with_post {
function set_post_page_vars() {
    global $user;
    $forms = array(
    'update_vcard' => array(
        'card_id' => array('int', 1, 'Card ID'),
        'a_email' => array('email', 1, 'Address 1'),
        'b_email' => array('email', 0, 'Address 2'),
        'c_email' => array('email', 0, 'Address 3'),
        'd_email' => array('email', 0, 'Address 4'),
        'n_family' => array('string', 0, 'Family Name'),
        'n_given' => array('string', 0, 'Given Name'),
        'fn' => array('string', 0, 'Display Name'),
        'n_middle' => array('string', 0, 'Middel Name'),
        'n_prefix' => array('string', 0, 'Name Prefix'),
        'n_suffix' => array('string', 0, 'Name Suffix'),
        'adr_poaddr' => array('string', 0, 'Post Office Address'),
        'adr_extaddr' => array('string', 0, 'Extended Address'),
        'adr_street' => array('string', 0, 'Street Address'),
        'adr_locality' => array('string', 0, 'City'),
        'adr_region' => array('string', 0, 'Region'),
        'adr_postalcode' => array('string', 0, 'Postal Code'),
        'adr_countryname' => array('string', 0, 'Country'),
        'adr_type' => array('int', 0, 'Address Type'),
        'a_tel_type' => array('int', 0, 'Phone 1 Type'),
        'a_tel' => array('string', 0, 'Phone 1'),
        'b_tel_type' => array('int', 0, 'Phone 2 Type'),
        'b_tel' => array('string', 0, 'Phone 2'),
        'c_tel_type' => array('int', 0, 'Phone 3 Type'),
        'c_tel' => array('string', 0, 'Phone 3'),
        'org_name' => array('string', 0, 'Company Name'),
        'org_unit' => array('string', 0, 'Company Unit'),
        'org_title' => array('string', 0, 'Title'),
    ),
    'add_vcard' => array(
        'a_email' => array('email', 1, 'Address 1'),
        'b_email' => array('email', 0, 'Address 2'),
        'c_email' => array('email', 0, 'Address 3'),
        'd_email' => array('email', 0, 'Address 4'),
        'n_family' => array('string', 0, 'Family Name'),
        'n_given' => array('string', 0, 'Given Name'),
        'n_middle' => array('string', 0, 'Middel Name'),
        'n_prefix' => array('string', 0, 'Name Prefix'),
        'fn' => array('string', 0, 'Display Name'),
        'n_suffix' => array('string', 0, 'Name Suffix'),
        'adr_poaddr' => array('string', 0, 'Post Office Address'),
        'adr_extaddr' => array('string', 0, 'Extended Address'),
        'adr_street' => array('string', 0, 'Street Address'),
        'adr_locality' => array('string', 0, 'City'),
        'adr_region' => array('string', 0, 'Region'),
        'adr_postalcode' => array('string', 0, 'Postal Code'),
        'adr_countryname' => array('string', 0, 'Country'),
        'adr_type' => array('int', 0, 'Address Type'),
        'a_tel_type' => array('int', 0, 'Phone 1 Type'),
        'a_tel' => array('string', 0, 'Phone 1'),
        'b_tel_type' => array('int', 0, 'Phone 2 Type'),
        'b_tel' => array('string', 0, 'Phone 2'),
        'c_tel_type' => array('int', 0, 'Phone 3 Type'),
        'c_tel' => array('string', 0, 'Phone 3'),
        'org_name' => array('string', 0, 'Company Name'),
        'org_unit' => array('string', 0, 'Company Unit'),
        'org_title' => array('string', 0, 'Title'),
    ),
    'clear_contact_search' => array(
    ),
    'contact_search' => array(
        'contact_search_keywords' => array('string', 0, 'Search Terms'),
    ),
    'import_card' => array(
    ),
    'delete_vcard' => array(
        'card_id' => array('int', 1, 'Card ID'),
    ),
    'add_group' => array(
        'group_members' => array('array', 0, 'Group Members'),
        'group_name' => array('string', 1, 'Group Name'),
    ),
    'update_group' => array(
        'existing_members' => array('array', 0, 'Existing Members'),
        'other_contacts' => array('array', 0, 'Other Contacts'),
        'group_name' => array('string', 1, 'Group Name'),
        'orig_name' => array('string', 1, 'Original Group Name'),
    ),
    'delete_group' => array(
        'orig_name' => array('string', 1, 'Group Name'),
    ),
    'remove_contacts' => array(
        'orig_name' => array('string', 1, 'Group Name'),
        'existing_members' => array('array', 0, 'Existing Members'),
        'other_contacts' => array('array', 0, 'Other Contacts'),
    ),
    'add_contacts' => array(
        'orig_name' => array('string', 1, 'Group Name'),
        'existing_members' => array('array', 0, 'Existing Members'),
        'other_contacts' => array('array', 0, 'Other Contacts'),
    ),
    ); return $forms;
}
function form_action_sort_contacts($form, $post) {
    global $user;
    global $contact_sort_types;
    if ($user->logged_in) {
        if (isset($contact_sort_types[$post['contact_sort']])) {
            $user->page_data['contact_sort'] = $post['contact_sort'];
        }
    }
}
function form_action_clear_contact_search($form, $post) {
    global $user;
    $user->page_data['contact_search_keywords'] = '';
    if (isset($_SESSION['contact_search_keywords'])) {
        unset($_SESSION['contact_search_keywords']);
    }
}
function form_action_contact_search($form, $post) {
    global $user;
    if (isset($post['contact_search_keywords']) && $post['contact_search_keywords']) {
        $user->page_data['contact_search_keywords'] = $post['contact_search_keywords'];
    }
}
function form_action_import_card($form, $post) {
    global $user;
    global $include_path;
    global $conf;
    global $fd;
    if ($user->logged_in) {
        $ufiles = array();
        $utype = false;
        if (isset($_FILES['card_upload']) && !empty($_FILES['card_upload'])) {
            $ufiles = $_FILES['card_upload'];
            $utype = 'single';
        }
        if (isset($_FILES['mcard_upload']) && !empty($_FILES['mcard_upload'])) {
            $ufiles = $_FILES['mcard_upload'];
            $utype = 'multiple';
        }
        if (!empty($ufiles)) {
            if (!$ufiles['error']) {
                $type = strtolower($ufiles['type']);
                if (strtolower(trim($type)) == 'text/x-vcard' || strtolower(trim($type)) == 'text/directory' || strtolower(trim($type)) == 'application/octet-stream') {
                    $src = $ufiles['tmp_name'];
                    $size = $ufiles['size'];
                    if ($ufiles['size']) {
                        $data = file($src);
                        if (!empty($data)) {
                            require_once($include_path.'lib'.$fd.'vcard.php');
                            if ($utype == 'single') {
                                $vcard = hm_new('vcard');
                                $vcard->import_card($data);
                                $user->page_data['import_card'] = $vcard; 
                            }
                            else {
                                $vcard = hm_new('vcard');
                                $vcard->get_card_list();
                                $res = $vcard->import_multiple_cards($data);
                                if ($res) {
                                    $vcard->write_cards();
                                    $this->errors[] = $user->str[358].': '.$res;
                                }
                                else {
                                    $this->errors[] = $user->str[359];
                                }
                            }
                        }
                        else {
                            $this->errors[] = $user->str[360];
                        }
                    }
                    else {
                        $this->errors[] = $user->str[361];
                    }
                }
                else {
                    $this->errors[] = $user->str[362].': '.$user->htmlsafe($type);
                }
            }
            else {
                switch ($ufiles['error']) {
                    case 4:
                        $this->errors[] = $user->str[363];
                        break;
                    default:
                        $this->errors[] = $user->str[364];
                        break;
                }
            }
        }
    }
}
function form_action_delete_vcard($form, $post) {
    global $user;
    global $include_path;
    global $conf;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        if (isset($_SESSION['contact_sort_order'])) {
            $vcard->sort_fld = $_SESSION['contact_sort_order'];
        }
        else {
            $vcard->sort_fld = 'EMAIL';
        }
        $vcard->get_card_list();
        if (isset($vcard->card_list[$post['card_id']])) {
            $cards = array();
            $n = 1;
            foreach ($vcard->card_list as $id => $vals) {
                if ($id != $post['card_id']) {
                    $cards[$n] = $vals;
                    $n++;
                }
            }
            $vcard->card_list = $cards;
            $res = $vcard->write_cards();
            if ($res) {
                unset($_GET['edit_card']);
                $this->errors[] = $user->str[365];
                $this->form_redirect = true;
            }
        }
    }
}
function form_action_update_vcard($form, $post) {
    global $user;
    global $include_path;
    global $conf;
    global $fd;
    if ($user->logged_in) {
        $atts = array();
        foreach ($form as $name => $vals) {
            if (isset($post[$name]) && trim($post[$name])) {
                $index = trim(strtoupper(str_replace('_', '.', $name)));
                $atts[$index] = $post[$name];
            }
        }
        if (!empty($atts)) {
            require_once($include_path.'lib'.$fd.'vcard.php');
            $vcard = hm_new('vcard');
            if (isset($_SESSION['contact_sort_order'])) {
                $vcard->sort_fld = $_SESSION['contact_sort_order'];
            }
            else {
                $vcard->sort_fld = 'EMAIL';
            }
            $vcard->get_card_list();
            $vcard->build_card($atts);
            if (isset($vcard->card_list[$post['card_id']])) {
                $vcard->set_card($post['card_id']);
                $res = $vcard->write_cards();
                if ($res) {
                    $this->errors[] = $user->str[366];
                    $this->form_redirect = true;
                }
            }
        }
    }
}
function form_action_add_vcard($form, $post) {
    global $user;
    global $include_path;
    global $conf;
    global $fd;
    if ($user->logged_in) {
        $atts = array();
        foreach ($form as $name => $vals) {
            if (isset($post[$name]) && trim($post[$name])) {
                $index = trim(strtoupper(str_replace('_', '.', $name)));
                $atts[$index] = $post[$name];
            }
        }
        if (!empty($atts)) {
            require_once($include_path.'lib'.$fd.'vcard.php');
            $vcard = hm_new('vcard');
            if (isset($_SESSION['contact_sort_order'])) {
                $vcard->sort_fld = $_SESSION['contact_sort_order'];
            }
            else {
                $vcard->sort_fld = 'EMAIL';
            }
            $vcard->get_card_list();
            $vcard->build_card($atts);
            $vcard->set_card();
            $res = $vcard->write_cards();
            if ($res) {
                $this->errors[] = $user->str[367];
                $this->form_redirect = true;
            }
        }
    }
}
function form_action_delete_group($form, $post) {
    global $user;
    global $include_path;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        $vcard->get_card_list(false);
        if (in_array($post['orig_name'], $vcard->card_groups)) {
            $groups = array();
            foreach ($vcard->card_groups as $v) {
                if ($v != $post['orig_name']) {
                    $groups[] = $v;
                }
            }
            $vcard->card_groups = $groups;
            $vcard->remove_group_members($post['orig_name']);
            $vcard->write_cards();
            $this->errors[] = 'Group Deleted';
            $this->form_redirect = true;
        }
    }
}
function form_action_add_group($form, $post) {
    global $user;
    global $include_path;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        $vcard->get_card_list(false);
        if (!in_array($post['group_name'], $vcard->card_groups)) {
            $this->errors[] = 'Group Added';
            $vcard->card_groups[] = $post['group_name'];
            if (is_array($post['group_members']) && !empty($post['group_members'])) {
                $members = $post['group_members'];
            }
            else {
                $members = array();
            }
            $vcard->update_group_members($post['group_name'], $members);
            $vcard->write_cards();
            $this->form_redirect = true;
        }
        else {
            $this->errors[] = $user->str[519];
        }
    }
}
function form_action_update_group($form, $post) {
    global $user;
    global $include_path;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        $vcard->get_card_list();
        if (isset($post['orig_name']) && in_array($post['orig_name'], $vcard->card_groups)) {
            if ($post['group_name'] != $post['orig_name'] && in_array($post['group_name'], $vcard->card_groups)) {
                $this->errors[] = $user->str[519];
            }
            else {
                $members = $this->get_group_contact_members($post, $vcard, $post['orig_name']);
                $key_list = array_keys($vcard->card_groups, $post['orig_name']);
                $key = $key_list[0];
                $vcard->card_groups[$key] = $post['group_name'];
                $vcard->remove_group_members($post['group_name']);
                $vcard->update_group_members($post['group_name'], $members);
                $vcard->write_cards();
                $this->errors[] = $user->str[513];
                $this->form_redirect = true;
            }
        }
    }
}
function form_action_add_contacts($form, $post) {
    global $user;
    global $include_path;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        $vcard->get_card_list();
        $diff = 0;
        if (isset($post['orig_name']) && in_array($post['orig_name'], $vcard->card_groups)) {
            $members = $this->get_group_contact_members($post, $vcard, $post['orig_name']);
            $orig_members = $vcard->get_group_members($post['orig_name']);
            if (isset($orig_members[$post['orig_name']])) {
                $diff = count($members) - count(array_keys($orig_members[$post['orig_name']]));
            }
            $vcard->remove_group_members($post['orig_name']);
            $vcard->update_group_members($post['orig_name'], $members);
            $vcard->write_cards();
            if ($diff == 1) {
                $this->errors[] = sprintf($user->str[515], $diff);
            }
            else {
                $this->errors[] = sprintf($user->str[514], $diff);
            }
            $this->form_redirect = true;
        }
    }
}
function form_action_remove_contacts($form, $post) {
    global $user;
    global $include_path;
    global $fd;
    if ($user->logged_in) {
        require_once($include_path.'lib'.$fd.'vcard.php');
        $vcard = hm_new('vcard');
        $vcard->get_card_list();
        $diff = 0;
        if (isset($post['orig_name']) && in_array($post['orig_name'], $vcard->card_groups)) {
            $members = $this->get_group_contact_members($post, $vcard, $post['orig_name']);
            $orig_members = $vcard->get_group_members($post['orig_name']);
            if (isset($orig_members[$post['orig_name']])) {
                $diff = count(array_keys($orig_members[$post['orig_name']])) - count($members);
            }
            $vcard->remove_group_members($post['orig_name']);
            $vcard->update_group_members($post['orig_name'], $members);
            $vcard->write_cards();
            if ($diff == 1) {
                $this->errors[] = sprintf($user->str[517], $diff);
            }
            else {
                $this->errors[] = sprintf($user->str[516], $diff);
            }
            $this->form_redirect = true;
        }
    }
}
function get_group_contact_members($post, $vcard, $group) {
    $to_remove = array();
    $to_add = array();
    $new_group_keys = array();
    if (isset($post['existing_members'])) {
        foreach ($post['existing_members'] as $v) {
            $to_remove[] = $v;
        }
    }
    if (isset($post['other_contacts'])) {
        foreach ($post['other_contacts'] as $v) {
            $to_add[] = $v;
        }
    }
    $orig_members = $vcard->get_group_members($group);
    if (isset($orig_members[$group])) {
        foreach ($orig_members[$group] as $i => $card) {
            if (!in_array($i, $to_remove)) {
                $new_group_keys[] = $i;
            }
        }
    }
    foreach ($to_add as $v) {
        if (!in_array($v, $new_group_keys) && in_array($v, array_keys($vcard->card_list))) {
            $new_group_keys[] = $v;
        }
    }
    return $new_group_keys;
}
}?>
