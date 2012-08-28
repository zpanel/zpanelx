<?php
/********************************************************
	include/log.DB.inc
	
	(C)Copyright 2001-2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Add log entry into DB log table.
	PRE-CONDITIONS:
		$log_time : date & time
		$log_timestamp : unix timestamp
		$log_ip : client IP address
		$log_account : User's account (user name @ host)
		$log_comment : error message or other comment

********************************************************/

include_once("../conf/db_conf.php");
include_once("../include/idba.$DB_TYPE.inc");
include_once("../include/array2sql.inc");

$db = new idba_obj;
if ($db->connect()){
	$backend_array = array(
		"logTime" => date("Y-m-d H:i:s", time()),
		"logTimeStamp" => $log_timestamp,
		"userID" => $dataID,
		"account" => $log_account,
		"action" => $log_action,
		"comment" => $log_comment,
		"ip" => $log_ip
	);
	$sql = Array2SQL($DB_LOG_TABLE, $backend_array, "INSERT");
	$db->query($sql);
}

?>