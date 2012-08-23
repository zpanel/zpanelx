<?php
/********************************************************
    save_colors.DB.inc
    
    (C)Copyright 2002 Ryo Chijiiwa
    This file is part of IlohaMail, released under GPL

	PURPOSE:
		Save colors (in $my_colors) to DB back-end
	PRE-CONDITIONS:
		"include/session_auth.inc"
                $dataID 
                $my_colors
		
********************************************************/

include_once("../include/array2sql.inc");
include_once("../conf/db_conf.php");
include_once("../include/idba.$DB_TYPE.inc");

$iOpened = false;

if ($session_dataID > 0){
	//connect to db
	$db = new idba_obj;
	if ($db->connect()){
		//update
		$sql = Array2SQL($DB_COLORS_TABLE, $my_colors, "UPDATE");
		$sql.= " WHERE id=$session_dataID";
		if (!$db->query($sql)) echo "DB query failed: $sql <br>\n";
	}else{
		echo "DB connection failed.<br>\n";
	}
}
?>