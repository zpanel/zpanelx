<?php

/**
  * SquirrelMail Demo Plugin
  *
  * This page is used as a place holder for custom plugin 
  * pages that are accessed directly by the client.
  *
  * @copyright 2006-2011 The SquirrelMail Project Team
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @version $Id$
  * @package plugins
  * @subpackage demo
  */


define('SM_PATH', '../../');
include_once(SM_PATH . 'include/validate.php');


// Make sure plugin is activated!
//
global $plugins;
if (!in_array('demo', $plugins))
   exit;


global $color;
displayPageHeader($color, 'None');


echo '<strong>HELLO WORLD</strong></body></html>';



