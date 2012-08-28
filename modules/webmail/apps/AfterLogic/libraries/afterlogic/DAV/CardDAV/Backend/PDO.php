<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class Afterlogic_Dav_CardDAV_Backend_PDO extends Sabre_CardDAV_Backend_PDO {

    /**
     * PDO connection 
     * 
     * @var PDO 
     */
    protected $pdo;

    /**
     * The PDO table name used to store addressbooks
     */
    protected $addressBooksTableName;

    /**
     * The PDO table name used to store cards
     */
    protected $cardsTableName;

    /**
     * Sets up the object 
     * 
     * @param PDO $pdo 
     */
    public function __construct(PDO $pdo, $dBPrefix = '', 
			$addressBooksTableName = afterlogic_DAV_Server::Tbl_Addressbooks, 
			$cardsTableName = afterlogic_DAV_Server::Tbl_Cards) {

        $this->pdo = $pdo;
        $this->addressBooksTableName = $dBPrefix.$addressBooksTableName;
        $this->cardsTableName = $dBPrefix.$cardsTableName; 

    }
}

