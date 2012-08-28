<?php
/////////////////////////////////////////////////////////
//	
//	include/save_prefs.inc
//
//	(C) Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Save prefs (in $my_prefs) to back-end
                Generate $usrPath/my_prefs.inc file.
	PRE-CONDITIONS:
		"include/session_auth.inc"
                $userPath (or $path) - user directory
                $my_prefs - array
		
********************************************************/
    include_once("../include/array2php.inc");

	if ((isset($userPath)) && (!isset($path))) $path = $userPath;

    // save prefs into $userPath/prefs file
    $file=$path."/prefs";
    $fd=fopen($file, "w");
    if ($fd){
        reset($my_prefs);
        while ( list($key, $val) = each($my_prefs)){
            fwrite($fd, $key.":".base64_encode($val)."\n");
        }
        fclose($fd);
    }
    
    // write dynamically created PHP code into $userPath/prefs.inc
    $fp = fopen($file.".inc", "w");
    if ($fp){
        $str = Array2PHP($my_prefs, "my_prefs");
        fputs($fp, "<?php\n");
        fputs($fp, $str);
        fputs($fp, "?>\n");
    }

?>