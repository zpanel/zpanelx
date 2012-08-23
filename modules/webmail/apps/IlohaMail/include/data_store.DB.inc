<?php
/********************************************************
    data_store.DB.inc
    
    (C)Copyright 2003 Ryo Chijiiwa
    This file is part of IlohaMail, released under GPL

	PURPOSE:
		Interface to user-centric permanent data storage backend.

********************************************************/

include_once("../include/array2sql.inc");
include_once("../conf/db_conf.php");
include_once("../include/idba.$DB_TYPE.inc");

class DataStore_obj extends idba_obj{
	var $owner; 	//owner ID
	var $error_str;		//error
	
	/*
	Function: DataStore_obj::DataStore_obj()
	Purpose:
		Constructor.  Initialize common vars, open DB connection.
	*/
	function DataStore_obj($owner){
		$this->owner = $owner;
		$this->error_str = "";
		if (!$this->connect())
			$this->error_str = $this->error()."\n";
	}
	
	
	/*
	Function: DataStore_obj::init
	Purpose: 
		Initializes entry with owner-key pair.
		Does nothing if entry already exists.	
	*/
	function init($key, $data){
		global $DB_DS_TABLE;
		
		//initialization
		$data = serialize($data);
		$data = base64_encode($data);
		$owner = $this->owner;
	
		//look for $owner $key entry
		$sql = "SELECT id FROM $DB_DS_TABLE";
		$sql.= " WHERE owner='$owner' and ds_key='$key'";
		$result = $this->query($sql);	

		if (!$result){
			//query failed.... error
			$this->error_str = $this->error()."\n";
			return false;
		}
		
		if ($this->num_rows($result)==0){
			//if none found, insert new entry
			$sql = "INSERT INTO $DB_DS_TABLE (owner, ds_key, ds_data, format)";
			$sql.= " VALUES ('$owner', '$key', '$data', 'b')";
			if ($this->query($sql)){
				return true;
			}else{
				$this->error_str= $this->error()."\n";
				return false;
			}
		}else{
			return true;
		}
	}


	/*
	Function: DataStore_obj::write
	Purpose:
		Update entry for $owner : $key to $data
		Assumes ds_init has already been called for
		the entry corresponding to $owner/$key pair.
	*/
	function write($key, $data){
		global $DB_DS_TABLE;
		
		//initialization
		$data = serialize($data);
		$data = base64_encode($data);
		$owner = $this->owner;
	
		//update entry
		if ($owner > 0){
			$sql = "UPDATE $DB_DS_TABLE ";
			$sql.= " SET ds_data = '$data',format='b'";
			$sql.= " WHERE owner='$owner' and ds_key='$key'";
			if ($this->query($sql)){
				//successfully updated
				return true;
			}else{
				//failed... error
				$this->error_str= $this->error()."\n";
				return false;
			}
		}
	}


	/*
	Function: DataStore_obj::read
	Purpose:
		Extract data for $owner $key pair.
	*/
	function read($key){
		global $DB_DS_TABLE;
		
		//initialization
		$data = serialize($data);
		$owner = $this->owner;
	
		//search for owner-key pair
		$sql = "SELECT ds_data,format FROM $DB_DS_TABLE";
		$sql.= " WHERE owner='$owner' and ds_key='$key'";
		$result = $this->query($sql);
	
		if (!$result){
			//query failed...error
			$this->error_str= $this->error()."\nSQL: $sql\n";
			return false;
		}
		if ($this->num_rows($result)==0){
			//not found...error
			$this->error_str= "Corresponding entry not found in Data Store\nSQL: $sql\n";
			return false;
		}
		
		//if found, extract and return
		$data = $this->result($result, 0, "ds_data");
		$format = $this->result($result, 0, "format");
		if (empty($data)){
			$this->error_str= "No data in \"$key\"";
			return false;
		}else{
			if ($format=='b') $data = base64_decode($data);
			$result = unserialize($data);
			if ($result===false){
				$data = str_replace("'", "\\", $data);
				$result = unserialize($data);
			}
			if ($result!==false) return $result;
			$this->error_str = "Failed to unserialize: $data\n";
			return false;
		}
	}


	/*
	Function: DataStore_obj::read_or_die
	Purpose:
		Same as ::read, except execution terminates on error.
		Used when accessing data that's critical.
	*/
	function read_or_die($key){
		$result = $this->read($key);
		if ($result!==false){
			return $result;
		}else{
			header("Content-type: text/plain");
			echo "Fatal error in data_store(DB): reading $key\n".$this->error_str;
			exit;
		}
	}
}

?>