<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 *
 */

defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../../'));

include_once WM_ROOTPATH.'libraries/afterlogic/api.php';
require_once WM_ROOTPATH.'libraries/afterlogic/DAV/autoload.php';

CApi::$bUseDbLog = false;

/* Mapping PHP errors to exceptions */
function exception_error_handler($errno, $errstr, $errfile, $errline )
{
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("exception_error_handler");

class afterlogic_DAV_Server extends Sabre_DAV_Server
{
	const Tbl_Accounts = 'awm_accounts';

	const Tbl_Principals = 'adav_principals';
	const Tbl_Groupmembers = 'adav_groupmembers';
	const Tbl_Delegates = 'adav_delegates';
	const Tbl_Calendars = 'adav_calendars';
	const Tbl_Calendarobjects = 'adav_calendarobjects';
	const Tbl_Addressbooks = 'adav_addressbooks';
	const Tbl_Cards = 'adav_cards';
	const Tbl_Locks = 'adav_locks';
	const Tbl_Cache = 'adav_cache';

	public $authBackend;
	public $principalBackend;
	public $caldavBackend;
	public $carddavBackend;
	public $lockBackend;
	public $cacheBackend;
	public $shareBackend;

	public function __construct($baseUri = '/')
	{
		self::$exposeVersion = false;
		$this->setBaseUri($baseUri);
		date_default_timezone_set('GMT');

		/* Get WebMail Settings */
		$oSettings =& CApi::GetSettings();

		$sDbPrefix = $oSettings->GetConf('Common/DBPrefix');

		/* Database */
		$pdo = CApi::GetPDO();
		if ($pdo)
		{
			/* Backends */
			$this->authBackend = new afterlogic_DAV_Auth_Backend_PDO($pdo, $sDbPrefix, self::Tbl_Accounts,
					self::Tbl_Principals, self::Tbl_Calendars, self::Tbl_Addressbooks);
			$this->principalBackend = new afterlogic_DAV_Principal_Backend_PDO($pdo, $sDbPrefix,
				self::Tbl_Principals, self::Tbl_Groupmembers, self::Tbl_Delegates);
			$this->caldavBackend = new afterlogic_DAV_CalDAV_Backend_PDO($pdo, $sDbPrefix,
				self::Tbl_Calendars, self::Tbl_Calendarobjects, self::Tbl_Delegates);
			$this->carddavBackend = new afterlogic_DAV_CardDAV_Backend_PDO($pdo, $sDbPrefix,
				self::Tbl_Addressbooks, self::Tbl_Cards);
			$this->lockBackend = new afterlogic_DAV_Locks_Backend_PDO($pdo, $sDbPrefix, self::Tbl_Locks);
			$this->cacheBackend = new afterlogic_DAV_Cache_Backend_PDO($pdo, $sDbPrefix, self::Tbl_Cache);
			$this->shareBackend = new afterlogic_DAV_Share_Backend_PDO($pdo, $sDbPrefix);


			$oApiCollaborationManager = CApi::Manager('collaboration');

			/* Directory tree */
			$tree = array();

			/* Authentication */
			$authPlugin = new Sabre_DAV_Auth_Plugin($this->authBackend, 'SabreDAV');
			$this->addPlugin($authPlugin);

			$pubCollection = array();

			/* Global Address Book */
			$pubCollection[] = new afterlogic_DAV_CardDAV_GlobaAddressBooks($authPlugin);

			/* Public files folder */
			$pubDirectory = CApi::DataPath() . '/files';
			if (!file_exists($pubDirectory))
			{
				mkdir($pubDirectory);
			}
			$pubDirectory .= '/public';
			if (!file_exists($pubDirectory))
			{
				mkdir($pubDirectory);
			}
			$pubCollection[] = new afterlogic_DAV_PublicDirectory($pubDirectory, 'files');
			/****/

			if (count($pubCollection) > 0)
			{
				$tree[] = new Sabre_DAV_SimpleCollection('public', $pubCollection);
			}

			$tree[] = new Sabre_DAV_SimpleDirectory('files');

			$tree[] = new Sabre_CalDAV_Principal_Collection($this->principalBackend);
			$tree[] = new Sabre_CalDAV_CalendarRootNode($this->principalBackend, $this->caldavBackend);
			if ($oApiCollaborationManager && $oApiCollaborationManager->IsCalendarSharingSupported())
			{
				$tree[] = new afterlogic_DAV_Delegates_Root($pdo, $this->principalBackend, $this->caldavBackend);
			}
			$tree[] = new Sabre_CardDAV_AddressBookRoot($this->principalBackend, $this->carddavBackend);

			/* Initializing server */
			parent::__construct($tree);

			$this->httpResponse->setHeader("X-Server", "AfterlogicDAVServer");

			/* DAV ACL Plugin */
			$aclPlugin = new Sabre_DAVACL_Plugin();
			$aclPlugin->hideNodesFromListings = true;
			$this->addPlugin($aclPlugin);

			/* Cache */
			$this->addPlugin(new afterlogic_DAV_Cache_Plugin($this->cacheBackend));

			$oApiDavManager = CApi::Manager('dav');
			$bEnableMobileSync = $oApiDavManager->IsMobileSyncEnabled();
			$headers = $this->httpRequest->getHeaders();
			if ($bEnableMobileSync ||
					(isset($headers['user-agent']) && $headers['user-agent'] == 'AfterlogicDAVClient'))
			{
				/* CalDAV plugin */
				$this->addPlugin(new Sabre_CalDAV_Plugin());

				/* CardDAV plugin */
				$CardDAVPlugin = new Sabre_CardDAV_Plugin();
				$CardDAVPlugin->directories = array(
					'public/contacts'
				);
				$this->addPlugin($CardDAVPlugin);
			}

			/* Locks plugin */
	//		$this->addPlugin(new Sabre_DAV_Locks_Plugin($this->lockBackend));

			/* ics export plugin */
			$bExportPlugin = CApi::GetConf('labs.dav.use-export-plugin', false);
			if ($bExportPlugin)
			{
				$this->addPlugin(new Sabre_CalDAV_ICSExportPlugin());
			}

			/* Support for html frontend */
			$bBrowserPlugin = CApi::GetConf('labs.dav.use-browser-plugin', false);
			if ($bBrowserPlugin)
			{
				$this->addPlugin(new Sabre_DAV_Browser_Plugin());
			}

			$this->subscribeEvent('beforeGetProperties', array($this, 'beforeGetProperties'), 90);
		}
    }

	/**
	 * @param string $path
	 * @param Sabre_DAV_INode $node
	 * @param array $requestedProperties
	 * @param array $returnedProperties
	 * @return void
	 */
	function beforeGetProperties($path, Sabre_DAV_INode $node, &$requestedProperties, &$returnedProperties)
	{
		if ($node instanceof afterlogic_DAV_Delegates_Principal)
		{
			$calHome = '{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}calendar-home-set';
			if (($index = array_search($calHome,$requestedProperties)) !== false)
			{
				$returnedProperties[200][$calHome] = new Sabre_DAV_Property_Href(dirname($path) . '/');
				unset($requestedProperties[$index]);
			}
		}
	}
}