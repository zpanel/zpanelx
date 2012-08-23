<?php

/**
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 * 
 */

/**
 * @property mixed $IdContact
 * @property string $IdContactStr
 * @property int $IdUser
 * @property array $GroupsIds
 * @property string $FullName
 * @property bool $UseFriendlyName
 * @property string $ViewEmail
 * @property int $PrimaryEmail
 * @property string $Title
 * @property string $FirstName
 * @property string $SurName
 * @property string $NickName
 * @property string $HomeEmail
 * @property string $HomeStreet
 * @property string $HomeCity
 * @property string $HomeState
 * @property string $HomeZip
 * @property string $HomeCountry
 * @property string $HomePhone
 * @property string $HomeFax
 * @property string $HomeMobile
 * @property string $HomeWeb
 * @property string $BusinessEmail
 * @property string $BusinessCompany
 * @property string $BusinessStreet
 * @property string $BusinessCity
 * @property string $BusinessState
 * @property string $BusinessZip
 * @property string $BusinessCountry
 * @property string $BusinessJobTitle
 * @property string $BusinessDepartment
 * @property string $BusinessOffice
 * @property string $BusinessPhone
 * @property string $BusinessMobile
 * @property string $BusinessFax
 * @property string $BusinessWeb
 * @property string $OtherEmail
 * @property string $Notes
 * @property int $BirthdayDay
 * @property int $BirthdayMonth
 * @property int $BirthdayYear
 * @property bool $ReadOnly;
 *
 * @package Contacts
 * @subpackage Classes
 */
class CContact extends api_AContainer
{
	const STR_PREFIX = '040000008200E00074C5B7101A82E008';

	/**
	 * @var bool
	 */
	public $__LOCK_DATE_MODIFIED__;

	/**
	 * @var bool
	 */
	public $__SKIP_VALIDATE__;

	public function __construct()
	{
		parent::__construct(get_class($this), 'IdContact');

		$this->__USE_TRIM_IN_STRINGS__ = true;

		$this->SetDefaults(array(
			'IdContact'		=> '',
			'IdContactStr'	=> '',
			'IdUser'		=> 0,

			'GroupsIds'			=> array(),

			'FullName'			=> '',
			'UseFriendlyName'	=> true,
			'ViewEmail'			=> '',
			'PrimaryEmail'		=> CApi::GetConf('contacts.default-primary-email', EPrimaryEmailType::Home),

			'DateCreated'		=> time(),
			'DateModified'		=> time(),

			'Title'			=> '',
			'FirstName'		=> '',
			'SurName'		=> '',
			'NickName'		=> '',

			'HomeEmail'		=> '',
			'HomeStreet'	=> '',
			'HomeCity'		=> '',
			'HomeState'		=> '',
			'HomeZip'		=> '',
			'HomeCountry'	=> '',
			'HomePhone'		=> '',
			'HomeFax'		=> '',
			'HomeMobile'	=> '',
			'HomeWeb'		=> '',

			'BusinessEmail'		=> '',
			'BusinessCompany'	=> '',
			'BusinessStreet'	=> '',
			'BusinessCity'		=> '',
			'BusinessState'		=> '',
			'BusinessZip'		=> '',
			'BusinessCountry'	=> '',
			'BusinessJobTitle'	=> '',
			'BusinessDepartment'=> '',
			'BusinessOffice'	=> '',
			'BusinessPhone'		=> '',
			'BusinessMobile'	=> '',
			'BusinessFax'		=> '',
			'BusinessWeb'		=> '',

			'OtherEmail'		=> '',
			'Notes'				=> '',

			'BirthdayDay'		=> 0,
			'BirthdayMonth'		=> 0,
			'BirthdayYear'		=> 0,

			'ReadOnly'			=> false
		));

		$this->__LOCK_DATE_MODIFIED__ = false;
		$this->__SKIP_VALIDATE__ = false;

		CApi::Plugin()->RunHook('api-contact-construct', array(&$this));
	}

	/**
	 * @return string
	 */
	public function GenerateStrId()
	{
		return self::STR_PREFIX.$this->IdContact;
	}

	/**
	 * @return void
	 */
	public function InitDependentValues()
	{
		if (0 === strlen($this->FullName))
		{
			$this->FullName = trim($this->FirstName.' '.$this->SurName);
		}
	}

