<?php

/**
 * plugin.php
 *
 * This file provides the framework for a plugin architecture.
 *
 * Documentation on how to write plugins might show up some time.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: plugin.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** Everything needs global.. */
require_once(SM_PATH . 'functions/global.php');

global $squirrelmail_plugin_hooks;
$squirrelmail_plugin_hooks = array();

/**
 * This function adds a plugin.
 * @param string $name Internal plugin name (ie. delete_move_next)
 * @return void
 */
function use_plugin ($name) {
    if (file_exists(SM_PATH . "plugins/$name/setup.php")) {
        include_once(SM_PATH . "plugins/$name/setup.php");
        $function = "squirrelmail_plugin_init_$name";
        if (function_exists($function)) {
            $function();
        }
    }
}

/**
 * This function executes a hook.
 * @param string $name Name of hook to fire
 * @return mixed $data
 */
function do_hook ($name) {
    global $squirrelmail_plugin_hooks;
    $data = func_get_args();
    $ret = '';

    if (isset($squirrelmail_plugin_hooks[$name])
          && is_array($squirrelmail_plugin_hooks[$name])) {
        foreach ($squirrelmail_plugin_hooks[$name] as $function) {
            /* Add something to set correct gettext domain for plugin. */
            if (function_exists($function)) {
                $function($data);
            }
        }
    }

    /* Variable-length argument lists have a slight problem when */
    /* passing values by reference. Pity. This is a workaround.  */
    return $data;
}

/**
 * This function executes a hook and allows for parameters to be passed.
 *
 * @param string name the name of the hook
 * @param mixed param the parameters to pass to the hook function
 * @return mixed the return value of the hook function
 */
function do_hook_function($name,$parm=NULL) {
    global $squirrelmail_plugin_hooks;
    $ret = '';

    if (isset($squirrelmail_plugin_hooks[$name])
          && is_array($squirrelmail_plugin_hooks[$name])) {
        foreach ($squirrelmail_plugin_hooks[$name] as $function) {
            /* Add something to set correct gettext domain for plugin. */
            if (function_exists($function)) {
                $ret = $function($parm);
            }
        }
    }

    /* Variable-length argument lists have a slight problem when */
    /* passing values by reference. Pity. This is a workaround.  */
    return $ret;
}

/**
 * This function executes a hook, concatenating the results of each
 * plugin that has the hook defined.
 *
 * @param string name the name of the hook
 * @param mixed parm optional hook function parameters
 * @return string a concatenation of the results of each plugin function
 */
function concat_hook_function($name,$parm=NULL) {
    global $squirrelmail_plugin_hooks;
    $ret = '';

    if (isset($squirrelmail_plugin_hooks[$name])
          && is_array($squirrelmail_plugin_hooks[$name])) {
        foreach ($squirrelmail_plugin_hooks[$name] as $function) {
            /* Concatenate results from hook. */
            if (function_exists($function)) {
                $ret .= $function($parm);
            }
        }
    }

    /* Variable-length argument lists have a slight problem when */
    /* passing values by reference. Pity. This is a workaround.  */
    return $ret;
}

/**
 * This function is used for hooks which are to return true or
 * false. If $priority is > 0, any one or more trues will override
 * any falses. If $priority < 0, then one or more falses will
 * override any trues.
 * Priority 0 means majority rules.  Ties will be broken with $tie
 *
 * @param string name the hook name
 * @param mixed parm the parameters for the hook function
 * @param int priority
 * @param bool tie
 * @return bool the result of the function
 */
function boolean_hook_function($name,$parm=NULL,$priority=0,$tie=false) {
    global $squirrelmail_plugin_hooks;
    $yea = 0;
    $nay = 0;
    $ret = $tie;

    if (isset($squirrelmail_plugin_hooks[$name]) &&
        is_array($squirrelmail_plugin_hooks[$name])) {

        /* Loop over the plugins that registered the hook */
        foreach ($squirrelmail_plugin_hooks[$name] as $function) {
            if (function_exists($function)) {
                $ret = $function($parm);
                if ($ret) {
                    $yea++;
                } else {
                    $nay++;
                }
            }
        }

        /* Examine the aftermath and assign the return value appropriately */
        if (($priority > 0) && ($yea)) {
            $ret = true;
        } elseif (($priority < 0) && ($nay)) {
            $ret = false;
        } elseif ($yea > $nay) {
            $ret = true;
        } elseif ($nay > $yea) {
            $ret = false;
        } else {
            // There's a tie, no action needed.
        }
        return $ret;
    }
    // If the code gets here, there was a problem - no hooks, etc.
    return NULL;
}

/**
 * This function checks whether the user's USER_AGENT is known to
 * be broken. If so, returns true and the plugin is invisible to the
 * offending browser.
 * *** THIS IS A TEST FOR JAVASCRIPT SUPPORT ***
 * FIXME: This function needs to have its name changed!
 *
 * @return bool whether this browser properly supports JavaScript
 */
function soupNazi(){

    $soup_menu = array('Mozilla/3','Mozilla/2','Mozilla/1', 'Opera 4',
                       'Opera/4', 'OmniWeb', 'Lynx');
    sqgetGlobalVar('HTTP_USER_AGENT', $user_agent, SQ_SERVER);
    foreach($soup_menu as $browser) {
        if(stristr($user_agent, $browser)) {
            return 1;
        }
    }
    return 0;
}
/*************************************/
/*** MAIN PLUGIN LOADING CODE HERE ***/
/*************************************/

/* On startup, register all plugins configured for use. 
   $plugins needs to be globalized because this file is
   sometimes included inside function (non-global) scope,
   such as for logout_error. */
global $plugins;
if (isset($plugins) && is_array($plugins)) {
    // turn on output buffering in order to prevent output of new lines
    ob_start();
    foreach ($plugins as $name) {
        use_plugin($name);
    }
    $output = trim(ob_get_contents());
    ob_end_clean();
    // if plugins output more than newlines and spacing, stop script execution.
    if (!empty($output)) {
        die($output);
    }
}

