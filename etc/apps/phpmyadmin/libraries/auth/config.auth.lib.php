<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Set of functions used to run config authentication (ie no authentication).
 *
 * @package phpMyAdmin-Auth-Config
 */

/**
 * Displays authentication form
 *
 * @return  boolean   always true
 *
 * @access  public
 */
function PMA_auth() {
    return TRUE;
}

// end of the 'PMA_auth()' function

/**
 * Gets advanced authentication settings
 *
 * @return  boolean   always true
 *
 * @access  public
 */
function PMA_auth_check() {
    return TRUE;
}

// end of the 'PMA_auth_check()' function

/**
 * Set the user and password after last checkings if required
 *
 * @return  boolean   always true
 *
 * @access  public
 */
function PMA_auth_set_user() {
    return TRUE;
}

// end of the 'PMA_auth_set_user()' function

/**
 * User is not allowed to login to MySQL -> authentication failed
 *
 * @global  string    the MySQL error message PHP returns
 * @global  string    the connection type (persistent or not)
 * @global  string    the MySQL server port to use
 * @global  string    the MySQL socket port to use
 * @global  array     the current server settings
 * @global  string    the font face to use in case of failure
 * @global  string    the default font size to use in case of failure
 * @global  string    the big font size to use in case of failure
 * @global  boolean   tell the "PMA_mysqlDie()" function headers have been
 *                    sent
 *
 * @return  boolean   always true (no return indeed)
 *
 * @access  public
 */
function PMA_auth_fails() {
    global $php_errormsg, $cfg;

    $conn_error = PMA_DBI_getError();
    if (!$conn_error) {
        if (isset($php_errormsg)) {
            $conn_error = $php_errormsg;
        } else {
            $conn_error = __('Cannot connect: invalid settings.');
        }
    }

    // Defines the charset to be used
    header('Content-Type: text/html; charset=' . $GLOBALS['charset']);
    /* HTML header */
    $page_title = __('Access denied');
    require './libraries/header_meta_style.inc.php';
    ?>
    </head>

    <body>
        <br /><br />
    <center>
        <h1><?php echo sprintf(__('Welcome to %s'), ' phpMyAdmin '); ?></h1>
    </center>
    <br />
    <table border="0" cellpadding="0" cellspacing="3" align="center" width="80%">
        <tr>
            <td>

    <?php
    $GLOBALS['is_header_sent'] = TRUE;

    if (isset($GLOBALS['allowDeny_forbidden']) && $GLOBALS['allowDeny_forbidden']) {
        trigger_error(__('Access denied'), E_USER_NOTICE);
    } else {
        // Check whether user has configured something
        if ($GLOBALS['PMA_Config']->source_mtime == 0) {
            echo '<p>' . sprintf(__('You probably did not create a configuration file. You might want to use the %1$ssetup script%2$s to create one.'), '<a href="setup/">', '</a>') . '</p>' . "\n";
        } elseif (!isset($GLOBALS['errno']) || (isset($GLOBALS['errno']) && $GLOBALS['errno'] != 2002) && $GLOBALS['errno'] != 2003) {
            // if we display the "Server not responding" error, do not confuse users
            // by telling them they have a settings problem
            // (note: it's true that they could have a badly typed host name, but
            //  anyway the current message tells that the server
            //  rejected the connection, which is not really what happened)
            // 2002 is the error given by mysqli
            // 2003 is the error given by mysql
            trigger_error(__('phpMyAdmin tried to connect to the MySQL server, and the server rejected the connection. You should check the host, username and password in your configuration and make sure that they correspond to the information given by the administrator of the MySQL server.'), E_USER_WARNING);
        }
        PMA_mysqlDie($conn_error, '', true, '', false);
    }
    $GLOBALS['error_handler']->dispUserErrors();
    ?>
            </td>
        </tr>
    <?php
    if (count($GLOBALS['cfg']['Servers']) > 1) {
        // offer a chance to login to other servers if the current one failed
        require_once './libraries/select_server.lib.php';
        echo '<tr>' . "\n";
        echo ' <td>' . "\n";
        PMA_select_server(TRUE, TRUE);
        echo ' </td>' . "\n";
        echo '</tr>' . "\n";
    }
    echo '</table>' . "\n";
    require './libraries/footer.inc.php';
    return TRUE;
}

// end of the 'PMA_auth_fails()' function
?>
