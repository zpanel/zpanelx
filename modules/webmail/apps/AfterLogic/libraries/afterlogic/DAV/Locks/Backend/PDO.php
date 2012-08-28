<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_Locks_Backend_PDO extends Sabre_DAV_Locks_Backend_PDO {

    /**
     * Constructor 
     * 
     * @param PDO $pdo
     * @param string $tableName 
     */
    public function __construct(PDO $pdo, $dBPrefix = '', 
			$tableName = afterlogic_DAV_Server::Tbl_Locks) {

        $this->pdo = $pdo;
        $this->tableName = $dBPrefix.$tableName;

    }
}
