<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

/**
 * @package SubAdmins
 */
class CApiSubAdminsCommandCreator extends api_CommandCreator
{
	/**
	 * @return	string
	 */
	function GetSubAdminDomains()
	{
		$sSql = 'SELECT id_domain, name FROM %sawm_domains';

		return sprintf($sSql, $this->Prefix());
	}

	/**
	 * @param string $sSearchDesc = ''
	 * @return string
	 */
	public function GetSubAdminCount($sSearchDesc = '')
	{
		$sWhere = '';
		if (!empty($sSearchDesc))
		{
			$sWhere = ' WHERE login LIKE '.$this->escapeString('%'.strtolower($sSearchDesc).'%');
		}

		$sSql = 'SELECT COUNT(id_admin) as subadmins_count FROM %sawm_subadmins%s';

		return sprintf($sSql, $this->Prefix(), $sWhere);
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return string
	 */
	public function GetSubAdminByLoginAndPassword($sLogin, $sPassword)
	{
		return $this->getSubAdminByWhere(sprintf('%s = %s AND %s = %s',
			$this->escapeColumn('login'), $this->escapeString(strtolower($sLogin)),
			$this->escapeColumn('password'),
			$this->escapeString($sPassword)));
	}

	/**
	 * @param int $iSubAdminId
	 * @return string
	 */
	public function GetSubAdminById($iSubAdminId)
	{
		return $this->getSubAdminByWhere(sprintf('%s = %d',
			$this->escapeColumn('id_admin'), $iSubAdminId));
	}

	/**
	 * @param string $sWhere
	 * @return string
	 */
	protected function getSubAdminByWhere($sWhere)
	{
		$aMap = api_AContainer::DbReadKeys(CSubAdmin::GetStaticMap());
		$aMap = array_map(array($this, 'escapeColumn'), $aMap);

		$sSql = 'SELECT %s FROM %sawm_subadmins WHERE %s';

		return sprintf($sSql, implode(', ', $aMap), $this->Prefix(), $sWhere);
	}

	/**
	 * @param int $iSubAdminId
	 * @return string
	 */
	public function GetSubAdminDomainsById($iSubAdminId)
	{
		$sSql = 'SELECT id_domain FROM %sawm_subadmin_domains WHERE id_admin = %d';

		return sprintf($sSql, $this->Prefix(), $iSubAdminId);
	}

	/**
	 * @param CSubAdmin $oSubAdmin
	 * @return string
	 */
	function CreateSubAdmin(CSubAdmin $oSubAdmin)
	{
		$aResults = api_AContainer::DbInsertArrays($oSubAdmin, $this->oHelper);

		if ($aResults[0] && $aResults[1])
		{
			$sSql = 'INSERT INTO %sawm_subadmins ( %s ) VALUES ( %s )';
			return sprintf($sSql, $this->Prefix(), implode(', ', $aResults[0]), implode(', ', $aResults[1]));
		}
		return '';
	}

	/**
	 * @param CSubAdmin $oSubAdmin
	 * @return string
	 */
	function UpdateSubAdmin(CSubAdmin $oSubAdmin)
	{
		$aResult = api_AContainer::DbUpdateArray($oSubAdmin, $this->oHelper);

		$sSql = 'UPDATE %sawm_subadmins SET %s WHERE id_admin = %d';
		return sprintf($sSql, $this->Prefix(), implode(', ', $aResult), $oSubAdmin->IdSubAdmin);
	}

	/**
	 * @param string $sLogin
	 * @param int $niExceptSubAdminId = null
	 * @return string
	 */
	public function SubAdminExists($sLogin, $niExceptSubAdminId = null)
	{
		$sAddWhere = (is_integer($niExceptSubAdminId)) ? ' AND id_admin <> '.$niExceptSubAdminId : '';

		$sSql = 'SELECT COUNT(id_admin) as subadmins_count FROM %sawm_subadmins WHERE login = %s%s';

		return sprintf($sSql, $this->Prefix(), $this->escapeString(strtolower($sLogin)), $sAddWhere);
	}

	/**
	 * @param array $aSubAdminsIds
	 * @return string
	 */
	function DeleteSubAdmins($aSubAdminsIds)
	{
		$aIds = api_Utils::SetTypeArrayValue($aSubAdminsIds, 'int');

		$sSql = 'DELETE FROM %sawm_subadmins WHERE id_admin in (%s)';
		return sprintf($sSql, $this->Prefix(), implode(',', $aIds));
	}

	/**
	 * @param array $aSubAdminsIds
	 * @return string
	 */
	function DeleteSubAdminsDomains($aSubAdminsIds)
	{
		$aIds = api_Utils::SetTypeArrayValue($aSubAdminsIds, 'int');

		$sSql = 'DELETE FROM %sawm_subadmin_domains WHERE id_admin in (%s)';
		return sprintf($sSql, $this->Prefix(), implode(',', $aIds));
	}

	/**
	 * @param CSubAdmin $oSubAdmin
	 * @return string
	 */
	function AddSubAdminDomains(CSubAdmin $oSubAdmin)
	{
		$aDomainSql = array();
		foreach ($oSubAdmin->DomainIds as $iDomainId)
		{
			$aDomainSql[] = '('.((int) $oSubAdmin->IdSubAdmin).', '.((int) $iDomainId).')';
		}

		if (0 < count($aDomainSql))
		{
			$sSql = 'INSERT INTO %sawm_subadmin_domains (id_admin, id_domain) VALUES ';
			return sprintf($sSql, $this->Prefix()).implode(',', $aDomainSql);
		}

		return '';
	}

	/**
	 * @param int $iSubAdminId
	 * @return string
	 */
	function ClearSubAdminDomains($iSubAdminId)
	{
		$sSql = 'DELETE FROM %sawm_subadmin_domains WHERE id_admin = %d';

		return sprintf($sSql, $this->Prefix(), $iSubAdminId);
	}
}

/**
 * @package SubAdmins
 */
class CApiSubAdminsCommandCreatorMySQL extends CApiSubAdminsCommandCreator
{

	/**
	 * @param int $iPage
	 * @param int $iSubAdminsPerPage
	 * @param string $sOrderBy = 'login'
	 * @param bool $bOrderType = true
	 * @param string $sSearchDesc = ''
	 * @return string
	 */
	public function GetSubAdminList($iPage, $iSubAdminsPerPage, $sOrderBy = 'login', $bOrderType = true, $sSearchDesc = '')
	{
		$sWhere = '';
		if (!empty($sSearchDesc))
		{
			$sWhere = 'WHERE login LIKE '.$this->escapeString('%'.strtolower($sSearchDesc).'%');
		}

		$sOrderBy = empty($sOrderBy) ? 'login' : $sOrderBy;

		$sSql = 'SELECT id_admin, login, description FROM %sawm_subadmins %s ORDER BY %s %s LIMIT %d, %d';

		$sSql = sprintf($sSql, $this->Prefix(), $sWhere, $sOrderBy,
			((bool) $bOrderType) ? 'ASC' : 'DESC', ($iPage > 0) ? ($iPage - 1) * $iSubAdminsPerPage : 0,
			$iSubAdminsPerPage);

		return $sSql;
	}
}
