<?php
/////////////////////////////////////////////////////////
//	
//	include/session_close.DB.inc
//
//	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail, and released under GPL.
//	See COPYING, or http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////
/********************************************************

	PURPOSE:
	PRE-CONDITIONS:
		This page is include'd in "source/login.php" which is loaded upon logout.
		Session should be closed, and any cached data (i.e. password) purged.
	COMMENTS:
		This file is for the default DB back-end.

********************************************************/

	// delete this session and any old unclosed ones
	include_once("../conf/db_conf.php");
	include_once("../conf/conf.inc");
	
	//connect to db
	include_once("../include/idba.$DB_TYPE.inc");
	$db = new idba_obj;
	if ($db->connect()){
			$expTime = time() - $MAX_SESSION_TIME; //close all session that are over 24 hours old
			$sql = "delete from $DB_SESSIONS_TABLE where (sid = '$user') or (inTime < $expTime)";
			if (!$db->query($sql)) echo "DB query failed: $sql <br>\n";
	}else{
		echo "DB connection failed.<br>\n";
	}
	

?>