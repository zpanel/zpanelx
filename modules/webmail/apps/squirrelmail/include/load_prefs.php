<?php

/**
 * load_prefs.php
 *
 * Loads preferences from the $username.pref file used by almost
 * every other script in the source directory and alswhere.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: load_prefs.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/plugin.php');
require_once(SM_PATH . 'functions/constants.php');
require_once(SM_PATH . 'functions/prefs.php');

if( ! sqgetGlobalVar('username', $username, SQ_SESSION) ) {
    $username = '';
}

$custom_css = getPref($data_dir, $username, 'custom_css', 'none' );

$theme = ( !isset($theme) ? array() : $theme );
$color = ( !isset($color) ? array() : $color );

$chosen_theme = getPref($data_dir, $username, 'chosen_theme');
$found_theme = false;

/* need to adjust $chosen_theme path with SM_PATH */
$chosen_theme = preg_replace("/(\.\.\/){1,}/", SM_PATH, $chosen_theme);

for ($i = 0; $i < count($theme); ++$i){
    if ($theme[$i]['PATH'] == $chosen_theme) {
        $found_theme = true;
        break;
    }
}
$chosen_theme = (!$found_theme ? '' : $chosen_theme);

/**
* This theme as a failsafe if no themes were found. It makes
* no sense to cause the whole thing to exit just because themes
* were not found. This is the absolute last resort.
* Moved here to provide 'sane' defaults for incomplete themes.
*/
$color[0]  = '#DCDCDC';  /* light gray    TitleBar                */
$color[1]  = '#800000';  /* red                                   */
$color[2]  = '#CC0000';  /* light red     Warning/Error Messages  */
$color[3]  = '#A0B8C8';  /* green-blue    Left Bar Background     */
$color[4]  = '#FFFFFF';  /* white         Normal Background       */
$color[5]  = '#FFFFCC';  /* light yellow  Table Headers           */
$color[6]  = '#000000';  /* black         Text on left bar        */
$color[7]  = '#0000CC';  /* blue          Links                   */
$color[8]  = '#000000';  /* black         Normal text             */
$color[9]  = '#ABABAB';  /* mid-gray      Darker version of #0    */
$color[10] = '#666666';  /* dark gray     Darker version of #9    */
$color[11] = '#770000';  /* dark red      Special Folders color   */
$color[12] = '#EDEDED';
$color[15] = '#002266';  /* (dark blue)      Unselectable folders */
$color[16] = '#ff9933';  /* (orange)         Highlight color      */

if (isset($chosen_theme) && $found_theme && (file_exists($chosen_theme))) {
    @include_once($chosen_theme);
} else {
    if (isset($theme) && isset($theme[$theme_default]) && file_exists($theme[$theme_default]['PATH'])) {
        @include_once($theme[$theme_default]['PATH']);
        $chosen_theme = $theme[$theme_default]['PATH'];
    }
}


if (!defined('download_php')) {
    sqsession_register($theme_css, 'theme_css');
}


/* Load the user's special folder preferences */
$move_to_sent =
    getPref($data_dir, $username, 'move_to_sent', $default_move_to_sent);
$move_to_trash =
    getPref($data_dir, $username, 'move_to_trash', $default_move_to_trash);
$save_as_draft =
    getPref($data_dir, $username, 'save_as_draft', $default_save_as_draft);

if ($default_unseen_type == '') {
    $default_unseen_type = 1;
}
if ($default_unseen_notify == '') {
    $default_unseen_notify = 2;
}
$unseen_type =
    getPref($data_dir, $username, 'unseen_type', $default_unseen_type);
$unseen_notify =
    getPref($data_dir, $username, 'unseen_notify', $default_unseen_notify);

$unseen_cum =
    getPref($data_dir, $username, 'unseen_cum', false);

$folder_prefix =
    getPref($data_dir, $username, 'folder_prefix', $default_folder_prefix);

