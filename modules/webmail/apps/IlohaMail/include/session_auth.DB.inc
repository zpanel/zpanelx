<?php
/////////////////////////////////////////////////////////
//	
//	include/session_auth.DB.inc
//
//	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail, and released under GPL.
//	See COPYING, or http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////
/********************************************************

	PURPOSE:
		1.  Make sure session (pass as $user) is valid
		2.	Initialize $loginID variable, containing IMAP login ID.
		3.  Initialize $host variable, containing IMAP server name
		4.  Initialize $password variable, containing plain text password
		5.  Initialize $my_prefs variable, which should be an associated array containing user preferecnes
		6.  Initialize $my_colors variable, which should be an associated array containing user defined colors
	PRE-CONDITIONS:
		$user - Session ID
	POST-CONDITIONS:
	COMMENTS:
		All source files should include this file for session verification and user data initialization.
		This file uses a DB backend for session management.

********************************************************/

	include_once("../include/encryption.inc");
        
	$loginID = $host = $password = "";
    $my_prefs = false;
    $my_colors = false;
    
    $dataID = 0;

	//connect to database
	include_once("../conf/db_conf.php");
	include_once("../include/idba.$DB_TYPE.inc");
	$db = new idba_obj;
	if ($db->connect()){
            //get session info
			$result = $db->query("select * from $DB_SESSIONS_TABLE where sid = '$user'");
			if (($result) && ($db->num_rows($result)==1)){
				$a = $db->fetch_row($result);
				$encLogin = $a["login"];
				$encPass = $a["password"];
				$encHost = $a["host"];
				$userPath = $a["path"];
                $dataID = $a["dataID"];
				$port = $a["port"];
                $lastSend = $a["lastSend"];
                $numSent = $a["numSent"];
				$userLevel = $a["userLevel"];
				$inTime = $a["inTime"];
                $session_dataID = $dataID;
				
				$ttl = time() - $inTime;
				if ($STAY_LOGGED_IN && ($MAX_SESSION_TIME/10)<$ttl){
					// if session time remaining is 10% of max session lifespan, update so we stay logged in
					$db->query("UPDATE $DB_SESSIONS_TABLE SET inTime=".time()." WHERE sid='$user'");
				}
			}else{
				echo "<html>";
				echo "Invalid session ID: $user<br>\n";
				echo "Please <a href=\"index.php\" ".(ereg("index.php",$_SERVER['PHP_SELF'])?"":"target=\"_parent\"").">log back in</a>.";
				echo "</html>";
				//if (!$do_not_die) exit;
			}
            
            //get prefs
            if ((!empty($DB_PREFS_TABLE)) && ($dataID > 0)){
                $r = $db->query("select * from $DB_PREFS_TABLE where id='$dataID'");
                if (($r) && ($db->num_rows($r)==1)) $my_prefs = $db->fetch_row($r);
				if ($port==110) $my_prefs["list_folders"] = 0;
            }

            //get colors
            if ((!empty($DB_COLORS_TABLE)) && ($dataID > 0)){
                $r = $db->query("select * from $DB_COLORS_TABLE where id='$dataID'");
                if (($r) && ($db->num_rows($r)==1)) $my_colors = $db->fetch_row($r);
            }
	}else{
		echo "DB connection failed<br>\n";
	}
    
    //--------- END DB Specific stuff -----------

	$ipkey = GetSessionEncKey($user);
	
	$loginID = DecodeMessage($ipkey, $encLogin);
	$password = DecodeMessage($ipkey, $encPass);
	$host = DecodeMessage($ipkey, $encHost);

    // If we're using FS backend for some things, check that out too
    if (!empty($userPath)){
        // Find path to user dir
        $userPath = DecodeMessage($ipkey, $userPath);

        // Read prefs and colors
        if (!$my_colors) include_once($userPath."/colors.inc");
        if (!$my_prefs) include_once($userPath."/prefs.inc");
	
    }
    
	if (empty($languages[$my_prefs['lang']])) $my_prefs['lang'] = 'eng/';
	if (empty($my_prefs['charset'])) $my_prefs['charset'] = 'ISO-8859-1';
    $my_charset=$my_prefs["charset"];
    
    if (($dataID==0)&&(!$do_not_die)){
    	exit;
    }
?>