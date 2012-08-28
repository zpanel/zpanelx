<?php

require_once('header.php');
require_once('Plugin.php');

/**
 * Stores plugin instances and handles all event
 * calls to plugins
 */

class PluginHandler
{
	
	var $plugins = array();
	
	var $handledEvents = array();
	
	
	function PluginHandler($pluginDir)
	{
		// load plugins
		$dirs = @glob("$pluginDir/*Plugin");
		if (is_array($dirs)) {
			
			foreach ($dirs as $dir) {
				
				if (!is_dir($dir)) {
					continue;
				}
				
				$files = @glob("$dir/*Plugin.php");
				if (is_array($files)) {
			
					foreach ($files as $file) {
						include_once($file);
						$class = str_replace('.php', '', basename($file));
						$this->plugins[] = new $class;
					}
				}
			}
		}
	}
	
	
	function triggerEvent($event, &$args)
	{	
		foreach ($this->plugins as $plugin) {
		    if (method_exists($plugin, $event)) {
			    $plugin->$event($args);
			    $args['caught'] = true;
		    } else {
		        $args['caught'] = false;
		    }
		}
	}
	
	
	function set($var, $value)
	{
		$this->$var = $value; 
	}
	
	
	function isHandled($event)
	{
	    foreach ($this->plugins as $plugin) {
	        if ($plugin->handlesEvent($event)) {
	            return true;
	        }
	    }
	    
	    return false;
	}
	
}
