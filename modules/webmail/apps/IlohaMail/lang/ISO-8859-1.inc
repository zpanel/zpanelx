<?php
/////////////////////////////
//	ISO-8859-1.inc
//	(C)2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//		
//		Description:
//				Encoding library for the ISO-8859-1 charset.
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
////////////////////////////

include_once("../include/qp_enc.inc");

function LangIs8Bit($string){
	$len = strlen($string);
	for ($i=0; $i < $len; $i++)
		if (ord($string[$i])>=128) return true;
	
	return false;
}

function LangConvert($string, $charset){
	return $string;
}

function LangEncodeSubject($input, $charset){
	$words = explode(" ", $input);
	if ( count($words) > 0){
		while ( list($k, $word) = each($words) ){
			if (LangIs8Bit($word)) $words[$k] = "=?".$charset."?Q?".qp_enc($word, 76)."?=";
		}
		$input = implode(" ", $words);
	}
	return $input;
}

function LangEncodeMessage($input, $charset){
	$message=$input;
	
	$result["type"]="Content-Type: text/plain; charset=".$charset."\r\n";
	$result["encoding"]="Content-Transfer-Encoding: quoted-printable\r\n";
	$result["data"] = qp_enc($message, 78);
		
	return $result;
}

include_once("../lang/common.inc");

function LangWrap($str){
	return wordwrap($str);
}
?>
