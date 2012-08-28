<?php

/**
 * options.php
 *
 * Displays the options page. Pulls from proper user preference files
 * and config.php. Displays preferences as selected and other options.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: options.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage prefs
 */

/** This is the options page */
define('PAGE_NAME', 'options');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/options.php');
require_once(SM_PATH . 'functions/strings.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'functions/forms.php');

/*********************************/
/*** Build the resultant page. ***/
/*********************************/

define('SMOPT_MODE_DISPLAY', 'display');
define('SMOPT_MODE_SUBMIT', 'submit');
define('SMOPT_MODE_LINK', 'link');

define('SMOPT_PAGE_MAIN', 'main');
define('SMOPT_PAGE_PERSONAL', 'personal');
define('SMOPT_PAGE_DISPLAY', 'display');
define('SMOPT_PAGE_HIGHLIGHT', 'highlight');
define('SMOPT_PAGE_FOLDER', 'folder');
define('SMOPT_PAGE_ORDER', 'order');

/**
  * Save submitted options and calculate the most 
  * we need to refresh the page
  *
  * @param string $optpage      The name of the page being submitted
  * @param array  $optpage_data An array of all the submitted options
  *
  * @return int The highest level of screen refresh needed per
  *             the options that were changed.  This value will
  *             correspond to the SMOPT_REFRESH_* constants found
  *             in functions/options.php.
  *
  */
function process_optionmode_submit($optpage, $optpage_data) {
    /* Initialize the maximum option refresh level. */
    $max_refresh = SMOPT_REFRESH_NONE;

    /* Save each option in each option group. */
    foreach ($optpage_data['options'] as $option_grp) {
        foreach ($option_grp['options'] as $option) {
            /* Remove Debug Mode Until Needed
            echo "name = '$option->name', "
               . "value = '$option->value', "
               . "new_value = '$option->new_value'\n";
            echo "<br />";
            */
            if ($option->changed()) {
                $option->save();
                $max_refresh = max($max_refresh, $option->refresh_level);
            }
        }
    }

    /* Return the max refresh level. */
    return ($max_refresh);
}

function process_optionmode_link($optpage) {
   /* There will be something here, later. */
}


/**
 * This function prints out an option page row.
 */
function print_optionpages_row($leftopt, $rightopt = false) {
    global $color;

    if ($rightopt) {
        $rightopt_name = html_tag( 'td', '<a href="' . $rightopt['url'] . '">' . $rightopt['name'] . '</a>', 'left', $color[9], 'valign="top" width="49%"' );
        $rightopt_desc = html_tag( 'td', $rightopt['desc'], 'left', $color[0], 'valign="top" width="49%"' );
    } else {
        $rightopt_name = html_tag( 'td', '&nbsp;', 'left', $color[4], 'valign="top" width="49%"' );
        $rightopt_desc = html_tag( 'td', '&nbsp;', 'left', $color[4], 'valign="top" width="49%"' );
    }

    echo
    html_tag( 'table', "\n" .
        html_tag( 'tr', "\n" .
            html_tag( 'td', "\n" .
                html_tag( 'table', "\n" .
                    html_tag( 'tr', "\n" .
                        html_tag( 'td',
                            '<a href="' . $leftopt['url'] . '">' . $leftopt['name'] . '</a>' ,
                        'left', $color[9], 'valign="top" width="49%"' ) .
                        html_tag( 'td',
                            '&nbsp;' ,
                        'left', $color[4], 'valign="top" width="2%"' ) . "\n" .
                        $rightopt_name
                    ) . "\n" .
                    html_tag( 'tr', "\n" .
                        html_tag( 'td',
                            $leftopt['desc'] ,
                        'left', $color[0], 'valign="top" width="49%"' ) .
                        html_tag( 'td',
                            '&nbsp;' ,
                        'left', $color[4], 'valign="top" width="2%"' ) . "\n" .
                        $rightopt_desc
                    ) ,
                '', '', 'width="100%" cellpadding="2" cellspacing="0" border="0"' ) ,
            'left', '', 'valign="top"' )
        ) ,
    '', $color[4], 'width="100%" cellpadding="0" cellspacing="5" border="0"' );
}

/* ---------------------------- main ---------------------------- */

/* get the globals that we may need */
sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);

sqgetGlobalVar('optpage',     $optpage);
sqgetGlobalVar('optmode',     $optmode,      SQ_FORM);
sqgetGlobalVar('optpage_data',$optpage_data, SQ_POST);
if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_FORM)) {
    $submitted_token = '';
}
/* end of getting globals */

