<?php
defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/../../'));

include_once WM_ROOTPATH.'libraries/afterlogic/api.php';

class afterlogic_DAV_PublicFile extends Sabre_DAV_File {

  private $myPath;

  function __construct($myPath) {

    $this->myPath = $myPath;

  }

  function getName() {

      return basename($this->myPath);

  }
 
  function get() {

    return fopen($this->myPath,'r');

  }

  function getSize() {

      return filesize($this->myPath);

  }

  function getETag() {

      return '"' . md5_file($this->myPath) . '"';

  }
  
  function getContentType(){
	  
	  return api_Utils::MimeContentType($this->myPath);
	  
  }
	
}
