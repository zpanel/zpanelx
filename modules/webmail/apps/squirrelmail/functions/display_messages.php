<?php

/**
 * display_messages.php
 *
 * This contains all messages, including information, error, and just
 * about any other message you can think of.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: display_messages.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/**
 * including plugin functions
 */
require_once(SM_PATH . 'functions/plugin.php');

function error_message($message, $mailbox, $sort, $startMessage, $color) {

    global $default_folder_prefix;

    $urlMailbox = urlencode($mailbox);
    $string = '<tr><td align="center">' . $message . '</td></tr>'.
              '<tr><td align="center">'.
              '<a href="'.sqm_baseuri()."src/right_main.php?sort=$sort&amp;startMessage=$startMessage&amp;mailbox=$urlMailbox\">";

    if (!empty($default_folder_prefix)) {
        if (strpos($mailbox, $default_folder_prefix) === 0)
            $mailbox = substr($mailbox, strlen($default_folder_prefix));
    }
    
    $string .= sprintf (_("Click here to return to %s"),
                  htmlspecialchars(imap_utf7_decode_local($mailbox))).
              '</a></td></tr>';
    error_box($string, $color);
}

function plain_error_message($message, $color) {
    error_box($message, $color);
}

function logout_error( $errString, $errTitle = '' ) {
    global $frame_top, $org_logo, $org_name, $org_logo_width, $org_logo_height,
           $hide_sm_attributions, $version, $squirrelmail_language, 
           $color, $theme, $theme_default;

    include_once( SM_PATH . 'functions/page_header.php' );

    $base_uri = sqm_baseuri();

    $logout_link = $base_uri . 'src/login.php';

    list($junk, $errString, $errTitle, $logout_link) 
        = do_hook('logout_error', $errString, $errTitle, $logout_link);

    /* Display width and height like good little people */
    $width_and_height = '';
    if (isset($org_logo_width) && is_numeric($org_logo_width) && $org_logo_width>0) {
        $width_and_height = " width=\"$org_logo_width\"";
    }
    if (isset($org_logo_height) && is_numeric($org_logo_height) && $org_logo_height>0) {
        $width_and_height .= " height=\"$org_logo_height\"";
    }

    if (!isset($frame_top) || $frame_top == '' ) {
        $frame_top = '_top';
    }

    // load default theme if possible
    if (!isset($color) && @file_exists($theme[$theme_default]['PATH']))
        @include ($theme[$theme_default]['PATH']);

    if ( !isset( $color ) ) {
        $color = array();
        $color[0]  = '#dcdcdc';  /* light gray    TitleBar               */
        $color[1]  = '#800000';  /* red                                  */
        $color[2]  = '#cc0000';  /* light red     Warning/Error Messages */
        $color[4]  = '#ffffff';  /* white         Normal Background      */
        $color[7]  = '#0000cc';  /* blue          Links                  */
        $color[8]  = '#000000';  /* black         Normal text            */
    }

    if ( $errTitle == '' ) {
        $errTitle = $errString;
    }
    set_up_language($squirrelmail_language, true);

    displayHtmlHeader( $org_name.' - '.$errTitle, '', false );

    echo '<body text="'.$color[8].'" bgcolor="'.$color[4].'" link="'.$color[7].'" vlink="'.$color[7].'" alink="'.$color[7]."\">\n\n".
         '<center>';

    if (isset($org_logo) && ($org_logo != '')) {
        echo '<img src="'.$org_logo.'" alt="'.sprintf(_("%s Logo"), $org_name).
             "\"$width_and_height /><br />\n";
    }
    echo ( $hide_sm_attributions ? '' :
            '<small>' . sprintf (_("SquirrelMail version %s"), $version) . '<br />'.
            _("By the SquirrelMail Project Team") . "<br /></small>\n" ).
         '<table cellspacing="1" cellpadding="0" bgcolor="'.$color[1].'" width="70%">'.
         '<tr><td>'.
         '<table width="100%" border="0" bgcolor="'.$color[4].'" align="center">'.
         '<tr><td bgcolor="'.$color[0].'" align="center">'.
         '<font color="'.$color[2].'"><b>' . _("ERROR") . '</b></font>'.
         '</td></tr>'.
         '<tr><td align="center">' . $errString . '</td></tr>'.
         '<tr><td bgcolor="'.$color[0].'" align="center">'.
         '<font color="'.$color[2].'"><b>'.
         '<a href="'.$logout_link.'" target="'.$frame_top.'">'.
         _("Go to the login page") . '</a></b></font></td></tr>'.
         '</table></td></tr></table></center></body></html>';
}

function error_box($string, $color) {
    global $pageheader_sent, $org_title;

    if ( !isset( $color ) ) {
        $color = array();
        $color[0]  = '#dcdcdc';  /* light gray    TitleBar               */
        $color[1]  = '#800000';  /* red                                  */
        $color[2]  = '#cc0000';  /* light red     Warning/Error Messages */
        $color[4]  = '#ffffff';  /* white         Normal Background      */
        $color[7]  = '#0000cc';  /* blue          Links                  */
        $color[8]  = '#000000';  /* black         Normal text            */
        $color[9]  = '#ababab';  /* mid-gray      Darker version of #0   */
    }
    if ( !isset( $org_title ) ) {
        $org_title = "SquirrelMail";
    }

    $err = _("ERROR");

    $ret = concat_hook_function('error_box', $string);
    if($ret != '') {
        $string = $ret;
    }

    /* check if the page header has been sent; if not, send it! */
    if(!isset($pageheader_sent) && !$pageheader_sent) {
        /* include this just to be sure */
        include_once( SM_PATH . 'functions/page_header.php' );
        displayHtmlHeader($org_title.': '.$err);
        $pageheader_sent = TRUE;
        echo "<body text=\"$color[8]\" bgcolor=\"$color[4]\" link=\"$color[7]\" vlink=\"$color[7]\" alink=\"$color[7]\">\n\n";
    }

    echo '<table width="100%" cellpadding="1" cellspacing="0" align="center" border="0" bgcolor="'.$color[9].'">'.
         '<tr><td>'.
         '<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0" bgcolor="'.$color[4].'">'.
         '<tr><td align="center" bgcolor="'.$color[0].'">'.
         '<font color="'.$color[2].'"><b>' . $err . ':</b></font>'.
         '</td></tr><tr><td>'.
         '<table cellpadding="1" cellspacing="5" align="center" border="0">'.
         '<tr>' . html_tag( 'td', $string."\n", 'left') . '</tr></table>'.
         '</td></tr></table></td></tr></table>';
}

/**
 * Adds message that informs about non fatal error that can happen while saving preferences
 * @param string $message error message
 * @since 1.5.1 and 1.4.5
 */
function error_option_save($message) {
    global $optpage_save_error;

    if (! is_array($optpage_save_error) )
        $optpage_save_error=array();

    $optpage_save_error=array_merge($optpage_save_error,array($message));
}
