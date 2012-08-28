<?php
// vim: set et ts=4 sw=4 fdm=marker:
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2008 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith, Frank M. Kromann                       |
// | All rights reserved.                                                 |
// +----------------------------------------------------------------------+
// | MDB2 is a merge of PEAR DB and Metabases that provides a unified DB  |
// | API as well as database abstraction for PHP applications.            |
// | This LICENSE is in the BSD license style.                            |
// |                                                                      |
// | Redistribution and use in source and binary forms, with or without   |
// | modification, are permitted provided that the following conditions   |
// | are met:                                                             |
// |                                                                      |
// | Redistributions of source code must retain the above copyright       |
// | notice, this list of conditions and the following disclaimer.        |
// |                                                                      |
// | Redistributions in binary form must reproduce the above copyright    |
// | notice, this list of conditions and the following disclaimer in the  |
// | documentation and/or other materials provided with the distribution. |
// |                                                                      |
// | Neither the name of Manuel Lemos, Tomas V.V.Cox, Stig. S. Bakken,    |
// | Lukas Smith nor the names of his contributors may be used to endorse |
// | or promote products derived from this software without specific prior|
// | written permission.                                                  |
// |                                                                      |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
// | FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE      |
// | REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
// | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS|
// |  OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED  |
// | AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT          |
// | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY|
// | WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE          |
// | POSSIBILITY OF SUCH DAMAGE.                                          |
// +----------------------------------------------------------------------+
// | Author: Frank M. Kromann <frank@kromann.info>                        |
// +----------------------------------------------------------------------+
//
// $Id: mssql.php 295587 2010-02-28 17:16:38Z quipo $
//
// {{{ Class MDB2_Driver_mssql
/**
 * MDB2 MSSQL Server driver
 *
 * @package MDB2
 * @category Database
 * @author  Frank M. Kromann <frank@kromann.info>
 */
class MDB2_Driver_mssql extends MDB2_Driver_Common
{
    // {{{ properties

    var $string_quoting = array('start' => "'", 'end' => "'", 'escape' => "'", 'escape_pattern' => false);

    var $identifier_quoting = array('start' => '[', 'end' => ']', 'escape' => ']');

    // }}}
    // {{{ constructor

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->phptype = 'mssql';
        $this->dbsyntax = 'mssql';

        $this->supported['sequences'] = 'emulated';
        $this->supported['indexes'] = true;
        $this->supported['affected_rows'] = true;
        $this->supported['transactions'] = true;
        $this->supported['savepoints'] = false;
        $this->supported['summary_functions'] = true;
        $this->supported['order_by_text'] = true;
        $this->supported['current_id'] = 'emulated';
        $this->supported['limit_queries'] = 'emulated';
        $this->supported['LOBs'] = true;
        $this->supported['replace'] = 'emulated';
        $this->supported['sub_selects'] = true;
        $this->supported['triggers'] = true;
        $this->supported['auto_increment'] = true;
        $this->supported['primary_key'] = true;
        $this->supported['result_introspection'] = true;
        $this->supported['prepared_statements'] = 'emulated';
        $this->supported['pattern_escaping'] = true;
        $this->supported['new_link'] = true;

