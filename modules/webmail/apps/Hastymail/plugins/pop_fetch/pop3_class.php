<?php

/*  pop3_class.php: POP3 routines 
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

class pop3 {
    var $server;
    var $starttls;
    var $port;
    var $ssl;
    var $auth;
    var $debug;
    var $command_count;
    var $commands;
    var $responses;
    var $connected;
    var $banner;
    var $handle;
   
    /* set defaults */ 
    function pop3() {
        $this->debug = array();
        $this->server = 'localhost';
        $this->port = 110; // ssl @ 995
        $this->ssl = false;
        $this->starttls = false;
        $this->command_count = 0;
        $this->commands = array();
        $this->responses = array();
        $this->connected = false;
        $this->state = 'started';
        $this->handle = false;
    }
    /* get server response */
    function get_response($multi_line=false) {
        if ($multi_line) {
            $res = $this->get_multi_line_response();
        }
        else {
            $res = array($this->get_single_line_response());
        }
        return $res;
    }
    /* read in a multi-line response */
    function get_multi_line_response($line_length=8192) {
        $n = -1;
        $result = array();
        if (!is_resource($this->handle)) {
            return $result;
        }
        do {
            if ($n > 0 && $result[$n] == "..\r\n") {
                $result[$n] = ".\r\n";
            }
            $n++;
            $result[$n] = fgets($this->handle, $line_length);
            while(substr($result[$n], -2) != "\r\n") {
                if (!is_resource($this->handle)) {
                    break;
                }
                $result[$n] .= fgets($this->handle, $line_length);
            }
            if ($this->is_error($result)) {
                break;
            }
            if ($n == 0) {
                $this->responses[$result[$n]] = microtime();
            }
        } while ($result[$n] != ".\r\n");
        return $result;
    }
    /* read in a single line response */
    function get_single_line_response($line_length=512) {
        if (!is_resource($this->handle)) {
            $res = '';
        }
        else {
            $res = fgets($this->handle, $line_length);
        }
        $this->responses[$res] = microtime();
        return $res;
    }
    /* send a command string to the server */
    function send_command($command) {
        if (is_resource($this->handle)) {
            fputs($this->handle, $command."\r\n");
        }
        $this->commands[trim($command)] = microtime();
    }
    /* establish a connection to the server. */
    function connect() {
        if ($this->ssl) {
            $this->server = 'tls://'.$this->server;
        } 
        $this->debug[] = 'Connecting to '.$this->server.' on port '.$this->port;
        $this->handle = @fsockopen($this->server, $this->port, $errorno, $errorstr, 30);
        if (is_resource($this->handle)) {
            $this->debug[] = 'Successfully opened port to the POP3 server';
            $this->connected = true;
            $this->state = 'connected';
            $res = $this->get_response();
            if (!empty($res)) {
                $this->banner = $res[0];
            }
            $this->commands['Connected'] = microtime();
        }
        else {
            $this->debug[] = 'Could not connect to the POP3 server';
            $this->debug[] = 'fsockopen errors #'.$errorno.'. '.$errorstr;
        }
        return $this->connected;
    }
    /* output debug */
    function puke() {
        $res = '<div style="margin: 20px;">';
        $res .= '<b>POP3 commands</b><br />'.timer_display($this->commands);
        $res .= '<b>POP3 responses</b><br />'.timer_display($this->responses);
        $res .= '<div>';
        return $res;
    }
    /* check the POP3 response code for errors */
    function is_error($response) {
        $index = count($response);
        $error = false;
        if ($index && substr($response[($index - 1)], 0, 3) == '+OK') {
            return false;
        }
        elseif ($index && substr($response[($index - 1)], 0, 4) == '-ERR') {
            $error = substr($response[($index - 1)], 5);
        }
        else {
            if (empty($response)) {
                $error = 'Empty response';
            }
            else {
                $errors = 'Unknown response: '.$response[($index - 1)];
            }
        }
        return $error;
    }
    /* quit an active pop3 session */
    function quit() {
        $this->send_command('QUIT');
        return $this->is_error($this->get_response());
    }
    /* stat a mailbox */
    function mstat() {
        $cnt = 0;
        $size = 0;
        $this->send_command('STAT');
        $res = $this->get_response();
        if ($this->is_error($res) == false) {
            if (preg_match('/^\+OK (\d+) (\d+)/', $res[0], $matches)) {
                $cnt = $matches[1]; 
                $size = $matches[2];
            }
        }
        return array('count' => $cnt, 'size' => $size);
    }
    function mlist($id=false) {
        $command = 'LIST';
        $multi = true;
        $mlist = array();
        $regex = '/^(\d+) (\d+)/';
        if ($id) {
            $command .= ' '.$id;
            $multi = false;
            $regex = '/^\+OK (\d+) (\d+)/';
        }
        $this->send_command($command);
        $res = $this->get_response($multi);
        if ($this->is_error($res) == false) {
            foreach ($res as $row) {
                if (preg_match($regex, $row, $matches)) {
                    $mlist[$matches[1]] = $matches[2];
                }
            }
        }
        return $mlist;
    }
    function retr_full($id) {
        $this->send_command('RETR '.$id);
        $res = $this->get_response(true);
        return $res;
    }
    function retr_start($id) {
        $this->send_command('RETR '.$id);
        $res = $this->get_response();
        return $this->is_error($res) == false;
    }
    function retr_feed() {
        $result = '';
        $line_length = 8192;
        $continue = true;
        $result = fgets($this->handle, $line_length);
        while(substr($result, -2) != "\r\n") {
            if (!is_resource($this->handle)) {
                break;
            }
            $result .= fgets($this->handle, $line_length);
        }
        if ($result == ".\r\n") {
            $continue = false;
            $result = false;
        }
        elseif ($result == "..\r\n") {
            $result = ".\r\n";
        }
        return array($result, $continue);
    }
    function dele($id) {
        $this->send_command('DELE '.$id);
        return $this->is_error($this->get_response()) == false;
    }
    function uidl($id=false) {
        $command = 'UIDL';
        $multi = true;
        $uidlist = array();
        $regex = '/^(\d+) (.+)/';
        if ($id) {
            $command .= ' '.$id;
            $multi = false;
            $regex = '/^\+OK (\d+) (.+)/';
        }
        $this->send_command($command);
        $res = $this->get_response($multi);
        if ($this->is_error($res) == false) {
            foreach ($res as $row) {
                if (preg_match($regex, $row, $matches)) {
                    $uidlist[$matches[1]] = trim($matches[2]);
                }
            }
        }
        return $uidlist;
    }
    function noop() {
        $this->send_command('NOOP');
        return $this->is_error($this->get_response()) == false;
    }
    function rset() {
        $this->send_command('RSET');
        return $this->is_error($this->get_response()) == false;
    }
    function user($user) {
        $this->send_command('USER '.$user);
        return $this->is_error($this->get_response()) == false;
    }
    function pass($pass) {
        $this->send_command('PASS '.$pass);
        return $this->is_error($this->get_response()) == false;
    }
    function auth($user, $pass) {
        global $phpversion;
        if ($this->starttls) {
            if ($phpversion >= 5) {
                $this->send_command('STLS');
                if ($this->is_error($this->get_response()) == false && is_resource($this->handle)) {
                    stream_socket_enable_crypto($this->handle, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                }
            }
        }
        if (preg_match('/<[0-9.]+@[^>]+>/', $this->banner, $matches)) {
            $res = $this->apop($user, $pass, $matches[0]);
        }
        else {
            if ($this->user($user)) {
                $res = $this->pass($pass);
            }
        }
        return $res;
    }
    function apop($user, $pass, $challenge) {
        $this->send_command('APOP '.$user.' '.md5($challenge.$pass));
        return $this->is_error($this->get_response()) == false;
    }
}

