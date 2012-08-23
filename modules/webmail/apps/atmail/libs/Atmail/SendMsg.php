<?php
// +----------------------------------------------------------------+
// | SendMsg.php													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: Febuary 2005												|
// +----------------------------------------------------------------+
require_once('header.php');

require_once('Config.php');
require_once('Mail/mime.php');
require_once('SQL.php');
require_once('Abook.class.php');	// Used to expand a group recipient
require_once('GetMail.php');
require_once('MIME_Words.php');
require_once('Mail/RFC822.php');


class SendMsg
{

	var $sql;
	var $Account;
	var $Username;
	var $Pop3host;
	var $RealName;
	var $abook;
	var $top;
	var $Date;
	var $EmailMessage;
	var $EmailTo;
	var $EmailCC;
	var $EmailBCC;
	var $EmailFrom;
	var $EmailBox;
	var $EmailEncoding;
	var $EmailUIDL;
	var $ReplyTo;
	var $EmailAttach;
	var $AddRecipients;
	var $Charset;
	var $X_Origin;
	var $PGPsign;
	var $PGPappend;
	var $ContentType;
	var $SessionID;

	var $inlineimages = array();

	/**
	 * Paths for all emails that need to
	 * be forwarded with msg
	 *
	 * @var array
	 */
    var $emailPaths = array();


	// New instance.
	function SendMsg($args)
	{
		global $atmail;

	    foreach ($args as $k => $v )
	    {
	    	$k = str_replace('-', '_', $k);
	        $this->$k = $v;
	    }

		list($this->Username, $this->Pop3host) = explode('@', $this->Account);

		// Some rendering errors with Mozilla editor
		$this->EmailMessage = str_replace('&lt;!--', '<!--', $this->EmailMessage);
		$this->EmailMessage = str_replace('--&gt;', '-->', $this->EmailMessage);

		// Create a real CLF pair from the email-message ( sanity check the output )
		$this->EmailMessage = str_replace("\r", '', $this->EmailMessage);

		// Replace mime.php src's with original CIDs
		$this->EmailMessage = preg_replace('/("|\')mime.php\?file=(.+?)&(amp;)?cid=(.+?)("|\')/i', '"cid:\\4"', $this->EmailMessage);

		$this->sql =& $atmail->db;
		//$this->sql->table_names( $this->Account );
		list($this->RealName, $this->ReplyTo, $this->EmailEncoding) = $this->sql->sqlarray( "select Realname, ReplyTo, EmailEncoding from {$this->sql->UserSettings} where Account=?", $this->Account);

		//if(!$this->EmailEncoding)
		// Set UTF-8 as all default encoding, will work for any setup and charset, less encoding problems
		$this->EmailEncoding = 'utf-8';

		// check for valid e-mail to prevent xss issues
		if (!$this->is_valid_email($this->EmailFrom))
		{
            $this->EmailFrom = $this->Account;
		}
		// If we are using a different email personality, switch the ReplyTo to the new account
		if ($this->EmailFrom != $this->Account)
			$this->ReplyTo = $this->EmailFrom;

		if ($this->ReplyTo == '')
			$this->ReplyTo = $this->EmailFrom;

		$this->abook = new Abook(array('Account' => $this->Account));
		$this->mime = new Mail_mime();
		$this->mime->_build_params['head_charset'] = $this->EmailEncoding;
		$this->mime->_build_params['html_charset'] = $this->EmailEncoding;
		$this->mime->_build_params['text_charset'] = $this->EmailEncoding;

		// Set the default character set, if the previous message did not specify one
		$this->Charset = $this->EmailEncoding;

		$this->log = new Log(array('Account' => $this->Account));
	}

	function Destroy()
	{
		$this->mime = null;
		$this->sql->disconnect();
	}

