<?php
// +----------------------------------------------------------------+
// | SQL.php														|
// +----------------------------------------------------------------+
// | Function: Use the PEAR DB Library to connect to our SQL server,|
// | let it be mySQL, Oracle, Sybase, mSQL , etc	  				|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: February 2005											|
// +----------------------------------------------------------------+

require_once('header.php');
require_once('DB.php');
require_once('Filter.class.php');
require_once('Config.php');

class SQL {

	var $dbh;
	var $debug;
	var $EmailDatabase;
    var $EmailMessage;
    var $EmailUIDL;
    var $UserSettings;
    var $SpamDB;
    var $MailSort;
    var $Abook;
    var $Calendar;
    var $AbookGroup;
    var $UserPgp;
	var $tableNames = array();
    
	// Constructor for SQL class
	function SQL()
	{
		global $pref;
	    $this->type = $pref['sql_type'];

	    $this->connect();
        if (DB::isError($this->dbh))
		{
            // display custom error message if have problems with dbserver
			if (!$this->dberror($this->dbh->getMessage())) {
				die($this->dbh->getMessage());
			}
		}

	    $this->debug = $pref['debug_sql'];

	    // Set some DB engine specific function names
	    if ($pref['sql_type'] == 'sqlite') {
	    	$this->NOW = "datetime('now', 'localtime')";
	    	$this->CURDATE = "datetime('now', strftime('-%H hours', 'now', 'localtime'), strftime('-%M minutes', 'now', 'localtime'), strftime('-%S seconds', 'now', 'localtime'), 'localtime')";

	    	// Register some custom functions for sqlite
	    	// that will emulate some MySQL functions that we use
	    	sqlite_create_function($this->dbh->connection, 'CONCAT', array('SQL', 'concat'));

	    } else {
	    	$this->NOW = 'NOW()';
	    	$this->CURDATE = 'CURDATE()';
	    }
	}

	function get($var)
	{
		return $this->$var;
	}


	function connect()
	{
		global $pref;
	    if ($this->type == 'sqlite') {
	    	$this->dbh = DB::connect("sqlite:///{$pref['sql_table']}");
	    } else {
	    	$this->dbh = DB::connect($pref['sql_type']."://".$pref['sql_user'].":".$pref['sql_pass']."@".$pref['sql_host']."/".$pref['sql_table'], array('debug' => 2));
	    }
	}


	// Get the message body from the SQL server
	function msgquery($query, $data, $type, $session=null, $num=null)
	{
		global $pref, $atmail;

		if (!is_array($data))
			settype($data, 'array');

		// If no type specified, by default write the email to
		// the user_dir/tmp/ directory. For parsing by the MIME module
		if (!$type)
		{
			$pid = getmypid();
			if(!$session && !$num)
			$theemail = $atmail->tmpdir . time() . "-$pid-" . intval(rand(0,1000)) . "email.data";
			else
			$theemail = $atmail->tmpdir . "$session-$num.data";

			// If our cache file already exists, no need to run the SQL query again!
			if(file_exists($theemail) && $session && $num)
			return $theemail;

			if (!$fh = fopen($theemail, 'w'))
				catcherror("Error writing temporary file. Check the ".$pref['user_dir']."/tmp/ directory exists, and has write permissions for the webserver user "); // add webserver user details to this string

			fwrite($fh, $this->dbh->getOne($query, $data));
			fclose($fh);
		}
		else
		{
			// If specified return a $var containing the email
			$res = $this->dbh->getOne($query, $data);
			if (DB::isError($res))
			{
				die('<br>error: '.$res->getDebugInfo());
			}
	        $theemail = explode("\n", $res);
	    }

	    // Return the pathname or email $var
	    return $theemail;
	}

