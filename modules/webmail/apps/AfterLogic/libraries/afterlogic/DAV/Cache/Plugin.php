<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Cache_Plugin extends Sabre_DAV_ServerPlugin {

    /**
     * Reference to Server class 
     * 
     * @var Sabre_DAV_Server 
     */
    private $server;
	
    /**
     * cacheBackend 
     * 
     * @var afterlogic_DAV_Cache_Backend_Abstract 
     */
    private $cacheBackend;	

    /**
     * __construct 
     * 
     * @param afterlogic_DAV_Cache_Backend_Abstract $cacheBackend 
     * @return void
     */
    public function __construct(afterlogic_DAV_Cache_Backend_Abstract $cacheBackend = null) {

        $this->cacheBackend = $cacheBackend;        
    }
	
	/**
     * Initializes the plugin and registers event handlers 
     * 
     * @param Sabre_DAV_Server $server 
     * @return void
     */
    public function initialize(Sabre_DAV_Server $server) 
	{

        $this->server = $server;
        $this->server->subscribeEvent('beforeMethod',array($this,'beforeMethod'), 90);

    }

    /**
     * @param string $method
     * @param string $uri
     * @return void
     */
    public function beforeMethod($method, $uri) 
	{
		$user = $this->server->getPlugin('auth')->getCurrentUser();
		$uriExt = pathinfo($uri, PATHINFO_EXTENSION);
		$isEvent = false;
		if ($uriExt != null)
		{
			$uri = dirname($uri);
			$isEvent = true;
		}
		$method = strtoupper($method);
		if ($method == 'PUT' || $method == 'DELETE')
		{
			$this->deleteRemindersCache($uri);
			if ($isEvent)
			{
				$this->createRemindersCache($user, $uri);
			}

			$delegates = $this->getDelegates($uri);
			if (count($delegates) > 0)
			{
				foreach ($delegates as $delegate)
				{
					$this->deleteRemindersCache($delegate['uri']);
					if ($isEvent)
					{
						$this->createRemindersCache($delegate['user'], $delegate['uri']);
					}
				}
			}
		}
    }
	
	public function getRemindersCache($checked)
	{
		return $this->cacheBackend->getRemindersCache($checked);
	}
	
	public function createRemindersCache($user, $calendarUri, $checked = false, $time = null, $startTime = null)
	{
		return $this->cacheBackend->createRemindersCache($user, $calendarUri, $checked, $time, $startTime = null);
	}
	
	public function updateRemindersCache($id, $user, $calendarUri, $checked, $time, $startTime = null)
	{
		return $this->cacheBackend->updateRemindersCache($id, $user, $calendarUri, $checked, $time, $startTime = null);
	}

	public function deleteRemindersCache($calendarUri)
	{
		return $this->cacheBackend->deleteRemindersCache($calendarUri);
	}
	
	public function getDelegates($calendarUri)
	{
		$calendarUri = basename($calendarUri);
		return $this->cacheBackend->getDelegates($calendarUri);
	}
}