/* Make sure we have an Option Page set. Default to main. */
if ( !isset($optpage) || $optpage == '' ) {
    $optpage = SMOPT_PAGE_MAIN;
} else {
    $optpage = strip_tags( $optpage );
}

/* Make sure we have an Option Mode set. Default to display. */
if (!isset($optmode)) {
    $optmode = SMOPT_MODE_DISPLAY;
}

/*
 * First, set the load information for each option page.   
 */

/* Initialize load information variables. */
$optpage_name = '';
$optpage_file = '';
$optpage_loader = '';

/* Set the load information for each page. */
switch ($optpage) {
    case SMOPT_PAGE_MAIN: 
        break;
    case SMOPT_PAGE_PERSONAL:
        $optpage_name     = _("Personal Information");
        $optpage_file     = SM_PATH . 'include/options/personal.php';
        $optpage_loader   = 'load_optpage_data_personal';
        $optpage_loadhook = 'optpage_loadhook_personal';
        break;
    case SMOPT_PAGE_DISPLAY:
        $optpage_name   = _("Display Preferences");
        $optpage_file   = SM_PATH . 'include/options/display.php';
        $optpage_loader = 'load_optpage_data_display';
        $optpage_loadhook = 'optpage_loadhook_display';
        break;
    case SMOPT_PAGE_HIGHLIGHT:
        $optpage_name   = _("Message Highlighting");
        $optpage_file   = SM_PATH . 'include/options/highlight.php';
        $optpage_loader = 'load_optpage_data_highlight';
        $optpage_loadhook = 'optpage_loadhook_highlight';
        break;
    case SMOPT_PAGE_FOLDER:
        $optpage_name   = _("Folder Preferences");
        $optpage_file   = SM_PATH . 'include/options/folder.php';
        $optpage_loader = 'load_optpage_data_folder';
        $optpage_loadhook = 'optpage_loadhook_folder';
        break;
    case SMOPT_PAGE_ORDER:
        $optpage_name = _("Index Order");
        $optpage_file = SM_PATH . 'include/options/order.php';
        $optpage_loader = 'load_optpage_data_order';
        $optpage_loadhook = 'optpage_loadhook_order';
        break;
    default: do_hook('optpage_set_loadinfo');
}

/**********************************************************/
/*** Second, load the option information for this page. ***/
/**********************************************************/

if ( !@is_file( $optpage_file ) ) {
    $optpage = SMOPT_PAGE_MAIN;
} else if ($optpage != SMOPT_PAGE_MAIN ) {
    /* Include the file for this optionpage. */
    
    require_once($optpage_file);

    /* Assemble the data for this option page. */
    $optpage_data = array();
    $optpage_data = $optpage_loader();
    do_hook($optpage_loadhook);
    $optpage_data['options'] =
        create_option_groups($optpage_data['grps'], $optpage_data['vals']);
}

/***********************************************************/
/*** Next, process anything that needs to be processed. ***/
/***********************************************************/

// security check before saving anything...
//FIXME: what about SMOPT_MODE_LINK??
if ($optmode == SMOPT_MODE_SUBMIT) {
   sm_validate_security_token($submitted_token, 3600, TRUE);
}

// set empty error message
$optpage_save_error=array();

if ( isset( $optpage_data ) ) {
    switch ($optmode) {
        case SMOPT_MODE_SUBMIT:
            $max_refresh = process_optionmode_submit($optpage, $optpage_data);
            break;
        case SMOPT_MODE_LINK:
            $max_refresh = process_optionmode_link($optpage, $optpage_data);
            break;
    }
}

$optpage_title = _("Options");
if (isset($optpage_name) && ($optpage_name != '')) {
    $optpage_title .= " - $optpage_name";
}

/*******************************************************************/
/* DO OLD SAVING OF SUBMITTED OPTIONS. THIS WILL BE REMOVED LATER. */
/*******************************************************************/

/* If in submit mode, select a save hook name and run it. */
if ($optmode == SMOPT_MODE_SUBMIT) {
    /* Select a save hook name. */
    switch ($optpage) {
        case SMOPT_PAGE_PERSONAL:
            $save_hook_name = 'options_personal_save';
            break;
        case SMOPT_PAGE_DISPLAY:
            $save_hook_name = 'options_display_save';
            break;
        case SMOPT_PAGE_FOLDER:
            $save_hook_name = 'options_folder_save';
            break;
        default: 
            $save_hook_name = 'options_save';
            break;
    }

    /* Run the options save hook. */
    do_hook($save_hook_name);
}

