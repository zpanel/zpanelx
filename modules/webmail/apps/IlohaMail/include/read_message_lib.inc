<?php
/*
	File:		read_message_lib.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	Common functions for use in read_message.php
*/

include_once('../include/main.inc');

function rm_show_moveform($params){
	global $my_prefs, $folderlist, $defaults;

	if ($my_prefs['js_usage']!='h' && !main_fetch_folderlist($folderlist)) return false;	
	extract($params);
	
	$out = '';
	echo  '<form method="POST" action="'.$action_url.'">'."\n";
	echo '<input type="hidden" name="user" value="'.$user."\">\n";
	echo '<input type="hidden" name="folder" value="'.$folder."\">\n";
	if ($tid!==false){
		echo '<input type="hidden" name="tid" value="'.$tid."\">\n";
		echo '<input type="hidden" name="t_uids[]" value="'.$t_uid."\">\n";
	}
	echo '<input type="hidden" name="checkboxes[]" value="'.$id."\">\n";
	echo '<input type="hidden" name="uids[]" value="'.$uid."\">\n";
	echo '<input type="hidden" name="total_num" value="'.$num_msgs.'">'."\n";
	echo '<input type="hidden" name="start" value="'.$start."\">\n";
	echo '<input type="hidden" name="max_messages" value="'.($id+1)."\">\n";

	echo "\n".'<select name=moveto class=small>'."\n";
	if ($my_prefs['js_usage']=='h'){
		echo '<script>'."\n";
		echo 'D([5]);'."\n";
		echo 'parent.flush_buffer();'."\n";
		echo '</script>'."\n";
	}else{
		RootedFolderOptions($folderlist, $defaults, $my_prefs['rootdir']);
	}
	echo '</select>';
	echo '<input type=submit name="move_selected" value="'.$move_str.'" class=small>';
	if ($tid!==false)
		echo '<label><input type="checkbox" name="move_thread" value="1" class=small><span class=small>'.$move_thread_str.'</span></label>';
	echo '</form>'."\n";
}

function rm_get_foldername($folder){
	global $ICL_CAPABILITY;
	global $conn;
	global $defaults;
	global $rmStrings;
	
	if (!$ICL_CAPABILITY['folders']) return $rmStrings[0];

	$folder_name = $defaults[$folder];
	if (empty($folder_name)){
		$delim = iil_C_GetHierarchyDelimiter($conn);
		$parts = explode($delim, $folder);
		$folder_name = $parts[count($parts)-1];
	}
	return iil_utf7_decode($folder_name);
}

function rm_format_nav_url($id, $uid=false){
	global $user, $t_uids;
	
	if (is_array($t_uids) && $uid===false) $uid=$t_uids[$id];
	
	return 'read_message.php?user='.$user.'&trav=1&id='.$id.(is_numeric($uid)?'&uid='.$uid:'');
}

function rm_get_navtext($sort_field, $sort_order, &$rmStrings){
	$result = array('previous'=>$rmStrings['previous'],
					'back'=>$rmStrings['back'],
					'next'=>$rmStrings['next']);
	
	if (stristr($sort_field,'date')!==false){
		if (strcasecmp($sort_order,'ASC')==0){
			$result['previous'] = $rmStrings['older'];
			$result['next'] = $rmStrings['newer'];
		}else{
			$result['previous'] = $rmStrings['newer'];
			$result['next'] = $rmStrings['older'];
		}
	}
	return $result;
}

?>