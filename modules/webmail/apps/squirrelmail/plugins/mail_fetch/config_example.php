<?php

/**
 * mail_fetch/config_example.php
 *
 * Configuration file for the mailfetch plugin.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: functions.php 13893 2010-01-25 02:47:41Z pdontthink $
 * @package plugins
 * @subpackage mail_fetch
 */

global $mail_fetch_allowable_ports, $mail_fetch_block_server_pattern;


// This is the list of POP3 ports the user may specify.
//
// Usually, this does not need to be used at all, and
// ports 110 and 995 will be the only available ports.
//
// If users are allowed to access POP3 that is served
// on a non-standard port, you'll need to add that port
// to this list and make sure this file is saved as
// "config.php" in the mail_fetch plugin directory
//
// If you do not wish to restrict the allowable port
// numbers at all, include "ALL" in this list.
//
$mail_fetch_allowable_ports = array(110, 995);



// This is a pattern match that allows you to block
// access to certain server addresses.  This prevents
// a user from attempting to try to specify certain
// servers when adding a POP3 address.
//
// By default, this plugin will block POP3 server
// addresses starting with "10.", "192.", "127." and
// "localhost" (the pattern shown below).
// 
// If you want to block other addresses, you'll need
// to add them to this pattern and make sure that this
// file is saved as "config.php" in the mail_fetch
// plugin diretory
//
// If you do not wish to restrict the allowable server
// addresses at all, set this value to be "UNRESTRICTED"
//
// This is a full regular expression pattern
//
// Allow anything:
//
// $mail_fetch_block_server_pattern = 'UNRESTRICTED';
//
// Default pattern:
//
$mail_fetch_block_server_pattern = '/(^10\.)|(^192\.)|(^127\.)|(^localhost)/';