        $this->options['DBA_username'] = false;
        $this->options['DBA_password'] = false;
        $this->options['database_device'] = false;
        $this->options['database_size'] = false;
        $this->options['max_identifiers_length'] = 128; // MS Access: 64
    }

    // }}}
    // {{{ errorInfo()

    /**
     * This method is used to collect information about an error
     *
     * @param integer $error
     * @return array
     * @access public
     */
    function errorInfo($error = null, $connection = null)
    {
        if (null === $connection) {
            $connection = $this->connection;
        }

        $native_code = null;
        if ($connection) {
            $result = @mssql_query('select @@ERROR as ErrorCode', $connection);
            if ($result) {
                $native_code = @mssql_result($result, 0, 0);
                @mssql_free_result($result);
            }
        }
        $native_msg = @mssql_get_last_message();
        if (null === $error) {
            static $ecode_map;
            if (empty($ecode_map)) {
                $ecode_map = array(
                    102   => MDB2_ERROR_SYNTAX,
                    110   => MDB2_ERROR_VALUE_COUNT_ON_ROW,
                    155   => MDB2_ERROR_NOSUCHFIELD,
                    156   => MDB2_ERROR_SYNTAX,
                    170   => MDB2_ERROR_SYNTAX,
                    207   => MDB2_ERROR_NOSUCHFIELD,
                    208   => MDB2_ERROR_NOSUCHTABLE,
                    245   => MDB2_ERROR_INVALID_NUMBER,
                    319   => MDB2_ERROR_SYNTAX,
                    321   => MDB2_ERROR_NOSUCHFIELD,
                    325   => MDB2_ERROR_SYNTAX,
                    336   => MDB2_ERROR_SYNTAX,
                    515   => MDB2_ERROR_CONSTRAINT_NOT_NULL,
                    547   => MDB2_ERROR_CONSTRAINT,
                    911   => MDB2_ERROR_NOT_FOUND,
                    1018  => MDB2_ERROR_SYNTAX,
                    1035  => MDB2_ERROR_SYNTAX,
                    1801  => MDB2_ERROR_ALREADY_EXISTS,
                    1913  => MDB2_ERROR_ALREADY_EXISTS,
                    2209  => MDB2_ERROR_SYNTAX,
                    2223  => MDB2_ERROR_SYNTAX,
                    2248  => MDB2_ERROR_SYNTAX,
                    2256  => MDB2_ERROR_SYNTAX,
                    2257  => MDB2_ERROR_SYNTAX,
                    2627  => MDB2_ERROR_CONSTRAINT,
                    2714  => MDB2_ERROR_ALREADY_EXISTS,
                    3607  => MDB2_ERROR_DIVZERO,
                    3701  => MDB2_ERROR_NOSUCHTABLE,
                    7630  => MDB2_ERROR_SYNTAX,
                    8134  => MDB2_ERROR_DIVZERO,
                    9303  => MDB2_ERROR_SYNTAX,
                    9317  => MDB2_ERROR_SYNTAX,
                    9318  => MDB2_ERROR_SYNTAX,
                    9331  => MDB2_ERROR_SYNTAX,
                    9332  => MDB2_ERROR_SYNTAX,
                    15253 => MDB2_ERROR_SYNTAX,
                );
            }
            if (isset($ecode_map[$native_code])) {
                if ($native_code == 3701
                    && preg_match('/Cannot drop the index/i', $native_msg)
                ) {
                   $error = MDB2_ERROR_NOT_FOUND;
                } else {
                    $error = $ecode_map[$native_code];
                }
            }
        }
        return array($error, $native_code, $native_msg);
    }

    // }}}
    // {{{ function escapePattern($text)

    /**
     * Quotes pattern (% and _) characters in a string)
     *
     * @param   string  the input string to quote
     *
     * @return  string  quoted string
     *
     * @access  public
     */
    function escapePattern($text)
    {
        $text = str_replace("[", "[ [ ]", $text);
        foreach ($this->wildcards as $wildcard) {
            $text = str_replace($wildcard, '[' . $wildcard . ']', $text);
        }
        return $text;
    }

    // }}}
    // {{{ escape()

    /**
     * Quotes a string so it can be safely used in a query. It will quote
     * the text so it can safely be used within a query.
     *
     * @param string $text             the input string to quote
     * @param bool   $escape_wildcards flag
     *
     * @return string  quoted string
     * @access public
     */
    function escape($text, $escape_wildcards = false)
    {
        $text = parent::escape($text, $escape_wildcards);
        // http://pear.php.net/bugs/bug.php?id=16118
        // http://support.microsoft.com/kb/164291
        return preg_replace("/\\\\(\r\n|\r|\n)/", '\\\\$1', $text);
    }

    // }}}
    // {{{ beginTransaction()

    /**
     * Start a transaction or set a savepoint.
     *
     * @param string $savepoint name of a savepoint to set
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function beginTransaction($savepoint = null)
    {
        $this->debug('Starting transaction/savepoint', __FUNCTION__, array('is_manip' => true, 'savepoint' => $savepoint));
        if (null !== $savepoint) {
            if (!$this->in_transaction) {
                return $this->raiseError(MDB2_ERROR_INVALID, null, null,
                    'savepoint cannot be released when changes are auto committed', __FUNCTION__);
            }
            $query = 'SAVE TRANSACTION '.$savepoint;
            return $this->_doQuery($query, true);
        }
        if ($this->in_transaction) {
            return MDB2_OK;  //nothing to do
        }
        if (!$this->destructor_registered && $this->opened_persistent) {
            $this->destructor_registered = true;
            register_shutdown_function('MDB2_closeOpenTransactions');
        }
        $result =& $this->_doQuery('BEGIN TRANSACTION', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $this->in_transaction = true;
        return MDB2_OK;
    }

    // }}}
    // {{{ commit()

    /**
     * Commit the database changes done during a transaction that is in
     * progress or release a savepoint. This function may only be called when
     * auto-committing is disabled, otherwise it will fail. Therefore, a new
     * transaction is implicitly started after committing the pending changes.
     *
     * @param string $savepoint name of a savepoint to release
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access  public
     */
    function commit($savepoint = null)
    {
        $this->debug('Committing transaction/savepoint', __FUNCTION__, array('is_manip' => true, 'savepoint' => $savepoint));
        if (!$this->in_transaction) {
            return $this->raiseError(MDB2_ERROR_INVALID, null, null,
                'commit/release savepoint cannot be done changes are auto committed', __FUNCTION__);
        }
        if (null !== $savepoint) {
            return MDB2_OK;
        }

        $result =& $this->_doQuery('COMMIT TRANSACTION', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $this->in_transaction = false;
        return MDB2_OK;
    }

    // }}}
    // {{{ rollback()

    /**
     * Cancel any database changes done during a transaction or since a specific
     * savepoint that is in progress. This function may only be called when
     * auto-committing is disabled, otherwise it will fail. Therefore, a new
     * transaction is implicitly started after canceling the pending changes.
     *
     * @param string $savepoint name of a savepoint to rollback to
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function rollback($savepoint = null)
    {
        $this->debug('Rolling back transaction/savepoint', __FUNCTION__, array('is_manip' => true, 'savepoint' => $savepoint));
        if (!$this->in_transaction) {
            return $this->raiseError(MDB2_ERROR_INVALID, null, null,
                'rollback cannot be done changes are auto committed', __FUNCTION__);
        }
        if (null !== $savepoint) {
            $query = 'ROLLBACK TRANSACTION '.$savepoint;
            return $this->_doQuery($query, true);
        }

        $result =& $this->_doQuery('ROLLBACK TRANSACTION', true);
        if (PEAR::isError($result)) {
            return $result;
        }
        $this->in_transaction = false;
        return MDB2_OK;
    }

    // }}}
    // {{{ _doConnect()

    /**
     * do the grunt work of the connect
     *
     * @param string  $username
     * @param string  $password
     * @param boolean $persistent
     *
     * @return connection on success or MDB2 Error Object on failure
     * @access protected
     */
    function _doConnect($username, $password, $persistent = false)
    {
        if (   !PEAR::loadExtension($this->phptype)
            && !PEAR::loadExtension('sybase_ct')
            && !PEAR::loadExtension('odbtp')
            && !function_exists('mssql_connect')
        ) {
            return $this->raiseError(MDB2_ERROR_NOT_FOUND, null, null,
                'extension '.$this->phptype.' is not compiled into PHP', __FUNCTION__);
        }

        $params = array(
            $this->dsn['hostspec'] ? $this->dsn['hostspec'] : 'localhost',
            $username ? $username : null,
            $password ? $password : null,
            );
        if ($this->dsn['port']) {
            $params[0].= ((substr(PHP_OS, 0, 3) == 'WIN') ? ',' : ':').$this->dsn['port'];
        }
        if (!$persistent) {
            if ($this->_isNewLinkSet()) {
                $params[] = true;
            } else {
                $params[] = false;
            }
        }

        $connect_function = $persistent ? 'mssql_pconnect' : 'mssql_connect';

        $connection = @call_user_func_array($connect_function, $params);
        if ($connection <= 0) {
            return $this->raiseError(MDB2_ERROR_CONNECT_FAILED, null, null,
                'unable to establish a connection', __FUNCTION__, __FUNCTION__);
        }

        @mssql_query('SET ANSI_NULL_DFLT_ON ON', $connection);

        /*
        if (!empty($this->dsn['charset'])) {
            $result = $this->setCharset($this->dsn['charset'], $connection);
            if (PEAR::isError($result)) {
                return $result;
            }
        }
        */

        if ((bool)ini_get('mssql.datetimeconvert')) {
            // his isn't the most elegant way of doing it but it prevents from
            // breaking anything thus preserves BC. Bug #11849
            if (isset($this->options['datetimeconvert']) && (bool)$this->options['datetimeconvert'] !== false) {
                @ini_set('mssql.datetimeconvert', '1');
            } else {
                @ini_set('mssql.datetimeconvert', '0');
            }
        }

        if (empty($this->dsn['disable_iso_date'])) {
            @mssql_query('SET DATEFORMAT ymd', $connection);
        }

        return $connection;
    }

    // }}}
    // {{{ connect()

    /**
     * Connect to the database
     *
     * @return true on success, MDB2 Error Object on failure
     */
    function connect()
    {
        if (is_resource($this->connection)) {
            //if (count(array_diff($this->connected_dsn, $this->dsn)) == 0
            if (MDB2::areEquals($this->connected_dsn, $this->dsn)
                && $this->opened_persistent == $this->options['persistent']
            ) {
                return MDB2_OK;
            }
            $this->disconnect(false);
        }

        $connection = $this->_doConnect(
            $this->dsn['username'],
            $this->dsn['password'],
            $this->options['persistent']
        );
        if (PEAR::isError($connection)) {
            return $connection;
        }

        $this->connection = $connection;
        $this->connected_dsn = $this->dsn;
        $this->connected_database_name = '';
        $this->opened_persistent = $this->options['persistent'];
        $this->dbsyntax = $this->dsn['dbsyntax'] ? $this->dsn['dbsyntax'] : $this->phptype;

        if ($this->database_name) {
            if ($this->database_name != $this->connected_database_name) {
                if (!@mssql_select_db($this->database_name, $connection)) {
                    $err = $this->raiseError(null, null, null,
                        'Could not select the database: '.$this->database_name, __FUNCTION__);
                    return $err;
                }
                $this->connected_database_name = $this->database_name;
            }
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ databaseExists()

    /**
     * check if given database name is exists?
     *
     * @param string $name    name of the database that should be checked
     *
     * @return mixed true/false on success, a MDB2 error on failure
     * @access public
     */
    function databaseExists($name)
    {
        $connection = $this->_doConnect($this->dsn['username'],
                                        $this->dsn['password'],
                                        $this->options['persistent']);
        if (PEAR::isError($connection)) {
            return $connection;
        }

        $result = @mssql_select_db($name, $connection);
        $errorInfo = $this->errorInfo(null, $connection);
        @mssql_close($connection);
        if (!$result) {
            if ($errorInfo[0] != MDB2_ERROR_NOT_FOUND) {
            exit;
                $result = $this->raiseError($errorInfo[0], null, null, $errorInfo[2], __FUNCTION__);
                return $result;
            }
            $result = false;
        }

        return $result;
    }

    // }}}
    // {{{ disconnect()

    /**
     * Log out and disconnect from the database.
     *
     * @param  boolean $force if the disconnect should be forced even if the
     *                        connection is opened persistently
     * @return mixed true on success, false if not connected and error
     *                object on error
     * @access public
     */
    function disconnect($force = true)
    {
        if (is_resource($this->connection)) {
            if ($this->in_transaction) {
                $dsn = $this->dsn;
                $database_name = $this->database_name;
                $persistent = $this->options['persistent'];
                $this->dsn = $this->connected_dsn;
                $this->database_name = $this->connected_database_name;
                $this->options['persistent'] = $this->opened_persistent;
                $this->rollback();
                $this->dsn = $dsn;
                $this->database_name = $database_name;
                $this->options['persistent'] = $persistent;
            }

            if (!$this->opened_persistent || $force) {
                $ok = @mssql_close($this->connection);
                if (!$ok) {
                    return $this->raiseError(MDB2_ERROR_DISCONNECT_FAILED,
                           null, null, null, __FUNCTION__);
                }
            }
        } else {
            return false;
        }
        return parent::disconnect($force);
    }

    // }}}
    // {{{ standaloneQuery()

    /**
     * execute a query as DBA
     *
     * @param string $query the SQL query
     * @param mixed   $types  array that contains the types of the columns in
     *                        the result set
     * @param boolean $is_manip  if the query is a manipulation query
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function standaloneQuery($query, $types = null, $is_manip = false)
    {
        $user = $this->options['DBA_username']? $this->options['DBA_username'] : $this->dsn['username'];
        $pass = $this->options['DBA_password']? $this->options['DBA_password'] : $this->dsn['password'];
        $connection = $this->_doConnect($user, $pass, $this->options['persistent']);
        if (PEAR::isError($connection)) {
            return $connection;
        }

        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $query = $this->_modifyQuery($query, $is_manip, $limit, $offset);
        
        $result = $this->_doQuery($query, $is_manip, $connection, $this->database_name);
        if (!PEAR::isError($result)) {
            $result = $this->_affectedRows($connection, $result);
        }

        @mssql_close($connection);
        return $result;
    }

    // }}}
    // {{{ _doQuery()

    /**
     * Execute a query
     * @param string $query  query
     * @param boolean $is_manip  if the query is a manipulation query
     * @param resource $connection
     * @param string $database_name
     * @return result or error object
     * @access protected
     */
    function _doQuery($query, $is_manip = false, $connection = null, $database_name = null)
    {
        $this->last_query = $query;
        $result = $this->debug($query, 'query', array('is_manip' => $is_manip, 'when' => 'pre'));
        if ($result) {
            if (PEAR::isError($result)) {
                return $result;
            }
            $query = $result;
        }
        if ($this->options['disable_query']) {
            $result = $is_manip ? 0 : null;
            return $result;
        }

        if (null === $connection) {
            $connection = $this->getConnection();
            if (PEAR::isError($connection)) {
                return $connection;
            }
        }
        if (null === $database_name) {
            $database_name = $this->database_name;
        }

        if ($database_name) {
            if ($database_name != $this->connected_database_name) {
                if (!@mssql_select_db($database_name, $connection)) {
                    $err = $this->raiseError(null, null, null,
                        'Could not select the database: '.$database_name, __FUNCTION__);
                    return $err;
                }
                $this->connected_database_name = $database_name;
            }
        }

        $result = @mssql_query($query, $connection);
        if (!$result) {
            $err = $this->raiseError(null, null, null,
                'Could not execute statement', __FUNCTION__);
            return $err;
        }

        $this->debug($query, 'query', array('is_manip' => $is_manip, 'when' => 'post', 'result' => $result));
        return $result;
    }

    // }}}
    // {{{ _affectedRows()

    /**
     * Returns the number of rows affected
     *
     * @param resource $result
     * @param resource $connection
     * @return mixed MDB2 Error Object or the number of rows affected
     * @access private
     */
    function _affectedRows($connection, $result = null)
    {
        if (null === $connection) {
            $connection = $this->getConnection();
            if (PEAR::isError($connection)) {
                return $connection;
            }
        }
        return @mssql_rows_affected($connection);
    }

    // }}}
    // {{{ _modifyQuery()

    /**
     * Changes a query string for various DBMS specific reasons
     *
     * @param string $query  query to modify
     * @param boolean $is_manip  if it is a DML query
     * @param integer $limit  limit the number of rows
     * @param integer $offset  start reading from given offset
     * @return string modified query
     * @access protected
     */
    function _modifyQuery($query, $is_manip, $limit, $offset)
    {
        if ($limit > 0) {
            $fetch = $offset + $limit;
            if (!$is_manip) {
                return preg_replace('/^([\s(])*SELECT( DISTINCT)?(?!\s*TOP\s*\()/i',
                    "\\1SELECT\\2 TOP $fetch", $query);
            }
        }
        return $query;
    }

    // }}}
    // {{{ getServerVersion()

    /**
     * return version information about the server
     *
     * @param bool   $native  determines if the raw version string should be returned
     * @return mixed array/string with version information or MDB2 error object
     * @access public
     */
    function getServerVersion($native = false)
    {
        if ($this->connected_server_info) {
            $server_info = $this->connected_server_info;
        } else {
            $query = 'SELECT @@VERSION';
            $server_info = $this->queryOne($query, 'text');
            if (PEAR::isError($server_info)) {
                return $server_info;
            }
        }
        // cache server_info
        $this->connected_server_info = $server_info;
        if (!$native && !PEAR::isError($server_info)) {
            if (preg_match('/(\d+)\.(\d+)\.(\d+)/', $server_info, $tmp)) {
                $server_info = array(
                    'major' => $tmp[1],
                    'minor' => $tmp[2],
                    'patch' => $tmp[3],
                    'extra' => null,
                    'native' => $server_info,
                );
            } else {
                $server_info = array(
                    'major' => null,
                    'minor' => null,
                    'patch' => null,
                    'extra' => null,
                    'native' => $server_info,
                );
            }
        }
        return $server_info;
    }

    // }}}
    // {{{ _checkSequence

    /**
     * Checks if there's a sequence that exists.
     *
     * @param  string $seq_name    The sequence name to verify.
     * @return bool   $tableExists The value if the table exists or not
     * @access private
     */
    function _checkSequence($seq_name)
    {
        $query = "SELECT * FROM $seq_name";
        $tableExists = $this->_doQuery($query, true);
        if (PEAR::isError($tableExists)) {
            if ($tableExists->getCode() == MDB2_ERROR_NOSUCHTABLE) {
                return false;
            }
            //return $tableExists;
            return false;
        }
        return mssql_result($tableExists, 0, 0);
    }

    // }}}
    // {{{ nextID()

    /**
     * Returns the next free id of a sequence
     *
     * @param string $seq_name name of the sequence
     * @param boolean $ondemand when true the sequence is
     *                          automatic created, if it
     *                          not exists
     *
     * @return mixed MDB2 Error Object or id
     * @access public
     */
    function nextID($seq_name, $ondemand = true)
    {
        $sequence_name = $this->quoteIdentifier($this->getSequenceName($seq_name), true);
        $seqcol_name = $this->quoteIdentifier($this->options['seqcol_name'], true);
        $this->pushErrorHandling(PEAR_ERROR_RETURN);
        $this->expectError(MDB2_ERROR_NOSUCHTABLE);
        
        $seq_val = $this->_checkSequence($sequence_name);

        if ($seq_val) {
            $query = "SET IDENTITY_INSERT $sequence_name OFF ".
                     "INSERT INTO $sequence_name DEFAULT VALUES";
        } else {
            $query = "INSERT INTO $sequence_name ($seqcol_name) VALUES (0)";
        }
        $result = $this->_doQuery($query, true);
        $this->popExpect();
        $this->popErrorHandling();
        if (PEAR::isError($result)) {
            if ($ondemand && !$this->_checkSequence($sequence_name)) {
                $this->loadModule('Manager', null, true);
                $result = $this->manager->createSequence($seq_name);
                if (PEAR::isError($result)) {
                    return $this->raiseError($result, null, null,
                        'on demand sequence '.$seq_name.' could not be created', __FUNCTION__);
                } else {
                    /**
                     * Little off-by-one problem with the sequence emulation
                     * here being fixed, that instead of re-calling nextID
                     * and forcing an increment by one, we simply check if it
                     * exists, then we get the last inserted id if it does.
                     *
                     * In theory, $seq_name should be created otherwise there would
                     * have been an error thrown somewhere up there.. 
                     *
                     * @todo confirm
                     */
                    if ($this->_checkSequence($seq_name)) {
                        return $this->lastInsertID($seq_name);
                    }

                    return $this->nextID($seq_name, false);
                }
            }
            return $result;
        }
        $value = $this->lastInsertID($sequence_name);
        if (is_numeric($value)) {
            $query = "DELETE FROM $sequence_name WHERE $seqcol_name < $value";
            $result = $this->_doQuery($query, true);
            if (PEAR::isError($result)) {
                $this->warnings[] = 'nextID: could not delete previous sequence table values from '.$seq_name;
            }
        }
        return $value;
    }

    // }}}
    // {{{ lastInsertID()

    /**
     * Returns the autoincrement ID if supported or $id or fetches the current
     * ID in a sequence called: $table.(empty($field) ? '' : '_'.$field)
     *
     * @param string $table name of the table into which a new row was inserted
     * @param string $field name of the field into which a new row was inserted
     *
     * @return mixed MDB2 Error Object or id
     * @access public
     */
    function lastInsertID($table = null, $field = null)
    {
        $server_info = $this->getServerVersion();
        if (is_array($server_info) && (null !== $server_info['major'])
           && $server_info['major'] >= 8
        ) {
            $query = "SELECT IDENT_CURRENT('$table')";
        } else {
            $query = "SELECT @@IDENTITY";
            if (null !== $table) {
                $query .= ' FROM '.$this->quoteIdentifier($table, true);
            }
        }

        return $this->queryOne($query, 'integer');
    }

    // }}}
}

// }}}
// {{{ Class MDB2_Result_mssql

/**
 * MDB2 MSSQL Server result driver
 *
 * @package MDB2
 * @category Database
 * @author  Frank M. Kromann <frank@kromann.info>
 */
class MDB2_Result_mssql extends MDB2_Result_Common
{
    // {{{ _skipLimitOffset()

    /**
     * Skip the first row of a result set.
     *
     * @param resource $result
     * @return mixed a result handle or MDB2_OK on success, a MDB2 error on failure
     * @access protected
     */
    function _skipLimitOffset()
    {
        if ($this->limit) {
            if ($this->rownum >= $this->limit) {
                return false;
            }
        }
        if ($this->offset) {
            while ($this->offset_count < $this->offset) {
                ++$this->offset_count;
                if (!is_array(@mssql_fetch_row($this->result))) {
                    $this->offset_count = $this->limit;
                    return false;
                }
            }
        }
        return MDB2_OK;
    }

    // }}}
    // {{{ fetchRow()

    /**
     * Fetch a row and insert the data into an existing array.
     *
     * @param int $fetchmode  how the array data should be indexed
     * @param int $rownum     number of the row where the data can be found
     *
     * @return int data array on success, a MDB2 error on failure
     * @access public
     */
    function fetchRow($fetchmode = MDB2_FETCHMODE_DEFAULT, $rownum = null)
    {
        if (!$this->_skipLimitOffset()) {
            return null;
        }
        if (null !== $rownum) {
            $seek = $this->seek($rownum);
            if (PEAR::isError($seek)) {
                return $seek;
            }
        }
        if ($fetchmode == MDB2_FETCHMODE_DEFAULT) {
            $fetchmode = $this->db->fetchmode;
        }
        if ($fetchmode & MDB2_FETCHMODE_ASSOC) {
            $row = @mssql_fetch_assoc($this->result);
            if (is_array($row)
                && $this->db->options['portability'] & MDB2_PORTABILITY_FIX_CASE
            ) {
                $row = array_change_key_case($row, $this->db->options['field_case']);
            }
        } else {
            $row = @mssql_fetch_row($this->result);
        }
        if (!$row) {
            if (false === $this->result) {
                $err = $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'resultset has already been freed', __FUNCTION__);
                return $err;
            }
            return null;
        }
        $mode = $this->db->options['portability'] & MDB2_PORTABILITY_EMPTY_TO_NULL;
        $rtrim = false;
        if ($this->db->options['portability'] & MDB2_PORTABILITY_RTRIM) {
            if (empty($this->types)) {
                $mode += MDB2_PORTABILITY_RTRIM;
            } else {
                $rtrim = true;
            }
        }
        if ($mode) {
            $this->db->_fixResultArrayValues($row, $mode);
        }
        if (!empty($this->types)) {
            $row = $this->db->datatype->convertResultRow($this->types, $row, $rtrim);
        }
        if (!empty($this->values)) {
            $this->_assignBindColumns($row);
        }
        if ($fetchmode === MDB2_FETCHMODE_OBJECT) {
            $object_class = $this->db->options['fetch_class'];
            if ($object_class == 'stdClass') {
                $row = (object) $row;
            } else {
                $rowObj = new $object_class($row);
                $row = $rowObj;
            }
        }
        ++$this->rownum;
        return $row;
    }

    // }}}
    // {{{ _getColumnNames()

    /**
     * Retrieve the names of columns returned by the DBMS in a query result.
     *
     * @return  mixed   Array variable that holds the names of columns as keys
     *                  or an MDB2 error on failure.
     *                  Some DBMS may not return any columns when the result set
     *                  does not contain any rows.
     * @access private
     */
    function _getColumnNames()
    {
        $columns = array();
        $numcols = $this->numCols();
        if (PEAR::isError($numcols)) {
            return $numcols;
        }
        for ($column = 0; $column < $numcols; $column++) {
            $column_name = @mssql_field_name($this->result, $column);
            $columns[$column_name] = $column;
        }
        if ($this->db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
            $columns = array_change_key_case($columns, $this->db->options['field_case']);
        }
        return $columns;
    }

    // }}}
    // {{{ numCols()

    /**
     * Count the number of columns returned by the DBMS in a query result.
     *
     * @return mixed integer value with the number of columns, a MDB2 error
     *      on failure
     * @access public
     */
    function numCols()
    {
        $cols = @mssql_num_fields($this->result);
        if (null === $cols) {
            if (false === $this->result) {
                return $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'resultset has already been freed', __FUNCTION__);
            }
            if (null === $this->result) {
                return count($this->types);
            }
            return $this->db->raiseError(null, null, null,
                'Could not get column count', __FUNCTION__);
        }
        return $cols;
    }

    // }}}
    // {{{ nextResult()

    /**
     * Move the internal result pointer to the next available result
     *
     * @return true on success, false if there is no more result set or an error object on failure
     * @access public
     */
    function nextResult()
    {
        if (false === $this->result) {
            return $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                'resultset has already been freed', __FUNCTION__);
        }
        if (null === $this->result) {
            return false;
        }
        return @mssql_next_result($this->result);
    }

    // }}}
    // {{{ free()

    /**
     * Free the internal resources associated with $result.
     *
     * @return boolean true on success, false if $result is invalid
     * @access public
     */
    function free()
    {
        if (is_resource($this->result) && $this->db->connection) {
            $free = @mssql_free_result($this->result);
            if (false === $free) {
                return $this->db->raiseError(null, null, null,
                    'Could not free result', __FUNCTION__);
            }
        }
        $this->result = false;
        return MDB2_OK;
    }

    // }}}
}

