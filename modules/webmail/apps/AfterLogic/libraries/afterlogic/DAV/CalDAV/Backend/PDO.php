<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_CalDAV_Backend_PDO extends Sabre_CalDAV_Backend_Abstract {

    /**
     * pdo 
     * 
     * @var PDO
     */
    protected $pdo;

	public $sDbPrefix;
    /**
     * The table name that will be used for calendars 
     * 
     * @var string 
     */
    public $calendarTableName;

    /**
     * The table name that will be used for calendar objects  
     * 
     * @var string 
     */
    public $calendarObjectTableName;
	
	public $delegatesTableName;

    /**
     * List of CalDAV properties, and how they map to database fieldnames
     *
     * Add your own properties by simply adding on to this array
     * 
     * @var array
     */
    public $propertyMap = array(
        '{DAV:}displayname'                          => 'displayname',
        '{urn:ietf:params:xml:ns:caldav}calendar-description' => 'description',
        '{urn:ietf:params:xml:ns:caldav}calendar-timezone'    => 'timezone',
        '{http://apple.com/ns/ical/}calendar-order'  => 'calendarorder',
        '{http://apple.com/ns/ical/}calendar-color'  => 'calendarcolor',
    );

    /**
     * Creates the backend 
     * 
     * @param PDO $pdo 
     */
    public function __construct(PDO $pdo, $dBPrefix, 
			$calendarTableName = afterlogic_DAV_Server::Tbl_Calendars, 
			$calendarObjectTableName = afterlogic_DAV_Server::Tbl_Calendarobjects, 
			$delegatesTableName = afterlogic_DAV_Server::Tbl_Delegates) {

        $this->pdo = $pdo;
		$this->sDbPrefix = $dBPrefix;
        $this->calendarTableName = $dBPrefix.$calendarTableName;
        $this->calendarObjectTableName = $dBPrefix.$calendarObjectTableName;
		$this->delegatesTableName = $delegatesTableName;

    }

    /**
     * Returns a list of calendars for a principal.
     *
     * Every project is an array with the following keys:
     *  * id, a unique id that will be used by other functions to modify the
     *    calendar. This can be the same as the uri or a database key.
     *  * uri, which the basename of the uri with which the calendar is 
     *    accessed.
     *  * principalUri. The owner of the calendar. Almost always the same as
     *    principalUri passed to this method.
     *
     * Furthermore it can contain webdav properties in clark notation. A very
     * common one is '{DAV:}displayname'. 
     *
     * @param string $principalUri 
     * @return array 
     */
    public function getCalendarsForUser($principalUri) {

        $fields = array_values($this->propertyMap);
        $fields[] = 'id';
        $fields[] = 'uri';
        $fields[] = 'ctag';
        $fields[] = 'components';
        $fields[] = 'principaluri';

        // Making fields a comma-delimited list 
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->prepare("SELECT " . $fields . " FROM `".$this->calendarTableName."` WHERE principaluri = ?"); 
        $stmt->execute(array($principalUri));

        $calendars = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $components = explode(',',$row['components']);

            $calendar = array(
                'id' => $row['id'],
                'uri' => $row['uri'],
                'principaluri' => $row['principaluri'],
                '{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $row['ctag']?$row['ctag']:'0',
                '{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet($components),
            );
        

            foreach($this->propertyMap as $xmlName=>$dbName) {
                $calendar[$xmlName] = $row[$dbName];
            }

            $calendars[] = $calendar;

        }

        return $calendars;
    }
	
	public function getDeligatedCalendarsForUser($principalId)
	{
        $fields = array_values($this->propertyMap);
        $fields[] = $this->calendarTableName.'.id';
        $fields[] = $this->calendarTableName.'.uri';
        $fields[] = $this->calendarTableName.'.ctag';
        $fields[] = $this->calendarTableName.'.components';
        $fields[] = $this->calendarTableName.'.principaluri';
        $fields[] = $this->delegatesTableName.'.mode';

        // Making fields a comma-delimited list 
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->prepare('SELECT ' . $fields . ' FROM `'.$this->calendarTableName.'`, `'.$this->delegatesTableName.'` 
		WHERE '.$this->delegatesTableName.'.calendarid = '.$this->calendarTableName.'.id AND '.$this->delegatesTableName.'.principalid = ?'); 
        $stmt->execute(array($principalId));

        $calendars = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $components = explode(',',$row['components']);

            $calendar = array(
                'id' => $row['id'],
                'uri' => $row['uri'],
                'principaluri' => $row['principaluri'],
                '{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $row['ctag']?$row['ctag']:'0',
                '{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet($components),
				'mode' => $row['mode'],
            );
        
            foreach($this->propertyMap as $xmlName=>$dbName) {
                $calendar[$xmlName] = $row[$dbName];
            }

            $calendars[] = $calendar;

        }

        return $calendars;
	}
	
	/**
     * Returns a calendar.
     *
     * @param string $calendarId 
     * @return array 
     */
    public function getCalendar($calendarId) {

        $fields = array_values($this->propertyMap);
        $fields[] = 'id';
        $fields[] = 'uri';
        $fields[] = 'ctag';
        $fields[] = 'components';
        $fields[] = 'principaluri';

        // Making fields a comma-delimited list 
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->prepare("SELECT " . $fields . " FROM `".$this->calendarTableName."` WHERE id = ?"); 
        $stmt->execute(array($calendarId));

        $calendar = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $components = explode(',',$row['components']);

            $calendar = array(
                'id' => $row['id'],
                'uri' => $row['uri'],
                'principaluri' => $row['principaluri'],
                '{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $row['ctag']?$row['ctag']:'0',
                '{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet($components),
            );
        

            foreach($this->propertyMap as $xmlName=>$dbName) {
                $calendar[$xmlName] = $row[$dbName];
            }
        }
		
		return $calendar;
    }

    /**
     * Creates a new calendar for a principal.
     *
     * If the creation was a success, an id must be returned that can be used to reference
     * this calendar in other methods, such as updateCalendar
     *
     * @param string $principalUri
     * @param string $calendarUri
     * @param array $properties
     * @return mixed
     */
    public function createCalendar($principalUri, $calendarUri, array $properties) {

		$fieldNames = array(
            'principaluri',
            'uri',
            'ctag',
        );
        $values = array(
            ':principaluri' => $principalUri,
            ':uri'          => $calendarUri,
            ':ctag'         => 1,
        );

        // Default value
        $sccs = '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set';
        $fieldNames[] = 'components';
        if (!isset($properties[$sccs])) {
            $values[':components'] = 'VEVENT,VTODO';
        } else {
            if (!($properties[$sccs] instanceof Sabre_CalDAV_Property_SupportedCalendarComponentSet)) {
                throw new Sabre_DAV_Exception('The ' . $sccs . ' property must be of type: Sabre_CalDAV_Property_SupportedCalendarComponentSet');
            }
            $values[':components'] = implode(',',$properties[$sccs]->getValue());
        }

        foreach($this->propertyMap as $xmlName=>$dbName) {
            if (isset($properties[$xmlName])) {

                $myValue = $properties[$xmlName];
                $values[':' . $dbName] = $properties[$xmlName];
                $fieldNames[] = $dbName;
            }
        }

        $stmt = $this->pdo->prepare("INSERT INTO `".$this->calendarTableName."` (".implode(', ', $fieldNames).") VALUES (".implode(', ',array_keys($values)).")");
        $stmt->execute($values);

        return $this->pdo->lastInsertId();

    }

    /**
     * Updates a calendars properties 
     *
     * The properties array uses the propertyName in clark-notation as key,
     * and the array value for the property value. In the case a property
     * should be deleted, the property value will be null.
     *
     * This method must be atomic. If one property cannot be changed, the
     * entire operation must fail.
     *
     * If the operation was successful, true can be returned.
     * If the operation failed, false can be returned.
     *
     * Deletion of a non-existant property is always succesful.
     *
     * Lastly, it is optional to return detailed information about any
     * failures. In this case an array should be returned with the following
     * structure:
     *
     * array(
     *   403 => array(
     *      '{DAV:}displayname' => null,
     *   ),
     *   424 => array(
     *      '{DAV:}owner' => null,
     *   )
     * )
     *
     * In this example it was forbidden to update {DAV:}displayname. 
     * (403 Forbidden), which in turn also caused {DAV:}owner to fail
     * (424 Failed Dependency) because the request needs to be atomic.
     *
     * @param string $calendarId
     * @param array $properties
     * @return bool|array 
     */
    public function updateCalendar($calendarId, array $properties) {

        $newValues = array();
        $result = array(
            200 => array(), // Ok
            403 => array(), // Forbidden
            424 => array(), // Failed Dependency
        );

        $hasError = false;

        foreach($properties as $propertyName=>$propertyValue) {

            // We don't know about this property. 
            if (!isset($this->propertyMap[$propertyName])) {
                $hasError = true;
                $result[403][$propertyName] = null;
                unset($properties[$propertyName]);
                continue;
            }

            $fieldName = $this->propertyMap[$propertyName];
            $newValues[$fieldName] = $propertyValue;
                
        }

        // If there were any errors we need to fail the request
        if ($hasError) {
            // Properties has the remaining properties
            foreach($properties as $propertyName=>$propertyValue) {
                $result[424][$propertyName] = null;
            }

            // Removing unused statuscodes for cleanliness
            foreach($result as $status=>$properties) {
                if (is_array($properties) && count($properties)===0) unset($result[$status]);
            }

            return $result;

        }

        // Success

        // Now we're generating the sql query.
        $valuesSql = array();
        foreach($newValues as $fieldName=>$value) {
            $valuesSql[] = $fieldName . ' = ?';
        }
        $valuesSql[] = 'ctag = ctag + 1';

        $stmt = $this->pdo->prepare("UPDATE `" . $this->calendarTableName . "` SET " . implode(', ',$valuesSql) . " WHERE id = ?");
        $newValues['id'] = $calendarId; 
        $stmt->execute(array_values($newValues));

        return true; 

    }

    /**
     * Delete a calendar and all it's objects 
     * 
     * @param string $calendarId 
     * @return void
     */
    public function deleteCalendar($calendarId) {

        $calendar = $this->getCalendar($calendarId);
		
		if (isset($calendar))
		{
			$stmt = $this->pdo->prepare('DELETE FROM `'.$this->calendarObjectTableName.'` WHERE calendarid = ?');
			$stmt->execute(array($calendarId));

			$stmt = $this->pdo->prepare('DELETE FROM `'.$this->calendarTableName.'` WHERE id = ?');
			$stmt->execute(array($calendarId));
			
			$stmt = $this->pdo->prepare('DELETE FROM `'.$this->delegatesTableName.'` WHERE calendarid = ?');
			$stmt->execute(array($calendarId));
		}
    }

    /**
     * Returns all calendar objects within a calendar object.
     *
     * Every item contains an array with the following keys:
     *   * id - unique identifier which will be used for subsequent updates
     *   * calendardata - The iCalendar-compatible calnedar data
     *   * uri - a unique key which will be used to construct the uri. This can be any arbitrary string.
     *   * lastmodified - a timestamp of the last modification time
     *   * etag - An arbitrary string, surrounded by double-quotes. (e.g.: 
     *   '  "abcdef"')
     *   * calendarid - The calendarid as it was passed to this function.
     *
     * Note that the etag is optional, but it's highly encouraged to return for 
     * speed reasons.
     *
     * The calendardata is also optional. If it's not returned 
     * 'getCalendarObject' will be called later, which *is* expected to return 
     * calendardata.
     * 
     * @param string $calendarId 
     * @return array 
     */
    public function getCalendarObjects($calendarId) {

        $stmt = $this->pdo->prepare('SELECT * FROM `'.$this->calendarObjectTableName.'` WHERE calendarid = ?');
        $stmt->execute(array($calendarId));
        return $stmt->fetchAll();

    }

    /**
     * Returns information from a single calendar object, based on it's object
     * uri.
     *
     * The returned array must have the same keys as getCalendarObjects. The 
     * 'calendardata' object is required here though, while it's not required 
     * for getCalendarObjects.
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @return array 
     */
    public function getCalendarObject($calendarId,$objectUri) {

        $stmt = $this->pdo->prepare('SELECT * FROM `'.$this->calendarObjectTableName.'` WHERE calendarid = ? AND uri = ?');
        $stmt->execute(array($calendarId, $objectUri));
        return $stmt->fetch();

    }

    /**
     * Creates a new calendar object. 
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @param string $calendarData 
     * @return void
     */
    public function createCalendarObject($calendarId,$objectUri,$calendarData) {

        $stmt = $this->pdo->prepare('INSERT INTO `'.$this->calendarObjectTableName.'` (calendarid, uri, calendardata, lastmodified) VALUES (?,?,?,?)');
        $stmt->execute(array($calendarId,$objectUri,$calendarData,time()));
        $stmt = $this->pdo->prepare('UPDATE `'.$this->calendarTableName.'` SET ctag = ctag + 1 WHERE id = ?');
        $stmt->execute(array($calendarId));

    }

    /**
     * Updates an existing calendarobject, based on it's uri. 
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @param string $calendarData 
     * @return void
     */
    public function updateCalendarObject($calendarId,$objectUri,$calendarData) {

        $stmt = $this->pdo->prepare('UPDATE `'.$this->calendarObjectTableName.'` SET calendardata = ?, lastmodified = ? WHERE calendarid = ? AND uri = ?');
        $stmt->execute(array($calendarData,time(),$calendarId,$objectUri));
        $stmt = $this->pdo->prepare('UPDATE `'.$this->calendarTableName.'` SET ctag = ctag + 1 WHERE id = ?');
        $stmt->execute(array($calendarId));

    }

    /**
     * Deletes an existing calendar object. 
     * 
     * @param string $calendarId 
     * @param string $objectUri 
     * @return void
     */
    public function deleteCalendarObject($calendarId,$objectUri) {

		$stmt = $this->pdo->prepare('DELETE FROM `'.$this->calendarObjectTableName.'` WHERE calendarid = ? AND uri = ?');
        $stmt->execute(array($calendarId,$objectUri));
        $stmt = $this->pdo->prepare('UPDATE `'. $this->calendarTableName .'` SET ctag = ctag + 1 WHERE id = ?');
        $stmt->execute(array($calendarId));

    }
	
	/*
	 * @param string $email
	 * @param string $calendarId
	 */
	public function getCalendarForUser($email, $calendarId)
	{
		$stmt = $this->pdo->prepare(
			'SELECT id FROM `'. $this->calendarTableName .'` WHERE uri = ? AND principaluri = ?');
		$stmt->execute(array(basename($calendarId), 'principals/' . $email));
		$result = $stmt->fetchAll();
		if (!$result)
		{
			return;
		}       

		return $result;
	}	

	/**
	 * @param string $principalUri
	 * @param string $calendarUri
	 */
	public function getCalendarUsers($principalUri, $calendarUri)	
	{
		$stmt = $this->pdo->prepare('
			SELECT p.uri, d.mode 
			FROM '.$this->sDbPrefix.afterlogic_DAV_Server::Tbl_Delegates.' AS d
			LEFT JOIN '.$this->sDbPrefix.afterlogic_DAV_Server::Tbl_Principals.' AS p ON p.id = d.principalid
			LEFT JOIN '.$this->sDbPrefix.afterlogic_DAV_Server::Tbl_Calendars.' AS c ON c.id = d.calendarid
			WHERE c.principaluri = ? AND c.uri = ?');
		$stmt->execute(array($principalUri, $calendarUri));

		return $stmt->fetchAll();	
	}
	
}
