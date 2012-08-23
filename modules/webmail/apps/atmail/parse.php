<?php
// +----------------------------------------------------------------+
// | parse.php														|
// +----------------------------------------------------------------+
// | Function: Open a file, parse embeded $vars and customize for 	|
// | user															|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');
require_once('GetMail.php');
require_once('Session.php');
require_once('Global.php');

session_start();

$type = $var = array();

$atmail = new AtmailGlobal;

$auth =& $atmail->getAuthObj();

$filename = preg_replace("/[^a-z0-9\-\/._$>]/i", "", $_REQUEST['file']);
$redirect = $_REQUEST['redirect'];

$_REQUEST['func'] = preg_replace("/[^a-z0-9]/i", "", $_REQUEST['func']);

// No auth necessary to display login page
if ($filename == 'html/login-light.html') {
    echo $atmail->parse('html/login-light.html');
    $atmail->end();
}

$atmail->status = $auth->getuser();

// Print the error screen if the account has auth errors, or session timeout.
// Ignore if user not authenticated, but wants to view the help documentation
if (preg_match('/html\/(.*?)\/help\/(.*?\.html)/', $filename)) {

} elseif ( $atmail->status == 1 ) $atmail->auth_error();
elseif ( $atmail->status == 2 ) $atmail->session_error();

if ($redirect)
{
	$redirect = str_replace('&amp;', '&', $redirect);
	$redirect = str_replace('$', '/', $redirect);

	// Unsure why this is implemented, might be used, verify
	//if(!preg_match('/http:\/\//', $redirect) && !preg)
	//$redirect = str_replace('http', 'http:', $redirect);

	header("Location: $redirect");
	$atmail->end();
}
elseif (preg_match('/\.css$/', $filename))
	header("Content-Type: text/css");
else
	$atmail->httpheaders();

if ($atmail->status == 0)
{
	$atmail->username = $auth->username;
	$atmail->pop3host = $auth->pop3host;
	$atmail->loadprefs();
}
elseif (preg_match('/html\/(.*?)\/help\/(.*?\.html)/', $filename))
	$atmail->LoginType = 'xp';
elseif (strpos($filename, 'showmail_interface') !== false)
	$atmail->LoginType = 'simple';
else
{
	$atmail->LoginType = 'simple';
	$atmail->username = 'Login to your account!';
	$atmail->Ajax = '1';
	$atmail->Language = 'english';
	$atmail->FontStyle = 'Verdana';
}


if (!$atmail->Language)
	$atmail->Language = $pref['Language'];

// Sub LANG for language name in the filename
$filename = str_replace(array('$this->Language', 'LANG'), $atmail->Language, $filename);

// Let any language file use the following

$type["html/$atmail->Language/xp/heading/contact.html"]        		= 1;
$type["html/$atmail->Language/blue_pane/blue_pane.html"]        	= 1;
$type["html/$atmail->Language/simple/simple.html"]          		= 1;
$type["html/$atmail->Language/simple/atmailstyle.css"]      		= 1;
$type["html/$atmail->Language/blue_pane/menubar-blue.html"] 		= 1;
$type["html/$atmail->Language/javascript/menubar-big.js"]   		= 1;
$type["html/$atmail->Language/simple/showmail_interface.html"]      = 1;
$type["html/$atmail->Language/simple/simple/simple.html"]           = 1;



$type["javascript/atmailstyle.css"] = 1;
$type["javascript/head.css"]        = 1;
$type["javascript/settings.js"]     = 1;
$type["javascript/menubar-big.js"]  = 1;
$type["javascript/menubar-xp.js"]   = 1;
$type["javascript/ajax/cal_loader.js"] = 1;
$type["html/$atmail->Language/javascript/ajax-lang.js"] = 1;
$type["html/$atmail->Language/javascript/xp.js"] = 1;

$type["html/demo.html"] = 1;

