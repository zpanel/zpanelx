<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class Afterlogic_Dav_Delegates_Principal extends Sabre_DAV_Directory implements Sabre_DAVACL_IPrincipal {

    protected $calendarInfo;

    public function __construct(Sabre_DAVACL_IPrincipalBackend $principalBackend, array $calendarInfo) {

        $this->calendarInfo = $calendarInfo;
        $this->principalBackend = $principalBackend;

    }

    function getName() {

        return 'principal';

    }

    function getAlternateUriSet() {

        return array();

    }

    function getPrincipalUrl() {

        return 'delegation/' . $this->calendarInfo['id'] . '/principal';

    }

    function getGroupMemberSet() {

        return array();

    }

    function setGroupMemberSet(array $groupMembers) {

        throw new Sabre_DAV_Exception_Forbidden('Updating group members on this principal is not allowed');

    }

    function getGroupMemberShip() {

        return array();

    }

    /**
     * This method returns the displayname for a calendar.
     *
     * We're returning the calendar name instead. 
     * 
     * @return string 
     */
    function getDisplayName() {

        $displayName = null;
		if($this->calendarInfo['{DAV:}displayname']) 
		{
            $displayName = $this->calendarInfo['{DAV:}displayname'];
        } 
		else 
		{
            $displayName = $this->calendarInfo['uri'];
        }
        return $displayName;  

    }

    function getChildren() {

        $properties = array(
            'uri' => $this->getPrincipalUrl(),
        );
        return array(
            new Sabre_CalDAV_Principal_ProxyRead($this->principalBackend,$properties),
            new Sabre_CalDAV_Principal_ProxyWrite($this->principalBackend, $properties),
        );

    }

}
