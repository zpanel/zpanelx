<?php

/**
 * SquirrelMail configtest script
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: configtest.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage config
 */

/************************************************************
 * NOTE: you do not need to change this script!             *
 * If it throws errors you need to adjust your config.      *
 ************************************************************/

function do_err($str, $exit = TRUE) {
    global $IND;
    echo '<p>'.$IND.'<font color="red"><b>ERROR:</b></font> ' .$str. "</p>\n";
    if($exit) {
         echo '</body></html>';
         exit;
    }
}

ob_implicit_flush();
/** This is the configtest page */
define('PAGE_NAME', 'configtest');

/** @ignore */
define('SM_PATH', '../');

/*
 * Load config before output begins.
 * functions/global.php cleans environment, then loads
 * functions/strings.php and config/config.php
 */
if (file_exists(SM_PATH . 'config/config.php')) {
    include(SM_PATH . 'functions/global.php');
}
$IND = str_repeat('&nbsp;',4);

// this must be done before the output is started because it may use the
// session
$test_location = get_location();
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta name="robots" content="noindex,nofollow">
  <title>SquirrelMail configtest</title>
</head>
<body>
<h1>SquirrelMail configtest</h1>

<p>This script will try to check some aspects of your SquirrelMail configuration
and point you to errors whereever it can find them. You need to go run <tt>conf.pl</tt>
in the <tt>config/</tt> directory first before you run this script.</p>

<?php

$included = array_map('basename', get_included_files() );
if(!in_array('config.php', $included)) {
    if(!file_exists(SM_PATH . 'config/config.php')) {
        do_err('Config file '.SM_PATH . 'config/config.php does not exist!<br />'.
                'You need to run <tt>conf.pl</tt> first.');
    }
    do_err('Could not read '.SM_PATH.'config/config.php! Check file permissions.');
}
if(!in_array('strings.php', $included)) {
    do_err('Could not include '.SM_PATH.'functions/strings.php!<br />'.
            'Check permissions on that file.');
}

/* checking PHP specs */

echo "<p><table>\n<tr><td>SquirrelMail version:</td><td><b>" . $version . "</b></td></tr>\n" .
     '<tr><td>Config file version:</td><td><b>' . $config_version . "</b></td></tr>\n" .
     '<tr><td>Config file last modified:</td><td><b>' .
         date ('d F Y H:i:s', filemtime(SM_PATH . 'config/config.php')) .
         "</b></td></tr>\n</table>\n</p>\n\n";

echo "Checking PHP configuration...<br />\n";

if(!check_php_version(4,1,0)) {
    do_err('Insufficient PHP version: '. PHP_VERSION . '! Minimum required: 4.1.0');
}

echo $IND . 'PHP version ' . PHP_VERSION . " OK.<br />\n";

// try to determine information about the user and group the web server is running as
//
$webOwnerID = 'N/A';
$webOwnerInfo = array('name' => 'N/A');
if (function_exists('posix_getuid'))
    $webOwnerID = posix_getuid();
if ($webOwnerID === FALSE)
    $webOwnerID = 'N/A';
if ($webOwnerID !== 'N/A' && function_exists('posix_getpwuid'))
    $webOwnerInfo = posix_getpwuid($webOwnerID);
if (!$webOwnerInfo)
    $webOwnerInfo = array('name' => 'N/A');
$webGroupID = 'N/A';
$webGroupInfo = array('name' => 'N/A');
if (function_exists('posix_getgid'))
    $webGroupID = posix_getgid();
if ($webGroupID === FALSE)
    $webGroupID = 'N/A';
if ($webGroupID !== 'N/A' && function_exists('posix_getgrgid'))
    $webGroupInfo = posix_getgrgid($webGroupID);
if (!$webGroupInfo)
    $webGroupInfo = array('name' => 'N/A');

echo $IND . 'Running as ' . $webOwnerInfo['name'] . '(' . $webOwnerID
          . ') / ' . $webGroupInfo['name'] . '(' . $webGroupID . ")<br />\n";

echo $IND . 'display_errors: ' . ini_get('display_errors') . "<br />\n";

echo $IND . 'error_reporting: ' . ini_get('error_reporting') . "<br />\n";

