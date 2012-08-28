<?php
/////////////////////////////////////////////////////////
//	
//	include/data_manager.MySQL.inc
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Data handling abstraction class.
	PRE-CONDITIONS:
		$DB_USERS_TABLE - Name of table cotaining users
	POST-CONDITIONS:
	COMMENTS:
		For DB based backend, none of the data will be stored
		in memory, and will be accessed through DB in real time.

********************************************************/

include_once("../include/array2sql.inc");

class DataManager_obj{
	var $user;
	var $host;
	var $table;		//directory for FS backend, table nmae for DB
	var $dataID;		//file name for FS backend, user's ID for DB backend
	var $data;			//actually contains
	var $error;
	var $db;
	
	function initialize($user, $host, $table, $backend){
		global $DB_USERS_TABLE;
		
		if (empty($table)){
			$this->error = "DB table name or ID is empty\n";
			return false;
		}
		
		$this->db = new idba_obj;
		if (!$this->db->connect()) return false;
		
		$sql = "SELECT * FROM $DB_USERS_TABLE WHERE login='$user' and host='$host'";
		$result = $this->db->query($sql);
		if (($result) && ($this->db->num_rows($result)>0)){
			$dataID = $this->db->result($result, 0, "id");
		}else{
			$this->error = $error;
		}
		
		if (!$dataID){
			$this->error.="User not found in database\n";
			return false;
		}
		
		$this->backend = $backend;
		$this->table = $table;
		$this->dataID = $dataID;
		$this->data = array();
		
		return true;
	}
	
	
	function read(){
		$data = array();
		$sql = "SELECT * FROM ".$this->table." WHERE owner='".$this->dataID."'";
		$result = $this->db->query($sql);
		if (($result) && ($this->db->num_rows($result)>0)){
			while ($a = $this->db->fetch_row($result)){
				$id = $a["id"];
				$data[$id] = $a;
			}
		}else{
			$this->error .= $error;
			return false;
		}
		
		return $data;
	}
	
	
	function save(){
		//everything's done in real time anyway
		return true;
	}
	
	
	function delete($id){
		$sql = "DELETE FROM ".$this->table;
		$sql.= " WHERE id='".$id."' and owner='".$this->dataID."'";
		return $this->db->query($sql);
	}
	
	
	function insert($array){
		//get list of fields in table
		$backend_fields = $this->db->list_fields($this->table);
		if (!is_array($backend_fields)){
			$this->error .= "Failed to fetch fields\n";
			$this->error .= $error;
			return false;
		}
		
		//pick out relevant fields
		$insert_data = array();
		while ( list($k,$field) = each($backend_fields) ){
			if (!empty($array[$field])){
				$insert_data[$field] = $array[$field];
			}
		}
		if (empty($insert_data["owner"])) $insert_data["owner"] = $this->dataID;
		
		//$this->error .= "Inserting: ".implode("," $insert_data)."\n";
		
		//insert
		$sql = Array2SQL($this->table, $insert_data, "INSERT");
		$backend_result = $this->db->query($sql);
				
		$this->error = $error;
		
		return $backend_result;
	}
	
	function update($id, $array){
		//get list of fields in table
		$backend_fields = $this->db->list_fields($this->table);
		if (!is_array($backend_fields)){
			$this->error .= "Failed to fetch fields\n";
			$this->error .= $error;
			return false;
		}
		
		//pick out relevant fields
		$insert_data = array();
		while ( list($k,$field) = each($backend_fields) ){
			if (isset($array[$field]))
				$insert_data[$field] = $array[$field];
		}
		
		//insert
		$sql = Array2SQL($this->table, $insert_data, "UPDATE");
		$sql.= " WHERE id='$id' and owner='".$this->dataID."'";
		$this->db->query($sql);
		
		//echo "updating: $sql<br>\n";
		$backend_result = $this->db->query($sql);
		$this->error .= $this->db->error();
		
		return $backend_result;
	}


	function sort($field, $order){
		$data = array();

		$backend_query = "SELECT * FROM ".$this->table;
		$backend_query.=" WHERE owner='".$this->dataID."'";
		$backend_query.=" ORDER BY $field $order";		
		
		$backend_result = $this->db->query($backend_query);
		
		if (($backend_result) && ($this->db->num_rows($backend_result)>0)){
			while ($a = $this->db->fetch_row($backend_result)){
				$data[] = $a;
			}
		}else{
			$this->error .= $this->db->error();
			return false;
		}
		
		return $data;
	}


	function getDistinct($field, $order){
		$data = array();

		$backend_query = "SELECT distinct $field FROM ".$this->table;
		$backend_query.=" WHERE owner='".$this->dataID."'";
		$backend_query.=" ORDER BY $field $order";		
		
		$backend_result = $this->db->query($backend_query);
		
		if (($backend_result) && ($this->db->num_rows($backend_result)>0)){
			while ($a = $this->db->fetch_row($backend_result)){
				$data[] = $a[$field];
			}
		}else{
			$this->error .= $this->db->error();
			return false;
		}
		
		return $data;
	}


	function search($array){
	}
	
	function showError(){
		echo nl2br($this->error);
	}
}