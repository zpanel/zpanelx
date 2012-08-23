<?php
/********************************************************
	include/sort2darray.inc
	
	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Contains function that sorts 2D arrays.
	PRE-CONDITIONS:
		$array - a 2D array
		$field - Field (or key) in 2nd dimension
					so given $a[x][y], $field would be 'y'
		$order - Sort order, 'ASC' for ascending order, 'DESC' for descending
	POST-CONDITIONS:
		Sorted 2D array

********************************************************/

function Sort2DArray($array, $field, $order){
	if (!is_array($array)) return array();
	if (count($array) <= 1) return $array;
	
	reset($array);
	while ( list($key, $val) = each($array) ){
		$index_a[$key] = $array[$key][$field];
	}
	
	if (strcasecmp($order, "ASC")==0) asort($index_a);
	else if (strcasecmp($order, "DESC")==0) arsort($index_a);
	
	$result = array();
	reset($index_a);
	while( list($key, $val) = each($index_a) ){
		$result[$key] = $array[$key];
	}
	
	return $result;
}

?>