if ((bool) ini_get('session.auto_start') && ini_get('session.auto_start') != 'off') {
    $msg = 'session.auto_start is turned on in your PHP configuration, but SquirrelMail'
         . ' 1.4.x will not work with it (otherwise valid logins will usually'
         . ' result in "You must be logged in to access this page" errors).'
         . ' You can disable session.auto_start only in the squirrelmail directory' 
         . ' if you need to leave it turned on for other applications.';
    do_err($msg, true);
}

$safe_mode = ini_get('safe_mode');
if ($safe_mode) {
    echo $IND . 'safe_mode: ' . $safe_mode;
    if (empty($prefs_dsn) || empty($addrbook_dsn))
        echo ' (<font color="red">double check data and attachment directory ownership, etc!</font>)';
    if (!empty($addrbook_dsn) || !empty($prefs_dsn) || !empty($addrbook_global_dsn))
        echo ' (<font color="red">does PHP have access to database interface?</font>)';
    echo "<br />\n";
    $safe_mode_exec_dir = ini_get('safe_mode_exec_dir');
    echo $IND . 'safe_mode_exec_dir: ' . $safe_mode_exec_dir . "<br />\n";
}

/* variables_order check */

// FIXME(?): Hmm, how do we distinguish between when an ini setting is
//           not available (ini_set() returns empty string) and when
//           the administrator set the value to an empty string?  The
//           latter is sure to be highly rare, so for now, just assume
//           that empty value means the setting isn't even available
//           (could also check PHP version when this setting was implemented)
//           although, we'll also warn the user if it is empty, with
//           a non-fatal error
$variables_order = strtoupper(ini_get('variables_order'));
if (empty($variables_order))
    do_err('Your variables_order setting seems to be empty.  Make sure it is undefined in any PHP ini files, .htaccess files, etc. and not specifically set to an empty value or SquirrelMail may not function correctly', false);
else if (strpos($variables_order, 'G') === FALSE
 || strpos($variables_order, 'P') === FALSE
 || strpos($variables_order, 'C') === FALSE
 || strpos($variables_order, 'S') === FALSE) {
    do_err('Your variables_order setting is insufficient for SquirrelMail to function.  It needs at least "GPCS", but you have it set to "' . htmlspecialchars($variables_order) . '"', true);
} else {
    echo $IND . "variables_order OK: $variables_order.<br />\n";
}


/* gpc_order check (removed from PHP as of v5.0) */

if (!check_php_version(5)) {
    // FIXME(?): Hmm, how do we distinguish between when an ini setting is
    //           not available (ini_set() returns empty string) and when
    //           the administrator set the value to an empty string?  The
    //           latter is sure to be highly rare, so for now, just assume
    //           that empty value means the setting isn't even available
    //           (could also check PHP version when this setting was implemented)
    //           although, we'll also warn the user if it is empty, with
    //           a non-fatal error
    $gpc_order = strtoupper(ini_get('gpc_order'));
    if (empty($gpc_order))
        do_err('Your gpc_order setting seems to be empty.  Make sure it is undefined in any PHP ini files, .htaccess files, etc. and not specifically set to an empty value or SquirrelMail may not function correctly', false);
    else if (strpos($gpc_order, 'G') === FALSE
     || strpos($gpc_order, 'P') === FALSE
     || strpos($gpc_order, 'C') === FALSE) {
        do_err('Your gpc_order setting is insufficient for SquirrelMail to function.  It needs to be set to "GPC", but you have it set to "' . htmlspecialchars($gpc_order) . '"', true);
    } else {
        echo $IND . "gpc_order OK: $gpc_order.<br />\n";
    }
}


$php_exts = array('session','pcre');
$diff = array_diff($php_exts, get_loaded_extensions());
if(count($diff)) {
    do_err('Required PHP extensions missing: '.implode(', ',$diff) );
}

echo $IND . "PHP extensions OK. Dynamic loading is ";

if (!(bool)ini_get('enable_dl') || (bool)ini_get('safe_mode')) {
    echo "disabled.<br />\n";
} else {
    echo "enabled.<br />\n";
}


