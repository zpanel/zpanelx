<?php

require_once('header.php');

require_once('SQL.php');
require_once('Config.php');

class Auth {

	var $password;
	var $username;
	var $Account;
	var $SessionID;
	var $pop3host;
	var $debug;
	var $pgpGenerateKeys;
	var $pgpPassword;
	var $mode;


	//constructor
	function Auth($password = null, $acc = null, $sessid = null, $debug = null, $pgpGenKeys = null, $pgpPass = null)
	{
		$this->password = $password;
		$this->Account = trim($acc);
		$this->SessionID = $sessid ? $sessid : session_id();
		$this->debug = $debug;
		$this->pgpGenerateKeys = $pgpGenKeys;
    	$this->pgpPassword = $pgpPass;
	}

	function update_pass($pass, $user=null)
	{
		global $atmail;

		if (!$user) $user = $this->Account;
		//$pass = str_rot13($pass);
		$query = 'UPDATE UserSession SET Password = ?, PasswordMD5 = ? WHERE Account = ?';
		$data = array($pass, md5($pass), $user);
		$atmail->db->sqldo($query, $data);
	}

	function newuser($create, $user=null, $allowReserved = false)
	{
		global $domains, $atmail;

		// Nothing to create here
		if (!$this->Account)
		    return;

		// Check for reserved usernames
		if (!$allowReserved && isset($domains[$this->pop3host]) && $this->usernameReserved()) {
		    return 3;
		}

		// Remove any Session ID's from other accounts that clash with ours.
		// Can occur if user doesn't log out of previous account and then attempts
		// to log into another with the same browser.
		//file_put_contents("php://stderr","update UserSession set SessionID='' where SessionID='$this->SessionID' and Account != '$this->Account'\n");
		//$data = array($this->SessionID, $this->Account);
	        //$atmail->db->sqldo("update UserSession set SessionID='' where SessionID=? and Account != ?", $data); // Doesn't seem to function?!

		$query = "select Account from UserSession where Account = ?";
		$account = $atmail->db->getvalue($query, $this->Account);

		if(!$account && $pref['ldap_auth'] && $this->ldap_auth($this->username,$this->password))	{

			// Create an Account for local users
        	if (!$this->createuser($user)) {
        	    return -1;
        	}

		}
		
		// if the user does not exist in the system
		if (!$account && !$domains[$this->pop3host]) {
			// Create an Account. Only for external POP3/IMAP users
        	// Users have to create an Account via the signup form for local domains
        	if (!$this->createuser($user)) {
        	   return -1;
        	}
		} elseif (!$account && $create) {
			// Create an Account for local users
        	if (!$this->createuser($user)) {
        	    return -1;
        	}
		} elseif ($account && $create) {
			return 2;
		} elseif ( !$account && $domains[$this->pop3host]) {
    		return 1;
		}

		return 0;
	}


	function getuser()
	{
		global $pref, $atmail, $reg;

		// If user has not been authenticated
                // return 1 for auth error page
                if (!isset($this->authenticated))
                        return 1;
		

		if (!$this->Account && $this->username && $this->pop3host)
			$this->Account = trim("$this->username@$this->pop3host");

		$query = "select Password, LastLogin, Account, SessionID from UserSession where SessionID = ?";
    	$db = $atmail->db->sqlhash($query, array($this->SessionID));
		if (PEAR::isError($db))
			die("DB Error: ".$db->getDebugInfo());

    	$time = time();

    	// The user exists, but the session is too old
    	if ( $db['Account'] && $time - $db['LastLogin'] > $pref['session_timeout'])
    	{
       		return 2;
    	}
    	elseif ($db['Account'] != '')
    	{
        	$this->Account = $db['Account'];

			if (list($usr, $pop) = explode("@", $db['Account']))
        	{
            	$this->username = $usr;
            	$this->pop3host = $pop;
        	}

        	// Update the LastLogin time
			$this->update_lastlogin();
    	}
    	else
        	return 1;

    	return 0;
	}

	// Update the LastLogin timestamp for the Session
	function update_lastlogin()
	{
		global $atmail;

    	$time = time();
		$query = "update UserSession set LastLogin = ? where Account = ?";
		$data = array($time, $this->Account);

       	$atmail->db->sqldo($query, $data);
	}

