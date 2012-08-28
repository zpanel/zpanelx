<?php

//$bDisabled = true;
$iSortIndex = 10;
$sCurrentModule = 'CCommonModule';
class CCommonModule extends ap_Module
{
	/**
	* @var CApiWebmailManager
	*/
	protected $oWebmailApi;
	
	/**
	 * @var CApiDomainsManager
	 */
	protected $oDomainsApi;
	
	/**
	 * @var CApiUsersManager
	 */
	protected $oUsersApi;
	
	/**
	 * @var CApiCapabilityManager
	 */
	protected $oCapabilityApi;
	
	/**
	 * @param CAdminPanel $oAdminPanel
	 * @param string $sPath
	 * @return CCommonModule
	 */
	public function __construct(CAdminPanel &$oAdminPanel, $sPath)
	{
		parent::__construct($oAdminPanel, $sPath);

		$this->oDomainsApi = CApi::Manager('domains');
		$this->oUsersApi = CApi::Manager('users');
		$this->oCapabilityApi = CApi::Manager('capability');
		$this->oWebmailApi = CApi::Manager('webmail');
		
		$this->aTabs[] = AP_TAB_DOMAINS;
		$this->aTabs[] = AP_TAB_SYSTEM;

		//$this->aQueryActions[] = 'new';
		$this->aQueryActions[] = 'edit';
		$this->aQueryActions[] = 'list';

		$this->oPopulateData = new CCommonPopulateData($this);
		$this->oStandardPostAction = new CCommonPostAction($this);
		$this->oStandardPopAction = new CCommonPopAction($this);
		$this->oTableAjaxAction = new CCommonAjaxAction($this);

		$aTabs =& $oAdminPanel->GetTabs();
		array_push($aTabs,
			array('Services', AP_TAB_SERVICES),
			array('Domains', AP_TAB_DOMAINS)
		);
	}
	
	/**
	 * @param int $iDomainId
	 * @return CDomain
	 */
	public function GetDomain($iDomainId)
	{
		if (0 === $iDomainId)
		{
			return $this->oDomainsApi->GetDefaultDomain();
		}
		return $this->oDomainsApi->GetDomainById($iDomainId);
	}
	
	/**
	 * @param CDomain $oDomain
	 * @return bool
	 */
	public function UpdateDomain(CDomain $oDomain)
	{
		if (!$this->oDomainsApi->UpdateDomain($oDomain))
		{
			$this->lastErrorCode = $this->oDomainsApi->GetLastErrorCode();
			$this->lastErrorMessage = $this->oDomainsApi->GetLastErrorMessage();
			return false;
		}
		
		if (CSession::Has(AP_SESS_DOMAIN_NEXT_EDIT_ID) &&
			$oDomain->IdDomain === CSession::Get(AP_SESS_DOMAIN_NEXT_EDIT_ID, null))
		{
			CSession::Clear(AP_SESS_DOMAIN_NEXT_EDIT_ID);
		}
		
		return true;
	}

	/**
	 * @param string $sDomainName
	 * @return bool
	 */
	public function DomainExists($sDomainName)
	{
		return $this->oDomainsApi->DomainExists($sDomainName);
	}
	
	/**
	 * @return bool
	 */
	public function HasSslSupport()
	{
		return $this->oCapabilityApi->HasSslSupport();
	}
	
	/**
	 * @param string $sTab
	 * @param ap_Screen $oScreen
	 */
	protected function initStandardMenuByTab($sTab, ap_Screen &$oScreen)
	{
		switch ($sTab)
		{
			case AP_TAB_SYSTEM:
				if ($this->oAdminPanel->PType && $this->oAdminPanel->LType || !$this->oAdminPanel->PType)
				{
					$oScreen->AddMenuItem(CM_MODE_DB, CM_MODE_DB_NAME, $this->sPath.'/templates/db.php');
					$oScreen->AddMenuItem(CM_MODE_SECURITY, CM_MODE_SECURITY_NAME, $this->sPath.'/templates/security.php');
					$oScreen->SetDefaultMode(CM_MODE_DB);
				}
				break;
		}
	}

	/**
	 * @param string $sTab
	 * @param ap_Screen $oScreen
	 */
	protected function initTableTopMenu($sTab, ap_Screen &$oScreen)
	{

	}

