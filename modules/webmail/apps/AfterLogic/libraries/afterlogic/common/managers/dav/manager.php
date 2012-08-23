<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

require_once CApi::RootPath().'/DAV/autoload.php';

class CApiDavManager extends AApiManager
{
	/**
	 * @var array
	 */
	protected $aDavClients;

	/**
	 * @param CApiGlobalManager &$oManager
	 */
	public function __construct(CApiGlobalManager &$oManager)
	{
		parent::__construct('dav', $oManager);
		CApi::Inc('common.dav.client');

		$this->aDavClients = array();
	}

	/**
	 * @param CAccount $oAccount
	 * @return DAVClient | false
	 */
	protected function &getDAVClient($oAccount)
	{
		$mResult = false;
		if (!isset($this->aDavClients[$oAccount->Email]))
		{
			$oSettings =& CApi::GetSettings();
			$sUrl = $oSettings->GetConf('Calendar/DAVUrl');

			$this->aDavClients[$oAccount->Email] = new DAVClient($sUrl, $oAccount->Email, $oAccount->IncomingMailPassword);
		}

		if (isset($this->aDavClients[$oAccount->Email]))
		{
			$mResult =& $this->aDavClients[$oAccount->Email];
		}

		return $mResult;
	}

	/**
	 * @return string
	 */
	public function GetServerUrl()
	{
		$oSettings =& CApi::GetSettings();
		return rtrim($oSettings->GetConf('Calendar/DAVUrl'), '/');
	}

	/**
	 * @return string
	 */
	public function GetCalendarStorageType()
	{
		$oSettings =& CApi::GetSettings();
		return rtrim($oSettings->GetConf('StorageTypes/Calendar'), '/');
	}

	/**
	 * @param string $sServerUrl
	 * @return bool
	 */
	public function SetServerUrl($sServerUrl)
	{
		$oSettings =& CApi::GetSettings();
		$oSettings->SetConf('Calendar/DAVUrl', $sServerUrl);
		return (bool) $oSettings->SaveToXml();
	}

	/**
	 * @return string
	 */
	public function GetServerHost()
	{
		$mResult = '';
		$sServerUrl = $this->GetServerUrl();
		if (!empty($sServerUrl))
		{
			$aUrlParts = parse_url($sServerUrl);
			if (!empty($aUrlParts['host']))
			{
				$mResult = $aUrlParts['host'];
			}
		}
		return $mResult;
	}

	/**
	* @return bool
	*/
	public function IsUseSsl()
	{
		$bResult = false;
		$sServerUrl = $this->GetServerUrl();
		if (!empty($sServerUrl))
		{
			$aUrlParts = parse_url($sServerUrl);
			if (!empty($aUrlParts['port']) && $aUrlParts['port'] === 443)
			{
				$bResult = true;
			}
			if (!empty($aUrlParts['scheme']) && $aUrlParts['scheme'] === 'https')
			{
				$bResult = true;
			}
		}
		return $bResult;
	}

	/**
	 * @return string
	 */
	public function GetServerPort()
	{
		$iResult = 80;
		$sServerUrl = $this->GetServerUrl();
		if (!empty($sServerUrl))
		{
			$aUrlParts = parse_url($sServerUrl);
			if (!empty($aUrlParts['port']))
			{
				$iResult = (int) $aUrlParts['port'];
			}
		}
		return $iResult;
	}

	/**
	 * @param CAccount $oAccount
	 * @return string
	 */
	public function GetPrincipalUrl($oAccount)
	{
		$mResult = false;
		try
		{
			$sServerUrl = $this->GetServerUrl();
			if (!empty($sServerUrl))
			{
				$aUrlParts = parse_url($sServerUrl);
				$sPort = '';
				if (!empty($aUrlParts['port']) && (int)$aUrlParts['port'] !== 80)
				{
					$sPort = ':'.$aUrlParts['port'];
				}

				if (!empty($aUrlParts['scheme']) && !empty($aUrlParts['host']))
				{
					$sServerUrl = $aUrlParts['scheme'].'://'.$aUrlParts['host'].$sPort;

					
					if ($this->GetCalendarStorageType() == 'caldav')
					{
						$oDav =& $this->getDAVClient($oAccount);
						if ($oDav && $oDav->Connect())
						{
							$mResult = $sServerUrl.$oDav->GetCurrentPrincipal();
						}
					}
					else
					{
						$mResult = $sServerUrl . '/principals/' . $oAccount->Email;
					}
				}
			}
		}
		catch (Exception $oException)
		{
			$mResult = false;
			$this->setLastException($oException);
		}
		return $mResult;
	}

	/**
	 * @param CAccount $oAccount
	 * @return string
	 */
	public function GetLogin($oAccount)
	{
		return $oAccount->Email;
	}

	/**
	 * @return bool
	 */
	public function IsMobileSyncEnabled()
	{
		$oSettings =& CApi::GetSettings();
		return (bool) $oSettings->GetConf('Common/EnableMobileSync');
	}

	/**
	 * @return bool
	 */
	public function SetMobileSyncEnable($bMobileSyncEnable)
	{
		$oSettings =& CApi::GetSettings();
		$oSettings->SetConf('Common/EnableMobileSync', $bMobileSyncEnable);
		return (bool) $oSettings->SaveToXml();
	}

	/**
	 * @param CAccount $oAccount
	 * @return bool
	 */
	public function TestConnection($oAccount)
	{
		$mResult = false;
		$oDav =& $this->getDAVClient($oAccount);
		if ($oDav && $oDav->Connect())
		{
			$mResult = true;
		}
		return $mResult;
	}
}
