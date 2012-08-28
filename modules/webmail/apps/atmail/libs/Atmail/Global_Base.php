<?php
// +----------------------------------------------------------------+
// | Global_Base.php												|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: Febuary 2005												|
// +----------------------------------------------------------------+

require_once('Config.php');

// Report simple running errors
if (isset($pref['display_php_errors']) && $pref['display_php_errors'] == 1)
    error_reporting(E_ERROR | E_WARNING | E_PARSE );
else
    error_reporting(0);

// Check for magic quotes
// and remove slashes
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

require_once('header.php');

define('ATMAIL_PEAR_PATH_SET', 1);


require_once('Log.php');
require_once('SQL.php');
require_once('Auth.php');
require_once('Filter.class.php');
require_once('PluginHandler.php');


// Disable iconv if not available
$pref['iconv'] = extension_loaded('iconv');

if (!$pref['iconv']) {
    function iconv($in, $out, $txt)
    {
        return $txt;
    }
}

if (!function_exists('file_get_contents')) {
    function file_get_contents($filename, $incpath = false, $resource_context = null)
    {
        if (false === $fh = fopen($filename, 'rb', $incpath)) {
            user_error('file_get_contents() failed to open stream: No such file or directory',
                E_USER_WARNING);
            return false;
        }

        clearstatcache();
        if ($fsize = @filesize($filename)) {
            $data = fread($fh, $fsize);
        } else {
            $data = '';
            while (!feof($fh)) {
                $data .= fread($fh, 8192);
            }
        }

        fclose($fh);
        return $data;
    }
}



if (!defined('FILE_USE_INCLUDE_PATH')) {
    define('FILE_USE_INCLUDE_PATH', 1);
}

if (!defined('LOCK_EX')) {
    define('LOCK_EX', 2);
}

if (!defined('FILE_APPEND')) {
    define('FILE_APPEND', 8);
}


/**
 * Replace file_put_contents()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.file_put_contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.25 $
 * @internal    resource_context is not supported
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $content, $flags = null, $resource_context = null)
    {
        // If $content is an array, convert it to a string
        if (is_array($content)) {
            $content = implode('', $content);
        }

        // If we don't have a string, throw an error
        if (!is_scalar($content)) {
            user_error('file_put_contents() The 2nd parameter should be either a string or an array',
                E_USER_WARNING);
            return false;
        }

        // Get the length of data to write
        $length = strlen($content);

        // Check what mode we are using
        $mode = ($flags & FILE_APPEND) ?
                    'a' :
                    'wb';

        // Check if we're using the include path
        $use_inc_path = ($flags & FILE_USE_INCLUDE_PATH) ?
                    true :
                    false;

        // Open the file for writing
        if (($fh = @fopen($filename, $mode, $use_inc_path)) === false) {
            user_error('file_put_contents() failed to open stream: Permission denied',
                E_USER_WARNING);
            return false;
        }

        // Attempt to get an exclusive lock
        $use_lock = ($flags & LOCK_EX) ? true : false ;
        if ($use_lock === true) {
            if (!flock($fh, LOCK_EX)) {
                return false;
            }
        }

        // Write to the file
        $bytes = 0;
        if (($bytes = @fwrite($fh, $content)) === false) {
            $errormsg = sprintf('file_put_contents() Failed to write %d bytes to %s',
                            $length,
                            $filename);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Close the handle
        @fclose($fh);

        // Check all the data was written
        if ($bytes != $length) {
            $errormsg = sprintf('file_put_contents() Only %d of %d bytes written, possibly out of free disk space.',
                            $bytes,
                            $length);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Return length
        return $bytes;
    }
}


/**
 * Checks for magic_quotes_gpc = On and strips them from incoming
 * requests if necessary
 */
if (get_magic_quotes_gpc()) {
 $_GET = array_map('stripslashes', $_GET);
 $_POST = array_map('stripslashes', $_POST);
 $_COOKIE = array_map('stripslashes', $_COOKIE);
}


class Global_Base {

	var $Bin;
	var $db;
	var $log;
	var $browser;
	var $auth;
	var $pluginHandler;