	/**
	 * generic wrapper function for making queries, uses DB::query()
	 *
	 * @param array $args arguments passed as assoc array.
	 * accepts query, data, fetchmode, type in any order. Type may be any
	 * one of:
	 * 'multi' for queries expected to return multiple rows
	 * 'row' for queries that will return 1 row
	 * 'one' for queries returning a single value
	 * 'IDU' for INSERT, DELETE, UPDATE queries where no value is returned
	 */
	function doquery($args)
	{
		extract($args);

		if (!is_array($data))
			settype($data, 'array');

		$mode = ($mode == 'hash')? DB_FETCHMODE_ASSOC : DB_FETCHMODE_ORDERED;

		if ($type == 'one')
		{
			$result = $this->dbh->getOne($query, $data);
			if (DB::isError($result))
			{
				catcherror("ERROR: Database query failed: <br>Query: <code>$query</code><br>DB Error Message: '".$result->getDebugInfo()."'");
			}

			return $result;
		}
		elseif ($type == 'IDU' || $type == 'UPDATE' || $type == 'INSERT' || $type == 'DELETE')
		{
			$result = $this->dbh->query($query, $data);
			if (DB::isError($result))
				$result = false;
			return true;
		}
		elseif ($type == 'row')
		{
			$result =& $this->dbh->getRow($query, $data, $mode);
			if (DB::isError($result))
			{
				//var_dump($result);
				catcherror("ERROR: Database query failed: <br>Query: <code>$query</code><br>DB Error Message: '".$result->getDebugInfo()."'");
			}
			return $result;
		}
		else
		{
			$result =& $this->dbh->query($query, $data);

			if (DB::isError($result))
			{
				catcherror("ERROR: Database query failed: <br>Query: <code>$query</code><br>DB Error Message: '".$result->getDebugInfo()."'");
			}

			$result_set = array();
			while ($row =& $result->fetchRow($mode))
				$result_set[] = $row;

			return $result_set;
		}
	}


	function getid()
	{
		if ($this->type == 'sqlite') {
			return $this->dbh->getOne('SELECT LAST_INSERT_ROWID()');
		}

		return $this->dbh->getOne('SELECT LAST_INSERT_ID()');
	}


	function movemsg(&$args)
	{
		extract($args);

		$data = array($box, $id);
		$query = "update $this->EmailDatabase set EmailBox=? where id=?";
		$result =& $this->dbh->query($query, $data);
		if (DB::isError($result)) {
			if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $result->getMessage() . " - " . $result->getUserInfo() . "\n");
		    }
		    
