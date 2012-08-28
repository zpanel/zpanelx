<?php
/*
	File:       sessions_lib.inc
	Author:     Ryo Chijiiwa, IlohaMail.org
	License:    GPL, part of IlohaMail
	Purpose:    Public library/API for creating/destroying IlohaMail sessions
	
	Notes:      This script MUST be included from a script accessed
	            on the same TLD (top level domain) as the domain IlohaMail
	            is running on (because cookies can not be set across domains).
				
				BEFORE USING this script, you MUST CONFIGURE IT FIRST.  See the
				config section below for more info.
				
				You should be able to move this file to anywhere you
				like, as long as it is configured properly.
				
				Including this script will change your current working directory
				to the IlohaMail source directory (assuming you have this script
				configured properly).  To return, do: 
				
				     chdir($SESSLIB_INIT_DIR);
				
				...after you use the necessary functions.  Running chdir before you
				call the functions will break things.

	Usage:
				In the following example, this file is being included from a script on 
				www.domain.com, while IlohaMail is accessed at webmail.domain.com, and 
				the mail server is mail.domain.com.  We also assume that the user's user 
				name is stored in $user, and we have the password as $pass.
				
				//include this file 
				include('/path/to/IlohaMail/include/sessions_lib.inc');
				
				//start session
				$sessionID = imsess_start($user, $pass, 'mail.domain.com', 143, $new, $err);
				
				if ($sessionID){
					//if successful, redirect to an active IlohaMail session
					$url = 'http://webmail.domain.com/index.php?session='.$sessionID;
				}else{
					//if failed, redirect to login screen and show error
					$url = 'http://webmail.domain.com/index.php?error='.urlencode($error);
				}
				
				//do redirect
				header('Location: '.$url);
*/

//--------
//  CONFIGS
//--------

/*
IM_COOKIE_DOMAIN
	If this script is being executed under a sub-domain different from the sub-domain 
	IlohaMail runs in, then set this to your top level domain.
	e.g. If you want to run this script from www.domain.tld but acess IlohaMail from
	webmail.domain.tld, set IM_COOKIE_DOMAIN to 'domain.tld'
*/
$IM_COOKIE_DOMAIN = 'domain.tld';


/*
IM_SRC_DIR
	Path to IlohaMail's source directory.  Because includes in files included from
	this file are specified by paths relative to the source folder, we need to be
	in the source folder or the includes will fail.
*/
$IM_SRC_DIR = '/path/to/IlohaMail/source/';


//--------
//	INCLUDES
//--------
$SESSLIB_INIT_DIR = getcwd();  	//store current working directory
chdir($IM_SRC_DIR);				//go to IlohaMail source directory
include('../include/write_sinc.inc');
include('../include/session_close.inc');


//-----------
//  FUNCTIONS
//-----------
	/*
		Function:   imsess_start
		Input:
		            $user - account user name
					$pass - account password
					$host - mail server host
					$port - port (defaults to 143 - imap)
					$new - set to true if new user, false otherwise
					$error - returns any error messages
		Output:     session ID or false (as well as $new_user, and $error)
		Purpose:    Starts an IlohaMail session, making necessary changes to backend
		            and sets necessary cookies.
		Usage:
				$session_id = imsess_start($user, $pass, 'mail.myhost.com', 143, $new_user, $error);
				if ($session_id)
					$url = 'http://webmail.myhost.com/index.php?session='.$session_id;
					header('Location: '.$url);
				else
					echo $error;
	*/
	function imsess_start($user, $pass, $host, $port, &$new, &$error){
		return wsinc($user, $host, $pass, $port, $new, $error);
	}
	
	

	/*
		Function:   imsess_close
		Input:      $sessionID - session ID
		Output:     none
		Purpose:    Destroy IlohaMail session and session info
		Note:       This will only destroy session data.  It doesn't do all the cleanup
		            that the normal "logout" script does.  For more info, see the actual
					logout script in source/login.php.
	*/
	function imsess_end($sessionID){
		return sess_close($sessionID);
	}
	
?>