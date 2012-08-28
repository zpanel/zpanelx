<?php
/**
 * Setup up a standard env for modules to run under
 */
error_reporting(E_ALL ^ E_NOTICE);

$libPath = dirname(dirname(__FILE__));

set_include_path("$libPath/libs/" . PATH_SEPARATOR . "$libPath/libs/Atmail/" . PATH_SEPARATOR . "$libPath/libs/PEAR/");

// Lets give the script some ram!
ini_set('memory_limit', -1);

// remove php time limit
set_time_limit(0);

// define STDIN constant if need be (non CLI php)
if (!defined('STDIN'))
{
    // Define stream constants
	define('STDIN', fopen('php://stdin', 'r'));
	define('STDOUT', fopen('php://stdout', 'w'));
    define('STDERR', fopen('php://stderr', 'w'));

   // Close the streams on script termination
   register_shutdown_function(
       create_function('',
       'fclose(STDIN); fclose(STDOUT); fclose(STDERR); return true;')
       );
}

?>