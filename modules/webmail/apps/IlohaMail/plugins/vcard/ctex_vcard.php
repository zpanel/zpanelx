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
function ctex_vcard_init(){
	header('Content-Type: application/x-vCard; name="ilohamail.vcf"');
	header('Content-Disposition: attachment; filename="ilohamail.vcf"');
}

function ctex_vcard_export($contacts){
	global $VCARD_FIELDS;
	reset($contacts);
	foreach($contacts as $c){
		$notes = "";
		extract($VCARD_FIELDS); //clear vars
		extract($c);
		$vcf = new Contact_Vcard_Build;
		if (!$lastname && !$firstname)  list($firstname,$lastname)=explode(" ",$name);
		if ($lastname || $firstname) $vcf->setName($lastname, $firstname, "", "", "");
		$vcf->setFormattedName($name);
		$vcf->addEmail($email);
		if ($email2) $vcf->addEmail($email2);
		$vcf->addCategories($grp);
		if ($url) $vcf->setURL($url);
		$vcf->addAddress('', $extended, $street, $city, $region, $postalcode, $country);
		$vcf->setNote($comments);
		
		$lines = explode("\n",$vcf->fetch());
		unset($lines[count($lines)-1]);
		if ($phone) $lines[] = 'TEL;TYPE=HOME:'.$phone;
		if ($work) $lines[] = 'TEL;TYPE=WORK:'.$work;
		if ($cell) $lines[] = 'TEL;TYPE=CELL:'.$cell;
		if ($aim) $lines[] = 'X-AIM:'.$aim;
		if ($icq) $lines[] = 'X-ICQ:'.$icq;
		if ($yahoo) $lines[] = 'X-YAHOO:'.$yahoo;
		if ($msn) $lines[] = 'X-MSN:'.$msn;
		if ($jabber) $lines[] = 'X-JABBER:'.$jabber;
		$lines[] = 'END:VCARD';
		echo implode("\n",$lines)."\n";
	}
}

?>