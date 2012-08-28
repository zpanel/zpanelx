<?php
/********************************************************
	include/icl.inc
	
	(C)Ryo Chijiiwa <Ryo@IlohaMail.org> 2002 

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Sets HTTP headers to disable cache if using non-Netscape browser

********************************************************/
if (isset($HTTP_USER_AGENT)) $ua = $HTTP_USER_AGENT;
else if (isset($_SERVER)) $ua = $_SERVER["HTTP_USER_AGENT"];

$isns = stristr($ua, "Mozilla") && (!(stristr($ua, "compatible"))) && (!(stristr($ua, "Safari")));
if (!$isns){
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");                          // HTTP/1.0
}
?>