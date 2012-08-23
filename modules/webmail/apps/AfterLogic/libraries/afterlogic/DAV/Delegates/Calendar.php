<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Delegates_Calendar extends Sabre_CalDAV_Calendar {

    public function __construct(Sabre_DAVACL_IPrincipalBackend $principalBackend, Sabre_CalDAV_Backend_Abstract $calendarBackend, array $calendarInfo) {

        parent::__construct($principalBackend, $calendarBackend, $calendarInfo);

    }

    public function getName() {

        return $this->calendarInfo['id'];

    }

    public function getMode() {

        return $this->calendarInfo['mode'];

    }

	public function getACL() {

        return array(
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'delegation/' . $this->calendarInfo['id'] . '/principal/calendar-proxy-write',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}write',
                'principal' => 'delegation/' . $this->calendarInfo['id'] . '/principal/calendar-proxy-write',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'delegation/' . $this->calendarInfo['id'] . '/principal/calendar-proxy-read',
                'protected' => true,
            ),
        );

    
    }
    /**
     * Returns a calendar object
     *
     * The contained calendar objects are for example Events or Todo's.
     * 
     * @param string $name 
     * @return Sabre_DAV_ICalendarObject 
     */
    public function getChild($name) {

        $obj = $this->caldavBackend->getCalendarObject($this->calendarInfo['id'],$name);
        if (!$obj) throw new Sabre_DAV_Exception_FileNotFound('Calendar object not found');
        return new afterlogic_DAV_Delegates_CalendarObject($this->caldavBackend,$this->calendarInfo,$obj);

    }

    /**
     * Returns the full list of calendar objects  
     * 
     * @return array 
     */
    public function getChildren() {

        $objs = $this->caldavBackend->getCalendarObjects($this->calendarInfo['id']);
        $children = array();
        foreach($objs as $obj) {
            $children[] = new afterlogic_DAV_Delegates_CalendarObject($this->caldavBackend,$this->calendarInfo,$obj);
        }
        return $children;

    }

}
