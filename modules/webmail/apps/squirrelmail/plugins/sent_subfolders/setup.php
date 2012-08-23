<?php

/**
 * setup.php -- Sent Subfolders Setup File
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This is a standard Squirrelmail-1.2 API for plugins.
 *
 * $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage sent_subfolders
 */

/** 
 * 
 */
define('SMPREF_SENT_SUBFOLDERS_DISABLED',  0);
define('SMPREF_SENT_SUBFOLDERS_YEARLY',    1);
define('SMPREF_SENT_SUBFOLDERS_QUARTERLY', 2);
define('SMPREF_SENT_SUBFOLDERS_MONTHLY',   3);
define('SMOPT_GRP_SENT_SUBFOLDERS','SENT_SUBFOLDERS');

/**
 * Adds plugin to squirrelmail hooks
 */
function squirrelmail_plugin_init_sent_subfolders() {
    /* Standard initialization API. */
    global $squirrelmail_plugin_hooks;

    /* The hooks to make the sent subfolders display correctly. */
    $squirrelmail_plugin_hooks
    ['check_handleAsSent_result']['sent_subfolders'] =
        'sent_subfolders_check_handleAsSent';

    /* The hooks to automatically update sent subfolders. */
    $squirrelmail_plugin_hooks
    ['left_main_before']['sent_subfolders'] =
        'sent_subfolders_update_sentfolder';

    $squirrelmail_plugin_hooks
    ['compose_send']['sent_subfolders'] =
        'sent_subfolders_update_sentfolder';

    /* The hook to load the sent subfolders prefs. */
    $squirrelmail_plugin_hooks
    ['loading_prefs']['sent_subfolders'] =
        'sent_subfolders_load_prefs';

    /* The hooks to handle sent subfolders options. */
    $squirrelmail_plugin_hooks
    ['optpage_loadhook_folder']['sent_subfolders'] =
        'sent_subfolders_optpage_loadhook_folders';

    /* mark base sent folder as special mailbox */
    $squirrelmail_plugin_hooks
    ['special_mailbox']['sent_subfolders'] = 
	'sent_subfolders_special_mailbox';
}

function sent_subfolders_check_handleAsSent() {
    global $handleAsSent_result, $sent_subfolders_base,
           $use_sent_subfolders;

    $args = func_get_arg(0);
    sqgetGlobalVar('delimiter', $delimiter, SQ_SESSION);

    /* Only check the folder string if we have been passed a mailbox. */
    if ($use_sent_subfolders && (count($args) > 1)) {
        /* Chop up the folder strings as needed. */
        $base_str = $sent_subfolders_base . $delimiter;
        $mbox_str = substr($args[1], 0, strlen($base_str));

        /* Perform the comparison. */
        $handleAsSent_result =
            ( $handleAsSent_result
            || ($base_str == $mbox_str)
            || ($sent_subfolders_base == $args[1])
            );
    }
}

/**
 * Loads sent_subfolders settings
 */
function sent_subfolders_load_prefs() {
    global $use_sent_subfolders, $data_dir, $username, $sent_folder,
           $sent_subfolders_setting, $sent_subfolders_base;

    $use_sent_subfolders = getPref
    ($data_dir, $username, 'use_sent_subfolders', SMPREF_OFF);

    $sent_subfolders_setting = getPref
    ($data_dir, $username, 'sent_subfolders_setting', SMPREF_SENT_SUBFOLDERS_DISABLED);

    $sent_subfolders_base = getPref
    ($data_dir, $username, 'sent_subfolders_base', $sent_folder);
}

/**
 * Adds sent_subfolders options in folder preferences
 */