/* dangerous php settings */
/** mbstring.func_overload<>0 fix. See cvs HEAD comments. */
if (function_exists('mb_internal_encoding') &&
    check_php_version(4,2,0) &&
    (int)ini_get('mbstring.func_overload')!=0) {
    $mb_error='You have enabled mbstring overloading.'
        .' It can cause problems with SquirrelMail scripts that rely on single byte string functions.';
    do_err($mb_error);
}

/**
 * We code with register_globals = off. SquirrelMail should work in such setup
 * since 1.2.9 and 1.3.0. Running SquirrelMail with register_globals = on can
 * cause variable corruption and security issues. Globals can be turned off in
 * php.ini, webserver config and .htaccess files. Scripts can turn off globals only
 * in php 4.2.3 or older.
 */
if ((bool) ini_get('register_globals') &&
    ini_get('register_globals') != 'off') {
    $rg_error='You have enabled PHP register_globals.'
        .' Running PHP installation with register_globals=on can cause problems.'
        .' See <a href="http://www.php.net/manual/en/security.registerglobals.php">'
        .'security information about register_globals</a>.';
    do_err($rg_error,false);
}

/**
 * Do not use SquirrelMail with magic_quotes_* on.
 */
if ( (function_exists('get_magic_quotes_runtime') &&  @get_magic_quotes_runtime()) ||
     (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) ||
    ( (bool) ini_get('magic_quotes_sybase') && ini_get('magic_quotes_sybase') != 'off' )
    ) {
    $magic_quotes_warning='You have enabled any one of <tt>magic_quotes_runtime</tt>, '
        .'<tt>magic_quotes_gpc</tt> or <tt>magic_quotes_sybase</tt> in your PHP '
        .'configuration. We recommend all those settings to be off. SquirrelMail '
        .'may work with them on, but when experiencing stray backslashes in your mail '
        .'or other strange behaviour, it may be advisable to turn them off.';
    do_err($magic_quotes_warning,false);
}

if (ini_get('short_open_tag') == 0) {
    $short_open_tag_warning = 'You have configured PHP not to allow short tags '
        . '(<tt>short_open_tag=off</tt>). This shouldn\'t be a problem with '
        . 'SquirrelMail or any plugin coded coded according to the '
        . 'SquirrelMail Coding Guidelines, but if you experience problems with '
        . 'PHP code being displayed in some of the pages and changing setting '
        . 'to "on" solves the problem, please file a bug report against the '
        . 'failing plugin. The correct contact information is most likely '
        . 'to be found in the plugin documentation.';
    do_err($short_open_tag_warning, false);
}

/* checking paths */

echo "Checking paths...<br />\n";

if(!file_exists($data_dir)) {
    do_err("Data dir ($data_dir) does not exist!");
}
if(!is_dir($data_dir)) {
    do_err("Data dir ($data_dir) is not a directory!");
}
// datadir should be executable - but no clean way to test on that
if(!is_writable($data_dir)) {
    do_err("I cannot write to data dir ($data_dir)!");
}

// todo_ornot: actually write something and read it back.
echo $IND . "Data dir OK.<br />\n";


if($data_dir == $attachment_dir) {
    echo $IND . "Attachment dir is the same as data dir.<br />\n";
} else {
    if(!file_exists($attachment_dir)) {
        do_err("Attachment dir ($attachment_dir) does not exist!");
    }
    if (!is_dir($attachment_dir)) {
        do_err("Attachment dir ($attachment_dir) is not a directory!");
    }
    if (!is_writable($attachment_dir)) {
        do_err("I cannot write to attachment dir ($attachment_dir)!");
    }
    echo $IND . "Attachment dir OK.<br />\n";
}


