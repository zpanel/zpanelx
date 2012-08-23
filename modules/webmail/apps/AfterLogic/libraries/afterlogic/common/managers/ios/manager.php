<?php

/*
 * Copyright (C) 2002-2011  AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in LICENSE.txt
 *
 */

require_once CApi::RootPath().'/DAV/autoload.php';

class CApiIosManager extends AApiManager
{
	/**
	 * @var DOMDocument
	 */
	private $oXmlDocument;

	/*
	 * @var CApiUsersManager
	 */
	private $oApiUsersManager;

	/*
	 * @var CApiCalendarManager
	 */
	private $oApiCalendarManager;

	/*
	 * @var CApiDavManager
	 */
	private $oApiDavManager;

	/**
	 * @var CAccout
	 */
	private $oAccount;

	/**
	 * @param CApiGlobalManager &$oManager
	 */
	public function __construct(CApiGlobalManager &$oManager)
	{
		parent::__construct('ios', $oManager);

		$oDomImplementation = new DOMImplementation();
		$oDocumentType = $oDomImplementation->createDocumentType(
			'plist',
			'-//Apple//DTD PLIST 1.0//EN',
			'http://www.apple.com/DTDs/PropertyList-1.0.dtd'
		);

		$this->oXmlDocument = $oDomImplementation->createDocument('', '', $oDocumentType);
		$this->oXmlDocument->xmlVersion = '1.0';
		$this->oXmlDocument->encoding = 'UTF-8';
		$this->oXmlDocument->formatOutput = true;

		$this->oApiUsersManager = CApi::Manager('users');
		$this->oApiCalendarManager = CApi::Manager('calendar');
		$this->oApiDavManager = CApi::Manager('dav');

		$this->oAccount = null;
		$iUserId = CSession::Get(APP_SESSION_USER_ID);
		if (0 < $iUserId)
		{
			$iAccountId = $this->oApiUsersManager->GetDefaultAccountId($iUserId);
			if (0 < $iAccountId)
			{
				$this->oAccount = $this->oApiUsersManager->GetAccountById($iAccountId);
			}
		}
	}

	/**
	 * @return DOMElement
	 */
	private function generateDict($aPayload)
	{
		$oDictElement = $this->oXmlDocument->createElement('dict');

		foreach ($aPayload as $sKey => $mValue)
		{
			$oDictElement->appendChild($this->oXmlDocument->createElement('key', $sKey));

			if (is_int($mValue))
			{
				$oDictElement->appendChild($this->oXmlDocument->createElement('integer', $mValue));
			}
			else if (is_bool($mValue))
			{
				$oDictElement->appendChild($this->oXmlDocument->createElement($mValue ? 'true': 'false'));
			}
			else
			{
				$oDictElement->appendChild($this->oXmlDocument->createElement('string', $mValue));
			}
		}
		return $oDictElement;
	}

	/**
	 * @param string $sPayloadId
	 * @param CAccount $oAccount
	 * @return DOMElement
	 */
	private function generateEmailDict($sPayloadId, $oAccount)
	{
		$bIsDemo = false;
		CApi::Plugin()->RunHook('plugin-is-demo-account', array(&$oAccount, &$bIsDemo));

		$aEmail = array(
			'PayloadVersion'					=> 1,
			'PayloadUUID'						=> Sabre_DAV_UUIDUtil::getUUID(),
			'PayloadType'						=> 'com.apple.mail.managed',
			'PayloadIdentifier'					=> $sPayloadId.'.email',
			'PayloadDisplayName'				=> 'Email Account',
			'PayloadOrganization'				=> $oAccount->Domain->SiteName,
			'PayloadDescription'				=> 'Configures email account',
			'EmailAddress'						=> $oAccount->Email,
			'EmailAccountType'					=> EMailProtocol::IMAP4 === $oAccount->IncomingMailProtocol
				? 'EmailTypeIMAP' : 'EmailTypePOP',
			'EmailAccountDescription'			=> $oAccount->Email,
			'EmailAccountName'					=> 0 === strlen($oAccount->FriendlyName)
				? $oAccount->Email : $oAccount->FriendlyName,
			'IncomingMailServerHostName'		=> $oAccount->IncomingMailServer,
			'IncomingMailServerPortNumber'		=> $oAccount->IncomingMailPort,
			'IncomingMailServerUseSSL'			=> $oAccount->IncomingMailUseSSL,
			'IncomingMailServerUsername'		=> $oAccount->IncomingMailLogin,
			'IncomingPassword'					=> $oAccount->IncomingMailPassword,
			'IncomingMailServerAuthentication'	=> 'EmailAuthPassword',
			'OutgoingMailServerHostName'		=> $oAccount->OutgoingMailServer,
			'OutgoingMailServerPortNumber'		=> $oAccount->OutgoingMailPort,
			'OutgoingMailServerUseSSL'			=> $oAccount->OutgoingMailUseSSL,
			'OutgoingMailServerUsername'		=> 0 === strlen($oAccount->OutgoingMailLogin)
				? $oAccount->IncomingMailLogin : $oAccount->OutgoingMailLogin,
			'OutgoingPassword'					=> $bIsDemo ? 'password' : (0 === strlen($oAccount->OutgoingMailPassword)
				? $oAccount->IncomingMailPassword : $oAccount->OutgoingMailPassword),
			'OutgoingMailServerAuthentication'	=> ESMTPAuthType::NoAuth === $oAccount->OutgoingMailAuth
				? 'EmailAuthNone' : 'EmailAuthPassword',
		);

		return $this->generateDict($aEmail);
	}

