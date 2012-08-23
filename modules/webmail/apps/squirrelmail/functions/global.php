<?php

/**
 * global.php
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: global.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/**
 * Set constants
 */
define('SQ_INORDER',0);
define('SQ_GET',1);
define('SQ_POST',2);
define('SQ_SESSION',3);
define('SQ_COOKIE',4);
define('SQ_SERVER',5);
define('SQ_FORM',6);

/** First code that should be executed before other files are loaded */

/**
 * Must be executed before any other scripts are loaded.
 *
 * If register_globals are on, unregister globals.
 * Second test covers boolean set as string (php_value register_globals off).
 */
if ((bool) ini_get('register_globals') &&
    strtolower(ini_get('register_globals'))!='off') {
    /**
     * Remove all globals that are not reserved by PHP
     * 'value' and 'key' are used by foreach. Don't unset them inside foreach.
     */
    foreach ($GLOBALS as $key => $value) {
        switch($key) {
        case 'HTTP_POST_VARS':
        case '_POST':
        case 'HTTP_GET_VARS':
        case '_GET':
        case 'HTTP_COOKIE_VARS':
        case '_COOKIE':
        case 'HTTP_SERVER_VARS':
        case '_SERVER':
        case 'HTTP_ENV_VARS':
        case '_ENV':
        case 'HTTP_POST_FILES':
        case '_FILES':
        case '_REQUEST':
        case 'HTTP_SESSION_VARS':
        case '_SESSION':
        case 'GLOBALS':
        case 'key':
        case 'value':
            break;
        default:
            unset($GLOBALS[$key]);
        }
    }
    // Unset variables used in foreach
    unset($GLOBALS['key']);
    unset($GLOBALS['value']);
}

/**
 * There are some PHP settings that SquirrelMail is incompatible with
 * and cannot be changed by software at run-time; refuse to run if such
 * settings are being used...
 */
$php_session_auto_start = ini_get('session.auto_start');
if ((bool)$php_session_auto_start && $php_session_auto_start != 'off') {
    die('SquirrelMail 1.4.x is not compatible with PHP\'s session.auto_start setting.  Please disable it at least for the location where SquirrelMail is installed.');
}

/**
 * Strip any tags added to the url from PHP_SELF.
 * This fixes hand crafted url XXS expoits for any
 * page that uses PHP_SELF as the FORM action.
 * Must be executed before strings.php is loaded (php_self() call in strings.php).
 * Update: strip_tags() won't catch something like
 * src/right_main.php?sort=0&startMessage=1&mailbox=INBOX&xxx="><script>window.open("http://example.com")</script>
 * or
 * contrib/decrypt_headers.php/%22%20onmouseover=%22alert(%27hello%20world%27)%22%3E
 * because it doesn't bother with broken tags.
 * htmlspecialchars() is the preferred method.
 */
if (isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = htmlspecialchars($_SERVER['PHP_SELF']);
}
/*
 * same needed for QUERY_STRING because SquirrelMail
 * uses it along with PHP_SELF when using location
 * strings
 */
if (isset($_SERVER['QUERY_STRING'])) {
    $_SERVER['QUERY_STRING'] = htmlspecialchars($_SERVER['QUERY_STRING']);
}
/*
 * same needed for REQUEST_URI because it's used in php_self()
 */
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = htmlspecialchars($_SERVER['REQUEST_URI']);
}

/**
 * Bring in the config file
 * We need $session_name
 * config.php $version depends on strings.php.
 * strings.php sets $PHP_SELF.
 */
require_once(SM_PATH . 'functions/strings.php');
require_once(SM_PATH . 'config/config.php');

/**
 * Allow disabling of all plugins or enabling just a select few
 *
 * $temporary_plugins can be set in config_local.php, and
 * must be set as an array of plugin names that will be
 * the only ones activated (overriding the activation from
 * the main configuration file).  If the list is empty,
 * all plugins will be disabled.  Examples follow:
 *
 * Enable only Preview Pane and TNEF Decoder plugins:
 * $temporary_plugins = array('tnef_decoder', 'preview_pane');
 *
 * Disable all plugins:
 * $temporary_plugins = array();
 */