/* check plugins and themes */
if (isset($plugins[0])) {
    foreach($plugins as $plugin) {
        if(!file_exists(SM_PATH .'plugins/'.$plugin)) {
            do_err('You have enabled the <i>'.$plugin.'</i> plugin, but I cannot find it.', FALSE);
        } elseif (!is_readable(SM_PATH .'plugins/'.$plugin.'/setup.php')) {
            do_err('You have enabled the <i>'.$plugin.'</i> plugin, but I cannot read its setup.php file.', FALSE);
        }
    }
    // load plugin functions
    include_once(SM_PATH . 'functions/plugin.php');
    // turn on output buffering in order to prevent output of new lines
    ob_start();
    foreach ($plugins as $name) {
        use_plugin($name);
    }
    // get output and remove whitespace
    $output = trim(ob_get_contents());
    ob_end_clean();
    // if plugins output more than newlines and spacing, stop script execution.
    if (!empty($output)) {
        $plugin_load_error = 'Some output is produced when plugins are loaded. Usually this means there is an error in one of the plugin setup or configuration files. The output was: '.htmlspecialchars($output);
        do_err($plugin_load_error);
    }
    /**
     * This hook was added in 1.5.2 and 1.4.10. Each plugins should print an error
     * message and return TRUE if there are any errors in its setup/configuration.
     */
    $plugin_err = boolean_hook_function('configtest', NULL, 1);
    if($plugin_err) {
        do_err('Some plugin tests failed.');
    } else {
        echo $IND . "Plugins OK.<br />\n";
    }
} else {
    echo $IND . "Plugins are not enabled in config.<br />\n";
}
foreach($theme as $thm) {
    if(!file_exists($thm['PATH'])) {
        do_err('You have enabled the <i>'.$thm['NAME'].'</i> theme but I cannot find it ('.$thm['PATH'].').', FALSE);
    } elseif(!is_readable($thm['PATH'])) {
        do_err('You have enabled the <i>'.$thm['NAME'].'</i> theme but I cannot read it ('.$thm['PATH'].').', FALSE);
    }
}

echo $IND . "Themes OK.<br />\n";

if ( $squirrelmail_default_language != 'en_US' ) {
    $loc_path = SM_PATH .'locale/'.$squirrelmail_default_language.'/LC_MESSAGES/squirrelmail.mo';
    if( ! file_exists( $loc_path ) ) {
        do_err('You have set <i>' . $squirrelmail_default_language .
                '</i> as your default language, but I cannot find this translation (should be '.
                'in <tt>' . $loc_path . '</tt>). Please note that you have to download translations '.
                'separately from the main SquirrelMail package.', FALSE);
    } elseif ( ! is_readable( $loc_path ) ) {
        do_err('You have set <i>' . $squirrelmail_default_language .
                '</i> as your default language, but I cannot read this translation (file '.
                'in <tt>' . $loc_path . '</tt> unreadable).', FALSE);
    } else {
        echo $IND . "Default language OK.<br />\n";
    }
} else {
    echo $IND . "Default language OK.<br />\n";
}

echo $IND . "Base URL detected as: <tt>" . htmlspecialchars($test_location) .
    "</tt> (location base " . (empty($config_location_base) ? 'autodetected' : 'set to <tt>' .
    htmlspecialchars($config_location_base)."</tt>") . ")<br />\n";

/* check outgoing mail */

if($use_smtp_tls || $use_imap_tls) {
    if(!check_php_version(4,3,0)) {
        do_err('You need at least PHP 4.3.0 for SMTP/IMAP TLS!');
    }
    if(!extension_loaded('openssl')) {
        do_err('You need the openssl PHP extension to use SMTP/IMAP TLS!');
    }
}

echo "Checking outgoing mail service....<br />\n";

if($useSendmail) {
    // is_executable also checks for existance, but we want to be as precise as possible with the errors
    if(!file_exists($sendmail_path)) {
        do_err("Location of sendmail program incorrect ($sendmail_path)!");
    }
    if(!is_executable($sendmail_path)) {
        do_err("I cannot execute the sendmail program ($sendmail_path)!");
    }

    echo $IND . "sendmail OK<br />\n";
} else {
    $stream = fsockopen( ($use_smtp_tls?'tls://':'').$smtpServerAddress, $smtpPort,
            $errorNumber, $errorString);
    if(!$stream) {
        do_err("Error connecting to SMTP server \"$smtpServerAddress:$smtpPort\".".
                "Server error: ($errorNumber) ".htmlspecialchars($errorString));
    }

    // check for SMTP code; should be 2xx to allow us access
    $smtpline = fgets($stream, 1024);
    if(((int) $smtpline{0}) > 3) {
        do_err("Error connecting to SMTP server. Server error: ".
                htmlspecialchars($smtpline));
    }

    fputs($stream, 'QUIT');
    fclose($stream);
    echo $IND . 'SMTP server OK (<tt><small>'.
            trim(htmlspecialchars($smtpline))."</small></tt>)<br />\n";

    /* POP before SMTP */
    if($pop_before_smtp) {
        if (empty($pop_before_smtp_host)) $pop_before_smtp_host = $smtpServerAddress;
        $stream = fsockopen($pop_before_smtp_host, 110, $err_no, $err_str);
        if (!$stream) {
            do_err("Error connecting to POP Server ($pop_before_smtp_host:110) "
                . $err_no . ' : ' . htmlspecialchars($err_str));
        }

        $tmp = fgets($stream, 1024);
        if (substr($tmp, 0, 3) != '+OK') {
            do_err("Error connecting to POP Server ($pop_before_smtp_host:110)"
                . ' '.htmlspecialchars($tmp));
        }
        fputs($stream, 'QUIT');
        fclose($stream);
        echo $IND . "POP-before-SMTP OK.<br />\n";
    }
}

