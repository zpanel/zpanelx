<?php
// +----------------------------------------------------------------+
// | Generic_Mail.php  													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: Febuary 2005												|
// +----------------------------------------------------------------+
require_once('header.php');

//error constants
define('GM_NO_POP3_SUPPORT', 'POP3 does not support this feature');
define('GM_FILE_OPEN_WRITE_ERROR', 'File could not be opened for writing');
define('GM_BAD_COMMAND', 'Command sent to IMAP/POP3 server was bad or failed');

//misc constants
define('LIST_SUBSCRIBED_ONLY', 0);
define('LIST_ALL', 1);

/**
 * A generic mailer interface for @Mail's mail functions.
 *
 * Uses PHP's imap_ functions if they are available
 * otherwise uses the PEAR Net_IMAP or Net_POP3
 * classes.
 *
 * @package Generic_Mail
 * @author Brad Kowalczyk <brad@ibiscode.com>
 * @copyright CalaCode.com 2004
 */
class Generic_Mail
{

	/**
	 * Do we use the native PHP imap_ functions?
     * @var bool
     * @access private
	 */
	var $use_native;

	/**
	 * The Net_IMAP or Net_POP3 object or PHP resourse as returned from imap_open()
     * @var mixed
     * @access private
	 */
	var $mailer;

	/**
	 * What type of conection, IMAP or POP3?
     * @var string
     * @access private
	 */
	var $protocol;

	/**
	 * The hostname to connect to
	 * @var string
	 * @access private
	 */
	var $host;

	/**
	 * The port to connect to
     * @var int
     * @access private
     */
	var $port;

	/**
	 * Timeout for connection in seconds
	 * @var int
	 * @access private
	 */
	var $timeout;

	/**
	 * array of errors that have occured
	 * @var array
	 * @access private
	 */
	var $errors = array();

	/**
	 * Current mailbox/folder
	 * @var string
	 * @access private
	 */
	var $cur_mailbox;


	/**
	 * Command id
	 * @var int;
	 * @access private
	 */
	var $cid;


	/**
	 *  Use SSL for connection
	 *  @var bool
	 *  @access private
	 */
	var $use_SSL;

	/**
	 * Debugging flag
	 * @var int
	 */
    var $debug;

	/**
	 * Filename to write debugging to (leave empty for no logging)
     * @var string
     */
	var $debugLogFile;


	/**
	 * File handle for debug log
	 * @var resource
	 */
	var $debugLogFileHandle;



	var $num = 0;



	/**
	 * Constructor
	 *
	 * @param string $host host to connect to
	 * @param string $protocol protocol to use (POP3/IMAP)
	 * @param int $timeout timout
	 * @param bool $SSL should we use SSL?
	 * @return void
	 * @access public
	 */
	function Generic_Mail($host, $protocol, $timeout=60, $SSL=false)
	{
		global $pref, $atmail;

		$this->use_SSL = $SSL;
		$this->errors = array();
		$protocol = strtoupper($protocol);
		$this->debug = $pref['popimap_debug'];
		$this->debugLogFile = $pref['popimap_debug_file'];
                $hosts = array($host);
		
                $args = array();
                $atmail->pluginHandler->triggerEvent('preMailServerConnect', $args);
                if (isset($args[0])) {
                    $hosts = $args[0];
                } 

		// Temporarily disable php imap funcs
		if (false)//(function_exists('imap_open') && $pref['imap_functions'] == 'php')
			$this->use_native = true;
		else
		{
			//PHP's imap functions are not available :( so create
			//the appropriate IMAP_Client or Net_POP3 object
			$this->use_native = false;

			if ($protocol == 'POP3')
			{
				require_once('Net/POP3.php');
				$this->mailer = new Net_POP3();

				if ($SSL) {
					$this->port = 995;
					$host = "ssl://$host";
				} else {
					$this->port = 110;
				}
				
				// set debugging output
				$this->mailer->setDebug($this->debug);

				// Catch debug output from Net_POP3
				//if ($this->debug)
				//	$conn = $this->catchDebugging('connect', array($host));
				//else
				ob_start();
				foreach ($hosts as $host) {
					if($conn = $this->mailer->connect($host, $this->port))
						break;
				}
					
				ob_end_clean();
	
				if (!$conn)
					$this->errors[] = "-ERR Connection to POP3 server failed";

			}
			elseif ($protocol == 'IMAP')
			{
				require_once('IMAP_Client.php');
				$this->mailer = new IMAP_Client;

				if ($SSL) {
					$this->port = 993;	
				} else {
					$this->port = 143;
				}
				
				// set debugging output
				$this->mailer->setDebug($pref['popimap_debug'], $pref['popimap_debug_file'], $pref['popimap_debug_file_size_limit']);
				
				$connect = false;
				foreach ($hosts as $host) {
					if ($this->mailer->connect($host, $this->port, $timeout, $SSL)) {
						$connect = true;
						break;
					}
				}

				if (!$connect) {
					$this->errors[] = "-ERR Connection to IMAP server failed";
				}
			}
		}

		$this->protocol = $protocol;
		$this->host = $host;
		$this->timeout = $timeout;
		$this->cid = 1;
	}


