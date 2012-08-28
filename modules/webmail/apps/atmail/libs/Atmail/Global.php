<?php
// +----------------------------------------------------------------+
// | Global.php														|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: Febuary 2005												|
// +----------------------------------------------------------------+


// PHP 4.3.10
// Notice: Undefined variable: pref in /var/www/html/phpport/atmailphp/webmail/libs/Atmail/Global.php on line 11
// $pref not loaded first in Config.php?
require_once('header.php');
require_once('Global_Base.php');

class AtmailGlobal extends Global_Base {

	var $MailA;
	var $Language;
	var $NumQuota;
	var $BrandDomain;
	var $username;
	var $pop3host;
	var $param;
	var $DisplayImages;
	var $BlockedImages;
	var $tmpdir;


function AtmailGlobal()
{
	$this->Global_Base();

	global $pref;

	// Adjust memory limit
	if (!$this->isset_chk($pref['memory_limit']) || $pref['memory_limit'] < 96)
		$pref['memory_limit'] = 96;

	ini_set('memory_limit', "{$pref['memory_limit']}M");

	// Adjust max message size
	if( !$this->isset_chk($pref['max_msg_size']))
		$pref['max_msg_size'] = '16';

	// Max exectuion time, increase, for slow responding POP3/IMAP servers
	ini_set('max_execution_time', "180");

	AtmailGlobal::do_branding();
}


// Auth the user account
function auth($mailauth=null)
{
    $args = array();
    $this->pluginHandler->triggerEvent('onAuthStart', $args);

	global $domains, $pref;

    // Lowercase the pop3host
    $pop3host = strtolower($_REQUEST['pop3host']);
    $pop3host = trim(str_replace("'", '', $pop3host));

    if ((isset($_REQUEST['UseSSL']) && $_REQUEST['UseSSL'] == 1) || strpos($_REQUEST['MailType'], 's')) {
        $this->UseSSL = 1;
    } else {
        $this->UseSSL = 0;
    }

	// Lowercase the username
	$username = strtolower($_REQUEST['username']);

	// Multi-demo login support; generate a new user ID
    if($pref['demologin'] && $username == "demo")     {
    $username = $this->generate_demouser($pop3host);
    //$this->demouser = '1';
    }

	//$username = trim(str_replace("'", '', $username));
    $username = trim($username);

    //Take away the @domain part
    $username = preg_replace('/\@.*/', '', $username);

    $this->auth->set_pop3host($pop3host);
	$this->auth->set_username($username);
    $this->auth->set_account("$username@$pop3host");
    $password = str_replace("'", '', $_REQUEST['password']);
    $this->auth->set_password($password);
	$this->auth->update_session();

    $this->SessionID = $this->auth->get_SessionID();

	$this->log = new Log( array('Account' => $this->auth->get_account()) );

    $tmp = $this->db->read_settings($this->auth->get_account());

    // Check a license capacity

	$this->MailAuth = str_replace("'", '', $tmp['MailAuth']);

	// insert data into stats
	$this->log = new Log(array('Account' => $this->auth->get_account()));

	// Load our username prefix tables
	$this->db->table_names( $username );

	// Check the user is allowed to access @Mail with the selected domain
	if ($this->isset_chk($pref['allowed_domains']) && !empty($pref['allowed_domains']) && !$domains[$this->auth->pop3host])
	{

		$doms = explode(',', $pref['allowed_domains']);
		$chk = 0;

		foreach ($doms as $dom)
		{
			$dom = str_replace(' ', '', $dom);	// Take out spaces
			$dom = strtolower($dom);
			if ($dom == $this->auth->pop3host) {
			    $chk++;
			}

		}

		if (!$chk)
		{
			print $this->parse("html/$this->Language/auth_notallowed.html",
								array('status' => "Remote POP3/IMAP access to the domain {$this->auth->pop3host} is not permitted
												   since the domain is not in the allowed access list. Contact the Administrator
												   to add the domain to the Webadmin > Config > User Restrictions panel"));

			$this->log->write_log('Error', "Blocked domain attempt");
			exit;
		}
	}

	// Check the user is allowed to acces selected mail server
	if (!empty($pref['allowed_mailservers']))
	{
		$doms = explode(',', $pref['allowed_mailservers']);
		$chk = 0;
		$server = $pref['mailserver'] ? $pref['mailserver'] : $_REQUEST['MailServer'];
		if (!$server)
			$server = $this->MailServer;

		foreach ($doms as $dom)
		{
			$dom = str_replace(' ', '', $dom);	// Take out spaces
			$dom = strtolower($dom);
			if ($dom == $server)
				$chk++;
		}

		if(!$chk)
		{
			print $this->parse("html/$this->Language/auth_notallowed.html",
								array('status' => "Remote POP3/IMAP access to the mailserver $server is not
								permitted since the mail-server is not in the allowed access list.
								Contact the Administrator to add the domain to the Webadmin > Config > User Restrictions panel"));

			$this->log->write_log('Error', "Blocked domain attempt");
			exit;
		}
	}

	else
	{
	    $mail = '';
	    $status = 0;

	    // get mail type
	    $type = (isset($_REQUEST['MailType']) && strlen($_REQUEST['MailType']) > 0)? $_REQUEST['MailType'] : 'pop3';
	    $type = str_replace('s', '', $type);

	    // If the user selects a different mailserver on the login page
	    // First try Webadmin > Config > Mailserver field, otherwise MailServer on the login page, otherwise the users domain
	    $server = ($pref['mailserver']) ? $pref['mailserver'] : $_REQUEST['MailServer'];
	    $server = ($server) ? $server : $this->auth->get_pop3host();
	    $server = str_replace("'", '', $server);

		if (!$domains[$this->auth->pop3host])
			$external = 1;

        require_once('Generic_Mail.php');

		// The user account is not local, try and auth via POP3/IMAP
        if ( $type == 'pop3' || $type == 'imap')
		{
            $mail = new Generic_Mail($server, $type, 60, $this->UseSSL);
            $status = $mail->lasterror();
        }

        if ($status)
		{
			// Error message, cannot connect to the remote server
	        header("Content-type: text/xml; charset: utf-8");
	        echo "<ErrorMessage action=\"logout\">$status</ErrorMessage>";
 
            if ($mail)
				$mail->quit();
			exit;
        }

		// Choose which method to authenticate to the mail server. [username] or [username@host]
    	if ( $pref['mailserver_auth'] || $_REQUEST['MailAuth'] || $mailauth || $this->MailAuth)
		{
        	if (!$mail->login( $this->auth->get_account(), $this->auth->get_password() ))
				$loginstatus = $mail->lasterror();
				$this->MailA = 1;
		}
		else
		{
			if (!$mail->login( $this->auth->get_username(), $this->auth->get_password() ))
				$loginstatus = $mail->lasterror();
			    $this->MailA = '';
		}

		$mail->quit();

		if (strpos($loginstatus, '-ERR') !== false && !$mailauth )
		{
			// Try and login again using the user@auth syntax
			return $this->auth(true);
		}
		elseif (strpos($loginstatus, '-ERR') !== false)
		{
			
			// Error message, cannot connect to the remote server
	        header("Content-type: text/xml; charset: utf-8");
	        echo "<ErrorMessage action=\"logout\">$loginstatus</ErrorMessage>";
 
            if ($mail)
				$mail->quit();
			exit;
        }
        else
		{
            $pass = $this->auth->get_password();
            $this->auth->update_pass($pass);
        }

    }

    // Find the number of new accounts for the IP address
	if ($this->isset_chk($_REQUEST['NewUser']))
	{
		$numaccts = $this->log->logcheck('Login', "New Account {$_SERVER['REMOTE_ADDR']}");

		// If the user has exceeded the max accounts per day, exit, and throw an error!
		if ($numaccts >= $pref['max_accounts_per_day'])
		{
			$this->httpheaders();
			print $this->parse("html/$this->Language/auth_maxaccounts.html");
			$this->log->write_log('Error', "Max accounts created from {$_SERVER['REMOTE_ADDR']}");
			exit;
		}
	}

	if ($this->isset_chk($_REQUEST['NewUser']) || $this->demouser == '1')
	{
		// If we are a new account, log the IP address into the log file
		$user = $this->auth->get_username();
		$this->log->write_log('Login', "New Account {$_SERVER['REMOTE_ADDR']}");

		// Cross check usernames are in an allowed format
		if ( !preg_match('/^[A-Z-0-9\._-]+$/i', $user)) {
			$this->invalidchars($user);
		}

	}

	//update the users password if it has changed in ldap maggy
    if ($pref['ldap_auth'] && $this->isset_chk($_REQUEST['NewUser'])) {
		$query = "update UserSession set Password = ? where Account = ?";
        $data  = array($this->password, $this->Account);
        $this->db->sqldo($query, $data);
    }

    if ($this->isset_chk($_REQUEST['NewUser']) || $this->demouser == '1')
		$status = $this->auth->newuser('1', $this->getinfo());
	else
		$status = $this->auth->newuser(null);

    if ( $status == 1 )
        $this->nosuchuser();
    elseif ( $status == 2 )
        $this->userexists();
    elseif ( $status == 3)
        $this->reserved_user();

    $status = $this->auth->authuser();

    if ( $status == 1 ) {
		 if (strpos($_SERVER['SCRIPT_NAME'] , 'wap.php') === false && strpos($_SERVER['SCRIPT_NAME'] , 'xhtml.php') === false)
        	$this->httpheaders();

        $this->auth_error();
        return;
    }

    elseif ( $status == 3 ) {
        $this->userdisabled();
        return;
    }
    
    // Update the LastLogin timestamp
    $this->auth->update_lastlogin();
    $this->username = $this->auth->get_username();
    $this->pop3host = $this->auth->get_pop3host();
    $this->account  = $this->auth->get_account();
    $this->loadprefs();
    $this->MailAuth = $this->MailA;
    $this->auth->authenticated = true;

	// Make our sample account data
	//if($this->demouser == '1')
	//system("/usr/bin/perl /usr/local/atmail/webmail.perl/modules/make-demouser.pl " . $this->username . "@" . $this->pop3host);

}

// Load the users preferences
function loadprefs()
{
	global $pref, $domains;

	$this->db->table_names( $this->username );
    $settings = $this->db->read_settings($this->auth->get_account());

	if (is_array($settings))
        foreach ($settings as $k => $v)
            $this->$k = $v;

    // Override MailType for local accounts
    if (isset($domains[$this->pop3host])) {
    	$this->MailType = 'file';
    }

	// If users Language is empty set to default
	if (!$this->Language)
	{
		$this->Language = $pref['Language'];
	}

	if ($this->LoginType == 'xul')
		$this->XUL = 1;

	if ( $this->Ajax && ($this->isset_chk($_REQUEST['ajax']) || $this->isset_chk($_REQUEST['LoginType'])))
	{
		$this->FromField = $this->loadpersonalities(1);
		if (!$this->FromField)
			$this->FromField = "$this->username@$this->pop3host" ;
	}

	// Load the transperant image type for the users background color
	$this->bgtrans();

	$this->pki_enabled = $this->pki_enabled();

	// Load our temporary directory
	$this->tmpdir = "{$pref['user_dir']}/tmp/" . "$this->username@$this->pop3host" . "/";

	// Create our temporary directory
	if (!is_dir($this->tmpdir))
		mkdir($this->tmpdir,0777);

	// LeaveMsgs = 0 is not spported as yet
	$this->LeaveMsgs = 1;

    if ((isset($_REQUEST['UseSSL']) && $_REQUEST['UseSSL'] == 1) || strpos($_REQUEST['MailType'], 's')) {
        $this->UseSSL = 1;
    } else {
        $this->UseSSL = 0;
    }

    $args = array();
    $this->pluginHandler->triggerEvent('onLoadPrefs', $args);
}


// Display a select box containing the email-personalities of the user
function loadpersonalities($type=null)
{
	$text = '';

	$users = $this->db->sqlarray("select UserAccount from Accounts where Account=?", "$this->username@$this->pop3host");

	if ($type)
	{
		$text = "$this->ReplyTo::$this->username@$this->pop3host::";

		foreach ($users as $user)
			$text .= "$user::";

		$text = substr($text, 0, -2);
	}
	else
	{
		// First append our local account name
		//$text = "<option value='$this->username@$this->pop3host'>$this->username@$this->pop3host</option>";
		$local_name = "$this->username@$this->pop3host";

		if ($this->ReplyTo) {

		    global $pref;

			if (strlen($this->RealName) > 0 && $pref['allow_FullName']) {
				$local_name = "$this->RealName &lt;$this->ReplyTo&gt;";
			} else {
				$local_name = $this->ReplyTo;
			}

			$text .= "<option value='$this->ReplyTo' selected>$local_name</option>";
		}

		$text .= "<option value='$this->username@$this->pop3host'>$this->username@$this->pop3host</option>";

		if (is_array($users))
		{
			foreach($users as $user)
				$text .= "<option value='$user'>$user</option>";
		}
	}

	return $text;
}


function allvars()
{
    if (!is_array($this->param) || !count($this->param)) return 0;
	foreach ($this->param as $key => $value)
	{
		$name = $this->escape_html($key);
		if (is_array($value))
		{
			foreach ($value as $v)
				print "$name = $v<br>";
		}
	}
}


// Return a WAP header
function wapheader()
{
    $header = "Content-type: text/vnd.wap.wml\n\n";
    $header .= "<?xml version=\"1.0\"?>\n";
    $header .= "<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\"";
    $header .= " \"http://www.wapforum.org/DTD/wml_1.1.xml\">\n\n";
    return $header;
}

// Retreive the SessionID from the users cookie
function cookie_read()
{
    // The SessionID is embeded into the URL
    if ( strpos($_SERVER['SCRIPT_NAME'], 'wap.php') !== false || strpos($_SERVER['SCRIPT_NAME'], 'xhtml.php') !== false)
        $this->auth->set_SessionID($this->genkey());
	else
		$this->auth->Account = $_COOKIE['Account'];
}

// send the cookie header
function cookie_header()
{
    $this->auth->update_session(session_id());
}

function cookie_header_delete()
{
	global $domains;

    // Optionally delete the users password if a remote POP3/IMAP account
    $this->cookie_read();
	preg_match('/@(.*)/', $this->auth->get_account(), $match=array() );

	// If the users domain isn't local, remove the password from the database
	if(!$domains[$match[1]])
	{
		// Find the users password
		$this->auth->getuser();

		// Change the users password to 'NULL'
		$this->auth->changepass(null, $this->auth->get_password());
	}

    $this->SessionID = "";

    $this->auth->update_session($this->SessionID);
}

function setupmsg()
{
	function handle_errors($errcode, $errstring, $file, $line)
	{
		$this->log->write_log( 'Error', "$errstring in file '$file' on line '$line'");

		if ( strpos($_SERVER['SCRIPT_NAME'], 'wap.pl', -6 ) !== false )
			print "<wml><card id='sent' title='Error'><p>Configuration Error: $errstring </p></card></wml>";
		else
		{
			eval ("\$errmsg = \"$pref[error_message]\";");
			print $errmsg;
		}

	}
	set_error_handler('handle_errors');
}


function myunescape($todecode)
{
    if (!$todecode)
    	return;

    $todecode = urldecode(strtr($todecode,"'",'%'));
    //$todecode = urldecode($todecode);

    return $todecode;
}


// Load a unique SessionID . Based on time  in secods, and 10char unique string
function genkey()
{
    $alpha = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'z', 'u', 'z', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0);
    $t = time();
	$k = '';
    for ($i=0; $i<10; $i++)
	{
        $num = rand(0,36);
        $k .= $alpha[$num];
    }

    return $t.$k;
}


function logintype()
{

	// Verify the users input is valid
	$language = $_REQUEST['Language'];
	$language = $this->checklanguage($language);

	$logintype = $_REQUEST['LoginType'];
	$logintype = $this->checklogintype($logintype);

	// If we are using the "Ajax" interface toggle support in user-settings table and revert to simple html templates

	if ($logintype == 'ajax')
	{
		$ajax = 1;
		$logintype = 'simple';
		$this->Ajax = 1;
	}
	else
		$ajax = 0;

	if ((isset($_REQUEST['UseSSL']) && $_REQUEST['UseSSL'] == 1) || strpos($_REQUEST['MailType'], 's')) {
		$this->UseSSL = 1;
	} else {
	    $this->UseSSL = 0;
	}

    // First check if another Language is specified
    if ($this->isset_chk($_REQUEST['Language']))
	{
		$query = 'UPDATE '.$this->db->get('UserSettings').' SET LoginType = ?, Language	= ?, Ajax = ?, UseSSL = ? WHERE Account = ?';
		$data  = array($logintype, $_REQUEST['Language'], $ajax, $this->UseSSL, $this->auth->get_account());

      	$this->db->sqldo($query, $data);
    }
    else
	{
        $query = 'UPDATE '.$this->db->UserSettings.' SET LoginType = ?, Ajax = ?, UseSSL = ? WHERE Account = ?';
		$data  = array($logintype, $ajax, $this->UseSSL, $this->auth->get_account());

        $res = $this->db->sqldo($query, $data);

		$query = 'select Language from '.$this->db->get('UserSettings').' where Account=?';

        $v = $this->db->getvalue($query, $this->auth->get_account());

        return $v;
    }

    return 0;
}

function mailtype()
{
	global $pref, $domains;

	// isset_chk broke $type and $mailserver
	// Set $type to $pref['mail_type'] if pop or imap is hardcoded
    if ($pref['mail_type'] == 'pop3' || $pref['mail_type'] == 'imap') {
        $type = $pref['mail_type'];
    } elseif ($domains[$this->pop3host]) {
    	$type = 'file';
    } else {

        $type = $_REQUEST['MailType'] ? $_REQUEST['MailType'] : 'pop3';     // Toggle POP or IMAP
    }

    if ((isset($_REQUEST['UseSSL']) && $_REQUEST['UseSSL'] == 1) || strpos($_REQUEST['MailType'], 's')) {
        $this->UseSSL = 1;
        $type = str_replace('s', '', $_REQUEST['MailType']);
    } else {
        $this->UseSSL = 0;
    }
    
    $server = ($pref['mailserver']) ? $pref['mailserver'] : $_REQUEST['MailServer'];

	// Turn MailAuth = 0 for username auth, otherwise 1 for user@domain
    $mailauth = ($this->MailAuth == 1 || $this->MailA == 1) ? 1 : 0;

    //$leavemsgs = ( $type == "imap" ) ? "1" : $this->LeaveMsgs;			// Toggle if we leave msgs on server, or access mail remotely
	$leavemsgs = 1;
    $account = $this->auth->get_account();
    $query = 'UPDATE '.$this->db->get('UserSettings').' set MailType = ?, MailServer = ?, MailAuth = ?, LeaveMsgs = ? where Account = ?';
	$data  = array($type, $server, $mailauth, $leavemsgs, $account);
	$this->db->sqldo($query, $data);
}

function startpage()
{
	$query = 'UPDATE '.$this->db->get('UserSettings').' set StartPage	= ? where Account = ?';
    $data  = array($_REQUEST['StartPage'], $this->auth->get_account());
    $this->db->sqldo($query, $data);
}

function saveautoreply_plesk($id, $autoreply)
{

	echo "IN HERE FOR PLESK, SAVE TO THEIR DB" . $id . ':' . $autoreply; 
	//assume that PHP bug of not reliably being able to simultaneously connect to multiple database servers still exists
	// so will copy pref, mod $pref[] settings to connect to psa database
	//connect to psa database
	//execute simple query
	//recreate valid/origional $this->db
	global $pref;
	$previous_sql_user = $pref['sql_user'];
	$previous_sql_pass = $pref['sql_pass'];
	$previous_sql_table = $pref['sql_table']; //table ~= database
	//set psa db settings
	$pref['sql_user'] = 'admin';
	$pref['sql_pass'] = file_get_contents('/etc/psa/.psa.shadow');
	$pref['sql_table'] = 'psa';
	$this->Global_Base(); //creates temporary psa database connection
	//if strlen($autoreply) < 1 then disable autoreply
	$query = 'SELECT  = ? WHERE Account= ?';
	$query = 'UPDATE  SET AutoReply = ? WHERE Account= ?';
	$data = array( $autoreply, $this->auth->get_account() );
	$res = $this->db->sqldo($query, $data);
	
	
	//now recreate origional db link
	$pref['sql_user'] = $previous_sql_user;
	$pref['sql_pass'] = $previous_sql_pass;
	$pref['sql_table'] = $previous_sql_table;
	$this->Global_Base(); //creates origional database connection
	
	
	// Prepare the SQL query
    //$query = 'UPDATE '.$this->db->get('UserSettings').' SET AutoReply = ? WHERE Account= ?';
    //$data = array( $autoreply, $this->auth->get_account() );
	//$this->db->sqldo($query, $data);
    //$res = $this->db->sqldo($query, $data);
}

function saveforward_plesk($id, $forward)
{

	echo "SAVE THE FORWARD HERE". $this->username. '@' . $this->pop3host . ':' . $id . ':' . $forward;
	$q = "UPDATE ";

}


function savesettings()
{
	global $pref;
	
	// Don't allow names with a ' breaks JS functions
	$RealName = str_replace(array(',', "'"), '', $_REQUEST['RealName']);

	// Load a list of user settings and verify the setting permissions
	$settings = $this->checksettings();

	$forward 	= ($settings['Forward'])? $settings['Forward'] : '';
	$autoreply  = ($settings['AutoReply']) ? $settings['AutoReply'] : '';
	$emptytrash = ($settings['EmptyTrash'])? $settings['EmptyTrash'] : 0;
	$newwindow 	= ($settings['NewWindow'])? $settings['NewWindow'] : 0;
	$htmled 	= ($settings['HtmlEditor'])? $settings['HtmlEditor'] : 0;
	$leavemsgs 	= ($settings['LeaveMsgs'])? $settings['LeaveMsgs'] : 0;
	$adv 		= ($settings['Advanced'])? $settings['Advanced'] : 0;
	$autotrash 	= ($settings['AutoTrash'])? $settings['AutoTrash'] : 0;
	$refresh 	= ($settings['Refresh'])? $settings['Refresh'] : 0;

	if(false && $pref['plesk'] == 1) //disabled 
		$this->saveautoreply_plesk($autoreply);

	if ($settings['LoginType'] == 'ajax')
	{
		$LoginType = 'simple';
		$Ajax = '1';
	}
	else	{
		$Ajax = '0';
		$LoginType = $settings['LoginType'];
	}

    // Prepare the SQL query
   $query = 'UPDATE '.$this->db->get('UserSettings').' SET

TopBg 			= ?,
OnColor 		= ?,
OffColor 		= ?,
TextHeadColor 	= ?,
SelectColor 	= ?,
HeaderColor 	= ?,
HeadColor 		= ?,
ThirdColor 		= ?,
SecondaryColor 	= ?,
BgColor 		= ?,
TextColor 		= ?,
PrimaryColor 	= ?,
VlinkColor 		= ?,
LinkColor 		= ?,
MboxOrder       = ?,
RealName        = ?,
EmailHeaders    = ?,
TimeZone        = ?,
MsgNum			= ?,
EmptyTrash      = ?,
NewWindow       = ?,
HtmlEditor      = ?,
ReplyTo			= ?,
Signature       = ?,
FontStyle       = ?,
LeaveMsgs       = ?,
Advanced        = ?,
AutoTrash       = ?,
LoginType       = ?,
Language		= ?,
Refresh         = ?,
DateFormat 		= ?,
TimeFormat 		= ?,
AutoComplete 	= ?,
EmailEncoding 	= ?,
DisplayImages 	= ?,
Ajax            = ?

WHERE
Account			= ?
';

    $data = array(

			  $_REQUEST['TopBg'],
			  $_REQUEST['OnColor'],
			  $_REQUEST['OffColor'],
			  $_REQUEST['TextHeadColor'],
			  $_REQUEST['SelectColor'],
			  $_REQUEST['HeaderColor'],
			  $_REQUEST['HeadColor'],
			  $_REQUEST['ThirdColor'],
			  $_REQUEST['SecondaryColor'],
			  $_REQUEST['BgColor'],
			  $_REQUEST['TextColor'],
			  $_REQUEST['PrimaryColor'],
			  $_REQUEST['VlinkColor'],
			  $_REQUEST['LinkColor'],
			  $settings['MboxOrder'],
			  $RealName,
			  $settings['EmailHeaders'],
			  $settings['TimeZone'],
			  $settings['MsgNum'],
			  $emptytrash,
			  $newwindow,
			  $htmled,
			  $settings['ReplyTo'],
			  $settings['Signature'],
			  $settings['FontStyle'],
			  $leavemsgs,
			  $adv,
			  $autotrash,
			  $LoginType,
			  $settings['Language'],
			  $refresh,
			  $settings['DateFormat'],
			  $settings['TimeFormat'],
			  $settings['AutoComplete'],
			  $settings['EmailEncoding'],
			  $settings['DisplayImages'],
			  $Ajax,
			  $this->auth->get_account());


	$res = $this->db->sqldo($query, $data);

	// Dont let the user save the forwarding address as themselves ( avoid mail loop )
	if ($forward == "$this->username@$this->pop3host")
		$forward = "";


	// Trigger onSetAutoReply event
	$args = array('autoreply' => $autoreply, 'forward' => $forward, 'account' => $this->Account);
	$this->pluginHandler->triggerEvent('onSetAutoReply', $args);
	if (isset($args['handled'])) {
	    return;
	}

    // Next, update the users AutoReply and mail-forwarding options if required
	$data = array($autoreply, $forward, "$this->username@$this->pop3host");
    $res = $this->db->sqldo("UPDATE Users set AutoReply = ?, Forward = ? where Account = ?", $data);
}

// Save the SQL PGP settings
function savesettingspgp($db)
{
    // Prepare the SQL query
    $query = 'UPDATE '.$this->db->get('UserSettings').' SET PGPenable = ?, PGPsign = ?, PGPappend = ? WHERE Account = ?';

	$PGPenable = $db['PGPenable'];
	$PGPsign   = ($db['PGPsign'])? $db['PGPsign'] : '0';
	$PGPappend = ($db['PGPappend'])? $db['PGPappend'] : '0';

	$data =  array($PGPenable, $PGPsign, $PGPappend, $this->auth->get_account());

	return $this->db->sqldo($query, $data);
}

// Load a hash containing the users settings, with permission control via the Webadmin.
// Also escape the users input and take out any HTML tags
function checksettings()
{
	global $pref;

	$settings = array();

	if( !$pref['allow_DateFormat'] )
		$settings['DateFormat'] = $this->DateFormat;
	else
		$settings['DateFormat'] = htmlentities($_REQUEST['DateFormat']);

	if( !$pref['allow_TimeFormat'] )
		$settings['TimeFormat'] = $this->allow_TimeFormat;
	else
		$settings['TimeFormat'] = htmlentities($_REQUEST['TimeFormat']);

	if ( !$pref['allow_Forward'] )
		$settings['Forward'] = $this->Forward;
	else
	{
		$settings['Forward'] = htmlentities($_REQUEST['Forward']);
		$emailexp = '|([^":\s<>()/;]*@[^":\s<>()/;]*)|';
		if (preg_match($emailexp, $settings['Forward'], $m))
			$settings['Forward'] = $m[1];
		else
			$settings['Forward'] = '';
	}

	if ( !$pref['allow_MboxOrder'] )
		$settings['MboxOrder'] = $this->MboxOrder;
	else
		$settings['MboxOrder'] = htmlentities($_REQUEST['MboxOrder']);

	if ( !$pref['allow_FullName'] )
		$settings['RealName'] = $this->RealName;
	else
		$settings['RealName'] = htmlspecialchars($_REQUEST['RealName']); // Need to support International names, unquoted

	// Remove any ,'s from the username
	$settings['RealName'] = str_replace(',', '', $settings['RealName']);

	// Default value
	$settings['EmailHeaders'] = htmlentities($_REQUEST['EmailHeaders']);

	if ( !$pref['allow_TimeZone'] )
		$settings['TimeZone'] = $this->TimeZone;
	else
	{
		$settings['TimeZone'] = stripslashes(htmlentities($_REQUEST['TimeZone']));
	}

	if ( !$pref['allow_MsgNum'] )
		$settings['MsgNum'] = $this->MsgNum;
	else
		$settings['MsgNum'] = htmlentities($_REQUEST['MsgNum']);

	if ( !$pref['allow_EmptyTrash'] )
		$settings['EmptyTrash'] = $this->EmptyTrash;
	else
	{
		if ($this->isset_chk($_REQUEST['EmptyTrash']))
			$settings['EmptyTrash'] = htmlentities($_REQUEST['EmptyTrash']);
		else
			$settings['EmptyTrash'] = 0;
	}

	// Default value
	if ($this->isset_chk($_REQUEST['NewWindow']))
		$settings['NewWindow'] = htmlentities($_REQUEST['NewWindow']);
	else
		$settings['NewWindow'] = 0;

	if ( !$pref['allow_HtmlEditor'] )
		$settings['HtmlEditor'] = $this->HtmlEditor;
	else
	{
		if ($this->isset_chk($_REQUEST['HtmlEditor']))
			$settings['HtmlEditor'] = htmlentities($_REQUEST['HtmlEditor']);
		else
			$settings['HtmlEditor'] = 0;
	}

	if( !$pref['allow_ReplyTo'] )
		$settings['ReplyTo'] = $this->ReplyTo;
	else
		$settings['ReplyTo'] = htmlentities($_REQUEST['ReplyTo']);

	if ( !$pref['allow_Signature'] )
		$settings['Signature'] = $this->Signature;
	else
		$settings['Signature'] = $this->escape_jscript($_REQUEST['Signature']);

	if ( !$pref['allow_FontStyle'] )
		$settings['FontStyle'] = $this->FontStyle;
	else
		$settings['FontStyle'] = htmlentities($_REQUEST['FontStyle']);

	if ( !$pref['allow_LeaveMsgs'] )
		$settings['LeaveMsgs'] = $this->LeaveMsgs;
	else
		$settings['LeaveMsgs'] = htmlentities($_REQUEST['LeaveMsgs']);

	if ( !$pref['allow_AdvancedPopup'] )
		$settings['Advanced'] = $this->Advanced;
	else
		$settings['Advanced'] = htmlentities($_REQUEST['Advanced']);

	if ( !$pref['allow_AutoTrash'] )
		$settings['AutoTrash'] = $this->AutoTrash;
	else
		$settings['AutoTrash'] = htmlentities($_REQUEST['AutoTrash']);

	if ( !$pref['allow_AutoComplete'] )
		$settings['AutoComplete'] = $this->AutoComplete;
	else
		$settings['AutoComplete'] = htmlentities($_REQUEST['AutoComplete']);

	if ( !$pref['allow_EmailEncoding'] )
		$settings['EmailEncoding'] = $this->EmailEncoding;
	else
		$settings['EmailEncoding'] = htmlentities($_REQUEST['EmailEncoding']);

    if (!$pref['allow_DisplayImages']) {

    } else {
        $settings['DisplayImages'] = htmlentities($_REQUEST['DisplayImages']);
    }
	// Verify the input
	$settings['LoginType'] = $this->checklogintype( htmlentities($_REQUEST['LoginType']) );

	if ( !$pref['allow_Language'] )
		$settings['Language'] = $this->Language;
	else
		$settings['Language'] = htmlentities($_REQUEST['Language']);

	// Verify the input
	$settings['Language'] = $this->checklanguage($settings['Language']);

	if ( !$pref['allow_Refresh'] )
		$settings['Refresh'] = $this->Refresh;
	else
		$settings['Refresh'] = htmlentities($_REQUEST['Refresh']);

	if ( !$pref['allow_Forward'] )
		$settings['Forward'] = $this->Forward;
	else
		$settings['Forward'] = htmlentities($_REQUEST['Forward']);

	$settings['AutoReply'] = htmlentities($_REQUEST['AutoReply'], ENT_NOQUOTES, "UTF-8");

	// Return the saved values
	return $settings;
}


// Change the color settings for the user
function changecolor($var)
{
    $query = ( "UPDATE ".$this->db->get('UserSettings')." SET
TopBg = ?,
OnColor = ?,
OffColor = ?,
TextHeadColor = ?,
SelectColor = ?,
HeaderColor = ?,
HeadColor = ?,
ThirdColor = ?,
SecondaryColor = ?,
BgColor = ?,
TextColor = ?,
PrimaryColor = ?,
VlinkColor = ?,
LinkColor = ?

WHERE Account='".$this->auth->get_account()."'");

    $data = array(	$var['TopBg'], 			$var['OnColor'],
					$var['OffColor'],		$var['TextHeadColor'],
					$var['SelectColor'], 	$var['HeaderColor'],
					$var['HeadColor'],		$var['ThirdColor'],
					$var['SecondaryColor'],	$var['BgColor'],
					$var['TextColor'],		$var['PrimaryColor'],
					$var['VlinkColor'],		$var['LinkColor'] );

	$this->db->sqldo($query, $data);
}

// Save the users WAP settings
function save_wapsettings()
{
    // Prepare the SQL query
    $query = "UPDATE ".$this->db->get('UserSettings')." SET RealName = ?, ReplyTo = ? WHERE Account = ?";
    $data = array($_REQUEST['RealName'], $_REQUEST['ReplyTo'], $this->auth->get_account() );
	$this->db->sqldo($query, $data);
}

function saveautoreply($autoreply)
{
    // Prepare the SQL query
    $query = 'UPDATE '.$this->db->get('UserSettings').' SET AutoReply = ? WHERE Account= ?';
    $data = array( $autoreply, $this->auth->get_account() );
	$this->db->sqldo($query, $data);
}

/****
 * Anti-Spam Functions
 * Add a Spammers email to the SpamDB . Referenced by the users account
 ****/

function addspamer()
{
	if ( preg_match('/([^":\s<>()\/;]*@[^":\s<>()\/;]*)/', htmlentities($_REQUEST['SpamEmail']), $match ) )
    	$addr = $match[1];
	else
		die('bad email');

    //if ( strpos($addr, '@') === false ) return ;

    $addr = str_replace(array('<', '>', '&gt;', '&lt;'), '', $addr); //$addr =~ s/&gt;|&lt;|<|>//g;

    // Prepare the SQL query
    $query = "INSERT INTO ".$this->db->get('SpamDB')." (SpamEmail, Account) VALUES( ? , ?)";
    $data = array( $addr, $this->auth->get_account() );
	$res = $this->db->sqldo($query, $data);
}

// Delete a spammer from the SpamDB
function delspamer()
{
    $del = $_REQUEST['spamdel'];
    $query = "DELETE FROM ".$this->db->get('SpamDB')." WHERE SpamEmail=? AND Account=?";
	$data = array( $del, $this->auth->get_account() );
	$this->db->sqldo($query, $data);
}

// Create a select box with the current spam emails
function spam_select()
{
    $tmp = '';

	$query = "select SpamEmail from ".$this->db->get('SpamDB')." where Account=?";

    $arr = $this->db->sqlarray($query, $this->auth->get_account());

    foreach ($arr as $val)
			 $tmp .= "<option value='$val'>$val</option>";

    return $tmp;
}

function spam_hash()
{
    $tmp = array();

	$query = "select SpamEmail from ".$this->db->get('SpamDB')." where Account=?";

	$arr = $this->db->sqlarray($query, $this->auth->get_account());

	foreach ($arr as $val)
	{
		if ($this->isset_chk($tmp[$val]))
			$tmp[$val]++;
		else
			$tmp[$val] = 0;
	}

    return $tmp;
}

/*****
 * Message Sorting Functions
 * Add a new Mailbox sort for the users account . Emails will be
 * routed to another folder if the sort matches
 *****/

function addsort()
{
    if ( !$this->isset_chk($_REQUEST['sort_email']) && !$this->isset_chk($_REQUEST['sort_subject'])) return;

    // Prepare the SQL query
    $query = "INSERT INTO {$this->db->MailSort} (EmailAddress, EmailSubject, EmailFolder, Account) VALUES ( ? , ? , ?, ?)";
	$data  = array($_REQUEST['sort_email'], $_REQUEST['sort_subject'], $_REQUEST['sort_box'], $this->auth->get_account() );

    $resp = $this->db->sqldo($query, $data);
}


// Create a select box with the users active filters
function getsort($type, $format=null)
{
	$h = array();
	$tmp = '';

	$type = Filter::cleanSqlFieldNames($type);

	$query = "SELECT $type, EmailFolder
			  FROM {$this->db->MailSort}
			  WHERE Account = ?";

	$data = array($this->auth->get_account());
    $arr = $this->db->sqlmultihash($query, $data);

    foreach ($arr as $v)
	{
		if (!$v[$type])
			continue;

        if ( $format == "hash" )
            $h[$v[$type]] = $v['EmailFolder'];
        else
            // Build the select box
            $tmp .= "<option value=\"" . base64_encode($v[$type]) . "\">" . htmlentities($v[$type], ENT_QUOTES, 'UTF-8') . " -&gt; {$v['EmailFolder']}</option>";

    }

    // What type to return
    if ( $format == "hash" )
        return $h;

    return $tmp;
}

// Delete a spammer from the SpamDB
function delsort($del, $type)
{
    $type = Filter::cleanSqlFieldNames($type);
	$query = "DELETE FROM ".$this->db->get('MailSort')." where $type=? and Account=?";
	$data  = array($del, $this->auth->get_account());
    $this->db->sqldo($query, $data);

}

// Make a select box with our current domains
function domainbox($domain=null, $type='local')
{
	if ($type == 'local') {
		global $domains;
	    $tmp = '';

	    if (count($domains) > 0) {
	        ksort($domains);
	    }

	    foreach ( array_keys($domains) as $k )
	    {
	    	if ($domain && $k == $domain)
	        	$tmp .= "<option value='$k' selected>$k</option>\n";
	        else
	        	$tmp .= "<option value='$k'>$k</option>\n";
	    }

	    return $tmp;

	} else {

		global $pref;

		$list = '';

		$doms = explode(',', $pref['allowed_domains']);
		sort($doms);
		foreach ($doms as $dom) {
			$dom = trim($dom);

			if (empty($dom)) {
				continue;
			}

			$list .= "<option value='$dom'>$dom</option>\n";
		}

		return $list;
	}
}


function mailserverbox()
{
	global $pref;

	$list = '';

	$servers = explode(',', $pref['allowed_mailservers']);
	sort($servers);
	foreach ($servers as $server) {
		$server = trim($server);

		if (empty($server)) {
			continue;
		}

		$list .= "<option value='$server'>$server</option>\n";
	}

	return $list;
}


function languages($type=null)
{
	global $pref, $language;
    $tmp = '';
	ksort($language);

    foreach ( array_keys($language) as $k )
	{
        $lang = $k;

        if ( $lang == $this->Language && $type == 2 )
            $tmp .= "<option value='$lang' selected>$language[$lang]</option>\n";
        elseif ( $lang == $pref['Language'] && !$type )
            $tmp .= "<option value='$lang' selected>$language[$lang]</option>\n";
        else
            $tmp .= "<option value='$lang'>$language[$lang]</option>\n";
    }

    return $tmp;
}

// The user is not correctly authenticated . Throw an error and exit
function auth_error()
{
	global $domains, $pref;

	if ($this->isset_chk($_REQUEST['XUL']) || $this->XUL)
	{
		$path = "html/$this->Language/auth_xulerror.html";
		$var['URL'] = "html/$this->Language/auth_timeout.html";
		$this->httpheaders();
		print $this->parse($path, $var);
    	exit;
	}

    if ($pref['opensource']) {
    	header('Location:index.php?error=auth');
    	exit;
    }
	if (file_exists("{$pref['install_dir']}/html/vhosts/{$_SERVER['HTTP_HOST']}.html"))
		$path = "html/vhosts/{$_SERVER['HTTP_HOST']}.html";
	else
		$path = "html/$this->Language/login.html";

	if (empty($this->pop3host)) {
		if ($pref['install_type'] == 'server') {
			$external = 0;
		} else {
			$external = 1;
		}
	} elseif (!$domains[$this->pop3host])
		$external = 1;
	else
		$external = 0;

	if ( strpos($_SERVER['SCRIPT_NAME'], 'wap.php') !== false )
    	$path = "html/$this->Language/wml/login.wml";

    elseif( strpos($_SERVER['SCRIPT_NAME'] , 'xhtml.php') !== false)
		$path = "html/$this->Language/xhtml/login.xhtml";

	$this->httpheaders();

    if (!is_object($this->log)) $this->log = new Log;

    //if ($this->LoginType != 'xul' && !$this->Ajax) {
        print $this->parse( $path, array('languagebox' => $this->languages(),
          								 'ErrorBody'   => $this->parse("html/$this->Language/msg/login_mailautherror.html"),
          								 'browser'     => $this->browser,
          								 'domainbox'   => $this->domainbox(),
          								 'mailtype'    => $this->print_mailtypes(),
    	  								 'external'    => $external,
										 'Ajax'        => $_REQUEST['ajax']) );
    //}

    $this->log->write_log( 'Error', 'Wrong Password' );
    exit;
}

function reserved_user()
{
    //global $reserved;
    $this->httpheaders();
    $user = $this->auth->get_username();
    //$rnames = join(', ', array_keys($reserved));
    print $this->parse( "html/$this->Language/auth_reserved.html", array('user' => $user) );
    $this->log->write_log( 'Error', "Reserved User $user" );
    exit;
}

function nosuchuser()
{
    $this->httpheaders();
    print $this->parse("html/$this->Language/auth_nouser.html");
    $this->log->write_log( 'Error', 'No Such User' );
	exit;
}

function userexists()
{
    $this->httpheaders();
    print $this->parse("html/$this->Language/auth_userexists.html");
    $this->log->write_log( 'Error', 'No Such User' );
	exit;
}

function userdisabled()
{
    $this->httpheaders();
    print $this->parse("html/$this->Language/auth_disabled.html");
    $this->log->write_log( 'Error', "User login disabled" );
	exit;
}

function session_error($path=null)
{
    if ($this->isset_chk($_REQUEST['ajax']) && strpos($_SERVER['REQUEST_URI'], 'parse.php') === false)
    {
        header("Content-type: text/xml; charset: utf-8");
        echo "<ErrorMessage action=\"logout\">Sorry, Your Session Has Timed out</ErrorMessage>";
        exit;
    }

    if ($this->isset_chk($_REQUEST['XUL']))
	{
		$path = "html/$this->Language/auth_xulerror.html";
		$var['URL'] = "html/$this->Language/auth_timeout.html";
	}
	else
	{
		if ( !$path )
			$path = "html/$this->Language/auth_timeout.html";

		$this->httpheaders();
	}

    print $this->parse($path, $var);
    exit;
}


function icon_hash()
{
    $icon = array(	"html" => 'html.gif',
      				"htm"  => 'html.gif',
      				"gif"  => 'gif.gif',
					"png"  => 'gif.gif',
					"jpeg" => 'jpg.gif',
      				"jpg"  => 'jpg.gif',
      				"mpeg" => 'mpeg.gif',
      				"exe"  => 'exe.gif',
      				"txt"  => 'txt.gif',
      				"doc"  => 'doc.gif',
					"pdf"  => 'pdf.gif',
      				"xls"  => 'xls.gif',
      				"zip"  => 'zip.gif',
      				"tgz"  => 'zip.gif',
      				"tar"  => 'zip.gif',
      				"gz"   => 'zip.gif');

    return $icon;
}

function bad_reg()
{
    print "
Content-Type: text/html

<h2>The @Mail Software is not registered for this machine</h2>
<h3>Use the <a href=\"webadmin/\">webadmin utility</a> to register the software for your server</h3>";
    //$this->cleanup();
}


// Print the different @Mail select box types
function print_mailtypes()
{
	global $pref;

    $type = htmlentities($_REQUEST['MailType']);

    if ( $pref['mail_type'] == 'pop3') {
        if ($pref['mail_type_ssl'] == 'deny') {
            $select = "<input type='hidden' name='MailType' value='pop3'>";
        } elseif ($pref['mail_type_ssl'] == 'force') {
            $select = "<input type='hidden' name='MailType' value='pop3s'>";
        } elseif ($pref['mail_type_ssl'] == 'allow') {
            $select = "<select name='MailType' class='protocol'><option value='pop3'>POP3</option><option value='pop3s'>POP3 Secure</option></select>";
            $select = str_replace("value='$type'", "value='$type' selected", $select);
        }
    } elseif ( $pref['mail_type'] == 'imap') {
        if ($pref['mail_type_ssl'] == 'deny') {
            $select = "<input type='hidden' name='MailType' value='imap'>";
        } elseif ($pref['mail_type_ssl'] == 'force') {
            $select = "<input type='hidden' name='MailType' value='imaps'>";
        } elseif ($pref['mail_type_ssl'] == 'allow') {
            $select = "<select name='MailType' class='protocol'><option value='imap'>IMAP</option><option value='imaps'>IMAP Secure</option></select>";
            $select = str_replace("value='$type'", "value='$type' selected", $select);
        }
    } elseif ( $pref['mail_type'] == 'pop3imap') {
        if ($pref['mail_type_ssl'] == 'deny') {
            $select = "<select name='MailType' class='protocol'><option value='imap'>IMAP</option><option value='pop3'>POP3</option></select>";
            $select = str_replace("value='$type'", "value='$type' selected", $select);
        } elseif ($pref['mail_type_ssl'] == 'force') {
            $select = "<select name='MailType' class='protocol'><option value='imaps'>IMAP Secure</option><option value='pop3s'>POP3 Secure</option></select>";
        $select = str_replace("value='$type'", "value='$type' selected", $select);
        } elseif ($pref['mail_type_ssl'] == 'allow') {
            $select = "<select name='MailType' class='protocol'><option value='imap' >IMAP</option><option value='imaps'>IMAP Secure</option><option value='pop3'>POP3</option><option value='pop3s'>POP3 Secure</option></select>";
            $select = str_replace("value='$type'", "value='$type' selected", $select);
        }
    }

    return $select;
}

// Clean the @Mail temp file directory
function clean_tmp($dir=null) {
	global $pref;

	$sessionId = session_id();
	$files = array();

    if (is_dir($pref['user_dir']."/tmp/$dir") && $dh = opendir($pref['user_dir']."/tmp/$dir")) {

    	while (false !== ($file = readdir($dh))) {
    	  	array_push($files, $file); //echo "$file\n";
    	}
    
        closedir($dh);

	    $this->clean_tmp_dir("{$pref['user_dir']}/tmp/$dir", $files);
    }
}

function clean_tmp_dir($dir=null, $files)
{
    // dont go up to any parent dirs
    if (is_string($dir) && strpos($dir, '../') !== false)
        return;

	global $pref;
	$sessionId = session_id();

    $timeout = "7200";    // Number of seconds (use 7200 for 2hrs)
    $pgpTimeout = "1800"; // Number of secs for PGP files
    $cacheTimeout = "1800"; // Number of secs for PGP files

    $time = time();

    foreach ($files as $file)
	{

		// Don't delete the . or .. dir, special .htaccess or index.html in the root dir ( in case users have in the httpd.conf Options Indexes on, we don't want people surfing the tmp dir via the Web!)
        if ( strpos($file, '../') !== false || $file == "." || $file == ".." || $file == '.htaccess' || $file == ".svn" || "$dir/$file" == "{$pref['user_dir']}/tmp/index.html")
            continue;

		if(is_dir("$dir/$file")) {
			$this->clean_tmp($file);
			continue;
		}

		$atime = fileatime("$dir/$file");
		$mtime = filemtime("$dir/$file");
        $secs = $time - $mtime;

        // If older then 2 hrs , delete!
        if ( $secs > $timeout )
            unlink("$dir/$file");

        // If pgp files are older than 30 minutes, delete
        elseif (strpos($file, '.ht') === false && ($secs > $pgpTimeout))
        	unlink("$dir/$file");

        // If the cache files are older then 1hr, delete
		elseif((preg_match('/\.data$/', $file)) && ($secs > $cacheTimeout))
        	unlink("$dir/$file");
    }

    if ($sessionId) // "special clean" too
    {
		if (file_exists("$dir/.ht$sessionId"))
    		unlink("$dir/.ht$sessionId") ;
    }

	$this->clean_tmp_check($dir);

}

function clean_tmp_check($dir)
{
	$files = array();
	$sub = explode('/tmp/', $dir);
	// Return if null, no child dir, or has .. in the directory
	if(!$dir || !$sub[1] || preg_match('/\.\./', $dir))
		return;

    $dh = opendir($dir);

	while (false !== ($file = readdir($dh))) {
	  	array_push($files, $file); //echo "$file\n";
	}

    closedir($dh);

	// If only the . and .. directory, its empty, delete
	if(count($files) == 2)
		rmdir($dir);

}
// Get the profile for the user
function getprofile($account=null)
{
	if (empty($account))
		$account = $this->auth->get_account();

	$h = $this->db->sqlhash("select * from Users where Account = ?", $account);

    $h['DateCreate'] = preg_replace('/(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)/', "$3/$2/$1 $4:$5:$6", $h['DateCreate']);
    $h['DateModified'] = preg_replace('/(\d\d\d\d)(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)/', "$3/$2/$1 $4:$5:$6", $h['DateModified']);

    return $h;
}

// Save the profile for the user
function saveprofile()
{
    $user = $this->getinfo();

    $update = "UPDATE Users SET ";
    $data = array();

    // Insert the users settings
    foreach ( $user as $k => $v)
	{
	    $k = Filter::cleanSqlFieldNames($k);
        if ( $k == 'LoginType' || $k == 'Language' || $k == 'Service' ||
             $k == 'UserQuota' || $k == 'UGroup' || $k == 'PasswordQuestion')
             continue;

        $update .= "$k = ?, ";
        $data[] = $v;
    }

    $data[] = $this->auth->get_account();
    $update = preg_replace('/, $/', '', $update);
    $update .= ", DateModified = {$this->db->NOW} where Account = ? ";
    $this->db->sqldo($update, $data);
}

function getinfo($user=null)
{
	global $pref;

	if (!$user)
	{
		$user = array();
		$account = $this->auth->get_account();
	}
	else
		$account = "$user[0]@$user[2]";

	$profile = $this->getprofile($account);

	// Cross validate the users input is allowed via the Webadmin restrictions
	if ($pref['pallow_FirstName'])
    	$user['FirstName'] = ucfirst($_REQUEST['FirstName']) ;
	else
		$user['FirstName'] = $profile['FirstName'];

	if($pref['pallow_LastName'])
    	$user['LastName']  = ucfirst( $_REQUEST['LastName'] );
	else
	 $user['LastName'] = $profile['LastName'];

	if($pref['pallow_PasswordQuestion'])
    	$user['PasswordQuestion'] = $_REQUEST['PasswordQuestion'];
    else
		$user['PasswordQuestion'] = $profile['PasswordQuestion'];

	if($pref['pallow_OtherEmail'])
		$user['OtherEmail'] = $_REQUEST['OtherEmail'];
    else
		$user['OtherEmail'] = $profile['OtherEmail'];

	if($pref['pallow_DOB'])
	{
		$user['BirthDay']         = $_REQUEST['BirthDay'];
    	$user['BirthMonth']       = $_REQUEST['BirthMonth'];
    	$user['BirthYear']        = $_REQUEST['BirthYear'];
    }
	else
	{
		$user['BirthDay']         = $profile['BirthDay'];
    	$user['BirthMonth']       = $profile['BirthMonth'];
    	$user['BirthYear']        = $profile['BirthYear'];
	}

	if ($pref['pallow_Gender'])
		$user['Gender'] = $_REQUEST['Gender'];
    else
		$user['Gender'] = $profile['Gender'];

	if ($pref['pallow_Industry'])
		$user['Industry'] = $_REQUEST['Industry'];
    else
		$user['Industry'] = $profile['Industry'];

	if ($pref['pallow_Occupation'])
		$user['Occupation'] = $_REQUEST['Occupation'];
    else
		$user['Occupation'] = $profile['Occupation'];

	if ($pref['pallow_Address'])
		$user['Address'] = $_REQUEST['Address'];
    else
		$user['Address'] = $profile['Address'];

	if ($pref['pallow_City'])
		$user['City'] = $_REQUEST['City'];
    else
		$user['City'] = $profile['City'];

	if ($pref['pallow_State'])
		$user['State'] = $_REQUEST['State'];
    else
		$user['State'] = $profile['State'];

	if ($pref['pallow_PostCode'])
		$user['PostCode'] = $_REQUEST['PostCode'];
    else
		$user['PostCode'] = $profile['PostCode'];

	if ($pref['pallow_Country'])
		$user['Country'] = $_REQUEST['Country'];
	else
		$user['Country'] = $profile['Country'];

	if ($pref['pallow_TelHome'])
    	$user['TelHome'] = $_REQUEST['TelHome'];
	else
		$user['TelHome'] = $profile['TelHome'];

	if ($pref['pallow_FaxHome'])
    	$user['FaxHome']   = $_REQUEST['FaxHome'];
	else
		$user['FaxHome']   = $profile['FaxHome'];

	if ($pref['pallow_TelWork'])
   	 	$user['TelWork'] = $_REQUEST['TelWork'];
	else
		$user['TelWork'] = $profile['TelWork'];

	if ($pref['pallow_FaxWork'])
    	$user['FaxWork'] = $_REQUEST['FaxWork'];
	else
		$user['FaxWork'] = $profile['FaxWork'];

	if ($pref['pallow_TelMobile'])
    	$user['TelMobile'] = $_REQUEST['TelMobile'];
	else
		$user['TelMobile']   = $profile['TelMobile'];

	if ($pref['pallow_TelPager'])
    	$user['TelPager'] = $_REQUEST['TelPager'];
	else
		$user['TelPager'] = $profile['TelPager'];

    // Toggle if we accept users to specify the UGroup / Quota via a HTTP call
    if($pref['allow_advanceduser'])
	{
    	$user['UGroup']    = $_REQUEST['UGroup'];
    	$user['UserQuota'] = $_REQUEST['UserQuota'];
    }

    $user['LoginType'] = ($_REQUEST['LoginType'])? $_REQUEST['LoginType'] : $this->LoginType;

	// Verify the input
	$user['LoginType'] = $this->checklogintype($user['LoginType']);

    $user['Service'] = ($_REQUEST['Service'])? $_REQUEST['Service'] : '3';

    return $user;
}

// Users can embded dodgey Javascript/ActiveX controls into our fields to mess with our account, get SesisonID's, cookies, etc. Remove such references!
// (code originally by John D. Hardin)
function escape_jscript($txt)
{
	$txt = $this->escape_images($txt);

    if (strpos($txt, '<') !== false)
    {

        $txt = preg_replace('/<(META|APP|SCRIPT|OBJECT|EMBED|FRAME|IFRAME|BASE|BODY)(\s|>)/i', '<DEFANGED_$1$2', $txt);

        // Take out XSS attempt at loading a script
        $txt = preg_replace('/<SCRIPT.*?>/i', 'DEFANGED_SCRIPT', $txt);

		$txt = preg_replace('/On(Abort|Blur|Change|Click|DblClick|DragDrop|Error|Focus|KeyDown|KeyPress|KeyUp|Load|MouseDown|MouseMove|MouseOut|MouseOver|MouseUp|Move|Reset|Resize|Select|Submit|Unload)/i', 'DEFANGED_On$1', $txt);
    }

    if (preg_match('/["\047][^"\047\s]*&#x?[1-9][0-9a-f]/i', $txt))
    {

	/* Keeps looping, needs to be checked.

        while (preg_match('/["\047][^"\047\s]*&#((4[6-9]|5[0-8]|6[4-9]|[78][0-9]|9[07-9]|1[0-1][0-9]|12[0-2]))/', $txt, $match))
        {
            $char = chr($match[1]);
            $txt = preg_replace('/&#$1;?/i', $char, $txt);
        }
	*/

        while (preg_match('/["\047][^"\047\s]*&#(x(2[ef]|3[0-9a]|4[0-9a-f]|5[0-9a]|6[1-9a-f]|7[0-9a]))/i', $txt, $match))
        {
            $char = chr( hexdec("0$match[1]"));
            $txt = preg_replace('/&#$1;?/i', $char, $txt);
        }
    }

    if (preg_match('/["\047][^"\047\s]*%[2-7][0-9a-f]/i', $txt))
    {
        while (preg_match('/["\047][^"\047\s]*%((2[ef]|3[0-9a]|4[0-9a-f]|5[0-9a]|6[1-9a-f]|7[0-9a]))/i', $txt, $match))
        {
            $char = chr(hexdec("0x$match[1]"));
            $txt = preg_replace("/%$match[1]/i", $char, $txt);
        }
    }

    preg_replace('/(["\047])([a-z]+script|mocha):/i', '${1}DEFANGED_$2:', $txt);
    preg_replace('/(["\047])&{/', '${1}DEFANGED_&{', $txt);

    return $txt;
}


function permission_error($error)
{
    $this->httpheaders();
    print $this->parse( "html/english/auth_permission.html", array('error' => $error) );
    $this->log->write_log( 'Error', 'Permission denined ' . $error );
}


// Find the servers hostname, and append the URL to a string
// Used for the multi-server configuration where the mime/spell
// check feature is local to a single machine
function findserver()
{
	$url;
	$hostname;

	// Specify a list of server hostnames, with the full URL to the server
	$h = array(
        'au.cgisupport.com' => 'http://server1.com/',
        'server2' => 'http://server2.com/');

	// Find the hostname of the machine, by running the Unix 'hostname' command
	$hostname = `hostname`;

	return $h[$hostname];

}

function bgtrans()
{
	global $pref;

	// Lowercase the color name and take away the # extension
	$bg = strtolower($this->BgColor);
	$bg = str_replace('#', '', $bg);

	if( file_exists($pref['install_dir']."/imgs/trans-corner-$bg.gif") && $this->Language != "arabic")
		$path = "imgs/trans-corner-$bg.gif";
	else
		$path = "imgs/trans-corner-blank.gif";

	$this->Bgtrans = $path;

	return 0;
}

function escape_pathname($path)
{
	// Escape going down one directory and pipes
	$path = str_replace('../', '/', $path); //~ s/\.\.//g;
	$path = str_replace('|', '\|', $path); //~ s/\|//g;
	return $path;
}


// Check the users login type
function checklogintype($value)
{

	// If using the Advanced interface, switch the template to XUL for FF
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'gecko') !== false && $value == 'xp')
	$value = "xul";

	if($value != "simple" && $value != "xul" && $value != "xp" && $value != 'ajax' && $value != 'blue_pane')
	{
		//$this->log->write_log( 'Error', "Security Breach - Invalid login type -$value-");
		die("Invalid logintype '$value'");
	}
	return $value;
}


function invalidchars($user)
{
    $this->httpheaders();
    print $this->parse("html/$this->Language/auth_invalidchars.html", array('user' => $user));
    $this->log->write_log( 'Error', "Invalid Chars $this->username");
    exit;
}


function outlook_date()
{
	$seconds = time();

	$days = intval($seconds / 60 / 60 / 24);

	// Really need a timezone check here
	// 25569 < GMT, 25570 > GMT?!
	$days_since_1899 = "25570";

	$numdays = $days + $days_since_1899;

	//$time = localtime(time());
	//$startday = $time;
	//$startday = preg_replace('/\d\d:\d\d:\d\d\s\d\d\d\d/', '', $startday);

	//$seconds = strtotime($startday);
	$secondsMidnight = strtotime('today 12am');
	//$secsnow = strtotime($time);

	$diff = $seconds - $secondsMidnight;

	$perc = $diff / 86400;

	$perc = sprintf("%6f", $perc);
	$perc = preg_replace('/\d+\./', '', $perc);

	return "$numdays.$perc";
}


/**
 * Determine whether a variable is set
 *
 * if is set the fucntion will return the value
 *
 * @param string/int $value
 * @return string/int
 */
function isset_chk($value)
{
    if(isset($value) && !empty($value)) {
        return  $value;
    } else {
        return 0;
    }

}

// Generate a unique Salt, for a crypt() password entry
function salt()
{
    $salt = '';
    $itoa64 = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

    // to64
    for ( $i = 0 ; $i < 8 ; $i++ )
	{
        $rand = mt_rand(0, 62);
        $salt .= $itoa64[$rand];
    }

    return '$1$'.$salt.'$';
}

function buildtype()
{
    return $this->parse("html/$this->Language/calendar/type-select.html");
}


function generatepermissions($user, $db, $field, $accounts)
{
	global $pref;


	$user = str_replace("'", '', $user);
    $user = $this->db->quote($user);
    $db = Filter::cleanSqlFieldNames($db);
    $field = Filter::cleanSqlFieldNames($field);

	// Create a string containing the list of permissions for Javascript to use
	if (is_array($accounts))
	{
    	foreach ($accounts as $account)
    	{
    		$account = $this->db->quote($account);

    		// If the element is an email-address
    		if (strpos($account, '@') != false)
    		{
    			$value = $this->db->getvalue("select CONCAT($db.Account, ':', Users.id, ':', $db.Permissions) from $db, Users where $db.Account=$account and Users.Account=$account and $db.$field=$user ");

 				if(!$value)
				$value = $this->db->getvalue("select CONCAT(Account, ':', id, ':', Permissions) from $db where Account=$account");

				$permissions .= $value . ",";

    		}
    		else
    		{
    			$account = str_replace("'", '', $account);

    			// Otherwise the reference points to a group, load the group-name
    			$users = $this->findgroups($account);

    			if ($account == "All Users" && $pref['GlobalAbook'] && $this->param('frames') != "To,Cc,Bcc")
    				$users[0] = "All Users" ;

    			// If users exist in the group, append the action
                if(!$users[0])
                    $users[0] = "$account";

				$grouppermissions .= "GroupNames[\"$account\"] = \"$account\";";

				$permission = $this->db->sqlgetfield("select $db.Permissions from $db where $db.Account=? and $field=$user", array($account) );

				if ($permission)
					$grouppermissions .= "AddedGroups[\"$account\"] = 'Write';";
				else
					$grouppermissions .= "AddedGroups[\"$account\"] = 'Read';";

				$grouppermissions .= "Groups[\"$account\"] = \"";

				$grouppermissions .= implode(',', $users);

				$grouppermissions .= "\";\n";
    		}
    	}
	}
	if ($grouppermissions && !$permissions)
		$permissions = " ";

	return array($permissions, $grouppermissions);
}


function findgroups($group)
{
	global $pref;

	// Disable function if GlobalAbook disabled
	if(!$pref['GlobalAbook'])
	return;

	$limit = 25;

	$group = $this->db->quote($group);

	$count = $this->db->getvalue("select count(Account) from Users where Ugroup=$group");
	$user = $this->db->sqlarray("select Account from Users where Ugroup=$group limit $limit");

	$count = $count - $limit;
	if($count > 0)
	$user[] = "... $count users truncated ...";

	return $user;
}


// Check a language for the correct format
function checklanguage($value)
{
	global $language;

	if (!$language[$value] && $value)
	{
		$this->log->write_log('Error', "Security Breach - Language field $value invalid");
		exit;
	}

	return $value;
}

function update_sharedlookup($id, $type, $date)
{
	// Update all datemodified to reflect the new copy
	$this->db->sqldo("update SharedLookup set DateModified=? where LookupID=? and Type=?", array($date, $id, $type));
}


function loadspamsettings()
{
	$username = $this->db->quote("$this->username@$this->pop3host");
	$hash = $this->db->hashelement("select preference, value from SpamSettings where username = $username and (preference != 'whitelist_from' and preference != 'whitelist_to' and preference != 'blacklist_from')");

	// Load the elements into the object
	foreach ($hash as $k => $v)
		$this->$k = $v;

	// Take out the "Subject" prefix for the rewrite_header
	$this->rewrite_header = str_replace('subject ', '', $this->rewrite_header);
}


function savespamsettings()
{

	$username = $this->db->quote("$this->username@$this->pop3host");

	// First delete our previous settings
	$this->db->sqldo("delete from SpamSettings where username=$username and (preference != 'whitelist_from' and preference != 'whitelist_to' and preference != 'blacklist_from')" );

    // Prepare the SQL query
    $query = "INSERT INTO SpamSettings (username, preference, value, domain) values(?, ?, ?, ?)";

	// Insert each setting
	$spam_treatment = ($this->spam_treatment)? $this->spam_treatment : 'mark';
	$this->db->sqldo($query, array("$this->username@$this->pop3host", "spam_treatment", $spam_treatment, $this->pop3host) );

	$rewrite_header = ($this->rewrite_header)? $this->rewrite_header : '{SPAM}';
	$this->db->sqldo($query, array("$this->username@$this->pop3host", "rewrite_header", "subject " . $rewrite_header, $this->pop3host) );

	$abook_trusted = ($this->abook_trusted)? $this->abook_trusted : '0';
	$this->db->sqldo($query, array("$this->username@$this->pop3host", "abook_trusted", $abook_trusted, $this->pop3host) );

	$required_score = ($this->required_score)? $this->required_score : '5';
	$this->db->sqldo($query, array("$this->username@$this->pop3host", "required_score", $required_score, $this->pop3host) );

	$report_safe = ($this->report_safe)? $this->report_safe : '0';
	$this->db->sqldo($query, array("$this->username@$this->pop3host", "report_safe", $report_safe, $this->pop3host) );

	$bayes_auto_learn = ($this->bayes_auto_learn)? $this->bayes_auto_learn : '0';
	$this->db->sqldo($query, array("$this->username@$this->pop3host", "bayes_auto_learn", $bayes_auto_learn, $this->pop3host) );

    $whitelist_only = $this->whitelist_only ? $this->whitelist_only : '0';
	$this->db->sqldo($query, array("$this->username@$this->pop3host", "whitelist_only", $whitelist_only, $this->pop3host) );

	if ($this->bayes_auto_learn)
	{
		$this->db->sqldo($query, array("$this->username@$this->pop3host", "use_bayes", '1', $this->pop3host) );
		$this->db->sqldo($query, array("$this->username@$this->pop3host", "bayes_path", $this->bayes_path, $this->pop3host) );
	}
	else
		$this->db->sqldo($query,  array("$this->username@$this->pop3host", "use_bayes", '0', $this->pop3host) );

	return;
}

function add_entryidmap($EntryID, $id, $type, $datemodified=null)
{

	if ($datemodified && $EntryID )
	{
		// Check if the record already exists
		$v = $this->db->sqlgetfield("select LookupID from SharedLookup where Account=? and LookupID=? and Type=?", array($this->Account, $id, $type));

		if (!$v)
			$this->db->sqldo("INSERT INTO SharedLookup (Type, DateModified, LookupID, EntryID, Account) VALUES(?, ?, ?, ?, ?)", array($type, $datemodified, $id, $EntryID, $this->Account));
		else
			$self->sqldo( "Update SharedLookup set DateModified=?, EntryID=? where LookupID=? and Account=?", array($datemodified, $EntryID, $id, $this->Account));
	}
	elseif ($datemodified)
	{
		// Check if the record already exists
		$v = $this->db->sqlgetfield("select LookupID from SharedLookup where Account=? and LookupID=? and Type=?", array($this->Account, $id, $type));

		if (!$v)
			$this->db->sqldo("INSERT INTO SharedLookup (Type, DateModified, LookupID, Account) VALUES(?, ?, ?, ?)", array($type, $datemodified, $id, $this->Account));
		else
			$this->db->sqldo("Update SharedLookup set DateModified=? where LookupID=? and Account=?", array($datemodified, $id, $this->Account));
	}
	else
	{
		// Check if the record already exists
		$v = $this->db->sqlgetfield("select LookupID from SharedLookup where Account=? and LookupID=? and Type=? and LookupID is not null", array($this->Account, $id, $type));

		if (!$v)
			$this->db->sqldo("INSERT INTO SharedLookup (Type, EntryID, LookupID, Account) VALUES(?, ?, ?, ?)", array($type, $EntryID, $id, $this->Account));
		else
			$this->db->sqldo("Update SharedLookup set EntryID=? where LookupID=? and Account=?", array($EntryID, $id, $this->Account));
	}

	return 1;
}

function view_entryidmap($id, $type, $datemodified=null)
{
	if ($datemodified)
		return $this->db->sqlgetfield("select DateModified from SharedLookup where Account=? and LookupID=? and Type=? and DateModified is not null", array($this->Account, $id, $type));

    return $this->db->sqlgetfield("select EntryID from SharedLookup where Account=? and LookupID=? and Type=?", array($this->Account, $id, $type));
}


// Build a select box with each hour
function buildhour()
{
	$list = "<select name=\"hour\" class=\"select\">\n";
    for ($hour=0; $hour<=23; $hour++)
	{
        if ($hour < 10)
			$hour = "0$hour";
        $list .= "<option value=\"$hour\">$hour</option>\n";
    }

    $list .= "</select>\n";

    return $list;
}

// Build a select box with each minute
function buildmin()
{
	$list = "<select name=\"minute\" class=\"select\">\n";
    for ($min=0; $min<=59; $min++)
	{
        if ($min < 10)
			$min = "0$min";
        $list .= "<option value=\"$min\">$min</option>\n";
    }

    $list .= "</select>\n";

    return $list;
}

// Build a select box for the DateAlert in the Calendar
function buildalert($select = '')
{

$list = <<<_EOF
	<select name="DateAlert" class="select">
	<option value="15 MINUTE">15 Minutes</option>
	<option value="30 MINUTE">30 Minutes</option>
	<option value="1 HOUR">1 Hour</option>
	<option value="2 HOUR">2 Hour</option>
	<option value="4 HOUR">4 Hour</option>
	<option value="1 DAY">1 Day</option>
	<option value="2 DAY">2 Day</option>
	<option value="7 DAY">1 Week</option>
	<option value="14 DAY">2 Week</option>
	</select>
_EOF;

if($select == '00:15:00')
    $list = str_replace('value="15 MINUTE"', 'value="15 MINUTE" selected', $list);
else if($select == '00:30:00')
    $list = str_replace('value="30 MINUTE"', 'value="30 MINUTE" selected', $list);
else if($select == '01:00:00')
    $list = str_replace('value="1 HOUR"', 'value="1 HOUR" selected', $list);
else if($select == '02:00:00')
    $list = str_replace('value="2 HOUR"', 'value="2 HOUR" selected', $list);
else if($select == '04:00:00')
    $list = str_replace('value="4 HOUR"', 'value="4 HOUR" selected', $list);
else if($select == '24:00:00')
    $list = str_replace('value="1 DAY"', 'value="1 DAY" selected', $list);
else if($select == '48:00:00')
    $list = str_replace('value="2 DAY"', 'value="2 DAY" selected', $list);
else if($select == '168:00:00')
    $list = str_replace('value="7 DAY"', 'value="7 DAY" selected', $list);
else if($select == '336:00:00')
    $list = str_replace('value="14 DAY"', 'value="14 DAY" selected', $list);

return $list;

}

// Escape any images from the input
function escape_images($txt)
{
	$tmp = strtolower($txt);

	// Watch out for javascript in img src URL,  IE doesn't handle these very well. Addresses SA18874.
	$txt = preg_replace('/<img [^>]+?javascript:.+?>/is', '<img src="" alt="Image Block Forced">', $txt);

	// Skip if we trust all images
	if ($this->DisplayImages == 1 || (is_array($_SESSION['DisplayImages']) && in_array($_REQUEST['id'], $_SESSION['DisplayImages'])))
		return $txt;

	// Block the image tags - Rewrite
	if (strpos($tmp, '<img') !== false)
	{
		$this->BlockImages++;
		$txt = preg_replace('/<img .+?>/is', '<img src="" alt="Blocked image">', $txt);
	}
	return $txt;
}

/**
 * Load if the user has permission to view images in emails
 *
 * @return int
 */
function load_displayimages()
{

	if(!$this->db->UserSettings)
	return;

	return $this->db->sqlgetfield("SELECT DisplayImages
	                               FROM {$this->db->UserSettings}
	                               WHERE Account = ?", "$this->username@$this->pop3host");
}

// Return if the sender of the message matches a trusted user in the abook
function load_abook_emails($from)
{
	if ($this->db->sqlgetfield("select UserEmail from {$this->db->Abook} where Account=? and UserEmail=?", array("$this->username@$this->pop3host", $from)))
		return 1;

	return 0;
}

// Update DisplayImages = 2 - Allowing images to be displayed in emails from abook users
function update_settings_displayimages()
{
	$this->db->sqldo("update {$this->db->UserSettings} set DisplayImages='2' where Account=?", "$this->username@$this->pop3host");

	return;
}

// Get parms, try normal, ucfirst, then uc all
function loadParameter($param)
{
	if ($this->isset_chk($_REQUEST[$param]))
		return $this->escape_html($_REQUEST[$param]);

	$param = ucfirst($param);

	if ($this->isset_chk($_REQUEST[$param]))
		return $this->escape_html($_REQUEST[$param]);

	$param = strtoupper($param);

	if ($this->isset_chk($_REQUEST[$param]))
		return $this->escape_html($_REQUEST[$param]);

	return '';
}

function ldap_error($error)
{
	fwrite(STDERR, "LDAP ERROR = $error\n");
}

function translate_special($r)
{
	$search = array('_CR_', '_APOS_', '_DOLLAR_', '_AMPER_', '_PLUS_', '_COMMA_', '_COLON_', '_SEMI_', '_EQUALS_', '_QUESTION_', '_AT_', '_POUND_');
	$replace = array("\n", "'", '$', '&', '+', ',', ':', ';', '=', '?', '@', chr(163));

	$r = str_replace($search, $replace, $r);
	return $r;
}

// useful routine that returns the type of pki enabled (pgp or smime)
// if one of them is enabled and functional.
function pki_enabled()
{
	global $pref;

	// Temp changes until SMIME complete
	if ($this->PGPenable && file_exists($pref['gpg_path']))
		return 'pgp';

	if (!$this->PGPenable && file_exists($pref['gpg_path']))
		return '0';

	return;

	if ($this->isset_chk($this->cached_pki_enabled))
		return $this->cached_pki_enabled;
	$this->cached_pki_enabled = ($self->PKIenable)?($this->PGPenable)?((file_exists($pref['gpg_path']))?'pgp':0):((file_exists($pref['openssl_path'])?'smime':0)):0;
	return $this->cached_pki_enabled;
}


function rmtree($dir)
{
	$dh = opendir($dir);
	while (false !== ($filename = readdir($dh)))
	{
		if ($filename != '.' && $filename != '..')
		{
			if (is_link($dir."/".$filename))
			{
				unlink($dir."/".$filename);
			}
			elseif (is_dir($dir."/".$filename))
			{
				// recurse subdirectory; call of function recursive
				$this->rmtree($dir."/".$filename);
			}
			elseif (is_file($dir."/".$filename))
			{
				// unlink file
				unlink($dir."/".$filename);
			}

		}
	}
	closedir($dh);
	if (rmdir($dir))
		return true;

	return false;
}



    function do_branding()
    {
        global $pref, $brand;

        $host = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);

        if (isset($brand[$host]))
        {
            foreach ($brand[$host] as $k => $v)
                $pref[$k] = $v;
        }
    }

    function isCalEventMember($id)
    {
    	return $this->db->sqlgetfield('select 1 from CalendarPermissions where CalID=? and Account=?', array($id, "$this->username@$this->pop3host"));
    }
} //end AtmailGlobal

?>
