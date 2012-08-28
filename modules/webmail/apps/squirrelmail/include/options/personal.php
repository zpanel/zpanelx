<?php

/**
 * options_personal.php
 *
 * Displays all options relating to personal information
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: personal.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** SquirrelMail required files. */
require_once(SM_PATH . 'functions/imap.php');

/* Define the group constants for the personal options page. */
define('SMOPT_GRP_CONTACT', 0);
define('SMOPT_GRP_REPLY', 1);
define('SMOPT_GRP_SIG', 2);
define('SMOPT_GRP_TZ', 3);

/**
 * This function builds an array with all the information about
 * the options available to the user, and returns it. The options
 * are grouped by the groups in which they are displayed.
 * For each option, the following information is stored:
 * - name: the internal (variable) name
 * - caption: the description of the option in the UI
 * - type: one of SMOPT_TYPE_*
 * - refresh: one of SMOPT_REFRESH_*
 * - size: one of SMOPT_SIZE_*
 * - save: the name of a function to call when saving this option
 * @return array all option information
 */
function load_optpage_data_personal() {
    global $data_dir, $username, $edit_identity, $edit_name,
           $full_name, $reply_to, $email_address, $signature, $tzChangeAllowed,
           $color;

    /* Set the values of some global variables. */
    $full_name = getPref($data_dir, $username, 'full_name');
    $reply_to = getPref($data_dir, $username, 'reply_to');
    $email_address  = getPref($data_dir, $username, 'email_address');
    $signature  = getSig($data_dir, $username, 'g');

    /* Build a simple array into which we will build options. */
    $optgrps = array();
    $optvals = array();

    /******************************************************/
    /* LOAD EACH GROUP OF OPTIONS INTO THE OPTIONS ARRAY. */
    /******************************************************/

    /*** Load the Contact Information Options into the array ***/
    $optgrps[SMOPT_GRP_CONTACT] = _("Name and Address Options");
    $optvals[SMOPT_GRP_CONTACT] = array();

    /* Build a simple array into which we will build options. */
    $optvals = array();

    if (!isset($edit_identity)) {
        $edit_identity = TRUE;
    }

    if ($edit_identity || $edit_name) {
        $optvals[SMOPT_GRP_CONTACT][] = array(
            'name'    => 'full_name',
            'caption' => _("Full Name"),
            'type'    => SMOPT_TYPE_STRING,
            'refresh' => SMOPT_REFRESH_NONE,
            'size'    => SMOPT_SIZE_HUGE
        );
    } else {
        $optvals[SMOPT_GRP_CONTACT][] = array(
            'name'    => 'full_name',
            'caption' => _("Full Name"),
            'type'    => SMOPT_TYPE_COMMENT,
            'refresh' => SMOPT_REFRESH_NONE,
            'comment' => $full_name
        );
    }

    if ($edit_identity) {
        $optvals[SMOPT_GRP_CONTACT][] = array(
            'name'    => 'email_address',
            'caption' => _("E-mail Address"),
            'type'    => SMOPT_TYPE_STRING,
            'refresh' => SMOPT_REFRESH_NONE,
            'size'    => SMOPT_SIZE_HUGE
        );
    } else {
        $optvals[SMOPT_GRP_CONTACT][] = array(
            'name'    => 'email_address',
            'caption' => _("E-mail Address"),
            'type'    => SMOPT_TYPE_COMMENT,
            'refresh' => SMOPT_REFRESH_NONE,
            'comment' => $email_address
        );
    }

    $optvals[SMOPT_GRP_CONTACT][] = array(
        'name'    => 'reply_to',
        'caption' => _("Reply To"),
        'type'    => SMOPT_TYPE_STRING,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_HUGE
    );

    $optvals[SMOPT_GRP_CONTACT][] = array(
        'name'    => 'signature',
        'caption' => _("Signature"),
        'type'    => SMOPT_TYPE_TEXTAREA,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_MEDIUM,
        'save'    => 'save_option_signature'
    );

    if ($edit_identity) {
        $identities_link_value = '<a href="options_identities.php">'
                               . _("Edit Advanced Identities")
                               . '</a> '
                               . _("(discards changes made on this form so far)");
        $optvals[SMOPT_GRP_CONTACT][] = array(
            'name'    => 'identities_link',
            'caption' => _("Multiple Identities"),
            'type'    => SMOPT_TYPE_COMMENT,
            'refresh' => SMOPT_REFRESH_NONE,
            'comment' =>  $identities_link_value
        );
    }

    if ( $tzChangeAllowed || function_exists('date_default_timezone_set')) {
        $TZ_ARRAY[SMPREF_NONE] = _("Same as server");
        $tzfile = SM_PATH . 'locale/timezones.cfg';
        if ((!is_readable($tzfile)) or (!$fd = fopen($tzfile,'r'))) {
            $message = _("Error opening timezone config, contact administrator.");
        }

        // TODO: make error user friendly
        if (isset($message)) {
            plain_error_message($message, $color);
            exit;
        }
        while (!feof ($fd)) {
            $zone = fgets($fd, 1024);
            if( $zone ) {
                $zone = trim($zone);
                $TZ_ARRAY[$zone] = $zone;
            }
        }
        fclose ($fd);

        $optgrps[SMOPT_GRP_TZ] = _("Timezone Options");
        $optvals[SMOPT_GRP_TZ] = array();

        $optvals[SMOPT_GRP_TZ][] = array(
            'name'    => 'timezone',
            'caption' => _("Your current timezone"),
            'type'    => SMOPT_TYPE_STRLIST,
            'refresh' => SMOPT_REFRESH_NONE,
            'posvals' => $TZ_ARRAY
        );
    }

    /*** Load the Reply Citation Options into the array ***/
    $optgrps[SMOPT_GRP_REPLY] = _("Reply Citation Options");
    $optvals[SMOPT_GRP_REPLY] = array();

    $optvals[SMOPT_GRP_REPLY][] = array(
        'name'    => 'reply_citation_style',
        'caption' => _("Reply Citation Style"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_NONE,
        'posvals' => array(SMPREF_NONE    => _("No Citation"),
                           'author_said'  => _("AUTHOR Wrote"),
                           'date_time_author' => _("On DATE, AUTHOR Wrote"),
                           'quote_who'    => _("Quote Who XML"),
                           'user-defined' => _("User-Defined"))
    );

    $optvals[SMOPT_GRP_REPLY][] = array(
        'name'    => 'reply_citation_start',
        'caption' => _("User-Defined Citation Start"),
        'type'    => SMOPT_TYPE_STRING,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_MEDIUM
    );

    $optvals[SMOPT_GRP_REPLY][] = array(
        'name'    => 'reply_citation_end',
        'caption' => _("User-Defined Citation End"),
        'type'    => SMOPT_TYPE_STRING,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_MEDIUM
    );

    /*** Load the Signature Options into the array ***/
    $optgrps[SMOPT_GRP_SIG] = _("Signature Options");
    $optvals[SMOPT_GRP_SIG] = array();

    $optvals[SMOPT_GRP_SIG][] = array(
        'name'    => 'use_signature',
        'caption' => _("Use Signature"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_SIG][] = array(
        'name'    => 'prefix_sig',
        'caption' => _("Prefix Signature with '-- ' Line"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    /* Assemble all this together and return it as our result. */
    $result = array(
        'grps' => $optgrps,
        'vals' => $optvals
    );
    return ($result);
}

/******************************************************************/
/** Define any specialized save functions for this option page. ***/
/******************************************************************/

/**
 * Saves the signature option.
 */
function save_option_signature($option) {
    global $data_dir, $username;
    setSig($data_dir, $username, 'g', $option->new_value);
}

