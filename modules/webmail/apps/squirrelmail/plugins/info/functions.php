<?php

/* functions for info plugin
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Here are two functions for the info plugin
 * The first gets the CAPABILITY response from your IMAP server.
 * The second runs the passed IMAP test and returns the results 
 * The third prints the results of the IMAP command
 * to options.php.
 * by: Jason Munro jason@stdbev.com
 *
 * $Id: functions.php 14084 2011-01-06 02:44:03Z pdontthink $ 
 *
 */

function get_caps($imap_stream) {
    $sid = sqimap_session_id();
    $query = "$sid CAPABILITY\r\n";
    fputs ($imap_stream, $query);
    $responses = sqimap_read_data_list($imap_stream, $sid, true, $responses, $message);
    return $responses;
}

function imap_test($imap_stream, $string) {
    global $default_charset;
    $message = '';
    $responses = array ();
    $sid = sqimap_session_id();
    $results = array();
    $query = "$sid ".trim($string)."\r\n";
    print "<tr><td>".htmlspecialchars($query)."</td></tr>";
    fputs ($imap_stream, $query);
    $response = sqimap_read_data_list($imap_stream, $sid, false, $responses, $message);
    array_push($response, $message);
    return $response;
}

function print_response($response) {
    foreach($response as $index=>$value) {
        if (is_array($value)) {
            print_response($value);
        }
        else {
            print htmlspecialchars($value)."<br>\n";
        }
    }
}

/**
 * Check if plugin is enabled
 * @param string $plugin_name plugin name
 * @return boolean
 */
function info_is_plugin_enabled($plugin_name) {
    global $plugins;

    if (empty($plugins) || ! is_array($plugins))
        return false;
    
    if ( in_array($plugin_name,$plugins) ) {
        return true;
    } else {
        return false;
    }
}
