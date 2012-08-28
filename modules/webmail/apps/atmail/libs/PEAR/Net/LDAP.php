<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * File containing the Net_LDAP interface class.
 *
 * PHP version 4
 *
 * LICENSE:  LGPL.
 *
 * @category    Net
 * @package     Net_LDAP
 * @author      Tarjej Huse
 * @author      Jan Wagner
 * @author      Del <del@babel.com.au>
 * @author      Benedikt Hallinger <beni@php.net>
 * @copyright   2003-2007 Tarjej Huse, Jan Wagner, Del Elson, Benedikt Hallinger
 * @license     http://www.gnu.org/copyleft/lesser.html
 * @version     CVS: $Id: LDAP.php,v 1.61 2007/06/28 07:59:44 beni Exp $
 * @link        http://pear.php.net/package/Net_LDAP/
 */

/**
 * Package includes.
 */
require_once('PEAR.php');
require_once('LDAP/Entry.php');
require_once('LDAP/Search.php');
require_once('LDAP/Util.php');
require_once('LDAP/Filter.php');

/**
 *  Error constants for errors that are not LDAP errors.
 */
define ('NET_LDAP_ERROR', 1000);

/**
 * Net_LDAP - manipulate LDAP servers the right way!
 *
 * @category    Net
 * @package     Net_LDAP
 * @author      Tarjej Huse
 * @author      Jan Wagner
 * @author      Del <del@babel.com.au>
 * @author      Benedikt Hallinger <beni@php.net>
 * @copyright   2003-2007 Tarjej Huse, Jan Wagner, Del Elson, Benedikt Hallinger
 * @license     http://www.gnu.org/copyleft/lesser.html
 * @version     CVS: $Id: LDAP.php,v 1.61 2007/06/28 07:59:44 beni Exp $
 * @link        http://pear.php.net/package/Net_LDAP/
 */
 class Net_LDAP extends PEAR
{
    /**
     * Class configuration array
     *
     * host     = the ldap host to connect to (may be an array of several hosts to try)
     * port     = the server port
     * version  = ldap version (defaults to v 3)
     * starttls = when set, ldap_start_tls() is run after connecting.
     * bindpw   = no explanation needed
     * binddn   = the DN to bind as.
     * basedn   = ldap base
     * options  = hash of ldap options to set (opt => val)
     * filter   = default search filter
     * scope    = default search scope
     *
     * @access private
     * @var array
     */
     var $_config = array('host'     => 'localhost',
                          'port'     => 389,
                          'version'  => 3,
                          'starttls' => false,
                          'binddn'   => '',
                          'bindpw'   => '',
                          'basedn'   => '',
                          'options'  => array(),
                          'filter'   => '(objectClass=*)',
                          'scope'    => 'sub');

    /**
     * List of hosts we try to establish a connection to
     *
     * @access private
     * @var array
     */
    var $_host_list = array();

    /**
     * List of hosts that are known to be down.
     *
     * @access private
     * @var array
     */
    var $_down_host_list = array();

    /**
     * LDAP resource link.
     *
     * @access private
     * @var resource
     */
    var $_link = false;

    /**
     * Net_LDAP_Schema object
     *
     * This gets set and returned by {@link schema()}
     *
     * @access private
     * @var object Net_LDAP_Schema
     */
    var $_schema = null;

    /**
     * Cache for attribute encoding checks
     *
     * @access private
     * @var array Hash with attribute names as key and boolean value
     *            to determine whether they should be utf8 encoded or not.
     */
    var $_schemaAttrs = array();

    /**
     * Returns the Net_LDAP Release version, may be called statically
     *
     * @static
     * @return string Net_LDAP version
     */
    function getVersion()
    {
        return '0.7.2';
    }

    /**
     * Creates the initial ldap-object
     *
     * Static function that returns either an error object or the new Net_LDAP
     * object. Something like a factory. Takes a config array with the needed
     * parameters.
     *
     * @access public
     * @param array $config Configuration array
     * @return Net_LDAP_Error|Net_LDAP   Net_LDAP_Error or Net_LDAP object
     */
    function &connect($config = array())
    {
        $ldap_check = Net_LDAP::checkLDAPExtension();
        if (Net_LDAP::iserror($ldap_check)) {
            return $ldap_check;
        }

        @$obj = & new Net_LDAP($config);

        $err  = $obj->bind();
        if (Net_LDAP::isError($err)) {
            return $err;
        }

        return $obj;
    }

    /**
     * Net_LDAP constructor
     *
     * Sets the config array
     *
     * Please note that the usual way of getting Net_LDAP to work is
     * to call something like:
     * <code>$ldap = Net_LDAP::connect($ldap_config);</code>
     *
     * @access protected
     * @param array $config Configuration array
     * @return void
     * @see $_config
     */
    function Net_LDAP($config = array())
    {
        $this->PEAR('Net_LDAP_Error');
        $this->_setConfig($config);
    }

