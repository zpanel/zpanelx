<?php

/*  site_page_class.php: Output parts of the page
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

class site_page extends fw_page_data {

function site_page() {
    $this->init_base_data();
}
function print_hm_html_head() {
    global $conf;
    global $page_id;
    $url_base = $conf['url_base'];
    $host_name = $conf['host_name'];
    $http_prefix = $conf['http_prefix'];
    if ($this->html_content_type== 'xhtml') {
        $data = '<meta http-equiv="Content-Type" content="application/xhtml-xml;charset=UTF-8" />';
    }
    else {
        $data = '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />';
    }
    if (isset($this->pd['plugin_css'])) {
        foreach ($this->pd['plugin_css'] as $val) {
            $data .= '<link rel="stylesheet" type="text/css" href="'.$val.'" />';
        }
    }
    if (isset($this->pd['plugin_style'])) {
        foreach ($this->pd['plugin_style'] as $val) {
            $data .= $val;
        }
    }
    if ($this->user->logged_in && isset($this->pd['settings']['font_size'])) {
        $fs = $this->pd['settings']['font_size'];
        $data .= '<style type="text/css">body, select, option, textarea, input { font-size: '.$fs.'%; }</style>';
    }
    else {
        $fs = '100%';
    }
    if (isset($this->pd['settings']['disable_folder_icons']) && $this->pd['settings']['disable_folder_icons']) {
        $data .= '<style type="text/css">.folder_lists ul li, .folder_lists ul li ul li{background-image: none !important;}</style>';
    }
    if (!$this->user->ajax_enabled && $this->dsp_page == 'new' && $this->pd['settings']['new_page_refresh']) {
        $data .= '<meta HTTP-EQUIV="refresh" content="'.$this->pd['settings']['new_page_refresh'].
                 ';url='.$http_prefix.'://'.$host_name.$url_base.'?page=new&amp;mailbox='.urlencode($this->pd['mailbox']).'" />';
    }
    return $data;
}
function print_inline_js() {
    global $conf;
    global $page_id;
    global $allow_js_exception;
    $url_base = $conf['url_base'];
    $host_name = $conf['host_name'];
    $http_prefix = $conf['http_prefix'];
    $data = '';
    if ($this->dsp_page == 'login' || ($this->dsp_page == 'main' && !$this->user->logged_in)) {
        $data .= '<script type="text/javascript">
        '.$this->start_cdata().'window.onload = function() {if (document.getElementById("login")) {document.getElementById("user").focus();}}'.
        $this->end_cdata().'</script>';
    }
    elseif ($this->user->logged_in) {
        $data .= 'var page_title="'.$this->user->page_title.'";';
        if ($this->user->ajax_enabled) {
            if (isset($this->pd['plugin_ajax']) && !empty($this->pd['plugin_ajax'])) {
                foreach ($this->pd['plugin_ajax'] as $vals) {
                    $data .= 'function x_ajax_'.$vals['plugin'].'_'.$vals['name'].'(){sajax_do_call("ajax_'.$vals['plugin'].'_'.$vals['name'].
                            '",x_ajax_'.$vals['plugin'].'_'.$vals['name'].'.arguments);} function hm_ajax_'.$vals['plugin'].'_'.$vals['name'].'(';
                    for ($i=0;$i<$vals['args'];$i++) {
                        $data .= 'arg_'.$i.', ';
                    }
                    $data = rtrim($data, ', ');
                    $data .= ') {';
                    if ($vals['div_id']) {
                        $data .= 'function callback_'.$vals['plugin'].'_'.$vals['name'].'(output) {';
                        $data .= 'if (document.getElementById("'.$vals['div_id'].'")) { document.getElementById("'.$vals['div_id'].'").innerHTML = output; }';
                        $data .= ' } '; 
                    }
                    else {
                        $data .= 'callback_'.$vals['plugin'].'_'.$vals['name'].';';
                    }
                    $data .= 'x_ajax_'.$vals['plugin'].'_'.$vals['name'].'("'.$vals['plugin'].'", ';
                    for ($i=0;$i<$vals['args'];$i++) {
                        $data .= 'arg_'.$i.', ';
                    }
                    $data .= 'callback_'.$vals['plugin'].'_'.$vals['name'].'); };';
                }
            }
            $data .= 'var update_delay = '.$this->pd['settings']['ajax_update_interval'].';';
            if ($this->pd['settings']['new_page_refresh'] && $this->dsp_page == 'new') {
                $data .= 'var do_new_page_refresh = '.$this->pd['settings']['new_page_refresh'].';';
            }
            else {
                $data .= 'var do_new_page_refresh = 0;';
            }
            if ($this->pd['settings']['dropdown_ajax']) {
                $data .= 'var do_folder_dropdown = \''.$this->user->htmlsafe($this->pd['mailbox']).'\';';
            }
            else {
                $data .= 'var do_folder_dropdown = 0;';
            }
            if ($this->pd['settings']['show_folder_list'] && isset($this->pd['settings']['folder_list_ajax']) && $this->pd['settings']['folder_list_ajax']) {
                $data .= 'var do_folder_list = 1;';
            }
            else {
                $data .= 'var do_folder_list = 0;';
            }
            if (isset($this->pd['settings']['compose_autosave']) && $this->pd['settings']['compose_autosave'] &&
                $this->dsp_page == 'compose') {
                $data .= 'var c_autosave = '.(5 + $this->pd['settings']['compose_autosave']).';';
                $data .= '
                    function get_compose_message() {
                        var type = "";
                        var res = "";
                        if (document.getElementById("compose_content_type")) {
                            type = document.getElementById("compose_content_type").value;
                        }
                        switch (type) {';
                if (isset($this->pd['plugin_get_compose_message']) && is_array($this->pd['plugin_get_compose_message'])) {
                    foreach ($this->pd['plugin_get_compose_message'] as $vals) {
                        $data .= 'case "'.$vals[0].'": try { res = '.$vals[1].' } catch (e) {}; break;';
                    }
                }
                $data .= '
                            default: 
                                try {res = document.getElementById("compose_message").value;} catch (e) {}
                                break;
                        }
                        return res;
    
                    }';
            }
            else {
                $data .= 'var c_autosave = 0;';
            }
            $data .= 'var update_notice = "'.$this->user->str[417].'";';
            if (isset($this->pd['settings']['mailbox_update']) && $this->pd['settings']['mailbox_update'] && $this->dsp_page == 'mailbox') {
                $data .= 'var mailbox_page = "'.$this->pd['mailbox_page'].'";';
                if (isset($this->pd['sort_by'])) {
                    $data .= 'var sort_by = "'.$this->pd['sort_by'].'";';
                }
                if (isset($this->pd['filter_by'])) {
                    $data .= 'var filter_by = "'.$this->pd['filter_by'].'";';
                }
            }
            else {
                $data .= 'var mailbox_page = -1;';
                $data .= 'var sort_by = false;';
                $data .= 'var filter_by = false;';
            }
        }
        if (isset($this->pd['settings']['disable_checked_js']) && $this->pd['settings']['disable_checked_js']) {
            $data .= 'var disable_hl = 1;';
        }
        else {
            $data .= 'var disable_hl = 0;';
        }
        if ($this->pd['new_window']) {
            $data .= 'var new_win = true;';
        }
        else {
            $data .= 'var new_win = false;';
        }
        $data .= 'window.onload = function(e) { start_timer();';
        if ($this->dsp_page == 'message') {
            $data .= 'if (document.getElementById("msg_iframe")) {autoAdjustIFrame(document.getElementById("msg_iframe"));}';
        }
        elseif ($this->dsp_page == 'compose') {
            $data .= 'if (document.getElementById("compose_to")) {document.getElementById("compose_to").focus();}';
        }
        if (((isset($this->pd['settings']['new_window_icon']) && $this->pd['settings']['new_window_icon']) ||
            (isset($this->pd['settings']['message_window']) && $this->pd['settings']['message_window']))
            && $this->parent_refresh && $this->dsp_page == 'message') {
            $data .= 'refresh_parent();';
        }
        if (isset($this->pd['plugin_js_onload'])) {
            foreach ($this->pd['plugin_js_onload'] as $val) {
                $data .= $val;
            }
        }
        $snd_evnt = false;
        $snd_evnt2 = false;
        if ($this->dsp_page == 'compose') {
            $this->pd['plugin_js_events']['send_btn']['onclick'][] = array('name' => 'core', 'handler' => 'send_message', 'arg' => '"'.$this->user->str[463].'"');
            $this->pd['plugin_js_events']['send_btn2']['onclick'][] = array('name'=> 'core', 'handler' => 'send_message', 'arg' => '"'.$this->user->str[463].'"');
        }
        if (isset($this->pd['plugin_js_events']) && !empty($this->pd['plugin_js_events'])) {
            foreach ($this->pd['plugin_js_events'] as $element_id => $events) {
                foreach ($events as $event_type => $vals) {
                    if ($element_id == 'window' && $event_type == 'onload') {
                        foreach ($vals as $v) {
                            if (!$allow_js_exception) {
                                $data .= 'try { ';
                            }
                            $data .= $v['handler'].'(e);';
                            if (!$allow_js_exception) {
                                $data .= '} catch(e) {};';
                            }
                        }
                        continue;
                    }
                    if (stristr($element_id, 'document')) {
                        if (!$allow_js_exception) {
                            $data .= 'try {';
                        }
                        $data .= ' if ('.$element_id.') { '.$element_id.'.'.$event_type .' = function(e) {var retval = true;';
                    }
                    else {
                        if (!$allow_js_exception) {
                            $data .= 'try {';
                        }
                        $data .= ' if (document.getElementById("'.$element_id.'")) { document.getElementById("'.
                            $element_id.'").'.$event_type.' = function(e) {var retval = true;';
                    }
                    foreach ($vals as $v) {
                        if (isset($v['arg'])) {
                            $data .= ' if (retval) {retval = '.$v['handler'].'('.$v['arg'].');}';
                        }
                        else {
                            $data .= ' if (retval) {retval = '.$v['handler'].'(e);}';
                        }
                    }
                    $data .=  'return retval;}}';
                    if (!$allow_js_exception) {
                        $data .= '}catch(e) {};';
                    }
                }
            }
        }
        $data .= '};';
        if (isset($this->pd['js_update_functions']) && !empty($this->pd['js_update_functions'])) {
            $data .= 'var js_update_functions = [';
            foreach ($this->pd['js_update_functions'] as $i => $v) {
                $data .= "'".$v."'";
                if ($i < (count($this->pd['js_update_functions']) -1)) {
                    $data .= ',';
                }
            }
            $data .= '];';
        }
        else {
            $data .= 'var js_update_functions = false;';
        }
        if (isset($this->pd['inline_plugin_js'])) {
            foreach ($this->pd['inline_plugin_js'] as $val) {
                $data .= $val;
            }
        }
        $data = '<complex-'.$page_id.'><script type="text/javascript">'.$this->start_cdata().$this->prep_js_str($data).$this->end_cdata().'</script></complex-'.$page_id.'>';
        $data = $this->print_plugin_script_tags().$data;
    }
    return $data;
}
function prep_js_str($str) {
    $str = preg_replace("/[\r\n]/", " ", $str);
    $str = preg_replace("/\s{2,}/", " ", $str);
    return $str;
}
function print_plugin_script_tags() {
    $data = '';
    if (isset($this->pd['plugin_js'])) {
        foreach ($this->pd['plugin_js'] as $val) {
            $data .= $this->prep_js_str($val);
        }
    }
    return $data;
}
function print_clock() {
    global $page_id;
    global $date_formats;
    global $time_formats;
    if (isset($this->pd['settings']['time_format'])) {
        if (isset($time_formats[$this->pd['settings']['time_format']])) {
            $time_format = $this->pd['settings']['time_format'];
        }
        else {
            $time_format = false;
        }
    }
    else {
        $time_format = 'h:i:s A';
    }
    if (isset($this->pd['settings']['date_format']) && isset($date_formats[$this->pd['settings']['date_format']])) {
        $date_format = $this->pd['settings']['date_format'];
    }
    else {
        $date_format = 'm/d/y';
    }
    if ($time_format) {
        return '<div>'.date($date_format).'&#160;'.date($time_format).'</div>';
    }
    else {
        return '<div>'.date($date_format).'</div>';
    }
}
function print_notices($page=false) {
    $data = '';
    if (!empty($this->notices)) {
        foreach ($this->notices as $v) {
            $data .= $v.'<br />';
        }
    }
    return $data;
}
function print_sort_form($name=false, $disabled=false) {
    global $sort_types;
    global $client_sort_types;
    global $sort_filters;
    global $page_id;
    $data = '<complex-'.$page_id.'>';
    if (stristr($this->pd['imap_capability'], 'SORT')) {
        $stype = 'server';
        $types = $sort_types;
    }
    else {
        $stype = 'client';
        $types = $client_sort_types;
    }
    if ($name !== false) {
        $data .= '<select ';
        if ($disabled) {
            $data .= 'disabled="disabled" ';
        }
        $data .= 'name="sort_by['.$name.']">';
    }
    else {
        $data .= '<input type="hidden" name="page" value="mailbox" /><input type="hidden" name="mailbox" value="'.
                 $this->user->htmlsafe($this->pd['mailbox']).'" />'.$this->user->str[39].' <select ';
        $data .= 'name="sort_by" ';
        if (isset($this->pd['frozen_folders'][$this->pd['mailbox']])) { $data .= 'class="disabled_sort" disabled="disabled" '; }
        $data .= 'onchange="display_notice(this, \'Resorting Mailbox...\');">';
    }
    foreach ($types as $i => $v) {
        $data .= '<option ';
        if ($i == $this->pd['sort_by']) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$this->user->str[$v].'</option>';
    }
    $data .= '</select> ';
    if ($name === false) {
        if ($stype == 'server') {
            $data .= '&#160;'.$this->user->str[38].' <select ';
            if (isset($this->pd['frozen_folders'][$this->pd['mailbox']])) { $data .= 'class="disabled_sort" disabled="disabled" '; }
            $data .= 'onchange="display_notice(this, \'Filtering Mailbox...\');" name="filter_by">';
            foreach ($sort_filters as $i => $v) {
                $data .= '<option ';
                if ($i == $this->pd['filter_by']) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$i.'">'.$this->user->str[$v].'</option>';
            }
            $data .= '</select>';
        }
        $data .= '<noscript><input type="submit" value="'.$this->user->str[39].'" /></noscript>';
    }
    $data .= '</complex-'.$page_id.'>';
    return $data;
}
function print_message_controls() {
    global $sticky_url;
    global $page_id;
    $data = '<input type="hidden" name="current_mailbox" value="'.$this->user->htmlsafe($this->pd['mailbox']).'" />';
    if (isset($this->pd['settings']['trash_folder']) && $this->pd['settings']['trash_folder'] == $this->pd['mailbox']) {
        $data .= '<input type="submit" onclick="return hm_confirm(\''.$this->user->str['421'].'\');" class="empty_trash_btn" name="empty_trash" value="'.$this->user->str[420].'" />';
    }
    $data .= '<input type="submit" '.
            'class="delete_btn" name="delete_message" onclick="return hm_confirm(\''.$this->user->str[63].'\');" value="'.$this->user->str[59].'" />';
    if ((isset($this->pd['settings']['always_expunge']) && $this->pd['settings']['always_expunge']) ||
         !isset($this->pd['settings']['trash_folder']) || !$this->pd['settings']['trash_folder']) {
        if (isset($this->pd['settings']['selective_expunge']) && $this->pd['settings']['selective_expunge']) {
            $msg = $this->user->str[422];
        }
        else {
            $msg = $this->user->str[525];
        }
        $data .= '<input type="submit" class="undelete_btn" name="undelete_message" value="'.$this->user->str[433].'" /><input type="submit" class="expunge_btn" name="expunge_messages" onclick="return '.
                 'hm_confirm(\''.$msg.'\');" value="'.$this->user->str[68].'" />';
    }
    $data .= '<input type="submit" class="read_btn" name="read_message" value="'.$this->user->str[33].'" /><input type="submit" class="unread_btn" name="unread_message" value="'.$this->user->str[34].'" />'.
              '<input type="submit" class="flag_btn" name="flag_message" value="'.$this->user->str[35].'" /><input type="submit" class="unflag_btn" name="unflag_message" value="'.$this->user->str[65].'" />'.
              '<input type="submit" class="attach_btn" name="attach_message" value="'.'Attach'.'" ';
        if ($this->pd['compose_onclick']) {
            $data .= 'onclick="return redirect_msg_controls();" /><input type="hidden" name="attach_new_win" value="1" />';
        }
        else {
            $data .= '/>';
        } 
        $data .= '<input type="submit" class="move_btn" name="move_message" value="'.$this->user->str[66].'" /><input type="submit" class="copy_btn" name="copy_message" value="'.$this->user->str[67].'" />
              &#160;&#160;'.$this->user->str[538].': &#160;<select name="destination_folder">'.
              $this->print_folder_option_list($this->pd['folders'], false, 0, array($this->pd['current_destination']), true, true).'</select><complex-'.$page_id.'>';
    if (isset($_SESSION['uid_cache'][$this->pd['mailbox']]['uids']) && isset($this->pd['settings']['full_mailbox_option']) &&
        $this->dsp_page == 'mailbox' && $this->pd['settings']['full_mailbox_option']) {
        $data .= '&#160; '.$this->user->str[462].': <select id="full_mailbox" onchange="check_full_mailbox(\''.$this->user->str[458].'\');" name="full_mailbox"><option value="0">'.$this->user->str[460].'</option><option value="1">'.$this->user->str[459].'</option></select>';
        if (isset($this->pd['show_all_msg']) && $this->pd['show_all_msg']) {
            $data .= '&#160; <a id="show_pages" class="show_all" href="'.str_replace('&amp;show_all_msg=1', '', $sticky_url).'">'.$this->user->str[502].'</a>';
        }
        else {
            $data .= '&#160; <a id="show_all" class="show_all" href="'.$sticky_url.'&amp;show_all_msg=1">'.$this->user->str[501].'</a>';
        }
    }
    $data .= '</complex-'.$page_id.'>';
    return $data;
}
function print_message_controls2() {
    $data = '';
    if (isset($this->pd['settings']['trash_folder']) && $this->pd['settings']['trash_folder'] == $this->pd['mailbox']) {
        $data .= '<input type="submit" onclick="return hm_confirm(\''.$this->user->str[421].'\');" class="empty_trash_btn" name="empty_trash" value="'.$this->user->str[420].'" />';
    }
    $data .= '<input type="submit" class="delete_btn" name="delete_message" onclick="'.
             'return hm_confirm(\''.$this->user->str[63].'\');" value="'.$this->user->str[59].'" />';
    if ((isset($this->pd['settings']['always_expunge']) && $this->pd['settings']['always_expunge']) ||
        !isset($this->pd['settings']['trash_folder']) || !$this->pd['settings']['trash_folder']) {
        $data .= '<input type="submit" class="undelete_btn" name="undelete_message" value="'.$this->user->str[433].'" /><input type="submit" class="expunge_btn" name="expunge_messages" onclick="return '.
                 'hm_confirm(\''.$this->user->str[422].'\');" value="'.$this->user->str[68].'" />';
    }
    $data .= '<input type="submit" class="read_btn" name="read_message" value="'.$this->user->str[33].'" /><input type="submit" class="unread_btn" name="unread_message" value="'.$this->user->str[34].'" />
              <input type="submit" class="flag_btn" name="flag_message" value="'.$this->user->str[35].'" /><input type="submit" class="unflag_btn" name="unflag_message" value="'.$this->user->str[65].'" />
              <input type="submit" class="attach_btn" name="attach_message" value="'.'Attach'.'" ';
        if ($this->pd['compose_onclick']) {
            $data .= 'onclick="return redirect_msg_controls();" /><input type="hidden" name="attach_new_win" value="1" />';
        }
        else {
            $data .= '/>';
        } 
        $data .= '<input type="submit" class="move_btn" name="move_message2" value="'.$this->user->str[66].'" /><input type="submit" class="copy_btn" name="copy_message2" value="'.$this->user->str[67].'" />
              &#160;&#160;'.$this->user->str[538].': &#160;<select name="destination_folder2">'.
              $this->print_folder_option_list($this->pd['folders'], false, 0, array($this->pd['current_destination']), true, true).'</select>';
    return $data;
}
function prep_selected($folders, $selected, $no_current, $allow_no_selection) {
    if (!empty($selected) && $selected[0]) {
        return array_flip($selected);
    }
    elseif (!$allow_no_selection) {
        foreach ($folders as $name => $vals) {
            if ($no_current && $this->pd['mailbox'] == $name) {
                continue;
            }
            if (!$vals['noselect']) {
                return array($name => '');
            }
        }
    }
    return array();
}
function print_folder_option_list($folders, $parent=false, $i=0, $selected=array(), $clean=false, $no_current=false, $selectable_type='selectable', $exclude_list=array(), $ignore_parents=false, $folder_check = array(), $allow_no_selection = false) {
    $data = '';
    global $used;
    global $conf;
    if ($i == 0) {
        $selected = $this->prep_selected($folders, $selected, $no_current, $allow_no_selection);
        $folder_check = array_flip($this->pd['settings']['folder_check']);
    }
    if ($this->pd['settings']['folder_style'] == 1) {
        $pre = str_repeat('&#160;', ($i*5));
    }
    else {
        $pre = '';
    }
    if (!$parent) {
        $used = array();
    }
    foreach ($folders as $vals) {
        $disabled_check = false;
        if (!isset($vals['name'])) {
            continue;
        }
        if (isset($used[$vals['name']])) {
            continue;
        }
        $used[$vals['name']] = '';
        $classes = array();
        if ((!$vals['hidden'] && $vals['parent'] == $parent) || $ignore_parents) {
            $data .= '<option ';
            if (isset($selected[$vals['realname']]) && (!$no_current || $vals['realname'] != $this->pd['mailbox'])) {
                $data .= 'selected="selected" ';
            }
            switch ($selectable_type) {
                case 'selectable':
                    if ($vals['realname'] != 'INBOX' && $vals['noselect']) {
                        $data .= 'disabled="disabled" ';
                        $classes[] = 'disabled_option';
                        $disabled_check = true;
                    }
                    break;
                case 'no_kids':
                    if ($vals['has_kids'] || $vals['realname'] == 'INBOX') {
                        $data .= 'disabled="disabled" ';
                        $classes[] = 'disabled_option';
                        $disabled_check = true;
                    }
                    break;
                case 'noselect':
                    if (!$vals['noselect'] || $vals['realname'] == 'INBOX') {
                        $data .= 'disabled="disabled" ';
                        $classes[] = 'disabled_option';
                        $disabled_check = true;
                    }
                    break;
                case 'custom':
                    if (in_array($vals['name'], $exclude_list)) {
                        $data .= 'disabled="disabled" ';
                        $classes[] = 'disabled_option';
                        $disabled_check = true;
                    }
                    break;
                case 'all':
                default:
                    break;

            }
            if ($this->pd['mailbox'] == $vals['realname'] && $no_current && $selectable_type == 'selectable') {
                $data .= 'disabled="disabled" ';
                $classes[] = 'disabled_option';
            }
            $cnt = '';
            if (!$clean) {
                if ($this->pd['settings']['folder_detail'] == 1 && isset($folder_check[$vals['name']])) {
                    if (isset($vals['status']['unseen'])) {
                        $cnt = ' ('.$vals['status']['unseen'].') ';
                    }
                }
                if ($this->pd['settings']['folder_detail'] == 2 && isset($folder_check[$vals['name']])) {
                    if (isset($vals['status']['unseen'])) {
                        $cnt = ' ('.$vals['status']['unseen'];
                    }
                    else {
                        $cnt = '(-';
                    }
                   if (isset($vals['status']['messages'])) {
                        $cnt .= '/'.$vals['status']['messages'].') ';
                    }
                }
            }
            if ($vals['special'] && !$disabled_check) {
                $classes[] = 'special_folder';
            }
            if ($vals['name'] == 'INBOX') {
                $vals['basename'] = 'INBOX';
            }
            if ($vals['name'] == $conf['imap_folder_prefix']) {
                $name = 'INBOX';
            }
            elseif ($this->pd['settings']['folder_style'] == 1) {
                $name = $vals['basename'];
            }
            else {
                $name = $vals['name'];
            }
            if ($ignore_parents) {
                $name = $vals['name'];
            }
            if (!empty($classes)) {
                $data .= 'class="'.join(' ', $classes).'" ';
            }
            if ($name == 'INBOX') {
                $data .= 'value="'.$this->user->htmlsafe($vals['name']).'">'.$pre.' '.$this->user->str[436].$cnt.'</option>';
            }
            else {
                $data .= 'value="'.$this->user->htmlsafe($vals['name']).'">'.$pre.' '.$this->user->htmlsafe($name, 0, 0, 1).$cnt.'</option>';
            }
        }
        if ($vals['has_kids'] || $vals['noselect']) {
            $subfolders = array();
            foreach ($this->pd['folders'] as $atts) {
                    if (!isset($atts['basename'])) {
                        continue;
                    }
                    if ($vals['realname'] == $atts['parent']) {
                        $subfolders[$atts['realname']] = $atts;
                    }
            }
            if (!empty($subfolders)) {
                $i++;
                $data .= $this->print_folder_option_list($subfolders, $vals['realname'], $i, $selected, $clean, $no_current, $selectable_type, $exclude_list, $ignore_parents, $folder_check); 
                $i--;
                $subfolders = array();
            }
        }
    }
    return $data;
}
function print_folder_dropdown($folders) {
    global $page_id;
    $data = '<form method="get" action=""><input type="hidden" name="page" value="mailbox" /><input type="hidden" id="enable_delete_warning" value="'.
            $this->pd['settings']['enable_delete_warning'].'" /><select onchange="display_notice(this, \''.$this->user->str[503].'\');" name="mailbox">'.
            $this->print_folder_option_list($folders, false, 0, array($this->pd['mailbox'])).'</select> &#160;'.
            '<input id="goto_mailbox" type="submit" value="'.$this->user->str[25].'" /></form> &#160;';
    if (isset($this->user->use_cookies) && !$this->user->use_cookies) {
        $data .= '<input type="hidden" id="sid" value="'.session_id().'" />';
    }
    return $data;
}
function print_folder_list($folders, $parent=false, $i=0, $inbox=false, $folder_check = array()) {
    $i++;
    global $conf;
    global $sticky_url;
    $data = '';
    if ($inbox && $inbox == $parent) {
        $data .= '<div class="inbox_list folder_list"><ul>';
    }
    elseif ($this->pd['settings']['folder_style']  == 2) {
        $data .= '<div class="flat_folder_lists"><ul>';
    }
    else {
        $data .= '<div class="folder_lists"><ul>';
    }
    $sid = '';
    if (!$this->user->use_cookies && isset($_GET['rs'])) {
        $sid = '&amp;PHPSESSID='.session_id();
    } 
    if ($i == 1) {
        $folder_check = array_flip($this->pd['settings']['folder_check']);
    }
    foreach ($folders as $vals) {
        if (!isset($vals['name'])) {
            continue;
        }
        $hash = md5($vals['name']);
        if ($vals['name'] == $conf['imap_folder_prefix']) {
            $inbox = true;
            if (!isset($_SESSION['folder_state'][$hash])) {
                $_SESSION['folder_state'][$hash] = 1;
            }
        }
        else {
            $inbox = false;
        }
        if (!$vals['hidden'] && $vals['parent'] == $parent) {
            $li_class = 'folder';
            if ($this->pd['settings']['folder_style'] == 1) {
                if ($vals['has_kids'] || $vals['noselect']) {
                    if ((isset($_SESSION['folder_state'][$hash]) && $_SESSION['folder_state'][$hash]) ||
                        (isset($this->pd['mailbox']) && substr($this->pd['mailbox'], 0, strlen($vals['name'])) == $vals['name'])) {
                        $li_class = ' open_folder';
                    }
                }
            }
            if (isset($this->pd['mailbox']) && ($this->pd['mailbox'] == $vals['name'] ||
                $this->pd['mailbox'] == $vals['realname'])) {
                $data .= '<li class="current_mailbox"><div class="'.$li_class.'">';
            }
            else {
                $data .= '<li><div class="'.$li_class.'">';
            }
            if ($this->pd['settings']['folder_style'] == 1) {
                if ($vals['has_kids'] || $vals['noselect']) {
                    if ((isset($_SESSION['folder_state'][$hash]) && $_SESSION['folder_state'][$hash]) ||
                        (isset($this->pd['mailbox']) && substr($this->pd['mailbox'], 0, strlen($vals['name'])) == $vals['name'])) {
                        $state = 1;
                    }
                    else {
                        $state = 0;
                    }
                    if (strstr($sticky_url, 'folder_state')) {
                        $url = preg_replace("/folder_state=\d+/", 'folder_state='.$hash.$state, $sticky_url);
                    }
                    else {
                        $url = $sticky_url.'&amp;folder_state='.$hash.$state;
                    }
                    $data .= '<a href="'.$url.'" class="expand_link" id="folder_link_'.$hash.
                             '" onclick="expand_folder(\'folder_div_'.$hash.'\', \'folder_link_'.$hash.'\'); ';
                    if ($this->user->ajax_enabled) {
                        $data .= 'save_folder_state(\''.$hash.'\');';
                    }
                    $data .= 'return false;">';
                    if ($state) {
                        $data .= '-';
                    }
                    else {
                        $data .= '+';
                    }
                    $data .= '</a> ';
                }
                else {
                    $data .= '<a class="expand_link" style="visibility: hidden;">+</a> ';
                }
            }
            $data .= '</div>';
            if ($vals['name'] == 'INBOX') {
                $vals['basename'] = 'INBOX';
            }
            if (!$vals['noselect']) {
                $data .= '<a ';
                if ($vals['special']) {
                    $data .= 'class="special_folder" ';
                }
                $data .= 'href="?page=mailbox'.$sid.'&amp;mailbox='.urlencode($vals['name']).'">';
            }
            elseif ($vals['noselect'] && $vals['special']) {
                $data .= '<span class="special_folder">';
            }
            if ($conf['imap_folder_prefix'] == $vals['name']) {
                $name = 'INBOX';
            }
            elseif ($this->pd['settings']['folder_style'] == 1) {
                $name = $vals['basename'];
            }
            else {
                $name = $vals['name'];
            }
            if ($name == 'INBOX') {
                $name = $this->user->str[436];
            }
            else {
                $name = $this->user->htmlsafe($name, 0, 0, 1);
            }
            $data .= $name;
            if (!$vals['noselect']) {
                $data .= '</a>';
            }
            elseif ($vals['noselect'] && $vals['special']) {
                $data .= '</span>';
            }
            if ($this->pd['settings']['folder_detail'] == 1) {
                if (isset($vals['status']['unseen']) && isset($folder_check[$vals['name']])) {
                    if ($vals['status']['unseen']) {
                        $data .= '  &#160;(<b>'.$vals['status']['unseen'].'</b>) ';
                    }
                    else {
                        $data .= '  &#160;('.$vals['status']['unseen'].') ';
                    }
                }
            }
            if ($this->pd['settings']['folder_detail'] == 2 && isset($vals['status']['messages']) && isset($folder_check[$vals['name']])) {
                if (isset($vals['status']['unseen'])) {
                    if ($vals['status']['unseen']) {
                        $data .= '  &#160;(<b>'.$vals['status']['unseen'].'</b>';
                    }
                    else {
                        $data .= '  &#160;('.$vals['status']['unseen'];
                    }
                }
                else {
                    $data .= ' &#160;( - ';
                }
                if (isset($vals['status']['messages']) && isset($folder_check[$vals['name']])) {
                    $data .= '/'.$vals['status']['messages'].') ';
                }
            }
            if ($vals['has_kids'] || $vals['noselect']) {
                $subfolders = array();
                foreach ($this->pd['folders'] as $atts) {
                    if (!isset($atts['basename'])) {
                        continue;
                    }
                    if ($vals['realname'] == $atts['parent']) {
                        $subfolders[$atts['realname']] = $atts;
                    }
                }
                if (!empty($subfolders)) {
                    if ($this->pd['settings']['folder_style'] == 1) {
                        $data .= '<div style="';
                        if ((isset($_SESSION['folder_state'][$hash]) && $_SESSION['folder_state'][$hash]) ||
                            (isset($this->pd['mailbox']) && substr($this->pd['mailbox'], 0, strlen($vals['name'])) == $vals['name'])) {
                            $data .= 'display: block;';
                        }
                        else {
                            $data .= 'display: none;';
                        }
                        $data .= '" id="folder_div_'.$hash.'">';
                    }
                    $data .= $this->print_folder_list($subfolders, $vals['realname'], $i, $inbox, $folder_check);
                    if ($this->pd['settings']['folder_style'] == 1) {
                        $data .= '</div>'; 
                    }
                }
            }
            $data .= '</li>';
        }
        $i++;
    }
    $data .= '</ul></div>';
    return $data;
}
function print_icon() {
    global $conf;
    global $page_id;
    if (isset($conf['site_logo'])) {
        $logo = $conf['site_logo'];
    }
    else {
        $logo = '<span class="hm_span">Hm<span class="super">2</span></span>';
    }
    $data = '<complex-'.$page_id.'>';
    $theme = 'default';
    if (isset($this->pd['settings']['theme'])) {
        $user_theme = $this->pd['settings']['theme'];
        if (isset($conf['site_themes'][$user_theme])) {
            if ($conf['site_themes'][$user_theme]['icons'] && $conf['site_themes'][$user_theme]['icons'] !== 'default') {
                $theme = $user_theme;
            }    
            elseif (!$conf['site_themes'][$user_theme]['icons']) {
                return $data;
            }
        }
    }
    if ($this->user->logged_in) { $data .= '<a href="?page=about&amp;mailbox='.urlencode($this->pd['mailbox']).'">'; }
    /*if ($this->user->user_agent_class == 'msie') {
        $data .= '<img src="images/spacer.png" style="width: 30px; height: 30px; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=themes/'.$theme.'/icons/';
    }
    else {
        $data .= '<img src="themes/'.$theme.'/icons/';
    }*/
    $class = 'default';
    if ($this->user->logged_in) {
        $class = 'mailbox';
        switch ($this->dsp_page) {
            case 'mailbox':
                if (isset($this->pd['settings']['trash_folder']) && $this->pd['mailbox'] == $this->pd['settings']['trash_folder']) {
                    if (!empty($this->pd['header_list'])) {
                        //$data .= 'trash';
                        $class = 'trash';
                    }
                    else {
                        $data .= 'empty_trash';
                        $class = 'empty_trash';
                    }
                }
                else {
                    if (!empty($this->pd['header_list'])) {
                        //$data .= 'mailbox';
                    }
                    else {
                        //$data .= 'empty_mailbox';
                        $class = 'empty_mailbox';
                    }
                }
                break;
            case 'login':
            case 'logout':
                //$data .= 'default';
                $class = 'default';
                break;
            case 'message':
            case 'about':
            case 'folders':
            case 'search':
            case 'compose':
            case 'profile':
            case 'new':
            case 'options':
            case 'contacts':
                //$data .= $this->dsp_page;
                $class = $this->dsp_page;
                break;
            default:
                //$data .= 'mailbox';
                break;
        }
    }
    /*else {
        $data .= 'default';
    }
    if ($this->user->user_agent_class == 'msie') {
        $data .= '.png, sizingMethod=scale);" />';
    }
    else {
        $data .= '.png" alt="-" title="Hastymail 2" />';
    }*/
    $data .= '<span class="'.$class.'_icon page_icon">&#160;</span>';
    $data .= '&#160;'.$logo;
    if ($this->user->logged_in) { $data .= '</a>'; }
    $data .= '</complex-'.$page_id.'>';
    return $data;
}
function print_imap_server_opts() {
    global $conf;
    $data = '';
    $alt_servers = get_alt_servers($conf);
    if (!empty($alt_servers)) {
        $data .= $this->user->str[273].'<br /><select class="logintext" name="imap_server">';
        $data .= '<option value="0">';
        if (isset($conf['imap_display_name']) && $conf['imap_display_name']) {
            $data .= $conf['imap_display_name'];
        }
        $data .= '</option>';
        foreach ($alt_servers as $i => $vals) {
            if (isset($vals['imap_server'])) {
                $data .= '<option value="'.$i.'">';
                if (isset($vals['imap_display_name'])) {
                    if (isset($vals['imap_display_name']) && $vals['imap_display_name']) {
                        $data .= $vals['imap_display_name'];
                    }
                    else {
                        $data .= $vals['imap_server'];
                    }
                }
                $data .= '</option>';
            }
        }
        $data .= '</select>';
    }
    return '<div class="alt_server">'.$data.'</div>';
}
function print_mailbox_list() {
    global $show_all_max;
    $data = $this->print_mailbox_list_rows($this->msg_list_flds, $this->pd['header_list'], $this->onclick, $this->pd['mailbox']);
    /*if (count($this->pd['header_list']) < $this->pd['settings']['mailbox_per_page_count'] && $this->pd['settings']['mailbox_per_page_count'] != $show_all_max) {
        $max = $this->pd['settings']['mailbox_per_page_count'] - count($this->pd['header_list']);
        for ($i=0;$i <= $max; $i++) {
            $data .= '<tr><td class="mbx_subject">&#160;</td></tr>';
        }
    }*/
    $_SESSION['toggle_all'] = false;
    $_SESSION['toggle_uids'] = array();
    $_SESSION['toggle_boxes'] = array();
    return $data;
}
function print_mailbox_list_headers() {
    global $page_id;
    $data = '';
    if (!$this->show_headers) {
        return $data;
    }
    if (isset($this->pd['page_count'])) {
        $pcnt = '<input type="hidden" id="page_count" name="page_count" value="'.$this->pd['page_count'].'" />';
    }
    else {
        $pcnt = '';
    }
    $labels = array(
        'subject_cell' => $this->user->str[13],
        'from_cell' => $this->user->str[56],
        'date_cell' => $this->user->str[58],
        'size_cell' => $this->user->str[57],
        'checkbox_cell' => $pcnt.'<input onclick="toggle_all(); return false;" id="toggle_all_button" type="submit" name="toggle_all_button" value="X" />',
        'indicators_cell' => '&#160;',
        'image_cell' => '&#160;',
        'plugin_cell' => '&#160;',
        'folder_cell' => $this->user->str[256]
    );
    if ((isset($this->pd['settings']['sent_folder']) && $this->dsp_page == 'mailbox' &&
        $this->pd['mailbox'] == $this->pd['settings']['sent_folder']) || (isset($this->pd['settings']['draft_folder']) &&
        $this->dsp_page == 'mailbox' && $this->pd['mailbox'] == $this->pd['settings']['draft_folder'])) {
        $labels['from_cell'] = $this->user->str[55];
    }
    if (isset($this->pd['settings']['disable_list_icons']) && $this->pd['settings']['disable_list_icons']) {
        array_pop($labels);
    }
    foreach ($this->msg_list_flds as $v) {
        if (isset($labels[$v])) {
            $data .= '<th class="'.$v.'_heading">'.$labels[$v].'</th>';
        }
    }
    return $data;
}
function print_mailbox_list_rows($cols, $rows, $onclick, $mailbox, $n=1, $ignore_sent=false) {
    global $page_id;
    $data = '';
    $search_res = array();
    $date_format_2 = false;
    $c_start  = '<complex-'.$page_id.'>';
    $c_end = '</complex-'.$page_id.'>';
    $td_end = $c_start.'</td>'.$c_end;
    $s_start  = '<simple-'.$page_id.'>';
    $s_end = '</simple-'.$page_id.'>';
    if (isset($this->pd['settings']['mailbox_date_format'])) {
        $date_format = $this->pd['settings']['mailbox_date_format'];
        if ($date_format != 'r' && $date_format != 'h') {
            if (isset($this->pd['settings']['mailbox_date_format_2'])) {
                $date_format_2 = $this->pd['settings']['mailbox_date_format_2'];
            }
        }
        elseif ($date_format == 'h') {
            $date_format = false;
        }
    }
    else {
        $date_format = false;
    }
    if (!$this->user->use_cookies) {
        $sid = '&amp;PHPSESSID='.session_id();
    } 
    else {
        $sid = '';
    }
    if ($this->dsp_page != 'search' && isset($this->pd['search_results'][$mailbox])) {
        $search_res = $this->pd['search_results'][$mailbox];
    }
    if (!isset($this->pd['mailbox_page'])) {
        $this->pd['mailbox_page'] = '';
    }
    if (!isset($this->pd['filter_by'])) {
        $this->pd['filter_by'] = '';
    }
    $list_count = count($rows);
    if (isset($_SESSION['toggle_uids'])) {
        $toggle_uids = $_SESSION['toggle_uids'];
    }
    else {
        $toggle_uids = array();
    }
    if (isset($_SESSION['toggle_all'])) {
        $toggle_all = $_SESSION['toggle_all'];
    }
    else {
        $toggle_all = false;
    }
    if (isset($_SESSION['toggle_boxes'])) {
        $toggle_boxes = $_SESSION['toggle_boxes'];
    }
    else {
        $toggle_boxes = array();
    }
    foreach ($rows as $vals) {
        if (isset($this->pd['settings']['hide_deleted_messages']) &&
            $this->pd['settings']['hide_deleted_messages'] &&
            stristr($vals['flags'], 'deleted')) {
            continue;
        }
        $vals['mailbox'] = $mailbox;
        $message_url = '?page=message'.$sid.'&amp;uid='.$vals['uid'].'&amp;mailbox_page='.
                       $this->pd['mailbox_page'].'&amp;sort_by='.$this->pd['sort_by'].
                       '&amp;filter_by='.$this->pd['filter_by'].'&amp;mailbox='.
                       urlencode($mailbox);
        $new_window = '';
        if ($onclick) {
            if (isset($this->pd['settings']['message_window']) && $this->pd['settings']['message_window']) {
                $js = 'onclick="open_window(\''.$message_url.'&amp;new_window=1&amp;parent_refresh=1'.$sid.'\', 1024, 900, '.$vals['uid'].');
                       return false;" onmouseover="this.style.cursor=\'pointer\'"';
            }
            else {
                $js = 'onclick="document.location.href=\''.$message_url.'\';" onmouseover="this.style.cursor=\'pointer\'"';
            }
        }
        else {
            $js = false;
            if (isset($this->pd['settings']['message_window']) && $this->pd['settings']['message_window']) {
                $new_window = 'onclick="open_window(\''.$message_url.'&amp;new_window=1&amp;parent_refresh=1'.$sid.'\', 1024, 900, '.$vals['uid'].'); return false;"';
            }
        }
        if (!$ignore_sent &&  ((isset($this->pd['settings']['sent_folder']) && $mailbox == $this->pd['settings']['sent_folder']) ||
            isset($this->pd['settings']['draft_folder']) && $mailbox == $this->pd['settings']['draft_folder'])) {
            $from = clean_from($vals['to']);
            $full_from = $vals['to'];
        }
        else {
            $from = clean_from($vals['from']);
            $full_from = $vals['from'];
        }
        if (isset($this->pd['settings']['trim_subject_fld']) && $this->pd['settings']['trim_subject_fld']) {
            $subject_len = $this->pd['settings']['trim_subject_fld'];
        }
        else {
            $subject_len = 0;
        }
        if (isset($this->pd['settings']['trim_from_fld']) && $this->pd['settings']['trim_from_fld']) {
            $trim_len = $this->pd['settings']['trim_from_fld'];
        }
        else {
            $trim_len = 0;
        }
        $xtra_class = '';
        if (!empty($search_res) && in_array($vals['uid'], $search_res)) {
            $xtra_class = 'search_res ';
        }
        if (stristr($vals['flags'], 'seen')) {
            $class_prefix= 'mbx_';
        }
        else {
            $class_prefix= 'mbx_unseen_';
        }
        if ($n == $list_count) {
            $xtra_class .= ' last_row ';
        }
        if (!trim($vals['subject'])) {
            $vals['subject'] = $this->user->str[524]; 
        }
        $indicators = '&#160;&#160;';
        if (stristr($vals['content-type'], 'multipart') && !stristr($vals['content-type'], 'alternative')) {
            $indicators .= '<span class="multi_ind">+&#160;</span>';
        }
        if (stristr($vals['flags'], 'answered')) {
            $indicators .= '<span class="reply_ind">r&#160;&#160;</span>';
        }
        if (stristr($vals['flags'], 'flagged')) {
            $indicators .= '<span class="flag_ind">f&#160;&#160;</span>';
            $xtra_class .= ' flagged ';
        }
        if (trim($vals['x-priority']) == '1') {
            $indicators .= '<span class="important_ind">!&#160;&#160;</span>';
        }
        if (!empty($search_res) && in_array($vals['uid'], $search_res)) {
            $indicators .= '<span class="search_ind">*&#160;&#160;</span>';
        }
        if ($indicators) {
            $indicators = '<span class="'.$xtra_class.'indicators">'.$indicators.'</span>';
        }
        $subj_post = '';
        $subj_pre = '';
        if (isset($this->pd['thread_data'][$vals['uid']])) {
            $ind = $this->pd['thread_data'][$vals['uid']]['level'] - 1;
            $subj_pre = str_repeat('&#160;', 5*$ind);
            /*if (!$this->pd['thread_data'][$vals['uid']]['parent']) {
                if (isset($this->pd['thread_data'][$vals['uid']]['reply_count'])) {
                    $subj_post .= ' <span class="reply_count">replies: <b>'.$this->pd['thread_data'][$vals['uid']]['reply_count'].'</b></span>';
                }
                else {
                    $subj_post .= ' <span class="reply_count">replies: 0</span>';
                }
            }*/
        }
        if (stristr($vals['flags'], 'deleted')) {
            $subj_post .= '</div>';
            $subj_pre .= '<div class="deleted_message">';
        }
        
        $indicators_cell = '';
        $image_cell = '';
        $subject_cell = '';
        $from_cell = '';
        $date_cell = '';
        $size_cell = '';
        $checkbox_cell = '';
        $data .= $c_start.'<tr class="mbx_row">'.$c_end;
        $indicators_cell = $c_start.'<td '.$js.' class="'.$xtra_class.$class_prefix.'indicators">';
        $indicators_cell .= $indicators.$c_end.$td_end;
        $checkbox_cell = $c_start.'<td class="'.$xtra_class.$class_prefix.'checkbox">';
        if (!isset($this->pd['settings']['disable_list_icons']) || !$this->pd['settings']['disable_list_icons']) {
            $image_cell = $c_start.'<td class="'.$xtra_class.$class_prefix.'image_cell"><div></div>';
            if (!isset($this->pd['settings']['message_window']) || !$this->pd['settings']['message_window']) {
                if (isset($this->pd['settings']['new_window_icon']) && $this->pd['settings']['new_window_icon']) {
                    $image_cell .= '<a href="'.$message_url.'" onclick="open_window(\''.$message_url.'&amp;new_window=1&amp;parent_refresh=1\', 1024, 900, '.$vals['uid'].'); return false;" '.
                        'title="'.$this->user->str[530].'"><span class="new_window_icon"></span></a> ';
                }
            }
            $image_cell .= '</td>'.$c_end;
        }
        if (isset($this->pd['last_message_read'][$mailbox]) &&
            $this->pd['last_message_read'][$mailbox] == $vals['uid']) {
            $checkbox_cell .= '<span class="last_read">&gt;</span>';
        }
        else {
            $checkbox_cell .= '<span class="last_read_hidden">&gt;</span>';
        }
        $checkbox_cell .= $c_end.'<input type="hidden" id="mailboxes-'.  $vals['uid'].'" name="mailboxes['.$vals['uid'].']" value="'.
                          $this->user->htmlsafe($mailbox, false, false, true).'" /><input type="checkbox" ';
        if ($toggle_all && !in_array($vals['uid'], $toggle_uids) && isset($toggle_boxes[$vals['uid']]) && $toggle_boxes[$vals['uid']] == $mailbox) {
            $checkbox_cell .= 'checked="checked" ';
        }
        elseif (isset($_GET['toggle_folder']) && $_GET['toggle_folder'] == $mailbox) { $checkbox_cell .= 'checked="checked" '; }
        $checkbox_cell .= 'id="message_'.$n.'" name="uids[]" value="'.$vals['uid'].'" onclick="save_checked_state('.$n.
            ');" /><input type="hidden" name="mailboxes['.$vals['uid'].']" value="'.$this->user->htmlsafe($mailbox, false, false, true).'" />'.$td_end;


        $subject_cell = $c_start.'<td class="'.$xtra_class.$class_prefix.'subject">';
        $subject_cell .= '<div '.$js.' class="sub_div">'.$c_end.$subj_pre.'<a title="'.$this->user->htmlsafe($vals['subject'], $vals['charset'], true).'" '.$new_window.' href="'.$message_url.'">'.
            trim_htmlstr($this->user->htmlsafe($vals['subject'], $vals['charset'], true), $subject_len).'</a>'.
            $subj_post.$c_start.'</div>'.$c_end.$td_end.$s_start.'<br />'.$s_end;
        $plugin_cell = $c_start.'<td class="'.$xtra_class.$class_prefix.'plugin_cell">'.$c_end.'<div>'.do_display_hook('msglist_after_subject', $vals).'</div>'.$c_start.'</td>'.$c_end;
        $from_cell = $c_start.'<td '.$js.' class="'.$xtra_class.$class_prefix.'from">'.$c_end.'<div title="'.
            $this->user->htmlsafe($full_from, $vals['charset'], true, false, false, false, true).'" class="from_inner">'.
            trim_htmlstr($this->user->htmlsafe($from, $vals['charset'], true, false, false, false, true), $trim_len).'</div>'.$td_end.$s_start.'<br />'.$s_end;
        $date_cell = $c_start.'<td '.$js.' class="'.$xtra_class.$class_prefix.'date" >'.
            $c_end.print_time2($vals['date'], $date_format, $date_format_2).$td_end.$s_start.'<br /><br />'.$s_end;
        $size_cell = $c_start.'<td '.$js.' class="'.$xtra_class.$class_prefix.'size">'.format_size($vals['size']/1024).'</td>'.$c_end;
        $folder_cell = $c_start.'<td '.$js.' class="'.$xtra_class.$class_prefix.'folder">'.$this->user->htmlsafe($mailbox).'</td>'.$c_end;
        foreach ($cols as $v) {
            if (isset($$v)) {
                $data .= $$v;
            }
        }
        $data .= $c_start.'</tr>'.$c_end;
        $n++;
    }
    return $data;
}
function print_contact_detail($message_view=false) {
    $data = '';
    if (!$message_view) {
        $data .= '<h4>Contact Details</h4>';
    }
    if (isset($this->pd['card_detail']) && !empty($this->pd['card_detail'])) {
        $data .= '<table id="card_details" cellpadding="0" cellspacing="0">';
        foreach ($this->pd['card_detail'] as $vals) {
            if (!trim($vals['value'])) {
                continue;
            }
            $data .= '<tr><th>'.$this->user->htmlsafe(ucfirst(strtolower($vals['name'])));
            if ($vals['group'] == 'N') {
                $data .= ' Name';
            }
            if (isset($vals['properties'][0])) {
                $data .= ' '.$vals['properties'][0];
            } 
            $data .= '</th><td>'.$this->user->htmlsafe($vals['value']).'</td></tr>';
        }
        if (!$message_view) {
            $data .= '<tr><td></td><td><a href="?page=contacts&amp;contacts_page='.$this->pd['contacts_page'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;edit_card='.$this->pd['card_id'].'#contactform">Edit</a>
                      / <a href="?page=contacts&amp;contacts_page='.$this->pd['contacts_page'].'&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;download_card='.$this->pd['card_id'].'">Export</a></td></tr>';
        }
        else {
            $data .= '<tr><td></td><td><a href="?page=contacts&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;import_card_attachment=1#contact_form">'.$this->user->str[146].'</a></td></tr>';
        }
        $data .= '</table>';
    }
    return $data;
}
}
?>
