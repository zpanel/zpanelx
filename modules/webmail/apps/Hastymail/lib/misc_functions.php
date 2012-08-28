<?php

/*  misc_functions.php: Various helper functions
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

function get_config($file) {
    $conf = @unserialize(file_get_contents($file));
    if (is_array($conf) && !empty($conf)) {
        if (isset($conf['http_prefix'])) {
            $pre = $conf['http_prefix'];
        }
        elseif (isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) == 'ON') {
            $pre = 'https';
        }
        else {
            $pre = 'http';
        }
        $conf['http_prefix'] = $pre;
        if (!isset($conf['host_name']) || !trim($conf['host_name'])) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $conf['host_name'] = $_SERVER['HTTP_HOST'];
            }
            elseif (isset($_SERVER['SERVER_NAME'])) {
                $conf['host_name'] = $_SERVER['SERVER_NAME'];
            }
            elseif (isset($_SERVER['SERVER_ADDR'])) {
                $conf['host_name'] = $_SERVER['SERVER_ADDR'];
            }
            else {
                echo 'Could not determine the webserver hostname!';
                die;
            }
        }
        return $conf;
    }
    else {
        echo 'Configuration file not found or unreadable';
        exit;
        
    }
}
function parse_address_fld($str, $charset=false) {
    global $page_id;
    global $user;
    $res = array();
    $replacements = array();
    if (strstr($str, ',') || strstr($str, ';')) {
        if (preg_match_all("/[\"']{1}[^\"']+[\"']{1}/", $str, $matches)) {
            foreach ($matches as $i => $v) {
                $str = str_replace($v, $page_id.'_'.$i, $str);
                $replacements[$page_id.'_'.$i] = $v[0];
            }
        }
        $bits = preg_split("/(;|,)/", $str);
        
    }
    else {
        $bits[] = $str;
    }
    foreach ($bits as $val) {
        if (trim($val)) {
            foreach ($replacements as $hash => $orig) {
                $val = str_replace($hash, $orig, $val);
            }
            $address = '';
            $labels = '';
            if (strstr($val, ' ')) {
                foreach (explode(' ', $val) as $v) {
                    $v = trim($v);
                    if (strstr($v, '@')) {
                        $address = trim($v);
                    }
                    else {
                        $labels .= ' '.trim($v);
                    }
                }
                $labels = trim($labels);
                if ($val != $user->decode_fld($val, $charset) && !preg_match("/^'[^']*'$/", $labels) && !preg_match('/^"[^"]*"$/', $labels)) {
                    $val = str_replace($labels, '"'.str_replace('"', '\"', $labels).'"', $val);
                }
            }
            else {
                if (strstr($val, '@')) {
                    $address = $val;
                }
                else {
                    $labels = $val;
                }
            }
            $res[] = array('raw' => $val, 'address' => trim(str_replace(array('<', '>'), '', $address)), 'label' => trim($labels));
        }
    }
    return $res;
}
function get_site_config() {
    global $user;
    global $conf;
    global $imap;
    foreach ($conf as $i => $v) {
        if (substr($i, 0, 5) == 'imap_') {
            $name = substr($i, 5);
            $imap->$name = $v;
        }
        elseif (substr($i, 0, 5) == 'site_') {
            $name = substr($i, 5);
            $user->$name = $v;
        }
    }  
    return true;
}
function remove_images_callback($val) {
    if ($val == 'img') {
        return false;
    }
    else {
        return true;
    }
}
function html_2_text ($html, $nl=false) {
    global $include_path;
    global $conf;
    global $fd;
    require_once($include_path.'lib'.$fd.'class.html2text.inc');
    $h2t = hm_new('html2text');
    $h2t->set_html($html);
    $text = $h2t->get_text();
    if ($nl) {
        $text = nl2br($text);
    }
    return $text;
}
function strip_other($str) {
    $str = preg_replace('/[\r\n]/m', ' ', $str);
    $str = preg_replace('/<\!doctype[^>]+>/im', '', $str);
    $str = preg_replace('/<\?xml[^>]+>/im', '', $str);
    $str = preg_replace('/<\!\[if[^\]]+]>/im', '', $str);
    $str = preg_replace('/<\!\[endif[^\]]*]>/im', '', $str);
    return $str;
}
function strip_head_section($str) {
    $res = preg_replace('/^.+<\/head>/Uim', '', $str);
    if ($res) {
        return $res;
    }
    else {
        return $str;
    }
}
function clean_up_html($str) {
    $str = strip_other($str);
    $str = strip_style_tags($str);
    $str = strip_head_section($str);
    return $str;
}
function strip_style_tags($str) {
    global $user;
    $regex = '/<style[A-Z0-9\s\=\'\"\r\n\-\/\\\>\*\.\,\{\}\:\;\#\%\_\[\]\@\(\)\s\!\&]+<[A-Z\s\/]+>/im';
    if (preg_match_all($regex, $str, $matches)) {
        foreach($matches[0] as $val) {
            $user->page_data['html_message_style'][] = $val;
            $str = str_replace($val, '', $str);
        }
    }
    return $str;
}
function filter_html ($body, $allowed) {
    global $filter_backend;
    $body = clean_up_html($body);
    switch ($filter_backend) {
        case 'htmlawed':
            return htmlawed_filter($body, $allowed);
            break;
        case 'htmlpure':
            return htmlpure_filter($body, $allowed);
        case 'none':
            return $body;
        case 'legacy':
        default: 
            return legacy_filter($body, $allowed);
            break;
    }
}
function htmlpure_filter($body, $allowed) {
    global $conf;
    global $include_path;
    global $fd;
    global $pure_serializer_path;
    require_once($include_path.'lib'.$fd.'HTMLPurifier.standalone.php');
    $config = HTMLPurifier_Config::createDefault();
    if ($pure_serializer_path && @is_writable($pure_serializer_path)) {
        $config->set('Cache.SerializerPath', $pure_serializer_path);
    }
    else {
        $config->set('Cache.DefinitionImpl', null);
    }
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($body);

}
function htmlawed_filter($body, $allowed) {
    global $conf;
    global $include_path;
    global $fd;
    require_once($include_path.'lib'.$fd.'htmLawed.php');
    $config = array('balance' => 0, 'comment' => 1, 'cdata' => 1, 'elements'=> implode(',', $allowed));
    $data = htmLawed($body, $config);
    return $data;
}
function legacy_filter($body, $allowed) {
    global $conf;
    global $include_path;
    global $fd;
    $tag_list = $allowed;
    array_unshift($tag_list, true);
    if (isset($conf['html_message_iframe']) && $conf['html_message_iframe']) {
        $rm_tags_with_content = Array( 'script', 'style', 'applet', 'embed', 'frameset');
        $tag_list[] = 'body';
    }
    else {
        $rm_tags_with_content = Array( 'script', 'style', 'applet', 'embed', 'head', 'frameset');
    }
    $self_closing_tags =  Array( 'img', 'br', 'hr', 'input');
    $force_tag_closing = true;
    $rm_attnames = Array( '/.*/' => Array('/^on.*/i', '/^dynsrc/i', '/^datasrc/i', '/^data.*/i'));
    $add_attr_to_tag = Array();
    $bad_attvals = Array(
        '/.*/' => Array(
	        '/.*/' => Array(
                Array( '/^([\'\"])\s*\S+\s*script\s*:*(.*)([\'\"])/i'),
		        Array( '\\1blah:\\2\\3')
            ),
            '/^style/i' => Array(
                Array( '/expression/i', '/behaviou*r/i', '/binding/i', '/url\(([\'\"]*)\s*https*:.*([\'\"]*)\)/i', '/url\(([\'\"]*)\s*\S+script:.*([\'\"]*)\)/i'),
                Array( 'idiocy', 'idiocy', 'idiocy', 'url(\\1http://securityfocus.com/\\2)', 'url(\\1http://securityfocus.com/\\2)')
            )
        )
    );
    require_once($include_path.'lib'.$fd.'htmlfilter.inc');
    $trusted = sanitize($body, $tag_list, $rm_tags_with_content, $self_closing_tags, $force_tag_closing, $rm_attnames, $bad_attvals, $add_attr_to_tag);
    return $trusted;
}
/* output the page with only $pd available */
function build_page($pd) {
    global $user;
    global $conf;
    global $include_path;
    global $fd;
    $theme = 'default';
    if ($user->logged_in) {
        if (isset($pd->pd['settings']['display_mode']) && $pd->pd['settings']['display_mode'] == 1) {
            if (isset($pd->pd['settings']['theme'])) {
                $user_theme = $pd->pd['settings']['theme'];
                if (isset($conf['site_themes'][$user_theme])) {
                    if ($conf['site_themes'][$user_theme]['templates']) {
                        $theme = $user_theme;
                    }    
                }
            }
        }
    }
    else {
        if (isset($conf['site_theme']) && $conf['site_themes'][$conf['site_theme']]['templates']) {
            $theme = $conf['site_theme'];
        }
    }
    if (!is_readable('themes'.$fd.$theme.$fd.'templates'.$fd.'main.php')) {
        $theme = 'default';
    }
    $file = 'themes'.$fd.$theme.$fd.'templates'.$fd.'main.php';
    require_check($file);
    require_once($include_path.$file);
}
/* return the strlen of a string that might have html entities in it */
function htmlstrlen($string) {
    global $hm_utils_mod;
    if ($hm_utils_mod) {
        return hm_html_strlen($string);
    }
    return strlen(preg_replace('/&([#a-z0-9]{3,}|lt|gt|mu|nu|xi|ni|or|le|ge);/i', chr(0), $string));
}
/* trim a string that could have html entities in it */
function trim_htmlstr($string, $len) {
    $count = 0;
    $entity_len = 0;
    $res = $string;
    $string = str_replace(array("\r", "\n"), '', $string);
    $str_len  = strlen($string);
    if ($len > 0) {
        for ($i = 0; $i < $str_len; $i++) {
            if ($entity_len > 0) {
                if ($string{$i} == ';') {
                    $count -= ($entity_len + 1);
                    $entity_len = 0;
                }
                else {
                    $entity_len++;
                }
            }
            if ($string{$i} == '&') {
                $entity_len = 1;
            }
            if ($entity_len == 0 && $count == $len) {
                $res = substr($string, 0, $i).'..';
                break;
            }
            $count++;
        }
    }
    return $res;
}
/* print timestamp as a difference from now in human readable form */
function print_time2($date_string, $date_format, $date_format_2) {
    global $user;
    $data = '';
    $date_string = trim($date_string);
    if (preg_match("/UT$/", $date_string)) {
       $date_string .= 'C';
    }
    if (!$date_format) {
        $data .= '<span title="'.$user->htmlsafe(date('r', strtotime(($date_string)))).'">';
        $data .= print_time(strtotime($user->htmlsafe($date_string)), $date_string).'</span>';
    }
    else {
        $data .= '<span title="'.print_time(strtotime($user->htmlsafe($date_string)), $date_string).'">';
        $data .= $user->htmlsafe(date($date_format, strtotime($date_string)));
        if ($date_format_2) {
            $data .= ' '.$user->htmlsafe(date($date_format_2, strtotime($date_string)));
        }
        $data .= '</span>';
    }
    return $data;
}
function build_time_diff($timestamp, $date_str=false) {
    if (!$timestamp) {
        $timestamp = strtotime($date_str);
        if (!$timestamp) {
            return 'unknown';
        }
    }
    return time() - $timestamp;
}
function build_time_array($diff) {
    $times = array(array('length' => (3600*24*365), 'label' => 'year'),
                   array('length' => (3600*24), 'label' => 'day'),
                   array('length' => 3600, 'label' => 'hour'),
                   array('length' => 60, 'label' => 'minute'),
                   array('length' => 1, 'label' => 'second'),
    );
    $trigger = false;
    $break = false;
    $res = array();
    if ($diff == 'unknown') {
        return $diff;
    }
    while ($diff > 0) {
        foreach ($times as $index => $vals) {
            if ($diff >= $vals['length']) {
                if ($vals['length'] == 1) {
                    break 2;
                    $diff = 0;
                }
                $trigger = true;
                $unit = floor($diff/$vals['length']);
                if ($unit != 1) {
                    $res[$vals['label'].'s'] = $unit;
                }
                else {
                    $res[$vals['label']] = $unit;
                }
                $diff = $diff % $vals['length'];
                break;
            }
        }
        if ($break) {
            break;
        }
        if ($trigger) {
            $break = true;
        }
    }
    return $res;
}
function print_time_since_arrival($vals) {
    global $user;
    if (is_array($vals)) {
        $index = join('-', array_keys($vals));
        $vals = array_values($vals);
    }
    else {
        $index = 'unknown';
    }
    switch ($index) {
        case 'minute':
            $str = $user->str[464];
            break;
        case 'minutes':
            $str = $user->str[465];
            break;
        case 'hour':
            $str = $user->str[466];
            break;
        case 'hour-minute':
            $str = $user->str[467];
            break;
        case 'hour-minutes':
            $str = $user->str[468];
            break;
        case 'hours':
            $str = $user->str[469];
            break;
        case 'hours-minute':
            $str = $user->str[470];
            break;
        case 'hours-minutes':
            $str = $user->str[471];
            break;
        case 'day':
            $str = $user->str[472];
            break;
        case 'day-minute':
            $str = $user->str[473];
            break;
        case 'day-minutes':
            $str = $user->str[474];
            break;
        case 'day-hour':
            $str = $user->str[475];
            break;
        case 'day-hours':
            $str = $user->str[476];
            break;
        case 'days':
            $str = $user->str[477];
            break;
        case 'days-minute':
            $str = $user->str[478];
            break;
        case 'days-minutes':
            $str = $user->str[479];
            break;
        case 'days-hour':
            $str = $user->str[480];
            break;
        case 'days-hours':
            $str = $user->str[481];
            break;
        case 'year':
            $str = $user->str[482];
            break;
        case 'year-minute':
            $str = $user->str[483];
            break;
        case 'year-minutes':
            $str = $user->str[484];
            break;
        case 'year-hour':
            $str = $user->str[485];
            break;
        case 'year-hours':
            $str = $user->str[486];
            break;
        case 'year-day':
            $str = $user->str[487];
            break;
        case 'year-days':
            $str = $user->str[488];
            break;
        case 'years':
            $str = $user->str[489];
            break;
        case 'years-minute':
            $str = $user->str[490];
            break;
        case 'years-minutes':
            $str = $user->str[491];
            break;
        case 'years-hour':
            $str = $user->str[492];
            break;
        case 'years-hours':
            $str = $user->str[493];
            break;
        case 'years-day':
            $str = $user->str[494];
            break;
        case 'years-days':
            $str = $user->str[495];
            break;
        case 'unknown':
            $str = $user->str[500];
            break;
        default:
            $str = $user->str[496];
            break;
    }
    if (strstr($str, '%s') && is_array($vals)) {
        if (count($vals) == 1) {
            return sprintf($str, $vals[0]);
        }
        else {
            return sprintf($str, $vals[0], $vals[1]);
        }
    }
    else {
        return $str;
    }
}
function print_time($timestamp, $date_str=false) {
    return print_time_since_arrival(build_time_array(build_time_diff($timestamp, $date_str)));
}
/* build page links HTML */
function build_page_links ($current, $total, $page_count, $url_base, $label='') {
    global $conf;
    global $user;
    global $page_id;
    $theme = 'default';
    if (isset($_SESSION['user_settings']['theme'])) {
        $user_theme = $_SESSION['user_settings']['theme'];
        if (isset($conf['site_themes'][$user_theme])) {
            if ($conf['site_themes'][$user_theme]['css']) {
                $theme = $user_theme;
            }    
        }
    }
    $img_path = 'themes/'.$theme.'/images';
    if ($total <= $page_count) {
        return '&#160;';
    }
    $range = 30;
    $middle = $range/2;
    $pages = floor($total/$page_count);
    if ($total % $page_count != 0) {
        $pages++;
    }
    $output = '';
    $start = $current - $middle;
    if ($start < 0) {
        $start = 1;
        $stop = $range;
    }
    elseif ($start == 0) {
        $start = 1;
        $stop = $range;
    }
    elseif ($start > 0) {
        if ($start = ($current - $middle)) {
            $stop = $current + $middle;
        }
        else {
            $stop = $range - $start;
        }
    }
    if ($stop > $pages) {
        $stop = $pages;
    }
    for ($i=$start; $i<=$stop; $i++) {
        if ($i == 1 && $current == 0) {
            $output .= $i;
        }
        elseif ($i != $current) {
            $output .= '<a href="'.$url_base.'&amp;mailbox_page='.$i.'">'.$i.'</a> ';
        }
        else {
            $output .= '<a class="current_page_link" href="'.$url_base.'&amp;mailbox_page='.$i.'">'.$i.'</a> ';
        }
    }
    $pre = '';
    $post = '';
    if ($current > 1) {
        $pre .= '<a class="prev_next_link" href="'.$url_base.'&amp;mailbox_page='.($current - 1).'">'.
                 '<complex-'.$page_id.'><span class="prev_button">&#160;</span></complex-'.$page_id.'>'.
                 '<simple-'.$page_id.'>&lt;</simple-'.$page_id.'></a>';
    }
    else {
        $pre .= '<complex-'.$page_id.'><span class="disabled_button prev_button"></span></complex-'.$page_id.'>';
    }
    if ($start != 1 && $current != 1) {
        $output = '<a href="'.$url_base.'&amp;mailbox_page=1">1</a>&#160;...&#160;'.$output;
    }
    if ($stop != $pages && $current != $pages) {
        $output .= '...&#160;<a href="'.$url_base.'&amp;mailbox_page='.$pages.'">'.$pages.'</a>';
    }
    if ($current < $stop) {
        $post .= '<a class="prev_next_link" href="'.$url_base.'&amp;mailbox_page='.($current + 1).'">'.
                 '<complex-'.$page_id.'><span class="next_button">&#160;</span></complex-'.$page_id.'>'.
                 '<simple-'.$page_id.'>&gt;</simple-'.$page_id.'></a>';
    }
    else {
        $post .= '<complex-'.$page_id.'><span class="next_button disabled_button"></span></complex-'.$page_id.'>';
    }
    return '<table align="center"><tr><td>'.$label.'</td><td>'.$pre.'</td><td>'.$output.'</td><td>'.$post.'</td></tr></table>';
}
function format_size($val, $extra=false) {
    if ($val == 0) {
        $result = '0 KB';
    }
    elseif ($val < 1) {
        $result = round(($val*1000), 2).' Bytes';
    }
    elseif ($val > 1000) {
        $result = round(($val/1000), 2).' MB';
    }
    else {
        $result = round($val, 2).' KB';
    }
    if ($extra && $val != 0) {
        $result .= $extra;
    }
    return $result;
}
function css_streamer($page, $theme) {
    global $app_pages;
    global $fd;
    global $css_max_age;
    global $include_path;
    global $conf;
    if (isset($conf['plugins']) && is_array($conf['plugins'])) {
        $plugins = $conf['plugins'];
    }
    else {
        $plugins = array();
    }
    if (!in_array($page, $app_pages) && !in_array($page, $plugins)) {
        $page = 'not_found';
    }
    if (!isset($conf['site_themes'][$theme])) {
        $theme = 'default';
    }
    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set('GMT');
    }
    $expire = date("D, d M Y H:i:s T", @gmmktime() + $css_max_age);
    $css_files = array(
        $include_path.'themes'.$fd.$theme.$fd.'css'.$fd.'main.css',
        $include_path.'themes'.$fd.$theme.$fd.'css'.$fd.$page.'.css'
    );
    ob_end_clean();
    header("Content-Type: text/css");
    header("Content-Style-Type: text/css");
    header("Expires: $expire");
    header('Cache-Control: max-age='.$css_max_age);
    foreach ($css_files as $file) {
        require_check($file);
        if ($fh = @fopen($file, 'r')) {
            while (!feof($fh)) {
                echo trim(preg_replace("/(\s|)(:|;|{|})(\s|)/", "$2", (
                    preg_replace("/\s{2,}/", ' ', fgets($fh, 8192))))).' ';
            } 
        }
    }
    exit;
}
function print_bool($val, $opposite=false) {
    if ($opposite) {
        if ($val) {
            $val = false;
        }
        else {
            $val = true;
        }
    }
    if ($val) {
        return 'Yes';
    }
    else {
        return 'No';
    }
}
function check_view_access($val) {
    global $user;
    $approved = false;
    switch ($val) {
        case 0:
            $approved = true;
            break;
        case 1:
            if ($user->logged_in) {
                $approved = true;
            }
            break;
        case 2:
            if ($user->admin) {
                $approved = true;
            }
            break;
    }
    return $approved;
}
function mt_to_num($val) {
    list($v1, $v2) = explode(' ', $val);
    return $v2.'.'.substr($v1, 2);
}
function echo_r($vals, $log=false) {
    if ($log) {
        error_log(print_r($vals, true));
    }
    else {
        echo '<div style="white-space: pre">'.htmlentities(print_r($vals, true)).'</div>';
    }
}
function new_folder_sort($a, $b) {
    if (strtoupper($a) == 'INBOX') {
        return -1;
    }
    elseif (strtoupper($b) == 'INBOX') {
        return 1;
    }
    else {
        return strnatcasecmp($a, $b);
    }
}
function folder_sort($a, $b) {
    return strnatcasecmp($a, $b);
}
function clean_from($string) {
    global $user;
    $return = $string;
    if (!trim($string)) {
        return 'No From';
    }
    else {
        $string = str_replace(';', ',', $string);
        if (!strstr($string, ',')) {
            if (strstr($string, '<')) {
                $return = trim(str_replace('"', '', preg_replace("/\<[^>]+\>/", '', $string)));
            }
            if (!$return) {
                $return = $string;
            }
        }
        else {
            $parts = explode(',', $string);
            $res = array();
            foreach ($parts as $i => $part) {
                $res[] = clean_from($part);
            }
            if (count($res) > 0) {
                $return = join(', ', $res);
            }
        }
    }
    return str_replace(array('<', '>', '"'), '', $return);
}
function hm_strlen($string) {
    global $user;
    global $mb_charset_codes;
    $charset = $user->page_data['charset'];
    if ($user->user_action->mb_support && $charset && isset($mb_charset_codes[strtoupper($charset)])) {
        return mb_strlen($string, $charset);
    }
    else {
        return strlen($string);
    }
}
function hm_substr($string, $start, $offset=false, $charset=false) {
    global $user;
    global $mb_charset_codes;
    if (!$charset) {
        $charset = $user->page_data['charset'];
    }
    if ($user->user_action->mb_support && $charset && isset($mb_charset_codes[strtoupper($charset)])) {
        if ($offset) {
            return mb_substr($string, $start, $offset, $charset);
        }
        else {
            return mb_substr($string, $start, mb_strlen($string), $charset);
        }
    }
    else {
        if ($offset) {
            return substr($string, $start, $offset);
        }
        else {
            return substr($string, $start);
        }
    }
}
function prep_html_part($string, $uid, $mailbox, $image_replace=false, $override=false) {
    global $user;
    global $sticky_url;
    global $conf;
    $regex = "/(background|src)=(\"|'|)(denied:|)cid:([^'\"@]+@[^>'\"]+)(\"|'|)/im";
    $regex2 = "/(background|src)=(\"|'|)(denied:|)cid:([^'\"\s]+)(\"|'|)/im";
    $regex3 = "/(background|src)=(\"|'|)()(\d+_multipart\?[^'\"\s]+)(\"|'|)/im";
    if (preg_match_all($regex, $string, $matches) || preg_match_all($regex2, $string, $matches) || preg_match_all($regex3, $string, $matches)) {
        $locations = $matches[0];
        $types     = $matches[1];
        $filenames = $matches[4];
        $_SESSION['inline_images'][$mailbox][$uid] = $filenames;
        foreach ($locations as $i => $v) {
            $string = str_replace($v, $types[$i].'="?page=inline_image&amp;mailbox='.urlencode($mailbox).
                                  '&amp;uid='.$uid.'&amp;filename='.urlencode($filenames[$i]).'" alt="'.
                                   $user->htmlsafe($filenames[$i]).'" ', $string);
        }
    }
    if ($image_replace) {
        $regex = "/((background|src))=(\"|'|)((http|ftp|rtsp)s?:\/\/|\/)[^\s]+(\"|'|)/im";
        $replaced = 0;
        if (preg_match_all($regex, $string, $matches)) {
            $outside_sources = $matches[0];
            $replaced = count($outside_sources);
            $src_type = $matches[1];
            foreach ($outside_sources as $i => $src) {
                $string = str_replace($src, $src_type[$i].'=images/place_holder.png', $string);
            }
            if ($replaced) {
                $msg = '<div style="padding: 10px;">'.$replaced.' external images replaced <a ';
                if (isset($conf['html_message_iframe']) && $conf['html_message_iframe']) {
                    $msg .= 'target="_top" ';
                }
                $msg .= 'href="'.$sticky_url.'&amp;show_external_images=1">Show External Images</a></div><br />';
                $string = $msg.$string;
            } 
        }
    }
    if ($override) {
        $url = preg_replace("/\&amp;show_external_images=(1|0)/", '&amp;show_external_images=0', $sticky_url);
        $msg = '<div style="padding: 10px;"><a ';
        if (isset($conf['html_message_iframe']) && $conf['html_message_iframe']) {
            $msg .= 'target="_top" ';
        }
        $msg .= 'href="'.$url.'">Hide External Images</a></div><br />';
        $string = $msg.$string;
    }
    $string = preg_replace("/<span[^>]+style[^>]+wingdings[^>]+>J<\/span>/i", '&#9786;', $string);
    $string = preg_replace("/<span[^>]+style[^>]+wingdings[^>]+>L<\/span>/i", '&#9785;', $string);
    $string = str_replace(array('<a ', '<A '), '<a target="_blank" ', $string);
    return $string;
}
function prep_text_part($string, $charset) {
    global $user;
    global $page_id;
    $email_regex = "/(([a-zA-Z0-9_\.\-\+])+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)/m";
    $link_regex = "/((http|ftp|rtsp)s?:\/\/(%[[:digit:]A-Fa-f][[:digit:]A-Fa-f]|[-_\.!~\*';\/\?#:@&=\+$,\[\]%[:alnum:]])+)/m";
    $max = 48;
    $links = false;
    $hl_reply = false;
    $alinks = false;
    if (isset($_SESSION['user_settings']['hl_reply']) && $_SESSION['user_settings']['hl_reply'] && !$user->page_data['raw_view']) {
        $hl_reply = true;
    }
    if (isset($_SESSION['user_settings']['text_email']) && $_SESSION['user_settings']['text_email'] && !$user->page_data['raw_view']) {
        $alinks = true;
    }
    if (isset($_SESSION['user_settings']['text_links']) && $_SESSION['user_settings']['text_links'] && !$user->page_data['raw_view']) {
        $links = true;
    }
    if ($user->page_data['raw_view']) {
        $links = false; 
        $alinks = false;
        $hl_reply = false;
    }
    $new_lines = array();
    $main_index = 0;
    $string = str_replace("\r\n", "\n", $string);
    $string = str_replace("\r", "\n", $string);
    $string = str_replace("&", "&amp;", $string);
    $string = $user->htmlsafe($string, $charset);
    $lines = explode("\n", $string);
    $pattern = '/([^ ]{'.$max.'})/';
    foreach ($lines as $line) {
        if ($hl_reply) {
            if (preg_match("/^(\&gt;|\|)/", trim($line))) {
                $new_lines[$main_index] = '<b class="reply">'.$line.'</b>';
            }
            else {
                $new_lines[$main_index] = $line;
            }
        }
        else {
            $new_lines[$main_index] = $line;
        }
        $main_index++;
    }
    unset($lines);
    $string = implode("<br />", $new_lines);
    $string = str_replace(array('"', "'", '&gt;', '&lt;', '&#160;'), array(' "', " '", ' &gt;', ' &lt;', ' &#160;', ' &gt;', '&lt;'), $string);
    if ($links) {
        if (preg_match_all($link_regex, $string, $matches, PREG_OFFSET_CAPTURE)) {
            $offset_adjust = 0;
            foreach ($matches[1] as $vals) {
                $offset = $vals[1] + $offset_adjust;
                $link = $vals[0];
                $link = str_replace('@', '^'.$page_id.'^', $link);
                $link_tag = '<a class="text_link" href="'.$link.'" title="'.$link.'" target="_blank">'.$user->str[526].'</a> ';
                $offset_adjust += hm_strlen($link_tag);
                if ($offset) {
                    $string = hm_substr($string, 0, $offset).$link_tag. hm_substr($string, $offset);
                }
                else {
                    $string = $link_tag.$string;
                }
            }
        }
    }
    if ($alinks) {
        if (preg_match_all($email_regex, $string, $matches, PREG_OFFSET_CAPTURE)) {
            $offset_adjust = 0;
            foreach ($matches[1] as $vals) {
                $offset = $vals[1] + $offset_adjust;
                $email = $vals[0];
                if (isset($_SESSION['user_settings']['compose_window']) && $_SESSION['user_settings']['compose_window']) {
                    $email_tag = '<a class="text_link" onclick="open_window(\'?page=compose&amp;to='.urlencode($email).'&amp;new_window=1\', 900, 950); return false;" '.
                                 'title="'.$email.'" href="?page=compose&amp;to='.urlencode($email).'">'.$user->str[527].'</a> ';
                }
                else {
                    $email_tag = '<a class="text_link" href="?page=compose&amp;to='.urlencode($email).'" title="'.$email.'">'.$user->str[527].'</a> ';
                }
                $offset_adjust += strlen($email_tag);
                if ($offset) {
                    $string = hm_substr($string, 0, $offset).$email_tag. hm_substr($string, $offset);
                }
                else {
                    $string = $email_tag.$string;
                }
            }
        }
    }
    $string = str_replace('^'.$page_id.'^', '@', $string);
    $string = str_replace(array(' "', " '", ' &gt;', ' &lt;', ' &#160;'), array('"', "'", '&gt;', '&lt;', '&#160;'), $string);
    return $string;
}
function timer_display($times) {
    global $page_start;
    $base = mt_to_num($page_start);
    $data = '<br /><table cellpadding="4" cellspacing="0">';
    $last = false;
    foreach ($times as $i => $v) {
        $data .= '<tr><td style="border-bottom: solid 1px #ccc;">'.round((mt_to_num($v) - $base), 4);
        if ($last) {
            $data .= ' ('.(round((mt_to_num($v) - $last), 4)).')';
        }
        $data .= '</td>';
        $data .= '<td style="border-bottom: solid 1px #ccc;">'.$i.' </td></tr>';
        $last = mt_to_num($v);
    }
    $data .= '</table>';
    return $data;
}
function output_filtered_content($tags) {
    global $user;
    global $conf;
    if (!$user->use_cookies && $user->logged_in) {
        ob_end_flush();
    }
    $string = ob_get_clean();
    foreach ($tags as $id => $val) {
        $string = remove_tags($string, $id, $val);
    }
    set_page_headers();
    echo $string;
}
function remove_tags($string, $tag_name, $strip) {
    global $page_id;
    global $conf;
    $new_page = '';
    if ($strip) {
        $marker_length = strlen("<$tag_name-$page_id>");
        $end_marker_length = $marker_length + 1;
        while (strpos($string, "<$tag_name-$page_id>") !== false) {
            $chunk = substr($string, 0, strpos($string, "<$tag_name-$page_id>"));
            $string = substr($string, (strlen($chunk) + $marker_length));
            $new_page .= $chunk;
            $chunk = substr($string, 0, strpos($string, "</$tag_name-$page_id>"));
            $string = substr($string,  (strlen($chunk) + $end_marker_length));
        }
        $new_page .= $string;
    }
    else {
        $new_page = str_replace(array("<$tag_name-$page_id>", "</$tag_name-$page_id>"), '', $string);
    }
    return ltrim($new_page);
}
function run_template() {
    global $pd;
    global $app_pages;
    global $conf;
    global $tools;
    global $include_path;
    global $fd;
    $found = false;
    $theme = 'default';
    if (in_array($pd->dsp_page, $app_pages)) {
        if (isset($conf['site_themes'][$pd->pd['theme']])) {
            $atts = $conf['site_themes'][$pd->pd['theme']];
        }
        if (isset($atts['templates']) && $atts['templates']) {
            $file = 'themes'.$fd.$pd->pd['theme'].$fd.'templates'.$fd.$pd->dsp_page.'.php';
            $theme = $pd->pd['theme'];
            require_check($file);
            require_once($include_path.$file);
        }
        else {
            $file = 'themes'.$fd.'default'.$fd.'templates'.$fd.$pd->dsp_page.'.php';
            require_check($file);
            require_once($include_path.$file);
        }
        $found = true;
    }
    if (!$found) { 
        $page_hooks = array();
        if ($pd->user->logged_in) {
            if (isset($_SESSION['plugins']['page_hooks'])) {
                $page_hooks = $_SESSION['plugins']['page_hooks'];
            }
        }
        else {
            $plugins = get_plugins(true, true);
            if (isset($plugins['page_hooks'])) {
                $page_hooks = $plugins['page_hooks'];
            }
        }
        if (!empty($page_hooks)) {
            foreach ($page_hooks as $plugin) {
                if ($pd->dsp_page == $plugin) {
                    $function_name = 'print_'.$plugin;
                    if (function_exists($function_name)) {
                        $pdata = array();
                        if (isset($pd->pd['plugin_data'][$plugin])) {
                            $pdata = $pd->pd['plugin_data'][$plugin];
                        }
                        echo $function_name($pdata, $tools[$plugin]);
                        $found = true;
                        break;
                    }
                }
            }
        } 
    }
    if (!$found) {
        $file = 'themes/'.$theme.'/templates/not_found.php';
        require_check($file);
        require_once($include_path.$file);
    }
}
function do_work_hook($location, $args=array(), $plugin_array=array()) {
    global $conf;
    global $tools;
    global $include_path;
    global $fd;
    if (empty($plugin_array) && isset($_SESSION['plugins'])) {
        $plugin_array = $_SESSION['plugins'];
    }
    if (isset($plugin_array['work_hooks'])) {
        $plugins = $plugin_array['work_hooks'];
        if (!empty($plugins)) {
            foreach ($plugins as $plugin => $vals) {
                foreach ($vals as $v) {
                    if ($location == $v) {
                        $function_name = $plugin.'_'.$location;
                        $file = 'plugins'.$fd.$plugin.$fd.'work.php';
                        if (is_readable($file)) {
                            require_check($file);
                            require_once($include_path.$file);
                            if (function_exists($function_name)) {
                                if (!$tools || !isset($tools[$plugin])) {
                                    $tools[$plugin] = hm_new('plugin_tools', $plugin);
                                }
                                if (isset($tools[$plugin])) {
                                    $function_name($tools[$plugin], $args);
                                }
                                else {
                                    error_log('HM2 Plugin Warning: '.$plugin.' unable to execute work hook: '.$location);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
function do_display_hook($location, $args=array()) {
    global $conf;
    global $tools;
    global $include_path;
    global $fd;
    $return = '';
    if (isset($_SESSION['plugins']['display_hooks'])) {
        $plugins = $_SESSION['plugins']['display_hooks'];
        if (!empty($plugins)) {
            foreach ($plugins as $plugin => $vals) {
                foreach ($vals as $v) {
                    if ($location == $v) {
                        $function_name = $plugin.'_'.$location;
                        $file = 'plugins'.$fd.$plugin.$fd.'display.php';
                        if (is_readable($file)) {
                            require_check($file);
                            require_once($include_path.$file);
                            if (function_exists($function_name)) {
                                $return .= $function_name($tools[$plugin], $args);
                            }
                        }
                    }
                }
            }
        }
    }
    return $return;
}
function get_plugins($pre_login=false, $force=false) {
    global $user;
    global $conf;
    global $available_display_hooks;
    global $available_work_hooks;
    global $force_plugin_reloading;
    global $include_path;
    global $fd;

    if (isset($conf['plugins'])) {
        $active_plugins = $conf['plugins'];
    }
    else {
        $active_plugins = array();
    }
    $active_page_hooks      = array();
    $active_display_hooks   = array();
    $active_work_hooks      = array();
    $plugin_list = array();
    $plugins_enabled = false;

    if ($force_plugin_reloading || $force || ($user->just_logged_in && is_array($active_plugins))) {
        foreach ($active_plugins as $v) {
            if (is_dir('plugins'.$fd.$v.$fd)) {
                $file = 'plugins'.$fd.$v.$fd.'config.php';
                if (is_readable($file)) {
                    require_check($file);
                    require($include_path.$file);
                    $name = $v.'_hooks';
                    $langs = $v.'_langs';
                    if (isset($$name)) {
                        foreach ($$name as $type => $vals) {
                            if ($type == 'page_hook' && $vals) {
                                    $active_page_hooks[] = $v;
                                    if (!in_array($v, $plugin_list)) {
                                        $plugin_list[] = $v;
                                    }
                                    $plugins_enabled = true;
                            }
                            elseif (is_array($vals)) {
                                foreach ($vals as $val) {
                                    if (in_array($val, ${'available_'.$type})) {
                                        ${'active_'.$type}[$v][] = $val;
                                        $plugins_enabled = true;
                                        if (!in_array($v, $plugin_list)) {
                                            $plugin_list[] = $v;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (isset($$langs)) {
                        $_SESSION['plugin_strings'][$v] = $$langs;
                    }
                }
            }
        }
        $plugins = array('work_hooks' => $active_work_hooks, 'display_hooks' => $active_display_hooks, 'page_hooks' => $active_page_hooks);
        if (!$pre_login) {
            $_SESSION['plugin_list'] = $plugin_list;
            $_SESSION['plugins'] = $plugins;
            $_SESSION['plugins_enabled'] = $plugins_enabled;
        }
    }
    else {
        $plugins = $_SESSION['plugins'];
    }
    return $plugins;
}
function require_check($file) {
    global $user;
    global $include_path;
    $file = $include_path.$file;
    $bail = false;
    if (stristr($file, '..')) {
        $bail = true;
    }
    elseif (!$include_path && substr(pathinfo($file, PATHINFO_DIRNAME), 0, 1) == '/') {
        $bail = true;
    }
    elseif (substr(pathinfo($file, PATHINFO_EXTENSION), 0, 3) != 'php' &&
            substr(pathinfo($file, PATHINFO_EXTENSION), 0, 3) != 'css' &&
            substr(pathinfo($file, PATHINFO_EXTENSION), 0, 3) != 'inc') {
        $bail = true;
    }
    elseif (!$include_path && !preg_match("/^[a-z0-9\/\\\._]+\.(inc|php|css)$/i", $file)) {
        $bail = true;
    }
    if ($bail) {
        echo 'Required file failure: '.$user->htmlsafe($file);
        exit;
    }
    return true;
}
function print_contact_page_links($total, $page, $mailbox) {
    global $user;
    global $contacts_per_page;
    $start = 1;
    $stop = ceil($total/$contacts_per_page);
    $data = '';
    if ($stop > 1) {
        $data .= $user->str[88].' ';
        $url = '?page=contacts&amp;mailbox='.urlencode($mailbox).'&amp;contacts_page=';
        for ($i=$start;$i<=$stop;$i++) {
            $data .= '<a href="'.$url.$i.'">'.$i.'</a> ';
        }
    }
    return $data;
}
function get_alt_servers($conf) {
    $alt_servers = array();
    foreach ($conf as $i => $v) {
        if (preg_match("/^alt_(\d+)_([^\s]+)$/", $i, $matches)) {
            $alt_servers[$matches[1]][$matches[2]] = $v;
        }
    }
    return $alt_servers;
}
function get_page_action($get, $post) {
    $url_class = 'mailbox';
    $post_class = false;
    
    if (isset($get['page']) && trim($get['page'])) {
        switch ($get['page']) {
            case 'compose':
            case 'contacts':
            case 'options':
            case 'search':
            case 'mailbox':
                $post_class = $get['page'];
                $url_class = $get['page'];
                break;
            case 'contact_groups':
                $post_class = 'contacts';
                $url_class = 'contacts';
                break;
            case 'folders':
            case 'profile':
                $post_class = $get['page'];
                $url_class = 'misc';
                break;
            case 'message':
            case 'new':
                $url_class = $get['page'];
                break;
            case 'about':
            case 'logout':
            case 'thread_view':
                $url_class = 'misc';
                break;
            case 'login':
                $url_class = 'mailbox';
                $post_class = 'mailbox';
                break;

            default:
                $url_class = false;
                break;
        }
    }
     if (isset($_POST['rs']) && $_POST['rs']) {
        if (isset($_POST['rsargs'][6]) && $_POST['rsargs'][6] != -1) {
            $url_class = 'mailbox';
        }
        else {
            $url_class = 'new';
        }
        switch ($_POST['rs']) {
            case 'ajax_save_outgoing_message':
            case 'ajax_next_contacts':
            case 'ajax_prev_contacts':
                $url_class = 'compose';
                break;
        }
    }
    return array('url' => $url_class, 'post' => $post_class);
}
function get_page_url() {
    $res = false;
    if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME']) {
        $res = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    }
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
        $res .= '?'.rebuild_page_args($_SERVER['QUERY_STRING']);
    }
    elseif (isset($_SERVER['argv'][0]) && $_SERVER['argv'][0]) {
        if (count($_SERVER['argv']) > 1) {
            $argv = join('+', $_SERVER['argv']);
        }
        else {
            $argv = $_SERVER['argv'][0];
        }
        $res .= '?'.rebuild_page_args($_SERVER['argv'][0]);
    }
    return $res;
}
function rebuild_page_args($str, $encode=false) {
    $res = array();
    $used = array();
    if ($str) {
        $args = explode('&', $str);
        foreach ($args as $val) {
            if (strpos($val, '=') !== false) {
                $pair = explode('=', $val);
                if (count($pair) == 2) {
                    if (!in_array($pair[0], $used)) {
                        if ($encode) {
                            $pair[1] = urlencode($pair[1]);
                        }   
                        else {
                            if (strpos($pair[1], ' ') !== false) {
                                $pair[1] = str_replace(' ', '+', $pair[1]);
                            }
                        }
                        $res[] = $pair[0].'='.$pair[1];
                        $used[] = $pair[0];
                    }
                }
            }
        }
    }
    return join('&amp;', $res);
}
function unset_attachments($id) {
    global $fd;
    global $conf;
    $path = $conf['attachments_path'];
    if (isset($_SESSION['attachments'][$id]) && !empty($_SESSION['attachments'][$id])) {
        foreach ($_SESSION['attachments'][$id] as $i => $v) {
        if (substr($path, -1) != $fd) {   
                $filename = $path.$fd.$i;
            }   
            else {      
                $filename = $path.$i;
            }   
            if (is_readable($filename)) {
                unlink($filename);
            }
        }
        $_SESSION['attachments'][$id] = array(); 
        unset($_SESSION['attachments'][$id]);
    }
}
function purge_old_attachments() {
    global $attachment_lifetime;
    $now = time();
    if (isset($_SESSION['compose_sessions'])) {
        foreach ($_SESSION['compose_sessions'] as $id => $time) {
            if ($now - $time > $attachment_lifetime) {
                unset_attachments($id);
            }
        }
    }
}
function get_mimetype_extension($type) {
    switch ($type) {
        case 'text/html':
            $exten = '.htm';
            break;
        case 'image/jpeg':
        case 'image/pjpeg':
        case 'image/jpg':
            $exten = '.jpg';
            break;
        case 'image/gif':
            $exten = '.gif';
            break;
        case 'image/png':
            $exten = '.png';
            break;
        case 'image/bmp':
            $exten = '.bmp';
            break;
        case 'message/rfc822':
            $exten = '.mime';
            break;
        case 'application/pgp-signature':
        case 'message/disposition-notification':
        case 'message/delivery-status':
        case 'message/rfc822-headers':
        case 'text/plain':
        case 'text/unknown':
            $exten = '.txt';
            break;
        case 'text/enriched':
            $exten = '.rtf';
            break;
        default:
            $exten = '';
            break;
    }
    return $exten;
}
function get_php_version() {
    $ver = phpversion();
    return (float) $ver{0}.'.'.$ver{2}.$ver{4};
}
function hm_new($class_name, $args=false) {
    /* for PHP4 replace the = with =& for better resource utilization */
    if ($args) {
        $obj = new $class_name($args);
    }
    else {
        $obj = new $class_name();
    }
    return $obj;
}
function add_forwarded_attachments($parts, $uid, $c_session) {
    global $conf;
    global $imap;
    global $fd;
    $enc = false;
    $path = $conf['attachments_path'];
    foreach ($parts as $id => $vals) {
        if (strtolower($vals['encoding']) == 'base64' || $vals['encoding'] === 'none') {
            $encoding = false;
        }
        else {
            $encoding = 'base64';
        }
        $output_id = md5(uniqid(rand(),1));
        if (substr($path, -1) != $fd) {
            $output_name = $path.$fd.$output_id;
        }
        else {
            $output_name = $path.$output_id;
        }
        $output_file = fopen($output_name, 'w+');
        if (strtolower($vals['encoding']) == 'base64' || $encoding == 'base64') {
            $enc = 'base64';
        }
        else {
            $enc = '8bit';
        }
        if (is_resource($output_file)) {
            $size = 0;
            $left_over = '';
            $read_size = 0;
            $lit_size = $imap->get_message_part_start($uid, $id);
            while ($clear = $imap->get_message_part_line()) {
                $read_size += strlen($clear);
                if ($read_size > $lit_size) {
                    $diff = $read_size - $lit_size;
                    $clear = substr($clear, 0, (0 - $diff));
                    $read_size -= $diff;
                }
                if ($encoding == 'base64') {
                    if ($left_over) {
                        $clear = $left_over.$clear;
                    }
                    $data = base64_encode($clear);
                    while ($data) {
                        if (strlen($data) > 76) {
                            fwrite($output_file, substr($data, 0, 76)."\r\n");
                            $size += 78;
                            $left_over = '';
                            $data = substr($data, 76);
                        }
                        elseif (strlen($data) < 76) {
                            $left_over = base64_decode($data);
                            $data = '';
                        }
                        else {
                            $left_over = base64_decode($data);
                            $data = '';
                        }
                    }
                }
                else {
                    fwrite($output_file, $clear);
                    $size += strlen($clear);
                }
            }
            if ($left_over && $encoding == 'base64') {
                $last_line = base64_encode($left_over)."\r\n";
                $size += strlen($last_line);
                fwrite($output_file, $last_line."\r\n");
            }
            $filename = '';
            if (isset($vals['filename']) && $vals['filename']) {
                $filename = $vals['filename'];
            }
            elseif (isset($vals['name']) && $vals['name']) {
                $filename = $vals['name'];
            }
            elseif (isset($vals['description']) && $vals['description']) {
                $filename = $vals['description'];
            }
            if (strtoupper($filename) == 'NIL') {
                $filename = '';
            }
            if (!$filename) {
                $filename = 'message_'.$id; 
                $exten = get_mimetype_extension(strtolower($vals['type'].'/'.$vals['subtype']));
                if (strtolower(substr($filename, -4)) != $exten) {
                    $filename .= $exten;
                }
            }
            $attributes = array('time' => time(), 'encoding' => $enc, 'realname' => $filename, 'filename' => $output_id, 'size' => $size, 'type' => $vals['type'].'/'.$vals['subtype']);
            $_SESSION['attachments'][$c_session][$output_id] = $attributes;
        }
    }
}
/* HTTP headers, XML declaration, doc type */
function set_page_headers($force_html=false) {
    global $pd;
    $declaration = '';
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false && $pd->html_content_type == 'xhtml') {
        header("Content-Type: application/xhtml+xml; charset=utf-8");
        $declaration .=  '<?xml version="1.0" encoding="UTF-8"?>';
    }
    $declaration .=  '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
                    '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
    echo $declaration;
}
function decode_unicode_url($str) {
    $res = '';
    $i = 0;
    $max = strlen($str) - 6;
    while ($i <= $max) {
        $character = $str[$i];
        if ($character == '%' && $str[$i + 1] == 'u') {
            $value = hexdec(substr($str, $i + 2, 4));
            $i += 6;
            if ($value < 0x0080) {
                $character = chr($value);
            }
            elseif ($value < 0x0800) {
                $character = chr((($value & 0x07c0) >> 6) | 0xc0).chr(($value & 0x3f) | 0x80);
            }
            else { 
                $character = chr((($value & 0xf000) >> 12) | 0xe0).chr((($value & 0x0fc0) >> 6) | 0x80).chr(($value & 0x3f) | 0x80);
            }
        }
        else {
            $i++;
        }
        $res .= $character;
    }
  return $res . substr($str, $i);
}
function stream_imap_append($message, $session, $mailbox) {
    global $imap;
    global $conf;
    global $include_path;
    global $fd;
    $path = $conf['attachments_path'];
    $message->output_smtp_message();
    $status = false;
    $email = $message->output_imap_message();
    $size = $message->get_imap_message_size(strlen($email));
    if ($imap->append_start($mailbox, $size)) {
        $imap->append_feed($email);
        if (isset($_SESSION['attachments'][$session]) && !empty($_SESSION['attachments'][$session])) {
            foreach ($_SESSION['attachments'][$session] as $i => $v) {
                $headers = $message->build_part_header($v['realname'], $v['type'], $v['encoding']);
                if (substr($path, -1) != $fd) {   
                    $filename = $path.$fd.$i;
                }   
                else {      
                    $filename = $path.$i;
                }   
                if (is_readable($filename)) {
                    $imap->append_feed($headers);
                    $input_file = fopen($filename, 'r');
                    if (is_resource($input_file)) {
                        while (!feof($input_file)) {
                            $string = fgets($input_file, 1024);
                            if ($string) {
                                $imap->append_feed(rtrim($string, "\r\n"));
                            }
                        }
                        fclose($input_file);
                    }
                }
                $imap->append_feed("--".$message->boundry."--");
            }
        }
        $status = $imap->append_end();
    }
    return $status;
}
/* sort functions */
function sort_date($a, $b, $rev=1) {
    if (strtotime(trim($a['date']))*$rev < strtotime(trim($b['date']))*$rev) {
        return true;
    }
    else {
        return false;
    }
} 
function sort_date_r($a, $b) {
    return sort_date($a, $b, -1);
}
function sort_idate($a, $b, $rev=1) {
    if (strtotime(trim($a['date']))*$rev < strtotime(trim($b['date']))*$rev) {
        return true;
    }
    else {
        return false;
    }
} 
function sort_idate_r($a, $b) {
    return sort_idate($a, $b, -1);
}
function get_msg_list_settings() {
    global $conf;
    global $include_path;
    global $fd;
    global $msg_list_flds;
    global $default_list_heading;
    global $default_onclick;

    $page_cols    = $msg_list_flds;
    $list_heading = $default_list_heading;
    $onclick      = $default_onclick;

    $theme = 'default';
    if (isset($_SESSION['user_settings']['theme'])) {
        $user_theme = $_SESSION['user_settings']['theme'];
        if (isset($conf['site_themes'][$user_theme])) {
            if ($conf['site_themes'][$user_theme]['templates']) {
                $theme = $user_theme;
            }    
        }
    }
    if (is_readable('themes/'.$theme.'/config.php')) {
        require($include_path.'themes'.$fd.$theme.$fd.'config.php');
    }
    if (isset($_SESSION['user_settings']['display_list_heading'])) {
        $list_heading = $_SESSION['user_settings']['display_list_heading'];
    }
    if (isset($_SESSION['user_settings']['msg_list_onclick'])) {
        $onclick = $_SESSION['user_settings']['msg_list_onclick'];
    }
    if (isset($_SESSION['user_settings']['msg_list_flds'])) {
        $page_cols = $_SESSION['user_settings']['msg_list_flds'];
    }
    return array($page_cols, $list_heading, $onclick);
}
?>
