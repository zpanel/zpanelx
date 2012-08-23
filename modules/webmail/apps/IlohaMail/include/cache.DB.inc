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

$EXISTING_CACHES = array();
$CACHE_ERROR = "";

function cache_read($user, $host, $key){
	global $DB_CACHE_TABLE;
	global $EXISTING_CACHES;
	global $session_dataID;
	
	$db = new idba_obj;
	if (!$db->connect()) return false;
	
	$data = false;
	$sql = "SELECT * FROM $DB_CACHE_TABLE WHERE owner='$session_dataID' and cache_key='$key'";
	$result = $db->query($sql);
	if (($result) && ($db->num_rows($result)>0)){
		$a = $db->fetch_row($result);
		$data = unserialize($a["cache_data"]);
		$EXISTING_CACHES[$key] = $a["id"];
	}else{
		$result = false;
		$EXISTING_CACHES[$key] = false;
	}
		
	return $data;
}

function cache_write($user, $host, $key, $data, $volatile=true){
	global $DB_CACHE_TABLE;
	global $session_dataID;
	global $CACHE_ERROR;
		
	$db = new idba_obj;
	if (!$db->connect()) return false;
	
	if (!$EXISTING_CACHES[$key]){
		$sql = "SELECT id FROM $DB_CACHE_TABLE WHERE owner='$session_dataID' and cache_key='$key'";
		$result = $db->query($sql);
		if (($result) && ($db->num_rows($result)>0)){
			$a = $db->fetch_row($result);
			$EXISTING_CACHES[$key] = $a["id"];
		}else{
			$EXISTING_CACHES[$key] = false;
		}
	}
	
	$data = serialize($data);
	if ($EXISTING_CACHES[$key]){
		$id = $EXISTING_CACHES[$key];
		$ownerID = $session_dataID;
		$sql = "UPDATE $DB_CACHE_TABLE SET cache_data='$data',volatile='$volatile' WHERE id='$id' AND owner='$session_dataID'";
		$result = $db->query($sql);	
	}else{
		$ownerID = $session_dataID;
		$sql = "INSERT INTO $DB_CACHE_TABLE (owner, cache_key, cache_data, cache_ts, volatile) ";
		$sql.= "VALUES ('$session_dataID', '$key', '$data', '".time()."', '$volatile')";
		$result = $db->query($sql);	
	}
	
	if (!$result) $CACHE_ERROR = $db->error();
		
	return $result;
}

function cache_clear($user, $host, $key){
	global $DB_CACHE_TABLE;
	global $session_dataID;
	global $CACHE_ERROR;
		
	$db = new idba_obj;
	if (!$db->connect()) return false;
	
	$sql = "UPDATE $DB_CACHE_TABLE SET cache_data='' WHERE owner='$session_dataID' and cache_key=$key'";
	$result =  $db->query($sql);
	if (!$result) $CACHE_ERROR = $db->error();
	return $result;
}

function cache_clear_all($user, $host){
	global $session_dataID;
	global $DB_CACHE_TABLE;
	
	$db = new idba_obj;
	if (!$db->connect()) return false;

	$expire = time() - (60 * 60 * 24 * 30);  //timestamp 30 days ago
	$sql = "DELETE FROM $DB_CACHE_TABLE ";
	$sql.= " WHERE (owner='$session_dataID' and volatile='1')";
	$sql.= " OR (cache_ts < '$expire' and volatile='1')";
	return $db->query($sql);
}

?>