<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

/**
 * @property int $IdSubAdmin
 * @property bool $IsOwner
 * @property bool $IsDisabled
 * @property string $Login
 * @property string $Password
 * @property string $Description
 * @property array $DomainIds
 *
 * @package SubAdmins
 * @subpackage Classes
 */
class CSubAdmin extends api_AContainer
{
	public function __construct()
	{
		parent::__construct(get_class($this), 'IdSubAdmin');

		$this->__USE_TRIM_IN_STRINGS__ = true;

		$this->SetDefaults(array(
			'IdSubAdmin'	=> 0,
			'IsOwner'		=> false,
			'IsDisabled'	=> false,
			'Login'			=> '',
			'Password'		=> '',
			'Description'	=> '',
			'DomainIds'		=> array()
		));
	}

	/**
	 * @return bool
	 */
	public function Validate()
	{
		switch (true)
		{
			case api_Validate::IsEmpty($this->Login):
				throw new CApiValidationException(Errs::Validation_FieldIsEmpty, null, array(
					'{{ClassName}}' => 'CSubAdmin', '{{ClassField}}' => 'Login'));

			case api_Validate::IsEmpty($this->Password):
				throw new CApiValidationException(Errs::Validation_FieldIsEmpty, null, array(
					'{{ClassName}}' => 'CSubAdmin', '{{ClassField}}' => 'Password'));

			case !$this->IsOwner && (!is_array($this->DomainIds) || count($this->DomainIds) < 1):
				throw new CApiValidationException(Errs::Validation_FieldIsEmpty, null, array(
					'{{ClassName}}' => 'CSubAdmin', '{{ClassField}}' => 'DomainIds'));
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
			'IdSubAdmin'	=> array('int', 'id_admin', false),
			'IsOwner'		=> array('bool', 'is_owner', true, false),
			'IsDisabled'	=> array('bool', 'disabled'),
			'Login'			=> array('string(255)', 'login'),
			'Password'		=> array('string(255)', 'password'),
			'Description'	=> array('string(255)', 'description'),
			'DomainIds'		=> array('array')
		);
	}
}