    /**
     * Sets the internal configuration array
     *
     * @access private
     * @param array $config Configuration array
     * @return void
     */
    function _setConfig($config)
    {

        //
        // Parameter check -- probably should raise an error here if config
        // is not an array.
        //
        if (! is_array($config)) {
            return;
        }

        foreach ($config as $k => $v) {
            if (isset($this->_config[$k])) {
                $this->_config[$k] = $v;
            } else {
                // map old (Net_LDAP) parms to new ones
                switch($k) {
                    case "dn":
                        $this->_config["binddn"] = $v;
                        break;
                    case "password":
                        $this->_config["bindpw"] = $v;
                        break;
                    case "tls":
                        $this->_config["starttls"] = $v;
                        break;
                    case "base":
                        $this->_config["basedn"] = $v;
                        break;
                }
            }
        }

        //
        // Ensure the host list is an array.
        //
        if (is_array($this->_config['host'])) {
            $this->_host_list = $this->_config['host'];
        } else {
             if (strlen($this->_config['host']) > 0) {
                $this->_host_list = array($this->_config['host']);
             } else {
                 $this->_host_list = array(); // this will cause an error in _connect(), so the user is notified
             }
        }

        //
        // Reset the down host list, which seems like a sensible thing to do
        // if the config is being reset for some reason.
        //
        $this->_down_host_list = array();
    }

