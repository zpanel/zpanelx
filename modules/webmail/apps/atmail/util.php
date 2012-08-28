<?php
// +----------------------------------------------------------------+
// | util.php														|
// +----------------------------------------------------------------+
// | Function: Folder Manager - View/Modify/Delete account folders  |
// | Used to view and save account preferences/settings				|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Session.php');
require_once('Global.php');
require_once('GetMail.php');
require_once('Language.php');

session_start();

// get the requested function
$var['func'] = $_REQUEST['func'];
$func = "util_{$var['func']}";

if (!function_exists($func)) {
	$func = str_replace("<", "&lt;", $func);
	$func = str_replace(">", "&gt;", $func);
	die("requested function '$func' does not exist");
}

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();

$var = array();

$atmail->status = $auth->getuser( $atmail->SessionID );

$atmail->username = $auth->username;
$atmail->pop3host = $auth->pop3host;

// Print the error screen if the account has auth errors, or session timeout.
if ( $atmail->status == 1 )
	$atmail->auth_error();
if ( $atmail->status == 2 )
	$atmail->session_error();

// Load the account preferences
$atmail->loadprefs(1);

if ($var['func'] != 'rename' && $var['func'] != 'delfolderxp')
	$atmail->httpheaders();

$var['languagebox'] = $atmail->languages(2);
$var['languagebox'] = str_replace("value='$atmail->Language'", "value='$atmail->Language' selected", $var['languagebox']);
$var['languagebox'] = str_replace("<select", "<select class=\"select\"", $var['languagebox']);

$mail = new GetMail(array(
  'Username' => $atmail->username,
  'Pop3host' => $atmail->pop3host,
  'Password' => $auth->password,
  'Mode'     => $atmail->Mode,
  'Type'     => $atmail->MailType)
);


$var['atmailstyle'] = $atmail->parse( "html/$atmail->Language/$atmail->LoginType/atmailstyle.css" );
$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-form.css");

include('snippets/quota_bar.php');

// now call the requested function
$func();

$atmail->end();


