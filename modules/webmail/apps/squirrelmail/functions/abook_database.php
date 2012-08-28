<?php

/**
 * abook_database.php
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: abook_database.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage addressbook
 */

/** Needs the DB functions */
if (!include_once('DB.php')) {
    // same error also in db_prefs.php
    require_once(SM_PATH . 'functions/display_messages.php');
    $error  = _("Could not include PEAR database functions required for the database backend.") . "<br />\n";
    $error .= sprintf(_("Is PEAR installed, and is the include path set correctly to find %s?"),
                        '<tt>DB.php</tt>') . "<br />\n";
    $error .= _("Please contact your system administrator and report this error.");
    error_box($error, $color);
    exit;
}

/**
 * Address book in a database backend
 *
 * Backend for personal/shared address book stored in a database,
 * accessed using the DB-classes in PEAR.
 *
 * IMPORTANT:  The PEAR modules must be in the include path
 * for this class to work.
 *
 * An array with the following elements must be passed to
 * the class constructor (elements marked ? are optional):
 * <pre>
 *   dsn       => database DNS (see PEAR for syntax)
 *   table     => table to store addresses in (must exist)
 *   owner     => current user (owner of address data)
 * ? name      => name of address book
 * ? writeable => set writeable flag (true/false)
 * ? listing   => enable/disable listing
 * </pre>
 * The table used should have the following columns:
 * owner, nickname, firstname, lastname, email, label
 * The pair (owner,nickname) should be unique (primary key).
 *
 *  NOTE. This class should not be used directly. Use the
 *        "AddressBook" class instead.
 * @package squirrelmail
 * @subpackage addressbook
 */
class abook_database extends addressbook_backend {
    /**
     * Backend type
     * @var string
     */
    var $btype = 'local';
    /**
     * Backend name
     * @var string
     */
    var $bname = 'database';

    /**
     * Data Source Name (connection description)
     * @var string
     */
    var $dsn       = '';
    /**
     * Table that stores addresses
     * @var string
     */
    var $table     = '';
    /**
     * Owner name
     *
     * Limits list of database entries visible to end user
     * @var string
     */
    var $owner     = '';
    /**
     * Database Handle
     * @var resource
     */
    var $dbh       = false;
    /**
     * Enable/disable writing into address book
     * @var bool
     */
    var $writeable = true;
    /**
     * Enable/disable address book listing
     * @var bool
     */
    var $listing = true;

    /* ========================== Private ======================= */

    /**
     * Constructor
     * @param array $param address book backend options
     */
    function abook_database($param) {
        $this->sname = _("Personal address book");

        if (is_array($param)) {
            if (empty($param['dsn']) ||
                empty($param['table']) ||
                empty($param['owner'])) {
                return $this->set_error('Invalid parameters');
            }

            $this->dsn   = $param['dsn'];
            $this->table = $param['table'];
            $this->owner = $param['owner'];

            if (!empty($param['name'])) {
               $this->sname = $param['name'];
            }

            if (isset($param['writeable'])) {
               $this->writeable = $param['writeable'];
            }

            if (isset($param['listing'])) {
               $this->listing = $param['listing'];
            }

            $this->open(true);
        }
        else {
            return $this->set_error('Invalid argument to constructor');
        }
    }


    /**
     * Open the database.
     * @param bool $new new connection if it is true
     * @return bool
     */
    function open($new = false) {
        $this->error = '';

        /* Return true is file is open and $new is unset */
        if ($this->dbh && !$new) {
            return true;
        }

        /* Close old file, if any */
        if ($this->dbh) {
            $this->close();
        }

        $dbh = DB::connect($this->dsn, true);

        if (DB::isError($dbh)) {
            return $this->set_error(sprintf(_("Database error: %s"),
                                            DB::errorMessage($dbh)));
        }

        $this->dbh = $dbh;

        /**
         * field names are lowercased.
         * We use unquoted identifiers and they use upper case in Oracle
         */
        $this->dbh->setOption('portability', DB_PORTABILITY_LOWERCASE);

        return true;
    }

    /**
     * Close the file and forget the filehandle
     */
    function close() {
        $this->dbh->disconnect();
        $this->dbh = false;
    }

