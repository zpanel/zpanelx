<?php
// +----------------------------------------------------------------+
// | reademail.php													|
// | Function: Read an email message								|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+


require_once('header.php');

require_once('Global.php');
require_once('ReadMsg.php');
require_once('SendMsg.php');
require_once('Session.php');

session_start();

if (isset($_REQUEST['DisplayImages']) && $_REQUEST['DisplayImages'] == 1) {
    $_SESSION['DisplayImages'][] = $_REQUEST['id'];
}

$var = array();

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();

// If we are exporting an email, pring the correct header, otherwise proceed as normal
if (isset($_REQUEST['rawemail']))
{
	require_once('GetMail.php');
	$getmail = new GetMail();
	$time = time($getmail->calc_timezone(time()));

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: message/rfc822; name=\"rawemail-$time.eml");
	header("Content-Disposition: attachment; filename=\"rawemail-$time.eml");
	header('Content-Transfer-Encoding: binary');
	header("Pragma: ");
}
else
{
	$atmail->httpheaders();
}

$atmail->status = $auth->getuser($atmail->SessionID);

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

// Load the account preferences
$atmail->loadprefs();

// Parse the users custom stylesheet
$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css");
$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-form.css");

// Which email to read
$var['id']     = $_REQUEST['id'];
$var['folder'] = $_REQUEST['folder'];
$var['print']  = $_REQUEST['print'];
$var['cache']  = $_REQUEST['cache'];

$var['head']	 = $_REQUEST['head'];
$var['rawemail'] = $_REQUEST['rawemail'];

if (file_exists($atmail->tmpdir . ".ht.".$auth->SessionID) || $_REQUEST['pgppass'])
	$var['PgpPass'] = 1 ;

// LeaveMsgs = 0 is not spported as yet
$atmail->LeaveMsgs = 1;

// Specify to open mail messages in new window, or parent location
$type = ( $atmail->LoginType == "simple") ? 1 : 0;

$head = ($var['head']) ? $var['head'] : $atmail->Ajax;

// Build a new ReadMsg object
$email = new ReadMsg(array(
  'Username' => $atmail->username,
  'Pop3host' => $atmail->pop3host,
  'Password' => $auth->password,
  'Type'     => $atmail->MailType,
  'Mode'     => $atmail->Mode,
  'SessionID' => $auth->SessionID,
  'Language' => $atmail->Language,
  'DateFormat' => $atmail->DateFormat,
  'TimeFormat' => $atmail->TimeFormat,
  'LoginType' => $type,
  'head' => $head,
  'rawemail' => $var['rawemail'])
);

// Fetch the message
$email->reademail( $var['id'], $var['folder'], '', $var['cache'] );

// An error occured while logging into the server
if ( $email->status )
    print $atmail->parse( "html/$atmail->Language/auth_misc.html", array('status' => "Remote mail-server not responding - Check connection - $email->status"));

// Escape any < > chars
foreach ( array('to', 'cc', 'from', 'subject', 'priority', 'date', 'type') as $name)
{
    if (!$atmail->Ajax) {
    	$email->$name = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $email->$name);
    }

    // Escape any newlines, they will mess up the javascript
    $email->$name = trim($email->$name);
}

if ($atmail->LoginType == 'xul') {
    $email->subject = str_replace("'", '', $email->subject);
}

$email->ToSelect = $email->to;
$email->CcSelect = $email->cc;

parse_attachments($email);

// Only display the text message if multipart/alternative
// encoding. Such as Microsoft Outlook sends the messages
if ($email->mimetype == 'multipart/alternative' && $email->html || $email->mimetype == 'multipart/mixed' && $email->html )
	$email->txt = '';

// If reading a message from the drafts, use the correct template
//if ( $var['folder'] == "Drafts" && $atmail->LoginType != "simple")
//	$var['misc'] = "_drafts" ;


$var['filename'] = "html/$atmail->Language/$atmail->LoginType/readmail_ajax.html";

foreach(array('cc', 'to') as $type)
{
	$name = "emaillist";
	if ($type == "cc")
		$name = $name."cc" ;

	$var[$name] = $email->$type;

	if (!$atmail->Ajax)
	{
		if ( strlen($var[$name]) > 103 )
		{
			$var[$name] = substr($var[$name], 0, 100 );
			$var[$name] .= " ...";
		}
	}
}

