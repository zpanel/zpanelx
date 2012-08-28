<?php
/*
	File:		fs_path.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	utilities for accessing FS backend dirs
	Pre:		$FS_LARGE_SCALE
				$FS_MIGRATE
*/

function fs_exists($path){
	$path = realpath($path);
	return file_exists($path);
}

function fs_mkdir($path){
	//if folder already exists, don't create
	if (fs_exists($path)) return true;
	
	//otherwise, check each level, and create as we go
	$offset = 0;
	while(($offset=strpos($path, "/", $offset+1))!==false){
		//for leading '/' do nothing
		if ($offset===0) continue;
				
		//if folder doesn't exists and we fail to create, return false
		$partial_path = substr($path, 0, $offset);
		$exists = fs_exists($partial_path);
		//echo 'fs_m: '.$partial_path.','.realpath($partial_path).'('.$exists.")\n";
		if (!fs_exists($partial_path) && !mkdir($partial_path, 0700)) return false;
	}
	
	return true;
}

function fs_get_path($dir, $user, $host, $create=true){
	global $FS_MIGRATE, $FS_LARGE_SCALE;
	global $CACHE_DIR, $USER_DIR, $SESSION_DIR, $UPLOAD_DIR;
	
	//figure out which directory we want
	$dir = strtolower($dir);
	$dir_code = $dir[1]; //uSer, sEssion, uPload, cAche
	
	switch($dir_code){
		case 'a': $root=$CACHE_DIR;break;
		case 's': $root=$USER_DIR;break;
		case 'e': $root=$SESSION_DIR;break;
		case 'p': $root=$UPLOAD_DIR;break;
	}
	
	if (!$root){
		echo "Invalid dir: $dir";
		return;
	}
	
	//make sure root has trailing '/'
	if ($root[strlen($root)-1]!='/') $root.='/';
	
	//user dir has format user.host
	if (strpos($user, "@")!==false) $user_dir = str_replace("@",".", $user);
	else $user_dir = $user.'.'.$host;
	$user_dir = strtolower(ereg_replace("[\\/]", "", $user_dir))."/";
	
	if (!$FS_LARGE_SCALE){
		// simple format: root/user_dir/ 
		$user_dir = $root.$user_dir;
		if ($create) return (fs_mkdir($user_dir)?$user_dir:false);
		else return $user_dir;
	}else{
		// form simple path, for migration purposes
		$simple_dir = $root.$user_dir;
		
		// large scale:  root/u/s/user_dir/
		if (ereg('^[a-z]{2,}',$user_dir))
			$root.= $user_dir[0].'/'.$user_dir[1].'/';
		else
			$root.= 'z/z/';
		
		//check for new root 
		if (!fs_mkdir($root)){
			echo "Failed to create: $root";
			return false;
		}
		
		//append user dir to root for final path
		$user_dir = $root.$user_dir;
		
		//for migration purposes, we'll try to look 
		if ($FS_MIGRATE && fs_exists($simple_dir) && !fs_exists($user_dir))
			rename($simple_dir, $user_dir);
		
		//make|check and return
		if ($create) return (fs_mkdir($user_dir)?$user_dir:false);
		else return $user_dir;
	}
}


?>