	function update_session()
	{
		global $atmail;

		// Don't update from the sync.pl or checkmail.pl script
		if (preg_match('/sync\.php$|checkmail\.php$/', $_SERVER['SCRIPT_NAME']))
			return;

		if (!$this->Account)
			return;

	        // Remove any active sessions with same session id
                // ie user may be logging into another account with
                // same browser before logging out of first
                //$atmail->db->sqldo("update UserSession set SessionID = '', SessionData = '' where SessionID = ?", array(session_id()));
                if ($atmail->db->getvalue('select count(SessionID) from UserSession where SessionID = ?', array(session_id()))) {
                        session_regenerate_id();
                }

		$query = "update UserSession set SessionID = ? where Account = ?";
		$data  = array(session_id(), $this->Account);

    	$atmail->db->sqldo($query, $data);
	$this->SessionID = session_id();
	}

        function ldap_auth($user = null, $pass = null) {
                global $pref;

		if(!$user || !$pass)
		return 0;
				
                $ldapconn = ldap_connect($pref['ldap_server'],$pref['ldap_port']);
         	
		// Bind anonymous for a search of the users DN, based from their mail
         	if ($ldapconn) {
           
           	$user_mail = $this->Account;
           	$ldapsearch = ldap_search($ldapconn, $pref['ldap_user_dn'], "mail=" . $user_mail);
           
           	$info = ldap_get_entries($ldapconn, $ldapsearch);
           
           	//Find the dn and the email in LDAP for auth
           	for ($i=0; $i<$info["count"]; $i++) {
             	$dn = $info[$i]["dn"];
             	$auth_mail = $info[$i]["mail"][0];
          }  
           
          	//Next login as the user
          	$ldapbind = ldap_bind($ldapconn, $dn, $pass);
           
         	//check the connection and email address match LDAP
         	if ($ldapbind) {
           	if ($auth_mail) {
           
           	return 1;
         } else {
           return 0;
         } 
         } 
           
         // We only get here if the auth failed
         return 0;
     }     
       ldap_close($ds);                                                                                                                                                                                     
 }         


	function authuser()
	{
		global $pref, $atmail;

		$query = "select LastLogin, Password, Account, PasswordMD5 from UserSession where Account = ?";
		$db = $atmail->db->sqlhash($query, $this->Account);

		// Authentication failed if no user account selected
		if (!$db['Account'] || !$this->password)
    		return 1;

		// See if the Account is enabled / disabled
		$query = "select UserStatus from Users where Account=?";
		$status = $atmail->db->getvalue($query, $this->Account);

        // If the password matches, update the LastLogin
        if ($pref['ldap_auth']) {

	        if ($this->ldap_auth($this->username,$this->password)) {
	
	            $this->update_lastlogin();
				
				//miggySet new password if it has changed in ldap
				$query = "update UserSession set Password = ? where Account = ?";
				$data  = array($this->password, $this->Account);
				$atmail->db->sqldo($query, $data);
	
	            // Update the session ID - but not from the Sync or checkmail scripts
	            $this->update_session();
	
	            // Right password, but the account is disabled
	            if ($status)
	            	return 3;
							
				// Update the SQL tables with what is stored in LDAP
				$user = $this->ldap_auth_populate($this->username);
				$atmail->db->sqldo("update {$atmail->db->UserSettings} set RealName=? where Account=?", array($user['RealName'], $this->Account) );
				
				// LDAP mod --- Change FirstName= , & LastName= to Industry=, & Occupation=
				$atmail->db->sqldo("update Users set Industry=?, Occupation=? where Account=?", array($user['FirstName'], $user['LastName'], $this->Account) );

                return 0;
			}
        } elseif ($this->checkpass($db['Password'], $db['PasswordMD5'])) {
			$this->password = $db['Password'];
			$this->update_lastlogin();
			
			// Update the session ID - but not from the Sync or checkmail scripts
			$this->update_session();
			
			// Right password, but the account is disabled
			if ($status)
			    return 3;
			
			return 0;
        }

		// User account could not be authenticated
		return 1;
	}

