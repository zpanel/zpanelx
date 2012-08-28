<?php
/*
	       File: write_sinc.common.inc
	     Author: Ryo Chijiiwa, ilohamail.org
	    License: GPL, part of IlohaMail
	Description: Common functions for write_sinc.*.inc
*/

include_once('../include/fs_path.inc');

function wsinc_common($user_name, $host, &$error){
	//prep uploads dir
	$uploadDir = fs_get_path("upload", $user_name, $host);
	
	//prep cache dir
	$cacheDir = fs_get_path("cache", $user_name, $host);
	
	if (!file_exists(realpath($uploadDir))) $error .= "Invalid uploads directory<br>\n";
}

function wsinc_getJSMode(){
	//determine JavaScript usage mode based on user agent, currently 'h' or 'l'
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$js_enabled = $_REQUEST['js_enabled'];
	
	if (!$js_enabled) return 'l';
	
	//look for MSIE, Gecko, or AppleWebKit.  MIA: Opera,Konqueror
	$pattern = "/(MSIE|Gecko|AppleWebKit)([\s\/])([0-9\.]*)/";
	preg_match($pattern, $ua, $matches);
	
	$engine = $matches[1];
	$version = (float)$matches[3];
	
	if ($engine=='MSIE' && $version>=5.5) return 'h';
	else if ($engine=='Gecko' && $version>=20040101.0) return 'h';
	else if ($engine=='AppleWebKit' && $version>=124.0) return 'h';
	return 'l';
}

?>