	/**
	 * @param string $sTab
	 * @param ap_Screen $oScreen
	 */
	protected function initTableListHeaders($sTab, ap_Screen &$oScreen)
	{
		$oScreen->SetEmptySearch(AP_LANG_RESULTEMPTY);
		switch ($sTab)
		{
			case AP_TAB_DOMAINS:
				$oScreen->ClearHeaders();
				$oScreen->AddHeader('Name', 138, true);
				$oScreen->SetEmptyList(CM_LANG_NODOMAINS);
				$oScreen->SetEmptySearch(CM_LANG_NODOMAINS_RESULTEMPTY);
				break;
		}
	}

	/**
	 * @param string $sTab
	 * @param ap_Screen $oScreen
	 */
	protected function initTableList($sTab, ap_Screen &$oScreen)
	{
		if (AP_TAB_DOMAINS === $sTab)
		{
			$searchDesc = $oScreen->GetSearchDesc() ;
			$iAllCount = $this->oDomainsApi->GetDomainCount($searchDesc);
			$oScreen->EnableSearch( ($iAllCount > 1) || $searchDesc ) ;
			
			$bAddDefaultDomain = false;
			if ($this->oAdminPanel->HasAccessDomain(0))
			{
				$iAllCount++;
				$bAddDefaultDomain = true;
				$oScreen->AddListItem(0, array(
					'Name' => 'Default domain settings'
				), true);
			}
			
			$oScreen->SetAllListCount($iAllCount);
			
			$aDomainsList = $this->oDomainsApi->GetDomainsList($oScreen->GetPage(),
				$bAddDefaultDomain ? $oScreen->GetLinesPerPage() - 1 : $oScreen->GetLinesPerPage(),
				$oScreen->GetOrderBy(), $oScreen->GetOrderType(), $searchDesc );
				
			if (is_array($aDomainsList) && 0 < count($aDomainsList))
			{
				foreach ($aDomainsList as $iDomainId => $aDomainArray)
				{
					if ($this->oAdminPanel->HasAccessDomain($iDomainId))
					{
						$oScreen->AddListItem($iDomainId, array(
							'Type' => ($aDomainArray[0])
								? '<img src="static/images/mailsuite-domain-icon-big.png">'
								: '<img src="static/images/wm-domain-icon-big.png">',
							'Name' => $aDomainArray[1]
						));
					}
				}
			}
		}

	}

	/**
	 * @param string $sTab
	 * @param ap_Screen $oScreen
	 */
	protected function initTableMainSwitchers($sTab, ap_Screen &$oScreen)
	{
		$sMainAction = $this->getQueryAction();
		if (AP_TAB_DOMAINS === $sTab)
		{
			switch ($sMainAction)
			{
				case 'edit':
					$iDomainId = isset($_GET['uid']) ? (int) $_GET['uid'] : null;

					$oDomain = null;
					if ($this->oAdminPanel->HasAccessDomain($iDomainId))
					{
						$oDomain =& $this->oAdminPanel->GetMainObject('domain_edit');
						if (!$oDomain && null !== $iDomainId)
						{
							$oDomain = $this->GetDomain($iDomainId);
							if ($oDomain)
							{
								$this->oAdminPanel->SetMainObject('domain_edit', $oDomain);
							}
						}
					}

					if ($oDomain)
					{
						$oScreen->Data->SetValue('strDomainName', $oDomain->Name);
						if (0 === $oDomain->IdDomain)
						{
							$oScreen->Data->SetValue('strDomainName', 'Default domain settings');
						}
						
						$oScreen->Main->AddTopSwitcher($this->sPath.'/templates/main-top-edit-domain-name.php');
						$oScreen->Main->AddTopSwitcher($this->sPath.'/templates/main-top-edit-domain.php');
					}
					break;
			}
		}

	}

	/**
	* @param string $sTab
	* @param ap_Screen $oScreen
	*/
	protected function initTableMainSwitchersPost($sTab, ap_Screen &$oScreen)
	{
		$sMainAction = $this->getQueryAction();
		if (AP_TAB_DOMAINS === $sTab)
		{
			switch ($sMainAction)
			{
				case 'edit':
					$oDomain =& $this->oAdminPanel->GetMainObject('domain_edit');
					if ($oDomain)
					{
						$oScreen->Main->AddSwitcher(
						WM_SWITCHER_MODE_EDIT_DOMAIN_GENERAL, WM_SWITCHER_MODE_EDIT_DOMAIN_GENERAL_NAME,
						$this->sPath.'/templates/main-edit-domain-general-webmail.php');
						$oScreen->Main->AddSwitcher(
						WM_SWITCHER_MODE_EDIT_DOMAIN_GENERAL, WM_SWITCHER_MODE_EDIT_DOMAIN_GENERAL_NAME,
						$this->sPath.'/templates/main-edit-domain-general-regional.php');
					}
					break;
			}
		}
	}
	
