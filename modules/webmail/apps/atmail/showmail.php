<?php

// +----------------------------------------------------------------+
// | showmail.php													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Global.php');
require_once('GetMail.php');
require_once('Language.php');
require_once('Session.php');


session_start();
$var = $size = $msgmove = $d = $h = array();

$atmail = new AtmailGlobal();
$auth = $atmail->getAuthObj();

$atmail->status = $auth->getuser();
$atmail->username = $auth->get_username();
$atmail->pop3host = $auth->get_pop3host();

// check for language version
if (!isset($atmail->Language) && strlen($atmail->Language) > 0)
	$atmail->Language = $pref['Language'];

// Print the error screen if the account has auth errors, or session timeout.
if ( $atmail->status == 1 )
	$atmail->auth_error();
if ( $atmail->status == 2 )
	$atmail->session_error();

$atmail->httpheaders();

// Load the account preferences
$atmail->loadprefs();

// Parse the users custom stylesheet
$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css" );
$var['mailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-mail.css");
$var['folder']    = $atmail->escape_html($_REQUEST['Folder'], false );
$var['newfolder'] = $atmail->escape_html( urldecode($_REQUEST['NewFolder']), false );

$var['acc'] = $acc;

if ($_REQUEST['sort']) {
	$var['sort'] = $_REQUEST['sort'];
} elseif ($atmail->MboxOrder) {
	$var['sort'] = $atmail->MboxOrder;
} else {
	$var['sort'] = 'id';
}

$var['order']  = ($_REQUEST['order'])? $_REQUEST['order'] : '';
$var['order']  = Filter::stringMatch($var['order'], array('desc', 'asc'));
$var['flag']   = $_REQUEST['Flag'];
$var['XML']	   = $_REQUEST['XML'];
$var['suffix'] = '_ajax';

// Make sure we are ordering the query with an allowed field
if ( $var['sort'] != "EmailFrom" && $var['sort'] != "EmailTo"
  && $var['sort'] != "EmailSubject" && $var['sort'] != "EmailAttach"
  && $var['sort'] != "EmailDate" && $var['sort'] != "id" && $var['sort'] != "EmailSize")
  {
	$atmail->end();
  }

// Load an array of msgs selected to be moved
$msgs = $_REQUEST['id'];
if (!is_array($msgs)) {
	settype($msgs, 'array');
}

if (isset($_REQUEST['msgmove'])) {
	$msgmove = $_REQUEST['msgmove'];
}

$mail = new GetMail(array(
	'Username' => $atmail->username,
	'Pop3host' => $atmail->pop3host,
	'Password' => $auth->password,
	'Type'     => $atmail->MailType,
	'Mode'     => $atmail->Mode)
);

$status = $mail->login();

// We have an error while logging in. Tell the user
if ($status) {
    print $atmail->parse( "html/$atmail->Language/auth_misc.html", array('status' => "Remote mail-server not responding - Check connection - $status"));
    $mail->quit();
    $atmail->end();
}

// Receive the list of mailbox folders
$folders = GetMail::_sort_folders($mail->listfolders());

// Make sure the requested folder exists (help avoid XSS etc)
if (!empty($var['folder']) && !in_array($var['folder'], $folders)) {
    die("requested folder does not exist");
}

$fol = array();

// Create the select box for moving messages to another folder
$var['folderbox'] = $mail->folder_select($var['folder'], $folders);
$var['folderbox'] = $mail->folder_select_lang( $var['folderbox'], $atmail->Language);

// By default, we want to move to Trash
$var['folderbox'] = str_replace("value=\"Trash\"", "value=\"Trash\" selected", $var['folderbox']);

// If the user has set the messages to another flag; update the UIDL database.
if ( $var['flag'] && $msgs[0] ) {

	$c=0;
    foreach ($msgs as $id) {
		$folder = urldecode($_REQUEST['folders'][$c]);
        // Load a hash containing the message headers
        $db = $mail->gethead( $id, $folder, 5 );
		$mail->updateuidl( $db['EmailUIDL'], $var['flag'], '1', $folder, $id );
		$c++;
    }


	print <<<EOF
<?xml version="1.0" ?>
<MovedMsgs>
<status>1</status>
<message>Changed message status</message>
</MovedMsgs>
EOF;

	// Exit our mail session, avoid mailbox lock
	$mail->quit();
	$atmail->end();

}
elseif ( $msgs && $var['newfolder'] )
{
    // Loop through the selected msgs to delete, if any.
    // Automatically delete messages if AutoTrash is defined

	$count = 0;
	$cnt = 0;
    $var['moved_status'] = true;
    
    foreach ($msgs as $id) {
		if (strpos($id, '::') !== false) {
			$tmp = explode('::', $id);
			$var['folder'] = $tmp[1];
			$arr[0] = $tmp[0];
			$arr[1] = $tmp[2];
		} else {
			$arr = explode(":", $id);
		}

		$cnt++;

		if($arr[1] && !$count) {
			// Move the selected message, but first check the sequence number/uidl matches the message header
        	if ($mail->move($arr[0], $var['folder'], $var['newfolder'], $atmail->AutoTrash, $arr[1]) === false) {
        	    $var['moved_status'] = false;    
        	} else {
        	    $cnt++;    
        	}
		} else {
			// Move the selected message
        	if ($mail->move( $arr[0], $var['folder'], $var['newfolder'], $atmail->AutoTrash ) === false) {
        	    $var['moved_status'] = false;    
        	} else {
        	    $cnt++;    
        	}
		}

		$uniqueid = $arr[0];
		$uniqueid = preg_replace('/:2.*/', '', $uniqueid);

		//$var['folder'] =~ s/'/\'/g;
		$var['folder'] = addslashes($var['folder']);

	}

	// If this is an Ajax call, print the header, then exit
	if ($atmail->Ajax && $var['newfolder'] ) {
	    if ($var['moved_status'] === false) {
		    echo $atmail->parse("html/$atmail->Language/msg/move_msg_fail.xml");
	    } else {
		    echo $atmail->parse("html/$atmail->Language/msg/move_msg_ok.xml");
	    }
		// Exit our mail session, avoid mailbox lock
		$mail->quit();
		$atmail->end();
	}

	// If this is an Ajax call, print the header, then exit
	if ($atmail->Ajax)
	{
		$var['folder'] = str_replace('&', '&amp;', $var['folder']);
		echo <<<_EOF
<?xml version="1.0" ?>

<MovedMsgs>
<status>1</status>
<message>Deleted $cnt messages from {$var['folder']}</message>
</MovedMsgs>

_EOF;
		$atmail->end();
	}
}

list($var['unread'], $var['total']) = $mail->showunread($var['folder']);

$var['fulltotal'] = $var['total'];

// Create the sideframe for the Simple interface containing the list of mailbox folders.
if ( $atmail->LoginType == "simple" ) {
	// Toggle our sort opposite if the user clicks the header again
	$var['ordersort'] = ( $var['order'] == "desc" ) ? "" : "desc";

	$name = 'count' . $var['folder'];
	$var[$name] = $var['unread'];


	// Create the default folders on the left sidebar
	foreach (array('Inbox', 'Trash', 'Sent', 'Drafts', 'Spam') as $fol) {
		$foldertranslate = Language::folder_language($fol, $atmail->Language, '1');

		// If we are the currently selected folder
		if ($fol == $var['folder']) {
			$folicon = "sidebar_" . strtolower($fol) . "_on.gif";
			$count = 'count' . $var['folder'];
			if ($var[$count] > 0)
				$counter = "({$var[$count]})";

			# Weird mod-perl read-only error, must manually specify HTML template?!
			// perhaps we can move this back into an html template with PHP version
			$var['folders_default'] .= <<<_EOF
<tr>
<td width="38" class="optionselectedimg" style="cursor: pointer; cursor: hand;" onclick="location.href='showmail.php?Folder=$fol'"><img src="imgs/simple/$folicon" width="38" height="31" border="0"></td>
<td width="125">
<table width='120' cellpadding='0' cellspacing=0 border=0 height="33">
<tr>
<td onClick="location.href='showmail.php?Folder=$fol'" class='optionselected'>
<font class="menuoption">&nbsp;&nbsp;$foldertranslate</font>

<font class="smallbold">$counter</font>
</td></tr>
</table>
</td>
</tr>

<tr>
<td width="38" bgcolor="#DEEBF6"><img src="imgs/simple/shim.gif" width="38" height="5" border="0"></td>
<td width="125"><img src="imgs/simple/shim.gif" width="125" height="12" border="0"></td>
</tr>
_EOF;

			if ($atmail->Ajax) {
				$icon = strtolower($fol);
				$counter = str_replace(array('(', ')'), '', $counter);
				$var['folders_default_ajax'] .= <<<_EOF
<Fol Name="$fol" Display="$foldertranslate" Count="$counter" Icon="$icon" State=""></Fol>
_EOF;

			}

		}
		// Otherwise the default folder view (unselected)
		else {
			$folicon = "sidebar_" . strtolower($fol) . "_off.gif";

			$var['folders_default'] .= <<<_EOF
<tr>
<td width="38" style="cursor: pointer; cursor: hand;" onclick="location.href='showmail.php?Folder=$fol'"><img src="imgs/simple/$folicon" width="38" height="31" border="0"></td>
<td width="125">

<table width='120' cellpadding='0' cellspacing=0 border=0 height="31">
<tr>
<td nowrap onClick="location.href='showmail.php?Folder=$fol'" onmouseover="this.className='optionroll'" onmouseout="this.className='optionover'" class='optionover'>
<font class="menuoption">&nbsp;&nbsp;$foldertranslate</font>
</td></tr>
</table>
</td>
</tr>

<tr>
<td width="38" bgcolor="#DEEBF6"><img src="imgs/simple/shim.gif" width="38" height="5" border="0"></td>
<td width="125"><img src="imgs/simple/shim.gif" width="125" height="12" border="0"></td>
</tr>
_EOF;

			if ($atmail->Ajax) {
				$icon = strtolower($fol);
				$var['folders_default_ajax'] .= <<<_EOF
<Fol Name="$fol" Display="$foldertranslate" Count="" Icon="$icon" State=""></Fol>
_EOF;
			}
		}
	}

	$var['folders_default_ajax'] .= <<<EOF
<Fol Name="erase" Display="Erase" Count="" Icon="erase" State=""></Fol>
EOF;

	foreach ($folders as $folder) {

		 if ( $folder == "Inbox" || $folder == "Trash" || $folder == "Sent" || $folder == "Drafts" || $folder == "Spam" )
			continue;

		if ($var['folder'] == $folder)
			$subfolderunread = $unread;

		if (preg_match('/Inbox\.Sent|Inbox\.Trash|Inbox\.Drafts|Inbox\.Spam/i', $folder)  && $pref['imap_subdirectory'] && $atmail->MailType == "imap" )
			continue;
		if (strtoupper($folder) == "INBOX")
			continue;

            $folderlink = $folder;

		// Determine if we are the selected folder
		if	($folder == $var['folder']) {
			$on = 1;
			$var['foldericon'] = 'sidebar_folder_on.gif';
		} else {
			$on = 0;
			$var['foldericon'] = 'sidebar_folder_off.gif';
		}

		if ($atmail->Ajax) {

		    $folder = str_replace('&', '&amp;', $folder);
			$var['folders_default_ajax'] .= <<<EOF
<Fol Name="$folder" Display="$folder" Count="" Icon="folder" State=""></Fol>
EOF;
		} else {
			$var['folders'] .= $atmail->parse( "html/$atmail->Language/simple/folderbar.html", array('folderlink' => urlencode($folderlink), 'folder' => $folder, 'unread' => $subfolderunread, 'read' => $read, 'on' => $on, 'foldericon' => 
$var['foldericon']) );
		}
    }
}


// Support unread flags for folders with spaces
$folderstate = $var['folder'];
$folderstate = str_replace(' ', '_', $var['folder']);

$var['Unreadscript'] .= "parent.FolderState[escape(\"{$folderstate}_fstatus\")] = '{$var['unread']}'; parent.FolderStateReDraw(escape(\"{$folderstate}\"));";

// If the user has selected to 'jump' between messages
// e.g next, prev, start, end
if ( $_REQUEST['jump'] ) {
    $type    = $_REQUEST['jump'];
    $current = $_REQUEST['msgid'];

	$msgids = $mail->msgid($var['folder'], $var['sort'], $var['order']);

    $newwin  = $_REQUEST['newwin'];

    if ( !$current )
    	die('ERROR');

	$i = 0;
	foreach ($msgids as $id) {
        // Find the array index, depending on our current msgID
        if ( $current == $id ) {
            $num = $i;
			break;
        }

        $i++;
    }

    // Jump to the next/prev/start or end record
    if ( $type == "next" && $num <= count($msgids) - 1)
        $num--;
    elseif ( $type == "prev" && $num >= 0 )
        $num++;
    elseif ( $type == "start" )
        $num = count($msgids) -1;
    elseif ( $type == "end" )
        $num = 0;

    // Loop back to the end message if user clicks 'prev' for the top message
    if ( !$msgids[$num] )
		$num = 0;

		$cnt = count($msgids);

    	$mail->quit();

    print "<html><head></head><body><script>location.href='reademail.php?id={$msgids[$num]}&folder={$var['folder']}&newwin=$newwin';</script></body></html>";

    // After printing the redirect don't run the rest of the script
    $atmail->end();
}

$var['prevtotal'] = $_REQUEST['prevtotal'];

if ($var['prevtotal'] && $var['total'] > $var['prevtotal'] ) {
	$var['newmailalert'] = "<EMBED src='javascript/newmail.wav' width=0 height=0 autostart=true loop=false>
                     <script language='Javascript'>this.window.focus();</script>";
}

// Filter out bad values
if (isset($_REQUEST['start']) && !is_numeric($_REQUEST['start'])) {
    $_REQUEST['start'] = '';
}

if ($_REQUEST['start'])
	$var['msg_pos'] = $_REQUEST['start'];
elseif ($var['total'] > $atmail->MsgNum)
	$var['msg_pos'] = $var['total'] - $atmail->MsgNum;
else
	$var['msg_pos'] = 1;

$var['start'] = $_REQUEST['start'];

$var['nrmsg'] = count($msgs);

// The default status string
$var['status'] = $atmail->parse( "html/$atmail->Language/msg/totalmsgs.html", array('folder' => Language::folder_language($var['folder'], $atmail->Language, '1'), 'total' => $var['total'] ));

if ( $msgs[0] )
	$var['status'] = $atmail->parse( "html/$atmail->Language/msg/movedmsgs.html", array('newfolder' => Language::folder_language($var['newfolder'], $atmail->Language, '1'), 'nrmsg' => $var['nrmsg'], 'status' => $var['status'] ));

// Change the HTML output depending if the popup's are set in the user prefs
$var['style']  = ( $atmail->Advanced == 1 ) ? "_popup" : "_plain";

// No popup's allowed for XUL
if ($atmail->XUL || $atmail->Ajax) $var['style'] = "_popup";

$var['class']  = "item";
$var['emails'] = null;

// Find which messages to display, depending if the user clicked 'next/prev' buttons
if ( $var['msg_pos'] + $atmail->MsgNum > $var['total'] )
	$var['msg_pos'] = $var['total'] - $atmail->MsgNum;

if ( $var['msg_pos'] + $atmail->MsgNum <= $var['total'] )
	$var['total'] = $var['msg_pos'] + $atmail->MsgNum;

// Get the list of users who are spam blocked
//$spam_hash = $atmail->spam_hash();
$var['count'] = 0;

// Finally print the table containing the messages
print $atmail->parse("html/$atmail->Language/simple/showmail_popup{$var['suffix']}.html", $var);



########################
# List any filtered messages from a POP3/IMAP account
#######################

$moved = array();

// List messages from the newest to oldest
if(is_array($msgmove)) {
    foreach($msgmove as $match) {
        if (preg_match('/(.*?):(\d+)/', $match, $matches = array())) {
            $folder = $matches[1];
            $id = $matches[2];
        }

        if(!$folder || !$id)
            continue;

        array_push($moved, $match);

        // Disable the message popup for simple and XUL interfaces
        if ($atmail->LoginType == "simple" || $atmail->XUL)
            $atmail->Advanced = "";

        // Default to the normal message font when displaying a message
        $match['msgclass'] = "itemi";

        if ( $user['Advanced'] ) {
            // Take away any references to ' and " characters. They break the Jscript for the popup
            $match['EmailSubject'] = str_replace(array("'", '"', "\n", "\r"), '', $match['EmailSubject']);
        }

        // Clean the email headers , chop unnessasary chars and strip
        // HTML chars
        $match = array_merge($match,  array('TimeZone' => $atmail->TimeZone, 'LoginType' => $atmail->LoginType));
        $match = $mail->clean_header($match);

        // Choose which colour the next row is
        $var['class'] = ( $var['class'] == "item" ) ? "item2" : "item";
        $match['class']  = $var['class'];

        $match['ReadTag'] = "<img src='imgs/xp/move.gif' width=16 height=12>";

        // Default style for the simple interface
        if ($atmail->LoginType == "simple")
            $var['style'] = "_popup";

        // Display the matched messages
        $match = array_merge($match, array('msgnum' => $match['id'], 'count' => $var['count']));
        print $atmail->parse("html/$atmail->Language/$atmail->LoginType/emailentry{$var['style']}{$var['suffix']}.html", $match);
    }
}
$var['start'] = $_REQUEST['start'];

$messages = array();
for ($i=$var['fulltotal']-1; $i>-1; $i--)
	$messages[] = $i;

if ($var['start'] >= $var['fulltotal'])
	$var['start'] = $var['fulltotal'] - $atmail->MsgNum;

if ($messages[1] && $var['fulltotal'] > 0)
	$messages = array_splice($messages, $var['start'], $atmail->MsgNum);

if ($var['start'] - $atmail->MsgNum >= 0)
	$previous_offset = $var['start'] - $atmail->MsgNum;

if ($var['start'] + $atmail->MsgNum < $var['fulltotal'] )
	$next_offset = $var['start'] + $atmail->MsgNum;

if (isset($previous_offset)) {

	$var['next'] = <<<_EOF
<a href="showmail.php?start=$previous_offset&Folder={$var['folder']}&sort={$var['sort']}&order={$var['order']}">
<img src="imgs/previous.gif" border=0 title="
_EOF;

	$var['next'] .= $atmail->parse("html/$atmail->Language/msg/prevmsg.html");
	$var['next'] .= <<<_EOF
"></a>
_EOF;

}

if (isset($next_offset)) {

	$var['prev'] = <<<_EOF
<a href="showmail.php?start=$next_offset&Folder={$var['folder']}&sort={$var['sort']}&order={$var['order']}">
<img src="imgs/next.gif" border=0 title="
_EOF;

	$var['prev'] .= $atmail->parse("html/$atmail->Language/msg/nextmsg.html");
	$var['prev'] .= <<<_EOF
"></a>
_EOF;

}

if ($var['next'] || $var['prev']) {
	$pages = $var['fulltotal'] / $atmail->MsgNum;
	if (is_float($pages))
		$pages = intval($pages) + 1;

	$start = 1;

	// Find our current page
	for ($page=1; $page <= $pages; $page++ ) {
		$jump = ( $page - 1 ) * $atmail->MsgNum;
		if ($jump < 1)
			$jump = "1";

		if ($var['start'] == $jump)
			$start = $page;
	}

	$newpages = $start + 7;

	if ($newpages < $pages) {
		$pages = $newpages;
		$extra = $var['fulltotal'] / $atmail->MsgNum;
		if (is_float($extra))
			$extra = intval($extra) + 1;
	} else {
		$diff = $pages - 7;
		$start = $diff;
	}

	if ($start < 0) $start = 1;

	if ($start != 1) {
		$var['jumppage'] = "<option value='start=&Folder={$var['folder']}&sort={$var['sort']}&order={$var['order']}'>First Page 1</option><option value='' style='color: gray;'>----------</option>";
	}

	if (!$_REQUEST['start'] )
		$start = $pages - 7;

	for ($i = $start; $i <= $pages; $i++ ) {
		if ($i <= 0) continue;

		$jump = ($i - 1) * $atmail->MsgNum;

		if ($jump == 1 && $i < $pages)
			$jump = 2;

		if ($var['start'] == $jump) {
			$var['jumppage'] .= "<font class='swbold' color='blue'><b>$i</b></font></a>,&nbsp;";
			$var['jumppage'] .= "<option value='' style='color: gray;' selected>Page $i</option>";
		} else {
			$var['jumppage'] .= "<option value='start=$jump&Folder={$var['folder']}&sort={$var['sort']}&order={$var['order']}'>Page $i</option>";
		}
	}

	if ($extra) {
		$id = ($extra -1) * $atmail->MsgNum;
		$var['jumppage'] .= "<option value='' style='color: gray;'>----------</option><option value='start=$id&Folder={$var['folder']}&sort={$var['sort']}&order={$var['order']}'>Last Page $extra</option>";
	}
}

$total = $var['fulltotal'];

// Calculate the number of pages
if ($var['msg_pos'] == 1) {
	$pages = ceil($var['fulltotal'] / $atmail->MsgNum);

	$num = $pages * $atmail->MsgNum;

	if ($_REQUEST['start'])
		$var['total'] = $var['fulltotal'] - $num;
}

if ($var['prev'])
	$var['msg_pos']++;

if ($_REQUEST['start']) {
	$start = $total - $_REQUEST['start'] - $atmail->MsgNum;
} else {
	if ($total < $atmail->MsgNum)
		$start = 0;
	else
		$start = $total - $atmail->MsgNum;
}

if ($total < $atmail->MsgNum)
	$end = $total;
else
	$end = $start + $atmail->MsgNum - 1;

if ($start < 0)
	$start = 0;

if ($end < $total)
    $end++;

$start++;


$emails = $mail->getmailboxsummary($start, $end, $var['folder']);

########################
// List messages from the newest to oldest
$emails = array_reverse($emails);

$id = $end;
$i = 1;

// keep track of cache ids so we don't
// have duplicates
$cacheIDs = array();

//Build message list
foreach ($emails as $email) {
	// Disable the message popup for external accounts
	if ($atmail->LoginType == 'simple' || $atmail->LoginType == 'xul' || $atmail->Ajax)
		$atmail->Advanced = '';

	$i++;

    // Load a hash containing the message headers
    //$db = $mail->gethead( $db['id'], $var['folder'], 9, $atmail->Advanced );

	// If we are reading from the local Maildir folders or SQL tables
	if ($mail->Mode == 'file' || $mail->Type == 'sql') {
		$id = $email['id'];
	} elseif (isset($email['UID'])) {
	    $id = $email['UID'];
	}

	$email = array_merge($email, $mail->message_headers($email['header'], $id));
	$email = $mail->cleanbody($email);

    // Create cache id for email. Added check for existing cache ID to help avoid any
	// clash where two emails have the same EmailUIDL e.g same address appears multiple
	// times in To, CC, or BCC headers
	// If cache id already exists we append '_$i' where $i is incremented until the cache
	// id is unique
	$email['EmailCache'] = $email['EmailUIDL'];

	if (in_array($email['EmailCache'], $cacheIDs)) {
	    $j = 2;
	    while (in_array("{$email['EmailCache']}_$j", $cacheIDs)) {
	        $j++;
	    }

        $email['EmailCache'] .= "_$j";
	}

	$cacheIDs[] = $email['EmailCache'];

	// Default to the normal message font when displaying a message
	if(!$email['msgclass'])
		$email['msgclass'] = "item";

    if ( $user['Advanced'] ) {
		// Take away any references to ' and " characters. They break the Jscript for the popup
        $email['EmailSubject'] = str_replace(array("'", '"', "\n", "\r"), '', $email['EmailSubject']);
    }

    if (!isset($email['folder']) || empty($email['folder'])) {
        $email['folder'] = $var['folder'];
    }


    // Clean the email headers , chop unnessasary chars and strip
    // HTML chars
    $email = $mail->clean_header(array_merge($email, array('TimeZone' => $atmail->TimeZone, 'LoginType' => $atmail->LoginType, 'Ajax' => $atmail->Ajax)));

	// If we are using the Ajax interface convert &'s to <>
	if ($atmail->Ajax || $atmail->XUL) {
		list($email['EmailSubject'], $email['EmailFrom'], $email['EmailTo'], $email['ReplyTo']) = str_replace( array('&gt;', '&lt;', ']]>', "\x04"), array('>', '<', '', ''), array($email['EmailSubject'], $email['EmailFrom'], $email['EmailTo'], $email['ReplyTo']));
	}

    // If using the sent folder, we want to display the To field
    if ( $var['folder'] == "Sent" )
    	$email['EmailFrom'] = $email['EmailTo'];

    // Choose which colour the next row is
    $var['class'] = ( $var['class'] == "item" ) ? "item2" : "item";
    $email['class']  = $var['class'];

    //if ($atmail->Mode == "file" && $atmail->MailType != "pop3" || $db['msgclass'] == "itemi")
    	//$email['id'] = $results[$i];

	// Add our custom class-name
	$email['ReadTag'] = str_replace('<img', "<img id=\"stat$id\"", $email['ReadTag']);

	// Message is moved class, from a filter or spam block
	if ( $email['msgclass'] == "itemi")
		$email['ReadTag'] = '<img src="imgs/xp/move.gif" width=16 height=12>';

	elseif ( preg_match('/unread.gif/i', $email['ReadTag'] ))
		$email['msgclass'] = "itemb";

	if ($_REQUEST['ajax']) {
		$email['ReadTag'] = preg_replace("/.*src=('|\").*\/(.*)\.gif('|\").*/", '$2', $email['ReadTag']);

		if( strpos($email['EmailAttach'], 'attachment.gif') !== false) {
    		$email['EmailAttach'] = '1';
		} else {
	    	$email['EmailAttach'] = '0';
		}
	}

	// Default style for the simple interface
	if ($atmail->LoginType == 'simple')
		$var['style'] = '_popup';

	// Display the email-row on the fly, if sorted using the default id field, and no mailbox sort
	// defined ( only for POP3/IMAP mail filters )
	if ($var['sort'] == 'id') {
	    echo $atmail->parse("html/$atmail->Language/$atmail->LoginType/emailentry{$var['style']}{$var['suffix']}.html",
	      array_merge($email, array('msgnum' => $i, 'count' => $var['count'], 'encfolder' => urlencode($email['folder']))));

        $args = array();
        $atmail->pluginHandler->triggerEvent('onEchoedEmailEntry', $args);
	} else {
		// Regular From/Subject field, sort via date
		if ($var['sort'] != "EmailDate")
			$h[$id] = $email[$var['sort']];
		else
			$h[$id] = $email['EmailDateEpoc'];

		$d[$id] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/emailentry{$var['style']}{$var['suffix']}.html",
			array_merge($email, array('msgnum' => $i, 'count' => $var['count'], 'encfolder' => urlencode($email['folder']))));

		$args = array();
        $atmail->pluginHandler->triggerEvent('onBufferedEmailEntry', $args);
	}

    $var['count']++;

	$id--;
}