function pop3_session($tools, $user, $pass, $port, $host, $ssl, $starttls, $keep, $destination, $acct_id, $uid_map) {
    $pop3 = hm_new('pop3'); 
    $pop3->server = $host;
    $pop3->port = $port;
    $pop3->starttls = $starttls;
    $pop3->ssl = $ssl;
    $msg_count = 0;
    $read_size = 0;
    $seen = false;
    $msg_list = array();
    if ($pop3->connect()) {
        if ($pop3->auth($user, $pass)) {
            $msg_list = $pop3->mlist();
            $uid_list = $pop3->uidl();
            foreach ($msg_list as $id => $size) {
                $headers = true;
                if ($keep) {
                    if (isset($uid_map[$acct_id][$uid_list[$id]])) {
                        continue;
                    }
                    $uid_map[$acct_id][$uid_list[$id]] = false;
                }
                $msg_count++;
                if ($pop3->retr_start($id)) {
                    if ($tools->imap_append_start($destination, $size, false)) {
                        $continue = true;
                        while ($continue) {
                            list($line, $continue) = $pop3->retr_feed();
                            $read_size += strlen($line);
                            if ($keep && $headers) {
                                $dt = date_check($line);
                                if ($dt) {
                                    $uid_map[$acct_id][$uid_list[$id]] = $dt;
                                }
                            }
                            if (!trim($line)) {
                                $headers = false;
                            }
                            if ($continue && strlen($line)) {
                                $tools->imap_append_feed($line, true);
                            }
                            else {
                                $tools->imap_append_feed("\r\n", true);
                                $tools->imap_append_end();
                                break;
                            }
                        }
                    }
                }
                if (!$keep) {
                    $pop3->dele($id);
                } 
            }
            if ($keep) {
                $new_uid_map = array();
                foreach ($msg_list as $id => $size) {
                    if (isset($uid_map[$acct_id][$uid_list[$id]])) {
                        $new_uid_map[$uid_list[$id]] = $uid_map[$acct_id][$uid_list[$id]];
                    }
                    else {
                        $new_uid_map[$uid_list[$id]] = false;
                    }
                }
                $uid_map[$acct_id] = $new_uid_map;
            }
            $pop3->quit();
        }
    }
    return array($msg_count, $uid_map);
}
function date_check($str) {
    $res = false;
    if (substr(strtolower($str), 0, 4) == 'date') {
        if (preg_match("/^date: (.+)$/i", $str, $matches)) {
            $dt = @strtotime($matches[1]);
            if ($dt != -1 && $dt) {
                $res = $dt;
            }
        }
    }
    return $res;
}
function save_pop_fetch_map($tools, $uid_map, $uid_map_dir) {
    if ($uid_map_dir) {
        $file = $uid_map_dir.$_SESSION['user_data']['username'].'.uid_map';
        if ($fh = @fopen($file, 'w')) {
            fwrite($fh, serialize($uid_map));
            fclose($fh);
        }
    }
    else { 
        $tools->save_setting('pop_fetch_map', $uid_map);
    }
}
function get_pop_fetch_map($tools, $uid_map_dir) {
    if ($uid_map_dir) {
        $file = $uid_map_dir.$_SESSION['user_data']['username'].'.uid_map';
        $data = @unserialize(join('', file($file)));
    }
    else {
        $data = $tools->get_setting('pop_fetch_map');
    }
    if (!is_array($data)) {
        $data = array();
    }
    return $data;
}
?>
