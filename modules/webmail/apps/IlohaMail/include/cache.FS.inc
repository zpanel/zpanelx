<?php
/////////////////////////////////////////////////////////
//	
//	include/cache.FS.inc
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail, and released under GPL.
//	See COPYING, or http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////
/********************************************************
	PURPOSE: Unified interface to read/write cache

********************************************************/

function cache_read($user, $host, $key){
	global $CACHE_DIR;
	
	//check if file is there
	$user_dir = ereg_replace("[\\/]", "", $user.".".$host);
	$path = $CACHE_DIR.$user_dir."/".$key;
	if (!@file_exists(realpath($path))) return false;
	
	//open file
	$fp = fopen($path, "r");
	if (!$fp) return false;
	
	//read data
	$data = false;
	$data = fread($fp, filesize($path));
	if ($data) $data = unserialize($data);
	
	fclose($fp);
	
	return $data;
}

function cache_write($user, $host, $key, $data){
	global $CACHE_DIR;
	
	//open file for writing
	$user_dir = ereg_replace("[\\/]", "", $user.".".$host);
	$path = $CACHE_DIR.$user_dir."/".$key;
	$fp = @fopen($path, "w");
	if (!$fp) return false;
	
	//write data
	fputs($fp, serialize($data));
	
	fclose($fp);
	
	return true;
}

function cache_clear($user, $host, $key){
	global $CACHE_DIR;
	
	//check if file is there
	$user_dir = ereg_replace("[\\/]", "", $user.".".$host);
	$path = $CACHE_DIR.$user_dir."/".$key;
	if (!@file_exists(realpath($path))) return false;
	else return unlink($path);
}

function cache_clear_all($user, $host){
	global $CACHE_DIR;
	
	//delete cache files
	$cacheDir = $CACHE_DIR.ereg_replace("[\\/]", "", $user.".".$host);
	if (@is_dir(realpath($cacheDir))){
		if ($handle = opendir($cacheDir)) {
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 
					$file_path = $cacheDir."/".$file;
					unlink($file_path);
				} 
			}
			closedir($handle); 
		}
	}
}

?>
