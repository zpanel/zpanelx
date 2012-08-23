<?php
/////////////////////////////////////////////////////////
//	
//	include/session_auth.FS.inc
//
//	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
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
		As long as the variables and functions defined above are initialized, where the data is
		(i.e. on the HD, in a SQL database, LDAP, etc) should not matter.

********************************************************/

	//cehck session ID
	if ((empty($user)) || (!file_exists(realpath($SESSION_DIR.$user.".inc")))){
		echo "Invalid session ID<br>\n";
		if (!$do_not_die) exit;
	}

	include_once("../include/encryption.inc");
	include_once($SESSION_DIR.$user.".inc");

	$ipkey = GetSessionEncKey($user);
	$userPath = DecodeMessage($ipkey, $userPath);

	if (!file_exists(realpath($userPath."/key.inc"))){
		echo "<html>";
		echo "Invalid session ID: $user<br>\n";
		echo "Please <a href=\"index.php\" ".(ereg("index.php",$_SERVER['PHP_SELF'])?"":"target=\"_parent\"").">log back in</a>.";
		echo "</html>";
		exit;
	}else{
		//get key
		include($userPath."/key.inc");
	}

	//include color prefs
	if (file_exists(realpath($userPath."/colors.inc"))){
		include_once($userPath."/colors.inc");
	}else{
		echo "Invalid color prefs include path: $userPath <br>\n";
		if (!$do_not_die) exit;
	}
	
	//include prefs
	if (file_exists(realpath($userPath."/prefs.inc"))){
		include_once($userPath."/prefs.inc");
	}else{
		echo "Invalid prefs include path: $usePath <br>\n";
		if (!$do_not_die) exit;
	}
	if (empty($my_prefs['charset'])) $my_prefs['charset'] = 'ISO-8859-1';
	if (empty($languages[$my_prefs['lang']])) $my_prefs['lang'] = 'eng/';

	//initialize session variables
	$my_charset=$my_prefs["charset"];
	if ($port==110) $my_prefs["list_folders"] = 0;	
	$loginID = DecodeMessage($passkey, GetUserName());
	$password = DecodeMessage($passkey, GetPassword());
	$host = DecodeMessage($passkey, GetHost());
	$dataID = $userPath;
	$session_dataID = $dataID;
?>