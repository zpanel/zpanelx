<?php

/*  db.php: Database wrapper 
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
$db_include_path = '';
/* connect/disconnect and setup sql servers for */
class fw_db_connections {
    function add_write_server($user, $pass, $host, $db) {
        $this->write_servers[] = array($user, $pass, $host, $db);
    }
    function add_read_server($user, $pass, $host, $db) {
        $this->read_servers[] = array($user, $pass, $host, $db);
    }
    function connect() {
        global $user;
        global $db_include_path;
        if ($this->pear_type == 'MDB2') {
            require_once($db_include_path.'MDB2.php');
            $this->mode = 'MDB2';
            $this->fetch_mode = MDB2_FETCHMODE_ASSOC;
        }
        else {
            require_once($db_include_path.'DB.php');
            $this->mode = 'DB';
            $this->fetch_mode = DB_FETCHMODE_ASSOC;
        }
        if ($this->db_split) {
            $this->debug[] = 'configuration: db_split is set to true, both read and write servers required';
        }
        else {
            $this->debug[] = 'configuration: db_split is set to false, only one server for read/write required';
        }
        if ($this->random_connect_read) {
            $this->debug[] = 'configuration: Read server order being randomized';
        }
        else {
            $this->debug[] = 'configuration: Maintaining read server order';
        }
        if ($this->random_connect_write) {
            $this->debug[] = 'configuration: Write server order being randomized';
        }
        else {
            $this->debug[] = 'configuration: Maintaining write server order';
        }
        if (empty($this->read_servers)) {
            $this->debug[] = 'connecting: No sql read servers defined';
        }
        else {
            if ($this->random_connect_read) {
                shuffle($this->read_servers);
            }
            foreach ($this->read_servers as $v) {
                $dsn = $this->db_type.'://'.$v[0].':'.$v[1].'@'.$v[2].'/'.$v[3];
                if ($this->pear_type == 'MDB2') {
                    $db_read =& MDB2::Connect($dsn, array('persistent' => $this->persistent));
                }
                else {
                    $db_read = DB::Connect($dsn, $this->persistent);
                }
                $this->db_read = $db_read;
                if (PEAR::isError($this->db_read) || !($this->db_read)) {
                    $this->debug[] = 'connecting: UNABLE TO CONNECT to read DB: '.$v[2].' ('.$this->db_type.') as '.$v[0].' using PEAR '.$this->pear_type;
                }
                else {
                    $this->debug[] = 'connecting: Connected to read DB: '.$v[2].' ('.$this->db_type.') as '.$v[0].' using PEAR '.$this->pear_type;
                    $this->current_read = $v;
                    break;
                }
            }
            if (!$this->db_read || PEAR::isError($this->db_read)) {
                $this->debug[] = 'connecting: Could not connect to any configured read servers';
            }
            else {
                if ($this->db_split) {
                    if (empty($this->write_servers)) {
                        $this->debug[] = 'connecting: No sql write servers defined';
                    }
                    else {
                        if ($this->random_connect_write) {
                            shuffle($this->write_servers);
                        }
                        foreach ($this->write_servers as $v) {
                            $dsn = $this->db_type.'://'.$v[0].':'.$v[1].'@'.$v[2].'/'.$v[3];
                            if ($this->pear_type == 'MDB2') {
                                $this->db_write =& MDB2::Connect($dsn, true);
                            }
                            else {
                                $this->db_write =& DB::Connect($dsn, true);
                            }
                            if (PEAR::isError($this->db_write) || !($this->db_write)) {
                                $this->debug[] = 'connecting: Unable to connect to write DB: '.$v[2].' as '.$v[0];
                            }
                            else {
                                $this->debug[] = 'connecting: Connected to write DB: '.$v[2].' ('.$this->db_type.') as '.$v[0].' using PEAR '.$this->pear_type;
                                $this->current_write = $v;
                                break;
                            }
                        }
                        if (!$this->db_write || PEAR::isError($this->db_write)) {
                            $this->debug[] = 'connecting: Could not connect to any configured write servers';
                        }
                    }
                }
                else {
                    $this->db_write = $this->db_read;
                    $this->debug[] = 'connection: db_split is set to false, using current read server as read/write';
                        $this->debug[] = 'connecting: Successfully connnected to required sql servers';
                        return true;
                }
            }
        }
        $user->notices[] = 'Could not establish a connection to the database';
    }
    function disconnect() {
        if ($this->db_split && is_object($this->db_write) && !PEAR::isError($this->db_read) && $this->db_write != $this->db_read) {
            $this->debug[] = 'disconnecting: disconnecting from write handle';
            $this->db_write->disconnect();
        }
        if ($this->db_split) {
            $this->debug[] = 'disconnecting: disconnecting from read handle';
        }
        else {
            $this->debug[] = 'disconnecting: disconnecting from read/write handle';
        }
        if (is_object($this->db_read) && !PEAR::isError($this->db_read)) {
            $this->db_read->disconnect();
        }
        $this->debug[] = 'disconnecting: all db connections terminated';
    }
}

