<?php

/**
 * mail_fetch/functions.php
 *
 * Functions for the mailfetch plugin.
 *
 * Original code from LexZEUS <lexzeus@mifinca.com>
 * and josh@superfork.com (extracted from PHP manual)
 * Adapted for MailFetch by Philippe Mingo <mingo@rotedic.com>
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: functions.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage mail_fetch
 */

/** declare plugin globals */
global $mail_fetch_allow_unsubscribed;

/**
 * Controls use of unsubscribed folders in plugin
 * @global boolean $mail_fetch_allow_unsubscribed
 * @since 1.5.1 and 1.4.5
 */
$mail_fetch_allow_unsubscribed = false;

/**
  * Validate a requested POP3 port number
  *
  * Allowable port numbers are configured in config.php
  * (see config_example.php for an example and more
  * rules about how the list of allowable port numbers
  * can be specified)
  *
  * @param int $requested_port The port number given by the user
  *
  * @return string An error string is returned if the port
  *                number is not allowable, otherwise an
  *                empty string is returned.
  *
  */
function validate_mail_fetch_port_number($requested_port) {
    global $mail_fetch_allowable_ports;
    @include_once(SM_PATH . 'plugins/mail_fetch/config.php');
    if (empty($mail_fetch_allowable_ports))
        $mail_fetch_allowable_ports = array(110, 995);

    if (in_array('ALL', $mail_fetch_allowable_ports))
        return '';

    if (!in_array($requested_port, $mail_fetch_allowable_ports)) {
        sq_change_text_domain('mail_fetch');
        $error = _("Sorry, that port number is not allowed");
        sq_change_text_domain('squirrelmail');
        return $error;
    }

    return '';
}

/**
  * Validate a requested POP3 server address
  *
  * Blocked server addresses are configured in config.php
  * (see config_example.php for more details)
  *
  * @param int $requested_address The server address given by the user
  *
  * @return string An error string is returned if the server
  *                address is not allowable, otherwise an
  *                empty string is returned.
  *
  */
function validate_mail_fetch_server_address($requested_address) {
    global $mail_fetch_block_server_pattern;
    @include_once(SM_PATH . 'plugins/mail_fetch/config.php');
    if (empty($mail_fetch_block_server_pattern))
        $mail_fetch_block_server_pattern = '/(^10\.)|(^192\.)|(^127\.)|(^localhost)/';

    if ($mail_fetch_block_server_pattern == 'UNRESTRICTED')
        return '';

    if (preg_match($mail_fetch_block_server_pattern, $requested_address)) {
        sq_change_text_domain('mail_fetch');
        $error = _("Sorry, that server address is not allowed");
        sq_change_text_domain('squirrelmail');
        return $error;
    }

    return '';
}

function hex2bin( $data ) {
    /* Original code by josh@superfork.com */

    $len = strlen($data);
    $newdata = '';
    for( $i=0; $i < $len; $i += 2 ) {
        $newdata .= pack( "C", hexdec( substr( $data, $i, 2) ) );
    }
    return $newdata;
}

function mf_keyED( $txt ) {
    global $MF_TIT;

    if( !isset( $MF_TIT ) ) {
        $MF_TIT = "MailFetch Secure for SquirrelMail 1.x";
    }

    $encrypt_key = md5( $MF_TIT );
    $ctr = 0;
    $tmp = "";
    for( $i = 0; $i < strlen( $txt ); $i++ ) {
        if( $ctr == strlen( $encrypt_key ) ) $ctr=0;
        $tmp.= substr( $txt, $i, 1 ) ^ substr( $encrypt_key, $ctr, 1 );
        $ctr++;
    }
    return $tmp;
}

function encrypt( $txt ) {
    srand( (double) microtime() * 1000000 );
    $encrypt_key = md5( rand( 0, 32000 ) );
    $ctr = 0;
    $tmp = "";
    for( $i = 0; $i < strlen( $txt ); $i++ ) {
        if ($ctr==strlen($encrypt_key)) $ctr=0;
        $tmp.= substr($encrypt_key,$ctr,1) .
            (substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1));
        $ctr++;
    }
    return bin2hex( mf_keyED( $tmp ) );
}

function decrypt( $txt ) {
    $txt = mf_keyED( hex2bin( $txt ) );
    $tmp = '';
    for ( $i=0; $i < strlen( $txt ); $i++ ) {
        $md5 = substr( $txt, $i, 1 );
        $i++;
        $tmp.= ( substr( $txt, $i, 1 ) ^ $md5 );
    }
    return $tmp;
}

/**
 * check mail folder
 * @param stream $imap_stream imap connection resource
 * @param string $imap_folder imap folder name
 * @return boolean true, when folder can be used to store messages.
 * @since 1.5.1 and 1.4.5
 */
function mail_fetch_check_folder($imap_stream,$imap_folder) {
    global $mail_fetch_allow_unsubscribed;

    // check if folder is subscribed or only exists.
    if (sqimap_mailbox_is_subscribed($imap_stream,$imap_folder)) {
        $ret = true;
    } elseif ($mail_fetch_allow_unsubscribed && sqimap_mailbox_exists($imap_stream,$imap_folder)) {
        $ret = true;
    } else {
        $ret = false;
    }

    // make sure that folder can store messages
    if ($ret && mail_fetch_check_noselect($imap_stream,$imap_folder)) {
        $ret = false;
    }

    return $ret;
}

/**
 * Checks if folder is noselect (can't store messages)
 * 
 * Function does not check if folder subscribed.
 * @param stream $imap_stream imap connection resource
 * @param string $imap_folder imap folder name
 * @return boolean true, when folder has noselect flag. false in any other case.
 * @since 1.5.1 and 1.4.5
 */
function mail_fetch_check_noselect($imap_stream,$imap_folder) {
    $boxes=sqimap_mailbox_list($imap_stream);
    foreach($boxes as $box) {
        if ($box['unformatted']==$imap_folder) {
            return (bool) check_is_noselect($box['raw']);
        }
    }
    return false;
}
