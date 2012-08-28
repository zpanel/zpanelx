<?php
/**
 * IMAP_Client.php
 *
 * IMAP Client class. Can be used instead of PHP's
 * imap functions to communicate with an IMAP server
 *
 * @author Brad Kowalczyk <brad@ibiscode.com>
 * @version 1.0
 * @package IMAP_Client
 */

// Define some error constants
define('IC_NOT_CONNECTED', 'Not connected to IMAP server');
define('IC_NOT_LOGGED_IN', 'Not logged in; cannot perform requested command');
define('IC_LOGIN_DISABLED', 'The server does not allow the LOGIN command');


/**
 * IMAP_Client
 *
 * Basic IMAP client class for talking to an
 * IMAP server
 *
 */
class IMAP_Client
{

	/**
	 * The socket connection
	 * @var Net_Socket
	 */
	var $socket;

	/**
	 * The hierarchy delimiter
	 * @var string
	 */
	var $delimiter;

	/**
	 * The command ID
	 * @var int
	 */
	var $cid = 'ATMAIL00';

	/**
	 * Debug flag
	 * @var bool
	 */
	var $debug = false;

	/**
	 * Are we logged in?
	 * @var bool
	 */
	 var $loggedIn = false;

	 /**
	  * IMAP server's capabilities
	  * @var array
	  */
	 var $capabilities;

	 /**
	  * Are we sing SSL?
	  * @var bool
	  */
	 var $SSL = false;


	/**
	 * Constructor
	 *
	 * @param array $args Associative array (name => value) of
	 * 				      member variables to set
	 */
	function IMAP_Client($args = array())
	{
		require_once('Net/Socket.php');

		foreach ($args as $k => $v)
			$this->$k = $v;

		$this->socket =& new Net_Socket();
		//$this->debug=true;
	}

	/**
	 * Connect to an IMAP server
	 *
	 * @param string $host
	 * @param int $port
	 * @param int $timeout
	 */
	function connect($host = 'localhost', $port = 143, $timeout=30, $ssl=false)
	{
		$this->host = $host;

		if ($ssl) {
			$host = "ssl://$host";
			$this->SSL = true;
		}

		$con = $this->socket->connect($host, $port, null, $timeout);
		if (PEAR::isError($con)) {
			return false;
		}

		$resp = $this->socket->readLine();

		if (substr($resp, 0, 4) != '* OK')
			return false;

		if (preg_match('/\[CAPABILITY (.+?)\]/i', $resp, $m)) {
			$this->capabilities = explode(' ', $m[1]);
		} else {
			$resp = $this->capability();

			if (is_array($resp))
				$this->capabilities = $resp;
		}

		//$this->startTLS();
		return true;
	}

	
	function startTLS()
	{
		$this->sendCMD('STARTTLS');		
	}
	
	function disconnect()
	{
		if (is_a($this->socket, 'Net_Socket'))
			$this->socket->disconnect();
		$this->socket = null;
	}


	/**
	 * Get the IMAP server's capabilities
	 *
	 * @return mixed bool false on error or array
	 *               of capabilities.
	 */
	function capability()
	{
		$resp = $this->sendCmd('CAPABILITY');

		if (!$resp)
			return false;

		list($capa,) = explode("\r\n", $resp);
		preg_match('/\* CAPABILITY (.+)/i', $capa, $m);
		$this->capabilities = explode(' ', $m[1]);

		return $m[1];
	}


	/**
	 * Login to IMAP server
	 *
	 * @param string $user The username
	 * @param string $pass The password
	 * @return bool true on success false on failure
	 */
	function login($user, $pass)
	{
		$this->user = $user;

		// Check that server allows the LOGIN command
		if (in_array('LOGINDISABLED', $this->capabilities))
		{
			$this->lastError = IC_LOGIN_DISABLED;
			return false;
		}

		$pass = str_replace('"', '\"', $pass);
		$resp = $this->sendCmd("LOGIN \"$user\" \"$pass\"");

		if (!$resp)
			return false;

		// Namespace command does this function now
		//$this->_getDelimiter();
		$this->loggedIn = true;

		// Load the folder namespace
		$this->getImapNamespace();

		return true;
	}


	/**
	 * Authenticate with the IMAP server
	 *
	 * @param string $method The authentication method
	 *                       to use.
	 * @return bool true on success false on failure
	 */
	function authenticate($method)
	{
		$method = strtoupper($method);

		// Check that the server supports the requested
		// auth method
		if (!in_array("AUTH=$method", $this->capabilities))
		{
			$this->lastError = IC_AUTH_METHOD_NOT_SUPPORTED;
			return false;
		}

		$func = "_authenticate$method";
		if (method_exists($this, $func))
			return $this->$func;

		return false;
	}


	function authenticateCRAMMD5()
	{
		if (!$resp = $this->sendCmd('AUTHENTICATE CRAM-MD5')) {
			return false;
		}
		
		$challenge = base64_decode($resp);
		file_put_contents('php://stderr', "challenge = $challenge\n");
	}


