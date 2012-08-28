<?php


/**
  * SquirrelMail Demo Plugin
  *
  * @copyright 2006-2011 The SquirrelMail Project Team
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @version $Id$
  * @package plugins
  * @subpackage demo
  */


/**
  * Add link to menu at top of content pane
  *
  * @return void
  *
  */
function demo_menuline_do()
{
   sq_change_text_domain('demo');
   displayInternalLink('plugins/demo/demo.php', _("Demo"), '');
   echo "&nbsp;&nbsp;\n";
   sq_change_text_domain('squirrelmail');
}



/**
  * Inserts an option block in the main SM options page
  *
  */
function demo_option_link_do()
{

   global $optpage_blocks;

   sq_change_text_domain('demo');

   $optpage_blocks[] = array(
      'name' => _("Demo"),
      'url' => sqm_baseuri() . 'plugins/demo/demo.php',
      'desc' => _("This is where you would describe what your plugin does."),
      'js' => FALSE
   );

   sq_change_text_domain('squirrelmail');

}



/**
  * Validate that this plugin is configured correctly
  *
  * @return boolean Whether or not there was a
  *                 configuration error for this plugin.
  *
  */
function demo_check_configuration_do()
{

   // test for something that this plugin requires, print error if 
   // misconfigured or requirements are missing
   //
   if (FALSE)  // put something meaningful here
   {
      do_err('Demo plugin is missing something important', FALSE);
      return TRUE;  // return FALSE if you only want to display a non-critical error
   }

   return FALSE;

}



