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

function url_action_message_tags($tools, $get, $post) {
    if (!$tools->logged_in() || $tools->get_setting('tags_disabled')) {
        $tools->page_not_found();
        return;
    }
    $tools->add_style('<link rel="stylesheet" type="text/css" href="?page=mailbox&amp;theme='.$tools->get_theme().'&amp;css=1" />');
    $tools->add_inline_js('
        function clear_rename_tag(tag) {
            document.getElementById("rename_"+tag).innerHTML = "";
            return false;
        }
        function submit_tag_rename(tag) {
            if( window.event ) {
                e = window.event;
            }
            if( typeof( e.keyCode ) == "number"  ) {
                e = e.keyCode;
            } else if( typeof( e.which ) == "number" ) {
                e = e.which;
            } else if( typeof( e.charCode ) == "number"  ) {
                e = e.charCode;
            }
            if (e == 13) {
                var new_tag = document.getElementById("rename_fld").value;
                if (new_tag != tag) {
                    document.getElementById("dest_name").value = new_tag;
                    document.getElementById("src_name").value = tag;
                    document.forms["rename_form"].submit();
                }
                else {
                    clear_rename_tag(tag);
                }
            }
            return false;
        }
        function rename_tag(tag) {
            document.getElementById("rename_"+tag).innerHTML = \'<input onkeyup="return submit_tag_rename(\\\'\'+tag+\'\\\')" onblur="return clear_rename_tag(\\\'\'+tag+\'\\\')" type="text" id="rename_fld" value="\'+tag+\'" />\';
            document.getElementById("rename_fld").select();
            return false;
        }
        function repair_tags(tag) {
            if (confirm("Attempting to correct tag data could result in some tags being lost. Continue?")) {
                document.getElementById("del_tag").value = "^repair^";
                document.forms["del_form"].submit();
            }
            return false;
        }
        function delete_all_tags(tag) {
            if (confirm("Are you sure you want to delete ALL your tags?")) {
                document.getElementById("del_tag").value = "^all^";
                document.forms["del_form"].submit();
            }
            return false;
        }
        function delete_tag(tag) {
            if (confirm("Are you sure you want to delete the \""+tag+"\" tag?")) {
                document.getElementById("del_tag").value = tag;
                document.forms["del_form"].submit();
            }
            return false;
        }
        function remove_tag(tag) {
            var url = document.location.href;
            var pattern = new RegExp(/tag\=(.+)/);
            var mymatch = pattern.exec(url);
            if (mymatch.length > 1) {
                var tags = mymatch[1].replace("%2B"+tag, "").replace("/%5E"+tag, "").replace(tag, "");
                tags = tags.replace(/^(%2B|%5E)/, "");
                if (tags) {
                    document.location.href = "?page=message_tags&tag="+tags;
                }
                else {
                    document.location.href = "?page=message_tags";
                }
            }
            return false;
        }
        function add_tag_start() {
            var tag_type = document.getElementById("add_type");
            if (tag_type) {
                if (tag_type.options[tag_type.selectedIndex].value == "or") {
                    return add_tag_or();
                }
            }
            return add_tag_and();
        }
        function add_tag_or() {
            var new_tag = get_tag();
            if (new_tag) {
                document.location.href = document.location.href+urlencode("^"+new_tag);
            }
            return false;
        }
        function add_tag_and() {
            var new_tag = get_tag();
            if (new_tag) {
                document.location.href = document.location.href+urlencode("+"+new_tag);
            }
            return false;
        }
        function reset_tag_list() {
            document.location.href = "?page=message_tags";
        }
        function get_tag() {
            var new_tag;
            var tag_list = document.getElementById("tag_list");
            if (tag_list) {
                new_tag = tag_list.options[tag_list.selectedIndex].value;
            }
            return new_tag;
        }
    ');
    $tags = hm_new('tags', $tools);
    $tools->set_title('Message Tags');
    $pd = array();
    $pd['tag'] = false;
    $pd['header_data'] = array();
    $pd['tag_list'] = $tags->tag_list;
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }
    else {
        if (isset($_POST['del_tag']) && isset($tags->tag_list[$_POST['del_tag']])) {
            $tools->send_notice($tags->delete_tag($_POST['del_tag']));
            $pd['tag_list'] = $tags->tag_list;
        }
        elseif (isset($_POST['del_tag']) && $_POST['del_tag'] == '^repair^') {
            $tags->repair();
            $pd['tag_list'] = $tags->tag_list;
            $tools->send_notice('Tag information repaired');
        }
        elseif (isset($_POST['del_tag']) && $_POST['del_tag'] == '^all^') {
            $tags->reset();
            $pd['tag_list'] = array();
            $tools->send_notice('All Tags Deleted');
        }
        elseif (isset($_POST['src_name']) && isset($_POST['dest_name'])) {
            if (isset($tags->tag_list[$_POST['src_name']])) {
                if ($tags->valid_tag($_POST['dest_name'])) {
                    $tools->send_notice($tags->rename_tag($_POST['src_name'], $_POST['dest_name']));
                    $pd['tag_list'] = $tags->tag_list;
                }
                else {
                    $tools->send_notice('Invalid tag name');
                }
            }
        }
        $tag_list = array();
        $tag = false;
        $_SESSION['tag_page_list'] = array();
        if (isset($_GET['tag'])) {
            if (isset($tags->tag_list[$_GET['tag']])) {
                $tag_list[0] = array($_GET['tag']);
                $tag = true;
            }
            elseif (strpos($_GET['tag'], '^') !== false || strpos($_GET['tag'], '+') !== false) {
                $group_id = 0;
                $bits = (preg_split("/(\^|\+)/", $_GET['tag'], -1, PREG_SPLIT_DELIM_CAPTURE));
                $bucket = false;
                foreach ($bits as $i => $v) {
                    if (!$bucket && isset($tags->tag_list[$v])) {
                        $tag_list[$group_id][] = $v;
                        $tag = true;
                    }
                    elseif ($bucket == 'and' && isset($tags->tag_list[$v])) {
                        $tag_list[$group_id][] = $v;
                        $tag = true;
                    }
                    elseif ($bucket == 'or' && isset($tags->tag_list[$v])) {
                        $group_id++;
                        $tag_list[$group_id][] = $v;
                        $tag = true;
                    }
                    if ($v == '^') {
                        $bucket = 'or';
                    }
                    elseif ($v == '+') {
                        $bucket = 'and';
                    }
                }
            }
        }
        if ($tag) {
            $_SESSION['tag_page_list'] = $tag_list;
            $pd['tag'] = true;
            $res = get_tag_msg_list($tag_list, $tools, $tags);
            if (isset($res['header_data'])) {
                $pd['header_data'] = $res['header_data'];
            }
            $pd['tag_dsp'] = $res['tag_dsp'];
            $pd['tags'] = $res['tags'];
            usort($pd['header_data'], 'sort_date');
        }
    }
    return $pd;
}
function print_message_tags($pd, $tools) {
    global $sticky_url;
    $data = '<div id="message_tags">';
    $data .= '<h2 id="mailbox_title2">'.$tools->str[0].'</h2>';
    $data .= '<div id="mbx_outer">';
    $cnt = 2;
    if ($pd['tag']) {
        $data .= '<div class="tag_title2">'.$pd['tag_dsp'].' '.print_tag_search_options($tools, $pd['tag_list'], $pd['tags']).'</div>';
        $data .= '<form method="post" id="msg_controls_form1" action="'.$sticky_url.'">';
        $data .= '<div class="message_controls">'.$tools->print_message_controls().'</div>';
        $data .= '<table id="mbx_table" cellpadding="0" cellspacing="0" width="100%">';
        $head = $tools->print_mailbox_list_headers(array('folder_cell'));
        if ($head) {
            $data .= '<tr>'.$head.'</tr>';
        }
        if (!empty($pd['header_data'])) {
            foreach ($pd['header_data'] as $vals) {
                $row = $tools->print_mailbox_list(array($vals), $vals['folder'], $cnt, array('folder_cell'));
                $data .= $row;
                $cnt++;
            }
        }
        else {
            $data .= '<tr><td colspan="9" class="empty_tag_list">No message found for the selected tags</td></tr>';
        }
        $data .= '<tr><th colspan="9">&#160;</th></tr>';
        $data .= '</table></form>';
        $data .= '<div class="list_link"><a href="?page=message_tags">Tag List</a></div>';
    }
    elseif (!empty($pd['tag_list'])) {
        $data .= '<table cellpadding="0" cellspacing="0" class="tag_table">';
        foreach ($pd['tag_list'] as $t => $folders) {
            $cnt = 0;
            foreach ($folders as $v) {
                $cnt += $v;
            }
            $data .= '<tr><td><a class="tag_label" href="?page=message_tags&amp;tag='.urlencode($tools->display_safe($t)).'">'.$tools->display_safe($t);
            $data .= ' ('.$cnt.')</a></td><td><a title="Permanently delete this tag from all messages" href="?page=message_tags" onclick="return delete_tag(\''.$tools->display_safe($t).'\')">Delete</a>';
            $data .= '</td><td><a href="?page=message_tags" onclick="return rename_tag(\''.$tools->display_safe($t).'\')">Rename</a><span id="rename_'.$tools->display_safe($t).'"></span>';
            $data .= '</td></tr>';
        }
        $data .= '</table><form method="post" id="rename_form" name="rename_form" action="?page=message_tags"><input type="hidden" name="src_name" id="src_name" /><input type="hidden" name="dest_name" id="dest_name" /></form>';
        $data .= '<a class="del_all" title="Permanently remove ALL tags" href="?page=message_tags" onclick="return delete_all_tags()">Delete All</a>';
        $data .= '<br /><a class="repair" title="Attempt to repair tag information inconsistencies" href="?page=message_tags" onclick="return repair_tags()">Repair Tag Data</a>';
        $data .= '<form name="del_form" id="del_form" method="post" action="?page=message_tags"><input type="hidden" name="del_tag" value="" id="del_tag" /></form>';
    }
    else {
        $data .= '<div class="empty_tag_list">No Tags Found</div>';
    }
    $data .= '</div></div>';
    return $data;
}
function print_tag_search_options($tools, $list, $current) {
    $data = '<div class="tag_search_opts">&#160;&#160;';
    $data .= '<select id="add_type"><option value="and">And</option><option value="or">Or</option></select>';
    $data .= '<select id="tag_list" >';
    foreach ($list as $v => $vals) {
        $data .= '<option value="'.$tools->display_safe($v).'">'.$tools->display_safe($v).'</option>';
    }
    $data .= '</select>';
    $data .= '<input title="Add another tag to the filter" onclick="return add_tag_start();" type="button" value="Add" />';
    #$data .= '&#160;&#160;&#160;&#160;<input title="Reset selected tags" onclick="return reset_tag_list();" type="button" value="Reset" />';
    $data .= '</div>';
    return $data;
}
?>
