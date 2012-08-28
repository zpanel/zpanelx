<?php
/********************************************************
	include/icl.inc
	
	(C)Ryo Chijiiwa <Ryo@IlohaMail.org> 2002 

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		IlohaMail Client Library.  Load imap.inc or pop3.inc
	COMMENTS:
		Assumes $host to be in the format:
		[protocol/]host[:port]
		Where protocol is one of {pop,imap,imaps}
		and port is a valid port number.

********************************************************/

if (!isset($port)) $port = 143;

//look for ':', if there, port number follows
$colon_pos = strpos($host, ":");
if ($colon_pos!==false){
	$port = substr($host, $colon_pos+1);
	$host = substr($host, 0, $colon_pos);
}else if (empty($port)){
	//no port, default to 143 for IMAP
	$port = 143;
}

//look for '/', split protocol and port
$slash_pos = strpos($host, "/");
if ($slash_pos!==false){
	$protocol = strtoupper(substr($host, 0, $slash_pos));
	$host = substr($host, $slash_pos + 1);
}else{
	//no protocol, guess from port #
	if ($port==110) $protocol = "POP3";
	else if ($port==143) $protocol = "IMAP";
	else if ($port==993) $protocol = "IMAPS";
}
$ICL_PORT = $port;

//little hack for IMAP-SSL
if ($protocol=="IMAPS"){
	$protocol = "IMAP";
	$ICL_SSL = true;
}else{
	$ICL_SSL = false;
}

if ($protocol=="IMAP"){
    $ICL_CAPABILITY["folders"] = true;
    $ICL_CAPABILITY["search"] = true;
    $ICL_CAPABILITY["radar"] = true;
	$ICL_CAPABILITY["calendar"] = true;
	$ICL_CAPABILITY["flags"] = true;
	include("../include/imap.inc");
}else if ($protocol=="POP3"){
    $ICL_CAPABILITY["folders"] = false;
    $ICL_CAPABILITY["search"] = false;
    $ICL_CAPABILITY["radar"] = false;
	$ICL_CAPABILITY["calendar"] = true;
	$ICL_CAPABILITY["flags"] = false;
	include("../include/pop3.inc");
}

?>