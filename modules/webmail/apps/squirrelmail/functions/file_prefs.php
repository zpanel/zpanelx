<?php

/**
 * file_prefs.php
 *
 * This contains functions for manipulating user preferences in files
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: file_prefs.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage prefs
 * @since 1.2.5
 */

/**
 * Check the preferences into the session cache.
 */
function cachePrefValues($data_dir, $username) {
    global $prefs_are_cached, $prefs_cache;

    sqgetGlobalVar('prefs_are_cached', $prefs_are_cached, SQ_SESSION );
    if ( isset($prefs_are_cached) && $prefs_are_cached) {
        sqgetGlobalVar('prefs_cache', $prefs_cache, SQ_SESSION );
        return;
    }

    sqsession_unregister('prefs_cache');
    sqsession_unregister('prefs_are_cached');

    /* Calculate the filename for the user's preference file */
    $filename = getHashedFile($username, $data_dir, "$username.pref");

    /* A call to checkForPrefs here should take eliminate the need for */
    /* this to be called throughout the rest of the SquirrelMail code. */
    checkForPrefs($data_dir, $username, $filename);

    /* Make sure that the preference file now DOES exist. */
    if (!file_exists($filename)) {
        logout_error( sprintf( _("Preference file, %s, does not exist. Log out, and log back in to create a default preference file."), $filename)  );
        exit;
    }

    /* Open the file, or else display an error to the user. */
    if(!$file = @fopen($filename, 'r'))
    {
        logout_error( sprintf( _("Preference file, %s, could not be opened. Contact your system administrator to resolve this issue."), $filename) );
        exit;
    }

    /* Read in the preferences. */
    $highlight_num = 0;
    while (! feof($file)) {
        $pref = '';
        /* keep reading a pref until we reach an eol (\n (or \r for macs)) */
        while($read = fgets($file, 1024))
        {
                $pref .= $read;
                if(strpos($read,"\n") || strpos($read,"\r"))
                        break;
        }
        $pref = trim($pref);
        $equalsAt = strpos($pref, '=');
        if ($equalsAt > 0) {
            $key = substr($pref, 0, $equalsAt);
            $value = substr($pref, $equalsAt + 1);
            /* this is to 'rescue' old-style highlighting rules. */
            if (substr($key, 0, 9) == 'highlight') {
                $key = 'highlight' . $highlight_num;
                $highlight_num ++;
            }

            if ($value != '') {
                $prefs_cache[$key] = $value;
            }
        }
    }
    fclose($file);

    $prefs_are_cached = TRUE;

    sqsession_register($prefs_cache, 'prefs_cache');
    sqsession_register($prefs_are_cached, 'prefs_are_cached');
}

/**
 * Return the value for the preference given by $string.
 */
function getPref($data_dir, $username, $string, $default = '') {
    global $prefs_cache;

    $result = do_hook_function('get_pref_override',array($username, $string));
//FIXME: testing below for !$result means that a plugin cannot fetch its own pref value of 0, '0', '', FALSE, or anything else that evaluates to boolean FALSE.
    if (!$result) {
        cachePrefValues($data_dir, $username);
        if (isset($prefs_cache[$string])) {
            $result = $prefs_cache[$string];
        } else {
//FIXME: is there justification for having these TWO hooks so close together?  who uses these?
            $result = do_hook_function('get_pref', array($username, $string));
//FIXME: testing below for !$result means that a plugin cannot fetch its own pref value of 0, '0', '', FALSE, or anything else that evaluates to boolean FALSE.
            if (!$result) {
                $result = $default;
            }
        }
    }
    return ($result);
}

/**
 * Save the preferences for this user.
 */
function savePrefValues($data_dir, $username) {
    global $prefs_cache;

    $filename = getHashedFile($username, $data_dir, "$username.pref");

    /* Open the file for writing, or else display an error to the user. */
    if(!$file = @fopen($filename.'.tmp', 'w'))
    {
        logout_error( sprintf( _("Preference file, %s, could not be opened. Contact your system administrator to resolve this issue."), $filename.'.tmp') );
        exit;
    }
    foreach ($prefs_cache as $Key => $Value) {
        if (isset($Value)) {
            if ( sq_fwrite($file, $Key . '=' . $Value . "\n") === FALSE ) {
               logout_error( sprintf( _("Preference file, %s, could not be written. Contact your system administrator to resolve this issue.") , $filename . '.tmp') );
               exit;
            }
        }
    }
    fclose($file);
    if (! @copy($filename . '.tmp',$filename) ) {
        logout_error( sprintf( _("Preference file, %s, could not be copied from temporary file, %s. Contact your system administrator to resolve this issue."), $filename, $filename . '.tmp') );
        exit;
    }
    @unlink($filename . '.tmp');
    @chmod($filename, 0600);
    sqsession_register($prefs_cache , 'prefs_cache');
}

