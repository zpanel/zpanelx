<?php
// +----------------------------------------------------------------+
// | sendmail.php													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Session.php');
require_once('Global.php');
require_once('SendMsg.php');
require_once('Log.php');
require_once('ReadMsg.php');


// We dont want to have a short session timeout for sendmail.php as
// the user may take a while to compose their message. Therefore
// we need to alter the $pref['session_timeout'] value. This must
// be done BEFORE session_start() is called so that session GC
// does not clear the session data. Lets give them 24 hours to
// compose their message.
if ($pref['session_timeout'] < 86400)
	$pref['session_timeout'] = 86400;

session_start();

// For IMAP append feature
require_once('GetMail.php');

$var = array();

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();

// Print the XML header for the Ajax interface
if($atmail->Ajax)
header('Content-Type: xml');
else
$atmail->httpheaders();

$atmail->status = $auth->getuser($atmail->SessionID);

$atmail->username = $auth->username;
$atmail->pop3host = $auth->pop3host;

// Print the error screen if the account has auth errors
if ( $atmail->status == 1 )
	$atmail->auth_error();
elseif ( $atmail->status == 2 )
	$atmail->session_error();

// Load the account preferences
$atmail->loadprefs();

// Parse the users custom stylesheet
$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css");

// Create a new log object
if (!$_REQUEST['Draft']) {
    $log = new Log(array('Account' => "$atmail->username@$atmail->pop3host"));
    
    $num = $log->logcheck('SendMail', $_SERVER['REMOTE_ADDR'], "{$atmail->username}@{$atmail->pop3host}");
    
    if ( $num > $pref['filter_max_msgs'] && $pref['filter_max_msgs'] > 1 )
    {
        print $atmail->parse("html/$atmail->Language/auth_spammer.html");
        $log->write_log( 'Error', "Spam Detected from {$_SERVER['REMOTE_ADDR']} : $num msgs sent" );
        $atmail->end();
    }
}

// Calculate the height of the menubar ( if the Webadmin user toggles off certain features )
$h = $atmail->calcmenu_height();
foreach($h as $k => $v) {
	$var[$k] = $v;
}

// Load our email message vars
$var['emailto']       = $_REQUEST['emailto'];
$var['emailcc']       = $_REQUEST['emailcc'];
$var['emailbcc']      = $_REQUEST['emailbcc'];
$var['emailsubject']  = $_REQUEST['emailsubject'];
$var['emailpriority'] = $_REQUEST['emailpriority'];
$var['contype']       = $_REQUEST['contype'];
$var['unique']        = $_REQUEST['unique'];
$var['UIDL']          = $_REQUEST['UIDL'];
$var['type']          = $_REQUEST['type'];
$var['emailfrom'] 	  = $_REQUEST['emailfrom'];
$var['Charset'] 	  = $_REQUEST['Charset'];
$var['DraftID'] 	  = $_REQUEST['DraftID'];
$var['ReadReceipt']   = $_REQUEST['ReadReceipt'];
$var['Draft'] 		  = $_REQUEST['Draft'];
$var['VideoStream']   = $_REQUEST['VideoStream'];
$var['id']   		  = $_REQUEST['id'];

// format the addresses
foreach (array('emailto', 'emailcc', 'emailbcc') as $k) {
    $var[$k] = ReadMsg::cleanEmailAddress($var[$k]);
}

// Switch to our default character-set if not defined
if(!$var['Charset'])
$var['Charset'] = $atmail->EmailEncoding;

// Make a new UIDL if one does not exist
if ( !$var['UIDL'] )
{
    $var['UIDL'] = time() . getmypid() . rand(0,9000) . $atmail->genkey();
    $var['UIDL'] = preg_replace('/\..*/', '', $var['UIDL']);
}

// Find which EmailBox to save into
if ( $_REQUEST['Draft'] )
	$var['msgbox'] = 'Drafts';
else
	$var['msgbox'] = 'Sent';


