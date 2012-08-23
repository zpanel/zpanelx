<?php
/////////////////////////////////////////////////////////
//	
//	include/ryosimap.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Legacy library from the "Ryo's Webmail" days.  Contains misc. functions.
	COMMENTS:
		Most of the functions here should eventually be phased out and/or moved to other 
		include's.

********************************************************/

function ShowSeen($obj,$true,$false){
	if (($obj->Unseen=='U')||($obj->Recent=='N')){
		return $false;
	}else{
		return $true;
	}
}

function ShowBytes($numbytes){
	if ($numbytes > 1024){
		$kb=(int)($numbytes/1024);
		return $kb." KB";
	}else{
		return $numbytes." B";
	}
}

function TZDate($tz){
	$server_tz = (int)date("Z");	//server timezone offset in seconds
	$gmt = time() - $server_tz;
	$user_tz = $tz * 60 * 60; 		//user tz offset in seconds
	$ts = $gmt + $user_tz;
	
	if ($tz < 0){
		if ($tz > -10){
			$ttz=$tz * -1;
			$tz_string = "-0".$ttz."00";
		}else{
			$tz_string = $tz."00";
		}
	}else if ($tz >= 0){
		if ($tz < 10) $tz_string = "+0".$tz."00";
		else $tz_string = "+".$tz."00";
	}
	return date("D, d M Y H:i:s", $ts)." $tz_string";
}

function ShowDate($obj){
	return $obj->date;
}

function ShowDate2($str,$cd_a,$mode){
	$pos=strpos($str, ",");
	if ($pos > 0) $str = substr($str, $pos+1);
	$str=trim($str);
	
	$a=explode(" ",$str);
	$month_a=array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
	$month_str=$a[1];
	$month=$month_a[$month_str];
	$day=(int)$a[0];
	$year=(int)$a[2];
	$time=$a[3];
	
	if ($mode=="short"){
		$str = $month."/".$day."/".$year;
	}else if ($mode!="full"){
		if (($cd_a["day"]==$day) && ($cd_a["month"]==$month)){
			$str="";
			$is_today=true;
		}else{
			$str=$month."/".$day;
			$is_today=false;
		}
		if ($cd_a["year"]!=$year) $str.="/".$a[2];
		if ($is_today){
			$ta=explode(":",$time);
			$str.=" ".$ta[0].":".$ta[1]." ".$a[4];
		}
	}

	return $str;
}

function ShowShortDate($timestamp, $format){
	global $my_prefs;
	
	//get timestamp in user's timezone
	$now = time(); //local time
	$now = $now - date("Z"); //GMT time
	$now = $now + ($my_prefs["timezone"] * 3600); //user's time

	$day_secs = 60*((int)date("H", $now)*60 + (int)date("i", $now));
	$week_secs = 60 * 60 * 24 * 7;
	$diff = $now - $timestamp;
	if ($diff < $day_secs){
		$str = $format["today"];
		if (empty($str)) $str="%t";
		$time = LangFormatIntTime(date("Hi", $timestamp), $my_prefs["clock_system"], $format["ampm"], $format["time_format"]);
		$str = str_replace("%t", $time, $str);
		//$str = str_replace("%t", date("H:i", $timestamp), $str);
	}else if ($diff < $week_secs){
		$dsow = $format["dsow"];
		$dow_code = date("w", $timestamp);
		$dow = $dsow[$dow_code];
		//$time = date("H:i", $timestamp);
		$time = LangFormatIntTime(date("Hi", $timestamp), $my_prefs["clock_system"], $format["ampm"], $format["time_format"]);
		$str = $format["lastweek"];
		$str = str_replace("%t", $time, $str);
		$str = str_replace("%w", $dow, $str);
	}else{
		$c_a = getdate();
		$d_a = getdate($timestamp);
		$message_year = $d_a["year"];
		$current_year = $c_a["year"];
		
		if ($message_year != $current_year) $str = $format["prevyears"];
		else $str = $format["thisyear"];
		
		$str = str_replace("%m", $d_a["mon"], $str);
		$str = str_replace("%d", $d_a["mday"], $str);
		$str = str_replace("%y", $d_a["year"], $str);
	}
	return $str;
}

function RootedFolderOptions($folders, $defaults, $root){
	if (!empty($root)) $root_len = strlen($root) + 1;
	
	if ($folders == false) {
    	echo "Call failed<br>\n";
		return array();
	} else {
		$fa=$defaults;
		reset($fa);
		while (list ($key,$val) = each ($fa)) {
			if (!empty($key)) echo "<option value=\"".$key."\">".iil_utf7_decode($val)."\n";
		}

		natcasesort($folders);
	   	while ( list($k,$folder)=each($folders) ){
			//$folder=$folders[$i];
			if (($fa[$folder]=="") && ($folder[0]!='.') && (!empty($folder))){
				$folder_name=$folder;
				if ($root_len > 0){
					$pos = strpos($folder, $root);
					if (($pos!==false) && ($pos==0)){
						$folder_name = substr($folder, $root_len);
					}
				}
				//$folder=$folder;
				//$fa[$folder]=$folder_name;
				if ($folder_name[0]!=".") echo "<option value=\"".$folder."\">".iil_utf7_decode($folder_name)."\n";
			}			
    	}
	}
	
	//reset($fa);
	//while (list ($key,$val) = each ($fa)) {
	//	if (!empty($key)) echo "<option value=\"".$key."\">$val\n";
	//}
}