    /**
     * Determine internal database field name given one of
     * the SquirrelMail SM_ABOOK_FIELD_* constants
     *
     * @param integer $field The SM_ABOOK_FIELD_* contant to look up
     *
     * @return string The desired field name, or the string "ERROR"
     *                if the $field is not understood (the caller
     *                is responsible for handing errors)
     *
     */
    function get_field_name($field) {
        switch ($field) {
            case SM_ABOOK_FIELD_NICKNAME:
                return 'nickname';
            case SM_ABOOK_FIELD_FIRSTNAME:
                return 'firstname';
            case SM_ABOOK_FIELD_LASTNAME:
                return 'lastname';
            case SM_ABOOK_FIELD_EMAIL:
                return 'email';
            case SM_ABOOK_FIELD_LABEL:
                return 'label';
            default:
                return 'ERROR';
        }
    }

    /* ========================== Public ======================== */

    /**
     * Search the database
     * @param string $expr search expression
     * @return array search results
     */
    function search($expr) {
        $ret = array();
        if(!$this->open()) {
            return false;
        }

        /* To be replaced by advanded search expression parsing */
        if (is_array($expr)) {
            return;
        }

        // don't allow wide search when listing is disabled.
        if ($expr=='*' && ! $this->listing) {
            return array();
        }

        /* lowercase expression in order to make it case insensitive */
        $expr = strtolower($expr);

        /* escape SQL wildcards */
        $expr = str_replace('_', '\\_', $expr);
        $expr = str_replace('%', '\\%', $expr);

        /* Convert wildcards to SQL syntax  */
        $expr = str_replace('?', '_', $expr);
        $expr = str_replace('*', '%', $expr);
        $expr = $this->dbh->quoteString($expr);
        $expr = "%$expr%";

        /* create escape expression */
        $escape = 'ESCAPE \'' . $this->dbh->quoteString('\\') . '\'';

        $query = sprintf("SELECT * FROM %s WHERE owner='%s' AND " .
                         "(LOWER(firstname) LIKE '%s' %s OR LOWER(lastname) LIKE '%s' %s)",
                         $this->table, $this->owner, $expr, $escape, $expr, $escape);
        $res = $this->dbh->query($query);

        if (DB::isError($res)) {
            return $this->set_error(sprintf(_("Database error: %s"),
                                            DB::errorMessage($res)));
        }

        while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            array_push($ret, array('nickname'  => $row['nickname'],
                                   'name'      => "$row[firstname] $row[lastname]",
                                   'firstname' => $row['firstname'],
                                   'lastname'  => $row['lastname'],
                                   'email'     => $row['email'],
                                   'label'     => $row['label'],
                                   'backend'   => $this->bnum,
                                   'source'    => &$this->sname));
        }
        return $ret;
    }

    /**
     * Lookup by the indicated field
     *
     * @param string  $value Value to look up
     * @param integer $field The field to look in, should be one
     *                       of the SM_ABOOK_FIELD_* constants
     *                       defined in functions/constants.php
     *                       (OPTIONAL; defaults to nickname field)
     *                       NOTE: uniqueness is only guaranteed
     *                       when the nickname field is used here;
     *                       otherwise, the first matching address
     *                       is returned.
     *
     * @return array search results
     *
     */
    function lookup($value, $field=SM_ABOOK_FIELD_NICKNAME) {
        if (empty($value)) {
            return array();
        }

        $value = strtolower($value);

        if (!$this->open()) {
            return false;
        }

        $query = sprintf("SELECT * FROM %s WHERE owner = '%s' AND LOWER(%s) = '%s'",
                         $this->table, $this->owner, $this->get_field_name($field), 
                         $this->dbh->quoteString($value));

        $res = $this->dbh->query($query);

        if (DB::isError($res)) {
            return $this->set_error(sprintf(_("Database error: %s"),
                                            DB::errorMessage($res)));
        }

        if ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            return array('nickname'  => $row['nickname'],
                         'name'      => "$row[firstname] $row[lastname]",
                         'firstname' => $row['firstname'],
                         'lastname'  => $row['lastname'],
                         'email'     => $row['email'],
                         'label'     => $row['label'],
                         'backend'   => $this->bnum,
                         'source'    => &$this->sname);
        }
        return array();
    }

    /**
     * List all addresses
     * @return array search results
     */
    function list_addr() {
        $ret = array();
        if (!$this->open()) {
            return false;
        }

        if(isset($this->listing) && !$this->listing) {
            return array();
        }


        $query = sprintf("SELECT * FROM %s WHERE owner='%s'",
                         $this->table, $this->owner);

        $res = $this->dbh->query($query);

        if (DB::isError($res)) {
            return $this->set_error(sprintf(_("Database error: %s"),
                                            DB::errorMessage($res)));
        }

        while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            array_push($ret, array('nickname'  => $row['nickname'],
                                   'name'      => "$row[firstname] $row[lastname]",
                                   'firstname' => $row['firstname'],
                                   'lastname'  => $row['lastname'],
                                   'email'     => $row['email'],
                                   'label'     => $row['label'],
                                   'backend'   => $this->bnum,
                                   'source'    => &$this->sname));
        }
        return $ret;
    }

    /**
     * Add address
     * @param array $userdata added data
     * @return bool
     */
    function add($userdata) {
        if (!$this->writeable) {
            return $this->set_error(_("Address book is read-only"));
        }

        if (!$this->open()) {
            return false;
        }

        /* See if user exist already */
        $ret = $this->lookup($userdata['nickname']);
        if (!empty($ret)) {
            return $this->set_error(sprintf(_("User \"%s\" already exists"), $ret['nickname']));
        }

        /* Create query */
        $query = sprintf("INSERT INTO %s (owner, nickname, firstname, " .
                         "lastname, email, label) VALUES('%s','%s','%s'," .
                         "'%s','%s','%s')",
                         $this->table, $this->owner,
                         $this->dbh->quoteString($userdata['nickname']),
                         $this->dbh->quoteString($userdata['firstname']),
                         $this->dbh->quoteString((!empty($userdata['lastname'])?$userdata['lastname']:'')),
                         $this->dbh->quoteString($userdata['email']),
                         $this->dbh->quoteString((!empty($userdata['label'])?$userdata['label']:'')) );

         /* Do the insert */
         $r = $this->dbh->simpleQuery($query);

         /* Check for errors */
         if (DB::isError($r)) {
             return $this->set_error(sprintf(_("Database error: %s"),
                                             DB::errorMessage($r)));
         }

         return true;
    }

    /**
     * Delete address
     * @param string $alias alias that has to be deleted
     * @return bool
     */
    function remove($alias) {
        if (!$this->writeable) {
            return $this->set_error(_("Address book is read-only"));
        }

        if (!$this->open()) {
            return false;
        }

        /* Create query */
        $query = sprintf("DELETE FROM %s WHERE owner='%s' AND (",
                         $this->table, $this->owner);

        $sepstr = '';
        while (list($undef, $nickname) = each($alias)) {
            $query .= sprintf("%s nickname='%s' ", $sepstr,
                              $this->dbh->quoteString($nickname));
            $sepstr = 'OR';
        }
        $query .= ')';

        /* Delete entry */
        $r = $this->dbh->simpleQuery($query);

        /* Check for errors */
        if (DB::isError($r)) {
            return $this->set_error(sprintf(_("Database error: %s"),
                                            DB::errorMessage($r)));
        }
        return true;
    }

    /**
     * Modify address
     * @param string $alias modified alias
     * @param array $userdata new data
     * @return bool
     */
    function modify($alias, $userdata) {
        if (!$this->writeable) {
            return $this->set_error(_("Address book is read-only"));
        }

        if (!$this->open()) {
            return false;
        }

         /* See if user exist */
        $ret = $this->lookup($alias);
        if (empty($ret)) {
            return $this->set_error(sprintf(_("User \"%s\" does not exist"), $alias));
        }

        /* make sure that new nickname is not used */
        if (strtolower($alias) != strtolower($userdata['nickname'])) {
            /* same check as in add() */
            $ret = $this->lookup($userdata['nickname']);
            if (!empty($ret)) {
                $error = sprintf(_("User '%s' already exist."), $ret['nickname']);
                return $this->set_error($error);
            }
        }

        /* Create query */
        $query = sprintf("UPDATE %s SET nickname='%s', firstname='%s', ".
                         "lastname='%s', email='%s', label='%s' ".
                         "WHERE owner='%s' AND nickname='%s'",
                         $this->table,
                         $this->dbh->quoteString($userdata['nickname']),
                         $this->dbh->quoteString($userdata['firstname']),
                         $this->dbh->quoteString((!empty($userdata['lastname'])?$userdata['lastname']:'')),
                         $this->dbh->quoteString($userdata['email']),
                         $this->dbh->quoteString((!empty($userdata['label'])?$userdata['label']:'')),
                         $this->owner,
                         $this->dbh->quoteString($alias) );

        /* Do the insert */
        $r = $this->dbh->simpleQuery($query);

        /* Check for errors */
        if (DB::isError($r)) {
            return $this->set_error(sprintf(_("Database error: %s"),
                                            DB::errorMessage($r)));
        }
        return true;
    }
} /* End of class abook_database */

// vim: et ts=4
