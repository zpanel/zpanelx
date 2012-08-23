<?php

/**
 * mail_fetch/setup.php
 *
 * Setup of the mailfetch plugin.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage mail_fetch
 */

/** @ignore*/
if (! defined('SM_PATH')) define('SM_PATH','../../');

// FIXME: do we have to include them here.
require_once(SM_PATH . 'plugins/mail_fetch/functions.php' );

function squirrelmail_plugin_init_mail_fetch() {
    global $squirrelmail_plugin_hooks;

    $squirrelmail_plugin_hooks['menuline']['mail_fetch'] = 'mail_fetch_link';
    $squirrelmail_plugin_hooks['loading_prefs']['mail_fetch'] = 'mail_fetch_load_pref';
    $squirrelmail_plugin_hooks['login_verified']['mail_fetch'] = 'mail_fetch_setnew';
    $squirrelmail_plugin_hooks['left_main_before']['mail_fetch'] = 'mail_fetch_login';
    $squirrelmail_plugin_hooks['optpage_register_block']['mail_fetch'] = 'mailfetch_optpage_register_block';
    $squirrelmail_plugin_hooks['rename_or_delete_folder']['mail_fetch'] = 'mail_fetch_folderact';
}

function mail_fetch_link() {
    displayInternalLink('plugins/mail_fetch/fetch.php', _("Fetch"), '');
    echo '&nbsp;&nbsp;';
}

function mail_fetch_load_pref() {
    global $data_dir;
    global $mailfetch_server_number;
    global $mailfetch_cypher, $mailfetch_port_;
    global $mailfetch_server_,$mailfetch_alias_,$mailfetch_user_,$mailfetch_pass_;
    global $mailfetch_lmos_, $mailfetch_uidl_, $mailfetch_login_, $mailfetch_fref_;
    global $PHP_SELF;

    sqgetGlobalVar('username', $username, SQ_SESSION);

    if( stristr( $PHP_SELF, 'mail_fetch' ) ) {
        $mailfetch_server_number = getPref($data_dir, $username, 'mailfetch_server_number', 0);
        $mailfetch_cypher = getPref($data_dir, $username, 'mailfetch_cypher', 'on' );
        if ($mailfetch_server_number<1) $mailfetch_server_number=0;
        for ($i=0;$i<$mailfetch_server_number;$i++) {
            $mailfetch_server_[$i] = getPref($data_dir, $username, "mailfetch_server_$i");
            $mailfetch_port_[$i] = getPref($data_dir, $username, "mailfetch_port_$i");
            $mailfetch_alias_[$i]  = getPref($data_dir, $username, "mailfetch_alias_$i");
            $mailfetch_user_[$i]   = getPref($data_dir, $username, "mailfetch_user_$i");
            $mailfetch_pass_[$i]   = getPref($data_dir, $username, "mailfetch_pass_$i");
            $mailfetch_lmos_[$i]   = getPref($data_dir, $username, "mailfetch_lmos_$i");
            $mailfetch_login_[$i]  = getPref($data_dir, $username, "mailfetch_login_$i");
            $mailfetch_fref_[$i]   = getPref($data_dir, $username, "mailfetch_fref_$i");
            $mailfetch_uidl_[$i]   = getPref($data_dir, $username, "mailfetch_uidl_$i");
            if( $mailfetch_cypher   == 'on' ) $mailfetch_pass_[$i] =    decrypt( $mailfetch_pass_[$i] );
        }
    }
}

