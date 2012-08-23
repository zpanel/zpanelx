<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../../'));

include_once WM_ROOTPATH.'libraries/afterlogic/api.php';

class afterlogic_DAV_Client extends Sabre_DAV_Client {

	/**
	* @var string
	*/
	protected $userAgent = 'AfterlogicDAVClient';

	/**
	* @var string
	*/
	protected $serverName = 'AfterlogicDAVServer';
	
	/**
     * Performs an HTTP options request
     *
     * This method returns all the features from the 'DAV:' header as an array.
     * If there was no DAV header, or no contents this method will return an
     * empty array.
     *
     * @return array
     */
    public function options_ex() 
	{
	    $response = array();
		$response = $this->request('OPTIONS');
		$result = array();
		$result['custom-server'] = false;
		
		if(isset($response['headers']['x-server']) && 
				($response['headers']['x-server'] == $this->serverName) != null)
		{
			$result['custom-server'] = true;
		}
		
        if (!isset($response['headers']['dav'])) 
		{
			$result['features'] = array();
        }
		else
		{
			$features = explode(',', $response['headers']['dav']);
			foreach($features as &$v) 
			{
				$v = trim($v);
			}
			$result['features'] = $features;
		}

		if (!isset($response['headers']['allow'])) 
		{
			$result['allow'] = array();
        }
		else
		{
			$allow = explode(',', $response['headers']['allow']);
			foreach($allow as &$v) 
			{
				$v = trim($v);
			}
			$result['allow'] = $allow;
		}
		return $result;
    }
	
	public function request($method, $url = '', $body = null, $headers = array()) 
	{
		$headers['user-agent'] = $this->userAgent;

		$sLog = "REQUEST: ".$method;
		if ($url != '')
		{
			$sLog = $sLog." ".$url;
		}
		if ($body != null)
		{
			$sLog = $sLog."\r\nBody:\r\n".$body;
		}
		CApi::Log($sLog, ELogLevel::Full, 'caldav-');
		CApi::LogObject($headers, ELogLevel::Full, 'caldav-');
		
		$response = array();
		try
		{
			$response = parent::request($method, $url, $body, $headers);
		}
		catch (Sabre_DAV_Exception $ex)
		{
			CApi::LogObject($ex->getMessage(), ELogLevel::Full, 'caldav-');
			throw $ex;
		}
		
		$sLog = "RESPONSE: ".$method;
		if (!empty($response['body']))
		{
			$sLog = $sLog."\r\nBody:\r\n".$response['body'];
		}
		CApi::Log($sLog, ELogLevel::Full, 'caldav-');
		if (!empty($response['headers']))
		{
			CApi::LogObject($response['headers'], ELogLevel::Full, 'caldav-');
		}
		
		return $response;
	}
	
	public function parseMultiStatus($body) 
	{
		$body = str_replace('<D:', '<d:', $body);
		$body = str_replace('</D:', '</d:', $body);
		$body = str_replace(':D=', ':d=', $body);
		
		return parent::parseMultiStatus($body);
	}
	
	public static function getUUID($length = 8)
	{
		$hash = "";  
		$arr = array('a','b','c','d','e','f','g','h','i',
					 'j','k','l','m','n','o','p','q','r',
					 's','t','u','v','w','x','y','z',  
					 'A','B','C','D','E','F','G','H','I',
					 'J','K','L','M','N','O','P','Q','R',
					 'S','T','U','V','W','X','Y','Z', 
					 '1','2','3','4','5','6','7','8','9','0','-','_');
		$pattern = '/^((?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z])(?=[^0-9]*[0-9-_]).{6,})$/';
		for($i = 0; $i < $length; $i++)  
		{  
			$index = rand(0, count($arr) - 1);
			$hash .= $arr[$index];
		}
		if (!preg_match($pattern, $hash)) {
			return self::getUUID($length);
		}
		return $hash;
	}	

}