$type["html/$atmail->Language/help/filexp.html"] = 1;
$type["html/$atmail->Language/help/menubarhelpxp.html"] = 1;
$type["html/$atmail->Language/help/acctconfig.html"] = 1;
$type["html/$atmail->Language/help/addpop3_faq.html"] = 1;
$type["html/$atmail->Language/help/addpop3.html"] = 1;
$type["html/$atmail->Language/help/addressbook.html"] = 1;
$type["html/$atmail->Language/help/address_faq.html"] = 1;
$type["html/$atmail->Language/help/calendar.html"] = 1;
$type["html/$atmail->Language/help/cal_faq.html"] = 1;
$type["html/$atmail->Language/help/calshared.html"] = 1;
$type["html/$atmail->Language/help/compose.html"] = 1;
$type["html/$atmail->Language/help/file.html"] = 1;
$type["html/$atmail->Language/help/filexp.html"] = 1;
$type["html/$atmail->Language/help/helptodo.txt"] = 1;
$type["html/$atmail->Language/help/helpxp.css"] = 1;
$type["html/$atmail->Language/help/hintsandtips.html"] = 1;
$type["html/$atmail->Language/help/imgs"] = 1;
$type["html/$atmail->Language/help/import.html"] = 1;
$type["html/$atmail->Language/help/index.html"] = 1;
$type["html/$atmail->Language/help/installation_help"] = 1;
$type["html/$atmail->Language/help/iphonehelp.html"] = 1;
$type["html/$atmail->Language/help/iphonehelphow.html"] = 1;
$type["html/$atmail->Language/help/iphonehelptroub.html"] = 1;
$type["html/$atmail->Language/help/ldap_faq.html"] = 1;
$type["html/$atmail->Language/help/ldap.html"] = 1;
$type["html/$atmail->Language/help/mailerror.html"] = 1;
$type["html/$atmail->Language/help/mailsend.html"] = 1;
$type["html/$atmail->Language/help/mngmsg.html"] = 1;
$type["html/$atmail->Language/help/newmbox.html"] = 1;
$type["html/$atmail->Language/help/rcvattach.html"] = 1;
$type["html/$atmail->Language/help/reademail.html"] = 1;
$type["html/$atmail->Language/help/recvmail.html"] = 1;
$type["html/$atmail->Language/help/search.html"] = 1;
$type["html/$atmail->Language/help/sendmail.html"] = 1;
$type["html/$atmail->Language/help/settings.html"] = 1;
$type["html/$atmail->Language/help/sidebar.html"] = 1;
$type["html/$atmail->Language/help/sms_faq.html"] = 1;
$type["html/$atmail->Language/help/sms.html"] = 1;
$type["html/$atmail->Language/help/sndattach.html"] = 1;
$type["html/$atmail->Language/help/spell_faq.html"] = 1;
$type["html/$atmail->Language/help/spell.html"] = 1;
$type["html/$atmail->Language/help/sync.html"] = 1;
$type["html/$atmail->Language/help/toggle.html"] = 1;
$type["html/$atmail->Language/help/wap_faq.html"] = 1;
$type["html/$atmail->Language/help/wap.html"] = 1;
$type["html/$atmail->Language/help/loginhelp.html"] = 1;
$type["html/$atmail->Language/help/mailmonitor.html"] = 1;
$type["html/$atmail->Language/help/xulhelp.html"] = 1;
$type["html/$atmail->Language/help/ajaxhelp.html"] = 1;
$type["html/$atmail->Language/help/videomail.html"] = 1;

// signup
$type["html/$atmail->Language/javascript/validate.js"] = 1;

// AJAX interface
$type["html/$atmail->Language/simple/showmail_interface.html"]   = 1;


// Load additional vars from the Ajax interface panel
if ( $filename == "html/$atmail->Language/simple/showmail_interface.html" )
{
	$var['Ajax'] = $_REQUEST['ajax'];
	$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/simple/atmailstyle.css" );
	$var['mailstyle'] = $atmail->parse("html/$atmail->Language/simple/atmailstyle-mail.css");
	$var['func'] = $_REQUEST['func'];
	$var['To']  = $_REQUEST['To'];
	$var['Cc']  = preg_replace('/^;\s+/', '', $atmail->loadParameter('cc'));
	$var['Bcc']  = preg_replace('/^;\s+/', '', $atmail->loadParameter('bcc'));
	
	// insert default values for Received After selects
	$after_date = strtotime("-61 days",time());
	$after_day = date("d", $after_date);
	$after_month = date("m", $after_date);
	$after_year = date("Y", $after_date);

    $y1 = date('Y', strtotime('-10 years', time()));
    $y2 = date('Y');
    $var['beforeYearOptions'] = $var['afterYearOptions'] = '';
    for ($y=$y1; $y<=$y2; $y++) {
        if ($y == $after_year) {
            $var['afterYearOptions'] .= "<option value=\"$y\" selected>$y</option>\n";
        } else {
            $var['afterYearOptions'] .= "<option value=\"$y\">$y</option>\n";
        }
    }
        
    $y2++;
    for ($y=$y1; $y<=$y2; $y++) {
        if ($y == $y2) {
            $var['beforeYearOptions'] .= "<option value=\"$y\" selected>$y</option>\n";
        } else {
            $var['beforeYearOptions'] .= "<option value=\"$y\">$y</option>\n";
        }
    }
}


// Calculate the length of a menu, shrink if we have settings disabled
if ( $filename == "html/$atmail->Language/xp/toolbar.html" || $filename == "html/$atmail->Language/xp/toolbar_abook.html")
	$var = $atmail->calcmenu_height($filename);

// Die if the file is not permitted
if ( !$type[$filename] )
	catcherror("Specified file $filename is not allowed");

$var['FirstLoad'] = $_REQUEST['FirstLoad'];

// If using the help menu, load the selected help-file or default to the settings
if ( $filename == "html/$atmail->Language/help/filexp.html" && $_REQUEST['HelpFile'])
{
	if ( $_REQUEST['HelpFile'])
		$var['HelpFile'] = $_REQUEST['HelpFile'];
	else
		$var['HelpFile'] = 'file.html';
}

if (!$_REQUEST['XUL'])
	$var['atmailstyle'] .= $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-form.css");

/*
if($_REQUEST['ajax'])	{
$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle.css" );
$var['mailstyle'] = $atmail->parse("html/$atmail->Language/$atmail->LoginType/atmailstyle-mail.css");
}
*/

include('snippets/quota_bar.php');

$result =  $atmail->parse( $filename, $var );

if ( $filename == "html/$atmail->Language/simple/showmail_interface.html" )
{
	$result = preg_replace("/(<select id=\"SearchAfterDay\".*)<option value=\"$after_day\"/", "\$1<option value=\"$after_day\" selected", $result);
	$result = preg_replace("/(<select id=\"SearchAfterMonth\".*)<option value=\"$after_month\"/", "\$1<option value=\"$after_month\" selected", $result);
}

print $result;

$atmail->end();

?>
