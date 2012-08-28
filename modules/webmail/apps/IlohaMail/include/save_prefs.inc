<?php
/********************************************************
	include/save_prefs.inc
	
	(C)Copyright 2000-2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Shell for save_prefs.backend.inc

********************************************************/

include_once("../conf/conf.inc");

$sp_backend = $backend;
if ($sp_backend != "FS"){
    include_once("../conf/db_conf.php");
	if ((!isset($DB_PREFS_TABLE)) || (empty($DB_PREFS_TABLE))) $sp_backend = "FS";
	else $sp_backend = "DB";
}

include("../include/save_prefs.".$sp_backend.".inc");

?>
