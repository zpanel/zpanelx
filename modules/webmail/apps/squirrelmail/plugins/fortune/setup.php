<?php

/**
 * plugins/fortune/setup.php
 *
 * Original code contributed by paulm@spider.org
 *
 * Simple SquirrelMail WebMail Plugin that displays the output of
 * fortune above the message listing.
 *
 * @copyright (c) 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage fortune
 *
 */

/**
 * Init plugin
 * @access private
 */
function squirrelmail_plugin_init_fortune() {
  global $squirrelmail_plugin_hooks;

  $squirrelmail_plugin_hooks['mailbox_index_before']['fortune'] = 'fortune';
  $squirrelmail_plugin_hooks['optpage_loadhook_display']['fortune'] = 'fortune_optpage_loadhook_display';
}

/**
 * Show fortune
 * @access private
 */
function fortune() {
    global $fortune_visible, $username, $data_dir;
    $fortune_visible = getPref($data_dir, $username, 'fortune_visible');

    // Don't show fortune if not enabled
    if (empty($fortune_visible)) {
        return;
    }

    include_once(SM_PATH . 'plugins/fortune/fortune_functions.php');
    fortune_show();
}

/**
 * Add fortune options
 * @access private
 */
function fortune_optpage_loadhook_display() {
    include_once(SM_PATH . 'plugins/fortune/fortune_functions.php');
    fortune_show_options();
}

