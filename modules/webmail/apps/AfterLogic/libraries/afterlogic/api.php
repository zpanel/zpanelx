<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

CApi::Run();
CApi::PostRun();

/**
 * @package Api
 */
class CApi
{
	/**
	 * @var CApiGlobalManager
	 */
	static $oManager;

	/**
	 * @var CApiPluginManager
	 */
	static $oPlugin;

	/**
	 * @var array
	 */
	static $aConfig;

	/**
	 * @var bool
	 */
	static $bIsValid;

	/**
	 * @var bool
	 */
	static $bUseDbLog = true;

	public static function Run()
	{
		defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../../'));

		if (!is_object(CApi::$oManager))
		{
			CApi::Inc('common.constants');
			CApi::Inc('common.enum');
			CApi::Inc('common.exception');
			CApi::Inc('common.utils');
			CApi::Inc('common.container');
			CApi::Inc('common.manager');
			CApi::Inc('common.xml');
			CApi::Inc('common.plugin');

			CApi::Inc('common.utils.get');
			CApi::Inc('common.utils.post');
			CApi::Inc('common.utils.session');

			CApi::Inc('common.http');

			CApi::Inc('common.db.storage');

			CApi::$oManager = new CApiGlobalManager();
			CApi::$aConfig = include CApi::RootPath().'/common/config.php';

			$sSettingsFile = CApi::DataPath().'/settings/config.php';
			if (@file_exists($sSettingsFile))
			{
				$aAppConfig = include $sSettingsFile;
				if (is_array($aAppConfig))
				{
					CApi::$aConfig = array_merge(CApi::$aConfig, $aAppConfig);
				}
			}

			CApi::$oPlugin = new CApiPluginManager(CApi::$oManager);
			CApi::$bIsValid = CApi::validateApi();

			CApi::$oManager->PrepareStorageMap();
		}
	}

	public static function PostRun()
	{
		CApi::Manager('users');
		CApi::Manager('domains');
	}

	/**
	 * @return CApiPluginManager
	 */
	public static function Plugin()
	{
		return CApi::$oPlugin;
	}

	/**
	 * @param string $sManagerType
	 */
	public static function Manager($sManagerType)
	{
		return CApi::$oManager->GetByType($sManagerType);
	}

	/**
	 * @return CApiGlobalManager
	 */
	public static function GetManager()
	{
		return CApi::$oManager;
	}

	/**
	 * @return api_Settings
	 */
	public static function &GetSettings()
	{
		return CApi::$oManager->GetSettings();
	}

	/**
	 * @param api_Http $oInput
	 * @return string
	 */
	public static function CsrfBrowserToken(api_Http $oInput)
	{
		$sUserAgent = $oInput->GetServer('HTTP_USER_AGENT', '');
		return md5('awm'.__FILE__.md5($sUserAgent).'awm');
	}

