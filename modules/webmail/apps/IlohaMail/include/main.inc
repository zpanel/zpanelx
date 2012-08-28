<?php
/////////////////////////////////////////////////////////
//	
//	include/main.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

function FormFieldHeader($p_sort_field, $p_string){

	global $user, $folder, $start, $sort_field, $sort_order, $textc;

	$p_user = $user;
	$p_folder = $folder;
	$p_start = $start;
	$p_cur_field = $sort_field;
	$p_cur_order = $sort_order;
	$p_color = $textc;
	
	$result="main.php?";
	$result.="user=$p_user";
	$result.="&folder=".urlencode($p_folder);
	$result.="&start=$p_start";
	$result.="&sort_field=$p_sort_field";
	if (strcasecmp($p_sort_field, $p_cur_field)==0){
		if ($p_cur_order=="ASC") $p_sort_order="DESC";
		else if ($p_cur_order=="DESC") $p_sort_order="ASC";
	}else{
		if (strcasecmp($p_sort_field, "date")==0) $p_sort_order="DESC";
		else $p_sort_order="ASC";
	}
	$result.="&sort_order=$p_sort_order";
	//$result ="<a href=\"".$result."\"><b><span class=\"tblheader\">$p_string</span></b></a>";
	$result ="<a href=\"".$result."\" class=\"tblheader\"><b>$p_string</b></a>";
	
	return $result;
}

function ShowFieldControls($field, $base_url, $num, $total){
	$total--;
	
	$result = "<td align=\"center\">";
	if ($num!=0){
		$result.= "<a href=\"".$base_url."&move_col=$field&move_direction=left\"><span class=\"tblheader\">&lt;&lt;</span></a>";
		$result.= "&nbsp;&nbsp;";
	}
	if ($num!=$total){
		$result.= "<a href=\"".$base_url."&move_col=$field&move_direction=right\"><span class=\"tblheader\">&gt;&gt;</span></a>";
		$result.= "</td>\n";
	}
	
	return $result;
}

function main_ReadCache($cache_dir, $folder, $messages_str, $sort_field, &$read_cache){
	global $loginID, $host;
	$read_cache = false;
	
	$msgset = cache_read($loginID, $host, $folder.".".$sort_field.".MSGS");
	if (!$msgset) return false;
	else if ($msgset == $messages_str){
		$data = cache_read($loginID, $host, $folder.".".$sort_field);
		if ($data){
			$read_cache = true;
			return $data;
		}
	}
	
	return false;
	/*
	//does cache file eixst?
	$cache_path = $cache_dir."/".ereg_replace("[\\/]", "", $folder.".".$sort_field);
	if (file_exists(realpath($cache_path))){
		//if yes, open
		$fd = fopen ($cache_path, "r");
		if ($fd){
			//read messages_str
			$cached_messages = chop(iil_ReadLine($fd, 1024));
			
			//same messages_str?
			if (strcmp($cached_messages, $messages_str)==0){
				//if yes, read cached data
				while(!feof($fd)){
					$data .= fread($fd, 1024);
				}
				$read_cache = true;
			}
			
			//close file
			fclose($fd);
			
			//return array
			return unserialize($data);
		}
	}
	return false;
	*/
}

function main_WriteCache($cache_dir, $folder, $sort_field, $index_array, $messages_str){
	global $loginID, $host;
	$read_cache = false;
	$key = $folder.".".$sort_field;

	if (cache_write($loginID, $host, $key.".MSGS", $messages_str)){
		return cache_write($loginID, $host, $key, $index_array);
	}
	
	return false;

	/*
	//if cache dir doesn't exist, try and make it
	if (!file_exists(realpath($cache_dir))){
		mkdir($cache_dir, 0700);
	}

	//is cache dir there?
	if (file_exists(realpath($cache_dir))){
		//if yes, try and open a cache file
		$cache_path = $cache_dir."/".ereg_replace("[\\/]", "", $folder.".".$sort_field);
		$fd = fopen ($cache_path, "w");
		
		//if opened...
		if ($fd){
			//write messages_str in first line
			fputs($fd, $messages_str."\n");
			//then serialized array
			fputs($fd, serialize($index_array));
			fclose($fd);
		}
	}
	*/
}

?>