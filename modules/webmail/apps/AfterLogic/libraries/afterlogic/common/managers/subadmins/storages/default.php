<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

/**
 * @package SubAdmins
 */
class CApiSubadminsStorage extends AApiManagerStorage
{
	/**
	 * @param CApiGlobalManager &$oManager
	 */
	public function __construct($sStorageName, CApiGlobalManager &$oManager)
	{
		parent::__construct('subadmins', $sStorageName, $oManager);
	}

	/**
	 * @return array | bool [DomainId => Name]
	 */
	public function GetSubAdminDomains()
	{
		return array();
	}

	/**
	 * @param string $sLogin
	 * @return bool
	 */
	public function SubAdminExists(CSubAdmin $oSubAdmin)
	{
		return false;
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	public function CreateSubAdmin(CSubAdmin &$oSubAdmin)
	{
		return false;
	}

	/**
	 * @param CSubAdmin &$oSubAdmin
	 */
	public function UpdateSubAdmin(CSubAdmin $oSubAdmin)
	{
		return false;
	}

	/**
	 * @param array $aSubAdminsIds
	 * @return bool
	 */
	public function DeleteSubAdmins(array $aSubAdminsIds)
	{
		return false;
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
		return array();
	}

	/**
	 * @param string $sSearchDesc = ''
	 * @return int | false
	 */
	public function GetSubAdminCount($sSearchDesc = '')
	{
		return 0;
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return CSubAdmin
	 */
	public function GetSubAdminByLoginAndPassword($sLogin, $sPassword)
	{
		return null;
	}

	/**
	 * @param int $iSubAdminId
	 * @return CSubAdmin
	 */
	public function GetSubAdminById($iSubAdminId)
	{
		return null;
	}
}