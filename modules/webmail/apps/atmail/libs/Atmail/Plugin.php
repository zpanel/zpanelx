<?php

/**
 * Base class for @Mail Plugins
 * 
 * This class defines all the 'events' that
 * your plugin can handle. If you do not wish
 * to handle an event you do not need to define
 * the method for it. ie if you only want to catch
 * the 'onCreate' event then that is the only
 * method your Plugin class need define.
 * 
 * All plugins should extend this class e.g:
 * 
 * class MyPlugin extends Plugin { ... }
 * 
 * NOTE: This file (Plugin.php) is included by the 
 * PluginHandler class so your custom Plugin classes 
 * do not need to bother including it.
 *
 */

class Plugin {

    var $handledEvents = array();
    
    
    function handlesEvent($event)
    {
        return in_array($event, $this->handledEvents);
    }

	/**
	 * The atmail object is being created
	 *
	 */
	function onCreate()
	{

	}

	/**
	 * The script is about to exit
	 *
	 */
	function onExit()
	{

	}
	
	/**
	 * A file is about to be parsed
	 * 
	 * The file to be parsed is the first argument ($args[0])
	 * The variables for the file is the second argument ($args[1])
	 * You can alter the values contained in these variables by
	 * simply assigning new values e.g. $args[0] = 'somfile.html';
	 *
	 * @param array $args
	 */
	function beginParse(&$args)
	{
		
	}
	
	
	/**
	 * The contents of a parsed file is about to be
	 * returned to caller
	 * 
	 * The contents of the output is contained in $args[0]
	 * You can alter the contents simply assigning a new
	 * value e.g. $args[0] = preg_replace('/$some_pattern/', $some_value, $args[0]);
	 * 
	 * You can also incorporate another parsed file:
	 * $args[0] .= $this->atmail->parse('somefile.html');
	 *
	 * @param array $args
	 */
	function endParse(&$args)
	{

	}
	
	
	function onCHS()
	{
		
	}
	
	
	/**
	 * Allows for custom handling of the writeconf() call
	 *
	 */
	function onWriteConf()
	{
		
	}

}
?>
