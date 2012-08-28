<?php

/**
 * setup.php
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This is a standard Squirrelmail-1.2 API for plugins.
 *
 * $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 */

/* This button fills out a form with your setup information already
   gathered -- all you have to do is type. */


/* Initialize the bug report plugin */
function squirrelmail_plugin_init_bug_report() {
    global $squirrelmail_plugin_hooks;

    $squirrelmail_plugin_hooks['menuline']['bug_report']
        = 'bug_report_button';
    $squirrelmail_plugin_hooks['optpage_loadhook_display']['bug_report']
        = 'bug_report_block';
}


/* Show the button in the main bar */
function bug_report_button() {
    include_once(SM_PATH . 'plugins/bug_report/functions.php');
    bug_report_button_do();
}


function bug_report_block() {
    include_once(SM_PATH . 'plugins/bug_report/functions.php');
    bug_report_block_do();
}

