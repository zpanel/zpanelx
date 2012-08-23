<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Share_Backend_PDO extends afterlogic_DAV_Share_Backend_Abstract
{

    /**
     * Reference to PDO connection 
     * 
     * @var PDO 
     */
    protected $pdo;

    protected $prefix;
	
    /**
     * Creates the backend object. 
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     * 
     * @param string $filename
     * @param string $tableName The PDO table name to use 
     * @return void
     */
    public function __construct(PDO $pdo, $prefix = '') {

        $this->pdo = $pdo;
        $this->prefix = $prefix;
    } 
	
	public function UpdateShare($sCalendarId, $FromUser, $ToUser, $Mode)
	{
		// Calendar id
		$stmt = $this->pdo->prepare(
			'SELECT id FROM '.$this->prefix.afterlogic_DAV_Server::Tbl_Calendars.' WHERE uri = ? AND principaluri = ?');
		
		$stmt->execute(array(basename($sCalendarId), 'principals/' . $FromUser));
		$result = $stmt->fetchAll();
		if (!$result)
		{
			return false;
		}       
		$calendarId = $result[0]['id'];
		$calendarUri = 'delegation/'.$calendarId.'/calendar';
		
		$stmt = $this->pdo->prepare('SELECT id FROM '.$this->prefix.afterlogic_DAV_Server::Tbl_Principals.' WHERE uri = ?');
		$stmt->execute(array('principals/' . $ToUser));
		$result = $stmt->fetchAll();
		if (!$result)
		{
			return false;
		}       
		$principalId = $result[0]['id'];
		
		$stmt = $this->pdo->prepare(
				'DELETE FROM '.$this->prefix.afterlogic_DAV_Server::Tbl_Delegates.' WHERE calendarid=? AND principalid=?'
				);
		$stmt->execute(array($calendarId, $principalId));
		
		if ($Mode != 0)
		{
			$query = 'INSERT INTO '.$this->prefix.afterlogic_DAV_Server::Tbl_Delegates.' (calendarid, principalid, mode) SELECT ?, '.$this->prefix.afterlogic_DAV_Server::Tbl_Principals.'.id, ? FROM '.$this->prefix.afterlogic_DAV_Server::Tbl_Principals.' WHERE uri = ?';
			
			$stmt = $this->pdo->prepare($query);
			$stmt->execute(array($calendarId, $Mode, 'principals/' . $ToUser));
		}
		return $calendarUri;
	}
	
	public function DeleteAllUsersShares($ToUser)
	{
		$stmt = $this->pdo->prepare('SELECT id FROM '.$this->prefix.afterlogic_DAV_Server::Tbl_Principals.' WHERE uri = ?');
		$stmt->execute(array('principals/' . $ToUser));
		$result = $stmt->fetchAll();
		if (!$result)
		{
			return false;
		}       
		$principalId = $result[0]['id'];
		
		$stmt = $this->pdo->prepare(
				'DELETE FROM '.$this->prefix.afterlogic_DAV_Server::Tbl_Delegates.' WHERE principalid=?'
				);
		$stmt->execute(array($principalId));		
	}
}