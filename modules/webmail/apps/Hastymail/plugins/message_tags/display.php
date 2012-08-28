<?php

/*  display.php: Plugin file responsible for the output of XHTML into existing Hastymail pages.
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

/* output message view page tag list */
function message_tags_message_headers_bottom($tools) {
    global $page_id;
    if ($tools->get_setting('tags_disabled')) {
        return '';
    }
    $uid = $tools->get_current_message_uid();
    $tag_list = $tools->get_from_store('tag_header_list');
    $mailbox = $tools->get_mailbox();
    $res = '<complex-'.$page_id.'><tr><th>'.$tools->str[0].':</th><td colspan="2"><table style="border: none; padding: 0px; margin: 0px; width: 100%" cellpadding="0" cellspacing="0">
            <tr><td style="padding: 0px;">';
    if ($uid) {
        $tags = hm_new('tags', $tools);
        if (isset($tags->tag_map[$mailbox][$uid])) {
            $arg = join(' ', $tags->tag_map[$mailbox][$uid]);
            $label = $arg;
        }
        else {
            $arg = false;
            $label = '<span class="tag_image" title="'.$tools->str[1].'"></span>';
        }
        $res .= '<div class="tag_cell message_page"><a onclick="return edit_tags(\''.
                $uid.'\', this, \''.esc_sq($mailbox).'\');">'.$label.'</a></div>';
    }
    $res .= '</td><td style="padding: 0px;">';
    if (is_array($tag_list) && !empty($tag_list)) {
        $next = false;
        $prev = false;
        usort($tag_list, 'sort_date');
        foreach ($tag_list as $key => $msg) {
            if ($msg['uid'] == $uid && $msg['folder'] == $mailbox) {
                if (isset($tag_list[$key + 1])) {
                    $next = $tag_list[$key + 1];
                }
                if (isset($tag_list[$key - 1])) {
                    $prev = $tag_list[$key - 1];
                }
                break;
            }
        }
        $tag_url = array();
        foreach ($_SESSION['tag_page_list'] as $v) {
            $tag_url[] = join('+', $v);
        }
        $url_arg = join('^', $tag_url);
        $res .= '<div style="float: right;"><table cellpadding="0" cellspacing="0" class="mprev_next">';
        $res .= '<tr><td><a style="font-size: 85%; padding-right: 2px; text-decoration: none;" href="?page=message_tags&amp;tag='.$tools->display_safe(urlencode($url_arg)).'">Tag list ('.count($tag_list).')</a>:&#160;&#160;</td><td>';
        if ($prev) {
            $res .= '<a style="text-decoration: none;" href="?page=message&amp;uid='.$prev['uid'].'&amp;mailbox='.urlencode($prev['folder']).
                     '"><span class="pbutton">&#160;</span><simple-'.$page_id.
                     '> &lt; </simple-'.$page_id.'></a>';
        }
        else {
            $res .= '<a><span class="disabled_button pbutton">&#160;</span></a>';
        }
        if ($next) {
            $res .= '</td><td>&#160;&#160;<a style="text-decoration: none;" href="?page=message&amp;uid='.$next['uid'].'&amp;mailbox='.urlencode($next['folder']).
                     '"><span class="nbutton">&#160;</span><simple-'.$page_id.
                     '> &lt; </simple-'.$page_id.'></a>';
        }
        else {
            $res .= ' </td><td>&#160;&#160;<a><span class="disabled_button nbutton">&#160;</span></a>';
        }
        $res .= '</td></tr></table></div>';
    }
    $res .= '</td></tr></table></td></tr></complex-'.$page_id.'>';
    return $res;
}

/* output the menu link */
function message_tags_menu($tools, $args) {
    if (isset($args['message_tags_menu'])) {
        return $args['message_tags_menu'];
    }
    if ($tools->get_setting('tags_disabled')) {
        return '';
    }
    return ' <a class="message_tags_link" href="?page=message_tags">'.$tools->str[0].'</a>&nbsp; ';
}

/* output message list tag cell */
function message_tags_msglist_after_subject($tools, $msg_vals) {
    global $page_id;
    if ($tools->get_setting('tags_disabled')) {
        return '';
    }
    $tags = hm_new('tags', $tools);
    $tag_list = array();
    $res = '';
    if (isset($tags->tag_map[$msg_vals['mailbox']][$msg_vals['uid']])) {
        $arg = join(' ', $tags->tag_map[$msg_vals['mailbox']][$msg_vals['uid']]);
        $label = $tools->display_safe($arg);
    }
    else {
        $arg = false;
        $label = '<span class="tag_image" title="'.$tools->str[1].'"></span>';
    }
    $res = '</div><div style="float: right;"><complex-'.$page_id.'><div class="tag_cell"><a onclick="return edit_tags(\''.
           $msg_vals['uid'].'\', this, \''.esc_sq($msg_vals['mailbox']).'\');">'.$label.'</a></div></complex-'.$page_id.'>';
    return $res;
}

/* output options to the general section of the options page */
function message_tags_general_options_table($tools) {
    $opts = array(0 => $tools->str[3], 1 => $tools->str[5], 2 => $tools->str[6], 3 => $tools->str[7]);
    $tag_dsp = $tools->get_setting('tags_dsp');
    $disable = $tools->get_setting('tags_disabled');
    $data = '<tr><td class="opt_leftcol">'.$tools->str[8].'</td><td><input ';
    if ($disable) {
        $data .= 'checked="checked" ';
    }
    $data .= 'type="checkbox" name="disable_tags" value="1" />';
    $data .= '</td></tr>';
    $data .= '<tr><td class="opt_leftcol">'.$tools->str[4].'</td><td><select name="tags_dsp">';
    foreach ($opts as $i => $opt) {
        $data .= '<option ';
        if ($tag_dsp == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="'.$i.'">'.$opt.'</option>';
    }
    $data .= '</select></td></tr>';
    return $data;

}

/* output tag list below the folder list */
function message_tags_folder_list_bottom($tools) {
    if ($tools->get_setting('tags_disabled')) {
        return '';
    }
    if ($tools->get_setting('tags_dsp') == 2) {
        return print_tag_list($tools);
    }
    return '';
}

/* output tag list above the folder list */
function message_tags_folder_list_top($tools) {
    if ($tools->get_setting('tags_disabled')) {
        return '';
    }
    if ($tools->get_setting('tags_dsp') == 1 || $tools->get_setting('tags_dsp') == 3) {
        return print_tag_list($tools);
    }
    return '';
}

/* couple utility functions */
function esc_sq($str) {
    return str_replace("'", "\'", $str);
}
function esc_dq($str) {
    return str_replace('"', '\"', $str);
}
?>