    /**
     * Log user into their email POP3 or IMAP account
     *
	 * @param string $user username
	 * @param string $pass password
	 * @return bool true on success, false on failure
	 * @access public
	 */
	function login($user, $pass)
	{
		if ($this->use_native)
		{
			$protocol = strtolower($this->protocol);

			if ($this->use_SSL)
			{
				//$mailbox = '{'.$this->host.':'.$this->port."/$protocol}INBOX/ssl";
				//$this->mailer = imap_open($mailbox, $user, $pass, OP_SECURE);
			}
			else
			{
				$mailbox = '{'.$this->host.':'.$this->port."/$protocol/notls}INBOX";
				$this->mailer = imap_open($mailbox, $user, $pass);
			}

			if ($this->mailer === false)
			{
				$this->errors[] = '-ERR ' . imap_last_error();
				return false;
			}

			$this->cur_mailbox = 'INBOX';
		}

		// IMAP
		elseif ($this->protocol == 'IMAP')
		{
			$login = $this->mailer->login($user, $pass);

			if ($login !== true)
			{
				$this->errors[] = "-ERR Login failed";
				return false;
			}

			$this->select('INBOX');
			$this->cur_mailbox = 'INBOX';
			return true;
		}

		// POP3
		elseif ($this->protocol == 'POP3')
		{
			if ($this->debug)
				$login = $this->catchDebugging('login', array($user, $pass));

			else
				$login = $this->mailer->login($user, $pass);

			if ($login !== true)
			{
				$this->errors[] = '-ERR Login failed';
				return false;
			}

			return true;
		}
	}


    /**
	 * Write debugging to file or browser/STDOUT
	 *
	 */

	function writeDebugging()
	{
		// Get output buffer contents and quit buffering
		$debug = ob_get_contents();
		ob_end_clean();

		// Check if we need to write to file
		if ($this->debugLogFile)
		{
			if (!is_resource($this->debugLogFileHandle))
			{
				$this->debugLogFileHandle = fopen($this->debugLogFile, 'a');

				// Add a header when we first open debug log
				$date = date('r');
				$debug = "\n## Debug output started on $date, server: $this->host ##\n$debug";
			}

			fwrite($this->debugLogFileHandle, $debug);
		}

		// Otherwise just echo
		else
			echo nl2br($debug);
	}


	/**
	 * Call a Net_POP3 method and catch
	 * debug output
	 *
	 * @param string $method Method name to call
	 * @param array  $params Parameters to pass
	 * @return mixed
	 */
	function catchDebugging($method, $params)
	{
		if (!method_exists($this->mailer, $method))
			die("$method is not a method of Net_POP3!");

		if (!is_a($this->mailer, 'Net_POP3'))
			die("you can only call Net_POP3 methods from Generic_Mail::catchDebugging()");

		ob_start();
		$result = call_user_func_array(array(&$this->mailer, $method), $params);
		$this->writeDebugging();

		return $result;
	}


