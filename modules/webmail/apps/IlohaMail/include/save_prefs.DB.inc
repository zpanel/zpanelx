<?php
/********************************************************
    save_prefs.DB.inc
    
    (C)Copyright 2002 Ryo Chijiiwa
    This file is part of IlohaMail, released under GPL

	PURPOSE:
		Save prefs (in $my_prefs) to DB back-end
	PRE-CONDITIONS:
		"include/session_auth.inc"
                $session_dataID 
                $my_prefs
		
********************************************************/

include_once("../include/array2sql.inc");
include_once("../conf/db_conf.php");
include_once("../include/idba.$DB_TYPE.inc");

$iOpened = false;
	
$prefs_summary = trim(chop(implode("", $my_prefs)));

if (($session_dataID > 0) && (strlen($prefs_summary) > 10)){
	//connect to db
	$db = new idba_obj;
	if ($db->connect()){
		//update
		$sql = Array2SQL($DB_PREFS_TABLE, $my_prefs, "UPDATE");
		$sql.= " WHERE id=$session_dataID";
		if (!$db->query($sql)) echo "DB query failed: $sql <br>\n";
	}else{
		echo "DB connection failed.<br>\n";
	}
}

?>
