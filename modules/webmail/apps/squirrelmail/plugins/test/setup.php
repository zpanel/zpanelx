<?php

/**
  * SquirrelMail Test Plugin
  * @copyright 2006-2011 The SquirrelMail Project Team
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @version $Id$
  * @package plugins
  * @subpackage test
  */

/**
  * Register this plugin with SquirrelMail
  * 
  * @return void
  *
  */
function squirrelmail_plugin_init_test() {

    global $squirrelmail_plugin_hooks;

    $squirrelmail_plugin_hooks['menuline']['test'] 
        = 'test_menuline';

}


/**
  * Add link to menu at top of content pane
  *
  * @return void
  *
  */
function test_menuline() {

    include_once(SM_PATH . 'plugins/test/functions.php');
    return test_menuline_do();

}


/**
  * Returns info about this plugin
  *
  * @return array An array of plugin information.
  *
  */
function test_info()
{

   return array(
             'english_name' => 'Test',
             'version' => 'CORE',
             'summary' => 'This plugin provides some test mechanisms for further diagnosis of the system upon which you are attempting to run SquirrelMail.',
             'details' => 'This plugin provides some test mechanisms for further diagnosis of the system upon which you are attempting to run SquirrelMail.',
             'requires_configuration' => 0,
             'requires_source_patch' => 0,
          );

}


/**
  * Returns version info about this plugin
  *
  */
function test_version()
{

   $info = test_info();
   return $info['version'];

}