/**
 * Check the IMAP server
 */
echo "Checking IMAP service....<br />\n";

/** Can we open a connection? */
$stream = fsockopen( ($use_imap_tls?'tls://':'').$imapServerAddress, $imapPort,
        $errorNumber, $errorString);
if(!$stream) {
    do_err("Error connecting to IMAP server \"$imapServerAddress:$imapPort\".".
            "Server error: ($errorNumber) ".
            htmlspecialchars($errorString));
}

/** Is the first response 'OK'? */
$imapline = fgets($stream, 1024);
if(substr($imapline, 0,4) != '* OK') {
    do_err('Error connecting to IMAP server. Server error: '.
            htmlspecialchars($imapline));
}

echo $IND . 'IMAP server ready (<tt><small>'.
    htmlspecialchars(trim($imapline))."</small></tt>)<br />\n";

/** Check capabilities */
fputs($stream, "A001 CAPABILITY\r\n");
$capline = fgets($stream, 1024);

echo $IND . 'Capabilities: <tt>'.htmlspecialchars($capline)."</tt><br />\n";

if($imap_auth_mech == 'login' && stristr($capline, 'LOGINDISABLED') !== FALSE) {
    do_err('Your server doesn\'t allow plaintext logins. '.
            'Try enabling another authentication mechanism like CRAM-MD5, DIGEST-MD5 or TLS-encryption '.
            'in the SquirrelMail configuration.', FALSE);
}

if (stristr($capline, 'XMAGICTRASH') !== false) {
    $magic_trash = 'It looks like IMAP_MOVE_EXPUNGE_TO_TRASH option is turned on '
        .'in your Courier IMAP configuration. Courier does not provide tools that '
        .'allow to detect folder used for Trash or commands are not documented. '
        .'SquirrelMail can\'t detect special trash folder. SquirrelMail manages '
        .'all message deletion or move operations internally and '
        .'IMAP_MOVE_EXPUNGE_TO_TRASH option can cause errors in message and '
        .'folder management operations. Please turn off IMAP_MOVE_EXPUNGE_TO_TRASH '
        .'option in Courier imapd configuration.';
    do_err($magic_trash,false);
}

/* add warning about IMAP delivery */
if (stristr($capline, 'XCOURIEROUTBOX') !== false) {
    $courier_outbox = 'OUTBOX setting is enabled in your Courier imapd '
        .'configuration. SquirrelMail uses standard SMTP protocol or sendmail '
        .'binary to send emails. Courier IMAP delivery method is not supported'
        .' and can create duplicate email messages.';
    do_err($courier_outbox,false);
}

/** OK, close connection */
fputs($stream, "A002 LOGOUT\r\n");
fclose($stream);