    /**
     * Get a message from the mail server
     *
	 * @param int $num message number
	 * @param bool $as_string whether to return as string
	 *        default is Array
	 * @return mixed array|string
	 * @access public
	 */
	function get($num, $as_string = false, $path = false)
	{
	 	if ($this->use_native)
	 	{
	 		$email = imap_fetchheader($this->mailer, $num) . imap_body($this->mailer, $num);
		}
		elseif ($this->protocol == 'POP3')
		{
			if ($this->debug)
				$email = $this->catchDebugging('getMsg', array($num));
			else
				$email = $this->mailer->getMsg($num);

			if (PEAR::isError($email))
			{
				$this->errors[] = $email->getMessage();
				return false;
			}
		}
	 	elseif ($this->protocol == 'IMAP')
	 	{
	 		$email = $this->mailer->fetchEmailFH($num, $path);
	 	}

		if ($as_string)
			return $email;

		return explode("\n", $email);
	}


	/**
	 * @param int $num message number
	 * @param string $path path to save message to
	 * @return bool
	 * @access public
	 */
	function saveEmail($num, $path)
	{
	 	$msg = $this->get($num, true);

		if ($fh = fopen($path, 'w'))
		{
			fwrite($fh, $msg);
			fclose($fh);
			return true;
		}

		$this->errors[] = GM_FILE_OPEN_ERROR;
		return false;
	}

	function saveEmailFH($num, $path)
	{
	 	$msg = $this->get($num, true, $path);

		if(file_exists($path))
			return true;
		else
			return false;

	}

	function getheaders($id, $folder)
	{
		if ($folder && $folder != $this->cur_mailbox && $this->protocol == 'IMAP')
			$this->select($folder);

		if ($this->use_native)
	 	{
	 		$headers = imap_fetchheader($this->mailer, $id);
		}
		elseif ($this->protocol == 'POP3')
		{
			if ($this->debug)
				$headers = $this->catchDebugging('getRawHeaders', array($id));
			else
				$headers = $this->mailer->getRawHeaders($id);
		}
	 	elseif ($this->protocol == 'IMAP')
			$headers = $this->mailer->fetchRawHeader($id);

		return $headers;
	}

    /**
	 * Get the size of email/s
	 *
	 * @param int $msg message number if requiring size of a particular message
	 * @param string $mailbox Mailbox to select
	 * @return mixed
	 * @access public
	 */
	function listmailsizes($msg=null, $mailbox=null)
	{
	    global $atmail;

		$sizes = array();

		if ($mailbox)
			$this->select($mailbox);

		if ($this->use_native)
	 	{
			//just fetch size of requested msg
			if ($atmail->isset_chk($msg))
				$sequence = $msg;
			else
	        	$sequence = "1:*";

			$overview = imap_fetch_overview($this->mailer, $sequence);

			$uids = '';

			foreach ($overview as $email)
			{
				$sizes[$email->msgno] = $email->size;

				if (!$email->seen)
					$uids .= "$email->uid,";
			}

			imap_clearflag_full($this->mailer, $uids, '\\Seen');

	   		return $sizes;
	 	}

  		//IMAP
		elseif ($this->protocol == 'IMAP')
		{
			$res = $this->mailer->fetchMailSizes($msg);

			if ($res === false)
			{
				//$this->errors[] = "Error in FETCH command";
				return array(); //false;
			}

			return $res;
		}
		//POP3 using PEAR
		elseif ($this->protocol == 'POP3')
		{
			if ($this->debug)
				$res = $this->catchDebugging('_cmdList', array($msg));
			else
				$res = $this->mailer->_cmdList($msg);

			if (is_array($res))
			{
				// Recreate so that the index = message
				// sequence number
				$i = 1;
				foreach ($res as $array)
				{
					$sizes[$i] = $array['size'];
					$i++;
				}

				if ($atmail->isset_chk($msg))
					return $sizes[$msg];
				else
					return $sizes;
			}
			else
			{
				$this->errors[] = "could not get result for LIST";
				return false;
			}
		}

	}


