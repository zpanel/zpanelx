<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 * 
 */

/**
 * @package SubAdmins
 */
class CApiSubadminsDbStorage extends CApiSubadminsStorage
{
	/**
	 * @var CDbStorage $oConnection
	 */
	protected $oConnection;

	/**
	 * @var CApiDomainsCommandCreator
	 */
	protected $oCommandCreator;

	/**
	 * @param CApiGlobalManager &$oManager
	 */
	public function __construct(CApiGlobalManager &$oManager)
	{
		parent::__construct('db', $oManager);

		$this->oConnection =& $oManager->GetConnection();
		$this->oCommandCreator =& $oManager->GetCommandCreator(
			$this, array(EDbType::MySQL => 'CApiSubAdminsCommandCreatorMySQL')
		);
	}

	/**
	 * @return array | bool [DomainId => Name]
	 */
	public function GetSubAdminDomains()
	{
		$aResult = false;
		if ($this->oConnection->Execute($this->oCommandCreator->GetSubAdminDomains()))
		{
			$oRow = null;
			$aResult = array();
			while (false !== ($oRow = $this->oConnection->GetNextRecord()))
			{
				$aResult[$oRow->id_domain] = $oRow->name;
			}
		}

		$this->throwDbExceptionIfExist();
		return $aResult;
	}

	/**
	 * @param string $sLogin
	 * @return bool
	 */
	public function SubAdminExists(CSubAdmin $oSubAdmin)
	{
		$bResult = false;
		$niExceptSubAdminId = (0 < $oSubAdmin->IdSubAdmin) ? $oSubAdmin->IdSubAdmin : null;
		if ($this->oConnection->Execute(
			$this->oCommandCreator->SubAdminExists($oSubAdmin->Login, $niExceptSubAdminId)))
		{
			$oRow = $this->oConnection->GetNextRecord();
			if ($oRow && 0 < (int) $oRow->subadmins_count)
			{
				$bResult = true;
			}
		}

		$this->throwDbExceptionIfExist();
		return $bResult;
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	public function CreateSubAdmin(CSubAdmin &$oSubAdmin)
	{
		$bResult = false;
		if ($this->oConnection->Execute($this->oCommandCreator->CreateSubAdmin($oSubAdmin)))
		{
			$bResult = true;
			$oSubAdmin->IdSubAdmin = $this->oConnection->GetLastInsertId();
			$this->updateSubAdminDomains($oSubAdmin);
		}

		$this->throwDbExceptionIfExist();
		return $bResult;
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	public function UpdateSubAdmin(CSubAdmin $oSubAdmin)
	{
		$bResult = false;
		if ($this->oConnection->Execute($this->oCommandCreator->UpdateSubAdmin($oSubAdmin)))
		{
			$bResult = true;
			$this->updateSubAdminDomains($oSubAdmin);
		}

		$this->throwDbExceptionIfExist();
		return $bResult;
	}

	/**
	 * @param int $iSubAdminId
	 * @return bool
	 */
	public function DeleteSubAdmin($iSubAdminId)
	{
		return $this->DeleteSubAdmins(array($iSubAdminId));
	}

	/**
	 * @param array $aSubAdminsIds
	 * @return bool
	 */
	public function DeleteSubAdmins(array $aSubAdminsIds)
	{
		$bResult = $this->oConnection->Execute(
			$this->oCommandCreator->DeleteSubAdmins($aSubAdminsIds));

		if ($bResult)
		{
			$bResult = $this->oConnection->Execute(
				$this->oCommandCreator->DeleteSubAdminsDomains($aSubAdminsIds));
		}

		$this->throwDbExceptionIfExist();
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
		$aSubAdmins = false;
		if ($this->oConnection->Execute(
			$this->oCommandCreator->GetSubAdminList($iPage, $iSubAdminsPerPage,
				$this->dbOrderBy($sOrderBy), $bOrderType, $sSearchDesc)))
		{
			$oRow = null;
			$aSubAdmins = array();
			while (false !== ($oRow = $this->oConnection->GetNextRecord()))
			{
				$aSubAdmins[$oRow->id_admin] = array($oRow->login, $oRow->description);
			}
		}

		$this->throwDbExceptionIfExist();
		return $aSubAdmins;
	}

	/**
	 * @param string $sSearchDesc = ''
	 * @return int | false
	 */
	public function GetSubAdminCount($sSearchDesc = '')
	{
		$iResultCount = false;
		if ($this->oConnection->Execute(
			$this->oCommandCreator->GetSubAdminCount($sSearchDesc)))
		{
			$oRow = $this->oConnection->GetNextRecord();
			if ($oRow)
			{
				$iResultCount = (int) $oRow->subadmins_count;
			}
		}

		$this->throwDbExceptionIfExist();
		return $iResultCount;
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return CSubAdmin
	 */
	public function GetSubAdminByLoginAndPassword($sLogin, $sPassword)
	{
		$oSubAdmin = null;
		if ($this->oConnection->Execute(
			$this->oCommandCreator->GetSubAdminByLoginAndPassword($sLogin, $sPassword)))
		{
			$oRow = $this->oConnection->GetNextRecord();
			if ($oRow)
			{
				$oSubAdmin = new CSubAdmin();
				$oSubAdmin->InitByDbRow($oRow);
				$this->initSubAdminDomains($oSubAdmin);
			}
		}

		$this->throwDbExceptionIfExist();
		return $oSubAdmin;
	}

	/**
	 * @param int $iSubAdminId
	 * @return CSubAdmin
	 */
	public function GetSubAdminById($iSubAdminId)
	{
		$oSubAdmin = null;
		if ($this->oConnection->Execute(
			$this->oCommandCreator->GetSubAdminById($iSubAdminId)))
		{
			$oRow = $this->oConnection->GetNextRecord();
			if ($oRow)
			{
				$oSubAdmin = new CSubAdmin();
				$oSubAdmin->InitByDbRow($oRow);
				$this->initSubAdminDomains($oSubAdmin);
			}
		}

		$this->throwDbExceptionIfExist();
		return $oSubAdmin;
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	protected function updateSubAdminDomains(CSubAdmin &$oSubAdmin)
	{
		if ($oSubAdmin)
		{
			$this->oConnection->Execute(
				$this->oCommandCreator->ClearSubAdminDomains($oSubAdmin->IdSubAdmin));

			if (0 < count($oSubAdmin->DomainIds))
			{
				$this->oConnection->Execute(
					$this->oCommandCreator->AddSubAdminDomains($oSubAdmin));
			}
		}

		$this->throwDbExceptionIfExist();
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	protected function initSubAdminDomains(CSubAdmin &$oSubAdmin)
	{
		if ($oSubAdmin && $this->oConnection->Execute(
			$this->oCommandCreator->GetSubAdminDomainsById($oSubAdmin->IdSubAdmin)))
		{
			$oRow = null;
			$aDomainIds = array();
			while (false !== ($oRow = $this->oConnection->GetNextRecord()))
			{
				$aDomainIds[] = (int) $oRow->id_domain;
			}

			$oSubAdmin->DomainIds = $aDomainIds;
		}

		$this->throwDbExceptionIfExist();
	}

	/**
	 * @param string $sOrderBy
	 * @return string
	 */
	protected function dbOrderBy($sOrderBy)
	{
		$sResult = $sOrderBy;
		switch ($sOrderBy)
		{
			case 'Description':
				$sResult = 'description';
				break;
			case 'Login':
				$sResult = 'login';
				break;
		}
		return $sResult;
	}
}