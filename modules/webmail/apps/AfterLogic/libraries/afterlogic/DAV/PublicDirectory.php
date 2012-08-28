<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

class afterlogic_DAV_PublicDirectory extends Sabre_DAV_Collection {

  private $myPath;

  private $myName;

  function __construct($myPath, $myName = null) {
    
	 $this->myPath = $myPath;
	 $this->myName = $myName;

  }

  function getChildren() {

    $children = array();
    // Loop through the directory, and create objects for each node
    foreach(scandir($this->myPath) as $node) {

      // Ignoring files staring with .
      if ($node[0]==='.') continue;
      $children[] = $this->getChild($node);
        
    }

    return $children;

  }

  function getChild($name) {

      $path = $this->myPath . '/' . $name;

      // We have to throw a NotFound exception if the file didn't exist
      if (!file_exists($path)) throw new Sabre_DAV_Exception_NotFound('The file with name: ' . $name . ' could not be found');
      // Some added security

      if ($name[0]=='.')  throw new Sabre_DAV_Exception_NotFound('Access denied');

      if (is_dir($path)) {

          return new afterlogic_DAV_PublicDirectory($path);

      } else {

          return new afterlogic_DAV_PublicFile($path);

      }

  }

  function childExists($name) {

        return file_exists($this->myPath . '/' . $name);

  }

  function getName() {

      if (isset($this->myName))
	  {
		return $this->myName;
	  }
	  else
	  {
		  return basename($this->myPath);
	  }

  }

}