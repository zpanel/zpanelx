<?php

/**
 * db_prefs.php
 *
 * This contains functions for manipulating user preferences
 * stored in a database, accessed though the Pear DB layer.
 *
 * Database:
 *
 * The preferences table should have three columns:
 *    user       char  \  primary
 *    prefkey    char  /  key
 *    prefval    blob
 *
 *   CREATE TABLE userprefs (user CHAR(128) NOT NULL DEFAULT '',
 *                           prefkey CHAR(64) NOT NULL DEFAULT '',
 *                           prefval BLOB NOT NULL DEFAULT '',
 *                           primary key (user,prefkey));
 *
 * Configuration of databasename, username and password is done
 * by using conf.pl or the administrator plugin
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: db_prefs.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage prefs
 * @since 1.1.3
 */

/** Unknown database */
define('SMDB_UNKNOWN', 0);
/** MySQL */
define('SMDB_MYSQL', 1);
/** PostgreSQL */
define('SMDB_PGSQL', 2);

if (!include_once('DB.php')) {
    // same error also in abook_database.php
    require_once(SM_PATH . 'functions/display_messages.php');
    $error  = _("Could not include PEAR database functions required for the database backend.") . "<br />\n";
    $error .= sprintf(_("Is PEAR installed, and is the include path set correctly to find %s?"),
                        '<tt>DB.php</tt>') . "<br />\n";
    $error .= _("Please contact your system administrator and report this error.");
    error_box($error, $color);
    exit;
}

global $prefs_are_cached, $prefs_cache;

/**
 * @ignore
 */
function cachePrefValues($username) {
    global $prefs_are_cached, $prefs_cache;

    sqgetGlobalVar('prefs_are_cached', $prefs_are_cached, SQ_SESSION );
    if ($prefs_are_cached) {
        sqgetGlobalVar('prefs_cache', $prefs_cache, SQ_SESSION );
        return;
    }

    sqsession_unregister('prefs_cache');
    sqsession_unregister('prefs_are_cached');

    $db = new dbPrefs;
    if(isset($db->error)) {
        printf( _("Preference database error (%s). Exiting abnormally"),
              $db->error);
        exit;
    }

    $db->fillPrefsCache($username);
    if (isset($db->error)) {
        printf( _("Preference database error (%s). Exiting abnormally"),
              $db->error);
        exit;
    }

    $prefs_are_cached = true;

    sqsession_register($prefs_cache, 'prefs_cache');
    sqsession_register($prefs_are_cached, 'prefs_are_cached');
}

/**
 * Completely undocumented class - someone document it!
 * @package squirrelmail
 */
class dbPrefs {
    var $table = 'userprefs';
    var $user_field = 'user';
    var $key_field = 'prefkey';
    var $val_field = 'prefval';

    var $dbh   = NULL;
    var $error = NULL;
    var $db_type = SMDB_UNKNOWN;

    var $default = Array('theme_default' => 0,
                         'show_html_default' => '0');

    function open() {
        global $prefs_dsn, $prefs_table;
        global $prefs_user_field, $prefs_key_field, $prefs_val_field;

        if(isset($this->dbh)) {
            return true;
        }

        if (preg_match('/^mysql/', $prefs_dsn)) {
            $this->db_type = SMDB_MYSQL;
        } elseif (preg_match('/^pgsql/', $prefs_dsn)) {
            $this->db_type = SMDB_PGSQL;
        }

        if (!empty($prefs_table)) {
            $this->table = $prefs_table;
        }
        if (!empty($prefs_user_field)) {
            $this->user_field = $prefs_user_field;
        }

        // the default user field is "user", which in PostgreSQL
        // is an identifier and causes errors if not escaped
        //
        if ($this->db_type == SMDB_PGSQL) {
           $this->user_field = '"' . $this->user_field . '"';
        }

        if (!empty($prefs_key_field)) {
            $this->key_field = $prefs_key_field;
        }
        if (!empty($prefs_val_field)) {
            $this->val_field = $prefs_val_field;
        }
        $dbh = DB::connect($prefs_dsn, true);

        if(DB::isError($dbh)) {
            $this->error = DB::errorMessage($dbh);
            return false;
        }

        $this->dbh = $dbh;
        return true;
    }

