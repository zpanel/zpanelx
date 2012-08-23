<?php
/********************************************************
    data_store.DB.inc
    
    (C)Copyright 2003 Ryo Chijiiwa
    This file is part of IlohaMail, released under GPL

	PURPOSE:
		Interface to user-centric permanent data storage backend.

********************************************************/

include_once("../include/array2php.inc");

class DataStore_obj{
	var $path; 	//owner ID
	var $error;		//error
	
	/*
	Function: DataStore_obj::DataStore_obj()
	Purpose:
		Constructor.  Initialize common vars
	*/
	function DataStore_obj($path){
		$this->path = $path;
		$this->error = "";
	}
	
	
	/*
	Function: DataStore_obj::init
	Purpose: 
		Initialize data store for $key.  
		Create data file and include.
		If creating for first time, fill with default data (passed in $data).
	*/
	function init($key, $data){
		//initialization
		$path = $this->path;
		
		$inc_path = $path.'/'.$key.'.inc';
		$php_path = $path.'/'.$key.'.php';
				
		//dynamically move old .inc files to .php
		if (file_exists($inc_path)){
			$result = rename($inc_path, $php_path);
		}
		if (!file_exists(realpath($path."/".$key)) || !file_exists($php_path)){
			//backend files missing, create them
			return $this->write($key, $data);
		}
		return true;
	}


	/*
	Function: DataStore_obj::write
	Purpose:
		Actually write $data to appropriate files
	*/
	function write($key, $data){
		//initialization
		$path = $this->path;
		
		// save data into $path/$key file
		$file=$path."/".$key;
		$fd = @fopen($file, "w");
    	if ($fd){
			reset($data);
			while ( list($field, $val) = each($data)){
				fwrite($fd, $field.":".base64_encode($val)."\n");
			}
			fclose($fd);
		}else{
			$this->error = "Failed to open \"$file\" for writing\n";
			return false;
		}
    
		// write dynamically created PHP code into $path/$key.inc
		$fp = @fopen($file.".php", "w");
		if ($fp){
			$str = Array2PHP($data, "my_".$key);
			fputs($fp, "<?php\n");
			fputs($fp, $str);
			fputs($fp, "?>\n");
		}else{
			$this->error = "Failed to open \"$file.inc\" for writing\n";
			return false;
		}
		
		return true;
	}


	/*
	Function: DataStore_obj::read
	Purpose:
		Extract data for $owner $key pair.
	*/
	function read($key){
		//initialization
		$path = $this->path;

		//include 
		$file = $path."/".$key.".php";
		if (file_exists(realpath($file))){
			include($file);
			$var = "my_".$key;
			return $$var;
		}else{
			$this->error = "Invalid $key include path: $file <br>\n";
			return false;
		}
	}


	/*
	Function: DataStore_obj::read_or_die
	Purpose:
		Same as ::read, except exectuion terminates on error.
		Used when accessing data that's critical.
	*/
	function read_or_die($key){
		$result = $this->read($key);
		if ($result!==false){
			return $result;
		}else{
			echo "Fatal error in data_store(FS): ".$this->error;
			exit;
		}
	}
}

?>