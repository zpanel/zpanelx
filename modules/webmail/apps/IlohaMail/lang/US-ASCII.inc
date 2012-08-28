<?php
/////////////////////////////
//	US-ASCII.inc
//	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//		
//		Description:
//				Shell encoding library for the US-ASCII charset.
//				Since no encoding conversion should be required, all functions
//				simply return the given strings.
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
////////////////////////////

function LangConvert($string, $charset){
	return $string;
}

function LangEncodeSubject($input, $charset){
	return $input;
}

function LangEncodeMessage($input, $charset){
	$message=$input;
	
	$result["type"]="Content-Type: text/plain; charset=\"us-ascii\"\r\n";
	$result["encoding"]="";
	$result["data"] = $message;
		
	return $result;
}

include_once("../lang/common.inc");

function LangWrap($str){
	return wordwrap($str);
}
?>