    function failQuery($res = NULL) {
        if($res == NULL) {
            printf(_("Preference database error (%s). Exiting abnormally"),
                  $this->error);
        } else {
            printf(_("Preference database error (%s). Exiting abnormally"),
                  DB::errorMessage($res));
        }
        exit;
    }


    function getKey($user, $key, $default = '') {
        global $prefs_cache;

        $result = do_hook_function('get_pref_override', array($user, $key));
//FIXME: testing below for !$result means that a plugin cannot fetch its own pref value of 0, '0', '', FALSE, or anything else that evaluates to boolean FALSE.
        if (!$result) {
            cachePrefValues($user);

            if (isset($prefs_cache[$key])) {
                $result = $prefs_cache[$key];
            } else {
//FIXME: is there justification for having these TWO hooks so close together?  who uses these?
                $result = do_hook_function('get_pref', array($user, $key));
//FIXME: testing below for !$result means that a plugin cannot fetch its own pref value of 0, '0', '', FALSE, or anything else that evaluates to boolean FALSE.
                if (!$result) {
                    if (isset($this->default[$key])) {
                        $result = $this->default[$key];
                    } else {
                        $result = $default;
                    }
                }
            }
        }
        return $result;
    }

    function deleteKey($user, $key) {
        global $prefs_cache;

        if (!$this->open()) {
            return false;
        }
        $query = sprintf("DELETE FROM %s WHERE %s='%s' AND %s='%s'",
                         $this->table,
                         $this->user_field,
                         $this->dbh->quoteString($user),
                         $this->key_field,
                         $this->dbh->quoteString($key));

        $res = $this->dbh->simpleQuery($query);
        if(DB::isError($res)) {
            $this->failQuery($res);
        }

        unset($prefs_cache[$key]);

        return true;
    }

    function setKey($user, $key, $value) {
        if (!$this->open()) {
            return false;
        }
        if ($this->db_type == SMDB_MYSQL) {
            $query = sprintf("REPLACE INTO %s (%s, %s, %s) ".
                             "VALUES('%s','%s','%s')",
                             $this->table,
                             $this->user_field,
                             $this->key_field,
                             $this->val_field,
                             $this->dbh->quoteString($user),
                             $this->dbh->quoteString($key),
                             $this->dbh->quoteString($value));

            $res = $this->dbh->simpleQuery($query);
            if(DB::isError($res)) {
                $this->failQuery($res);
            }
        } elseif ($this->db_type == SMDB_PGSQL) {
            $this->dbh->simpleQuery("BEGIN TRANSACTION");
            $query = sprintf("DELETE FROM %s WHERE %s='%s' AND %s='%s'",
                             $this->table,
                             $this->user_field,
                             $this->dbh->quoteString($user),
                             $this->key_field,
                             $this->dbh->quoteString($key));
            $res = $this->dbh->simpleQuery($query);
            if (DB::isError($res)) {
                $this->dbh->simpleQuery("ROLLBACK TRANSACTION");
                $this->failQuery($res);
            }
            $query = sprintf("INSERT INTO %s (%s, %s, %s) VALUES ('%s', '%s', '%s')",
                             $this->table,
                             $this->user_field,
                             $this->key_field,
                             $this->val_field,
                             $this->dbh->quoteString($user),
                             $this->dbh->quoteString($key),
                             $this->dbh->quoteString($value));
            $res = $this->dbh->simpleQuery($query);
            if (DB::isError($res)) {
                $this->dbh->simpleQuery("ROLLBACK TRANSACTION");
                $this->failQuery($res);
            }
            $this->dbh->simpleQuery("COMMIT TRANSACTION");
        } else {
            $query = sprintf("DELETE FROM %s WHERE %s='%s' AND %s='%s'",
                             $this->table,
                             $this->user_field,
                             $this->dbh->quoteString($user),
                             $this->key_field,
                             $this->dbh->quoteString($key));
            $res = $this->dbh->simpleQuery($query);
            if (DB::isError($res)) {
                $this->failQuery($res);
            }
            $query = sprintf("INSERT INTO %s (%s, %s, %s) VALUES ('%s', '%s', '%s')",
                             $this->table,
                             $this->user_field,
                             $this->key_field,
                             $this->val_field,
                             $this->dbh->quoteString($user),
                             $this->dbh->quoteString($key),
                             $this->dbh->quoteString($value));
            $res = $this->dbh->simpleQuery($query);
            if (DB::isError($res)) {
                $this->failQuery($res);
            }
        }

        return true;
    }

