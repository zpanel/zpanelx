<?php
// +----------------------------------------------------------------+
// | abook.php								                        |
// +----------------------------------------------------------------+
// | Function: Function: User Addressbook Utility . 			    |
// | Add / Edit / Delete users in the address-book			        |
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+


require_once('header.php');
require_once('Session.php');
require_once('Global.php');
require_once('Abook.class.php');
require_once('GetMail.php');

session_start();

// Set some override for $abook->limit
// useful for contact pane in adv int
// and select list in group creation
$abookLimitOverride = (is_numeric($pref['AbookLimitOverride'])) ? $pref['AbookLimitOverride'] : '99999';

$atmail = new AtmailGlobal();

$auth =& $atmail->getAuthObj();

$var = array();

// Load which function we run
$var['func'] = $_REQUEST['func'];

// If exporting the addressbook print a different content-type
if ($var['func'] == 'export')
{
	header("Content-Type: application/octet-stream; name=\"abook.csv\"\n");
	header("Content-Disposition: attachment; filename=\"abook.csv\"\n\n");
	header("Pragma: ");
}
elseif ($var['func'] == 'quicksearch')
{
	// Print an XML header for the quicksearch ( required for some setups )
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	header('Content-Type: text/html');
	else
	header('Content-Type: xml');
}
else
	print $atmail->httpheaders();

$auth =& $atmail->auth;

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

$var['sort'] = $_REQUEST['sort'];
if (!$var['sort']) $var['sort'] = 'UserEmail';

// Load the vars if updating or adding a new user
$var['Email']     		= strip_tags($_REQUEST['Email']);
$var['EmailNew']  		= strip_tags($_REQUEST['EmailNew']);
$var['UserEmail']		= strip_tags($_REQUEST['UserEmail']);
$var['UserEmail2']		= strip_tags($_REQUEST['UserEmail2']);
$var['UserEmail3']		= strip_tags($_REQUEST['UserEmail3']);
$var['UserEmail4']		= strip_tags($_REQUEST['UserEmail4']);
$var['UserEmail5']		= strip_tags($_REQUEST['UserEmail5']);
$var['UserFirstName']	= strip_tags($_REQUEST['UserFirstName']);
$var['UserMiddleName']  = strip_tags($_REQUEST['UserMiddleName']);
$var['UserLastName']	= strip_tags($_REQUEST['UserLastName']);
$var['UserTitle']		= strip_tags($_REQUEST['UserTitle']);
$var['UserGender']	    = strip_tags($_REQUEST['UserGender']);
$var['UserDOB']		    = strip_tags($_REQUEST['UserDOB']);
$var['UserHomeAddress']	= strip_tags($_REQUEST['UserHomeAddress']);
$var['UserHomeCity']	= strip_tags($_REQUEST['UserHomeCity']);
$var['UserHomeState']	= strip_tags($_REQUEST['UserHomeState']);
$var['UserHomeZip']		= strip_tags($_REQUEST['UserHomeZip']);
$var['UserHomeCountry']	= strip_tags($_REQUEST['UserHomeCountry']);
$var['UserHomePhone']	= strip_tags($_REQUEST['UserHomePhone']);
$var['UserHomeMobile']	= strip_tags($_REQUEST['UserHomeMobile']);
$var['UserHomeFax']		= strip_tags($_REQUEST['UserHomeFax']);
$var['UserURL']			= strip_tags($_REQUEST['UserURL']);
$var['UserWorkCompany']	= strip_tags($_REQUEST['UserWorkCompany']);
$var['UserWorkTitle']	= strip_tags($_REQUEST['UserWorkTitle']);
$var['UserWorkDept']	= strip_tags($_REQUEST['UserWorkDept']);
$var['UserWorkOffice']	= strip_tags($_REQUEST['UserWorkOffice']);
$var['UserWorkAddress']	= strip_tags($_REQUEST['UserWorkAddress']);
$var['UserWorkCity']	= strip_tags($_REQUEST['UserWorkCity']);
$var['UserWorkState']	= strip_tags($_REQUEST['UserWorkState']);
$var['UserWorkZip']		= strip_tags($_REQUEST['UserWorkZip']);
$var['UserWorkCountry']	= strip_tags($_REQUEST['UserWorkCountry']);
$var['UserWorkPhone']	= strip_tags($_REQUEST['UserWorkPhone']);
$var['UserWorkMobile']	= strip_tags($_REQUEST['UserWorkMobile']);
$var['UserWorkFax']		= strip_tags($_REQUEST['UserWorkFax']);
$var['id'] 				= strip_tags($_REQUEST['id']);
$var['UserType']		= strip_tags($_REQUEST['UserType']);
$var['UserInfo']		= strip_tags($_REQUEST['UserInfo']);
$var['UserInfo'] 		= strip_tags($var['UserInfo']); // Take away any HTML characters
$var['UserPgpKey']		= htmlentities($_REQUEST['UserPgpKey']);

$var['WriteSelectedGroups'] = htmlentities($_REQUEST['WriteSelectedGroups']);
$var['WriteSelectedUsers'] 	= htmlentities($_REQUEST['WriteSelectedUsers']);
$var['ReadSelectedGroups'] 	= htmlentities($_REQUEST['ReadSelectedGroups']);
$var['ReadSelectedUsers'] 	= htmlentities($_REQUEST['ReadSelectedUsers']);
$var['abookview'] 			= $_REQUEST['type'] ? $_REQUEST['type'] : $_REQUEST['abookview'];
$var['abookview'] 			= Filter::stringMatch(strtolower($var['abookview']), array('global', 'shared', 'personal'));

$var['order'] 				= htmlentities($_REQUEST['order']);

