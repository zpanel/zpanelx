<?php

// +----------------------------------------------------------------+
// | search.php														|
// +----------------------------------------------------------------+
// | Function: Search a users Mailbox								|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Session.php');
require_once('Global.php');
require_once('GetMail.php');

session_start();

// Keep a storage of the months in a hash. Used to reformat the search date
$month = array(
  '01'  => 'Jan',
  '02'  => 'Feb',
  '03'  => 'Mar',
  '04'  => 'Apr',
  '05'  => 'May',
  '06'  => 'Jun',
  '07'  => 'Jul',
  '08'  => 'Aug',
  '09'  => 'Sep',
  '10'  => 'Oct',
  '11'  => 'Nov',
  '12'  => 'Dec'
);

// Load which function we run
if(isset($_REQUEST['func']))
$func = $_REQUEST['func'];
else
$func = '';

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();

$atmail->httpheaders();

$atmail->status = $auth->getuser($atmail->SessionID);
$atmail->username = $auth->username;
$atmail->pop3host = $auth->pop3host;

// check for language version
if (!$atmail->Language)
	$atmail->Language = $pref['Language'];

// Load the account preferences
$atmail->loadprefs();

// If using the XUL interface, toggle to use the XP HTML templates
if ($atmail->LoginType == 'xul')
{
	$atmail->LoginType = 'xp';
	if (!$func)
		$func = 'start' ;
}

if ($func != 'searchhelp')
{
	// Print the error screen if the account has auth errors, or session timeout.
	if ( $atmail->status == 1 )
		$atmail->auth_error();
	if ( $atmail->status == 2 )
		$atmail->session_error();
}

if (!$atmail->Langage && !$atmail->LoginType)
{
	$atmail->Language = $settings['Language'];
	$atmail->LoginType = 'xp';
}

$var['newfolder'] = $_REQUEST['NewFolder'];

