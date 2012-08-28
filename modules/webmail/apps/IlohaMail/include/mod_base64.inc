<?php
/*
	File:		mod_base64.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	base64 that uses '_' instead of '/' character for 
				use in file names.
*/


function mod_base64_encode($str){
	return str_replace("/","_", base64_encode($str));
}

function mod_base64_decode($str){
	return base64_decode(str_replace("_", "/", $str));
}

?>