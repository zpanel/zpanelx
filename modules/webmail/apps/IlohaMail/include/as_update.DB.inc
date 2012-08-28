<?php
/********************************************************
	include/as_update.DB.inc
	
	(C)2002-2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
        Update session table with latest send operation.

********************************************************/

	// delete this session and any old unclosed ones
	include_once("../conf/db_conf.php");
	
	//connect to db
	include_once("../include/idba.$DB_TYPE.inc");
	$db = new idba_obj;
	if ($db->connect()){
		$numSent = $numSent+$num_recepients;
		$sql = "UPDATE $DB_SESSIONS_TABLE";
		$sql.= " SET lastSend=".time().", numSent=$numSent";
		$sql.= " WHERE sid='$sid'";
		if ($db->query($sql)) echo "as updated done.<br>\n";
		else echo "as update failed: $sql <br>\n";
	}else{
		echo "DB connection failed.<br>\n";
	}
?>