<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Delegates_CalendarParent extends Sabre_DAV_Directory {

    protected $pdo;
    protected $calendarInfo;
    protected $principalBackend;

    public function __construct(Sabre_DAVACL_IPrincipalBackend $principalBackend, Sabre_CalDAV_Backend_Abstract $calendarBackend, array $calendarInfo) {

        $this->calendarInfo = $calendarInfo;
        $this->principalBackend = $principalBackend;
        $this->calendarBackend = $calendarBackend;

    }

    function getName() {

        return $this->calendarInfo['id'];

    }

    function getChildren() {

        return array(
            new afterlogic_DAV_Delegates_Principal($this->principalBackend, $this->calendarInfo),
            new afterlogic_DAV_Delegates_Calendar($this->principalBackend, $this->calendarBackend, $this->calendarInfo),
        );

    }

}
