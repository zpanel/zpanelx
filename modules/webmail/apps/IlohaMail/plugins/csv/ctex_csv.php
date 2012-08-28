<?php

require_once(PLUGIN_DIR.'/vcard/Contact_Vcard_Build.php');
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
				"country" => $country);
function ctex_csv_init($name="ilohamail"){
	if (!eregi("\\.csv$", $name)) $name.=".csv";
	header('Content-Type: application/x-csv; name="'.$name.'"');
	header('Content-Disposition: attachment; filename="'.$name.'"');
}

function ctex_csv_export($contacts){
	global $VCARD_FIELDS;
	
	//output column list
	$line = "";
	foreach($VCARD_FIELDS as $field=>$foobar){
		$line.=($line?',':'').'"'.$field.'"';
	}
	echo $line."\r\n";
	
	//output data
	reset($contacts);
	foreach($contacts as $c){
		$line = "";
		foreach($VCARD_FIELDS as $field=>$foobar){
			$data = $c[$field];
			//$data = ereg_replace("[\\r\\n]", ' ', $data);
			$data = str_replace("\n", " ", $data);
			$data = str_replace("\r", " ", $data);
			$line.=($line?',':'').'"'.$data.'"';
		}
		echo $line."\r\n";
	}
}

?>