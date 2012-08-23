<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class CApiContactsVcardFormatter extends CIcsFormatter
{
	/**
	 * @var CGroup
	 */
	protected $oGroup;
	
	public function __construct()
	{
		parent::__construct('VCARD');

		$this->oGroup = null;

		$this->InitParameters(array(
			
			'tokens' => array(
				'EMAIL;TYPE=internet;TYPE=home' => 'HomeEmail',
				'FN' => 'FullName',
				'NOTE' => 'Notes',
				'TEL;TYPE=home' => 'HomePhone',
				'TEL;TYPE=home;TYPE=fax' => 'HomeFax',
				'TEL;TYPE=home;TYPE=cell' => 'HomeMobile',
				'URL;TYPE=home' => 'HomeWeb',

				'EMAIL;TYPE=internet;TYPE=work' => 'BusinessEmail',
				'TITLE' => 'BusinessJobTitle',
				'TEL;TYPE=work' => 'BusinessPhone',
				'TEL;TYPE=work;TYPE=fax' => 'BusinessFax',
				'URL;TYPE=work' => 'BusinessWeb',

				'EMAIL;TYPE=internet' => 'OtherEmail',

				'UID' => 'IdContactStr'
			),

			'static' => array(
				'VERSION' => '3.0',
			),

			'tokensWithSpecialTreatment' => array(
				'ADR;TYPE=home' => array('addrForm',
					'', 'HomeStreet', 'HomeCity',	'HomeState', 'HomeZip', 'HomeCountry'),

				'ADR;TYPE=work' => array('addrForm',
					'BusinessOffice', 'BusinessStreet', 'BusinessCity',
					'BusinessState', 'BusinessZip', 'BusinessCountry'),
				
				'ORG' => array('orgForm', 'BusinessDepartment', 'BusinessCompany'),
				'BDAY' => array('bdayForm', 'BirthdayDay', 'BirthdayMonth', 'BirthdayYear'),
				'REV' => array('utcDateForm', 'DateModified'),

				'X-WR-GROUPID' => array('xwrGroupStrId'),
				'X-WR-GROUPNAME' => array('xwrGroupName'),
			)
		));
	}

	/**
	 * @param mixed $oContainer
	 */
	public function SetContainer($oContainer)
	{
		parent::SetContainer($oContainer);

		$this->oGroup = null;
	}

	/**
	 * @param CGroup $oGroup
	 */
	public function SetGroup(CGroup $oGroup)
	{
		$this->oGroup = $oGroup;
	}

	/**
	 * @param string $sToken
	 * @return string
	 */
	protected function xwrGroupStrId($sToken)
	{
		return ($this->oGroup) ? $sToken.':'.$this->escapeValue($this->oGroup->IdGroupStr) : '';
	}

	/**
	 * @param string $sToken
	 * @return string
	 */
	protected function xwrGroupName($sToken)
	{
		return ($this->oGroup) ? $sToken.':'.$this->escapeValue($this->oGroup->Name) : '';
	}

	/**
	 * @param string $sToken
	 * @param string $sOfficeFieldName = ''
	 * @param string $sStreetFieldName = ''
	 * @param string $sCityFieldName = ''
	 * @param string $sStateFieldName = ''
	 * @param string $sZipFieldName = ''
	 * @param string $sCountryFieldName = ''
	 * @return string
	 */
	protected function addrForm($sToken,
		$sOfficeFieldName = '', $sStreetFieldName = '', $sCityFieldName = '',
		$sStateFieldName = '', $sZipFieldName = '', $sCountryFieldName = '')
	{
		$sOffice = empty($sOfficeFieldName) ? '' : trim($this->oContainer->{$sOfficeFieldName});
		$sStreet = empty($sStreetFieldName) ? '' : trim($this->oContainer->{$sStreetFieldName});
		$sCity = empty($sCityFieldName)? '' : trim($this->oContainer->{$sCityFieldName});
		$sState = empty($sStateFieldName) ? '' : trim($this->oContainer->{$sStateFieldName});
		$sZip = empty($sZipFieldName) ? '' : trim($this->oContainer->{$sZipFieldName});
		$sCountry = empty($sCountryFieldName) ? '' : trim($this->oContainer->{$sCountryFieldName});

		if (0 < strlen($sOffice.$sStreet.$sCity.$sState.$sZip.$sCountry))
		{
			return $sToken.':'.sprintf('%s;%s;%s;%s;%s;%s;%s', '',
				$this->escapeValue($sOffice), $this->escapeValue($sStreet),
				$this->escapeValue($sCity), $this->escapeValue($sState),
				$this->escapeValue($sZip), $this->escapeValue($sCountry));
		}

		return '';
	}

	/**
	 * @param string $sToken
	 * @param string $sDepartmentFieldName
	 * @param string $sCompanyFieldName
	 * @return string
	 */
	protected function orgForm($sToken, $sDepartmentFieldName, $sCompanyFieldName)
	{
		$sDepartment = trim($this->oContainer->{$sDepartmentFieldName});
		$sCompany = trim($this->oContainer->{$sCompanyFieldName});

		$sResult = '';
		if (!empty($sDepartment))
		{
			$sResult .= sprintf('%s;%s', $this->escapeValue($sCompany), $this->escapeValue($sDepartment));
		}
		else if (!empty($sCompany))
		{
			$sResult .= $sCompany;
		}

		return !empty($sCompany) ? $sToken.':'.$sResult : '';
	}

	/**
	 * @param string $sToken
	 * @param string $sDayFieldName
	 * @param string $sMonthFieldName
	 * @param string $sYearFieldName
	 * @return string
	 */
	protected function bdayForm($sToken, $sDayFieldName, $sMonthFieldName, $sYearFieldName)
	{
		$iDay = $this->oContainer->{$sDayFieldName};
		$iMonth = $this->oContainer->{$sMonthFieldName};
		$iYear = $this->oContainer->{$sYearFieldName};

		return (0 < $iDay && 0 < $iMonth && 0 < $iYear) ? $sToken.':'.$iYear.'-'.$iMonth.'-'.$iDay : '';
	}

	/**
	 * @param string $sToken
	 * @param string $sDateFieldName
	 * @return string
	 */
	protected function utcDateForm($sToken, $sDateFieldName)
	{
		$iUtcTimeStamp = $this->oContainer->{$sDateFieldName};

		return (0 < $iUtcTimeStamp) ? $sToken.':'.$this->escapeValue(
			gmdate('Ymd', $iUtcTimeStamp).'T'.gmdate('His', $iUtcTimeStamp).'Z') : '';
	}
}