    /**
     * Delete an email
     *
	 * @param $num
	 * @return bool
	 * @access public
	 * @todo implement uid checking before delete
	 */
	function deletemail($num, $uid=null)
	{
        if ($this->use_native)
	 	{
	 	 	$status = imap_delete($this->mailer, $num);
	 	 	if (!$status)
	 	 	{
	 	 	 	$this->errors[] = imap_last_error();
	 	 	 	return false;
			}
	 	 	imap_expunge($this->mailer);
	 	 	return true;
		}
		elseif ($this->protocol == 'IMAP')
		{
		 	$status = $this->mailer->markAsDeleted($num);

			if(!$pref['expunge_logout'])
			$status = $this->mailer->expunge();
		}
		elseif ($this->protocol == 'POP3')
		{
		 	if ($this->debug)
		 		$status = $this->catchDebugging('deleteMsg', array($num));
			else
		 		$status = $this->mailer->deleteMsg($num);

            if (DB::isError($status))
	 	 	{
	 	 	 	$this->errors[] = $status->getMessage();
	 	 	 	return false;
			}
	 	 	return true;
		}
	}


    /**
     * Read the first $num_lines lines from the message
     *
	 * @param $num
	 * @param $num_lines
	 * @return mixed string on success bool false on failure
	 * @access public
	 */
	function top($num, $folder=null, $num_lines=null, $cmd=null)
	{
		// Change folders if we need to
 	 	if ($folder && $folder != $this->cur_folder)
 	 		$this->_changeMailbox($folder);

        if ($this->use_native)
	 	{
            if ($this->protocol == 'POP3')
    			$command = "TOP $num $num_lines";
            else
    			$command = "$this->cid FETCH $num (RFC822.SIZE rfc822.header BODY.PEEK[1]<0.250>)";

			return '';//$this->_send_cmd($command);

		}
		elseif ($this->protocol == 'IMAP')
		{
			$param = "(RFC822.SIZE rfc822.header BODY.PEEK[1]<0.250>)";
			$resp = $this->mailer->top($num);
		}
		elseif ($this->protocol == 'POP3')
		{
		 	if ($this->debug)
				$top = $this->catchDebugging('getRawHeaders', array($num, 10));
			else
		 		$top = $this->mailer->getRawHeaders($num, $num_lines);

		 	return $top;
		}
	}


    /**
	 * @param int $index index of the result array to return
	 * @return
	 * @access public
	 */
	function stat($index)
	{
		// use PHP IMAP functions
        if ($this->use_native)
	 	{
			//$type = strtolower($this->protocol);
			//$mailbox = "\{$this->host:$this->port/$type}$this->cur_mailbox";

			//map the index to the object property
			//$obj_properties = array(0 => 'messages', 1 => 'recent', 2 => 'unseen');
			$obj_properties = array(0 => 'Nmsgs', 1 => 'Recent', 2 => 'Unread');

			// imap_status() aparently faster than imap_mailboxmsginfo() but sets all msgs to read
	 		//$data = imap_status($this->mailer, $mailbox, SA_ALL);

			$data = imap_mailboxmsginfo($this->mailer);

			if (!$data)
				return false;

			return $data->$obj_properties[$index];
		}

		$protocol = strtolower($this->protocol);

		// PHP IMAP funcs not available, use PEAR
		if ($protocol == 'imap')
		{
			switch ($index)
			{
				case 0: $resp = $this->mailer->getNumberOfMessages('TOTAL');break;
				case 1: $resp = $this->mailer->getNumberOfMessages('RECENT');break;
				case 2: $resp = $this->mailer->getNumberOfMessages('UNSEEN');break;
			}

		}

		elseif ($protocol == 'pop3')
		{
			$mailbox = "\{$this->host:$this->port/$protocol}$this->cur_mailbox";

			if ($this->debug)
				$resp = $this->catchDebugging('numMsg', array($mailbox));
			else
				$resp = $this->mailer->numMsg($mailbox);
		}

		if (PEAR::isError($resp))
			die($resp->getMessage(). " $mailbox");

		return $resp;
	}


