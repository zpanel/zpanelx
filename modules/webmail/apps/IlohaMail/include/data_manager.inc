<?php
/////////////////////////////////////////////////////////
//	
//	include/data_manager.FS.inc
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

include("../conf/conf.inc");

if ($backend=="FS"){
	include("../include/data_manager.FS.inc");
	$DB_TYPE = "FS";
}else{
	include_once("../conf/db_conf.php");
	include_once("../include/idba.$DB_TYPE.inc");
	include_once("../include/data_manager.$DB_TYPE.inc");
}


?>