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
function get_account($id, $accounts) {
    foreach ($accounts as $account) {
        if ($account[0] == $id) {
            return $account;
        }
    }
    return array();
}
function url_action_pop_fetch($tools, $get, $post) {
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }

    /* get saved settings and accounts */
    $pop_accounts = $tools->get_setting('pop_fetch_accounts');
    $folders = $tools->imap_get_folders();
    $pd = array();
    $pd['mode'] = 'add';
    $pd['notices'] = array();
    $pd['pop_account_name'] = '';
    $pd['pop_account_host'] = '';
    $pd['pop_account_port'] = 110;
    $pd['pop_account_user'] = '';
    $pd['pop_account_password'] = '';
    $pd['pop_account_ssl'] = '';
    $pd['pop_account_starttls'] = '';
    $pd['pop_account_id'] = '';
    $pd['pop_account_folder'] = '';
    $pd['pop_account_keep'] = '';

    /* edit form */
    if (isset($get['edit_id'])) {
        $atts = get_account($get['edit_id'], $pop_accounts);
        if (!empty($atts)) {
            $pd['mode'] = 'edit';
            $pd['pop_account_id'] = $atts[0];
            $pd['pop_account_name'] = $atts[1];
            $pd['pop_account_host'] = $atts[2];
            $pd['pop_account_port'] = $atts[3];
            $pd['pop_account_user'] = $atts[6];
            $pd['pop_account_password'] = $atts[7];
            $pd['pop_account_folder'] = $atts[8];
            if ($atts[4]) {
                $pd['pop_account_ssl'] = 'checked="checked"';
            }
            if ($atts[5]) {
                $pd['pop_account_starttls'] = 'checked="checked"';
            }
            if ($atts[9]) {
                $pd['pop_account_keep'] = 'checked="checked"';
            }
        }
        elseif (!isset($post['delete_pop_account'])) {
            $tools->send_notice('Could not the requested account');
        }
    }

    /* update general settings */
    if (isset($post['update_pop_fetch'])) {
        if (isset($post['account_update_interval']) && intval($post['account_update_interval']) > 0 &&
            intval($post['account_update_interval']) <= 60*60) {
            $tools->save_setting('pop_update_interval', intval($post['account_update_interval'])); 
        }
        else {
            $tools->save_setting('pop_update_interval', 0);
        }
        if (isset($post['account_folder_tree'])) {
            $tools->save_setting('pop_folder_tree', 1);
        }
        else {
            $tools->save_setting('pop_folder_tree', 0);
        }
        $tools->send_notice('Settings Updated');
    }

    /* delete an account */
    elseif (isset($post['delete_pop_account'])) {
        if (isset($get['edit_id'])) {
            $atts = get_account($get['edit_id'], $pop_accounts);
            if (!empty($atts)) {
                $new_accounts = array();
                foreach ($pop_accounts as $vals) {
                    if ($vals[0] == $atts[0]) {
                        continue;
                    }
                    $new_accounts[] = $vals;
                }
                $pop_accounts = $new_accounts;
                $tools->save_setting('pop_fetch_accounts', $pop_accounts);
                $tools->send_notice('Account Deleted');
                $pd['mode'] = 'add';
                $pd['pop_account_name'] = '';
                $pd['pop_account_host'] = '';
                $pd['pop_account_port'] = '';
                $pd['pop_account_user'] = '';
                $pd['pop_account_password'] = '';
                $pd['pop_account_ssl'] = '';
                $pd['pop_account_starttls'] = '';
                $pd['pop_account_id'] = '';
                $pd['pop_account_folder'] = '';
                $pd['pop_account_keep'] = '';
            }
        }
    }

    /* update an account */
    elseif (isset($post['edit_pop_account'])) {
        $pop_account_name = '';
        $pop_account_host = '';
        $pop_account_port = 110;
        $pop_account_user = '';
        $pop_account_password = '';
        $pop_account_ssl = '';
        $pop_account_starttls = '';
        $pop_account_id = '';
        $pop_account_folder = '';
        $pop_account_keep = '';
        if (isset($get['edit_id']) && trim($get['edit_id'])) {
            $pop_account_id = $get['edit_id'];
        }
        else {
            $pd['notices'][] = 'Account id is required';
        }
        if (isset($post['pop_account_folder']) && trim($post['pop_account_folder'])) {
            $pop_account_folder = $post['pop_account_folder'];
        }
        else {
            $pd['notices'][] = 'Destination folder is required';
        }
        if (isset($post['pop_account_name']) && trim($post['pop_account_name'])) {
            $pop_account_name = $post['pop_account_name'];
        }
        else {
            $pd['notices'][] = 'Account name is required';
        }
        if (isset($post['pop_account_host']) && trim($post['pop_account_host'])) {
            $pop_account_host = $post['pop_account_host'];
        }
        else {
            $pd['notices'][] = 'Account host is required';
        }
        if (isset($post['pop_account_port']) && trim($post['pop_account_port'])) {
            $pop_account_port = $post['pop_account_port'];
        }
        else {
            $pd['notices'][] = 'Account port is required';
        }
        if (isset($post['pop_account_user']) && trim($post['pop_account_user'])) {
            $pop_account_user = $post['pop_account_user'];
        }
        else {
            $pd['notices'][] = 'Account username is required';
        }
        if (isset($post['pop_account_password']) && trim($post['pop_account_password'])) {
            $pop_account_password = $post['pop_account_password'];
        }
        else {
            $pd['notices'][] = 'Account password is required';
        }
        if (isset($post['pop_account_starttls'])) {
            $pop_account_starttls = true;
        }
        else {
            $pop_account_starttls = false;
        }
        if (isset($post['pop_account_ssl'])) {
            $pop_account_ssl = true;
        }
        else {
            $pop_account_ssl = false;
        }
        if (isset($post['pop_account_keep'])) {
            $pop_account_keep = true;
        }
        else {
            $pop_account_keep = false;
        }
        if (empty($pd['notices'])) {
            $pop_account = array(
                $pop_account_id,
                $pop_account_name,
                $pop_account_host,
                $pop_account_port,
                $pop_account_ssl,
                $pop_account_starttls,
                $pop_account_user,
                $pop_account_password,
                $pop_account_folder,
                $pop_account_keep
            );
            if ($pop_account_id) {
                $atts = get_account($pop_account_id, $pop_accounts);
                foreach ($pop_accounts as $index => $vals) {
                    if ($vals[0] == $atts[0]) {
                        $pop_accounts[$index] = $pop_account;
                        break;
                    }
                }
                $tools->save_setting('pop_fetch_accounts', $pop_accounts);
                $tools->send_notice('Account Updated');
                $pd['mode'] = 'add';
                $pd['pop_account_name'] = '';
                $pd['pop_account_host'] = '';
                $pd['pop_account_port'] = '';
                $pd['pop_account_user'] = '';
                $pd['pop_account_password'] = '';
                $pd['pop_account_ssl'] = '';
                $pd['pop_account_starttls'] = '';
                $pd['pop_account_id'] = '';
                $pd['pop_account_folder'] = '';
                $pd['pop_account_keep'] = '';
            }
        }
    }

    /* add an account */
    elseif (isset($post['add_pop_account'])) {
        $pop_account_name = '';
        $pop_account_host = '';
        $pop_account_port = '';
        $pop_account_user = '';
        $pop_account_password = '';
        $pop_account_ssl = '';
        $pop_account_starttls = '';
        $pop_account_folder = '';
        $pop_account_keep = '';
        if (isset($post['pop_account_folder']) && trim($post['pop_account_folder'])) {
            $pop_account_folder = $post['pop_account_folder'];
        }
        else {
            $pd['notices'][] = 'Destination folder is required';
        }
        if (isset($post['pop_account_name']) && trim($post['pop_account_name'])) {
            $pop_account_name = $post['pop_account_name'];
        }
        else {
            $pd['notices'][] = 'Account name is required';
        }
        if (isset($post['pop_account_host']) && trim($post['pop_account_host'])) {
            $pop_account_host = $post['pop_account_host'];
        }
        else {
            $pd['notices'][] = 'Account host is required';
        }
        if (isset($post['pop_account_port']) && trim($post['pop_account_port'])) {
            $pop_account_port = $post['pop_account_port'];
        }
        else {
            $pd['notices'][] = 'Account port is required';
        }
        if (isset($post['pop_account_user']) && trim($post['pop_account_user'])) {
            $pop_account_user = $post['pop_account_user'];
        }
        else {
            $pd['notices'][] = 'Account username is required';
        }
        if (isset($post['pop_account_password']) && trim($post['pop_account_password'])) {
            $pop_account_password = $post['pop_account_password'];
        }
        else {
            $pd['notices'][] = 'Account password is required';
        }
        if (isset($post['pop_account_starttls'])) {
            $pop_account_starttls = true;
        }
        else {
            $pop_account_starttls = false;
        }
        if (isset($post['pop_account_ssl'])) {
            $pop_account_ssl = true;
        }
        else {
            $pop_account_ssl = false;
        }
        if (isset($post['pop_account_keep'])) {
            $pop_account_keep = true;
        }
        else {
            $pop_account_keep = false;
        }
        if (empty($pd['notices'])) {
            $pop_account_id = md5(uniqid(rand(),1));
            $pop_accounts[] = array(
                $pop_account_id,
                $pop_account_name,
                $pop_account_host,
                $pop_account_port,
                $pop_account_ssl,
                $pop_account_starttls,
                $pop_account_user,
                $pop_account_password,
                $pop_account_folder,    
                $pop_account_keep
            );
            $tools->save_setting('pop_fetch_accounts', $pop_accounts);
        }
        else {
            $pd['pop_account_name'] = $pop_account_name;
            $pd['pop_account_host'] = $pop_account_host;
            $pd['pop_account_port'] = $pop_account_port;
            $pd['pop_account_user'] = $pop_account_user;
            $pd['pop_account_password'] = $pop_account_password;
            if ($pop_account_ssl) {
                $pd['pop_account_ssl'] = 'checked="checked"';
            }
            if ($pop_account_starttls) {
                $pd['pop_account_starttls'] = 'checked="checked"';
            }
            if ($pop_account_keep) {
                $pd['pop_account_keep'] = 'checked="checked"';
            }
        }
    }
    $pd['account_update_interval'] = $tools->get_setting('pop_update_interval');
    $fetch_link = $tools->get_setting('pop_folder_tree');
    if ($fetch_link) {
        $pd['account_folder_tree'] = 'checked="checked"';
    }
    else {
        $pd['account_folder_tree'] = '';
    }
    $pd['folder_options']  = $tools->print_folder_dropdown($folders, array($pd['pop_account_folder']), true);
    $pd['pop_accounts'] = $pop_accounts;
    foreach ($pd['notices'] as $notice) {
        $tools->send_notice($notice);
    }
    $tools->set_title('Fetch Mail');
    return $pd;
}
function print_pop_fetch($pd, $tools) {
    $intervals = array(
        0 => 'Never',
        60 => '1 Minute',
        60*15 => '15 Minutes',
        60*30 => '30 Minutes',
        60*60 => '1 Hour', 
    );
    if ($pd['mode'] == 'edit') {
        $form_url = '?page=pop_fetch&amp;edit_id='.$pd['pop_account_id'];
        $buttons = '<input type="submit" value="Update" name="edit_pop_account" /> &nbsp;
            <input type="submit" value="Delete" onclick="return confirm(\'Are you sure you want to delete this account?\');" name="delete_pop_account" /> &nbsp;
            <a href="?page=pop_fetch">Cancel</a>';
        $title = 'Edit Account';
    }
    else {
        $form_url = '?page=pop_fetch';
        $buttons = '<input type="submit" value="Add" name="add_pop_account" />';
        $title = 'Add Account';
    }
    $data = '
        <div id="fetch_page">
        <h2 id="mailbox_title2">Fetch Options</h2>
        <div style="clear: both;">
        <div><b>Existing Accounts</b></div>
        <table cellpadding="0" cellspacing="0" class="existing_accounts"><tr><th>Name</th><th>Host</th><th>Port</th><th>SSL/TLS</th>
        <th>STARTTLS</th><th>Username</th><th>Destination folder</th><th>Keep on server</th></tr>';
    if (!empty($pd['pop_accounts'])) {
        foreach ($pd['pop_accounts'] as $vals) {
            if ($vals[4]) {
                $ssl = 'Yes';
            }
            else {
                $ssl = 'No';
            }
            if ($vals[5]) {
                $starttls = 'Yes';
            }
            else {
                $starttls = 'No';
            }
            if ($vals[9]) {
                $keep = 'Yes';
            }
            else {
                $keep = 'No';
            }
            $data .= '<tr><td><a href="?page=pop_fetch&amp;edit_id='.$vals[0].'">'.$tools->display_safe($vals[1]).'</a></td>
                <td>'.$tools->display_safe($vals[2]).'</td>
                <td>'.$tools->display_safe($vals[3]).'</td>
                <td>'.$ssl.'</td><td>'.$starttls.'</td>
                <td>'.$tools->display_safe($vals[6]).'</td>
                <td>'.$tools->display_safe($vals[8]).'</td>
                <td>'.$keep.'</td>
            </tr>';
        }
    }
    else {
        $data .= '<tr><td align="center" colspan="6" style="padding-top: 20px; font-style: italic;">No Accounts Found</td></tr>';
    }
    $data .= '</table>
        <div><b>'.$title.'</b></div>
        <form method="post" action="'.$form_url.'">
        <table class="fetch_form">
        <tr><td class="opt_leftcol">Name</td><td><input type="text" name="pop_account_name" value="'.$tools->display_safe($pd['pop_account_name']).'" style="width: 200px;" /></td></tr>
        <tr><td class="opt_leftcol">Host</td><td><input type="text" name="pop_account_host" value="'.$tools->display_safe($pd['pop_account_host']).'" style="width: 200px;" /></td></tr>
        <tr><td class="opt_leftcol">Port</td><td><input type="text" name="pop_account_port" value="'.$tools->display_safe($pd['pop_account_port']).'" style="width: 50px;" /></td></tr>
        <tr><td class="opt_leftcol">SSL/TLS</td><td><input type="checkbox" name="pop_account_ssl" '.$pd['pop_account_ssl'].' /></td></tr>
        <tr><td class="opt_leftcol">Enable STARTTLS</td><td><input type="checkbox" name="pop_account_starttls" '.$pd['pop_account_starttls'].' /></td></tr>
        <tr><td class="opt_leftcol">Username</td><td><input type="text" name="pop_account_user" style="width: 200px;" value="'.$tools->display_safe($pd['pop_account_user']).'" /></td></tr>
        <tr><td class="opt_leftcol">Password</td><td><input type="password" name="pop_account_password" style="width: 200px;" value="'.$tools->display_safe($pd['pop_account_password']).'" /></td></tr>
        <tr><td class="opt_leftcol">Destination folder</td><td><select name="pop_account_folder">'.$pd['folder_options'].'</select></td></tr>
        <tr><td class="opt_leftcol">Keep mail on server</td><td><input type="checkbox" name="pop_account_keep" '.$pd['pop_account_keep'].' /></td></tr>
        <tr><td class="opt_leftcol" colspan="2"><br />'.$buttons.'</td></tr>
        </table>
        </form>
        <div><b>Fetch Options</b></div>
        <form method="post" action="?page=pop_fetch">
        <table class="fetch_form">
        <tr><td class="opt_leftcol">Show fetch link on the folder tree</td><td><input type="checkbox" name="account_folder_tree" '.$pd['account_folder_tree'].' /></td></tr>
        <tr><td class="opt_leftcol">Automatically fetch mail</td><td><select name="account_update_interval">';
        foreach ($intervals as $i => $v) {
            $data .= '<option ';
            if ($pd['account_update_interval'] == $i) {
                $data .= 'selected="selected" ';
            }
            $data .= 'value="'.$i.'">'.$v.'</option>';
        }
    $data .= '</select></td></tr>
        <tr><td class="opt_leftcol"><br /><input type="submit" name="update_pop_fetch" value="Update" /></td></tr>
        </table>
        </form>
        </div>
        </div>';
    return $data;
}
?>
