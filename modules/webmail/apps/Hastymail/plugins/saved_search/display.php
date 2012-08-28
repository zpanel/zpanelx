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

function saved_search_search_page_bottom($tools) {
    $saved_searches = $tools->get_setting('saved_searches');
    $prev_searches = $tools->get_setting('prev_searches');
    $str = $tools->get_hm_strings();
    $data = '<br /><table class="prev_searches" cellpadding="0" cellspacing="0" style="min-width: 50%;">';
    $data .= '<tr><th style="width: 25%;"><b>'.$tools->str[1].'</b></th><th>'.$tools->str[5].'</th><th>'.$tools->str[6].'</th><th>'.$tools->str[7].'</th></tr>';
    if ($prev_searches) {
        $i = 1;
        foreach (array_reverse($prev_searches) as $index => $vals) {
            $data .= '<tr>';
            $data .= '<td nowrap="nowrap">'.$i++.' &#160;<a href="?page=search&amp;save_search='.urlencode($index).'">'.$tools->str[8].'</a>';
            $data .= '<a href="?page=search&amp;run_search='.urlencode($index).'">'.$tools->str[9].'</a>';
            $data .= '<a href="?page=search&amp;edit_search='.urlencode($index).'">'.$tools->str[11].'</a>';
            $data .= '<a href="?page=search&amp;forget_search='.urlencode($index).'">'.$tools->str[10].'</a></td>';
            $data .= '<td>'.$tools->display_safe($vals[0]['words']).'</td><td>';
            $data .= search_fld($str, $vals[0]['fld']);
            $data .= '</td><td>'.$tools->display_safe(implode(', ', $vals['folders'])).'</td>';
            $data .= '</tr>';
            if (count($vals) > 2) {
                foreach ($vals as $i => $v) {
                    if ($i == 0 || $i == 'folders') {
                        continue;
                    }
                    $data .= '<tr><td></td><td>';
                    if ($v['and_or'] == 1) {
                        $data .= $tools->str[13];
                    }
                    else {
                        $data .= $tools->str[14];
                    }
                    $data .= ' &#160;'.$tools->display_safe($v['words']).'</td><td></td><td></td></tr>';
                }
            }
        }
    }
    else {
        $data .= '<tr><td colspan="4" class="empty_list">'.$tools->str[3].'</td></tr>';
    }
    $data .= '<tr><td colspan="4"><br /></td></tr><tr><th><b>'.$tools->str[2].'</b></th><th>'.$tools->str[5].'</th><th>'.$tools->str[6].'</th><th>'.$tools->str[7].'</th></tr>';
    if ($saved_searches) {
        $i = 1;
        foreach (array_reverse($saved_searches) as $index => $vals) {
            $data .= '<tr><td nowrap="nowrap">'.$i++.' &#160;<a href="?page=search&amp;run_search='.urlencode($index).'">'.$tools->str[9].'</a>';
            $data .= '<a href="?page=search&amp;edit_search='.urlencode($index).'">'.$tools->str[11].'</a>';
            $data .= '<a href="?page=search&amp;delete_search='.urlencode($index).'">'.$tools->str[12].'</a></td>';
            $data .= '<td>'.$tools->display_safe($vals[0]['words']).'</td><td>';
            $data .= search_fld($str, $vals[0]['fld']).'</td><td>'.$tools->display_safe(implode(', ', $vals['folders'])).'</td></tr>';
            if (count($vals) > 2) {
                foreach ($vals as $i => $v) {
                    if ($i == 0 || $i == 'folders') {
                        continue;
                    }
                    $data .= '<tr><td></td><td>';
                    if ($v['and_or'] == 1) {
                        $data .= 'AND';
                    }
                    else {
                        $data .= 'OR';
                    }
                    $data .= ' &#160;'.$tools->display_safe($v['words']).'</td><td></td><td></td></tr>';
                }
            }
        }
    }
    else {
        $data .= '<tr><td colspan="4" class="empty_list">'.$tools->str[4].'</td></tr>';
    }
    $data .= '</table>';
    return $data;
}
function search_fld($str, $val) {
    $opts = array(2 => $str[107], 3 => $str[108], 4 => $str[109], 5 => $str[110], 6 =>  $str[111]);
    if (isset($opts[$val])) {
        return $opts[$val];
    }
    if ($val == 1) {
        return $str[106];
    }
    return '?';
}
?>
