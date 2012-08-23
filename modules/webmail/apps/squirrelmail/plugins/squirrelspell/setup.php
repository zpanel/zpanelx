<?php
/**
 * setup.php
 * -----------
 * Squirrelspell setup file, as defined by the SquirrelMail-1.2 API.
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * @author Konstantin Riabitsev <icon@duke.edu>
 * @version $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

/** @ignore */
if (! defined('SM_PATH')) define('SM_PATH','../../');

/**
 * Standard SquirrelMail plugin initialization API.
 *
 * @return void
 */
function squirrelmail_plugin_init_squirrelspell() {
  global $squirrelmail_plugin_hooks;
  $squirrelmail_plugin_hooks['compose_button_row']['squirrelspell'] =
      'squirrelspell_setup';
  $squirrelmail_plugin_hooks['optpage_register_block']['squirrelspell'] =
      'squirrelspell_optpage_register_block';
  $squirrelmail_plugin_hooks['options_link_and_description']['squirrelspell'] =
      'squirrelspell_options';
}

/**
 * This function formats and adds the plugin and its description to the
 * Options screen.
 *
 * @return void
 */
function squirrelspell_optpage_register_block() {
  global $optpage_blocks;
  /**
   * soupNazi checks if this browser is capable of using the plugin.
   */
  if (!soupNazi()) {
    /**
     * The browser checks out.
     * Register Squirrelspell with the $optionpages array.
     */
    $optpage_blocks[] =
      array(
        'name' => _("SpellChecker Options"),
        'url'  => '../plugins/squirrelspell/sqspell_options.php',
        'desc' => _("Here you may set up how your personal dictionary is stored, edit it, or choose which languages should be available to you when spell-checking."),
        'js'   => TRUE);
  }
}

/**
 * This function adds a "Check Spelling" link to the "Compose" row
 * during message composition.
 *
 * @return void
 */
function squirrelspell_setup() {
  /**
   * Check if this browser is capable of displaying SquirrelSpell
   * correctly.
   */
  if (!soupNazi()) {
    /**
     * Some people may choose to disable javascript even though their
     * browser is capable of using it. So these freaks don't complain,
     * use document.write() so the "Check Spelling" button is not
     * displayed if js is off in the browser.
     */
    echo "<script type=\"text/javascript\">\n".
      "<!--\n".
      'document.write("<input type=\"button\" value=\"'.
      _("Check Spelling").
      '\" name=\"check_spelling\" onclick=\"window.open(\'../plugins/squirrelspell/sqspell_'.
      'interface.php\', \'sqspell\', \'status=yes,width=550,height=370,'.
      'resizable=yes\')\" />");' . "\n".
      "//-->\n".
      "</script>\n";
  }
}

?>