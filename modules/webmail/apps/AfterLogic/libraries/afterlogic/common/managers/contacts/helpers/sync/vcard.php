<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class CApiContactsSyncVcard
{
	/**
	 * @var CApiContactsManager
	 */
	protected $oApiContactsManager;
	
	/**
	 * @var CApiContactsVcardFormatter
	 */
	protected $oFormatter;

	/**
	 * @var CApiContactsVcardParser
	 */
	protected $oParser;
	
	/**
	 * @var CIcsReader
	 */
	protected $oReader;

	public function __construct($oApiContactsManager)
	{
		$this->oApiContactsManager = $oApiContactsManager;
		$this->oFormatter = new CApiContactsVcardFormatter();
		$this->oParser = new CApiContactsVcardParser();
		$this->oReader = new CIcsReader();
		$this->oGroup = null;
	}

	/**
	 * @param int $iUserId
	 * @return ''
	 */
	public function Export($iUserId)
	{
		$iOffset = 0;
		$iRequestValue = 50;

		$sResult = '';
		$aGroupsCache = array();

		$iCount = $this->oApiContactsManager->GetContactItemsCount($iUserId);
		if (0 < $iCount)
		{
			while ($iOffset < $iCount)
			{
				$aList = $this->oApiContactsManager->GetContactItemsWithoutOrder($iUserId, $iOffset, $iRequestValue);
				
				if (is_array($aList))
				{
					$oContactListItem = null;
					foreach ($aList as $oContactListItem)
					{
						$oContact = $this->oApiContactsManager->GetContactById($iUserId, $oContactListItem->Id);
						if ($oContact)
						{
							$aGroupIds = $oContact->GroupsIds;
							$this->oFormatter->SetContainer($oContact);
							if (0 < count($aGroupIds))
							{
								$oGroup = null;
								$sGroupId = (string) $aGroupIds[0];
								
								if (!isset($aGroupsCache[$sGroupId]))
								{
									$oGroup = $this->oApiContactsManager->GetGroupById($iUserId, $sGroupId);
									if ($oGroup)
									{
										$aGroupsCache[$sGroupId] = $oGroup;
									}
								}

								if (isset($aGroupsCache[$sGroupId]))
								{
									$this->oFormatter->SetGroup($aGroupsCache[$sGroupId]);
								}
							}
							
							$this->oFormatter->Form();
							$sResult .= $this->oFormatter->GetValue();
						}
					}

					$iOffset += $iRequestValue;
				}
				else
				{
					break;
				}
			}
		}
		
		return $sResult;
	}

	/**
	 * @param int $iUserId
	 * @param string $sTempFileName
	 * @param int $iParsedCount
	 * @return int
	 */
	public function Import($iUserId, $sTempFileName, &$iParsedCount)
	{
		$iCount = -1;
		$iParsedCount = 0;
		if (file_exists($sTempFileName))
		{
			$this->oReader->Reset();
			$this->oReader->Parse($sTempFileName);
			$this->oParser->ParseData($this->oReader);

			$aGroupCache = array();

			$aVCards = $this->oParser->GetParameter('VCARD');
			if (is_array($aVCards))
			{
				$iCount = 0;

				$aUpdatedContactIds = array();
				$aUpdatedGroupsIds = array();

				foreach ($aVCards as $oApiContactsVcardParser)
				{
					if ($oApiContactsVcardParser)
					{
						$aParameters = $oApiContactsVcardParser->GetParametersList();
						if (is_array($aParameters) && isset($aParameters['IdContactStr'], $aParameters['DateModified']))
						{
							$bCreate = false;
							$sIdContactStr = $aParameters['IdContactStr'];
							$iUpdateDateModified = $aParameters['DateModified'];

							$oContact = $this->oApiContactsManager->GetContactByStrId($iUserId, $sIdContactStr);
							if (null === $oContact)
							{
								$bCreate = true;
								$oContact = new CContact();
							}

							if ($oContact)
							{
								$iCount++;
								$iParsedCount++;

								$oContact->IdUser = $iUserId;
								
								if (isset($aParameters['GroupStrId'], $aParameters['GroupName'])
									&& 0 < strlen($aParameters['GroupStrId']) && 0 < strlen($aParameters['GroupName']))
								{
									$oGroup = null;

									$sGroupIdStr = $aParameters['GroupStrId'];
									$sGroupName = $aParameters['GroupName'];

									if (!isset($aGroupCache[$sGroupIdStr]))
									{
										$oGroup = $this->oApiContactsManager->GetGroupByStrId($iUserId, $aParameters['GroupStrId']);
										if ($oGroup)
										{
											if ($oGroup->Name !== $sGroupName)
											{
												$oGroup->Name = $sGroupName;
												$this->oApiContactsManager->UpdateGroup($oGroup);
											}

											$aGroupCache[$oGroup->IdGroupStr] = $oGroup;
										}
										else if (null === $oGroup)
										{
											$oGroup = new CGroup();
											$oGroup->IdGroupStr = $sGroupIdStr;
											$oGroup->IdUser = $iUserId;
											$oGroup->Name = $sGroupName;

											if ($this->oApiContactsManager->CreateGroup($oGroup))
											{
												$aGroupCache[$oGroup->IdGroupStr] = $oGroup;
											}
										}
									}
									else
									{
										$oGroup = $aGroupCache[$sGroupIdStr];
									}

									$iContactGroupId = 0;
									if ($oGroup)
									{
										$iContactGroupId = $oGroup->IdGroup;
									}

									$oContact->GroupsIds = (0 < $iContactGroupId) ? array($iContactGroupId) : array();
								}
								else
								{
									$oContact->GroupsIds = array();
								}

								if ($bCreate || $iUpdateDateModified > $oContact->DateModified)
								{
									foreach ($aParameters as $sPropertyName => $mValue)
									{
										if ($oContact->IsProperty($sPropertyName))
										{
											$oContact->{$sPropertyName} = $mValue;
										}
									}
									
									$oContact->InitDependentValues();

									if ($bCreate)
									{
										$this->oApiContactsManager->CreateContact($oContact);
									}
									else
									{
										$oContact->__LOCK_DATE_MODIFIED__ = true;
										
										$this->oApiContactsManager->UpdateContact($oContact);
									}
								}

								if (0 < $oContact->IdContact)
								{
									$aUpdatedContactIds[] = $oContact->IdContact;
								}
							}
						}
					}
				}

				$oCachedGroup = null;
				foreach ($aGroupCache as $oCachedGroup)
				{
					$aUpdatedGroupsIds[] = $oCachedGroup->IdGroup;
				}

				$this->oApiContactsManager->DeleteContactsExceptIds($iUserId, $aUpdatedContactIds);
				$this->oApiContactsManager->DeleteGroupsExceptIds($iUserId, $aUpdatedGroupsIds);
			}
		}

		return $iCount;
	}
}
