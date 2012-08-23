<?php
/////////////////////////////
//	UTF-8.inc
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
//      Added by SnAKes (snx@ifrance.com)
////////////////////////////

function LangIs8Bit($string){
	$len = strlen($string);
	for ($i=0; $i < $len; $i++)
		if (ord($string[$i])>=128) return true;
	
	return false;
}

function LangConvert($string, $charset, $charset2){
	if($charset!=$charset2) return utf8_encode($string);
	else return $string;
}

function LangEncodeSubject($input, $charset){
	if (LangIS8Bit($input)){
		return "=?UTF-8?B?".base64_encode($input)."?=";
	}else{
		return $input;
	}
}

function LangEncodeMessage($input, $charset){
	$message=$input;
	$result["type"]="Content-Type: text/plain; charset=\"UTF-8\"\r\n";
	$result["encoding"]="";
	$result["data"] = $message;
		
	return $result;
}

include_once("../lang/common.inc");

function LangWrap($str){
	return wordwrap($str);
}
?>
