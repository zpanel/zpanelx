<?php

/* options page for IMAP info plugin 
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *   
 * This is where it all happens :)
 *
 * Written by: Jason Munro 
 * jason@stdbev.com
 * 
 * $Id: options.php 14084 2011-01-06 02:44:03Z pdontthink $
 * 
 */

define('SM_PATH','../../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/page_header.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/forms.php');
require_once(SM_PATH . 'plugins/info/functions.php');

global $username, $color, $folder_prefix, $default_charset;
$default_charset = strtoupper($default_charset);
displayPageHeader($color, 'None');
$mailbox = 'INBOX';

/**
 * testing installation
 *
 * prevent use of plugin if it is not enabled
 */
if (! info_is_plugin_enabled('info')) {
    echo '<p align="center"><big>'.
        _("Plugin is disabled.").
        '</big></p></body></html>';
    exit;
}

/* GLOBALS */
sqgetGlobalVar('username', $username, SQ_SESSION);     
sqgetGlobalVar('key', $key, SQ_COOKIE);     
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);  

sqgetGlobalVar('submit', $submit, SQ_POST);

for($i = 0; $i <= 9; $i++){
    $varc = 'CHECK_TEST_'.$i;
    sqgetGlobalVar($varc, $$varc, SQ_POST);
    $vart  = 'TEST_'.$i;
    sqgetGlobalVar($vart, $$vart, SQ_POST);
}

/* END GLOBALS */

$imap_stream = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$caps_array = get_caps($imap_stream);
$list = array (
               'TEST_0',
               'TEST_1',
               'TEST_2',
               'TEST_3',
               'TEST_4',
               'TEST_5',
               'TEST_6',
               'TEST_7',
               'TEST_8',
               'TEST_9');

print "<br><center><b>IMAP server information</b></center><br>\n";
print "<center><table bgcolor=\"".$color[3]."\" width=\"100%\" border=\"1\" cellpadding=\"2\"><tr><td bgcolor=".$color[3]."><br>\n";
print "<center><table width=\"95%\" border=\"1\" bgcolor=\"".$color[3]."\">\n";
print "<tr><td bgcolor=\"".$color[4]."\"><b>Server Capability response:</b><br>\n";

foreach($caps_array[0] as $value) {
    print htmlspecialchars($value);
}

print "</td></tr><tr><td>\n";

if (!isset($submit) || $submit == 'default') {
    print "<br><font color=".$color[6]."><small>Select the IMAP commands you would like to run.
        Most commands require a selected mailbox so the SELECT-command is already setup.
        You can clear all the commands and test your own IMAP command strings. The
        commands are executed in order. The default values are simple IMAP commands using
        your default_charset and folder_prefix from SquirrelMail when needed.<br><br>
        </small></font><center><font color=".$color[6]."><small><b>NOTE: These commands
        are live, any changes made will effect your current
        email account.</b></small></font></center><br>\n";
    if (!isset($submit)) {
        $submit = '';
    }
}
else {
    print 'folder_prefix = ' . htmlspecialchars($folder_prefix) . "<br>\n".
          'default_charset = ' . htmlspecialchars($default_charset) . "\n";
}

print "<br></td></tr></table></center><br>\n";


if ($submit == 'submit') {
    $type = array();
    for ($i=0;$i<count($list);$i++) {
        $type[$list[$i]] = $$list[$i];
    }
}

elseif ($submit == 'clear') {
    for ($i=0;$i<count($list);$i++) {
        $type[$list[$i]] = '';
    }
}

elseif (!$submit || $submit == 'default')  {
    $type = array (
        'TEST_0' => "SELECT $mailbox",
        'TEST_1' => "STATUS $mailbox (MESSAGES RECENT)",
        'TEST_2' => "EXAMINE $mailbox",
        'TEST_3' => "SEARCH CHARSET \"$default_charset\" ALL *",
        'TEST_4' => "THREAD REFERENCES $default_charset ALL",
        'TEST_5' => "SORT (DATE) $default_charset ALL",
        'TEST_6' => "FETCH 1:* (FLAGS BODY[HEADER.FIELDS (FROM DATE TO)])",
        'TEST_7' => "LSUB \"$folder_prefix\" \"*%\"",
        'TEST_8' => "LIST \"$folder_prefix*\" \"*\"",
        'TEST_9' => "");
}

print "<form action=\"options.php\" method=\"post\">\n";
print "<center><table border=\"1\">\n";
print "<tr><th>Select</th><th>Test Name</th><th>IMAP command string</th>\n";
print "</tr><tr><td>\n";

foreach($type as $index=>$value) {
    print "</td></tr><tr><td width=\"10%\"><input type=\"checkbox\" value=\"1\" name=\"CHECK_$index\"";
    if ($index == 'TEST_0' && ($submit == 'default' || $submit == '')) {
        print " checked";
    }
    $check = "CHECK_".$index;
    if (isset($$check) && $submit != 'clear' && $submit != 'default') {
        print " checked";
    }
    print "></td><td width=\"30%\">$index</td><td width=\"60%\">\n";
    print addInput($index, $value, 60);
}

print "</td></tr></table></center><br>\n";
print "<center>".
addSubmit('submit','submit').
addSubmit('clear','submit').
addSubmit('default','submit').
"</center><br>\n";

$tests = array();

if ($submit == 'submit') {
    foreach ($type as $index=>$value) {
        $check = "CHECK_".$index;
        if (isset($$check)) {
            $type[$index] = $$index;
            array_push($tests, $index); 
        }
    }
    for ($i=0;$i<count($tests);$i++) {
        print "<center><table width=\"95%\" border=\"0\" bgcolor=\"".$color[4]."\">\n";
        print "<tr><td><b>".$tests[$i]."</b></td></tr>";
        print "<tr><td><font color=\"".$color[7]."\"><small><b>".
              "Request:</b></small></font></td></tr>\n";
        $response = imap_test($imap_stream, $type[$tests[$i]]);
        print "<tr><td><font color=\"".$color[7]."\"><small><b>".
              "Response:</b></small></font></td></tr>\n";
        print "<tr><td>";
        print_response($response);
        print "</td></tr></table></center><br>\n";
    }
}
    print "</form></td></tr></table></center></body></html>";
    sqimap_logout($imap_stream);
    do_hook('info_bottom');
?>
