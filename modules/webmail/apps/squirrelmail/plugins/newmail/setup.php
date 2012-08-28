<?php

/**
 * newmail.php
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Copyright (c) 2000 by Michael Huttinger
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Quite a hack -- but my first attempt at a plugin.  We were
 * looking for a way to play a sound when there was unseen
 * messages to look at.  Nice for users who keep the squirrel
 * mail window up for long periods of time and want to know
 * when mail arrives.
 *
 * Basically, I hacked much of left_main.php into a plugin that
 * goes through each mail folder and increments a flag if
 * there are unseen messages.  If the final count of unseen
 * folders is > 0, then we play a sound (using the HTML at the
 * far end of this script).
 *
 * This was tested with IE5.0 - but I hear Netscape works well,
 * too (with a plugin).
 *
 * @version $Id: setup.php 14116 2011-07-12 03:19:15Z pdontthink $
 * @package plugins
 * @subpackage new_mail
 */

/**
 * Checks if mailbox contains new messages.
 *
 * @param stream $imapConnection
 * @param string $mailbox FIXME: option is not used
 * @param string $real_box unformated mailbox name
 * @param string $delimeter FIXME: option is not used
 * @param string $unseen FIXME: option is not used
 * @param integer $total_new number of new messages
 * @return bool true, if there are new messages
 */
function CheckNewMailboxSound($imapConnection, $mailbox, $real_box, $delimeter, $unseen, &$total_new) {
    global $folder_prefix, $trash_folder, $sent_folder,
        $color, $move_to_sent, $move_to_trash,
        $unseen_notify, $unseen_type, $newmail_allbox, 
        $newmail_recent, $newmail_changetitle;

    $mailboxURL = urlencode($real_box);
    $unseen = $recent = 0;

    // Skip folders for Sent and Trash

    if ($real_box == $sent_folder ||
        $real_box == $trash_folder) {
        return 0;
    }

    if (($unseen_notify == 2 && $real_box == 'INBOX') ||
        ($unseen_notify == 3 && ($newmail_allbox == 'on' ||
                                 $real_box == 'INBOX'))) {
        $status = sqimap_status_messages( $imapConnection, $real_box);
        if($newmail_recent == 'on') {
            $total_new += $status['RECENT'];
        } else {
            $total_new += $status['UNSEEN'];
        }
        if ($total_new) {
            return 1;
        }
    }
    return 0;
}

function squirrelmail_plugin_init_newmail() {
    global $squirrelmail_plugin_hooks;

    $squirrelmail_plugin_hooks['left_main_before']['newmail'] = 'newmail_plugin';
    $squirrelmail_plugin_hooks['optpage_register_block']['newmail'] = 'newmail_optpage_register_block';
    $squirrelmail_plugin_hooks['options_save']['newmail'] = 'newmail_sav';
    $squirrelmail_plugin_hooks['loading_prefs']['newmail'] = 'newmail_pref';
    $squirrelmail_plugin_hooks['optpage_set_loadinfo']['newmail'] = 'newmail_set_loadinfo';
}

function newmail_optpage_register_block() {
    // Gets added to the user's OPTIONS page.
    global $optpage_blocks;

    if ( !soupNazi() ) {

        /* Register Squirrelspell with the $optionpages array. */
        $optpage_blocks[] = array(
            'name' => _("NewMail Options"),
            'url'  => SM_PATH . 'plugins/newmail/newmail_opt.php',
            'desc' => _("This configures settings for playing sounds and/or showing popup windows when new mail arrives."),
            'js'   => TRUE
            );
    }
}

function newmail_sav() {
    global $data_dir, $username;

    if ( sqgetGlobalVar('submit_newmail', $submit, SQ_POST) ) {
        $media_enable = '';
        $media_popup = '';
        $media_allbox = '';
        $media_recent = '';
        $media_changetitle = '';
        $media_sel = '';

        sqgetGlobalVar('media_enable',         $media_enable,         SQ_POST);
        sqgetGlobalVar('media_popup',          $media_popup,          SQ_POST);
        sqgetGlobalVar('media_allbox',         $media_allbox,         SQ_POST);
        sqgetGlobalVar('media_recent',         $media_recent,         SQ_POST);
        sqgetGlobalVar('media_changetitle',    $media_changetitle,    SQ_POST);
        sqgetGlobalVar('popup_height',         $newmail_popup_height, SQ_POST);
        sqgetGlobalVar('popup_width',          $newmail_popup_width,  SQ_POST);        

        setPref($data_dir,$username,'newmail_enable',$media_enable);
        setPref($data_dir,$username,'newmail_popup', $media_popup);
        setPref($data_dir,$username,'newmail_allbox',$media_allbox);
        setPref($data_dir,$username,'newmail_recent',$media_recent);
        setPref($data_dir,$username,'newmail_popup_height',$newmail_popup_height);
        setPref($data_dir,$username,'newmail_popup_width',$newmail_popup_width);
        setPref($data_dir,$username,'newmail_changetitle',$media_changetitle);
            
        if( sqgetGlobalVar('media_sel', $media_sel, SQ_POST) &&
            ($media_sel == '(none)' || $media_sel == '(local media)') ) {
            removePref($data_dir,$username,'newmail_media');
        } else {
            setPref($data_dir,$username,'newmail_media',$media_sel);
        }
    }
}

