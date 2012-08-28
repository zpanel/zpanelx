<?php
/*
	File:     bookmark_actions.inc
	Author:   Ryo Chijiiwa, ilohamail.org
	License:  GPL, part of IlohaMail
	Purpose:  Code for handling actions for bookmarks feature
*/

include_once('../include/data_manager.inc');

class BookmarksManager_obj extends DataManager_obj{
	var $bmerr;
	var $bm_url;
	var $news_url;
	var $xml_text;
	
	function BookmarksManager_obj($bm_url, $news_url, $xml_text){
		$this->bm_url = $bm_url;
		$this->news_url = $news_url;
		$this->xml_text = $xml_text;
	}
	
	function addBookmark(){
		global $bmError;
		
		$new_name = $_REQUEST['new_name'];
		$new_url = $_REQUEST['new_url'];
		$new_rss = $_REQUEST['new_rss'];
		$new_grp = $_REQUEST['new_grp'];
		$new_grp_other = $_REQUEST['new_grp_other'];
		$new_comments = $_REQUEST['new_comments'];
		$new_private = $_REQUEST['new_private'];
		
		if ((empty($new_name)) || (empty($new_url))){
			$this->bmerr = $bmError[1];
			return false;
		}
		
		if (!ereg("[fht]+tp[s]*://", $new_url)) $new_url = "http://".$new_url;
		
		$new_entry["name"] = $new_name;
		$new_entry["url"] = $new_url;
		$new_entry["rss"] = $new_rss;
		$new_entry["grp"] = (empty($new_grp)?$new_grp_other:$new_grp);
		$new_entry["comments"] = $new_comments;
		$new_entry["is_private"] = $new_private;
		
		$result = $this->insert($new_entry);
		if (!$result) $this->bmerr = $this->error;
		return $result;
	}
	
	function editBookmark($edit_id){
		$edit_name = $_REQUEST['edit_name'];
		$edit_url = $_REQUEST['edit_url'];
		$edit_rss = $_REQUEST['edit_rss'];
		$edit_grp = (empty($_REQUEST['edit_grp'])?$_REQUEST['edit_grp_other']:$_REQUEST['edit_grp']);		
		$edit_comments = $_REQUEST['edit_comments'];
		$edit_private = ($_REQUEST['edit_private']?$_REQUEST['edit_private']:'');

		if (!ereg("[fht]+tp[s]*://", $edit_url)) $edit_url = "http://".$edit_url;
		
		$entry['name'] = $edit_name;
		$entry['url'] = $edit_url;
		$entry['rss'] = $edit_rss;
		$entry['grp'] = $edit_grp;
		$entry['comments'] = $edit_comments;
		$entry['is_private'] = $edit_private;
		
		
		$result = $this->update($edit_id, $entry);
		if (!$result) $this->bmerr = $this->error;
		return $result;
	}
	
	function getGroups(){
		return $this->getDistinct("grp", "ASC");
	}
	
	function getBookmarks(){
		$urls_a = $this->sort("grp,name", "ASC");
		if (!$urls_a) return false;
		
		$out = array();
		foreach($urls_a as $a){
			$grp = $a['grp'];
			$out[$grp][] = $a;
		}
		
		return $out;
	}
	
	
	function showBookmarkRow(&$v){
		$out= '<tr class="lt">';
			$out.= '<td valign="middle"><a href="'.$this->bm_url.'&edit_id='.$v['id'].'#frm">'.$v['name'].'</a></td>';
			$out.= '<td valign="middle">';
			if ($v['rss']){
				$out.= '<a href="'.$this->news_url.'&show_group='.urlencode($v['grp']).'&show_feed='.$v['id'].'">';
				$out.= '&nbsp;'.$this->xml_text;
				$out.= '</a>';
			}
			$out.= '<a href="'.$v['url'].'" target=_blank>'.$v['url'].'</a>';
			$out.= '</td>';
			$out.= '<td valign="middle">'.$v['comments'].'</td>';
		$out.= "</tr>\n";
		return $out;
	}
	
	function showBookmarkGroup($group, &$bookmarks){
		foreach($bookmarks as $v)
			if ($v['rss']){ 
				$has_rss = true;
				break;
			}
		
		$html = '';
		
		$html.= '<tr class="dk">';
		$html.= '<td colspan=3 valign=bottom>';
		$html.= '<a href="'.$this->news_url.'&show_group='.urlencode($group).'" class="tblheader">'.$group;
		if ($has_rss) $html.= '&nbsp;'.$this->xml_text;
		$html.= '</a>';
		$html.= '</td>';
		$html.= '</tr>';
		
		foreach($bookmarks as $bookmark){
			$html.= $this->showBookmarkRow($bookmark);
		}
		
		return $html;
	}
	
	function showBookmarks(&$bookmarks){
		$out = '';
		foreach($bookmarks as $group=>$a){
			$out.=$this->showBookmarkGroup($group, $bookmarks[$group]);
		}
		return $out;
	}
	
	function createGroupBlogroll($group, &$bookmarks){
		$group = str_replace('"','\"',$group);
		$out='';
		$out.='document.write("<p><span style=\""+IMB_CAT_STYLE+"\" class=\""+IMB_CAT_CLASS+"\">'.str_replace('"','\"',$group).'</span>\n");'."\n";
		$out.='document.write("<table>\n");'."\n";
		foreach($bookmarks as $v){
			$label = str_replace('"','\"',$v['name']);
			$out.='document.write("<tr><td><a href=\"'.$v['url'].'\" class=\""+IMB_LINK_CLASS+"\" style=\""+IMB_LINK_STYLE+"\">'.$label.'</a></td></tr>\n");'."\n";
		}
		$out.='document.write("</table>\n");'."\n";
		return $out;
	}
	
	function createBlogroll(&$bookmarks){
		$out = '';
		$out.='var IMB_LINK_STYLE;'."\n";
		$out.='var IMB_CAT_STYLE;'."\n";
		$out.='var IMB_LINK_CLASS;'."\n";
		$out.='var IMB_CAT_CLASS;'."\n";
		
		foreach($bookmarks as $group=>$a){
			$out.=$this->createGroupBlogroll($group, $bookmarks[$group]);
		}
		return $out;
	}
}

?>