<?php
// +----------------------------------------------------------------+
// | atmail.php														|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');
require_once('Global.php');

// Check for account sent as one string
if (isset($_REQUEST['account'])) {
	list($_REQUEST['username'], $_REQUEST['pop3host']) = explode('@', $_REQUEST['account']);
}

// sanitize some vars
if (isset($_REQUEST['username'])) {
	$_REQUEST['username'] = htmlspecialchars($_REQUEST['username']);
}

if (isset($_REQUEST['pop3host'])) {
	$_REQUEST['pop3host'] = htmlspecialchars($_REQUEST['pop3host']);
}

$atmail = new AtmailGlobal();

$atmail->getAuthObj(false);
$atmail->auth();

// Only start session if user is authentcated
require_once('Session.php');

session_start();

// force refresh of imap folder cache
// so we see all folders once logged in
$_SESSION['ForceImapRefresh'] = 1;

$atmail->auth->update_session();
$_SESSION['auth'] =& $atmail->auth;

$lang = $atmail->logintype();
$atmail->loadprefs();

// Toggle which MailType to use. IMAP or POP3
$atmail->mailtype();

$atmail->Language = ($lang)? $lang : $_REQUEST['Language'];
if (!$atmail->Language)
	$atmail->Language = $pref['Language'];

echo $atmail->FromField;

$log = new Log(array('Account' => "$atmail->username@$atmail->pop3host"));

// Log the access
$log->write_log( "Login", "Access from {$_SERVER['REMOTE_ADDR']}");

$atmail->end();
