<?php

/**
  * SquirrelMail Demo Plugin
  * @copyright 2006-2011 The SquirrelMail Project Team
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @version $Id$
  * @package plugins
  * @subpackage demo
  */



/**
  * Register this plugin with SquirrelMail
  *
  * @return void
  *
  */
function squirrelmail_plugin_init_demo() 
{
//FIXME: put *ALL* SM hooks in here... and put them in the right order
//FIXME: many hooks have examples in the original demo plugin in trunk/plugins/demo

   global $squirrelmail_plugin_hooks;

//FIXME: this hook not yet implemented below 
   $squirrelmail_plugin_hooks['login_cookie']['demo']
      = 'demo_login_cookie';

//FIXME: this hook not yet implemented below 
   $squirrelmail_plugin_hooks['login_top']['demo']
      = 'demo_login_top';

//FIXME: this hook not yet implemented below 
   $squirrelmail_plugin_hooks['login_form']['demo']
      = 'demo_login_form';

//FIXME: this hook not yet implemented below 
   $squirrelmail_plugin_hooks['login_bottom']['demo']
      = 'demo_login_bottom';

   $squirrelmail_plugin_hooks['menuline']['demo']
      = 'demo_menuline';

   $squirrelmail_plugin_hooks['optpage_register_block']['demo']
      = 'demo_option_link';

   $squirrelmail_plugin_hooks['configtest']['demo']
      = 'demo_check_configuration';
}



/**
  * Returns info about this plugin
  *
  * @return array An array of plugin information.
  *
  */
function demo_info()
{

   return array(
             'english_name' => 'Demo',
             'version' => 'CORE',
             'summary' => 'This plugin provides test/sample code for many of the hook points in the SquirrelMail core.',
             'details' => 'This plugin provides test/sample code for many of the hook points in the SquirrelMail core.', 
             'requires_configuration' => 0,
             'requires_source_patch' => 0,
          );

}



/**
  * Returns version info about this plugin
  *
  */
function demo_version()
{
   $info = demo_info();
   return $info['version'];
}



/**
  * Add link to menu at top of content pane
  *
  * @return void
  *
  */
function demo_menuline() 
{
   include_once(SM_PATH . 'plugins/demo/functions.php');
   demo_menuline_do();
}



/**
  * Inserts an option block in the main SM options page
  *
  * @return void
  *
  */
function demo_option_link()
{
   include_once(SM_PATH . 'plugins/demo/functions.php');
   demo_option_link_do();
}



/**
  * Validate that this plugin is configured correctly
  *
  * @return boolean Whether or not there was a
  *                 configuration error for this plugin.
  *
  */
function demo_check_configuration()
{
   include_once(SM_PATH . 'plugins/demo/functions.php');
   return demo_check_configuration_do();
}



