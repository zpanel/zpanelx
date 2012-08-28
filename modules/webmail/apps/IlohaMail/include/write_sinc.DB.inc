<?php
/////////////////////////////////////////////////////////
//	
//	include/write_sinc.DB.inc
//
//	(C)Copyright 2002-2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		1.  Generate session ID
		2.  Initialize session
	PRE-CONDITIONS:
		$user_name - User name
		$host - IMAP server
	POST-CONDITIONS:
		$user - Session ID
        $new_user - true if new user, else false
		session info is stored in database
        prefs and colors stored in backend (DB or FS)
	NOTE:
        In conf/db_conf.php
        ...assumes the following tables are present:
            $DB_USERS_TABLE
            $DB_SESSIONS_TABLE
        ...following are optional:
            $DB_PREFS_TABLE
            $DB_COLORS_TABLE
        If $DB_PREFS_TABLE or $DB_COLORS_TABLE is empty, a file based backend is used.

********************************************************/

function GetPrefsFolder($user, $host){
	global $UESR_DIR;
	
	$result=false;
	$path = $USER_DIR.ereg_replace("[\\/]", "", $user.".".$host);
	if (file_exists(realpath($path))){
		$result=$path;
	}else{
		if (mkdir($path, 0700)) $result=$path;
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

    include_once("../include/array2php.inc");
    include_once("../include/array2sql.inc");
    include_once("../conf/db_conf.php");
	include_once('../conf/defaults.inc');

    // initialize some vars
    $prefs_saved = false;
    $colors_saved = false;
    $new_user = false;

    // we only need user dirs if contacts, prefs, or colors table aren't specified
    if ((empty($DB_CONTACTS_TABLE)) || (empty($DB_PREFS_TABLE)) || (empty($DB_COLORS_TABLE))){
        //if needed, look for a path, or create one
        $path=GetPrefsFolder($user_name, $host);
    }

    // create session ID
    if (!isset($session)){
		$session=time()."-".GenerateRandomString(5,"0123456789");
        $user=$session;	
    }
    
    // generate random session key
    $key=GenerateMessage(strlen($password)+5);

	$ipkey = InitSessionEncKey($session);        
    
    // encrypt login ID, host, and passwords
    $encpass = EncryptMessage($ipkey, $password);
    $encHost = EncryptMessage($ipkey, $host);
    $encUser = EncryptMessage($ipkey, $user_name);
    if (!empty($path)) $encPath = EncryptMessage($ipkey, $path);
	
	//connect to database
	include_once("../include/idba.$DB_TYPE.inc");
	$db = new idba_obj;
	if ($db->connect()){
			// check users table, create entry if necessary
			$sql = "select id,userLevel from $DB_USERS_TABLE where (login='$user_name') and (host='$host')";
			$r = $db->query($sql);
			if ($r){
				if ($db->num_rows($r)<1){
                    // if user not in db, insert
					$now = time();
					$sql = "insert into $DB_USERS_TABLE (login, host, dateCreated, lastLogin, userLevel) ";
					$sql .= "values ('$user_name', '$host', '$now', '$now', 0)";
					if (!$db->query($sql)){
						$error.="DB error: Couldn't add user to users table<br>\n";
						echo "\n<!--\nSQL:$sql\nERROR:".$db->error()."\n//-->\n";
                    }else{
                        $dataID = $db->insert_id();
						$userLevel = 0;
                        $new_user = true;
                    }

                    // create record in prefs
                    if ((empty($error)) && (!empty($DB_PREFS_TABLE))){
                        $my_prefs = $default_prefs;
                        $my_prefs["id"] = $dataID;
                        $sql = Array2SQL($DB_PREFS_TABLE, $my_prefs, "INSERT");
                        if ($db->query($sql)){
                            $prefs_saved = true;
                        }else{
                            $error .= "DB error: Couldn't insert into $DB_PREFS_TABLE<br>\n";
                            echo "\n<!--\nSQL:$sql\nERROR:".$db->error()."\n//-->\n";
                            $db->query("delete from $DB_USERS_TABLE where id='$dataID'");
                        }
                    }
                    
                    // create record in colors
                    if ((empty($error)) && (!empty($DB_COLORS_TABLE))){
                        $my_colors = $default_colors;
                        $my_colors["id"] = $dataID;
                        $sql = Array2SQL($DB_COLORS_TABLE, $my_colors, "INSERT");
                        if ($db->query($sql)){
                            $colors_saved = true;
                        }else{
                            $error .= "DB error: Couldn't insert into $DB_COLORS_TABLE<br>\n";
                            echo "\n<!--\nSQL:$sql\nERROR:".$db->error()."\n//-->\n";
                            if ($prefs_saved) $db->query("delete from $DB_COLORS_TABLE where id='$dataID'");
                            $db->query("delete from $DB_USERS_TABLE where id='$dataID'");
                        }
                    }

				}else{
                    $dataID = $session_dataID = $db->result($r, 0, "id");
					$userLevel = $db->result($r, 0, "userLevel");
                    $colors_saved = true;
                    $prefs_saved = true;
                }
                //echo "<!-- Selected: $dataID -->";
                
			}else{
				$error.="DB error: Couldn't access users table <br>\n";
			}
			
			// Initialize session
            if (empty($error)){
				if (empty($port)) $port = 143;
                $sql = "insert into $DB_SESSIONS_TABLE (sid, login, password, host, path, dataID, port, userLevel, inTime)";
                $sql.= " values ('$user', '$encUser', '$encpass', '$encHost', '$encPath', '$dataID', '$port', '$userLevel', ".time().")";
                if (!$db->query($sql)) $error .= "DB Insert failed: ".$db->error()." <br>\n";
				$sql = "update $DB_USERS_TABLE set lastLogin='".time()."' where id='$dataID'";
				if (!$db->query($sql)) $error .= "DB Update failed: ".$db->error()." <br>\n";
            }
	}else{
		$error .= "DB connection failed. <br>\n";
	}

    if (!empty($path)){
        if (!$prefs_saved){
            echo "\n<!-- Saving prefs to FS backend -->\n";
            // initialize $my_prefs, and create $userPath/prefs.inc file
            if (file_exists(realpath($path."/prefs"))) $my_prefs = GetSettings($init["my_prefs"], $path."/prefs");
            else $my_prefs = $init["my_prefs"];
            include("../include/save_prefs.inc");
        }
    
        if (!$colors_saved){
            echo "\n<!-- Saving colors to FS backend -->\n";
            // initialize $my_colors, and create $userPath/colors.inc file
            if (file_exists(realpath($path."/colors"))) $my_colors = GetSettings($init["my_colors"], $path."/colors");
            else $my_colors = $init["my_colors"];
            include("../include/save_colors.inc");
        }
    }
    
    if (!empty($error)){
        $session="";
        $user = $user_name;
    }
?>
