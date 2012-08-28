<?php
// +----------------------------------------------------------------+
// | GetMail.php													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: February 2005											|
// +----------------------------------------------------------------+

require_once('header.php');

require_once("SQL.php");
require_once("Flat.php");
require_once("Generic_Mail.php");
require_once("Language.php");
require_once("IMAP_Client.php");

class GetMail
{
 	/**
 	 * The mail handling object
 	 * @var object
 	 */
	var $mailer;

    var $MailServer;

    var $MailAuth;

	var $DateFormat;

	var $TimeFormat;

	var $Language;

	var $TimeZone;

	var $Account;

	var $Username;

	var $Pop3host;

	var $MailDir;

	var $Type;

	var $Mode;

	var $Deliminator;


	// Atmail default folders
	var $atmailFolders = array(
		'Inbox',
		'Sent',
		'Spam',
		'Drafts',
		'Trash'
	);


	/**
	 * Constructor
	 *
	 * @param array $args
	 * @return void
	 */
	function GetMail($args=array())
	{
	    global $domains;
	    $this->mailer = null;

	    foreach ($args as $k => $v)
	        $this->$k = $v;

		$this->init_sql( $args['Username'] );

		$this->Account = "$this->Username@$this->Pop3host";

		// See if the user has defined another MailServer to connect and access the MailAuth type
		$UserSettings = Filter::cleanSqlFieldNames($this->sql->UserSettings);
		$query = "select MailServer, MailAuth, DateFormat, TimeFormat, Language, TimeZone, UseSSL from $UserSettings where Account=?";

		list($this->MailServer, $this->MailAuth, $this->DateFormat, $this->TimeFormat, $this->Language, $this->TimeZone, $this->UseSSL) = $this->sql->sqlarray($query, $this->Account);

		$this->MailDir = $this->sql->getvalue("select MailDir from Users where Account=?",$this->Account);


		$this->SessionID = session_id();

		$this->tree = array();

		$this->months = array('Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05',
		                'Jun' => '06', 'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10',
						'Nov' => '11', 'Dec' => '12');
	}


	// Get a message
	function get($num, $folder=null, $format=0, $cache=1)
	{
	 	global $pref;

	    if ( (preg_match('/pop3|imap/', $this->Type) && $folder == "Inbox") || ($this->Type == "imap" && $pref['imap_folders']) )
	    {
	        $type = "get_" . $this->Type;
	    }
	    else
	        $type = "get_" . $this->Mode;

	    return $this->$type( $num, $folder, $format, $cache );
	}

	//  a message to another folder , or delete
	function move($num, $folder, $newfolder, $del=null, $use_uid=false)
	{
		// If removing a message from the trash, delete the message
		if ($folder == "Trash" && $newfolder == "Trash") $newfolder = "erase";

		if ($this->Type == 'pop3' && strtolower($folder) != 'inbox')
			$type = "move_$this->Mode";
		else
			$type = "move_" . $this->Type;

	    return $this->$type( $num, $folder, $newfolder, $del, $use_uid );
	}

	function select($folder)
	{
		if ($this->Type == 'imap')
			return $this->mailer->select($folder);
	}

	// Check the account is active and authentication passes
	function login()
	{
		$type = "login_" . $this->Type;
	    return $this->$type();
	}

	// List the first X amount of lines from the message
	function top($num, $folder, $lines)
	{
	    $type = "top_" . $this->Type;
	    return $this->$type( $num, $folder, $lines );
	}

	// Get the number of messages on the server
	function msgid($folder, $sort, $order='', $unread=false)
	{
	    $type = "msgid_" . $this->Type;
	 	return $this->$type( $folder, $sort, $order, $unread);
	}

	// Check the depth of a mailbox folder for the Javascript tree
	function checkchildren($folder, $folders)
	{
	    //If the user account is using IMAP folders
	    if ( $this->Type == "imap" )
	        $type = "checkchildren_imap";
	    else
	        $type = "checkchildren_" . $this->Mode;

		return $this->$type( $folder, $folders );
	}

	function list_($folder, $msg=null)
	{
	    $type = "list_" . $this->Type;

	    return $this->$type($folder, $msg);

	}

	// Create a new folder
	function newfolder($folder, $subfolder=null)
	{
	 	global $pref;

	    // Support IMAP folders, otherwise default to SQL/mbox
	    if ( $this->Type == "imap" && $pref['imap_folders'] )
	        $type = "newfolder_" . $this->Type;
	    else
	        $type = "newfolder_" . $this->Mode;

	    return $this->$type($folder, $subfolder);
	}

	// Rename a folder
	function renamefolder($folder, $newfolder, $traverse=null)
	{
	 	global $pref;

	    // Support IMAP folders, otherwise default to SQL/mbox
	    if ( $this->Type == "imap" && $pref['imap_folders'] )
	        $type = "renamefolder_" . $this->Type;
	    else
	        $type = "renamefolder_" . $this->Mode;

	    return $this->$type($folder, $newfolder, $traverse);
	}

	/**
	 * Delete selected
	 *
	 * @param string $folder
	 * @return object
	 */
	function delfolder($folder)
	{
	 	global $pref;

	    // Support IMAP folders, otherwise default to SQL/mbox
	    if ( $this->Type == "imap" && $pref['imap_folders'] )
	    {
	        $type = "delfolder_" . $this->Type;
	    }
	    else
	    {
	        $type = "delfolder_" . $this->Mode;
	    }
	    return $this->$type($folder);
	}


	function purgefolder($folder)
	{
		global $pref;

	    // Support IMAP folders, otherwise default to SQL/mbox
	    if ( $this->Type == "imap" && $pref['imap_folders'] ) {
	        $type = "purgefolder_" . $this->Type;
	    } else {
	        $type = "purgefolder_" . $this->Mode;
	    }

	    return $this->$type($folder);
	}

	// List folders
	function listfolders($folder=null, $flag=null, $filter=null)
	{
	 	global $pref;

	    // If the user account is using IMAP folders
	    if ( $pref['imap_folders'] && $this->Type == "imap" )
	    {
	        $type = "listfolder_imap";
	        $folders = $this->$type($folder, $flag, $filter );
	    }
	    else
	    {
	        $type = "listfolder_" . $this->Mode;
	        $folders = $this->$type($folder);
	    }


	    // discard UTF-7 folders if not decoded
	    if (!$pref['allow_utf7_folders'] || !extension_loaded('mbstring')) {
	    	$tmp = array();
		    foreach ($folders as $f) {
		    	if (preg_match('/&[a-z0-9]{3,}-/i', $f)) {
		    		continue;
				}
				$tmp[] = $f;
			}

			return $tmp;
	    }

	    return $folders;
	}

	// List folders
	function searchfolders($args)
	{
	 	global $pref;

	    // If the user account is using IMAP folders
	    if ( $pref['imap_folders'] && $this->Type == "imap" )
	        $type = "searchfolders_imap";
	    else
	        $type = "searchfolders_" . $this->Mode;

	    return $this->$type($args);
	}


	function sizefolder($folder, $mode=null)
	{
	 	global $pref;

	    if ( $this->Type == "imap" && $pref['imap_folders'] )
	        $type = "sizefolder_" . $this->Type;
	    else
	        $type = "sizefolder_" . $this->Mode;

	    return $this->$type( $folder, $mode );
	}


	/**
	 * Check for get quota method
	 *
	 * @return array
	 */
	function getquota()
	{
	 	global $pref;

	    if ( $this->Type == "imap" && $pref['imap_folders'] )
			$type = "getquota_" . $this->Type;
	    else
			$type = "getquota_" . $this->Mode;

		return $this->$type();
	}

	function getmailboxsummary($start=null, $end=null, $mailbox=null, $lines=null)
	{
		global $pref;

		if ( ($this->Type == "imap" && $pref['imap_folders']) || ($this->Type == 'pop3' && (!$mailbox || strtolower($mailbox) == 'inbox')))
			$type = "getmailboxsummary_" . $this->Type;
	    else
			$type = "getmailboxsummary_" . $this->Mode;

		$summary = $this->$type($start, $end, $mailbox);

		// Run mailbox filters if viewing INBOX
		if ($this->Type != 'pop3' && (strtolower($mailbox) == 'inbox' || $mailbox == '')) {
		    $summary = $this->filtermail($summary);
		}

		return $summary;
	}


	function filtermail($mail)
	{
	    global $atmail, $folders;

		// If we are POP3, return, not yet implemented
		if ($this->Type == 'pop3')
			return $mail;

        $sort_subject = $atmail->getsort( "EmailSubject", "hash" );
        $sort_email   = $atmail->getsort( "EmailAddress", "hash" );

        $count = 1;

        foreach ($mail as $k => $email) {

            $match = false;

            $id = isset($email['id']) ? $email['id'] : $email['UID'];

            if (!$id)
                $id = $count;

            // Check if the subject matches any of our filters
            foreach (array_keys($sort_subject) as $sort) {

    			$orig = $sort;
    			$this->sortactive = true;

    		    // Remove any slashes added when inserted into DB
    		    $sort = stripslashes($sort);

                // Escape any regular expression characters
                $sort = preg_quote($sort, '/');

                if (preg_match("/Subject:.*?$sort.*?/i", $email['header'])  && $this->folderexists($sort_subject[$orig], $folders)) {

                    $this->msgmove++;
    				$email['UID'] = $this->move($id, "Inbox", $sort_subject[$orig], 0, true);
    				$match = true;
                    $email['folder'] = $sort_subject[$orig];
    				$email['class'] = 'itembold';
                    $email['Status'] = $atmail->parse("html/$atmail->Language/msg/sortmsgs.html", array('folder' => $email['folder']));

    				// Change the message font to italic ( in the stylesheet )
                    $email['msgclass'] = "itemi";
                    $mail[$k] = $email;
                    break;
                }
            }

            if ($match)
                continue;

    		// Check if the email equals any of our filters
    		foreach ( array_keys($sort_email) as $sort) {

                $orig = $sort;
    			$this->sortactive = true;

    		    // Remove any slashes added when inserted into DB
    		    $sort = stripslashes($sort);

    		    // Escape any regular expression characters
                $sort = preg_quote($sort, '/');

    			if ( preg_match("/From:.*?$sort.*?/i", $email['header']) && $this->folderexists($sort_email[$orig], $folders)) {

    				$this->msgmove++;
    				$match = true;
    				$email['UID'] = $this->move( $id, "Inbox", $sort_email[$orig], 0, true);
    				$email['folder'] = $sort_email[$orig];
    				$email['Status'] = $atmail->parse("html/$atmail->Language/msg/sortmsgs.html", array('folder' => $sort_email[$orig]));

    				// Change the message font to bold ( in the stylesheet )
    				$email['msgclass'] = "itemi";
    				$mail[$k] = $email;
    				break;
    			}
    		}

    		if ($match)
                continue;

            if ($this->Type == 'pop3') {
                $mail[$k]['popid'] = $count;
                $count++;
            }

            /*
            // Check if the block-sender matches any of our specified fields ( POP3/IMAP only )
    		foreach ( array_keys($spam_hash) as $sort)
    		{
    			$orig = $sort;

    			$mailType = strtolower($mail->Type);
    			if ($mailType ==  'pop3' || $mailType == 'imap')
    				continue;

    		   // Remove any slashes added when inserted into DB
    		   $sort = stripslashes($sort);

    		   // Escape any regular expression characters
    		   $sort = preg_quote($sort, '/');

    			if ( preg_match("/$sort/i", $email['EmailFrom']))
    			{
    				$var['msgmove']++;
    				$match++;
    				$mail->move( $email['id'], "Inbox", "Spam", 0 );
    				array_push($msgmove, "Spam:{$email['id']}");
    				$email['folder'] = "Spam";
    				$email['EmailSubject'] = "SPAM: {$email['EmailSubject']}";

    				$email['Status'] = $atmail->parse("html/$atmail->Language/msg/sortmsgs.html", array('folder' => "Spam"));

    				// Change the message font to bold ( in the stylesheet )
    				$email['msgclass'] = "itemi";
    			}
    		}
    		*/
        }

		return $mail;
	}


