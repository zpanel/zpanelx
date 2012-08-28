<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

	defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	require_once WM_ROOTPATH.'common/class_dbstorage.php';
	require_once WM_ROOTPATH.'common/class_tempfiles.php';
	
	define('ACTION_Remove', 0);
	define('ACTION_Set', 1);
	
	define('FOLDER_LIST_INDEX_DELIMITR', '#$%$#');
	
	/**
	 * @abstract
	 */
	class MailStorage
	{
		/**
		 * @var MailProcessor
		 */		
		var $mailproc = null; 
		
		/**
		 * @var CAccount
		 */
		var $Account;
	
		/**
		 * @access protected
		 * @var api_Settings
		 */
		var $_settings;
		
		/**
		 * @access protected
		 * @var resource
		 */
		var $_connectionHandle = null;
		
		/**
		 * @var string
		 */
		var $DownloadedMessagesHandler = null;
		
		/**
		 * @var string
		 */
		var $ShowDeletingMessageNumber = null;

		/**
		 * @var string
		 */
		var $UpdateFolderHandler = null;
		
		/**
		 * @param CAccount $account
		 * @param api_Settings $settings = null
		 * @return MailStorage
		 */
		function MailStorage(&$account, $settings = null)
		{
			if (null === $settings)
			{
				$this->_settings =& CApi::GetSettings();
			}
			else
			{
				$this->_settings =& $settings;
			}
			
			$this->Account =& $account;
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param DbStorage $dbStorage
		 * @param Folder $folder
		 * @return bool
		 */	
		function ApplyFilters(&$message, &$dbStorage, &$folder, &$filters)
		{
			$result = true;
			$needToSave = true;
			
			if ($folder->Type == FOLDERTYPE_Inbox && $result && isset($GLOBALS['useFilters']))
			{
				$mailProcessor = null;
				if (null == $this->mailproc)
				{
					$this->mailproc = new MailProcessor($this->Account);
				}

				$mailProcessor =& $this->mailproc;
				
				$messageIdUidSet = array($message->IdMsg => $message->Uid);
				
				$filtersKeys = array_keys($filters->Instance());
				foreach ($filtersKeys as $key)
				{
					$filter =& $filters->Get($key);
					$action = $filter->GetActionToApply($message);
	
					switch ($action)
					{
						case FILTERACTION_DeleteFromServerImmediately:
							$result &= $mailProcessor->DeleteFromServerImmediately($messageIdUidSet, $folder);
							$needToSave = false;
							break 2;

						case FILTERACTION_MoveToSpamFolder:
							$folders =& $mailProcessor->GetFolders();
							$spamFolder = $folders->GetFolderByType(FOLDERTYPE_Spam);
							if ($spamFolder && $spamFolder->IdDb)
							{
								$filter->IdAcct = $this->Account->IdAccount;
								$filter->IdFolder = $spamFolder->IdDb;
							}
							else
							{
								break;
							}
							
						case FILTERACTION_MoveToFolder:
							if ($filter->IdFolder != $folder->IdDb)
							{
								if ($folder->SyncType == FOLDERSYNC_NewEntireMessages || $folder->SyncType == FOLDERSYNC_AllEntireMessages)
								{
									$result &= $dbStorage->SaveMessage($message, $folder);
								}
								else if ($folder->SyncType == FOLDERSYNC_NewHeadersOnly || $folder->SyncType == FOLDERSYNC_AllHeadersOnly)
								{
									$result &= $dbStorage->SaveMessageHeader($message, $folder, false);
								}
								
								$messageIdUidSet = array($message->IdMsg => $message->Uid);
							
								if ($result)
								{
									$needToSave = false;
									$toFolder = new Folder($filter->IdAcct, $filter->IdFolder, '');
									$dbStorage->GetFolderInfo($toFolder);

									$_tDowmloaded = $this->DownloadedMessagesHandler;
									$_tDeleting = $this->ShowDeletingMessageNumber;

									$this->DownloadedMessagesHandler = $this->ShowDeletingMessageNumber = null;

									$result &= $mailProcessor->MoveMessages($messageIdUidSet, $folder, $toFolder);

									$this->DownloadedMessagesHandler = $_tDowmloaded;
									$this->ShowDeletingMessageNumber = $_tDeleting;
									
									if ($this->UpdateFolderHandler != null)
									{
										call_user_func_array($this->UpdateFolderHandler, array($toFolder->IdDb, $toFolder->FullName));
									}
								}
								else 
								{
									if ($this->UpdateFolderHandler != null)
									{
										call_user_func_array($this->UpdateFolderHandler, array($folder->IdDb, $folder->FullName));
									}
								}
							}
							break 2;
							
						case FILTERACTION_MarkGrey:
							$result &= $mailProcessor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Grayed, ACTION_Set, false);
							$message->Flags |= MESSAGEFLAGS_Grayed;
							break;
					}
					
					unset($filter);
				}
			}
			
			if ($needToSave)
			{
				if ($folder->SyncType == FOLDERSYNC_NewEntireMessages || $folder->SyncType == FOLDERSYNC_AllEntireMessages)
				{
					$result &= $dbStorage->SaveMessage($message, $folder);
					if ($this->UpdateFolderHandler != null)
					{
						call_user_func_array($this->UpdateFolderHandler, array($folder->IdDb, $folder->FullName));
					}
				}
				else if ($folder->SyncType == FOLDERSYNC_NewHeadersOnly || $folder->SyncType == FOLDERSYNC_AllHeadersOnly)
				{
					$result &= $dbStorage->SaveMessageHeader($message, $folder, false);
					if ($this->UpdateFolderHandler != null)
					{
						call_user_func_array($this->UpdateFolderHandler, array($folder->IdDb, $folder->FullName));
					}
				}
			}
			
			return $result;
		}
	
		function GetFolderCollectionFromArrays($folders, $subScrFolders, $seporator, $existsIndex, $flags)
		{
			$newFolderArray = array();
			if (is_array($folders))
			{
				foreach ($folders as $folder)
				{
					$p = null;
					$fullName = array();
					$temp = array();
					$p =& $temp;
					$seporatedNames = explode($seporator, $folder);
					foreach ($seporatedNames as $name)
					{
						$fullName[] = $name;
						$name .= FOLDER_LIST_INDEX_DELIMITR.implode($seporator, $fullName);
						$temp[$name] = null;
						$temp =& $temp[$name];
					}

					$newFolderArray = array_merge_recursive($newFolderArray, $p);
					unset($p, $temp, $fullName);
				}
			}

			$folderCollection = new FolderCollection();
			if (0 < count($newFolderArray))
			{
				$this->_recFillFolderCollection($folderCollection, $newFolderArray, $subScrFolders, $existsIndex, $flags);
			}

			
			return $folderCollection;
		}

		/**
		 * @param Folder $folderObj
		 */
		function _xlistFolderInit(&$folderObj)
		{
			if (false !== strpos($folderObj->Flags, '\inbox'))
			{
				$folderObj->Type = FOLDERTYPE_Inbox;
				$folderObj->XListType = true;
			}
			else if (false !== strpos($folderObj->Flags, '\sent'))
			{
				$folderObj->Type = FOLDERTYPE_SentItems;
				$folderObj->XListType = true;
			}
			else if (false !== strpos($folderObj->Flags, '\drafts'))
			{
				$folderObj->Type = FOLDERTYPE_Drafts;
				$folderObj->XListType = true;
			}
			else if (false !== strpos($folderObj->Flags, '\trash'))
			{
				$folderObj->Type = FOLDERTYPE_Trash;
				$folderObj->XListType = true;
			}
			else if (false !== strpos($folderObj->Flags, '\spam'))
			{
				$folderObj->Type = FOLDERTYPE_Spam;
				$folderObj->XListType = true;
			}
		}
	
		function _recFillFolderCollection(&$folderCollection, $folders, $subScrFolders, &$existsIndex, $flags, $isInbox = false)
		{
			$aFolderMap =& $this->Account->Domain->GetFoldersMap();
			foreach ($folders as $folder => $subFolders)
			{
				$folderName = $folderFullName = null;
				$tArray = explode(FOLDER_LIST_INDEX_DELIMITR, $folder);
				if (count($tArray) != 2)
				{
					continue;
				}

				$folderName = $tArray[0];
				$folderFullName = $tArray[1];
				
				$folderObj = new Folder($this->Account->IdAccount, -1, $folderFullName, $folderName);
				if (isset($flags[$folderObj->FullName]))
				{
					$folderObj->Flags = $flags[$folderObj->FullName];
				}
				
				$this->_xlistFolderInit($folderObj);
				$this->SetFolderType($folderObj, $existsIndex, $aFolderMap);
				
				$folderObj->Hide = (defined('USE_LSUB') && USE_LSUB) ? false : !in_array($folderObj->FullName, $subScrFolders);
				if ($folderObj->Type != FOLDERTYPE_Custom)
				{
					$folderObj->Hide = false;
				}
				
				if (null !== $subFolders && is_array($subFolders))
				{
					$newCollection = new FolderCollection();
					
					$this->_recFillFolderCollection($newCollection, $subFolders, $subScrFolders, $existsIndex, $flags, $folderObj->Type == FOLDERTYPE_Inbox);
					
					if ($newCollection->Count() > 0)
					{
						$folderObj->SubFolders = $newCollection;
					}
					
					unset($newCollection);
				}

				$folderCollection->Add($folderObj);
				unset($folderObj);
			}
		}

		/**
		 * @param Folder $folderObj
		 * @param array $existsIndex
		 * @param array $aFolderMap
		 */
		function SetFolderType(&$folderObj, &$existsIndex, $aFolderMap)
		{
			if (EFolderType::System !== $folderObj->Type)
			{
				if (EFolderType::Custom !== $folderObj->Type)
				{
					// Gmail xlist bug
					if ('INBOX' === strtoupper($folderObj->FullName) && 'INBOX' !== $folderObj->FullName
						&& EFolderType::Inbox === $folderObj->Type && false !== strpos(strtolower($folderObj->Flags), '\inbox'))
					{
						$folderObj->Type = EFolderType::Custom;
						if (false === strpos(strtolower($folderObj->Flags), '\noselect'))
						{
							$folderObj->Flags = $folderObj->Flags.' \noselect';
						}
					}
					
					if (isset($aFolderMap[$folderObj->Type]))
					{
						if (isset($existsIndex[$folderObj->Type]))
						{
							if ($folderObj->XListType)
							{
								unset($existsIndex[$folderObj->Type]);
								$existsIndex[$folderObj->Type] =& $folderObj;
							}
							else
							{
								$folderObj->Type = EFolderType::Custom;
							}
						}
						else
						{
							$existsIndex = is_array($existsIndex) ? $existsIndex : array();
							$existsIndex[$folderObj->Type] =& $folderObj;
						}
					}
					else
					{
						$folderObj->Type = EFolderType::Custom;
					}
				}
				else
				{
					$sKey = false;
					foreach ($aFolderMap as $iFolderType => $mFolderName)
					{
						if (is_string($mFolderName) && $mFolderName === $folderObj->Name)
						{
							$sKey = $iFolderType;
							break;
						}
						else if (is_array($mFolderName) && in_array($folderObj->Name, $mFolderName))
						{
							$sKey = $iFolderType;
							break;
						}
					}
					
					if (false !== $sKey)
					{
						$folderObj->Type = $sKey;
						$existsIndex = is_array($existsIndex) ? $existsIndex : array();
						$existsIndex[$folderObj->Type] =& $folderObj;
					}
				}
			}
		}
	}
	
	/**
	 * @abstract
	 */
	class MailServerStorage extends MailStorage
	{
		/**
		 * @param CAccount $account
		 * @return MailServerStorage
		 */
		function MailServerStorage(&$account, $settings = null)
		{
			MailStorage::MailStorage($account, $settings);
		}
	}
	
	/**
	 * @static
	 */
	class DbStorageCreator
	{
		/**
		 * @param Account $account
		 * @return MySqlStorage
		 */
		public static function &CreateDatabaseStorage(&$account, $settings = null)
		{
			/**
			 * @var DbStorage
			 */
			static $instance = null;
			
    		if (is_object($instance))
    		{
    			if ($account)
    			{
    				$instance->Account = $account;	
    			}
    			return $instance;
    		}
			
			require_once(WM_ROOTPATH.'common/class_dbstorage.php');

			if (null === $settings)
			{
				$settings =& CApi::GetSettings();
			}
			
			switch ($settings->GetConf('Common/DBType'))
			{
				default:
				case EDbType::MySQL:
					$instance = new MySqlStorage($account, $settings);
					break;
			}
    		
			if ($account)
    		{
    			$instance->Account = $account;	
    		}
			
			return $instance;
		}
	}
