<?php

/**
 * functions.php
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This is a standard Squirrelmail-1.2 API for plugins.
 *
 * $Id: setup.php 10633 2006-02-03 22:27:56Z jervfors $
 */


/**
 * Show the button in the main bar
 */
function bug_report_button_do() {

    global $username, $data_dir, $color;

    $bug_report_visible = getPref($data_dir, $username, 'bug_report_visible');

    if (! $bug_report_visible) {
        return;
    }

    displayInternalLink('plugins/bug_report/bug_report.php', _("Bug"), '');
    echo "&nbsp;&nbsp;\n";
}


/**
 * Register bug report option block
 *
 * @since 1.4.14
 *
 * @access private
 *
 */
function bug_report_block_do() {
    global $username, $data_dir, $optpage_data, $bug_report_visible;
    $bug_report_visible = getPref($data_dir, $username, 'bug_report_visible', FALSE);
    $optpage_data['grps']['bug_report'] = _("Bug Reports");
    $optionValues = array();
    $optionValues[] = array(
        'name'    => 'bug_report_visible',
        'caption' => _("Show button in toolbar"),
        'type'    => SMOPT_TYPE_BOOLEAN,
        'refresh' => SMOPT_REFRESH_ALL,
        'initial_value' => false
        );
    $optpage_data['vals']['bug_report'] = $optionValues;
}