global $temporary_plugins;
if (isset($temporary_plugins)) {
    $plugins = $temporary_plugins;
}

/**
 * Detect SSL connections
 */
$is_secure_connection = is_ssl_secured_connection();

/** set the name of the session cookie */
if(isset($session_name) && $session_name) {
    ini_set('session.name' , $session_name);
} else {
    ini_set('session.name' , 'SQMSESSID');
}

/**
 * If magic_quotes_runtime is on, SquirrelMail breaks in new and creative ways.
 * Force magic_quotes_runtime off.
 * tassium@squirrelmail.org - I put it here in the hopes that all SM code includes this.
 * If there's a better place, please let me know.
 */
ini_set('magic_quotes_runtime','0');

/**
 * [#1518885] session.use_cookies = off breaks SquirrelMail
 *
 * When session cookies are not used, all http redirects, meta refreshes,
 * src/download.php and javascript URLs are broken. Setting must be set
 * before session is started.
 */
if (!(bool)ini_get('session.use_cookies') ||
    ini_get('session.use_cookies') == 'off') {
    ini_set('session.use_cookies','1');
}

/**
 * Make sure to have $base_uri always initialized to avoid having session
 * cookie set separately for each $base_uri subdirectory that receives direct
 * requests from user's browser (typically $base_uri and $base_uri/src).
 */
$base_uri = sqm_baseuri();

sqsession_is_active();

/* if running with magic_quotes_gpc then strip the slashes
   from POST and GET global arrays */

if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
    sqstripslashes($_GET);
    sqstripslashes($_POST);
}

/**
 * returns true if current php version is at mimimum a.b.c
 *
 * Called: check_php_version(4,1)
 * @param int a major version number
 * @param int b minor version number
 * @param int c release number
 * @return bool
 */
function check_php_version ($a = '0', $b = '0', $c = '0')
{
    global $SQ_PHP_VERSION;

    if(!isset($SQ_PHP_VERSION))
        $SQ_PHP_VERSION = substr( str_pad( preg_replace('/\D/','', PHP_VERSION), 3, '0'), 0, 3);

    return $SQ_PHP_VERSION >= ($a.$b.$c);
}

/**
 * returns true if the current internal SM version is at minimum a.b.c
 * These are plain integer comparisons, as our internal version is
 * constructed by us, as an array of 3 ints.
 *
 * Called: check_sm_version(1,3,3)
 * @param int a major version number
 * @param int b minor version number
 * @param int c release number
 * @return bool
 */
function check_sm_version($a = 0, $b = 0, $c = 0)
{
    global $SQM_INTERNAL_VERSION;
    if ( !isset($SQM_INTERNAL_VERSION) ||
         $SQM_INTERNAL_VERSION[0] < $a ||
         ( $SQM_INTERNAL_VERSION[0] == $a &&
           $SQM_INTERNAL_VERSION[1] < $b) ||
         ( $SQM_INTERNAL_VERSION[0] == $a &&
           $SQM_INTERNAL_VERSION[1] == $b &&
           $SQM_INTERNAL_VERSION[2] < $c ) ) {
        return FALSE;
    }
    return TRUE;
}


/**
 * Recursively strip slashes from the values of an array.
 * @param array array the array to strip, passed by reference
 * @return void
 */
function sqstripslashes(&$array) {
    if(count($array) > 0) {
        foreach ($array as $index=>$value) {
            if (is_array($array[$index])) {
                sqstripslashes($array[$index]);
            }
            else {
                $array[$index] = stripslashes($value);
            }
        }
    }
}

/**
 * Squelch error output to screen (only) for the given function.
 *
 * This provides an alternative to the @ error-suppression
 * operator where errors will not be shown in the interface
 * but will show up in the server log file (assuming the
 * administrator has configured PHP logging).
 * 
 * @since 1.4.12 and 1.5.2
 * 
 * @param string $function The function to be executed
 * @param array  $args     The arguments to be passed to the function
 *                         (OPTIONAL; default no arguments)
 *                         NOTE: The caller must take extra action if
 *                               the function being called is supposed
 *                               to use any of the parameters by 
 *                               reference.  In the following example,
 *                               $x is passed by reference and $y is
 *                               passed by value to the "my_func"
 *                               function.
 * sq_call_function_suppress_errors('my_func', array(&$x, $y));
 * 
 * @return mixed The return value, if any, of the function being
 *               executed will be returned.
 * 
 */ 
