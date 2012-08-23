<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../../../'));

include_once WM_ROOTPATH.'libraries/afterlogic/api.php';
require_once WM_ROOTPATH.'application/include.php';
require_once WM_ROOTPATH.'common/inc_constants.php';

class afterlogic_DAV_CardDAV_GlobaAddressBooks extends Sabre_DAV_Directory implements Sabre_CardDAV_IDirectory {

	/** 
	 * @var $apiGcontactsManager CApiGcontactsManager
	 */
	private $apiGcontactsManager;	

	/** 
	 * @var $apiUsersManager CApiUsersManager
	 */
	private $apiUsersManager;	

    /** 
	 * @var Sabre_DAV_Auth_Plugin
     */
    private $authPlugin;	

	/**
     * Constructor
     */
    public function __construct(Sabre_DAV_Auth_Plugin $authPlugin) 
	{
		$oApiCollaborationManager = CApi::Manager('collaboration');
		if (isset($oApiCollaborationManager) && $oApiCollaborationManager->IsContactsGlobalSupported())
		{
			$this->apiGcontactsManager = $oApiCollaborationManager->GetGlobalContactsManager();
		}
		
		$this->apiUsersManager = CApi::Manager('users');
		$this->authPlugin = $authPlugin;
    }


	public function getAccount()
	{
		$oAccount = null;
		$sUser = $this->authPlugin->getCurrentUser();
		if (!empty($sUser))
		{
			$oAccount = $this->apiUsersManager->GetAccountOnLogin($sUser);
		}
		return $oAccount;
	}
	
	/**
     * @return string 
     */
    public function getName() 
	{
        return 'contacts';
    }

    /**
     * @return array 
     */
    public function getChildren() 
	{
		$oAccount = $this->getAccount();
        $aCards = array();
		if (isset($oAccount) && isset($this->apiGcontactsManager))
		{
			$aContacts = $this->apiGcontactsManager->GetContactItems($oAccount,
				EContactSortField::EMail, ESortOrder::ASC, 0, 999);
			
			foreach($aContacts as $oContact) 
			{
				$vCard = new Sabre_VObject_Component('VCARD');
				$vCard->VERSION = '3.0';
				$vCard->UID = $oContact->Id;
	            $vCard->{"item1.email"} = $oContact->Email;
		        $vCard->FN = $oContact->Name;
				$vCard->N = $oContact->Name . ';;;';
				
				$aCards[] = new afterlogic_DAV_CardDAV_GlobaAddressBooksCard(
					array(
						'uri' => $oContact->Email .'-'. $oContact->Id . '.vcf',
						'carddata' => $vCard->serialize(),
						'lastmodified' => strtotime('2000-01-01 11:11:11')
					)
				);
			}
		}
        return $aCards;
    }
}