		    return false;
	    }
	    
	    return true;
	}

	function savemsg($arg)
	{
		// Find the size of the message on disk if no EmailSize defined
		if ($arg['EmailFile'])
			$arg['EmailSize'] = filesize($arg['EmailFile']);
		else
			$arg['EmailSize'] = strlen($arg['EmailMessage']);

		// Insert the message into the EmailDatabase table (header info only)
		$query = "INSERT INTO $this->EmailDatabase
				 (EmailSubject, EmailTo, EmailFrom, EmailDate, EmailBox,
				  EmailFlag, EmailAttach, EmailSize, Account,EmailUIDL)
				 VALUES (? , ? , ? , ? , ? , ?, ?, ?, ?, ?) ";

		$data = array($arg['EmailSubject'], $arg['EmailTo'],   $arg['EmailFrom'],
	      			  $arg['EmailDate'],    $arg['EmailBox'],  $arg['EmailFlag'],
					  $arg['EmailAttach'],  $arg['EmailSize'], $arg['Account'],
	      			  $arg['EmailUIDL']);

		$result =& $this->dbh->query($query, $data);
		unset($data);

		if (DB::isError($result)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $result->getMessage() . " - " . $result->getUserInfo() . "\n");
		    }
            return false;
		}

		// Get the unique ID key from the EmailDatabase table, from the last Insert
	    $key = $this->getid();

		// Insert the actual message into another table, referenced by the id
		$query = "INSERT INTO $this->EmailMessage (EmailMessage, id) VALUES (? , ?)";
		$data = array();

		// if the user specified an email file
		if (isset($arg['EmailFile']))
		{
			if (!$emailfile = file_get_contents($arg['EmailFile']))
				catcherror("Cannot open {$arg['EmailFile']}\n");
			$data[] = $emailfile;
		}
		// Otherwise print the EmailMessage that is defined
		else
		{
			$data[] = $arg['EmailMessage'];
		}

		$data[] = $key;
		$result = $this->dbh->query($query, $data);

		// if the email was incorrectly inserted into the database
		if (DB::isError($result))
		{
		    if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $result->getMessage() . " - " . $result->getUserInfo() . "\n");
		    }

			$data = array($key, $arg['Account']);
			$query = "DELETE FROM $this->EmailDatabase WHERE id=? AND Account=?";
			$this->dbh->query($query, $data);
            return false;
		}

		return true;
	}

	function mailboxsize($query)
	{
		$db = array();

		$result =& $this->dbh->query($query, null, DB_FETCHMODE_ASSOC);
		if (DB::isError($result)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $result->getMessage() . " - " . $result->getUserInfo() . "\n");
		    }
		}

		$num = 0;

		while ($fields =& $result->fetchrow())
		{
			foreach($fields as $fname => $fvalue)
			{
				$db[$fname] = $fvalue;

				if ($fname == "EmailBox")
				{
					$foldername = $fvalue;
	             	$folders[$db['EmailBox']][$num++];
				}
			}

			$msg_len = strlen($db['EmailMessage']) / 1024;
			$folders[$db['EmailBox']]['size'] += $msg_len;
			$folders[$db['EmailBox']]['size'] = sprintf('%2.1f', $folders[$db['EmailBox']]['size']);
		}
	}

	# Function to find the table names for the user account
	# The database tables are split up depending on the first or second
	# character of the username. This improves access to the DB tables
	function table_names($account)
	{
		global $pref;

        if ($pref['sql_type'] == 'mysql') {

	    	$type = null;

	    	if ( $pref['install_size'] == "large" )
			{
	        	if ( preg_match("/^([A-Z]{2})/i", $account, $match) )
					$type = $match[1];
	    	}

	    	if ( $pref['install_size'] == "normal" )
			{
	        	if ( preg_match("/^([A-Z]{1})/i", $account, $match ) );
					$type = $match[1];
	    	}

	    	if ( !$type ) $type = "other";

	    	$this->EmailDatabase = "EmailDatabase_$type";
	    	$this->EmailMessage  = "EmailMessage_$type";
	    	$this->EmailUIDL     = "EmailUIDL_$type";
	    	$this->UserSettings  = "UserSettings_$type";
	    	$this->SpamDB        = "SpamDB_$type";
	    	$this->MailSort      = "MailSort_$type";
	    	$this->Abook         = "Abook_$type";
	    	$this->Calendar      = "Calander_$type";
	    	$this->AbookGroup    = "AbookGroup_$type";
	    	$this->UserPgp	     = "UserPgp_$type";
        } else {
			$this->EmailDatabase = "EmailDatabase";
	    	$this->EmailMessage  = "EmailMessage";
	    	$this->EmailUIDL     = "EmailUIDL";
	    	$this->UserSettings  = "UserSettings";
	    	$this->SpamDB        = "SpamDB";
	    	$this->MailSort      = "MailSort";
	    	$this->Abook         = "Abook";
	    	$this->Calendar      = "Calander";
	    	$this->AbookGroup    = "AbookGroup";
	    	$this->UserPgp	     = "UserPgp";
		}
	    return;
	}

	function read_settings($account)
	{
		global $pref, $groups;

		$this->table_names($account);

		$account = $this->dbh->quote($account);
		$query = "select * from $this->UserSettings, Users where {$this->UserSettings}.Account=$account
				  and Users.Account=$account";

		$user = $this->dbh->getRow($query, null, DB_FETCHMODE_ASSOC);

		// Strip out table names if using sqlite
		$this->cleanKeys($user);

		// If the user has a group selected, enable/disable any features
		// set for the group account

		if($user['Ugroup'] && is_array($groups[$user['Ugroup']]))
		{
			// Toggle the permission for the user-account
			$pref = array_merge($pref, $groups[$user['Ugroup']]);
		}

	    return $user;
	}

	/**
	 * Display custom error message
	 *
	 * @param string $error
	 *
	 * @return bool
	 */
	function dberror($error)
	{
	    global $pref;

	    if (preg_match('/connect failed/i', $error))
	    {
	        echo "<h1>Database Connection Error</h1>
				 <font color='red'>$error</font>
				 <ul>
				    <li>Verify the SQL database is running</li>
				    <li>Check the SQL server is listening to the specified socket, IP address</li>
				    <li>Check the global configuration file ({$pref['install_dir']}/libs/Atmail/Config.php) for the correct database details</li>\n"
	        ."<li>Verify the database server is running correctly</li>\n"
	        ."<li>Verify the MySQL /etc/my.cnf file has the correct settings for the number of database connections </li>"
	        ."</ul>\n";
	exit;
            return 1;
	    }

	    return 0;

	}

	/**
	 * Quote an SQL query string
	 *
	 * @param string $string
	 * @return string
	 **/
	function quote($string)
	{
		return $this->dbh->quoteSmart($string);
	}

	/**
	 * Execute an SQL query that returns a single field result
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed $var on success, -1 on failure
	 **/
	function sqlgetfield($query, $data=array())
	{
		if (!is_array($data))
			settype($data, 'array');

		$res = $this->dbh->getOne($query, $data);

		if (DB::isError($res)) {
		    if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
		    return false;
		}

		return $res;
	}


	/**
	 * Alias for sqlgetfield()
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed $var on success, -1 on failure
	 **/
	function getvalue($query, $data=array())
	{
		if (!is_array($data))
			settype($data, 'array');

		$res = $this->dbh->getOne($query, $data);
		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return false;
		}
		return $res;
	}


	/**
	 * Execute an SQL query and return a numerically indexed array of results
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed array on success or error string on failure
	 **/
	function sqlarray($query, $data=null)
	{

		$res = $this->dbh->query($query, $data);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		$array = array();
		while ($row = $res->fetchRow()) {
			$array = array_merge($array, $row);
		}

		return $array;
	}

	/**
	 * Execute an SQL query and return via $array[] = $row ( faster )
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed array on success or error string on failure
	 **/
	function sqlarray_fast($query, $data=null)
	{

		$res = $this->dbh->query($query, $data);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		$array = array();
		while ($row = $res->fetchRow()) {
			$array[] = $row[0];
		}

		return $array;
	}


	/**
	 * Execute an SQL query and return a numerically indexed array of results
	 * Alias for sqlarray()
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed $array on success, -1 on failure
	 **/
	function idquery($query, $data=null)
	{
		return $this->sqlarray($query, $data);
	}

	/**
	 * Execute an SQL query and return an associative array (hash) of the results
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed $array on success, -1 on failure
	 **/
	function sqlhash($query, $data=null)
	{
		if (!is_array($data))
			settype($data, 'array');

		$res = $this->dbh->getRow($query, $data, DB_FETCHMODE_ASSOC);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		$this->cleanKeys($res);
		return $res;
	}

	/**
	 * Execute an SQL query and return a 2d associative array (hash) of the results
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed $array on success, -1 on failure
	 **/
	function sqlmultihash($query, $data=null)
	{
		if (!is_array($data))
			settype($data, 'array');
		$res = $this->dbh->query($query, $data);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		$array = array();
		while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
			$this->cleanKeys($row);
			$array[] = $row;
		}

		return $array;
	}


	/**
	 * Execute an SQL query and return an associative array (hash) of the results
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed $array on success, -1 on failure
	 **/
	function hashelement($query, $data=null)
	{
		if (!is_array($data))
			settype($data, 'array');
		$res = $this->dbh->query($query, $data);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		$array = array();
		while ($row = $res->fetchRow()) {
			$array[$row[0]] = $row[1];
		}
		return $array;
	}


	/**
	 * Execute an SQL query that does NOT return data e.g. INSERT, DELETE, UPDATE
	 *
	 * @param string $query the query string
	 * @param mixed $data the data to be inserted into the query string
	 * @return mixed 1 on success, PEAR_Error on failure
	 **/
	function sqldo($query, $data=null)
	{
		$data = $this->cleanData($data);
		
		$res = $this->dbh->query($query, $data);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		return 1;
	}


	function sqldoraw($query, $data=null)
	{
		$data = $this->cleanData($data);
		
		$res = $this->dbh->query($query, $data);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }

			$this->ping();

			$res = $this->dbh->query($query, $data);
			if (DB::isError($res)) {
                if ($this->debug) {
                    file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
    		    }
				return -1;
			}

		}

		return 1;
	}

    function ping()
    {
        global $pref;

        if ($pref['sql_type'] == 'mysql')
            return mysql_ping($this->dbh->connection);
    }


	function tables()
	{
		$res = $this->dbh->getListOf('tables');

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		return $res;
	}


	function &prepare($query)
	{
		$handle = $this->dbh->prepare($query);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		return $handle;
	}

	function execute_multiple($handle, $data)
	{
		$data = $this->cleanData($data);
		
		$res = $this->dbh->executeMultiple($handle, $data);

		if (DB::isError($res)) {
            if ($this->debug) {
                file_put_contents("php://stderr", "SQL Error = " . $res->getMessage() . " - " . $res->getUserInfo() . "\n");
		    }
			return -1;
		}

		return 1;
	}


	function cleanKeys(&$array)
	{
		if ($this->type == 'sqlite') {
			$tmp = array();
			foreach ($array as $k => $v) {
				$k = preg_replace('/^[a-z0-9_]+\./i', '', $k);
				$tmp[$k] = $v;
			}
			$array = $tmp;
		}
	}


	function makedatetime($factor, $interval, $when='NOW')
	{
		$when = strtoupper($when);

		if ($this->type == 'sqlite') {
			if ($factor >= 0) {
				$sign = '+';
			} else {
				$sign = '';
			}
			$date = "datetime({$this->$when}, '$sign$factor $interval')";
		} else {
			$date = "DATE_SUB({$this->$when}, INTERVAL $factor $interval)";
		}

		return $date;
	}


	function optimize_table($table)
	{
		$table = Filter::cleanSqlFieldNames($table);

		if ($this->type == 'mysql') {
			$this->dbh->query("optimize table $table");
		}
	}


	function unix_timestamp($field)
	{
		if ($this->type == 'sqlite') {
			return "strftime('%s', $field)";
		}

		return "UNIX_TIMSTAMP($field)";
	}

	/**
	 * Emulate MYSQL DATE_ADD() function
	 *
	 * @param string $date
	 * @param string $modify
	 * @param bool $returnSQL true: Return the SQL query string
	 *                        false: Return the result from the query
	 * @return mixed
	 */
	function date_add($date, $modify, $returnSQL=false)
	{
		if ($this->type == 'sqlite') {
			if ($returnSQL) {
				return "datetime('$date', '+$modify')";

			}

			return $this->dbh->getOne("select datetime('$date', '+$modify')");

		} else {
			if ($returnSQL) {
				return "DATE_ADD('$date', INTERVAL $modify)";
			}

			return $this->dbh->getOne("select DATE_ADD('$date', INTERVAL $modify)");
		}
	}


	/**
	 * Emulate MYSQL DATE_SUB() function
	 *
	 * @param string $date
	 * @param string $modify
	 * @param bool $returnSQL true: Return the SQL query string
	 *                        false: Return the result from the query
	 * @return mixed
	 */
	function date_sub($date, $modify, $returnSQL=false)
	{
		$date = $this->parse_date($date);

		if ($this->type == 'sqlite') {
			if ($returnSQL) {
				return "datetime($date, '-$modify')";
			}
			return $this->dbh->getOne("select datetime($date, '-$modify')");
		} else {
			if ($returnSQL) {
				return "DATE_SUB($date, INTERVAL $modify)";
			}
			return $this->dbh->getOne("select DATE_SUB('$date', INTERVAL $modify)");
		}
	}

	function parse_date($date)
	{
		if ($date != 'NOW') {
			return "'$date'";
		}

		if ($this->type == 'sqlite') {
			return "'now'";
		}

		return 'NOW()';
	}

	/**
	 * Emulates the MySQL CONCAT() function
	 * by concatenating all arguments into
	 * one string.
	 *
	 * This function is registered with sqlite as
	 * 'CONCAT' so CONCAT() can be used in any query.
	 * It behaves just as MySQL CONCAT()
	 *
	 * @return string
	 */
	function concat()
	{
		$string = '';
		foreach (func_get_args() as $arg) {
			$string .= $arg;
		}

		return $string;
	}

	
	function cleanData($data)
	{
		// hack to fix issue with sqlite and php4
		if (!is_null($data) && $this->type == 'sqlite') {
			
			if (is_array($data)) {
				foreach ($data as $k => $v) {
					if (empty($v)) {
						$data[$k] = NULL;
					}
				}
			} elseif (empty($data)) {
				$data = NULL;
			}
		}
		
		return $data;
	}
	
	
}//end SQL class

?>
