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
function url_action_filters($tools, $get, $post) {
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }
    $page_data       = array();
    $filters         = $tools->get_setting('filters');
    $form_type       = 'add';
    $action          = false;
    $target_type     = 'standard';
    $pattern         = '';
    $id              = 0;
    $target          = '';
    $source          = array();
    $filter_source   = 'any';
    $custom_header   = '';
    $mailbox         = '';
    $time_filter     = false;
    $days_old        = 0;
    $pattern_targets = targets($tools);
    $folders         = $tools->imap_get_folders();

    if (isset($get['edit']) && isset($filters[$get['edit']])) {
        list($pattern, $target, $mailbox) = $filters[$get['edit']];
        if (isset($filters[$get['edit']][4])) {
            $days_old = $filters[$get['edit']][4];
        }
        if ($days_old > 0) {
            $time_filter = 'newer';
        }
        elseif ($days_old < 0) {
            $time_filter = 'older';
            $days_old = abs($days_old);
        }
        if (isset($filters[$get['edit']][5])) {
            $source = $filters[$get['edit']][5];
        }
        if (isset($filters[$get['edit']][3])) {
            $action = $filters[$get['edit']][3];
        }
        if (substr($target, 0, 7) == 'HEADER ') {
            $target_type = 'custom';
            $custom_header = substr($target, 7);
        }
        $form_type = 'edit';
        $id = $get['edit'];
    }
    if (!empty($post)) {
        $failed = false;
        if (isset($post['add'])) {
            if (process_add_action($post, $tools, $filters)) {
                $filters = $tools->get_setting('filters');
            }
            else {
                $failed = true;
            }
        }
        elseif (isset($post['edit'])) {
            if (process_edit_action($post, $tools, $filters)) {
                $filters = $tools->get_setting('filters');
            }
            else {
                $failed = true;
            }
        }
        elseif (isset($post['delete'])) {
            if (process_delete_action($post, $tools, $filters)) {
                $filters = $tools->get_setting('filters');
            }
            else {
                $failed = true;
            }
        }
        elseif (isset($post['filter_update_settings'])) {
            if (isset($_POST['filter_menu']) && $_POST['filter_menu']) {
                $tools->save_setting('filter_menu', 1);
            }
            else {
                $tools->save_setting('filter_menu', 0);
            }
            if (isset($_POST['auto_filter']) && $_POST['auto_filter']) {
                $tools->save_setting('auto_filter', 1);
            }
            else {
                $tools->save_setting('auto_filter', 0);
            }
            $tools->send_notice('Settings Updated');
        }
        if ($failed) {
            if (isset($post['pattern'])) {
                $pattern = $post['pattern'];
            }
            if (isset($post['target_type'])) {
                $target_type = $post['target_type'];
            }
            if (isset($post['custom_header'])) {
                $custom_header = $post['custom_header'];
            }
            if (isset($post['target'])) {
                $target = $post['target'];
            }
            if (isset($post['mailbox'])) {
                $mailbox = $post['mailbox'];
            }
        }
    }
    if (isset($get['action']) && isset($get['id'])) {
        if (isset($filters[$get['id']])) {
            $dir = false;
            switch ($get['action']) {
                case 'up': 
                    $dir = 'up';
                    break;
                case 'down': 
                    $dir = 'down';
                    break;
            }
            if ($dir) {
                $filters = resort_filters($filters, $dir, $get['id']);
                $tools->save_setting('filters', $filters);
                $tools->send_notice('Filters Updated');
            }
        }
    }
    if (!empty($source)) {
        $filter_source = 'select';
    }

    $page_data['time_filter']     = $time_filter;
    $page_data['days_old']        = $days_old;
    $page_data['pattern_targets'] = $pattern_targets;
    $page_data['custom_header']   = $custom_header;
    $page_data['pattern']         = $pattern;
    $page_data['target_type']     = $target_type;
    $page_data['source']          = $source;
    $page_data['filter_source']   = $filter_source;
    $page_data['target']          = $target;
    $page_data['mailbox']         = $mailbox;
    $page_data['action']          = $action;
    $page_data['folder_options']  = $tools->print_folder_dropdown($folders, array($mailbox), true);
    $page_data['source_options']  = $tools->print_folder_dropdown($folders, $source, true);
    $page_data['auto_filter']     = $tools->get_setting('auto_filter');
    $page_data['filter_menu']     = $tools->get_setting('filter_menu');
    $page_data['form_type']       = $form_type;
    $page_data['filters']         = $filters;
    $page_data['id']              = $id;
    $page_data['tools']           = $tools;
    $tools->set_title($tools->str[1]);

    return $page_data;
}
function resort_filters($filters, $dir, $id) {
    if ($dir == 'up') {
        $marker = $id - 1;
    }
    else {
        $marker = $id + 1;
    }
    $rule = $filters[$id];
    $old_rule = $filters[$marker];
    $filters[$id] = $old_rule;
    $filters[$marker] = $rule;
    return $filters;
}

