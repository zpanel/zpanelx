<?php

// +----------------------------------------------------------------+
// | ldap.php														|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Session.php');
require_once('Global.php');
require_once('GetMail.php');

session_start();

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();

$var = array();

$atmail->httpheaders();

$atmail->status = $auth->getuser($atmail->SessionID);
$atmail->username = $auth->username;
$atmail->pop3host = $auth->pop3host;

// Print the error screen if the account has auth errors, or session timeout.
if ( $atmail->status == 1 )
	$atmail->auth_error();
if ( $atmail->status == 2 )
	$atmail->session_error();

// Load the account preferences
$atmail->loadprefs();

// Parse the users custom stylesheet
$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css" );
$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-form.css");
$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-mail.css");

include('snippets/quota_bar.php');

if (!$_REQUEST['func'])
{
    $var['search']   = $_REQUEST['search'];
	echo $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap.html", $var);
	$atmail->end();
}

// apend '_ldap_' to the function name passed via $_REQUEST so that
// user cannot call some arbitrary function such as 'phpinfo()'
$func = '_ldap_'.$_REQUEST['func'];

// check that the requested function exists then call it
if (function_exists($func))
	$func();
else
	die("the function <b>'$func'</b> is not defined");

	$atmail->end();
	
function _ldap_search()
{
	global $atmail, $pref, $var;

	if ($_REQUEST['type'] != '0')
	{
		// Search the users global, shared, or personal address-book
		require_once('Abook.class.php');

	    $abook = new Abook(array('Account' => "$atmail->username@$atmail->pop3host"));

	    $h = $abook->search("Account", array(
	      'FirstName' 		=> $_REQUEST['FirstName'],
	      'LastName'  		=> $_REQUEST['LastName'],
	      'Account'   		=> $_REQUEST['email'],
	      'UserWorkCompany' => $_REQUEST['UserWorkCompany'],
	      'UserHomeAddress' => $_REQUEST['UserHomeAddress'],
	      'UserHomeCity' 	=> $_REQUEST['UserHomeCity'],
	      'UserHomeState' 	=> $_REQUEST['UserHomeState'],
	      'UserHomeCountry' => $_REQUEST['UserHomeCountry'],
		  'abookview' 		=> $_REQUEST['type'])
	    );

		ksort($h);

	    foreach ( array_keys($h) as $email )
		{
	        if ( !$h[$email]['Account'] && $var['type'] == "global" )
				continue;

			#next if ( $h{"$email"}{Account} !~ $atmail->{pop3host} && $pref['ldap_local'] == 1 && $var['type'] eq "global" );

			// Display the fields for the "global" e.g system database
			if ($_REQUEST['type'] == "global")
			{
				// Create the location from the City and country
		        $location = "$h[$email]['City'], $h[$email]['Country']";
		        $location = preg_replace('/^,/', '', $location);

		        $var['users'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_entry.html",
					array('Email'    => $h[$email]['Account'],
				          'FullName' => $h[$email]['FirstName'] . " " . $h[$email]['LastName'],
				          'FirstName' => $h[$email]['FirstName'],
				          'LastName' => $h[$email]['LastName'],
				          'Location' => $location,
				          'Phone'    => $h[$email]['TelWork'] ? $h[$email]['TelWork'] : $h[$email]['TelHome'],
				          'class'    => $var['class'])
		        );

				// For personal + shared addressbooks, display the results
			}
			elseif ($_REQUEST['type'] == "personal" || $_REQUEST['type'] == "shared")
			{
				// Create the location from the City and country
		        $location = "$h[$email]['UserHomeCity'], $h[$email]['UserHomeCountry']";
		        $location = preg_replace('/^,/', '', $location);

		        $var['users'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_entry.html",
					array('Email'    => $h[$email]['Account'],
				          'FullName' => $h[$email]['UserFirstName'] . " " . $h[$email]['UserLastName'],
				          'FirstName' => $h[$email]['FirstName'],
				          'LastName' => $h[$email]['LastName'],
				          'Location' => $location,
				          'Phone'    => $h[$email]['UserWorkPhone'] ? $h[$email]['UserWorkPhone'] : $h[$email]['UserHomePhone'],
				          'class'    => $var['class'])
		        );

			}
	    }

		// If no search results defined, throw an alert
	    if ( !$var['users'] )
			$var['users'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_entry_blank.html");

		// Display the users in the addressbook
	    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_search.html", $var);
	}

	// Do LDAP search
	else
	{
		// Check for PHP LDAP extension
		if (!defined('LDAP_OPT_TIMELIMIT'))
		{
			if ( !$var['users'] )
				$var['users'] = "<tr><td colspan=\"4\">The <a href=\"http://php.net//manual/en/ref.ldap.php\">PHP LDAP extension</a> must be
				installed/enabled to use LDAP search with @Mail.</td></tr>";

			print $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_search.html", $var);
			$atmail->end();
		}

	    $var['servername'] = $_REQUEST['servername'];
	    $var['FirstName']  = $_REQUEST['FirstName'];
	    $var['LastName']   = $_REQUEST['LastName'];
	    $var['mail']       = $_REQUEST['email'];
	    $var['advanced']   = $_REQUEST['advanced'];

	    foreach ( array('FirstName', 'LastName', 'mail') as $field)
	        $var[$field] = str_replace(' ', '', $var[$field]);

		$ldap_config = array(
			'host'		=> $var['servername'],
			'binddn'	=> (!empty($_REQUEST['bind_dn']))     ? $_REQUEST['bind_dn']     : $pref['bind_dn'],
			'basedn'	=> (!empty($_REQUEST['base_dn']))     ? $_REQUEST['base_dn']     : $pref['base_dn'],
			'bindpw'	=> (!empty($_REQUEST['ldap_passwd'])) ? $_REQUEST['ldap_passwd'] : $pref['ldap_passwd']
		);


		$ldap = ldap_connect($ldap_config['host'], 389) or ldapError($ldap);

		if (!ldap_bind($ldap, $ldap_config['binddn'], $ldap_config['bindpw']))
			ldapError($ldap);

		$query = "(& ";

		if (!empty($var['mail']))
	        $query .= "(mail={$var['mail']}*) ";

		if (!empty($var['FirstName']) && !empty($var['LastName']))
	        $query .= "(cn={$var['FirstName']}*{$var['LastName']}*) ";

		if (!empty($var['FirstName']))
			$query .= "(givenName={$var['FirstName']}*) ";

		if (!empty($var['LastName']))
			$query .= "(sn=*{$var['LastName']}*) ";

		$query .= ")";

		$result = ldap_search($ldap, $ldap_config['basedn'], $query) or ldapError($ldap);

	    $var['class'] = "item2";

	    foreach (ldap_get_entries($ldap, $result) as $entry)
		{
			if (!is_array($entry))
				continue;

	        $var['class'] = ( $var['class'] == "item" ) ? "item2" : "item";

			$var['count']++;
		
	        $var['users'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_entry.html",
				array(
					'Email'     => $entry['mail'][0],
					'FullName'  => $entry['cn'][0],
					'FirstName' => $entry['givenname'][0],
					'LastName'  => $entry['sn'][0],
					'Location'  => $entry['l'][0],
					'Phone'     => $entry['telephonenumber'][0],
					'class'     => $var['class'])
	        );
	    }

		if ( !isset($var['users']) )
			$var['users'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_entry_blank.html");

    	print $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_search.html", $var);
	}
}


function ldapError(&$ldap)
{
	global $atmail;

	$error = ldap_error($ldap);

    if ( strpos($error, 'Bad hostname') !== false)
        $var['users'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_bad_hostname.html",array('error' => $var['servername']));
    else
        $var['users'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_error.html", array('error' => $error));

    print $atmail->parse("html/$atmail->Language/$atmail->LoginType/ldap_search.html", $var);

	$atmail->end();
}

?>