	function buildmsg()
	{
		global $pref, $brand, $atmail;

		// Save UTF-8 versions of the strings, which are sent to the browser
		$this->RawEmailSubject = $this->EmailSubject;

	    // Format the date correctly
	    $this->Date = date('r');

	    // return an error if To: field contains only , or ;
        if (strlen($this->EmailTo) == 0 || (!preg_match('/\@/', $this->EmailTo) && (preg_match('/,/', $this->EmailTo))) && $this->EmailBox != "Draft")
        {
          	return $this->smtperror("Please specify an email in the To: field $this->EmailTo");
        }

		   // Do not test if the email has a @ symbol, if a user specifies an add-recipients > group , the format is
		   // Groupname Group , there is no email address with an @

           // return an error if To: field is empty or contains no email addresses
           // if ((strlen($this->EmailTo) == 0 || !(preg_match('/\@/', $this->EmailTo)))
           //     && $this->EmailBox != "Draft" )
           // {
           //             return $this->smtperror("Please specify an email in the To: field $this->EmailTo");

           // }

           // return an error if CC string contains characters but no email addresses
           //if (strlen($this->EmailCC) > 0)
           //{
           //           return $this->smtperror("Please specify an email in the CC: field $this->EmailCC");
           //}
           // return an error if BCC string contains characters but no email addresses
           //if (strlen($this->EmailBcc) > 0)
           //   {
           //           return $this->smtperror("Please specify an email in the BCC: field $this->EmailBcc");
           //   }

                // Read our attachment directory
		$dir = $atmail->tmpdir;

		// If we don't have a valid directory ( e.g new account via webadmin , use user_dir )
		if(!is_dir($atmail->tmpdir))
			$dir = $pref['user_dir'];

		if (!file_exists($dir))
			mkdir($dir, 0777);

	    $dh = opendir($dir);
	    if (!is_resource($dh))
	    	catcherror("Cannot read attachment dir: {$this->tmpdir}");

		if ( !$atmail->isset_chk($this->Unique) )
	    	$this->Unique = "0";

        $acc = preg_quote($this->Account, '/');
        $unique = preg_quote($this->Unique, '/');

	    while (false !== $file = readdir($dh))
	    {
	        if (preg_match("/^$acc-$unique-cid:(.+?)-name:(.+?)$/", $file, $m))
	        {
	            $this->inlineimages[] = array('filename' => "$dir/$file", 'cid' => $m[1], 'name' => $m[2]);
	        }
	        elseif (preg_match("/^$acc-$unique/", $file))
	        {	
	            $this->attach($file);
	            $this->EmailAttach++;
	        }

	    }

	    closedir($dh);

	    // Add our message footer, only for outgoing messages
	    if ( preg_match('/plain/', $this->ContentType) && $this->EmailBox != 'Drafts')
	    {
	    	if (isset($brand[$_SERVER['HTTP_HOST']]["footer_msg"]))
				$pref['footer_msg'] = $brand[$_SERVER['HTTP_HOST']]["footer_msg"];


			// Take away any HTML characters
			$pref['footer_msg'] = str_replace(array('<hr>', '<HR>'), '---- ', $pref['footer_msg']);
			$pref['footer_msg'] = strip_tags($pref['footer_msg']);

			// Clean the footer_msg and make it CLF clean
			$pref['footer_msg'] = str_replace("\r", '', $pref['footer_msg']);

			// Evaluate any $vars
			$pref['domain'] = $this->Pop3host;
			//$pref['footer_msg'] = preg_replace('/(\$[0-9A-Za-z\-_\[\]>]+)/e', '$1', $pref['footer_msg']);
			$pref['footer_msg'] = preg_replace('/(\$pref[0-9A-Za-z\-_\[\]>]+)/e', '$1', $pref['footer_msg']);

			if (strlen($this->VideoStream) > 0 )
			{
				$this->EmailMessage = "Video mail attached:\nTo view please see: http://{$pref['videomail_server']}/videomail/view/$this->VideoStream/\n\n" . $this->EmailMessage;
			}

			// Only add the footer message if it is not already at bottom of email
			if (!empty($pref['footer_msg']) && strpos($this->EmailMessage, $pref['footer_msg']) !== strlen($this->EmailMessage) - strlen($pref['footer_msg'])) {
				$this->EmailMessage .= "\r\n{$pref['footer_msg']}";
			}
	    }
	    elseif ( preg_match('/html/', $this->ContentType) && $this->EmailBox != 'Drafts')
	    {
	    	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'gecko') !== false) {
		    	// Create links from plain urls
		    	//$this->EmailMessage = preg_replace('/(?<!href="|\')(http:\/\/[^\s<>\'"]+)(?!/i', '<a href="$1">$1</a>', $this->EmailMessage);
		    	$this->EmailMessage = preg_replace('/(?<!\'|"|>|&gt;)(http:\/\/[^\s<>\'"]+)/i', '<a href="$1">$1</a>', $this->EmailMessage);
	    	}

			// Create 'real' newlines from <BR>'s
			$this->EmailMessage = str_replace(array('<BR>', '<br>'), "<br>\r\n", $this->EmailMessage);

			// Evaluate any $vars
			$pref['domain'] = $this->Pop3host;

			if ( $brand[$_SERVER['HTTP_HOST']]["footer_msg"] ) {
				$pref['footer_msg'] = $brand[$_SERVER['HTTP_HOST']]["footer_msg"];
			}

            // Evaluate any $vars
			$pref['footer_msg'] = preg_replace('/(\$pref[0-9A-Za-z\-_\[\]>]+)/e', '$1', $pref['footer_msg']);

			// Clean the footer_msg and make it CLF clean
			$pref['footer_msg'] = str_replace("\r", '', $pref['footer_msg']);


			if (!empty($pref['footer_msg']) && strpos($this->EmailMessage, $pref['footer_msg']) !== strlen($this->EmailMessage) - strlen($pref['footer_msg'])) {
				$this->EmailMessage .= "<BR>{$pref['footer_msg']}";
			}

