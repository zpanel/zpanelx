<?php

/*  url_action_class.php: Process $_GET vars
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
class fw_user_action_page extends fw_user_action {
function url_action_options($get) {
    global $user;
    global $imap;
    global $sticky_url;
    if ($user->logged_in) {
        do_work_hook('options_page_start');
        $user->page_data['options_link_class'] ='current_page';
        $user->dsp_page = 'options';
        $user->page_title .= ' | '.$user->str[4].' |';
        $user->page_data['folders'] = $_SESSION['folders'];
        $user->page_data['top_link'] = '<a href="'.$sticky_url.'#top">'.$user->str[186].'</a>';
    }
}
}

class site_page_options extends site_page {
function print_general_options() {
    global $conf;
    global $date_formats;
    global $time_formats;
    global $start_pages;
    global $phpversion;
    ksort($conf['site_themes']);
    $times = array_reverse($time_formats);
    $times['none'] = '-';
    $times = array_reverse($times);
    $dates = array_reverse($date_formats);
    $dates['h'] = $this->user->str[300];
    $dates = array_reverse($dates);
    $modes = array(1 => 'Default', 2 => 'Simple');
    $vals = $this->pd['settings'];
    $data = '<tr><td class ="opt_leftcol">'.$this->user->str[196].'</td><td><select name="theme">';
    foreach ($conf['site_themes'] as $name => $atts) {
        $data .= '<option ';
        if (isset($vals['theme']) && $vals['theme'] == $name) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$name.'">'.ucfirst(str_replace('_', ' ', $name)).'</option>';
    }
    $data .= '</select></td></tr><tr><td class ="opt_leftcol">'.$this->user->str[197].'</td><td><select name="display_mode">';
    foreach ($modes as $name => $v) {
        $data .= '<option ';
        if (isset($vals['display_mode']) && $vals['display_mode'] == $name) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$name.'">'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    if ($phpversion >= 5.2) {
        $data .= '<tr><td class="opt_leftcol">'.$this->user->str[198].'</td><td>'.$this->print_tz_dropdown($vals['timezone']).'</td></tr>';
    }
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[199].'</td><td><select name="date_format">';
    foreach ($date_formats as $i => $v) {
        $data .= '<option ';
        if (isset($this->pd['settings']['date_format']) && $this->pd['settings']['date_format'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$this->user->htmlsafe($v).'</option>';
    }
    $data .= '</select> ';
    $data .= '<select name="time_format">';
    foreach ($times as $i => $v) {
        $data .= '<option ';
        if (isset($this->pd['settings']['time_format']) && $this->pd['settings']['time_format'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$this->user->htmlsafe($v).'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[200].'</td><td><select name="mailbox_date_format">';
    foreach ($dates as $i => $v) {
        $data .= '<option ';
        if (isset($this->pd['settings']['mailbox_date_format']) && $this->pd['settings']['mailbox_date_format'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select> <select name="mailbox_date_format_2">';
    foreach ($times as $i => $v) {
        $data .= '<option ';
        if (isset($this->pd['settings']['mailbox_date_format_2']) && $this->pd['settings']['mailbox_date_format_2'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class ="opt_leftcol">'.$this->user->str[201].'</td><td><select name="start_page">';
    foreach ($start_pages as $i => $v) {
        $data .= '<option ';
        if (isset($vals['start_page']) && $vals['start_page'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$this->user->str[$v].'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[202].'</td><td><select name="font_size">';
    if (!$vals['font_size']) {
        $vals['font_size'] = 100;
    }
    for ($i=150;$i>40;$i -= 5) {
        $data .= '<option ';
        if ($vals['font_size'] == $i) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$i.'%</option>';
    }
    $data .= '</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[203].'</td><td><select name="lang">';
    foreach ($this->user->langs as $i => $v) {
        $data .= '<option ';
        if (isset($this->pd['settings']['lang']) && $this->pd['settings']['lang'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[204].' <span class="js1">*</span></td><td><input type="checkbox" value="1" name="enable_delete_warning" ';
    if ($vals['enable_delete_warning']) {
        $data .= 'checked="checked" ';
    }
    $data .= ' /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[205].'</td><td><input name="show_folder_list" type="checkbox" ';
    if ($vals['show_folder_list']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[499].'</td><td><input name="disable_folder_icons" type="checkbox" ';
    if (isset($vals['disable_folder_icons']) && $vals['disable_folder_icons']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[531].'</td><td><input name="new_window_icon" type="checkbox" ';
    if (isset($vals['new_window_icon']) && $vals['new_window_icon']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[504].' <span class="js1">*</span></td><td><input name="disable_checked_js" type="checkbox" ';
    if (isset($vals['disable_checked_js']) && $vals['disable_checked_js']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[498].'</td><td><input name="disable_list_icons" type="checkbox" ';
    if (isset($vals['disable_list_icons']) && $vals['disable_list_icons']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[206].'</td><td><input name="auto_switch_simple_mode" type="checkbox" ';
    if (isset($vals['auto_switch_simple_mode']) && $vals['auto_switch_simple_mode']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[427].'</td><td><input name="expunge_on_exit" type="checkbox" ';
    if (isset($vals['expunge_on_exit']) && $vals['expunge_on_exit']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';

    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[447].'</td><td><input name="hide_deleted_messages" type="checkbox" ';
    if (isset($vals['hide_deleted_messages']) && $vals['hide_deleted_messages']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';

    $data .= do_display_hook('general_options_table');
    $data .= '<tr><td class="opt_leftcol"><a href="?page=profile">'.$this->user->str[207].'</a></td></tr>';
    $data .= '<tr><td colspan="4" class="opt_leftcol"><br />'.
             '<input type="submit" name="update_settings" value="'.$this->user->str[193].'" /><br /><br /></td></tr>';
    return $data;
}
function print_folder_options() {
    $vals = $this->pd['settings'];
    $update_intervals = array( 30 => $this->user->str[536], 60  => $this->user->str[333], 120  => $this->user->str[334], 300  => $this->user->str[335], 600  => $this->user->str[336], 1200 => $this->user->str[337]);
    $trash_folder = array();
    $sent_folder = array();
    $draft_folder = array();
    if (isset($vals['trash_folder']) && $vals['trash_folder']) {
        $trash_folder = array($vals['trash_folder']);
    }
    if (isset($vals['sent_folder']) && $vals['sent_folder']) {
        $sent_folder = array($vals['sent_folder']);
    }
    if (isset($vals['draft_folder']) && $vals['draft_folder']) {
        $draft_folder = array($vals['draft_folder']);
    }
    $data = '<tr><td class="opt_leftcol">'.$this->user->str[208].'</td><td><select name="trash_folder"><option value="">'.$this->user->str[242].'</option>'.$this->print_folder_option_list(
            $this->pd['folders'], false, 0, $trash_folder, true, false, 'selectable', array(), false, array(), true).'</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[209].'</td><td><select '.
            'name="sent_folder"><option value="">'.$this->user->str[242].'</option>'.$this->print_folder_option_list($this->pd['folders'], false, 0, $sent_folder, true, false, 'selectable', array(), false, array(), true).'</select></td></tr>'.
            '<tr><td class="opt_leftcol">'.$this->user->str[210].'</td><td><select name="draft_folder"><option value="">'.$this->user->str[242].'</option>'.$this->print_folder_option_list(
            $this->pd['folders'], false, 0, $draft_folder, true, false, 'selectable', array(), false, array(), true).'</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[211].'</td><td><select '.
            'name="folder_style"><option value="1">'.$this->user->str[301].'</option><option value="2" ';
    if ($vals['folder_style'] == 2) { $data .= 'selected="selected" '; }
    $data .= '>'.$this->user->str[302].'</option></select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[212].'</td><td><select name="folder_detail"><option value="0" >'.$this->user->str[242].'</option>
        <option value="1" ';
    if ($vals['folder_detail'] == 1) { $data .= 'selected="selected" '; }
    $data .= '>'.$this->user->str[303].'</option><option value="2" ';
    if ($vals['folder_detail'] == 2) { $data .= 'selected="selected" '; }
    $data .= '>'.$this->user->str[304].'</option></select></td></tr>';
    if ($this->user->ajax_enabled) {
        $data .= '<tr><td class="opt_leftcol">'.$this->user->str[213].' <span class="js1">*</span></td><td><select name="ajax_update_interval"><option value="0">'.$this->user->str[242].'</option>';
        foreach ($update_intervals as $i => $v) {
            $data .= '<option ';
            if ($vals['ajax_update_interval'] == $i) { $data .= 'selected="selected" '; }
            $data .= 'value="'.$i.'">'.$v.'</option>';
        }
        $data .= '</select></td></tr>';
    }
    $data .= do_display_hook('folder_options_table');
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[434].'</td><td><input type="checkbox" name="subscribed_only" value="1" ';
    if (isset($vals['subscribed_only']) && $vals['subscribed_only']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr>';
    $data .= '<tr><td colspan="4" class="opt_leftcol"><br /><input type="submit" name="update_settings" value="'.$this->user->str[193].'" /><br /><br /></td></tr>';
    return $data;
}
function print_message_view_options() {
    global $small_header_options;
    global $prev_next_actions;
    $vals = $this->pd['settings'];
    $font_families = array( 'monospace', 'serif', 'sans-serif', 'cursive', 'fantasy');
    $data = '<tr><td class="opt_leftcol">'.$this->user->str[214].'</td><td><input name="text_links" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['text_links']) && $this->pd['settings']['text_links']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr><tr><td class="opt_leftcol">'.$this->user->str[215].'</td><td><input name="text_email" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['text_email']) && $this->pd['settings']['text_email']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr><tr><td class="opt_leftcol">'.$this->user->str[216].'</td><td><input name="hl_reply" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['hl_reply']) && $this->pd['settings']['hl_reply']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr><tr><td class="opt_leftcol">'.$this->user->str[217].'</td><td><input name="html_first" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['html_first']) && $this->pd['settings']['html_first']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr><tr><td class="opt_leftcol">'.$this->user->str[455].'</td><td><input name="short_message_parts" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['short_message_parts']) && $this->pd['settings']['short_message_parts']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr><tr><td class="opt_leftcol">'.$this->user->str[429].'</td><td><input name="remote_image" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['remote_image']) && $this->pd['settings']['remote_image']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr><tr><td class="opt_leftcol">'.$this->user->str[451].' <span class="js1">*</span></td><td><input name="message_window" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['message_window']) && $this->pd['settings']['message_window']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr><tr><td class="opt_leftcol">'.$this->user->str[430].'</td><td><select name="default_message_action">';
    foreach ($prev_next_actions as $v => $num) {
        $data .= '<option value="'.$v.'" ';
        if (isset($vals['default_message_action']) && $v == $vals['default_message_action']) { $data .= 'selected="selected" '; }
        $data .= '>'.$this->user->str[$num].'</option>';
    }
    $data .= '</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[218].'</td><td><select name="font_family">';
    foreach ($font_families as $v) {
        $data .= '<option value="'.$v.'" ';
        if ($v == $vals['font_family']) { $data .= 'selected="selected" '; }
        $data .= '>'.$v.'</option>';
    }
    $data .= '</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[219].'</td><td><input name="image_thumbs" type="checkbox" ';
    if ($vals['image_thumbs']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr><tr><td class="opt_leftcol">'.$this->user->str[220].'</td><td><input name="full_headers_default" type="checkbox" ';
    if ($vals['full_headers_default']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr><tr><td class="opt_leftcol">'.$this->user->str[221].'</td><td><select size="10" class="small_headers" multiple="multiple" name="small_headers[]">';
    foreach ($small_header_options as $v) {
        $data .= '<option value="'.$v.'" ';
        if (in_array($v, $this->pd['settings']['small_headers'])) {
            $data .= 'selected="selected" ';
        }
        $data .= '>'.ucfirst($v).'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= do_display_hook('message_options_table');
    $data .= '<tr><td colspan="4" class="opt_leftcol"><br /><input type="submit" name="update_settings" value="'.$this->user->str[193].'" /><br /><br /></td></tr>';
    return $data;
}
function print_new_page_options() {
    $update_intervals = array( 30 => $this->user->str[536], 60  => $this->user->str[333], 120  => $this->user->str[334], 300  => $this->user->str[335], 600  => $this->user->str[336], 1200 => $this->user->str[337]);
    $vals = $this->pd['settings'];
    $data = '<tr><td class="opt_leftcol">'.$this->user->str[225].' <span class="js2">**</span></td><td><select name="new_page_refresh"><option value="0">'.$this->user->str[242].'</option>';
    foreach ($update_intervals as $i => $v) {
        $data .= '<option ';
        if ($i == $this->pd['settings']['new_page_refresh']) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[226].'</td><td><input type="checkbox" name="hide_folder_on_empty" value="1" ';
    if (isset($vals['hide_folder_on_empty']) && $vals['hide_folder_on_empty']) { $data .= 'checked="checked" '; }
    $data .= '/></td></tr>';
    $data .= do_display_hook('new_options_table');
    $data .= '<tr><td colspan="4" class="opt_leftcol"><br /><input type="submit" name="update_settings" value="'.$this->user->str[193].'" /><br /><br /></td></tr>';
    return $data;
}
function print_compose_options() {
    global $smtp_auth_mechs;
    global $smtp_dsp_mechs;
    global $text_encodings;
    global $text_formats;
    global $conf;
    $update_intervals = array( 60  => $this->user->str[333], 120  => $this->user->str[334], 300  => $this->user->str[335], 600  => $this->user->str[336], 1200 => $this->user->str[337]);
    $vals = $this->pd['settings'];
    $data = '<tr><td class="opt_leftcol">'.$this->user->str[227].'</td><td><select name="compose_text_format">';
    foreach ($text_formats as $i => $v) {
        $data .= '<option ';
        if (isset($vals['compose_text_format']) && $vals['compose_text_format'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$this->user->str[$v].'</option>';
    }
    $data .= '</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[228].'</td><td><select name="compose_text_encoding">';
    foreach ($text_encodings as $i => $v) {
        $data .= '<option ';
        if (isset($vals['compose_text_encoding']) && $vals['compose_text_encoding'] == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$this->user->str[$v].'</option>';
    }
    $data .= '</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[229].'</td><td><input type="checkbox" name="compose_hide_mailer" value="1" ';
    if (isset($vals['compose_hide_mailer']) && $vals['compose_hide_mailer']) {
        $data .= 'checked="checked" ';
    } 
    $data .= '/></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[448].'</td><td><input type="checkbox" name="delete_draft" value="1" ';
    if (isset($vals['delete_draft']) && $vals['delete_draft']) {
        $data .= 'checked="checked" ';
    } 
    $data .= '/></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[450].' <span class="js1">*</span></td><td><input name="compose_window" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['compose_window']) && $this->pd['settings']['compose_window']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[520].' <span class="js1">*</span></td><td><input name="close_on_send" type="checkbox" value="1" ';
    if (isset($this->pd['settings']['close_on_send']) && $this->pd['settings']['close_on_send']) {
        $data .= 'checked="checked" ';
    }
    $data .= '/></td></tr>';
    if ($this->user->ajax_enabled) {
        $data .= '<tr><td class="opt_leftcol">'.$this->user->str[232].' <span class="js1">*</span></td><td><select name="compose_autosave"><option value="0">'.$this->user->str[242].'</option>';
        foreach ($update_intervals as $i => $v) {
            $data .= '<option ';
            if (isset($vals['compose_autosave']) && $vals['compose_autosave'] == $i) { $data .= 'selected="selected" '; }
            $data .= 'value="'.$i.'">'.$v.'</option>';
        }
        $data .= '</select></td></tr>';
    }
    if (isset($conf['smtp_authentication_type']) && $conf['smtp_authentication_type'] == 'user') {
        $data .= '<tr><td class="opt_leftcol">'.$this->user->str[233].'</td><td><select name="smtp_auth">';
        foreach ($smtp_auth_mechs as $v) {
            $data .= '<option ';
            if (isset($vals['smtp_auth']) && $vals['smtp_auth'] == $v) {
                $data .= 'selected="selected" ';
            }
            $data .= 'value="'.$v.'">'.$this->user->str[$smtp_dsp_mechs[$v]].'</option>';
        }
        $smtp_user = '';
        $smtp_pass = '';
        if (isset($vals['smtp_user'])) {
            $smtp_user = $this->user->htmlsafe($vals['smtp_user']);
        }
        if (isset($vals['smtp_pass'])) {
            $smtp_pass = $this->user->htmlsafe($vals['smtp_pass']);
        }
        $data .= '</select></td></tr><tr><td class="opt_leftcol">'.$this->user->str[234].'</td><td><input type="text" name="smtp_user" value="'.$smtp_user.'" /></td></tr>';
        $data .= '<tr><td class="opt_leftcol">'.$this->user->str[235].'</td><td><input type="password" name="smtp_pass" value="'.$smtp_pass.'" /></td></tr>';
    }
    $data .= do_display_hook('compose_options_table');
    $data .= '<tr><td colspan="4" class="opt_leftcol"><br /><input type="submit" name="update_settings" value="'.$this->user->str[193].'" /><br /><br /></td></tr>';
    return $data;
}
function print_mailbox_options() {
    $vals = $this->pd['settings'];
    $data = '<tr><td class="opt_leftcol">'.$this->user->str[222].'</td><td><input name="mailbox_per_page_count" size="3" type="text" value="'.$vals['mailbox_per_page_count'].'" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[223].'</td><td><input name="mailbox_controls_bottom" type="checkbox" ';
    if (isset($vals['mailbox_controls_bottom']) && $vals['mailbox_controls_bottom']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[224].'</td><td><input name="mailbox_freeze" type="checkbox" ';
    if (isset($vals['mailbox_freeze']) && $vals['mailbox_freeze']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[423].'</td><td><input name="always_expunge" type="checkbox" ';
    if (isset($vals['always_expunge']) && $vals['always_expunge']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[424].'</td><td><input name="selective_expunge" type="checkbox" ';
    if (isset($vals['selective_expunge']) && $vals['selective_expunge']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[435].'</td><td><input name="top_page_links" type="checkbox" ';
    if (isset($vals['top_page_links']) && $vals['top_page_links']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[461].'</td><td><input name="full_mailbox_option" type="checkbox" ';
    if (isset($vals['full_mailbox_option']) && $vals['full_mailbox_option']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[497].' <span class="js1">*</span>'.'</td><td><input name="mailbox_update" type="checkbox" ';
    if (isset($vals['mailbox_update']) && $vals['mailbox_update']) { $data .= 'checked="checked" '; }
    $data .= 'value="1" /></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[518].'</td><td><input name="trim_from_fld" size="2" type="text" ';
    if (isset($vals['trim_from_fld']) && intval($vals['trim_from_fld'])) { $data .= 'value="'.intval($vals['trim_from_fld']).'" '; }
    else { $data .= 'value="0" '; }
    $data .= '/></td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$this->user->str[523].'</td><td><input name="trim_subject_fld" size="2" type="text" ';
    if (isset($vals['trim_subject_fld']) && intval($vals['trim_subject_fld'])) { $data .= 'value="'.intval($vals['trim_subject_fld']).'" '; }
    else { $data .= 'value="0" '; }
    $data .= '/></td></tr>';
    $data .= do_display_hook('mailbox_options_table');
    $data .= '<tr><td colspan="4" class="opt_leftcol"><br /><input type="submit" name="update_settings" value="'.$this->user->str[193].'" /><br /><br /></td></tr>';
    return $data;
}
function print_tz_dropdown($tz) {
    $tz_vals = DateTimeZone::listIdentifiers();
    if (!$tz) {
        $tz = date_default_timezone_get();
    }
    $data = '<select id="timezones" name="timezone">';
    foreach ($tz_vals as $v) {
        $data .= '<option ';
        if ($tz == $v) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$v.'">'.$v.'</option>';
    }
    $data .= '</select>';
    return $data;
}
}?>
