<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 *
 */

class afterlogic_DAV_Auth_Backend_PDO extends Sabre_DAV_Auth_Backend_AbstractBasic
{

    /**
     * Reference to PDO connection
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * PDO table name we'll be using
     *
     * @var string
     */
    protected $tableName;

    /**
     * PDO table name we'll be using
     *
     * @var string
     */
    protected $principalstableName;

    /**
     * PDO table name we'll be using
     *
     * @var string
     */
    protected $calendarstableName;

    /**
     * PDO table name we'll be using
     *
     * @var string
     */
    protected $addressbookstableName;

	/*
	 * @var bool
	 */
	protected $isPrincipalChecked;

    /**
     * Creates the backend object.
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     *
     * @param string $filename
     * @param string $tableName The PDO table name to use
     * @return void
     */
    public function __construct(PDO $pdo, $dBPrefix = '',
			$tableName = afterlogic_DAV_Server::Tbl_Accounts,
			$principalstableName = afterlogic_DAV_Server::Tbl_Principals,
			$calendarstableName = afterlogic_DAV_Server::Tbl_Calendars,
			$addressbookstableName = afterlogic_DAV_Server::Tbl_Addressbooks) {

        $this->pdo = $pdo;
        $this->tableName = $dBPrefix.$tableName;
        $this->principalstableName = $dBPrefix.$principalstableName;
        $this->calendarstableName = $dBPrefix.$calendarstableName;
        $this->addressbookstableName = $dBPrefix.$addressbookstableName;
    }
    /**
     * Validates a username and password
     *
     * This method should return true or false depending on if login
     * succeeded.
     *
     * @return bool
     */
    protected function validateUserPass($username, $password)
	{
		if (class_exists('CApi') && CApi::IsValid())
		{
			$oApiUsersManager = CApi::Manager('users');
			$oAccount = $oApiUsersManager->GetAccountOnLogin($username);

			/* @var $oApiCalendarManager CApiCalendarManager */
			$oApiCalendarManager = CApi::Manager('calendar');

			if ($username ===  $oApiCalendarManager->GetPublicUser() ||
				($oAccount && $oAccount->IncomingMailPassword === $password))
			{
				if (!$this->isPrincipalChecked)
				{
					$this->checkPrincipals($username);
				}
				return true;
			}
		}

		return false;
	}

	public function checkPrincipals($username)
	{
		$this->isPrincipalChecked = true;
		$principal = 'principals/' . $username;

		$stmt = $this->pdo->prepare('SELECT id FROM `'.$this->principalstableName.'` WHERE uri = ? LIMIT 1');
		$stmt->execute(array($principal));

		$result = $stmt->fetchAll();
		$hasPrinicpal = (count($result) != 0);

		$stmt = $this->pdo->prepare('SELECT principaluri FROM `'.$this->calendarstableName.'` WHERE principaluri = ? LIMIT 1');
		$stmt->execute(array($principal));

		$result = $stmt->fetchAll();
		$hasCalendars = (count($result) != 0);

		$stmt = $this->pdo->prepare('SELECT principaluri FROM `'.$this->addressbookstableName.'` WHERE principaluri = ? LIMIT 1');
		$stmt->execute(array($principal));

		$result = $stmt->fetchAll();
		$hasAddressbooks = (count($result) != 0);

		if(!$hasPrinicpal)
		{
			$stmt = $this->pdo->prepare('INSERT INTO `'.$this->principalstableName.'` (uri,email,displayname) VALUES (?, ?, ?)');
			$stmt->execute(array($principal, $username, ''));
		}

		if (!$hasCalendars)
		{
			$stmt = $this->pdo->prepare('INSERT INTO `'.$this->calendarstableName.'` (principaluri, displayname, uri, description, components, ctag, calendarcolor) VALUES (?, ?, ?, "", "VEVENT,VTODO", 1, "#EF9554")');
			$stmt->execute(array($principal, 'Default', afterlogic_DAV_Client::getUUID()));
		}
		
		if (!$hasAddressbooks)
		{
			$stmt = $this->pdo->prepare('INSERT INTO `'.$this->addressbookstableName.'` (principaluri, displayname, uri, description, ctag) VALUES (?, ?, ?, "", 1)');
			$stmt->execute(array($principal, 'Default', afterlogic_DAV_Client::getUUID()));
		}
	}
}
