<?php
/////////////////////////////////////////////////////////
//	
//	source/contacts.php
//
//	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE:  source/contacts.php
	PURPOSE:
		List basic information of all contacts. 
		Offer links to
			-view/edit contact
			-send email to contact
			-add new contact
		Process posted data to edit/add/remove contacts information
	PRE-CONDITIONS:
		Required:
			$user-Session ID for session validation and user prefernce retreaval
		Optional:
			POST'd data for add/remove/edit entries.  See source/edit_contact.php
	POST-CONDITIONS:
	COMMENTS:

********************************************************/

function FormatHeaderLink($user, $label, $color, $new_sort_field, $sort_field, $sort_order){
	if (strcasecmp($new_sort_field, $sort_field)==0){
		if (strcasecmp($sort_order, "ASC")==0) $sort_order="DESC";
		else $sort_order = "ASC";
	}
	$link = "<a href=\"contacts.php?user=$user&sort_field=$new_sort_field&sort_order=$sort_order\" class=\"mainHeading\">";
	$link .= "<b>".$label."</b></a>";
	return $link;
}

include("../include/super2global.inc");
include("../include/contacts_commons.inc");
include_once("../include/data_manager.inc");
if (isset($user)){
	include("../include/header_main.inc");
	include("../lang/".$my_prefs["lang"]."/contacts.inc");
	include("../lang/".$my_prefs["lang"]."/compose.inc");

	//authenticate
	include_once("../include/icl.inc");
	$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if ($conn){
		iil_Close($conn);
	}else{
		echo "Authentication failed.";
		echo "</html>\n";
		exit;
	}

	echo "\n<table width=\"100%\" cellpadding=2 cellspacing=0><tr bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
	echo "<td align=left valign=bottom>\n";
	echo "<span class=\"bigTitle\">".$cStrings[0]."</span>\n";
	echo "</td></tr></table>\n";

	//initialize source name
	$source_name = $DB_CONTACTS_TABLE;
	if (empty($source_name)) $source_name = "contacts";
	
	//open data manager connection
	$dm = new DataManager_obj;
	if ($dm->initialize($loginID, $host, $source_name, $backend)){
	}else{
		echo "Data Manager initialization failed:<br>\n";
		$dm->showError();
	}
	
	//do add
	if (isset($add)){
		//set group if "other"
		if (strcmp($group,"_otr_")==0) $group=$other_group;
		
		//create data array
    	$new_contact_array = array(
        	"owner" => $session_dataID,
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
        	"comments" => $comments
    	);
		
		if ($edit<=0){	//if not edit (i.e. new), do an insert
			if (!$dm->insert($new_contact_array)){
				echo "Insert failed<br>";
				$dm->showError();
			}
		}else{			//is edit, do an update
			if (!$dm->update($edit, $new_contact_array)){
				echo "update failed<br>";
				$dm->showError();
			}
		}
	}else if (isset($delete)){	//delete entry
		$dm->delete($delete_item);
	}else if (isset($remove)){	//confirm removal of entry
		include("../lang/".$my_prefs["lang"]."/edit_contact.inc");
		echo "<font color=red>".$errors[6].$name.$errors[7]."</font>\n";
		echo "[<a href=\"contacts.php?user=$sid&delete=1&delete_item=$delete_item\" class=\"mainLight\">".$ecStrings[13]."</a>]\n";
		echo "[<a href=\"contacts.php?user=$sid\" class=\"mainLight\">Cancel</a>]\n";
	}
	
	//initialize sort fields and order
	if (empty($sort_field)) $sort_field = "grp,name";
	if (empty($sort_order)) $sort_order = "ASC";
	
	//fetch and sort
	$contacts = $dm->sort($sort_field, $sort_order);
	$numContacts = count($contacts);

	//show error, if any
	if (!empty($error)) echo "<p>".$error."<br>\n";
	
	
	$groups = GetGroups($contacts);
	echo '<p><a href="edit_contact.php?user='.$sid.'&edit=-1" class="mainLight">'.$cStrings[1].'</a><br>';
	echo "\n";

	//show contacts
	if ( is_array($contacts) && count($contacts) > 0){
		reset($contacts);
		$target = ($my_prefs["compose_inside"]?"list2":"_blank"); 
        echo "<form method=\"POST\" action=\"compose2.php\" target=\"$target\">\n";
        echo "<input type=\"hidden\" name=\"user\" value=\"$user\">\n";
		echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"".$my_colors["main_hilite"]."\">\n";
		//echo "<table border=1>\n";
		echo "<tr bgcolor=\"".$my_colors["main_head_bg"]."\">";
        echo "<td class=\"mainHeading\"><b>".$composeHStrings[2]."</b></td>";
        echo "<td class=\"mainHeading\"><b>".$composeHStrings[3]."</b></td>";
        echo "<td class=\"mainHeading\"><b>".$composeHStrings[4]."</b></td>";        
		echo "<td>".FormatHeaderLink($user, $cStrings[3], $textc, "name", $sort_field, $sort_order)."</td>";
		echo "<td>".FormatHeaderLink($user, $cStrings[4], $textc, "email", $sort_field, $sort_order)."</td>";
		echo "<td>".FormatHeaderLink($user, $cStrings[6], $textc, "grp,name", $sort_field, $sort_order)."</td>";
		echo "</tr>";
		while( list($k1, $foobar) = each($contacts) ){
			echo "<tr bgcolor=\"".$my_colors["main_bg"]."\">\n";
			$a=$contacts[$k1];
			$id=$a["id"];
			$toString=(!empty($a["name"])?"\"".$a["name"]."\" ":"")."<".$a["email"].">";
			$toString=urlencode($toString);
			if (empty($a["name"])) $a["name"]="--";
            echo "<td><input type=\"checkbox\" name=\"contact_to[]\" value=\"$toString\"></td>";
            echo "<td><input type=\"checkbox\" name=\"contact_cc[]\" value=\"$toString\"></td>";
            echo "<td><input type=\"checkbox\" name=\"contact_bcc[]\" value=\"$toString\"></td>";
			echo "<td><a href=\"edit_contact.php?user=$sid&k=$k1&edit=$id\">".$a["name"]."</a></td>";
			echo "<td><a href=\"compose2.php?user=$sid&to=$toString\" target=$target>".$a["email"]."</a></td>";
			echo "<td>".$a["grp"]."</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
        echo "<input type=\"submit\" name=\"contacts_submit\" value=\"".$cStrings[10]."\">\n";
	}else{
		echo "<p>".$cErrors[0];
	}
}
?>
</BODY></HTML>