function newmail_pref() {
    global $username, $data_dir, $newmail_media, $newmail_enable, $newmail_popup,
           $newmail_allbox, $newmail_recent, $newmail_changetitle, $newmail_popup_height,
           $newmail_popup_width;
    

    $newmail_recent = getPref($data_dir,$username,'newmail_recent');
    $newmail_enable = getPref($data_dir,$username,'newmail_enable');
    $newmail_media = getPref($data_dir, $username, 'newmail_media', '(none)');
    $newmail_popup = getPref($data_dir, $username, 'newmail_popup');
    $newmail_allbox = getPref($data_dir, $username, 'newmail_allbox');
    $newmail_popup_height = getPref($data_dir, $username, 'newmail_popup_height',130);
    $newmail_popup_width = getPref($data_dir, $username, 'newmail_popup_width',200);
    $newmail_changetitle = getPref($data_dir, $username, 'newmail_changetitle');

}

/**
 * Set loadinfo data
 *
 * Used by option page when saving settings.
 */
function newmail_set_loadinfo() {
    global $optpage, $optpage_name;
    if ($optpage=='newmail') {
        $optpage_name=_("NewMail Options");
    }
}

function newmail_plugin() {
    global $username, $key, $imapServerAddress, $imapPort,
        $newmail_media, $newmail_enable, $newmail_popup,
        $newmail_popup_height, $newmail_popup_width, $newmail_recent, 
        $newmail_changetitle, $imapConnection;

    include_once(SM_PATH . 'functions/display_messages.php');

    if ($newmail_enable == 'on' ||
        $newmail_popup == 'on' ||
        $newmail_changetitle) {

        // open a connection on the imap port (143)
        $boxes = sqimap_mailbox_list($imapConnection);
        $delimeter = sqimap_get_delimiter($imapConnection);

        $status = 0;
        $totalNew = 0;

        for ($i = 0;$i < count($boxes); $i++) {

            $line = '';
            $mailbox = $boxes[$i]['formatted'];

            if (! isset($boxes[$i]['unseen'])) {
                $boxes[$i]['unseen'] = '';
            }
            if ($boxes[$i]['flags']) {
                $noselect = false;
                for ($h = 0; $h < count($boxes[$i]['flags']); $h++) {
                    if (strtolower($boxes[$i]["flags"][$h]) == 'noselect') {
                        $noselect = TRUE;
                    }
                }
                if (! $noselect) {
                    $status += CheckNewMailboxSound($imapConnection, 
                                                    $mailbox,
                                                    $boxes[$i]['unformatted'], 
                                                    $delimeter, 
                                                    $boxes[$i]['unseen'],
                                                    $totalNew);
                }
            } else {
                $status += CheckNewMailboxSound($imapConnection, 
                                                $mailbox, 
                                                $boxes[$i]['unformatted'],
                                                $delimeter, 
                                                $boxes[$i]['unseen'], 
                                                $totalNew);
            }

        }

        // sqimap_logout($imapConnection);

        // If we found unseen messages, then we
        // will play the sound as follows:

        if ($newmail_changetitle) {
            global $org_title;
            echo "<script language=\"javascript\" type=\"text/javascript\">\n" .
                "function ChangeTitleLoad() {\n";
            if( $totalNew > 1 || $totalNew == 0 ) {
                echo 'window.parent.document.title = "' . $org_title . ' [' .
                    sprintf(_("%s New Messages"), $totalNew ) . 
                    "]\";\n";
            } else {
                echo 'window.parent.document.title = "' . $org_title . ' [' .
                    sprintf(_("%s New Message"), $totalNew ) . 
                    "]\";\n";
            }
            echo    "if (BeforeChangeTitle != null)\n".
                "BeforeChangeTitle();\n".
                "}\n".
                "BeforeChangeTitle = window.onload;\n".
                "window.onload = ChangeTitleLoad;\n".
                "</script>\n";
        }

        if ($totalNew > 0 && $newmail_enable == 'on' && $newmail_media != '' && $newmail_media != '(none)') {
            $newmail_media=sqm_baseuri().'plugins/newmail/sounds/'.basename($newmail_media);
            echo '<embed src="'.htmlspecialchars($newmail_media)
                ."\" hidden=\"true\" autostart=\"true\" width=\"2\" height=\"2\">\n";
        }
        if ($totalNew > 0 && $newmail_popup == 'on') {
            echo "<script language=\"JavaScript\">\n".
                "<!--\n".
                "function PopupScriptLoad() {\n".
                'window.open("'.sqm_baseuri().'plugins/newmail/newmail.php?numnew='.$totalNew.
                '", "SMPopup",'.
                "\"width=" . (int)$newmail_popup_width . ",height=" . (int)$newmail_popup_height . ",scrollbars=no\");\n".
                "if (BeforePopupScript != null)\n".
                "BeforePopupScript();\n".
                "}\n".
                "BeforePopupScript = window.onload;\n".
                "window.onload = PopupScriptLoad;\n".
                // Idea by:  Nic Wolfe (Nic@TimelapseProductions.com)
                // Web URL:  http://fineline.xs.mw
                // More code from Tyler Akins
                "// End -->\n".
                "</script>\n";
        }
    }
}
