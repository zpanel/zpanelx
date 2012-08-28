<?php
/**
 * Sample Plugin that adds a banner ad to certain pages
 * without the need to modify any templates
 *
 */
class BannerPlugin extends Plugin
{
	
	var $config = array();
	
	
	function BannerPlugin()
	{
		if (include_once(dirname(__FILE__) . '/config.php')) {
			$this->config = $config;
		}
	}
	
	
	function beginParse(&$args)
	{
		$this->parseFile = $args[0];
	}
	
	
	function endParse(&$args)
	{
		global $atmail;
		
		// Only for simple interface
		if ($atmail->LoginType != 'simple') {
			return;
		}
		
		$file = basename($this->parseFile);

		if (in_array($file, $this->config['pages'])) {
			$args[0] = '<img src="http://atmail.com/images/splash/webmail-refreshed.jpg">' . $args[0];
		}
	}
}