function sq_call_function_suppress_errors($function, $args=array()) {
   $display_errors = ini_get('display_errors');
   ini_set('display_errors', '0');
   $ret = call_user_func_array($function, $args);
   ini_set('display_errors', $display_errors);
   return $ret;
}

/**
 * Add a variable to the session.
 * @param mixed $var the variable to register
 * @param string $name the name to refer to this variable
 * @return void
 */
function sqsession_register ($var, $name) {

    sqsession_is_active();

    $_SESSION[$name] = $var;
}

/**
 * Delete a variable from the session.
 * @param string $name the name of the var to delete
 * @return void
 */
function sqsession_unregister ($name) {

    sqsession_is_active();

    unset($_SESSION[$name]);

    // starts throwing warnings in PHP 5.3.0 and is
    // removed in PHP 6 and is redundant anyway
    //session_unregister($name);
}

/**
 * Checks to see if a variable has already been registered
 * in the session.
 * @param string $name the name of the var to check
 * @return bool whether the var has been registered
 */
function sqsession_is_registered ($name) {
    $test_name = &$name;
    return isset($_SESSION[$test_name]);
}

/**
 * Search for the var $name in $_SESSION, $_POST, $_GET,
 * $_COOKIE, or $_SERVER and set it in provided var.
 *
 * If $search is not provided,  or == SQ_INORDER, it will search
 * $_SESSION, then $_POST, then $_GET. Otherwise,
 * use one of the defined constants to look for
 * a var in one place specifically.
 *
 * Note: $search is an int value equal to one of the
 * constants defined above.
 *
 * example:
 *    sqgetGlobalVar('username',$username,SQ_SESSION);
 *  -- no quotes around last param!
 *
 * @param string name the name of the var to search
 * @param mixed value the variable to return
 * @param int search constant defining where to look
 * @return bool whether variable is found.
 */
function sqgetGlobalVar($name, &$value, $search = SQ_INORDER) {

    /* NOTE: DO NOT enclose the constants in the switch
       statement with quotes. They are constant values,
       enclosing them in quotes will cause them to evaluate
       as strings. */
    switch ($search) {
        /* we want the default case to be first here,
           so that if a valid value isn't specified,
           all three arrays will be searched. */
      default:
      case SQ_INORDER: // check session, post, get
      case SQ_SESSION:
        if( isset($_SESSION[$name]) ) {
            $value = $_SESSION[$name];
            return TRUE;
        } elseif ( $search == SQ_SESSION ) {
            break;
        }
      case SQ_FORM:   // check post, get
      case SQ_POST:
        if( isset($_POST[$name]) ) {
            $value = $_POST[$name];
            return TRUE;
        } elseif ( $search == SQ_POST ) {
          break;
        }
      case SQ_GET:
        if ( isset($_GET[$name]) ) {
            $value = $_GET[$name];
            return TRUE;
        }
        /* NO IF HERE. FOR SQ_INORDER CASE, EXIT after GET */
        break;
      case SQ_COOKIE:
        if ( isset($_COOKIE[$name]) ) {
            $value = $_COOKIE[$name];
            return TRUE;
        }
        break;
      case SQ_SERVER:
        if ( isset($_SERVER[$name]) ) {
            $value = $_SERVER[$name];
            return TRUE;
        }
        break;
    }
    /* if not found, return false */
    return FALSE;
}

/**
 * Deletes an existing session, more advanced than the standard PHP
 * session_destroy(), it explicitly deletes the cookies and global vars.
 */
