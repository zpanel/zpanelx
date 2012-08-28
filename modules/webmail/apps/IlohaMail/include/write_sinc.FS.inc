<?php
/////////////////////////////////////////////////////////
//	
//	include/write_sinc.inc
//
//	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		1.  Generate session ID
		2.	Read user preference settings from data source, and write into session file.
		3. Initialize session
	PRE-CONDITIONS:
		$user_name - User name
		$host - IMAP server
	POST-CONDITIONS:
		$user - Session ID
        $new_user - true if new user, else false
		Session file (in the form of a PHP include) is written into sessions folder,
		with file name $user."inc".
	COMMENTS:
		This file is fairly specific to the file-based data back-end.  For DB-based 
		back-ends, session data should be registered.
		Session data include:
			-session ID
			-remote IP
			-user name
			-password (clear or encrypted)
			-host
			-time of login (optional)
	NOTE:
		How to crack the session/password encryption mechanism:
			If you know the user name, host, and session ID
				1. Get session encryption key in user's directory
				2. Access session file.
				3.  Decrypt password
			If you don't know the user name, but have a session ID
				1.  Get IP address the session was opened from
				2.  Encrypt path using IP address 
                                    (in reverse order, no '.'s, as string)
                                    (e.g. 127.0.0.1 -> "100127")
				3.  Access user's directory and get session key
				4.  Decrypt password

********************************************************/

function GetPrefsFolder($user, $host, &$created){
	global $USER_DIR;
	
    $created = false;
	$result = false;
	
	$user = strtolower($user);
	$host = strtolower($host);
	
	$path = $USER_DIR.ereg_replace("[\\/]", "", $user.".".$host);
	if (@file_exists(realpath($path))){
		$result=$path;
	}else{
		if (@mkdir($path, 0700)){
            $created = true;
            $result=$path;
        }else{
            $result = false;
        }
	}
	return $result;
}

function GetSettings($result, $file){
	$lines = file($file);
	if (is_array($lines)){
                while ( list($k, $line) = each($lines) ){
			list($key, $val) = explode(":", $line);
			$result[$key] = base64_decode($val);
		}
	}else{
		$result=false;
	}
	
	return $result;
}

    include_once('../include/array2php.inc');
	include_once('../conf/defaults.inc');

    // find user's directory, or create one
    $path=GetPrefsFolder($user_name, $host, $new_user);
    if ($path){

        // create session ID
        if (!isset($session)){
            $session=time()."-".GenerateRandomString(5,"0123456789");
            $user=$session;	
        }
    
        // generate random session key
        $key=GenerateMessage(strlen($password)+5);
    
        // save session key in $userPath/key.inc
        $fp=fopen($path."/key.inc", 'w');
        if ($fp){
            fputs($fp, '<?php $passkey="'.$key.'"; ?>');
            fclose($fp);
        }

        // encrypt login ID, host, and passwords
        $encpass = EncryptMessage($key, $password);
        $encHost = EncryptMessage($key, $host);
        $encUser = EncryptMessage($key, $user_name);
    
		$ipkey = InitSessionEncKey($session);        
        $encPath = EncryptMessage($ipkey, $path);

        // dynamically generate code to put in session include file.
        $string="<?php\n";
        $string.="function GetPassword(){ return \"".$encpass."\";}\n";
        $string.="function GetHost(){ return \"".$encHost."\"; }\n";
        $string.="function GetUserName(){ return \"".$encUser."\";}\n";
        $string.="\$userPath=\"".$encPath."\";\n";
        $string.="\$port=".$port.";\n";
        $string.="\n?>";
    
		// write code to session include file (in sessions folder)
		$session_file_path = $SESSION_DIR.$user.".inc";
		$fp=fopen($session_file_path, 'w');
		if ($fp){
			if (!fputs($fp,$string))
				$error.= "Failed to write to \"$session_file_path\"\n";
			fclose($fp);
		}else{
			$error .= "Failed to open \"$session_file_path\"\n";
			echo "filesystem error";
		}

        // initialize $my_prefs, and create $userPath/prefs.inc file
        if (@file_exists(realpath($path."/prefs"))) $my_prefs = GetSettings($init["my_prefs"], $path."/prefs");
        else $my_prefs = $init["my_prefs"];
        include("../include/save_prefs.inc");

        // initialize $my_colors, and create $userPath/colors.inc file
        if (@file_exists(realpath($path."/colors"))) $my_colors = GetSettings($init["my_colors"], $path."/colors");
        else $my_colors = $init["my_colors"];
        include("../include/save_colors.inc");
    }else{
        $error .= "Couldn't create user dir<br>\n";
    }
?>
