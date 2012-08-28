<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

/**
 * @property int $IdUser
 * @property int $MailsPerPage
 * @property int $ContactsPerPage
 * @property int $AutoCheckMailInterval
 * @property int $LastLogin
 * @property int $LoginsCount
 * @property string $DefaultSkin
 * @property string $DefaultLanguage
 * @property int $DefaultEditor
 * @property int $SaveMail
 * @property int $Layout
 * @property string $DefaultIncomingCharset
 * @property int $DefaultTimeZone
 * @property int $DefaultTimeFormat
 * @property string $DefaultDateFormat
 * @property string $Question1
 * @property string $Question2
 * @property string $Answer1
 * @property string $Answer2
 * @property bool $AllowWebmail
 * @property bool $AllowContacts
 * @property bool $AllowCalendar
 * @property mixed $CustomFields
 *
 * @package Users
 * @subpackage Classes
 */
class CUser extends api_AContainer
{
	/**
	 * @return void
	 */
	public function __construct(CDomain $oDomain)
	{
		parent::__construct(get_class($this), 'IdUser');

		$oSettings =& CApi::GetSettings();
		$iSaveMail = $oSettings->GetConf('WebMail/SaveMail');
		$iSaveMail = ESaveMail::Always !== $iSaveMail
			? $oSettings->GetConf('WebMail/SaveMail') : ESaveMail::DefaultOn;

		$this->__USE_TRIM_IN_STRINGS__ = true;

		$this->SetDefaults(array(
			'IdUser' => 0,

			'MailsPerPage'			=> $oDomain->MailsPerPage,
			'ContactsPerPage'		=> $oDomain->ContactsPerPage,
			'AutoCheckMailInterval'	=> $oDomain->AutoCheckMailInterval,

			'LastLogin'		=> 0,
			'LoginsCount'	=> 0,

			'DefaultSkin'		=> $oDomain->DefaultSkin,
			'DefaultLanguage'	=> $oDomain->DefaultLanguage,
			'DefaultEditor'		=> EUserHtmlEditor::Html,
			'SaveMail'			=> $iSaveMail,
			'Layout'			=> $oDomain->Layout,

			'DefaultTimeZone'	=> $oDomain->DefaultTimeZone,
			'DefaultTimeFormat'	=> $oDomain->DefaultTimeFormat,
			'DefaultDateFormat'	=> 'MM/DD/YY', // TODO Magic

			'DefaultIncomingCharset' => CApi::GetConf('webmail.default-inc-charset', 'iso-8859-1'),

			'Question1'	=> '',
			'Question2'	=> '',
			'Answer1'	=> '',
			'Answer2'	=> '',

			'AllowWebmail'		=> $oDomain->AllowWebMail,
			'AllowContacts'		=> $oDomain->AllowContacts,
			'AllowCalendar'		=> $oDomain->AllowCalendar,

			'CustomFields'		=> ''
		));

		CApi::Plugin()->RunHook('api-user-construct', array(&$this));
	}

	/**
	 * @return bool
	 */
	public function Validate()
	{
		switch (true)
		{
			case false:
				throw new CApiValidationException(Errs::Validation_FieldIsEmpty, null, array(
					'{{ClassName}}' => 'CUser', '{{ClassField}}' => 'Error'));
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

			'IdUser' => array('int', 'id_user'),

			'MailsPerPage'			=> array('int', 'msgs_per_page'),
			'ContactsPerPage'		=> array('int', 'contacts_per_page'),
			'AutoCheckMailInterval'	=> array('int', 'auto_checkmail_interval'),

			'LastLogin'			=> array('datetime', 'last_login'),
			'LoginsCount'		=> array('int', 'logins_count'),

			'DefaultSkin'		=> array('string(255)', 'def_skin'),
			'DefaultLanguage'	=> array('string(255)', 'def_lang'),
			'DefaultEditor'		=> array('int', 'def_editor'),
			'SaveMail'			=> array('int', 'save_mail'),
			'Layout'			=> array('int', 'layout'),

			'DefaultIncomingCharset'	=> array('string(30)', 'incoming_charset'),

			'DefaultTimeZone'	=> array('int', 'def_timezone'),
			'DefaultTimeFormat'	=> array('int', 'def_time_fmt'),
			'DefaultDateFormat'	=> array('string(255)', 'def_date_fmt'),

			'Question1'	=> array('string(255)', 'question_1'),
			'Question2'	=> array('string(255)', 'question_2'),
			'Answer1'	=> array('string(255)', 'answer_1'),
			'Answer2'	=> array('string(255)', 'answer_2'),

			'AllowWebmail'		=> array('bool'),
			'AllowContacts'		=> array('bool'),
			'AllowCalendar'		=> array('bool'),

			'CustomFields'		=> array('serialize', 'custom_fields')
		);
	}
}