		function ldap_auth_search($user = null)	{
			global $pref;
			
			//if(!$user)
			//return 0;
			
            $ldapconn = ldap_connect($pref['ldap_server'],$pref['ldap_port']);

            if ($ldapconn) {

                    $ldapbind = ldap_bind($ldapconn, $pref['bind_dn'], $pref['ldap_passwd']);
				    // search for user
				    if (($res_id = ldap_search( $ldapconn,
				                                $pref['base_dn'],
				                                "mail=$this->Account")) == false) {
				                                //"cn=$user")) == false) {
				      //print "failure: search in LDAP-tree failed<br>";
				      return false;
				    }

				    if (ldap_count_entries($ldapconn, $res_id) != 1) {
				      #print "failure: username $username found more than once<br>\n";
				    }
				
				    if (( $entry_id = ldap_first_entry($ldapconn, $res_id))== false) {
				      #print "failur: entry of searchresult couln't be fetched<br>\n";
				      return false;
				    }
				
				    if (( $user_dn = ldap_get_dn($ldapconn, $entry_id)) == false) {
				      #print "failure: user-dn coulnd't be fetched<br>\n";
				      return false;
				    }
				
					if($user_dn)
						return 1;
					else
						return 0;
										
				}
			
		}

	function ldap_auth_populate($user)
	{
		global $pref;
		
		if(!$user)
		return 0;
		
		//echo "AUTH POPULATE = $user\n";
		
        $ldapconn = ldap_connect($pref['ldap_server'],$pref['ldap_port']);

        if ($ldapconn) {

                $ldapbind = ldap_bind($ldapconn, $pref['bind_dn'], $pref['ldap_passwd']);

			    // search for user
			    if (($res_id = ldap_search( $ldapconn,
			                                $pref['base_dn'],
			                                "mail=$this->Account")) == false) {
			                                //"cn=$user")) == false) {
			      print "failure: search in LDAP-tree failed<br>";
			      return false;
			    }
			
				$entry_id = ldap_first_entry($ldapconn, $res_id);
				
				$arr = ldap_get_values($ldapconn, $entry_id, "givenName");
				$users['FirstName'] = $arr[0];
				
				$arr = ldap_get_values($ldapconn, $entry_id, "sn");
				$users['LastName'] = $arr[0];
				
				$arr = ldap_get_values($ldapconn, $entry_id, "displayName");
				$users['RealName'] = $arr[0];
				
				//$users['dn'] = $user_dn;
				
				return $users;
			}
					
	}
	