function util_info()
{
	global $mail, $atmail, $var, $domains;

    $mail->login();
	$reload = false;

	// We keep the folder info in session to speed up page loading as
    // when we have large folders this can take a while. Data stays valid
    // for 15 minutes
    //if (isset($_SESSION['folderinfo']) && ($_SESSION['folderinfo']['created'] < (time() - 900))) {
    //	unset($_SESSION['folderinfo']);
    //}

	$var['delim'] = $mail->Deliminator;
	$curname = urldecode($_REQUEST['curname']);
	
	if (isset($_REQUEST['renamefolder']) && !empty($curname) && !empty($_REQUEST['foldername'])) {
		if ($curname != 'Sent' && $curname != 'Trash' &&
		    $curname != 'Drafts' && $curname != 'Spam') {

		    // Cannot change parent name
		    if (false !== $pos = strrpos($curname, $mail->Deliminator)) {
    		    $parent = substr($curname, 0, $pos);
    		    $new_folder = $parent . $mail->Deliminator . $_REQUEST['foldername'];
		    } else {
		        $new_folder = $_REQUEST['foldername'];
		    }

			$mail->renamefolder(str_replace('/', $mail->Deliminator, $curname),
			                    str_replace('/', $mail->Deliminator, $new_folder));
			$reload = true;
			//$_SESSION['folderinfo'][$new_folder] = $_SESSION['folderinfo'][$_REQUEST['curname']];
			//unset($_SESSION['folderinfo'][$_REQUEST['curname']]);
		}
	}

    // The user selects to create a new mailbox
    if ( $_REQUEST['creatembox'] )
    {
		$folder = $_REQUEST['foldername'];
        $mail->newfolder($folder);

        // Add the folder to the session data if required
        //if (isset($_SESSION['folderinfo']) && !preg_match('^(inbox|spam|trash|drafts|sent)$/i', $folder)) {
        //	$_SESSION['folderinfo'][$folder]['msgs'] = 0;
        //	$_SESSION['folderinfo'][$folder]['size'] = 0;
		//}

		$reload = true;
    }

    // A folder is set to delete
    elseif ( $_REQUEST['delete'] )
    {
		$del = urldecode($_REQUEST['delete']);

		if ($_REQUEST['purge'] == 1) {
			$mail->purgefolder($del);
		} else {
			$mail->delfolder($del);
		}

        // Update the folder data in the session if required
        //if (isset($_SESSION['folderinfo']) && preg_match('/^(inbox|spam|trash|drafts|sent)$/i', $del)) {
        //	$_SESSION['folderinfo'][$del]['msgs'] = 0;
        //	$_SESSION['folderinfo'][$del]['size'] = 0;
		//} elseif (isset($_SESSION['folderinfo'])) {
        //	unset($_SESSION['folderinfo'][$del]);
        //	unset($_SESSION['folderinfo'][$del]);
		//}


		if ($_REQUEST['purge'] != 1 && $del != 'Trash' && $del != 'Spam')
		{
			// Delete any message filters that match the foldername
			$sort_subject = $atmail->getsort( 'EmailSubject', 'hash' );
			foreach ($sort_subject as $k => $v)
			{
				if ($v == $del)
					$atmail->delsort($k, 'EmailSubject') ;
			}

			$sort_email   = $atmail->getsort( 'EmailAddress', 'hash' );
			foreach ($sort_email as $k => $v)
			{
				if ($v == $del)
					$atmail->delsort($k, 'EmailAddress');
			}
		}
		$reload = true;
    }

    // Add a new Email to the Spam List
    elseif ( $_REQUEST['spamadd'] )
        $atmail->addspamer();

    // Delete an address from the SpamList
    elseif ( $_REQUEST['spamdel'] )
        $atmail->delspamer();

    // Add a new rule to the mail filters
    elseif ( $_REQUEST['addsort'] )
        $atmail->addsort();

    elseif ( $_REQUEST['delsort'] )
        $atmail->delsort( base64_decode($_REQUEST['delete_sort']), $_REQUEST['type'] );

    // Find the folder names , size and number of messages
    $folders = $mail->listfolders();

	// sort the folders by name
	$folders = GetMail::_sort_folders($folders);

    // Make a select box with the foldernames
    $var['folderbox'] = $var['renamefolderbox'] = $mail->folder_select( 'Inbox', $folders, false);

    // Remove System folders from rename options
    $patterns = array("<option value=\"Trash\">Trash</option>",
                      "<option value=\"Spam\">Spam</option>",
                      "<option value=\"Drafts\">Drafts</option>",
                      "<option value=\"Sent\">Sent</option>");
    $var['renamefolderbox'] = str_replace($patterns, '', $var['renamefolderbox']);
    $var['renamefolderbox'] = '<option value="">--</option>' . $var['renamefolderbox'];

	$var['folderbox'] = str_replace("value=\"Trash\"", "value=\"Trash\" selected", $var['folderbox']);
	//$var['folderbox'] = preg_replace('/<option value="erase">.*?<\/option>/', '', $var['folderbox']);
	//$var['folderbox'] = preg_replace('/<option value="" style=\'color: gray;\'>.*?<\/option>/', '', $var['folderbox']);

	// Translate the mailbox folders into another language
	$var['folderbox'] = Language::folder_language( $var['folderbox'], $atmail->Language, null);

	//list($var['usedquota'], $var['totalquota']) = $mail->getquota();

    // The size of our quota in Kb
    /*
	if (!$var['totalquota'])
	{
		$var['totalquota'] = $atmail->UserQuota;
		$var['totalquota'] = sprintf("%2.0f", $var['totalquota']);
	}

	if ($var['usedquota'] > 0 && $var['totalquota'] > 0)
	{
		$var['used'] = ( $var['usedquota'] / $var['totalquota']) * 100;

		if ($var['used'] < 1)
			$var['used'] = '1%';
		else
			$var['used'] = round($var['used'], 2) . '%';
	}

	$var['used_percent'] = $var['used']? $var['used'] : '1%';
	*/

    foreach ($folders as $folder)
    {
        // Retrieve the foldername without any / extension
		$folderlink = $mail->folder_getlink($folder);

        list( $num, $size ) = $mail->sizefolder($folderlink);

		// Get the data from the session if it exists
		//if (isset($_SESSION['folderinfo'][$folder]) && $_SESSION['folderinfo'][$folder]['msgs'] > 0) {
		//	$num = $_SESSION['folderinfo'][$folder]['msgs'];
		//	$size = $_SESSION['folderinfo'][$folder]['size'];
		//} else {
		// Implement cache function soon - Read the size for the moment
        //	list( $num, $size ) = $mail->sizefolder($folderlink);
        //	$_SESSION['folderinfo'][$folder] = array('msgs' => $num, 'size' => $size);
        //	$_SESSION['folderinfo']['created'] = time();
		//}

		// Skip Inbox for the POP3 protocol
        if ( strtoupper($folder) == 'INBOX' && !$domains[$atmail->pop3host] && $atmail->MailType == 'pop3' )
        	continue;

        // Change the display language, depending on the user language login
        $foldername = Language::folder_language( $folder, $atmail->Language, 1 );

        $var['folders'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/foldertr.html", array(
          'Folder'     => $folder,
		  'FolderLink' => $folderlink,
          'FolderName' => $foldername,
          'Size'       => $size,
          'MsgNum'     => $num,
          'SystemFolder' => $mail->isAtmailFolder($folder)
        ));
    }

    // Make a select box with our list of spammers
    //$var['spam_select'] = $atmail->spam_select();
    $var['sort_email']  = $atmail->getsort('EmailAddress');
    $var['sort_subj']   = $atmail->getsort('EmailSubject');


	// Translate the mailbox folders into another language
	$var['sort_email'] = $mail->folder_select_lang( $var['sort_email'], $atmail->Language, 1 );
	$var['sort_subj'] = $mail->folder_select_lang( $var['sort_subj'], $atmail->Language, 1 );


	$var['reloadFolders'] = (isset($_REQUEST['noreload']) || $reload == false) ? 'false' : 'true';

    print $atmail->parse( "html/$atmail->Language/$atmail->LoginType/folders.html", $var );
}