function sent_subfolders_optpage_loadhook_folders() {
    global $optpage_data, $imapServerAddress, $imapPort;

    sqgetGlobalVar('username', $username, SQ_SESSION);
    sqgetGlobalVar('key', $key, SQ_COOKIE);

    /* Get some imap data we need later. */
    $imapConnection =
        sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
    $boxes = sqimap_mailbox_list($imapConnection);
    sqimap_logout($imapConnection);

    /* Load the Sent Subfolder Options into an array. */
    $optgrp = _("Sent Subfolders Options");
    $optvals = array();

    $optvals[] = array(
        'name'    => 'sent_subfolders_setting',
        'caption' => _("Use Sent Subfolders"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_FOLDERLIST,
        'posvals' => array(SMPREF_SENT_SUBFOLDERS_DISABLED  => _("Disabled"),
                        SMPREF_SENT_SUBFOLDERS_MONTHLY   => _("Monthly"),
                        SMPREF_SENT_SUBFOLDERS_QUARTERLY => _("Quarterly"),
                        SMPREF_SENT_SUBFOLDERS_YEARLY    => _("Yearly")),
        'save'    => 'save_option_sent_subfolders_setting'
    );

    $filtered_folders=array_filter($boxes, "filter_folders");
    $sent_subfolders_base_values = array('whatever'=>$filtered_folders);

    $optvals[] = array(
        'name'    => 'sent_subfolders_base',
        'caption' => _("Base Sent Folder"),
        'type'    => SMOPT_TYPE_FLDRLIST,
        'refresh' => SMOPT_REFRESH_FOLDERLIST,
        'posvals' => $sent_subfolders_base_values,
        'save'    => 'save_option_sent_subfolders_base',
    );

    /* Add our option data to the global array. */
    $optpage_data['grps'][SMOPT_GRP_SENT_SUBFOLDERS] = $optgrp;
    $optpage_data['vals'][SMOPT_GRP_SENT_SUBFOLDERS] = $optvals;
}

/**
 * Defines folder filtering rules
 *
 * Callback function that should exclude some folders from folder listing.
 * @param array $fldr list of folders. See sqimap_mailbox_list
 * @return boolean returns true, if folder has to included in folder listing
 * @access private 
 */
function filter_folders($fldr) {
    return strtolower($fldr['unformatted'])!='inbox';
}

/**
 * Saves sent_subfolder_options
 */
function save_option_sent_subfolders_setting($option) {
    global $data_dir, $username, $use_sent_subfolders, $sent_subfolders_base;

    /* Set use_sent_subfolders as either on or off. */
    if ($option->new_value == SMPREF_SENT_SUBFOLDERS_DISABLED) {
        setPref($data_dir, $username, 'use_sent_subfolders', SMPREF_OFF);
        // for lack of anything better
        setPref($data_dir, $username, 'sent_folder', $sent_subfolders_base);
    } else {
        setPref($data_dir, $username, 'use_sent_subfolders', SMPREF_ON);
        setPref($data_dir, $username, 'move_to_sent', SMPREF_ON);
    }

    /* Now just save the option as normal. */
    save_option($option);
}

/**
 * Update sent_subfolders settings
 *
 * function updates default sent folder value and 
 * creates required imap folders
 */
function sent_subfolders_update_sentfolder() {
    global $sent_folder, $auto_create_special, $auto_create_done;
    global $sent_subfolders_base, $sent_subfolders_setting;
    global $data_dir, $imapServerAddress, $imapPort;
    global $use_sent_subfolders, $move_to_sent, $imap_server_type;

    sqgetGlobalVar('username', $username, SQ_SESSION);
    sqgetGlobalVar('key', $key, SQ_COOKIE);
    sqgetGlobalVar('delimiter', $delimiter, SQ_SESSION);
    
    if ($use_sent_subfolders || $move_to_sent) {
        $year = date('Y');
        $month = date('m');
        $quarter = sent_subfolder_getQuarter($month);

        /*
            Regarding the structure we've got three main possibilities.
            One sent holder. level 0.
            Multiple year holders with messages in it. level 1.
            Multiple year folders with holders in it. level 2.
        */
/*
        if( $imap_server_type == 'uw' ) {
            $cnd_delimiter = '';
        } else {
            $cnd_delimiter = $delimiter;
        }
*/        
        $cnd_delimiter = $delimiter;
                                        
        switch ($sent_subfolders_setting) {
        case SMPREF_SENT_SUBFOLDERS_YEARLY:
            $level = 1;
            $sent_subfolder = $sent_subfolders_base . $cnd_delimiter
                            . $year;
            break;
        case SMPREF_SENT_SUBFOLDERS_QUARTERLY:
            $level = 2;
            $sent_subfolder = $sent_subfolders_base . $cnd_delimiter 
                            . $year
                            . $delimiter . $quarter;
            $year_folder = $sent_subfolders_base . $cnd_delimiter
                            . $year;
            break;
        case SMPREF_SENT_SUBFOLDERS_MONTHLY:
            $level = 2;
            $sent_subfolder = $sent_subfolders_base . $cnd_delimiter
                            . $year
                            . $delimiter . $month;
            $year_folder = $sent_subfolders_base . $cnd_delimiter . $year;
            break;
        case SMPREF_SENT_SUBFOLDERS_DISABLED:
        default:
            $level = 0;
            $sent_subfolder = $sent_folder;
            $year_folder = $sent_folder;
        }

        /* If this folder is NOT the current sent folder, update stuff. */
        if ($sent_subfolder != $sent_folder) {
            /* First, update the sent folder. */

            setPref($data_dir, $username, 'sent_folder', $sent_subfolder);
            setPref($data_dir, $username, 'move_to_sent', SMPREF_ON);
            $sent_folder = $sent_subfolder;
            $move_to_sent = SMPREF_ON;

            /* Auto-create folders, if they do not yet exist. */
            if ($sent_folder != 'none') {
                /* Create the imap connection. */
                $ic = sqimap_login
                ($username, $key, $imapServerAddress, $imapPort, 10);

                /* Auto-create the year folder, if it does not yet exist.
                   (only for monthly/quarterly modes */
                if ($sent_subfolders_setting != SMPREF_SENT_SUBFOLDERS_YEARLY) {
                    if (!sqimap_mailbox_exists($ic, $year_folder)) {
                        sqimap_mailbox_create($ic, $year_folder, ($level==1)?'':'noselect');
                    } else if (!sqimap_mailbox_is_subscribed($ic, $year_folder)) {
                        sqimap_subscribe($ic, $year_folder);
                    }
                }

                /* Auto-create the subfolder, if it does not yet exist. */
                if (!sqimap_mailbox_exists($ic, $sent_folder)) {
                    sqimap_mailbox_create($ic, $sent_folder, '');
                } else if (!sqimap_mailbox_is_subscribed($ic, $sent_subfolder)) {
                    sqimap_subscribe($ic, $sent_subfolder);
                }

                /* Close the imap connection. */
                sqimap_logout($ic);
            }
        }
    }
}

/**
 * Update the folder settings/auto-create new subfolder
 */
function save_option_sent_subfolders_base($option) {
    // first save the option as normal
    save_option($option);

    // now update folder settings and auto-create first subfolder if needed
    sent_subfolders_update_sentfolder();
}

/**
 * Sets quarter subfolder names
 *
 * @param string $month numeric month
 * @return string quarter name (Q + number)
 */
function sent_subfolder_getQuarter($month) {
    switch ($month) {
        case '01':
        case '02':
        case '03':
            $result = '1';
            break;
        case '04':
        case '05':
        case '06':
            $result = '2';
            break;
        case '07':
        case '08':
        case '09':
            $result = '3';
            break;
        case '10':
        case '11':
        case '12':
            $result = '4';
            break;
        default:
            $result = 'ERR';
    }

    /* Return the current quarter. */
    return ('Q' . $result);
}

/**
 * detects if mailbox is part of sent_subfolders
 *
 * @param string $mb imap folder name
 * @return boolean 1 - is part of sent_subfolders, 0 - is not part of sent_subfolders
 */
function sent_subfolders_special_mailbox($mb) {
    global $data_dir, $username, $sent_folder;

    sqgetGlobalVar('delimiter', $delimiter, SQ_SESSION);
/*
    if( $imap_server_type == 'uw' ) {
        $cnd_delimiter = '';
    } else {
        $cnd_delimiter = $delimiter;
    }
*/        
    $cnd_delimiter = $delimiter;

    $use_sent_subfolders = getPref
        ($data_dir, $username, 'use_sent_subfolders', SMPREF_OFF);
    $sent_subfolders_base = getPref($data_dir, $username, 'sent_subfolders_base', $sent_folder);

    if ($use_sent_subfolders == SMPREF_ON && 
    ($mb == $sent_subfolders_base || stristr($mb,$sent_subfolders_base. $cnd_delimiter) ) ) {
	return 1;
    }
    return 0;
}