	// List the email headers
	function gethead($id, $folder, $lines=null, $cmd=null)
	{
	 	global $pref;

	    if ( (preg_match('/pop3|imap/', $this->Type) && $folder == "Inbox")
	      || ($this->Type == "imap" && $pref['imap_folders']) )
	        $type = "gethead_" . $this->Type;
	    else
	        $type = "gethead_" . $this->Mode;

	    return $this->$type($id, $folder, $lines, $cmd);
	}

	// Retreive a list of message UIDL's
	function getuidl($id, $msgid=null)
	{
		// If the user account is using IMAP folders
	    if ( $this->Type == "imap" )
			$type = "getuidl_imap";
	    else
			$type = "getuidl_" . $this->Mode;

	    return $this->$type( $id, $msgid );
	}

	// Search a users Mailbox
	function search($db)
	{
		// Escape any special characters
	    //foreach(array('EmailSubject', 'EmailTo', 'EmailFrom', 'EmailMessage') as $name)
		//    $db[$name] = preg_replace('/([()*+.?\\\])/', '\$1', $db[$name]);

	    // If the user account is using IMAP folders
	    if ( $this->Type == "imap" )
			$type = "search_imap";
	    else
			$type = "search_" . $this->Mode;

		return $this->$type($db);
	}

	// Quit the connection to the mail-server
	function quit()
	{
		if ($this->Type == 'file')
			return;

	    $type = "quit_" . $this->Type;

	    return $this->$type();
	}

	// Append a message to a folder ; IMAP style
	function append($folder, $msg)
	{
	    // If the user account is using IMAP folders
	    if ( $this->Type == "imap" )
			$type = "append_imap";
	    else
			$type = "append_" . $this->Mode;

	    return $this->$type($folder, $msg);
	}

	// Retrieve the EmailUIDL from a database table
	function get_emailuidl($id, $box, $msgs)
	{
		$type = "get_emailuidl_" . $this->Mode;
	    return $this->$type($id, $box, $msgs);
	}

	// Retrieve the EmailSize from a database table
	function get_emailsize($id, $box, $msgs)
	{
		$type = "get_emailsize_" . $this->Mode;
	    return $this->$type($id, $box, $msgs);
	}

	// Receive the message size
	function get_msgsize($id)
	{
		$type = "get_msgsize_" . $this->Mode;
	    return $this->$type($id);
	}

	// Subscribe to a message folder
	function subscribe($folder)
	{
	 	global $pref;

	    // If the user account is using IMAP folders
	    if ( $pref['imap_folders'] && $this->Type == "imap" )
	    {
	        $type = "subscribe_imap";
	        return $this->$type($folder);
	    }
	}

	// Unsubscribe to a message folder
	function unsubscribe($folder)
	{
	 	global $pref;

	    // If the user account is using IMAP folders
	    if ( $pref['imap_folders'] && $this->Type == "imap" )
	    {
	        $type = "unsubscribe_imap";
	        return $this->$type($folder);
	    }
	}

	// Show new messages
	function shownewmessages($folder)
	{
		// If the user account is using IMAP folders
	    if ( $this->Type == "imap" )
			$type = "shownewmessages_imap";
	    else
			$type = "shownewmessages_" . $this->Mode;

	    return $self->$type( $folder );
	}

	// Show unread messages
	function showunread($folder=null)
	{
		// If the user account is using IMAP folders
	    if ( $this->Type == 'imap' )
			$type = 'showunread_imap';
		elseif ($this->Type == 'pop3' && (!$folder || strtolower($folder) == 'inbox'))
			$type = 'showunread_pop3';
	    else
			$type = 'showunread_' . $this->Mode;

	    return $this->$type( $folder );
	}

	//
	function updateuidl($uidl, $type, $force=null, $folder='', $id='')
	{
	    // If the user account is using IMAP folders
	    if ( $this->Type == "imap" ) {
		$func = "updateuidl_imap";
	    } else	{
		$func = "updateuidl_" . $this->Mode;
		}

	    return $this->$func($uidl, $type, $force, $folder, $id);
	}



	// Create a new folder
	function newfolder_sql($folder, $subfolder='')
	{
		// Strip 'INBOX.' or 'INBOX/' from folder name
		if(preg_match('/^INBOX\./', $folder)) {
			$folder = preg_replace('/^INBOX.{1}/', '', $folder);
		} elseif (preg_match('/^INBOX\//', $folder)) {
			$folder = preg_replace('/^INBOX\/{1}/', '', $folder);
		}
		// Strip 'INBOX.' or 'INBOX/' from subfolder name
		if(preg_match('/^INBOX\./', $subfolder)) {
			$subfolder = preg_replace('/^INBOX.{1}/', '', $subfolder);
		} elseif (preg_match('/^INBOX\//', $subfolder)) {
			$subfolder = preg_replace('/^INBOX\/{1}/', '', $subfolder);
		}
		if($this->Type == 'imap') {
		$subscribe = '1';
		// Check that the folder-name does not already exist
		$exist = $this->sql->sqlgetfield("select id from Folders where Account = ? and FolderName = ? and Subscribe='1'", array($this->Account, $folder));
		}
		else {
		$subscribe = '0';
		// Check that the folder-name does not already exist
		$exist = $this->sql->sqlgetfield("select id from Folders where Account = ? and FolderName = ? and Subscribe='0'", array($this->Account, $folder));
		}

	    // Create a new Folder name, if the folder does not already exist in the database
		if (!$exist)
		{
			$parentid = $this->sql->sqlgetfield("select id from Folders where Account = ? and FolderName=?", array($this->Account, $subfolder));
			$query = "INSERT INTO Folders (Account, FolderName, ParentID, Subscribe) VALUES (?, ?, ?, ?) ";
			$data  = array("$this->Username@$this->Pop3host", $folder, $parentid, $subscribe);
	    	$this->sql->sqldo($query, $data);
		}
	}

	// buildos_ignore {
	/**
	 * Delete a folder in the SQL database, including the messages,
	 * emailuidl referneces and any sub-folders + messages
	 *
	 * Updated to use new SQL functions with placeholders
	 *
	 * @param string $folder
	 */
	function delfolder_sql($folder)
	{
		if (!$folder)
			return;

		// First remove all messages
	    $this->purgefolder_sql($folder);

	    // Delete the folder, only if it's not a default folder
	    if (!$this->isAtmailFolder($folder))
	    {
			// First, delete any sub-folders and call the function again()
			$id = $this->sql->sqlgetfield("SELECT id
										   FROM Folders
										   WHERE FolderName = ? AND Account = ?",
										  array($folder, $this->Account));

			$children = $this->sql->sqlarray("SELECT FolderName
											  FROM Folders
											  WHERE ParentID = ? AND Account = ?",
											 array($id, $this->Account));

			foreach($children as $child)
			{
				 if(!$child)
				 	continue;

				 $this->delfolder($child);
			 }

			// Remove the folder
			$this->sql->sqldo("DELETE
							   FROM Folders
							   WHERE FolderName = ? AND Account = ?",
							  array($folder, $this->Account));
	    }
	}