    function fillPrefsCache($user) {
        global $prefs_cache;

        if (!$this->open()) {
            return;
        }

        $prefs_cache = array();
        $query = sprintf("SELECT %s as prefkey, %s as prefval FROM %s ".
                         "WHERE %s = '%s'",
                         $this->key_field,
                         $this->val_field,
                         $this->table,
                         $this->user_field,
                         $this->dbh->quoteString($user));
        $res = $this->dbh->query($query);
        if (DB::isError($res)) {
            $this->failQuery($res);
        }

        while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $prefs_cache[$row['prefkey']] = $row['prefval'];
        }
    }

} /* end class dbPrefs */


/**
 * returns the value for the pref $string
 * @ignore
 */
function getPref($data_dir, $username, $string, $default = '') {
    $db = new dbPrefs;
    if(isset($db->error)) {
        printf( _("Preference database error (%s). Exiting abnormally"),
              $db->error);
        exit;
    }

    return $db->getKey($username, $string, $default);
}

/**
 * Remove the pref $string
 * @ignore
 */
function removePref($data_dir, $username, $string) {
    global $prefs_cache;
    $db = new dbPrefs;
    if(isset($db->error)) {
        $db->failQuery();
    }

    $db->deleteKey($username, $string);

    if (isset($prefs_cache[$string])) {
        unset($prefs_cache[$string]);
    }

    sqsession_register($prefs_cache , 'prefs_cache');
    return;
}

/**
 * sets the pref, $string, to $set_to
 * @ignore
 */
function setPref($data_dir, $username, $string, $set_to) {
    global $prefs_cache;

    if (isset($prefs_cache[$string]) && ($prefs_cache[$string] == $set_to)) {
        return;
    }

    if ($set_to === '') {
        removePref($data_dir, $username, $string);
        return;
    }

    $db = new dbPrefs;
    if(isset($db->error)) {
        $db->failQuery();
    }

    $db->setKey($username, $string, $set_to);
    $prefs_cache[$string] = $set_to;
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_BAIL, 1);
    assert ('$set_to == $prefs_cache[$string]');
    sqsession_register($prefs_cache , 'prefs_cache');
    return;
}

/**
 * This checks if the prefs are available
 * @ignore
 */
function checkForPrefs($data_dir, $username) {
    $db = new dbPrefs;
    if(isset($db->error)) {
        $db->failQuery();
    }
}

/**
 * Writes the Signature
 * @ignore
 */
function setSig($data_dir, $username, $number, $string) {
    if ($number == "g") {
        $key = '___signature___';
    } else {
        $key = sprintf('___sig%s___', $number);
    }
    setPref($data_dir, $username, $key, $string);
    return;
}

/**
 * Gets the signature
 * @ignore
 */
function getSig($data_dir, $username, $number) {
    if ($number == "g") {
        $key = '___signature___';
    } else {
        $key = sprintf('___sig%d___', $number);
    }
    return getPref($data_dir, $username, $key);
}

