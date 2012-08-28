<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

/**
 * @package SubAdmins
 */
class CApiSubAdminsManager extends AApiManagerWithStorage
{
	/**
	 * @param CApiGlobalManager &$oManager
	 */
	public function __construct(CApiGlobalManager &$oManager)
	{
		parent::__construct('subadmins', $oManager);

		$this->inc('classes.subadmin');
	}

	/**
	 * @return array | bool [DomainId => Name]
	 */
	public function GetSubAdminDomains()
	{
		$aResult = false;
		try
		{
			$aResult = $this->oStorage->GetSubAdminDomains();
		}
		catch (CApiBaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $aResult;
	}

	/**
	 * @param string $sLogin
	 * @return bool
	 */
	public function SubAdminExists(CSubAdmin $oSubAdmin)
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->SubAdminExists($oSubAdmin);
		}
		catch (CApiBaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	public function CreateSubAdmin(CSubAdmin &$oSubAdmin)
	{
		$bResult = false;
		try
		{
			if ($oSubAdmin->Validate())
			{
				if (!$this->SubAdminExists($oSubAdmin))
				{
					if (!$this->oStorage->CreateSubAdmin($oSubAdmin))
					{
						throw new CApiManagerException(Errs::SubAdminManager_SubAdminCreateFailed);
					}
				}
				else
				{
					throw new CApiManagerException(Errs::SubAdminManager_SubAdminAlreadyExists);
				}
			}

			$bResult = true;
		}
		catch (CApiBaseException $oException)
		{
			$bResult = false;
			$this->setLastException($oException);
		}

		return $bResult;
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	public function UpdateSubAdmin(CSubAdmin $oSubAdmin)
	{
		$bResult = false;
		try
		{
			if ($oSubAdmin->Validate())
			{
				if (!$this->SubAdminExists($oSubAdmin))
				{
					if (!$this->oStorage->UpdateSubAdmin($oSubAdmin))
					{
						throw new CApiManagerException(Errs::SubAdminManager_SubAdminUpdateFailed);
					}

					if ($oSubAdmin->IsOwner && null !== $oSubAdmin->GetObsoleteValue('IsDisabled'))
					{
						/* @var $oDomainsApi CApiDomainsManager */
						$oDomainsApi = CApi::Manager('domains');
						if (!$oDomainsApi->EnableOrDisableDomains($oSubAdmin->DomainIds, !$oSubAdmin->IsDisabled))
						{
							$oException = $oDomainsApi->GetLastException();
							if ($oException)
							{
								throw $oException;
							}
						}
					}
				}
				else
				{
					throw new CApiManagerException(Errs::SubAdminManager_SubAdminAlreadyExists);
				}
			}

			$bResult = true;
		}
		catch (CApiBaseException $oException)
		{
			$bResult = false;
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param CSubAdmin $oSubAdmin
	 * @return bool
	 */
	public function DeleteSubAdmin(CSubAdmin $oSubAdmin)
	{
		$bResult = false;
		try
		{
			if ($oSubAdmin)
			{
				if ($oSubAdmin->IsOwner)
				{
					/* @var $oDomainsApi CApiDomainsManager */
					$oDomainsApi = CApi::Manager('domains');
					if (!$oDomainsApi->DeleteDomains($oSubAdmin->DomainIds, true))
					{
						$oException = $oDomainsApi->GetLastException();
						if ($oException)
						{
							throw $oException;
						}
					}
				}

				$this->oStorage->DeleteSubAdmin($oSubAdmin->IdSubAdmin);
			}
		}
		catch (CApiBaseException $oException)
		{
			$this->setLastException($oException);
		}

		return $bResult;
	}

	/**
	 * @param array $aSubAdminsIds
	 * @return bool
	 */
	public function DeleteSubAdmins(array $aSubAdminsIds)
	{
		$bResult = false;
		if (is_array($aSubAdminsIds) && 0 < count($aSubAdminsIds))
		{
			$bResult = true;
			foreach ($aSubAdminsIds as $iSubAdminId)
			{
				$oSubAdmin = $this->GetSubAdminById($iSubAdminId);
				if ($oSubAdmin)
				{
					if ($this->DeleteSubAdmin($oSubAdmin))
					{
						$bResult = false;
						break;
					}
				}
				else
				{
					$bResult = false;
					break;
				}
			}
		}

		return $bResult;
	}

	/**
	 * @param int $iPage
	 * @param int $iSubAdminsPerPage
	 * @param string $sOrderBy = 'login'
	 * @param bool $bOrderType = true
	 * @param string $sSearchDesc = ''
	 * @return array | false [Id => [Login, Description]]
	 */
	public function GetSubAdminList($iPage, $iSubAdminsPerPage, $sOrderBy = 'Login', $bOrderType = true, $sSearchDesc = '')
	{
		$aResult = false;
		try
		{
			$aResult = $this->oStorage->GetSubAdminList($iPage, $iSubAdminsPerPage, $sOrderBy, $bOrderType, $sSearchDesc);
		}
		catch (CApiBaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $aResult;
	}

	/**
	 * @param string $sSearchDesc = ''
	 * @return int | false
	 */
	public function GetSubAdminCount($sSearchDesc = '')
	{
		$iResult = false;
		try
		{
			$iResult = $this->oStorage->GetSubAdminCount($sSearchDesc);
		}
		catch (CApiBaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $iResult;
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return CSubAdmin
	 */
	public function GetSubAdminByLoginAndPassword($sLogin, $sPassword)
	{
		$oSubAdmin = null;
		try
		{
			$oSubAdmin = $this->oStorage->GetSubAdminByLoginAndPassword($sLogin, $sPassword);
		}
		catch (CApiBaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $oSubAdmin;
	}

	/**
	 * @param int $iSubAdminId
	 * @return CSubAdmin
	 */
	public function GetSubAdminById($iSubAdminId)
	{
		$oSubAdmin = null;
		try
		{
			$oSubAdmin = $this->oStorage->GetSubAdminById($iSubAdminId);
		}
		catch (CApiBaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $oSubAdmin;
	}
}
