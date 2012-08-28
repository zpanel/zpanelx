<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 *
 */

defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../../../'));

function Libraries_autoload($className)
{
    if(strpos($className,'afterlogic_')===0)
	{
        include WM_ROOTPATH . 'libraries/afterlogic/' . str_replace('_','/',substr($className,11)) . '.php';
    }
    if(strpos($className,'Sabre_')===0)
	{
        include WM_ROOTPATH . 'libraries/Sabre/' . str_replace('_','/',substr($className,6)) . '.php';
    }
}

spl_autoload_register('Libraries_autoload');

