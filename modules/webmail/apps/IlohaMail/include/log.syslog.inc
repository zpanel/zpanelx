<?php
/********************************************************
        include/log.syslog.inc
        
        Copyright (C)  2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
        Copyright (C)  2002  Ziberex andf Torben Rybner

        This file is part of IlohaMail, and released under GPL.
        See COPYING, or http://www.fsf.org/copyleft/gpl.html
        
        PURPOSE:
                Add log entry into system log.
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
if (! empty($log_comment))
  $log_entry = str_replace("comment", "($log_comment)", $log_entry);
else
  $log_entry = str_replace("comment", "", $log_entry);

$log_priority = 6; // 7 = debug, 6 = info, 5 = notice, 4 = warning, 3 = err, 2 = crit, 1 = alert, 0 = emerg

syslog($log_priority, $log_entry);

?>
