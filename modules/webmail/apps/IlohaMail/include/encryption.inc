<?php
/////////////////////////////////////////////////////////
//	
//	include/encryption.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Provide basic encryption related functionality.
	COMMENTS:
		This library commits the worst crime in cryptography: 
		Never write your own crypto algos.

********************************************************/

function GenerateRandomString($messLen, $seed){
	srand ((double) microtime() * 1000000);
	if (empty($seed)) $seed="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	$seedLen=strlen($seed);
	if ($messLen==0) $messLen = rand(10, 20);
	for ($i=0;$i<$messLen;$i++){
		$point=rand(0, $seedLen-1);
		$message.=$seed[$point];
	}
	return $message;
}

function GenerateMessage($messLen){
	$seed="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	return GenerateRandomString($messLen, $seed);
}

function EncryptMessage($key,$message){
	$messLen=strlen($message);
	$keylen=strlen($key);
	$enc_message="";
	
	for ($i=0;$i<$messLen;$i++){
		$j=$i % $keylen;
		$code=chr((ord($message[$i]) + ord($key[$j])) % 128);
		$enc_message.=$code;
	}

	return base64_encode($enc_message);
}

function DecodeMessage($pass, $message){
	$message=base64_decode($message);
	$messLen=strlen($message);
	$passLen=strlen($pass);
	
	$decMessage="";
	for ($i=0;$i<$messLen;$i++){
		$j=$i % $passLen;
		$num=ord($message[$i]);
		$decNum=(($num + 128) - ord($pass[$j])) % 128;
		$decMessage.=chr($decNum);
	}
	
	return $decMessage;
}


function GenerateKeyFromIP(){
	$ip = $_SERVER["REMOTE_ADDR"];
	$ipkey="";
	$ip_a = explode(".", $ip);
	for ($i=3; $i>=0; $i--) $ipkey.=$ip_a[$i];
	return $ipkey;
}


function GetSessionEncKey($sid){
	global $MAX_SESSION_TIME, $STAY_LOGGED_IN;
	$cookie_name = "IMAIL_SESS_KEY_".$sid;
	if (empty($_COOKIE[$cookie_name])){
		// No cookies, turn IP into encryption key
		$ipkey = GenerateKeyFromIP();		
	}else{
		// use cookie
		$ipkey = $_COOKIE[$cookie_name];
		if ($STAY_LOGGED_IN){
			setcookie ($cookie_name, $ipkey, time()+$MAX_SESSION_TIME, "/", $_SERVER[SERVER_NAME]);
		}
	}
	return $ipkey;
}


function InitSessionEncKey($sid){
	global $MAX_SESSION_TIME;
	
	if (empty($_COOKIE['IMAIL_TEST_COOKIE'])){
		//cookies disabled
		$key = GenerateKeyFromIP();
	}else{
		//cookies enabled
		$cookie_name = "IMAIL_SESS_KEY_".$sid;
		$key = GenerateRandomString(16, "");
		$_COOKIE[$cookie_name] = $key;
		setcookie ($cookie_name, $key, time()+$MAX_SESSION_TIME, "/", $_SERVER[SERVER_NAME]);
	}
	return $key;
}

?>