$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css" );
$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-form.css");
$var['mailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-mail.css");


// Make a new mail object, used to search and list the users folders
$mail = new GetMail(array(
  'Username' => $atmail->username,
  'Pop3host' => $atmail->pop3host,
  'Password' => $auth->password,
  'Type'     => $atmail->MailType,
  'Mode'     => $atmail->Mode)
);

// Load an array of msgs selected to be moved
$msgs = $_REQUEST['id'];

// If a value exists in the array, start to move the messages.
if ($msgs[0])
{
	$mail->login();

    // Loop through the selected msgs to move, the new folder to move to
    // is seperated by :: . e.g 56::Trash , msg 56 from the Trash folder
    foreach ($msgs as $id)
	{
        if (preg_match('/::(.*)/', $id, $m))
			$folder = $m[1];

		$id = preg_replace('/::.*/', '', $id);

        // Don't move messages if we are already in the same folder
        if ( $folder == $var['newfolder'] )
			continue;

        $mail->move( $id, $folder, $var['newfolder'] );

        $var['move']++;
    }

    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/searchmove.html", $var );
	$atmail->end();
}

// apend 'search_' to the function name passed via $_REQUEST so that
// user cannot call some arbitrary function such as 'phpinfo()'
$func = 'search_'.$_REQUEST['func'];

// check that the requested function exists then call it
if (function_exists($func))
	$func();
else
	die("the function <b>'$func'</b> is not defined");

$atmail->end();

//
// Start function definitions
//

function search_searchhelp()
{
	global $pref, $atmail;

	$h = $search = $ignore = array();

	$ignore["filexp.html"]++;
	$ignore["search_match.html"]++;
	$ignore["search.html"]++;
	$ignore["index.html"]++;
	$ignore["tipsjscript.html"]++;

	// Toggle the permissions on which help files a user can see
	if (!$pref['allow_SMS']) $ignore["sms.html"]++;
	if (!$pref['allow_LDAP'])
	{
		$ignore["ldap.html"]++;
		$ignore["ldap_faq.html"]++;
	}
	if (!$pref['allow_MultiAccounts'])
	{
		$ignore["addpop3.html"]++;
		$ignore["addpop3_faq.html"]++;
	}
	if (!$pref['allow_Calendar'])
	{
		$ignore["calendar.html"]++;
		$ignore["cal_faq.html"]++;
	}
	if (!$pref['allow_Sync']) $ignore["sync.html"]++;
	if (!$pref['GlobalAbook']) $ignore["calshared.html"]++;
	if (!$pref['allow_AbookImportExport']) $ignore["import.html"]++;
	if (!file_exists("{$pref['install_dir']}/xhtml.php")) $ignore["wap.html"]++;

	$var['keywords'] = $_REQUEST['keywords'];

	$dh = opendir("{$pref['install_dir']}/html/$atmail->Language/help/");
	$words = explode(' ', $var['keywords']);

	while(false !== $file = readdir($dh))
	{
		if (!preg_match('/\.html$/', $file) || $ignore[$file])
			continue;

		$fh = fopen("{$pref['install_dir']}/html/$atmail->Language/help/$file", 'r');

		while (false !== $line = fgets($fh))
		{
			if (preg_match('/<title>(.*?)<\/title>/i', $line, $m))
				$h[$file]['title'] = $m[1];

			if (strpos($line, '<') !== false)
				$skip = 1 ;
			if (strpos($line, '>') !== false)
				$skip = 0 ;

			if ($skip)
				continue;

			if (preg_match('/<p>(.*)<\/p>/i', $line, $m))
				$h[$file]['head'] = $m[1];

			if (preg_match('/<ul>(.*)<\/ul>/i', $line, $m) && !$h[$file]['head'])
				$h[$file]['head'] = $m[1];

			foreach ($words as $word)
			{
				$word = preg_quote($word, '/');
				if (preg_match("/$word/i", $line))
					$search[$file]++ ;
			}

			$i++;
		}

		fclose($fh);
	}

	$matches = array_keys($search);
	sort($matches);
	$p = $search[$matches[0]];

	foreach ($matches as $m)
	{
		$percent = intval($search[$m] / $p * 100);
		if ($percent < 25)
			continue;

		$text = '';

		$fh = fopen("{$pref['install_dir']}/html/$atmail->Language/help/$m", 'r');

		while(false !== $line = fgets($fh))
		{
			$line = preg_replace('/<.*>/', '', $line);
			$text = str_replace('&nbsp;', '', $text);

			foreach ($words as $w)
			{
				if (strpos($line, $w) !== false)
					$text .= $line;
			}
		}

		$text = substr($text, 0, 200) . "...";
		fclose($fh);

		$size = filesize("{$pref['install_dir']}/html/$atmail->Language/help/$m");
		$size = intval($size / 1024) . "K";

		$var['matches'] .= $atmail->parse("html/$atmail->Language/help/search_match.html", array('Title' => $h[$m]['title'], 'Percent' => $percent, 'Text' => $text, 'File' => $m, 'Size' => $size));
	}

	if (!$var['matches'])
		$var['matches'] = "Your search shows 0 results . Try again";

	print $atmail->parse("html/$atmail->Language/help/search.html", array('matches' => $var['matches'], 'Keywords' => $var['keywords']));
	$atmail->end();
}


function search_searchframe()
{
	global $atmail, $var;

	$var['DefaultFolder'] = $_REQUEST['DefaultFolder'];
	$var['DefaultFrom'] = $_REQUEST['DefaultFrom'];

	// Just extract the email address
	$emailexp = '([^":\s<>()\/;]*@[^":\s<>()\/;]*)';
	if (preg_match("/$emailexp/", $var['DefaultFrom'], $m))
		$var['DefaultFrom'] = $m[1];

    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/searchframe.html", $var);
}


function search_search()
{
	global $var, $atmail, $mail;

	$var['DefaultFolder'] = $_REQUEST['DefaultFolder'];
	$var['DefaultFrom'] = $_REQUEST['DefaultFrom'];

    // Fetch an array containing our mailboxes
    $folders = $mail->listfolders();

    // Create a select box . No searching of the Inbox if remote pop3, since
    // the messages are stored on the remote server. Searching too slow
    if ( $atmail->MailType == 'pop3' )	{
        $var['folder_select'] = $mail->folder_select('', $folders, false);
	// Take out the Inbox, cannot search in POP3 mode. Make the Sent folder the default folder to search
		$var['folder_select'] = preg_replace('/<option value="Inbox">.*?<\/option>/', '', $var['folder_select']);
		if($var['DefaultFolder'] == 'Inbox' || !$var['DefaultFolder'])
		$var['DefaultFolder'] = 'Sent';
	}
    else
        $var['folder_select'] = $mail->folder_select('', $folders, false);

    $var['folder_select'] = $mail->folder_select_lang($var['folder_select'], $atmail->Language);

	// Take away the erase option for the search menu
	//$var['folder_select'] = preg_replace('/<option value="erase">.*?<\/option>/', '', $var['folder_select']);
	//$var['folder_select'] = preg_replace('/<option value="" style=\'color: gray;\'>.*?<\/option>/', '', $var['folder_select']);
	//$var['folder_select'] = preg_replace('/<option value=\'\'>.*?<\/option>/', '', $var['folder_select']);

	// Select our default folder name ( e.g when a user right-clicks on a folder to search )
	if ($var['DefaultFolder'])
		$var['folder_select'] = str_replace("<option value=\"{$var['DefaultFolder']}\"", "<option value=\"{$var['DefaultFolder']}\" selected", $var['folder_select']);
	else
		$var['folder_select'] = str_replace('<option value="Inbox"', '<option value="Inbox" selected', $var['folder_select']);

	// insert default values for Received After selects
	$after_date = strtotime("-61 days",time());
	$after_day = date("d", $after_date);
	$after_month = date("m", $after_date);
	$after_year = date("y", $after_date);

    $result = $atmail->parse("html/$atmail->Language/$atmail->LoginType/search.html", $var);
    $result = preg_replace("/(<select name=\"AfterDay\".*)<option value=\"$after_day\"/", "\$1<option value=\"$after_day\" selected", $result);
    $result = preg_replace("/(<select name=\"AfterMonth\".*)<option value=\"$after_month\"/", "\$1<option value=\"$after_month\" selected", $result);
    $result = preg_replace("/(<select name=\"AfterYear\".*)<option value=\"$after_year\"/", "\$1<option value=\"$after_year\" selected", $result);
    print $result;
}


function search_start()
{
	global $atmail, $var, $mail, $month;

    if ( $_REQUEST['All'] )
		$EmailBox = '';
	else
		$EmailBox = $_REQUEST['EmailBox'];

    $EmailMessage = $_REQUEST['EmailMessage'];
	$EmailSubject = $_REQUEST['EmailSubject'];
	$EmailAttach  = $_REQUEST['EmailAttach'];
	$EmailFlag = $_REQUEST['EmailFlag'];

	// Cleanup the EmailTo & EmailFrom fields . Remove spaces from the front / back
    $EmailTo = trim($_REQUEST['EmailTo']);
    $EmailFrom = trim($_REQUEST['EmailFrom']);

    // Fetch the dates, if supplied
    $BeforeDay   = $_REQUEST['BeforeDay'] ? $_REQUEST['BeforeDay'] : '31';
    $BeforeMonth = $_REQUEST['BeforeMonth'] ? $_REQUEST['BeforeMonth'] : '12';
    $BeforeYear  = $_REQUEST['BeforeYear'] ? $_REQUEST['BeforeYear'] : date('y');

    $AfterDay   = $_REQUEST['AfterDay'] ? $_REQUEST['AfterDay'] : '01';
    $AfterMonth = $_REQUEST['AfterMonth'] ? $_REQUEST['AfterMonth'] : '01';
    $AfterYear  = $_REQUEST['AfterYear'] ? $_REQUEST['AfterYear'] : '00';

	if ($atmail->MailType == 'imap')
	{
		// Set default dates required for the IMAP search
		$BeforeMonth = $month[$BeforeMonth] ? $month[$BeforeMonth] : 'Jan';
		$AfterMonth = $month[$AfterMonth] ? $month[$AfterMonth] : 'Jan';

		// Don't specify a date if the user has selected the default values
		$before = "$BeforeDay-$BeforeMonth-20$BeforeYear";
		if ( $before == "31-Dec-2009")
			$before = '';

		// Don't specify a date if the user has selected the default values
		$after = "$AfterDay-$AfterMonth-20$AfterYear";
		if ($after == "01-Jan-2000")
		{
			$after = "";
		}


		// Query the IMAP server for the search results
	    $id = $mail->search(array(
	      'SUBJECT'      => $EmailSubject,
	      'TO'           => $EmailTo,
	      'FROM'         => $EmailFrom,
		  'EmailBox'     => $EmailBox,
	      'EmailAttach'  => $EmailAttach,
	      'BODY'	     => $EmailMessage,
	      'FLAGGED'      => $EmailFlag,
	      'BEFORE'       => $before,
	      'SINCE'        => $after)
	    );

		$results = array();

		foreach ($id as $i)
		{
			list($folder, $seq_no) = explode('::', $i);
			$results[$folder] .= "$seq_no,";
		}

		foreach ($results as $k => $v)
		{
			$sizes[$k] = $mail->mailer->listmailsizes(trim($v, ','), $k);
		}

		$var['fulltotal'] = $mail->mailer->stat(0);
	}
	else
	{
		$id = $mail->search(array(
		  'EmailFlag'    => $EmailFlag,
	      'EmailSubject' => $EmailSubject,
	      'EmailTo'      => $EmailTo,
	      'EmailFrom'    => $EmailFrom,
	      'EmailAttach'  => $EmailAttach,
	      'EmailBox'     => $EmailBox,
	      'EmailMessage' => $EmailMessage,
	      'DateBefore'   => "$BeforeYear$BeforeMonth$BeforeDay",
	      'DateAfter'    => "$AfterYear$AfterMonth$AfterDay")
	    );

		// Optionally search a users POP3 Inbox ( can be slow )
		if ($mail->Type == "pop3" && $EmailBox == "Inbox")
		{
			$id = array_merge($id, $mail->search_pop3(array(
			  'EmailSubject' => $EmailSubject,
			  'EmailTo'      => $EmailTo,
			  'EmailFrom'    => $EmailFrom,
			  'EmailAttach'  => $EmailAttach,
			  'EmailBox'     => $EmailBox,
			  'EmailMessage' => $EmailMessage,
			  'DateBefore'   => $BeforeYear.$BeforeMonth.$BeforeDay,
			  'DateAfter'    => $AfterYear.$AfterMonth.$AfterDay)
			));

			// Retrieve a list of mailbox sizes
			$size = $mail->list_("Inbox");
			$var['fulltotal'] = count($size) + 1;
		}
	}

    $folders = $mail->listfolders();
    $var['folderbox'] = $mail->folder_select($var['folder'], $folders);

    //$var['nrrecords'] = count($id);

	if ($_REQUEST['ajax'])
		print $atmail->parse("html/$atmail->Language/$atmail->LoginType/showmail_popup_ajax.html", $var);

    // Add a jscript prompt on the status-bar
    $var['status'] = $atmail->parse("html/$atmail->Language/msg/searchnr.html", $var);

	if (is_array($id))
	{
	    foreach ($id as $i)
		{
	        preg_match('/(.*?)::(.*)/', "$i", $m);
	        $folder = $m[1];
	        $i = $m[2];

			// Skip if searching an SQL box via POP3, and SQL query return messages in Inbox ( since they are on pop3 )
			if ($atmail->MailType == 'pop3' && $EmailBox != 'Inbox' && $folder == 'Inbox')
				continue;

			$var['nrrecords']++;
			$EmailBox = $folder;

			// Load a hash containing the message headers
	        $db = $mail->gethead($i, $EmailBox, 9);

			if ( $atmail->MailType == "pop3" ||  $atmail->MailType == "imap" )
				$db['EmailCache'] = $db['EmailUIDL'];

	        $db['id'] = $i;

			// Find the size of the message if searching a POP3 mailbox
			if ($atmail->MailType == "pop3" && $size[$i])
			{
				$db['EmailSize'] = $size[$i];
			}
			elseif ($atmail->MailType == 'imap')
			{
				$db['EmailSize'] = $sizes[$folder][$i];
				
				// Get the message flag for IMAP search
				$db['UIDL'] = $mail->mailer->mailer->getFlags($i, true);
			}

	        $db = $mail->clean_header($db, array('TimeZone' => $atmail->TimeZone));

	        $db['folder'] = $db['EmailBox'] = $EmailBox;

	        // If using the sent folder, we want to display the To field
	        if ( $EmailBox == "Sent" && !$var['jscript_sent'] )
			{
	            $db['EmailFrom'] = $db['EmailTo'];

				$var['jscript_sent'] .= <<<EOF
oForm = top.main.topFrame.document.sent
if(oForm == null)       {
top.main.topFrame.location.href='html/$atmail->Language/xp/heading/mail_searchsent.html';
}
EOF;

	        }

			elseif (!$var['jscript_sent'] )
            {
				$var['jscript_sent'] .= <<<EOF
oForm = top.main.topFrame.document.mail
// If not, change the frame to the mailhead
if(oForm == null)       {
top.main.topFrame.location.href='html/$atmail->Language/$atmail->LoginType/heading/mail_search.html';
}
EOF;
            }

	        // Choose which colour the next row is
	        $var['class'] = ( $var['class'] == "item" ) ? "item2" : "item";
	        $db['class']  = $var['class'];

			if ($_REQUEST['ajax'])
			{
				// If we are using the Ajax interface, fix the headers.
				$db = $mail->fixheaders_ajax($db);
				$db['folder'] = $db['EmailBox'];

				print $atmail->parse("html/$atmail->Language/$atmail->LoginType/emailentry_popup_ajax.html", $db);
			}
			else
				$var['emails'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/searchentry.html", $db);
				
                    $args = array($db['EmailUIDL']);
                    $atmail->pluginHandler->triggerEvent('onAddEmailSearchResult', $args); 
	}
}

    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
        $var['browser'] = "ie";
    else
        $var['browser'] = "ns";

	if ($_REQUEST['ajax'])
	{
		// Display a blank page if no messages exist
		if ( !$var['nrrecords'] )
		{
			print $atmail->parse(
			"html/$atmail->Language/$atmail->LoginType/emailentry_blank_ajax.html",
			array('FolderName' => "Search"));
		}

		$var['search'] = 1;
		if (!$var['start'])
			$var['start'] = '0';

		print $atmail->parse("html/$atmail->Language/$atmail->LoginType/showmail_bottom_ajax.html", $var);
	}
	else
	{
		// Add a jscript prompt on the status-bar
		$var['status'] = $atmail->parse( "html/$atmail->Language/msg/searchnr.html", $var);
		print $atmail->parse("html/$atmail->Language/$atmail->LoginType/searchresults.html", $var);
	}

	// Close the IMAP / POP3 connection
	$mail->quit();
}

?>
