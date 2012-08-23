<?php
/////////////////////////////
//	X-EUC-JP.inc
//	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//		
//		Description:
//				Charset conversion library for Japanese text.
//				Converts JIS (used in email) to X-EUC-JP (used for web pages),
//				and X-EUC-JP (submitted by web forms) to JIS (used in email).
//		
//		Contributions:
//				TOMO <groove@spencernetwork.org>: jcode.php
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
////////////////////////////

include("../lang/jp/jcode.inc");
include_once("../include/qp_enc.inc");

function LangIs8Bit($string){
	$len = strlen($string);
	for ($i=0; $i < $len; $i++)
		if (ord($string[$i])>=128) return true;
	
	return false;
}

function LangConvert($string, $charset, $from_charset){
	$CS_CAN_CONVERT["ISO-2022-JP"]=1;
	$CS_CAN_CONVERT["EUC-JP"]=1;
	$CS_CAN_CONVERT["X-EUC-JP"]=1;
	$CS_CAN_CONVERT["Shift-JIS"]=1;
	$CS_CAN_CONVERT["JIS"]=1;
	$CS_CAN_CONVERT["ISO-8859-1"]=1;

	$from_charset = strtoupper($from_charset);

	if (!$CS_CAN_CONVERT[$from_charset]){
		return $string;
	}else if ($from_charset=="ISO-8859-1"){
		return LangEncode8bitLatin($string);
	}else if (strcasecmp($charset, "x-euc-jp")==0){
		return JcodeConvert($string, 0, 1);
	}else{
		return $string;
	}
}

function LangEncodeSubject($input, $charset){
	$subject=$input;
	if (strcasecmp($charset, "x-euc-jp")==0){
		$charset_code = AutoDetect($input);
		if (($charset_code > 0) && ($charset_code < 3)){
			$subject=JcodeConvert($subject,0,3);
			$subject="=?ISO-2022-JP?B?".base64_encode($subject)."?=";		
		}else if (LangIS8Bit($subject)){
			$subject = "=?ISO-8859-1?Q?".qp_enc($subject)."?=";
		}
	}
	
	return $subject;
}

function LangEncodeMessage($input, $charset){
	$message=$input;
	
	$result["type"]="Content-Type: text/plain; charset=\"us-ascii\"\r\n";	
	$result["data"]=$message;
	
	if (strcasecmp($charset, "x-euc-jp")==0){
		$charset_code = AutoDetect($message);
		if ($charset_code > 0){
			$result["type"]="Content-Type: text/plain; charset=ISO-2022-JP\r\n";
 			$result["encoding"]="Content-Transfer-Encoding: 7bit\r\n";
 			$result["data"] = JcodeConvert($message,0,3);
		}else{
			$result["data"] = $message;
		}		
	}
	
	return $result;
}

include_once("../lang/common.inc");

function LangWrap($str){
	return $str;
}

?>