echo "Checking internationalization (i18n) settings...<br />\n";
echo "$IND gettext - ";
if (function_exists('gettext')) {
    echo 'Gettext functions are available.'
        .' On some systems you must have appropriate system locales compiled.'
        ."<br />\n";
} else {
    echo 'Gettext functions are unavailable.'
        .' SquirrelMail will use slower internal gettext functions.'
        ."<br />\n";
}
echo "$IND mbstring - ";
if (function_exists('mb_detect_encoding')) {
    echo "Mbstring functions are available.<br />\n";
} else {
    echo 'Mbstring functions are unavailable.'
        ." Japanese translation won't work.<br />\n";
}
echo "$IND recode - ";
if (function_exists('recode')) {
    echo "Recode functions are available.<br />\n";
} elseif (isset($use_php_recode) && $use_php_recode) {
    echo "Recode functions are unavailable.<br />\n";
    do_err('Your configuration requires recode support, but recode support is missing.');
} else {
    echo "Recode functions are unavailable.<br />\n";
}
echo "$IND iconv - ";
if (function_exists('iconv')) {
    echo "Iconv functions are available.<br />\n";
} elseif (isset($use_php_iconv) && $use_php_iconv) {
    echo "Iconv functions are unavailable.<br />\n";
    do_err('Your configuration requires iconv support, but iconv support is missing.');
} else {
    echo "Iconv functions are unavailable.<br />\n";
}
// same test as in include/validate.php
echo "$IND timezone - ";
if ( (!ini_get('safe_mode')) ||
    !strcmp(ini_get('safe_mode_allowed_env_vars'),'') ||
    preg_match('/^([\w_]+,)*TZ/', ini_get('safe_mode_allowed_env_vars')) ) {
        echo "Webmail users can change their time zone settings.<br />\n";
} else {
    echo "Webmail users can't change their time zone settings.<br />\n";
}

// Pear DB tests
echo "Checking database functions...<br />\n";
if(!empty($addrbook_dsn) || !empty($prefs_dsn) || !empty($addrbook_global_dsn)) {
    @include_once('DB.php');
    if (class_exists('DB')) {
        echo "$IND PHP Pear DB support is present.<br />\n";
        $db_functions=array(
                'dbase' => 'dbase_open',
                'fbsql' => 'fbsql_connect',
                'interbase' => 'ibase_connect',
                'informix' => 'ifx_connect',
                'msql' => 'msql_connect',
                'mssql' => 'mssql_connect',
                'mysql' => 'mysql_connect',
                'mysqli' => 'mysqli_connect',
                'oci8' => 'ocilogon',
                'odbc' => 'odbc_connect',
                'pgsql' => 'pg_connect',
                'sqlite' => 'sqlite_open',
                'sybase' => 'sybase_connect'
                );

        $dsns = array();
        if($prefs_dsn) {
            $dsns['preferences'] = $prefs_dsn;
        }
        if($addrbook_dsn) {
            $dsns['addressbook'] = $addrbook_dsn;
        }
        if($addrbook_global_dsn) {
            $dsns['global addressbook'] = $addrbook_global_dsn;
        }

        foreach($dsns as $type => $dsn) {
            $aDsn = explode(':', $dsn);
            $dbtype = array_shift($aDsn);

            if(isset($db_functions[$dbtype]) && function_exists($db_functions[$dbtype])) {
                echo "$IND$dbtype database support present.<br />\n";
            } elseif(!(bool)ini_get('enable_dl') || (bool)ini_get('safe_mode')) {
                do_err($dbtype.' database support not present!');
            } else {
                // Non-fatal error
                do_err($dbtype.' database support not present or not configured!
                    Trying to dynamically load '.$dbtype.' extension.
                    Please note that it is advisable to not rely on dynamic loading of extensions.', FALSE);
            }


            // now, test this interface:

            $dbh = DB::connect($dsn, true);
            if (DB::isError($dbh)) {
                do_err('Database error: '. htmlspecialchars(DB::errorMessage($dbh)) .
                        ' in ' .$type .' DSN.');
            }
            $dbh->disconnect();
            echo "$IND$type database connect successful.<br />\n";
        }
    } else {
        $db_error='Required PHP PEAR DB support is not available.'
            .' Is PEAR installed and is the include path set correctly to find <tt>DB.php</tt>?'
            .' The include path is now: "<tt>' . ini_get('include_path') . '</tt>".';
        do_err($db_error);
    }
} else {
    echo $IND."not using database functionality.<br />\n";
}
?>

<p>Congratulations, your SquirrelMail setup looks fine to me!</p>

<p><a href="login.php">Login now</a></p>

</body>
</html>
