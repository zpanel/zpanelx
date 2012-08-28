<?php

/*  url_action_class.php: Process $_GET values
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
function url_action_search($get) {
    global $user;
    global $imap;
    global $conf;
    global $sticky_url;
    if (!isset($conf['search_max'])) {
        $conf['search_max'] = 5;
    }
    if ($user->logged_in) {
        do_work_hook('search_page_start');
        if (isset($get['reset_search']) && $get['reset_search']) {
            $_SESSION['search_terms'] = array();
            $_SESSION['search_results'] = array();
            $_SESSION['search_max'] = 1;
            $_SESSION['search_total'] = 0;
        }
        if (isset($get['advanced_view']) && $get['advanced_view']) {
            $_SESSION['advanced_search'] = 1;
        }
        if (isset($get['simple_view']) && $get['simple_view']) {
            $_SESSION['advanced_search'] = 0;
        }
        $user->page_data['advanced_view'] = 0;
        if (isset($_SESSION['advanced_search']) && $_SESSION['advanced_search']) {
            $user->page_data['advanced_view'] = 1;
        }
        $user->page_data['search_flags'] = array(
            2 => $user->str[114], 3 => $user->str[115],
            4 => $user->str[116], 5 => $user->str[117],
            6 => $user->str[118], 7 => $user->str[119],
        );
        $user->page_data['search_start'] = 0;
        $user->page_data['search_end'] = 0;
        $user->page_data['mailbox_page'] = 1;
        $user->page_data['search_per_page'] = 1000;
        $user->page_data['search_flds'] = array(2 => $user->str[107], 3 => $user->str[108], 4 => $user->str[109], 5 => $user->str[110], 6 =>  $user->str[111]);
        $user->page_data['search_size_1'] = array(2 => $user->str[120], 3 => $user->str[121]);
        $user->page_data['search_date_1'] = array(1 => $user->str[431], 2 => $user->str[123], 3 => $user->str[124]);
        $user->page_data['search_size_3'] = array(1 => $user->str[125], 2 => $user->str[126],  3 => $user->str[127]);
        $user->page_data['search_date_2'] = array(1 => $user->str[131], 2 => $user->str[132], 3 => $user->str[133], 4 => $user->str[134], 5 => $user->str[135], 6 => $user->str[136],
                                                  7 => $user->str[137], 8 => $user->str[138], 9 => $user->str[139], 10 => $user->str[140], 11 => $user->str[141], 12 => $user->str[142]);
        $user->page_data['search_total'] = 0;
        $user->page_data['max_search'] = 1;
        if (isset($get['reset_results']) && $get['reset_results']) {
            $_SESSION['search_results'] = array();
            $_SESSION['search_total'] = 0;
        }
        if (isset($_SESSION['search_max'])) {
            $user->page_data['max_search'] = $_SESSION['search_max'];
        }
        if ($user->page_data['max_search'] > $conf['search_max']) {
            $this->errors[] = $user->str[386];
            $_SESSION['search_max'] = $conf['search_max'];
            $user->page_data['max_search'] = $conf['search_max'];
        }
        $user->page_data['search_results'] = array();
        $user->page_data['search_link_class'] ='current_page';
        if (isset($_SESSION['search_total'])) {
            if ($_SESSION['search_total'] > 15) {
                $user->page_data['top_link'] = '<br /><a href="'.$sticky_url.'#top">'.$user->str[186].'</a>';
            }
            $user->page_data['search_total'] = $_SESSION['search_total'];
            $search_res = array();
            $ordered_res = array();
            if (isset($_SESSION['search_results'])) {
                $per_page_count = $user->page_data['settings']['mailbox_per_page_count'];
                $page = 1;
                if (isset($get['mailbox_page'])) {
                    $page = (int) $get['mailbox_page'];
                    if (!$page) {
                        $page = 1;
                    }
                } 
                if ($page > 1) {
                    $start = ($page*$per_page_count+1) - $per_page_count;
                    $end = $page*$per_page_count;
                }
                else {
                    $start = 1;
                    $end = $per_page_count;
                }
                $user->page_data['search_start'] = $start;
                $user->page_data['search_end'] = $end;
                $user->page_data['mailbox_page'] = $page;
                $user->page_data['search_per_page'] = $per_page_count;
                foreach ($_SESSION['search_results'] as $mbx => $vals) {
                    $imap->select_mailbox($mbx, false);
                    $search_res[$mbx] = array('total' => count($vals), 'headers' => $this->sort_search_res($imap->get_mailbox_page($mbx, $vals, false)));
                    if (!empty($search_res[$mbx]['headers'])) {
                    foreach(array_reverse($search_res[$mbx]['headers']) as $atts) {
                            $ordered_res[$mbx][] = $atts['uid'];
                        }
                    }
                }
                $user->page_data['search_results'] = $search_res;
                $_SESSION['search_results'] = $ordered_res;
            }
        }
        if (isset($_SESSION['search_terms'])) {
            $user->page_data['search_terms'] = $_SESSION['search_terms'];
        }
        $user->dsp_page = 'search';
        $user->page_title .= ' | '.$user->str[9].' |';
        $user->page_data['folders'] = $_SESSION['folders'];
    }
}
function sort_search_res($vals) {
    usort($vals, array($this, 'search_sort_by_arrival'));
    return $vals; 
}
function search_sort_by_arrival($a, $b) {
    if (strtotime($a['internal_date']) > strtotime($b['internal_date'])) {
        return -1;
    }
    else {
        return 1;
    }
}
}

class site_page_search extends site_page {
function print_search_res($cols=array(), $onclick=false, $headers=true) {

    $n = 1;
    $data = '<div id="search_page_inner">';
    $empty = true;
    foreach ($this->pd['search_results'] as $vals) {
        if (isset($vals['headers']) && !empty($vals['headers'])) {
            $empty = false;
            break;
        }
    }
    if (!$empty) {
        $data .= '<table class="search_res_table" cellpadding="0" cellspacing="0" width="100%">';
        if ($headers) {
            $data .= '<tr>'.$this->print_mailbox_list_headers().'</tr>';
        }
        $cnt = 0;
        $start = $this->pd['search_start'];
        $end = $this->pd['search_end'];
        foreach ($this->pd['search_results'] as $i => $array) {
            $list = array();
            foreach ($array['headers'] as $vals) {
                $cnt++;
                if ($start && $end && ($cnt < $start || $cnt > $end)) {
                    continue;
                }
                $list[] = $vals;
            }
            if (empty($list)) {
                continue;
            }
            $array['headers'] = $list;
            if (!isset($array['total'])) {
                $array['total'] = 0;
            }
            $data .= '<tr><td colspan="'.count($this->msg_list_flds).'"><h4>'.$array['total'].'&#160;&#160;<a title="'.$this->user->htmlsafe($i, 0, 0, 1).'" href="?page=mailbox&amp;mailbox='.urlencode($i).'">'.
                $this->user->htmlsafe($this->pd['folders'][$i]['basename'], 0, 0, 1).'</a></h4></td></tr>';
            if (!empty($array['headers'])) {
                $data .= $this->print_mailbox_list_rows($this->msg_list_flds, $array['headers'], $this->onclick, $i, $n);
                $n += count($array['headers']);
            }
            else {
                $data .= '<tr><td colspan="'.count($this->msg_list_flds).'"><div class="empty_unread">No matching search results in this folder</div></td></tr>';
            }
        }
        $_SESSION['toggle_all'] = false;
        $_SESSION['toggle_uids'] = array();
        $_SESSION['toggle_boxes'] = array();
        $data .= '</table><input type="hidden" id="page_count" value="'.($n - 1).'" />';
        $data .= build_page_links($this->pd['mailbox_page'], $this->pd['search_total'], $this->pd['search_per_page'], '?page=search');
    }
    else {
        $data .= '<div class="empty_search_res">'.$this->user->str[90].'</div>';
    }
    $data .= '</div>';
    return $data;
}
function print_search_form() {
    global $conf;
    if (isset($this->pd['search_terms']['folders'])) {
        $selected_folders = $_SESSION['search_terms']['folders'];
    }
    else {
        $selected_folders = array();
        if (isset($this->pd['mailbox'])) {
            $selected_folders[] = $this->pd['mailbox'];
        }
    }
    $max = $this->pd['max_search'];
    $data = '<h4>'.$this->user->str[92].'</h4><table cellpadding="0" style="width: 300px;" cellspacing="0"><tr><td><select size="15" multiple="multiple" '.
            'style="width: 100%;" name="search_folders[]">'.$this->print_folder_option_list($this->pd['folders'], false, 0, $selected_folders).'</select></td></tr>'.
            '</table><h4>'.$this->user->str[93].'</h4>';
    if (!$this->pd['advanced_view']) {
        $data .= '<a href="?page=search&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;advanced_view=1#search_form">'.$this->user->str[94].'</a>';
    }
    else {
        $data .= '<a href="?page=search&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;simple_view=1#search_form">'.$this->user->str[95].'</a>';
    }
    $data .= '&#160;&#160;<a href="?page=search&amp;mailbox='.urlencode($this->pd['mailbox']).'&amp;reset_search=1" onclick="form.reset();" >'.$this->user->str[97].' </a>'.
             '<br /><br /><table cellpadding="0" cellspacing="0">';
    for ($i=0;$i<$max;$i++) {
        if ($i == 0) {
            $data .= '<tr><th class="top_row">'.($i + 1).'. '.$this->user->str[96].'</th><td>';
        }
        else {
            $data .= '<tr><th colspan="2" class="and_or">'.$this->user->str[99].' <input type="radio" ';
            if (isset($this->pd['search_terms'][$i]['and_or']) && $this->pd['search_terms'][$i]['and_or'] == 1) {
                $data .= 'checked="checked" ';
            }
            $data .= 'name="and_or_'.$i.'" value="1" />'.$this->user->str[100].' <input ';
            if (isset($this->pd['search_terms'][$i]['and_or']) && $this->pd['search_terms'][$i]['and_or'] == 2) {
                $data .= 'checked="checked" ';
            }
            $data .= 'type="radio" name="and_or_'.$i.'" value="2" /></th></tr><tr><td></td></tr><tr><th class="top_row">'.($i + 1).'. '.$this->user->str[96].'</th><td>';
        }
        $data .= '<input type="text" name="keywords_1_'.$i.'" value="';
        if (isset($this->pd['search_terms'][$i]['words'])) {
            $data .= $this->user->htmlsafe($this->pd['search_terms'][$i]['words']);
        }
        $data .= '" /></td><th>'.$this->user->str[102].'</th><td><select name="search_fld_1_'.$i.'"><option value="1">'.$this->user->str[106].'</option>';
        foreach ($this->pd['search_flds'] as $n => $v) {
            $data .= '<option ';
            if (isset($this->pd['search_terms'][$i]['fld']) &&
                $this->pd['search_terms'][$i]['fld'] == $n) {
                $data .= 'selected="selected" ';
            }
            $data .= 'value="'.$n.'">'.$v.'</option>';
        }
        $data .= '</select></td></tr>';
        $data .= '</table>';
        if ($this->pd['advanced_view']) {
            $data .= '<table cellpadding="0" style="width: 300px;" cellspacing="0">';
            $data .= '<tr><th>'.$this->user->str[103].'</th><td><select name="search_flags_1_'.$i.'"><option value="1">'.$this->user->str[113].'</option>';
            foreach ($this->pd['search_flags'] as $n => $v) {
                $data .= '<option ';
                if (isset($this->pd['search_terms'][$i]['location']) &&
                    $this->pd['search_terms'][$i]['location'] == $n) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$n.'">'.$v.'</option>';
            }
            $data .= '</select></td></tr><tr><th>'.$this->user->str[104].'</th><td>
                <select name="search_size_1_'.$i.'"><option value="0">'.$this->user->str[122].'</option>';
            foreach ($this->pd['search_size_1'] as $n => $v) {
                $data .= '<option ';
                if (isset($this->pd['search_terms'][$i]['size1']) && $this->pd['search_terms'][$i]['size1'] == $n) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$n.'">'.$v.'</option>';
            }
            $data .= '</select><input type="text" value="';
            if (isset($this->pd['search_terms'][$i]['size2']) && $this->pd['search_terms'][$i]['size2']) {
                $data .= $this->user->htmlclean($this->pd['search_terms'][$i]['size2']);
            }
            $data .= '" name="search_size_2_'.$i.'" size="5" /><select name="search_size_3_'.$i.'">';
                foreach ($this->pd['search_size_3'] as $n => $v) {
                $data .= '<option ';
                if (isset($this->pd['search_terms'][$i]['size3']) && $this->pd['search_terms'][$i]['size3'] == $n) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$n.'">'.$v.'</option>';
                }
            $data .= '</select></td></tr><tr><th>'.$this->user->str[105].'</th><td><select name="search_date_1_'.$i.'"><option value="0">'.$this->user->str[122].'</option>';
            foreach ($this->pd['search_date_1'] as $n => $v) {
                $data .= '<option ';
                    if (isset($this->pd['search_terms'][$i]['date1']) && $this->pd['search_terms'][$i]['date1'] == $n) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$n.'">'.$v.'</option>';
            }
            $data .= '</select><select name="search_date_2_'.$i.'"><option value="0">'.$this->user->str[128].'</option>';
            foreach ($this->pd['search_date_2'] as $n => $v) {
                $data .= '<option ';
                if (isset($this->pd['search_terms'][$i]['date2']) && $this->pd['search_terms'][$i]['date2'] == $n) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$n.'">'.$v.'</option>';
            }
            $data .= '</select><select name="search_date_3_'.$i.'"><option value="0">'.$this->user->str[129].'</option>';
            for ($n=1;$n<32;$n++) {
                $data .= '<option ';
                if (isset($this->pd['search_terms'][$i]['date3']) && $this->pd['search_terms'][$i]['date3'] == $n) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$n.'">'.$n.'</option>';
            }
            $data .= '</select><select name="search_date_4_'.$i.'"><option value="0">'.$this->user->str[130].'</option>';
            $ymax = date("Y");
            $ymin = $ymax - 25;
            for ($n=$ymax;$n>$ymin;$n--) {
                $data .= '<option ';
                if (isset($this->pd['search_terms'][$i]['date4']) && $this->pd['search_terms'][$i]['date4'] == $n) {
                    $data .= 'selected="selected" ';
                }
                $data .= 'value="'.$n.'">'.$n.'</option>';
            }
            $data .= '</select></td></tr></table>';
        }
        $data .= '<table cellpadding="0" style="width: 300px;" cellspacing="0">';
    }
    $data .= '<tr><td colspan="2">';
    if ($i < $conf['search_max']) {
        $data .= '<input class="search_button alt_button" type="submit" name="full_search" id="full_search" value="'.$this->user->str[9].'" />'.
                 '&#160; <input class="search_button" type="submit" name="more_search" value="'.$this->user->str[98].'" />
                  &#160; <input type="hidden" name="search_max" value="'.$max.'" />';
    }
        if ($i > 1) {
            $data .= '<input type="submit" class="search_button" name="less_search" value="'.$this->user->str[101].'" /><input type="hidden" name="search_max" value="'.$max.'" />&#160;&#160;';
        }
    $data .= '</td></tr></table>';
    return $data;
}
}
?>