/* sql command wrappers */
class fw_sql_commands extends fw_db_connections {
    function fw_sql_commands() {
    }
    function select($sql, $force=false, $explain=false, $ses=false) {
        $result_set = array();
        if ($force) {
            $src = 'write handle';
            $res =& $this->db_write->query($sql);
        }
        else {
            $src = 'read handle';
            $res =& $this->db_read->query($sql);
        }
        $this->sql_list[] = $src.': '.$sql;
        if (PEAR::isError($res)) {
            $this->debug[] = $src.': sql id: '.(count($this->sql_list) - 1).' : '.str_replace('**', "\n      **", $res->userinfo);
        }
        else {
            while ($row =& $res->fetchRow($this->fetch_mode)) {
                $result_set[] = $row;
            }
            if ($explain) {
                $res = $this->select('explain '.$sql);
                $this->debug[] = array('EXPLAIN output for sql id '.(count($this->sql_list) - 1) => $res);
            }
        }
        return $result_set;
    }
    function write($sql, $ses=false) {
        if ($this->read_only) {
            return 0;
        }
        $result = 0;
        if ($this->pear_type == 'MDB2') {
            $sth = $this->db_write->prepare($sql);
            if (method_exists($sth, 'execute')) {
                $res = $sth->execute();
                $result = $res;
            }
            else {
                $res = $sth;
            }
        }
        else {
            $res =& $this->db_write->query($sql);
        }
        $src = 'write handle';
        $this->sql_list[] = $src.': '.$sql;
        if (PEAR::isError($res)) {
            $this->debug[] = $src.': '.(count($this->sql_list) - 1).' : '.str_replace('**', "\n      **", $res->userinfo);
        }
        else {
            if ($this->pear_type == 'DB') {
                $result = $this->db_write->affectedRows();
            }
        }
        return $result;
    }
    function delete($sql, $ses=false) {
        return $this->write($sql, $ses);
    }
    function update($sql, $ses=false) {
        return $this->write($sql, $ses);
    }
    function insert($sql, $ses=false) {
        return $this->write($sql, $ses);
    }
    function single($sql, $force=false, $explain=false, $ses=false) {
        if ($this->pear_type == 'MDB2') {
            $cmd = 'queryOne';
        }
        else {
            $cmd = 'getOne';
        }
        if ($force) {
            $res = $this->db_write->$cmd($sql);
            $src = 'write handle';
        }
        else {
            $res = $this->db_read->$cmd($sql);
            $src = 'read handle';
        }
        $this->sql_list[] = $src.': '.$sql;
        if (is_object($res)) {
            if (isset($res->userinfo)) {
                $this->debug[] = $src.': '.(count($this->sql_list) - 1).' : '.str_replace('**', "\n      **", $res->userinfo);
            }
            else {
                $this->debug[] = $src.': '.(count($this->sql_list) - 1).' : Unkown Error';
            }
            return false;
        }
        else {
            if ($explain) {
                $exp = $this->select('explain '.$sql);
                $this->debug[] = array('EXPLAIN output for sql id '.(count($this->sql_list) - 1) => $exp);
            }
            return $res;
        }
    }
    function last_insert($force=true) {
        if ($this->read_only) {
            return 0;
        }
        $sql = 'select last_insert_id()';
        return $this->single($sql, $force);
    }
    function calc_rows($force=false) {
        $sql = 'select FOUND_ROWS()';
        return $this->single($sql, $force);
    }
    function lock_table($table, $lock_type, $read=false) {
        $sql = 'lock table '.$table.' '.$lock_type;
        $result_set = array();
        if (!$read) {
            $src = 'write handle';
            $res =& $this->db_write->query($sql);
        }
        else {
            $src = 'read handle';
            $res =& $this->db_write->query($sql);
        }
        $this->sql_list[] = $src.': '.$sql;
        if (PEAR::isError($res)) {
            $this->debug[] = $src.': sql id: '.(count($this->sql_list) - 1).' : '.str_replace('**', "\n      **", $res->userinfo);
            return false;
        }
        else {
            return true;
        }
    }
    function unlock_table($read=false) {
        $sql = 'unlock tables';
        if (!$read) {
            $src = 'write handle';
            $res =& $this->db_write->query($sql);
        }
        else {
            $src = 'read handle';
            $res =& $this->db_write->query($sql);
        }
        $this->sql_list[] = $src.': '.$sql;
        if (PEAR::isError($res)) {
            $this->debug[] = $src.': sql id: '.(count($this->sql_list) - 1).' : '.str_replace('**', "\n      **", $res->userinfo);
            return false;
        }
        else {
            return true;
        }
    }
    function get_tables() {
        $sql = 'show tables';
        $res = $this->select($sql);
        $clean = array();
        foreach ($res as $vals) {
            $indexes = array_keys($vals);
            foreach ($indexes as $v) {
                $clean[] = $vals[$v];
            }
        }
        return $clean;
    }
    function qt($string, $wc=false) {
        if (!$string && !is_int($string)) {
            return "''";
        }
        else {
            if ($wc) {
                $string = '%'.$string.'%';
            }
            return $this->db_read->quote($string);
        }
    }
    function puke($continue=false) {
        if (!$continue) {
            $this->disconnect();
        }
        $data = '<div class="debug"><h4>SQL DEBUG<br /></h4><pre>'.print_r($this->sql_list, true)."\n".
             print_r($this->debug, true)."\n</pre></div>";
        return $data;
        if (!$continue) {
            die;
        }
    }
}

/* myql db wrapper requires fw_sql_commands and fw_db_connections */
class db_wrap extends fw_sql_commands {
    var $db_write;
    var $db_read;
    var $debug;
    var $sql_list;
    var $db_split;
    var $read_servers;
    var $write_servers;
    var $random_connect_read;
    var $random_connect_write;
    var $read_only;
    var $current_read;
    var $current_write;
    var $session_server;
    var $mode;
    var $db_type;
    var $pear_type;
    var $fetch_mode;
    var $persistent;

    function db_wrap() {
        $this->db_type = 'mysql';
        $this->fetch_mode = false;
        $this->pear_type = 'DB';
        $this->current_read = array();
        $this->current_write = array();
        $this->db_split = false;
        $this->session_server = array();
        $this->read_servers = array();
        $this->write_servers = array();
        $this->debug = array('db_wrap DEBUG MESSAGES');
        $this->sql_list = array('SQL STATEMENTS IN ORDER');
        $this->random_connect_read = false;
        $this->rendom_connect_write = false;
        $this->read_only = false;
        $this->persistent = false;
    }
}


?>