/* Load special folder - trash */
$load_trash_folder = getPref($data_dir, $username, 'trash_folder');
if (($load_trash_folder == '') && ($move_to_trash)) {
    $trash_folder = $folder_prefix . $trash_folder;
} else {
    $trash_folder = $load_trash_folder;
}

/* Load special folder - sent */
$load_sent_folder = getPref($data_dir, $username, 'sent_folder');
if (($load_sent_folder == '') && ($move_to_sent)) {
    $sent_folder = $folder_prefix . $sent_folder;
} else {
    $sent_folder = $load_sent_folder;
}

/* Load special folder - draft */
$load_draft_folder = getPref($data_dir, $username, 'draft_folder');
if (($load_draft_folder == '') && ($save_as_draft)) {
    $draft_folder = $folder_prefix . $draft_folder;
} else {
    $draft_folder = $load_draft_folder;
}

$show_num = getPref($data_dir, $username, 'show_num', 15 );

$wrap_at = getPref( $data_dir, $username, 'wrap_at', 86 );
if ($wrap_at < 15) { $wrap_at = 15; }

$left_size = getPref($data_dir, $username, 'left_size');
if ($left_size == '') {
    if (isset($default_left_size)) {
        $left_size = $default_left_size;
    } else {
        $left_size = 200;
    }
}

$editor_size = getPref($data_dir, $username, 'editor_size', 76 );
$editor_height = getPref($data_dir, $username, 'editor_height', 20 );
$use_signature = getPref($data_dir, $username, 'use_signature', SMPREF_OFF );
$prefix_sig = getPref($data_dir, $username, 'prefix_sig');

/* Load timezone preferences */
$timezone = getPref($data_dir, $username, 'timezone', SMPREF_NONE );

/* Load preferences for reply citation style. */

$reply_citation_style =
    getPref($data_dir, $username, 'reply_citation_style', SMPREF_NONE );
$reply_citation_start = getPref($data_dir, $username, 'reply_citation_start');
$reply_citation_end = getPref($data_dir, $username, 'reply_citation_end');

$body_quote = getPref($data_dir, $username, 'body_quote', '>');
if ($body_quote == 'NONE') $body_quote = '';

// Load preference for cursor behavior for replies
//
$reply_focus = getPref($data_dir, $username, 'reply_focus', '');

/* left refresh rate, strtolower makes 1.0.6 prefs compatible */
$left_refresh = getPref($data_dir, $username, 'left_refresh', 600 );
$left_refresh = strtolower($left_refresh);

$sort = getPref($data_dir, $username, 'sort', 6 );

/* Load up the Signature file */
$signature_abs = $signature = getSig($data_dir, $username, 'g');

/* Message Highlighting Rules */
$message_highlight_list = array();

/* use new way of storing highlighting rules */
if( $ser = getPref($data_dir, $username, 'hililist') ) {
    $message_highlight_list = unserialize($ser);
} else {
    /* use old way */
    for ($i = 0; $hlt = getPref($data_dir, $username, "highlight$i"); ++$i) {
        $highlight_array = explode(',', $hlt);
        $message_highlight_list[$i]['name'] = $highlight_array[0];
        $message_highlight_list[$i]['color'] = $highlight_array[1];
        $message_highlight_list[$i]['value'] = $highlight_array[2];
        $message_highlight_list[$i]['match_type'] = $highlight_array[3];
        removePref($data_dir, $username, "highlight$i");
    }
// NB: The fact that this preference is always set here means that some plugins rely on testing it to know if a user has logged in before - the "old way" above is probably long since obsolete and unneeded, but the setPref() below should not be removed
    /* store in new format for the next time */
    setPref($data_dir, $username, 'hililist', serialize($message_highlight_list));
}

