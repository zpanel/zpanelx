<?php

/**
 * view_header.php
 *
 * This is the code to view the message header.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: view_header.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the view_header page */
define('PAGE_NAME', 'view_header');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'functions/url_parser.php');

function parse_viewheader($imapConnection,$id, $passed_ent_id) {
    global $uid_support;

    $header_full = array();
    $header_output = array();
    $second = array();
    $first = array();

    if (!$passed_ent_id) {
        $read=sqimap_run_command ($imapConnection, "FETCH $id BODY[HEADER]", 
                              true, $a, $b, $uid_support);
    } else {
        $query = "FETCH $id BODY[".$passed_ent_id.'.HEADER]';
        $read=sqimap_run_command ($imapConnection, $query, 
                              true, $a, $b, $uid_support);
    }    
    $cnum = 0;

    for ($i=1; $i < count($read); $i++) {
        $line = htmlspecialchars($read[$i]);
        switch (true) {
            case (preg_match('/^&gt;/i', $line)):
                $second[$i] = $line;
                $first[$i] = '&nbsp;';
                $cnum++;
                break;
// FIXME: is the pipe character below a mistake?  I think the original author might have thought it carried special meaning in the character class, which it does not... but then again, I am not currently trying to understand what this code actually does
            case (preg_match('/^[ |\t]/', $line)):
                $second[$i] = $line;
                $first[$i] = '';
                break;
            case (preg_match('/^([^:]+):(.+)/', $line, $regs)):
                $first[$i] = $regs[1] . ':';
                $second[$i] = $regs[2];
                $cnum++;
                break;
            default:
                $second[$i] = trim($line);
                $first[$i] = '';
                break;
        }
    }

    for ($i=0; $i < count($second); $i = $j) {
        $f = (isset($first[$i]) ? $first[$i] : '');
        $s = (isset($second[$i]) ? nl2br($second[$i]) : ''); 
        $j = $i + 1;
        while (($first[$j] == '') && ($j < count($first))) {
            $s .= '&nbsp;&nbsp;&nbsp;&nbsp;' . nl2br($second[$j]);
            $j++;
        }
        $lowf=strtolower($f);
        /* do not mark these headers as emailaddresses */
        if($lowf != 'message-id:' && $lowf != 'in-reply-to:' && $lowf != 'references:') {
            parseEmail($s);
        }
        if ($f) {
            $header_output[] = array($f,$s);
        }
    }
    return $header_output;
}

function view_header($header, $mailbox, $color) {
    sqgetGlobalVar('QUERY_STRING', $queryStr, SQ_SERVER);
    $ret_addr = SM_PATH . 'src/read_body.php?'.$queryStr;

    displayPageHeader($color, $mailbox);

    echo '<br />' .
         '<table width="100%" cellpadding="2" cellspacing="0" border="0" '.
            'align="center">' . "\n" .
         '<tr><td bgcolor="'.$color[9].'" width="100%" align="center"><b>'.
         _("Viewing Full Header") . '</b> - '.
         '<a href="'; 
    echo_template_var($ret_addr);
    echo '">' ._("View message") . "</a></b></td></tr></table>\n";

    echo_template_var($header, 
        array(
            '<table width="99%" cellpadding="2" cellspacing="0" border="0" '.
                "align=center>\n".'<tr><td>',
            '<nobr><tt><b>',
            '</b>',
            '</tt></nobr>',
            '</td></tr></table>'."\n" 
         )
    );
    echo '</body></html>';
}

/* get global vars */
if ( sqgetGlobalVar('passed_id', $temp, SQ_GET) ) {
  $passed_id = (int) $temp;
}
if ( sqgetGlobalVar('mailbox', $temp, SQ_GET) ) {
  $mailbox = $temp;
}
if ( !sqgetGlobalVar('passed_ent_id', $passed_ent_id, SQ_GET) ) {
  $passed_ent_id = '';
} 
sqgetGlobalVar('key',        $key,          SQ_COOKIE);
sqgetGlobalVar('username',   $username,     SQ_SESSION);
sqgetGlobalVar('onetimepad', $onetimepad,   SQ_SESSION);
sqgetGlobalVar('delimiter',  $delimiter,    SQ_SESSION);

$imapConnection = sqimap_login($username, $key, $imapServerAddress, 
                               $imapPort, 0);
$mbx_response = sqimap_mailbox_select($imapConnection, $mailbox, false, false, true);

$header = parse_viewheader($imapConnection,$passed_id, $passed_ent_id); 
view_header($header, $mailbox, $color);
sqimap_logout($imapConnection);