			if (strlen($this->VideoStream) > 0) {
				$this->EmailMessage = <<<EOF
<HTML><table width="100%" style="border: 1px solid #468BC7;">
<tr>
<td style="background-color: #D8E7F5; padding: 3px;" nowrap>
Video Mail Attached. To view in your browser click the link below:<br>
<a href="http://{$pref['videomail_server']}/videomail/view/$this->VideoStream/">http://{$pref['videomail_server']}/videomail/view/$this->VideoStream/</a>
</td>
</tr>
</table>
<br>
$this->EmailMessage
</HTML>
EOF;
			} else {
				// Append <HTML> tags to make Spamassassin score less
				$this->EmailMessage = "<HTML>\n" . $this->EmailMessage . "</HTML>\n";
			}
	    }

        /* disabled for now
		// Create a new PGP object if required
		if ($this->PGPsign || $this->PGPappend)
		{
			$userWrkDir = ($atmail->MailDir) ? $atmail->MailDir : $atmail->tmpdir;
		    $ownFile = $atmail->tmpdir . ".ht.".$this->SessionID;

			$pgp = new PGP( array('wrkDir' => "$userWrkDir/pgp", 'ownFile' => $ownFile) );

			// Automatically sign the mail with the users PGP key, only if our pass-phrase is available
			if ($this->PGPsign && file_exists($pgp->ownFile))
			{
				$rec = array();
				$this->EmailMessage = $pgp->encrypt($this->EmailFrom, $rec , $this->EmailMessage, "s");
			}

			// Automatically append the users PGP key to an outgoing message
			if ($this->PGPappend)
			{
				// Load a temporary var containing for PGP public key
				$msgTmp = $pgp->retrieveAsciiPub();

				// Turn newlines into <BR>'s for the HTML emails
				if ( strpos($this->ContentType, 'html') !== false )
					$msgTmp = nl2br($msgTmp);

				// Append the PGP public key to the email message
		    	$this->EmailMessage .= $msgTmp;
			}

		}
        */
        
	    // The from Header is our ReplyTo if specified in the settings, only if we are the default account
	    if ( $this->ReplyTo && $this->Account == $this->EmailFrom )
	    	$this->EmailFrom = $this->ReplyTo;


		$rfc822 = new Mail_RFC822;

		foreach (array('EmailTo', 'EmailCC', 'EmailBCC') as $type)
		{
		    if ($this->$type != '')
		    {
		        //remove leading semi-colon
		        $this->$type = preg_replace('/^\s*;\s*/', '', $this->$type);
		        $this->$type = str_replace(array(';', ' Shared Group', ' Group'), array(',', '@SharedGroup', '@Group'), $this->$type);

		        // Remove "smart quotes" (aka dumb quotes)
		        $smartquotes = array('“', '”', '‘', '’', "\x98", "\x99", "\x8c", "\x9d", chr(147), chr(148), chr(146), 'R20;', 'R21;', 'R17;', 'R16;');

		        $this->$type = str_replace($smartquotes, '"', $this->$type);

		        // Optionally encode the users name in the header
		        preg_match_all('/(?<=")(.+?)(?=" <)/', $this->$type, $m, PREG_SET_ORDER);
		        if (is_array($m[0])) {
		        	foreach ($m[0] as $match) {
		        		$this->$type = str_replace($match, $this->encodeUTF8($match), $this->$type);
		        	}
		        }
		        //$this->$type = preg_replace('/(?<=")(.+?)(?=" <)/e', '$this->encodeUTF8(\'$1\')', $this->$type);

		        $groups = $rfc822->parseAddressList($this->$type, null, false, true);

		        $this->$type = '';

		        foreach ($groups as $group)
		        {
		            if (is_string($group))
		            {
		                preg_match('/(.*?)<(.*?)>/', $group, $m);
		                $name = trim($m[1]);
		                $mail = $m[2];
		            }
		            else
		            {
    		            $name = $group->personal;

    		            // Replace ", ' and , from the name, cleanup and parse the address below
    		            //$name = str_replace(array('"', ','), '', $name);

    		            $mail = $group->mailbox . '@' . $group->host;
		            }

		            // insert recipients from shared groups
		            if ( strpos($mail, 'Shared Group') !== false )
		            {
		                preg_match('/(.*?)Shared Group/', $mail, $match);
		                $this->$type .= $match[1]."SharedGroup, ";
		            }

		            // insert recipients from another groups
		            elseif ( preg_match('/(.+?)Group$/i', $mail, $match))
		            {
		                $this->$type .= $match[1]."Group, ";
		            }
		            // insert single recipient with personal information (Lastname/Firstname)
		            elseif (strlen($name) > 0)
		            {
	                    $address = "$name <$mail>";
	                    $this->$type .= $address . ", ";
	                    $this->AddRecipients .= "$mail, ";
		            }
		            else
		            {
		                $address = "<$mail>";
		                $this->$type .= $address . ", ";
		                $this->AddRecipients .= "$mail, ";
		            }

		        }
		    }

		    // Remove the trailing comma @ the end of the text
		    $this->$type = preg_replace('/, $/', '', $this->$type);
		}

		$this->AddRecipients = preg_replace('/, $/', '', $this->AddRecipients);

		// If there is a video-message prepend "VideoMail:" in the subject
		if ($this->VideoStream && !preg_match('/VideoMail:/i', $this->EmailSubject))
			$this->EmailSubject = "VideoMail: " . $this->EmailSubject;

		// Decode our RealName and EmailSubject from UTF8 -> Charset
		$this->RealName = GetMail::encode_language($this->Charset, $this->RealName);
		$this->EmailSubject = GetMail::encode_language($this->Charset, $this->EmailSubject);

		// If we are not using the standard charset, encoding the email-subject with the subject ( base64 for quoted printed encoding)
		// Only encode if the output contains non ASCII characters
		$this->EmailMessage = str_replace("\r", '', $this->EmailMessage);;

		if ( preg_match('/iso/i', $this->Charset))
		{
			if ($this->_check_if_contain_utf8($this->EmailSubject))
				$this->EmailSubject = MIME_Words::encode_mimeword($this->EmailSubject, "Q", $this->Charset);
			if ($this->_check_if_contain_utf8($self->RealName))
				$this->RealName = MIME_Words::encode_mimeword($this->RealName, "Q", $this->Charset);

			$this->EmailMessage = GetMail::encode_language($this->Charset, $this->EmailMessage);
		}
		else
		{
			if ($this->_check_if_contain_utf8($this->EmailSubject))
				$this->EmailSubject = MIME_Words::encode_mimeword($this->EmailSubject, "B", $this->Charset);
			if ($this->_check_if_contain_utf8($this->RealName))
				$this->RealName = MIME_Words::encode_mimeword($this->RealName, "B", $this->Charset);

			$this->EmailMessage = GetMail::encode_language($this->Charset, $this->EmailMessage);
		}

		$this->mime->setSubject($this->EmailSubject);

		$this->RealName = trim($this->RealName);

		if (strlen($this->RealName) > 0 && $pref['allow_FullName'])
            $this->mime->setFrom("$this->RealName <$this->EmailFrom>");
        else
            $this->mime->setFrom($this->EmailFrom);

        // Convert back any @Mail created links that redirect through parse.php
        // and javascript.opencompose() (only if replying to or forwarding a msg)
        if ($this->ReplyFwd == 'reply' || $this->ReplyFwd == 'forward')
            $this->_cleanLinks();

        // If user is using the HTML editor, send a HTML message otherwise plain txt
	    if ( strpos($this->ContentType, 'html') !== false )
	    {
	        $this->mime->headers(array(
	          	'To'        => str_replace(array('@SharedGroup', '@Group'), array(' Shared Group', ' Group'), $this->EmailTo),
	          	'Reply-To'  => $this->ReplyTo,
			  	'Content-Type'    	=> "multipart/related; charset=\"$this->Charset\"",
	          	'X-Mailer'  => $this->XMailer,
	          	'X-Origin'	=> $this->X_Origin,
	          	'X-Atmail-Account' => $this->Account,
			  	'Date' 		=> $this->Date));

	        // Now create the text/plain part also
            require_once('class.html2text.inc');
            $html2text = new html2text($this->EmailMessage);
            $txt = $html2text->get_text();
            $txt = preg_replace('/^\s*BODY\s*\{.+?\}/s', '', $txt);

            $html = $this->EmailMessage;
            
            // Cleanup PGP block if one exists
	        if (strpos($this->EmailMessage, '-----BEGIN PGP MESSAGE-----') !== false) {
	            $html = PGP::cleanPgpBlock($this->EmailMessage, 'message');
	            $txt  = PGP::cleanPgpBlock($txt, 'message');
	        }

	        // Cleanup PGP block if one exists
            if (strpos($this->EmailMessage, '-----BEGIN PGP PUBLIC KEY BLOCK-----') !== false) {
                $html = PGP::cleanPgpBlock($this->EmailMessage, 'pubkey');
                $txt  = PGP::cleanPgpBlock($txt, 'pubkey');
            }

            // add the text/html part
			$this->mime->setHTMLBody($html);
			$this->mime->setTXTBody($txt);
	    }
	    else
	    {
	        $this->mime->headers(array(
	          	'To'        => str_replace(array('@SharedGroup', '@Group'), array(' Shared Group', ' Group'), $this->EmailTo),
	          	'Reply-To'  => $this->ReplyTo,
			  	'Content-Type'    	=> "text/plain; charset=\"$this->Charset\"",
	          	'X-Origin'	=> $this->X_Origin,
	          	'X-Atmail-Account' => $this->Account,
			  	'Date' 		=> $this->Date
	    	));

	    	$this->mime->setTXTBody($this->EmailMessage);
	    }

		// Append our X-Video mail message
		if (strlen($this->VideoStream) > 0)
		{
			$this->mime->headers(array('X-VideoMail' => "http://{$pref['videomail_server']}/videomail/view/$this->VideoStream"));
		}

	    // Added support for CC / BCC messages
	    if ( $this->EmailCC )
	    	$this->mime->addCc(str_replace(array('@SharedGroup', '@Group'), array(' Shared Group', ' Group'), $this->EmailCC));

		$messageid = "<{$_SERVER['REMOTE_PORT']}." . time() . "@$this->Pop3host>";
		$this->mime->headers( array('Message-ID' =>  $messageid) );

		// Replace the X-Mailer with our custom copy
		$this->mime->headers( array('X-Mailer' => $this->XMailer) );

		if ($this->ReadReceipt)
		{
			// Define the Read-receipt if toggled on - Split over two calls, all in one seemed to fail
			$this->mime->headers(array('X-Confirm-Reading-To' => $this->ReplyTo));
			$this->mime->headers(array('Return-Receipt-To' => $this->ReplyTo));
			$this->mime->headers(array('Disposition-Notification-To' => $this->ReplyTo));
		}


	    if ( $this->EmailPriority )
	    {
	        $this->mime->headers( array('X-Priority' => $this->EmailPriority) );
	        if ( $this->EmailPriority == 5 )
	        	$this->mime->headers( array('X-MSMail-Priority' => 'Low') );
	        if ( $this->EmailPriority == 1 )
	        	$this->mime->headers( array('X-MSMail-Priority' => 'High') );
	    }

	    $TypeFor = array(
	        	'txt'  => 'text/plain',
	          	'sh'   => 'text/x-sh',
	          	'csh'  => 'text/x-csh',
	          	'pm'   => 'text/x-perl',
	          	'pl'   => 'text/x-perl',
	          	'jpg'  => 'image/jpeg',
	          	'jpeg' => 'image/jpeg',
	          	'gif'  => 'image/gif',
	          	'png'  => 'image/png',
	          	'tif'  => 'image/tiff',
	          	'tiff' => 'image/tiff',
	          	'xbm'  => 'image/xbm',
	          	'eml'  => 'message/rfc822'
	        );

	    /*
	    // attach any messages forwarded as attachments
	    $names = array();
	    $i = 1;
	    foreach ($this->emailPaths as $path) {
	        $fh = fopen($path, 'r');
	        while (false !== $line = fgets($fh)) {
	           if (preg_match('/^subject:\s*(.+)/i', $line, $m)) {
	               $name = GetMail::quote_header($m[1]);

	               while (in_array($name, $names)) {
	                   if ($i == 1)
	                       $name = "{$name}_1";
	                   else
    	                   $name = preg_replace('/_\d+$/', $name, "_$i");

    	               $i++;
	               }

	               $names[] = $name;
	               $name = "$name.eml";
	               break;
	           }
            }
	        $this->mime->addAttachment($path, 'message/rfc822', $name);
	    }
        */

	    // We have attachments in our folder
	    if ( $this->EmailAttach )
	    {
	        // Loop through each attachment
	        foreach ($this->attachname as $file)
	        {
	            if ( strpos($file, $this->Account) === false )
	            	continue;

	            $name = $file;

	            // Strip the filename header with our account, rand and pid prefix
				$name = preg_replace("/^$this->Account-\d+-/", '', $name);
				
				// strip the .safe extension
				$name = preg_replace('/\.safe$/', '', $name);

	            // Find the extension of the file
	            if ( preg_match('/\.(\w+)$/', $name, $match) )
	            	$ext = $match[1];

				// Language encode if we contain different characters
				if ( $this->_check_if_contain_utf8($name) )
					$name = MIME_Words::encode_mimeword(GetMail::encode_language('UTF-8', $name ), "B", 'UTF-8');

	            // Attach the file to the message
	            $ext = strtolower($ext);
	            $type = ($TypeFor[$ext])? $TypeFor[$ext] : 'application/octet-stream';

	            $this->mime->addAttachment($atmail->tmpdir . "/$file", $type, $name) || catcherror("Cannot attach filename to message : $name");
	        }
	    }

	    // Add CID images
	    foreach ($this->inlineimages as $image) {
	        // Find the extension of the file
            if ( preg_match('/\.(\w+)$/', $image['name'], $match) )
            	$ext = $match[1];
            $type = $TypeFor[$ext];
	        $this->mime->addHtmlImage($image['filename'], $type, $image['name'], true, $image['cid']);
	    }

	    $this->body = $this->mime->get(array('text_encoding'=>'quoted-printable', 'html_encoding'=>'quoted-printable'));
	    $this->headers = trim($this->mime->txtHeaders());
	}


    function toString($object)
    {
      $string  = (string) $object;
      return $string;
    }

	function &header_as_arrayref($fields)
	{
		$lines = array();

		foreach ($fields as $tag => $value)
		{
			if ($value == '')
				continue;          ### skip empties
			$tag = preg_replace('/\b([a-z]+)/e', "strtoupper('\\1')", $tag);   ### make pretty
			$tag = preg_replace('/^mime-/', 'MIME-', $tag);       ### even prettier
			$lines[] = "$tag: $value\n";
		}
		return $lines;
	}


	function savemsg()
	{
		$emailbox = ($this->EmailBox)? $this->EmailBox : 'Sent';

	    return $this->sql->savemsg(array(
	      'Account'      => $this->Account,
	      'EmailSubject' => $this->EmailSubject,
	      'EmailTo'      => str_replace(array('@SharedGroup', '@Group'), array(' Shared Group', ' Group'), $this->EmailTo),
	      'EmailFrom'    => $this->EmailFrom,
	      'EmailDate'    => $this->EmailDate,
	      'EmailBox'     => $emailbox,
	      'EmailFlag'    => $this->EmailFlag,
	      'EmailAttach'  => $this->EmailAttach,
	      'EmailUIDL'    => $this->EmailUIDL,
	      'EmailMessage' => $this->headers . "\r\n\r\n" . $this->body)
	    );
	}


	// Deliver the email message via the SMTP server defined in Config.php
	function deliver($catch_smtp_error=true)
	{
		global $pref, $atmail, $domains;

	    $rcpt = array();

	    // Make an array with the email addresses to deliver the
	    // message to
	    /*
	    $rcpt = array_merge($rcpt, preg_split('/;|,/', $this->EmailTo));

		if (!empty($this->EmailCC))
			$rcpt = array_merge($rcpt, preg_split('/;|,/', $this->EmailCC));

		if (!empty($this->EmailBCC))
			$rcpt = array_merge($rcpt, preg_split('/;|,/', $this->EmailBCC));
		*/
	    $rcpt = $this->EmailTo;

	    if (!empty($this->EmailCC))
			$rcpt .= ",$this->EmailCC";

		if (!empty($this->EmailBCC))
			$rcpt .= ",$this->EmailBCC";

        // Remove personal part - it is not needed for send RCPT cmd
        // and just causes problems for the address parser if it contains
        // certain utf-8 chars (e.g. Japanese)
        $rcpt = preg_replace('/".+?"/', '', $rcpt);

		$rcpt = Mail_RFC822::parseAddressList($rcpt, null, false);

	    $mail = array();
		$groupmails = 0;

	    // Loop through the non-existant recipients
	    foreach ($rcpt as $entry)
	    {
			// The entry is a group email-address
			if ($entry->host == 'Group')
			{
				$users = $this->abook->getgroup($entry->mailbox);

				foreach ($users as $user)
				{
					// Skip emailing the selected user, if local, and the user does not exist any longer
					if ($this->rcptOK($user)) {
    					$groupmails++;
    					array_push($mail, $user);
					}
				}
			}
			elseif ($entry->host == 'SharedGroup')
			{
				$users = $this->abook->getsharedgroup($entry->mailbox);

				foreach ($users as $user)
				{
					// Skip emailing the selected user, if local, and the user does not exist any longer
					if ($this->rcptOK($user)) {
    					$groupmails++;
    					array_push($mail, $user);
					}
				}
			}

			else
			{
	        	array_push($mail, stripslashes("$entry->mailbox@$entry->host"));
			}

	    }

	    // Block more then X outgoing recipients at a time ( useful to prevent spammers/bots abusing the service )
		if ( (count($mail) - $groupmails) > $pref['max_recipients_per_msg'] )
			$this->smtperror("Cannot send to more than {$pref['max_recipients_per_msg']} users per email message.");

		require_once('Net/SMTP.php');

	    $smtp = new Net_SMTP($pref['smtphost'], null, $_SERVER['HTTP_HOST']);

		//$smtp->setDebug(true);

		if ($smtp->connect(30) !== true)
	    {
	        if ($catch_smtp_error)
	            $this->smtperror("Error connecting to {$pref['smtphost']} - Check the hostname resolves, accepts incoming SMTP connections and is active.");
	        else
	            return false;
	    }

		// Check if we should use each account's username and password
		// for the SMTP
		if ($pref['smtp_per_account_auth']) {
		    $pref['smtpauth_username'] = $atmail->auth->get_username();
		    $pref['smtpauth_password'] = $atmail->auth->get_password();
		}
		
		// Optionally authenticate with the SMTP server
		if ($pref['smtpauth_username'] && $pref['smtpauth_password']) {
			if($smtp->auth($pref['smtpauth_username'], $pref['smtpauth_password']) !== true) {
				if ($catch_smtp_error)
				    $this->smtperror("Error authenticating to {$pref['smtphost']} - Check the SMTP authentication is correct");
				else {
					$this->smtp_error_msg = "Error authenticating to {$pref['smtphost']} - Check the SMTP authentication is correct";
					return false;
				}
			}
		}

	    $smtp->mailFrom($this->EmailFrom);
        $fails = array();

        //$smtp->setDebug(true);
		foreach ($mail as $v)
		{
			$res = $smtp->rcptTo($v);

			if (PEAR::isError($res))
			{
			    if ($catch_smtp_error) {
    				$output = $res->getMessage();
    				$this->smtperror("Could not send message to SMTP server. Check you have access to send messages via the server and that all To/CC/BCC addresses are valid\\nError: $output");
    				break;
			    } else {
			        $fails[] = $v;
			        continue;
			    }
			}
		}

		$res = $smtp->data($this->headers . "\r\n\r\n" . $this->body);

		if (PEAR::isError($res)) {
			if ($catch_smtp_error)
                $this->smtperror("Message rejected by SMTP server. Check message content and attachments\\nServer Responded: " . $res->getMessage());
		    else {
		    	$this->smtp_error_msg = "Message rejected by SMTP server. Check message content and attachments\nServer Responded: " . $res->getMessage();
		        return false;
		    }
		} elseif ($pref['install_type'] == 'standalone') {

			// Only log sent msgs if running only as webmail client
			// as Exim already does this when in server mode

			foreach($mail as $addr)
			{
				$addr = preg_replace('/\s+/', '', $addr);
				$this->log->write_log("SendMail", "WebMail:{$_SERVER['REMOTE_ADDR']}:$addr");
			}
		}

		$smtp->disconnect();

		// Reset some fields back to the raw UTF8 copy, since it's sent to the browser
		$this->EmailSubject = $this->RawEmailSubject;

		return $fails;
	}


	function delete_attachments()
	{
		global $pref, $atmail;

		if (is_array($this->attachname))
		{
			foreach($this->attachname as $file)
			{
				$file = basename($file);
				
				// Delete the attachment after added to the msg
				if (file_exists($atmail->tmpdir . "/$file"))
					@unlink($atmail->tmpdir . "/$file");
			}
		}

		// Delete CID images
		foreach ($this->inlineimages as $image) {
		    if (file_exists($image['filename'])) {
		        unlink($image['filename']);
		    }
        }
	}


	function delete_attachment($file, $unique)
	{
		global $pref, $atmail;

	    // Just in case ...
	    $file = basename($file);

		// add the .safe extension which the file has on disk
		$file .= ".safe";
		
	    // Delete the attachment after added to the msg
	    if (file_exists($atmail->tmpdir . "/{$atmail->Account}-$unique-$file")) {
	       @unlink($atmail->tmpdir . "/{$atmail->Account}-$unique-$file");
	    }
	}


	function attach($name)
	{
		if (!is_array($this->attachname))
			$this->attachname = array();

	    array_push($this->attachname, $name);
	}


	// List the number of attachments in the tmp directory, unique for each user
	// e.g /www/mailos/tmp/me@host.com-image.gif
	function list_attachments($unique)
	{
		global $pref, $atmail;

		$h = array();

	    // Read the User Directory tmp location
	    $dh = opendir($atmail->tmpdir . "/");

	    // List the number of files
	    while (false !== $filename = readdir($dh))
	    {
			if ( $filename == "." || $filename == ".." || !preg_match("/^{$atmail->Account}-$unique-/", $filename) )
				continue;

	        if (file_exists($atmail->tmpdir . "/$filename")) {
	            // Get the size of the filename
	            $size = filesize($atmail->tmpdir . "/$filename");
	            $size = $size / 1024;
	            $filename = preg_replace("/^{$atmail->Account}-$unique-/", '', $filename);
	            $filename = preg_replace('/\.safe$/', '', $filename);
	            $h[$filename]['size'] = sprintf("%2.1f", $size);
	        }

	    }

	    return $h;
	}


	function add_attachment($unique)
	{
		global $pref, $atmail;

		if (!isset($_FILES['fileupload']) || $_FILES['fileupload']['error'] > 2) {
			print "<script language='JavaScript'>alert('Upload Failed');</script>";
	    	return;
		}

	    if ($_FILES['fileupload']['error'] == 1 || $_FILES['fileupload']['error'] == 2 || $_FILES['fileupload']['size'] > ( $pref['max_msg_size'] * 1048576 )) {
	        return false;
	    }

		// strip any path and add ".safe" as the extension to avoid any uploaded scripts being exectued (e.g. .php)
		$filename = basename($_FILES['fileupload']['name']) . ".safe";
		$unique = basename($unique);
		$pathname = AtmailGlobal::escape_pathname($atmail->tmpdir . "/{$atmail->Account}-$unique-$filename");

		if ( file_exists($pathname) ) {
			$pathname = AtmailGlobal::escape_pathname($atmail->tmpdir . "/tmp/$atmail->Account-$unique-".getmypid().$filename);
		}


		if (move_uploaded_file($_FILES['fileupload']['tmp_name'], $pathname)) {
		    // don't allow .htaccess execution
		    if ($filename == '.htaccess')
		    {
		    	chmod($pathname, 0444);
		    }

			$this->Import = "{$atmail->Account}-$filename";
			return "Added $filename";
		}

		print "<script language='JavaScript'>alert('Upload failed');</script>";

	}

    function create_attachment($filename, $content)
    {
        global $atmail;
        
        $filename = basename($filename) . ".safe";
        $pathname = AtmailGlobal::escape_pathname($atmail->tmpdir . "/{$this->Account}-$this->Unique-$filename");

        file_put_contents($pathname, $content);
        $this->attach($pathname);
    }

	function smtperror($msg)
	{
		global $pref, $atmail;

		if($this->IgnoreError)
		return;

	    // Find the origin of the script
	    $location = $_SERVER['SCRIPT_NAME'];

		if (strpos($location, 'admin.php'))
			$location = "$location?func={$_REQUEST['func']}";

	    // If a send message errors on the compose screen, reload the email message
	    // and print a Javascript alert to the browser

	    $editor = $atmail->isset_chk($_REQUEST['HtmlEditor'])? $_REQUEST['HtmlEditor'] : '2';

	    if ( strpos($location, 'sendmail.php') !== false )
	    {
	        $path = time() . getmypid() . "err";

	        // Redirect the user to the compose screen, with their email intact
	        $location = "compose.php?spellcheck=$path&func=spellcheck&HtmlEditor=$editor&unique=" . $_REQUEST['unique'];

	        $fh = fopen($atmail->tmpdir . "/$path", 'w');
			if (!is_resource($fh))
				catcherror("Could not open {$atmail->tmpdir}/$path");

			$tmp = array();

	        // Create a temp hash containing our vars
	        foreach ( array('emailto', 'emailsubject', 'emailcc', 'emailbcc', 'contype') as $v )
	            $tmp[$v] = $_REQUEST[$v];

	        // Print the email message, raw headers, encoding will be set 100%
	        fwrite($fh, $this->headers . "\r\nBcc: {$tmp['emailbcc']}\r\n" . "\r\n\r\n" . $this->body );
	        fclose($fh);
	    }

	    print $atmail->parse( "html/english/errorsmtp.html", array('error' => $msg, 'location' => $location) );

		exit;
	}

	// Generate a temporary file
	function gettempfile()
	{
		global $pref, $atmail;

	    $alpha = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'z', 'u', 'z', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0);

	    $s = time();

	    for ($i=1; $i<=10; $i++)
	    {
	        $num = rand(0,30);
	        $s .= $alpha[$num];
	    }

	    // Generate another key if it already exists
	    if ( file_exists($atmail->tmpdir . "/tmp/$s") )
	    	$this->gettempfile();

	    return $s;
	}

	function _check_if_contain_japanese($string)
	{
		$string = str_replace(array("\n", "\r"), '', $string); // ignore line-break
		return preg_match('/[\x01-\x08\x0B\x0C\x0E-\x1F\x7F\x21\x23-\x5B\x5D-\x7E\x20]+/', $string);
	}

	function _check_if_contain_utf8($string)
	{
		// Ignore line break
		$string = rtrim($string);

		$invalid = preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f-\xff]/', $string);

		# Convert back our original encoding
		#if($invalid > 0 && $self->{Charset} eq "iso-8859-1")	{
		#$self->{Charset} = "UTF-8";
		#}

		return $invalid;

		#return ($string =~ /[\x00-\x08\x0b\x0c\x0e-\x1f\x7f-\xff]/g);
        #$string =~ tr/\n|\r//d; # ignore line-break
        #return $string =~ tr/\x00-\x7F//c;
	}

	# Rename an attachment on disk for Ajax interface
	function renameattach($unique, $attach)	{
		global $pref, $atmail;
		
		$unique = basename($unique);
		$attach = basename($attach);
		
		$file = $atmail->tmpdir . $attach;

		# If the filename exists
		if(file_exists($file))	{
			$this->copyFile($file, $atmail->tmpdir . $atmail->Account . "-$unique-$attach.safe");
		}

	}

	function copyFile($file, $new)
	{
		global $pref;

		if (!file_exists($file))
		{
			return;
		}

		$fh = fopen($file, 'r');

		if (!$fh2 = fopen($new, 'w'))
			die("Could not open $new for writing");

		while (!feof($fh))
		{
			$line = fgets($fh);
			fwrite($fh2, $line);
		}

			fclose($fh2);
			fclose($fh);
	}

	/**
	 * Check for valid e-mail address
	 *
	 * @param string $email
	 * @return boolean
	 */
	function is_valid_email($email)
	{
	    if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email))
	    {
	        return false;
	    }
	    // Split it into sections to make life easier
	    $email_array = explode("@", $email);
	    $local_array = explode(".", $email_array[0]);
	    for ($i = 0; $i < sizeof($local_array); $i++)
	    {
	        if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~\-][A-Za-z0-9!#$%&'*+/=?^_`{|}~.\-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i]))
	        {
	            return false;
	        }
	    }
	    if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1]))
	    {
	        $domain_array = explode(".", $email_array[1]);
	        if (sizeof($domain_array) < 2)
	        {
	            return false;
	        }

	        for ($i = 0; $i < sizeof($domain_array); $i++)
	        {
	            if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i]))
	            {
	                return false;
	            }
	        }
	    }
	    return true;
	}


	function rcptOK($user)
	{
        global $domains;
        list($uname, $dom) = explode('@', $user);

        if (empty($uname) || empty($dom))
            return false;

        if (AtmailGlobal::isset_chk($domains[$dom])) {

            // Check for non-existant local users
            if (!$this->sql->sqlgetfield("select Account from UserSession where Account=?", $user))
                return false;

            // Check if recipient has whitelist on
            $res = $this->sql->sqlgetfield('select distinct value from SpamSettings where (username=? or username="GLOBAL") and preference="whitelist_only" and value="1"', $user);

            if ($res == 1) {
                // Whitelisting is on so check for sender
                $query = 'SELECT DISTINCT value from SpamSettings where ( username=? or username="GLOBAL" ) and preference="whitelist_from" and ( value=? OR value=? )';
                $senderDom = strstr($this->EmailFrom, '@');
                $data = array($user, $this->EmailFrom, $senderDom);
                $res = $this->sql->sqlgetfield($query, $data);
                if (empty($res)) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Converts any links created by @Mail in ReadMsg.php
     * back into normal links or plain text depending on
     * email content type
     */
    function _cleanLinks()
    {
        if (strpos($this->ContentType, 'html') !== false) {
            if (strpos($this->EmailMessage, 'parse.php')) {
                // strip @Mail created links
                $this->EmailMessage = preg_replace('/(?<=href="|\')parse\.php\?redirect=(.+?)(?="|\')/e', 'urldecode(\'$1\')', $this->EmailMessage);
            }

            if (strpos($this->EmailMessage, 'top.opencompose')) {
                $this->EmailMessage = preg_replace('/"javascript:top\.opencompose\(\'(.+?)\'(.+?)>/', '"mailto:$1">', $this->EmailMessage);
            }
        } else {
            $this->EmailMessage = strip_tags($this->EmailMessage);
        }
    }


    function encodeUTF8($string)
    {
        if ( $this->_check_if_contain_utf8($string)) {
            $string = MIME_Words::encode_mimeword($string, "B", $this->Charset);
        }

        return $string;
    }

}

?>