if( $var['sortactive'] && $var['msgmove'] ) {

	# We are a POP3/IMAP acount, and a message filter has been detected
	# Refresh the mailbox list, because our sequence ID's are all out
	# of whack!

	print <<<EOF
	</form>
	<form method="post" action="showmail.php" name="reloadinbox">
	<input type="hidden" name="sort" value="{$var['sort']}">
	<input type="hidden" name="order" value="{$var['order']}">
	<input type="hidden" name="Folder" value="{$var['folder']}">
	<input type="hidden" name="start" value="{$var['msg_pos']}">
	<input type="hidden" name="msgend" value="{$var['msgend']}">
EOF;

	foreach($msgmove as $value) {
		if (!$value)
			continue;
		$value = serialize($value);
		print "<input type='hidden' name='msgmove[]' value='$value'>\n";
	}

	foreach($moved as $value) {
		if (!$value)
			continue;

		print "<input type='hidden' name='msgmove[]' value='$value'>\n";
	}

	print "</form>\n";
	print "<script>document.reloadinbox.submit();</script>";

}

// Next, sort through the mailbox and display the email rows
if($var['sort'] == "EmailSize") {
	// If size has MB, calculate the size in KB to avoid a mixup in the sort-order
	foreach($h as $k => $v) {
		if (strpos($v, 'MB') !== false) {
			$h[$k] = str_replace(' MB', '', $v);
			$h[$k] = round($v * 1024 * 1024);
			//$h[$k] .= " K";
		}

		elseif (strpos($v, 'K') !== false) {
			$h[$k] = str_replace(' K', '', $v);
			$h[$k] = round($v * 1024);
			//$h[$k] .= " K";
		}
	}

	if (strlen($var['order']) > 0) {
		// Sort the results from A - Z
		asort($h);
		foreach (array_keys($h) as $key)
			print $d[$key];
	} else {
		//Sort the results from Z - A
		arsort($h);
		foreach (array_keys($h) as $key)
			print $d[$key];
	}
} elseif ($var['sort'] != "id" || $var['sortactive'] && !$var['msgmove']) {
	// Sort from received date
	if ($var['order'] && $var['sort'] == "id") {
		// lower case the keys
		$tmp = array();
		foreach ($h as $k => $v)
			$tmp[$k] = $v;

		// Sort the results from A - Z
		asort($tmp);
		foreach (array_keys($tmp) as $key)
			print $d[$key];
	} elseif($var['sort'] == "id") {
		// lower case the keys
		$tmp = array();
		foreach ($h as $k => $v)
			$tmp[$k] = $v;

		// Sort the results from Z - A
		arsort($tmp);
		foreach (array_keys($tmp) as $key)
			print $d[$key];
	}

	// Sort from the specified order
	if($var['order'] && $var['sort'] != "id") {
		// lower case the keys
		$tmp = array();
		foreach ($h as $k => $v)
			$tmp[$k] = strtolower($v);

		// Sort the results from A - Z
		asort($tmp);
		foreach (array_keys($tmp) as $key)
			print $d[$key];

	} elseif($var['sort'] != "id") {
		// lower case the keys
		$tmp = array();
		foreach ($h as $k => $v)
			$tmp[$k] = strtolower($v);

		// Sort the results from Z - A
		arsort($tmp);
		foreach (array_keys($tmp) as $key)
			print $d[$key];
	}
}