	function Global_Base()
	{
		global $pref;

		$this->db = new SQL();

		$path = (dirname(dirname(dirname(__FILE__))));
		$this->pluginHandler = new PluginHandler("$path/plugins/");
        $args = array();
		$this->pluginHandler->triggerEvent('onCreate', $args);

		$this->Bin = dirname($_SERVER['SCRIPT_FILENAME']);

	    foreach ($pref as $k => $v)
	        $this->$k = $v;

	    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	        $this->browser = "ie";
	    else
	        $this->browser = "ns";

		$_SESSION['referer'] = $_SERVER['SCRIPT_NAME'];
	}


	function &getAuthObj($return=true)
	{
		if (!session_id()) {
			$this->auth = new Auth();
			return $return ? $this->auth : null;
		}

		if (isset($_SESSION['auth']))
	    	$this->auth =& $_SESSION['auth'];
	    else
	    {
	    	$this->auth = new Auth();
	    	$_SESSION['auth'] =& $this->auth;
	    }

		return $return ? $this->auth : null;
	}


	// Parse a HTML/Template file, and expand all vars into a value
	function parse($file, $var=null)
	{
	    global $pref, $domains ;

	    $args = array(&$file, &$var);
	    $this->pluginHandler->triggerEvent('beginParse', $args);

	    // Just in case the user is requesting a file out of our directory limit
	    if (strpos($file, '../') !== false)
		{
	        $this->log->write_log( 'Error', "Security Alert : $file");

	        catcherror( "Security Alert: IP address {$_SERVER['REMOTE_ADDR']} logged" );
	    }

	    if (file_exists($this->Bin . "/$file"))
	    	$location = $this->Bin."/$file";
	    elseif (file_exists($pref['install_dir']."/$file"))
			$location = $pref['install_dir']."/$file";
	    elseif (file_exists($pref['install_dir']."/webadmin/$file"))
			$location = $pref['install_dir']."/webadmin/$file";
	    else
	        $location = "$file";

        if (!is_readable($location))
            die("$location is not readable, please check file permission!");

        $fh = fopen($location, 'r');

		if (!is_resource($fh))
			die("can't open $location");

		$page = '';
		$template = 0;

		while (!feof($fh))
		{
			$line = fgets($fh);

			//end of 'if' statement
	        if ( preg_match('/<!--\s+}\s+-->/', $line) )
			{
	            $template = 0;
	            continue;
	        }
			if ($template == 1) continue;

	        //if we are not inside an 'if' keep an eye out for one
			if ($template == 0)
			{
				// The start of an 'if' statement
				if ( preg_match('/<!--\s+if\s*\((.*?)\)\s+{\s+-->/', $line, $match) )
				{
					//echo "match = $match[1]<br>";
					// If the if expression is true
					eval("\$condition = ($match[1])? 1:0;");

					if ($condition)
					{
						$template = 2;
					}
					else
					{
						// Otherwise, it is false, and ignore block
						$template = 1;
					}

					// Skip the line
					continue;
				}
			}
			if ( preg_match('/<!--Include="(.*)"-->/', $line, $match) )
			{
				$include = $match[1];
				$path    = str_replace($file, "$include", $location);
				$path = addslashes($path);
				eval("\$path = \"$path\";");
				$page .= $this->parse($path, $var);
			}

			if (preg_match_all('/(\$[0-9A-Za-z_]+(\[\'[a-zA-Z0-9_]+\'\]|->[a-zA-Z0-9_]+))/', $line, $matches))
			{
				foreach ($matches[1] as $match)
				{
					eval("\$tmp = $match;");
					$line = str_replace($match, $tmp, $line);
				}
			}
//if (strpos($location, 'stats')) echo $line;
			$page .= $line;
		}
		//echo "end :$file<br>";

		$args = array(&$page);
		$this->pluginHandler->triggerEvent('endParse', $args);

	    return $page;
	}


	function &share_db()
	{
		return $this->db;
	}

	// Get the CGI params escaping any HTML characters
	function param_escape($name)
	{
	    return $this->escape_html($_REQUEST[$name]);
	}


