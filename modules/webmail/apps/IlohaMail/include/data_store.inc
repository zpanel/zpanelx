<?php
/********************************************************
    data_store.inc
    
    (C)Copyright 2003 Ryo Chijiiwa
    This file is part of IlohaMail, released under GPL

	PURPOSE:
		Abstraction layer for Data Store.  Decides which
		file to include based on backend config.
		Then, instantiates a DataStore_obj as $DS.

********************************************************/

include_once('../conf/conf.php');
include_once('../conf/db_conf.php');

if (($backend=="FS") || (empty($DB_DS_TABLE))){
	//load data_store for FS backend
	include_once("../include/data_store.FS.inc");
}else{
	//load data_store for DB backend
	include_once("../include/data_store.DB.inc");
}
?>