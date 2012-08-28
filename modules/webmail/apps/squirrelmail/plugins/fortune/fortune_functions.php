<?php
/**
 * plugins/fortune/fortune_functions.php
 *
 * Original code contributed by paulm@spider.org
 *
 * Simple SquirrelMail WebMail Plugin that displays the output of
 * fortune above the message listing.
 *
 * @copyright (c) 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id$
 * @package plugins
 * @subpackage fortune
 *
 */


/**
 * Function to show fortune
 * @access private
 */
function fortune_show() {

    global $color;
    
    $fortune_location = '/usr/bin/fortune';
    $exist = is_executable($fortune_location);
    
    if (!$exist) {
        $sMsg = sprintf(_("%s is not found."),$fortune_location);
    } else {
        $sMsg = htmlspecialchars(shell_exec($fortune_location . ' -s'));
    }
    
    
    echo "<center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"$color[10]\">\n".
        "<tr><td><table width=\"100%\" cellpadding=\"2\" cellspacing=\"1\" border=\"0\" bgcolor=\"$color[5]\">\n".
        "<tr><td align=\"center\">\n";
    echo '<table><tr><td>';
    echo '<center><em>' . _("Today's Fortune") . '</em></center><br /><pre>' .
        $sMsg .
        '</pre>';

    echo '</td></tr></table></td></tr></table></td></tr></table></center>';
}


/**
 * Add fortune options
 * @access private
 */
function fortune_show_options() {

    global $optpage_data, $username, $data_dir, $fortune_visible;
    $fortune_visible = getPref($data_dir, $username, 'fortune_visible');
    
    $optgrp = _("Fortunes");
    $optvals = array();
    
    $optvals[] = array(
                'name' => 'fortune_visible',
                'caption' => _("Show fortunes at top of mailbox"),
                'type' => SMOPT_TYPE_BOOLEAN,
                'refresh' => SMOPT_REFRESH_NONE                
            );

     $optpage_data['grps']['fortune'] = $optgrp;
     $optpage_data['vals']['fortune'] = $optvals;
}