	// Escape any HTML strings for Javascript XSS attacks or invalid input from the users URL
	function escape_html($string, $extended=true)
	{
		// Check any XSS and alert
		if (preg_match('/<SCRIPT|document.cookie|<\/script>/i', $string))
		{
	        $this->log->write_log( 'Error', "XSS Alert : {$_SERVER['SCRIPT_NAME']} : $string" );
	        catcherror( "Security Alert: IP address {$_SERVER['REMOTE_ADDR']} logged - XSS Attack detected" );
		}

		if ($extended) {
			// Escape <> chars
			$string = str_replace('<', '&lt;', $string);
			$string = str_replace('>', '&gt;', $string);

			// Change () to the Hex vlaues
			$string = str_replace('(', '&#40', $string);
			$string = str_replace(')', '&#41', $string);

			// Change # , & to the Hex values
			$string = str_replace('#', '&#35', $string);
			$string = str_replace('&', '&#38', $string);
		}

		return $string;
	}

	function param($param)
	{
		if (isset($_REQUEST[$param]))
			return $_REQUEST[$param];
		else
			return $this->$param;
	}

	// Provides access to member variables
	function get($name)
	{
		return $this->$name;
	}

	function httpheaders()
	{
	    ini_set('default_charset', 'utf-8');

		if (isset($_REQUEST['XUL']))
		{
			header('Content-Type: application/vnd.mozilla.xul+xml');
			header('Expires: -365d');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
		}
		elseif (isset($_REQUEST['RDF']))
		{
			header('Content-Type: text/rdf');
			header('Expires: -365d');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
		}
		elseif (isset($_REQUEST['ajax']) && strpos($_SERVER['SCRIPT_NAME'], 'parse.php') === false)
		{
			header("Content-type: text/xml; charset=utf-8");
			//header('Content-Type: text/xml');
			header('Expires: -365d');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
		}
		else
		{
			header('Content-type: text/html; charset=utf-8');
		}

		header('Cache-Control: public');
	}

	function calcmenu_height($filename=null)
	{
		global $pref, $domains;

		$h = array();

		// Calculate the length of a menu, shrink if we have settings disabled
		if ( $filename == "html/$this->Language/xp/toolbar.html" )
		{
			$h['MenuPullFileHeight'] = 168;
			if (!$pref['allow_Calendar']) $h['MenuPullFileHeight'] -= 42 ;
			if (!$pref['allow_SMS']) $h['MenuPullFileHeight'] -= 21 ;

			$h['MenuPullNewHeight'] = 120;
			if( !$pref['allow_Calendar']) $h['MenuPullNewHeight'] -= 43 ;
			if (!$pref['allow_SMS']) $h['MenuPullNewHeight'] -= 22 ;

            $h['MenuPullPreferencesHeight'] = 110;
            
			if (!$domains[$this->pop3host] || !$pref['allow_Passutil'] ) $h['MenuPullPreferencesHeight'] -= - 22 ;

			$h['MenuPullMessageHeight'] = 169;
			if (!$pref['allow_SpamSettings']) $h['MenuPullMessageHeight'] -= 30 ;
			
		}

		if ( $filename == "html/$this->Language/xp/toolbar_abook.html" )
		{
			$h['MenuPullFileHeight'] = 140;

			if(!$pref['allow_Calendar']) $h['MenuPullFileHeight'] -= 42;
			if(!$pref['allow_SMS']) $h['MenuPullFileHeight'] -= 21 ;

			$h['MenuPullPreferencesHeight'] = 98;
			if(!$domains[$this->pop3host] || !$pref['allow_Passutil'] ) $h['MenuPullPreferencesHeight'] -= 22 ;

			$h['MenuPullContactHeight'] = 96;
			if(!$pref['GlobalAbook'] && !$pref['GlobalAbookRead']) $h['MenuPullContactHeight'] -= 42 ;
		}

		$h['MenuPullHelpHeight'] = 146;
		if(!$pref['allow_Calendar']) $h['MenuPullHelpHeight'] -= 21 ;
		if(!$pref['allow_AskQuestion']) $h['MenuPullHelpHeight'] -= 28 ;

		return $h;
	}

	function substring($string, $start, $length)
	{
		if (function_exists('mb_substr')) {
			return mb_substr($string, $start, $length);
		}

		return substr($string, $start, $length);
	}


	function end()
	{
        $args = array();
		$this->pluginHandler->triggerEvent('onExit', $args);
		exit;
	}


	function writeConfig()
	{
		// catch any plugins that write alternate configs
		$return = false;
		$args = array(&$return);
		$this->pluginHandler->triggerEvent('onWriteConf', $args);
		if ($return) {
			return;
		}

		writeconf();
	}
	
	function debug($string)
	{
	    file_put_contents('php://stderr', "$string\n");
	}
}
