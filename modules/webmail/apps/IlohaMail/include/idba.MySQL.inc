<?php
/********************************************************
	include/idba.MySQL.inc
	
	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Database access functions for abstraction.

********************************************************/

include_once("../conf/db_conf.php");

class idba_obj{
	var $conn;
	function connect(){
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_PERSISTENT, $DB_NAME;
		
		if ($this->conn>0) return true;
		
		$this->conn = false;
		$mysql_conn = false;
		
		if ($DB_PERSISTENT) $mysql_conn = mysql_pconnect($DB_HOST, $DB_USER, $DB_PASSWORD);	
		if (!$mysql_conn) $mysql_conn = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD);
	
		if ($mysql_conn){
			if (mysql_select_db($DB_NAME)){
				$this->conn = $mysql_conn;
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function query($sql){
		if ($this->conn){
			$result = @mysql_query($sql, $this->conn);
			return $result;
		}else{
			return false;
		}
	}

	function num_rows($result){
		return mysql_num_rows($result);
	}

	function fetch_row($result){
		return mysql_fetch_assoc($result);
	}
	
	function result($result, $row, $field){
		return mysql_result($result, $row, $field);
	}

	function list_fields($table){
		global $DB_NAME;
		$result = false;
		
		if ($this->conn){
			$fields = mysql_list_fields($DB_NAME, $table, $this->conn);
			$columns = mysql_num_fields($fields);

			for ($i = 0; $i < $columns; $i++) {
    			$result[$i] = mysql_field_name($fields, $i);
			}	 	
		}
		return $result;
	}
	
	function insert_id(){
		return mysql_insert_id($this->conn);
	}
	
	function error(){
		return mysql_error();
	}
}
?>