function mail_fetch_login() {
    require_once (SM_PATH . 'include/validate.php');
    include_once (SM_PATH . 'functions/imap.php');
    require_once (SM_PATH . 'plugins/mail_fetch/class.POP3.php');
    require_once (SM_PATH . 'plugins/mail_fetch/functions.php');

    global $data_dir, $imapServerAddress, $imapPort;
        
    sqgetGlobalVar('username', $username, SQ_SESSION);
    sqgetGlobalVar('key',      $key,      SQ_COOKIE);

    $mailfetch_newlog = getPref($data_dir, $username, 'mailfetch_newlog');

    $outMsg = '';

    $mailfetch_server_number = getPref($data_dir, $username, 'mailfetch_server_number');
    if (!isset($mailfetch_server_number)) $mailfetch_server_number=0;
    $mailfetch_cypher = getPref($data_dir, $username, 'mailfetch_cypher');
    if ($mailfetch_server_number<1) $mailfetch_server_number=0;

    for ($i_loop=0;$i_loop<$mailfetch_server_number;$i_loop++) {

        $mailfetch_login_[$i_loop] = getPref($data_dir, $username, "mailfetch_login_$i_loop");
        $mailfetch_fref_[$i_loop] = getPref($data_dir, $username, "mailfetch_fref_$i_loop");
        $mailfetch_pass_[$i_loop] = getPref($data_dir, $username, "mailfetch_pass_$i_loop");
        if( $mailfetch_cypher == 'on' )
            $mailfetch_pass_[$i_loop] = decrypt( $mailfetch_pass_[$i_loop] );

        if( $mailfetch_pass_[$i_loop] <> '' &&          // Empty passwords no allowed
            ( ( $mailfetch_login_[$i_loop] == 'on' &&  $mailfetch_newlog == 'on' ) || $mailfetch_fref_[$i_loop] == 'on' ) ) {

            $mailfetch_server_[$i_loop] = getPref($data_dir, $username, "mailfetch_server_$i_loop");
            $mailfetch_port_[$i_loop] = getPref($data_dir, $username , "mailfetch_port_$i_loop");
            $mailfetch_alias_[$i_loop] = getPref($data_dir, $username, "mailfetch_alias_$i_loop");
            $mailfetch_user_[$i_loop] = getPref($data_dir, $username, "mailfetch_user_$i_loop");
            $mailfetch_lmos_[$i_loop] = getPref($data_dir, $username, "mailfetch_lmos_$i_loop");
            $mailfetch_uidl_[$i_loop] = getPref($data_dir, $username, "mailfetch_uidl_$i_loop");
            $mailfetch_subfolder_[$i_loop] = getPref($data_dir, $username, "mailfetch_subfolder_$i_loop");

            $mailfetch_server=$mailfetch_server_[$i_loop];
            $mailfetch_port=$mailfetch_port_[$i_loop];
            $mailfetch_user=$mailfetch_user_[$i_loop];
            $mailfetch_alias=$mailfetch_alias_[$i_loop];
            $mailfetch_pass=$mailfetch_pass_[$i_loop];
            $mailfetch_lmos=$mailfetch_lmos_[$i_loop];
            $mailfetch_login=$mailfetch_login_[$i_loop];
            $mailfetch_uidl=$mailfetch_uidl_[$i_loop];
            $mailfetch_subfolder=$mailfetch_subfolder_[$i_loop];

            // $outMsg .= "$mailfetch_alias checked<br>";

            // $outMsg .= "$mailfetch_alias_[$i_loop]<br>";

            $pop3 = new POP3($mailfetch_server, 60);

            if (!$pop3->connect($mailfetch_server,$mailfetch_port)) {
                $outMsg .= _("Warning, ") . $pop3->ERROR;
                continue;
            }

            $imap_stream = sqimap_login($username, $key, $imapServerAddress, $imapPort, 10);

            $Count = $pop3->login($mailfetch_user, $mailfetch_pass);
            if (($Count == false || $Count == -1) && $pop3->ERROR != '') {
                $outMsg .= _("Login Failed:") . $pop3->ERROR;
                continue;
            }

            //   register_shutdown_function($pop3->quit());

            $msglist = $pop3->uidl();

            $i = 1;
            for ($j = 1; $j < sizeof($msglist); $j++) {
                if ($msglist["$j"] == $mailfetch_uidl) {
                    $i = $j+1;
                    break;
                }
            }

            if ($Count < $i) {
                $pop3->quit();
                continue;
            }
            if ($Count == 0) {
                $pop3->quit();
                continue;
            } else {
                $newmsgcount = $Count - $i + 1;
            }

            // Faster to get them all at once
            $mailfetch_uidl = $pop3->uidl();

            if (! is_array($mailfetch_uidl) && $mailfetch_lmos == 'on')
                $outMsg .= _("Server does not support UIDL.");

            for (; $i <= $Count; $i++) {
                if (!ini_get('safe_mode'))
                    set_time_limit(20); // 20 seconds per message max
                $Message = "";
                $MessArray = $pop3->get($i);

                if ( (!$MessArray) or (gettype($MessArray) != "array")) {
                    $outMsg .= _("Warning, ") . $pop3->ERROR;
                    continue 2;
                }

                while (list($lineNum, $line) = each ($MessArray)) {
                    $Message .= $line;
                }

                /**
                 * check if mail folder is not null and subscribed
                 * Function can check if mail folder is only unsubscribed 
                 * and use unsubscribed mail folder.
                 */
                if ($mailfetch_subfolder=='' || 
                    ! mail_fetch_check_folder($imap_stream,$mailfetch_subfolder)) {
                    fputs($imap_stream, "A3$i APPEND INBOX {" . strlen($Message) . "}\r\n");
                } else {
                    fputs($imap_stream, "A3$i APPEND $mailfetch_subfolder {" . strlen($Message) . "}\r\n");
                }
                $Line = fgets($imap_stream, 1024);
                if (substr($Line, 0, 1) == '+') {
                    fputs($imap_stream, $Message);
                    fputs($imap_stream, "\r\n");
                    sqimap_read_data($imap_stream, "A3$i", false, $response, $message);

                    if ($mailfetch_lmos != 'on') {
                        $pop3->delete($i);
                    }
                } else {
                    echo "$Line";
                    $outMsg .= _("Error Appending Message!");
                }
            }

            $pop3->quit();
            sqimap_logout($imap_stream);
            if (is_array($mailfetch_uidl)) {
                setPref($data_dir,$username,"mailfetch_uidl_$i_loop", array_pop($mailfetch_uidl));
            }
        }
    }

    if( trim( $outMsg ) <> '' ) {
        echo '<br><font size="1">' . _("Mail Fetch Result:") . "<br>$outMsg</font>";
    }
    if( $mailfetch_newlog == 'on' ) {
        setPref($data_dir, $username, 'mailfetch_newlog', 'off');
    }
}

