<?php
/********************************************************
	include/contacts_commons.inc
	
	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Common functions used by source/contacts.php
        and source/edit_contact.php
********************************************************/

function GetGroups($contacts){
	if (!is_array($contacts)) return "";
	
	$grp_ka=array();
	$result=array();
	$i=0;
	while ( list($key, $val) = each($contacts) ){
		$group = $contacts[$key]["grp"];
		$group = trim(chop($group));
		if ((!empty($group))&&($grp_ka[$group]!=1)) $grp_ka[$group]=1;
	}
	reset($grp_ka);
	while ( list($key, $val) = each($grp_ka) ){
		$result[$i]=$key;
		$i++;
	}
	
	return base64_encode(implode(",", $result));
}

?>