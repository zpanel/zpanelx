<?php

/*  work.php
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
function quota_init($tools) {
    $opts = array($tools->str[1]);
    $tools->save_to_global_store('help_strings', $opts);
    $quota_dsp = $tools->get_setting('quota_display');
    $tools->add_to_store('quota_display', $quota_dsp);
    if ((($quota_dsp == 1 || $quota_dsp == 4) && $tools->get_page() == 'mailbox') || $quota_dsp == 3 || $quota_dsp == 2) {
        $tools->add_to_store('quota_vals', get_imap_quota_root($tools));
    }
}
function get_imap_quota($tools, $root) {
    list($res, $status) = $tools->imap_custom_command("GETQUOTA \"$root\"\r\n", true);
    $res_list = array();
    $roots = array();
    if ($status) {
        array_pop($res);
        foreach ($res as $line) {
            list($res, $roots) = parse_quota_line($line, $roots, $tools);
            if (count($res) > 0) {
                $res_list[] = $res;
            }
        }
    }
    return $res_list;
}
function get_imap_quota_root($tools) {
    list($res, $status) = $tools->imap_custom_command("GETQUOTAROOT \"INBOX\"\r\n", true);
    $res_list = array();
    $roots = array();
    if ($status) {
        array_pop($res);
        foreach ($res as $line) {
            list($res, $roots) = parse_quota_line($line, $roots, $tools);
            if (count($res) > 0) {
                $res_list[] = $res;
            }
        }
    }
    return $res_list;
}
function parse_quota_line($line, $roots, $tools) {
    $root_atts = array();
    if ($line[0] == '*' && strtolower($line[1]) == 'quota') {
        if (isset($line[2]) && !in_array($line[2], $roots)) {
            $roots[] = $line[2];
            $root_atts = array('root' => $line[2]);
            $i = 3;
            $started = false;
            while (isset($line[$i]) && $line[$i] != ')') {
                if ($line[$i] == '(' || $started) {
                    if (isset($line[$i + 3])) {
                        if (strtolower($line[$i + 1]) == 'storage') {
                            $root_atts[$line[$i + 1]] = array('used' => $line[$i + 2], 'max' => $line[$i + 3]);
                        }
                        else {
                            $root_atts[$line[$i + 1]] = array('used' => $line[$i + 2], 'max' => $line[$i + 3]);
                        }
                        $started = true;
                    }
                    $i += 2;
                }
                $i++;
            }
        }
    }
    return array($root_atts, $roots);
}
function quota_update_settings($tools) {
    $quota = 0;
    if (isset($_POST['quota_display']) && $_POST['quota_display']) {
        if (in_array($_POST['quota_display'], array(1,2,3,4))) {
            $quota = $_POST['quota_display'];
        }
    }
    $tools->save_options_page_setting('quota_display', $quota);
}
?>
