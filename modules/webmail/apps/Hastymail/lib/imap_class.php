<?php

/*  imap_class.php: Imap routines 
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

/* internal methods used only from within the imap object */
class imap_base {

    var $max_read;

    function imap_base() {
        $this->max_read = false;
    }
    /* break up a "line" response from imap. If we find
       a literal we read ahead on the stream and include it.
    */
    function parse_line($line, $current_size, $max, $line_length) {
        $line = str_replace(')(', ') (', $line);
        $parts = array();
        $line_cont = false;
        while ($line) {
            $chunk = false;
            switch ($line{0}) {
                case "\r":
                case "\n":
                    $line = false;
                    break;
                case ' ':
                    $line = substr($line, 1);
                    break;
                case '*':
                case '[':
                case ']':
                case '(':
                case ')':
                    $chunk = $line{0};
                    $line = substr($line, 1);
                    break;
                case '"':
                    if (preg_match("/^(\"[^\"\\\]*(?:\\\.[^\"\\\]*)*\")/", $line, $matches)) {
                        $chunk = substr($matches[1], 1, -1);
                    }
                    $line = substr($line, strlen($chunk) + 2);
                    break;
                case '{':
                    $end = strpos($line, '}');
                    if ($end !== false) {
                        $literal_size  = substr($line, 1, ($end - 1));
                    }
                    $lit_result = $this->read_literal($literal_size, $max, $current_size, $line_length);
                    $chunk = $lit_result[0];
                    if ($lit_result[1]) {
                        $line = str_replace(')', ' )', $lit_result[1]);
                    }
                    else {
                        $line_cont = true;
                        $line = false;
                    }
                    break;
                default:
                    if (strpos($line, ' ') !== false) {
                        $marker = strpos($line, ' ');
                        $marker_adjust = $marker;
                        $chunk = substr($line, 0, $marker);
                        $char_check = substr($chunk, -1);
                        $temp_chunk = $chunk;
                        while ($temp_chunk && ($char_check == ')' || $char_check == ']')) {
                            $marker_adjust--;
                            $temp_chunk = substr($temp_chunk, 0, -1);
                            $char_check = substr($temp_chunk, -1);
                        }
                        if ($marker_adjust != $marker) {
                            $marker = $marker_adjust;
                        }
                        $chunk = substr($line, 0, $marker);
                        $line = substr($line, strlen($chunk));
                    }
                    else {
                        $chunk = trim($line);
                        $line = false;
                        $marker = strlen($chunk);
                        $marker_adjust = $marker;
                        $temp_chunk = trim($chunk);
                        $char_check = substr($temp_chunk, -1);
                        while ($temp_chunk && ($char_check == ')' || $char_check == ']')) {
                            $marker_adjust--;
                            $temp_chunk = substr($temp_chunk, 0, -1);
                            $char_check = substr($temp_chunk, -1);
                        }
                        if ($marker_adjust != $marker) {
                            $marker = $marker_adjust;
                            $line = $chunk;
                            $chunk = substr($line, 0, $marker);
                            $line = substr($line, strlen($chunk));
                        }
                    }
                    break;
            }
            if (is_string($chunk)) {
                $parts[] = $chunk;
            }
        }
        return array($line_cont, $parts);
    }
    /* Read literal found during parse_line().
    */
    function read_literal($size, $max, $current, $line_length) {
        $left_over = false;
        $literal_data = $this->fgets($line_length);
        $current += strlen($literal_data);
        $lit_size = strlen($literal_data);
        while ($lit_size < $size) {
            $chunk = $this->fgets($line_length);
            $chunk_size = strlen($chunk);
            $lit_size += $chunk_size;
            $current += $chunk_size;
            $literal_data .= $chunk;
            if ($max && $current > $max) {
                $this->max_read = true;
                break;
            }
        }
        if ($this->max_read) {
            while ($lit_size < $size) {
                $temp = $this->fgets($line_length);
                $lit_size += strlen($temp);
            }
        }
        elseif ($size < strlen($literal_data)) {
            $left_over = substr($literal_data, $size);
            $literal_data = substr($literal_data, 0, $size);
        }
        return array($literal_data, $left_over);
    }
    /* loop through "lines" returned from imap and parse
       them with parse_line() and read_literal. it can return
       the lines in a raw format, or parsed into atoms. It also
       supports a maximum number of lines to return, in case we
       did something stupid like list a loaded unix homedir
       in UW
    */
    function get_response($max=false, $chunked=false, $line_length=8192, $sort=false) {
        global $hm_utils_mod;
        $result = array();
        $current_size = 0;
        $chunked_result = array();
        $last_line_cont = false;
        $line_cont = false;
        $c = -1;
        $n = -1;
        do {
            $n++;
            if (!is_resource($this->handle)) {
                break;
            }
            $result[$n] = $this->fgets($line_length);
            $current_size += strlen($result[$n]);
            if ($max && $current_size > $max) {
                $this->max_read = true;
                break;
            }
            while(substr($result[$n], -2) != "\r\n" && substr($result[$n], -1) != "\n") {
                if (!is_resource($this->handle)) {
                    break;
                }
                $result[$n] .= $this->fgets($line_length);
                if ($result[$n] === false) {
                    break;
                }
                $current_size += strlen($result[$n]);
                if ($max && $current_size > $max) {
                    $this->max_read = true;
                    break 2;
                }
            }
            if ($line_cont) {
                $last_line_cont = true;
                $pres = $n - 1;
                if ($chunks) {
                    $pchunk = $c;
                }
            }
            if ($sort) {
                $line_cont = false;
                $chunks = explode(' ', trim($result[$n]));
            }
            else {
                if ($hm_utils_mod) {
                    list($line_cont, $chunks) = hm_parse_imap_line($result[$n], $current_size, $max, $this->handle);
                }
                else {
                    list($line_cont, $chunks) = $this->parse_line($result[$n], $current_size, $max, $line_length);
                }
            }
            if ($chunks && !$last_line_cont) {
                $c++;
            }
            if ($last_line_cont) {
                $result[$pres] .= ' '.implode(' ', $chunks);
                if ($chunks) {
                    $line_bits = array_merge($chunked_result[$pchunk], $chunks);
                    $chunked_result[$pchunk] = $line_bits;
                }
                $last_line_cont = false;
            }
            else {
                $result[$n] = join(' ', $chunks);
                if ($chunked) {
                    $chunked_result[$c] = $chunks;
                }
            }
        } while (substr($result[$n], 0, strlen('A'.$this->command_count)) != 'A'.$this->command_count);
        $this->responses[] = $result;
        if ($chunked) {
            $result = $chunked_result;
        }
        return $result;
    }
    /* increment the imap command prefix such that it counts
       up on each command sent. ('A1', 'A2', ...) */
    function command_number() {
        $this->command_count += 1;
        return $this->command_count;
    }
    /* put a prefix on a command and send it to the server */
    function send_command($command, $piped=false) {
        $this->on_demand_connect();
        if ($piped) {
            $final_command = '';
            foreach ($command as $v) {
                $final_command .= 'A'.$this->command_number().' '.$v;
            }
            $command = $final_command;
        }
        else {
            $command = 'A'.$this->command_number().' '.$command;
        }
        if (is_resource($this->handle)) {
            fputs($this->handle, $command);
        }
        $this->commands[trim($command)] = microtime();
    }
    /* determine if an imap response returned an "OK", returns
       true or false */
    function check_response($data, $chunked=false) {
        $result = false;
        if ($chunked) {
            if (!empty($data)) {
                $vals = $data[(count($data) - 1)];
                if ($vals[0] == 'A'.$this->command_count) {
                    $this->short_responses[implode(' ', $vals)] = microtime();
                    if (strtoupper($vals[1]) == 'OK') {
                        $result = true;
                    }
                }
            }
        }
        else {
            $line = array_pop($data);
            $this->short_responses[$line] = microtime();
            if (preg_match("/^A".$this->command_count." OK/i", $line)) {
                $result = true;
            }
        }
        return $result;
    }
    /* check the cache size and reduce stored data if its bloated */
    function bust_cache($mailbox) {
        $data = array();
        $max = 20000;
        $min = 9000;
        $total = 0;
        if (isset($_SESSION['frozen_folders'][$mailbox])) {
            return;
        }
        foreach ($_SESSION['mailbox_activity'] as $i => $vals) {
            if ($i != $mailbox && $i != 'INBOX') {
                $total += $vals[1];
                $data[$i] = $vals[0];
            }
        }
        if ($total > $max) {
            $kb = 0;
            if (isset($_SESSION['uid_cache'])) {
                $kb += strlen(serialize($_SESSION['uid_cache']));
            }
            if (isset($_SESSION['header_cache'])) {
                $kb += strlen(serialize($_SESSION['header_cache']));
            }
            $approx = number_format(($kb/1024));
            if ($approx > 400) {
                asort($data);
                foreach ($data as $name => $val) {
                    if (isset($_SESSION['frozen_folders'][$name]) || $name == $mailbox) {
                        continue;
                    }
                    if (isset($_SESSION['uid_cache'][$name])) {
                        unset($_SESSION['uid_cache'][$name]);
                        if (isset($_SESSION['header_cache'][$name])) {
                            unset($_SESSION['header_cache'][$name]);
                        }
                        $total -= $_SESSION['mailbox_activity'][$name][1];
                        unset($_SESSION['mailbox_activity'][$name]);
                        if ($total < $max && $total > $min) {
                            break;
                        }
                    }
                }
            }
        }
    }
    function utf7_decode($string) {
        global $user;
        global $conf;
        if (isset($conf['utf7_folders']) && $conf['utf7_folders']) {
            if (isset($user->user_action->mb_support) && $user->user_action->mb_support) {
                $string = mb_convert_encoding($string, "UTF-8", "UTF7-IMAP" );
            }
        }
        return $string;
    }
    function utf7_encode($string) {
        global $user;
        global $conf;
        if (isset($conf['utf7_folders']) && $conf['utf7_folders']) {
            if (isset($user->user_action->mb_support) && $user->user_action->mb_support) {
                $string = mb_convert_encoding($string, "UTF7-IMAP", "UTF-8" );
            }
        }
        return $string;
    }
}

