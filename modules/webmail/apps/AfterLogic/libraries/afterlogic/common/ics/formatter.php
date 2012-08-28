<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class CIcsFormatter
{
	/**
	 * rfc2445
	 * Lines of text SHOULD NOT be longer than 75 octets, excluding the line break.
	 */
	const SPLIT_LINE_LIMIT = 75;

	/**
	 * @var mixed
	 */
	protected $oContainer;

	/**
	 * @var array
	 */
	protected $aMap;

	/**
	 * @var string
	 */
	protected $sName;
	
	/**
	 * @var string
	 */
	protected $sValue;

	/**
	 * @var bool
	 */
	protected $bHasEnclosed;

	/**
	 * @param string $sName
	 */
	public function __construct($sName)
	{
		$this->oContainer = null;
		$this->aMap = array();
		
		$this->sName = $sName;
		$this->sValue = '';
		$this->bHasEnclosed = true;
	}

	/**
	 * @param mixed $oContainer
	 */
	public function SetContainer($oContainer)
	{
		$this->sValue = '';
		$this->oContainer = $oContainer;
	}

	/**
	 * @param array $aMap
	 */
	public function InitParameters($aMap)
    {
        $this->aMap = $aMap;
    }

	/**
	 * @return string
	 */
	public function GetValue()
	{
		return $this->sValue;
	}

	/**
	 * @return bool
	 */
	public function Form()
	{
		$this->sValue .= $this->writeToken('BEGIN', $this->sName);
		$this->formStatic();
		$this->formTagsFromContainer();
		$this->formSpecialTreatments(true);
		if ($this->bHasEnclosed)
		{
			$this->formSpecialTreatments();
			$this->writeToken('END', $this->sName);
		}
		else
		{
			$this->writeToken('END', $this->sName);
			$this->formSpecialTreatments();
		}
		
		return true;
	}

	protected function formStatic()
	{
		if (isset($this->aMap['static']) && is_array($this->aMap['static']))
		{
			foreach ($this->aMap['static'] as $sToken => $sValue)
			{
				$this->writeToken($sToken, $sValue);
			}
		}
	}

	protected function formTagsFromContainer()
	{
		if (isset($this->aMap['tokens']) && is_array($this->aMap['tokens']))
		{
			foreach ($this->aMap['tokens'] as $sToken => $sPropertyName)
			{
				if ($this->oContainer->IsProperty($sPropertyName))
				{
					$mValue = (string) $this->oContainer->{$sPropertyName};
					if (!empty($mValue))
					{
						$this->writeToken($sToken, $mValue);
					}
				}
			}
		}
		
		if (isset($this->aMap['tokensWithSpecialTreatment']) && is_array($this->aMap['tokensWithSpecialTreatment']))
		{
			foreach ($this->aMap['tokensWithSpecialTreatment'] as $sToken => $aParams)
			{
				$sFunctionName = $this->aMap['tokensWithSpecialTreatment'][$sToken][0];
				$aParams = $this->aMap['tokensWithSpecialTreatment'][$sToken];
				$aParams[0] = $sToken;
				$mValue = (string) @call_user_func_array(array(&$this, $sFunctionName), $aParams);
				if (!empty($mValue))
				{
					$this->writeLine($mValue);
				}
			}
		}
	}

	/**
	 * @param bool $bIsInside
	 */
	protected function formSpecialTreatments($bIsInside = false)
	{
		$aSpecialTreatments = array();
		if ($bIsInside && isset($this->aMap['specialInsideTreatments']) && is_array($this->aMap['specialInsideTreatments']))
		{
			$aSpecialTreatments = $this->aMap['specialInsideTreatments'];
		}
		else if (!$bIsInside && isset($this->aMap['specialTreatments']) && is_array($this->aMap['specialTreatments']))
		{
			$aSpecialTreatments = $this->aMap['specialTreatments'];
		}

		foreach ($aSpecialTreatments as $sPropertyName => $sTreatmentClassName)
		{
			if ($this->oContainer->IsProperty($sPropertyName) && class_exists($sTreatmentClassName) &&
				is_array($this->oContainer->{$sPropertyName}))
			{
				$aElements = $this->oContainer->{$sPropertyName};
				
				$oTreatment = new $sTreatmentClassName;
				foreach ($aElements as $oContainer)
				{
					$oTreatment->SetContainer($oContainer);
					if ($oTreatment->Form())
					{
						$this->sValue .= $oTreatment->GetValue();
					}
				}
			}
		}
	}

	/**
	 * @param string $sToken
	 * @param string $sValue
	 * @param bool $bSameLine = false
	 */
	protected function writeToken($sToken, $sValue, $bSameLine = false)
	{
		$this->sValue .= $this->writeLine($sToken.':'.$this->escapeValue($sValue), $bSameLine);
	}

	/**
	 * @param string $sLine
	 * @param bool $bSameLine = false
	 */
	protected function writeLine($sLine, $bSameLine = false)
	{
		if (!empty($sLine))
		{
			$sText = $sLine;
			if (strlen($sText) > self::SPLIT_LINE_LIMIT)
			{
				$sText = $this->utfArrayTostringWithLimitLine(
					$this->smartUtfStrInArray($sText), self::SPLIT_LINE_LIMIT);
			}
			$this->sValue .= $sText."\r\n";
		}
	}

	/**
	 * @param array $aStrArray
	 * @param int $iLen
	 * @return string
	 */
	private function utfArrayTostringWithLimitLine($aStrArray, $iLen = 75)
	{
		$iIndex = 0;
		$sResult = '';
		foreach ($aStrArray as $sValue)
		{
			if ($iIndex > $iLen)
			{
				$sResult .= "\r\n\t".$sValue;
				$iIndex = 0;
			}
			else
			{
				$sResult .= $sValue;
			}
			
			$iIndex++;
		}
		return $sResult;
	}

	/**
	 *
	 * @param string $sString
	 * @return array
	 */
	private function smartUtfStrInArray($sString)
	{
		$iSplit = 1;
		$aResult = array();
		for ($iIndex = 0; $iIndex < strlen($sString);)
		{
			$iOrdValue = ord($sString[$iIndex]);
			if ($iOrdValue > 127)
			{
				if ($iOrdValue >= 192 && $iOrdValue <= 223)
				{
					$iSplit = 2;
				}
				else if ($iOrdValue >= 224 && $iOrdValue <= 239)
				{
					$iSplit = 3;
				}
				else if ($iOrdValue >= 240 && $iOrdValue <= 247)
				{
					$iSplit = 4;
				}
			}
			else
			{
				$iSplit = 1;
			}

			$sKey = '';
			for ($iSecIndex = 0; $iSecIndex < $iSplit; $iSecIndex++, $iIndex++)
			{
				$sKey .= $sString[$iIndex];
			}

			array_push($aResult, $sKey);
		}

		return $aResult;
	}

	/**
	 * @param string $sValue
	 * @return string
	 */
	protected function escapeValue($sValue)
	{
		$sText = str_replace('\\', '\\\\', $sValue);
		$sText = str_replace(',', '\\,', $sText);
		$sText = str_replace(';', '\\;', $sText);
		$sText = str_replace(array("\r", "\n"), array('\r', '\n'), $sText);
		return $sText;
	}
}
