<?php
/////////////////////////////////////////////////////////
//	
//	include/identities.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

class identity_obj{
	var $name;
	var $email;
	var $replyto;
	var $sig;
}

function identities_parse($str){
	$result = array();
	$a = iil_ExplodeQuotedString(";", $str);
	if (is_array($a)){
		reset($a);
		while( list($k, $v) = each($a) ){
			$a2 = iil_ExplodeQuotedString(",", $v);
			if (count($a2)==4){
				$temp = new identity_obj;
				$temp->name = $a2[0];
				$temp->email = $a2[1];
				$temp->replyto = $a2[2];
				$temp->sig = $a2[3];
				$result[] = $temp;
			}
		}
	}else{
		echo "Not array: ".$a."<br>";
	}
	
	return $result;
}

function identities_package($a){
	$result = "";

	if (is_array($a)){
		while ( list($k,$v)=each($a) ){
			$result.=$v->name.",".$v->email.",".$v->replyto.",".$v->sig.";";
		}
	}
	
	return $result;
}

?>