	/**
	 * @return void
	 */
	protected function initInclude()
	{
		include $this->sPath.'/inc/constants.php';
		include $this->sPath.'/inc/populate.php';
		include $this->sPath.'/inc/post.php';
		include $this->sPath.'/inc/pop.php';
		include $this->sPath.'/inc/ajax.php';
	}

	/**
	 * @return void
	 */
	public function AuthCheckSet()
	{
		$mType = CSession::Get(AP_SESS_AUTH_TYPE, null);
		if (null !== $mType && md5(CSession::Id().AP_VERSION.__FILE__) === CSession::Get(AP_SESS_AUTH, null))
		{
			$this->setAdminAccessType((int) $mType);
			$this->setAdminAccessDomains(CSession::Get(AP_SESS_AUTH_DOMAINS, null));
		}
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return bool
	 */
	public function AuthLogin($sLogin, $sPassword)
	{
		$oSettings = null;
		$oSettings =& CApi::GetSettings();
		$sDemoLogin = CApi::GetConf('demo.adminpanel.login', '');

		if ($oSettings->GetConf('Common/AdminLogin') === $sLogin && 
				($sPassword === $oSettings->GetConf('Common/AdminPassword') ||
				md5($sPassword) === $oSettings->GetConf('Common/AdminPassword')))
		{
			$this->setAdminAccessType(AP_SESS_AUTH_TYPE_SUPER_ADMIN);
			return true;
		}
		else if (CApi::GetConf('demo.adminpanel.enable', false) &&
			0 < strlen($sDemoLogin) && $sDemoLogin === CPost::Get('AdmloginInput'))
		{
			$this->setAdminAccessType(AP_SESS_AUTH_TYPE_SUPER_ADMIN_ONLYREAD);
			return true;
		}
		else if ($this->oAdminPanel->PType())
		{
			$aDomainsIds = $this->oAdminPanel->CallModuleFunction('CProModule',
				'GetSubAdminDomainsIdsByLoginPassword', array($sLogin, md5($sPassword)));

			if (is_array($aDomainsIds) && 0 < count($aDomainsIds))
			{
				$this->setAdminAccessType(AP_SESS_AUTH_TYPE_SUBADMIN);
				$this->setAdminAccessDomains($aDomainsIds);
				return true;
			}
		}

		return false;
	}

	/**
	 * @param int $iAccessType
	 */
	protected function setAdminAccessType($iAccessType = AP_SESS_AUTH_TYPE_NONE)
	{
		$this->oAdminPanel->SetAuthType((int) $iAccessType);
		if (in_array((int) $iAccessType, array(AP_SESS_AUTH_TYPE_SUBADMIN,
			AP_SESS_AUTH_TYPE_SUPER_ADMIN, AP_SESS_AUTH_TYPE_SUPER_ADMIN_ONLYREAD)))
		{
			$this->oAdminPanel->SetIsAuth(true);
			CSession::Set(AP_SESS_AUTH, md5(CSession::Id().AP_VERSION.__FILE__));
			CSession::Set(AP_SESS_AUTH_TYPE, (int) $iAccessType);
		}
	}

	/**
	 * @param array $aDomainsIds
	 */
	protected function setAdminAccessDomains($aDomainsIds)
	{
		CSession::Set(AP_SESS_AUTH_DOMAINS, is_array($aDomainsIds) ? $aDomainsIds : null);
		$this->oAdminPanel->SetAuthDomains($aDomainsIds);
	}
	
	/**
	* @return array
	*/
	public function GetTimeZoneList()
	{
		return array(
			'Default', #0
			'(GMT -12:00) Eniwetok, Kwajalein, Dateline Time', #1
			'(GMT -11:00) Midway Island, Samoa', #2
			'(GMT -10:00) Hawaii', #3
			'(GMT -09:00) Alaska', #4
			'(GMT -08:00) Pacific Time (US & Canada); Tijuana', #5
			'(GMT -07:00) Arizona', #6
			'(GMT -07:00) Mountain Time (US & Canada)', #7
			'(GMT -06:00) Central America', #8
			'(GMT -06:00) Central Time (US & Canada)', #9
			'(GMT -06:00) Mexico City, Tegucigalpa', #10
			'(GMT -06:00) Saskatchewan', #11
			'(GMT -05:00) Indiana (East)', #12
			'(GMT -05:00) Eastern Time (US & Canada)', #13
			'(GMT -05:00) Bogota, Lima, Quito', #14
			'(GMT -04:00) Santiago', #15
			'(GMT -04:00) Caracas, La Paz', #16
			'(GMT -04:00) Atlantic Time (Canada)', #17
			'(GMT -03:30) Newfoundland', #18
			'(GMT -03:00) Greenland', #19
			'(GMT -03:00) Buenos Aires, Georgetown', #20
			'(GMT -03:00) Brasilia', #21
			'(GMT -02:00) Mid-Atlantic', #22
			'(GMT -01:00) Cape Verde Is.', #23
			'(GMT -01:00) Azores', #24
			'(GMT) Casablanca, Monrovia', #25
			'(GMT) Dublin, Edinburgh, Lisbon, London', #26
			'(GMT +01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', #27
			'(GMT +01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague', #28
			'(GMT +01:00) Brussels, Copenhagen, Madrid, Paris', #29
			'(GMT +01:00) Sarajevo, Skopje, Sofija, Warsaw, Zagreb', #30
			'(GMT +01:00) West Central Africa', #31
			'(GMT +02:00) Athens, Istanbul, Minsk', #32
			'(GMT +02:00) Bucharest', #33
			'(GMT +02:00) Cairo', #34
			'(GMT +02:00) Harare, Pretoria', #35
			'(GMT +02:00) Helsinki, Riga, Tallinn, Vilnius', #36
			'(GMT +02:00) Israel, Jerusalem Standard Time', #37
			'(GMT +03:00) Baghdad', #38
			'(GMT +03:00) Arab, Kuwait, Riyadh', #39
			'(GMT +03:00) East Africa, Nairobi', #40
			'(GMT +03:30) Tehran', #41
			'(GMT +04:00) Moscow, St. Petersburg, Volgograd', #42
			'(GMT +04:00) Abu Dhabi, Muscat', #43
			'(GMT +04:00) Baku, Tbilisi, Yerevan', #44
			'(GMT +04:30) Kabul', #45
			'(GMT +05:00) Islamabad, Karachi, Sverdlovsk, Tashkent', #46
			'(GMT +05:30) Calcutta, Chennai, Mumbai, New Delhi, India Standard Time', #47
			'(GMT +05:45) Kathmandu, Nepal', #48
			'(GMT +06:00) Ekaterinburg', #49
			'(GMT +06:00) Almaty, North Central Asia', #50
			'(GMT +06:00) Astana, Dhaka', #51
			'(GMT +06:00) Sri Jayawardenepura, Sri Lanka', #52
			'(GMT +06:30) Rangoon', #53
			'(GMT +07:00) Bangkok, Novosibirsk, Hanoi, Jakarta', #54
			'(GMT +08:00) Krasnoyarsk', #55
			'(GMT +08:00) Beijing, Chongqing, Hong Kong SAR, Urumqi', #56
			'(GMT +08:00) Ulaan Bataar', #57
			'(GMT +08:00) Kuala Lumpur, Singapore', #58
			'(GMT +08:00) Perth, Western Australia', #59
			'(GMT +08:00) Taipei', #60
			'(GMT +09:00) Osaka, Sapporo, Tokyo, Irkutsk', #61
			'(GMT +09:00) Seoul, Korea Standard time', #62
			'(GMT +09:30) Adelaide, Central Australia', #63
			'(GMT +09:30) Darwin', #64
			'(GMT +10:00) Yakutsk', #65
			'(GMT +10:00) Brisbane, East Australia', #66
			'(GMT +10:00) Canberra, Melbourne, Sydney, Hobart', #67
			'(GMT +10:00) Guam, Port Moresby', #68
			'(GMT +10:00) Hobart, Tasmania', #69
			'(GMT +11:00) Vladivostok', #70
			'(GMT +11:00) Solomon Is., New Caledonia', #71
			'(GMT +12:00) Auckland, Wellington, Magadan', #72
			'(GMT +12:00) Fiji Islands, Kamchatka, Marshall Is.', #73
			'(GMT +13:00) Nuku\'alofa, Tonga' #74
		);
	}
	
	/**
	 * @return array
	 */
	public function GetSkinList()
	{
		return $this->oWebmailApi->GetSkinList();
	}
	
	
	/**
	 * @return array
	 */
	public function GetLangsList()
	{
		return $this->oWebmailApi->GetLanguageList();
	}
	
}