function print_filters($pd, $tools) {
    $str = $tools->str;
    $data = '<div id="filters"><h2 id="mailbox_title2">'.$str[1].'</h2>';
    $data .= '<div style="clear: both;">';
    if ($pd['form_type'] == 'add') {
        $data .= '<b>'.$str[2].'</b>';
        $data .= '<table cellpadding="0" cellspacing="0" id="existing">';
        if (empty($pd['filters'])) {
            $data .= '<tr><td align="center" style="padding: 20px;" ><i>'.$str[3].'</i></td></tr>';
        }
        else {
            $data .= '<tr><th>'.$str[4].'</th><th>'.$str[5].'</th><th>'.$str[6].
                     '</th><th>'.$str[7].'</th><th>'.$str[39].'</th><th>'.$str[45].'</th><th></th></tr>';
            $cnt = count($pd['filters']);
            foreach ($pd['filters'] as $i => $vals) {
                $data .= '<tr><td>'.$tools->display_safe($vals[0]).'</td><td>';
                if (isset($pd['pattern_targets'][$vals[1]])) {
                    $data .= $tools->display_safe($pd['pattern_targets'][$vals[1]]);
                }
                else {
                    $data .= $tools->display_safe(substr($vals[1], 7));
                }
                $data .= '</td><td>';
                if (isset($vals[3]) && $vals[3] != 'move') {
                    $data .= ucfirst($vals[3]).'</td><td></td>';
                }
                else {
                    $data .= $str[28].'</td><td>'.$tools->display_safe($vals[2]).'</td>';
                }
                if (isset($vals[4])) {
                    if ($vals[4] > 0) {
                        $data .= '<td>'.$str[41].' '.$tools->display_safe($vals[4]).' '.$str[42].'</td>';
                    }
                    elseif ($vals[4] < 0) {
                        $data .= '<td>'.$str[40].' '.$tools->display_safe(abs($vals[4])).' '.$str[42].'</td>';
                    }
                    else {
                        $data .= '<td>'.$str[44].'</td>';
                    }
                }
                else {
                    $data .= '<td>'.$str[44].'</td>';
                }
                if (isset($vals[5]) && is_array($vals[5]) && !empty($vals[5])) {
                    $data .= '<td style="max-width: 300px;">';
                    $sfolders = array();
                    foreach ($vals[5] as $v) {
                        $sfolders[] = $tools->display_safe($v);
                    }
                    $data .= implode(', ', $sfolders);
                    $data .= '</td>';
                }
                else {
                    $data .= '<td>'.$str[44].'</td>';
                }
                $data .= '<td><a href="?page=filters&amp;edit='.$i.'">'.$str[10].'</a> ';
                if ($i > 0) {
                    $data .= '&#160; <a href="?page=filters&amp;action=up&amp;id='.$i.'">'.$str[8].'</a>';
                }
                if (($i + 1) < count($pd['filters'])) {
                    $data .= ' &#160; <a href="?page=filters&amp;action=down&amp;id='.$i.'">'.$str[9].'</a>';
                }
                $data .= '</td></tr>';
            }
        }
        $data .= '</table>';
    }
    $data .= '<form method="post" action="?page=filters">';
    $data .= '<input type="hidden" name="id" value="'.$pd['id'].'" />';
    if ($pd['form_type'] == 'add') {
        $data .= '<b>'.$str[11].'</b>';
    }
    else {
        $data .= '<b>'.$str[12].'</b>';
    }
    $data .= '<table cellpadding="4" id="form"><tr><td class="opt_leftcol">Pattern</td><td><input type="text" value="'.
             $tools->display_safe($pd['pattern']).'" name="pattern" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$str[13].'</td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['target_type'] == 'standard') { $data .= 'checked="checked" '; }
    $data .= 'name="target_type" value="standard" /> ';
    $data .= '<select name="target">';
    foreach ($pd['pattern_targets'] as $i => $v) {
        $data .= '<option ';
        if ($pd['target'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class="opt_leftcol"></td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['target_type'] == 'custom') { $data .= 'checked="checked" '; }
    $data .= 'name="target_type" value="custom" /> '.$str[26].': <input type="text" name="custom_header" value="'.
             $pd['custom_header'].'" />';
    $data .= '</td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$str[39].'</td><td>';
    $data .= '<input style="background: none; border: none;" type="radio" ';
    if (!$pd['time_filter']) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="time_filter" value="" />';
    $data .= $str[43].'</td></tr><tr><td></td><td>';
    $data .= '<input style="background: none; border: none;" type="radio" ';
    if ($pd['time_filter'] == 'older') {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="time_filter" value="older" />';
    $data .= $str[40].' <input type="text" value="';
    if ($pd['time_filter'] == 'older') {
        $data .= $pd['days_old'];
    } 
    $data .= '" size="2" name="days_old" style="width: 30px;" /> '.$str[42];
    $data .= '</td></tr>';
    $data .= '<tr><td></td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['time_filter'] == 'newer') {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="time_filter" value="newer" />';
    $data .= $str[41].' <input type="text" value="';
    if ($pd['time_filter'] == 'newer') {
        $data .= $pd['days_old'];
    } 
    $data .= '" size="2" name="days_old2" style="width: 30px;" /> '.$str[42];
    $data .= '</td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$str[6].'</td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['action'] == 'move' || $pd['action'] == false) {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="filter_action" value="move" /> '.$str[14].': <select name="mailbox">'.$pd['folder_options'];
    $data .= '</select></td></tr>';
    $data .= '<tr><td></td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['action'] == 'flag') {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="filter_action" value="flag" /> '.$str[15].'</td></tr>';
    $data .= '<tr><td></td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['action'] == 'delete') {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="filter_action" value="delete" /> '.$str[16].'</td></tr>';
    $data .= '<tr><td>'.$str[45].'</td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['filter_source'] == 'any') {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="filter_source" value="any" />'.$str[46].'</td></tr>';
    $data .= '<tr><td></td><td><input style="background: none; border: none;" type="radio" ';
    if ($pd['filter_source'] == 'select') {
        $data .= 'checked="checked" ';
    }
    $data .= 'name="filter_source" value="select" />'.$str[47].': <br /><select class="source_dropdown" multiple="multiple" size="10" name="source[]">'.$pd['source_options'];
    $data .= '</select></td></tr>';
    if ($pd['form_type'] == 'add') {
        $data .= '<tr><td colspan="2"><input type="submit" name="add" value="'.$str[17].'" /></td></tr>';
    }
    else {
        $data .= '<tr><td colspan="2"><br /><input type="submit" name="edit" value="'.$str[18].'" />
                  <input type="submit" name="cancel" value="'.$str[19].'" />
                  <input type="submit" name="delete" value="'.$str[20].'" /></td></tr>';
    }
    $data .= '</table></form>';
    if ($pd['form_type'] == 'add') {
        $data .= '<b>'.$str[21].'</b>';
        $data .= '<div><form method="post" action="?page=filters">';
        $data .= '<table cellpadding="0" cellspacing="0" id="options">';
        $data .= '<tr><td class="opt_leftcol">'.$str[22].'</td><td>
                  <input type="checkbox" ';
        if ($pd['auto_filter']) {
            $data .= 'checked ';
        }
        $data .= ' name="auto_filter" value="1" /></td></tr>';
        $data .= '<tr><td class="opt_leftcol">'.$str[23].'</td><td>
                  <input type="checkbox" ';
        if ($pd['filter_menu']) {
            $data .= 'checked="checked" ';
        }
        $data .= ' name="filter_menu" value="1" /></td></tr>';
        $data .= '<tr><td colspan="2"><input type="submit" name="filter_update_settings" value="'.$str[18].'" /></td></tr>';
        $data .= '</table></form></div>';
    }
    $data .= '</div></div>';
    return $data;
}
function targets($tools) {
    return array(
        'all'     => $tools->str[32],
        'body'    => $tools->str[33],
        'to'      => $tools->str[34],
        'cc'      => $tools->str[35],
        'from'    => $tools->str[36],
        'subject' => $tools->str[37],
        'date'    => $tools->str[38]
    );
}
function process_delete_action($post, $tools, $filters) {
    $res    = false;
    $notice = 'An Unknown Error Occured';
    if (isset($filters[$post['id']])) {
        $new_filters = Array();
        foreach ($filters as $i => $vals) {
            if ($i == $post['id']) {
                continue;
            }
            $new_filters[] = $vals;
        }
        $tools->save_setting('filters', $new_filters);
        $notice = $tools->str[29];
        $res = true;
    }
    $tools->send_notice($notice);
    return $res;
}
function process_edit_action($post, $tools, $filters) {
    $notice = 'An Unknown Error Occured';
    $pattern = '';
    $mailbox = '';
    $target = '';
    $source = array();
    $days_old = 0;
    $res = false;
    if (isset($filters[$post['id']])) {
        foreach (array('filter_action', 'pattern', 'mailbox', 'target') as $v) {
            if (isset($post[$v])) {
                if (!trim($v)) {
                    $notice = ucfirst($v).' cannot be blank';
                }
                else {
                    $$v = $post[$v];
                }
            }
            else {
                $notice = ucfirst($v).' is required';
            }
        }
        if (isset($post['time_filter']) && $post['time_filter'] == 'newer' || $post['time_filter'] == 'older') {
            if ($post['time_filter'] == 'older') {
                if (isset($post['days_old']) && abs(intval($post['days_old'])) > 0) {
                    $days_old = 0 - abs(intval($post['days_old']));
                }
            }
            if ($post['time_filter'] == 'newer') {
                if (isset($post['days_old2']) && abs(intval($post['days_old2'])) > 0) {
                    $days_old = abs(intval($post['days_old2']));
                }
            }
        }
        if (isset($post['target_type']) && $post['target_type'] == 'custom') {
            if (isset($post['custom_header']) && trim($post['custom_header']) != '') {
                $target = 'HEADER '.trim($post['custom_header']);
            }
        }
        if (isset($post['filter_source']) && $post['filter_source'] == 'select') {
            if (isset($post['source']) && is_array($post['source'])) {
                $source = $post['source'];
            }
        }
        if ($filter_action == 'move') {
            if (($pattern || $days_old) && $target && $mailbox) {
                $vals = array($pattern, $target, $mailbox, $filter_action, $days_old, $source);
                $filters[$post['id']] = $vals;
                $tools->save_setting('filters', $filters);
                $notice = $tools->str[30];
                $res = true;
            }
        }
        else {
            if (($pattern || $days_old) && $target) {
                $vals = array($pattern, $target, $mailbox, $filter_action, $days_old, $source);
                $filters[$post['id']] = $vals;
                $tools->save_setting('filters', $filters);
                $notice = $tools->str[30];
                $res = true;
            }
        }
    }
    $tools->send_notice($notice);
    return $res;
}
function process_add_action($post, $tools, $filters) {
    $notice = 'An Unknown Error Occured';
    $pattern = '';
    $mailbox = '';
    $target = '';
    $source = array();
    $time_filter = '';
    $days_old = 0;
    $target_type = 'standard';
    $res = false;
    foreach (array('filter_action', 'pattern', 'mailbox', 'target') as $v) {
        if (isset($post[$v])) {
            if (!trim($post[$v])) {
                $notice = ucfirst($v).' cannot be blank';
                $$v = false;
            }
            else {
                $$v = $post[$v];
            }
        }
        else {
            $notice = ucfirst($v).' is required';
            $$v = false;
        }
    }
    if (isset($post['time_filter']) && $post['time_filter'] == 'newer' || $post['time_filter'] == 'older') {
        if ($post['time_filter'] == 'older') {
            if (isset($post['days_old']) && abs(intval($post['days_old'])) > 0) {
                $days_old = 0 - abs(intval($post['days_old']));
            }
        }
        if ($post['time_filter'] == 'newer') {
            if (isset($post['days_old2']) && abs(intval($post['days_old2'])) > 0) {
                $days_old = abs(intval($post['days_old2']));
            }
        }
    }
    if (isset($post['target_type']) && $post['target_type'] == 'custom') {
        if (isset($post['custom_header']) && trim($post['custom_header']) != '') {
            $target = 'HEADER '.trim($post['custom_header']);
        }
    }
    if (isset($post['filter_source']) && $post['filter_source'] == 'select') {
        if (isset($post['source']) && is_array($post['source'])) {
            $source = $post['source'];
        }
    }
    if ($filter_action == 'move') {
        if (($pattern || $days_old) && $target && $mailbox) {
            $filters[] = array($pattern, $target, $mailbox, $filter_action, $days_old, $source);
            $tools->save_setting('filters', $filters);
            $notice = $tools->str[31];
            $res = true;
        }
    }
    else {
        if (($pattern || $days_old) && $target) {
            $filters[] = array($pattern, $target, $mailbox, $filter_action, $days_old, $source);
            $tools->save_setting('filters', $filters);
            $notice = $tools->str[31];
            $res = true;
        }
    }
    $tools->send_notice($notice);
    return $res;
}
?>
