<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

abstract class afterlogic_DAV_Share_Backend_Abstract {
	
	abstract function UpdateShare($sCalendarId, $FromUser, $ToUser, $Mode);
}