	function purgefolder_sql($folder)
	{
		$ids = $this->sql->sqlarray("SELECT id
	    							 FROM {$this->sql->EmailDatabase}
	    							 WHERE EmailBox = ? AND Account = ?",
	    							array($folder, $this->Account));

		// Cleaup the EmailUIDL table, remove any references to previous messageID's
		$uidl = $this->sql->sqlarray("SELECT {$this->sql->EmailDatabase}.EmailUIDL
									  FROM {$this->sql->EmailDatabase}, {$this->sql->EmailUIDL}
									  WHERE {$this->sql->EmailUIDL}.EmailUIDL = {$this->sql->EmailDatabase}.EmailUIDL
									  AND {$this->sql->EmailDatabase}.EmailBox = ? AND {$this->sql->EmailDatabase}.Account = ? ",
									 array($folder, $this->Account));

		// Remove each entry in the EmailUIDL table that matches
		foreach ($uidl as $v)
		{
			$this->sql->sqldo("DELETE
							   FROM {$this->sql->EmailUIDL}
							   WHERE EmailUIDL = ? AND Account = ?",
							  array($v, $this->Account));
		}
		// Next, remove any email messages that are marked in the selected folder
	    foreach ($ids as $id)
	    {
	        $this->sql->sqldo("DELETE
	        				   FROM {$this->sql->EmailDatabase}
	        				   WHERE id = ? AND Account = ? AND EmailBox = ?",
	        				  array($id, $this->Account, $folder));

	        $this->sql->sqldo( "DELETE
	        					FROM {$this->sql->EmailMessage}
	        					WHERE id = ?", $id );
		}
	}
	// } buildos_ignore


	function listfolder_imap($folder=null, $type=null)
	{
		global $pref;

	    $Sent = $Trash = $Drafts = $Spam = 0;
	    $a = array();

	    if ( !$this->mailer )
	        $this->login( $this->Username, $this->Password );

	    // If the script calling the listfolder function is util.php, reload the folder-cache
        if ((strpos($_SERVER['SCRIPT_NAME'], 'util.php') !== false) && $pref['imapfolder_cache'] )
        {
            // Delete the subscribed folder cache
            $this->delfolder_cache();
            $tmp = array();
        }
	    // Load the folder-list directly from the IMAP server ( no cache )
        elseif (!$pref['imapfolder_cache'] || $_GET['FolderLoad'] == '1')   {
            $tmp = $this->mailer->mailboxes($folder, $type);
        } elseif ($pref['imapfolder_cache']) {
            $tmp = $this->listfolder_sql($folder, $type);
        }

		// First time logged into the account
		if ( !count($tmp) && $pref['imapfolder_cache'] )
		{
			$tmp = $this->mailer->mailboxes($folder, $type);
			//$this->delfolder_cache();
			// Create each folder in our local cache
			foreach ($tmp as $v)
				$this->newfolder_sql($v, '');
		}

		// Return mailboxes that really exist. Delete folders that are filtered (e.g .folder names)
		if(is_array($tmp)) {
		    foreach ($tmp as $v)
		    {
		        // Ignore blank names or INBOX
			    if (!$v || strtolower($v) == 'inbox') continue;

				// If loading folders and cache enabled, store folders in sql cache
				if (($_GET['FolderLoad'] == 1) && $pref['imapfolder_cache']) {
				$this->newfolder_sql($v, '');
	            }

				// Strip 'INBOX.' or 'INBOX/' from folder names
				if(preg_match('/^INBOX\./', $v)) {
					$v = preg_replace('/^INBOX.{1}/', '', $v);
				} elseif (preg_match('/^INBOX\//', $v)) {
					$v = preg_replace('/^INBOX\/{1}/', '', $v);
				}

				array_push($a, $v);

		        if ( $v == "Sent") $Sent++;
		        if ( $v == "Trash") $Trash++;
		        if ( $v == "Drafts") $Drafts++;
		        if ( $v == "Spam") $Spam++;
			}
		}

		// Add INBOX
		array_push($a, 'Inbox');

	    $status = null;

	    // Check our default folders exist, otherwise create the mailboxes.
		// If our default folders are missing, create them
		if ( !$Trash )
		{
		    $status = $this->mailer->create_mailbox('Trash');
			$this->mailer->subscribe('Trash');
		}

		if ( !$Sent )
		{
		    $status = $this->mailer->create_mailbox('Sent');
			$this->mailer->subscribe('Sent');
		}

		if ( !$Spam )
		{
		    $status = $this->mailer->create_mailbox('Spam');
			$this->mailer->subscribe('Spam');
		}

		if ( !$Drafts )
		{
		    $status = $this->mailer->create_mailbox('Drafts');
			$this->mailer->subscribe('Drafts');
		}

		natcasesort($a);
	    return $a;
	}



	// Check the number of subfolders under imap
	function checkchildren_imap($folder, $folders)
	{
	    $a = array();
		$del = preg_quote($this->Deliminator, '/');
		$folder = preg_quote($folder, '/');

		foreach($folders as $f)
		{
			// If the folder has additional parent folders
			if (preg_match("/^$folder$del(.*)$/", $f, $match))
			{
				// Skip if another folder exists
				if (strpos($del, $f) !== false)
					continue;
				array_push($a, $f);
			}
		}

		return $a;
	}

	// Create a folder list on the current parentid and foldername ( e.g Mailbox/Subfolder/Archive )
	function traverse_folder_sql($parentid, $newfolder)
	{
		$db = $this->sql->sqlhash("select FolderName, id, ParentID from Folders where id=? and Account=?", array($parentid, $this->Account));
		$newfolder = $db['FolderName'] . "/" . $newfolder;

		if ($db['ParentID'] > 0)
			$newfolder = $this->traverse_folder_sql($db['ParentID'], $newfolder);

		return $newfolder;
	}

	function listfolder_sql($folder='', $sub='',$subscribe=null)
	{
		$a = array();

		// If using IMAP folders, we use the Subscribed filed in the table to identify the cache ( otherwise POP3 folders may be returned, even if not on IMAP server )
		if($this->Type == 'imap')	{
		    $a = $this->sql->sqlarray("SELECT FolderName
		    						   FROM Folders
		    						   WHERE Account= ? AND ParentID IS NULL AND Subscribe = '1'
		    						   GROUP BY FolderName
		    						   ORDER BY FolderName ASC", $this->Account);

			$subs = $this->sql->sqlarray("SELECT FolderName
										  FROM Folders
										  WHERE Account = ? AND ParentID IS NOT NULL
										  AND Subscribe = '1'
										  GROUP BY FolderName
										  ORDER BY FolderName ASC", $this->Account);

			// Push the default folders
			array_push($a, 'Inbox');
			array_push($a, 'Sent');
			array_push($a, 'Trash');
			array_push($a, 'Drafts');
			array_push($a, 'Spam');

		}
		else
		{
		    // Get personal folder names (ignoring any default @Mail folder names)
			$a = $this->sql->sqlarray("SELECT FolderName
									   FROM Folders
									   WHERE Account = ? AND (Subscribe IS NULL OR Subscribe = 0) AND ParentID IS NULL
									   AND FolderName NOT IN ('Inbox', 'Sent', 'Trash', 'Drafts', 'Spam')
									   GROUP BY FolderName
									   ORDER BY FolderName ASC", $this->Account);

			// Add the default @Mail folders
			array_push($a, 'Inbox');
			array_push($a, 'Sent');
			array_push($a, 'Trash');
			array_push($a, 'Drafts');
			array_push($a, 'Spam');

			$subs = $this->sql->sqlarray("SELECT FolderName
										  FROM Folders
										  WHERE Account = ? AND (Subscribe IS NULL OR Subscribe = 0) AND ParentID IS NOT NULL
										  GROUP BY FolderName
										  ORDER BY FolderName ASC", $this->Account);
		}

		if (is_array($subs))
		{
			foreach($subs as $sub)
			{
				$parentid = $this->sql->getvalue("SELECT ParentID
												  FROM Folders
												  WHERE FolderName = ? AND Account = ?", array($sub, $this->Account));
				array_push($a, $this->traverse_folder_sql($parentid, $sub));
			}
		}

		natcasesort($a);
	    return $a;
	}



	/**
	 * Get an email address from a string
	 * e.g return ben@cgisupport.com from 'Ben Duncan <ben@cgisupport.com>'
	 *
	 * @param string $addr
	 * @return string
	 */
	function extract_email($addr)
	{
	    $emailexp = '/([^":\s<>()\/;]*@[^":\s<>()\/;]*)/';

	    if ( preg_match($emailexp, $addr, $match))
	    	$addr = $match[1];

		$addr = str_replace(array('<', '>', '&gt', '&lt'), '', $addr);

	    return $addr;
	}

	function getnewdate($newdate, $TimeZone)
	{
		global $atmail;

		if($this->Language == 'espanol')
		setlocale(LC_TIME, 'es_ES', 'en_US');
		else if($this->Language == 'italiano')
		setlocale(LC_TIME, 'it_IT', 'en_US');
		else if($this->Language == 'russian')
		setlocale(LC_TIME, 'ru_RU.utf8', 'en_US');

		else if($this->Language != 'japanese' && $this->Language != 'greek' && $this->Language != 'thai')
		setlocale(LC_TIME, strtolower($this->Language), 'en_US');

		$ctime = array();

		if (strlen( $newdate ) == 11)
	    	$newdate = '0'.$newdate;

		$newdate = $this->calc_timezone($newdate);

	    // Change the date if we are using mySQL
	    if (preg_match('/(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)/', $newdate, $matches))
	        $newdate = "$matches[2]/$matches[3]/$matches[1] $matches[4]:$matches[5]:$matches[6]";
	    else
	    {
			$newdate = preg_replace('/\+\d\d\d\d|-\d\d\d\d/', '', $newdate);
			$time = strtotime($newdate);
			$newdate = strftime("%D %X", $time );
		}

	    $newdate = strtotime($newdate);
		$ctime = localtime(null, true);

	    // Get the hour and minute of current time
	    $hour   = $ctime['tm_hour'];
	    $minute = $ctime['tm_min'];

        if ( date('dmy', $newdate) == date('dmy') ) {
	        $newdate = strftime( $atmail->parse("html/$atmail->Language/msg/today.html") . " $this->TimeFormat", $newdate );
        } elseif ( $this->Language != "japanese" && date('W', $newdate) == date('W') ) {
	        $newdate = strftime( "%a $this->TimeFormat", $newdate);

			if($this->Language == 'polish')
			$newdate = iconv('iso-8859-2', "UTF-8", $newdate);
			else if($this->Language == 'russian')
			$newdate = $newdate;
			else
			$newdate = iconv('iso-8859-1', "UTF-8", $newdate);

		} elseif ( $this->Language == "japanese") {
	        $newdate = strftime( "$this->DateFormat %a $this->TimeFormat", $newdate);

			if($this->Language == 'polish')
			$newdate = iconv('iso-8859-2', "UTF-8", $newdate);
			else if($this->Language == 'russian')
			$newdate = $newdate;
			else
			$newdate = iconv('iso-8859-1', "UTF-8", $newdate);

		} else {
	        $newdate = strftime( "%a $this->DateFormat $this->TimeFormat", $newdate);

			if($this->Language == 'polish')
			$newdate = iconv('iso-8859-2', "UTF-8", $newdate);
			else if($this->Language == 'russian')
			$newdate = $newdate;
			else
			$newdate = iconv('iso-8859-1', "UTF-8", $newdate);

 		}

	    return $newdate;
	}



	//
	// IMAP Specific Functions
	//

	// Get a message from the IMAP server
	function get_imap($num, $folder, $type, $cache)
	{
	    global $pref, $atmail;

	    $this->mailer->select($folder);

		// If using an IMAP account, save the message state ( if unread, read, etc )
		if($this->Type == 'imap')
		$this->MessageState = $this->getuidl( '', $num );

	    // Write the msg to a flat file, which is read by the MIME module
	    if (!$type)
	    {
			if ($cache && $pref['message_cache'] )
			{
		        $path = $atmail->tmpdir . "$this->SessionID-$cache.data";
		        if (is_string($this->check_cache($atmail->tmpdir . "$this->SessionID-$cache.data")))
					return $atmail->tmpdir . "$this->SessionID-$cache.data";
			}
			else
	        	$path = $atmail->tmpdir . time() . intval(rand(0,9999)) .  '-'.getmypid().'.data';

			//$this->mailer->saveEmail($num, $path);
			// Directly save the message as a filehandle, without loading entire message into memory

			$fh = fopen($path, "w+");
			$this->mailer->saveEmailFH($num, $fh);
			fclose($fh);

			return $path;

	        // Otherwise, return the $var that contains the msg
	    }
	    else
	    {
			$msg = $this->mailer->get($num);
			return $msg;
	    }

	}

	// Move a message
	function move_imap($num, $folder, $newfolder, $del, $use_uid)
	{
		global $pref;
		$id = null;


		// If we are moving to the same folder, different case,
		// ignore
		if (strtolower($folder) == strtolower($newfolder)) {
			return;
		}

	   	if (!$this->sql && $this->Mode == "sql")
		    $this->init_sql( $this->Username );
		if(!$this->mailer)
			$this->login_imap();

	    if ( $newfolder == "Trash" && $del || $newfolder == "erase")
	    {
	        $this->mailer->select($folder);
	        $this->mailer->deletemail($num, $use_uid);
	    }
	    else
	    {
	        if ( $pref['imap_folders'] )
	        {
	            $this->mailer->select($folder);

				// Move the selected email message
				$id = $this->mailer->movemail($num, $newfolder, $use_uid);
	        }
	        else
	        {
	            // If saving to a folder other then the trash, first retrieve
	            // the message
	            $msg = $this->get( $num, "Inbox", 1);

	            // Save the message in the appropriate folder
	            $this->savemsg_sql( $msg, $newfolder );

	            $id = $this->sql->getid();

	            // Then delete the message from the POP3 server
	            $this->mailer->deletemail($num);
	        }
	    }

	    return $id;
	}


	function getmailboxsummary_imap($start=null, $end=null, $mailbox=null, $lines=null)
	{
		return $this->mailer->getMailboxSummary($start, $end, $mailbox, $lines);
	}



	// Receive the first X lines of the msg
	function top_imap($num, $folder, $lines, $cmd='')
	{
	    $ret = $this->mailer->top($num, $folder, $lines, $cmd);
	    return $ret;
	}

	// List the message sizes
	function list_imap($folder, $msg)
	{
	    // Select the folder to view
	    $this->mailer->select($folder);

	    // Return a list of all messages
	    return $this->mailer->listmailsizes($msg);
	}

	function gethead_imap($id, $folder)
	{
		global $pref;
	    $db = array();

		if ($pref['jpsupport'])
		{
			//fix this
			$enc = $this->mailer->getcode($folder);
			if($enc != "ascii")
				$folder = $this->mailer->UTF7_encode($folder);
		}

	    // Get the start of the message
	    $headers = $this->mailer->getheaders($id, $folder);

	    // Return the message headers
	    $headersArray = $this->message_headers($headers, $id);

	    return $headersArray;
	}

	function msgid_imap($folder, $num, $order, $unread)
	{
		if ($this->mailer)
		{
			$this->mailer->select($folder);
			if ($unread) {
				return $this->mailer->getUnreadUIDList();
			}
			return $this->mailer->getUIDList();
		}
	}

	// Make the $this->mailer object
	function login_imap($max=0)
	{
		global $pref;

		// Return if the Mail handler already exists. Avoid connecting to the mailserver again and
	    // creating a possible lock on the mailbox.
	    if ( $this->count != null)
	    	return;

		// Choose which method to authenticate the user
	    if ( $this->MailServer )
	    {
	        $this->mailer = new Generic_Mail($this->MailServer, 'IMAP', 60, $this->UseSSL);
			if ($this->mailer->lasterror())
	        	return false;
		}
	    else
	    {
	        // Connect to the pop3host the user defined
	        $this->mailer = new Generic_Mail($this->Pop3host, 'IMAP', 60, $this->UseSSL);
	        if ($this->mailer->lasterror())
				return false;
		}

	    // Choose which method to authenticate to the mail server
	    if ( $pref['mailserver_auth'] || $this->MailAuth )
	    {
	    	//If set in the Webadmin, authenticate using user@domain.
	        $status = $this->mailer->login("$this->Username@$this->Pop3host", $this->Password);

			// If we received an error attempt to login with different authentication
			if (!$status)
			{
				$error = $this->mailer->lasterror();
				$this->mailer->quit();
			}
		}
	    else
	    {
	        // Otherwise, use the default username syntax.
	        $status = $this->mailer->login( $this->Username, $this->Password );

			// If we received an error attempt to login with different authentication
			if (!$status)
			{
				$error = $this->mailer->lasterror();
				$this->mailer->quit();
			}
	    }

	    // If we get an error from the server, exit and print the response
	    if (!$status)
	    {
	        return "-ERR Incorrect login or password incorrect. Check you have the correct username and
password for the account. Server returned ( $error )";
	    }

		$this->Deliminator = $this->mailer->Deliminator();

		if ($this->Deliminator == "")
			$this->Deliminator = "/";

		if ($this->mailer)
        	$this->count = $this->mailer->select("INBOX");

        return;
	}

	// Select the size of individual folders
	function sizefolder_imap($folder)
	{
	    $msgsize = 0;

	    if ( !$this->mailer )
	    	$this->login();

	    $this->mailer->select($folder);

	    // Create a temporary hash containing the message sizes
	    $size = $this->mailer->listmailsizes();

		$num = count($size);

		if ($size === false)
			die($this->mailer->lastError());

	    // Find the grand total of the message size.
		if(is_array($size))
	    foreach ($size as $v)
	    	$msgsize += $v;

	    // Find the message size in Kb
	    $msgsize = $msgsize / 1024;

	    // Take away any decimal points . Round off and return a whole number
	    $msgsize = round($msgsize, 0);
		if (!$msgsize)
	    	$msgsize = 0;

	    return array($num, $msgsize);
	}

	// Delete a mailbox via the IMAP server
	function delfolder_imap($folder)
	{
	    global $pref;

	    // Create the $mail object is not already created
	    if ( !$this->mailer )
	    	$this->login();

	    if ($this->isAtmailFolder($folder)) {

            $this->mailer->purge_mailbox($folder);
            if (strtolower($folder) != 'inbox') {
                $this->mailer->select('INBOX', 1);
            }

	    } elseif( $this->mailer->delete_mailbox($folder) ) {
			// Find the number of children folders that need to be renamed
			//$children = $this->mailer->mailboxes($folder, true);
			//foreach($children as $child)
				//$this->mailer->delete_mailbox($child);

            // Delete from SQL cache
            if ($pref['imapfolder_cache']) {
                $this->delfolder_sql($folder);
            }
		}

	    return;
	}


	function purgefolder_imap($folder)
	{
		$this->mailer->purge_mailbox($folder);
        if (strtolower($folder) != 'inbox') {
            $this->mailer->select('INBOX', 1);
        }
	}


	// Create a mailbox via the IMAP server
	function newfolder_imap($folder, $subfolder)
	{
		global $pref;

		//fix this
		if ($pref['jpsupport'])
		{
			$folder = $this->mailer->UTF7_encode($folder);
			$subfolder = $this->mailer->UTF7_encode($subfolder);
		}

	    if ( !$this->mailer )
			$this->login();

		// we need to break up into individual folders if
		// a heirarchy has been given
		if (strpos($folder, $this->Deliminator) && empty($subfolder)) {
			$folders = explode($this->Deliminator, $folder);
			$path = '';
			foreach ($folders as $f) {

				if (empty($path)) {
					$path = $f;
				} else {
					$path .= $this->Deliminator . $f;
				}

				$status = $this->mailer->create_mailbox($path, '');
			}

		} else {
	    	$status = $this->mailer->create_mailbox($folder, $subfolder);
		}

	    return $status;
	}

	// Rename an IMAP folder
	function renamefolder_imap($oldfolder, $newfolder)
	{
	    if ( !$this->mailer ) $this->login();

		$del = "\\" . $this->Deliminator;

		if (strpos($oldfolder, $del) !== false)
		{
			preg_match("/(.*$del)/", $oldfolder, $match);
			$oldroot = $match[1];
		}
		if ($oldroot)
			$newfolder = "$oldroot$newfolder";

		$status = $this->mailer->renamefolder($oldfolder, $newfolder);

		/*if($status)
		{

			// Find the number of children folders that need to be unsubscribed/subscribed
			$children = $this->listfolders($newfolder, "1");

			foreach($children as $child)
			{
				$old = $child;
				//check this
				$old = str_replace($newfolder, $oldfolder, $old); //$old =~ s/$newfolder/$oldfolder/g;
				$this->mailer->unsubscribe($old);
				$this->mailer->subscribe($child);
			}
		}
		*/
		return $status;
	}


	function append_imap($folder, $msg)
	{
		if ( !$this->mailer )
	    	$this->login();
	    return $this->mailer->append( $folder, $msg );
	}

	function quit_imap()
	{
		if($this->mailer)
	    	$this->mailer->quit();
	    $this->count = null;

	    return;
	}


	function search_imap($args)
	{
		$a = $folders = $msgs = array();

		if (!$this->mailer)
	        $this->login( $this->Username, $this->Password );

	    // Search all mailboxes, or only the folder specified by the user
	    if (!$args['EmailBox'])
	        $folders = $this->listfolder_imap();
	    else
	        $folders[0] = $args['EmailBox'];

		foreach($folders as $folder)
		{
			$args['EmailBox'] = $folder;
			$a = $this->mailer->search($args);

			// Add the additional search elements to the array
			if (is_array($a)) {
			    $msgs = array_merge($msgs, $a);
			}
		}

		return $msgs;
	}

	function getquota_imap($folder=null)
	{
	    if (!$this->mailer) $this->login();

		// Find the quota in KB from the server
		$quota = $this->mailer->getquota();

		return $quota;
	}

	function showunread_imap($folder='')
	{
		if (!$this->mailer)  $this->login();

		//if ($folder)
			//$this->mailer->select($folder);

		// Find the quota in KB from the server
		$info = $this->mailer->showunread($folder);

		return $info;
	}

	function getuidl_imap($uidl, $id)
	{
		// Double check the IMAP server for our flag
		$status = $this->mailer->seen($id);
		if ($status) return "o";
	}

	//
	// Misc Mail Functions
	//

	function clean_body($msg)
	{
	    $msg = substr($msg, 0, 2000);

		// Check for PGP signatures
		if (strpos($msg, '-----BEGIN PGP MESSAGE-----') !== false)
			$msg = "Message encrypted for security";

		// Detect any invalid characters ( e.g MIME encoding ) and remove
		$msg = preg_replace("/.*?\w{15}.*/", '', $msg);

		// keep 8-bit stuff. forget mapping charsets though
		//$msg = str_replace('_', ' ', $msg);
		//$msg = preg_replace('/\=([0-9A-Fa-f]{2})/', "chr(hexdec('\\1'))", $msg);

	    // Cleanup the message . Used to show the brief message intro
	    $patterns = array('/\t.*/',
	    				  '/.*MIME.*/',
	    				  '/.*charset.*/',
	    				  '/-?-?/',
	    				  '/<.*?>/',
	    				  '/\r/',
	    				  "/'/",
	    				  '/"/',
	    				  '/&#.*?;/',
	    				  "/From.*(\d['4'])/",
						  '/{\d+}/');

	    $msg = preg_replace($patterns, '', $msg);
	    $msg = preg_replace("/\n/", ' ', $msg);
	    $msg = substr($msg, 0, 500);

		return $msg;
	}

	function clean_header($db)
	{
	    foreach ($db as $k => $v) {
            foreach (range(0, 31) as $chr) {
                $db[$k] = str_replace(chr($chr), '', $v);
            }
        }
        
		if (!$db['EmailSubject'])
	    	$db['EmailSubject'] = "No Subject";

	    if ( strlen($db['EmailSubject']) > 30 && $db['LoginType'] != "xp" && $db['LoginType'] != 'xul' && !$db['Ajax'])
	    {
	    	$EmailSubject = Global_Base::substring($db['EmailSubject'], 0, 30);
	    	$db['EmailSubject'] = "$EmailSubject...";
	    }

	    if ( strlen( $db['EmailFrom'] ) > 30 && $db['LoginType'] != "xp" && $db['LoginType'] != 'xul' && !$db['Ajax'])
	    {
	    	$EmailFrom = Global_Base::substring( $db['EmailFrom'], 0, 30 );
	    	$db['EmailFrom'] = "$EmailFrom..." ;
	    }

	    if ( strlen( $db['EmailTo'] ) > 30 && $db['LoginType'] != "xp" && $db['LoginType'] != 'xul' && !$db['Ajax'])
	    {
	    	$EmailTo = Global_Base::substring( $db['EmailTo'], 0, 30 );
	    	$db['EmailTo'] = "$EmailTo..." ;
	    }

		// If we are not using the Ajax interface, need to clean the data
		if (!$db['Ajax'])
		{
		    foreach ( array('EmailFrom', 'EmailSubject', 'EmailTo') as $v )
			{
				//$db[$v] = preg_replace('/&(\w+\s+)|&(\s+)|&$/', '&amp;$1', $db[$v]);
		        $db[$v] = str_replace(array('<', '>'), array('&lt;', '&gt;'), $db[$v]);
			}
		}

	    // Find the size of the message
	    $db['EmailSizeRaw'] = $db['EmailSize'];
	    $db['EmailSize'] = $db['EmailSize'] / 1024;

		if($db['EmailSize'] > 1000)
		{
			$db['EmailSize'] = $db['EmailSize'] / 1024;
		    $db['EmailSize'] = preg_replace('/(.*)\.(\d).*/', '$1.$2 MB', $db['EmailSize']);
		}
		else
		    $db['EmailSize'] = preg_replace('/(.*)\.(\d).*/', '$1.$2 K', $db['EmailSize']);

	    // Change the date into something more readable

		// Remove malformed UT header, strtotime does not translate
		//$db['EmailDateEpoc'] = strtotime(preg_replace('/[a-zA-Z]+$/', '', $db['EmailDate']));
		$db['EmailDateEpoc'] = strtotime($db['EmailDate']);
		// if strtotime() failed try removing timezone
		if (!$db['EmailDateEpoc'])
			$db['EmailDateEpoc'] = strtotime(preg_replace('/((\+|\-)\d{4})|([a-z]+)$/i', '', $db['EmailDate']));

		$db['EmailDate'] = $this->getnewdate( $db['EmailDate'], $db['TimeZone'] );

	    // Flag as attachment if nessasary. Display no attachment
	    // if the msg has alternative encoding.
		if ($db['EmailAttach'] && $db['Priority'] || strpos($db['EmailType'], 'multipart') !== false && strpos($db['EmailType'], 'alternative') === false && $db['Priority'] )
			$db['EmailAttach'] = '<img width="16" height="13" src="imgs/attachmentpr.gif" />';

	    elseif ( $db['EmailAttach'] || ((strpos($db['EmailType'], 'multipart') !== false) && (strpos($db['EmailType'], 'alternative') === false)))
	        $db['EmailAttach'] = '<img width="16" height="13" src="imgs/attachment.gif" />';

		elseif ($db['Priority'])
			$db['EmailAttach'] = '<img width="16" height="13" src="imgs/highpr.gif" />';

	    else
	        $db['EmailAttach'] = '<img width="16" height="15" src="imgs/trans.gif" />';


	    // Check if message marked, and add to our newmsgs var
		if($this->Type != "imap")
		{
	       $db['UIDL'] = $this->getuidl($db['EmailUIDL'], $db['id']);
	       $db['UIDL'] = $this->clean_uidl($db['UIDL']);
		}

	    if (!$db['UIDL'] && $db['EmailUIDL'] || ($db['flags'] && $db['flags'] != 'Seen'))
	    {
	        $db['ReadTag'] = '<img src="imgs/xp/unread.gif" hspace="3" width="16" height="14" />';
	    }
	    elseif ($db['UIDL'] == 'm')
	    {
	        // Message is flagged / marked
	        $db['ReadTag'] = '<img src="imgs/xp/flag.gif" hspace="3" width="16" height="14" />';
	    }

	    elseif ( $db['UIDL'] == 'r' )
	        $db['ReadTag'] = '<img src="imgs/xp/reply.gif" hspace="3" width="16" height="14" />';

	    elseif ( $db['UIDL'] == 'o' || $db['UIDL'] == 's')
	        $db['ReadTag'] = '<img src="imgs/xp/read.gif" hspace="3" width="16" height="14" />';

	    elseif ( $db['UIDL'] == 'f' )
	        $db['ReadTag'] = '<img src="imgs/xp/forward.gif" hspace="3" width="16" height="14" />';

	    elseif ( $db['UIDL'] == 'd' )
	        $db['ReadTag'] = '<img src="imgs/xp/deleteflag.gif" hspace="3" width="16" height="14" />';

	    elseif ( $db['UIDL'] == 2 )
	    {
	        $db['ReadTag']  = '<I>';
	        $db['CloseTag'] = '</I>';
	    }

	    // Clean the Subject from both ' and " can break the Jscript
	    $db['SubjectJS'] = str_replace(array("'", '"'), '', $db['EmailSubject']); // =~ s/'/\\'/g;
	    																		// =~ s/"/\\'/g;
	   	//check this
		//$db['SubjectJS'] =~ s/\\$//g;

		$db['EmailFromJS'] = str_replace(array("'", '"'), '', $this->extract_email($db['EmailFrom']));
		//$db['EmailFromJS'] =~ s/"/\\"/g;

		$db['EmailMsg'] = $this->clean_body($db['EmailMsg']);

	    return $db;
	}

	// Make a select box that lists the folders
	function folder_select($curfolder, $folders, $appendErase = true)
	{
		global $pref, $domains;
	    $tmp = '';

		$curfolder = preg_quote($curfolder, '/');

		// For IMAP Accounts, allow the user to move the message back to the Inbox
		//$tmp .= "<option value='Inbox'>- Inbox</option>" if($this->Type == "imap" && $curfolder !~ /^Inbox$/i);

		sort($folders);

		// Loop through each folder and append the folder to the select box
	    foreach ($folders as $folder)
	    {
	        $encFolder = urlencode($folder);
			// Skip if the folder matches the current selection, or the Inbox via POP3
			if ( (!empty($curfolder) && preg_match("/^$curfolder$/i", $folder)) || !$folder
	          || $folder == "Inbox" && ( !$domains[$this->Pop3host] && $this->Type == "sql" && $_SERVER['SCRIPT_NAME'] != "search.php" ) || $arr["$folder"] == 1) continue;

	        if ( $pref['imap_subdirectory']
	        	&& $this->Type == "imap"
	        	&& $pref['imap_folders']&& preg_match('/Sent|Trash|Drafts/', $folder))
	        {
	            $sub = preg_replace('/^INBOX\./', '', $folder);
	            $tmp .= "<option value=\"$encFolder\">$sub</option>";
	        }
	        elseif ( $pref['imap_subdirectory'] && $this->Type == "imap"
	          && $pref['imap_folders'] )
	        {
	            $sub = preg_replace('/^INBOX\./', 'Inbox/', $folder);
	            $tmp .= "<option value=\"$encFolder\">$sub</option>";
	        }

			// If we are using POP3, the folder-name is unique, take away the trailing / 's
			elseif ( $this->Type == "pop3" )
	        {
				$sub = urlencode(preg_replace('/.*\//', '', $folder));
	            $tmp .= "<option value=\"$sub\">$folder</option>";
	        }

			else
	            $tmp .= "<option value=\"$encFolder\">$folder</option>";

		// Avoid display duplicate folders if they are returned
		$arr["$folder"] = 1;

	    }

		// Append the 'erase' option to remove the message
		if ($appendErase)
		{
			$tmp .= '<option value="" style="color: gray;">------</option>';
			$tmp .= '<option value="erase">Erase Selected</option>';
		}

	    return $tmp;
	}

	// Change the folder names, depending on the language
	function folder_select_lang($popup, $language, $type=null)
	{
		return Language::folder_language($popup, $language, $type);
	}

	// Create a new database handle
	function init_sql($username)
	{
	    $this->sql = new SQL();

	    // Load the table names
	    $this->sql->table_names($username);
	}

	function encode_language($encoding, $data)
	{
		global $pref;

		if (!$encoding || !$pref['iconv'])
			return $data;

		// Check the encoding is valid, otherwise return without converting
		$encdata = iconv('utf-8', $encoding, $data);

		if ($encdata === false)
			return $data;

		return $encdata;
	}

	function decode_language($encoding = '', $data)
	{
		global $pref;

		if (empty($encoding)) {
			$encoding = $pref['DefaultEncoding'];
		}

		// Check the encoding is valid, otherwise return without converting
		if (!$encoding || !$pref['iconv'] || !$data)
			return $data;

		// Map GB2321 > CP936 - iconv seems to need this to
		// convert this encoding properly
		if (strtoupper($encoding) == 'GB2312') {
		    $encoding = 'CP936';
		}

		$encdata = iconv($encoding, 'UTF-8', $data);
        
		if (strlen($encdata) > 0 ) {
			return trim($encdata);
		} else {
			return $data;
		}
	}


	// Fix a header and take away unnessasary characters
	function quote_header($header)
	{
        // Detect if we may need decoding (avoid regex later if not)
        if (strpos($header, '?=')) {
    
            $headerParts = preg_split('/,|;/', $header);
            $decode = true;
        } else {
            
            // Catch a Non-Standard(?) MIME charset encoding
            // This was required for a client who got many emails with
            // this type of encoding
            if (preg_match('/^(ISO-8859-\d+)\'.*?\'(.+)/i', $header, $m)) {
                return iconv($m[1], 'UTF-8', rawurldecode($m[2]));
            }
            
            $headerParts = array($header);
            $decode = false;
        }
    
        $decoded = '';
    
        foreach ($headerParts as $h) {

            if ($decode && preg_match('/\s*=\?([^\?]+)\?([QqBb])/', $h, $m)) {
               if (strtoupper($m[2]) == 'Q') {
                    $h = preg_replace('/\s*=\?([^\?]+)\?[Qq]\?([^\?]+)?\?=/e', 'GetMail::decode_language(\'$1\', GetMail::decode_mime_head(\'$1\', \'$2\'))', $h);
                } else {
                    $h = preg_replace('/\s*=\?([^\?]+)\?[Bb]\?([^\?]+)?\?=/e', 'GetMail::decode_language(\'$1\', base64_decode(\'$2\'))', $h);
                }
            } else {
                $h = GetMail::decode_language('', $h);
            }
        
            //$header = preg_replace('/(.+?)<(.*?)>/', '$1&lt;$2&gt;', $h);
            $h = str_replace(array('<', '>'), array('&lt;', '&gt;'), $h);
            $h = trim($h);
            $decoded .= $h;
        }
        
        return $decoded;
    }


	// If the message has special encoding change the format
	function decode_mime_head($encoding, $text)
	{
		$encoding = strtoupper($encoding);

	    if ( $encoding == 'US-ASCII' ||
			 $encoding == 'ISO646-US'||
			 preg_match('/ISO-8859-\d+$/', $encoding)||
			 $encoding == 'UTF-8' ||
			 $encoding == 'ISO-2022-JP'||
			 preg_match('/KOI8-\w$/', $encoding) ||
			 preg_match('/^WINDOWS-125\d$/', $encoding) ||
			 preg_match('/TIS-620/i', $encoding) ||
			 preg_match('/^BIG5/i', $encoding) ||
			 preg_match('/^GB/i', $encoding))
	    {
	        // keep 8-bit stuff. forget mapping charsets
	        $text = str_replace('_', ' ', $text);
	        $text = preg_replace('/\=([0-9A-Fa-f]{2})/e', "@chr(@hexdec('\\1'))", $text);
	    }

	    if ( $encoding == 'UTF-16' )
		{
	        // we just dump the high bits and keep the 8-bit chars.
	        $text = str_replace('_', ' ', $text);
	        $text = str_replace('=00', '', $text);
	        $text = preg_replace('/\=([0-9A-Fa-f]{2})/se', "@chr(@hexdec('\\1'))", $text);
	    }

	    return $text;
	}


	function process_headers($headers)
	{
		$headers = explode("\n", $headers);

		$db = array();

		foreach ($headers as $line)
		{
			if (strpos($line, 'Content-Type:') === 0)
				$db['EmailType'] = trim(substr($line, 13));

			elseif (strpos($line, 'Subject:') === 0)
				$db['EmailSubject'] = trim(substr($line, 8));

			elseif (strpos($line, 'From:') === 0)
				$db['EmailFrom'] = trim(substr($line, 5));

			elseif (strpos($line, 'To:') === 0)
				$db['EmailTo'] = trim(substr($line, 3));

			elseif (strpos($line, 'Reply-To:') === 0)
				$db['ReplyTo'] = trim(substr($line, 9));

			elseif (strpos($line, 'Date:') === 0)
				$db['EmailDate'] = trim(substr($line, 5));

			elseif (strpos($line, 'Message-ID:') === 0)
				$db['EmailID'] = trim(substr($line, 11));

			elseif (strpos($line, 'X-Priority:') === 0)
				$db['Priority'] = trim(substr($line, 11));

			elseif (strpos($line, 'X-MSMail-Priority:') === 0)
				$db['Priority'] = trim(substr($line, 18));

			elseif (strpos($line, 'Importance:') === 0)
				$db['Priority'] = trim(substr($line, 11));
		}
		// the below code proved to be a bit on the slow side when
		// dealing with a lot of msgs, hopefully the code above is
		// a bit faster
		/*preg_match('/^Content-Type:\s*(.+?)$/im', $headers,  $m);
		$db['EmailType'] = $m[1];

		preg_match('/^Subject:\s*(.+?)$/im', $headers,  $m);
		$db['EmailSubject'] = $m[1];

		preg_match('/^From:\s*(.+?)$/im', $headers,  $m);
		$db['EmailFrom'] = $m[1];

		preg_match('/^To:\s*(.+?)$/im', $headers,  $m);
		$db['EmailTo'] = $m[1];

		preg_match('/^Date:\s*(.+?)$/im', $headers,  $m);
		$db['EmailDate'] = $m[1];

		preg_match('/^Message-ID:\s*(.+?)$/im', $headers,  $m);
		$db['EmailID'] = $m[1];

		preg_match('/^(X-Priority|X-MSMail-Priority|Importance):(.+?)$/im', $headers,  $m);

		if ($m[1] == 1 || strtolower($m[1]) == 'high')
			$db['Priority'] = '1' ;
		*/

		$db['Priority'] = ($db['Priority'] == 1 || strtolower($db['Priority']) == 'high') ? 1 : null;

		return $db;
	}


	function bodyclean($bound, $msg)
	{
	    // Escape the EmailBoundary. Just in case.
	    $bound = preg_replace('/([()*+.?\\])/', '\\$1', $bound);

	    // Cleanup the message . Used to show the brief message intro
	    $patterns = array(
			'/.*: .*/',
			'/.*MIME.*/',
			'/.*charset.*/',
			'/-?-?$bound/',
			'/<.*?>/');
		$msg = preg_replace($patterns, '', $msg);
	    $msg = str_replace("\n", ' ', $msg);
	    $msg = str_replace(array("\r/", "'", '"'), '', $msg);
	    $msg = substr($msg, 0, 255);

	    return $msg;
	}

	function message_headers($headers, $id=null, $nomsg=null)
	{
		global $pref;
	    $head = null;
	    $db = $tmp = array();
        $key = '';

	    if (!is_array($headers)) {
	    	$headers = explode("\n", $headers);
		}
		
	    foreach ($headers as $v) {
	    
			if (trim($v) == '' || trim($v) == '=20') {
				break;
			}

		    if ( preg_match('/^([a-z0-9]+([\-a-z0-9])*):\s*(.*)/i', $v, $match) ) {
		    	$key = $match[1];
		    	$tmp[$key] = $match[3];
		   	} elseif ( preg_match('/charset="(.+?)"/i', $v, $match) ) {
	            $key = 'Charset';
	        	$db['Charset'] = $match[1];
	        } elseif ( preg_match('/boundary="(.+?)"/i', $v, $match) ) {
	            $key = 'EmailBoundary';
	        	$db['EmailBoundary']  = $match[1];
	        } elseif (!empty($key) && preg_match('/^(\s+.+)/', $v, $match)) {
			    $tmp[$key] .= $match[1];
	        }
	    }
	    
    	$db['EmailDate']    = is_null($tmp['Date'])? '' : $tmp['Date'];
		$db['EmailSubject'] = is_null($tmp['Subject'])? '' : $tmp['Subject'];
		$db['EmailUIDL']    = is_null($tmp['X-UIDL'])? '' : $tmp['X-UIDL'];
		$db['EmailFrom']    = is_null($tmp['From'])? '' : $tmp['From'];
		$db['EmailTo']      = is_null($tmp['To'])? '' : $tmp['To'];
		$db['EmailType']    = is_null($tmp['Content-Type'])? '' : $tmp['Content-Type'];
		$db['EmailID']      = is_null($tmp['Message-ID'])? '' : $tmp['Message-ID'];
		$db['ReplyTo']      = is_null($tmp['Reply-To'])? '' : $tmp['Reply-To'];
		
		if ( (isset($tmp['X-Priority']) && $tmp['X-Priority'] == 1) || 
		     (isset($tmp['X-MSMail-Priority']) && strtolower($tmp['X-MSMail-Priority']) == "high") || 
		     (isset($tmp['Importance']) && strtolower($tmp['Importance']) == "high") ) {
		     
			$db['Priority'] = '1';
		}
			
		$db = $this->quotemessage($db);

	    $db['id'] = $id ? $id : '';

	    return $db;
	}

	function clean_uidl($uidl)
	{
	    // Take away any <> characters from the UIDL string
		// Take away " or ' signs from the UIDL, they will break the HTML formatting when
	    // passing as a var. Also confuse the SQL query
	    // Take away and & symbols - They break the XUL interface
	    $uidl = str_replace(array('<', '>', '"', "'", '/', '&'), '', $uidl);

		// RFC 1939 - UIDL's must be < 70 characters
		//$uidl = substr($uidl, 0, 70);

	    return $uidl;
	}


	function foldertree_imap()
	{
	    $tree = array();
		$cnt = 2;
	    $foldertree = "var fld1 = new TreeMenu();\n";
	    $files = $this->mailer->mailboxes();
	    $folders = $this->mailer->mailfolders();
	    $tree["root"] = 1;
	    $del = "/";

	    foreach ($folders as $folder)
		{
	        if ( !$folder ) continue;

			$folder = str_replace("\n\r", '', $folder);

	        chop($folder);

	        $tree[$folder] = $cnt;
	        $cnt++;
	    }

	    foreach ( $tree as $fol )
		{
	        $foldertree .= "var fld$tree[$fol] = new TreeMenu();\n";

	        $sub = $this->mailer->mailboxes($fol);
	        foreach ($sub as $folder)
			{
	            if ($tree[$folder]) continue;

	            if ( preg_match('/.*\/(.*)/', $folder, $match )) $file = $match[1];

	            $foldertree .=
	"fld$tree[$fol].addItem(new TreeMenuItem(\"$file\", \"showmail.php?Folder=$file\",\"emailwin\", \"mailbox.gif\"));\n";

	            $tree[$fol]['count']++;
	        }
	    }

	    $tree["/"] = 1;
	    $subs;

		ksort($tree);
	    foreach ( array_keys($tree) as $dir)
		{
	        $subdir = split($del, $dir);

	        if ( preg_match('/(.*\/)/', $dir, $match))
	        	$subdir = $match[1];

	        $subdir = preg_replace('/\/$/', '', $subdir);

	        if ( !$subdir ) $subdir = "/";
	        $parent = count($subdir) - 1;

	        $foldertree .= "fld$tree[$subdir].addItem(new TreeMenuItem(\"{$subdir[-1]}\") );\n";

	        if ( !$tree[$subdir]['count'] )
	            $tree[$subdir]['count'] = 0;
	        else
	        {
	        }

	        $foldertree .=
	"fld$tree[$subdir].items[$tree[$subdir]['count']].makeSubmenu(fld$tree[$dir]);\n\n";

	        $tree[$subdir]['count']++;

	        $foldertree .= $subs;

	        return $foldertree;
	    }

	}

	function isDirectory($parent)
	{
	    #print "$folder - $parent\n";

	    preg_match("/($parent\/.*)\//", $folder, $match);

	    $isDir = $match[1];

	    if ($isDir) return 1;
	}


	function decode_head($head)
	{
	    $head = preg_replace('/\s*=\?([^\?]+)\?[Qq]\?([^\?]+)\?=/e', '$this->decode_mime_head(\'$1\', \'$2\')', $head);
        $head = preg_replace('/\s*=\?([^\?]+)\?[Bb]\?([^\?]+)\?=/e', 'base64_decode(\'$2\')', $head);
	    return $head;
	}


	// Get a message flag
	function getflag($folder)
	{
		$type = "getflag_" . $this->Type;
	    return $this->$type($folder);
	}


	// Load a message flag
	function getmsgflag($uid)
	{
		$type = "getmsgflag_" . $this->Mode;
	    return $this->$type($uid);
	}

	// Load message flags from a database table
	function getmsgflag_sql($uid)
	{
		$uid = $this->sql->quote($uid);

		// Select all the folder flag and return a hash
		return $this->sql->doquery("select FlagSeen, FlagAnswered, FlagDeleted, FlagFlagged, FlagRecent, FlagDraft,
	    FlagInferiors, FlagSelect from {$this->sql->EmailDatabase} where id=? and
		Account=?", array($uid, $this->Account));
	}

	// Expung a message
	function expunge($folder)
	{
		if ($this->Type == 'imap')
		$type = "expunge_" . $this->Type;
		else
		$type = "expunge_" . $this->Mode;

	    return $this->$type($folder);
	}



	// Expunge the selected folder of deleted messages
	function expunge_imap($folder)	{
		$this->mailer->select($folder);
		$this->mailer->expunge('1');
	}

	// Wrapper for the SQL/flat-file flag function
	function setmsgflag($uid, $args)
	{
		$type = "setmsgflag_" . $this->Mode;
	    return $this->$type($uid, $args);
	}


	// Set a message flag
	function setflag($folder, $args)
	{
		$type = "setflag_" . $this->Mode;
	    return $this->$type($folder, $args);
	}


	// Count the number of flags for a folder
	function getflagcnt($folder, $args)
	{
		$type = "getflagcnt_" . $this->Mode;
	    return $this->$type($folder, $args);
	}


	// Copy a message
	function msgcopy()
	{
		$type = "msgcopy_" . $this->Mode;
	    return $this->$type($folder, $newfolder, $id);
	}


	function cleanbody($db)
	{
		if (isset($db['EmailMsg']) && !empty($db['EmailMsg']))
		{

			$db['EmailMsg'] = preg_replace("/=([\da-fA-F]{2})/e", "chr(hexdec('\\1'))", $db['EmailMsg']);
		    $db['EmailMsg'] = substr($db['EmailMsg'], 0, 1000);

		    // Cleanup the message . Used to show the brief message intro
		    $patterns = array('/.*: .*/', '/.*MIME.*/', '/.*charset.*/', '/--.*$/', '/<.*?>/');
		    $db['EmailMsg'] = preg_replace($patterns, '', $db['EmailMsg']);
			$db['EmailMsg'] = str_replace(array("'", '"', "\r"), '', $db['EmailMsg']);
		    $db['EmailMsg'] = str_replace("\n", ' ', $db['EmailMsg']);

		    $db['EmailMsg'] = substr( $db['EmailMsg'], 0, 254 );

			$db['EmailMsg'] = $this->decode_language($db['Charset'], $db['EmailMsg']);
		}

		return $db;

	}

	function quotemessage($db)
	{
	    if ( !$db['EmailSubject'] ) $db['EmailSubject'] = "No Subject";

	    // Escape the EmailBoundary. Just in case.
	    $db['EmailBoundary'] = preg_replace('/([()*+.?\\\])/', '\\$1', $db['EmailBoundary']);

		if (isset($db['EmailMsg']) && !empty($db['EmailMsg']))
		{
			$db['EmailMsg'] = preg_replace("/=([\da-fA-F]{2})/e", "chr(hexdec('\\1'))", $db['EmailMsg']);
		    $db['EmailMsg'] = substr($db['EmailMsg'], 0, 1000);

		    // Cleanup the message . Used to show the brief message intro
		    $patterns = array('/.*: .*/', '/.*MIME.*/', '/.*charset.*/', '/--.*$/', '/<.*?>/');
		    $db['EmailMsg'] = preg_replace($patterns, '', $db['EmailMsg']);
			$db['EmailMsg'] = str_replace(array("'", '"', "\r"), '', $db['EmailMsg']);
		    $db['EmailMsg'] = str_replace("\n", ' ', $db['EmailMsg']);

		    $db['EmailMsg'] = substr( $db['EmailMsg'], 0, 254 );

			$db['EmailMsg'] = $this->decode_language($db['Charset'], $db['EmailMsg']);
		}

	    foreach ( $db as $k=>$v) {
	        $db[$k] = $this->quote_header($v);
	    }

		if ( !$db['EmailUIDL'] )
		{
		    // Grab the EmailID if the message does not contain an X-UIDL header
			$db['EmailUIDL'] = $db['EmailID'];

			// Otherwise grab the EmailSubject + Date
		    if ( !$db['EmailID'] ) $db['EmailUIDL'] = md5($db['EmailSubject'] . $db['EmailDate']);

		}
		elseif($this->Type == "pop3")
		{
			// Escape invalid UIDL characters by default for POP3 headers
			$db['EmailUIDL'] = str_replace(array("\n", "\r", ":", "+", "<", ">", "*", "|", "\\", "/"), '', $db['EmailUIDL']);
		}

	    // Take away any ugly chars in the EmailUIDL header
	    $db['EmailUIDL'] = $this->clean_uidl( $db['EmailUIDL'] );

		return $db;
	}

	function filterwords($line)
	{
		// Set type = 0 for **** words ( first and last letter preserved )
		// Set type = 1 for ['blocked'] words
		$type = 1;
		$replace;

		// Define the bad words here
		foreach(array('bitch','fuck','kill','assasin') as $word)
		{
			if(!$type)
			{
				// Find the length of the string
				$len = strlen($word);

				// Find the first and last characters
				preg_match('/(\w).*(\w)$/', $word, $match);
				$first = $match[1];
				$last = $match[2];

				// Build the string [firstletter]****[lastletter]
				for($i=2; $i < $len - 1; $i++)
					$replace .= '*';

				$replace = $first . $replace . $last;

			}
			else
				$replace = "{blocked}";

			// Match the word in the message line
			$line = str_replace($word, $replace, $line);
		}

		return $line;
	}

	function subscribe_imap($folder)
	{
		if ( !$this->mailer )
	        $this->login( $this->Username, $this->Password );

		$this->mailer->subscribe($folder);
	}

	function unsubscribe_imap($folder)
	{
		if ( !$this->mailer )
	        $this->login( $this->Username, $this->Password );

		$this->mailer->unsubscribe($folder);
	}



	// Return a formatted date in seconds from EPOC
	function datesort($date)
	{
		return strtotime($date);
	}


	function mailrelay_sql($ip)
	{
		if (!$ip) return;

		$orig = $ip;
		$ip = $this->sql->quote($ip);

		// First, purge any records older then 2 hours
		//$seconds = '7200';
		//$this->sql->sqldo("delete from MailRelay where DateAdded < (NOW() - $seconds)");

		// Check if the record exists already
		if (!$this->sql->getvalue("select IPaddress from MailRelay where IPaddress = $ip") )
		{
			// Add the record into the database
			$data = array($orig, "$this->Username@$this->Pop3host");
			$this->sql->sqldo( "INSERT INTO MailRelay (IPaddress, Account, DateAdded ) VALUES (?, ?, NOW()) ", $data);

			return 1;

		}
		else
		{
			// Add the record into the database
			$this->sql->sqldo("update MailRelay set DateAdded = NOW() where IPaddress=$ip and Account=?", "$this->Username@$this->Pop3host" );
		}

		return 0;
	}

	function getfolder()
	{
	}

	function newfolder_tree()
	{
		if(!is_array($this->folderarray))
		{
			$this->folderarray = array();
			array_push($this->folderarray, "v", "v");
		}

		array_push($this->folderarray, "v");

		$this->nextfolder = "fld" . (count($this->folderarray) - 1);

	}

	// Search a folder and create a folder-tree
	function folder_depth($folder, $tree, $folders, $displayingSystemFolders=true, $getNewMsgCount=false)
	{
	    global $atmail, $pref;
	    $children = array();
	    $output = '';

		$truefolder = $folder;

//		if($displayingSystemFolders && !$this->treecount["fld"] && $this->Type == "imap" && $pref['allow_IMAPutility'] && !$pref['PersonalTree'])
//			$this->treecount[$tree] = 5;
//		elseif($displayingSystemFolders && !$this->treecount["fld"] && $this->Type == "imap" && !$pref['allow_IMAPutility'] && !$pref['PersonalTree'])
//			$this->treecount["$tree"] = 4;

		//file_put_contents("php://stderr", "Count = $tree\n");

		// Escape the deliminator for our regular expression
		if($this->Deliminator == '/')
		$del = preg_quote($this->Deliminator, '/');
		else
		$del = preg_quote($this->Deliminator);

		if (preg_match("/.*$del(.*)/", $folder, $match))
			$truefolder = $match[1];

	    if (preg_match('/^Inbox$/i', $folder) || $folder == "Sent" || $folder == "Trash" || $folder == "Drafts" || $folder == "Outbox" || $folder == "Spam")
	    	return;

	    $drag ='1'; #if($atmail->['LoginType'] == 'xp');

		// Skip folders that have already been displayed
		if ($this->activefolders[$folder])
			return;

		$children = $this->checkchildren($folder, $folders);
	    $folder = preg_replace("/(?<!\\\)'/", "\\'", $folder);
		$truefolder = preg_replace("/(?<!\\\)'/", "\\'", $truefolder);

		$numMsgs = 0;

		if ($getNewMsgCount) {
			list($numMsgs,) = $this->showunread($folder);
		}

		$encfolder = urlencode($folder);
		if ($children[0])
			$output .= "$tree.addItem(new TreeMenuItem(\"$folder\", \"$encfolder\", \"showmail.php?Folder=$encfolder\", \"emailwin\", '', \"1\",'$drag','1', '$truefolder', \"$numMsgs\"));\n";

		else
			$output .= "$tree.addItem(new TreeMenuItem(\"$folder\", \"$encfolder\", \"showmail.php?Folder=$encfolder\", \"emailwin\", '', \"1\",'$drag','','$truefolder', \"$numMsgs\"));\n";

		$this->activefolders[$folder]++;

		//$mytree =& $this->$tree;
		//$this->treecount["$fld"] = 0;

		// Increment the number of elements in the folder tree
		if (isset($this->treecount["$tree"]))
			$this->treecount["$tree"]++;
		else
			$this->treecount["$tree"] = 0;

		$this->Folders++;

			//echo "TREE = $tree " . $this->treecount["$fld"]. "<HR>";

		// Load the current foldername
		$currentfolder = $tree;

		if ($children[0])
		{
			//print "$currentfolder with children ...\n";
			$this->newfolder_tree();
			$nextfolder = $this->nextfolder;

			$output .= "var $nextfolder = new TreeMenu();\n";
			$this->SubFolders++;
		}

			// Loop through each of the children folders
	        foreach ($children as $child)
	        	$output .= $this->folder_depth($child, $nextfolder, $folders, $displayingSystemFolders, $getNewMsgCount);

		if ($children[0])
		{
			$output .= "$tree.items[".$this->treecount["$tree"]."].makeSubmenu($nextfolder);\n";
		}

		return $output;
	}


	function folder_getlink($folder)
	{
		global $domains;

		if ($this->Mode == "sql" && !$domains[$this->pop3host])
	    	$folder = preg_replace('/.*\//', '', $folder);

		//if ($this->Type == 'imap')
			//$folder = preg_replace('/\{.+?\}/', '', $folder);

		return $folder;
	}

	function fix_folder($folder)
	{
		if (preg_match('/^Inbox$/i', $folder))
		{
			$folder = "";
			return $folder;
		}

		// Escape / characters back into the . mailbox prefix that maildir uses
		$folder = str_replace('/', '.', $folder);
		if (!preg_match('/^\./', $folder))
			$folder = "." . $folder;

		return $folder;
	}


	function updateuidl_imap($uidl, $type, $force, $folder, $id)
	{
	    // Take the first character
	    //if (preg_match('/^(\w)/', $type, $match))
	    	$type = strtolower(substr($type, 0, 1));//$match[1]);

		if($type == 'x')
		$this->mailer->markAsFlag($id, '-FLAGS', "\\Seen \\Answered \\Flagged");

		if($type == 'o')
		$this->mailer->markAsFlag($id, 'FLAGS', "\\Seen");

		if($type == 'r' || $type == 'f') {
		 $this->mailer->markAsFlag($id, '+FLAGS', "\\Answered");
		}


		if($type == 'm')
		$this->mailer->markAsFlag($id, '+FLAGS', "\\Flagged");

	}



	function check_cache($file)
	{
	 	global $pref;

		// Sanity check
		$file = str_replace(array('..', "\0", '|'), '', $file);

		if (!file_exists($file) || !$pref['message_cache'])
			return -1;

		$time = time();
		$size = filesize($file);
		$atime = fileatime($file);
		$mtime = filemtime($file);
	    $secs = $time - $atime;

	    // If older then 60 mins, delete!
	    if ( $secs > ( $pref['message_cache_time'] * 60) )
		{
	        unlink($file);
			return 0;
		}
	    elseif ($size > 0)
			return $file;

		return 0;
	}

	function calc_timezone($newdate)
	{
		global $pref;

		if (!$pref['datetime'] || !$this->TimeZone) {
		    $newdate = preg_replace('/UT$/', 'UTC', $newdate);
		    $newdate = date("D, j M Y G:i O", strtotime($newdate));
		    return $newdate;
		}

		require_once('Date.php');

		// convert to ISO 8601 format
		if(preg_match('/(\d+) ([a-z]+) (\d\d\d?\d?) (\d\d:\d\d:\d\d) ((\+|-)\d\d\d\d|[a-z]+)/i', $newdate, $m)) {

		    if (strlen($m[1]) == 1)
    			$m[1] = "0$m[1]";

    		// If a 2 digit date is given then we need to make an assumption
    		// about the actual year. If year >= 80 we can safely assume that
    		// it's a 20th Century date e.g. 89 becomes 1989. Otherwise lets make it
    		// a 21st Century date, e.g. 08 becomes 2008
    		if (strlen($m[3]) == 2) {
    		    $m[3] = ($m[3] >= 80)? "19$m[3]" : "20$m[3]";
    		}

    		// format month name with uppercase firt char e.g Sep
    		$m[2] = ucfirst(strtolower($m[2]));

    		// Convert Timezone ID to GMT offset
    		if (!is_numeric($m[5])) {

    		    // convert UT > UTC
    		    if ($m[5] == 'UT') {
    		        $m[5] = 'UTC';
    		    }

    			$dt = new Date_TimeZone($m[5]);
    			$m[5] = $dt->getRawOffset();
    			if ($m[5] == 0) {
    				$m[5] = 'Z';
    			} else {
    				$m[5] = $m[5] / 36000;
    				settype($m[5], 'string');
    				$m[5] = preg_replace('/(-|\+)/', '${1}0', $m[5]);
    			}
    		}

    		$newdate = "$m[3]{$this->months[$m[2]]}$m[1]T$m[4]$m[5]";

    		// Do timezone conversion
    		$date = new Date($newdate);
    		$date->convertTZbyID($this->TimeZone);

    		$newdate = $date->getDate();
            $newdate = date("D, j M Y G:i O", strtotime($newdate));

		} elseif (preg_match('/(\d+) ([a-z]+) (\d\d\d\d) (\d\d:\d\d:\d\d) (\w+)/i', $newdate, $m)) {

    		if (strlen($m[1]) == 1)
    			$m[1] = "0$m[1]";

    		// If a 2 digit date is given then we need to make an assumption
            // about the actual year. If year >= 80 we can safely assume that
            // it's a 20th Century date e.g. 89 becomes 1989. Otherwise lets make it
            // a 21st Century date, e.g. 08 becomes 2008
            if (strlen($m[3]) == 2) {
                $m[3] = ($m[3] >= 80)? "19$m[3]" : "20$m[3]";
            }

            // format month name with uppercase firt char e.g Sep
            $m[2] = ucfirst(strtolower($m[2]));

    		$newdate = "$m[3]{$this->months[$m[2]]}$m[1]";

    		// Convert date from timezone ID, rather than +0900 or -1100 format, format in GMT, MST, etc
    		$date = new Date($newdate);
    		$date->setTZByID($m[5]);

    		$newdate = $date->getDate();
            $newdate = date("D, j M Y G:i O", strtotime($newdate));

		} else {
            $newdate = date("D, j M Y G:i O", strtotime($newdate));
		}

		return $newdate;
	}


	// Calculate a messages new ID number, if message sort defined
	function filter_id($id, $filters)
	{
		$count = 0;
		$tmp = array();

		foreach ($filters as $filter)
		{
			if ($tmp[$filter]) continue;
			if ($id > $filter) $count++;
			$tmp[$filter]++;
		}

		$newid = $id;
		if ($count >= 1) $newid = $id - $count;

		if ($newid > 0 && $count >= 1)
		{
			if ($count >= 2) $newid++;
			return $newid;
		}

		return $id;
	}


	function folderexists($folder, $folders)
	{
		$cnt=0;

		foreach($folders as $fol)
		{
			if (strpos($fol, '/') !== false)
				$fol = preg_replace('/.*\//', '', $fol);
			if ($folder == $fol)
				$cnt++;
		}

		return $cnt;
	}

	function escape_pathname($path)
	{
		return AtmailGlobal::escape_pathname($path);
	}

	function fixheaders_ajax($email)
	{
		//$email['EmailSubject'] = utf8_encode($email['EmailSubject']);
		//$email['EmailFrom'] = preg_replace("/([\x80-\xFF])/e", "chr(0xC0|ord('\\1')>>6).chr(0x80|ord('\\1')&0x3F)", $email['EmailFrom']);//utf8_encode(urlencode($email['EmailFrom']));
		//$email['EmailTo'] = utf8_encode($email['EmailTo']);

		$email['ReadTag'] = preg_replace("/.*src='?\"?.*\/(.*)\.gif'?\"? .*/", '$1', $email['ReadTag']);

		if ( strpos($email['EmailAttach'], 'attachment.gif') !== false)
			$email['EmailAttach'] = '1';
		else
			$email['EmailAttach'] = '0';

		return $email;
	}

	/**
	 * Delete a local IMAP folder cache in the SQL DB
	 *
	 */
	function delfolder_cache()
	{
		$this->sql->sqldo("DELETE
						   FROM Folders
						   WHERE Subscribe = '1' AND Account = ?",
						  array($this->Account));
		return;
	}

	// Sorts an array of folder names, putting default folders first
    // then personal folders after in alphabetical order
    function _sort_folders($folders)
    {
    	// Set up the default folders first
    	$default = array('Inbox', 'Trash', 'Sent', 'Drafts', 'Spam');
    	$tmp = array();

    	foreach ($folders as $folder)
    	{
    		// If not a default folder add to temp array
    		if (!in_array(ucfirst(strtolower($folder)), $default))
    			$tmp[] = $folder;
    	}

    	// Sort the temp array and merge with default folders
    	natcasesort($tmp);
    	$folders = array_merge($default, $tmp);

    	return $folders;
    }


    function isAtmailFolder($folder)
    {
    	return in_array($folder, $this->atmailFolders);
    }

} // End GetMail