/* parsing routines for the imap bodstructure response */
class imap_bodystruct extends imap_base {
    function update_part_num($part) {
        if (!strstr($part, '.')) {
            $part++;
        }
        else {
            $parts = explode('.', $part);
            $parts[(count($parts) - 1)]++;
            $part = implode('.', $parts);
        }
        return $part;
    }
    function parse_single_part($array) {
        $vals = $array[0];
        array_shift($vals);
        array_pop($vals);
        $atts = array('name', 'filename', 'type', 'subtype', 'charset', 'id', 'description', 'encoding',
            'size', 'lines', 'md5', 'disposition', 'language', 'location', 'att_size', 'c_date', 'm_date');
        $res = array();
        if (count($vals) > 7) {
            $res['type'] = strtolower(trim(array_shift($vals)));
            $res['subtype'] = strtolower(trim(array_shift($vals)));
            if ($vals[0] == '(') {
                array_shift($vals);
                while($vals[0] != ')') {
                    if (isset($vals[0]) && isset($vals[1])) {
                        $res[strtolower($vals[0])] = $vals[1];
                        $vals = array_splice($vals, 2);
                    }
                }
                array_shift($vals);
            }
            else {
                array_shift($vals);
            }
            $res['id'] = array_shift($vals);
            $res['description'] = array_shift($vals);
            $res['encoding'] = strtolower(array_shift($vals));
            $res['size'] = array_shift($vals);
            if ($res['type'] == 'text' && isset($vals[0])) {
                $res['lines'] = array_shift($vals);
            }
            if (isset($vals[0]) && $vals[0] != ')') {
                $res['md5'] = array_shift($vals);
            }
            if (isset($vals[0]) && $vals[0] == '(') {
                array_shift($vals);
            }
            if (isset($vals[0]) && $vals[0] != ')') {
                $res['disposition'] = array_shift($vals);
                if (strtolower($res['disposition']) == 'attachment' && $vals[0] == '(') {
                    array_shift($vals);
                    $len = count($vals);
                    $flds = array('filename' => 'name', 'size' => 'att_size', 'creation-date' => 'c_date', 'modification-date' => 'm_date');
                    $index = 0;
                    for ($i=0;$i<$len;$i++) {
                        if ($vals[$i] == ')') {
                            $index = $i;
                            break;
                        }
                        if (isset($vals[$i]) && isset($flds[strtolower($vals[$i])]) && isset($vals[($i + 1)]) && $vals[($i + 1)] != ')') {
                            $res[$flds[strtolower($vals[$i])]] = $vals[($i + 1)];
                            $i++;
                        }
                    }
                    if ($index) {
                        array_splice($vals, 0, $index);
                    }
                    else {
                        array_shift($vals);
                    }
                    while ($vals[0] == ')') {
                        array_shift($vals);
                    }
                }
            }
            if (isset($vals[0])) {
                $res['language'] = array_shift($vals);
            }
            if (isset($vals[0])) {
                $res['location'] = array_shift($vals);
            }
            foreach ($atts as $v) {
                if (!isset($res[$v]) || trim(strtoupper($res[$v])) == 'NIL') {
                    $res[$v] = false;
                }
                else {
                    if ($v == 'charset') {
                        $res[$v] = strtolower(trim($res[$v]));
                    }
                    else {
                        $res[$v] = trim($res[$v]);
                    }
                }
            }
            if (!isset($res['name'])) {
                $res['name'] = 'message';
            }
        }
        return $res;
    }
    function filter_alternatives($struct, $filter, $parent_type=false, $cnt=0) {
        $filtered = array();
        if (!is_array($struct) || empty($struct)) {
            return array($filtered, $cnt);
        }
        if (!$parent_type) {
            if (isset($struct['subtype'])) {
                $parent_type = $struct['subtype'];
            }
        }
        foreach ($struct as $index => $value) {
            if ($parent_type == 'alternative' && isset($value['subtype']) && $value['subtype'] != $filter) {
                    $cnt += 1;
                }
            else {
                $filtered[$index] = $value;
            }
            if (isset($value['subs']) && is_array($value['subs'])) {
                if (isset($struct['subtype'])) {
                    $parent_type = $struct['subtype'];
                }
                else {
                    $parent_type = false;
                }
                list($filtered[$index]['subs'], $cnt) = $this->filter_alternatives($value['subs'], $filter, $parent_type, $cnt);
            }
        }
        return array($filtered, $cnt);
    }
    function parse_multi_part($array, $part_num, $run_num) {
        $struct = array();
        $index = 0;
        foreach ($array as $vals) {
            if ($vals[0] != '(') {
                break;
            }
            $type = strtolower($vals[1]);
            $sub = strtolower($vals[2]);
            $part_type = 1;
            switch ($type) {
                case 'message':
                    switch ($sub) {
                        case 'delivery-status':
                        case 'external-body':
                        case 'disposition-notification':
                        case 'rfc822-headers':
                            break;
                        default:
                            $part_type = 2;
                            break;
                    }
                    break;
            }
            if ($vals[0] == '(' && $vals[1] == '(') {
                $part_type = 3;
            }
            if ($part_type == 1) {
                $struct[$part_num] = $this->parse_single_part(array($vals));
                $part_num = $this->update_part_num($part_num);
            }
            elseif ($part_type == 2) {
                $parts = $this->split_toplevel_result($vals);
                $struct[$part_num] = $this->parse_rfc822($parts[0], $part_num);
                $part_num = $this->update_part_num($part_num);
            }
            else {
                $parts = $this->split_toplevel_result($vals);
                $struct[$part_num]['subs'] = $this->parse_multi_part($parts, $part_num.'.1', $part_num);
                $part_num = $this->update_part_num($part_num);
            }
            $index++;
        }
        if (isset($array[$index][0])) {
            $struct['type'] = 'message';
            $struct['subtype'] = $array[$index][0];
        }
        return $struct;
    }
    function parse_rfc822($array, $part_num) {
        $res = array();
        array_shift($array);
        $res['type'] = strtolower(trim(array_shift($array)));
        $res['subtype'] = strtolower(trim(array_shift($array)));
        if ($array[0] == '(') {
            array_shift($array);
            while($array[0] != ')') {
                if (isset($array[0]) && isset($array[1])) {
                    $res[strtolower($array[0])] = $array[1];
                    $array = array_splice($array, 2);
                }
            }
            array_shift($array);
        }
        else {
            array_shift($array);
        }
        $res['id'] = array_shift($array);
        $res['description'] = array_shift($array);
        $res['encoding'] = strtolower(array_shift($array));
        $res['size'] = array_shift($array);
        $envelope = array();
        if ($array[0] == '(') {
            array_shift($array);
            $index = 0;
            $level = 1;
            foreach ($array as $i => $v) {
                if ($level == 0) {
                    $index = $i;
                    break;
                }
                $envelope[] = $v;
                if ($v == '(') {
                    $level++;
                }
                if ($v == ')') {
                    $level--;
                }
            }
            if ($index) {
                $array = array_splice($array, $index);
            }
        }
        $res = $this->parse_envelope($envelope, $res);
        $parts = $this->split_toplevel_result($array); 
        $res['subs'] = $this->parse_multi_part($parts, $part_num.'.1', $part_num);
        return $res;
    }
    function split_toplevel_result($array) {
        if (empty($array) || $array[1] != '(') {
            return array($array);
        }
        $level = 0;
        $i = 0;
        $res = array();
        foreach ($array as $val) {
            if ($val == '(') {
                $level++;
            }
            $res[$i][] = $val;
            if ($val == ')') {
                $level--;
            }
            if ($level == 1) {
                $i++;
            }
        }
        return array_splice($res, 1, -1);
    }
    function parse_envelope_address($array) {
        $count = count($array) - 1;
        $string = '';
        $name = false;
        $mail = false;
        $domain = false;
        for ($i = 0;$i<$count;$i+= 6) {
            if (isset($array[$i + 1])) {
                $name = $array[$i + 1];
            }
            if (isset($array[$i + 3])) {
                $mail = $array[$i + 3];
            }
            if (isset($array[$i + 4])) {
                $domain = $array[$i + 4];
            }
            if ($name && strtoupper($name) != 'NIL') {
                $name = str_replace(array('"', "'"), '', $name);
                if ($string != '') {
                    $string .= ', ';
                }
                if ($name != $mail.'@'.$domain) {
                    $string .= '"'.$name.'" ';
                }
                if ($mail && $domain) {
                    $string .= $mail.'@'.$domain;
                }
            }
            if ($mail && $domain) {
                $string .= $mail.'@'.$domain;
            }
            $name = false;
            $mail = false;
            $domain = false;
        }
        return $string;
    }
    function parse_envelope($array, $res) {
    $flds = array('date', 'subject', 'from', 'sender', 'reply-to', 'to', 'cc', 'bcc', 'in-reply-to', 'message_id');
        foreach ($flds as $val) {
            if (strtoupper($array[0]) != 'NIL') {
                if ($array[0] == '(') {
                    array_shift($array);
                    $parts = array();
                    $index = 0;
                    $level = 1;
                    foreach ($array as $i => $v) {
                        if ($level == 0) {
                            $index = $i;
                            break;
                        }
                        $parts[] = $v;
                        if ($v == '(') {
                            $level++;
                        }
                        if ($v == ')') {
                            $level--;
                        }
                    }
                    if ($index) {
                        $array = array_splice($array, $index);
                        $res[$val] = $this->parse_envelope_address($parts);
                    }
                }
                else {
                    $res[$val] = array_shift($array);
                }
            }
            else {
                $res[$val] = false;
            }
        }
        return $res;
    }
}
/* this class is the wrapper used from lib/url_action_class.php and
   lib/post_action_class.php. It uses imap_base and imap_bodystruct above, and
   is intended to make talking to the IMAP server "easy" from the files
   we use it from */
