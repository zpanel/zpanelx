<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

	defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	include_once WM_ROOTPATH.'application/include.php';
	require_once WM_ROOTPATH.'common/inc_constants.php';
	require_once WM_ROOTPATH.'common/class_convertutils.php';
	
	$oAccount = /* @var $oAccount CAccount */ AppGetAccount(CSession::Get(APP_SESSION_ACCOUNT_ID, false));
	if ($oAccount)
	{
		ConvertUtils::SetLimits();
		
		AppIncludeLanguage($oAccount->User->DefaultLanguage);
		
		$oApiContactsManager = /* @var $oApiContactsManager CApiContactsManager */ CApi::Manager('contacts');

		$sOutput = $oApiContactsManager->Export($oAccount->IdUser, 'csv');
	}
		
	if (false !== $sOutput)
	{
		header('Pragma: public');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="export.csv";');
		header('Content-Transfer-Encoding: binary');
		
		echo $sOutput;
	}