// display mail headers
if ( $_REQUEST['head'] )
{
    print "<pre>$email->headers</pre>";
	$atmail->end();
}
elseif ( $_REQUEST['rawemail'] )
{
	print $email->rawbody;
	$atmail->end();
}
elseif( $email->Virus )
{
	print $atmail->parse("html/$atmail->Language/virusmessage.html",
	  array(
		  'VirusName'		=> $email->VirusName,
	      'EmailFrom'		=> $email->emailfrom,
		  'atmailstyle'   => $var['atmailstyle'])
	);
}
else
{
    // parse any email forwarded as attachment (message/rfc822)
    // and append to the original message
    foreach ($email->attachedemails as $path) {
        extract_eml($path);
    }

	// Translate the email-priority
	if ($email->priority == "Normal")
		$email->priority = $atmail->parse("html/$atmail->Language/msg/normalpriority.html");

	elseif($email->priority == "Low")
		$email->priority = $atmail->parse("html/$atmail->Language/msg/lowpriority.html");

	elseif($email->priority == "High")
		$email->priority = $atmail->parse("html/$atmail->Language/msg/highpriority.html");

	// Take out ' characters, they break the Javascript for the Ajax abook
	$fromaddress = str_replace("'", '', $email->from);

	if ($atmail->Ajax)
	{
		$email->subject = str_replace(array('&gt;', '&lt;'), array('>', '<'), $email->subject);
		$email->from = str_replace(array('&gt;', '&lt;'), array('>', '<'), $email->from);

		$var['emaillist'] = str_replace(array('&gt;', '&lt;'), array('>', '<'), $var['emaillist']);
		$var['emaillistcc'] = str_replace(array('&gt;', '&lt;'), array('>', '<'), $var['emaillistcc']);
	}

	// Load the replyto field
	$var['EmailReplyTo'] = ($email->replyto)? $email->replyto : $email->from;

	// If using IMAP, get the message flag from the ReadMsg.php module ( need to get it before the message for the correct flag )
	if($atmail->MailType == 'imap')
	$MessageState = $email->MessageState;
	else
	$MessageState = $email->mail->getuidl( $email->UIDL, $var['id'] );

	if($email->txt)
	$HTMLtoTextReply = $email->clean_html_to_text($email->txt);
	else
	$HTMLtoTextReply = $email->clean_html_to_text($email->html);

    print $atmail->parse(
      $var['filename'],
      array(
	      'id'            => $var['id'],
	      'folder'        => $var['folder'],
	      'folderstate'   => str_replace(' ', '_', $var['folder']), // Required for IE Adv int, unread count on JS tree
	      'EmailSubject'  => cleanText($email->subject),
	      'EmailSubjectRaw'  => $email->decode_htmlspecialchars($email->subject),
	      'EmailTo'       => cleanText($email->ToSelect),
	      'EmailToList'   => cleanText($var['emaillist']),
	      'EmailCc'       => cleanText($email->CcSelect),
	      'EmailCcList'   => cleanText($var['emaillistcc']),
	      'EmailFrom'     => cleanText($email->from),
		  'EmailFromAddress' => cleanText($fromaddress),
	      'EmailAddress'  => cleanText($email->emailfrom),
	      'EmailHtml'     => ReadMsg::strip_style(cleanText($email->html)),
	      'EmailTxt'      => cleanText($email->txt),
	      'EmailDate'     => $email->date,
	      'EmailType'     => $email->type,
		  'Virus'		  => $email->Virus,
	      'EmailPriority' => $email->priority,
	      'atmailstyle'   => $var['atmailstyle'],
	      'Attachments'   => $var['attachments'],
	      'print'         => $var['print'],
		  'MailEncrypt'	  => $email->MailEncrypt,
		  'SmimeEncrypt'  => $email->SmimeEncrypt,
		  'Encrypt'		  => $email->Encrypt,
	      'PgpPass'		  => $var['PgpPass'],
		  'EmailUIDL'	  => $email->UIDL,
		  'EmailCache'	  => $email->EmailCache,
		  'Charset'	      => $email->Charset,
		  'BlockImages'	  => $atmail->BlockImages,
		  'RawAttachments' => $var['rawattachments'],
		  'VideoMail'      => $email->VideoMail,
		  'headers'       => nl2br(cleanText($email->headers)),
//'headers'       => nl2br($email->headers),
		  'MessageState'  => $MessageState,
		  'EmailReplyTo'  => cleanText($var['EmailReplyTo']),
		  'ImageAttachments' => $var['image_attachments'],
		  'HTMLtoTextReply' => cleanText($HTMLtoTextReply)));

	// If mail account POP3/SQL or maildir, update the flag as read ( IMAP will do auto )
	if($atmail->MailType != 'imap')
	$email->mail->updateuidl( $email->UIDL, 'o', '', $var['folder'], $var['id'] );
}

$email->mail->quit();
$atmail->end();

