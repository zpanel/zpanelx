<?php
// +----------------------------------------------------------------+
// | Log.php  														|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: Febuary 2005												|
// +----------------------------------------------------------------+

require_once('header.php');

define ('QUERY_ERROR', 'Error in SQL Query');

require_once('Config.php');

class Log
{

	var $account;
	var $errtype;
	var $msg;
	var $logtype;
	var $db;
	var $tables = array('Error', 'Login', 'RecvMail', 'SMS', 'SendMail', 'Spam', 'Virus');


	function Log($args=null)
	{
		if (is_array($args))
		{
			foreach ($args as $k => $v)
				$this->$k = $v;
		}

		global $atmail, $pref;

		// can't establish db instance
		if (is_a($atmail->db, 'SQL'))
			$this->db =& $atmail->db;
		else
		{
			require_once('libs/Atmail/SQL.php');
			$this->db = new SQL;
			$this->db->table_names();
		}
	}

	function write_log($type, $msg=null)
	{
		if ($this->is_log_type($type))
			$this->errtype = $type;
		else
            return -1;

		if (!empty($msg)) {
			$this->msg = $msg;
		}

		if ($this->Account) {
			$account = $this->Account;
		} else {
			$account = 'System';
		}
		
		$query = "INSERT INTO Log_{$this->errtype} (Account, LogMsg, LogDate) VALUES (?, ?, {$this->db->NOW})";
		$data =  array($account, $this->msg);
		$this->db->sqldo($query, $data);
	}

	// Raw log into the database for the log-daemon, use mysql_ping() if insert fails
	function log_raw($type, $msg=null)
	{
	    return $this->write_log($type, $msg);

        if ($type)
			$this->errtype = $type;
		else
            return -1;

		if (!empty($msg)) {
			$this->msg = $msg;
		}

		if ($this->Account) {
			$account = $this->Account;
		} else {
			$account = 'System';
		}
		
		$query = "INSERT INTO Log_{$this->errtype} (Account, LogMsg, LogDate) VALUES (?, ?, {$this->db->NOW})";
		$data =  array($account, $this->msg);
		$this->db->sqldo($query, $data);
	}

	function logcheck($type, $ip=null)
	{
		$time = date('Ymd');

		if (!$this->is_log_type($type))
            return -1;

		if (isset($ip))
		{
			$ip = '%' . $ip . '%';

		    //if ($ip && $this->account && !preg_match('/New Account/i', $ip) ) {

		    //$query = sprintf("select count(id) from Log_$type where LogMsg like '%s' and LogDate > DATE_SUB(NOW(), INTERVAL 24 HOUR) and Account='%s'", $ip, $this->Account);
		    //$res = mysql_query($query);
		    //if ($res === false)
            //    return -1;

            //return mysql_result($res, 0);
		//   } else	{
		    $date_sub = $this->db->date_sub('NOW', '24 HOUR', true);
            return $this->db->getvalue( "select count(id) from Log_$type where LogMsg like ? and LogDate > $date_sub and Account = ?", array($ip, $this->Account) );
		 //	}

		}

//			$query = "select id from Log_$type where LogMsg like '%$ip%' and LogDate like '$time?%' ";
//			return $this->db->getvalue($query);
//		}

		return -2;
	}

	function logError($message, $file, $line, $type)
	{
		global $pref;

		if (!$pref['error_log'])
			return;

		$date = date('D M d H:i:s Y');
		if ($fh = fopen($pref['error_log'], 'a'))
		{
			fwrite($fh, "[$date] $type: $message in $file on line $line\n");
			fclose($fh);
		}
	}

	function addrelay($ip)
	{
		global $pref;
        /*
		if (!preg_match('/\d+\.\d+\.\d+\.\d+/', $ip))
            return 1;

		// Delete any entries older than 1 hour from the "user" table
		$query = sprintf("delete from MailRelay where Account='User' and DateAdded < DATE_SUB(NOW(), INTERVAL %d MINUTE)", $pref['smtp_popimaprelay_timeout']);
		mysql_query($query);

		// Add the new IP into the database, only if we do not exist
		$ip = mysql_real_escape($ip);
		$query = sprintf("select IPaddress from MailRelay where IPaddress='%s'", $ip);
		$res = mysql_query($query);
		if ($res === false)
            return -1;

        if (mysql_result($res, 0)) {
            $query = sprintf("INSERT INTO MailRelay (IPaddress, Account, DateAdded) VALUES('%s', 'User', NOW() )", $ip);
            $res = mysql_query($query);
		} else {
            // Update the DateAdded to now, so user always in sync to relay
            $query = sprintf("UPDATE MailRelay set DateAdded=NOW() where IPaddress='%s' and Account='User'", $ip);
            $res2 = mysql_query($query);
		}
        */
		return;
	}

	function updatelastlogin($user)
	{
		// Return if no user supplied or account invalid
		if (!strpos($user, '@'))
            return -1;

		$time = time();

		// Update the users last login date
		//$query = sprintf("update UserSession set LastLogin='%d' where Account='%s'", $time, $user);
        //mysql_query($query);
        $this->db->sqldo("update UserSession set LastLogin=? where Account=?", array($time, $user));
		return 1;
	}

	/**
	 * Check for valid Log table name
	 *
	 * @param string $name
	 * @return bool  True if valid, false if not
	 */
	function is_log_type($name)
	{
	    return in_array($name, $this->tables);
	}

}//end Log

?>
