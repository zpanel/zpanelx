<?php
/*
	File:         rss_aggregator.inc
	Author:       Ryo Chijiiwa, ilohamail.org
	License:      GPL, part of IlohaMail
	Description:  RSS aggregator for IlohaMail, built on DataManager.
*/

//include Magpie RSS
define('MAGPIE_DIR', '../include/magpie/');
define('MAGPIE_CACHE_ON', true);
define('MAGPIE_CACHE_DIR', fs_get_path("cache", "rss", "feed"));
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
require_once(MAGPIE_DIR.'rss_fetch.inc');
require_once(MAGPIE_DIR.'rss_utils.inc');

//include required modules
include_once('../include/data_manager.inc');
include_once('../include/utf8.inc');

class RSSAggregator_obj extends DataManager_obj{
	var $max_items_channel;
	var $max_items_total;
	var $timezone;
	var $is_public;
	
	function RSSAggregator_obj($max_items_channel=10, $max_total=50, $tz=0, $public=false){		
		$this->max_items_channel = $max_items_channel;
		$this->max_items_total = $max_total;
		$this->timezone = $tz;
		$this->is_public = $public;
	}
	
	
	function setTimezone($tz){
		$this->timezone = $tz;
	}
	
	
	function fetchGroups($sort_order='ASC'){
	/*
		Return array of group names
	*/
		return $this->getDistinct('grp', 'ASC');
	}
	
	
	function fetchNames($show_group=false){
	/*
		Return array of feed names, where key is id and value is feed name.
		If $show_group is false, returns all feeds, otherwise returns feeds
		for that group/category.
	*/
		$where = array(array('f'=>'rss','o'=>'!=','v'=>'null'));
		if ($show_group) $where[] = array('f'=>'grp','o'=>'=','v'=>$show_group);
		return $this->fetch_fields_where("id,name", $where, "name", "ASC");
	}
	
	
	function fetchSingleFeed($feed_id){
	/*
		Given feed ID, returns feed URL.
	*/
		if (!$feed_id) return false;
		
		$query = array(array('f'=>'id','o'=>'=','v'=>$feed_id));
		if ($this->is_public) $query[] = array('f'=>'is_private','o'=>'is','v'=>'NULL');
		$feeds_a = $this->fetch_fields_where("rss", $query);
		if (is_array($feeds_a)) return $feeds_a[0];
		return false;
	}
	
	
	function fetchGroupFeeds($group){
	/*
		Given group name, returns RSS URLs for all feeds in that group.
	*/
		$query = array(array('f'=>'grp','o'=>'=','v'=>$group));
		if ($this->is_public) $query[] = array('f'=>'is_private','o'=>'is','v'=>'NULL');
		$feeds_a = $this->fetch_fields_where("rss", $query);

		if (!is_array($feeds_a)) return false;
		
		reset($feeds_a);
		while(list($k,$a)=each($feeds_a)){
			if ($a["rss"]) $feeds[] = $a["rss"];
		}
		return $feeds;
	}
	
	
	function fetchAllFeeds(){
	/*
		Get all feeds.... duh.
	*/
		if (!$this->is_public){
			$feeds = $this->getDistinct('rss', 'ASC');
			return $feeds;
		}
		
		$feeds_a = $this->fetch_fields_where("rss", array(array('f'=>'is_private','o'=>'is','v'=>'NULL')));
		if (!is_array($feeds_a)) return false;
		
		reset($feeds_a);
		while(list($k,$a)=each($feeds_a)){
			if ($a["rss"]) $feeds[] = $a["rss"];
		}
		return $feeds;
	}
	
	
	function parse_rfc822time($str){
	/*
		Parses dates formatted as specified in RFC 822, returns timestamp in server's timezone.
	*/
	
		//try PHP's strtotime first	
		if ($str) $time1 = strtotime($str);
		if ($time1 && $time1!=-1) return $time1-(int)date('Z');
		
		//replace double spaces with single space
		$str = trim($str);
		$str = str_replace("  ", " ", $str);
	
		//strip off day of week, if present
		$pos=strpos($str, " ");
		if (!is_numeric(substr($str, 0, $pos))) $str = substr($str, $pos+1);

		//3char-month to int conversion table
		$month_a=array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);

		//explode, take good parts
		$a=explode(" ",$str);
		$month_str=$a[1];
		$month=$month_a[$month_str];
		$day=$a[0];
		$year=$a[2];
		$time=$a[3];
		$tz_str = $a[4];
		$tz = substr($tz_str, 0, 3);
		$ta=explode(":",$time);
		$hour=(int)$ta[0]-(int)$tz;
		$minute=$ta[1];
		$second=$ta[2];
	