function sqsession_destroy() {

    /*
     * php.net says we can kill the cookie by setting just the name:
     * http://www.php.net/manual/en/function.setcookie.php
     * maybe this will help fix the session merging again.
     *
     * Changed the theory on this to kill the cookies first starting
     * a new session will provide a new session for all instances of
     * the browser, we don't want that, as that is what is causing the
     * merging of sessions.
     */

    global $base_uri;

    if (isset($_COOKIE[session_name()])) {
        sqsetcookie(session_name(), $_COOKIE[session_name()], 1, $base_uri);

        /*
         * Make sure to kill /src and /src/ cookies, just in case there are
         * some left-over or malicious ones set in user's browser.
         * NB: Note that an attacker could try to plant a cookie for one
         *     of the /plugins/* directories.  Such cookies can block
         *     access to certain plugin pages, but they do not influence
         *     or fixate the $base_uri cookie, so we don't worry about
         *     trying to delete all of them here.
         */
        sqsetcookie(session_name(), $_COOKIE[session_name()], 1, $base_uri . 'src');
        sqsetcookie(session_name(), $_COOKIE[session_name()], 1, $base_uri . 'src/');
    }

    if (isset($_COOKIE['key'])) sqsetcookie('key', 'SQMTRASH', 1, $base_uri);

    /* Make sure new session id is generated on subsequent session_start() */
    unset($_COOKIE[session_name()]);
    unset($_GET[session_name()]);
    unset($_POST[session_name()]);

    $sessid = session_id();
    if (!empty( $sessid )) {
        $_SESSION = array();
        @session_destroy();
    }

}

/**
 * Function to verify a session has been started.  If it hasn't
 * start a session up.  php.net doesn't tell you that $_SESSION
 * (even though autoglobal), is not created unless a session is
 * started, unlike $_POST, $_GET and such
 */

function sqsession_is_active() {
    sqsession_start();
}

/**
 * Function to start the session and store the cookie with the session_id as
 * HttpOnly cookie which means that the cookie isn't accessible by javascript
 * (IE6 only)
 * Note that as sqsession_is_active() no longer discriminates as to when
 * it calls this function, session_start() has to have E_NOTICE suppression
 * (thus the @ sign).
 *
 * @return void
 *
 * @since 1.4.16
 *
 */
function sqsession_start() {
    global $base_uri;

    session_set_cookie_params (0, $base_uri);
    @session_start();
    // could be: sq_call_function_suppress_errors('session_start');
    $session_id = session_id();

    // session_starts sets the sessionid cookie but without the httponly var
    // setting the cookie again sets the httponly cookie attribute
    //
    // need to check if headers have been sent, since sqsession_is_active()
    // has become just a passthru to this function, so the sqsetcookie()
    // below is called every time, even after headers have already been sent
    //
    if (!headers_sent())
       sqsetcookie(session_name(),$session_id,false,$base_uri);
}

/**
 * Set a cookie
 *
 * @param string  $sName     The name of the cookie.
 * @param string  $sValue    The value of the cookie.
 * @param int     $iExpire   The time the cookie expires. This is a Unix
 *                           timestamp so is in number of seconds since
 *                           the epoch.
 * @param string  $sPath     The path on the server in which the cookie
 *                           will be available on.
 * @param string  $sDomain   The domain that the cookie is available.
 * @param boolean $bSecure   Indicates that the cookie should only be
 *                           transmitted over a secure HTTPS connection.
 * @param boolean $bHttpOnly Disallow JS to access the cookie (IE6/FF2)
 * @param boolean $bReplace  Replace previous cookies with same name?
 *
 * @return void
 *
 * @since 1.4.16 and 1.5.1
 *
 */
