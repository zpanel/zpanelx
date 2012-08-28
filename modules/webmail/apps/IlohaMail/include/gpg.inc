<?php
/////////////////////////////////////////////////////////
//	
//	include/gpg.inc
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail, and released under GPL.
//	See COPYING, or http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////
/********************************************************
	PURPOSE: GPG interface
	COMMENTS:  Based on code contributed by Paul A. Martin

********************************************************/

function gpg_list_keys(){
	global $GPG_HOME_STR, $GPG_PATH;
	global $loginID, $host;
	
	$gpg_home = str_replace("%h", $host, str_replace("%u", $loginID, $GPG_HOME_STR));
	$gpgkeys=`"$GPG_PATH" --home="$gpg_home" --list-public-keys`;
	preg_match_all("/pub\s+[\w\/]+\s+[\w-]+\s+([\w ]+).*<([\w@.]+)>/", $gpgkeys, $works);
	
	$result = array();
	for($i=0; $works[1][$i] != ""; $i++){
		$key = $works[1][$i];
		$str = $works[1][$i]." &lt;".$works[2][$i]."&gt;";
		$result[$key] = $str;
	}
	
	return $result;
}

function gpg_export($person){
	global $loginID, $host;
	global $GPG_HOME_STR, $GPG_PATH;

	$person = escapeshellcmd(stripslashes($person));
	$gpg_home = str_replace("%h", $host, str_replace("%u", $loginID, $GPG_HOME_STR));
	$command = $GPG_PATH." --home=".$gpg_home." --export -a \"$person\"";
	$temp = exec($command, $result, $errorno);
	return implode("\n", $result);
}

function gpg_encrypt($loginID, $host, $gpgrecp, &$gpgmessage){
	global $GPG_HOME_STR, $GPG_PATH;
	
	$original_message = $gpgmessage;
	
	if($gpgrecp!="noencode")
	{
		//disable command injection
		$gpgmessage = str_replace("`", "\\`", $gpgmessage);
	
		//format home directory path
		$gpg_home = str_replace("%h", $host, str_replace("%u", $loginID, $GPG_HOME_STR));
		$gpg_home = realpath($gpg_home);
		
		//encrypt
		$tempcom = 'echo "'.$gpgmessage.'" | '.$GPG_PATH.' --home='.$gpg_home.' -a --always-trust --batch -e -r "'.$gpgrecp.'"';
		echo $tempcom."<br>\n";
		$oldhome = getEnv("HOME");
		$msg = exec($tempcom, $encrypted, $errorcode);
		echo "msg: $msg <br>\n";
		echo "errorcode: $errorcode <br>\n";
		$gpgmessage = implode("\n", $encrypted);
		echo "New message: <pre>$gpgmessage</pre> <br>\n";
		$gpg_encrypted = true;
		if ($errorcode!=0){
			$gpgmessage = $original_message;
			return false;
		}else{
			return true;
		}
	}
	return false;
}

function gpg_decrypt($gpg_passphrase, &$body){
	global $GPG_HOME_STR, $GPG_PATH;
	global $loginID, $host, $user;

	//$oldhome = getEnv("HOME");
	//$blah = nl2br($body);
	$original = $body;
	$gpg_home = str_replace("%h", $host, str_replace("%u", $loginID, $GPG_HOME_STR));
	$temp_file = $gpg_home."/$user-gpg.tmp";
	$fp = fopen($temp_file,'w');
	//$fp = fopen("/home/$loginID/.gnupg/blah",'w');
	if ($fp){
		fwrite($fp, $body, strlen($body));
		fclose($fp);
		
		$temp = 'echo "'.escapeshellcmd($gpg_passphrase).'" | '.$GPG_PATH.' --home='.$gpg_home.' -v --batch --passphrase-fd 0 --decrypt '.escapeshellcmd($temp_file);
		$blah = exec($temp, $body, $errorcode);
		
		if ($errorcode==0){
			$body = implode("\n", $body);
			$body = stripslashes($body);
		}else{
			$body = "gpg_decrypt: Decryption failed... (errorno: $errorcode)\n\n".$original;
		}
		unlink($temp_file);
		//unlink("/home/$loginID/.gnupg/$fp");
	}else{
		$body =  "gpg_decrypt: Couldn't open temp file: $temp_file\n\n".$original;
	}
}

?>