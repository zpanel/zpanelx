<?php
/////////////////////////////////////////////////////////
//	
//	include/array2php.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************
    FUNCTION: Array2PHP
    PURPOSE:
        Given an array, generate equivalent PHP code that
        will return the same array.
    PRE-CONDITION:
        $a - Array
        $varname - Variable name to use in generated code
    POST-CONDITION:
        string - PHP code equivalent to array.  Does not
                include '<?php' tags.
********************************************************/

function Array2PHP($a,$varname){
	$str.="\$".$varname."=array(\n";
	$i=0;
	reset($a);
 	while (list($key, $value) = each ($a)) {
	 	$str.=($i>0?",":"")."\"".$key."\"=>\"".$value."\"\n";
		 $i++;
	 }
	$str.=");\n";
	
	return $str;
}

?>
