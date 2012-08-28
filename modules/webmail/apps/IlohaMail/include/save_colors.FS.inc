<?php
/////////////////////////////////////////////////////////
//	
//	include/save_colors.FS.inc
//
//	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Save color prefs (in $my_colors) to file-based back-end
	PRE-CONDITIONS:
		"include/session_auth.inc"
                $userPath (or $path) - user directory
                $my_colors - array
		
********************************************************/

    include_once("../include/array2php.inc");

	if ((isset($userPath)) && (!isset($path))) $path = $userPath;

    // save colors into $userPath/colors file
    $file = $path."/colors";
    $fd=fopen($file, "w");
    if ($fd){
        reset($my_colors);
        while ( list($key, $val) = each($my_colors) ){
            fwrite($fd, $key.":".base64_encode($val)."\n");
        }
        fclose($fd);
    }

    // write dynamically created PHP code into $userPath/colors.inc
    $fp = fopen($file.".inc", "w");
    if ($fp){
        $str = Array2PHP($my_colors, "my_colors");
        fputs($fp, "<?php\n");
        fputs($fp, $str);
        fputs($fp, "?>\n");
    }

?>