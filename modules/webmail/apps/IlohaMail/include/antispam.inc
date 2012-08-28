<?php
/////////////////////////////////////////////////////////
//	
//	include/antispam.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Provide spam filtering functions.
	COMMENTS:
		EXPERIMENTAL.  Currently not used.

********************************************************/

Function isSpam($text){
	$level=0;
	if (stristr($text,"!")){
		$level++;
	}
	$a=explode(" ",$text);
	$last=count($a)-1;
	$num=(int)$a[$last];
	if (($last>2)&&($num>1000))	$level++;
	
	return $level;
}

?>