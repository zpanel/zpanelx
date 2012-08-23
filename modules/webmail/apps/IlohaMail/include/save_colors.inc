<?php
/********************************************************
	include/save_colors.inc
	
	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Shell for save_colors.backend.inc

********************************************************/

include_once("../conf/conf.inc");

$sc_backend = $backend;
if ($sc_backend != "FS"){
    include_once("../conf/db_conf.php");
	if ((!isset($DB_COLORS_TABLE)) || (empty($DB_COLORS_TABLE))) $sc_backend = "FS";
	else $sc_backend = "DB";
}

include("../include/save_colors.".$sc_backend.".inc");

?>