// Build the message to send . Add the headers, message body and UIDL
$sendmsg = new SendMsg(array(
  'Account'       => "$atmail->username@$atmail->pop3host",
  'EmailTo'       => $var['emailto'],
  'EmailFrom'     => $var['emailfrom'],
  'EmailCC'       => $var['emailcc'],
  'EmailBCC'      => $var['emailbcc'],
  'EmailSubject'  => $var['emailsubject'],
  'EmailPriority' => $var['emailpriority'],
  'ContentType'   => $var['contype'],
  'XMailer'       => "AtMail {$pref['version']}",
  'EmailBox'      => $var['msgbox'],
  'EmailUIDL'     => $var['UIDL'],
  'Unique'        => $var['unique'],
  'X-Origin'	  => $_SERVER['REMOTE_ADDR'],
  'EmailMessage'  => $_REQUEST['emailmessage'],
  'SessionID'	  => $auth->SessionID,
  'PGPappend'	  => $atmail->PGPappend,
  'PGPsign'		  => $atmail->PGPsign,
  'SMIMEencrypt'  => $var['SMIMEencrypt'],
  'SMIMEsign'	  => $var['SMIMEsign'],
  'Charset'		  => $var['Charset'],
  'ReadReceipt'	  => $var['ReadReceipt'],
  'ReplyFwd'      => $var['type'],
  'VideoStream'   => $var['VideoStream'] // Hello world via video!
)
);

// Save the message into the Sent users folder
// Create a new mail object
$mail = new GetMail(array(
	  'Username' => $atmail->username,
	  'Pop3host' => $atmail->pop3host,
	  'Password' => $auth->password,
	  'Type'     => $atmail->MailType,
	  'Mode'     => $atmail->Mode)
	);

$mail->login();

// Build the email message to send
$sendmsg->buildmsg();

// Exit if no message defined
if (!$sendmsg->mime)
	$atmail->end();

// If the user chooses to save the message
if ( $sendmsg->EmailBox == 'Drafts' )
{
    // Print the draft save message
    $var['msg'] = $atmail->parse("html/$atmail->Language/msg/savedraft.html");
}
else
{
	// Send the email-message via SMTP
	$sendmsg->deliver();

	// Display a different notification in the email display to the browser ( depending if Sent or Drafts folder )
	if (!$sendmsg->attachname[0])
	    $var['msg'] = $atmail->parse("html/$atmail->Language/msg/sentmsg.html");
	else
	    $var['msg'] = $atmail->parse("html/$atmail->Language/msg/sentmsga.html");
}

$args = array();
$atmail->pluginHandler->triggerEvent('onEmailSent', $args);

// Print the message sent
$var['emailmessage'] = $_REQUEST['emailmessage'];
// cleanup xss issues
$var['emailmessage'] = $atmail->escape_jscript($var['emailmessage']);

if ( strpos($var['contype'], 'html') === false )
	$var['emailmessage'] = nl2br($var['emailmessage']);


// Delete the attach files (if any)
$sendmsg->delete_attachments();

// When printing the message, change the <> chars , so not to confuse the browser
// Also strip the @ for Groups
foreach ( array('EmailTo', 'EmailCC', 'EmailBCC') as $v) {
	$key = strtolower($v);
    $var[$key] = str_replace(array('<', '>'), array('&lt;', '&gt;'), $sendmsg->$v);
    $var[$key] = preg_replace('/@((Shared)?Group)(?=[^a-zA-Z0-9\-.]*)/', '$1', $var[$key]);
    $var[$key] = $mail->quote_header($var[$key]);
}

// Receive a list of recipients from the user
$var['AddRecipients'] = $sendmsg->AddRecipients;

// Quote the email-message, subject & to fields ( e.g if in another language like Japanese )
#$var['emailmessage'] = Atmail::GetMail->decode_language("", $var['emailmessage'] );
$var['emailsubject'] = $mail->quote_header($sendmsg->EmailSubject);

// If the user is replying to a draft, delete the original copy from the server
if ($var['DraftID'])
{
	// Remove the message from the drafts folder
	$mail->move( $var['DraftID'], "Drafts", "erase", 1);
}

// Fix for IMAP update UIDL
if (!empty($var['id'])) {  
    $update_id = ($pref['install_type'] != "server" && $mail->Type == 'imap')  ? $var['id'] : "cur/{$var['UIDL']}";
    // Update the email UIDL/unique-id if the message is a reply or forward
    $mail->updateuidl( $var['UIDL'], $var['type'], '', "Inbox",  $update_id);
}

// Log the sent message only if not in server mode
if (!$_REQUEST['Draft']) {
	$log->write_log( "SendMail", "{$_SERVER['REMOTE_ADDR']}:{$var['emailto']} {$var['emailcc']} {$var['emailbcc']}" );
}


	// Append the message to the server
	$sentuid = $mail->append($sendmsg->EmailBox, $sendmsg->headers . "\r\n\r\n" . $sendmsg->body);
	//$mail->mailer->markAsFlag($sentuid, '+FLAGS', '\\Answered');

// Display the sent-message template to the user
print $atmail->parse("html/$atmail->Language/$atmail->LoginType/sendmsg_ajax.html", array_merge(array('Status' => '0', 'StatusMessage' => 'Sent'), $var));

$atmail->end();
