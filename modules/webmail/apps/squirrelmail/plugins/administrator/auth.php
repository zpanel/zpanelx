<?php
/**
 * Administrator plugin - Authentication routines
 *
 * This function tell other modules what users have access
 * to the plugin.
 *
 * @version $Id: auth.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @author Philippe Mingo
 * @copyright (c) 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package plugins
 * @subpackage administrator
 */

/**
 * Check if user has access to administrative functions
 *
 * @return boolean
 */
function adm_check_user() {
    global $plugins;
    require_once(SM_PATH . 'functions/global.php');
    
    if ( !in_array('administrator', $plugins) ) {
        return FALSE;
    }
    
    if ( !sqgetGlobalVar('username',$username,SQ_SESSION) ) {
        $username = '';
    }

    /* This needs to be first, for all non_options pages */
    //if (!defined('PAGE_NAME') || strpos(PAGE_NAME, 'options') === FALSE) {
    if (!defined('PAGE_NAME') 
     || (PAGE_NAME != 'administrator_options' && PAGE_NAME != 'options')) {
        $auth = FALSE;
    } else if (file_exists(SM_PATH . 'plugins/administrator/admins')) {
        $auths = file(SM_PATH . 'plugins/administrator/admins');
        array_walk($auths, 'adm_array_trim');
        $auth = in_array($username, $auths);
    } else if (file_exists(SM_PATH . 'config/admins')) {
        $auths = file(SM_PATH . 'config/admins');
        array_walk($auths, 'adm_array_trim');
        $auth = in_array($username, $auths);
    } else if (($adm_id = fileowner(SM_PATH . 'config/config.php')) &&
               function_exists('posix_getpwuid')) {
        $adm = posix_getpwuid( $adm_id );
        $auth = ($username == $adm['name']);
    } else {
        $auth = FALSE;
    }

    return ($auth);
}

/**
 * Removes whitespace from array values
 * @param string $value array value that has to be trimmed
 * @param string $key array key
 * @since 1.5.1 and 1.4.5
 * @access private
 */
function adm_array_trim(&$value,$key) {
    $value=trim($value);
}
