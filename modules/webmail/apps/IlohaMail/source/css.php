<?php
/////////////////////////////////////////////////////////
//	
//	source/main.php
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

header("Content-Type: text/css");
include("../include/super2global.inc");
include_once("../conf/conf.inc");
include_once("../conf/db_conf.php");
include("../include/session_auth.inc");

$linkc=$my_colors["main_link"];
//$bgc=$my_colors["main_bg"];
$bgc=$my_colors["main_darkbg"];
$textc=$my_colors["main_text"];
$hilitec=$my_colors["main_hilite"];
$font_size = $my_colors["font_size"];

$raw_css = true;
include("../include/css.inc");
?>