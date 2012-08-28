<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class CApiContactsVcardNullParser
{
	public function closeParser()
	{
		return false;
	}

	public function isTokenRegister()
	{
		return false;
	}
}

class CApiContactsVcardParser extends CIcsParser
{
	public function __construct()
	{
		parent::__construct('VCARD');

		$this->InitParameters(array(
			
			'tokens' => array(),

			'enclosed' => array(

				'VCARD' => array(
					
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

						'UID' => 'IdContactStr',

						'X-WR-GROUPID' => 'GroupStrId',
						'X-WR-GROUPNAME' => 'GroupName'
					),
					
					'static' => array(
						'VERSION' => '3.0'
					),
					
					'tokensWithSpecialTreatmentImport' => array(
						'ADR;TYPE=home' => 'addrImportForm',
						'ADR;TYPE=work' => 'addrImportForm',
						'ORG' => 'orgImportForm',
						'BDAY' => 'bdayImportForm',
						'REV' => 'utcDateImportForm',
					),
				),
			)
		));
		
	}

	public function parseOperateStructure($sOperateStructure)
    {
		return array($sOperateStructure, null);
    }

	public function createNewParser()
    {
		$oParser = null;
        if (array_key_exists($this->sCurrentValue, $this->aMap['enclosed']))
        {
            $oParser = new CApiContactsVcardParser($this->sCurrentValue);
            $aMapForEnclosed =& $this->aMap['enclosed'][$this->sCurrentValue];
            $oParser->initParameters($aMapForEnclosed);
        }
        else
        {
            $oParser = new CApiContactsVcardNullParser($this->sCurrentValue);
        }

        if ($oParser && true === $oParser->parseData($this->oDataSource))
        {
            $this->aParameters[$this->sCurrentValue][] = $oParser;
        }
    }

	protected function utcDateImportForm($sToken, $sTokenValue)
	{
		$sDate = substr($sTokenValue, 0, 4).'-'.substr($sTokenValue, 4, 2).'-'.substr($sTokenValue, 6, 2);
		if (false !== strpos($sTokenValue, 'T'))
		{
			$sDate .= ' '.substr($sTokenValue, 9, 2).':'.substr($sTokenValue, 11, 2).':'.substr($sTokenValue, 13, 2);
		}
		else
		{
			$sDate .= ' 00:00:00';
		}

		$iDateTime = 0;
		$aDateTime = api_Utils::DateParse($sDate);
		if (is_array($aDateTime))
		{
			$iDateTime = gmmktime($aDateTime['hour'], $aDateTime['minute'], $aDateTime['second'],
				$aDateTime['month'], $aDateTime['day'], $aDateTime['year']);

			if (false === $iDateTime || $iDateTime <= 0)
			{
				$iDateTime = 0;
			}
		}
		
		return array('DateModified' => $iDateTime);
	}

	protected function orgImportForm($sToken, $sTokenValue)
	{
		$aExplodeArray = preg_split('/;/', $sTokenValue, 2);

		$sCompany = isset($aExplodeArray[0]) ? $aExplodeArray[0] : '';
		$sDepartment = isset($aExplodeArray[1]) ? $aExplodeArray[1] : '';

		$aReturn = array();
		if (!empty($sCompany))
		{
			$aReturn['BusinessCompany'] = $sCompany;
		}
		if (!empty($sDepartment))
		{
			$aReturn['BusinessDepartment'] = $sDepartment;
		}

		return $aReturn;
	}

	protected function bdayImportForm($sToken, $sTokenValue)
	{
		$aExplodeArray = explode('-', $sTokenValue, 3);

		$iYear = isset($aExplodeArray[0]) ? (int) $aExplodeArray[0] : 0;
		$iMonth = isset($aExplodeArray[1]) ? (int) $aExplodeArray[1] : 0;
		$iDay = isset($aExplodeArray[2]) ? (int) $aExplodeArray[2] : 0;

		$aReturn = array();
		if ($iDay > 0 && $iMonth > 0 && $iYear > 0)
		{
			$aReturn['BirthdayDay'] = $iDay;
			$aReturn['BirthdayMonth'] = $iMonth;
			$aReturn['BirthdayYear'] = $iYear;
		}
		
		return $aReturn;
	}

	protected function addrImportForm($sToken, $sTokenValue)
	{
		$aExplodeArray = preg_split('/;/', $sTokenValue, 7);

		$sOffice = isset($aExplodeArray[1]) ? $aExplodeArray[1] : '';
		$sStreet = isset($aExplodeArray[2]) ? $aExplodeArray[2] : '';
		$sCity = isset($aExplodeArray[3]) ? $aExplodeArray[3] : '';
		$sState = isset($aExplodeArray[4]) ? $aExplodeArray[4] : '';
		$sZip = isset($aExplodeArray[5]) ? $aExplodeArray[5] : '';
		$sCountry = isset($aExplodeArray[6]) ? $aExplodeArray[6] : '';

		$aReturn = array();
		if ('ADR;TYPE=HOME' === strtoupper($sToken))
		{
			if (!empty($sStreet))
			{
				$aReturn['HomeStreet'] = $sStreet;
			}

			if (!empty($sCity))
			{
				$aReturn['HomeCity'] = $sCity;
			}

			if (!empty($sState))
			{
				$aReturn['HomeState'] = $sState;
			}

			if (!empty($sZip))
			{
				$aReturn['HomeZip'] = $sZip;
			}
			
			if (!empty($sCountry))
			{
				$aReturn['HomeCountry'] = $sCountry;
			}
		}
		else if ('ADR;TYPE=WORK' === strtoupper($sToken))
		{
			if (!empty($sOffice))
			{
				$aReturn['BusinessOffice'] = $sOffice;
			}
			
			if (!empty($sStreet))
			{
				$aReturn['BusinessStreet'] = $sStreet;
			}

			if (!empty($sCity))
			{
				$aReturn['BusinessCity'] = $sCity;
			}

			if (!empty($sState))
			{
				$aReturn['BusinessState'] = $sState;
			}

			if (!empty($sZip))
			{
				$aReturn['BusinessZip'] = $sZip;
			}

			if (!empty($sCountry))
			{
				$aReturn['BusinessCountry'] = $sCountry;
			}
		}

		return $aReturn;
	}
}