    /**
     * Select a folder
     *
	 * @param string $folder
	 * @return bool
	 * @access public
	 */
	function select($folder, $force=null)
	{
		if (!$force && strtoupper($folder) == strtoupper($this->cur_mailbox))
			return true;

		if ($this->use_native)
	 	{

			//if (strtoupper($folder) != 'INBOX')
			//	$folder = "INBOX.$folder";

			$protocol = strtolower($this->protocol);
			$mailbox = "\{$this->host:$this->port/$protocol}$folder";

			if ($this->use_SSL)
				$mailbox .= '/ssl';

			if (!imap_reopen($this->mailer, $mailbox))
			{
	 		 	$this->errors[] = imap_last_error();
	 		 	return false;
			}

			$this->cur_mailbox = $folder;
			return true;

		}
		elseif ($this->protocol == 'IMAP')
		{
			//if (strtoupper($folder) != 'INBOX')
			//	$folder = "INBOX.$folder";

		 	$status = $this->mailer->select($folder);

            $this->cur_mailbox = $folder;
			return true;
		}
		elseif ($this->protocol == 'POP3')
		{
            //POP3 does not support folders so just return false
		 	$this->errors[] = GM_NO_POP3_SUPPORT;
		 	return false;
		}
	}


    /**
     * Copy a mail message to another folder
     *
	 * @param int $num
	 * @param string $newfolder
	 * @return
	 * @access public
	 */
	function copymail($num, $newfolder)
	{
		if ($this->protocol == 'POP3')
		{
         	//POP3 does not support folders so just return false
		 	$this->errors[] = GM_NO_POP3_SUPPORT;
		 	return false;
		}

		//if (strtoupper($newfolder) != 'INBOX')
		//		$folder = "INBOX.$newfolder";

     	if ($this->use_native)
	 	{
	 	 	$status = imap_mail_copy($this->mailer, $num, $newfolder);
	 	 	if (!$status)
	 	 	{
	 	 	 	$this->errors[] = imap_last_error();
	 	 	 	return false;
			}
			return true;
		}
		else
		{
            $status = $this->mailer->copyMessages($num, $newfolder);
		 	if ($status !== true)
		 	{
		 	 	$this->errors[] = $status->getMessage();
		 	 	return false;
			}
			return true;
		}

	}


    /**
     * Move a mail message to another folder
     *
	 * @param mixed $num message sequence number/s as int or string of ints
	 * @param string $newfolder name of new folder
	 * @param bool $use_uid Whether or not $num is the UID
	 * @return bool
	 * @access public
	 */
	function movemail($num, $newfolder, $use_uid=false)
	{
     	if ($this->use_native)
	 	{
		//	if (strtoupper($newfolder) != 'INBOX')
		//		$newfolder = "INBOX.$newfolder";

	 	 	$status = imap_mail_move($this->mailer, $num, $newfolder);

	 	 	if (!$status)
	 	 	{

	 	 	 	$this->errors[] = imap_last_error();
				die("could not move mail with id '$num' to '$newfolder'!".$this->lasterror());
	 	 	 	return false;
			}

			imap_expunge($this->mailer);
			return true;
		}
		elseif ($this->protocol == 'IMAP')
		{
		 	return $this->mailer->moveMessages($num, $newfolder, $use_uid);
		}
		elseif ($this->protocol == 'POP3')
		{
		 	//POP3 does not support folders so just return false
		 	$this->errors[] = GM_NO_POP3_SUPPORT;
		 	return false;
		}
	}


    /**
	 *
	 * @return
	 * @access public
	 */
	function prefix($mbox='')
	{
		if (strtoupper($mbox) == 'INBOX')
			return $mbox;

		// Return the full prefix of the mailbox if applicable
		if (!preg_match("/^$this->prefix/", strtoupper($mbox)))
			return $this->prefix.$mbox;
		else
		return $mbox;
	}

	function deliminator()
	{
		return $this->mailer->Deliminator;
	}

    /**
     * Delete a mailbox
     *
	 * @param string $folder
	 * @return bool
	 * @access public
	 */
	function delete_mailbox($folder)
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

