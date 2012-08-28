<?php
/*	
	File: include/do_login.inc

	Copyright:		2004 Ryo Chijiiwa <Ryo@IlohaMail.org>
	License: 		see http://www.fsf.org/copyleft/gpl.html
	Purpose:  		Attempt to log in and initiate session
	Precondition: 	Include from index.php
*/

	//clean up
	$user = trim(chop($user));
	$host = trim(chop($host));
	$password = trim(chop($password));

    //validate host
    if (isset($host)){
        //validate host
        if (!empty($default_host)){
            if (is_array($default_host)){
                if (empty($default_host[$host])){
                    $host="";
                    $error .= $loginErrors[0]."<br>\n";
                }
            }else{
                if (strcasecmp($host, $default_host)!=0){
                    $host="";
                    $error .= $loginErrors[0]."<br>\n";
                }
            }
        }
    }
    
	//auto append
	if ((empty($error)) && (is_array($AUTO_APPEND)) && (!empty($AUTO_APPEND[$host]))){
		if (strpos($user, $AUTO_APPEND[$host])===false) $user .= $AUTO_APPEND[$host];
	}

	//attempt to initiate session
	if ((isset($user))&&(isset($password))&&(isset($host))){
		include_once("../include/icl.inc");
		$user_name = $user;
		
		//first, authenticate against server
		$iil_conn=iil_Connect($host, $user, $password, $AUTH_MODE);
		if ($iil_conn){
			//run custom authentication code
            include("../conf/custom_auth.inc");
            
			//if successful, start session
            if (empty($error)){
                include("../include/write_sinc.inc");
				$session = wsinc($user_name, $host, $password, $port, $new_user, $error);
                if (!$session) $user = "";
				if ($new_user){
                    include("../conf/new_user.inc");
					$new_user = 1;
                }else{
					$new_user = 0;
				}
				$authenticated = true;
			}
            
			//iil_Close($iil_conn);
		}else{
			$error = $iil_error."<br>";
		}
		
		//make log entry
		$log_action = "log in";
		include("../include/log.inc");
	}

?>