/**
 * Remove a preference for the current user.
 */
function removePref($data_dir, $username, $string) {
    global $prefs_cache;

    cachePrefValues($data_dir, $username);

    if (isset($prefs_cache[$string])) {
        unset($prefs_cache[$string]);
    }

    savePrefValues($data_dir, $username);
}

/**
 * Set a there preference $string to $value.
 */
function setPref($data_dir, $username, $string, $value) {
    global $prefs_cache;

    cachePrefValues($data_dir, $username);
    if (isset($prefs_cache[$string]) && ($prefs_cache[$string] == $value)) {
        return;
    }

    if ($value === '') {
        removePref($data_dir, $username, $string);
        return;
    }

    $prefs_cache[$string] = $value;
    savePrefValues($data_dir, $username);
}

/**
 * Check for a preferences file. If one can not be found, create it.
 */
function checkForPrefs($data_dir, $username, $filename = '') {
    /* First, make sure we have the filename. */
    if ($filename == '') {
        $filename = getHashedFile($username, $data_dir, "$username.pref");
    }

    /* Then, check if the file exists. */
    if (!@file_exists($filename) ) {
        /* First, check the $data_dir for the default preference file. */
        if(substr($data_dir,-1) != '/') {
            $data_dir .= '/';
        }
        $default_pref = $data_dir . 'default_pref';

        /* If it is not there, check the internal data directory. */
        if (!@file_exists($default_pref)) {
            $default_pref = SM_PATH . 'data/default_pref';
        }

        /* Otherwise, report an error. */
        $errTitle = sprintf( _("Error opening %s"), $default_pref );
        if (!is_readable($default_pref)) {
            $errString = $errTitle . "<br />\n" .
                         _("Default preference file not found or not readable!") . "<br />\n" .
                         _("Please contact your system administrator and report this error.") . "<br />\n";
            logout_error( $errString, $errTitle );
            exit;
        } else if (!@copy($default_pref, $filename)) {
            $uid = 'httpd';
            if (function_exists('posix_getuid')){
                $user_data = posix_getpwuid(posix_getuid());
                $uid = $user_data['name'];
            }
            $errString = $errTitle . '<br />' .
                       _("Could not create initial preference file!") . "<br />\n" .
                       sprintf( _("%s should be writable by user %s"), $data_dir, $uid ) .
                       "<br />\n" . _("Please contact your system administrator and report this error.") . "<br />\n";
            logout_error( $errString, $errTitle );
            exit;
        }
    }
}

/**
 * Write the User Signature.
 */
function setSig($data_dir, $username, $number, $value) {
    // Limit signature size to 64KB (database BLOB limit)
    if (strlen($value)>65536) {
        error_option_save(_("Signature is too big."));
        return;
    }
    $filename = getHashedFile($username, $data_dir, "$username.si$number");
    /* Open the file for writing, or else display an error to the user. */
    if(!$file = @fopen("$filename.tmp", 'w')) {
        logout_error( sprintf( _("Signature file, %s, could not be opened. Contact your system administrator to resolve this issue."), $filename . '.tmp') );
        exit;
    }
    if ( sq_fwrite($file, $value) === FALSE ) {
       logout_error( sprintf( _("Signature file, %s, could not be written. Contact your system administrator to resolve this issue.") , $filename . '.tmp'));
       exit;
    }
    fclose($file);
    if (! @copy($filename . '.tmp',$filename) ) {
       logout_error( sprintf( _("Signature file, %s, could not be copied from temporary file, %s. Contact your system administrator to resolve this issue."), $filename, $filename . '.tmp') );
       exit;
    }
    @unlink($filename . '.tmp');
    @chmod($filename, 0600);

}

/**
 * Get the signature.
 */
function getSig($data_dir, $username, $number) {
    $filename = getHashedFile($username, $data_dir, "$username.si$number");
    $sig = '';
    if (file_exists($filename)) {
        /* Open the file, or else display an error to the user. */
        if(!$file = @fopen($filename, 'r'))
        {
            logout_error( sprintf( _("Signature file, %s, could not be opened. Contact your system administrator to resolve this issue."), $filename) );
            exit;
        }
        while (!feof($file)) {
            $sig .= fgets($file, 1024);
        }
        fclose($file);
    }
    return $sig;
}