/***************************************************************/
/* Apply logic to decide what optpage we want to display next. */
/***************************************************************/

/* If this is the result of an option page being submitted, then */
/* show the main page. Otherwise, show whatever page was called. */

if ($optmode == SMOPT_MODE_SUBMIT) {
    $optpage = SMOPT_PAGE_MAIN;
    $optpage_title = _("Options");
}

/***************************************************************/
/* Finally, display whatever page we are supposed to show now. */
/***************************************************************/

displayPageHeader($color, 'None', (isset($optpage_data['xtra']) ? $optpage_data['xtra'] : ''));

echo html_tag( 'table', '', 'center', $color[0], 'width="95%" cellpadding="1" cellspacing="0" border="0"' ) . "\n" .
        html_tag( 'tr' ) . "\n" .
            html_tag( 'td', '', 'center' ) .
                "<b>$optpage_title</b><br />\n".
                html_tag( 'table', '', '', '', 'width="100%" cellpadding="5" cellspacing="0" border="0"' ) . "\n" .
                    html_tag( 'tr' ) . "\n" .
                        html_tag( 'td', '', 'center', $color[4] ) . "\n";

/*
 * The main option page has a different layout then the rest of the option
 * pages. Therefore, we create it here first, then the others below.
 */
if ($optpage == SMOPT_PAGE_MAIN) {
    /**********************************************************/
    /* First, display the results of a submission, if needed. */
    /**********************************************************/
    if ($optmode == SMOPT_MODE_SUBMIT) {
        if (!isset($frame_top)) {
            $frame_top = '_top';
        }

        if (isset($optpage_save_error) && $optpage_save_error!=array()) {
            echo "<font color=\"$color[2]\"><b>" . _("Error(s) occurred while saving your options") . "</b></font><br />\n";
            echo "<ul>\n";
            foreach ($optpage_save_error as $error_message) {
                echo '<li><small>' . $error_message . "</small></li>\n";
            }
            echo "</ul>\n";
            echo '<b>' . _("Some of your preference changes were not applied.") . "</b><br />\n";
        } else {
            /* Display a message indicating a successful save. */
            // i18n: The %s represents the name of the option page saving the options
            echo '<b>' . sprintf(_("Successfully Saved Options: %s"), $optpage_name) . "</b><br />\n";
        }

        /* If $max_refresh != SMOPT_REFRESH_NONE, provide a refresh link. */
        if ( !isset( $max_refresh ) ) {
        } else if ($max_refresh == SMOPT_REFRESH_FOLDERLIST) {
            echo '<a href="../src/left_main.php" target="left">' . _("Refresh Folder List") . '</a><br />';
        } else if ($max_refresh) {
            echo '<a href="../src/webmail.php?right_frame=options.php" target="' . $frame_top . '">' . _("Refresh Page") . '</a><br />';
        }
    }
    /******************************************/
    /* Build our array of Option Page Blocks. */
    /******************************************/
    $optpage_blocks = array();

    /* Build a section for Personal Options. */
    $optpage_blocks[] = array(
        'name' => _("Personal Information"),
        'url'  => 'options.php?optpage=' . SMOPT_PAGE_PERSONAL,
        'desc' => _("This contains personal information about yourself such as your name, your email address, etc."),
        'js'   => false
    );

    /* Build a section for Display Options. */
    $optpage_blocks[] = array(
        'name' => _("Display Preferences"),
        'url'  => 'options.php?optpage=' . SMOPT_PAGE_DISPLAY,
        'desc' => _("You can change the way that SquirrelMail looks and displays information to you, such as the colors, the language, and other settings."),
        'js'   => false
    );

    /* Build a section for Message Highlighting Options. */
    $optpage_blocks[] = array(
        'name' =>_("Message Highlighting"),
        'url'  => 'options_highlight.php',
        'desc' =>_("Based upon given criteria, incoming messages can have different background colors in the message list. This helps to easily distinguish who the messages are from, especially for mailing lists."),
        'js'   => false
    );

    /* Build a section for Folder Options. */
    $optpage_blocks[] = array(
        'name' => _("Folder Preferences"),
        'url'  => 'options.php?optpage=' . SMOPT_PAGE_FOLDER,
        'desc' => _("These settings change the way your folders are displayed and manipulated."),
        'js'   => false
    );

    /* Build a section for Index Order Options. */
    $optpage_blocks[] = array(
        'name' => _("Index Order"),
        'url'  => 'options_order.php',
        'desc' => _("The order of the message index can be rearranged and changed to contain the headers in any order you want."),
        'js'   => false
    );

    /* Build a section for plugins wanting to register an optionpage. */
    do_hook('optpage_register_block');

    /*****************************************************/
    /* Let's sort Javascript Option Pages to the bottom. */
    /*****************************************************/
    $js_optpage_blocks = array();
    $reg_optpage_blocks = array();
    foreach ($optpage_blocks as $cur_optpage) {
        if (!isset($cur_optpage['js']) || !$cur_optpage['js']) {
            $reg_optpage_blocks[] = $cur_optpage;
        } else if ($javascript_on == SMPREF_JS_ON) {
            $js_optpage_blocks[] = $cur_optpage;
        }
    }
    $optpage_blocks = array_merge($reg_optpage_blocks, $js_optpage_blocks);

    /********************************************/
    /* Now, print out each option page section. */
    /********************************************/
    $first_optpage = false;
    echo html_tag( 'table', '', '', $color[4], 'width="100%" cellpadding="0" cellspacing="5" border="0"' ) . "\n" .
                html_tag( 'tr' ) . "\n" .
                    html_tag( 'td', '', 'left', '', 'valign="top"' ) .
                        html_tag( 'table', '', '', $color[4], 'width="100%" cellpadding="3" cellspacing="0" border="0"' ) . "\n" .
                            html_tag( 'tr' ) . "\n" .
                                html_tag( 'td', '', 'left' );
    foreach ($optpage_blocks as $next_optpage) {
        if ($first_optpage == false) {
            $first_optpage = $next_optpage;
        } else {
            print_optionpages_row($first_optpage, $next_optpage);
            $first_optpage = false;
        }
    }

    if ($first_optpage != false) {
        print_optionpages_row($first_optpage);
    }

    echo "</td></tr></table></td></tr></table>\n";

    do_hook('options_link_and_description');


/*************************************************************************/
/* If we are not looking at the main option page, display the page here. */
/*************************************************************************/
} else {
    echo addForm('options.php', 'POST', 'f', '', '', '', TRUE)
       . create_optpage_element($optpage)
       . create_optmode_element(SMOPT_MODE_SUBMIT)
       . html_tag( 'table', '', '', '', 'width="100%" cellpadding="2" cellspacing="0" border="0"' ) . "\n"
       . html_tag( 'tr' ) . "\n"
       . html_tag( 'td', '', 'left' ) . "\n";

    /* Output the option groups for this page. */
    print_option_groups($optpage_data['options']);

    /* Set the inside_hook_name and submit_name. */
    switch ($optpage) {
        case SMOPT_PAGE_PERSONAL:
            $inside_hook_name = 'options_personal_inside';
            $bottom_hook_name = 'options_personal_bottom';
            $submit_name = 'submit_personal';
            break;
        case SMOPT_PAGE_DISPLAY:
            $inside_hook_name = 'options_display_inside';
            $bottom_hook_name = 'options_display_bottom';
            $submit_name = 'submit_display';
            break;
        case SMOPT_PAGE_HIGHLIGHT:
            $inside_hook_name = 'options_highlight_inside';
            $bottom_hook_name = 'options_highlight_bottom';
            $submit_name = 'submit_highlight';
            break;
        case SMOPT_PAGE_FOLDER:
            $inside_hook_name = 'options_folder_inside';
            $bottom_hook_name = 'options_folder_bottom';
            $submit_name = 'submit_folder';
            break;
        case SMOPT_PAGE_ORDER:
            $inside_hook_name = 'options_order_inside';
            $bottom_hook_name = 'options_order_bottom';
            $submit_name = 'submit_order';
            break;
        default:
            $inside_hook_name = '';
            $bottom_hook_name = '';
            $submit_name = 'submit';
    }

    /* If it is not empty, trigger the inside hook. */
    if ($inside_hook_name != '') {
        do_hook($inside_hook_name);    
    }

    /* Spit out a submit button. */
    OptionSubmit($submit_name);
    echo '</td></tr></table></form>';

    /* If it is not empty, trigger the bottom hook. */
    if ($bottom_hook_name != '') {
        do_hook($bottom_hook_name);    
    }
}
?>
</td></tr>
</table>
</td></tr>
</table>
</body></html>
