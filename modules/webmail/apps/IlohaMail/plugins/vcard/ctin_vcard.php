<?php

require_once(PLUGIN_DIR.'/vcard/Contact_Vcard_Parse.php');
$VCARD_FIELDS=array(
				"name" => $name,
				"email" => $email,
				"email2" => $email2,
				"grp" => $group,
				"aim" => $aim,
				"icq" => $icq,
				"yahoo" => $yahoo,
				"msn" => $msn,
				"jabber" => $jabber,
				"phone" => $phone,
				"work" => $work,
				"cell" => $cell,
				"address" => $address,
				"url" => $url,
				"comments" => $comments,
				"firstname" => $firstname,
				"lastname" => $lastname,
				"street" => $street,
				"extended" => $extended,
				"city" => $city,
				"region" => $region,
				"postalcode" => $postalcode,
				"country" => $country
		);
		
function ctin_vcard_init(){
}

function ctin_vcard_confirm($file, $filename, &$dm){
	global $user;	//session ID
	
	$parser = new Contact_Vcard_Parse;
	$data = $parser->fromFile($file);
		
	$num = count($data);
	if ($num==0){
		echo '<span class="mainLight">';
		echo '<p>No valid vCard data found in file.';
		echo '</span>';
		exit;
	}
	echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="hidden" name="user" value="'.$user.'">';
	echo '<input type="hidden" name="file" value="'.$filename.'">';
	echo '<input type="hidden" name="func_name" value="import">';
	echo '<span class="mainLight">Import '.$num.' records?</span>';
	echo '<input type="submit" name="submit" value="Import">';
	echo '</form>';
	
	return false; //not done
	//print_r($data);
}

function vcard_getFN(&$vcard){
	return $vcard['FN'][0]['value'][0];
}

function vcard_getEmail(&$vcard, $n){
	$n--;
	return $vcard['EMAIL'][$n]['value'][0];
}

function vcard_getCategory(&$vcard){
	return $vcard['CATEGORIES'][0]['value'][0];
}

function vcard_getField(&$vcard, $field){
	return $vcard[$field][0]['value'][0];
}

function vcard_getPhone(&$vcard, $type){
	if (!is_array($vcard['TEL'])) return "";
	foreach($vcard['TEL'] as $phone){
		if (strcasecmp($phone['params']['TYPE'][0],$type)==0)
			return $phone['value'][0];
	}
}

function vcard_getName(&$vcard, $type){
	return $vcard['N'][0]['value'][$type][0];
}

function vcard_getAddress(&$vcard, $type){
	return $vcard['ADR'][0]['value'][$type][0];
}

function ctin_vcard_import($file, $filename, &$dm){
	global $session_dataID;
	
	$parser = new Contact_Vcard_Parse;
	$data = $parser->fromFile($file);
	//print_r($data);
	
	reset($data);
	foreach($data as $vcard){
		$new_contact_array = array(
			"owner" => $session_dataID,
			"name" => vcard_getFN($vcard),
			"email" => vcard_getEmail($vcard, 1),
			"email2" => vcard_getEmail($vcard, 2),
			"grp" => vcard_getCategory($vcard),
			"aim" => vcard_getField($vcard, 'X-AIM'),
			"icq" => vcard_getField($vcard, 'X-ICQ'),
			"yahoo" => vcard_getField($vcard, 'X-YAHOO'),
			"msn" => vcard_getField($vcard, 'X-MSN'),
			"jabber" => vcard_getField($vcard, 'X-JABBER'),
			"phone" => vcard_getPhone($vcard, 'HOME'),
			"work" => vcard_getPhone($vcard, 'WORK'),
			"cell" => vcard_getPhone($vcard, 'CELL'),
			"url" => vcard_getField($vcard, 'URL'),
			"comments" => vcard_getField($vcard, 'NOTES'),
			"firstname" => vcard_getName($vcard, 'given_name'),
			"lastname" => vcard_getName($vcard, 'family_name'),
			"street" => vcard_getAddress($vcard, 'street'),
			"extended" => vcard_getAddress($vcard, 'extended'),
			"city" => vcard_getAddress($vcard, 'locality'),
			"region" => vcard_getAddress($vcard, 'region'),
			"postalcode" => vcard_getAddress($vcard, 'postalcode'),
			"country" => vcard_getAddress($vcard, 'country')
		);
		//print_r($new_contact_array);
		if ($dm->insert($new_contact_array)) $imported++;
	}
	
	echo "Successfully imported $imported items.";
	
	return true; //done
}

?>