function mail_fetch_setnew()    {

    global $data_dir;
    require_once(SM_PATH . 'functions/prefs.php');

    sqgetGlobalVar('username', $username, SQ_SESSION);

    setPref( $data_dir, $username, 'mailfetch_newlog', 'on' );
}

function mailfetch_optpage_register_block() {
    global $optpage_blocks;

    $optpage_blocks[] = array(
        'name' => _("POP3 Fetch Mail"),
        'url'  => '../plugins/mail_fetch/options.php',
        'desc' => _("This configures settings for downloading email from a POP3 mailbox to your account on this server."),
        'js'   => false
        );
}

function mail_fetch_folderact($args) {
    global $username, $data_dir;

    if (empty($args) || !is_array($args)) {
        return;
    }

    /* Should be 3 ars, 1: old folder, 2: action, 3: new folder */
    if (count($args) != 3) {
        return;
    }

    list($old_folder, $action, $new_folder) = $args;

    $mailfetch_server_number = getPref($data_dir, $username, 'mailfetch_server_number');

    for ($i = 0; $i < $mailfetch_server_number; $i++) {
        $mailfetch_subfolder = getPref($data_dir, $username, 'mailfetch_subfolder_' . $i);

        if ($mailfetch_subfolder != $old_folder) {
            continue;
        }

        if ($action == 'delete') {
            setPref($data_dir, $username, 'mailfetch_subfolder_' . $i, 'INBOX');
        } elseif ($action == 'rename') {
            setPref($data_dir, $username, 'mailfetch_subfolder_' . $i, $new_folder);
        }
    }
}