include('snippets/quota_bar.php');

$abook = new Abook(array('Account' => "$atmail->username@$atmail->pop3host"));

// Decide the amount of entries to display
$amount = '50';

$abook->limit = $amount;

$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css");
$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-mail.css");

// Append the form/input/select stylesheet
$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-form.css");

// Delete any users
if ( $_REQUEST['del'] && ( !$atmail->XUL && !$_REQUEST['delmulti'] ) )
{
    $var['user'] = $_REQUEST['email'];
    $abook->delete( $var['user'], $var['id'] );

    // Delete the group if specified
    if ( $_REQUEST['group'] )
    	$abook->deletegroup( $var['user'] );

    $var['status'] = "Deleted {$var['user']} - ";
}

// delete group(s)/user(s) from address books
if ( ($_REQUEST['del'] || $_REQUEST['delgroup'])  && ( $atmail->XUL || $_REQUEST['delmulti'] ) )
{

	$ids = $_REQUEST['del'];
	$idsgroup = $_REQUEST['delgroup'];

	// delete selected group(s)
	if(is_array($idsgroup))
	{
	    foreach($idsgroup as $id)
	    {
	        $abook->deletegroup( $atmail->db->sqlgetfield("SELECT UserEmail
	                                                       FROM {$atmail->db->Abook}
	                                                       WHERE id = ?", $id), $id);
	    }
	}

	// delete selected contact(s)
	if(is_array($ids))
	{
	    foreach($ids as $id)
	    {
	        $abook->delete( $atmail->db->sqlgetfield("SELECT UserEmail
	                                                  FROM {$atmail->db->Abook}
	                                                  WHERE id = ?", $id), $id);
	    }
	}
}

/*
if( $_REQUEST['del'] && $atmail->XUL )
{
	// Delete multiple addressbook entries
	$ids = $_REQUEST['del'];

	foreach ($ids as $id)
		$abook->delete( $atmail->db->sqlgetfield("select UserEmail from $atmail->Abook where id=? and Account=?", array($id, $atmail->Account)), $id);
}
*/


// Update a Group Address
if ( $_REQUEST['savegroupxp'] )
{
    // Retrieve the new group name
    $var['UserGroup'] = $_REQUEST['UserGroup'];
	$var['UserGroup'] = preg_replace('/shared|group/i', '', $var['UserGroup']);

	// If editing an existing entry, delete the record and re-create the permissions
	$var['UserGroupPrev'] = $_REQUEST['UserGroupPrev'];

    // Load an array containing all the new addresses, if using the simple/ajax interface
	$users = $_REQUEST['ToAddress'];

	// Switch the users email-address into the group-name
	$var['UserEmail'] = $var['UserGroup'];
	$var['GroupType'] = $_REQUEST['GroupType'];

	if ($var['UserGroupPrev'])
		$var['GroupType'] = '1' ;	// Enable if editing a previous group

	// If the user is adding a personal addressbook entry
	if ($var['UserGroupPrev'])
		$abook->deletegroup($var['UserGroupPrev'], $var['id']);
		
	$abook->add($var);
	$var['abookview'] = 'personal';

    foreach ($users as $user)
    {
		// Take away any leading/ending spaces
        $user = trim($user);

		// Add the new entry into the personal group addressbook
		$abook->addgroup( $var['UserGroup'], $user );
	}

}

if ( $_REQUEST['savegroup'] )
{
    $var['existing'] = $_REQUEST['existing'];

    // Retrieve the new group name
    $var['UserGroup'] = $_REQUEST['UserGroup'];

    // Load an array containing all the new addresses
    $users = $_REQUEST['ToAddress'];

    $tmp = array();

    // Delete the emails in the group
    $abook->deletegroup( $var['UserGroup'] );

    foreach ($users as $user)
    {
        // Take away any leading/ending spaces
        $user = trim($user);

        // Skip if we already have the address
        if ( isset($tmp[$user]) ) continue;

		// If the user contains multiple users, split them up and insert each entry
        if ( strpos($user, ',') !== false )
		{
            $addr = explode(',', $user);
            /*???
			$mail = $_;
            $mail =~ s/^\s+//g;
            $mail =~ s/\s+$//g;
			*/
            foreach ($addr as $a)
				$abook->addgroup($var['UserGroup'], $a);
        }
        else
		{
            // Add the new entry into the database
            $abook->addgroup( $var['UserGroup'], $user );
        }

        // Record we have already read this user
        $tmp[$user]++;
    }
}

// Update any record
if ( $_REQUEST['update'] )
{
	$abook->update($var);

    $var['status'] = "Updated {$var['Email']} - ";
}


// Add a new record to the addressbook
if ( AtmailGlobal::isset_chk($_REQUEST['add']) )
{
	// Update the users prefs to show images in messages from users within the abook
	if ( AtmailGlobal::isset_chk($_REQUEST['AbookAllow']) ) {
		$atmail->update_settings_displayimages();
	}
	$var['AddRecipients'] = $_REQUEST['AddRecipients'];

	if ( $var['AddRecipients'] )
	{
		if (preg_match('/(;|,)/', $var['AddRecipients'], $m))
			$recipients = explode($m[1], $var['AddRecipients']);
		else
			$recipients = array($var['AddRecipients']);

		foreach ( $recipients as $addr)
		{
			$addr = html_entity_decode($addr);
			$exp = '/([^":\s<>()\/;]*@[^":\s<>()\/;]*)/';

			// Load the users email address from the string
			preg_match($exp, $addr, $match);
			$email = $match[1];

			if (!$email)
				continue;

			// Find the users Fullname if applicable
			$addr = str_replace($email, '', $addr);
			$addr = str_replace(array('<', '>', '&gt;', '&lt;'), '', $addr);

			if (preg_match('/^(\w+)\s?(\w+)/', $addr, $match))
			{
				$firstname = $match[1];
				$lastname = $match[2];
			}
			else
				$firstname = $addr;

			// Only add if once, if it does not exist already
			if (!$atmail->db->sqlgetfield("SELECT COUNT(UserEmail)
			                               FROM {$atmail->db->Abook}
			                               WHERE Account = ?
			                               AND UserEmail=?",
			                               array("$atmail->username@$atmail->pop3host", $email)))
            {
				$abook->add(array('UserEmail' => $email, 'UserFirstName' => $firstname, 'UserLastName' => $lastname) );

            }

		}
	}
	elseif ( $_REQUEST['add'] )
	{
		// If the user is adding a personal addressbook entry
		$abook->add($var);
	}
}

// Import a users addressbook
if ( $_REQUEST['importupdate'] )
{
	$abook->ImportType = $_REQUEST['ImportType'];
	$abook->Import	 = $_REQUEST['fileupload'];
    $abook->ColumnType = $_REQUEST['ColumnType'];
	$var['count'] = $abook->importfile();

	print $atmail->parse("html/$atmail->Language/msg/importalert.html", array('count' => $var['count']));
	//if ($atmail->XUL) exit;
}

// Open the addressbook
if ( $var['func'] == 'open' )
{
	$var['count'] = 0;

	$abook->limit = $amount;

	$current = $_REQUEST['current'];
    if ($current == 1)
        $current = 0;

    $var['current'] = $current;	//for the html template

    // Check if we are adding a new group to the list
    if ( $_REQUEST['addgroup'] )
	{
        $var['UserGroup'] = $_REQUEST['UserGroup'];
        $users = $_REQUEST['ToAddress'];

        foreach ($users as $user)
            $abook->addgroup( $var['UserGroup'], $user );
    }

    $h = $group = array();

	$var['search'] = $_REQUEST['search'];

	if ($var['search'])
	{
	    if (isset($_REQUEST['current']) && $_REQUEST['current'] != '')
	       $args = $_SESSION['search_params'];
	    else
	    {
    	    $args = array('FirstName'		=> $_REQUEST['FirstName'],
        				  'LastName'		=> $_REQUEST['LastName'],
        				  'Account'		    => $_REQUEST['email'],
        				  'UserWorkCompany' => $_REQUEST['UserWorkCompany'],
        				  'UserHomeAddress' => $_REQUEST['UserHomeAddress'],
        				  'UserHomeCity'    => $_REQUEST['UserHomeCity'],
        				  'UserHomeState'   => $_REQUEST['UserHomeState'],
        				  'UserHomeCountry' => $_REQUEST['UserHomeCountry'],
        				  'abookview'		=> $var['abookview']);
	    }

		// Search the users addressbook with the search details
		$h = $abook->search( 'Account', $args, $current);

	   $var['size'] = $h['FullTotal'];
	   unset($h['FullTotal']);

	   // see if we need to save search params for next/back action
	   if ($var['size'] > $amount)
	       $_SESSION['search_params'] = $args;

	}
	elseif ( $var['abookview'] == 'global' )
	{
		//Optionally list the system global addressbook
		$h = $abook->viewglobal(false, $current, $_REQUEST['order']);

		$var['size'] = $abook->get_global_abook_size();

		$var['sort'] = 'UserEmail';

    }
	elseif( $var['abookview'] == 'shared' )
	{
		// List the shared addressbook
		$h = $abook->viewshared( $var['sort'], '', $current, $_REQUEST['order']);

		$var['size'] = $abook->get_shared_abook_size();

		// Get a hash containing all our Groups
		$group = $abook->viewsharedgroup( $var['sort'] );

	}
	else
	{
		$h = $abook->view( $var['sort'], 'UserEmail, UserFirstName, UserLastName, UserGender, UserHomePhone, UserWorkPhone, id', $current, $_REQUEST['order']);

		$var['size'] = $abook->get_abook_size();

		// Get a hash containing all our Groups
		$group = $abook->viewgroup( $var['sort'] );
    }
/*
    if (isset($h['FullTotal']))
    {
    	$fullTotal = $h['FullTotal'];
    	unset($h['FullTotal']);
	}
*/
	$count;

	//$sortarray = $emptyarray = array();

	/*
	// Sort the results, depending on the users preference
	foreach ( array_keys(multi_ksort($h, $var['sort'])) as $k )
	{
		if ($h[$k][$var['sort']] == '')
			array_push($emptyarray, $k);
		else
			array_push($sortarray, $k);
	}

	if ($var['order'] == 'desc')
		$sortarray = array_reverse($sortarray);

	// List entries with a blank field last, the sort function by default picks these up first
	$sortarray = array_merge($sortarray, $emptyarray);
*/
	$var['count'] = -1;

	foreach ($h as $key => $elem)
	{
		// Increment the number of records we have displayed
		$var['count']++;

		//if ($var['count'] < $current && !$var['search'])
		//	continue;

		// Skip if the count exceeds X amount ( XUL searches, return all matches )
		//if ($var['count'] > $amount + $current && !$var['search'])
			//continue;

		// Find the unique ID of the addressbook entry
        $id = $key;

        if ( !$id )
			continue;

		//Load an element the domains the users email-address
        $var['ViewEmail'] = $elem['UserEmail'];

		if ($atmail->Language == 'japanese')
			$var['FullName'] = $elem['UserLastName'].' '.$elem['UserFirstName'].' ';
		else
			$var['FullName'] = $elem['UserFirstName'].' '.$elem['UserLastName'];

		// If the user is not using the XP interface, crop the email/fullname field to fit in the table
		if ($atmail->LoginType == 'simple')
			$var['ViewEmail'] = $abook->clean( $var['ViewEmail'], 18 );

		// Take out any newlines characters that will mess with the Jscript functions
        $elem['UserEmail'] = str_replace(array("\n", "\r"), '', $elem['UserEmail']);
        if ( $elem['UserEmail'] == '@' )
			continue;

        // We are a group address entry
        if ( $group[$var['ViewEmail']] )
		{
			$shared = "";
			$viewgroupemail = "{$var['ViewEmail']} Group";

			// Parse the HTML template with the group details
            $var['users'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_group$shared" . "entry.html",
              array('Email'			 => $elem['UserEmail'],
					'ViewGroupEmail' => $viewgroupemail,
					'FullName'		 => $var['FullName'],
					'WorkPhone'		 => $elem['UserWorkPhone'],
					'HomePhone'		 => $elem['UserHomePhone'],
					'id'			 => $id,
					'class'			 => $var['class'],
					'Permissions'	 => $elem['Permissions'],
					'count'			 => $var['count'])

            );

        }
		else
		{
            $var['users'] .= $atmail->parse(
            	"html/$atmail->Language/$atmail->LoginType/abook_entry.html",
				array(
					'ViewEmail' => $var['ViewEmail'],
					'Email'     => $elem['UserEmail'],
					'FullName'  => $var['FullName'],
					'WorkPhone' => $elem['UserWorkPhone'],
					'HomePhone' => $elem['UserHomePhone'],
					'id'		=> $id,
					'class'     => $var['class'],
					'count'	    => $var['count'],
					'count'	    => $var['count'],
					'ldap'	    => $elem['ldap']
				)
            );
        }
    }

    if (!$var['users'])
		$var['users'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_entry_blank.html");

	// If there are more addressbook records then displayed, add a next button
	if ( $current + $amount < $var['size'])
		$var['next_pos'] = $current + $amount;

	// Add a previous button if records exists before us
	if ( $current - $amount >= 0 && $current != 0)
	{
		$var['prev_pos'] = $current - $amount;
		if ( $var['prev_pos'] <= 0 )
			$var['prev_pos'] = 1 ;
	}

    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook.html", $var );

}

elseif ( $var['func'] == 'add' )
    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook.html", $var );

elseif ( $var['func'] == 'new' )
{
	if($_REQUEST['abookajax']) {
		print $atmail->parse("html/$atmail->Language/simple/abook_ajax.html", $var );
	} else {
		$var['func'] = 'add';
		print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_new.html", $var );
	}
}
// view of selected contact from address book
elseif ( $var['func'] == 'view' )
{
    $var['email'] = $_REQUEST['email'];

    // Select the user from the personal, shared, or system database
    $db = $abook->viewuser( $var['email'], $var['abookview'] );

    // Loop through each entry in the hash and build the list of users
    if (isset($h) && is_array($h)) {

        foreach(array_keys($h) as $key)
        {
            $id = $h[$key]['Account'];

            if ($h[$email]['Gender'] == 'F')
            {
                $gender = 'female';
            }
            else
            {
                $gender = 'male';
            }


            // Append the FirstName Lastname <email@host>, otherwise use the accounts email-address
            if (strlen($h[$key]['FirstName']) > 0  && strlen($h[$key]['LastName']) > 0)
            {
                $username = $h[$key]['FirstName'].' '.$h[$key]['LastName'].' &lt;'.$id.'&gt;';
            } else {
                $username = $id ;
            }

            $db['UserArray'] .= "Users[\"$id\"] = '$username';\nGender[\"$id\"] = '$gender';\n";
        }
    }

    // array of shared usergroups
    $db['UserArray'] .= findgroups();

    // Switch our function to update a record
    $var['func']  = 'open';
    $db['update'] = 1;
    $db['abookview'] = $var['abookview'];
    $db['atmailstyle'] = $var['atmailstyle'];
    if ($db['Permissions'] == 0) {
        $db['Disabled'] = "disabled title='Read only access'";
    }

    if ( $_REQUEST['profile'] )
    {
        foreach (array('UserEmail', 'UserEmail1', 'UserEmail2', 'UserEmail3', 'UserEmail4') as $email)
        {
            if ($db[$email])
            $db['UserEmailList'] .= "<a href=\"javascript:top.opencompose('$db[$email]')\">$db[$email]</a> , ";
        }

        $db['UserEmailList'] = preg_replace('/, $/', '', $db['UserEmailList']);
        $db['abookview'] = $var['abookview'];

        print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_viewprofile.html", $db);

    }
    // view selected contact at single window
    else
    {
        print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_view.html", $db);
    }


}
elseif ( $var['func'] == 'checkgroup' )
{
	// Detect if the groupname already exists
	print $abook->checkgroup($_REQUEST['GroupName']);
	$atmail->end();
}
elseif ( $var['func'] == 'quicksearch' )
{
	$var['sort'] = 'UserEmail';

	$mail  = array();

	$to = preg_split('/,|;/', $_REQUEST['addr']);

	$email = $to[count($to)-1];

	// Take away leading/trailing whitespace
	$email = trim($email);

	if (!$email) $atmail->end();


	// Search the users addressbook with the search details
	$h = $abook->search('Account', array(
		'Account'   => $email,
		'FirstName' => $email,
		'LastName' => $email,
		'abookview' => 'personal',
		'SearchType' => 'or',
		'limit' => 15)
	);

	$personalgroup = $abook->viewgroup( $var['sort'] );

	$matches = 0;

	// Sort the results, depending on the users preference
	if (is_array($h)) {
		ksort($h);
		foreach ( $h as $k => $v)
		{
			// Skip, if we have already displayed
			if ($mail[$v['UserEmail']] || !$v['UserEmail'] || !strpos($v['UserEmail'], '@') && !$personalgroup[$v['UserEmail']])
				continue;

			$matches++;

			// Escape ' , they break our JS
			$v['UserFirstName'] = str_replace("'", '', $v['UserFirstName']);
			$v['UserLastName'] = str_replace("'", '', $v['UserLastName']);

			if ($matches == 1)
				quicksearch_header();

			$clean_email = $v['UserEmail'];
			if ($personalgroup[$v['UserEmail']])
				$h[$k]['UserEmail'] = "{$v['UserEmail']} Group";

			elseif ($v['UserFirstName'] && $v['UserLastName'])
				$v['UserEmail'] = "\\'{$v['UserFirstName']} {$v['UserLastName']}\\' &lt;{$v['UserEmail']}&gt;";
			else
				$v['UserEmail'] = "&lt;${v['UserEmail']}&gt;";

			// For the format
			$var['UserEmailFormat'] = str_replace("'", '', $v['UserEmail']);
			$var['UserEmailFormat'] = stripslashes($v['UserEmail']);

			$var['UserEmailFormat'] = str_replace($email, "<b>$email</b>", $var['UserEmailFormat']);

			print $atmail->parse("html/abook_quicksearch.html", array('UserEmail' => $v['UserEmail'], 'UserEmailFormat' => $var['UserEmailFormat']));

			// Only print unique emails
			$mail[] = $clean_email;
		}
	}

	if (count($mail) > 0)
		print '</table>';
}

elseif ( $var['func'] == 'sidebar' )
{
	$var['sort'] = 'UserFirstName';

	$abook->limit = $abookLimitOverride;

	$h = $abook->view( $var['sort'], 'UserEmail, UserFirstName, UserLastName, UserGender, id' );

    // Get a hash containing all our Groups
    $group = $abook->viewgroup( $var['sort'] );

	// Make the users name appear as the email-address, if no name is specified
	foreach ( array_keys(multi_ksort($h, $var['sort'])) as $k )
	{
		if (!$h[$k]['UserFirstName'])
		$h[$k]['UserFirstName'] = $h[$k]['UserEmail'];
	}

	$var['rows'] = '';
	$i = 0;
	
	// Sort the sidebar, by default from A - Z by the email address
	//foreach ( array_keys( multi_ksort($h, $var['sort'])) as $id )
	foreach (array_keys($h) as $id)
	{
		$email = $h[$id]['UserEmail'];

		if (!$email || strpos($email, '@') === false)
			continue;

        // We are a group address entry
        if ( $group[$id] )
		{
            foreach ( array_keys( $group[$id] ) as $key )
                $email .= "$key, ";
        }
        else
		{
            $name  = $h[$id]['UserFirstName'].' '.$h[$id]['UserLastName'];
			if ($name == ' ') $name = $email;

			$name = $abook->clean( $name, 18 );

            $group = $h[$id]['UserGlobal'];
        }

        $h[$id]['UserGender'] = ($h[$id]['UserGender'] == '-' ) ? 'M' : $h[$id]['UserGender'];

        $i++;

        $var['rows'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_row.html",
										array('Email'      => $email,
											  'FullName'   => $name,
											  'UserGender' => $h[$id]['UserGender'],
											  'count'	   => $i));

    }

	if ( !$var['rows'] )
		$var['rows'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_blank.html");

    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_sidebar.html",
						 array('rows'        => $var['rows'],
							   'atmailstyle' => $var['atmailstyle'],
							   'abookview'   => $var['abookview'] ? $var['abookview'] : 'personal'));

}
// show address book for compose mail interfaces
elseif ( $var['func'] == 'composebook' )
{
	unset($var['rows']); // = '';

	$abook->limit = $abookLimitOverride;

	// get account contacts
	$h = $abook->view( $var['sort'], 'UserEmail, UserFirstName, UserLastName, UserGender, id' );
	// get account groups
	$grp = $abook->listgroup();

	// Sort the global addressbook by FullName first, otherwise email address
	$var['sort'] = 'UserEmail';

	// Next, display the group of emails
	foreach ( array_keys(multi_ksort($grp, $var['sort'])) as $group)
	{
		if($var['abookview'] != 'shared')
		{
			$grp[$group]['Shared'] = 0;
		}

		// build group list for simple interfaces
		if ($atmail->LoginType == 'simple') {
			$var['rows'] .=  "GroupNames[\"$group\"] = \"$group Group\"\n"
			."Groups[\"$group\"] = \"$group Group\"\n";

		} else {
			$var['rows'] .= sort_group($group, $grp[$group], $grp[$group]['Shared'] );
		}
	}

	// Loop through each Group address in the hash
	// Sort the results, depending on the users preference
	//fix this
	foreach ( array_keys(multi_ksort($h, $var['sort'])) as $k )
	{
		// Skip if the entry is a group name
		if ( $grp[$h[$k]['UserEmail']] || $seen[$h[$k]['UserEmail']])
		continue;

		// Skip if the address does not contain a FullName ( sort by Fullname, otherwise email )
		#next if($h{$_}{UserFullName} == ' ');

		$h[$k]['UserFirstName'] = str_replace(array('\\', "'"), array('', "\'"), $h[$k]['UserFirstName']);
		$h[$k]['UserLastName'] = str_replace(array('\\', "'"), array('', "\'"), $h[$k]['UserLastName']);

		$var['rows'] .= sort_entry($abook, $h[$k]['id'], $h);
		$seen[$h[$k]['UserEmail']] = 1;
	}

	// Next, search the addressbook and order from the users email-address
	$var['sort'] = 'UserEmail';
	//fix this
	foreach ( array_keys( multi_ksort($h, $var['sort'])) as $k )
	{
		$i++;
		// Skip if the entry is a group name
		if ( $grp[$k] ) continue;

		// Skip if the address does not contain a FullName ( sort by Fullname, otherwise email )
		if ($h[$k]['UserFullName'] != ' ')
		continue;

		$var['rows'] .= sort_entry($abook, $h[$k]['id'], $h);

	}

	// Loop through the fields
	foreach ( array('emailto', 'emailcc', 'emailbcc') as $name )
	{
		$val = $atmail->param($name);

		// Split entrys by a ,
		$emails = explode(',', $val);

		foreach ($emails as $e)
		{
			// Skip entrys that do not contain an @
			if ( strpos($e, '@') === false ) continue;

			// Build the select box
			$var[$name] .= "<option value='$e'>$e</option>";
		}
	}

	// Print the result for the compose screen
	print $atmail->parse( "html/$atmail->Language/$atmail->LoginType/composebook.html", $var );
}

elseif ( $var['func'] == 'editgroupxp' )
{
	$db = $abook->listpersonalgroup( $_REQUEST['edit'] );

	$var['GroupName'] = $db['UserEmail'];

	// Load a Javascript array of users to add into the address-book
	$db['rows'] = loadpersonalusers($var);

	$db['atmailstyle'] = $var['atmailstyle'];
	$db['type'] = $_REQUEST['edit'];
	print $atmail->parse( "html/$atmail->Language/$atmail->LoginType/editgroup.html", $db );
	$atmail->end();
}
// display add/edit group interface
elseif ( $var['func'] == 'group' )
{
    $abook->limit = $abookLimitOverride;

    $var['edit'] = $_REQUEST['edit'];

    if ($atmail->LoginType == 'simple')
    {
        // Build an option stetements for simple interfaces
        $var['rows'] = loadpersonalusers_simple($var);
    }
    else
    {
        // Load a Javascript array of users to add into the address-book
        $var['rows'] = loadpersonalusers($var);

    }

    $db = $abook->listpersonalgroup( $_REQUEST['edit'] );

    // Load a Javascript array of users to add into the address-book
    $db['rows'] = $var['rows'];

    if ( $_REQUEST['edit'] )
    {
        // The user is editing a group
        // Make a select box from the emails in the group
        $emails = explode(',', $db['UsersArray']);

        foreach ($emails as $email)
        {
            if ( !$email || $email == ' ' )
            continue;

            $db['emailto'] .= "<option value='$email'>$email</option>\n";
            $db['rows'] = str_replace("<option value='$email'>$email</option>", '', $db['rows']);
        }

        $db['atmailstyle'] = $var['atmailstyle'];
        $db['type'] = $_REQUEST['edit'];

        print $atmail->parse( "html/$atmail->Language/$atmail->LoginType/editgroup.html", $db );
    }
    else
    {
        // Print the result
        print $atmail->parse( "html/$atmail->Language/$atmail->LoginType/addgroup.html", $var );
    }
}

// If the user has selected to 'jump' between messages
// e.g next, prev, start, end
elseif ( $var['func'] == 'jump' )
{
	$results = $abook->abookid($var['abookview']);

    $type    = $_REQUEST['jump'];
    $current = $_REQUEST['msgid'];
    $newwin  = $_REQUEST['newwin'];

	$num = count($results) - 1;

    if ( !$current )
		exit();

	$i = 0;

    foreach ($results as $id)
	{
        // Find the array index, depending on our current msgID
        if ( $current == $id )
		{
            $num = $i;

            // Finish the index search
            break;
        }

        $i++;
    }

    // Jump to the next/prev/start or end record
    if ( $type == 'next' && $num <= count($results) - 1	 )
        $num--;
    elseif ( $type == 'prev' && $num >= 0 )
        $num++;
    elseif ( $type == 'start' )
        $num = count($results) - 1;
    elseif ( $type == 'end' )
        $num = 0;

    // Loop back to the end message if user clicks 'prev' for the top message
    if ( !$results[$num] ) $num = 0 ;

    print "<html><head></head><body><script>location.href='abook.php?func=view&email=$results[$num]&type={$var['abookview']}&profile=1';</script></body></html>";

    // After printing the redirect don't run the rest of the script
    $atmail->end();
}

elseif ( $var['func'] == 'import' )
	print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_importfile.html", $var);

elseif ( $var['func'] == 'importfile' )
{
	if (!$abook->importupload()) {
	    $var['jsalert'] = $abook->jsalert;
	    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_importfile.html", $var);
	    $atmail->end();
	}

	$abook->ImportType = $_REQUEST['ImportType'];
	$var['ColumnType'] = $_REQUEST['ColumnType'];

	$fields = $abook->importparse(1);


	$cnt = 0;
	$selectbox = $atmail->parse("html/$atmail->Language/abook_importselect.html");


	foreach ($fields[0] as $field)
	{
		$cnt++;
		if (empty($field) || in_array($field, $abook->ignoreFields)) {
			continue;
		}


		// Try and match the column name
		$match = $abook->importsuggest($field, $selectbox);

		$var['rows'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_importfield.html",
					array('field' => $field , 'rows' => $match, 'count' => $cnt));
	}

	$var['fileupload'] = $abook->Import;
	$var['ImportType'] = $abook->ImportType;

	print $atmail->parse("html/$atmail->Language/$atmail->LoginType/abook_import.html", $var);
}

elseif ( $var['func'] == 'exportopen')
{
	print $atmail->parse("html/$atmail->Language/$atmail->LoginType/export-window.html", array('abookview' => $var['abookview']));
}

elseif ($var['func'] == 'export')
{
	require_once('Language.php');

	$abook->limit = '99999999';

    $h = $abook->view( $var['sort'] );

	$i = 0;

    foreach ( array_keys( multi_ksort($h, $var['sort'])) as $email )
	{
		$i++;

		$tmp = $h[$email];
		ksort($tmp);

		// Display the header once
		if ($i == 1)
		{
			$line = '';
			foreach ( array_keys($tmp) as $k )
			{
				if (in_array($k, $abook->ignoreFields))
					continue;

				$k = Language::translateAbookField($k, $atmail->Language);

				$line .=  "\"$k\",";
			}

			$line = preg_replace('/,$/', '', $line);
			print "$line\n";

		}

		$line = '';

		// Loop through all the addressbook elements and display the results
		foreach ($tmp as $k => $v)
		{
			if (in_array($k, $abook->ignoreFields))
			    continue;

			// Turn newlines into dollar signs for the export
			$v = str_replace("\n", "\$", $v);

			// If the content contains commas, quote the text in double quotes
			if ( strpos($v, ',') !== false ) {
				$v = "\"$v\"" ;
			}
			$line .=  "$v,";
		}

		$line = preg_replace('/,$/', '', $line);
		print "$line\n";
	}

	$atmail->end();
}

elseif( $var['func'] == 'permissionsearch' )
{
    // Set Abook limit
    $abook->limit = $abookLimitOverride;

	$search = $grp = array();

	// Toggle which frames to reload after the search query
	$frames = $_REQUEST['frames'];

	$search[$_REQUEST['PermissionsSearchField']] = $_REQUEST['PermissionsSearchQuery'];

	// Select which addressbook to search
	$search['abookview'] = $_REQUEST['abookview'];

    if ($var['abookview'] == 'shared' )
		$grp = $abook->viewsharedgroup( $var['sort'] );

	elseif ($var['abookview'] == 'personal')
		$grp = $abook->listgroup();

	// Search for all users on the system, ordered by the Account then Firstname
	$h = $abook->search('UserEmail', $search);

	print "<html><body><script>";

	print <<<_EOF
for (var i in parent.Users) {
parent.NullFrameValue(i)
}

for (var i in parent.Groups) {
parent.NullFrameValueGroup(i)
}

_EOF;

	$i = 0;

	// build group list
	foreach (array_keys($groups) as $k)
	{
		if ($k == 'Default')
			continue;

		if ($pref['GlobalAbook'] == 2 && $frames != 'To,Cc,Bcc' && $frames != 'NewGroup')
			print "parent.TestFrameGroupValue(\"$k\", \"$k\")\n";
	}

	// build users list
	foreach ( array_keys($h) as $k )
	{
		$i = $h[$k]['Account'];
		if ( strpos($h[$k]['Account'], '@') === false )
			continue;

        if ($h[$k]['UserGender'] == 'F')
        {
            $gender = 'female';
        }
        else
        {
            $gender = 'male';
        }

        // Append the FirstName Lastname <email@host>, otherwise use the accounts email-address
        if (strlen($h[$k]['UserFirstName']) > 0  && strlen($h[$k]['UserLastName']) > 0)
        {
        	$username = addslashes($h[$k]['UserFirstName']).' '. addslashes($h[$k]['UserLastName']).' &lt;'. addslashes($i) .'&gt;';
        } else {
        	$username = addslashes($i) ;
        }

		$i = htmlentities(addslashes($i));
		$username = str_replace(array('"', "'"), '', $username);
		$i = str_replace(array('"', "'"), '', $i);

		print "parent.TestFrameValue(\"$i\", \"{$username}\", \"$gender\")\n";
	}

	if ($pref['GlobalAbook'] == 1 && $frames != 'To,Cc,Bcc' && $frames != 'NewGroup')
	{
		print <<<_EOF
parent.TestFrameGroupValue("$atmail->pop3host", "$atmail->pop3host")

_EOF;
	}
	elseif ($pref['GlobalAbook'] == 2 && $frames != 'To,Cc,Bcc' && $frames != 'NewGroup')
		print "parent.TestFrameGroupValue(\"All Users\", \"All Users\")\n";

	foreach (array_keys($grp) as $group)
	{
		#next if($h{$_}{Account} !~ /@/);
		if ($grp[$group]['Shared'] == 1)
			print "parent.TestFrameGroupValue(\"$group\", \"$group Shared Group\")\n";
		else
			print "parent.TestFrameGroupValue(\"$group\", \"$group Group\")\n";
	}


	print <<<_EOF
	for (var i in top.Users) {
		parent.SelectedUsers[i] = false;
	}

	for (var i in top.Groups) {
		parent.SelectedGroups[i] = false;
	}

_EOF;

	$frame = explode(',', $frames);

	if (is_array($frame)) {
	    foreach ($frame as $f)
	       print "parent.DrawSelectedFrame('$f');\n";
	}

	$numEmails = count($h) - 1;
	if($h['FullTotal'] > $numEmails)   {
	    print "alert('{$h['FullTotal']} records matched - First $numEmails returned - Refine your search query for additional users');";
	}

	print "parent.DrawUnselectedFrame();\n";
	print "</script>";
	print "</body></html>";
}

else if($var['func'] == 'viewphoto')
{

	echo $abook->viewphoto($var['id'], $var['abookview']);
	
}

$atmail->end();

/**
 * Enter description here...
 *
 * @param string $name group name
 * @param string $email
 * @param int $type group type = 1 for shared
 * @return string
 */
function sort_group($name, $email, $type)
{
	global $atmail;

	// Skip entrys that have no email address
	if (strlen($email) == 0)
	   return;

	// Take away any trailing spaces
	$email = rtrim($email);

	// Build a list of emails
	if($atmail->LoginType == 'simple')
	{
		return;
	}
	elseif ($type == '1')
	{
		return "GroupNames[\"$name\"] = \"$name Shared Group\"\n"
		."Groups[\"$name\"] = \"$name Shared Group\"\n";
	}
	else
	{
		return "GroupNames[\"$name\"] = \"$name Group\"\n"
		."Groups[\"$name\"] = \"$name Group\"\n";
	}


}

/**
 * Sorty selected addressbook entries
 *
 * @param object $abook
 * @param string $key
 * @param array $h
 * @return string
 */
function sort_entry($abook, $key , $h)
{
	global $atmail;

	$name  = $h[$key]['UserFirstName'] . $h[$key]['UserLastName'];
	$email = $h[$key]['UserEmail'];
	// Cleanup the entry to fit on screen
	if ( !preg_match('/\w/', $name) )
	$name = $email;
	$name = $abook->clean( $name, 35 );

	// Skip entrys that have no email address
	if (strlen($email) == 0)
	return;


	if ($h[$key]['Gender'] == 'F')
	{
		$gender = 'female';
	}
	else
	{
		$gender = 'male';
	}

	// Append the FirstName Lastname <email@host>, otherwise use the accounts email-address
	if (strlen($h[$key]['UserFirstName']) > 0  || strlen($h[$key]['UserLastName']) > 0)
	{
		$full_name = $h[$key]['UserFirstName'].' '.$h[$key]['UserLastName'];
		$full_name = trim($full_name);

		if (substr($full_name, 0, 1) != '"' && substr($full_name, -1) != '"') {
            $full_name = '"'.$full_name.'"';
		}

		$username = $full_name.' &lt;'.$email.'&gt;';
	} else {
		$username = $email ;
	}


	return "Users[\"$email\"] = '$username';\nGender[\"$email\"] = '$gender';\n";
}

function findgroups()
{
	return;
}

function loadpersonalusers($var)
{
	global $abook;

    // array of current addresses
    $h = $abook->view('', 'UserEmail, UserFirstName, UserLastName, UserGender, id');
    $i = $rows = '';

    // Retrieve a list of group addresses
    $grp = $abook->listgroup();

    // Sort the global addressbook by FullName first, otherwise email address
    $var['sort'] = 'UserEmail';

	// Next, display the group of emails
	foreach ( array_keys(multi_ksort($grp, $var['sort'])) as $group)
	{
		if ($group == $var['GroupName'])
			continue;
		$rows .= sort_group($group, $grp[$group], '0');
	}

    // Loop through each Group address in the hash
    // Sort the results, depending on the users preference
    foreach ( array_keys(multi_ksort($h, $var['sort'])) as $k )
	{
		// Skip if the entry is a group name
		if ( $grp[$h[$k]['UserEmail']] || strpos($h[$k]['UserEmail'], '@') === false )
			continue;

		$rows .= sort_entry($abook, $h[$k]['id'], $h);
	}

	return $rows;
}


function multi_ksort($array, $field)
{
	if (!is_array($array))
		settype($array, 'array');

	$tmp = $sorted = array();

	foreach (array_keys($array) as $k)
		$tmp[] = $array[$k][$field];

	sort($tmp);

	foreach ($tmp as $t)
	{
		foreach (array_keys($array) as $k)
		{
			if ($array[$k][$field] == $t)
			{
				if (!is_array($array[$k]))
					settype($array[$k], 'array');

				foreach ($array[$k] as $k2 => $v2)
				{
					$sorted[$k][$k2] = $v2;
				}
			}
		}

	}

	return $sorted;
}

function quicksearch_header()
{
	print <<<EOF
<?xml version="1.0" encoding="utf-8" ?>
<table id='tabledata' width="100%">

EOF;
}

/**
 * Build an option stetements for simple interfaces
 *
 * @param array $var
 * @return string $row
 */
function loadpersonalusers_simple($var)
{
	global $abook;

	// array of current addresses
	$h = $abook->view('', 'UserEmail, UserFirstName, UserLastName, id');
	$rows = '';

	// Retrieve a list of group addresses
	$grp = $abook->listgroup();

	// Sort the global addressbook by FullName first, otherwise email address
	$var['sort'] = 'UserEmail';

	// Next, display the group of emails
	foreach ( array_keys(multi_ksort($grp, $var['sort'])) as $group)
	{
		if ($group == $var['GroupName'])
		continue;
		$rows .= sort_group($group, $grp[$group], '0');
	}

	// Loop through each Group address in the hash
	// Sort the results, depending on the users preference
	foreach ( array_keys(multi_ksort($h, $var['sort'])) as $k )
	{

		// Skip if the entry is a group name
		if ( $grp[$h[$k]['UserEmail']] || strpos($h[$k]['UserEmail'], '@') === false )
		continue;

		// Append the FirstName Lastname <email@host>, otherwise use the accounts email-address
		if (strlen($h[$k]['UserFirstName']) > 0  && strlen($h[$k]['UserLastName']) > 0)
		{
			$username = $h[$k]['UserFirstName'].' '.$h[$k]['UserLastName'].' &lt;'.$h[$k]['UserEmail'].'&gt;';
			$rows .=  '<option value="'.$h[$k]['UserEmail'].'">'.$username.'</option>';

		} else {
			$rows .=  '<option value="'.$h[$k]['UserEmail'].'">'.$h[$k]['UserEmail'].'</option>';
		}

	}

	return $rows;
}
?>
