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

/* work functions */
function url_action_news($tools, $get, $post) {
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }
    $pd = array();
    $feed_id = 0;
    $dsp_page = 'news';
    $news_title = false;
    $url = false;
    $title = false;
    $news_limit = 10;
    $added = false; 
    $format = false;
    $limit = 10;
    $pd['filter_opts'] = array('None', 'Filtered HTML', 'Text Only');
    $feeds = $tools->get_setting('news_feeds');
    $feed_filter = $tools->get_setting('feed_filter');
    $feed_ttl = $tools->get_setting('feed_ttl');
    if (isset($get['manage_news'])) {
        $dsp_page = 'manage';
        if (isset($post['add_feed']) || isset($post['update_feed'])) {
            if (isset($post['feed_url'])) {
                $url = trim($post['feed_url']);
            }
            if (isset($post['feed_title'])) {
                $title = trim($post['feed_title']);
            }
            if (isset($post['feed_limit'])) {
                $limit = (int) $post['feed_limit'];
            }
            if (isset($post['feed_type']) && in_array($post['feed_type'], array('rss', 'atom'))) {
                $format = $post['feed_type'];
            }
            if ($url && $title && $limit && $format) {
                /* check feed here */
                $news_check = get_feed($tools, $url, $format, $limit, 0, $feed_ttl);
                if (count($news_check) > 1) {
                    $vals = array($title, $url, $limit, $format);
                    $cache = hm_new('cache');
                    if (isset($post['update_feed'])) {
                        if (isset($post['feed_id']) && isset($feeds[$post['feed_id']])) {
                            $feeds[$post['feed_id']] = $vals;
                            $tools->save_setting('news_feeds', $feeds);
                            $tools->send_notice('News Source Updated');
                            $cache->save_feed($feed_id, $news_check);
                        }
                    }
                    else {
                        $feeds[] = $vals;
                        $cache->save_feed((count($feeds) - 1), $news_check);
                        $tools->save_setting('news_feeds', $feeds);
                        $tools->send_notice('News Source Added');
                        $title = '';
                        $added = true;
                        $url = '';
                        $limit = 10;
                        $format = 'rss';
                    }
                }
                else {
                    $tools->send_notice('Could not get news from that Feed URL');
                }
            }
            else {
                $tools->send_notice('All fields are required');
            }
        }
        elseif (isset($post['update_news_options'])) {
            if (isset($post['feed_ttl'])) {
                $feed_ttl = (int) $post['feed_ttl'];
            }
            else {
                $feed_ttl = 300;
            }
            if (isset($post['feed_filter'])) {
                $feed_filter = (int) $post['feed_filter'];
            }
            else {
                $feed_filter = 1;
            }
            $tools->save_setting('feed_filter', $feed_filter);
            $tools->save_setting('feed_ttl' , $feed_ttl);
            $tools->send_notice('News Options Updated');
        }
        elseif (isset($post['delete_feed'])) {
            if (isset($get['feed'])) {
                if (isset($feeds[$get['feed']])) {
                    unset($feeds[$get['feed']]);
                    $tools->save_setting('news_feeds', $feeds);
                    unset($get['feed']);
                    $tools->send_notice('Source Deleted');
                }
            }
        }
        if (isset($get['feed']) && !$added) {
            if (isset($feeds[$get['feed']])) {
                $dsp_page = 'edit';
                $url = $feeds[$get['feed']][1];
                $title = $feeds[$get['feed']][0];
                $limit = $feeds[$get['feed']][2];
                $format = $feeds[$get['feed']][3];
                $feed_id = $get['feed'];
            }
        }
    }
    else {
        if (isset($get['feed'])) {
            if ($get['feed'] == -1) {
                $news_title = 'All News';
                $format = 'rss';
                $pd['news'] = get_all_news($feeds, $tools, $feed_ttl);
                $news_limit = 50;
                $feed_id = -1;
            }
            elseif (isset($feeds[$get['feed']])) {
                $news_title = $feeds[$get['feed']][0];
                $format = $feeds[$get['feed']][3];
                $feed_id = $get['feed'];
                $pd['news'] = get_feed($tools, $feeds[$get['feed']][1], $feeds[$get['feed']][3], $feeds[$get['feed']][2], $feed_id, $feed_ttl);
            }
        }
    }
    $pd['feeds'] = $feeds;
    $pd['news_title'] = $news_title;
    $pd['news_limit'] = $news_limit;;
    $pd['dsp_page'] = $dsp_page;
    $pd['limit'] = $limit;
    $pd['url'] = $url;
    $pd['feed_filter'] = $feed_filter;
    $pd['feed_ttl'] = $feed_ttl;
    $pd['feed_id'] = $feed_id;
    $pd['title'] = $title;
    $pd['format'] = $format;
    $tools->set_title('News');
    return $pd;
}
function get_feed($tools, $url, $type, $limit, $feed_id, $ttl) {
    if ($ttl > 0) {
        $cache = hm_new('cache');
        $cache->ttl = $ttl;
        $data = $cache->get_feed($feed_id);
        if ($data) {
            return $data;
        }
    }
    $feed = hm_new('feed');
    $feed->limit = $limit;
    $feed->feed_type = $type;
    $feed->parse_feed($url);
    $cache = hm_new('cache');
    $cache->save_feed($feed_id, $feed->parsed_data);
    return $feed->parsed_data;
}
function get_feed_data_from_url($url) {
    if (!preg_match("?^http://?", ltrim($url))) {
        $url = 'http://'.ltrim($url);
    }
    $buffer = '';
    if (function_exists('curl_setopt')) {
        $type = 'curl';
    }
    else {
        $type = 'file';
    }
    switch ($type) {
        case 'curl':
            $rand =  md5(uniqid(rand(), 1));
            $curl_handle=curl_init();
            curl_setopt($curl_handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
            curl_setopt($curl_handle,CURLOPT_URL, $url);
            curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15);
            curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl_handle, CURLOPT_COOKIEJAR, '/tmp/'.$rand.'.txt');
            curl_setopt($curl_handle, CURLOPT_COOKIEFILE, '/tmp/'.$rand.'.txt');
            $buffer = curl_exec($curl_handle);
            curl_close($curl_handle);
            unset($curl_handle);
            break;
        case 'file':
            $buffer = file_get_contents($url); 
            break;
    }
    return $buffer;
}
function get_all_news($feeds, $tools, $ttl) {
    $all_news = array();
    sort($feeds);
    foreach ($feeds as $i => $vals) {
        $data[] = get_feed($tools, $vals[1], $vals[3], $vals[2], $i, $ttl);
    }
    $titles = array();
    foreach ($data as $i => $vals) {
        if (is_array($vals)) {
            foreach ($vals as $n => $news) {
                if ($n == 0) {
                    continue;
                }
                if (isset($news['title'])) {
                    if (in_array($news['title'], $titles)) {
                        continue;
                    }
                    else {
                        $titles[] = $news['title'];
                    }
                }
                if (isset($news['updated'])) {
                    $news['pubdate'] = $news['updated'];
                }
                if (isset($news['pubdate'])) {
                    $news['source'] = $feeds[$i][0];
                    $news['source_url'] = $feeds[$i][1];
                    $all_news[] = $news;
                }
            }
        }
    }
    usort($all_news, 'sort_by_time');
    array_unshift($all_news, array('title' => '', 'description' => ''));
    return $all_news;
}
class feed {
    var $url;
    var $id;
    var $xml_data;
    var $parsed_data;
    var $depth;
    var $type;
    var $limit;
    var $heading_block;
    var $data_block;
    var $update_cache;
    var $collect;
    var $item_count;
    var $refresh_cache;
    var $init_cache;
    var $cache_limit;
    var $sort;