	/**
	 * @return bool
	 */
	public function InitBeforeChange()
	{
		parent::InitBeforeChange();

		if (0 === strlen($this->IdContactStr) &&
			((is_int($this->IdContact) && 0 < $this->IdContact) ||
			(is_string($this->IdContact) && 0 < strlen($this->IdContact)))
		)
		{
			$this->IdContactStr = $this->GenerateStrId();
		}

		if (!$this->__LOCK_DATE_MODIFIED__)
		{
			$this->DateModified = time();
		}

		switch ((int) $this->PrimaryEmail)
		{
			case EPrimaryEmailType::Home:
				$this->ViewEmail = (string) $this->HomeEmail;
				break;
			case EPrimaryEmailType::Business:
				$this->ViewEmail = (string) $this->BusinessEmail;
				break;
			case EPrimaryEmailType::Other:
				$this->ViewEmail = (string) $this->OtherEmail;
				break;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function Validate()
	{
		if (!$this->__SKIP_VALIDATE__)
		{
			switch (true)
			{
				case
					api_Validate::IsEmpty($this->FullName) &&
					api_Validate::IsEmpty($this->HomeEmail) &&
					api_Validate::IsEmpty($this->BusinessEmail) &&
					api_Validate::IsEmpty($this->OtherEmail):

					throw new CApiValidationException(Errs::Validation_FieldIsEmpty_OutInfo);
			}
		}

		return true;
	}

	/**
	 * @return array
	 */
	public function GetMap()
	{
		return self::GetStaticMap();
	}

	/**
	 * @return array
	 */
	public static function GetStaticMap()
	{
		return array(
			'IdContact'		=> array('string', 'id_addr', false, false),
			'IdContactStr'	=> array('string(255)', 'str_id', false),
			'IdUser'		=> array('int', 'id_user'),

			'GroupsIds'			=> array('array'),

			'FullName'			=> array('string(255)', 'fullname'),
			'UseFriendlyName'	=> array('bool', 'use_friendly_nm'),
			'ViewEmail'			=> array('string(255)', 'view_email'),
			'PrimaryEmail'		=> array('int', 'primary_email'),

			'DateCreated'		=> array('datetime', 'date_created', true, false),
			'DateModified'		=> array('datetime', 'date_modified'),

			'Title'			=> array('string'),
			'FirstName'		=> array('string'),
			'SurName'		=> array('string'),
			'NickName'		=> array('string'),

			'HomeEmail'		=> array('string(255)', 'h_email'),
			'HomeStreet'	=> array('string(255)', 'h_street'),
			'HomeCity'		=> array('string(200)', 'h_city'),
			'HomeState'		=> array('string(200)', 'h_state'),
			'HomeZip'		=> array('string(10)', 'h_zip'),
			'HomeCountry'	=> array('string(200)', 'h_country'),
			'HomePhone'		=> array('string(50)', 'h_phone'),
			'HomeFax'		=> array('string(50)', 'h_fax'),
			'HomeMobile'	=> array('string(50)', 'h_mobile'),
			'HomeWeb'		=> array('string(255)', 'h_web'),

			'BusinessEmail'		=> array('string(255)', 'b_email'),
			'BusinessCompany'	=> array('string(200)', 'b_company'),
			'BusinessStreet'	=> array('string(255)', 'b_street'),
			'BusinessCity'		=> array('string(200)', 'b_city'),
			'BusinessState'		=> array('string(200)', 'b_state'),
			'BusinessZip'		=> array('string(10)', 'b_zip'),
			'BusinessCountry'	=> array('string(200)', 'b_country'),
			'BusinessJobTitle'	=> array('string(100)', 'b_job_title'),
			'BusinessDepartment'=> array('string(200)', 'b_department'),
			'BusinessOffice'	=> array('string(200)', 'b_office'),
			'BusinessPhone'		=> array('string(50)', 'b_phone'),
			'BusinessMobile'	=> array('string'),
			'BusinessFax'		=> array('string(50)', 'b_fax'),
			'BusinessWeb'		=> array('string(255)', 'b_web'),

			'OtherEmail'		=> array('string(255)', 'other_email'),
			'Notes'				=> array('string(255)', 'notes'),

			'BirthdayDay'		=> array('int', 'birthday_day'),
			'BirthdayMonth'		=> array('int', 'birthday_month'),
			'BirthdayYear'		=> array('int', 'birthday_year'),

			'ReadOnly'		=> array('bool')
		);
	}
}