class imap extends imap_bodystruct {
    var $server;
    var $starttls;
    var $port;
    var $ssl;
    var $auth;
    var $disable_sort_speedup;
    var $handle;
    var $debug;
    var $command_count;
    var $banner;
    var $capability;
    var $connected;
    var $folder_list;
    var $folder_list_restricted;
    var $folder_prefix;
    var $folder_max;
    var $folder_namespace;
    var $namespace_count;
    var $namespaces;
    var $folder_special;
    var $folder_exclude_hidden;
    var $folder_delimiter_override;
    var $delimiter;
    var $use_folder_cache;
    var $short_responses;
    var $search_charset;
    var $use_uid_cache;
    var $use_header_cache;
    var $state;
    var $read_only;
    
   
    /* set defaults */ 
    function imap() {
        $this->debug = array();
        $this->folder_list_restricted;
        $this->server = '127.0.0.1';
        $this->search_charset = 'UTF-8';
        $this->port = 143;
        $this->delimiter = false;
        $this->ssl = false;
        $this->starttls = true;
        $this->auth = false;
        $this->disable_sort_speedup = false;
        $this->handle = false;
        $this->state = 'started';
        $this->command_count = 0;
        $this->commands = array();
        $this->responses = array();
        $this->short_responses = array();
        $this->banner = '';
        $this->read_only = false;
        $this->capability = '';
        $this->connected = false;
        $this->folder_list = array();
        $this->folder_prefix = '';
        $this->folder_max = 350000;
        $this->folder_namespace = array();
        $this->namespaces = array();
        $this->namespace_count = 0;
        $this->use_namespaces = false;
        $this->folder_exclude_hidden = false;
        $this->folder_delimiter_override = false;
        $this->use_folder_cache = 0;
        $this->use_uid_cache = 0;
        $this->use_header_cache = 0;
    }
    /* establish a connection to the server. */
    function connect() {
        if ($this->ssl) {
            $this->server = 'tls://'.$this->server;
        } 
        $this->debug[] = 'Connecting to '.$this->server.' on port '.$this->port;
        $this->handle = @fsockopen($this->server, $this->port, $errorno, $errorstr, 30);
        if (is_resource($this->handle)) {
            $this->debug[] = 'Successfully opened port to the IMAP server';
            $this->connected = true;
            $this->state = 'connected';
        }
        else {
            $this->debug[] = 'Could not connect to the IMAP server';
            $this->debug[] = 'fsockopen errors #'.$errorno.'. '.$errorstr;
        }
    }
    /* authenticate the username and password to the server */
    function authenticate($username, $pass, $proxyuser) {
        global $user;
        global $phpversion;
        if ($this->starttls) {
            if ($phpversion < 5) {
                echo 'FATAL: you must have PHP5 to use STARTTLS';
                exit;
            }
            $command = "STARTTLS\r\n";
            $this->send_command($command);
            $response = $this->get_response();
            if (!empty($response)) {
                $end = array_pop($response);
                if (substr($end, 0, strlen('A'.$this->command_count.' OK')) == 'A'.$this->command_count.' OK') {
                    stream_socket_enable_crypto($this->handle, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                }
            }
        }
        switch (strtolower($this->auth)) {
            case 'cram-md5':
                $this->banner = $this->fgets(1024);
                $cram1 = 'A'.$this->command_number().' AUTHENTICATE CRAM-MD5'."\r\n";
                fputs ($this->handle, $cram1);
                $this->commands[trim($cram1)] = microtime();
                $response = $this->fgets(1024);
                $this->responses[] = $response;
                $challenge = base64_decode(substr(trim($response), 1));
                $pass .= str_repeat(chr(0x00), (64-strlen($pass)));
                $ipad = str_repeat(chr(0x36), 64);
                $opad = str_repeat(chr(0x5c), 64);
                $digest = bin2hex(pack("H*", md5(($pass ^ $opad).pack("H*", md5(($pass ^ $ipad).$challenge)))));
                $challenge_response = base64_encode($username.' '.$digest);
                $this->commands[trim($challenge_response)] = microtime();
                fputs($this->handle, $challenge_response."\r\n");
                break;
            default:
                if ( isset($proxyuser) && $proxyuser != false ) {
                    $login = 'A'.$this->command_number().' LOGIN "'.str_replace('"', '\"', $proxyuser).'" "'.str_replace('"', '\"', $pass). "\"\r\n";
                } else {
                    $login = 'A'.$this->command_number().' LOGIN "'.str_replace('"', '\"', $username).'" "'.str_replace('"', '\"', $pass). "\"\r\n";
                }
                $this->commands[trim(str_replace($pass, 'xxxx', $login))] = microtime();
                fputs($this->handle, $login);
                break;
        }
        $res = $this->get_response();
        $authed = false;
        if (is_array($res) && !empty($res)) {
            $response = array_pop($res);
            $this->short_responses[$response] = microtime();
            if (!$this->auth) {
                if (isset($res[1])) {
                    $this->banner = $res[1];
                }
                if (isset($res[0])) {
                    $this->banner = $res[0];
                }
            }
            if (stristr($response, 'A'.$this->command_count.' OK')) {
                if ( isset($proxyuser) && $proxyuser != false ) {
                    $command = "PROXYAUTH \"".str_replace('"', '\"', $username)."\"\r\n";
                    $this->send_command($command);
                    $res = $this->get_response(false, true);
                    if ( $this->check_response($res, true) ) {
                        $authed = true;
                        $this->state = 'authed';
                    }
                }
                else {
                    $authed = true;
                    $this->state = 'authed';
                }
            }
        }
        return $authed;
    }
    /* get a folder list from the server. Only called once
       when a user logs in then saved in the session if 
       $imp->use_folder_cache is set to true */
    function get_folders($force=false) {
        $this->folder_special = array();
        foreach (array('trash_folder', 'draft_folder', 'sent_folder') as $val) {
            if (isset($_SESSION['user_settings'][$val]) && $_SESSION['user_settings'][$val]) {
                $this->folder_special[] = $_SESSION['user_settings'][$val];
            }
        }
        if (!isset($_SESSION['imap_capability'])) {
            $this->get_capability();
            $_SESSION['imap_capability'] = $this->capability;
        }
        if (isset($_SESSION['folders']) && $this->use_folder_cache && !$force) {
            $this->folder_list = $_SESSION['folders'];
            return;
        }
        if (isset($_SESSION['user_settings']['subscribed_only']) && $_SESSION['user_settings']['subscribed_only']) {
            $imap_command = 'LSUB';
        }
        else {
            $imap_command = 'LIST';
        }
        $excluded = array();
        $parents = array();
        $delim = false;
        $this->folder_namespace = array();
        if ($this->use_namespaces) {
            if (!isset($_SESSION['namespaces'])) {
                $namespaces = $this->get_namespaces();
                $_SESSION['namespaces'] = $namespaces;
            }
            else {
                $namespaces = $_SESSION['namespaces'];
            }
            if (!empty($namespaces)) {
                $this->folder_namespace = $namespaces; 
            }
            else {
                $this->folder_namespace[] = array('prefix' => '', 'delim' => false);
            }
        }
        else {
            $this->folder_namespace[] = array('prefix' => $this->folder_prefix, 'delim' => false, 'class' => 'manual');
        }
        $folders = array();
        foreach ($this->folder_namespace as $nsvals) {
            $namespace = $nsvals['prefix'];
            $delim = $nsvals['delim'];
            $ns_class = $nsvals['class'];
            if (strtoupper($namespace) == 'INBOX') { 
                $namespace = '';
            }
            $command = $imap_command.' "'.$namespace."\" \"*\"\r\n";
            $this->send_command($command);
            $result = $this->get_response($this->folder_max, true);
            foreach ($result as $vals) {
                if (!isset($vals[0])) {
                    continue;
                }
                if ($vals[0] == 'A'.$this->command_count) {
                    continue;
                }
                $flags = false;
                $count = count($vals);
                $folder = $this->utf7_decode($vals[($count - 1)]);
                $flag = false;
                $delim_flag = false;
                $parent = '';
                $base_name = '';
                $folder_parts = array();
                $no_select = false;
                $can_have_kids = false;
                $has_kids = false;
                $marked = false;
                $special = false;
                $hidden = false;
                $folder_sort_by = 'ARRIVAL';
                $check_for_new = false;
                foreach ($vals as $v) {
                    if ($v == '(') {
                        $flag = true;
                    }
                    elseif ($v == ')') {
                        $flag = false;
                        $delim_flag = true;
                    }
                    else {
                        if ($flag) {
                            $flags .= ' '.$v;
                        }
                        if ($delim_flag && !$delim) {
                            $delim = $v;
                            $delim_flag = false;
                        }
                    }
                }
                if ($this->folder_delimiter_override) {
                    $delim = $this->folder_delimiter_override;
                }
                if (!$this->delimiter) {
                    $this->delimiter = $delim;
                    $_SESSION['imap_delimiter'] = $this->delimiter;
                }
                if ($delim && strstr($folder, $delim)) {
                    $temp_parts = explode($delim, $folder);
                    $folder_parts = array();
                    foreach ($temp_parts as $g) {
                        if (trim($g)) {
                            $folder_parts[] = $g;
                        }
                    }
                }
                if (isset($folder_parts[(count($folder_parts) - 1)])) {
                    $base_name = $folder_parts[(count($folder_parts) - 1)];
                }
                else {
                    $base_name = $folder;
                }
                if ($this->folder_exclude_hidden) {
                    if (substr($base_name, 0, 1) == '.') {
                        if (!in_array($base_name, $excluded)) {
                            $excluded[] = $base_name;
                        }
                        continue;
                    }
                    else {
                        $excl = false;
                        foreach ($folder_parts as $v) {
                            if (substr($v, 0, 1) == '.') {
                                $excl = true;
                                if (!in_array($v, $excluded)) {
                                    $excluded[] = $v;
                                }
                                break;
                            }
                        }
                        if ($excl) {
                            continue;
                        }
                    }
                }
                if (isset($folder_parts[(count($folder_parts) - 2)])) {
                    $parent = join($delim, array_slice($folder_parts, 0, -1));
                    if ($parent.$delim == $namespace) {
                        $parent = '';
                    }
                }
                if (stristr($flags, 'marked')) { 
                    $marked = true;
                }
                if (!stristr($flags, 'noinferiors')) { 
                    $can_have_kids = true;
                }
                if (($folder == $namespace && $namespace) || stristr($flags, 'haschildren')) { 
                    $has_kids = true;
                }
                if ($folder != 'INBOX' && $folder != $namespace && stristr($flags, 'noselect')) { 
                    $no_select = true;
                }
                if (isset($_SESSION['user_settings']['hidden_folders']) && in_array($folder, $_SESSION['user_settings']['hidden_folders'])) {
                    $hidden = true;
                }
                if (isset($_SESSION['user_settings']['sort_by'][$folder])) {
                    $folder_sort_by = $_SESSION['user_settings']['sort_by'][$folder];
                }
                if (isset($_SESSION['user_settings']['folder_check']) && in_array($folder, $_SESSION['user_settings']['folder_check'])) {
                    $check_for_new = true;
                }
                $temp_name = '';
                if (!empty($folder_parts)) {
                    $temp_name = ''; 
                    foreach ($folder_parts as $name) {
                        if ($temp_name) {
                            $temp_name = $delim.$this->prep_folder_name($name, false, false, false);
                        }
                        else {
                            $temp_name = $this->prep_folder_name($name, false, false, false);
                        }
                        if (in_array($temp_name, $this->folder_special)) {
                            $special = true;
                            break;
                        }
                    }
                    if (!$special && in_array($folder, $this->folder_special)) {
                        $special = true;
                    }
                }
                else {
                    if (in_array($folder, $this->folder_special)) {
                        $special = true;
                    }
                }
                if (!isset($folders[$folder]) && $folder) {
                    $folders[$folder] = array('parent' => $parent, 'delim' => $delim, 'name' => $folder,
                                            'name_parts' => $folder_parts, 'basename' => $base_name,
                                            'realname' => $folder, 'namespace' => $namespace, 'marked' => $marked,
                                            'noselect' => $no_select, 'can_have_kids' => $can_have_kids,
                                            'has_kids' => $has_kids, 'special' => $special, 'hidden' => $hidden,
                                            'check_for_new' => $check_for_new, 'sort_by' => $folder_sort_by,
                                            'ns_class' => $ns_class);
                }
                if ($parent && !in_array($parent, $parents)) {
                    $parents[$parent][] = $folders[$folder];
                }
            }
        }
        $place_holders = array();
        foreach ($parents as $val => $parent_list) {
            foreach ($parent_list as $parent) {
                $found = false;
                foreach ($folders as $i => $vals) {
                    if ($vals['name'] == $val) {
                        $folders[$i]['has_kids'] = 1;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    if (count($parent['name_parts']) > 1) {
                        foreach ($parent['name_parts'] as $i => $v) {
                            $fname = join($delim, array_slice($parent['name_parts'], 0, ($i + 1)));
                            $name_parts = array_slice($parent['name_parts'], 0, ($i + 1));
                            if (!isset($folders[$fname])) {
                                $freal = $v;
                                if ($i > 0) {
                                    $fparent = join($delim, array_slice($parent['name_parts'], 0, $i));
                                }
                                else {
                                    $fparent = false;
                                }
                                $place_holders[] = $fname;
                                $folders[$fname] = array('parent' => $fparent, 'delim' => $delim, 'name' => $freal,
                                                'name_parts' => $name_parts, 'basename' => $freal,
                                                'realname' => $fname, 'namespace' => $namespace, 'marked' => false,
                                                'noselect' => true, 'can_have_kids' => true,
                                                'has_kids' => true, 'special' => false, 'hidden' => false,
                                                'check_for_new' => false, 'sort_by' => false, 'ns_class' => $ns_class);
                            }
                        }
                    }
                }
            }
        }
        $_SESSION['folder_place_holders'] = $place_holders;
        uksort($folders, 'folder_sort');
        if (isset($_SESSION['folder_state'])) {
            unset($_SESSION['folder_state']);
        }
        $new_folders = array();
        $special_folders = array();
        $index_vals = array();
        foreach ($folders as $index => $vals) {
            if (strtoupper($index) == 'INBOX' || $index == $namespace) {
                $index_vals['INBOX'] = $vals;
                if ($index == $namespace) {
                    $index_vals['INBOX']['has_kids'] = 0;
                }
                $index_vals['INBOX']['realname'] = 'INBOX';
            }
            elseif ($vals['special']) {
                $special_folders[$index] = $vals;
            }
            else {
                $new_folders[$index] = $vals;
            }
        }
        foreach ($special_folders as $i => $vals) {
            if (!empty($vals['name_parts'])) {
                $special_parent = '';
                foreach ($vals['name_parts'] as $v) {
                    if ($special_parent) {
                        $special_parent .= $delim.$v;
                    }
                    else {
                        $special_parent .= $v;
                    }
                    if ($i != $special_parent && isset($folders[$special_parent])) {
                        $special_folders[$special_parent] = $folders[$special_parent];
                        unset($new_folders[$special_parent]);
                        $special_folders[$special_parent]['special'] = true;
                    }
                }
            }
        }
        uksort($special_folders, 'folder_sort');
        $folders = array();
        foreach (array('index_vals', 'special_folders', 'new_folders') as $folder_type) {
            foreach ($$folder_type as $i => $v) {
                $folders[$i] = $v;
            }
        }
        if (!isset($folders['INBOX'])) {
            $folders = array_merge(array('INBOX' => array(
                    'name' => 'INBOX', 'basename' => 'INBOX', 'realname' => 'INBOX', 'noselect' => false,
                    'parent' => false, 'hidden' => false, 'has_kids' => false, 'special' => false)), $folders);
        }
        $_SESSION['folders'] = $folders;
        $this->folder_list = $folders;
    }
    /* get the imap CAPABILITY response */
    function get_capability() {
        $command = "CAPABILITY\r\n";
        $this->send_command($command);
        $response = $this->get_response();
        $this->capability = implode(' ', $response);
    }
    /* takes an array of folder names and gets the number of unread
       messages and the total number of messages. Puts its result data
       in the folders array created by get_folders() in the session */
    function get_unseen_status($folders) {
        global $user;
        $command = array();
        if (empty($folders)) {
            return;
        }
        foreach ($folders as $v) {
            if (!$this->clean($v, 'mailbox')) {
                return;
            }
            $command[] =  'STATUS "'.$this->utf7_encode($v)."\" (UNSEEN MESSAGES)\r\n";
        }
        $unseen_total = 0;
        $unseen_array = array();
        $this->send_command($command, true);
        $res = $this->get_response(false, true);
        if (is_array($res) && !empty($res)) {
            foreach ($res as $vals) {
                if (!isset($vals[0])) {
                    continue;
                }
                if ($vals[0] == '*' && strtoupper($vals[1]) == 'STATUS') {
                    $folder = $this->utf7_decode($vals[2]);
                    if (isset($_SESSION['folders'][$folder])) {
                        $messages = 0;
                        $unseen = 0;
                        $cnt = count($vals);
                        for ($i=3;$i<$cnt;$i++) {
                            if (strtoupper($vals[$i]) == 'UNSEEN') {
                                if (isset($vals[$i + 1])) {
                                    $unseen = (int) $vals[$i + 1];
                                }
                            }
                            if (strtoupper($vals[$i]) == 'MESSAGES') {
                                if (isset($vals[$i + 1])) {
                                    $messages = (int) $vals[$i + 1];
                                }
                            }
                        }
                        $unseen_total += $unseen;
                        $unseen_array[$folder] = array($unseen, $messages);
                        $invalidate_cache = false;
                        if (isset($_SESSION['folders'][$folder]['status']['messages'])) {
                            if ($_SESSION['folders'][$folder]['status']['messages'] != $messages) {
                                $invalidate_cache = true;
                            }
                        }
                        if (isset($_SESSION['folders'][$folder]['status']['unseen'])) {
                            if ($_SESSION['folders'][$folder]['status']['unseen'] != $unseen) {
                                $invalidate_cache = true;
                            }
                        }
                        if ($invalidate_cache) {
                            $_SESSION['uid_cache_refresh'][$folder] = 1;
                            $_SESSION['header_cache_refresh'][$folder] = 1;
                        }
                        $_SESSION['folders'][$folder]['status']['messages'] = $messages;
                        $_SESSION['folders'][$folder]['status']['unseen'] = $unseen;
                    }
                }
            }
        }
        $_SESSION['total_unread'] = $unseen_total;
        return $unseen_array;
    }
    function on_demand_connect() {
        global $user;
        if (!$this->connected) {
            $user->user_session->imap_continue();
            if ($user->logged_in && $this->banner) {
                $_SESSION['imap_banner'] = $this->banner;
            }
        }
        if ($user->logged_in && !$this->connected && !in_array($user->str[505], $user->notices)) {
            $user->notices[] = $user->str[505]; //'Could not connect to the IMAP server';
        }
    }
    /* select a mailbox. Any return information is then stored
       in the folders array created by get_folders() in the session */
    function select_mailbox($mailbox, $sort_by, $unseen=false, $quick=false, $filter='ALL') {
        $box = $this->utf7_encode(str_replace('"', '\"', $mailbox));
        if (!$this->clean($box, 'mailbox')) {
            return false;
        }
        if (!$this->read_only) {
            $command = "SELECT \"$box\"\r\n";
        }
        else {
            $command = "EXAMINE \"$box\"\r\n";
        }
        $this->send_command($command);
        $res = $this->get_response(false, true);
        $status = $this->check_response($res, true);
        $uidvalidity = 0;
        $exists = 0;
        $uidnext = 0; 
        $flags = array();
        $pflags = array();
        foreach ($res as $vals) {
            if (in_array('UIDNEXT', $vals)) {
                foreach ($vals as $i => $v) {
                    if (intval($v) && isset($vals[($i - 1)]) && $vals[($i - 1)] == 'UIDNEXT') {
                        $uidnext = $v;
                    }
                }
            }
            if (in_array('UIDVALIDITY', $vals)) {
                foreach ($vals as $i => $v) {
                    if (intval($v) && isset($vals[($i - 1)]) && $vals[($i - 1)] == 'UIDVALIDITY') {
                        $uidvalidity = $v;
                    }
                }
            }
            if (in_array('PERMANENTFLAGS', $vals)) {
                $collect_flags = false;
                foreach ($vals as $i => $v) {
                    if ($v == ')') {
                        $collect_flags = false;
                    }
                    if ($collect_flags) {
                        $pflags[] = $v;
                    }
                    if ($v == '(') {
                        $collect_flags = true;
                    }
                }
            }
            if (in_array('FLAGS', $vals)) {
                $collect_flags = false;
                foreach ($vals as $i => $v) {
                    if ($v == ')') {
                        $collect_flags = false;
                    }
                    if ($collect_flags) {
                        $flags[] = $v;
                    }
                    if ($v == '(') {
                        $collect_flags = true;
                    }
                }
            }
            if (in_array('EXISTS', $vals)) {
                foreach ($vals as $i => $v) {
                    if (intval($v) && isset($vals[($i + 1)]) && $vals[($i + 1)] == 'EXISTS') {
                        $exists = $v;
                    }
                }
            }
        }
        if ($status) {
            $invalidate_cache = false;
            if (isset($_SESSION['folders'][$mailbox]['status']['messages'])) {
                if ($_SESSION['folders'][$mailbox]['status']['messages'] != $exists) {
                    $invalidate_cache = true;
                }
            }
            if (isset($_SESSION['folders'][$mailbox]['status']['uidvalidity'])) {
                if ($_SESSION['folders'][$mailbox]['status']['uidvalidity'] != $uidvalidity) {
                    $invalidate_cache = true;
                }
            }
            if (isset($_SESSION['folders'][$mailbox]['status']['uidnext'])) {
                if ($_SESSION['folders'][$mailbox]['status']['uidnext'] != $uidnext) {
                    $invalidate_cache = true;
                }
            }
            if ($invalidate_cache) {
                $_SESSION['uid_cache_refresh'][$mailbox] = 1;
                $_SESSION['header_cache_refresh'][$mailbox] = 1;
            }
            $_SESSION['folders'][$mailbox]['status']['uidnext'] = $uidnext;
            $_SESSION['folders'][$mailbox]['status']['uidvalidity'] = $uidvalidity;
            $_SESSION['folders'][$mailbox]['status']['messages'] = $exists;
            $_SESSION['folders'][$mailbox]['flags'] = $flags;
            $_SESSION['folders'][$mailbox]['permanentflags'] = $pflags;
            $_SESSION['mailbox_activity'][$mailbox] = array(time(), $exists);
            if (count($_SESSION['mailbox_activity']) > 2) {
                $this->bust_cache($mailbox);
            }
            if (!$quick) {
                $unseen_ids = $this->get_mailbox_unseen($mailbox);
            }
            if ($sort_by) {
                $status = $this->sort_mailbox($mailbox, $sort_by, $filter);
            }
            elseif ($unseen) {
                $status = $unseen_ids;
            }
            $this->state = 'selected';
        }
        return $status;
    }
    function get_mailbox_unseen($folder) {
        global $user;
        $command = "UID SEARCH (UNSEEN) ALL\r\n";
        $this->send_command($command);
        $res = $this->get_response(false, true);
        $status = $this->check_response($res, true);
        $unseen = 0;
        $uids = array();
        if ($status) {
            array_pop($res);
            foreach ($res as $vals) {
                foreach ($vals as $v) {
                    if ($user->user_action->match_int($v)) {
                        $unseen++;
                        $uids[] = $v;
                    }
                }
            }
            if (isset($_SESSION['folders'][$folder]['status']['unseen'])) {
                if ($_SESSION['folders'][$folder]['status']['unseen'] != $unseen) {
                    if (isset($_SESSION['total_unread']) && isset($_SESSION['user_settings']['folder_check']) &&
                        is_array($_SESSION['user_settings']['folder_check']) && in_array($folder, $_SESSION['user_settings']['folder_check'])) {
                        $_SESSION['total_unread'] += ($unseen - $_SESSION['folders'][$folder]['status']['unseen']);
                    }
                    $_SESSION['uid_cache_refresh'][$folder] = 1;
                    $_SESSION['header_cache_refresh'][$folder] = 1;
                }
            }
            if (!isset($_SESSION['total_unread']) && is_array($_SESSION['user_settings']['folder_check']) && in_array($folder, $_SESSION['user_settings']['folder_check'])) {
                $_SESSION['total_unread'] = $unseen;
            }
            $_SESSION['folders'][$folder]['status']['unseen'] = $unseen;
        }
        return array($unseen, $uids);
    }
    /*  get the headers for a mailbox page display. Saved in the session
        for re-use, controled by the $imap->use_header_cache setting */
    function get_mailbox_page($mailbox, $uids, $page) {
        if ($page) {
            if ($this->use_header_cache) {
                if (isset($_SESSION['header_cache'][$mailbox][$page])) {
                    if (!isset($_SESSION['header_cache_refresh'][$mailbox]) && count($_SESSION['header_cache'][$mailbox][$page]) == count($uids)) {
                        return $_SESSION['header_cache'][$mailbox][$page];
                    }
                    else {
                        unset($_SESSION['header_cache_refresh'][$mailbox]);
                        unset($_SESSION['header_cache'][$mailbox]);
                    }
                }
            }
        }
        $sorted_string = implode(',', $uids);
        if (!$this->clean($sorted_string, 'uid_list')) {
            return array();
        }
        $command = 'UID FETCH '.$sorted_string.' (FLAGS INTERNALDATE RFC822.SIZE BODY.PEEK[HEADER.FIELDS (SUBJECT FROM '.
                   "DATE CONTENT-TYPE X-PRIORITY TO)])\r\n";
        $this->send_command($command);
        $res = $this->get_response(false, true);
        $status = $this->check_response($res, true);
        $tags = array('UID' => 'uid', 'FLAGS' => 'flags', 'RFC822.SIZE' => 'size', 'INTERNALDATE' => 'internal_date');
        $junk = array('SUBJECT', 'FROM', 'CONTENT-TYPE', 'TO', '(', ')', ']', 'X-PRIORITY', 'DATE');
        $flds = array('date' => 'date', 'from' => 'from', 'to' => 'to', 'subject' => 'subject', 'content-type' => 'content_type', 'x-priority' => 'x_priority');
        $headers = array();
        foreach ($res as $n => $vals) {
            if (isset($vals[0]) && $vals[0] == '*') {
                $uid = 0;
                $size = 0;
                $subject = '';
                $from = '';
                $date = '';
                $x_priority = 0;
                $content_type = '';
                $to = '';
                $flags = '';
                $internal_date = '';
                $count = count($vals);
                for ($i=0;$i<$count;$i++) {
                    if ($vals[$i] == 'BODY[HEADER.FIELDS') {
                        $i++;
                        while(isset($vals[$i]) && in_array(strtoupper($vals[$i]), $junk)) {
                            $i++;
                        }
                        $last_header = false;
                        $lines = explode("\r\n", $vals[$i]);
                        foreach ($lines as $line) {
                            $header = strtolower(substr($line, 0, strpos($line, ':')));
                            if (!$header || (!isset($flds[$header]) && $last_header)) {
                                ${$flds[$last_header]} .= "\r\n".$line;
                            }
                            elseif (isset($flds[$header])) {
                                ${$flds[$header]} = substr($line, (strpos($line, ':') + 1));
                                $last_header = $header;
                            }
                        }
                    }
                    elseif (isset($tags[strtoupper($vals[$i])])) {
                        if (isset($vals[($i + 1)])) {
                            if ($tags[strtoupper($vals[$i])] == 'flags' && $vals[$i + 1] == '(') {
                                $n = 2;
                                while (isset($vals[$i + $n]) && $vals[$i + $n] != ')') {
                                    $flags .= ' '.$vals[$i + $n];
                                    $n++;
                                }
                                $i += $n;
                            }
                            else {
                                $$tags[strtoupper($vals[$i])] = $vals[($i + 1)];
                                $i++;
                            }
                        }
                    }
                }
                if ($uid) {
                    $cset = '';
                    if (stristr($content_type, 'charset=')) {
                        if (preg_match("/charset\=([^\s;]+)/", $content_type, $matches)) {
                            $cset = trim(strtolower(str_replace(array('"', "'"), '', $matches[1])));
                        }
                    }
                    $headers[$uid] = array('uid' => $uid, 'flags' => $flags, 'internal_date' => $internal_date, 'size' => $size,
                                     'date' => $date, 'from' => $from, 'to' => $to, 'subject' => $subject, 'content-type' => $content_type,
                                     'timestamp' => time(), 'charset' => $cset, 'x-priority' => $x_priority);
                }
            }
        }
        $final_headers = array();
        foreach ($uids as $v) {
            if (isset($headers[$v])) {
                $final_headers[$v] = $headers[$v];
            }
        }
        if ($page) {
            $_SESSION['header_cache'][$mailbox][$page] = $final_headers;
        }
        return $final_headers;
    }
    /* wrapper around various sort types. Check the CAPABILITY
       repsonse to decide how to sort */
    function sort_mailbox($mailbox, $sort_type, $filter='ALL') {
        if (substr($sort_type, 0, 2) == 'R_') {
            $sort = substr($sort_type, 2);
            if ($sort == 'ARRIVAL' || $sort == 'DATE') {
                $reverse = false;
            }
            else {
                $reverse = true;
            }
        }
        else {
            $sort = $sort_type;
            if ($sort == 'ARRIVAL' || $sort == 'DATE') {
                $reverse = true;
            }
            else {
                $reverse = false;
            }
        }
        if ($this->use_uid_cache) {
            if (isset($_SESSION['uid_cache'][$mailbox])) {
                if (isset($_SESSION['frozen_folders'][$mailbox])) {
                    return true;
                }
                if (!isset($_SESSION['uid_cache_refresh'][$mailbox])) {
                    if ($sort == $_SESSION['uid_cache'][$mailbox]['sort'] && $filter == $_SESSION['uid_cache'][$mailbox]['filter'] &&
                        ((isset($_SESSION['uid_cache'][$mailbox]['reverse']) && $reverse == $_SESSION['uid_cache'][$mailbox]['reverse']) ||
                        $sort == 'THREAD_R' || $sort == 'THREAD_O')) {
                        return true;
                    }
                    else {
                        $_SESSION['header_cache_refresh'][$mailbox] = 1;
                        if (isset($_SESSION['header_cache'][$mailbox])) {
                            unset($_SESSION['header_cache'][$mailbox]);
                        }
                    }
                }
                else {
                    unset($_SESSION['uid_cache_refresh'][$mailbox]);
                }
            }
        }
        if (($sort == 'THREAD_R' || $sort == 'THREAD_O')) {
            if ($sort == 'THREAD_O') {
                if (stristr($_SESSION['imap_capability'], 'ORDEREDSUBJECT')) {
                    return $this->thread_sort($mailbox, $sort, $filter);
                }
                else {
                    return $this->server_side_sort($mailbox, 'ARRIVAL', false, $filter);
                }
            }
            if ($sort == 'THREAD_R') {
                if (stristr($_SESSION['imap_capability'], 'THREAD')) {
                    return $this->thread_sort($mailbox, $sort, $filter);
                }
                else {
                    return $this->server_side_sort($mailbox, 'ARRIVAL', false, $filter);
                }
            }
        }
        elseif (stristr($_SESSION['imap_capability'], 'SORT')) {
            return $this->server_side_sort($mailbox, $sort, $reverse, $filter);
        }
        else {
            return $this->client_side_sort($mailbox, $sort, $reverse, $filter);
        }
    }
    /* use the SORT extension to get a sorted UID list */
    function server_side_sort($mailbox, $sort, $reverse, $filter) {
        if (!$this->clean($sort, 'keyword') || !$this->clean($filter, 'keyword')) {
            return false;
        }
        $command = 'UID SORT ('.$sort.') US-ASCII '.$filter."\r\n";
        $this->send_command($command);
        if ($this->disable_sort_speedup) {
            $speedup = false;
        }
        else {
            $speedup = true;
        }
        $res = $this->get_response(false, true, 8192, $speedup);
        $status = $this->check_response($res, true);
        $uids = array();
        foreach ($res as $vals) {
            if ($vals[0] == '*' && strtoupper($vals[1]) == 'SORT') {
                array_shift($vals);
                array_shift($vals);
                $uids = array_merge($uids, $vals);
            }
            else {
                if (preg_match("/^(\d)+$/", $vals[0])) {
                    $uids = array_merge($uids, $vals);
                }
            }
        }
        unset($res);
        if ($reverse) {
            $uids = array_reverse($uids);
        }
        $_SESSION['uid_cache'][$mailbox] = array('uids' => $uids, 'total' => count($uids), 'thread_data' => array(),
                                                 'sort' => $sort, 'filter' => $filter, 'reverse' => $reverse, 'timestamp' => time());
        $_SESSION['folders'][$mailbox]['status']['messages'] = count($uids);
        return $status;
    }
    /* use the FETCH command to manually sort the mailbox */
    function client_side_sort($mailbox, $sort, $reverse) {
        if (!$this->clean($mailbox, 'mailbox') || !$this->clean($sort, 'keyword')) {
            return false;
        }
        $command1 = 'UID FETCH 1:* ';
        switch ($sort) {
            case 'DATE':
            case 'R_DATE':
                $command2 = "BODY.PEEK[HEADER.FIELDS (DATE)]\r\n";
                $key = "BODY[HEADER.FIELDS";
                break;
            case 'SIZE':
            case 'R_SIZE':
                $command2 = "RFC822.SIZE\r\n";
                $key = "RFC822.SIZE";
                break;
            case 'ARRIVAL':
                $command2 = "INTERNALDATE\r\n";
                $key = "INTERNALDATE";
                break;
            case 'R_ARRIVAL':
                $command2 = "INTERNALDATE\r\n";
                $key = "INTERNALDATE";
                break;
            case 'FROM':
            case 'R_FROM':
                $command2 = "BODY.PEEK[HEADER.FIELDS (FROM)]\r\n";
                $key = "BODY[HEADER.FIELDS";
                break;
            case 'SUBJECT':
            case 'R_SUBJECT':
                $command2 = "BODY.PEEK[HEADER.FIELDS (SUBJECT)]\r\n";
                $key = "BODY[HEADER.FIELDS";
                break;
            default:
                $command2 = "INTERNALDATE\r\n";
                $key = "INTERNALDATE";
                break;
        }
        $command = $command1.$command2;
        $this->send_command($command);
        $res = $this->get_response(false, true);
        $status = $this->check_response($res, true);
        $uids = array();
        $sort_keys = array();
        foreach ($res as $vals) {
            if (!isset($vals[0]) || $vals[0] != '*') {
                continue;
            }
            $uid = 0;
            $sort_key = 0;
            $body = false;
            foreach ($vals as $i => $v) {
                if ($body) {
                    if ($v == ']' && isset($vals[$i + 1])) {
                        if ($command2 == "BODY.PEEK[HEADER.FIELDS (DATE)]\r\n") {
                            $sort_key = strtotime(trim(substr($vals[$i + 1], 5)));
                        }
                        else {
                            $sort_key = $vals[$i + 1];
                        }
                        $body = false;
                    }
                }
                if (strtoupper($v) == 'UID') {
                    if (isset($vals[($i + 1)])) {
                        $uid = $vals[$i + 1];
                        $uids[] = $uid;
                    }
                }
                if ($key == strtoupper($v)) {
                    if (substr($key, 0, 4) == 'BODY') {
                        $body = 1;
                    }
                    elseif (isset($vals[($i + 1)])) {
                        if ($key == "INTERNALDATE") {
                            $sort_key = strtotime($vals[$i + 1]);
                        }
                        else {
                            $sort_key = $vals[$i + 1];
                        }
                    }
                }
            }
            if ($sort_key && $uid) {
                $sort_keys[$uid] = $sort_key;
            }
        }
        if (count($sort_keys) != count($uids)) {
            if (count($sort_keys) < count($uids)) {
                foreach ($uids as $v) {
                    if (!isset($sort_keys[$v])) {
                        $sort_keys[$v] = false;
                    }
                }
            }
        }
        unset($res);
        natcasesort($sort_keys);
        $uids = array_keys($sort_keys);
        if ($reverse) {
            $uids = array_reverse($uids);
        }
        $_SESSION['uid_cache'][$mailbox] = array('uids' => $uids, 'total' => count($uids), 'thread_data' => array(),
                                                 'sort' => $sort, 'filter' => 'ALL', 'reverse' => $reverse, 'timestamp' => time());
        $_SESSION['folders'][$mailbox]['status']['messages'] = count($uids);
        return $status;
    }
    /* use the THREAD extension to get the sorted UID list and thread data */
    function thread_sort($mailbox, $sort ,$filter) {
        if (!$this->clean($filter, 'keyword')) {
            return false;
        }
        if (substr($sort, 7) == 'R') {
            $method = 'REFERENCES';
        }
        else {
            $method = 'ORDEREDSUBJECT';
        }
        $command = 'UID THREAD '.$method.' US-ASCII '.$filter."\r\n";
        $this->send_command($command);
        $res = $this->get_response();
        $status = $this->check_response($res);
        $uid_string = '';
        foreach ($res as $val) {
            if (strtoupper(substr($val, 0, 8)) == '* THREAD') {
                $uid_string .= ' '.substr($val, 8);
            }
        }
        unset($res);
        $uids = array();
        $thread_data = array();
        $uid_string = str_replace(array(' )', ' ) ', ')', ' (', ' ( ', '( '), array(')', ')', ')', '(', '(', '('), $uid_string);
        $branches = array();
        $level = 0;
        $thread = 0;
        $last_id = 0;
        $offset = 0;
        $parents = array();
        while($uid_string) {
            switch ($uid_string{0}) {
                case ' ':
                    $level++;
                    $offset++;
                    $parents[$level] = $last_id;
                    $uid_string = substr($uid_string, 1);
                    break;
                case '(':
                    $level++;
                    if ($level == 2) {
                        $parents[$level] = $thread;
                    }
                    $uid_string = substr($uid_string, 1);
                    break; 
                case ')':
                    $uid_string = substr($uid_string, 1);
                    if ($offset) {
                        $level -= $offset;
                        $offset = 0;
                    }
                    $level--;
                    break; 
                default:
                    if (preg_match("/^(\d+)/", $uid_string, $matches)) {
                        if ($level == 1) {
                            $thread = $matches[1];
                            $parents = array(1 => 0);
                        }
                        if (!isset($parents[$level])) {
                            if (isset($parents[$level - 1])) {
                                $parents[$level] = $parents[$level - 1];
                            }
                            else {
                                $parents[$level] = 0;
                            }
                        }
                        $thread_data[$thread][$matches[1]] = array('parent' => $parents[$level], 'level' => $level, 'thread' => $thread);
                        $parents[$level] = $thread;
                        $last_id = $matches[1];
                        $uid_string = substr($uid_string, strlen($matches[1]));
                    }
                    else {
                        echo 'BUG'.$uid_string."\r\n";;
                        $uid_string = substr($uid_string, 1);
                    }
            }
        }
        $thread_data = array_reverse($thread_data);
        $new_thread_data = array();
        $threads = array();
        foreach ($thread_data as $vals) {
            foreach ($vals as $i => $v) {
                $uids[] = $i;
                if ($v['parent'] && isset($new_thread_data[$v['parent']])) {
                    if (isset($new_thread_data[$v['thread']]['reply_count'])) {
                        $new_thread_data[$v['thread']]['reply_count']++;
                    }
                    else {
                        $new_thread_data[$v['thread']]['reply_count'] = 1;
                    }
                }
                else {
                    $threads[] = $i;
                }
                $new_thread_data[$i] = $v;
            }
        }
        $_SESSION['uid_cache'][$mailbox] = array('uids' => $uids, 'total' => count($uids), 'thread_data' => $new_thread_data,
                                                 'sort' => $sort, 'filter' => $filter, 'timestamp' => time(), 'threads' => $threads);
        return $status;
    }
    /* sort a mailbox by thread */
    function client_thread_sort() {
        $command = "UID FETCH 1:* BODY.PEEK[HEADER.FIELDS (SUBJECT MESSAGE-ID REFERENCES DATE IN-REPLY-TO)]\r\n";
        $this->send_command($command);
        $res = $this->get_response(false, true);
        $status = $this->check_response($res, true);
        $data = array();
        $thread_ref = array();
        foreach ($res as $vals) {
            if (strtoupper($vals[1]) != 'OK') {
                $cnt = count($vals);
                $uid = false;
                $header_vals = false;
                for($i = 0; $i < $cnt; $i++) {
                    if (strtoupper($vals[$i]) == 'UID' && isset($vals[$i + 1])) {
                        $uid = $vals[$i + 1];
                    }
                    if ($vals[$i] == ']' && isset($vals[$i + 1])) {
                        $header_vals = $vals[$i + 1];
                    }
                }
                if ($header_vals && $uid) {
                    $headers = explode("\n", trim($header_vals));
                    $atts = array('subject' => '', 'message-id' => '', 'references' => '', 'date' => '', 'in-reply-to' => '');
                    foreach ($headers as $v) {
                        if ($v && isset($name) && $name && ($v{0} == ' ' || $v{0} == "\t") && isset($atts[strtolower($name)])) {
                            $atts[strtolower($name)] .= ' '.$v;
                        }
                        elseif ($v && strpos($v, ':') !== false) {
                            list($name, $value) = explode(':', $v, 2);
                            $value = trim($value);
                            if (isset($atts[strtolower($name)])) {
                                if (strtolower($name) == 'date') {
                                    $atts[strtolower($name)] = strtotime(trim($value));
                                }
                                else {
                                    if (strtolower($name) == 'references') {
                                        if (strpos($value, ' ') !== false) {
                                            $thread_ref[substr($value, 0, strpos($value, ' '))] = $uid;
                                        }
                                        else {
                                            $thread_ref[$value] = $uid;
                                        }
                                    }
                                    $atts[strtolower($name)] = $value;
                                }
                            }
                        }
                    }
                    if ($atts['message-id'] && !isset($thread_ref[$atts['message-id']]) && !$atts['references'] && !$atts['in-reply-to'] && $atts['subject']) {
                        if (!preg_match("/^\s*(|\[[^\]])\s*(re\:|fw(|d))/i", $atts['subject'])) {
                            $thread_ref[$atts['message-id']] = $uid;
                        }
                    }
                    $data[$uid] = $atts;
                }
                //else { echo_r("BUG!"); }
            }
        }
        $threads = array();
        foreach ($data as $uid => $vals) {
            if (!in_array($uid, $thread_ref)) {
                echo_r($vals);
            }
        }
    }
    /* get the MIME structure of a message */
    function get_message_structure($uid, $filter=false) {
        if (!$this->clean($uid, 'uid')) {
            return array();
        }
        $part_num = 1;
        $struct = array();
        $command = "UID FETCH $uid BODYSTRUCTURE\r\n";
        $this->send_command($command);
        $result = $this->get_response(false, true);
        while (isset($result[0][0]) && isset($result[0][1]) && $result[0][0] == '*' && strtoupper($result[0][1]) == 'OK') {
            array_shift($result);
        }
        $status = $this->check_response($result, true);
        $response = array();
        if (!isset($result[0][4])) {
            $status = false;
        }
        if ($status) {
            if (strtoupper($result[0][4]) == 'UID')  {
                $response = array_slice($result[0], 7, -1);
            }
            else {
                $response = array_slice($result[0], 5, -1);
            }
            $response = $this->split_toplevel_result($response);
            if (count($response) > 1) {
                $struct = $this->parse_multi_part($response, 1, 1);
            }
            else {
                $struct[1] = $this->parse_single_part($response);
            }
        } 
        if ($filter) {
            return $this->filter_alternatives($struct, $filter);
        }
        return $struct;
    }
    /* get the headers for the selected message */
    function get_message_headers($uid, $message_part) {
        if (!$this->clean($uid, 'uid')) {
            return array();
        }
        if ($message_part == 1 || !$message_part) {
            $command = "UID FETCH $uid (FLAGS BODY[HEADER])\r\n";
        }
        else {
            if (!$this->clean($message_part, 'msg_part')) {
                return array();
            }
            $command = "UID FETCH $uid (FLAGS BODY[$message_part.HEADER])\r\n";
        }
        $this->send_command($command);
        $result = $this->get_response(false, true);
        $status = $this->check_response($result, true);
        $headers = array();
        $flags = array();
        if ($status) {
            foreach ($result as $vals) {
                if ($vals[0] != '*') {
                    continue;
                }
                $search = true;
                $flag_search = false;
                foreach ($vals as $v) {
                    if ($flag_search) {
                        if ($v == ')') {
                            $flag_search = false;
                        }
                        elseif ($v == '(') {
                            continue;
                        }
                        else {
                            $flags[] = $v;
                        }
                    }
                    elseif ($v != ']' && !$search) {
                        $parts = explode("\r\n", $v);
                        if (is_array($parts) && !empty($parts)) {
                            $i = 0;
                            foreach ($parts as $line) {
                                $split = strpos($line, ':');
                                if (preg_match("/^from /i", $line)) {
                                    continue;
                                }
                                if (isset($headers[$i]) && trim($line) && ($line{0} == "\t" || $line{0} == ' ')) {
                                    $headers[$i][1] .= ' '.trim($line);
                                }
                                elseif ($split) {
                                    $i++;
                                    $last = substr($line, 0, $split);
                                    $headers[$i] = array($last, trim(substr($line, ($split + 1))));
                                }
                            }
                        }
                        break;
                    }
                    if (stristr(strtoupper($v), 'BODY')) {
                        $search = false;
                    }
                    elseif (stristr(strtoupper($v), 'FLAGS')) {
                        $flag_search = true;
                    }
                }
            }
            if (!empty($flags)) {
                $headers[] = array('Flags', join(' ', $flags));
            }
        }
        return $headers;
    }
    function get_message_part_start($uid, $message_part) {
        if (!$this->clean($uid, 'uid')) {
            return false;
        }
        if ($message_part == 0) {
            $command = "UID FETCH $uid BODY[]\r\n";
        }
        else {
            if (!$this->clean($message_part, 'msg_part')) {
                return false;
            }
            $command = "UID FETCH $uid BODY[$message_part]\r\n";
        }
        $this->send_command($command);
        $result = $this->fgets(1024);
        $size = false;
        if (preg_match("/\{(\d+)\}\r\n/", $result, $matches)) {
            $size = $matches[1];
        }
        return $size;
    }
    function get_message_part_line() {
        $res = $this->fgets(1024);
        while(substr($res, -2) != "\r\n") {
            $res .= $this->fgets(1024);
        }
        if ($this->check_response(array($res))) {
            $res = false;
        }
        return $res;
    }
    function get_message_part($uid, $message_part, $raw=false, $max=false) {
        if (!$this->clean($uid, 'uid')) {
            return '';
        }
        if ($raw) {
            $command = "UID FETCH $uid BODY[]\r\n";
        }
        else {
            if (!$this->clean($message_part, 'msg_part')) {
                return '';
            }
            $command = "UID FETCH $uid BODY[$message_part]\r\n";
        }
        $this->send_command($command);
        $result = $this->get_response($max, true);
        $status = $this->check_response($result, true);
        $res = '';
        foreach ($result as $vals) {
            if ($vals[0] != '*') {
                continue;
            }
            $search = true;
            foreach ($vals as $v) {
                if ($v != ']' && !$search) {
                    if ($v == 'NIL') {
                        $res = '';
                        break 2;
                    }
                    $res = trim(preg_replace("/\s*\)$/", '', $v));
                    break 2;
                }
                if (stristr(strtoupper($v), 'BODY')) {
                    $search = false;
                }
            }
        }
        return $res;
    }
    /* perform message action */
    function message_action($uids, $action, $mailbox=false, $uid_str='') {
        $keepers = array();
        $uid_strings = array();
        if (!empty($uids)) {
            if (count($uids) > 1000) {
                while (count($uids) > 1000) { 
                    $uid_strings[] = implode(',', array_splice($uids, 0, 1000));
                }
                if (count($uids)) {
                    $uid_strings[] = implode(',', $uids);
                }
            }
            else {
                $uid_strings[] = implode(',', $uids);
            }
        }
        else {
            $uid_strings[] = $uid_str;
        }
        foreach ($uid_strings as $uid_string) {
            if ($uid_string) {
                if (!$this->clean($uid_string, 'uid_list')) {
                    return false;
                }
            }
            switch ($action) {
                case 'READ':
                    $command = "UID STORE $uid_string +FLAGS (\Seen)\r\n";
                    break;
                case 'FLAG':
                    $command = "UID STORE $uid_string +FLAGS (\Flagged)\r\n";
                    break;
                case 'UNFLAG':
                    $command = "UID STORE $uid_string -FLAGS (\Flagged)\r\n";
                    break;
                case 'ANSWERED':
                    $command = "UID STORE $uid_string +FLAGS (\Answered)\r\n";
                    break;
                case 'UNREAD':
                    $command = "UID STORE $uid_string -FLAGS (\Seen)\r\n";
                    break;
                case 'DELETE':
                    $command = "UID STORE $uid_string +FLAGS (\Deleted)\r\n";
                    break;
                case 'UNDELETE':
                    $command = "UID STORE $uid_string -FLAGS (\Deleted)\r\n";
                    break;
                case 'EXPUNGE':
                    if (is_array($uids) && !empty($uids)) {
                        $res = $this->full_search('DELETED');
                        if (!empty($res)) {
                            foreach ($res as $val) {
                                if (!in_array($val, $uids)) {
                                    $keepers[] = $val;
                                }
                            }
                            if (!empty($keepers)) {
                                $this->message_action($keepers, 'UNDELETE');
                            }
                        }
                    }
                    $command = "EXPUNGE\r\n";
                    break;
                default:
                    if (!$this->clean($mailbox, 'mailbox')) {
                        return false;
                    }
                    $command = "UID COPY $uid_string \"".$this->utf7_encode($mailbox)."\"\r\n";
                    break;
            }
            $this->send_command($command);
            $res = $this->get_response();
            $status = $this->check_response($res);
            if ($status && !empty($keepers)) {
                $this->message_action($keepers, 'DELETE');
            }
            if (!$status) {
                return $status;
            }
        }
        return $status;
    }
    function prep_folder_name($mailbox, $prefix='', $parent=false, $subs=false) {
        if ($prefix) {
            $prefix = rtrim($prefix, $_SESSION['imap_delimiter']);
        }
        if ($parent) {
            $mailbox = $parent.$_SESSION['imap_delimiter'].$mailbox;
            $prefix = false;
        }
        if ($prefix) {
            if (strtoupper(substr($mailbox, 0, (strlen($prefix) + 1))) != strtoupper($prefix.$_SESSION['imap_delimiter'])) {
                $new_box_name = str_replace(array('"'), array('\"'), $prefix.$_SESSION['imap_delimiter'].$mailbox);
            }
            else {
                $new_box_name = str_replace('"', '\"', $mailbox);
            }
        }
        else {
            $new_box_name = str_replace('"', '\"', $mailbox);
        }
        if ($subs) {
            $new_box_name .= $_SESSION['imap_delimiter'];
        }
        return $new_box_name;
    }
    function get_namespaces() {
        $data = array();
        $this->send_command("NAMESPACE\r\n");
        $res = $this->get_response();
        $this->namespace_count = 0;
        if ($this->check_response($res)) {
            if (preg_match("/\* namespace (\(.+\)|NIL) (\(.+\)|NIL) (\(.+\)|NIL)/i", $res[0], $matches)) {
                $classes = array(1 => 'personal', 2 => 'other_users', 3 => 'shared');
                foreach ($classes as $i => $v) {
                    if (trim(strtoupper($matches[$i])) == 'NIL') {
                        continue;
                    }
                    $list = str_replace(') (', '),(', substr($matches[$i], 1, -1));
                    $prefix = '';
                    $delim = '';
                    foreach (explode(',', $list) as $val) {
                        $val = trim($val, ")(\r\n ");
                        if (strlen($val) == 1) {
                            $delim = $val;
                            $prefix = '';
                        }
                        else {
                            $delim = substr($val, -1);
                            $prefix = trim(substr($val, 0, -1));
                        }
                        $this->namespace_count++;
                        $data[] = array('delim' => $delim, 'prefix' => $prefix, 'class' => $v);
                    }
                }
            }
        }
        return $data;
    }
    function delete_folder($mailbox) {
        if (!$this->clean($mailbox, 'mailbox')) {
            return true;
        }
        if ($this->read_only) {
            return 'Operation not permitted in read only mode';
        }
        $command = 'DELETE "'.str_replace('"', '\"', $this->utf7_encode($mailbox))."\"\r\n";
        $this->send_command($command);
        $result = $this->get_response(false);
        $status = $this->check_response($result, false);
        if ($status) {
            return false;
        }
        else {
            return str_replace('A'.$this->command_count, '', $result[0]);
        }
    }
    function rename_folder($prefix, $mailbox, $new_mailbox) {
        if (!$this->clean($mailbox, 'mailbox') || !$this->clean($new_mailbox, 'mailbox')) {
            return true;
        }
        if ($this->read_only) {
            return 'Operation not permitted in read only mode';
        }
        $command = 'RENAME "'.$this->prep_folder_name($this->utf7_encode($mailbox), $prefix).'" "'.
            $this->prep_folder_name($this->utf7_encode($new_mailbox), $prefix).'"'."\r\n";
        $this->send_command($command);
        $result = $this->get_response(false);
        $status = $this->check_response($result, false);
        if ($status) {
            return false;
        }
        else {
            return str_replace('A'.$this->command_count, '', $result[0]);
        }
    } 
    function create_folder($prefix, $mailbox, $parent) {
        if (!$this->clean($mailbox, 'mailbox')) {
            return true;
        }
        if ($parent) {
            if (!$this->clean($parent, 'mailbox')) {
                return true;
            }
        }
        if ($this->read_only) {
            return 'Operation not permitted in read only mode';
        }
        $command = 'CREATE "'.$this->prep_folder_name($this->utf7_encode($mailbox), $prefix, $parent).'"'."\r\n";
        $this->send_command($command);
        $result = $this->get_response(false);
        $status = $this->check_response($result, false);
        if ($status) {
            return false;
        }
        else {
            return str_replace('A'.$this->command_count, '', $result[0]);
        }
    }
    function full_search($terms) {
        if (!$this->clean($this->search_charset, 'charset') || !$this->clean($terms, 'search_str')) {
            return array();
        }
        if ($this->search_charset) {
            $charset = 'CHARSET '.strtoupper($this->search_charset).' ';
        }
        else {
            $charset = '';
        }
        $command = 'UID SEARCH '.$charset.$terms."\r\n";
        $this->send_command($command);
        $result = $this->get_response(false, true);
        $status = $this->check_response($result, true);
        $res = array();
        if ($status) {
            array_pop($result);
            foreach ($result as $vals) {
                foreach ($vals as $v) {
                    if (preg_match("/^\d+$/", $v)) {
                        $res[] = $v;
                    }
                }
            }
        }
        return $res;
    }
    function append_end() {
        $result = $this->get_response(false, true);
        $status = $this->check_response($result, true);
        return $status;
    }
    function append_feed($string, $as_is=false) {
        if ($as_is) {
            fwrite($this->handle, $string);
        }
        else {
            fwrite($this->handle, $string."\r\n");
        }
    }
    function append_start($mailbox, $size, $seen=true) {
        if (!$this->clean($mailbox, 'mailbox') || !$this->clean($size, 'uid')) {
            return false;
        }
        if ($seen) {
            $command = 'APPEND "'.$this->utf7_encode($mailbox).'" (\Seen) {'.$size."}\r\n";
        }
        else {
            $command = 'APPEND "'.$this->utf7_encode($mailbox).'" () {'.$size."}\r\n";
        }
        $this->send_command($command);
        $result = $this->fgets();
        if (substr($result, 0, 1) == '+') {
            return true;
        }
        else {
            return false;
        }
    }
    function fgets($len=false) {
        if (is_resource($this->handle)) {
            if ($len) {
                return fgets($this->handle, $len);
            }
            else {
                return fgets($this->handle);
            }
        }
        return '';
    }
    function simple_search($fld, $uids, $term) {
        if (!$this->clean($fld, 'search_str') || !$this->clean($this->search_charset, 'charset') || !$this->clean($term, 'search_str')) {
            return array();
        }
        if (!empty($uids)) {
            $uids = implode(',', $uids);
            if (!$this->clean($uids, 'uid_list')) {
                return array();
            }
            $uids = 'UID '.$uids;
        }
        else {
            $uids = 'ALL';
        }
        if ($this->search_charset) {
            $charset = 'CHARSET '.strtoupper($this->search_charset).' ';
        }
        else {
            $charset = '';
        }
        $command = 'UID SEARCH '.$charset.$uids.' '.$fld.' "'.str_replace('"', '\"', $term)."\"\r\n";
        $this->send_command($command);
        $result = $this->get_response(false, true);
        $status = $this->check_response($result, true);
        $res = array();
        if ($status) {
            array_pop($result);
            foreach ($result as $vals) {
                foreach ($vals as $v) {
                    if (preg_match("/^\d+$/", $v)) {
                        $res[] = $v;
                    }
                }
            }
        }
        return $res;
    }
    /* output debug. Called from the bottom of index.php it shows all the
       imap commands and responses, and anything manually stuffed in the
       $this->debug array. */
    function puke($full_output=false) {
        echo '<div style="margin: 20px;">';
        if (!$full_output) {
            echo '<b>IMAP commands</b><br />'.timer_display($this->commands);
            echo '<b>IMAP responses</b><br />'.timer_display($this->short_responses);
        }
        else {
            echo '<br /><b>IMAP commands</b>'.timer_display($this->commands);
            echo '<br /><b>IMAP responses</b>'.timer_display($this->short_responses);
            echo '<br /><b>Debug info</b>';
            echo_r($this->debug);
            echo '<br><b>Full response data</b><br />';
            echo_r($this->responses);
        }
        echo '<div>';
    }
    function show_cache() {
        $total = 0;
        if (isset($_SESSION['folders'])) {
            $total += strlen(serialize($_SESSION['folders']));
        }
        if (isset($_SESSION['header_cache'])) {
            $total += strlen(serialize($_SESSION['header_cache']));
        }
        if (isset($_SESSION['uid_cache'])) {
            $total += strlen(serialize($_SESSION['uid_cache']));
        }
        if (isset($_SESSION['header_cache'])) {
            $total += strlen(serialize($_SESSION['header_cache']));
        }
        echo '<br />Cache: ~'.format_size($total/1024);
    } 
    /* issue a logout and close the socket to the server */
    function disconnect() {
        $command = "LOGOUT\r\n";
        $this->send_command($command);
        $this->state = 'disconnected';
        $result = $this->get_response();
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
    function input_validate($val, $type) {
        global $imap_search_charsets;
        global $imap_keywords;
        $valid = false;
        switch ($type) {
            case 'search_str':
                if (preg_match("/^[^\r\n]+$/", $val)) {
                    $valid = true;
                }
                break;
            case 'msg_part':
                if (preg_match("/^[\d\.]+$/", $val)) {
                    $valid = true;
                }
                break;
            case 'charset':
                if (!$val || in_array(strtoupper($val), $imap_search_charsets)) {
                    $valid = true;
                }
                break;
            case 'uid':
                if (preg_match("/^\d+$/", $val)) {
                    $valid = true;
                }
                break;
            case 'uid_list';
                if (preg_match("/^(\d+\s*,*\s*|(\d+|\*):(\d+|\*))+$/", $val)) {
                    $valid = true;
                }
                break;
            case 'mailbox';
                if (preg_match("/^[^\r\n]+$/", $val)) {
                    $valid = true;
                }
                break;
            case 'keyword';
                if (in_array(strtoupper($val), $imap_keywords)) {
                    $valid = true;
                }
                break;
        }
        return $valid;
    }
    function clean($val, $type) {
        global $user;
        if (!$this->input_validate($val, $type)) {
            $user->notices[] = 'INVALID IMAP INPUT DETECTED: '.$type.' : '.$user->htmlsafe($val);
            return false;
        }
        return true;
    }
}

?>
