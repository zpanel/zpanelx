<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Cache_Backend_PDO extends afterlogic_DAV_Cache_Backend_Abstract
{

    /**
     * Reference to PDO connection 
     * 
     * @var PDO 
     */
    protected $pdo;

    /**
     * PDO table name we'll be using  
     * 
     * @var string
     */
    protected $tableName;
	
    protected $calendarTableName;

	protected $delegatesTableName;
	
	protected $principalsTableName;

	/**
     * Creates the backend object. 
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     * 
     * @param string $filename
     * @param string $tableName The PDO table name to use 
     * @return void
     */
    public function __construct(PDO $pdo, $dBPrefix = '', 
			$tableName = afterlogic_DAV_Server::Tbl_Cache,
			$calendarTableName = afterlogic_DAV_Server::Tbl_Calendars, 
			$delegatesTableName = afterlogic_DAV_Server::Tbl_Delegates, 
			$principalsTableName = afterlogic_DAV_Server::Tbl_Principals) 
	{
        $this->pdo = $pdo;
        $this->tableName = $dBPrefix.$tableName;
        $this->calendarTableName = $dBPrefix.$calendarTableName;
        $this->delegatesTableName = $dBPrefix.$delegatesTableName;
        $this->principalsTableName = $dBPrefix.$principalsTableName;
    } 
	
	public function getRemindersCache($type = 1, $start = null, $end = null)
	{
		$fields = array();
        $fields[] = 'id';
        $fields[] = 'user';
        $fields[] = 'calendaruri';
        $fields[] = 'type';
        $fields[] = 'time';
        $fields[] = 'starttime';
        $fields[] = 'eventid';

		$values = array();
		$values[] = (int) $type;

		$timeFilter = '';
		if ($start != null && $end != null)
		{
			$timeFilter = ' and time > ? and time < ?';
			$values[] = (int) $start;
			$values[] = (int) $end;
		}
		
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->prepare('SELECT ' . $fields . ' FROM `'.$this->tableName.
				'` WHERE type = ?' . $timeFilter); 
		
        $stmt->execute($values);
		
        $cache = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
            $cache[] = array(
                'id' => $row['id'],
                'user' => $row['user'],
                'calendaruri' => $row['calendaruri'],
                'type' => $row['type'],
                'time' => $row['time'],
                'starttime' => $row['starttime'],
                'eventid' => $row['eventid']
            );
		}		
		return $cache;
	}
	
	public function createRemindersCache($user, $calendarUri, $type = 0, $time = null, $startTime = null, $eventid = null)
	{
		$values = $fieldNames = array();
        $fieldNames[] = 'user';
		$values[':user'] = $user;

		$fieldNames[] = 'calendaruri';
		$values[':calendaruri'] = $calendarUri;

		$fieldNames[] = 'type';
		$values[':type'] = (int) $type;

		if ($time != null)
		{
			$fieldNames[] = 'time';
			$values[':time'] = (int) $time;
		}

		if ($startTime != null)
		{
			$fieldNames[] = 'starttime';
			$values[':starttime'] = (int) $startTime;
		}

		$fieldNames[] = 'eventid';
		$values[':eventid'] = $eventid;

		$stmt = $this->pdo->prepare("INSERT INTO `".$this->tableName."` (".implode(', ', $fieldNames).") VALUES (".implode(', ',array_keys($values)).")");
        $stmt->execute($values);

        return $this->pdo->lastInsertId();		
	}
	
	public function updateRemindersCache($id, $user, $calendarUri, $type, $time, $startTime, $eventid = null)
	{
        $values = array(
            $user,
            $calendarUri,
            $type,
            $time,
            $startTime,
            $eventid,
            $id,
        );
        
		$valuesSql = array(
            'user = ?',
            'calendaruri = ?',
            'type = ?',
            'time = ?',
            'starttime = ?',
            'eventid = ?',
        );
		
        $stmt = $this->pdo->prepare('UPDATE `' . $this->tableName . '` SET (' . implode(', ',$valuesSql) . ') WHERE id = ?');
        try
		{
			$stmt->execute($values);
		}
		catch(Exception $ex)
		{
			CApi::Log('updateRemindersCache: ');
			CApi::Log($ex->getTraceAsString());
		}
		
        return true; 		
	}

	public function deleteRemindersCache($calendarUri)
	{
        $stmt = $this->pdo->prepare('DELETE FROM `'.$this->tableName.'` WHERE calendaruri = ?');
        $stmt->execute(array($calendarUri));
	}
	
	public function getDelegates($calendarUri)
	{
        $fields[] = $this->calendarTableName.'.id';
        $fields[] = $this->delegatesTableName.'.principalid';

        // Making fields a comma-delimited list 
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->prepare('SELECT ' . $fields . ' FROM `'.$this->calendarTableName.'`, `'.$this->delegatesTableName.'` 
		WHERE '.$this->delegatesTableName.'.calendarid = '.$this->calendarTableName.'.id AND '.$this->calendarTableName.'.uri = ?'); 
        $stmt->execute(array($calendarUri));

        $calendars = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			$stmt1 = $this->pdo->prepare('SELECT uri FROM `'.$this->principalsTableName.'`
			WHERE id = ?'); 
			$stmt1->execute(array($row['principalid']));
			$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
			if ($row1)
			{
				$calendar = array(
					'uri' => 'delegation/' . $row['id'] . '/calendar',
					'user' => basename($row1['uri'])
				);
			}        
            $calendars[] = $calendar;

        }
        return $calendars;
	}
}