    function feed() {
        $this->sort = true;
        $this->limit = 5;
        $this->cache_limit = 0;
        $this->url = false;
        $this->xml_data = false;
        $this->id = 0;
        $this->parsed_data = array();
        $this->depth = 0;
        $this->feed_type = 'rss';
        $this->heading_block = false;
        $this->data_block = false;
        $this->collect = false;
        $this->refresh_cache = false;
        $this->update_cache = false;
        $this->init_cache = false;
        $this->item_count = 0;
    }
    function get_feed_data($url) {
        $this->xml_data = get_feed_data_from_url($url);
    }
    function sort_parsed_data() {
        $data = $this->parsed_data;
        $title = array_shift($data);
        usort($data, 'sort_by_time');
        $final_list = array();
        $i = 1;
        foreach ($data as $vals) {
            $final_list[] = $vals;
            if ($i == $this->limit) {
                break;
            }
            $i++;
        }
        array_unshift($final_list, $title);
        $this->parsed_data = $final_list;
    }
    function parse_feed($url) {
        global $dbwrap;
        $this->get_feed_data($url);
        if (!empty($this->parsed_data)) {
            return true;
        }
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $this);
        if ($this->feed_type == 'atom' || $this->feed_type == 'rss') {
            xml_set_element_handler($xml_parser, $this->feed_type.'_start_element', $this->feed_type.'_end_element');
            xml_set_character_data_handler($xml_parser, $this->feed_type.'_character_data');
            if  (xml_parse($xml_parser, $this->xml_data)) {
                xml_parser_free($xml_parser);
                if ($this->sort) {
                    $this->sort_parsed_data();
                }
                /* cache here ... */
                return true;
            }
            else {
                return false; 
            }
        }
        else {
            return false;
        }
    }
    /* ATOM FEED FUNCTIONS */
    function atom_start_element($parser, $tagname, $attrs) {
        if ($tagname == 'FEED') {
            $this->heading_block = true;
        }
        if ($tagname == 'ENTRY') {
            $this->heading_block = false;
            $this->item_count++;
            $this->data_block = true;
        }
        if ($this->data_block) {
            switch ($tagname) {
                case 'TITLE':
                case 'SUMMARY':
                case 'CONTENT':
                case 'GUID':
                case 'UPDATED':
                case 'MODIFIED':
                    $this->collect = strtolower($tagname);
                    break;
                case 'LINK':
                    if (isset($attrs['REL'])) {
                        $rel = $attrs['REL'];
                    }
                    else {
                        $rel = '';
                    }
                    $this->parsed_data[$this->item_count]['link_'.$rel] = $attrs['HREF'];
                    break;
            }
        }
        if ($this->heading_block) {
            switch ($tagname) {
                case 'TITLE':
                case 'UPDATED':
                case 'LANGUAGE':
                case 'ID':
                    $this->collect = strtolower($tagname);
                    break;
                case 'LINK':
                    if (isset($attrs['REL'])) {
                        $rel = $attrs['REL'];
                    }
                    else {
                        $rel = '';
                    }
                    $this->parsed_data[0]['link_'.$rel] = $attrs['HREF'];
                    break;
            }
        }
        $this->depth++;
    }
    function atom_end_element($parser, $tagname) {
        $this->collect = false;
        if ($tagname == 'ENTRY') {
            $this->data_block = false;
        }
        $this->depth--;
    }
    function atom_character_data($parser, $data) {
        if ($this->heading_block && $this->collect) {
            $this->parsed_data[0][$this->collect] = trim($data);
        }
        if ($this->data_block && $this->collect) {
            if ($this->collect == 'updated' || $this->collect == 'modified') {
                $this->collect = 'pubdate';
            }
            if (isset($this->parsed_data[$this->item_count][$this->collect])) {
                $this->parsed_data[$this->item_count][$this->collect] .= trim($data);
            }
            else {
                $this->parsed_data[$this->item_count][$this->collect] = trim($data);
            }
        }
    }
    /* RSS FEED FUNCTIONS */
    function rss_start_element($parser, $tagname, $attrs) {
        if ($tagname == 'CHANNEL') {
            $this->heading_block = true;
        }
        if ($tagname == 'ITEM') {
            $this->heading_block = false;
            $this->item_count++;
            $this->data_block = true;
        }
        if ($this->data_block) {
            switch ($tagname) {
                case 'TITLE':
                case 'LINK':
                case 'DESCRIPTION':
                case 'GUID':
                case 'PUBDATE':
                case 'DC:DATE':
                    $this->collect = strtolower($tagname);
                    break;
            }
        }
        if ($this->heading_block) {
            switch ($tagname) {
                case 'TITLE':
                case 'PUBDATE':
                case 'LANGUAGE':
                case 'DESCRIPTION':
                case 'LINK':
                    $this->collect = strtolower($tagname);
                    break;
                    
            }
        }
        $this->depth++;
    }
    function rss_end_element($parser, $tagname) {
        $this->collect = false;
        if ($tagname == 'ITEM') {
            $this->data_block = false;
        }
        $this->depth--;
    }
    function rss_character_data($parser, $data) {
        if ($this->heading_block && $this->collect) {
            $this->parsed_data[0][$this->collect] = $data;
        }
        if ($this->data_block && $this->collect) {
            if ($this->collect == 'dc:date') {
                $this->collect = 'pubdate';
            }
            if (isset($this->parsed_data[$this->item_count][$this->collect])) {
                $this->parsed_data[$this->item_count][$this->collect] .= trim($data);
            }
            else {
                $this->parsed_data[$this->item_count][$this->collect] = trim($data);
            }

        }
    }
}
class cache {
    var $ttl;
    function cache() {
        $this->ttl = 300;
        if (!isset($_SESSION['news_cache'])) {
            $_SESSION['news_cache'] = array();
        }
    }
    function save_feed($id, $data) {
        $_SESSION['news_cache'][$id] = array(time(), $data);
    }
    function get_feed($id) {
        if (isset($_SESSION['news_cache'][$id])) {
            $start_time = $_SESSION['news_cache'][$id][0];
            $diff = time() - $start_time;
            if ($diff < $this->ttl) {
                return $_SESSION['news_cache'][$id][1];
            }
        }
        return false;
    }
}
function sort_by_time($a, $b) {
    if (!isset($a['pubdate']) || !isset($b['pubdate'])) {
        return 0;
    }
    $time1 = strtotime($a['pubdate']);
    $time2 = strtotime($b['pubdate']);
    if ($time1 == $time2) {
        return 0;
    }
    elseif ($time1 < $time2) {
        return 1;
    }
    else {
        return -1;
    }
}
/* display functions */
function print_news($pd, $tools) {
    $data = '<div id="news">';
    $page = $pd['dsp_page'];
    if ($page == 'manage' || $page == 'edit') {
        $data .= '<h2 id="mailbox_title2">Manage News</h2>';
        $data .= '<div class="manage"><a href="?page=news">News</a></div>';
        $data .= '<div id="manage_news">';
        $data .= '<form method="post" action="">';
        $data .= '<table class="manage_form" cellpadding="0" cellspacing="0">';
        $data .= '<tr><th colspan="2">Existing Sources</th></tr>';
        $data .= '<tr><td class="heading">Title</td><td class="heading">URL</td><td class="heading">Format</td><td class="heading">Limit</td></tr>';
        if (is_array($pd['feeds']) && !empty($pd['feeds'])) {
            foreach ($pd['feeds'] as $i => $v) {
                $data .= '<tr><td><a href="?page=news&amp;manage_news=1&amp;feed='.$i.'">'.$tools->display_safe($v[0]).'</a></td><td>'.
                        str_replace('&', '&amp;', $tools->display_safe($v[1])).'</td><td>'.$tools->display_safe($v[3]).
                         '</td><td>'.$tools->display_safe($v[2]).'</td></tr>';
            }
        }
        else {
            $data .= '<tr><td colspan="3" align="center"><br /><span style="font-style: italic; font-size: 90%;">No Sources Found</span></td></tr>';
        }
        $data .= '</table><br /><br />';
        $data .= '<table class="manage_form" cellpadding="0" cellspacing="0">';
        if ($page == 'edit') {
            $data .= '<tr><th colspan="2">Edit News Source</th></tr>';
        }
        else {
            $data .= '<tr><th colspan="2">Add a News Source</th></tr>';
        }
        $data .= '<tr><td class="opt_leftcol">Title</td><td><input type="text" name="feed_title" value="'.
                 $tools->display_safe($pd['title']).'" /></td></tr>';
        $data .= '<tr><td class="opt_leftcol">Feed URL</td><td><input type="text" style="width: 300px;" name="feed_url" value="'.
                 $tools->display_safe($pd['url']).'" /></td></tr>';
        $data .= '<tr><td class="opt_leftcol">News Format</td><td><select name="feed_type">';
        $data .= '<option ';
        if ($pd['format'] == 'rss') {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="rss">RSS</option>';
        $data .= '<option ';
        if ($pd['format'] == 'atom') {
            $data .= 'selected="selected" ';
        }
        $data .= 'value="atom">ATOM</option>';
        $data .='</select></td></tr>';
        $data .= '<tr><td class="opt_leftcol">Item Limit</td><td><input type="text" style="width: 30px;" name="feed_limit" value="'.
                 $tools->display_safe($pd['limit']).'" /></td></tr>';
        if ($page == 'edit') {
            $data .= '<tr><td colspan="2"><br /><input type="submit" name="update_feed" value="Update" />';
            $data .= ' &nbsp;<input type="submit" name="delete_feed" value="Delete" />';
            $data .= ' &nbsp;&nbsp;<a href="?page=news&amp;manage_news=1">Back</a>';
            $data .= '<input type="hidden" name="feed_id" value="'.$pd['feed_id'].'" /></td></tr>';
        }
        else {
            $data .= '<tr><td><br /><input type="submit" name="add_feed" value="Add" /></td></tr>';
        }
        $data .= '</table><br /><br />';
        $data .= '<table class="manage_form" cellpadding="0" cellspacing="0">';
        $data .= '<tr><th colspan="2">News Options</th></tr>';
        $data .= '<tr><td class="opt_leftcol">Cache Lifetime (seconds)</td><td><input type="text" style="width: 40px;" name="feed_ttl" value="'.$pd['feed_ttl'].'" /></td></tr>';
        $data .= '<tr><td class="opt_leftcol">Content filter</td><td><select name="feed_filter">';
        foreach ($pd['filter_opts'] as $i => $v) {
            $data .= '<option value="'.$i.'" ';
            if ($i == $pd['feed_filter']) {
                $data .= 'selected="selected" ';
            }
            $data .= '>'.$v.'</option>';
        }
        $data .= '</select></td></tr>';
        $data .= '<tr><td colspan="2"><br /><input type="submit" name="update_news_options" value="Update" /></td></tr>';
        $data .= '</table>';
        $data .= '</form>';
        $data .= '</div>';
    }
    else {
        $data .= '<h2 id="mailbox_title2">News</h2>';
        $data .= '<div class="manage"><a href="?page=news&amp;manage_news=1">Manage</a></div>';
        $data .= '<table style="clear: both;"><tr><td valign="top">';
        $data .= '<div class="feed_list">';
        if (!empty($pd['feeds'])) {
            $data .= '<ul>';
            $data .= '<li><a href="?page=news&amp;feed=-1" ';
            if ($pd['feed_id'] == -1 && isset($_GET['feed'])) {
                $data .= 'style="font-weight: bold;" ';
            }
            $data .= '>All</a></li>';
            foreach ($pd['feeds'] as $i => $vals) {
                $data .= '<li><a ';
                if ($i == $pd['feed_id'] && isset($_GET['feed'])) {
                    $data .= 'style="font-weight: bold;" ';
                }
                $data .= 'href="?page=news&amp;feed='.$i.'">'.$tools->display_safe($vals[0]).'</a></li>';
            }
            $data .= '</ul>';
        }
        else {
            $data .= '<div style="padding: 10px; font-style: italic;">No Feeds Found</div><br/>';
        }
        $data .= '</div>';
        $data .= '</td><td valign="top">';
        if ($pd['feed_id'] == -1) {
            $data .= '<span style="font-size: 120%; font-weight: bold;">All News</span>';
        }
        if ($pd['news_title']) {
            if (isset($pd['news'][0]) && !empty($pd['news'][0])) {
                $data .= print_feed($pd['news'], $pd['format'], $pd['news_limit'], $tools, $pd['news_title'], $pd['feed_filter'], $pd['feed_id']);
            }
            else {
                $data .= '<div style="padding-left: 30px; font-style: italic;">No News Items found for: '.$tools->display_safe($pd['news_title']).'</div>';
            }
        }
        $data .= '</td></tr></table>';
    }
    $data .= '</div>';
    return $data;
}
function print_feed($news, $type, $limit, $tools, $title, $filter, $feed_id) {
    $data = '<div class="feed">';
    $n = 0;
    if (is_array($news) && !empty($news)) {
    foreach ($news as $i => $vals) {
        if ($n == $limit) {
            break;
        }
        if ($i == 0) {
            $link = false;
            switch (true) {
                case isset($vals['link']):
                    $link = trim($vals['link']);
                    break;
                case isset($vals['link_alternate']):
                    $link = trim($vals['link_alternate']);
                    break;
                case isset($vals['link_']):
                    $link = trim($vals['link_']);
                    break;
            }
            $data .= '<div class="feed_title">';
            if ($link) {
                $data .= '<a href="'.$link.'">'.$tools->display_safe($vals['title']).'</a>';
            }
            else {
                $data .= $tools->display_safe($vals['title']);
            }
            $data .= '</div>';
        }
        else {
            $link = false;
            switch (true) {
                case isset($vals['link']):
                    $link = trim($vals['link']);
                    break;
                case isset($vals['link_alternate']):
                    $link = trim($vals['link_alternate']);
                    break;
                case isset($vals['link_']):
                    $link = trim($vals['link_']);
                    break;
            }
            $data .= '<div class="feed_item_title">';
            if (isset($vals['description'])) {
                $content = $vals['description'];
            }
            elseif (isset($vals['content'])) {
                $content = $vals['content'];
            }
            else {
                $content = '';
            }
            $title = $vals['title'];
            switch ($filter) {
                case 0:
                    break;
                case 2:
                    $title = $tools->display_safe(strip_tags($title));
                    $content = $tools->display_safe(strip_tags($content));
                    break;
                default:
                    $title = $tools->display_safe(strip_tags($title));
                    $content = $tools->display_html($content);
                    break;
            }
            if ($link) {
                $data .= '<a href="'.str_replace('&', '&amp;', $link).'">'.$title.'</a>';
            }
            else {
                $data .= $title;
            }
            $data .= '</div>';
            $data .= '<div class="meta">';
            if ($feed_id == -1) {
                $data .= '<span class="source"><a target="_blank" href="'.
                str_replace('&', '&amp;', $tools->display_safe($vals['source_url'])).'">'.
                $tools->display_safe($vals['source']).'</a></span>';
            }
            if (isset($vals['pubdate'])) {
                $data .= $tools->display_safe($vals['pubdate']);
            }
            $data .= '</div><div class="feed_item_content">'.$content.'</div>';
            $n++;
        }
    }
    }
    else {
        $data .= '<span style="color: #999;">Unable to connect to feed</span>';
    }
    $data .= '</div>';
    return $data;
}
?>
