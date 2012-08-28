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
function quota_folder_list_top($tools) {
    if ($tools->get_from_store('quota_display') != 2) {
        return '';
    }
    return output_quotas($tools, 'folders');
}
function quota_folder_list_bottom($tools) {
    if ($tools->get_from_store('quota_display') != 3) {
        return '';
    }
    return output_quotas($tools, 'folders');
}
function quota_mailbox_meta($tools) {
    if ($tools->get_from_store('quota_display') != 1) {
        return '';
    }
    return output_quotas($tools, 'mailbox');
}
function quota_mailbox_search($tools) {
    if ($tools->get_from_store('quota_display') != 4) {
        return '';
    }
    return output_quotas($tools, 'mailbox');
}
function output_quotas($tools, $format) {
    $vals = $tools->get_from_store('quota_vals');
    if ($format == 'mailbox') {
        $data = '<div style="display: inline; font-weight: normal; font-size: 95%; padding-left: 15px;"><b>Quota</b> &nbsp;';
    }
    else {
        $data = '<div style="padding-bottom: 10px; font-weight: normal; font-size: 95%; padding-left: 20px;"><b>Quota</b> &nbsp;';
    }
    if (is_array($vals) && !empty($vals)) {
        foreach ($vals as $root) {
            foreach ($root as $name => $value) {
                if ($name == 'root' && $value) {
                    $data .= $value.': ';
                }
                elseif (is_array($value)) {
                    $data .= '<span style="padding-right: 5px; font-size: 85%;">'.ucfirst(strtolower($name)).': '.round(($value['used']/$value['max'])*100, 3).'%</span>';
                }
                if ($format == 'folders') {
                    $data .= '<br />';
                }
            }
        }
        $data .= '</div>';
        return $data;
    }
    return '';
}
function quota_general_options_table($tools) {
    $quota_display = $tools->get_setting('quota_display');
    $opts = array(0 => $tools->str[5], 1 => $tools->str[2], 4 => $tools->str[6], 2 => $tools->str[3], 3 => $tools->str[4]); 
    $data = '<tr><td class="opt_leftcol">'.$tools->str[1].'</td><td><select name="quota_display">';
    foreach ($opts as $i => $v) {
        $data .= '<option value="'.$i.'" ';
        if ($quota_display == $i) {
            $data .= 'selected="selected" ';
        }
        $data .= '>'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    return $data;
}
?>