// }}}
// {{{ class MDB2_BufferedResult_mssql

/**
 * MDB2 MSSQL Server buffered result driver
 *
 * @package MDB2
 * @category Database
 * @author  Frank M. Kromann <frank@kromann.info>
 */
class MDB2_BufferedResult_mssql extends MDB2_Result_mssql
{
    // {{{ seek()

    /**
     * Seek to a specific row in a result set
     *
     * @param int $rownum number of the row where the data can be found
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     * @access public
     */
    function seek($rownum = 0)
    {
        if ($this->rownum != ($rownum - 1) && !@mssql_data_seek($this->result, $rownum)) {
            if (false === $this->result) {
                return $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'resultset has already been freed', __FUNCTION__);
            }
            if (null === $this->result) {
                return MDB2_OK;
            }
            return $this->db->raiseError(MDB2_ERROR_INVALID, null, null,
                'tried to seek to an invalid row number ('.$rownum.')', __FUNCTION__);
        }
        $this->rownum = $rownum - 1;
        return MDB2_OK;
    }

    // }}}
    // {{{ valid()

    /**
     * Check if the end of the result set has been reached
     *
     * @return mixed true or false on sucess, a MDB2 error on failure
     * @access public
     */
    function valid()
    {
        $numrows = $this->numRows();
        if (PEAR::isError($numrows)) {
            return $numrows;
        }
        return $this->rownum < ($numrows - 1);
    }

    // }}}
    // {{{ numRows()

    /**
     * Returns the number of rows in a result object
     *
     * @return mixed MDB2 Error Object or the number of rows
     * @access public
     */
    function numRows()
    {
        $rows = @mssql_num_rows($this->result);
        if (null === $rows) {
            if (false === $this->result) {
                return $this->db->raiseError(MDB2_ERROR_NEED_MORE_DATA, null, null,
                    'resultset has already been freed', __FUNCTION__);
            }
            if (null === $this->result) {
                return 0;
            }
            return $this->db->raiseError(null, null, null,
                'Could not get row count', __FUNCTION__);
        }
        if ($this->limit) {
            $rows -= $this->offset;
            if ($rows > $this->limit + 1) {
                $rows = $this->limit + 1;
            }
            if ($rows < 0) {
                $rows = 0;
            }
        }
        return $rows;
    }
}

// }}}
// {{{ MDB2_Statement_mssql

/**
 * MDB2 MSSQL Server statement driver
 *
 * @package MDB2
 * @category Database
 * @author  Frank M. Kromann <frank@kromann.info>
 */
class MDB2_Statement_mssql extends MDB2_Statement_Common
{

}

// }}}
?>