<?php

/*  smtp_class.php: SMTP routines 
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

class smtp {
    var $server;
    var $starttls;
    var $port;
    var $tls;
    var $auth;
    var $handle;
    var $debug;
    var $hostname;
    var $command_count;
    var $commands;
    var $responses;
    var $smtp_err;
    var $banner;
    var $capability;
    var $connected;
    var $state;
    var $crlf;
    var $line_length;
    var $username;
    var $password;
   
    /* set defaults */ 
    function smtp() {
        global $conf;
        $this->hostname = $conf['host_name'];
        $this->debug = array();
        if (isset($conf['smtp_server'])) {
            $this->server = $conf['smtp_server'];
        }
        else {
            $this->server = '127.0.0.1';
        }
        if (isset($conf['smtp_port'])) {
            $this->port = $conf['smtp_port'];
        }
        else {
            $this->port = 25;
        }
        if (isset($conf['smtp_starttls'])) {
            $this->starttls = $conf['smtp_starttls'];
        }
        else {
            $this->starttls = false;
        }
        if (isset($conf['smtp_tls']) && $conf['smtp_tls']) {
            $this->tls = true;
        }
        else {
            $this->tls = false;
        }
        $this->smtp_err = '';
        $this->supports_tls = false;
        $this->auth = false;
        $this->supports_auth = array();
        $this->handle = false;
        $this->state = 'started';
        $this->command_count = 0;
        $this->commands = array();
        $this->responses = array();
        $this->banner = '';
        $this->crlf = "\r\n";
        $this->capability = '';
        $this->line_length = 2048;
        $this->connected = false;
        $this->username = false;
        $this->password = false;
        $this->max_message_size = 0; //in bytes; 0 = no limit
    }

    /* send command to the server. Append "\r\n" to the end. */
    function send_command($command) {
        if (is_resource($this->handle)) {
            fputs($this->handle, $command.$this->crlf);
        }
        $this->commands[] = trim($command);
    }

    /* loop through "lines" returned from smtp and parse
       them. It can return the lines in a raw format, or 
       parsed into atoms. 
    */
    function get_response($chunked=true) {
        $n = -1;
        $result = array();
        $chunked_result = array();
        do {
            $n++;
            if (!is_resource($this->handle)) {
                break;
            }
            $result[$n] = fgets($this->handle, $this->line_length);
            $chunks = $this->parse_line($result[$n]);
            if ($chunked) {
                $chunked_result[] = $chunks;
            }
            if (!trim($result[$n])) {
                unset($result[$n]);
                break;
            }
            $cont = false;
            if (strlen($result[$n]) > 3 && substr($result[$n], 3, 1) == '-') {
                $cont = true;
            }
        } while ($cont);
        $this->responses[] = $result;
        if ($chunked) {
            $result = $chunked_result;
        }
        return $result;
    }

    /* parse out a line */
    function parse_line($line) {
        $parts = array();

        $code = substr($line, 0, 3);
        $parts[] = $code;

        $remainder = explode(' ',substr($line, 4));
        $parts[] = $remainder;

        return $parts;
        
    }
    /* Checks if the numeric response matches the code in $check. 
       The return value is simalar to strcmp
       Returns <0 if $check is less than the response
       Returns  0 if $check is equal to the response
       Returns >0 if $check is greater than the response
    */
    function compare_response($chunked_response, $check) {
        $size = count($chunked_response);
        if ($size) {
            $last = $chunked_response[$size-1];
            $code = $last[0];
        }
        else {
            $code = false;
        }
        $return_val = strcmp($check,$code);
        if ($return_val) {
            if (isset($chunked_response[0][1])) {
                $this->smtp_err = join(' ', $chunked_response[0][1]);
            }
        }
        return $return_val;
    }

    /* determine what capabilities the server has.
       Pass it the chunked response from EHLO  */
    function capabilities($ehlo_response) {
        foreach($ehlo_response as $line) {
            $feature = trim($line[1][0]);
            switch(strtolower($feature)) {
                case 'starttls': // supports starttls
                    $this->supports_tls = true;
                    break;
                case 'auth.': // supported auth mechanisims
                    $auth_mecs = array_slice($line[1], 1);
                    $this->supports_auth = $auth_mecs;
                    break;
                case 'size': // advisary maximum message size
                    if(isset($line[1][1]) && is_numeric($line[1][1])) {
                        $this->max_message_size = $line[1][1];
                    }
                    break;
            }
        }

    }

    /* establish a connection to the server. */
    function connect($servers_attempted=array()) {
        global $smtp_server_pool;
        global $user;
        global $phpversion;
        $result = 'An error occured connecting to the SMTP server';

        if ($smtp_server_pool) {
            // get the list of available smtp servers to connect to
            $available_servers = array_diff(explode(',', $this->server), $servers_attempted);
            $server = trim($available_servers[array_rand($available_servers)]);
        }
        else {
            $server = $this->server;
        }

        if ($this->tls) {
            $server = 'tls://'.$server;
        } 
        $this->debug[] = 'Connecting to '.$server.' on port '.$this->port;
        $this->handle = @fsockopen($server, $this->port, $errorno, $errorstr, 30);
        if (is_resource($this->handle)) {
            $this->debug[] = 'Successfully opened port to the SMTP server';
            $this->connected = true;
            $this->state = 'connected';
        }
        else {
            $this->debug[] = 'Could not connect to the SMTP server';
            $this->debug[] = 'fsockopen errors #'.$errorno.'. '.$errorstr;
            $result = 'Could not connect to the configured SMTP server';
        }
        $this->banner = $this->get_response();
        $command = 'EHLO '.$this->hostname;
        $this->send_command($command);
        $response = $this->get_response();
        $this->capabilities($response);
        if ($this->starttls && $this->supports_tls) {
            if ($phpversion < 5) {
                $result = 'You must have PHP5 to use STARTTLS';
            }
            else {
                $command = 'STARTTLS';
                $this->send_command($command);
                $response = $this->get_response();
                if ($this->compare_response($response, '220') != 0) {
                    $result = 'An error occured during the STARTTLS command';
                }
            }
            if(isset($user->certfile) && $user->certfile) {
                stream_context_set_option($this->handle, 'tls', 'local_cert', $user->certfile);
                if($user->certpass) {
                    stream_context_set_option($this->handle, 'tls', 'passphrase', $user->certpass);
                }
            }
            stream_socket_enable_crypto($this->handle, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $command = 'EHLO '.$this->hostname;
            $this->send_command($command);
            $response = $this->get_response();
            $this->capabilities($response);
        }
        if($this->compare_response($response,'250') != 0) {
            $result = 'An error occured during the EHLO command';
        }
        else {
            if($this->auth) {
                //$mech = $this->choose_auth();
                $mech = $this->auth;
                $result = $this->authenticate($this->username, $this->password, $mech);
            }
            else {
                if ($this->state == 'connected') {
                    $result = false;
                }
            }
        }

        // failed to connect
        if ( $this->state != 'connected' && $this->state != 'authed' ) {

            // recurse if there are more smtp servers to try connecting to
            if ($smtp_server_pool && count($available_servers) > 1 ) {
                array_push($servers_attempted, $server);
                return $this->connect($servers_attempted);
            }

        }

        return $result;
    }


    /* Choose an auth mech to use.  The mech choosen is the most secure
        of the intersection of what we support and what the server supports.
        The user may optionally reduce the list of what we support, to 
        eliminate the use of unwanted mechs (ie, PLAIN).  If there is no
        intersection, the last mech (least preferred) is choosen, since
        this will generally be considered the most comptabile for a last
        ditch effort.
    */ 
    function choose_auth() {
        global $user;
        $requested = array('cram-md5','login','plain');
        if (!empty($user->smtp_mechs)) {
            $requested = array();
            foreach($user->smtp_mechs as $m) {
                $m = strtolower($m);
                if($m == 'external' ||
                   $m == 'cram-md5' ||
                   $m == 'login' ||
                   $m == 'plain') {
                     $requested[] = $m;
                }
            }
        }
        else {
            if($this->tls && $this->cert) {
                array_unshift($requested,'external');
            }
        }
        $intersect = array_intersect($requested,$this->supports_auth);
        if(count($intersect) > 0) {
            return $intersect[0];
        }
        // No common mechs, so choose the last of the requested mechs
        return $requested[ count($requested)-1 ];
    }
         
          

    /* authenticate the username and password to the server */
    function authenticate($username, $password, $mech) {
        $result = false;
        switch (strtolower($mech)) {
            case 'external':
                $command = 'AUTH EXTERNAL '.base64_encode($username);
                $this->send_command($command);
                break;
            case 'cram-md5':
                $command = 'AUTH CRAM-MD5';
                $this->send_command($command);
                $response = $this->get_response();
                if (empty($response) || !isset($response[0][1][0]) || $this->compare_response($response,'334') != 0) {
                    $result = 'FATAL: SMTP server does not support AUTH CRAM-MD5';
                }
                else {
                    $challenge = base64_decode(trim($response[0][1][0]));
                    $password .= str_repeat(chr(0x00), (64-strlen($password)));
                    $ipad = str_repeat(chr(0x36), 64);
                    $opad = str_repeat(chr(0x5c), 64);
                    $digest = bin2hex(pack('H*', md5(($password ^ $opad).pack('H*', md5(($password ^ $ipad).$challenge)))));
                    $command = base64_encode($username.' '.$digest);
                    $this->send_command($command);
                }
                break;
            case 'login':
                $command = 'AUTH LOGIN';
                $this->send_command($command);
                $response = $this->get_response();
                if (empty($response) || $this->compare_response($response,'334') != 0) {
                    $result =  'FATAL: SMTP server does ont support AUTH LOGIN';
                }
                else {
                    $command = base64_encode($username);
                    $this->send_command($command);
                    $response = $this->get_response();
                    if (empty($response) || $this->compare_response($response,'334') != 0) {
                        $result = 'FATAL: SMTP server does not support AUTH LOGIN';
                    }
                    $command = base64_encode($password);
                    $this->send_command($command);
                }
                break;
            case 'plain':
                $command = 'AUTH PLAIN '.base64_encode("\0".$username."\0".$password);
                $this->send_command($command);
                break;
            default:
                $result = 'FATAL: Unknown SMTP AUTH mechanism: '.$mech;
                exit;
        }
        if (!$result) {
            $result = 'An error occured authenticating to the SMTP server';
            $res = $this->get_response();
            if ($this->compare_response($res, '235') == 0) {
                $this->state = 'authed';
                $result = false;
            }
            else {
                $result = 'Authorization failure';
                if (isset($res[0][1])) {
                    $result .= ': '.implode(' ', $res[0][1]);
                }
            }
        }
        return $result;
    }

    /* Send a message */
    function send_message($from, $recipients, $message) {
        global $conf;
        global $fd;
        global $max_outbound_recipients;
        if ($max_outbound_recipients && count($recipients) >= $max_outbound_recipients) {
            return 'Maximum number of recipients exceeded, sending canceled';
        }
        $this->clean($from);
        $command = 'MAIL FROM:<'.$from.'>';
        $this->send_command($command);
        $res = $this->get_response();
        $bail = false;
        $result = 'An error occured sending the message';
        if(is_array($recipients)) {
            foreach($recipients as $rcpt) {
                $this->clean($rcpt);
                $command = 'RCPT TO:<'.$rcpt.'>';
                $this->send_command($command);
                $res = $this->get_response();
                if ($this->compare_response($res, '250') != 0) {
                    $bail = true;
                    break;
                }
            }
        }
        else {
            $this->clean($recipients);
            $command = 'RCPT TO:<'.$recipients.'>';
            $this->send_command($command);
            $res = $this->get_response();
            if ($this->compare_response($res, '250') != 0) {
                $bail = true;
            }
        }
        if (!$bail) {
            $command = 'DATA';
            $this->send_command($command);
            $res = $this->get_response();
            if ($this->compare_response($res, '354') != 0) {
                $result = 'An error occured during the DATA command';
            }
            else {
                $command = $message->output_smtp_message();
                $this->send_command($command);
                if (isset($_SESSION['attachments'][$message->compose_session]) && !empty($_SESSION['attachments'][$message->compose_session])) {
                    $path = $conf['attachments_path'];
                    foreach ($_SESSION['attachments'][$message->compose_session] as $i => $v) {
                        $headers = $message->build_part_header($v['realname'], $v['type'], $v['encoding']);
                        if (substr($path, -1) != $fd) {   
                            $filename = $path.$fd.$i;
                        }   
                        else {      
                            $filename = $path.$i;
                        }   
                        if (is_readable($filename)) {
                            $input_file = fopen($filename, 'r');
                            if (is_resource($input_file)) {
                                $this->send_command($headers);
                                while (!feof($input_file)) {
                                    $this->send_command(rtrim(fgets($input_file, 1024), "\r\n"));
                                }
                                fclose($input_file);
                            }
                        }
                    }
                    $this->send_command('--'.$message->boundry."--\r\n");
                }
                $command = $this->crlf.'.';
                $this->send_command($command);
                $res = $this->get_response();
                if ($this->compare_response($res, '250') == 0) {
                    $result = false;
                }
                else {
                    $result = 'An error occured sending the message DATA';
                }
            }
        }
        else {
            $result = 'An error occured during the RCPT command';
        }
        return $result;
    }

    function puke($commands_only=false) {
        if ($commands_only) {
            echo_r($this->commands);
            echo_r($this->responses);
        }
        else {
            echo_r($this->debug);
            echo_r($this->commands);
            echo_r($this->responses);
        }
    } 

    /* issue a logout and close the socket to the server */
    function disconnect() {
        $command = 'QUIT';
        $this->send_command($command);
        $this->state = 'disconnected';
        $result = $this->get_response();
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
    function clean($val) {
        if (!preg_match("/^[^\r\n]+$/", $val)) {
            echo_r("INVALID SMTP INPUT DETECTED: <b>$val</b>");
            exit;
        }
    }
}

class mime {
    var $headers;
    var $body;
    var $to;
    var $cc;
    var $bcc;
    var $from;
    var $from_address;
    var $reply_to;
    var $in_reply_to;
    var $date;
    var $references;
    var $recipients;
    var $message_id;
    var $subject;
    var $boundry;
    var $text_format;
    var $text_encoding;
    var $header_str;
    var $alt_part;
    var $alt_part_mime;
    var $alt_part_encoding;
    var $alt_part_charset;
    var $allow_unqualified_addresses;
    var $compose_session;
    
    function mime($c_session) {
        $this->subject = '';
        $this->headers = array();
        $this->body = '';
        $this->alt_part = false;
        $this->alt_part_mime = false;
        $this->compose_session = $c_session;
        $this->alt_part_encoding = false;
        $this->alt_part_charset = false;
        $this->header_str = '';
        $this->to = '';
        $this->allow_unqualified_addresses = true;
        $this->cc = '';
        $this->text_format = 0;
        if (isset($_SESSION['user_settings']['compose_text_format'])) {
            $this->text_format = $_SESSION['user_settings']['compose_text_format'];
        }
        $this->text_encoding = 0;
        if (isset($_SESSION['user_settings']['compose_text_encoding'])) {
            $this->text_encoding = $_SESSION['user_settings']['compose_text_encoding'];
        }
        $this->bcc = '';
        $this->from = '';
        $this->from_address = '';
        $this->reply_to = '';
        $this->in_reply_to = '';
        $this->boundry = '--=='.md5(uniqid(rand(),1));
        $this->recipients = array();
        $this->date = '';
        $this->references = '';
        $this->message_id = '';
    }
    function reset() {
        $this->mime();
    }
    function set_header($name, $value) {
        if (trim($value)) {
            $value = trim(preg_replace("/(\r|\n|\t)/m", '', $value));
            $this->headers[$name] = $value;
            if (isset($this->{$name})) {
                $this->{$name} = $value;
            }
        }
    }
    function build_headers() {
        global $conf;
        $this->set_header('to', $this->encode_header_fld($this->to));
        $this->set_header('cc', $this->encode_header_fld($this->cc));
        $this->set_header('reply_to', $this->encode_header_fld($this->reply_to));
        $this->set_header('in_Reply_To', $this->encode_header_fld($this->in_reply_to));
        $this->set_header('references', $this->encode_header_fld($this->references));
        $this->set_header('from', $this->encode_header_fld($this->from));
        $this->set_header('subject', $this->encode_header_fld($this->subject));
        $this->set_header('date', date("r"));
        $this->set_header('content_Type', 'text/plain; charset=UTF-8');
        $this->set_header('MIME-Version', '1.0');
        if (!$this->message_id) {
            $this->set_header('message_id', '<'.md5(uniqid(rand(),1)).'@'.$conf['host_name'].'>');
        }
        else {
            $this->set_header('message_id', $this->message_id);
        }
    }
    function encode_header_fld($input) {
        global $user;
        $res = array();
        $input = trim($input, ',; ');
        if (strstr($input, ' ')) {
            $parts = explode(' ', $input);
        }
        else {
            $parts[] = $input;
        }
        foreach ($parts as $v) {
            if (preg_match('/(?:[^\x00-\x7F])/',$v) === 1) {
                $leading_quote = false;
                $trailing_quote = false;
                if (substr($v, 0, 1) == '"') {
                    $v = substr($v, 1);
                    $leading_quote = true;
                }
                if (substr($v, -1) == '"') {
                    $trailing_quote = true;
                    $v = substr($v, 0, -1);
                }
                $enc_val = '=?UTF-8?B?'.base64_encode($v).'?=';
                if ($leading_quote) {
                    $enc_val = '"'.$enc_val;
                }
                if ($trailing_quote) {
                    $enc_val = $enc_val.'"';
                }
                $res[] = $enc_val;
            }
            else {
                if ($user->user_action->match_email($v)) {
                    $res[] = '<'.$v.'>';
                }
                else {
                    $res[] = $v;
                }
            }
        }
        $string = preg_replace("/\s{2,}/", ' ', trim(implode(' ', $res)));
        return $string;
    }
    function get_recipient_addresses() {
        global $user;
        $res = array();
        foreach (array($this->to, $this->cc, $this->bcc) as $v) {
            $v = trim(preg_replace("/(\r|\n|\t)/m", ' ', $v));
            $v = preg_replace("/^(\"[^\"\\\]*(?:\\\.[^\"\\\]*)*\")/", ' ', $v);
            $v = str_replace(array(',', ';'), array(' , ', ' ; '), $v); 
            $v = preg_replace("/\s+/", ' ', $v);
            $bits = explode(' ', $v);
            foreach ($bits as $val) {
                $val = trim($val);
                if (!$val) {
                    continue;
                }
                if (strstr($val, '@')) {
                    $address = ltrim(rtrim($val ,'>'), '<');
                    if ($user->user_action->match_email($address)) {
                        $res[] = $address;
                    }
                }
            }
            if ($this->allow_unqualified_addresses) {
                $bits = preg_split("/(;|,)/", $v);
                foreach ($bits as $val) {
                        $val = trim($val);
                    if (!strstr($val, ' ') && !strstr($val, '@') && strlen($val) > 2) {
                        $res[] = $val;
                    }
                }
            }
        }
        $this->recipients = $res;
        return $res;
    }
    function prep_message_body($type) {
        if ($this->body) {
            switch ($this->text_format) {
                case 1:     // flowed
                    $message = trim($this->body);
                    $message = str_replace("\r\n", "\n", $message);
                    $lines = explode("\n", wordwrap($message, 79, " \n"));
                    $new_lines = array();
                    foreach($lines as $line) {
                        $line = trim($line, "\r\n")."\r\n";
                        if ($type == 'smtp') {
                            $new_lines[] = preg_replace("/^\.\r\n/", "..\r\n", $line);
                        }
                        else {
                            $new_lines[] = $line;
                        }
                    }
                    $this->headers['content_Type'] .= '; format=flowed';
                    $this->body = implode('', $new_lines);
                    break;
                case 2:     // preformatted
                    $message = str_replace("\r\n", "\n", $this->body);
                    $lines = explode("\n", $message);
                    $new_lines = array();
                    foreach($lines as $line) {
                        $line = trim($line, "\r\n")."\r\n";
                        if ($type == 'smtp') {
                            $new_lines[] = preg_replace("/^\.\r\n/", "..\r\n", $line);
                        }
                        else {
                            $new_lines[] = $line;
                        }
                    }
                    $this->headers['content_Type'] .= '; format=X-preformatted';
                    $this->body = implode('', $new_lines);
                    break;
                default:    // fixed
                    $message = trim($this->body);
                    $message = str_replace("\r\n", "\n", $message);
                    $lines = explode("\n", wordwrap($message, 79, "\n"));
                    $new_lines = array();
                    foreach($lines as $line) {
                        $line = trim($line, "\r\n")."\r\n";
                        if ($type == 'smtp') {
                            $new_lines[] = preg_replace("/^\.\r\n/", "..\r\n", $line);
                        }
                        else {
                            $new_lines[] = $line;
                        }
                    }
                    $this->headers['content_Type'] .= '; format=fixed';
                    $this->body = implode('', $new_lines);
                    break;
            }
            switch ($this->text_encoding) {
                case 1:     // quoted-printable
                    $this->set_header('content_Transfer_Encoding', 'quoted-printable');
                    $this->body = $this->qp_encode($this->body);
                    break;
                case 2:     // base64
                    $this->set_header('content_Transfer_Encoding', 'base64');
                    $this->body = chunk_split(base64_encode($this->body));
                    break;
                default;    // 8bit
                    $this->set_header('content_Transfer_Encoding', '8bit');
                    break;
            }
        }
        else {
            $this->set_header('content_Transfer_Encoding', '8bit');
        }
    }
    function qp_encode($string) {
        $string = str_replace("\r\n", "\n", $string);
        $lines = explode("\n", $string, 78);
        $res = array();
        $new_lines = array();
        foreach ($lines as $v) {
            $new_line = '';
            $char_count = 0;
            while ($v) {
                $char = substr($v, 0, 1);
                $ord = ord($char);
                $v = substr($v, 1);
                switch (true) {
                    case ($ord > 32 && $ord < 61) || ($ord > 61 && $ord < 127):
                        $new_line .= $char;
                        $char_count++;
                        break;
                    case $ord == 9:
                    case $ord == 32:
                        $new_line .= $char;
                        break;
                    default:
                        $new_line .= '='.strtoupper(dechex($ord));
                        $char_count += 3;
                        break;
                }
                if ($char_count > 72) {
                    $new_lines[] = $new_line.'=';
                    $char_count = 0;
                    $new_line = '';
                }
            }
            $new_lines[] = $new_line;
        }
        $string = implode("\r\n", $new_lines);
        return $string;
    }
    function output_smtp_message() {
        $this->build_headers();
        $this->prep_message_body('smtp');
        $this->header_str = '';
        if (isset($_SESSION['attachments'][$this->compose_session]) && !empty($_SESSION['attachments'][$this->compose_session])) {
            $body_atts = $this->headers['content_Type'];
            $body_encoding = $this->headers['content_Transfer_Encoding'];
            $this->set_header('content_Type', 'multipart/mixed; boundary="'.$this->boundry.'"');
            $this->set_header('content_Transfer_Encoding', '8bit');
            if ($this->alt_part) {
                $boundry = '--=='.md5(uniqid(rand(),1));
                $body = '--'.$this->boundry."\r\nContent-Type: multipart/alternative; boundary=\"$boundry\"\r\n\r\n";
                $body .= '--'.$boundry."\r\nContent-Type: ".$body_atts."\r\nContent-Transfer-Encoding: ".$body_encoding."\r\n\r\n".$this->body."\r\n";
                $body .= '--'.$boundry."\r\nContent-Type: ".$this->alt_part_mime."\r\nContent-Transfer-Encoding: ".$this->alt_part_encoding."\r\n\r\n".$this->alt_part."\r\n\r\n";
                $body .= '--'.$boundry."--\r\n";
            }
            else {
                $body = '--'.$this->boundry."\r\nContent-Type: ".$body_atts."\r\nContent-Transfer-Encoding: ".$body_encoding."\r\n\r\n".$this->body."\r\n";
            }
        }
        elseif ($this->alt_part) {
            $body_atts = $this->headers['content_Type'];
            $body_encoding = $this->headers['content_Transfer_Encoding'];
            $this->set_header('content_Type', 'multipart/mixed; boundary="'.$this->boundry.'"');
            $this->set_header('content_Transfer_Encoding', '8bit');
            $boundry = '--=='.md5(uniqid(rand(),1));
            $body = '--'.$this->boundry."\r\nContent-Type: multipart/alternative; boundary=\"$boundry\"\r\n\r\n";
            $body .= '--'.$boundry."\r\nContent-Type: ".$body_atts."\r\nContent-Transfer-Encoding: ".$body_encoding."\r\n\r\n".$this->body."\r\n";
            $body .= '--'.$boundry."\r\nContent-Type: ".$this->alt_part_mime."\r\nContent-Transfer-Encoding: ".$this->alt_part_encoding."\r\n\r\n".$this->alt_part."\r\n\r\n";
            $body .= '--'.$boundry."--\r\n--".$this->boundry."--\r\n";
        }
        else {
            $body = $this->body;
        }
        foreach ($this->headers as $i => $v) {
            $this->header_str .= ucfirst(str_replace('_', '-', $i)).': '.rtrim(wordwrap($v, 78, "\r\n        "))."\r\n";
        }
        return $this->header_str."\r\n".$body;
    }
    function output_imap_message() {
        $this->build_headers();
        $this->body = str_replace("\r\n..\r\n", "\r\n.\r\n", $this->body);
        $this->header_str = '';
        if (isset($_SESSION['attachments'][$this->compose_session]) && !empty($_SESSION['attachments'][$this->compose_session])) {
            $body_atts = $this->headers['content_Type'];
            $body_encoding = $this->headers['content_Transfer_Encoding'];
            $this->set_header('content_Type', 'multipart/mixed; boundary="'.$this->boundry.'"');
            $this->set_header('content_Transfer_Encoding', '8bit');
            if ($this->alt_part) {
                $boundry = '--=='.md5(uniqid(rand(),1));
                $body = '--'.$this->boundry."\r\nContent-Type: multipart/alternative; boundary=\"$boundry\"\r\n\r\n";
                $body .= '--'.$boundry."\r\nContent-Type: ".$body_atts."\r\nContent-Transfer-Encoding: ".$body_encoding."\r\n\r\n".$this->body."\r\n";
                $body .= '--'.$boundry."\r\nContent-Type: ".$this->alt_part_mime."\r\nContent-Transfer-Encoding: ".$this->alt_part_encoding."\r\n\r\n".$this->alt_part."\r\n\r\n";
                $body .= '--'.$boundry."--\r\n";
            }
            else {
                $body = '--'.$this->boundry."\r\nContent-Type: ".$body_atts."\r\nContent-Transfer-Encoding: ".$body_encoding."\r\n\r\n".$this->body."\r\n";
            }
        }
        elseif ($this->alt_part) {
            $body_atts = $this->headers['content_Type'];
            $body_encoding = $this->headers['content_Transfer_Encoding'];
            $this->set_header('content_Type', 'multipart/alternative; boundary="'.$this->boundry.'"');
            $this->set_header('content_Transfer_Encoding', '8bit');
            $boundry = '--=='.md5(uniqid(rand(),1));
            $body = '--'.$this->boundry."\r\nContent-Type: multipart/alternative; boundary=\"$boundry\"\r\n\r\n";
            $body .= '--'.$boundry."\r\nContent-Type: ".$body_atts."\r\nContent-Transfer-Encoding: ".$body_encoding."\r\n\r\n".$this->body."\r\n";
            $body .= '--'.$boundry."\r\nContent-Type: ".$this->alt_part_mime."\r\nContent-Transfer-Encoding: ".$this->alt_part_encoding."\r\n\r\n".$this->alt_part."\r\n\r\n";
            $body .= '--'.$boundry."--\r\n--".$this->boundry."--\r\n";
        }
        else {
            $body = $this->body;
        }
        foreach ($this->headers as $i => $v) {
            $this->header_str .= ucfirst(str_replace('_', '-', $i)).': '.rtrim(wordwrap($v, 78, "\r\n        "))."\r\n";
        }
        return $this->header_str."\r\n".$body;
    }
    function build_part_header($filename, $type, $encoding) {
        return '--'.$this->boundry."\r\nContent-Type: ".$type.'; name="'.$filename."\"\r\n".
               "Content-Disposition: attachment; filename=\"".$filename."\"\r\n".
               "Content-Transfer-Encoding: $encoding\r\n";
    }
    function get_imap_message_size($size) {
        if (isset($_SESSION['attachments'][$this->compose_session]) && !empty($_SESSION['attachments'][$this->compose_session])) {
            foreach ($_SESSION['attachments'][$this->compose_session] as $i => $v) {
                $headers = $this->build_part_header($v['realname'], $v['type'], $v['encoding']);
                $size += strlen($headers) + 2;
                $size += $v['size'];
            }
            $size += strlen($this->boundry) + 6;
        }
        return $size;
    }
} 
?>
