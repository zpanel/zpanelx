<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

abstract class afterlogic_DAV_Cache_Backend_Abstract {
	
	abstract function getRemindersCache($type = 1, $start = null, $end = null);	
	
	abstract function createRemindersCache($user, $calendarUri, $type = 0, $time = null, $startTime = null, $eventid = null);
	
	abstract function updateRemindersCache($id, $user, $calendarUri, $type, $time, $startTime, $eventid = null);

	abstract function deleteRemindersCache($calendarUri);

	abstract function getDelegates($calendarUri);
}
