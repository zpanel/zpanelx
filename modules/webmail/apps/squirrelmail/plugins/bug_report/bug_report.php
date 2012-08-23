<?php

/**
 * bug_report.php
 *
 * This generates the bug report data, gives information about where
 * it will be sent to and what people will do with it, and provides
 * a button to show the bug report mail message in order to actually
 * send it.
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This is a standard Squirrelmail-1.2 API for plugins.
 *
 * @version $Id: bug_report.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage bug_report
 */

/**
 * @ignore
 */
define('SM_PATH','../../');

require_once(SM_PATH . 'include/validate.php');


// if this plugin is not enabled, don't continue
//
global $plugins, $color;
if (!in_array('bug_report', $plugins)) {
    plain_error_message(_("Plugin is not enabled"), $color);
    exit;
}



/* load sqimap_get_user_server() */
include_once(SM_PATH . 'functions/imap_general.php');
// loading form functions
require_once(SM_PATH . 'functions/forms.php');

displayPageHeader($color, 'None');

function Show_Array($array) {
    $str = '';
    foreach ($array as $key => $value) {
        if ($key != 0 || $value != '') {
        $str .= "    * $key = $value\n";
        }
    }
    if ($str == '') {
        return "    * Nothing listed\n";
    }
    return $str;
}

$browscap = ini_get('browscap');
if(!empty($browscap)) {
    $browser = get_browser();
}

sqgetGlobalVar('HTTP_USER_AGENT', $HTTP_USER_AGENT, SQ_SERVER);
if ( ! sqgetGlobalVar('HTTP_USER_AGENT', $HTTP_USER_AGENT, SQ_SERVER) )
    $HTTP_USER_AGENT="Browser information is not available.";

$body_top = "I subscribe to the squirrelmail-users mailing list.\n" .
            "  [ ]  True - No need to CC me when replying\n" .
            "  [ ]  False - Please CC me when replying\n" .
            "\n" .
            "This bug occurs when I ...\n" .
            "  ... view a particular message\n" .
            "  ... use a specific plugin/function\n" .
            "  ... try to do/view/use ....\n" .
            "\n\n\n" .
            "The description of the bug:\n\n\n" .
            "I can reproduce the bug by:\n\n\n" .
            "(Optional) I got bored and found the bug occurs in:\n\n\n" .
            "(Optional) I got really bored and here's a fix:\n\n\n" .
            "----------------------------------------------\n" .
            "\nMy browser information:\n" .
            '  '.$HTTP_USER_AGENT . "\n" ;
	    if(isset($browser)) {
                $body_top .= "  get_browser() information (List)\n" .
                Show_Array((array) $browser);
            }
            $body_top .= "\nMy web server information:\n" .
            "  PHP Version " . phpversion() . "\n" .
            "  PHP Extensions (List)\n" .
            Show_Array(get_loaded_extensions()) .
            "\nSquirrelMail-specific information:\n" .
            "  Version:  $version\n" .
            "  Plugins (List)\n" .
            Show_Array($plugins);
if (isset($ldap_server) && $ldap_server[0] && ! extension_loaded('ldap')) {
    $warning = 1;
    $warnings['ldap'] = "LDAP server defined in SquirrelMail config, " .
        "but the module is not loaded in PHP";
    $corrections['ldap'][] = "Reconfigure PHP with the option '--with-ldap'";
    $corrections['ldap'][] = "Then recompile PHP and reinstall";
    $corrections['ldap'][] = "-- OR --";
    $corrections['ldap'][] = "Reconfigure SquirrelMail to not use LDAP";
}

$body = "\nMy IMAP server information:\n" .
            "  Server type:  $imap_server_type\n";

/* check imap server's mapping */
$imapServerAddress = sqimap_get_user_server($imapServerAddress, $username);

/*
 * add tls:// prefix, if tls is used.
 * No need to check for openssl.
 * User can't use SquirrelMail if this part is misconfigured
 */
if ($use_imap_tls == true) $imapServerAddress = 'tls://' . $imapServerAddress;