	/**
	 * @param string $sPayloadId
	 * @return DOMElement
	 */
	private function generateCaldavDict($sPayloadId)
	{
		$aCaldav = array(
			'PayloadVersion'			=> 1,
			'PayloadUUID'				=> Sabre_DAV_UUIDUtil::getUUID(),
			'PayloadType'				=> 'com.apple.caldav.account',
			'PayloadIdentifier'			=> $sPayloadId.'.caldav',
			'PayloadDisplayName'		=> 'CalDAV Account',
			'PayloadOrganization'		=> $this->oAccount->Domain->SiteName,
			'PayloadDescription'		=> 'Configures CalDAV Account',
			'CalDAVAccountDescription'	=> $this->oAccount->Domain->SiteName.' Calendars',
			'CalDAVHostName'			=> $this->oApiDavManager->GetServerHost(),
			'CalDAVUsername'			=> $this->oAccount->Email,
			'CalDAVPassword'			=> $this->oAccount->IncomingMailPassword,
			'CalDAVUseSSL'				=> $this->oApiDavManager->IsUseSsl(),
			'CalDAVPort'				=> $this->oApiDavManager->GetServerPort(),
			'CalDAVPrincipalURL'		=> $this->oApiDavManager->GetPrincipalUrl($this->oAccount),
		);

		return $this->generateDict($aCaldav);
	}

	/**
	 * @param string $sPayloadId
	 * @return DOMElement
	 */
	private function generateCarddavDict($sPayloadId)
	{
		$aCarddav = array(
			'PayloadVersion'			=> 1,
			'PayloadUUID'				=> Sabre_DAV_UUIDUtil::getUUID(),
			'PayloadType'				=> 'com.apple.carddav.account',
			'PayloadIdentifier'			=> $sPayloadId.'.carddav',
			'PayloadDisplayName'		=> 'CardDAV Account',
			'PayloadOrganization'		=> $this->oAccount->Domain->SiteName,
			'PayloadDescription'		=> 'Configures CardDAV Account',
			'CardDAVAccountDescription'	=> $this->oAccount->Domain->SiteName.' Contacts',
			'CardDAVHostName'			=> $this->oApiDavManager->GetServerHost(),
			'CardDAVUsername'			=> $this->oAccount->Email,
			'CardDAVPassword'			=> $this->oAccount->IncomingMailPassword,
			'CardDAVUseSSL'				=> $this->oApiDavManager->IsUseSsl(),
			'CardDAVPort'				=> $this->oApiDavManager->GetServerPort(),
			'CardDAVPrincipalURL'		=> $this->oApiDavManager->GetPrincipalUrl($this->oAccount),
		);

		return $this->generateDict($aCarddav);
	}

	/**
	 * @return string
	 */
	public function GenerateXMLProfile()
	{
		$sResult = '';
		if (isset($this->oAccount))
		{
			$oPlist = $this->oXmlDocument->createElement('plist');
			$oPlist->setAttribute('version', '1.0');

			$sPayloadId = 'afterlogic.'.$this->oApiDavManager->GetServerHost();
			$aPayload = array(
				'PayloadVersion'			=> 1,
				'PayloadUUID'				=> Sabre_DAV_UUIDUtil::getUUID(),
				'PayloadType'				=> 'Configuration',
				'PayloadRemovalDisallowed'	=> false,
				'PayloadIdentifier'			=> $sPayloadId,
				'PayloadOrganization'		=> $this->oAccount->Domain->SiteName,
				'PayloadDescription'		=> $this->oAccount->Domain->SiteName.' Mobile',
				'PayloadDisplayName'		=> $this->oAccount->Domain->SiteName.' Mobile Profile',
			);

			$oArrayElement = $this->oXmlDocument->createElement('array');

			// Emails
			$oAccounts = AppGetAccounts($this->oAccount);
			foreach ($oAccounts as $oAccount)
			{
				$oEmailDictElement = $this->generateEmailDict($sPayloadId, $oAccount);
				$oArrayElement->appendChild($oEmailDictElement);
			}

			if (true || $this->oApiDavManager->TestConnection($this->oAccount))
			{
				// Calendars
				$oCaldavDictElement = $this->generateCaldavDict($sPayloadId);
				$oArrayElement->appendChild($oCaldavDictElement);

				// Contacts
				$oCarddavDictElement = $this->generateCarddavDict($sPayloadId);
				$oArrayElement->appendChild($oCarddavDictElement);
			}

			$oDictElement = $this->generateDict($aPayload);
			$oPayloadContentElement = $this->oXmlDocument->createElement('key', 'PayloadContent');
			$oDictElement->appendChild($oPayloadContentElement);
			$oDictElement->appendChild($oArrayElement);
			$oPlist->appendChild($oDictElement);

			$this->oXmlDocument->appendChild($oPlist);
			$sResult = $this->oXmlDocument->saveXML();
		}

		return $sResult;
	}
}
