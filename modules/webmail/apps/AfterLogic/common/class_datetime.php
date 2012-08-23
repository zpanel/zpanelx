<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

	defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	require_once(WM_ROOTPATH.'common/class_convertutils.php');
	
	define('DATEFORMAT_DEFAULT', 0);
	define('DATEFORMAT_DDMMYY', 1);
	define('DATEFORMAT_MMDDYY', 2);
	define('DATEFORMAT_DDMonth', 3);
	define('DATEFORMAT_Advanced', 4);
	
	define('DATEFORMAT_FLAG', '|#');
	
	define('DATEFORMAT_DEF0', 'mm/dd/yy');
	define('DATEFORMAT_DEF1', 'd/m/y');
	
	define('DATEFORMAT_TIME_24', 'H:i');
	define('DATEFORMAT_TIME_12', 'g:i A');

	class CDateTime
	{
		/**
		 * @var int
		 */
		var $TimeStamp;
		
		/**
		 * @var string
		 */
		var $FormatString = 'Default';
		
		/**
		 * @var int
		 */
		var $TimeFormat = 1; // 0/1 - 24/12
		
		/**
		 * @param int $timestamp optional
		 * @return CDateTime
		 */
		function CDateTime($timestamp = null)
		{
			if ($timestamp != null)
			{
				$this->TimeStamp = $timestamp;
			}
		}
		
		/**
		 * @param string $str
		 * @return CDateTime
		 */
		public static function CreateFromStr($str)
		{
			return new CDateTime(ConvertUtils::GetTimeFromString($str));
		}
		
		/**
		 * @return string
		 */
		function GetAsStr()
		{
			return date('D, j M Y H:i:s O (T)', $this->TimeStamp);
		}
		
		/**
		 * @return string
		 */
		function GetAsStrNew()
		{
			return date('D, j M Y H:i:s O', $this->TimeStamp);
		}
		
		/**
		 * $date should have YYYY-MM-DD HH:II:SS format 
		 * @param string $datetime
		 */
		function SetFromANSI($datetime)
		{
			$dt = explode(' ', $datetime);
			$date = explode('-', $dt[0]);
			$time = explode(':', $dt[1]);
			$this->TimeStamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
		}

		function SetTimeStampToUtc()
		{
			if ($this->TimeStamp)
			{
				$this->TimeStamp = $this->TimeStamp - CDateTime::GetServerTimeZoneOffset();
			}
		}
		
		/**
		 * return current timestamp in ANSI format
		 * @return string
		 */
		function ToANSI($newStamp = null)
		{
			if ($newStamp != null) return date('Y-m-d H:i:s', $newStamp);
			return date('Y-m-d H:i:s', $this->TimeStamp);
		}
		
		/**
		 * @return int
		 */
		public static function GetServerTimeZoneOffset() 
		{
		    return date('O') / 100 * 60 * 60; // Seconds from GMT
		}
		
		/**
		 * @param short $timeOffsetInMinutes
		 * @return string
		 */
		function GetFormattedDate($timeOffsetInMinutes)
		{
			$localTimeStamp = $this->TimeStamp + $timeOffsetInMinutes * 60;
			
			$timeTemp = ($this->TimeFormat === 1) ? DATEFORMAT_TIME_12 : DATEFORMAT_TIME_24;
			
			switch ($this->GetDateFormatTypeByString())
			{
				default:
				case DATEFORMAT_DEFAULT:
					return date(DATEFORMAT_DEF1.' '.$timeTemp, $localTimeStamp);
					
				case DATEFORMAT_DDMMYY:
					return date('d/m/y '.$timeTemp, $localTimeStamp);
					
				case DATEFORMAT_MMDDYY:
					return date('m/d/y '.$timeTemp, $localTimeStamp);
					
				case DATEFORMAT_DDMonth:
					return date('d M '.$timeTemp, $localTimeStamp);
					
				case DATEFORMAT_Advanced:
					$outStr = $this->FormatString;
					$outStr = preg_replace('/month/i', date('M', $localTimeStamp), $outStr);
					$outStr = preg_replace('/yyyy/i', date('Y', $localTimeStamp), $outStr);
					$outStr = preg_replace('/yy/i', date('y', $localTimeStamp), $outStr);
					$outStr = str_replace('y', date('z', $localTimeStamp)+1, $outStr);
					$outStr = preg_replace('/dd/i', date('d', $localTimeStamp), $outStr);
					$outStr = preg_replace('/mm/i', date('m', $localTimeStamp), $outStr);
					$outStr = str_replace('q', floor((date('n', $localTimeStamp)-1)/4)+1, $outStr);
					$outStr = str_replace('ww', date('W', $localTimeStamp), $outStr);
					$outStr = str_replace('w', date('w', $localTimeStamp)+1, $outStr);
					$outStr .= date(' '.$timeTemp, $localTimeStamp);

					return $outStr;
			}
		}
		
		function GetFormattedFullDate($timeOffsetInMinutes)
		{
			$localTimeStamp = $this->TimeStamp + $timeOffsetInMinutes * 60;
			return CDateTime::GetShortDay(date('w', $localTimeStamp)).', '.
				CDateTime::GetShortMonthJanuary($localTimeStamp).', '.date('Y, '.
				(($this->TimeFormat === 1) ? DATEFORMAT_TIME_12 : DATEFORMAT_TIME_24), $localTimeStamp);
		}
		
		function GetFormattedShortDate($timeOffsetInMinutes)
		{
			$localTimeStamp = $this->TimeStamp + $timeOffsetInMinutes * 60;
			$utc_str = gmdate('M d Y H:i:s', time());
			$todayTime = strtotime($utc_str) + $timeOffsetInMinutes * 60;
			
			if (date('j', $localTimeStamp) + 0 == date('j', $todayTime) + 0 && date('j n Y', $localTimeStamp) == date('j n Y', $todayTime))
			{
				return date(($this->TimeFormat === 1) ? DATEFORMAT_TIME_12 : DATEFORMAT_TIME_24, $localTimeStamp);
			}
			else if (date('j', $localTimeStamp) + 1 == date('j', $todayTime) + 0 && date('n Y', $localTimeStamp) == date('n Y', $todayTime))
			{
				return DateYesterday;
			}
			else if ($localTimeStamp > $todayTime - 28512000)
			{
				return CDateTime::GetShortMonthJanuary($localTimeStamp);
			}

			return CDateTime::GetShortMonthJanuary($localTimeStamp).', '.date('Y', $localTimeStamp);
		}
		
		function GetFormattedTime($timeOffsetInMinutes)
		{
			$localTimeStamp = $this->TimeStamp + $timeOffsetInMinutes * 60;
			return date(($this->TimeFormat === 1) ? DATEFORMAT_TIME_12 : DATEFORMAT_TIME_24, $localTimeStamp);
		}
		
		public static function GetShortMonthJanuary($timestamp)
		{
			return CDateTime::GetShortMonth(date('n', $timestamp)).' '.date('j', $timestamp);
		}
		
		public static function GetShortMonth($shotMonth)
		{
			$shotMonth = (int) $shotMonth;
			switch ($shotMonth)
			{
				case 1: return ShortMonthJanuary;
				case 2: return ShortMonthFebruary;
				case 3: return ShortMonthMarch;
				case 4: return ShortMonthApril;
				case 5: return ShortMonthMay;
				case 6: return ShortMonthJune;
				case 7: return ShortMonthJuly;
				case 8: return ShortMonthAugust;
				case 9: return ShortMonthSeptember;
				case 10: return ShortMonthOctober;
				case 11: return ShortMonthNovember;
				case 12: return ShortMonthDecember;
			}
			
			return '';
		}
		
		public static function GetShortDay($shotDay)
		{
			switch ($shotDay)
			{
				case 1: return DayToolMonday;
				case 2: return DayToolTuesday;
				case 3: return DayToolWednesday;
				case 4: return DayToolThursday;
				case 5: return DayToolFriday;
				case 6: return DayToolSaturday;
				case 0: return DayToolSunday;
			}
			
			return '';
		}
		
		/**
		 * @return short
		 */
		function GetDateFormatTypeByString()
		{
			switch (strtolower($this->FormatString))
			{
				case 'default':
					return DATEFORMAT_DEFAULT;
				case 'dd/mm/yy':
					return DATEFORMAT_DDMMYY;
				case 'mm/dd/yy':
					return DATEFORMAT_MMDDYY;
				case 'dd month':
					return DATEFORMAT_DDMonth;
				default:
					return DATEFORMAT_Advanced;
			}
		}
		
		/**
		 * @param string $bdDateFormat
		 * @return int
		 */
		public static function GetTimeFormatFromBd($bdDateFormat)
		{
			if (!$bdDateFormat) return 0;
			$l = strlen($bdDateFormat);
			
			return (int) ($l > 2 && substr($bdDateFormat, -2) == DATEFORMAT_FLAG);
		}
		
		/**
		 * @return string
		 */
		public static function GetMySqlDateFormat($fieldName)
		{
			return 'DATE_FORMAT('.$fieldName.', "%Y-%m-%d %T")';
		}
	}