	/**
	 * Select a mailbox to work on
	 *
	 * @param string $mailbox The mailbox name
	 */
	function select($mailbox, $force='')
	{
		if (!$this->loggedIn)
		{
			$this->lastError = IC_NOT_LOGGED_IN;
			return false;
		}

		$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));

		if (strtolower($this->mailbox) == strtolower($mailbox) && !$force)
	       return true;

	    // Must select with quotes, otherwise folders with a space fail
		$resp = $this->sendCmd("SELECT \"$mailbox\"");

		if (preg_match('/\d+ (NO|BAD) (.+)/', $resp, $m))
		{
			$this->lastError = $m[2];
			return false;
		}

		// Find number of messages in mailbox
		if (preg_match('/\* (\d+) EXISTS/', $resp, $m))
			$total = $m[1];

		// Find number of recent messages in mailbox
		if (preg_match('/\* (\d+) RECENT/', $resp, $m))
			$recent = $m[1];

		// Find out my rights
		if (preg_match('/\* OK \[MYRIGHTS "([a-z]+)"/', $resp, $m))
			$rights = $m[1];

		$this->rights = $rights;
		$this->total = $total;
		$this->recent = $recent;
		$this->mailbox = $mailbox;

		return array('total_msgs' => $total, 'recent_msgs' => $recent, 'rights' => $rights);
	}


	/**
	 * Get info on the messages in the mailbox
	 * i.e. get headers, size and flags
	 * for each email in the mailbox.
	 *
	 * @param string $mailbox (optional) Mailbox to summarize
	 *                        If $mailbox is not specified the
	 *                        current mailbox is used
	 */
	function getMailboxSummary($start = null, $end = null, $mailbox = null, $lines = 0)
	{
		global $pref;

		if (!$this->loggedIn) {
			$this->lastError = IC_NOT_LOGGED_IN;
			return false;
		}

		if ($mailbox) {
			if (!$this->select($mailbox))
				return false;
		}

		if (!$this->mailbox)
			return false;

		$start = (is_numeric($start) && $start > 0) ? $start : 1;
		$end = is_numeric($end) ? $end : $this->total;

		$summary = array();

		if ($this->total < 1)
			return $summary;

		// check whether we're configured to use server side sorting
		// and whether this server even has the sort extension available!
		// NB: we ignore the 'id' (ARRIVAL) sort as simply using the
		// msg sequence ids is faster as we can skip issuing the SORT
		// command and processing its response.
		if ( $var['sort'] !== 'id' && $this->hasCapability('SORT') ) {

			global $var;

			// get encoding
			$encoding = ($pref['imap_sort_charset']) ? $pref['imap_sort_charset'] : 'us-ascii';

			// use server side sorting
			// sort that bad boy out depending on config
			switch ($var['sort']) {
				case "id":
					$resp = $this->sendCmd("SORT (ARRIVAL) $encoding ALL");
					break;
				case "EmailSubject":
					$resp = $this->sendCmd("SORT (SUBJECT) $encoding ALL");
					break;
				case "EmailFrom":
					$resp = $this->sendCmd("SORT (FROM) $encoding ALL");
					break;
				case "EmailDate":
					$resp = $this->sendCmd("SORT (DATE) $encoding ALL");
					break;
				default:
					$resp = $this->sendCmd("SORT (ARRIVAL) $encoding ALL");
					break;
			}
			// bugger
			if (preg_match('/\d+ (NO|BAD) (.+)/', $resp, $m)) {
				$this->lastError = $m[2];
				return $summary;
			}
			// sorted!
			$server_sort_array = array();
			if (preg_match("/^\* SORT (.+)$/", $resp, $regs)) {
				$server_sort_array = preg_split("/ /", trim($regs[1]));
			}
			// fekker
			if ($var['sort'] == 'EmailSubject' || $var['sort'] == 'EmailFrom') {
				$server_sort_array = array_reverse($server_sort_array);
			}

			$length = ($end - $start) + 1;
			$server_sort_array = array_slice($server_sort_array, $start - 1, $length);
			$msg_uids = join(',', $server_sort_array);

			$var['sort'] = "id";

			if($lines > 0)
				$resp = $this->sendCmd("FETCH $msg_uids (UID FLAGS RFC822.SIZE rfc822.header BODY.PEEK[1]<0.250>)");
			else
				$resp = $this->sendCmd("FETCH $msg_uids (UID FLAGS BODY.PEEK[HEADER] RFC822.SIZE)");

			$msgs = preg_split('/\* \d+ FETCH/', $resp);

		} else {
			// do not use server side sorting
			if($lines > 0) {
				$resp = $this->sendCmd("FETCH $start:$end (UID FLAGS RFC822.SIZE rfc822.header BODY.PEEK[1]<0.250>)");
			} else {
				$resp = $this->sendCmd("FETCH $start:$end (UID FLAGS BODY.PEEK[HEADER] RFC822.SIZE)");
			}

			$msgs = preg_split('/\* \d+ FETCH/', $resp);
		}

		$summary = $this->_processMessageSummaries($msgs);

        // sort the msgs for imap servers that don't return
        // msgs in the order they were specified in the FETCH command
        //$summary = $this->_sortMessages($summary, $server_sort_array);
		return $summary;
	}


	/**
	 * Get summaries for an array of messages
	 *
	 * @param array $msgs The array of messages to process
	 * @return array
	 */
	function _processMessageSummaries($msgs)
	{
		$summary = array();

		foreach ($msgs as $info) {

			if (!$info) {
				continue;
			}

			// Extract the UID
			if (preg_match('/UID\s+(\d+)/i', $info, $m)) {
				$uid = $m[1];
			} else {
				// If no UID we should skip as email cannot be opened
				//continue;
			}

			$EmailMsg = substr($info, -255);
			$EmailMsg = preg_replace('/BODY.*?\s/im', '', $EmailMsg);
			$EmailMsg = preg_replace('/\)/', '', $EmailMsg);

			$info = preg_replace('/BODY\[1\].*/is', '', $info);

			// Extract the size from the response
			preg_match('/RFC822\.SIZE\s*(\d+)/i', $info, $m);
			$size = $m[1];

			// Extract the Flags from the response
			preg_match('/FLAGS\s*\(\\\(.*?)\)/', $info, $m);
			$flags = $m[1];

			if(preg_match('/Deleted/i', $flags)) {
				$flags = 'd';
			} elseif(preg_match('/Flagged/i', $flags)) {
				$flags = 'm';
			} elseif(preg_match('/Answered/i', $flags)) {
				$flags = 'r';
			} elseif(preg_match('/Seen/i', $flags)) {
				$flags = 'o';
			} else {
				$flags = '';
			}

			// Strip the IMAP server response string
			$info = preg_replace('/^\s*\(UID.+?$/ims', '', $info);
			$info = preg_replace('/^\s+/ms', '', $info);

			$summary[] = array('header' => $info, 'EmailSize' => $size, 'UIDL' => $flags, 'UID' => $uid, 'EmailMsg' => $EmailMsg);
		}

		return $summary;
	}


    function _sortMessages($msgs, $order)
    {
        $sortedMessages = array();
        foreach ($order as $uid) {
        echo "\n$uid: ";
            foreach ($msgs as $m) {
            	echo "{$m['UID']}, ";
                if ($m['UID'] == $uid) {
                    $sortedMessages[] = $m;
                    break;    
                }    
            }    
        }
        return $sortedMessages;
    }
    
    
	/**
	 * List mailboxes according to reference and mailbox name
	 *
	 * @param string $ref The reference name
	 * @param string $mailbox The mailbox name (may include wildcards)
	 * @param bool $subscribedOnly Only list subscribed mailboxes
	 * @return array Returns an array of mailbox names
	 */
	function listMailboxes($ref='', $mailbox='*', $subscribedOnly=true)
	{
		if (!$this->loggedIn)
		{
			$this->lastError = IC_NOT_LOGGED_IN;
			return false;
		}

		if (!$subscribedOnly)
		$resp = $this->sendCmd("LIST \"$ref\" \"$mailbox\"");
		else
		$resp = $this->sendCmd("LSUB \"$ref\" \"$mailbox\"");

		$lines = explode("\n", $resp);
		$mboxes = array();

		// Search each line for the folders
		foreach($lines as $line)	{

			if(preg_match("/^\*\s+$type.*\s+\"(.*?)\"\s*$/i", $line, $m))
				array_push($mboxes, $this->decodeUTF7($m[1]) );

			else if(preg_match("/^\*\s+$type.*\s+(\S+)\s*$/i", $line, $m))
					array_push($mboxes, $this->decodeUTF7($m[1]) );

			else if(preg_match("/^$id\s+(OK|NO|BAD)/", $line, $m))
					break;

			else if(preg_match("/^\*\s+NO/", $line, $m))
					break;


			}

		return $mboxes;
	}


	/**
	 * Get the number of messages for current mailbox
	 *
	 * NOTE (from RFC3501): Because the STATUS command
	 * is not guaranteed to be fast in its results,
	 * clients SHOULD NOT expect to be able to
     * issue many consecutive STATUS commands and obtain
     * reasonable performance.
     *
	 * @param string $flags A string of comma deliminated
	 *                      flags that specify what we want
	 *                      to count. The flags are:
	 *                      TOTAL - get total number of messages
	 *                      UNSEEN - get number of unseen messages
	 *                      RECENT - get number of recent messages
	 *
	 * @return array An array containing the counts requested
	 */
	function getNumberOfMessages($flags, $mailbox='')
	{
		$flags = explode(',', $flags);
		$args = '';
		$numFlags = 0;

		if (!empty($mailbox)) {
			$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));
		} else {
			$mailbox = $this->mailbox;
		}

		foreach ($flags as $flag)
		{
			$flag = strtoupper(trim($flag));

			if ($flag == 'TOTAL')
				$args .= 'MESSAGES ';
			elseif ($flag == 'UNSEEN')
				$args .= 'UNSEEN ';
			elseif ($flag == 'RECENT')
				$args .= 'RECENT ';

			$numFlags++;
		}

		// Take out any trailing space
		$args = preg_replace('/\s+$/', '', $args);

		#  Mailbox MUST be escaped, otherwise if it has spaces will error
		$resp = $this->sendCmd("STATUS \"$mailbox\" ($args)");

		if (!$resp)
			return false;

		if ($numFlags > 1)
		{
			preg_match_all('/(MESSAGES|UNSEEN|RECENT) (\d+)/', $resp, $matches, PREG_SET_ORDER);

			$count = array();
			foreach ($matches as $match)
			{
				$count[$match[1]] = $match[2];
			}
		}
		else
		{
			preg_match('/(MESSAGES|UNSEEN|RECENT) (\d+)/', $resp, $match);
			$count = $match[2];
		}

		// If Surgemail, requesting a FETCH after a status, breaks ( surgemail bug )
		// Select the same folder after the STATUS, works as expected.
		if ($this->hasCapability('SURGEMAIL'))
			$this->select($mailbox, 1);

		return $count;
	}

	/**
	 * Sends the NAMESPACE command and parses the response
	 */
	function getImapNamespace()
	{

		if (!$this->loggedIn)
		{
			$this->lastError = IC_NOT_LOGGED_IN;
			return false;
		}

		$resp = $this->sendCmd('NAMESPACE');

		// Check the namespace command returns success
		if (preg_match('/^\*/', $resp, $m))	{

            $resp = preg_replace("/\x0d?\x0a$/", '', $resp);

            // A standard response from the namespace command, e.g Courier-IMAP
            preg_match('/(NIL|\((?:\([^\)]+\)\s*)+\))\s(NIL|\((?:\([^\)]+\)\s*)+\))\s(NIL|\((?:\([^\)]+\)\s*)+\))/', $resp, $m);

            if(is_array($m))	{
                $this->PersonalPrefix = $m[1];
                $this->PersonalPrefix = preg_match("/\"(.*?)\" \"(.*?)\"/", $this->PersonalPrefix, $imapfol);

                    if(is_array($imapfol))	{
                    $this->Prefix = $imapfol[1];
                    $this->Deliminator = $imapfol[2];
                    }

            }

		} elseif (preg_match('/NO The command "NAMESPACE" is unsupported in this state/', $resp)) {
			// Workaround for an IMAP server which does not support the namespace command, but Inbox/ is the prefix
			$this->Prefix = 'Inbox/';
			$this->Deliminator = '';
		} else {
			// There is no prefix nor namespace
			$this->Prefix = '';
			$this->Deliminator = '';
		}

        // Double check
        if ($this->Prefix == '' && !$this->select('Trash') && $this->select('INBOX.Trash')) {
            $this->Prefix = 'INBOX';
            $this->Deliminator = '.';
        }

}
	/**
	 * Get an email from the server complete
	 * with all headers
	 *
	 * @param int $uid The email's UID
	 */
	function fetchEmail($uid)
	{
		$resp = $this->sendCmd("UID FETCH $uid (BODY[])");

		if (!$resp)
			return false;

		$resp = rtrim($resp);

		// Remove the first and last lines, as they
		// are just server responses and not part of the email
		$resp = preg_replace("/^.+?\n/", '', $resp);
		$resp = preg_replace("/.+$/", '', $resp);

		// Courier still has a trailing ) that needs to
		// be removed
		$resp = preg_replace('/\)$/', '', $resp);

		return $resp;
	}

	/**
	 * Get an email from the server complete
	 * with all headers as a filehandle, it is much faster
	 *
	 * @param int $uid The email's UID
	 */
	function fetchEmailFH($uid, $file=false)
	{
	    $resp = $this->sendCmd("UID FETCH $uid RFC822", 0, $file);

		if (!$resp)
			return false;

		return true;
	}


	/**
	 * Fetch an emails header as a string
	 *
	 * @param int $uid The email's UID
	 * @return string|bool Email header on success
	 *                     bool false on error
	 */
	function fetchRawHeader($uid)
	{
		$resp = $this->sendCmd("UID FETCH $uid (BODY.PEEK[HEADER])");

		if (!$resp)
			return false;

		// Remove the first line, as it is
		// not part of the headers
		$resp = preg_replace("/^.+?\n/", '', $resp);

		//file_put_contents("php://stderr", "$resp\n\n");

		return $resp;
	}


	/**
	 * Fetch as emails header as an array
	 *
	 * @param int $uid The email's UID
	 */
	function fetchHeaderArray($uid)
	{
		$resp = rtrim($this->fetchRawHeader($uid));

		if (!$resp)
			return false;

		$array = explode("\n", $resp);
		$header = array();

		foreach ($array as $line)
		{
			if (preg_match('/^(.+?):\s*(.+)/', $line, $m))
			{
				if (!isset($header[$m[1]]))
					$header[$m[1]] = $m[2];
				else
					$header[$m[1]] .= $m[2];
			}
		}

		return $header;
	}


	/**
	 * Fetch sizes for emails
	 *
	 * @param int|string $seq (optional) The email/s to fetch
	 *                         the size/s for. $seq should be
	 *                         a valid message sequence number
	 *                         or sequence set
	 *
	 * @return array An array where index = message sequence number
	 *               and value = size of cooresponding message
	 */
	function fetchMailSizes($seq=null)
	{
		// If no sequence is defined we will just
		// fetch for all messages
		if (!isset($seq))
			$seq = '1:*';

		$resp = $this->sendCmd("UID FETCH $seq (RFC822.SIZE)");

		if (!$resp)
			return false;

		$sizes = array();

		if (is_bool($resp))
			return $sizes;

		$resp = explode("\n", trim($resp));

		foreach ($resp as $line)
		{
			if (preg_match('/\d+ FETCH /', $line)) {
				preg_match('/UID (\d+)/', $line, $m);
				$uid = $m[1];
				preg_match('/RFC822\.SIZE (\d+)/', $line, $m);
				$sizes[$uid] = $m[1];
			}
		}

		return $sizes;
	}


	/**
	 * Check whether a message has a particular flag set
	 *
	 * @param int $uid  The message UID
	 * @param string $flag The flag to check for
	 * @return int 1 on true, 0 on false, -1 on error
	 */
	function hasFlag($uid, $flag)
	{
		$flag = str_replace('\\', '', $flag);

		// Fetch the message flags
		$resp = $this->sendCmd("UID FETCH $uid (FLAGS)");

		if (!$resp)
			return -1;

		return preg_match("/FLAGS \(.*?\\$flag/i", $resp);
	}


	/**
	 * Fetch message flags
	 *
	 * @param int $uid The message UID
	 * @return string on success, bool false on error
	 */
	function getFlags($uid, $type='')
	{
	    // Fetch the message flags
		$resp = $this->sendCmd("UID FETCH $uid (FLAGS)");
		if (!$resp)
			return false;

		if(!$type)
		return $resp;
		else {

			// Extract the Flags from the response
			preg_match('/FLAGS\s*\(\\\(.*?)\)/', $resp, $m);
			$flags = $m[1];

			if(preg_match('/Deleted/i', $flags))
			$flags = 'd';
			elseif(preg_match('/Flagged/i', $flags))
			$flags = 'm';
			elseif(preg_match('/Answered/i', $flags))
			$flags = 'r';
			elseif(preg_match('/Seen/i', $flags))
			$flags = 'o';
			else
			$flags = '';

			return $flags;

		}
	}

	/**
	 * Mark messages to be deleted
	 *
	 * @param int|string $seq The sequence number
	 *                   or set of the message/s to
	 *                   be marked for deletion
	 * @param bool $use_uid Whether $seq is a UID
	 */
	function markAsDeleted($seq, $use_uid=true)
	{
		if (preg_match('/\d+(:(\*|%|\d+))?/', $seq)) {

            if ($use_uid) {
                return $this->sendCmd("UID STORE $seq +FLAGS (\Deleted)");
            }

            return $this->sendCmd("STORE $seq +FLAGS (\Deleted)");
		}
	}


	/**
	 * Remove all messages from current
	 * mailbox with \Deleted flag set
	 */
	function expunge($force='')
	{
		global $pref;

		if(!$pref['expunge_logout'])
		$this->sendCmd('EXPUNGE');
		elseif($pref['expunge_logout'] && $force == '1')
		$this->sendCmd('EXPUNGE');

	}

	/**
	 * Mark messages to be deleted
	 *
	 * @param int|string $seq The sequence number
	 *                   or set of the message/s to
	 *                   be marked for deletion
	 */
	function markAsFlag($seq, $type, $flag)
	{
		if (preg_match('/\d+(:(\*|%|\d+))?/', $seq)) {
			$this->sendCmd("UID STORE $seq $type ($flag)");
		}
	}


	/**
	 * Fetch the first few lines of a message (well
	 * actually the first 250 bytes)
	 *
	 *  @param int $uid The message UID
	 */
	function top($uid)
	{
		$resp = $this->sendCmd("UID FETCH $uid (BODY.PEEK[1]<0.250>)");

		if (is_string($resp))
		{
			// Remove first line of response
			$resp = preg_replace("/^.+?\n/", '', $resp);
		}

		return $resp;
	}


	/**
	 * Copy messages to a new mailbox
	 *
	 * @param int|string $seq The seqence number or set
	 *                        of the message/s to be copied
	 * @param string $mailbox The name of the mailbox to copy
	 *                        the message/s to
	 * @param bool $use_uid Whether $seq is a UID
	 */
	function copyMessages($uid, $mailbox)
	{
		$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));

		$res = $this->sendCmd("UID COPY $uid \"$mailbox\"");
		if (!$res)
			return false;

		// Return new UID
		if (preg_match("/$uid\s+(\d+)\]/", $res, $m))
			return $m[1];

		// courier_imap fix
		if (preg_match("/OK/", $res))
    		return true;
	}


	/**
	 * Move messages to another mailbox
	 *
	 * @param int|string $seq The seqence number or set
	 *                        of the message/s to be copied
	 * @param string $mailbox The name of the mailbox to move
	 *                        the messages to
	 */
	function moveMessages($uid, $mailbox)
	{
	    global $pref;

		// Copy the messages to the new mailbox
		// then mark as deleted in original mailbox
		// and expunge
		$res = $this->copyMessages($uid, $mailbox, false);

		if ($res !== false)
		{
			$this->markAsDeleted($uid);

			if(!$pref['expunge_logout']) {
				$this->expunge();
			}

			return $res;
		}
	}

	/**
	 * Delete a mailbox
	 *
	 * @param string $mailbox The mailbox name
	 * @return bool true on success false on failure
	 */
	function deleteMailbox($mailbox)
	{
		$delmailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));

		$resp = $this->sendCmd("DELETE \"$delmailbox\"");

		// Folder cannot be deleted, remove all the emails in it
		if($resp == false)	{
			$arr = $this->select($mailbox);

			for($i = 1; $i <= $arr['total_msgs']; $i++) {
			$this->markAsDeleted($i, false);
			}

			// Expung the messages
			$this->expunge();

			$resp = $this->sendCmd("DELETE \"$delmailbox\"");

		}

		$resp = $this->sendCmd("UNSUBSCRIBE \"$delmailbox\"");
        return ($resp !== false);
	}


	function purgeMailbox($mailbox)
	{
	    //$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));

	    $this->select($mailbox);
	    $seq = '1:*';
	    $this->markAsDeleted($seq);

		// Expung the messages
		$this->expunge();
	}

	/**
	 * Create a new mailbox
	 *
	 * @param string $mailbox The name for the new mailbox
	 * @return bool true on success false on failure
	 */
	function createMailbox($mailbox, $subfolder='')
	{

		// Support subfolder[prefix]folder
		if($subfolder)	 {
		$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7("$subfolder" . "{$this->Deliminator}" . "$mailbox"));
		} else	{
		$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));
		}

		$resp = $this->sendCmd("CREATE \"$mailbox\"");
		return $this->sendCmd("SUBSCRIBE \"$mailbox\"");

	}


	/**
	 * Rename a mailbox
	 *
	 * @param string $curName Current name of mailbox
	 * @param string $newName Rename to this name
	 * @return bool true on success false on failure
	 */
	function renameMailbox($curName, $newName)
	{
		$curName = $this->makeAbsoluteMailbox($this->encodeUTF7($curName));
		$newName = $this->makeAbsoluteMailbox($this->encodeUTF7($newName));

		$status = $this->sendCmd("RENAME \"$curName\" \"$newName\"");

		// If the rename is successful, unsubscribe the old folder name, subscribe the new one
		if ($status) {
			// Some servers fail to subscribe properly without a small delay
			usleep(1000);

			$this->sendCmd("UNSUBSCRIBE \"$curName\"");
			$this->sendCmd("SUBSCRIBE \"$newName\"");

			// get list of subfolders
			$subs = $this->listMailboxes('', "$curName.*", true);

			foreach ($subs as $sub) {
				$sub = $this->encodeUTF7($sub);
				$this->sendCmd("UNSUBSCRIBE \"$sub\"");
				$sub = str_replace($curName, $newName, $sub);
				$this->sendCmd("SUBSCRIBE \"$sub\"");
			}

			return 1;
		}

		return;
	}


	/**
	 * Subscribe a mailbox
	 *
	 * @param string $mailbox The mailbox name
	 * @return bool true on success false on failure
	 */
	function subscribeMailbox($mailbox)
	{
		$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));

		return $this->sendCmd("SUBSCRIBE \"$mailbox\"");
	}


	/**
	 * Unsubscribe a mailbox
	 *
	 * @param string $mailbox The mailbox name
	 * @return bool true on success false on failure
	 */
	function unsubscribeMailbox($mailbox)
	{
		$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));

		return $this->sendCmd("UNSUBSCRIBE \"$mailbox\"");
	}


	/**
	 * Append a message to a mailbox
	 *
	 * @param string $message The message text
	 * @param string $mailbox The mailbox to append to
	 * @return bool true on success false on failure
	 */
	function appendMessage($message, $mailbox, $params = '')
	{
		$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));

		if (is_string($message))
		{
			$size = strlen($message);

			$res = $this->sendCmd("APPEND \"$mailbox\" (\\SEEN) {" . $size . "}", $message);

			// First check for the APPENDUID response then just for OK
			if (preg_match('/\[APPENDUID (\d+)/i', $res, $m)) {
                return $m[1];
			} elseif (strpos($res, 'OK')) {
				return true;
			}

            return false;
		}
	}


	/**
	 * Perform a search
	 *
	 * @param string $arg The argument to pass
	 *                    with the SEARCH command
	 * @return array|bool
	 */
	function search($arg)
	{
		if (is_string($arg))
		{
			$arg = trim($arg); // Cleanup any whitespace at the end
			if (!$resp = $this->sendCmd("UID SEARCH $arg"))
				return false;

			preg_match_all('/\d+/', $resp, $m);
			return $m[0];
		}

		return false;
	}


	/**
	 * Get user quota
	 */
	function getQuota($mailbox)
	{
		// First check that server supports
		// QUOTA commands
		if ($this->hasCapability('QUOTA'))
		{
			$mailbox = $this->makeAbsoluteMailbox($this->encodeUTF7($mailbox));
			$resp = $this->sendCmd("GETQUOTAROOT \"$mailbox\"");
			if (preg_match('/STORAGE (\d+) (\d+)/i', $resp, $m))
				return array($m[1], $m[2]);
		}

		return false;
	}


	function getUIDList()
	{
	    $resp = $this->sendCmd('FETCH 1:* (UID)');
	    preg_match_all('/UID (\d+)/', $resp, $m, PREG_PATTERN_ORDER);
	    return $m[1];
	}

	function getUnreadUIDList()
	{
		$uids = array();
	    $resp = $this->sendCmd('FETCH 1:* (UID FLAGS)');
	    foreach (explode("\n", $resp) as $line) {

	    	if (strpos(strtolower($line), 'seen')) {
	    		continue;
	    	}
	    	$m = array();

	    	preg_match('/UID (\d+)/', $line, $m);
	    	$uids[] = $m[1];
	    }

	    return $uids;
	}

	/**
	 * Gets the hierarchy delimiter from the server
	 * and sets $this->delimiter to its value
	 */
	function _getDelimiter()
	{
		if (!$this->loggedIn)
		{
			$this->lastError = IC_NOT_LOGGED_IN;
			return false;
		}

		$resp = $this->sendCmd('LIST "" ""');

		if ($resp)
		{
			preg_match('/"(.+?)"/', $resp, $m);
			$this->delimiter = $m[1];
		}
	}

	/**
	 * Send a command to the IMAP server and retrieve
	 * the response
	 *
	 * @param string $command The command to send
	 *
	 * @param string $data    If we are issuing a command
	 *                        that means the server will
	 *                        consequently expect literal
	 *                        data sent then this is that data.
	 *
	 * @return mixed          Server response string on success
	 *                        or bool false on error
	 */
    function sendCmd($command, $data=null, $fh=false)
	{
		if (!is_resource($this->socket->fp)) {
			$this->lastError = IC_NOT_CONNECTED;
			return false;
		}

		if (feof($this->socket->fp)) {
			$this->lastError = IC_NOT_CONNECTED;
       		return false;
    	}

		$cid = $this->cid;

		$this->socket->writeLine("$this->cid $command");
		$this->cid++;

		if ($this->debug)
			$this->debugOutput("C: $cid $command\n");

		$resp = '';

		$loop = 0;
		$lines = 0;
		$bytesRead = 0;
		$respSize = 0;
        $code = '';
		$fetch = (substr($command, 0, 5) == 'FETCH');
        $select = (substr($command, 0, 6) == 'SELECT');

		while(true)
		{
			// Check we are still connected
			if(!is_resource($this->socket->fp))
				return $resp;

			if (feof($this->socket->fp)) {
				$this->lastError = IC_NOT_CONNECTED;
	       		return false;
	    	}

			$line = $this->socket->gets(8192); // 8192 same buffer used in Pear Net::POP3

			if ($this->debug)
				$this->debugOutput("S: $line");

			if (preg_match("/^$cid (OK|BAD|NO)/", $line, $m))
				$code = $m[1];
			elseif (!$fetch && !$select && preg_match("/^\* (BAD|NO|BYE)/", $line, $m)) {
				$code = $m[1];
			}

			// UW-IMAP been brain dead for 'status' command on a mailbox
			if ($code == 'NO' && preg_match("/NO CLIENT BUG DETECTED/", $line)) {
				$code = '';
				continue;
			}

			else if ($code == 'BAD' || $code == 'NO' || $code == 'BYE')
				return false;

			else if ($code == 'OK') {
				return (!empty($resp)) ? $resp : $line;
			}
			$lines++;


			// If writing to the filehandle, get msg size and skip the first line response from the IMAP server
			if (is_resource($fh) && preg_match('/^\* /', $line) && $lines == '1')
			{
				preg_match('/\{(\d+)\}/', $line, $m);
				$respSize = $m[1];
				continue;
			}

			// Check if we are sending literal data
			// and server is ready for literal data
			if ($data && preg_match('/^\+/', $line))
			{
				$size = $this->socket->write($data);

				if ($this->debug)
			         $this->debugOutput("C: $cid $data\n");

			    sleep(1);
				$this->socket->write("\r\n");
				continue;
			}

			// Double check that we are not hung on a
			// command (ie not enough data sent)
			if ($data && $line == '')
			{
				// Send some new lines to try and
				// reach expected bytes
				$this->socket->writeLine('');
				if ($this->debug)
			         $this->debugOutput("C: $cid \n");

			}

			// Replace/fix - Under cyrus, multiple IMAP logins, mailbox locks, sockets are closed automatically
			if($line == '' && $loop >1000)
				return $resp;
			else if($line == '')
				$loop++;


			// Print to the filehandle only up to $respSize bytes so we do not append the last IMAP header on the message
			if ($stop) {
			    continue;
			}
			elseif (is_resource($fh))	{

				$bytesRead += strlen($line);

				// Strip Control-M chars
				$line = preg_replace('/\cM+$/', '', $line);

				fwrite($fh, $line);

				// Only read up to expected byte size
				if ($bytesRead == $respSize)
					$stop = true;
			}
			else
			    $resp .= "$line";
		}
	}


	/**
	 * Encode a string as UTF7
	 *
	 * @param string $str The string to encode
	 * @access public
	 * @return string
	 */
	function encodeUTF7($str)
    {
    	global $pref;

    	if (function_exists('mb_convert_encoding') && $pref['allow_utf7_folders']) {
            return mb_convert_encoding($str, 'UTF7-IMAP', 'UTF-8');
        } else {
        	return $str;
        }
    }


	/**
	 * Decode a UTF7 string
	 *
	 * @param string $str The string to decode
	 * @access public
	 * @return string
	 */
    function decodeUTF7($str)
    {
    	global $pref;

        if (function_exists('mb_convert_encoding') && $pref['allow_utf7_folders']) {
            return mb_convert_encoding($str, 'UTF-8', 'UTF7-IMAP');
        } else {
        	return $str;
        }

    }

	/**
	 * Decode an encoded mime-word
	 *
	 * @param string $string The encoded string
	 * @return string The decoded string
	 */
	function mimeWordDecode($string)
	{
		// Remove whitespace from between words
	    $string = preg_replace('/(\?\=)\s*(\=\?)/', '$1$2', $string);

		if (!preg_match_all('/=\?(.+?)\?(.+?)\?(.+?)\?=/', $string, $matches, PREG_SET_ORDER))
			return $string;

		foreach ($matches as $m)
		{
			$encoded = $m[0];
			$charset = $m[1];
			$encoding = strtoupper($m[2]);
			$text = $m[3];

			if ($encoding == 'B')
				$string = str_replace($encoded, base64_decode($text), $string);

			elseif ($encoding == 'Q')
			{
				$text = str_replace('_', "\x20", $text);
				$text = preg_replace('/=([a-f0-9]{2})/ie', "chr(hexdec('\\1'))", $text);

				$string = str_replace($encoded, $text, $string);
			}
		}

		return $string;
	}


	/**
	 * Set the debugging flag and optional
	 * debugging output file
	 *
	 * @param bool $flag
	 * @param string $file optional filename to write
	 *                     debug info to
	 */
	function setDebug($flag, $file=null, $sizeLimit=null)
	{
		$this->debug = $flag;
		$this->debugOutputFile = (!empty($file)) ? $file : "/tmp/popimap_debug"; //$file;
		$this->debugOutputFileSizeLimit = 0; //'320000'; //$sizeLimit;
	}

	/**
	 * Handles debugging output
	 *
	 * Writes debugging output to a file or
	 * echoes to the browser depending on config
	 *
	 * @param string $output The debugging output
	 */
	function debugOutput($output)
	{

		// Check if we need to write to a file
		if (!empty($this->debugOutputFile))
		{
			// Check that debug file is open
			if (!is_resource($this->debugOutputFileHandle))
			{
				// If debug file exists and is over its file size limit
				// then truncate it
				if (file_exists($this->debugOutputFile))
				{
					if ($this->debugOutputFileSizeLimit && filesize($this->debugOutputFile) >= $this->debugOutputFileSizeLimit)
						$mode = 'w';
					else
						$mode = 'a';
				}

				if (!$this->debugOutputFileHandle = @fopen($this->debugOutputFile, $mode))
					return;

				// Write a header for this block of debug data
				$date = date('r');
				$header = "\n## Debug output started on $date, server: $this->host, account: $this->user ##\n";
				fwrite($this->debugOutputFileHandle, $header);
			}

			fwrite($this->debugOutputFileHandle, "$output\n");
		}
		// Otherwise just echo to brower
		else
			echo "$output<br>\n";
	}


	/**
	 * Prepends a mailbox name with 'INBOX'
	 * if not already done.
	 *
	 * Can only be used after login and the
	 * hierarchy delimiter has been attained
	 *
	 * @param string $mailbox
	 * @return string
	 */
	function makeAbsoluteMailbox($mailbox)
	{
		//if (!$this->delimiter)
		//	$this->_getDelimiter();

		// Return if the prefix is the Inbox, we are we selected as the Inbox
		if(preg_match('/^Inbox$/i', $mailbox))
		return $mailbox;

		# Return the full prefix of the mailbox if applicable
		$prefix = $this->Prefix;
		$prefix = str_replace("/", "\/", $prefix);
		if(!preg_match("/^{$prefix}/i", $mailbox))
			return $this->Prefix . $mailbox;
		else
			return $mailbox;

	}


	/**
	 * Check if the server has a particular
	 * capability
	 */
	function hasCapability($capability)
	{
		if ($this->capabilities == null)
			$this->capability();

		return in_array($capability, $this->capabilities);
	}



	function encodeBASE64($s)
	{
	    $B64Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+,';
	    $p = 0;     // phase: 1 / 2 / 3 / 1 / 2 / 3...
	    $e = '';    // base64-encoded string

	    for ($i = 0; $i < strlen($s); $i++) {

	        $c = $s[$i];

	        if ($p == 0) {
	            $e = $e . substr($B64Chars, ((ord($c) & 252) >> 2), 1);
	            $t = (ord($c) & 3);
	            $p = 1;

	        } elseif ($p == 1) {
	            $e = $e . $B64Chars[($t << 4) + ((ord($c) & 240) >> 4)];
	            $t = (ord($c) & 15);
	            $p = 2;
	        } elseif ($p == 2) {
	            $e = $e . $B64Chars[($t << 2) + ((ord($c) & 192) >> 6)];
	            $e = $e . $B64Chars[ord($c) & 63];
	            $p = 0;
	        }
	    }

	    //
	    // flush buffer
	    //

	    if ($p == 1) {
	        $e = $e . $B64Chars[$t << 4];
	    } elseif ($p == 2) {
	        $e = $e . $B64Chars[$t << 2];
	    }

	    return $e;
	}



