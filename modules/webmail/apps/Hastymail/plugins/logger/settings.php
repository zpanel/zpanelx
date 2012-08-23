<?php

/*  settings.php: Settings file for the logger plugin
    Copyright (C) 2002-2010  Hastymail Development group

    This file is part of Hastymail.

    Hastymail is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Hastymail is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hastymail; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

* $Id:$
*/

/* Enable or disable all logging
--------------------------------*/
$enable_log = false;

/* Log event options
--------------------*/

$log_logins            = true;
$log_logouts           = true;
$log_page_not_found    = true;
$log_first_time_logins = true;
$log_all_pages         = true;

/* log detail options
---------------------*/

$log_usernames      = true;
$log_user_agent     = true;
$log_referer        = true;
$log_server_name    = true;
$log_remote_address = true;
$log_remote_port    = true;
$log_server_port    = true;
$log_query_string   = true;
$log_php_self       = true;

/* log type options
--------------------------------------------------
available options are:
    file        log to a specific file
    syslog      log to syslog
    php         log to the php/webserver log
    db          log to the configured hastymail db
*/

$log_type  = 'file';

/* required delimiter for the file, syslog and php log types
---------------------------------------------------*/

$log_delim = "\t";

/* location for the file log type
---------------------------------*/

$log_file  = '/var/hastymail2/access_log';

/* syslog log level options
---------------------------
    LOG_EMERG
    LOG_ALERT
    LOG_CRIT
    LOG_ERR
    LOG_WARNING
    LOG_NOTICE
    LOG_INFO 
    LOG_DEBUG

these are constants and should NOT be quoted
*/

$log_syslog_priority = LOG_INFO;

/* databse table for the db log type
------------------------------------*/

$log_table = 'hastymail_log';
?>
