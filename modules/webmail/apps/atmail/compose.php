<?php

// +----------------------------------------------------------------+
// | compose.php													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');
require_once('Session.php');
require_once('Global.php');
require_once('Log.php');
require_once('SendMsg.php');
require_once('Mail/RFC822.php');

session_start();

// get global settings from config file
global $settings, $pref;
$var = array();

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();
$atmail->httpheaders();

$atmail->status = $auth->getuser( $atmail->SessionID );

$atmail->username = $auth->username;
$atmail->pop3host = $auth->pop3host;

// check for language version
if (!$atmail->Language)
	$atmail->Language = $pref['Language'];

// Print the error screen if the account has auth errors, or session timeout.
if ( $atmail->status == 1 )
	$atmail->auth_error();
if ( $atmail->status == 2 )
	$atmail->session_error();

// Which function
$var['func'] = $_REQUEST['func'];

// Check for an attachment upload that has gone over post_max_size
// Set func to 'attachment' as $_POST will be empty.
if (isset($_GET['sending_attachment']) && !count($_POST) && !count($_FILES)) {
	$var['func'] = 'attachment';
}

// Load the account preferences
$atmail->loadprefs();

// Parse the users custom stylesheet
$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css");

// Load the time to display in the compose window
$var['localtime'] = strftime("%c");

// Create a unique number - Each compose screen is unique. Used to
// reference which attachments are for what window. Based on the
// PID and a random number.
$var['unique'] = $atmail->param_escape('unique');
$var['delete'] = $_REQUEST['delete'];

if ( !$var['unique'] ) {
    $var['unique'] = getmypid() + rand(0, 1000);
}

// Avoid any fake/malformed unique ID, e.g ../ in pathname
$var['unique'] = basename($var['unique']);

// see if something is cached
if (file_exists($atmail->tmpdir .  ".ht$auth->SessionID"))
	$var['PgpPass'] = 1;

// The From address of our
$var['FromAddress'] = $atmail->loadpersonalities();

// Calculate the height of the menubar ( if the Webadmin user toggles off certain features )
$h = $atmail->calcmenu_height();
foreach($h as $k => $v)
	$var[$k] = $v;

// Display the attachment modal window
if ( $var['func'] == "attachmentmodal" ) {
    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/attachmentmodal.html", $var);
} elseif ( $var['func'] == "attachmentmodalframe" ) {
	$var['maxfilesize'] = ini_get('upload_max_filesize');
	if (!is_numeric($var['maxfilesize'])) {
	    if (strpos($var['maxfilesize'], 'M') !== false)
	        $var['maxfilesize'] = intval($var['maxfilesize'])*1024*1024;
	    elseif (strpos($var['maxfilesize'], 'K') !== false)
	        $var['maxfilesize'] = intval($var['maxfilesize'])*1024;
	    elseif (strpos($var['maxfilesize'], 'G') !== false)
	        $var['maxfilesize'] = intval($var['maxfilesize'])*1024*1024*1024;
	}
    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/attachment.html", $var );
}
// Rename an attachment, when forwarding via Ajax
elseif( $var['func'] == "renameattach")	{

    $sendmsg = new SendMsg(array('Account' => "$atmail->username@$atmail->pop3host"));
	$AttachmentList = $_REQUEST['Attachment'];

	// Loop through each attachment to rename on disk
		if(is_array($AttachmentList))
		foreach($AttachmentList as $attach)	{
			$attach = $atmail->myunescape($attach);
			$sendmsg->renameattach($var['unique'], $attach);
		}

		echo "<ATTACH STATUS='OK'/>";

	$atmail->end();

} elseif ($var['func'] == "attachmentmodallist" ) {
    $icon = $atmail->icon_hash();
    $sendmsg = new SendMsg(array('Account' => "$atmail->username@$atmail->pop3host"));

    // Delete the selected attachment
    if ( $var['delete'] )
   		$var['status'] = $sendmsg->delete_attachment($var['delete'], $var['unique']);

    // List the number of attachments . Based on the unique compose
    // window and our logged in account
    $h = $sendmsg->list_attachments($var['unique']);

    foreach ($h as $k => $v) {
        $var['filename'] = $k;
        if ( preg_match('/\.(\w+)$/', $var['filename'], $match) )
        	$var['ext'] = $match[1];

        $var['size'] = $v['size'];
        $var['mime'] = $v['mime'];

        // Load the icon for the filename
        $var['icon'] = $icon[strtolower($var['ext'])];

        // Use the standard if none exists
        if ( !$var['icon'] )
        	$var['icon'] = "plain.gif";

		$var['AttachNames'] .= "{$var['filename']}; ";
        // Build the HTML table that lists the attachments
        $var['attachments'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/attachfilemodal.html", $var );

		// Increment the counter ( for the Javascript id field )
		$var['id']++;
    }

	$var['AttachNames'] = preg_replace('/; $/', '', $var['AttachNames']);
    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/attachmentmodallist.html", $var );
}

// Print the attachment window
elseif ( $var['func'] == "attachment" ) {
    $icon = $atmail->icon_hash();

    $var['delete'] = $_REQUEST['delete'];
    $var['unique'] = $atmail->param_escape('unique');
	$var['unique'] = basename($var['unique']);
	
    $sendmsg = new SendMsg(array('Account' => "$atmail->username@$atmail->pop3host"));

    // Delete the selected attachment
    if (isset($var['delete'])) {
    	$var['status'] = $sendmsg->delete_attachment( $var['delete'], $var['unique'] );
	}
    // Upload an attachment to the server if required
	elseif (isset($_FILES['fileupload'])) {
		$var['status'] = $sendmsg->add_attachment($var['unique']);
		if ($var['status'] === false) {
			print $atmail->parse("html/$atmail->Language/msg/attachtoolarge.html");
		}
	} elseif (isset($_GET['sending_attachment'])) {
		// Probably exceeded post_max_size if we get here
		print $atmail->parse("html/$atmail->Language/msg/attachtoolarge.html");
	}

    // List the number of attachments . Based on the unique compose
    // window and our logged in account
    $h = $sendmsg->list_attachments( $var['unique'] );

    foreach ($h as $k => $v) {
        $var['filename'] = $k;
        if ( preg_match('/\.(\w+)$/', $var['filename'], $match) )
        	$var['ext'] = $match[1];

        $var['size'] = $v['size'];
        $var['mime'] = $v['mime'];

        // Load the icon for the filename
        $var['icon'] = $icon[ strtolower( $var['ext'] ) ];

        // Use the standard if none exists
        if ( !$var['icon'] )
        	$var['icon'] = "plain.gif";

		$var['AttachNames'] .= "{$var['filename']} ; ";
        // Build the HTML table that lists the attachments
        $var['attachments'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/attachfile.html", $var);

    }

    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/attachment.html", $var);
}