function FolderOptions3($folders, $defaults){
	if ($folders == false) {
    	echo "Call failed<br>\n";
		return array();
	} else {
		natcasesort($folders);
		$fa=$defaults;
		while ( list($k,$folder) = each($folders) ){
			$folder=$folders[$i];
			if (($fa[$folder]=="") && ($folder[0]!='.') && (!empty($folder))){
				$folder_name=$folder;
				$folder=$folder;
				$fa[$folder]=$folder_name;
			}			
    	}
	}
	
	reset($fa);
	while (list ($key,$val) = each ($fa)) {
		if (!empty($key)) echo "<option value=\"".$key."\">".iil_utf7_decode($val)."\n";
	}
}

function FolderOptions2($folderlist, $default){
    if (is_array($folderlist)){
		natcasesort($folderlist);
        while(list($key,$item)=each($folderlist)){
            if ($item[0]!="."){
                echo "<option value=\"$item\" ".(strcmp($item, $default)==0?"SELECTED":"").">";
				echo iil_utf7_decode($item)."\n";
			}
        }
    }
}

function DefaultOptions($folderlist, $default){
    if (is_array($folderlist)){
		natcasesort($folderlist);
        while(list($key,$item)=each($folderlist)){
            if ($item[0]!=".")
                echo "<option value=\"$key\" ".(strcmp($key, $default)==0?"SELECTED":"").">".iil_utf7_decode($item)."\n";
        }
    }
}

function DefaultOptions2($folders, $defaults, $default){
	if ($folders == false) {
    	echo "Call failed<br>\n";
		return array();
	} else {
		natcasesort($folders);
		$fa=$defaults;
		while ( list($k,$folder) = each($folders) ){
			$folder=$folders[$i];
			if (($fa[$folder]=="") && ($folder[0]!='.')){
				$folder_name=$folder;
				$folder=$folder;
				$fa[$folder]=$folder_name;
			}			
    	}
	}
	
	reset($fa);
	while (list ($key,$val) = each ($fa)) {
		echo "<option value=\"".$key."\" ".(strcmp($key, $default)==0?"SELECTED":"").">".iil_utf7_decode($val)."\n";
	}
}


function myWordWrap($string,$num){
	$len=strlen($string);
	$curpos=0;
	$str="";
	
	while (($curpos+$num)<$len){
		$str.=substr($string,$curpos,$num);
		$str.="\n";
		$curpos=$curpos+$num;
	}
	$str.=substr($string,$curpos,$len);
	
	return $str;
}

function encodeUTFSafeHTML($str){
	$result = $str;
	$result = str_replace("\"", "&quot;", $result);
	$result = str_replace("<", "&lt;", $result);
	$result = str_replace(">", "&gt;", $result);
	
	return $result;
}

function encodeHTML($str){
	$result = $str;
	$result = str_replace("&", "&amp;", $result);
	$result = str_replace("<", "&lt;", $result);
	$result = str_replace(">", "&gt;", $result);
	
	return $result;
}

function detectURLinWord($word){
	if (ereg("[.]*([fht]+tp[s]*://[a-zA-Z0-9_~#=&%/:;@,\.\?\+-]+)[.]*", $word, $result)){
		return "<a href=\"".$result[0]."\" target=_blank>".htmlspecialchars($word)."</a>";
	}else{
		return encodeHTML($word);
	}
}

function sanitizeHTML(&$html){
/*
	Strip tags and scriptable attributes from HTML
	that might be used in a XSS attack.
	Return true if modified, false otherwise.
*/
	$orig_len = strlen($html);
	
	$tags = array("script"=>'t', 'javascript'=>'t',
			"object"=>'t', "iframe"=>'t', "applet"=>'t', "meta"=>'t', "form"=>'t',
			"onMouseOver"=>'a', 'onMove'=>'a', 'onMouseOut'=>'a', 'onFocus'=>'a', 'onUnload'=>'a',
			'onLoad'=>'a', 'onClick'=>'a', 'onSelect'=>'a', 'onBlur'=>'a', 'onClick'=>'a',
			'onError'=>'a', 'style'=>'a');
			
	$patterns = array();
	$replace = array();
	foreach($tags as $tag=>$type){
		if ($type=='a'){
			$patterns[] =  '/(<[^>]*)[\s]+('.$tag.'[=\s]+)([^>]*>)/i';
			$replace[] = '\1 no_\2\3';
		}else if ($type=='t'){
			$patterns[] = '/(<[\s\/]*)('.$tag.')([^>]*>)/i';
			$replace[] = '\1no_\2\3';
		}
	}

	$html = preg_replace($patterns, $replace, $html);
	
	return (strlen($html)!=$orig_len);
}

?>