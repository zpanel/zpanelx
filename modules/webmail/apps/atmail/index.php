<?php

// +----------------------------------------------------------------+
// | index.php														|
// | Function: Open the signup screen and newuser page				|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

// Check for system installation
if (!file_exists('libs/Atmail/Config.php'))
    redirectToInstaller();

require_once('Global.php');

// Check again for system installation, just in case Config.php
// was manually created
if (!$pref['installed'])
    redirectToInstaller();

/** For future use
if (isset($_REQUEST['mode']) && !empty($_REQUEST['mode'])) {
    $file = $_REQUEST['mode'] . '.php';
    if (file_exists($file)) {
        include($file);
        $atmail->end();
    }
}
*/

$var = array();

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
    $var['browser'] = "ie";
else
    $var['browser'] = "ns";

$atmail = new AtmailGlobal();

$var['func'] = $_REQUEST['func'];
$var['version'] = $pref['version'];


if (!$atmail->Language)
	$atmail->Language = $atmail->param('Language');


// If the user if logging off, print a cookie header with
// a blank SessionID. Delete the Session for the DB too
if ( $var['func'] == "logout" )
{
	require_once('Session.php');

	session_start();

	$auth =& $atmail->getAuthObj();

	// Find the users current settings, if to delete the trash on logout
	//$atmail->cookie_read($auth);
	//$auth->getuser();
	$atmail->username = $auth->username;
	$atmail->pop3host = $auth->pop3host;
	$atmail->SessionID = $auth->SessionID;

    //$atmail->cookie_header_delete();

    if (!$pref['opensource']) {
		$var['ErrorHead'] = $atmail->parse("html/$atmail->Language/msg/logoff.html");
    	$var['ErrorHead'] .= "<script language='Javascript'>window.focus();</script>";
    }

	$atmail->clean_tmp();

	// clear tmp directory
	if ($handle = opendir($pref['install_dir'].'/tmp/')) {
	    while (false !== ($file_name = readdir($handle))) {
	        if ($file_name != "." && $file_name != ".." && $file_name != '.htaccess' && is_file($file_name)) {
	            if (strtotime("+ 180 seconds") > fileatime($file_name)) {
	                unlink($file_name);
	            }
	        }

	    }
	    closedir($handle);
	}

	// If we have expunge on logout ( e.g PDMF IMAP server)
	if($pref['expunge_logout'] == '1')	{

		$atmail->status = $auth->getuser( $atmail->SessionID );
		$atmail->loadprefs(1);

		require_once('GetMail.php');

		$mail = new GetMail(array(
		  'Username' => $atmail->username,
		  'Pop3host' => $atmail->pop3host,
		  'Password' => $auth->password,
		  'Mode'     => $atmail->Mode,
		  'Type'     => $atmail->MailType)
		);

		if($atmail->MailType == 'imap')	{
		$mail->login();
		$folders = $mail->listfolders();

		// Create a new folder-tree element
		$mail->newfolder_tree();

		// Loop through each of the folders
		foreach ($folders as $folder)
		{
		$mail->expunge($folder);
		}

	}

	}

	session_destroy();
}

$var['Ajax'] = '1';
$var['error'] = $_REQUEST['error'];
$atmail->LoginType = "simple";
$atmail->Ajax = '1';
$atmail->Language = 'english';
$atmail->FontStyle = 'Verdana';
$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/simple/atmailstyle.css" );
$var['mailstyle'] = $atmail->parse("html/$atmail->Language/simple/atmailstyle-mail.css");
$var['func'] = 'login';
$atmail->FromField = 'me';
print $atmail->parse("html/$atmail->Language/simple/showmail_interface.html", $var);
$atmail->end();


function redirectToInstaller()
{
    if (!file_exists('install/index.php')) {
        die('your @Mail system has not yet been configured');
    }

    header('Location: install/');
}

