<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

abstract class CIcsParser
{
	const START_TOKEN = 'BEGIN';
	const STOP_TOKEN = 'END';

	/**
	 * @var string
	 */
	protected $sParserName;

	/**
	 * @var string
	 */
    protected $aParameters;

    protected $aTokens;

    protected $aInsideParser;

    protected $oDataSource;

    protected $sCurrentValue;

    protected $sCurrentToken;

    protected $sCurrentAdditionalParameter;

    protected $aMap;

    protected $aRow;

    public function __construct($sParserName)
    {
        $this->sParserName = $sParserName;

		$this->aParameters = array();
		$this->aTokens = array();
		$this->oDataSource = null;
		$this->aInsideParser = array();
    }

	public function ParseData(&$oDataSource)
    {
        $this->oDataSource =& $oDataSource;
        while (false != $this->nextLine())
        {
            $this->parseRow();
            if ($this->isParserEnd())
            {
                return $this->closeParser();
            }

			if ($this->isSpecialImportTokenRegister())
			{
				$this->processData(true);
			}
			else if ($this->isTokenRegister())
            {
                $this->processData();
            }
        }
    }

	function nextLine()
	{
		$this->aRow = $this->oDataSource->NextLine();
		return $this->aRow;
	}

	function isParserEnd()
	{
		$bResult = (self::STOP_TOKEN == $this->sCurrentToken && $this->sParserName === $this->sCurrentValue);
		return $bResult;
	}

	function closeParser()
	{
		return true;
	}

	abstract function createNewParser();

	function isTokenRegister()
	{
		$bResult = isset($this->aMap['tokens'])
			&& array_key_exists($this->sCurrentToken, $this->aMap['tokens'])
			|| self::START_TOKEN == $this->sCurrentToken;

		return $bResult;
	}

	function isSpecialImportTokenRegister()
	{
		$bResult = isset($this->aMap['tokensWithSpecialTreatmentImport'])
			&& array_key_exists($this->sCurrentToken, $this->aMap['tokensWithSpecialTreatmentImport'])
			|| self::START_TOKEN == $this->sCurrentToken;

		return $bResult;
	}

	function processData($bSpecial = false)
	{
		if (self::START_TOKEN == $this->sCurrentToken)
		{
			$this->createNewParser();
		}
		else
		{
			$sKey = '';
			if ($bSpecial)
			{
				$sFunctionName = $this->aMap['tokensWithSpecialTreatmentImport'][$this->sCurrentToken];
				$mReturn = call_user_func_array(array(&$this, $sFunctionName),
					array($this->sCurrentToken, $this->sCurrentValue, $this->sCurrentAdditionalParameter));
				
				if (is_array($mReturn))
				{
					foreach ($mReturn as $sKey => $mValue)
					{
						if (is_array($mValue))
						{
							foreach ($mValue as $sValuePart)
							{
								$this->aParameters[$sKey][] = $this->unescape($sValuePart);
							}
						}
						else
						{
							$this->aParameters[$sKey] = $this->unescape($mValue);
						}
					}
				}
			}
			else
			{
				$sKey = $this->aMap['tokens'][$this->sCurrentToken];
				$this->aParameters[$sKey] = $this->unescape($this->sCurrentValue);
			}
		}
	}

	function parseRow()
	{
		$sOperateStructure = '';
		list($sOperateStructure, $this->sCurrentValue) = $this->aRow;
		list($this->sCurrentToken, $this->sCurrentAdditionalParameter) = $this->parseOperateStructure($sOperateStructure);
	}

	abstract function parseOperateStructure($sOperateStructure);

	function InitParameters($aMap)
	{
		$this->aMap = $aMap;
	}

	function ClearDataSource()
	{
		$this->oDataSource = null;
	}

	/**
	 * @param string $sName
	 * @return array
	 */
	function GetParameter($sName)
	{
		return isset($this->aParameters[$sName]) ? $this->aParameters[$sName] : array();
	}

	/**
	 * @return array
	 */
	function GetParametersList()
	{
		return $this->aParameters;
	}

	/**
	 * @param string $sValue
	 * @return string
	 */
	protected function unescape($sValue)
	{
		$sText = str_replace('\\\\', '\\', $sValue);
		$sText = str_replace('\\,', ',', $sText);
		$sText = str_replace('\\;', ';', $sText);
		$sText = str_replace(array('\r', '\n'), array("\r", "\n"), $sText);
		return $sText;
	}
}
