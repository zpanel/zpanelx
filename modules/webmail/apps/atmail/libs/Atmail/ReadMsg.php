<?php

// Read an email message

require_once('header.php');

require_once('Config.php');
require_once('GetMail.php');
require_once('MailParser.php');

class ReadMsg
{

	var $mail;
	var $Username;
	var $Pop3host;
	var $Password;
	var $Type;
	var $Mode;
	var $SessionID;
	var $mailexp;
	var $inline;
	var $emailattachname;
	var $attachedemails = array();
    var $processedSections = array();
    
	/**
	 * Class constructor
	 *
	 * @param array $args
	 *
	 */
	function ReadMsg($args)
	{
	    foreach ($args as $k=>$v)
	        $this->$k = $v;

		$this->mail = new GetMail(array(
	          'Username' => $this->Username,
	          'Pop3host' => $this->Pop3host,
	          'Password' => $this->Password,
	          'Type'     => $this->Type,
	          'Mode'     => $this->Mode,
			  'SessionID' => $this->SessionID)
	    );

	    // A regular expression to find an email
	    $this->emailexp = '([^":\s<>()\/;]*@[^":\s<>()\/;]*)';

	    $this->inline = $this->attachname = array();
		$this->Account = $this->Username . '@' . $this->Pop3host;

		// Add check for tnef program and override $pref setting if it doesn't
		// exist or we are on a windows box
		global $pref;
		if ($pref['decode_tnef']) {
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || !is_executable($pref['tnef_path'])) {
				$pref['decode_tnef'] = false;
			}
		}
	}



	/**
	 * Parse it as a MIME message
	 *
	 * @param int $id message id
	 * @param string $folder
	 * @param string [$nopersonalities]
	 * @param string [$cache]
	 *
	 * @access public
	 */
	function reademail($id, $folder, $nopersonalities = null, $cache = null, $path=null)
	{
		global $atmail, $domains, $pref;

		// Load our temporary filename
		$this->tmpdir = "{$pref['user_dir']}/tmp/" . $this->Username . '@' . $this->Pop3host . "/";

		// Create our temporary directory, if its missing, init in Global.php
		if (!is_dir($this->tmpdir))
			mkdir($this->tmpdir,0777);

		if (!$this->File &&  $cache)
		{
			$this->File = GetMail::check_cache($atmail->tmpdir . "/$this->SessionID-$cache.data");

			// Set to blank if the cache no longer exists
			//if(!file_exists($this->File))
			//	$this->File = '';
		}

	    // Read our email from the server
	    if (!is_string($this->File))
	    {
	    	if (!$nopersonalities && isset($atmail))
				$this->FromField = $atmail->loadpersonalities();

	        $status = $this->mail->login();

	        // We have an error while logging in. Tell the user
	        if ($status)
	        {
	            $this->status = $status;
	            return;
	        }

	        if (is_null($path))
	           $this->path = $this->mail->get( $id, $folder, '', $cache );
	        else
	           $this->path = $path;

	        $this->mail->quit();

			if($this->Type == 'imap')
			$this->MessageState = $this->mail->MessageState;

	        // User has specified the msg from the spellchecker
	    }

		// If using POP3, cache is defined above as $this->File, code below duplicates the filename twice!
	    else if(file_exists($atmail->tmpdir . "/$this->File")) {
	        $this->path = $atmail->tmpdir . "/$this->File";
			$this->MessageState = 'o';
		} else if(file_exists($this->File)) {
			$this->path = $this->File;
			$this->MessageState = 'o';
		}

		    // Just to be sure nobody is reading below a directory
	    $this->path = str_replace('../', '', $this->path);

	    $this->parser = new MailParser;

		if (!is_dir($this->tmpdir))
			mkdir($this->tmpdir,0777);

	    // Users have their own mime-tmp directory
	    if (!$this->parser->output_dir($this->tmpdir))
			catcherror("Could not parse message to temp directory '{$this->tmpdir}' -
	        Check the directory exists with permissions to write");

	    if (!$this->parser->parse_open($this->path)) {
	    	$this->txt = array_pop($this->parser->errors);
	    	return;
		}

		if ($this->rawemail)
		{
			$this->rawbody = $this->parser->stringify();
			$this->rawbody = str_replace("\r/", "\r\n", $this->rawbody);
			return;
		}

		if ($this->head)
		{
		    $this->headers = $this->parser->stringify_header();
		    $this->headers = str_replace(array('<', '>'), array('&lt;', '&gt;'), $this->headers);
		}

		$this->Charset = $this->parser->get_charset();

	    // Get any mail headers
	    $this->from = $this->parser->get_header_field('From');
	    $this->replyto = $this->parser->get_header_field('Reply-To');

		if (preg_match('/(.*?)<.*?>/', $this->from, $match))
	    	$this->username = $match[1];

		// Default to our previous subject in msg, if it does not exist. e.g , reading
	    // a msg with multiple attachments
	    $this->subject = $this->parser->get_header_field('Subject');

	    if ($this->subject == '')
	    	$this->subject = 'No Subject';

	    $this->cc = $this->parser->get_header_field('CC');
	    $this->bcc = $this->parser->get_header_field('BCC');
	    $this->to = $this->parser->get_header_field('To');

	    $this->VideoMail = $this->quote_header($this->parser->get_header_field('X-VideoMail'));
		if ($this->VideoMail)
			$this->VideoMail .= "/mini" ;

	    // Quote any ISO headers
		foreach (array('from', 'replyto', 'subject', 'to', 'cc', 'bcc') as $field)
		{
			// Store the encoding of the email-message
			if (preg_match('/\s*=\?([^\?]+)\?([QqBb])/', $this->$field, $match))
			{
				$this->Encoding = $match[1];
				if (strtoupper($match[2]) == 'Q') {
				    $this->$field = preg_replace('/\s*=\?([^\?]+)\?[Qq]\?([^\?]+)?\?=/e', "\$this->mail->decode_language('\\1', GetMail::decode_mime_head('\\1', stripslashes('\\2')))", $this->$field);
				} else {
				    $this->$field = preg_replace('/\s*=\?([^\?]+)\?[Bb]\?([^\?]+)?\?=/e', "\$this->mail->decode_language('\\1', base64_decode('\\2'))", $this->$field);
				}
			} else {
				$this->$field = $this->mail->decode_language($this->Charset, $this->$field);
			}
		}

	    $this->date = $this->parser->get_header_field('Date');

		$date = $this->date ? $this->date : 'today';

		$date = $this->mail->calc_timezone($date);
		$time = strtotime($date);
        if ($this->Language == "japanese") {
            setlocale(LC_TIME, 'ja_JP.UTF-8', 'en_US');
            $this->date = strftime("{$this->mail->DateFormat} %a {$this->mail->TimeFormat}", $time);
        } else {
            setlocale(LC_TIME, strtolower($this->Language), 'en_US');
            $this->date = strftime("%a " . $this->mail->DateFormat . " " . $this->mail->TimeFormat, $time);
            $this->date = iconv('iso-8859-1', "UTF-8", $this->date);
        }

	    // Take away the timezone and seconds
	    $this->date = preg_replace('/:\d\d \+?-?\d{4}.*/', '', $this->date);
	    $this->ctype = $this->parser->get_header_field('Content-Type');
	    list($this->mimetype) = explode(';', $this->ctype);
		$this->mimetype = strtolower(trim($this->mimetype));

	    if ($this->mimetype == 'text/html')
			$this->type = 'HTML Msg';
	    elseif ($this->mimetype == 'multipart/alternative')
	    	$this->type = 'Embeded HTML/Text';
		elseif (strpos($this->mimetype, 'multipart') !== false)
	    	$this->type = 'Attachments';
		else {
	   		$this->type = 'Text';
		}

		// If we are using the maildir format, the message-id number if the unique id
		if ($this->mail->Type == 'file' && $domains[$this->Pop3host])
		{
			$this->UIDL = $id;
			$this->UIDL = preg_replace('/cur\/|new\//', '', $this->UIDL);
		}
		// If a POP3 or IMAP message, make a UIDL unique from the header ( Used for the email cache )
		else
		{
			if (!$this->UIDL = $this->parser->get_header_field('x-uidl'))
				$this->UIDL =  $this->parser->get_header_field('message-id');

			// Make the UIDL header from the Subject/Date if the Message-ID or XUIDL does not exist
			if (!$this->UIDL)
			{
				$this->UIDL = md5($this->subject.$this->parser->get_header_field('date'));
			}
		}

		// Take away illegal characters from the UIDL
	    $this->UIDL = str_replace("'", '"', $this->UIDL);
	    $this->UIDL = str_replace('"', '', $this->UIDL);
		$this->UIDL = preg_replace('/:.*/', '', $this->UIDL);

	    $this->UIDL = str_replace(array("\n", "\r", ' ', ':', '+', '<', '>', '*', '|', '\\', '/', '&gt;', '&lt;'), '', $this->UIDL);

	    if ($this->Type == 'pop3' || $this->Type == 'imap')
			$this->EmailCache = $this->UIDL;

	    // Take away any newlines from the UIDL
	    $this->UIDL = trim($this->UIDL);

	    // Set the email priority as Normal, otherwise find the value in the header(s)
	    $this->priority = 'Normal';

	    if (substr($this->parser->get_header_field('x-priority'), 0, 1) == 1
	      || $this->parser->get_header_field('X-MSMail-Priority') == 'High'
	      || $this->parser->get_header_field('Importance') == 'High')
	      	$this->priority = 'High';

		if (substr($this->parser->get_header_field('x-priority'), 0, 1) == 5
	      || $this->parser->get_header_field('X-MSMail-Priority') == 'Low'
	      || $this->parser->get_header_field('Importance') == 'Low')
	      	$this->priority = 'Low';

	    if (preg_match("/$this->emailexp/", $this->from, $match))
	    	$this->emailfrom = $match[1];

	    $this->emailfrom = str_replace(array('&gt;', '&lt;'), '', $this->emailfrom);

	    // Cleaup the email, take away " signs, which close the HTML input tag
	    //$this->to = preg_replace('/"(.*?),(.*?)"/', '$1 $2', $this->to);
	    //$this->cc = preg_replace('/"(.*?),(.*?)"/', '$1 $2', $this->cc);

	    //$this->to = str_replace('"', "'", $this->to);
	    //$this->cc = str_replace('"', "'", $this->cc);
	    //$this->bcc = str_replace('"', "'", $this->bcc);

	    //$this->ctype = $this->parser->get_header_field('Content-Type');

		// See if we are permitted to display images in messages
		if(isset($atmail))	{
    		$this->DisplayImages = $atmail->load_displayimages();
    		if ($atmail->DisplayImages == '2')
    			$atmail->DisplayImages = $atmail->load_abook_emails($this->emailfrom);
		}

	    $this->dump_entity();

	    if (isset($this->multiparttxt) && !empty($this->multiparttxt)) {
            $this->multiparttxt = $atmail->escape_jscript($this->multiparttxt);
        }

	    if (isset($this->html) && !empty($this->html)) {
	        $this->html = $atmail->escape_jscript($this->html);
	        if (isset($this->multiparttxt)) $this->html .= $this->multiparttxt;
	    }

	    if (isset($this->txt) && !empty($this->txt)) {
            $this->txt = $atmail->escape_jscript($this->txt);
	        if (isset($this->multiparttxt)) $this->txt .= $this->multiparttxt;
        }

		$this->scan_inline();

		// Fix an error where certain messages cannot be displayed ( e.g Apple mailers as multipart msgs )
		if (!$this->html && !$this->txt)
			$this->txt = $this->multiparttxt;
	}

	function dump_entity($parts=null)
	{
        global $atmail, $pref;

        if (is_null($parts))
	        $parts = $this->parser->get_parts();

        $count = 0;
	    if (!is_array($parts))
	    	settype($parts, 'array');

	    foreach ($parts as $part)
	    {
            // Get MIME type, and display accordingly...
	        list($type, $subtype) = $part->get_mime_ctypes();
            $type = strtolower($type);
            $subtype = strtolower($subtype);

	        $body = $part->get_body();

	        $body = explode("\n", $body);

	 		// Break up long lines in HTML emails without
	 		// breaking any tags or HTML entities

	 		if ($subtype == 'html') {
	 			$tmpBody = array();
	 			$buff = '';

	 			foreach ($body as $line) {
	 			    // strip unnecessary whitespace
                    $line = preg_replace("/\s+/", ' ', $line);

	 				$lineSize = strlen($line);
					$last = 0;
					$count = 0;

					if ($lineSize > 500) {

		 				while ($last < $lineSize) {

		 					$pos = strpos($line, '>', $last);

		 					if ($pos !== false && $pos >= $count + 500) {
		 						$tmpBody[] = $buff . substr($line, $last, $pos - $last + 1);
		 						$last = $pos + 1;
		 						$buff = '';
		 						$count = $pos;
		 					} elseif ($pos === false) {
		 						$tmpBody[] = $buff . substr($line, $last);
		 						$buff = '';
		 						break;
		 					} else {
		 						$buff .= substr($line, $last, $pos - $last + 1);
		 						$last = $pos + 1;
		 					}
						}

					} else {
						$tmpBody[] = $buff . $line;
						$buff = '';
	 				}
	 			}
	 			$body = $tmpBody;
	 		}

			// end break up long HTML lines

			$this->Charset = $part->get_charset();

			// Detect messages forwarded as attachments
	        if (strpos(strtolower($part->content_type), 'message/rfc822') !== false)
	        {
                $this->attachedemails[] = $part->get_path();
                $this->type = "Forwarded Message Attached";

			}
			// Ignore multipart/alternative messages that contain a text attachment of the same message ( decoded from HTML )
			elseif ((($type == 'text' || $type == 'message') && $part->parent_mime_type() != 'multipart/alternative'
			     && ($subtype == 'plain' || preg_match('/text\/(v|i)?calendar/i', $this->mimetype)) && !$part->is_attachment())
			     || !$this->mimetype || $this->mimetype == 'multipart/report')
			{
	        	foreach ($body as $line)
	        	{
					$line = $this->mail->decode_language($this->Charset, $line);
	                //$line = $atmail->escape_jscript($line);   // Check jscript attacks
					$line = $this->scanpgp($line);
					$line = htmlentities($line, ENT_COMPAT, 'UTF-8');
	                $line = $this->quoteurl($line);

					// Optional filter for censored words
					//$line = GetMail::filterwords($line);

					// Check from HTML tags - if exist, don't <BR> \n characters
	                $line = preg_replace('/^(&gt;.*)/', '<font color="#666666">$1</font>', $line);
	                $line = preg_replace('/^(>.*)/', '<font color="#666666">$1</font>', $line);

	                // And finally print it
	                $line = "$line<br>\n";
	                $this->multiparttxt .= $line;
            	}
			}

			// Optionally ignore MS-TNEF attachments ( Binary file. Can only be parsed if tnef program exists)
			elseif ($subtype == "ms-tnef" && $pref['decode_tnef'])
			{
				// Use the tnef program to decode the attachment
				$myfiles = array();
				$mypath = escapeshellarg($part->get_path());
				exec("{$pref['tnef_path']} -t $mypath", $myfiles);
				$save = escapeshellarg($this->tmpdir);
				system("{$pref['tnef_path']} --overwrite -C $save -f " . $part->get_path());

				if (is_array($myfiles)) {
					foreach ($myfiles as $f) {
						$path = "$this->tmpdir/$f";

			            $size = (file_exists($path)) ? filesize($path) : '???';
			            $size = $size / 1024;
			            $size = preg_replace('/(\.\d)\d+/', '$1', $size);

						$filename = str_replace(array('/', '\\'), '', $f);

			            // Escape the filename, just in case it contains special characters
			            $encfilename = rawurlencode($filename);

			            $this->attachname[$filename]['type'] = "file";
			            $this->attachname[$filename]['size'] = $size;
			            $this->attachname[$filename]['path'] = $path;
			            $this->attachname[$filename]['rawname'] = $encfilename;							 // Link to the message on disk
			            $this->attachname[$filename]['name'] = $this->mail->quote_header($filename); // Display the unencoded name
					}
				}

				$this->type = "Attachments/Decoded MS-TNEF";
			}
			// Detect other instances of a message. If a text or html message is already defined, skip
			//elseif(($type == 'text' || $type == 'message') && $this->txt && !$this->html && $subtype == 'html' && strpos($part->parent_ctype, 'alternative') !== false ||
	        //($type == 'text' || $type == 'message') && !$this->html && !$this->txt)
	        elseif (($type == 'text' || $type == 'message') && !$part->is_attachment()
	        && (($part->parent_mime_type() == 'multipart/alternative')
	        || ($part->parent_mime_type() != 'multipart/alternative' && !in_array($part->parent_ctype, $this->processedSections))
	        || $this->attachedemail))
	        {
                if (!$this->html && $subtype == 'html')
                {
                    $this->txt = '';

                	// HTML email msg
                    foreach ($body as $line)
                    {
						$line = $this->mail->decode_language($this->Charset, $line);

						// Verfiy this is still required
                        $line = preg_replace('/<br>?\n?&gt;/i', '<br><b>&gt;</b>', $line);

						// Open targets in a new window always
						if(preg_match('/href=(["\'])mailto:/i', $line))
                        $line = preg_replace('/href=(["\'])mailto:(.*?)["\']/i', 'href='.'$1'.'javascript:top.opencompose(\'$2\',\'\',\'\',\'\')$1/', $line);
						else if(preg_match('/<a href=/i', $line) && !preg_match('/<a .*?target=["\']?/i', $line))
						$line = preg_replace('/<a href=["\']?(.*?)["\']/i', '<a href="$1" target="_blank"', $line);

						// Detect MSword double quotes
						$line = str_replace(array('R20;', 'R21'), '"', $line);

						// Detect MSword single quotes
						$line = str_replace(array('R16;', 'R17'), "'", $line);

						// Detect MSword ellipsis
						$line = str_replace('R30;', '...', $line);

						$line = $this->_clean_tags($line);

						//$line = $atmail->escape_jscript($line);
						//$line = $this->scanpgp($line);
	                    $this->html .= $line . "\n";
	                }
	            }

                elseif (!$this->txt && $subtype == 'plain')
                {
                    foreach ($body as $line)
                    {
						$line = $this->mail->decode_language($this->Charset, $line);
						//if(isset($atmail))
                        //$line = $atmail->escape_jscript($line);   // Check jscript attacks

						//$line = $this->scanpgp($line);
						$line = htmlentities($line, ENT_COMPAT, 'UTF-8');
                        $line = $this->quoteurl($line);

						// Check from HTML tags - if exist, don't <BR> \n characters
		                $line = preg_replace('/^(&gt;.*)/', '<font color="red">$1</font>', $line);
		                $line = preg_replace('/^(>.*)/', '<font color="red">$1</font>', $line);

                        // And finally print it
                        $this->txt .= "$line<br>";
	                }
	            }
	        }

			// A CID image. Display the image embeded into the email-message
	        elseif ($part->is_image() && $part->get_contentid() && $subtype != 'tiff')
	        {
				$path = $part->get_path();
				$cid = $part->get_contentid();
				$cid = str_replace(array('<', '>'), '', $cid);

	            $filename = $part->get_filename();

				$filename = str_replace(array('/', '\\'), '', $filename);

	            $size = (file_exists($path)) ? filesize($path) : '???';
	            $size = $size / 1024;
	            $size = preg_replace('/(\.\d)\d+/', '$1', $size);

	            $filename = rawurlencode($filename);
				$this->inline[$cid] = "mime.php?file=$filename&cid=$cid";
				$this->inlinepics = 1;

	            $this->attachname[$filename]['inline'] = "1";
                $this->attachname[$filename]['type'] = "pic";
	            $this->attachname[$filename]['size'] = $size;
	            $this->attachname[$filename]['path'] = $path;
	            $this->attachname[$filename]['rawname'] = $filename;
	            $this->attachname[$filename]['name'] = $this->mail->quote_header($part->get_filename());
	            $this->attachname[$filename]['cid']  = $cid;
	            $this->attachname[$filename]['desc'] = $part->get_header_field("Content-Description");

	            # Save the name correctly, when forwarding
	            #$self->{attachname}{$filename}{mime} = $this->mail->quote_header($filename); $entity->head->mime_attr('content-type.name') );
	            #$self->{attachname}{$filename}{desc} = Atmail::GetMail->quote_header($entity->head->get("Content-Description"));
				$this->attachname[$filename]['desc'] = $part->get_header_field("Content-Description");

			}
			elseif ($type == 'image' && preg_match('/^(gif|jpeg|jpg|png)$/', $subtype))
			{
	            $path = $part->get_path();

	            $filename = $part->get_filename();

				$filename = str_replace(array('/', '\\'), '', $filename);

	            $size = (file_exists($path)) ? filesize($path) : '???';
	            $size = $size / 1024;
	            $size = preg_replace('/(\.\d)\d+/', '$1', $size);

	            // Escape the filename, just in case it contains special characters
	            $filename = rawurlencode($filename);

	            $this->attachname[$filename]['type'] = "pic";
	            $this->attachname[$filename]['size'] = $size;
	            $this->attachname[$filename]['path'] = $path;
	            $this->attachname[$filename]['rawname'] = $filename;									 // Link to the message on disk
	            $this->attachname[$filename]['name'] = $this->mail->quote_header($part->get_filename()); // Display the unencoded name
	            $this->attachname[$filename]['mime'] = $part->get_mime_type();
		        $this->attachname[$filename]['desc'] = $part->get_header_field("Content-Description");
	        }
            else
            {
			    $path = $part->get_path();

	            $size = (file_exists($path)) ? filesize($path) : '???';
	            $size = $size / 1024;
	            $size = preg_replace('/(\.\d)\d+/', '$1', $size);

	            $myname = basename($path);

	            if ($myname)
	            	$filename = $myname;
	            else
	            	$filename = $part->get_filename();

				$filename = str_replace(array('/', '\\'), '', $filename);

	            // Escape the filename, just in case it contains special characters
	            $encfilename = rawurlencode($filename);

	            $this->attachname[$filename]['type'] = "file";
	            $this->attachname[$filename]['size'] = $size;
	            $this->attachname[$filename]['path'] = $path;
	            $this->attachname[$filename]['rawname'] = $encfilename;							 // Link to the message on disk
	            $this->attachname[$filename]['name'] = $this->mail->quote_header($filename); // Display the unencoded name
	            $this->attachname[$filename]['mime'] = $part->get_mime_type();
				$this->attachname[$filename]['desc'] = $part->get_header_field("Content-Description");
            }

            $this->processedSections[] = $part->parent_ctype;
	    }
	}

	// Quote URL strings and parse to parse.php?func=refer to avoid $_SERVER['HTTP_REFERER'] hack
	// attempts! Read Online Help Center for more info ...
	function quoteurl($theline)
	{
		$type = $this->LoginType;

	    if (preg_match('/(\w+:\/\/.*[a-z-0-9\/])/i', $theline, $match))
	    {
	        $url = $match[1];

	        // Check we don't contain any spaces, if so, grab the URL
	        if (preg_match('/(.*?) /', $url, $match))
	        	$url = $match[1];

	        $newurl = urlencode($url);

	        // Escape ? chars since they affect the regexp
	        //$url = str_replace(array('?', ')', '('), array('\?', '\)', '\('), $url);

	        $exp = "<a target=\"_blank\" href=\"parse.php?redirect=$newurl\"><font color='red'>$url</font></a>";

	        $theline = str_replace($url, $exp, $theline);
	    }

	    elseif (preg_match('/(www\.[a-z\-0-9\/~._,\#=;\?&]+\.[a-z\-0-9\/_]+)/i', $theline, $match))
	    {
	        $url = $match[1];
	        $newurl = urlencode($url);

	        // Escape ? chars since they affect the regexp
	        //$url = str_replace('?', '\?', $url);

	        $theline = str_replace($url, "<a target=\"_blank\" href=\"parse.php?redirect=http://$newurl\">$url</a>", $theline);
	        return $theline;
	    }

	    elseif (preg_match('/(ftp:\/\/[a-z\-0-9\/~._,]+[a-z\-0-9\/_])/i', $theline, $match))
	    {
	        $url    = $match[1];
	        $newurl = urlencode($url);
	        $theline = str_replace($url, "<a target=\"_blank\" href=\"parse.php?redirect=$url\"><font color=\"red\">".'$1'.'</font></a>', $theline);
	        return $theline;
	    }

		// Parse emails and search for email-addreses. If found, parse into compose.php!
		if(preg_match('/mailto:.*?"/', $theline))
	    $theline = preg_replace('/mailto:(.*?)"/', "<a href=\"javascript:top.opencompose(".'$1'.",'','','$type')\">'".'$1'."'</a>", $theline);

		// 2nd match attempt
		elseif(preg_match('/mailto:([a-z\-0-9._,]+\@[a-z0-9.\-]+[a-z])/i', $theline))
	    $theline = preg_replace('/mailto:([a-z\-0-9._,]+\@[a-z0-9.\-]+[a-z])/i', "<a href=\"javascript:top.opencompose('".'$1'."','','','$type')\">".'$1'.'</a>', $theline);

		// Last match, anything@domain.com , so long as it's not been escaped with top.opencompose earlier
		elseif(preg_match('/([a-z\-0-9._,]+\@[a-z0-9.\-]+[a-z])/i', $theline) && !preg_match('/top.opencompose/', $theline))
	    $theline = preg_replace('/([a-z\-0-9._,]+\@[a-z0-9.\-]+[a-z])/i', "<a href=\"javascript:top.opencompose('".'$1'."','','','$type')\">".'$1'.'</a>', $theline);

	    return $theline;
	}

	function quote_header($header)
	{
    	$header = str_replace('"', "'", $header);

	    if ($header)
	    {
	        //$header = htmlentities($header);
	        trim($header);
	    }
	    //$header = utf8_encode($header);
        $header = $this->mail->decode_language($this->Charset, $header);
	    return $header;
	}

	// Parse the date
	function date_header($date)
	{
	    $newdate = $this->quote_header($date);

	    $newdate = strtotime($newdate);

	    // Get the hour and minute of current time
	    $hour   = date('G');
	    $minute = date('i');

	    if ( $newdate > ( time() - ( ( $minute * 60 ) + ( $hour * 60 * 60 ) ) ) )
	    {
	        $newdate = strftime( "Today %R", $newdate);
	    }
	    elseif ( $newdate > ( time - ( 60 * 60 * 24 * 7 ) ) )
	    {
	        $newdate = strftime( "%a %R", $newdate);
	    }
	    else
	        $newdate = strftime( "%a %e/%m/%y %R", $newdate);

	    return $newdate;
	}

	/**
	 * Strip from header to 40 characters
	 *
	 * @param string $from
	 * @param int $type
	 * @return $string
	 */

	function from_header($from, $type)
	{
	    if ( $from = preg_replace('/=\?iso-8859-1\?[qq]\?/', '', $from))
	    {
	        $from = str_replace('?=', '', $from);
	        $from = preg_replace('/=(..)/e', "chr(hexdec('\\1'))", $from);
	    }

	    $from = str_replace(array('<', '>'), array('(', ')'), $from);

	    if (!$from)
	    	$from = "Unknown";

	    if ( strlen($from) > 40 )
	    {
	        if (!$type)
	        {
	            $from = substr($from, 0, 40);
	            $from .= "...";
	        }
	    }

	    return $from;
	}

	function parseheader($header)
	{
	    // Quote the ISO language headers
	    if ( $header = preg_replace('/=\?iso-8859-1\?[qq]\?/', '', $header))
	    {
	        $header = str_replace('?=', '', $header);
	        $header = preg_replace('/=(..)/e', "chr(hexdec('\\1'))", $header);
	    }

	    $header = str_replace(array('<', '>', '"'), array('', '', "'"), $header);

	    return $header;
	}

	function write_uidl()
	{
	}

	function myescape($toencode)
	{
	    if (empty($toencode))
	    	return null;

	    //$toencode = urlencode($toencode);
	    //preg_replace('/([^a-zA-Z0-9_.-]+)/', strtoupper(sprintf("%%%02x",ord('$1'))), $toencode);
	    $toencode = str_replace(array("'",'%'),array('%27',"'"),urlencode($toencode));
		$toencode = str_replace(array("'28","'29"),array("%28","%29"), $toencode);

		$toencode = str_replace(array("'5B","'5D"),array("%5B","%5D"), $toencode);
		$toencode = str_replace(array("'7B","'7D"),array("%7B","%7D"), $toencode);
		$toencode = str_replace("'24", "%24", $toencode);
		$toencode = str_replace("'25", "%25", $toencode);

	    return $toencode;
	}

	function myunescape($todecode)
	{
	    if (empty($todecode))
	    	return null;
	   // $todecode = str_replace('+', ' ', $todecode);
	   // $todecode = preg_replace('/%([0-9a-fA-f]{2})/e', "chr(hexdec('\\1'))", $todecode);
	      $todecode = urldecode(strtr($todecode,"'",'%'));

	    return $todecode;
	}

	// Scan for inline messages
	function scan_inline()
	{
		foreach ($this->inline as $k => $v)
		{
			if (strpos($this->html, "cid:$k") !== false) {
			    $this->html = str_replace("cid:$k", $v, $this->html);
			} else {
			    // cid reference did not exist in the HTML
			    // so we should display the image as an attachment
			    foreach ($this->attachname as $k2 => $v2) {
			        if ($v2['cid'] == $k) {
			            $this->attachname[$k2]['inline'] = false;
			        }
			    }
			}
		}

		$this->html = preg_replace('/js:opencompose/i', 'javascript:opencompose', $this->html);
	}

	function decryptmsg($emailmsg, $password)
	{
		global $pref, $atmail;

		if (strpos($emailmsg, '-----BEGIN PGP MESSAGE-----') === false)
			return;

		require_once('PGP.php');
		$userWrkDir = ($this->mail->MailDir) ? $this->mail->MailDir : $atmail->tmpdir;
		$ownFile = $atmail->tmpdir . ".ht.$this->SessionID";
		$pgp = new PGP(array('wrkDir' => "$userWrkDir/pgp", 'ownFile' => $ownFile));

		if (empty($pgp->ErrorMsg))
		{
			if (!$pgp->Word) //if not cached
				$pgp->Word = $password; //take it from user

			if (empty($pgp->Word))
				return $emailmsg;

			//try to decrypt with the password
			$emailmsg = $pgp->decrypt($emailmsg);

			if ($pgp->is_error())
				return $pgp->ErrorMsg . $emailmsg;
		}

		else
			$emailmsg = $pgp->ErrorMsg . $emailmsg;

		return $emailmsg;
	}

	function scanpgp($line)
	{
		if ($this->MailEncryptDone)
			return $line;

		// The message contains a PGP encryption block
		if (strpos($line, '-----BEGIN PGP MESSAGE-----') !== false)
			$this->Encrypt = 1;

		// The message contains a users PGP public key
		elseif(strpos($line, '-----END PGP PUBLIC KEY BLOCK-----') !== false)
		{
			$line = str_replace(array('<BR>', '<br>'), '', $line);
			$this->MailEncryptPGP .= $line;

			$this->MailEncrypt .= "<textarea name=\"UserPgpKey\" style=\"display:none;\">$this->MailEncryptPGP</textarea></form>";

			$this->MailEncryptDone = 1;
			$line = "";
		}

		elseif ($this->MailEncrypt)
		{
			$line = str_replace(array('<BR>', '<br>'), '', $line);
            $line = trim($line);
			$this->MailEncryptPGP .= "$line\n";
			$line = "";
		}

		elseif (strpos($line, '-----BEGIN PGP PUBLIC KEY BLOCK-----') !== false)
		{
			$from = $this->from;
			$from = preg_replace('/.*(&lt;|<)/', '', $from);
			$from = str_replace(array('&gt;', '>'), '', $from);

			$this->MailEncrypt = <<<_EOF
<script language="Javascript">
function addpgp(){

	if ( parent.emailwin )	{
		document.abook.target='emailwin';
		document.abook.submit();

	}else{

		document.abook.target='emailwin';
		document.abook.submit();
	}

}

</script>
<form method="POST" name="abook" action="abook.php">
<input type="hidden" name="func" value="open">
<input type="hidden" name="updatepgp" value="1">
<input type="hidden" name="UserEmail" value="$from">
<table width="290" border="0" cellspacing="0" cellpadding="0">
<tr><td width="32">
<img src="imgs/xp/decrypt-icon.gif">
</td><td width="259">&nbsp;<a href="javascript:addpgp()"><font class='sw'>Add users PGP key to address-book</font></a></td></tr></table>
_EOF;

			$line = str_replace(array('<BR>', '<br>'), '', $line);
			$line = trim($line);
            $this->MailEncryptPGP .= "$line\n";
			$line = "";
		}

		return $line;
	}

	function _clean_tags($line)
	{
		global $atmail;
        // Break the regex into parts and simplify pattern - seems that preg_replace
        // cannot handle matching this pattern in a large string in one go
        // the problem may just be with multi-byte charsets
        /*$line = preg_replace("/<.+?(href|src)\s*=\s*('|\")?\s*(?!http:\/\/|http:\/\/{$_SERVER['SERVER_ADDR']}|http:\/\/{$_SERVER['SERVER_NAME']}).+?(    abook.php|atmail.php|cal.php|checkmail.php|compose.php|lang.php|ldap.php|mime.php|parse.php|printcal.php|printday.php|reademail.php|search.php|sendmail.php|sendsms.php|showmail.php|sms.php|spell.php|sync.php|task.php|util.php|videomail.php|xhtml.php).*?('|\")?.*?>/i", '*Possibly malicious HTML tag removed* ', $line);*/

        /* the following works - though will require the mbstring functions enabled/compiled on the system
        $line = mb_ereg_replace("/<.+?(href|src)\s*=\s*('|\")?\s*(?!http:\/\/|http:\/\/{$_SERVER['SERVER_ADDR']}|http:\/\/{$_SERVER['SERVER_NAME']}).+?(    abook.php|atmail.php|cal.php|checkmail.php|compose.php|lang.php|ldap.php|mime.php|parse.php|printcal.php|printday.php|reademail.php|search.php|sendmail.php|sendsms.php|showmail.php|sms.php|spell.php|sync.php|task.php|util.php|videomail.php|xhtml.php).*?('|\")?.*?>/i", '*Possibly malicious HTML tag removed* ', $line);
        */

        $line = preg_replace("/(href|src)\s*=.*?(abook\.php|atmail\.php|cal\.php|checkmail\.php|compose\.php|lang\.php)/i", '', $line);
	    $line = preg_replace("/(href|src)\s*=.*?(ldap\.php|mime\.php|parse\.php|printcal\.php|printday\.php|reademail\.php)/i", '', $line);
    	$line = preg_replace("/(href|src)\s*=.*?(search\.php|sendmail\.php|sendsms\.php|showmail\.php|sms\.php)/i", '', $line);
    	$line = preg_replace("/(href|src)\s*=.*?(spell\.php|sync\.php|task\.php|util\.php|videomail\.php|xhtml\.php)/i", '', $line);

    	//Remove any CDATA sections, they break AJAX interface
    	if ($atmail->Ajax)
    		$line = str_replace(array('<![CDATA[', ']]>'), '', $line);

	   return $line;
	}

	function strip_style($msg)
	{
	    //return $msg;
	    // strip <style> tags
	    return preg_replace('/<style(\s+.*?)?>.*?<\/style>/im', '', $msg);
    }

	function decode_htmlspecialchars($field)
	{

	// PHP 4 compat.
	if (!function_exists("htmlspecialchars_decode")) {
		return strtr($field, array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT)));
	}

	return htmlspecialchars_decode($field);
    }

	function clean_html_to_text($msg=null)	{

		if(!$msg)
		$msg = &$this->html;

		$msg = $this->strip_style($msg);

        // strip out leading whitespace
        $msg = preg_replace('/^ +/m', '', $msg);

		// Intelligently convert HTML to text
        require_once('class.html2text.inc');
        $html2text = new html2text($msg);
        $msg = $html2text->get_text();

        $msg = ltrim($msg);

        $lines = preg_split("/\r|\n/", $msg);
		$msg = '> ';
		foreach ($lines as $line)
		{
			$line = ltrim($line);
			if (strlen($line) > 75)
				$msg .= wordwrap($line, 75, "\n"."> ", 1);
			else
				$msg .= "$line\n> ";
		}

		return $msg;
	}

	function cleanEmailAddress($address)
	{
    	$address = trim($address);

        // add quotes around personal parts
        $address = preg_replace('/(^|,|;)\s*([^"\']{1}[^@]+?[^"\',;]{1})(?!Group)\s+([^\s]+?@[a-z0-9.\-]+)/i', '$1"$2" $3', $address);

        // add angle brackets around address parts
        $address = preg_replace('/(?<!"<\')([^\s]+?@[a-z0-9.\-]+)(?!>|"|\')(,|;|$)/i', '<$1>$2', $address);

        // Convert single quotes to double quotes
        $address = preg_replace("/'([^<]+?)' </", '"$1" <', $address);

        return $address;
	}

} // end ReagMsg
?>