		//make UNIX timestamp
		$time2 = mktime($hour, $minute, $second, $month, $day, $year);
		return $time2;
	}
	
	
	function mergeEntries(&$channel, &$items, &$entries, $max){
	/*
		Given array of items (as returned from magpie), merges into $entries,
		with key as timestamp.
	*/
	
		$channel_time = 0;
		if ($channel['dc']['date']) $channel_time = parse_w3cdtf($channel['dc']['date']);
		$num_items = 0;

		while(list($inum, $item)=each($items)){
			$item['channel_id'] = $channel['channel_id'];
			if ($item['dc']['date']) $timestamp = parse_w3cdtf($item['dc']['date']);
			else if ($item['pubdate']) $timestamp = $this->parse_rfc822time($item['pubdate'])+((float)$this->timezone * (float)3600);
			else if ($item['created']) $timestamp = parse_w3cdtf($item['created']);
			else if ($item['modified']) $timestamp = parse_w3cdtf($item['modified']);
			else $timestamp = $channel_time+$inum;
			if ($timestamp<10000) $timestamp = $num_items;
			if (isset($entries[$timestamp])) $timestamp+=$inum;
			$item['timestamp'] = $timestamp;
			$item['date'] = date("h:i:s A", $timestamp);
			$entries[$timestamp] = $item;
			$num_items++;
			if ($num_items >= $max) break;
		}
	}
	
	
	function aggregateFeeds($feeds, &$channels, &$entries){
	/*
		Given array of feeds (URLs), returns:
			$channels - array of channels as returned from Magpie
			$entries  - array of entries with key as timestamp
	*/
		if (!is_array($feeds) || !count($feeds)){
			$channels = false;
			$entries = false;
			return 0;
		}
		
		$channels = array();
		$entries = array();		
		
		$max_per_chan = (count($feeds)>1?$this->max_items_channel:$this->max_items_total);
		$num_channels = 0;
		
		while(list($n,$feed)=each($feeds)){
			if (empty($feed)) continue;
			
			// fetch RSS data
			$rss = fetch_rss($feed);
			if (!$rss){
				$error.= '<p>'.$feed.':<br>'.magpie_error()."\n";
				continue;
			}
			
			// add channel to array of channels
			$channels[$num_channels] = $rss->channel;
			$rss->channel['channel_id'] = $num_channels;
			
			// merge items in channel into entries array
			if (!is_array($rss->items)) continue;			
			$this->mergeEntries($rss->channel, $rss->items, $entries, $max_per_chan);

			$num_channels++;
		}
		
		return $num_channels;
	}
	
	
	function encodeBrackets($str){
		return str_replace('<', '&lt;', str_replace('>', '&gt;', str_replace('&','&amp;',$str)));
	}
	
	function createAtom(&$channels, &$entries, $group, &$my_prefs){
	
		if (!is_array($channels) || !count($channels)) return false;
		if (!is_array($entries) || !count($entries)) return false;

		$data = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$data.= '<feed version="0.3" xml:lang="en-US" xmlns="http://purl.org/atom/ns#">'."\n";
		$data.= '<title>'.$my_prefs['user_name'].'\'s feeds '.($group?' - '.$group:'').'</title>';
		$data.= '<link href="http://example.org/"/>'."\n";
		$data.= '<author><name>'.$my_prefs['user_name'].'</name></author>'."\n";

		reset($entries);
		$num_items = 0;
		while(list($timestamp,$entry)=each($entries)){
			$cid = $entry["channel_id"];
			$title = $this->encodeBrackets($entry['title']);
			$id = $cid.'.'.$timestamp;
			$link = $this->encodeBrackets($entry['link']);
			$author_name = $this->encodeBrackets($channels[$cid]['title']);
			$author_link = $this->encodeBrackets($channels[$cid]['link']);
			$modified = $timestamp - (int)date('Z');	//shift to Zulu
			$moddate = date('Y-m-d', $modified).'T'.date('H:i:s', $modified).'Z'; 
			if ($entry['description']) $content = $entry['description'];
			else if ($entry['atom_content']) $content = $entry['atom_content'];
			$content = LangEncode8bitLatin($content);
			$content = $this->encodeBrackets($content);
			
			$data.= '<entry>'."\n";
			$data.= '<title>'.$title.'</title>'."\n";
			$data.= '<link href="'.$link.'"/>'."\n";
			$data.= '<id>'.$id.'</id>'."\n";
			$data.= '<timestamp>'.$timestamp.'</timestamp>'."\n";
			$data.= '<modified>'.$moddate.'</modified>'."\n";
			$data.= '<content type="html">'.$content.'</content>'."\n";
			$data.= '<author>';
			$data.= '<name>'.$author_name.'</name>';
			$data.= '<link href="'.$author_link.'"/>';
			$data.= '</author>'."\n";
			$data.= '</entry>'."\n";

			$num_items++;
			if ($num_items >= $this->max_items_total) break;
		}
		
		$data.= '</feed>'."\n";
		
		return $data;
	}
};

?>