<?php

/**
 * options_display.php
 *
 * Displays all optinos about display preferences
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: display.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** Define the group constants for the display options page. */
define('SMOPT_GRP_GENERAL', 0);
define('SMOPT_GRP_MAILBOX', 1);
define('SMOPT_GRP_MESSAGE', 2);

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
function load_optpage_data_display() {
    global $theme, $language, $languages, $js_autodetect_results,
    $compose_new_win, $default_use_mdn, $squirrelmail_language, $allow_thread_sort,
    $optmode;

    /* Build a simple array into which we will build options. */
    $optgrps = array();
    $optvals = array();

    /******************************************************/
    /* LOAD EACH GROUP OF OPTIONS INTO THE OPTIONS ARRAY. */
    /******************************************************/

    /*** Load the General Options into the array ***/
    $optgrps[SMOPT_GRP_GENERAL] = _("General Display Options");
    $optvals[SMOPT_GRP_GENERAL] = array();

    /* Load the theme option. */
    $theme_values = array();
    foreach ($theme as $theme_key => $theme_attributes) {
        $theme_values[$theme_attributes['NAME']] = $theme_attributes['PATH'];
    }
    ksort($theme_values);
    $theme_values = array_flip($theme_values);
    $optvals[SMOPT_GRP_GENERAL][] = array(
        'name'    => 'chosen_theme',
        'caption' => _("Theme"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_ALL,
        'posvals' => $theme_values,
        'save'    => 'save_option_theme'
    );

    $css_values = array( 'none' => _("Default" ) );
    $css_dir = SM_PATH . 'themes/css';
    if (is_readable($css_dir) && is_dir($css_dir)) {
        $handle=opendir($css_dir);
        while ($file = readdir($handle) ) {
            if ( substr( $file, -4 ) == '.css' ) {
                $css_values[$file] = substr( $file, 0, strlen( $file ) - 4 );
            }
        }
        closedir($handle);
    }

    if ( count( $css_values ) > 1 ) {

        $optvals[SMOPT_GRP_GENERAL][] = array(
            'name'    => 'custom_css',
            'caption' => _("Custom Stylesheet"),
            'type'    => SMOPT_TYPE_STRLIST,
            'refresh' => SMOPT_REFRESH_ALL,
            'posvals' => $css_values
        );

    }

    $language_values = array();
    foreach ($languages as $lang_key => $lang_attributes) {
        if (isset($lang_attributes['NAME'])) {
            $language_values[$lang_key] = $lang_attributes['NAME'];
        }
    }

    asort($language_values);
    $language_values =
        array_merge(array('' => _("Default")), $language_values);
    $language = $squirrelmail_language;
    $optvals[SMOPT_GRP_GENERAL][] = array(
        'name'    => 'language',
        'caption' => _("Language"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_ALL,
        'posvals' => $language_values,
        'htmlencoded' => true
    );

    /* Set values for the "use javascript" option. */
    $optvals[SMOPT_GRP_GENERAL][] = array(
        'name'    => 'javascript_setting',
        'caption' => _("Use Javascript"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_ALL,
        'posvals' => array(SMPREF_JS_AUTODETECT => _("Autodetect"),
                           SMPREF_JS_ON         => _("Always"),
                           SMPREF_JS_OFF        => _("Never"))
    );

    if ($optmode != 'submit')
       // FIXME: our form is named "f" - why? let's use something better
       $onLoadScript = 'document.f.new_js_autodetect_results.value = \'' . SMPREF_JS_ON . '\'';
    else
       $onLoadScript = '';

    $optvals[SMOPT_GRP_GENERAL][] = array(
        'name'    => 'js_autodetect_results',
        'caption' => '',
        'type'    => SMOPT_TYPE_HIDDEN,
        'refresh' => SMOPT_REFRESH_NONE,
        //'post_script' => $js_autodetect_script,
        'save'    => 'save_option_javascript_autodetect'
    );

    /*** Load the General Options into the array ***/
    $optgrps[SMOPT_GRP_MAILBOX] = _("Mailbox Display Options");
    $optvals[SMOPT_GRP_MAILBOX] = array();

    $optvals[SMOPT_GRP_MAILBOX][] = array(
        'name'    => 'show_num',
        'caption' => _("Number of Messages per Page"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY
    );

    $optvals[SMOPT_GRP_MAILBOX][] = array(
        'name'    => 'alt_index_colors',
        'caption' => _("Enable Alternating Row Colors"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MAILBOX][] = array(
        'name'    => 'page_selector',
        'caption' => _("Enable Page Selector"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MAILBOX][] = array(
        'name'    => 'page_selector_max',
        'caption' => _("Maximum Number of Pages to Show"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY
    );

    $optvals[SMOPT_GRP_MAILBOX][] = array(
        'name'    => 'show_full_date',
        'caption' => _("Always Show Full Date"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MAILBOX][] = array(
        'name'    => 'truncate_sender',
        'caption' => _("Length of From/To Field (0 for full)"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY,
        'initial_value' => 55
    );

    $optvals[SMOPT_GRP_MAILBOX][] = array(
        'name'    => 'truncate_subject',
        'caption' => _("Length of Subject Field (0 for full)"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY,
        'initial_value' => 55
    );

    /*** Load the General Options into the array ***/
    $optgrps[SMOPT_GRP_MESSAGE] = _("Message Display and Composition");
    $optvals[SMOPT_GRP_MESSAGE] = array();

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'wrap_at',
        'caption' => _("Wrap Incoming Text At"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'editor_size',
        'caption' => _("Width of Editor Window"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'editor_height',
        'caption' => _("Height of Editor Window"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'location_of_buttons',
        'caption' => _("Location of Buttons when Composing"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_NONE,
        'posvals' => array(SMPREF_LOC_TOP     => _("Before headers"),
                           SMPREF_LOC_BETWEEN => _("Between headers and message body"),
                           SMPREF_LOC_BOTTOM  => _("After message body"))
    );


    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'use_javascript_addr_book',
        'caption' => _("Address Book Display Format"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_NONE,
        'posvals' => array('1' => _("Javascript"),
                           '0' => _("HTML"))
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'addrsrch_fullname',
        'caption' => _("Format of Addresses Added From Address Book"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_NONE,
        'posvals' => array('noprefix' => _("No prefix/Address only"),
                           'nickname' => _("Nickname and address"),
                           'fullname' => _("Full name and address"))
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'show_html_default',
        'caption' => _("Show HTML Version by Default"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'enable_forward_as_attachment',
        'caption' => _("Enable Forward as Attachment"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'include_self_reply_all',
        'caption' => _("Include Me in CC when I Reply All"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'show_xmailer_default',
        'caption' => _("Enable Mailer Display"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'attachment_common_show_images',
        'caption' => _("Display Attached Images with Message"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'pf_cleandisplay',
        'caption' => _("Enable Printer Friendly Clean Display"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    if ($default_use_mdn) {
        $optvals[SMOPT_GRP_MESSAGE][] = array(
            'name'    => 'mdn_user_support',
            'caption' => _("Enable Mail Delivery Notification"),
            'type'    => SMOPT_TYPE_BOOLEAN,
            'refresh' => SMOPT_REFRESH_NONE
        );
    }

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'compose_new_win',
        'caption' => _("Compose Messages in New Window"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_ALL
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'compose_width',
        'caption' => _("Width of Compose Window"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_ALL,
        'size'    => SMOPT_SIZE_TINY
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'compose_height',
        'caption' => _("Height of Compose Window"),
        'type'    => SMOPT_TYPE_INTEGER,
        'refresh' => SMOPT_REFRESH_ALL,
        'size'    => SMOPT_SIZE_TINY
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'sig_first',
        'caption' => _("Prepend Signature before Reply/Forward Text"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'strip_sigs',
        'caption' => _("Strip signature when replying"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_NONE
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'body_quote',
        'caption' => _("Prefix for Original Message when Replying"),
        'type'    => SMOPT_TYPE_STRING,
        'refresh' => SMOPT_REFRESH_NONE,
        'size'    => SMOPT_SIZE_TINY,
        'save'    => 'save_option_reply_prefix'
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'    => 'reply_focus',
        'caption' => _("Cursor Position when Replying"),
        'type'    => SMOPT_TYPE_STRLIST,
        'refresh' => SMOPT_REFRESH_NONE,
        'posvals' => array('' => _("To: field"),
                           'focus' => _("Focus in body"),
                           'select' => _("Select body"),
                           'none' => _("No focus"))
    );

    $optvals[SMOPT_GRP_MESSAGE][] = array(
        'name'          => 'internal_date_sort',
        'caption'       => _("Sort by Received Date"),
        'type'          => SMOPT_TYPE_BOOLEAN,
        'refresh'       => SMOPT_REFRESH_ALL,
        'initial_value' => SMPREF_ON
    );
    if ($allow_thread_sort == TRUE) {
        $optvals[SMOPT_GRP_MESSAGE][] = array(
            'name'    => 'sort_by_ref',
            'caption' => _("Enable Thread Sort by References Header"),
            'type'    => SMOPT_TYPE_BOOLEAN,
            'refresh' => SMOPT_REFRESH_ALL
        );
    }
    /* Assemble all this together and return it as our result. */
    $result = array(
        'grps' => $optgrps,
        'vals' => $optvals,
        'xtra' => $onLoadScript
    );
    return ($result);
}

/******************************************************************/
/** Define any specialized save functions for this option page. ***/
/******************************************************************/

/**
 * This function saves a new theme setting.
 * It updates the theme array.
 */
function save_option_theme($option) {
    global $theme;

    /* Do checking to make sure $new_theme is in the array. */
    $theme_in_array = false;
    for ($i = 0; $i < count($theme); ++$i) {
        if ($theme[$i]['PATH'] == $option->new_value) {
            $theme_in_array = true;
            break;
        }
    }

    if (!$theme_in_array) {
        $option->new_value = '';
    }

    /* Save the option like normal. */
    save_option($option);
}

/**
 * This function saves the javascript detection option.
 */
function save_option_javascript_autodetect($option) {
    global $data_dir, $username;

    sqGetGlobalVar('new_javascript_setting', $new_javascript_setting);

    // Set javascript either on or off.
    if ($new_javascript_setting == SMPREF_JS_AUTODETECT) {
        if ($option->new_value == SMPREF_JS_ON) {
            setPref($data_dir, $username, 'javascript_on', SMPREF_JS_ON);
        } else {
            setPref($data_dir, $username, 'javascript_on', SMPREF_JS_OFF);
        }
    } else {
        setPref($data_dir, $username, 'javascript_on', $new_javascript_setting);
    }
}

function save_option_reply_prefix($option) {
    global $data_dir, $username, $new_javascript_setting;

    // save as "NONE" if it was blanked out
    //
    if (empty($option->new_value)) $option->new_value = 'NONE';


    // Save the option like normal.
    //
    save_option($option);

}