$imap_stream = fsockopen ($imapServerAddress, $imapPort, $error_number, $error_string);
$server_info = fgets ($imap_stream, 1024);
if ($imap_stream) {
    // SUPRESS HOST NAME
    $list = explode(' ', $server_info);
    $list[2] = '[HIDDEN]';
    $server_info = implode(' ', $list);
    $body .=  "  Server info:  $server_info";
    fputs ($imap_stream, "a001 CAPABILITY\r\n");
    $read = fgets($imap_stream, 1024);
    $list = explode(' ', $read);
    array_shift($list);
    array_shift($list);
    $read = implode(' ', $list);
    $body .= "  Capabilities:  $read";
    fputs ($imap_stream, "a002 LOGOUT\r\n");
    fclose($imap_stream);
} else {
    $body .= "  Unable to connect to IMAP server to get information.\n";
    $warning = 1;
    $warnings['imap'] = "Unable to connect to IMAP server";
    $corrections['imap'][] = "Make sure you specified the correct mail server";
    $corrections['imap'][] = "Make sure the mail server is running IMAP, not POP";
    $corrections['imap'][] = "Make sure the server responds to port $imapPort";
}
$warning_html = '';
$warning_num = 0;
if (isset($warning) && $warning) {
    foreach ($warnings as $key => $value) {
        if ($warning_num == 0) {
            $body_top .= "WARNINGS WERE REPORTED WITH YOUR SETUP:\n";
            $body_top = "WARNINGS WERE REPORTED WITH YOUR SETUP -- SEE BELOW\n\n$body_top";
            $warning_html = "<h1>Warnings were reported with your setup:</h1>\n<dl>\n";
        }
        $warning_num ++;
        $warning_html .= "<dt><b>$value</b></dt>\n";
        $body_top .= "\n$value\n";
        foreach ($corrections[$key] as $corr_val) {
            $body_top .= "  * $corr_val\n";
            $warning_html .= "<dd>* $corr_val</dd>\n";
        }
    }
    $warning_html .= "</dl>\n<p>$warning_num warning(s) reported.</p>\n<hr />\n";
    $body_top .= "\n$warning_num warning(s) reported.\n";
    $body_top .= "----------------------------------------------\n";
}

$body = htmlspecialchars($body_top . $body);

?>
    <br />
    <table width="95%" align="center" border="0" cellpadding="2" cellspacing="0"><tr>
        <?php echo html_tag('td','<b>'._("Submit a Bug Report").'</b>','center',$color[0]); ?>
    </tr></table>

<?php
echo $warning_html; 

echo '<p><big>';
echo _("Before you send your bug report, please make sure to check this checklist for any common problems.");
echo "</big></p>\n";

echo '<ul>';
echo '<li>';
printf(_("Make sure that you are running the most recent copy of %s. You are currently using version %s."), '<a href="http://squirrelmail.org/" target="_blank">SquirrelMail</a>', $version);
echo "</li>\n";

echo '<li>';
printf(_("Check to see if your bug is already listed in the %sBug List%s on SourceForge. If it is, we already know about it and are trying to fix it."), '<a href="http://sourceforge.net/bugs/?group_id=311" target="_blank">', '</a>');
echo "</li>\n";
   
echo '<li>';
echo _("Try to make sure that you can repeat it. If the bug happens sporatically, try to document what you did when it happened. If it always occurs when you view a specific message, keep that message around so maybe we can see it.");
echo "</li>\n";

echo '<li>';
printf(_("If there were warnings displayed above, try to resolve them yourself. Read the guides in the %s directory where SquirrelMail was installed."), '<tt>doc/</tt>');
echo "</li>\n";
echo "</ul>\n";

echo '<p>';
echo _("Pressing the button below will start a mail message to the developers of SquirrelMail that will contain a lot of information about your system, your browser, how SquirrelMail is set up, and your IMAP server. It will also prompt you for information. Just fill out the sections at the top. If you like, you can scroll down in the message to see what else is being sent.");
echo "</p>\n";

echo '<p>';
echo _("Please make sure to fill out as much information as you possibly can to give everyone a good chance of finding and removing the bug. Submitting your bug like this will not have it automatically added to the bug list on SourceForge, but someone who gets your message may add it for you.");
echo "</p>\n";
?>
    <form action="../../src/compose.php" method=post>
      <table align="center" border="0">
        <tr>
          <td>
            <?php echo _("This bug involves:"); ?> <select name="send_to">
              <option value="squirrelmail-users@lists.sourceforge.net"><?php
                  echo _("the general program"); ?></option>
              <option value="squirrelmail-plugins@lists.sourceforge.net"><?php
                  echo _("a specific plugin"); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <td align="center">
<?php
echo addHidden("send_to_cc","");
echo addHidden("send_to_bcc","");
echo addHidden("subject","Bug Report");
echo addHidden("body",$body);
echo addSubmit(_("Start Bug Report Form"));
?>
          </td>
        </tr>
      </table>
    </form>
  </body>
</html>
