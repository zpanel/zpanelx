<?php
/********************************************************
	include/session_auth.inc
	
	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Shell for session_auth.backend.inc

********************************************************/

include_once("../conf/conf.inc");

//sanitize session ID
$user = eregi_replace("[^0-9-]", "", $user);

//time out
if (!$STAY_LOGGED_IN){
	$session_parts = explode("-", $user);
	$in_time = $session_parts[0];
	$valid_time = time() - $MAX_SESSION_TIME;
	if ($in_time < $valid_time){
		echo "Session timeout.  Please log out.";
		if (!$do_not_die) exit;
	}
}

//determine backend
$sa_backend = $backend;
include("../conf/db_conf.php");
if ($backend!="FS"){
	if (empty($DB_SESSIONS_TABLE)) $sa_backend = "FS";
	else $sa_backend = "DB";
}

//get session ID
$session_cookie = false;
if (!empty($ILOHAMAIL_SESSION)){
	$user = $ILOHAMAIL_SESSION;
	$session_cookie = true;
}

//do it...
include("../include/session_auth.".$sa_backend.".inc");

//theme stuff
if (empty($my_prefs["theme"])) $my_prefs["theme"] = "default";
include("themes/".$my_prefs["theme"]."/override.inc");

//remove session ID if cookies are used
if ($session_cookie) $user = "";

//set content-type and charset headers
if ($CS_OVERRIDE) $display_cs = $CS_OVERRIDE;
else $display_cs = $my_prefs['charset'];
header("Content-Type: text/html; charset=".$display_cs);

?>