function sqsetcookie($sName, $sValue='deleted', $iExpire=0, $sPath="", $sDomain="",
                     $bSecure=false, $bHttpOnly=true, $bReplace=false) {

    // if we have a secure connection then limit the cookies to https only.
    global $is_secure_connection;
    if ($sName && $is_secure_connection)
        $bSecure = true;

    // admin config can override the restriction of secure-only cookies
    //
    // (we have to check if the value is set and default it to true if
    // not because when upgrading without re-running conf.pl, it will
    // not be found in config/config.php and thusly evaluate to false,
    // but we want to default people who upgrade to true due to security
    // implications of setting this to false)
    //
    global $only_secure_cookies;
    if (!isset($only_secure_cookies)) $only_secure_cookies = true;
    if (!$only_secure_cookies)
        $bSecure = false;

    if (false && check_php_version(5,2)) {
       // php 5 supports the httponly attribute in setcookie, but because setcookie seems a bit
       // broken we use the header function for php 5.2 as well. We might change that later.
       //setcookie($sName,$sValue,(int) $iExpire,$sPath,$sDomain,$bSecure,$bHttpOnly);
    } else {
        if (!empty($sDomain)) {
            // Fix the domain to accept domains with and without 'www.'.
            if (strtolower(substr($sDomain, 0, 4)) == 'www.')  $sDomain = substr($sDomain, 4);
            $sDomain = '.' . $sDomain;

            // Remove port information.
            $Port = strpos($sDomain, ':');
            if ($Port !== false)  $sDomain = substr($sDomain, 0, $Port);
        }
        if (!$sValue) $sValue = 'deleted';
        header('Set-Cookie: ' . rawurlencode($sName) . '=' . rawurlencode($sValue)
                            . (empty($iExpire) ? '' : '; expires=' . gmdate('D, d-M-Y H:i:s', $iExpire) . ' GMT')
                            . (empty($sPath) ? '' : '; path=' . $sPath)
                            . (empty($sDomain) ? '' : '; domain=' . $sDomain)
                            . (!$bSecure ? '' : '; secure')
                            . (!$bHttpOnly ? '' : '; HttpOnly'), $bReplace);
    }
}

/**
 * Detect whether or not we have a SSL secured (HTTPS)
 * connection to the browser
 *
 * It is thought to be so if you have 'SSLOptions +StdEnvVars'
 * in your Apache configuration,
 *     OR if you have HTTPS set to a non-empty value (except "off")
 *        in your HTTP_SERVER_VARS,
 *     OR if you have HTTP_X_FORWARDED_PROTO=https in your HTTP_SERVER_VARS,
 *     OR if you are on port 443.
 *
 * Note: HTTP_X_FORWARDED_PROTO could be sent from the client and
 *       therefore possibly spoofed/hackable - for now, the
 *       administrator can tell SM to ignore this value by setting 
 *       $sq_ignore_http_x_forwarded_headers to boolean TRUE in
 *       config/config_local.php, but in the future we may
 *       want to default this to TRUE and make administrators
 *       who use proxy systems turn it off (see 1.5.2+).
 *
 * Note: It is possible to run SSL on a port other than 443, and
 *       if that is the case, the administrator should set
 *       $sq_https_port to the applicable port number in
 *       config/config_local.php
 *
 * @return boolean TRUE if the current connection is SSL-encrypted;
 *                 FALSE otherwise.
 *
 * @since 1.4.17 and 1.5.2 
 *
 */
function is_ssl_secured_connection()
{ 
    global $sq_ignore_http_x_forwarded_headers, $sq_https_port;
    $https_env_var = getenv('HTTPS');
    if ($sq_ignore_http_x_forwarded_headers
     || !sqgetGlobalVar('HTTP_X_FORWARDED_PROTO', $forwarded_proto, SQ_SERVER))
        $forwarded_proto = '';
    if (empty($sq_https_port)) // won't work with port 0 (zero)
       $sq_https_port = 443;
    if ((isset($https_env_var) && strcasecmp($https_env_var, 'on') === 0)
     || (sqgetGlobalVar('HTTPS', $https, SQ_SERVER) && !empty($https)
      && strcasecmp($https, 'off') !== 0)
     || (strcasecmp($forwarded_proto, 'https') === 0)
     || (sqgetGlobalVar('SERVER_PORT', $server_port, SQ_SERVER)
      && $server_port == $sq_https_port))
        return TRUE;
    return FALSE;
}

/**
 * Determine if there are lines in a file longer than a given length
 *
 * @param string $filename   The full file path of the file to inspect
 * @param int    $max_length If any lines in the file are GREATER THAN
 *                           this number, this function returns TRUE.
 *
 * @return boolean TRUE as explained above, otherwise, (no long lines
 *                 found) FALSE is returned.
 *
 */
function file_has_long_lines($filename, $max_length) {

    $FILE = @fopen($filename, 'rb');

    if ($FILE) {
        while (!feof($FILE)) {
            $buffer = fgets($FILE, 4096);
            if (strlen($buffer) > $max_length) {
                fclose($FILE);
                return TRUE;
            }
        }
        fclose($FILE);
    }

    return FALSE;
}

