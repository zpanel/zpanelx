<?php
/********************************************************
	include/log.FS.inc
	
	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Add log entry into log file.
	PRE-CONDITIONS:
		$log_time : date & time
		$log_timestamp : unix timestamp
		$log_ip : client IP address
		$log_account : User's account (user name @ host)
		$log_comment : error message or other comment

********************************************************/

$log_entry = $log_template;
$log_entry = str_replace("date", $log_time, $log_entry);
$log_entry = str_replace("acct", $log_account, $log_entry);
$log_entry = str_replace("ip", $log_ip, $log_entry);
$log_entry = str_replace("action", $log_action, $log_entry);
$log_entry = str_replace("comment", $log_comment, $log_entry);

$fp = fopen($log_file, "a");
if ($fp){
	fputs($fp, $log_entry."\n");
	fclose($fp);
}

?>