// Display a blank page if no messages exist
if ( !$var['count'] ) {
	print $atmail->parse("html/$atmail->Language/$atmail->LoginType/emailentry_blank{$var['suffix']}.html", array('FolderName' => $mail->folder_select_lang( $var['folder'], $atmail->Language, 1 )));

	if ($atmail->XUL)
		$var['xulstyle'] = "<RDF:li resource=\"http://www.atmail.com/rdf/MESSAGES/0\" />\n";
}

$var['msgend'] = $msgmove[(count($msgmove) - 1)];

// Next, tag the selected messages as Spam and learn the results
if( $msgs[0] && $var['folder'] == "Spam" && $var['newfolder'] == "Inbox")
	$mail->learnspam_messages($var['newfolder'], "ham", $msgs);

elseif( $msgs[0] && $var['newfolder'] && $_REQUEST['Unspam'] )
	$mail->learnspam_messages($var['newfolder'], "ham", $msgs);

elseif( $msgs[0] && $var['newfolder'] == "Spam")
	$mail->learnspam_messages($var['newfolder'], "spam", $msgs);

print $atmail->parse("html/$atmail->Language/$atmail->LoginType/showmail_bottom{$var['suffix']}.html", $var);

// Quit gracefully from the session
$mail->quit();
$atmail->end();