        if ($this->use_native)
	 	{
	 		$mailbox = "\{$this->host}INBOX.$folder";
	 		$res = imap_deletemailbox($this->mailer, $mailbox);
	 		if (!$res)
	 		{
	 			$this->errors[] = imap_last_error();
	 			return false;
	 		}
		}
		elseif ($this->protocol == 'IMAP')
		{

			$res = $this->mailer->deleteMailbox($folder);

			if ($res !== true)
			{
				return false;
			}
		}

		return true;
	}


	/**
	 * Delete all emails from a mailbox
	 *
	 * @param strong $folder
	 *
	 */
	function purge_mailbox($folder)
	{
	   if ($this->protocol == 'IMAP') {
	       $this->mailer->purgeMailbox($folder);
	   }
	}


    /**
     * Create a new mailbox
     *
	 * @param string $folder
	 * @param string $subfolder
	 * @return bool
	 * @access public
	 */
	function create_mailbox($folder, $subfolder=null)
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

		// Use PHP imap functions
        if ($this->use_native)
	 	{
			$mailbox = $this->_fixFolderName($subfolder);

			if ($folder)
				$mailbox .= ".$folder";

			$mailbox = "\{$this->host}$mailbox";

	 		$res = imap_createmailbox($this->mailer, $mailbox);

			if (!$res)
	 		{
	 			$this->errors[] = imap_last_error();
	 			return false;
	 		}

			$res = imap_subscribe($this->mailer, $mailbox);

	 		return true;
		}

		// Use IMAP_Client class
		else
		{
			//if ($folder)
			//	$mailbox .= $folder;

			return $this->mailer->createMailbox($folder, $subfolder);
		}
	}


    /**
     * Rename a mailbox
     *
	 * @param string $oldfolder
	 * @param string $newfolder
	 * @return bool
	 * @access public
	 */
	function renamefolder($oldfolder, $newfolder)
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

        if ($this->use_native)
	 	{
	 		if (!imap_renamemailbox($this->mailer, $oldfolder, $newfolder))
	 		{
	 			$this->errors[] = imap_last_error();
	 			return false;
	 		}
		}
		else
		{
			$res = $this->mailer->renameMailbox($oldfolder, $newfolder);
			if ($res !== true)
			{
				$this->errors[] = 'rename failed';
				return false;
			}
		}

	}


    /**
     * Subscribe a mailbox
     *
	 * @param string $subscribe
	 * @return bool
	 * @access public
	 */
	function subscribe($mailbox)
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

        if ($this->use_native)
	 	{
	 		$res = imap_subscribe($this->mailer, $mailbox);
	 		if (!$res)
	 		{
	 			$this->errors[] = imap_last_error();
	 			return false;
	 		}
		}
		else
		{
			$res = $this->mailer->subscribeMailbox($mailbox);
			if ($res !== true)
			{
				//$this->errors[] = $res->getMessage();
				return false;
			}
		}

		return true;
	}


    /**
     * Unsubscribe a mailbox
     *
	 * @param string $unsubscribe
	 * @return bool
	 * @access public
	 */
	function unsubscribe($mailbox)
	{
        if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

        if ($this->use_native)
	 	{
	 		$res = imap_unsubscribe($this->mailer, $mailbox);
	 		if (!$res)
	 		{
	 			$this->errors[] = imap_last_error();
	 			return false;
	 		}
		}
		else
		{
			$res = $this->mailer->unsubscribeMailbox($mailbox);
			if ($res === false)
			{
				$this->errors[] = 'mailbox could not be unsubscribed';
				return false;
			}
		}

		return true;
	}


    /**
	 * @param string $folder
	 * @param string $msg
	 * @return
	 * @access public
	 */
	function append($folder, $msg)
	{
		$folder = $this->_fixFolderName($folder);

        if ($this->protocol == 'POP3')
        {
        	$this->errors[] = GM_NO_POP3_SUPPORT;
        	return false;
        }

		if ($this->use_native)
	 	{
	 		$res = imap_append($this->mailer, $folder, ltrim($msg));
	 		if (!$res)
	 		{
	 			$this->errors[] = imap_last_error();
	 			return false;
	 		}
		}
		else
		{
			$res = $this->mailer->appendMessage(ltrim($msg), $folder);
			if ($res === false)
			{
				return false;
			}
		}

		return $res;
	}


	/**
	 * @param array $args associative array of arguments
	 * @return array
	 * @access public
	 */
	function search($args)
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

		//construct the query
		$query = '';
		foreach ($args as $k => $v)
		{
			if (!preg_match('/SUBJECT|FROM|TO|BODY|BEFORE|SINCE|FLAGGED/', $k))
				continue;

			if ($k == 'FLAGGED') {
			    if (!$v)
                    continue;

                $query .= "$k ";
			} else {
			    if (!$v)
                    continue;
				$query .= "$k \"$v\" ";
			}
		}

		if ($args['EmailAttach'])
			$query .= "HEADER Content-Type mixed";

		if ($this->cur_mailbox != $args['EmailBox'])
			$this->_changeMailbox($args['EmailBox']);

     	if ($this->use_native)
	 		$res = imap_search($this->mailer, $query);

		else
			$res = $this->mailer->search($query);

		if(is_array($res))
		foreach ($res as $k => $id)
			$res[$k] = $args['EmailBox'] . "::$id";

		return $res;
	}


    /**
	 * Get the user's quota
	 *
	 * @return array
	 * @access public
	 */
	function getquota()
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

        if ($this->use_native)
	 	{
	 		$quota = imap_get_quotaroot($this->mailer, 'INBOX');
	 		if ($quota)
	 			return $quota['STORAGE'];
	 		else
	 			return array(0, 0);
		}
		else
		{
			$quota = $this->mailer->getQuota($this->cur_mailbox);
			if (!is_array($quota))
			{
				$quota = array(0, 0);
			}

			return $quota;
		}
	}


    /**
	 * Get number of unread messages and total number of messages
	 *
	 * @return array()
	 * @access public
	 */
	function showunread($folder='')
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

        if ($this->use_native)
	 	{
	 		$info = imap_mailboxmsginfo($this->mailer);
	 		if (!$info)
	 		{
	 			$this->errors[] = imap_last_error();
	 			return false;
	 		}

	 		return array($info->Unread, $info->Nmsgs);
		}
		else
		{
			$msgs = $this->mailer->getNumberOfMessages('UNSEEN,TOTAL', $folder);

			return array($msgs['UNSEEN'], $msgs['MESSAGES']);
		}
	}

    function getUIDList()
    {
        if ($this->protocol == 'IMAP')
            return $this->mailer->getUIDList();
    }
    
    function getUnreadUIDList()
    {
        if ($this->protocol == 'IMAP')
            return $this->mailer->getUnreadUIDList();
    }

	function getMailboxSummary($start=null, $end=null, $mailbox=null, $lines=null)
	{
		global $atmail;

		// Do not show the message body if using ajax or XUL interface, msg popup not supported ( faster w/o it )
		if($atmail->Advanced && $atmail->Ajax != '1' && $atmail->XUL != '1')
			$lines = 15;
		elseif (is_null($lines))
			$lines = 0;

		if ($this->use_native)
		{

		}
		elseif ($this->protocol == 'IMAP')
		{
			return $this->mailer->getMailboxSummary($start, $end, $mailbox, $lines);
		}
		elseif ($this->protocol == 'POP3')
		{
			$summary = array();

			if ($start == 0)
				$start = 1;

			for ($i = $start; $i <= $end; $i++)
			{
				if ($this->debug)
				{
					$headers = $this->catchDebugging('getRawHeaders', array($i, $lines));

					if ($list = $this->catchDebugging('_cmdList', array($i)))
						$size = $list['size'];
					else
						$size = 0;
				}
				else
				{
					$headers = $this->mailer->getRawHeaders($i, $lines);

					if ($list = $this->mailer->_cmdList($i))
						$size = $list['size'];
					else
						$size = 0;
				}

				$summary[] = array('header' => $headers, 'EmailSize' => $size);
			}

			return $summary;
		}
	}


    /**
	 * Has Message been seen already?
	 *
	 * @param $id Message id
	 * @return mixed -1 on error, true on seen, false on unseen
	 * @access public
	 */
	function seen($id)
	{
		if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return -1;
		}

        if ($this->use_native)
	 	{
	 		$info = imap_fetch_overview($this->mailer, $id);
	 		return $info->seen;
		}
		elseif ($this->protocol == 'IMAP')
		{
			return $this->mailer->hasFlag($id, 'Seen');
		}
	}


    /**
     * Gets list of mailboxes from IMAP server
     *
	 * @return array
	 * @access public
	 */
	function mailboxes($folder = null, $type)
	{
	    global $atmail;

		$this->cid++;

	 	if ($this->protocol == 'POP3')
		{
			$this->errors[] = GM_NO_POP3_SUPPORT;
			return false;
		}

	 	if ($folder && $folder != $this->cur_folder)
		 	$this->select($folder);

        if ($this->use_native)
	 	{
	 		if ($type == LIST_SUBSCRIBED_ONLY)
            	$mboxes = imap_lsub($this->mailer, "\{$this->host}", "*");
	 		else
	 			$mboxes = imap_list($this->mailer, "\{$this->host}", "*");

            if (is_array($mboxes))
            {
			  foreach ($mboxes as $k => $v)
			  {
					if (preg_match('/\{.*?\}INBOX\.(.+)/', $v, $m))
						$mboxes[$k] = imap_utf7_encode($m[1]);
					else
						$mboxes[$k] = 'Inbox';
			  }
			}

			return $mboxes;
		}
		else
		{
			if ($atmail->isset_chk($type))
				$mboxes = $this->mailer->listMailboxes($folder, '*', false);
			else
				$mboxes = $this->mailer->listMailboxes($folder, '*', true);

			return $mboxes;
		}
	}


    /**
	 * Returns last error
	 *
	 * @return string
	 * @access public
	 */
	function lasterror()
	{
	    $last_err = '';

	    if (count($this->errors) > 0) {
	        $i = count($this->errors) - 1;
	        $last_err = $this->errors[$i];
	    }

		return $last_err;
	}


    /**
	 *
	 * @return void
	 * @access public
	 */
	function quit()
	{
		if ($this->use_native)
			imap_close($this->mailer, CL_EXPUNGE);

		elseif($this->protocol == 'IMAP')
			$this->mailer->disconnect(true);

		elseif ($this->protocol == 'POP3')
		{
			if ($this->debug)
				$this->catchDebugging('disconnect', array(true));
			else
				$this->mailer->disconnect(true);
		}
	}


	function decode($string)
	{
		$array = imap_mime_header_decode($string);
		$str = '';
		foreach ($array as $key => $part)
			$str .= $part->text;

		return $str;
	}


	/**
	 * Turn debugging on/off
	 *
	 * @param bool $flag
	 * @access public
	 * @return void
	 */
	function debug($flag)
	{
		if ($this->use_native)
			$this->debug = $flag;
		else
			$this->mailer->setDebug($flag, $pref['pop3imap_debug_file']);
	}


	/**
	 * Change current mailbox
	 *
	 * @param string $mailbox
	 * @return void
	 * @access private
	 */
	function _changeMailbox($mailbox)
	{
		if ($this->protocol == 'POP3')
			return;
        $this->select($mailbox);
        return;
		$this->cid++;

		if ($this->use_native)
			imap_reopen($this->mailer, $mailbox);

		else
			$this->mailer->select($mailbox);

		$this->cur_mailbox = $mailbox;
	}


	function _fixFolderName($folderName)
	{
		if (strtoupper($folderName) == 'INBOX')
			return $foldername;

		//$foldername = "INBOX.$foldername";

		return $folderName;
	}

	function imap_last_error()	{
		// Implement error checking, return what the problem is
		return;
	}

    /**
	 *
	 * @return void
	 * @access public
	 */
	function markAsFlag($id, $type, $flag)
	{
		$id = preg_replace('/cur\//', '', $id);

		if($this->protocol == 'IMAP')
			$this->mailer->markAsFlag($id, $type, $flag);

	}

    /**
	 *
	 * @return void
	 * @access public
	 */
	function expunge($force='')
	{

		$this->mailer->expunge($force);

	}

}//end Generic_Mail
?>