	function createuser($user=null)
	{
		global $pref, $atmail, $settings, $domains;
		
		// If we are using LDAP, grab the user details via the LDAP server
		if($pref['ldap_auth']) {
		$ldapuser = $this->ldap_auth_populate($this->username);

		// LDAP mod ---  changed user[FirstName], & user[LastName] to user[Industry], & user[Occupation]
		$user['Industry'] = $ldapuser['FirstName'];
		$user['Occupation'] = $ldapuser['LastName'];
		$user['RealName'] = $ldapuser['RealName'];

		//echo $user['FirstName'] . ":" . $user['LastName'] . "\n";
		
		}
		
	    if ( !$pref['crypt'] || !$atmail->isset_chk($domains[$this->pop3host]) ) {
			// Plaintext password
			$pass = $this->password;
	    }
	    else {
			// Encrypt the password
	        $pass = crypt( $this->password );
	    }

		$this->SessionID = session_id();

		// Load our table names
		$atmail->db->table_names( $this->Account );

		// Log the time we created the account
		$time = time();

		// Specify the 'default' user group if none exists
		if (!$user['UGroup']) $user['UGroup'] = 'Default';

		// Purge any invalid entries in the DB
		$atmail->db->sqldo("delete from UserSession where Account=?", $this->Account);

		// Create a new SessionID for the user
		$query = "INSERT INTO UserSession (Account, Password, SessionID, LastLogin, PasswordMD5, SessionData) VALUES(?, ?, ?, ?, ?, ?)";
		$data  = array($this->Account, $pass, $this->SessionID, $time, md5($pass), '');
		$res = $atmail->db->sqldo($query, $data);
		if ($res != 1) {
		    return -1;
		}

		$settings['UseSSL'] = 0;  
		
		// Select the MailType - SQL or Flatfile
	    // All functions are based on what type of account the user has
	    if ( !$domains[$this->pop3host]) {
	        $settings['MailType'] = $_REQUEST['MailType'];
	        if (strpos($settings['MailType'], 's')) {
	        	$settings['UseSSL'] = 1;
	        	$settings['MailType'] = str_replace('s', '', $settings['MailType']);
	        }
	    }
	    elseif ( $pref['sql_type'] && $domains[$this->pop3host])
	        $settings['MailType'] = 'sql';

	    elseif ( !$pref['sql_type'] && $domains[$this->pop3host])
	        $settings['MailType'] = 'file';

	    if ( $pref['sql_type'] )
			$settings['Mode'] = 'sql';
		else
			$settings['Mode'] = 'file';



		if (!$user['UserQuota'])
			$user['UserQuota'] = $settings['UserQuota'];

		// Build an SQL query for the new User
		$query = "INSERT INTO Users (UGroup, Address, BirthDay, BirthMonth, BirthYear, City, Country, TelHome,
				  FaxHome, TelWork, FaxWork, TelMobile, TelPager, FirstName, Gender, Industry, LastName,
				  Occupation, OtherEmail, PasswordQuestion, PostCode, State, DateCreate, UserStatus,
				  Account, MailDir, UserQuota) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, {$atmail->db->NOW},
				  ?, ?, ?, ?)";

		if (empty($user['BirthYear'])) {
		    $user['BirthYear'] = 0;
		}

		$data = array( $user['UGroup'], $user['Address'], $user['BirthDay'], $user['BirthMonth'],
	  				   $user['BirthYear'], $user['City'], $user['Country'], $user['TelHome'],
					   $user['FaxHome'], $user['TelWork'], $user['FaxWork'], $user['TelMobile'],
					   $user['TelPager'], $user['FirstName'], $user['Gender'], $user['Industry'],
					   $user['LastName'], $user['Occupation'], $user['OtherEmail'],
					   $user['PasswordQuestion'], $user['PostCode'], $user['State'],
					   $pref['UserStatus'], $this->Account, $user['MailDir'], $user['UserQuota']);

		if ($atmail->db->sqldo($query, $data) != 1) {
		    return -1;
		}

		// Build the query
		$insert = '';
		$values = '';
		$data = array();

		// Insert the users settings
		foreach ( $settings as $key => $value)
		{
			if ($key == 'UserQuota')
				continue;

			$insert .= Filter::cleanSqlFieldNames($key) . ',';
			$values .= '?,';

			// Insert custom preferences for account, depending on the
			// new user form
			if ( $key == "RealName" )
				$data[] = $user['FirstName'].' '.$user['LastName'];
			elseif ( $key == "LoginType" )
				$data[] = $user['LoginType'];
			elseif ( $key == "Service" )
				$data[] = $user['Service'];
			elseif ( $key == "ReplyTo" && $atmail->isset_chk($_REQUEST['email']) )
				$data[] = $_REQUEST['email'];
			elseif ( $key == "ReplyTo" && !$atmail->isset_chk($_REQUEST['email']) )
				$data[] = $this->Account;
			elseif ( $key == "Language" && $atmail->isset_chk($_REQUEST['Language']) )
				$data[] = $_REQUEST['Language'];
			else
				$data[] = $value;
		}

		$user_settings = $atmail->db->get('UserSettings');

		$query = "INSERT INTO $user_settings ($insert Account) values ($values ?)";
		$data[] = $this->Account;

		if ($atmail->db->sqldo($query, $data) != 1) {
		    return -1;
		}

		list($this->username, $this->pop3host) = explode('@', $this->Account);

		require_once('GetMail.php');
		//'Username' 'Pop3host' 'Type' 'Mode'
		$mail = new GetMail(array(
			'Username' => $this->username, 
			'Pop3host' => $this->pop3host, 
			'Type' => $settings['MailType'], 
			'Mode' => 'sql',
			'UseSSL' => $settings['UseSSL']));


		$mail->login();

		// Create the users default folders
		$folders = array('Inbox', 'Sent', 'Trash', 'Drafts', 'Spam');

		foreach ($folders as $folder) {
			$mail->newfolder($folder);
		}




        
	    return 1;
	}


	function checkpass($Password, $PasswordMD5)
	{
	    global $pref, $domains;

	    // Check the selected password matches the DB, in raw, crypt or MD5 format
	    if ( ($Password == $this->password && !$pref['crypt'])
	    || ($Password == $this->password && !$domains[$this->pop3host])
	    || (crypt($this->password, $Password) == $Password && $pref['crypt'])
	    || $Password == $this->password
	    || md5($Password) == $this->password
	    || $PasswordMD5 == $this->password
	    || md5($this->password) == $PasswordMD5)
	    {
	        return 1;
	    }

	    return 0;
	}


	function changepass($newpass, $oldpass, $question, $newpass2=false)
	{
		global $atmail, $domains, $pref;

		// Trigger plugin event.
		// Only continue execution if event is not handled
		$args = array('newpass' => $newpass);
		$atmail->pluginHandler->triggerEvent('onChangePass', $args);
		if (isset($args['handled'])) {
		    return 1;
		}
		
		$query = 'SELECT Password
				  FROM UserSession
				  WHERE Account = ?';
		$data  = "$this->username@$this->pop3host";

		$pass = $atmail->db->getvalue($query, $data);

		if ($pref['crypt']) {
			//$newpass = crypt($newpass);
			$oldpass = crypt($oldpass, $pass);
			$newpass = crypt($newpass);
			
		}

		if ($oldpass != $pass) {
			return 0;
		}
		
		$msg = 0;

		/*
		// Check validity of new password:
		// 1. Must be at least 8 chars long
		// 2. Must contain a mixture of alphanumeric chars
		// 3. Current password must be correct
		// 4. Password and Confirm password must match
		// 5. New password must not be same as current password
		// 6. Must not contain the username

		if (strlen($newpass) < 8) {
            $msg += 2;
		}

		if (preg_match('/[^a-z0-9]+/i', $newpass) || !preg_match('/[a-z]+/i', $newpass) || !preg_match('/[0-9]+/i', $newpass)) {
            $msg += 4;
		}

		if (strpos(strtolower($newpass), strtolower($this->username)) !== false) {
            $msg += 8;
		}

		if ($oldpass != $pass) {

			$msg += 16;
		}

        if ($newpass2 !== false && $newpass != $newpass2) {
            $msg += 32;
        }

        if ($pass == $newpass) {
            $msg = 64;
        }

		if ($msg > 0) {
		    return $msg;
		}

		*/

		$query = 'UPDATE Users
				  SET PasswordQuestion = ?
				  WHERE Account = ?';
        $data  = array($question, "$this->username@$this->pop3host");

		$atmail->db->sqldo($query, $data);

		$this->update_pass( $newpass, "$this->username@$this->pop3host" );

		$msg = 1;

		$this->password = $newpass;

		return $msg;
	}


	function usernameReserved()
	{
	   global $reserved;

	   list($uname,) = explode('@', $this->Account);
	   $uname = preg_quote($uname);

	   foreach (array_keys($reserved) as $rname) {
	       $rname = preg_quote(trim($rname), '/');
	       $rname = str_replace('\*', '.*', $rname);
	       if (preg_match("/^$rname$/i", $uname))
	           return true;
	   }

	   return false;
	}


	function get_username()
	{
		return $this->username;
	}

	function get_pop3host()
	{
		return $this->pop3host;
	}

	function get_account()
	{
		return $this->Account;
	}

	function get_password()
	{
		return $this->password;
	}

	function get_SessionID()
	{
		return $this->SessionID;
	}

	function set_username($uname)
	{
		$this->username = $uname;
	}

	function set_pop3host($host)
	{
		$this->pop3host = $host;
	}

	function set_account($acc)
	{
		$this->Account = $acc;
	}

	function set_password($pwd)
	{
		$this->password = $pwd;
	}

	function set_SessionID($id)
	{
		$this->SessionID = $id;
	}

}
?>
