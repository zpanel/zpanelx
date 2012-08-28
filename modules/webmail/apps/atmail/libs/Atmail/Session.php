<?php
// +----------------------------------------------------------------+
// | Session.php													|
// +----------------------------------------------------------------+
// | Function: implement SQL based session handling.				|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: March 2006												|
// +----------------------------------------------------------------+

require_once('header.php');

require_once('SQL.php');
$sql = new SQL;

/**
 * Opens a session
 *
 * Since we are using an sql database, we simply check
 * that the SQL object exists (for DB connection & query)
 *
 * @param string $path only here for compatability reasons
 * @param string $name only here for compatability reasons
 * @global object $sql the object we use to talk to the DB
 * @return bool true if $sql is SQL object, false otherwise
 */
function sessionOpen($path, $name)
{
	global $sql;
	return is_a($sql, 'SQL');
}

/**
 * Close the session
 *
 * Since we are using a DB we just return true here
 * @return bool true
 */
function sessionClose()
{
	return true;
}

/**
 * Read the session data in from the database
 *
 * @param string $id the session id
 * @return string the serialised session data or an empty string
 */
function sessionRead($id)
{
	global $sql, $pref;
	if (!is_a($sql, 'SQL')) return '';

	$time = time();
	$query = "SELECT SessionData FROM UserSession WHERE SessionID= ? AND LastLogin > ($time - {$pref['session_timeout']})";
	$value = $sql->getvalue($query, $id);
	return (!PEAR::isError($value))? $value : '';
}

/**
 * Write the session data to the database
 *
 * @param string $id the session id
 * @param string $sess_data the serialised session data
 * @return bool
 */
function sessionWrite($id, $sess_data)
{
	global $sql;
	if (!is_a($sql, 'SQL'))
	{
		// PHP5 destroys objects before this func is called
		// so we need to recreate the SQL object
		$path = dirname(dirname(dirname(__FILE__)));
		set_include_path($path . PATH_SEPARATOR . get_include_path());
		$sql = new SQL;
	}

	// Make new connection of old done away
	if (is_a($sql, 'SQL') && !$sql->ping()) {
		unset($sql);
		$sql = new SQL();
	}
	$time = time();
	$query = "UPDATE UserSession SET SessionData = ? WHERE SessionID = ?";
	$data = array($sess_data, $id);
	if (PEAR::isError($sql->sqldo($query, $data)))
		return false;

	return true;
}

/**
 * Destroy the session data
 *
 * @param string $id the session id
 * @return bool
 */
function sessionDestroy($id)
{
	global $sql;
	if (!is_a($sql, 'SQL')) return false;

	$query = "UPDATE UserSession SET SessionData = '', SessionID = '' WHERE SessionID = ?";

	if (PEAR::isError($sql->sqldo($query, $id)))
		return false;

	return true;
}

/**
 * Garbage collection
 *
 * The probability that this function is called is determined
 * by session.gc_probability. It is called to destroy old session data
 *
 * @param int $maxlife the maximum life of a session. We do not use this
 * variable but $pref['session_timeout'] from Config.php.
 *
 * @return bool true
 */
function sessionGC($maxlife)
{
	global $sql, $pref;
	if (!is_a($sql, 'SQL')) return false;

	$timeout = time() - $pref['session_timeout'];

	$query = "UPDATE UserSession SET SessionData = '', SessionID = '' WHERE LastLogin < ?";
	if (PEAR::isError($sql->sqldo($query, $timeout)))
		return false;

	return true;
}

// set the php.ini variable session.save_handler to 'user'
ini_set('session.save_handler', 'user');

// tell PHP session management to use our user defined functions
session_set_save_handler('sessionOpen', 'sessionClose', 'sessionRead', 'sessionWrite', 'sessionDestroy', 'sessionGC');

session_name('atmail');

?>