	/**
	 * @param bool $bCreate = true
	 * @return string
	 */
	public static function GetPDO()
	{
		$oSettings =& CApi::GetSettings();

		$sDbHost = $oSettings->GetConf('Common/DBHost');
		$sDbName = $oSettings->GetConf('Common/DBName');
		$sDbLogin = $oSettings->GetConf('Common/DBLogin');
		$sDbPassword = $oSettings->GetConf('Common/DBPassword');

		$sUnixSocket = '';
		$iPos = strpos($sDbHost, '/');
		if (false !== $iPos)
		{
			$sUnixSocket = substr($sDbHost, $iPos);
			$sDbHost = rtrim(substr($sDbHost, 0, $iPos), ':');
		}

		$oPdo = false;
		try
		{
			$oPdo = new PDO('mysql:dbname='.$sDbName.';host='.$sDbHost.
				(empty($sUnixSocket) ? '' : ';unix_socket='.$sUnixSocket), $sDbLogin, $sDbPassword);

			$oPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (Exception $oException)
		{
			self::Log($oException->getTraceAsString(), ELogLevel::Error);
			$oPdo = false;
		}

		return $oPdo;
	}

	/**
	 * @param string $sKey
	 * @param mixed $mDefault = null
	 * @return mixed
	 */
	public static function GetConf($sKey, $mDefault = null)
	{
		return (isset(CApi::$aConfig[$sKey])) ? CApi::$aConfig[$sKey] : $mDefault;
	}

	/**
	 * @param string $sKey
	 * @param mixed $mValue
	 * @return void
	 */
	public static function SetConf($sKey, $mValue)
	{
		CApi::$aConfig[$sKey] = $mValue;
	}

	/**
	 * @return bool
	 */
	public static function ManagerInc($sManagerName, $sFileName)
	{
		$sManagerName = preg_replace('/[^a-z]/', '', strtolower($sManagerName));
		return CApi::Inc('common.managers.'.$sManagerName.'.'.$sFileName);
	}

	/**
	 * @return bool
	 */
	public static function ManagerPath($sManagerName, $sFileName)
	{
		$sManagerName = preg_replace('/[^a-z]/', '', strtolower($sManagerName));
		return CApi::IncPath('common.managers.'.$sManagerName.'.'.$sFileName);
	}

	/**
	 * @return bool
	 */
	public static function StorageInc($sManagerName, $sStorageName, $sFileName)
	{
		$sManagerName = preg_replace('/[^a-z]/', '', strtolower($sManagerName));
		$sStorageName = preg_replace('/[^a-z]/', '', strtolower($sStorageName));
		return CApi::Inc('common.managers.'.$sManagerName.'.storages.'.$sStorageName.'.'.$sFileName);
	}

	/**
	 * @return bool
	 */
	public static function IncPath($sFileName)
	{
		$sFileName = preg_replace('/[^a-z0-9\._\-]/', '', strtolower($sFileName));
		$sFileName = preg_replace('/[\.]+/', '.', $sFileName);
		$sFileName = str_replace('.', '/', $sFileName);

		return CApi::RootPath().'/'.$sFileName.'.php';
	}
	/**
	 * @param string $sFileName
	 * @param bool $bDoExitOnError = true
	 * @return bool
	 */
	public static function Inc($sFileName, $bDoExitOnError = true)
	{
		static $aCache = array();

		$sFileFullPath = '';
		$sFileName = preg_replace('/[^a-z0-9\._\-]/', '', strtolower($sFileName));
		$sFileName = preg_replace('/[\.]+/', '.', $sFileName);
		$sFileName = str_replace('.', '/', $sFileName);
		if (isset($aCache[$sFileName]))
		{
			return true;
		}
		else
		{
			$sFileFullPath = CApi::RootPath().'/'.$sFileName.'.php';
			if (@file_exists($sFileFullPath))
			{
				$aCache[$sFileName] = true;
				include_once $sFileFullPath;
				return true;
			}
		}

		if ($bDoExitOnError)
		{
			exit('FILE NOT EXITS = '.$sFileFullPath);
		}
		return false;
	}

	/**
	 * @param string $sNewLocation
	 */
	public static function Location($sNewLocation)
	{
		CApi::Log('Location: '.$sNewLocation);
		@header('Location: '.$sNewLocation);
	}

	/**
	 * @param string $sDesc
	 * @param CAccount $oAccount
	 */
	public static function LogEvent($sDesc, CAccount $oAccount)
	{
		$oSettings =& CApi::GetSettings();

		if ($oSettings && $oSettings->GetConf('Common/EnableEventLogging'))
		{
			$sDate = @date('H:i:s');
			CApi::Log('Event: '.$oAccount->Email.' > '.$sDesc);
			CApi::LogOnly('['.$sDate.'] '.$oAccount->Email.' > '.$sDesc, CApi::GetConf('log.event-file', 'event.txt'));
		}
	}

	/**
	 * @param mixed $mObject
	 * @param int $iLogLevel = ELogLevel::Full
	 * @param string $sFilePrefix = ''
	 */
	public static function LogObject($mObject, $iLogLevel = ELogLevel::Full, $sFilePrefix = '')
	{
		CApi::Log(print_r($mObject, true), $iLogLevel, $sFilePrefix);
	}

	/**
	 * @param string $sDesc
	 * @param int $iLogLevel = ELogLevel::Full
	 * @param string $sFilePrefix = ''
	 * @param bool $bIdDb = false
	 */
	public static function Log($sDesc, $iLogLevel = ELogLevel::Full, $sFilePrefix = '')
	{
		static $bIsFirst = true;

		$oSettings =& CApi::GetSettings();
		$sLogFile = $sFilePrefix.CApi::GetConf('log.log-file', 'log.txt');
		$bSpecifidedByUser = CApi::GetConf('labs.log.specified-by-user', false) && !empty($_COOKIE['user-log']);
		if ($bSpecifidedByUser)
		{
			$sLogFile = substr(preg_replace('/[^a-z0-9]/', '', $_COOKIE['user-log']), 0, 20).'-'.$sLogFile;
		}

		if ($oSettings && $oSettings->GetConf('Common/EnableLogging')
			&& ($iLogLevel <= $oSettings->GetConf('Common/LoggingLevel') ||
				$bSpecifidedByUser ||
				(ELogLevel::Spec === $oSettings->GetConf('Common/LoggingLevel') &&
					isset($_COOKIE['spec-log']) && '1' === (string) $_COOKIE['spec-log'])))
		{
			$aMicro = explode('.', microtime(true));
			$sDate = @date('H:i:s.').str_pad((isset($aMicro[1]) ? substr($aMicro[1], 0, 2) : '0'), 2, '0');
			if ($bIsFirst)
			{
				$sUri = api_Utils::RequestUri();
				$bIsFirst = false;
				$sPost = (isset($_POST) && count($_POST) > 0) ? ' [POST('.count($_POST).')]' : '';

				CApi::LogOnly(API_CRLF.'['.$sDate.']'.$sPost.' '.$sUri, $sLogFile);
				if (!empty($sPost))
				{
					if (CApi::GetConf('labs.log.post-view', false))
					{
						CApi::LogOnly('['.$sDate.'] POST > '.print_r($_POST, true), $sLogFile);
					}
					else
					{
						CApi::LogOnly('['.$sDate.'] POST > ['.implode(', ', array_keys($_POST)).']', $sLogFile);
					}
				}
				CApi::LogOnly('['.$sDate.']', $sLogFile);

				@register_shutdown_function('CApi::LogEnd');
			}

			CApi::LogOnly('['.$sDate.'] '.$sDesc, $sLogFile);
		}
	}

	/**
	 * @param string $sDesc
	 * @param string $sLogFile
	 */
	public static function LogOnly($sDesc, $sLogFile)
	{
		@error_log($sDesc.API_CRLF, 3, CApi::DataPath().'/logs/'.$sLogFile);
	}

	public static function LogEnd()
	{
		CApi::Log('# script shutdown');
	}

	/**
	 * @return string
	 */
	public static function RootPath()
	{
		defined('API_ROOTPATH') || define('API_ROOTPATH', rtrim(dirname(__FILE__), '/\\'));
		return API_ROOTPATH;
	}

	/**
	 * @return string
	 */
	public static function WebMailPath()
	{
		return CApi::RootPath().API_PATH_TO_WEBMAIL;
	}

	/**
	 * @return string
	 */
	public static function Version()
	{
		static $sVersion = null;
		if (null === $sVersion)
		{
			$sAppVersion = @file_get_contents(CApi::WebMailPath().'VERSION');
			$sVersion = (false === $sAppVersion) ? '0.0.0' : $sAppVersion;
		}
		return $sVersion;
	}

	/**
	 * @return string
	 */
	public static function VersionJs()
	{
		return preg_replace('/[^0-9a-z]/', '', CApi::Version());
	}

	/**
	 * @return string
	 */
	public static function DataPath()
	{
		$dataPath = 'data';
		if (!defined('API_DATA_FOLDER') && @file_exists(CApi::WebMailPath().'inc_settings_path.php'))
		{
			include CApi::WebMailPath().'inc_settings_path.php';
		}

		if (!defined('API_DATA_FOLDER') && isset($dataPath) && null !== $dataPath)
		{
			define('API_DATA_FOLDER', api_Utils::GetFullPath($dataPath, CApi::WebMailPath()));
		}

		return defined('API_DATA_FOLDER') ? API_DATA_FOLDER : '';
	}

	/**
	 * @return bool
	 */
	protected static function validateApi()
	{
		$iResult = 1;

		$oSettings =& CApi::GetSettings();
		$iResult &= $oSettings && ($oSettings instanceof api_Settings);

		return (bool) $iResult;
	}

	/**
	 * @return bool
	 */
	public static function IsValid()
	{
		return (bool) CApi::$bIsValid;
	}
}
