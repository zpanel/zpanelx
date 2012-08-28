<?php

require_once('header.php');
require_once('Global.php');
require_once('Calendar.php');


class MailOS
{
	
	function MailOS($args=array())
	{
	    foreach ($args as $k =>$v) {
	        $this->$k = $v;
	    }
	    
	    global $atmail;
	    $this->db =& $atmail->db;
	}
	
	
	function email_assign_users()
	{
		$accounts = $this->db->sqlarray("select Account from Users");
	
		$v = "<option value=''>Select Assign</option>";
		$v .= "<option value='All'>All Users</option>";
	
		foreach ($accounts as $account)	{
			$realName = $this->db->sqlgetfield("select concat(FirstName, ' ', LastName) 
			                            from Users where Account=?", array($account));
			$v .= "<option value='$account'>$realName</option>\n";
		}
	
		return $v;
	}
	
	function emailthread_update_status($id, $status)
	{
		return $this->db->sqldo("update {$this->db->EmailDatabase} set EmailStatus=? where id=?", array($status, $id));
	}
	
	function emailthread_update_assign($id, $status)
	{
		return $this->db->sqldo("update {$this->db->EmailDatabase} set EmailAssign=? where id=?", array($status, $id));
	}
	
	function select_status_field($field)
	{
		global $atmail;
		
		if ($field == 'Urgent' && !$atmail->XUL) {
			$field = "<font color='Red'>Urgent</font>";
		}
		
		elseif ($field == 'Complete' && !$atmail->XUL) {
			$field = "<font color='blue'>Complete</font>";
		}
		
		return $field;
	}
	
	
	function mailos_thread($id, $uidl)
	{
	    $id = $this->cleanId($id);
	    $uidl = $this->cleanId($uidl);
	
	    $this->db->sqldo( "INSERT INTO MailThreads (MessageID, ThreadID, Account) 
	                       VALUES(?, ?, ?)", array($uidl, $id, $this->Account));
	}
	
	function mailos_list_threads($id)
	{
        $id = $this->cleanId($id);
		return $this->db->sqlarray( "select ThreadID from MailThreads where MessageID=?", array($id) );
	}
	
	function mailos_followup($id, $subject, $from, $days, $account)
	{
		$localtime = time();
		
		$followup = <<<EOF
		Calendar alert to follow up via email:
		
		From: $from
		Subject = $subject
		MessageID = ($id)
		
		Added = $localtime
		Days = $days
EOF;
		
		$title = "Follow up $from ($subject)";
		
		// Starts today
		$start = time();
	
		// Due in X days from today
		$due = $start + ($days * 86400);
		$end = $start + ($days * 86400) + 3600;
		
		$startdate = strftime("%Y-%m-%d %R:00", time($due));
		$enddate =  strftime("%Y-%m-%d %R:00", time($end));
		
		list($username, $pop3host) = explode('@', $account);
		
		$cal = new Calendar(
			array(
				'username' => $username,
				'pop3host' => $pop3host,
				'userfrom' => $account
			)
		);
		
		// Add to our own Personal calendar
		$cal->addrecord(
			array(
				'UserTo'     => $account,
				'UserFrom'   => $account,
				'Title'      => $title,
				'CalMessage' => $followup,
				'Type'	     => 'Pending',
				'Importance' => '1',
				'Alert'      => '',
				'DateStart'  => "$startdate",
				'DateEnd'    => "$enddate",
				'Permission' => '1'
			)
		);
	}
	
	function cleanId($id)
	{
		$id = preg_replace('/:.*/', '', $id);
	    $id = str_replace(array('cur/', 'new/'), '', $id);
	    return $id;
	}

}