function util_settings()
{
	global $pref, $atmail, $var;

    if ( isset($_POST['save']) )
    {
		$atmail->savesettings();
		$atmail->loadprefs();
    }

	$var['Tab'] 		= $_REQUEST['Tab'];

    // Plesk Autoreply/forward code
	if(false && $pref['plesk'] == 1) //currently disabled
	{
		$previous_sql_user = $pref['sql_user'];
		$previous_sql_pass = $pref['sql_pass'];
		$previous_sql_table = $pref['sql_table']; //table ~= database
		//set psa db settings
		$pref['sql_user'] = 'admin';
		$pref['sql_pass'] = file_get_contents('');
		$pref['sql_table'] = 'psa';
		$atmail->Global_Base(); //creates temporary psa database connection

		$q =   "SELECT `mail.id`, `mail.redirect`, `mail.redir_addr`, `mail_resp.resp_on`, `mail_resp.text`
				FROM `mail`
				LEFT JOIN `domains` ON `domains.id` = `mail.dom_id`
				LEFT JOIN `mail_resp` ON `mail_resp.mn_id` = `mail.id`
				WHERE `mail.mail_name` = ? AND  `domains.name` = ?";
		echo '<br />' . $q . '<br />';
		$d = array(
			$atmail->username,
			$atmail->pop3host
			);
		$arr = $this->db->sqlmultihash($query, $data);
        echo '<pre>';
		print_r($arr);
		echo '</pre>';
		$atmail->AutoReply = '';
		$atmail->Forward = '';
        if(count($arr) > 0)
		{
			if($arr[0]['redirect'] == 'true')
				$atmail->AutoReply = $arr[0]['redir_addr'];
			if($arr[0]['resp_on'] == 'true')
				$atmail->Forward = $arr[0]['text'];
		}
		//now recreate origional db link
		$pref['sql_user'] = $previous_sql_user;
		$pref['sql_pass'] = $previous_sql_pass;
		$pref['sql_table'] = $previous_sql_table;
		$this->Global_Base(); //creates origional database connection
	}

    $settings = $atmail->parse( "html/$atmail->Language/$atmail->LoginType/settings.html", $var );

	#$settings =~ s/<option value="$atmail->UserLanguage"/<option value="$atmail->UserLanguage" selected/g;

    $settings = str_replace("<option value=\"$atmail->MboxOrder\"", "<option value=\"$atmail->MboxOrder\" selected", $settings);
    $settings = str_replace("<option value=\"$atmail->EmailHeaders\"", "<option value=\"$atmail->EmailHeaders\" selected", $settings);


    if ( $atmail->TimeZone != '' )
    {
		$tz = $atmail->TimeZone;
        $settings = str_replace("<option value=\"$tz\"", "<option value=\"$tz\" selected", $settings);
    }

    $settings = str_replace("<option value=\"$atmail->MsgNum\"", "<option value=\"$atmail->MsgNum\" selected", $settings);
    $settings = str_replace("<option value=\"$atmail->FontStyle\"", "<option value=\"$atmail->FontStyle\" selected", $settings);
    $settings = str_replace("<option name='Refresh' value=\"$atmail->Refresh\"", "<option name='Refresh' value=\"$atmail->Refresh\" selected", $settings);
    $settings = str_replace("<option name='DisplayImages' value=\"$atmail->DisplayImages\"", "<option name='DisplayImages' value=\"$atmail->DisplayImages\" selected", $settings);

	$settings = str_replace("<option value=\"$atmail->Language\"", "<option value=\"$atmail->Language\" selected", $settings);
    $settings = str_replace("<option name='LeaveMsgs' value=\"$atmail->LeaveMsgs\">Yes", "<option value=\"$atmail->LeaveMsgs\" selected>Yes", $settings);
	$settings = str_replace("<option name='LeaveMsgs' value=\"$atmail->LeaveMsgs\">No", "<option value=\"$atmail->LeaveMsgs\" selected>No", $settings);
    $settings = str_replace("<option value=\"$atmail->TimeFormat\"", "<option value=\"$atmail->TimeFormat\" selected", $settings);
	$settings = str_replace("<option value=\"$atmail->DateFormat\"", "<option value=\"$atmail->DateFormat\" selected", $settings);
	$settings = str_replace("<option value=\"$atmail->EmailEncoding\"", "<option value=\"$atmail->EmailEncoding\" selected", $settings);

	if ( $atmail->EmptyTrash )
    	$settings = str_replace('name="EmptyTrash"', 'name="EmptyTrash" checked', $settings);

    if ( $atmail->HtmlEditor )
    	$settings = str_replace('name="HtmlEditor"', 'name="HtmlEditor" checked', $settings);

    if ( $atmail->NewWindow )
    	$settings = str_replace('name="NewWindow"', 'name="NewWindow" checked', $settings);

    if ( $atmail->AutoTrash )
    	$settings = str_replace('name="AutoTrash"', 'name="AutoTrash" checked', $settings);

    if ( $atmail->Advanced )
    	$settings = str_replace('name="Advanced"', 'name="Advanced" checked', $settings);

    if ( $atmail->PGPappend )
    	$settings = str_replace('name="PGPappend"', 'name="PGPappend" checked', $settings);

	if ( $atmail->PGPsign )
		$settings = str_replace('name="PGPsign"', 'name="PGPsign" checked', $settings);

	if ( $atmail->SMIMEencrypt );
		$settings = str_replace('name="SMIMEencrypt"', 'name="SMIMEencrypt" checked', $settings);

	if ( $atmail->SMIMEsign )
		$settings = str_replace('name="SMIMEsign"', 'name="SMIMEsign" checked', $settings);

	if ( $atmail->AutoComplete )
		$settings = str_replace('name="AutoComplete"', 'name="AutoComplete" checked', $settings);

	$settings = str_replace("<option value=\"$atmail->PGPenable\">PGP", "<option value=\"$atmail->PGPenable\" selected>PGP", $settings);
	$settings = str_replace("<option value=\"$atmail->PGPenable\">S/MIME", "<option value=\"$atmail->PGPenable\" selected>S/MIME", $settings);

	$settings = preg_replace("/<option value=\"$atmail->PKIenable\">(Enabled|Disabled)/", "<option value=\"$atmail->PKIenable\" selected>\$1", $settings);
	//$settings = str_replace("<option value=\"$atmail->PKIenable\">Disabled", "<option value=\"$atmail->PKIenable\" selected>Disabled", $settings);

	if ($refresh)
		print $refresh;
	else
		print $settings;
}


