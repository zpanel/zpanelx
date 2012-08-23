<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class CIcsReader
{
	/**
	 * @var array
	 */
    protected $aRows;

	/**
	 * @var int
	 */
    protected $iRowsCount;

	/**
	 * @var int
	 */
    protected $iPosition;

	public function __construct()
	{
		$this->iPosition = 0;
        $this->iRowsCount = 0;
        $this->aRows = array();
	}

	/**
	 * @param string $sFile
	 */
    public function Parse($sFile)
    {
        $this->aRows = array();

        $rHandle = fopen($sFile, 'r');
        $sNextLine = fgets($rHandle, 1024);
		
        while (!feof($rHandle))
        {
            $sLine = $sNextLine;
            $sNextLine = fgets($rHandle, 1024);
            $sNextLine = preg_replace("[\r\n]", '', $sNextLine);
			
            // handle continuation lines that start with either a space or a tab (MS Outlook)
            while (isset($sNextLine{0}) && (' ' === $sNextLine{0} || "\t" === $sNextLine{0}))
            {
                $sLine = $sLine.substr($sNextLine, 1);
                $sNextLine = fgets($rHandle, 1024);
                $sNextLine = preg_replace("[\r\n]", '', $sNextLine);
            }
			
            $sLine = trim($sLine);
            $this->aRows[] = explode(':', $sLine, 2);
        }

        fclose($rHandle);
        $this->Reset();
    }


	/**
	 * reset array pointer
	 */
    function Reset()
    {
        $this->iPosition = 0;
        $this->iRowsCount = count($this->aRows);
        reset($this->aRows);
    }

	/**
	 * get next string from array
	 *
	 * @return array
	 */
    function NextLine()
    {
        if ($this->iPosition == $this->iRowsCount)
        {
            $this->Reset();
            return false;
        }
		
        $this->iPosition += 1;
        return $this->aRows[$this->iPosition - 1];
    }
}