<?php
/////////////////////////////
//	Windows-1251.inc
//	(C)2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//	CyrLize function by Yozh <yozh.com.ua>
//		
//	Description:
//		Encoding library for the Windows-1251 charset (Cyrillic).
//	Comments:
//		The LangConvert function in this charset library, unlike others,
//		uses the third parameter to determine how to convert.	
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

function CyrLize ($string, $cset) {
	$charset=strtolower($cset);
	if ($charset=="koi8-r") {
		$string=convert_cyr_string($string, "k", "w");
		$c=true;
	}else if ($charset=="iso8859-5") {
		$string=convert_cyr_string($string, "i", "w");
		$c=true;
	}else if ($charset=="x-cp866") {
		$string=convert_cyr_string($string, "a", "w");
		$c=true;
	}else if ($charset=="x-mac-cyrillic") {
		$string=convert_cyr_string($string, "m", "w");
		$c=true;
	}else if ($charset=="windows-1251") {
		$string=$string;
		$c=true;
	}

	return $string;
}

function LangConvert($string, $charset, $from_charset){
	return CyrLize($string, $from_charset);
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