function parse_attachments(&$obj)
{
	global $var, $atmail;

	// Add links to download messages forwarded as
	// attachments (they are also displayed inline)
	foreach ($obj->attachedemails as $path) {
		if (!file_exists($path)) {
			continue;
		}

		$name = basename($path);

		// don't allow .htaccess execution
		if ($name == '.htaccess') {
			chmod($path, 0444);
		}

		$size = filesize($path);

		// Make a string of raw attachments for JS functions
		$var['rawattachments'] .= "$name::";

	    $var['attachments'] .= $atmail->parse(
			"html/$atmail->Language/readmail-attachment.html",
			  array(
				  'Path'     => $path,
				  'FileName' => rawurlencode($name),
				  'Size'     => $size,
				  'Desc'     => '',
				  'Download' => $obj->myescape($name),
				  'Name'     => $name,
				  'Icon'     => 'txt.gif',
				  'Target'   => '_blank')
				);

	}

    foreach ($obj->attachname as $k => $v)
	{
	    if ( !$v['path'] || $v['inline'])
	    	continue;

	    // Unescape the filename if required
	    $name = $obj->myescape($v['name']);

		$name = preg_replace('/\.safe$', $name);
		
		// Take away the number extension, if the attachment name already exists on the server
	    $name = preg_replace('/-\d+\.(\w+)/', '\.$1', $name);
	    
	    if ( $v['type'] == "file")
	    {
	        if ($v['fwdmsg']) {
	            $var['icon'] = "txt.gif";
	        } else {
	            $icon = $atmail->icon_hash();

	            if ( preg_match('/\.(\w+)$/', $v['name'], $match))
	            	$var['ext'] = $match[1];

	            // Load the icon for the filename
	            $var['icon'] = $icon[strtolower($var['ext'])];
	            if ( !$var['icon'] )
	            	$var['icon'] = "txt.gif";
	        }

			// Find the target to open the message
			$target = '';

			if (!preg_match('/html|htm|text|txt/', $var['ext']) || $atmail->LoginType == "simple" || $atmail->LoginType == "blue_pane" )
				$target = "_blank" ;

			$obj->attachname[$k]['name'] = $obj->myescape( $obj->attachname[$k]['name'] );

			if (!$v['mime'])
				$obj->attachname[$k]['mime'] = $name;

			// Make a string of raw attachments for JS functions
			$var['rawattachments'] .= "{$obj->attachname[$k]['name']}::";

			// don't allow .htaccess execution
			if (preg_match('/.htaccess/', $v['path'])) {
				chmod($v['path'], 0444);
			}

		    $var['attachments'] .= $atmail->parse(
				"html/$atmail->Language/readmail-attachment.html",
				  array(
					  'Path'     => $v['path'],
					  'FileName' => rawurlencode($v['rawname']),
					  'Size'     => $v['size'],
					  'Desc'     => $v['desc'],
					  'Download' => rawurlencode($v['name']),
					  'Name'     => preg_replace('/\.safe$/', '', $v['name']),
					  'Icon'     => $var['icon'],
					  'Target'   => $target)
						);

	    }

	    else
		{
			//$displayname = $v['mime'] ? $v['mime'] : $name; // Correctly read the MIME header for the real filename , not yet implemented
			//$displayname = $name;//$obj->myunescape($name);
	        $download = $obj->myescape($v['name']);

			// Make a string of raw attachments for JS functions
			$var['rawattachments'] .= "{$obj->attachname[$k]['name']}::";
			$var['image_attachments'] .= "<hr noshade width='95%'><font class='sw'>{$v['desc']} ($download):</font><br>
			<a target=\"_blank\" href=\"mime.php?file=" . rawurlencode($v['rawname']) . ".safe&name=$download.safe\" title=\"download image ({$v['size']}kb)\">
			<img src='mime.php?file=" . rawurlencode($v['rawname']) . ".safe&amp;name=" . $obj->myescape($v['desc']) . "' style='max-width: 95%;'>
			</a>";

		}
	}
}

function cleanText($txt)
{
    foreach (range(0, 31) as $chr) {
        // Leave in linefeeds
        if ($chr == 10) {
            continue;
        }
        $txt = str_replace(chr($chr), '', $txt);
    }

	return str_replace(']]>', '', $txt);
}


function extract_eml($path)
{
	global $email, $atmail, $auth;

	$email2 = new ReadMsg(array(
          'Username'   => $atmail->username,
          'Pop3host'   => $atmail->pop3host,
          'Password'   => $auth->password,
          'Type'       => $atmail->MailType,
          'Mode'       => $atmail->Mode,
          'SessionID'  => $auth->SessionID,
          'Language'   => $atmail->Language,
          'DateFormat' => $atmail->DateFormat,
          'TimeFormat' => $atmail->TimeFormat,
          'LoginType'  => $type,
          'head'       => 1,
          'rawemail'   => $var['rawemail'])
        );

    $email2->reademail('', '' ,'', '', $path);

    if ($email2->html)
        $type = 'html';
    elseif ($email2->txt)
        $type = 'txt';
    elseif ($email2->multiparttxt)
        $type = 'multiparttxt';
	else {
		$type = 'html';
	}

    $myvars = array('subject' => $email2->subject, 'from' => htmlentities($email2->from), 'to' => htmlentities($email2->to), 'date' => $email2->date, 'body' => $email2->$type);

    $fwdmsg = $atmail->parse("html/$atmail->Language/fwdmsg.html", $myvars);

    if ($email->html)
    	$email->html .= $fwdmsg;
    elseif ($email->txt)
    	$email->txt .= $fwdmsg;
    else
    	$email->html .= $fwdmsg;

    unset($fwdmsg);

    parse_attachments($email2);

    foreach ($email2->attachedemails as $path2) {
    	extract_eml($path2);
    }
}
?>
