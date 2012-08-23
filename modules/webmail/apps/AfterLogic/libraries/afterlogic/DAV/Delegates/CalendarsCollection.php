<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Delegates_CalendarsCollection extends Sabre_DAV_Directory implements Sabre_DAV_ICollection {

	private $principalInfo;

	function __construct(array $principalInfo) {

		$this->principalInfo = $principalInfo;

	}

	function getName() {

		list(, $name) = Sabre_DAV_URLUtil::splitPath($this->principalInfo['uri']);
		return $name;

	}

	function getChildren() {

		return array(
            new afterlogic_DAV_Delegates_CalendarsReadable($this->principalInfo),
            new afterlogic_Dav_Delegates_CalendarsWriteable($this->principalInfo),
        );

	}

}

?>
