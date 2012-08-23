<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Delegates_Root extends Sabre_DAV_Directory {

    protected $pdo;

    public $disableListing = false;

    function __construct(PDO $pdo, Sabre_DAVACL_IPrincipalBackend $principalBackend, Sabre_CalDAV_Backend_Abstract $calendarBackend) {

        $this->pdo = $pdo;
        $this->principalBackend = $principalBackend;
        $this->calendarBackend = $calendarBackend;

    }

	function getName() {

		return 'delegation';

	}

    function getChildren() {

        if ($this->disableListing) {
            throw new Sabre_DAV_Exception_MethodNotAllowed('Listing of items in this collection is not allowed');
        }

        $fields = array_values($this->calendarBackend->propertyMap);
        $fields[] = 'id';
        $fields[] = 'uri';
        $fields[] = 'ctag';
        $fields[] = 'components';
        $fields[] = 'principaluri';

        // Making fields a comma-delimited list 
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->query('SELECT ' . $fields . ' FROM `'.$this->calendarBackend->calendarTableName.'`'); 

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
        

            foreach($this->calendarBackend->propertyMap as $xmlName=>$dbName) {
                $calendar[$xmlName] = $row[$dbName];
            }
            $calendars[] = new afterlogic_DAV_Delegates_CalendarParent($this->principalBackend, $this->calendarBackend, $calendar);

        }

        return $calendars;

    }

    function getChild($name) {

        $fields = array_values($this->calendarBackend->propertyMap);
        $fields[] = 'id';
        $fields[] = 'uri';
        $fields[] = 'ctag';
        $fields[] = 'components';
        $fields[] = 'principaluri';

        // Making fields a comma-delimited list 
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->prepare('SELECT ' . $fields . ' FROM `'.$this->calendarBackend->calendarTableName.'` WHERE id = ?'); 
        $stmt->execute(array($name));

        if(!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Sabre_DAV_Exception_FileNotFound('Calendar with id: ' . $name . ' could not be found');
        }


        $components = explode(',',$row['components']);

        $calendar = array(
            'id' => $row['id'],
            'uri' => $row['uri'],
            'principaluri' => $row['principaluri'],
            '{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $row['ctag']?$row['ctag']:'0',
            '{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet($components),
        );
    

        foreach($this->calendarBackend->propertyMap as $xmlName=>$dbName) {
            $calendar[$xmlName] = $row[$dbName];
        }

        return new afterlogic_DAV_Delegates_CalendarParent($this->principalBackend, $this->calendarBackend, $calendar);

    }

}

?>