    /**
     * Bind to the ldap-server
     *
     * This function binds with the given dn and password to the server. In case
     * no connection has been made yet, it will be startet and startTLS issued
     * if appropiate.
     *
     * @access public
     * @param string $dn        Distinguished name for binding
     * @param string $password  Password for binding
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function bind($dn = null, $password = null)
    {
        $msg = $this->_connect();
        if (Net_LDAP::isError($msg)) {
            return $msg;
        }
        if (is_null($dn)) {
            $dn = $this->_config["binddn"];
        }
        if (is_null($password)) {
            $password = $this->_config["bindpw"];
        }
        if (is_null($dn)) {
            $msg = @ldap_bind($this->_link);
        } else {
            $msg = @ldap_bind($this->_link, $dn, $password);
        }
        if (false === $msg) {
            return PEAR::raiseError("Bind failed: " .
                                    @ldap_error($this->_link),
                                    @ldap_errno($this->_link));
        }
        return true;
    }

    /**
     * Connect to the ldap-server
     *
     * This function connects to the given LDAP server.
     *
     * @access private
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function _connect()
    {

        //
        // Return true if we are already connected.
        //
        if ($this->_link !== false) {
            return true;
        }

        //
        // Connnect to the LDAP server if we are not connected.  Note that
        // with some LDAP clients, ldap_connect returns a link value even
        // if no connection is made.  We need to do at least one anonymous
        // bind to ensure that a connection is actually valid.
        //
        // Ref: http://www.php.net/manual/en/function.ldap-connect.php
        //

        //
        // Default error message in case all connection attempts fail but no message is set
        //
        $current_error =& new PEAR_Error('Unknown connection error');

        //
        // Catch empty $_host_list arrays.
        //
        if (!is_array($this->_host_list) || count($this->_host_list) == 0) {
            $current_error = PEAR::raiseError('No Servers configured! Please pass in an array of servers to Net_LDAP');
            return $current_error;
        }

        //
        // Cycle through the host list.
        //
        foreach ($this->_host_list as $host) {

            //
            // Ensure we have a valid string for host name
            //
            if (is_array($host)) {
                $current_error = PEAR::raiseError('No Servers configured! Please pass in an one dimensional array of servers to Net_LDAP! (multidimensional array detected!)');
                continue;
            }

            //
            // Skip this host if it is known to be down.
            //
            if (in_array($host, $this->_down_host_list)) {
                continue;
            }

            //
            // Record the host that we are actually connecting to in case
            // we need it later.
            //
            $this->_config['host'] = $host;

            //
            // Attempt a connection.
            //
            $this->_link = @ldap_connect($host, $this->_config['port']);
            if (false === $this->_link) {
                $current_error = PEAR::raiseError('Could not connect to ' .
                    $host . ':' . $this->_config['port']);
                continue;
            }

            //
            // Set LDAP version before trying to bind.
            //
            if (Net_LDAP::isError($msg = $this->setLDAPVersion())) {
                $current_error = $msg;
                $this->_link = false;
                $this->_down_host_list[] = $host;
                continue;
            }

            //
            // Attempt an anonymous bind to see if we can bind to the server
            // If it fails, we try a privilegued bind since some servers don't
            // allow anonymous connections at all
            //
            if (! @ldap_bind($this->_link)) {
                // Anonymous bind failed, try privilegued bind if we have credentials
                if ($this->_config['binddn']) {
                    $msg = $this->bind($this->_config['binddn'], $this->_config['bindpw']);
                    if (Net_LDAP::isError($msg)) {
                        // The privilegued bind failed too, discard link and save error msg
                        $this->_link = false;
                        $current_error = $msg;
                    }
                } else {
                    // Failed, so discard the link
                    // DO NOT attempt to call ldap_close on this connection ID,
                    // even attempting to do so can be fatal.  Just discard the
                    // link ID!
                    $this->_link = false;
                    $current_error = PEAR::raiseError("Bind failed: " .
                                        @ldap_error($this->_link),
                                        @ldap_errno($this->_link));
                }

                // Record the host as down and try next host
                // if the privelegued bind failed or we don't
                // had credentials and the anonymous bind failed
                if ($this->_link == false) {
                    $this->_down_host_list[] = $host;
                    continue;
                }
            }

            //
            // Set LDAP parameters, now we know we have a valid connection.
            //
            if ($this->_config["starttls"] === true) {
                if (Net_LDAP::isError($msg = $this->startTLS())) {
                    $current_error = $msg;
                    $this->_link = false;
                    $this->_down_host_list[] = $host;
                    continue;
                }
            }
            if (isset($this->_config['options']) &&
                is_array($this->_config['options']) &&
                count($this->_config['options'])) {
                foreach ($this->_config['options'] as $opt => $val) {
                    $err = $this->setOption($opt, $val);
                    if (Net_LDAP::isError($err)) {
                        $current_error = $err;
                        $this->_link = false;
                        $this->_down_host_list[] = $host;
                        continue 2;
                    }
                }
            }

            //
            // At this stage we have connected, bound, and set up options,
            // so we have a known good LDAP server.  Time to go home.
            //
            return true;
        }


        //
        // All connection attempts have failed, return the last error.
        //
        return $current_error;
    }

    /**
     * Starts an encrypted session
     *
     * @access public
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function startTLS()
    {
        if (false === @ldap_start_tls($this->_link)) {
            return $this->raiseError("TLS not started: " .
                                     @ldap_error($this->_link),
                                     @ldap_errno($this->_link));
        }
        return true;
    }

    /**
     * alias function of startTLS() for perl-ldap interface
     *
     * @see startTLS()
     */
    function start_tls()
    {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'startTLS' ), $args);
    }

    /**
     * Close LDAP connection.
     *
     * Closes the connection. Use this when the session is over.
     *
     * @return void
     */
    function done()
    {
        $this->_Net_LDAP();
    }

    /**
     * Destructor
     *
     * @access private
     */
    function _Net_LDAP()
    {
        @ldap_close($this->_link);
    }

    /**
     * Add a new entryobject to a directory.
     *
     * Use add to add a new Net_LDAP_Entry object to the directory.
     * This also links the entry to the connection used for the add,
     * if it was a fresh entry ({@link Net_LDAP_Entry::createFresh()})
     *
     * @param Net_LDAP_Entry $entry   Net_LDAP_Entry
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function add(&$entry)
    {
        if (false === is_a($entry, 'Net_LDAP_Entry')) {
            return PEAR::raiseError('Parameter to Net_LDAP::add() must be a Net_LDAP_Entry object.');
        }
        if (@ldap_add($this->_link, $entry->dn(), $entry->getValues())) {
             // entry successfully added, we should update its $ldap reference
             // in case it is not set so far (fresh entry)
             if (!$entry->_ldap) {
                 $entry->_ldap =& $this;
             }
             return true;
        } else {
             return PEAR::raiseError("Could not add entry " . $entry->dn() . " " .
                                     @ldap_error($this->_link),
                                     @ldap_errno($this->_link));
        }
    }

    /**
     * Delete an entry from the directory
     *
     * The object may either be a string representing the dn or a Net_LDAP_Entry
     * object. When the boolean paramter recursive is set, all subentries of the
     * entry will be deleted as well.
     *
     * @access public
     * @param string|Net_LDAP_Entry $dn DN-string or Net_LDAP_Entry
     * @param boolean $recursive   Should we delete all children recursive as well?
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function delete($dn, $recursive = false)
    {
        if (is_a($dn, 'Net_LDAP_Entry')) {
             $dn = $dn->dn();
        }
        if (false === is_string($dn)) {
            return PEAR::raiseError("Parameter is not a string nor an entry object!");
        }
        // Recursive delete searches for children and calls delete for them
        if ($recursive) {
            $result = @ldap_list($this->_link, $dn, '(objectClass=*)', array(null));
            if (@ldap_count_entries($this->_link, $result)) {
                $subentry = @ldap_first_entry($this->_link, $result);
                $this->delete(@ldap_get_dn($this->_link, $subentry), $recursive);
                while ($subentry = @ldap_next_entry($this->_link, $subentry)) {
                    $this->delete(@ldap_get_dn($this->_link, $subentry), $recursive);
                }
            }
        }
        // Delete the DN
        if (false == @ldap_delete($this->_link, $dn)) {
            $error = @ldap_errno($this->_link);
            if ($error == 66) {
                return PEAR::raiseError("Could not delete entry $dn because of subentries. Use the recursive param to delete them.");
            } else {
                return PEAR::raiseError("Could not delete entry $dn: " .
                                         $this->errorMessage($error), $error);
            }
        }
        return true;
    }

    /**
     * Modify an ldapentry directly on the server
     *
     * This one takes the DN or a Net_LDAP_Entry object and an array of actions.
     * This array should be something like this:
     *
     * array('add' => array('attribute1' => array('val1', 'val2'),
     *                      'attribute2' => array('val1')),
     *       'delete' => array('attribute1'),
     *       'replace' => array('attribute1' => array('val1')),
     *       'changes' => array('add' => ...,
     *                          'replace' => ...,
     *                          'delete' => array('attribute1', 'attribute2' => array('val1')))
     *
     * The changes array is there so the order of operations can be influenced
     * (the operations are done in order of appearance).
     * The order of execution is as following:
     *   1. adds from 'add' array
     *   2. deletes from 'delete' array
     *   3. replaces from 'replace' array
     *   4. changes (add, replace, delete) in order of appearance
     * All subarrays (add, replace, delete, changes) may be given at the same time.
     *
     * The function calls the corresponding functions of an Net_LDAP_Entry
     * object. A detailed description of array structures can be found there.
     *
     * Unlike the modification methods provided by the Net_LDAP_Entry object,
     * this method will instantly carry out an update() after each operation,
     * thus modifying "directly" on the server.
     *
     * @access public
     * @param string|Net_LDAP_Entry $entry DN-string or Net_LDAP_Entry
     * @param array $params Array of changes
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function modify($entry , $parms = array())
    {
        if (is_string($entry)) {
            $entry = $this->getEntry($entry);
            if (Net_LDAP::isError($entry)) {
                return $entry;
            }
        }
        if (!is_a($entry, 'Net_LDAP_Entry')) {
            return PEAR::raiseError("Parameter is not a string nor an entry object!");
        }

        foreach (array('add', 'delete', 'replace') as $action) {
            if (isset($parms[$action])) {
                $msg = $entry->$action($parms[$action]);
                if (Net_LDAP::isError($msg)) {
                    return $msg;
                }
                $msg = $entry->update($this);
                if (Net_LDAP::isError($msg)) {
                    return $msg;
                }
            }
        }

        if (isset($parms['changes']) && is_array($parms['changes'])) {
            foreach ($parms['changes'] as $action => $value) {
                $msg = $this->modify($entry->dn(), array($action => $value));
                if (Net_LDAP::isError($msg)) {
                    return $msg;
                }
            }
        }

        return true;
    }

    /**
     * Run a ldap query
     *
     * Search is used to query the ldap-database.
     * $base and $filter may be ommitted.The one from config will then be used.
     * Params may contain:
     *
     * scope: The scope which will be used for searching
     *        base - Just one entry
     *        sub  - The whole tree
     *        one  - Immediately below $base
     * sizelimit: Limit the number of entries returned (default: 0 = unlimited),
     * timelimit: Limit the time spent for searching (default: 0 = unlimited),
     * attrsonly: If true, the search will only return the attribute names,
     * attributes: Array of attribute names, which the entry should contain.
     *             It is good practice to limit this to just the ones you need.
     * [NOT IMPLEMENTED]
     * deref: By default aliases are dereferenced to locate the base object for the search, but not when
     *        searching subordinates of the base object. This may be changed by specifying one of the
     *        following values:
     *
     *        never  - Do not dereference aliases in searching or in locating the base object of the search.
     *        search - Dereference aliases in subordinates of the base object in searching, but not in
     *                locating the base object of the search.
     *        find
     *        always
     *
     * @access public
     * @param string $base LDAP searchbase
     * @param string|Net_LDAP_Filter $filter     LDAP search filter or a Net_LDAP_Filter object
     * @param array $params Array of options
     * @return Net_LDAP_Search|Net_LDAP_Error    Net_LDAP_Search object or Net_LDAP_Error object
     */
    function search($base = null, $filter = null, $params = array())
    {
        if (is_null($base)) {
            $base = $this->_config['basedn'];
        }
        if (is_null($filter)) {
            $filter = $this->_config['filter'];
        }
        if (is_a($filter, 'Net_LDAP_Filter')) {
            $filter = $filter->asString(); // convert Net_LDAP_Filter to string representation
        }
        if (PEAR::isError($filter)) {
            return $filter;
        }

        /* setting searchparameters  */
        (isset($params['sizelimit']))  ? $sizelimit  = $params['sizelimit']  : $sizelimit = 0;
        (isset($params['timelimit']))  ? $timelimit  = $params['timelimit']  : $timelimit = 0;
        (isset($params['attrsonly']))  ? $attrsonly  = $params['attrsonly']  : $attrsonly = 0;
        (isset($params['attributes'])) ? $attributes = $params['attributes'] : $attributes = array();

        if (!is_array($attributes)) {
            PEAR::raiseError("The param attributes must be an array!");
        }

        // reorganize the attributes array index keys
        // sometimes there are problems with not consecutive indexes
        $attributes = array_values($attributes);

        // scoping makes searches faster!
        $scope = (isset($params['scope']) ? $params['scope'] : $this->_config['scope']);

        switch ($scope) {
            case 'one':
                $search_function = 'ldap_list';
                break;
            case 'base':
                $search_function = 'ldap_read';
                break;
            default:
                $search_function = 'ldap_search';
        }

        $search = @call_user_func($search_function,
                                  $this->_link,
                                  $base,
                                  $filter,
                                  $attributes,
                                  $attrsonly,
                                  $sizelimit,
                                  $timelimit);

        if ($err = @ldap_errno($this->_link)) {
            if ($err == 32) {
                // Errorcode 32 = no such object, i.e. a nullresult.
                return $obj = & new Net_LDAP_Search ($search, $this, $attributes);
            } elseif ($err == 4) {
                // Errorcode 4 = sizelimit exeeded. TODO
                return $obj = & new Net_LDAP_Search ($search, $this, $attributes);
            } elseif ($err == 87) {
                // bad search filter
                return $this->raiseError($this->errorMessage($err) . "($filter)", $err);
            } else {
                $msg = "\nParameters:\nBase: $base\nFilter: $filter\nScope: $scope";
                return $this->raiseError($this->errorMessage($err) . $msg, $err);
            }
        } else {
            return $obj = & new Net_LDAP_Search($search, $this, $attributes);
        }
    }

    /**
     * Set an LDAP option
     *
     * @access public
     * @param string $option Option to set
     * @param mixed $value Value to set Option to
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function setOption($option, $value)
    {
        if ($this->_link) {
            if (defined($option)) {
                if (@ldap_set_option($this->_link, constant($option), $value)) {
                    return true;
                } else {
                    $err = @ldap_errno($this->_link);
                    if ($err) {
                        $msg = @ldap_err2str($err);
                    } else {
                        $err = NET_LDAP_ERROR;
                        $msg = $this->errorMessage($err);
                    }
                    return $this->raiseError($msg, $err);
                }
            } else {
                return $this->raiseError("Unkown Option requested");
            }
        } else {
            return $this->raiseError("Could not set LDAP option: No LDAP connection");
        }
    }

    /**
     * Get an LDAP option value
     *
     * @access public
     * @param string $option Option to get
     * @return Net_LDAP_Error|string Net_LDAP_Error or option value
     */
    function getOption($option)
    {
        if ($this->_link) {
            if (defined($option)) {
                if (@ldap_get_option($this->_link, constant($option), $value)) {
                    return $value;
                } else {
                    $err = @ldap_errno($this->_link);
                    if ($err) {
                        $msg = @ldap_err2str($err);
                    } else {
                        $err = NET_LDAP_ERROR;
                        $msg = $this->errorMessage($err);
                    }
                    return $this->raiseError($msg, $err);
                }
            } else {
                $this->raiseError("Unkown Option requested");
            }
        } else {
            $this->raiseError("No LDAP connection");
        }
    }

    /**
     * Get the LDAP_PROTOCOL_VERSION that is used on the connection.
     *
     * A lot of ldap functionality is defined by what protocol version the ldap server speaks.
     * This might be 2 or 3.
     *
     * @return int
     */
    function getLDAPVersion()
    {
        if($this->_link) {
            $version = $this->getOption("LDAP_OPT_PROTOCOL_VERSION");
        } else {
            $version = $this->_config['version'];
        }
        return $version;
    }

    /**
     * Set the LDAP_PROTOCOL_VERSION that is used on the connection.
     *
     * @param int Version to set
     * @return Net_LDAP_Error|true    Net_LDAP_Error object or true
     */
    function setLDAPVersion($version = 0)
    {
        if (!$version) {
            $version = $this->_config['version'];
        }
        return $this->setOption("LDAP_OPT_PROTOCOL_VERSION", $version);
    }


    /**
     * Tell if a dn already exists
     *
     * @param string $dn  The DN of the object to test
     * @return boolean
     */
    function dnExists($dn)
    {
        // make dn relative to parent
        $base = Net_LDAP_Util::ldap_explode_dn($this->_newdn, array('casefolding' => 'none', 'reverse' => false, 'onlyvalues' => false));
        if (Net_LDAP::isError($base)) {
           return $base;
        }
        $filter = array_shift($base);
        // maybe the dn consist of a multivalued RDN, we must build the dn in this case
        // because the $child-RDN is an array!
        if (is_array($filter)) {
            $filter = Net_LDAP_Util::canonical_dn($filter);
        }
        $base = Net_LDAP_Util::canonical_dn($base);

        $result = @ldap_list($this->_link, $base, $filter, array(), 1, 1);
        if (ldap_errno($this->_link) == 32) {
            $return = false;
            return $return;
        }
        if (ldap_errno($this->_link) != 0) {
            PEAR::raiseError(ldap_error($this->_link), ldap_errno($this->_link));
        }
        if (@ldap_count_entries($this->_link, $result)) {
            $return = true;
            return $return;
        }
        $return = false;
        return $return;
    }


   /**
    * Get a specific entry based on the DN
    *
    * @todo Maybe check against the shema should be done to be sure the attribute type exists
    * @param string $dn   DN of the entry that should be fetched
    * @param array  $attr Array of Attributes to select
    * @return Net_LDAP_Entry|Net_LDAP_Error    Reference to a Net_LDAP_Entry object or Net_LDAP_Error object
    */
   function &getEntry($dn, $attr = array())
   {
        if (!is_array($attr)) {
            $attr = array($attr);
        }
        $result = $this->search($dn, '(objectClass=*)',
                                array('scope' => 'base', 'attributes' => $attr));
        if (Net_LDAP::isError($result)) {
            return $result;
        } elseif ($result->count() == 0) {
            return PEAR::raiseError('Could not fetch entry: no entry found');
        }
        $entry = $result->shiftEntry();
        if (false == $entry) {
            return PEAR::raiseError('Could not fetch entry (error retrieving entry from search result)');
        }
        return $entry;
   }

   /**
   * Rename or move an entry
   *
   * This method will instantly carry out an update() after the move,
   * so the entry is moved instantly.
   * You can pass an optional Net_LDAP object. In this case, a cross directory
   * move will be performed which deletes the entry in the source (THIS) directory
   * and adds it in the directory $target_ldap.
   *
   * @param string|Net_LDAP_Entry $entry   Entry DN or Entry object
   * @param string $newdn                  New location
   * @param Net_LDAP $target_ldap          (optional) Target directory for cross server move; should be passed via reference
   * @return Net_LDAP_Error|true
   */
   function move(&$entry, $newdn, $target_ldap = null)
   {
       if (!is_string($entry)) {
           $entry = new Net_LDAP_Entry($this, $entry);
       }
       if (!is_a($entry, 'Net_LDAP_Entry')) {
           return PEAR::raiseError('Parameter $entry is expected to be a Net_LDAP_Entry object! (If DN was passed, conversion failed)');
       }
       if (null !== $target_ldap && !is_a($target_ldap, 'Net_LDAP')) {
           return PEAR::raiseError('Parameter $target_ldap is expected to be a Net_LDAP object!');
       }

       if ($target_ldap && $target_ldap !== $this) {
           // cross directory move
           if ($target_ldap->dnExists($entry->dn())) {
               return PEAR::raiseError('Unable to perform cross directory move: entry does exist in target directory');
           }
           $res = $target_ldap->add($entry);
           if (Net_LDAP::isError($res)) {
               return PEAR::raiseError('Unable to perform cross directory move: '.$res->getMessage().' in target directory');
           }
           $res = $this->delete();
           if (Net_LDAP::isError($res)) {
               $res2 = $target_ldap->delete($entry, true); // undo add
               if (Net_LDAP::isError($res2)) {
                   $add_error_string = 'Additionally, the deletion of $entry in target directory failed.';
               }
               return PEAR::raiseError('Unable to perform cross directory move: '.$res->getMessage().' in target directory. '.$add_error_string);
           }
           return true;
       } else {
           // local move
           $entry->dn($newdn);
           return $entry->update($this);
       }
   }

   /**
   * Copy an entry to a new location
   *
   * The entry will be immediately copied.
   * If you pass an Net_LDAP_Entry object, the source entry is not required
   * to be existent on this LDAP server, which can be used to
   * copy between directory servers.
   *
   * @param string|Net_LDAP_Entry $entry   Entry DN or Entry object
   * @param string $newdn                  New location
   * @return Net_LDAP_Error|Net_LDAP_Entry Error Message or reference to the copied entry
   */
   function &copy(&$entry, $newdn)
   {
       if (!is_string($entry)) {
           $entry = new Net_LDAP_Entry($this, $entry);
       }
       if (!is_a($entry, 'Net_LDAP_Entry')) {
           return PEAR::raiseError('Parameter $entry is expected to be a Net_LDAP_Entry object! (If DN was passed, conversion failed)');
       }

       $newentry = Net_LDAP_Entry::createFresh($newdn, $entry->getValues());
       $result = $this->add($newentry);

       if (is_a($result, 'Net_LDAP_Error')) {
           return $result;
       } else {
           return $newentry;
       }
   }


    /**
     * Returns the string for an ldap errorcode.
     *
     * Made to be able to make better errorhandling
     * Function based on DB::errorMessage()
     * Tip: The best description of the errorcodes is found here:
     * http://www.directory-info.com/LDAP/LDAPErrorCodes.html
     *
     * @param int $errorcode Error code
     * @return string The errorstring for the error.
     */
    function errorMessage($errorcode)
    {
        $errorMessages = array(
                              0x00 => "LDAP_SUCCESS",
                              0x01 => "LDAP_OPERATIONS_ERROR",
                              0x02 => "LDAP_PROTOCOL_ERROR",
                              0x03 => "LDAP_TIMELIMIT_EXCEEDED",
                              0x04 => "LDAP_SIZELIMIT_EXCEEDED",
                              0x05 => "LDAP_COMPARE_FALSE",
                              0x06 => "LDAP_COMPARE_TRUE",
                              0x07 => "LDAP_AUTH_METHOD_NOT_SUPPORTED",
                              0x08 => "LDAP_STRONG_AUTH_REQUIRED",
                              0x09 => "LDAP_PARTIAL_RESULTS",
                              0x0a => "LDAP_REFERRAL",
                              0x0b => "LDAP_ADMINLIMIT_EXCEEDED",
                              0x0c => "LDAP_UNAVAILABLE_CRITICAL_EXTENSION",
                              0x0d => "LDAP_CONFIDENTIALITY_REQUIRED",
                              0x0e => "LDAP_SASL_BIND_INPROGRESS",
                              0x10 => "LDAP_NO_SUCH_ATTRIBUTE",
                              0x11 => "LDAP_UNDEFINED_TYPE",
                              0x12 => "LDAP_INAPPROPRIATE_MATCHING",
                              0x13 => "LDAP_CONSTRAINT_VIOLATION",
                              0x14 => "LDAP_TYPE_OR_VALUE_EXISTS",
                              0x15 => "LDAP_INVALID_SYNTAX",
                              0x20 => "LDAP_NO_SUCH_OBJECT",
                              0x21 => "LDAP_ALIAS_PROBLEM",
                              0x22 => "LDAP_INVALID_DN_SYNTAX",
                              0x23 => "LDAP_IS_LEAF",
                              0x24 => "LDAP_ALIAS_DEREF_PROBLEM",
                              0x30 => "LDAP_INAPPROPRIATE_AUTH",
                              0x31 => "LDAP_INVALID_CREDENTIALS",
                              0x32 => "LDAP_INSUFFICIENT_ACCESS",
                              0x33 => "LDAP_BUSY",
                              0x34 => "LDAP_UNAVAILABLE",
                              0x35 => "LDAP_UNWILLING_TO_PERFORM",
                              0x36 => "LDAP_LOOP_DETECT",
                              0x3C => "LDAP_SORT_CONTROL_MISSING",
                              0x3D => "LDAP_INDEX_RANGE_ERROR",
                              0x40 => "LDAP_NAMING_VIOLATION",
                              0x41 => "LDAP_OBJECT_CLASS_VIOLATION",
                              0x42 => "LDAP_NOT_ALLOWED_ON_NONLEAF",
                              0x43 => "LDAP_NOT_ALLOWED_ON_RDN",
                              0x44 => "LDAP_ALREADY_EXISTS",
                              0x45 => "LDAP_NO_OBJECT_CLASS_MODS",
                              0x46 => "LDAP_RESULTS_TOO_LARGE",
                              0x47 => "LDAP_AFFECTS_MULTIPLE_DSAS",
                              0x50 => "LDAP_OTHER",
                              0x51 => "LDAP_SERVER_DOWN",
                              0x52 => "LDAP_LOCAL_ERROR",
                              0x53 => "LDAP_ENCODING_ERROR",
                              0x54 => "LDAP_DECODING_ERROR",
                              0x55 => "LDAP_TIMEOUT",
                              0x56 => "LDAP_AUTH_UNKNOWN",
                              0x57 => "LDAP_FILTER_ERROR",
                              0x58 => "LDAP_USER_CANCELLED",
                              0x59 => "LDAP_PARAM_ERROR",
                              0x5a => "LDAP_NO_MEMORY",
                              0x5b => "LDAP_CONNECT_ERROR",
                              0x5c => "LDAP_NOT_SUPPORTED",
                              0x5d => "LDAP_CONTROL_NOT_FOUND",
                              0x5e => "LDAP_NO_RESULTS_RETURNED",
                              0x5f => "LDAP_MORE_RESULTS_TO_RETURN",
                              0x60 => "LDAP_CLIENT_LOOP",
                              0x61 => "LDAP_REFERRAL_LIMIT_EXCEEDED",
                              1000 => "Unknown Net_LDAP Error"
                              );

         return isset($errorMessages[$errorcode]) ?
            $errorMessages[$errorcode] :
            $errorMessages[NET_LDAP_ERROR] . ' (' . $errorcode . ')';
    }

    /**
     * Tell whether value is a Net_LDAP_Error or not
     *
     * @access public
     * @param mixed  $value  A variable, most commonly some Net_LDAP* object
     * @return boolean
     */
    function isError($value)
    {
        return (is_a($value, "Net_LDAP_Error") || parent::isError($value));
    }

    /**
     * gets a root dse object
     *
     * @access public
     * @author Jan Wagner <wagner@netsols.de>
     * @param array $attrs Array of attributes to search for
     * @return Net_LDAP_Error|Net_LDAP_RootDSE   Net_LDAP_Error or Net_LDAP_RootDSE object
     */
    function &rootDse($attrs = null)
    {
        require_once('Net/LDAP/RootDSE.php');

        if (is_array($attrs) && count($attrs) > 0 ) {
            $attributes = $attrs;
        } else {
            $attributes = array('namingContexts',
                                'altServer',
                                'supportedExtension',
                                'supportedControl',
                                'supportedSASLMechanisms',
                                'supportedLDAPVersion',
                                'subschemaSubentry' );
        }
        $result = $this->search('', '(objectClass=*)', array('attributes' => $attributes, 'scope' => 'base'));
        if (Net_LDAP::isError($result)) {
            return $result;
        }
        $entry = $result->shiftEntry();
        if (false === $entry) {
            return PEAR::raiseError('Could not fetch RootDSE entry');
        }
        $ret = new Net_LDAP_RootDSE($entry);
        return $ret;
    }

    /**
     * alias function of rootDse() for perl-ldap interface
     *
     * @access public
     * @see rootDse()
     */
    function &root_dse()
    {
        $args = func_get_args();
        return call_user_func_array(array(&$this, 'rootDse'), $args);
    }

    /**
     * get a schema object
     *
     * @access public
     * @author Jan Wagner <wagner@netsols.de>
     * @param string $dn Subschema entry dn
     * @return Net_LDAP_Schema|Net_LDAP_Error  Net_LDAP_Schema or Net_LDAP_Error object
     */
     function &schema($dn = null)
     {
        if (false == is_a($this->_schema, 'Net_LDAP_Schema'))
        {
            require_once('Net/LDAP/Schema.php');

            $this->_schema = & new Net_LDAP_Schema();

            if (is_null($dn)) {
                // get the subschema entry via root dse
                $dse = $this->rootDSE(array('subschemaSubentry'));
                if (false == Net_LDAP::isError($dse)) {
                    $base = $dse->getValue('subschemaSubentry', 'single');
                    if (!Net_LDAP::isError($base)) {
                        $dn = $base;
                    }
                }
            }

            //
            // Support for buggy LDAP servers (e.g. Siemens) that incorrectly
            // call this entry subSchemaSubentry instead of subschemaSubentry.
            // Note the correce case/spelling as per RFC 2251.
            //
            if (is_null($dn)) {
                // get the subschema entry via root dse
                $dse = $this->rootDSE(array('subSchemaSubentry'));
                if (false == Net_LDAP::isError($dse)) {
                    $base = $dse->getValue('subSchemaSubentry', 'single');
                    if (!Net_LDAP::isError($base)) {
                        $dn = $base;
                    }
                }
            }

            //
            // Final fallback case where there is no subschemaSubentry attribute
            // in the root DSE (this is a bug for an LDAP v3 server so report this
            // to your LDAP vendor if you get this far).
            //
            if (is_null($dn)) {
                $dn = 'cn=Subschema';
            }

            // fetch the subschema entry
            $result = $this->search($dn, '(objectClass=*)',
                                    array('attributes' => array_values($this->_schema->types),
                                          'scope' => 'base'));
            if (Net_LDAP::isError($result)) {
                return $result;
            }

            $entry = $result->shiftEntry();
            if (false === $entry) {
                return PEAR::raiseError('Could not fetch Subschema entry');
            }

            $this->_schema->parse($entry);
        }
        return $this->_schema;
    }

    /**
    * Checks if phps ldap-extension is loaded
    *
    * If it is not loaded, it tries to load it manually using PHPs dl().
    * It knows both windows-dll and *nix-so.
    *
    * @static
    * @return Net_LDAP_Error|true
    */
    function checkLDAPExtension()
    {
        if (!extension_loaded('ldap') && !@dl('ldap.' . PHP_SHLIB_SUFFIX)) {
            return PEAR::raiseError("It seems that you do not have the ldap-extension installed. Please install it before using the Net_LDAP package.");
        } else {
            return true;
        }
    }

    /**
     * Encodes given attributes to UTF8 if needed
     *
     * This function takes attributes in an array and then checks against the schema if they need
     * UTF8 encoding. If that is so, they will be encoded. An encoded array will be returned and
     * can be used for adding or modifying.
     *
     * @access public
     * @param array $attributes Array of attributes
     * @return array Array of UTF8 encoded attributes
     */
    function utf8Encode($attributes)
    {
        return $this->_utf8($attributes, 'utf8_encode');
    }

    /**
     * Decodes the given attribute values
     *
     * @access public
     * @param array $attributes Array of attributes
     * @return array Array with decoded attribute values
     */
    function utf8Decode($attributes)
    {
        return $this->_utf8($attributes, 'utf8_decode');
    }

    /**
     * Encodes or decodes attribute values if needed
     *
     * @access private
     * @param array $attributes Array of attributes
     * @param array $function   Function to apply to attribute values
     * @return array Array of attributes with function applied to values
     */
    function _utf8($attributes, $function)
    {
        if (!$this->_schema) {
            $this->_schema = $this->schema();
        }

        if (!$this->_link || Net_LDAP::isError($this->_schema) || !function_exists($function)) {
           return $attributes;
        }

        if (is_array($attributes) && count($attributes) > 0) {

            foreach( $attributes as $k => $v ) {

                if (!isset($this->_schemaAttrs[$k])) {

                    $attr = $this->_schema->get('attribute', $k);
                    if (Net_LDAP::isError($attr)) {
                        continue;
                    }

                    if (false !== strpos($attr['syntax'], '1.3.6.1.4.1.1466.115.121.1.15')) {
                        $encode = true;
                    } else {
                        $encode = false;
                    }
                    $this->_schemaAttrs[$k] = $encode;

                } else {
                    $encode = $this->_schemaAttrs[$k];
                }

                if ($encode) {
                    if (is_array($v)) {
                        foreach ($v as $ak => $av) {
                            $v[$ak] = call_user_func($function, $av );
                        }
                    } else {
                        $v = call_user_func($function, $v);
                    }
                }
                $attributes[$k] = $v;
            }
        }
        return $attributes;
    }

    /**
     * Get the LDAP link
     *
     * @access public
     * @return resource LDAP link
     */
    function &getLink()
    {
        return $this->_link;
    }
}

/**
 * Net_LDAP_Error implements a class for reporting portable LDAP error messages.
 *
 * @package Net_LDAP
 */
class Net_LDAP_Error extends PEAR_Error
{
    /**
     * Net_LDAP_Error constructor.
     *
     * @param string  $message    String with error message.
     * @param integer $code       Net_LDAP error code
     * @param integer $mode       what "error mode" to operate in
     * @param mixed   $level      what error level to use for $mode & PEAR_ERROR_TRIGGER
     * @param mixed   $debuginfo  additional debug info, such as the last query
     * @access public
     * @see PEAR_Error
     */
    function Net_LDAP_Error($message = 'Net_LDAP_Error', $code = NET_LDAP_ERROR, $mode = PEAR_ERROR_RETURN,
                            $level = E_USER_NOTICE, $debuginfo = null)
    {
        if (is_int($code)) {
            $this->PEAR_Error($message . ': ' . Net_LDAP::errorMessage($code), $code, $mode, $level, $debuginfo);
        } else {
            $this->PEAR_Error("$message: $code", NET_LDAP_ERROR, $mode, $level, $debuginfo);
        }
    }
}

?>