/* Index order lets you change the order of the message index */
$order = getPref($data_dir, $username, 'order1');
for ($i = 1; $order; ++$i) {
    $index_order[$i] = $order;
    $order = getPref($data_dir, $username, 'order'.($i+1));
}
if (!isset($index_order)) {
    $index_order[1] = 1;
    $index_order[2] = 2;
    $index_order[3] = 3;
    $index_order[4] = 5;
    $index_order[5] = 4;
}

$alt_index_colors =
    getPref($data_dir, $username, 'alt_index_colors', SMPREF_ON );

// Folder List Display Format 
$location_of_bar =
    getPref($data_dir, $username, 'location_of_bar', SMPREF_LOC_LEFT);
$location_of_buttons =
    getPref($data_dir, $username, 'location_of_buttons', SMPREF_LOC_BETWEEN);

$collapse_folders =
    getPref($data_dir, $username, 'collapse_folders', SMPREF_ON);

$show_html_default =
   getPref($data_dir, $username, 'show_html_default', SMPREF_OFF);

$addrsrch_fullname =
   getPref($data_dir, $username, 'addrsrch_fullname', 'fullname');

$enable_forward_as_attachment =
   getPref($data_dir, $username, 'enable_forward_as_attachment', SMPREF_ON);

$show_xmailer_default =
    getPref($data_dir, $username, 'show_xmailer_default', SMPREF_OFF );
$attachment_common_show_images = getPref($data_dir, $username, 'attachment_common_show_images', SMPREF_OFF );
$pf_cleandisplay = getPref($data_dir, $username, 'pf_cleandisplay', SMPREF_OFF);

/* message disposition notification support setting */
$mdn_user_support = getPref($data_dir, $username, 'mdn_user_support', SMPREF_ON);

$include_self_reply_all =
    getPref($data_dir, $username, 'include_self_reply_all', SMPREF_ON);

/* Page selector options */
$page_selector = getPref($data_dir, $username, 'page_selector', SMPREF_ON);
$page_selector_max = getPref($data_dir, $username, 'page_selector_max', 10);

/* SqClock now in the core */
$date_format = getPref($data_dir, $username, 'date_format', 3);
$hour_format = getPref($data_dir, $username, 'hour_format', SMPREF_TIME_12HR);

/*  compose in new window setting */
$compose_new_win = getPref($data_dir, $username, 'compose_new_win', 0);
$compose_height = getPref($data_dir, $username, 'compose_height', 550);
$compose_width = getPref($data_dir, $username, 'compose_width', 640);


/* signature placement settings */
$sig_first = getPref($data_dir, $username, 'sig_first', 0);

/* strip signature from replies setting */
$strip_sigs = getPref($data_dir, $username, 'strip_sigs', 0);

/* use the internal date of the message for sorting instead of the supplied header date */
$internal_date_sort = getPref($data_dir, $username, 'internal_date_sort', SMPREF_ON);

/* if server sorting is enabled/disabled */
$sort_by_ref = getPref($data_dir, $username, 'sort_by_ref', 1);

/* Load the javascript settings. */
$javascript_setting = getPref($data_dir, $username, 'javascript_setting', SMPREF_JS_AUTODETECT);
$javascript_on = getPref($data_dir, $username, 'javascript_on', SMPREF_ON);
$use_javascript_addr_book = getPref($data_dir, $username, 'use_javascript_addr_book', $default_use_javascript_addr_book);

$search_memory = getPref($data_dir, $username, 'search_memory', 0);

$mailbox_select_style = getPref($data_dir, $username, 'mailbox_select_style', 1);

/* Allow user to customize, and display the full date, instead of day, or time based
   on time distance from date of message */
$show_full_date = getPref($data_dir, $username, 'show_full_date', 0);

/* Allow user to customize length of from field */
$truncate_sender = getPref($data_dir, $username, 'truncate_sender', 50);
/* Allow user to customize length of subject field */
$truncate_subject = getPref($data_dir, $username, 'truncate_subject', 50);

do_hook('loading_prefs');

