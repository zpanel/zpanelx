<?php

/*  work.php: Plugin file responsible for the backend processing
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


/*  WORK HOOKS FUNCTIONS
    For every work hook the plugin registers in config.php there must
    be a corresponding function in this file called <plugin name>_<hook name>
    See docs/work_hooks.txt for a list of work hooks and descriptions.
*/
function logger_first_time_login($tools) {
    log_event($tools, 'first_time_login');
}
function logger_just_logged_in($tools) {
    log_event($tools, 'just_logged_in');
}
function logger_not_found_start($tools) {
    log_event($tools, 'not_found_start');
}
function logger_search_page_start($tools) {
    log_event($tools, 'search_page_start');
}
function logger_thread_view_start($tools) {
    log_event($tools, 'thread_view_start');
}
function logger_about_page_start($tools) {
    log_event($tools, 'about_page_start');
}
function logger_folders_page_start($tools) {
    log_event($tools, 'folders_page_start');
}
function logger_logged_out($tools) {
    log_event($tools, 'logged_out');
}
function logger_mailbox_page_start($tools) {
    log_event($tools, 'mailbox_page_start');
}
function logger_message_page_start($tools) {
    log_event($tools, 'message_page_start');
}
function logger_compose_page_start($tools) {
    log_event($tools, 'compose_page_start');
}
function logger_contacts_page_start($tools) {
    log_event($tools, 'contacts_page_start');
}
function logger_profile_page_start($tools) {
    log_event($tools, 'profile_page_start');
}
function logger_new_page_start($tools) {
    log_event($tools, 'new_page_start');
}
function log_event($tools, $event) {
    require($tools->include_path.'settings.php');
    if ($enable_log) {
        $log_event = false;
        if ($log_all_pages) {
            $log_event = true;
        }
        else {
            switch ($event) {
                case 'first_time_login':
                    if ($log_first_time_logins) {
                        $log_event = true;
                    }
                    break;
                case 'logged_out':
                    if ($log_logouts) {
                        $log_event = true;
                    }
                    break;
                case 'just_logged_in':
                    if ($log_logins) {
                        $log_event = true;
                    }
                    break;
                case 'not_found_start':
                    if ($log_page_not_found) {
                        $log_event = true;
                    }
                    break;
            }
        }
        if ($log_event) {
            write_event($tools, get_event_data($tools, $event, $enable_log, $log_logins, $log_logouts,
                $log_page_not_found, $log_first_time_logins, $log_all_pages, $log_usernames,
                $log_user_agent, $log_referer, $log_server_name, $log_remote_address, $log_remote_port,
                $log_server_port, $log_query_string, $log_php_self), $log_type, $log_delim, $log_table,
                $log_syslog_priority, $log_file);
        }
    }
}
function get_event_data($tools, $event, $enable_log, $log_logins, $log_logouts, $log_page_not_found,
        $log_first_time_logins, $log_all_pages, $log_usernames, $log_user_agent, $log_referer,
        $log_server_name, $log_remote_address, $log_remote_port, $log_server_port, $log_query_string,
        $log_php_self) {

    if ($log_usernames) {
        $username = $_SESSION['user_data']['username'];
    }
    else {
        $username = '';
    }
    if ($log_user_agent) {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }
        else {
            $agent = '';
        }
    }
    else {
        $agent = '';
    }
    if ($log_server_name) {
        if (isset($_SERVER['SERVER_NAME'])) {
            $server_name = $_SERVER['SERVER_NAME'];
        }
        else {
            $server_name = '';
        }
    }
    else {
        $server_name = '';
    }
    if ($log_remote_address) {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $remote_addr = $_SERVER['REMOTE_ADDR'];
        }
        else {
            $remote_addr = '';
        }
    }
    else {
        $remote_addr = '';
    }
    if ($log_remote_port) {
        if (isset($_SERVER['REMOTE_PORT'])) {
            $remote_port = $_SERVER['REMOTE_PORT'];
        }
        else {
            $remote_port = '';
        }
    }
    else {
        $remote_port = '';
    }
    if ($log_server_port) {
        if (isset($_SERVER['SERVER_PORT'])) {
            $port = $_SERVER['SERVER_PORT'];
        }
        else {
            $port = '';
        }
    }
    else {
        $port = '';
    }
    if ($log_query_string) {
        if (isset($_SERVER['QUERY_STRING'])) {
            $query = $_SERVER['QUERY_STRING'];
        }
        else {
            $query = '';
        }
    }
    else {
        $query = '';
    }
    if ($log_php_self) {
        if (isset($_SERVER['PHP_SELF'])) {
            $self  = $_SERVER['PHP_SELF'];
        }
        else {
            $self = '';
        }
    }
    else {
        $self = '';
    }
    if ($log_referer) {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        }
        else {
            $referer = '';
        }
    }
    else {
        $referer = '';
    }
    $date = date('Y-m-d H:i:s');
    return array(
        'username'   => $username,
        'server_name' => $server_name,
        'server_port' => $port,
        'remote_address' => $remote_addr,
        'remote_port' => $remote_port,
        'query_string' => $query,
        'php_self' => $self,
        'event'      => $event,
        'date'       => $date,
        'referer'    => $referer,
        'user_agent' => $agent,
    );
}
function write_event($tools, $flds, $log_type, $log_delim, $log_table,
    $log_syslog_priority, $log_file) {
    switch (strtolower($log_type)) {
        case 'file':
            $str = build_log_string($flds, $log_delim);
            if ($fh = @fopen($log_file, 'a')) {
                fwrite($fh, "$str\n");
                fclose($fh);
            }
            break;
        case 'syslog':
            $str = build_log_string($flds, $log_delim);
            @syslog($log_syslog_priority, $str);
            break;
        case 'db':
            if ($tools->get_db()) {
                $res = $tools->db_insert('insert into event_log
                (ts, username, server_name, server_port, remote_address,
                 remote_port, query_string, php_self, referer, user_agent)
                 values(
                '.$tools->db_quote($flds['date']).',
                '.$tools->db_quote($flds['username']).',
                '.$tools->db_quote($flds['server_name']).',
                '.$tools->db_quote($flds['server_port']).',
                '.$tools->db_quote($flds['remote_address']).',
                '.$tools->db_quote($flds['remote_port']).',
                '.$tools->db_quote($flds['query_string']).',
                '.$tools->db_quote($flds['php_self']).',
                '.$tools->db_quote($flds['referer']).',
                '.$tools->db_quote($flds['user_agent']).
                ')');
            }
            break;
        case 'php':
            $str = build_log_string($flds, $log_delim);
            @error_log($str);
            break;
    }
}
function build_log_string($flds, $delim) {
    $fld_names = array('date', 'username', 'server_name', 'server_port',
        'remote_address', 'remote_port', 'query_string', 'php_self', 'referer',
        'user_agent');
    $fld_str = '';
    foreach ($fld_names as $val) {
        if (trim($flds[$val]) != '') {
            $fld_str .= $flds[$val].$delim;
        }
    }
    return rtrim($fld_str, $delim);
}
?>