/**

 * Converts string from base64
 *
 * @param string $s base64 encoded string
 * @return string decoded string
 * @since 1.2.7
 */

	function decodeBASE64($s)
	{
    	$B64Values = array(

            'A' =>  0, 'B' =>  1, 'C' =>  2, 'D' =>  3, 'E' =>  4, 'F' =>  5,
            'G' =>  6, 'H' =>  7, 'I' =>  8, 'J' =>  9, 'K' => 10, 'L' => 11,
            'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17,
            'S' => 18, 'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
            'Y' => 24, 'Z' => 25,
            'a' => 26, 'b' => 27, 'c' => 28, 'd' => 29, 'e' => 30, 'f' => 31,
            'g' => 32, 'h' => 33, 'i' => 34, 'j' => 35, 'k' => 36, 'l' => 37,
            'm' => 38, 'n' => 39, 'o' => 40, 'p' => 41, 'q' => 42, 'r' => 43,
            's' => 44, 't' => 45, 'u' => 46, 'v' => 47, 'w' => 48, 'x' => 49,
            'y' => 50, 'z' => 51,
            '0' => 52, '1' => 53, '2' => 54, '3' => 55, '4' => 56, '5' => 57,
            '6' => 58, '7' => 59, '8' => 60, '9' => 61, '+' => 62, ',' => 63
            );

	    $p = 0;
	    $d = '';

	    $unicodeNullByteToggle = 0;

	    for ($i = 0, $len = strlen($s); $i < $len; $i++) {

	        $c = $s[$i];
	        if ($p == 0) {
	            $t = $B64Values[$c];
	            $p = 1;

	        } elseif ($p == 1) {

	            if ($unicodeNullByteToggle) {
	                $d = $d . chr(($t << 2) + (($B64Values[$c] & 48) >> 4));
	                $unicodeNullByteToggle = 0;
	            } else {
	                $unicodeNullByteToggle = 1;
	            }

	            $t = ($B64Values[$c] & 15);
	            $p = 2;

	        } elseif ($p == 2) {

	            if ($unicodeNullByteToggle) {

	                $d = $d . chr(($t << 4) + (($B64Values[$c] & 60) >> 2));
	                $unicodeNullByteToggle = 0;
	            } else {
	                $unicodeNullByteToggle = 1;
	            }

	            $t = ($B64Values[$c] & 3);
	            $p = 3;

	        } elseif ($p == 3) {

	            if ($unicodeNullByteToggle) {
	                $d = $d . chr(($t << 6) + $B64Values[$c]);
	                $unicodeNullByteToggle = 0;

	            } else {
	                $unicodeNullByteToggle = 1;
	            }

	            $t = ($B64Values[$c] & 3);
	            $p = 0;
	        }
	    }

	    return $d;
	}

	/**
     * get the length of string
     *
     * @param string $string String
     *
     * @return int Line length
     * @access private
     */
    function _getLineLength($string)
    {die($string);
        if (extension_loaded('mbstring')) {
            return mb_strlen($string, 'UTF-8');
        } else {
            return strlen($string);
        }
    }



    /**
     * get substring from string
     *
     * @param string $string String
     * @param int    $start  Position to start from
     * @param int    $length Number of characters
     *
     * @return string Substring
     * @access private
     */
    function _getSubstr($string, $start, $length = false)
    {
        if (extension_loaded('mbstring')) {
            if ($length !== false) {
                return mb_substr($string, $start, $length, 'utf-8');
            } else {
                $strlen = mb_strlen($string, 'utf-8');
                return mb_substr($string, $start, $strlen, 'utf-8');
            }
        } else {
            if ($length !== false) {
                return substr($string, $start, $length);
            } else {
                return substr($string, $start);
            }
        }
    }

}

?>