// About page for @Mail - Used in XP interface
function util_about()
{
	global $atmail, $var;
    print $atmail->parse("html/$atmail->Language/about.html", array('os' => PHP_OS, 'atmailstyle' => $var['atmailstyle']));
}

function util_profile()
{
	global $atmail, $var, $pref;

    if ( $_REQUEST['save'] )
    {
        $var['status'] = $atmail->parse("html/$atmail->Language/msg/updated.html");
        $atmail->saveprofile();
    }

    $user = $atmail->getprofile();

    $settings = $atmail->parse("html/$atmail->Language/$atmail->LoginType/profile.html", array_merge($user, $var));
    $settings = str_replace("<option value=\"{$user['Gender']}\"", "<option value=\"{$user['Gender']}\" selected", $settings);
    $settings = str_replace("<option value=\"{$user['Industry']}\"", "<option value=\"{$user['Industry']}\" selected", $settings);
    $settings = str_replace("<option value=\"{$user['Occupation']}\"", "<option value=\"{$user['Occupation']}\" selected", $settings);
    $settings = str_replace("<option value=\"{$user['Country']}\"", "<option value=\"{$user['Country']}\" selected", $settings);
    $settings = preg_replace("/name=\"BirthDay\"(.*?)<option value=\"{$user['BirthDay']}\"/", 'name="BirthDay" $1'." <option value=\"{$user['BirthDay']}\" selected", $settings);
    $settings = preg_replace("/name=\"BirthMonth\"(.*?)<option value=\"{$user['BirthMonth']}\"/", 'name="BirthMonth" $1'." <option value=\"{$user['BirthMonth']}\" selected", $settings);

    print $settings;
}

function util_logout()
{
	global $atmail, $mail;

    if ( $atmail->EmptyTrash )
    {
        $mail->delfolder('Trash');
    }
    print "<html><head></head><body><script language='javascript'> location.href='index.php?func=logout'</script></body></html>";
	$atmail->end();
}


?>
